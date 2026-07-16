<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AdminAuthController extends Controller
{
    public function login(): Response
    {
        return Inertia::render('Auth/AdminLogin', [
            'urls' => [
                'login' => url('/login'),
                'forgotPassword' => route('admin.password.request'),
            ],
        ]);
    }

    public function passwordRequest(): Response
    {
        return Inertia::render('Auth/AdminForgotPassword', [
            'urls' => [
                'sendResetLink' => route('password.email'),
                'login' => route('admin.login'),
            ],
        ]);
    }
}
