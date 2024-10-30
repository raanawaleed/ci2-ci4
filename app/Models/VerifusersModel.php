<?php

namespace App\Models;

use CodeIgniter\Model;

class VerifusersModel extends Model
{
    protected $table = 'users'; // Specify the table name
    protected $primaryKey = 'id'; // Define the primary key
    protected $allowedFields = ['username', 'hashed_password', 'email']; // Specify allowed fields for mass assignment

    // Retrieve all users
    public function verifUsername()
    {
        return $this->findAll(); // Retrieve all users
    }

    // Get username by user ID
    public function getUsername($id)
    {
        return $this->select('username')
            ->where('id', $id)
            ->first(); // Retrieve a single record
    }

    // Get hashed password by user ID
    public function getPassword($id)
    {
        return $this->select('hashed_password')
            ->where('id', $id)
            ->first(); // Retrieve a single record
    }

    // Get email by user ID
    public function getEmail($id)
    {
        return $this->select('email')
            ->where('id', $id)
            ->first(); // Retrieve a single record
    }

    // Retrieve all emails (you can modify this if you only want emails)
    public function getEmails()
    {
        return $this->findAll(); // Retrieve all users
    }
}
