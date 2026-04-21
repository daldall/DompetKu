<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\DefaultCategory;
use App\Models\Transaction;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::query()->count();
        $totalTransactions = Transaction::query()->count();

        $totalPemasukan = (int) Transaction::query()->where('tipe', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = (int) Transaction::query()->where('tipe', 'pengeluaran')->sum('jumlah');

        $totalAiCalls = AiUsageLog::query()->count();
        $aiCallsToday = AiUsageLog::query()->whereDate('created_at', today())->count();

        $defaultKategoriCount = DefaultCategory::query()->count();

        $latestUsers = User::query()->orderByDesc('id')->limit(5)->get(['id', 'nama', 'email', 'role', 'created_at']);

        $topAiUsers = AiUsageLog::query()
            ->selectRaw('user_id, COUNT(*) as total_calls')
            ->groupBy('user_id')
            ->orderByDesc('total_calls')
            ->limit(5)
            ->with(['user:id,nama,email'])
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalTransactions',
            'totalPemasukan',
            'totalPengeluaran',
            'totalAiCalls',
            'aiCallsToday',
            'defaultKategoriCount',
            'latestUsers',
            'topAiUsers',
        ));
    }
}
