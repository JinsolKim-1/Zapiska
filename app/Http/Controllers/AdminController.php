<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function analytics()
    {
        return view('admin.analytics'); // create this Blade
    }

    public function departments()
    {
        return view('admin.departments');
    }

    public function assets()
    {
        return view('admin.assets');
    }

    public function requests()
    {
        return view('admin.requests');
    }

    public function receipts()
    {
        return view('admin.receipts');
    }

    public function users()
    {
        return view('admin.users');
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
