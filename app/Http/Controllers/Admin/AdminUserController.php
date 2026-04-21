<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\Transaction;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->withCount('transactions')
            ->orderByDesc('id')
            ->paginate(10);

        $totalTransactionsGlobal = Transaction::query()->count();
        $totalAiCallsGlobal = AiUsageLog::query()->count();

        return view('admin.users.index', compact('users', 'totalTransactionsGlobal', 'totalAiCallsGlobal'));
    }
}
