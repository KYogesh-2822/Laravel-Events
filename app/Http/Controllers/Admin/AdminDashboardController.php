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

    // Search
    if ($request->filled('search')) {

        $search = trim($request->search);

        if (filter_var($search, FILTER_VALIDATE_EMAIL) || str_contains($search, '@')) {
            $query->where('email', 'like', $search.'%');
        }
        elseif (ctype_digit($search)) {
            $query->where('phone', 'like', $search.'%');
        }
        else {

        //    $query->where(function ($q) use ($search) {
                $query->where('name', 'like', $search . '%');
                // ->orWhere('city', 'like', $search . '%');
            // });

        }
    }
    

    // Status Filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Measure query time
    $start = microtime(true);

    $users = $query->orderByDesc('id')
                   ->simplePaginate(50)
                   ->withQueryString();

    $time = round((microtime(true) - $start) * 1000, 2); // milliseconds

    if ($request->ajax()) {
        return view('admin.dashboard.users.partials.table', compact('users', 'time'))->render();
    }

    return view('admin.includes.dashboard', compact('users', 'time'));
}

    // public function index(Request $request)
    // {
    //     $query = User::query()
    //         ->select('id', 'name', 'email', 'phone', 'city', 'status');

    //     if ($request->filled('search')) {

    //         $search = trim($request->search);

    //         $query->where(function ($q) use ($search) {

    //             $q->where('email', $search)
    //             ->orWhere('name', $search)
    //             ->orWhere('phone', $search)
    //             ->orWhere('city', $search);

    //         });
    //     }

    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     $start = microtime(true);

    //     $users = $query->orderByDesc('id')
    //         ->simplePaginate(50)
    //         ->withQueryString();

    //     $time = microtime(true) - $start;

    //     if ($request->ajax()) {
    //         return view('admin.dashboard.users.partials.table', compact('users', 'time'))->render();
    //     }

    //     return view('admin.includes.dashboard', compact('users', 'time'));
    // }
}
