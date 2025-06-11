<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of all users (Admin only).
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Delete a user by ID (Admin only).
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Search for users by name.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $term = $request->get('term');
            $currentUserId = Auth::id();
            
            Log::info('Search started', [
                'term' => $term,
                'userId' => $currentUserId
            ]);

            // Simplified search query
            $users = User::where('id', '!=', $currentUserId)
                ->where('name', 'LIKE', "%{$term}%")
                ->select('id', 'name')
                ->get();
            
            Log::info('Search results', [
                'count' => $users->count(),
                'results' => $users->toArray()
            ]);

            // Simplify response
            return response()->json([
                'success' => true,
                'data' => $users->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'status' => 'none'  // Default status
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Search failed'
            ], 500);
        }
    }

    /**
     * Add a friend to the user's friend list.
     */
    public function addFriend(Request $request): JsonResponse
    {
        try {
            $friendId = $request->friend_id;
            $user = Auth::user();

            if (!$friendId || !$user) {
                throw new \Exception('Invalid request');
            }

            $exists = DB::table('friendships')
                        ->where('user_id', $user->id)
                        ->where('friend_id', $friendId)
                        ->exists();

            if ($exists) {
                throw new \Exception('Already friends');
            }

            DB::table('friendships')->insert([
                'user_id' => $user->id,
                'friend_id' => $friendId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Friend request sent!',
                'buttonText' => 'Request Sent'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'buttonText' => 'Add Friend'
            ], 400);
        }
    }

    /**
     * Display user settings.
     */
    public function settings()
    {
        return view('user.settings', ['user' => Auth::user()]);
    }

    /**
     * Update user attributes
     */
    public function update($id, array $attributes)
    {
        $user = User::findOrFail($id);
        $user->fill($attributes);
        $user->save();
        return $user;
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $this->update($user->id, [
            'password' => Hash::make($validated['new_password'])
        ]);

        return back()->with('success', 'Password updated successfully');
    }

    /**
     * Get all current friends with their details.
     */
    public function getFriends(): JsonResponse
    {
        $currentUserId = Auth::id();
        
        $friends = DB::table('users')
            ->select('users.id', 'users.name', 'users.streak')
            ->join('friendships', 'users.id', '=', 'friendships.friend_id')
            ->where('friendships.user_id', '=', $currentUserId)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $friends
        ]);
    }

    /**
     * Remove a friend from the user's friend list.
     */
    public function removeFriend($friendId): JsonResponse
    {
        $currentUserId = Auth::id();
        
        DB::table('friendships')
            ->where('user_id', $currentUserId)
            ->where('friend_id', $friendId)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Friend removed successfully'
        ]);
    }
}
