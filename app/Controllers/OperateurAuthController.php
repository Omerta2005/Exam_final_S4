<?php

namespace App\Controllers;

class OperateurAuthController extends BaseController
{
    public function index()
    {
        return view('operateur/login');
    }
}