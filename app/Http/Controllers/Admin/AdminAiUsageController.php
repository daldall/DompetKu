<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;

class AdminAiUsageController extends Controller
{
    public function index()
    {
        $totalCalls = AiUsageLog::query()->count();
        $successCalls = AiUsageLog::query()->where('success', true)->count();
        $failedCalls = $totalCalls - $successCalls;

        $byFeature = AiUsageLog::query()
            ->selectRaw('feature, COUNT(*) as total, SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as success_total')
            ->groupBy('feature')
            ->orderByDesc('total')
            ->get();

        $byDay = AiUsageLog::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $topUsers = AiUsageLog::query()
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->with(['user:id,nama,email'])
            ->limit(10)
            ->get();

        $latestLogs = AiUsageLog::query()
            ->with(['user:id,nama,email'])
            ->orderByDesc('id')
            ->limit(25)
            ->get();

        return view('admin.ai-usage.index', compact(
            'totalCalls',
            'successCalls',
            'failedCalls',
            'byFeature',
            'byDay',
            'topUsers',
            'latestLogs'
        ));
    }
}
