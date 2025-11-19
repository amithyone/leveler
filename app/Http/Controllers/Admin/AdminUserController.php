<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        return view('admin.admin-users.index');
    }

    public function view()
    {
        return view('admin.admin-users.view');
    }
}

