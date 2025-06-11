<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard($sort = 'asc')
    {
        $users = User::orderBy('name', $sort)->get();
        
        // Get counts by role
        $userCounts = User::selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        return view('admin.dashboard', [
            'users' => $users,
            'currentSort' => $sort,
            'adminCount' => $userCounts['admin'] ?? 0,
            'userCount' => $userCounts['user'] ?? 0
        ]);
    }

    public function updateUserName(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User name updated successfully'
        ]);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    public function index()
    {
        $users = User::where('role', '!=', 'admin')->get();
        return view('admin.dashboard', compact('users'));
    }
}
