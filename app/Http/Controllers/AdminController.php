<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::where('role', 'user')->get();
        return view('admin.adminspage', compact('users'));
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'User deleted');
    }

    public function updateUserName(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $user = User::findOrFail($id);
        $user->update(['name' => $validated['name']]);

        return back()->with('success', 'User name updated successfully');
    }

    public function index()
    {
        $users = User::where('role', '!=', 'admin')->get();
        return view('admin.dashboard', compact('users'));
    }
}
