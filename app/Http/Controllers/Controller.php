<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function isAdmin()
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }
    public function isOperator()
    {
        return auth()->check() && auth()->user()->role === 'operator';
    }
    public function isGuestRole()
    {
        return auth()->check() && auth()->user()->role === 'guest';
    }
}
