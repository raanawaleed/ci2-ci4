<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'email', 'password', 'created_at', 'updated_at', 'status', 'last_login', 'hashed_password'];

    protected $password = false;
    protected $hashed_password;

    // Relationships
    public function tickets()
    {
        return $this->hasMany(TicketModel::class, 'user_id');
    }

    public function projectHasWorkers()
    {
        return $this->hasMany(ProjectHasWorkerModel::class, 'user_id');
    }

    public function projectHasTasks()
    {
        return $this->hasMany(ProjectHasTaskModel::class, 'user_id');
    }

    public function projectHasTimesheets()
    {
        return $this->hasMany(ProjectHasTimeSheetModel::class, 'user_id');
    }

    // Get user by username
    public function getByName($username)
    {
        return $this->where('username', $username)->first();
    }

    // Get the salary affectation by ID
    public function idsal($id)
    {
        return $this->db->table('salaries')->select('seraffectation')->where('id', $id)->get()->getResult();
    }

    // Get the last user ID
    public function getLastId()
    {
        return $this->selectMax('id')->first()['id'] ?? null;
    }

    // Get all active users
    public function getAll()
    {
        return $this->where('status', 'active')->findAll();
    }

    // Get user by ID
    public function getUserById($id)
    {
        return $this->find($id);
    }

    // Update user by email
    public function updateUser($data)
    {
        return $this->where('email', $data['email'])->set($data)->update();
    }

    // Password handling
    public function setPassword($plaintext)
    {
        $this->hashed_password = $this->hashPassword($plaintext);
        $this->password = true; // Set to true to indicate password is being set
    }

    public function hashPassword($password)
    {
        $salt = substr(sha1(rand()), 0, 30);
        $hash = hash('sha256', $salt . $password);
        return $salt . $hash;
    }

    public function validatePassword($password)
    {
        $salt = substr($this->hashed_password, 0, 30);
        $hash = substr($this->hashed_password, 30);
        $password_hash = hash('sha256', $salt . $password);
        return $password_hash === $hash;
    }

    public static function validate_login($username, $password)
    {
        $userModel = new self();
        $user = $userModel->where('email', $username)->first();
        // var_dump($user);
        // die();
        if ($user && $user['status'] === 'active') {
            self::login($user['id'], 'user_id');
            $user['last_login'] = time();
            $userModel->save($user);
            return $user;
        }

        return false;
    }

    public static function login($user_id, $type)
    {
        $session = session();
        $session->set($type, $user_id);
    }

    public static function logout()
    {
        $session = session();
        $session->destroy();
    }

    public static function insertToken($user_id, $user_email)
    {
        $token = substr(sha1(rand()), 0, 30);
        $date = date('Y-m-d');

        $data = [
            'token' => $token,
            'user' => $user_id,
            'timestamp' => $date,
            'email' => $user_email
        ];

        $db = \Config\Database::connect();
        $db->table('pw_reset')->insert($data);
        return $token . $user_id;
    }

    public static function isTokenValid($token)
    {
        $tkn = substr($token, 0, 30);
        $uid = substr($token, 30);

        $db = \Config\Database::connect();
        $row = $db->table('pw_reset')
            ->where(['token' => $tkn, 'user' => $uid])
            ->get()
            ->getRow();

        if ($row) {
            $created = $row->timestamp;
            if (date('Y-m-d', strtotime($created)) === date('Y-m-d')) {
                return $row->user;
            }
        }

        return false;
    }
}
