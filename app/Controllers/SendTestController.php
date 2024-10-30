<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SendTestController extends BaseController
{
    public function index()
    {
        return view('messages/all');
    }

    public function mailing()
    {
        $email = \Config\Services::email();

        $email->setFrom('bilelweslatisi@gmail.com', 'Bilel');
        $email->setTo('bilelweslatisi@gmail.com');
        $email->setSubject('Email Test');
        $email->setMessage('Testing the email class.');

        if ($email->send()) {
            return 'Email sent successfully!';
        } else {
            return 'Failed to send email: ' . $email->printDebugger(['headers']);
        }
    }
}