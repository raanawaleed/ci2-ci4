<?php

namespace App\Controllers;

use App\Models\UserModel; // Ensure you have a UserModel for user-related DB operations
use CodeIgniter\Controller;

class ForgotPassController extends Controller
{
    protected $userModel, $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->loadDatabase();
    }
    private function loadDatabase()
    {
        // Assuming database is already set in the configuration
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'error' => false,
            'theme_view' => 'login',
            'content_view' => 'auth/forgotpass',
        ];

        if ($this->request->getMethod() === 'post') {
            $email = htmlspecialchars(trim($this->request->getPost('email')));
            $user = $this->userModel->findByEmail($email);

            if ($user && $user->status === "active") {
                session()->set('reset_password', true);
                $token = $this->userModel->insertToken($user->id, $user->email);
                $qstring = $this->base64url_encode($token);
                $url = site_url('/forgotpass/reset_password/' . $qstring);

                $ret = $this->sendMail($user, $url);
                session()->set('send_email', $ret);
                return redirect()->to('login');
            } else {
                session()->setFlashdata('message', 'error: L\'email entré ne correspond à aucun compte.');
                return redirect()->to('forgotpass');
            }
        }

        return view($data['theme_view'], $data);
    }

    public function reset_password($xtoken)
    {
        $token = $this->base64url_decode($xtoken);
        $cleanToken = $this->security->xss_clean($token);
        $user_id = $this->userModel->isTokenValid($cleanToken);

        if (!$user_id) {
            session()->setFlashdata('message', "error: Token n'est pas valide ou a expiré");
            return redirect()->to('login');
        }

        $user_info = $this->userModel->findById($user_id);

        if ($this->request->getMethod() === 'post') {
            $post = $this->request->getPost();
            $cleanPost = $this->security->xss_clean($post);
            $hashedPassword = $this->hashPassword($cleanPost['password']);

            if ($this->userModel->updatePassword($user_info->id, $hashedPassword)) {
                session()->setFlashdata('message', "success: Votre mot de passe a été modifié. Vous pouvez vous connecter.");
            } else {
                session()->setFlashdata('message', "error: Un problème a été rencontré en réinitialisant votre mot de passe.");
            }

            return redirect()->to("auth/login");
        }

        $data = [
            'form_action' => 'forgotpass/resetpwd',
            'user_info' => $user_info,
            'token' => $this->base64url_encode($token),
            'content_view' => 'auth/resetpwd',
        ];

        return view('layout/main', $data); // Use your main layout file
    }

    public function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    public function sendMail($user, $url)
    {
        $fromEmail = "bonjour@bimmapping.com";
        $myCompany = $this->db->table('v_companies')->get()->getRow();

        if (!$myCompany) {
            session()->setFlashdata('message', 'error: Une erreur a été rencontrée en récupérant la société.');
            return redirect()->to('login');
        }

        $data = [
            'user' => $user,
            'url' => $url,
            'employeur' => $myCompany,
            'to_user' => ucwords(strtolower($user->firstname . ' ' . $user->lastname)),
        ];

        // Use CodeIgniter's email service
        $email = \Config\Services::email();
        $email->setFrom($fromEmail);
        $email->setTo($user->email);
        $email->setSubject('Réinitialisation du mot de passe');
        $email->setMessage(view('emails/reset_password', $data)); // Create a separate view for the email content

        if ($email->send()) {
            session()->setFlashdata('message', 'success: Email envoyé avec succès.');
            return true;
        } else {
            session()->setFlashdata('message', 'error: Une erreur est survenue lors de l\'envoi de l\'email. Veuillez réessayer.');
            return false;
        }
    }

    private function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
