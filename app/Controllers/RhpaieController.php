<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class RhpaieController extends BaseController
{
    public function __construct()
    {
        // Check if user or client is authenticated; redirect to login if not
        if (!session()->get('client') && !session()->get('user')) {
            return redirect()->to('login')->send(); // Redirect and stop execution
        }
    
    }

    public function index()
    {
        // This method can be used to display a view or handle logic for the index page
        return view('rhpaie/index'); // Example view
    }
}
