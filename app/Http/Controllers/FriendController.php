<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FriendRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function index()
    {
        return view('friends');
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = $request->input('query');
                $currentUserId = Auth::id();
                
                $users = DB::table('users')
                    ->select('id', 'name', 'email')
                    ->where('name', 'like', "%{$query}%")
                    ->where('id', '!=', $currentUserId)
                    ->where('role', '!=', 'admin')
                    ->whereNotExists(function ($query) use ($currentUserId) {
                        $query->select(DB::raw(1))
                            ->from('friendships')
                            ->whereRaw('friendships.friend_id = users.id')
                            ->where('friendships.user_id', $currentUserId);
                    })
                    ->whereNotExists(function ($query) use ($currentUserId) {
                        $query->select(DB::raw(1))
                            ->from('friend_requests')
                            ->whereRaw('friend_requests.receiver_id = users.id')
                            ->where('friend_requests.sender_id', $currentUserId)
                            ->where('friend_requests.status', 'pending');
                    })
                    ->take(5)
                    ->get();

                return response()->json([
                    'status' => 'success',
                    'data' => $users,
                    'count' => $users->count()
                ]);
            } catch (\Exception $e) {
                Log::error('Search error: ' . $e->getMessage());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error searching for users'
                ], 500);
            }
        }

        return view('friends.search');
    }

    public function sendRequest(Request $request)
    {
        try {
            $friendId = $request->input('friend_id');
            $user = Auth::user();

            if (!$friendId || !$user) {
                throw new \Exception('Invalid request');
            }

            // Check if already friends
            $existingFriendship = DB::table('friendships')
                ->where('user_id', $user->id)
                ->where('friend_id', $friendId)
                ->exists();

            if ($existingFriendship) {
                throw new \Exception('Already friends');
            }

            // Check for existing pending request
            $existingRequest = FriendRequest::where('sender_id', $user->id)
                ->where('receiver_id', $friendId)
                ->where('status', 'pending')
                ->exists();

            if ($existingRequest) {
                throw new \Exception('Friend request already sent');
            }

            // Create new friend request
            FriendRequest::create([
                'sender_id' => $user->id,
                'receiver_id' => $friendId,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Friend request sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getPendingRequests()
    {
        try {
            $requests = FriendRequest::with('sender')
                ->where('receiver_id', Auth::id())
                ->where('status', 'pending')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $requests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading friend requests'
            ], 500);
        }
    }

    public function acceptRequest($id)
    {
        try {
            DB::beginTransaction();

            $request = FriendRequest::where('id', $id)
                ->where('receiver_id', Auth::id())
                ->where('status', 'pending')
                ->firstOrFail();

            // Update request status
            $request->status = 'accepted';
            $request->save();

            // Create friendship
            DB::table('friendships')->insert([
                'user_id' => $request->sender_id,
                'friend_id' => $request->receiver_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create reverse friendship for bidirectional relationship
            DB::table('friendships')->insert([
                'user_id' => $request->receiver_id,
                'friend_id' => $request->sender_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Friend request accepted'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error accepting friend request'
            ], 500);
        }
    }

    public function rejectRequest($id)
    {
        try {
            $request = FriendRequest::where('id', $id)
                ->where('receiver_id', Auth::id())
                ->where('status', 'pending')
                ->firstOrFail();

            $request->status = 'rejected';
            $request->save();

            return response()->json([
                'success' => true,
                'message' => 'Friend request rejected'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting friend request'
            ], 500);
        }
    }

    public function getFriends()
    {
        try {
            $friends = DB::table('users')
                ->join('friendships', 'users.id', '=', 'friendships.friend_id')
                ->where('friendships.user_id', Auth::id())
                ->select('users.id', 'users.name', 'users.streak')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $friends
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading friends'
            ], 500);
        }
    }

    public function removeFriend($id)
    {
        try {
            DB::beginTransaction();
            
            // Remove both directions of friendship
            DB::table('friendships')
                ->where(function($query) use ($id) {
                    $query->where('user_id', Auth::id())
                          ->where('friend_id', $id);
                })
                ->orWhere(function($query) use ($id) {
                    $query->where('user_id', $id)
                          ->where('friend_id', Auth::id());
                })
                ->delete();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Friend removed successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error removing friend'
            ], 500);
        }
    }
}
