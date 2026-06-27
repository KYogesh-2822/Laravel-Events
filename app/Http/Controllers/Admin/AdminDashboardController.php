<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
class AdminDashboardController extends Controller
{

public function index(Request $request)
{
    $query = User::query()
        ->select('id', 'name', 'email', 'phone', 'city', 'status');

    $allowedColumns = [
        'name' => 'name',
        'email' => 'email',
        'phone' => 'phone',
        'city' => 'city',
    ];

    $searchBy = $request->input('search_by', 'name');

    if (! array_key_exists($searchBy, $allowedColumns)) {
        $searchBy = 'name';
    }

    $column = $allowedColumns[$searchBy];

    if ($request->filled('search')) {

        $search = trim($request->search);

        // Avoid running huge searches like city LIKE 'a%'
        if (strlen($search) >= 2) {

            if ($searchBy === 'phone') {
                // Only needed if user types +91, spaces, dash, etc.
                $search = preg_replace('/\D+/', '', $search);
                }
                
                if ($search !== '') {
                // dd($request->all(),$search,$column);
                $query->where($column, 'like', $search . '%');
            }
        }
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $start = microtime(true);

    if ($request->filled('search') && strlen(trim($request->search)) >= 2) {
        $query->orderBy($column)->orderByDesc('id');
    } else {
        $query->orderByDesc('id');
    }

    $users = $query->simplePaginate(50)->withQueryString();

    $time = round((microtime(true) - $start) * 1000, 2);

    if ($request->ajax()) {
        return view('admin.dashboard.users.partials.table', compact('users', 'time'))->render();
    }

    return view('admin.includes.dashboard', compact('users', 'time'));
}

}
