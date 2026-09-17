<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;

class SystemLogController extends Controller
{
    public function index()
    {
        $logs = SystemLog::with('user')->latest()->paginate(10);

        return view('admin.logs.index', compact('logs'));
    }
}
