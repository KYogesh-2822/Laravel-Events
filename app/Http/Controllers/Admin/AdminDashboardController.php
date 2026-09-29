<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->guard('admin')->check(), 403, 'Access denied.');

        $allowedColumns = [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'city' => 'city',
        ];

        $searchBy = $request->input('search_by', 'name');
        $searchBy = array_key_exists($searchBy, $allowedColumns) ? $searchBy : 'name';

        $column = $allowedColumns[$searchBy];
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10,50, 100, 250], true) ? $perPage : 10;
        $hasSearch = strlen($search) >= 2;

        $query = User::query()
            ->select('id', 'name', 'email', 'phone', 'city', 'status');

        if ($request->filled('status') && in_array($status, ['active', 'inactive'], true)) {
            $query->where('status', $status);
        }

        if ($search !== '' && ! $hasSearch) {
            $query->whereRaw('1 = 0');
        }

        if ($hasSearch) {
            if ($searchBy === 'phone') {
                $search = preg_replace('/\D+/', '', $search);
            }

            if ($search === '') {
                $query->whereRaw('1 = 0');
            } elseif ($searchBy === 'email' && filter_var($search, FILTER_VALIDATE_EMAIL)) {
                $query->where('email', $search);
            } else {
                $query->where($column, 'like', $this->escapeLike($search) . '%');
            }
        }

        $start = microtime(true);

        if ($hasSearch) {
            $query->orderBy($column)->orderByDesc('id');
        } else {
            $query->orderByDesc('id');
        }

        $users = $query->simplePaginate($perPage)->withQueryString();
        $time = round((microtime(true) - $start) * 1000, 2);

        if ($request->ajax()) {
            return view('admin.dashboard.users.partials.table', compact('users', 'time', 'perPage'))->render();
        }

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
            'revenue' => 0,
        ];

        return view('admin.includes.dashboard', compact('users', 'time', 'stats', 'perPage'));
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}


