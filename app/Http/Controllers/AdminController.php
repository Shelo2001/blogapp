<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function AdminDashboard(Request $request){
        $data = [
            'pageTitle' => 'Admin Dashboard'
        ];

        return view('back.pages.dashboard', $data);
    }
}