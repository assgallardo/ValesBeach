<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Redirect staff to admin dashboard since they have access to admin features
        return redirect()->route('admin.dashboard');
    }
}
