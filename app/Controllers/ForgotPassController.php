<?php

namespace App\Controllers;

use App\Models\UserModel;
use Config\Services;
use App\Controllers\BaseController;

class ForgotPassController extends BaseController
{

    protected $userModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }
    public function index()
    {
        if ($this->request->getMethod() === 'post') {
            $email = $this->request->getPost('email');

            $user = $this->userModel->findByEmail(trim($email));

            if ($user && $user->status === 'active') {
                $this->session->set('reset_password', true);
                $token = $this->userModel->insertToken($user->id, $user->email);
                $url = site_url('forgotpass/reset_password/' . $this->base64url_encode($token));

                if ($this->sendMail($user, $url)) {
                    return redirect()->to('login')->with('message', 'Email sent successfully.');
                }
            } else {
                return redirect()->to('forgotpass')->with('message', "L'email entré ne correspond à aucun compte.");
            }
        }

        return view('auth/forgotpass');
    }

    /**
     * Modification du mot de passe
     */
    public function reset_password($xtoken)
    {
        $token = $this->base64url_decode($xtoken);
        $userId = $this->userModel->isTokenValid($token);

        if (!$userId) {
            return redirect()->to('login')->with('message', "error: Token n'est pas valide ou a expiré");
        }

        $userInfo = $this->userModel->find($userId);

        if ($this->request->getMethod() === 'post') {
            $password = $this->request->getPost('password');
            $hashedPassword = $this->hash_password($password);

            if ($this->userModel->updatePassword($userId, $hashedPassword)) {
                return redirect()->to('auth/login')->with('message', "success: Votre mot de pass a été modifié.");
            } else {
                return redirect()->to('forgotpass/reset_password/' . $xtoken)->with('message', "error: Un problème a été recontré en réinisialisant votre mot de passe.");
            }
        }

        return view('auth/resetpwd', ['user_info' => $userInfo, 'token' => $this->base64url_encode($token)]);
    }

    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }


    /**
     * Sends a mail.
     *
     * @param      <type>   $user      The user
     * @param      string   $to_email  To email
     *
     * @return     boolean  statut de l'envoie
     */
    private function sendMail($user, $url)
    {
        $email = Services::email();
        $email->setFrom('bonjour@bimmapping.com', 'BimMapping');
        $email->setTo($user->email);
        $email->setSubject('Réinitialisation du mot de passe');

        $message = view('emails/reset_password', [
            'user' => $user,
            'url' => $url
        ]);
        $email->setMessage($message);

        if ($email->send()) {
            return true;
        } else {
            log_message('error', 'Email not sent: ' . $email->printDebugger());
            return false;
        }
    }

    public function confirmPwd($id)
    {
        $password = $this->request->getPost('password');
        $hashedPassword = $this->hash_password($password);
        
        if ($this->userModel->updatePassword($id, $hashedPassword)) {
            return redirect()->to('auth/login')->with('message', "success: Votre mot de pass a été modifié.");
        } else {
            return redirect()->to('forgotpass/resetpwd/' . $id)->with('message', "error: Un problème a été rencontré lors de la mise à jour du mot de passe.");
        }
    }

    public function resetpwd($id)
    {
        return view('auth/resetpwd', ['form_action' => 'forgotpass/confirmPwd/' . $id, 'id' => $id]);
    }
    private function hash_password($password)
    {
        $salt = bin2hex(random_bytes(32));
        return $salt . hash('sha256', $salt . $password);
    }
}
