<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            $query = $request->input('query');
            $currentUserId = Auth::id();
            
            $users = DB::table('users')
                ->where('name', 'like', "%{$query}%")
                ->where('id', '!=', $currentUserId)
                ->where('role', '!=', 'admin')
                ->whereNotExists(function ($query) use ($currentUserId) {
                    $query->select(DB::raw(1))
                        ->from('friendships')
                        ->whereRaw('friendships.friend_id = users.id')
                        ->where('friendships.user_id', $currentUserId);
                })
                ->take(5)
                ->get(['id', 'name']);
                
            return response()->json($users);
        }

        return view('friends.search');
    }

    public function addFriend(Request $request)
    {
        try {
            $friendId = $request->input('friend_id');
            $user = Auth::user();

            if (!$friendId || !$user) {
                throw new \Exception('Invalid request');
            }

            // Check using the correct table name 'friendships'
            $existingFriendship = DB::table('friendships')
                ->where('user_id', $user->id)
                ->where('friend_id', $friendId)
                ->exists();

            if ($existingFriendship) {
                throw new \Exception('Already friends');
            }

            // Insert into friendships table
            DB::table('friendships')->insert([
                'user_id' => $user->id,
                'friend_id' => $friendId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Friend added successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function removeFriend($id)
    {
        try {
            DB::beginTransaction();
            
            $friend = User::findOrFail($id);
            $user = Auth::user();
            
            // Remove friendship in both directions
            DB::table('friendships')
                ->where(function($query) use ($user, $id) {
                    $query->where('user_id', $user->id)
                          ->where('friend_id', $id);
                })
                ->orWhere(function($query) use ($user, $id) {
                    $query->where('user_id', $id)
                          ->where('friend_id', $user->id);
                })
                ->delete();
            
            DB::commit();
            return back()->with('success', "{$friend->name} removed from friends.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Could not remove friend.');
        }
    }

    public function getFriends()
    {
        $userId = Auth::id();
        
        try {
            $friends = DB::table('friendships')
                ->join('users', 'users.id', '=', 'friendships.friend_id')
                ->where('friendships.user_id', $userId)
                ->select('users.id', 'users.name', 'users.streak')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $friends
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Could not load friends'
            ]);
        }
    }

    public function updateWorkoutDays(Request $request)
    {
        try {
            $userId = Auth::id();
            $schedule = DB::table('workout_schedules')
                ->where('user_id', $userId)
                ->first();

            if ($schedule && $schedule->is_locked) {
                return response()->json([
                    'error' => 'Schedule is locked for this week'
                ], 403);
            }

            $days = $request->validate([
                'monday' => 'required|boolean',
                'tuesday' => 'required|boolean',
                'wednesday' => 'required|boolean',
                'thursday' => 'required|boolean',
                'friday' => 'required|boolean',
                'saturday' => 'required|boolean',
                'sunday' => 'required|boolean',
            ]);

            DB::table('workout_schedules')
                ->updateOrInsert(
                    ['user_id' => $userId],
                    array_merge($days, [
                        'updated_at' => now()
                    ])
                );

            return response()->json([
                'message' => 'Workout days updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not update workout days'
            ], 500);
        }
    }

    public function lockWorkoutSchedule()
    {
        try {
            $userId = Auth::id();
            $schedule = DB::table('workout_schedules')
                ->where('user_id', $userId)
                ->first();

            if (!$schedule) {
                return response()->json([
                    'error' => 'No schedule found to lock'
                ], 404);
            }

            DB::table('workout_schedules')
                ->where('user_id', $userId)
                ->update([
                    'is_locked' => true,
                    'locked_at' => now(),
                    'updated_at' => now()
                ]);

            return response()->json([
                'message' => 'Schedule locked successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not lock schedule'
            ], 500);
        }
    }

    public function getWorkoutSchedule()
    {
        $userId = Auth::id();
        $schedule = DB::table('workout_schedules')
            ->where('user_id', $userId)
            ->first();

        return response()->json($schedule ?? [
            'monday' => false,
            'tuesday' => false,
            'wednesday' => false,
            'thursday' => false,
            'friday' => false,
            'saturday' => false,
            'sunday' => false,
            'is_locked' => false
        ]);
    }

    public function sendRequest($id)
    {
        try {
            $sender = Auth::id();
            
            // Check if request already exists
            $existingRequest = DB::table('friend_requests')
                ->where('sender_id', $sender)
                ->where('receiver_id', $id)
                ->where('status', 'pending')
                ->first();

            if ($existingRequest) {
                return response()->json(['error' => 'Friend request already sent']);
            }

            // Check if they're already friends
            $existingFriendship = DB::table('friendships')
                ->where('user_id', $sender)
                ->where('friend_id', $id)
                ->first();

            if ($existingFriendship) {
                return response()->json(['error' => 'Already friends']);
            }

            DB::table('friend_requests')->insert([
                'sender_id' => $sender,
                'receiver_id' => $id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['message' => 'Friend request sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not send friend request'], 500);
        }
    }

    public function getPendingRequests()
    {
        $userId = Auth::id();
        
        try {
            $requests = DB::table('friend_requests')
                ->join('users', 'users.id', '=', 'friend_requests.sender_id')
                ->where('friend_requests.receiver_id', $userId)
                ->where('friend_requests.status', 'pending')
                ->select('users.id as user_id', 'users.name', 'friend_requests.id')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $requests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Could not load requests'
            ]);
        }
    }

    public function handleRequest(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $friendRequest = DB::table('friend_requests')
                ->where('sender_id', $id)
                ->where('receiver_id', Auth::id())
                ->where('status', 'pending')
                ->first();

            if (!$friendRequest) {
                return response()->json(['error' => 'Friend request not found'], 404);
            }

            if ($request->action === 'accept') {
                // Create friendships
                DB::table('friendships')->insert([
                    ['user_id' => Auth::id(), 'friend_id' => $id, 'created_at' => now()],
                    ['user_id' => $id, 'friend_id' => Auth::id(), 'created_at' => now()]
                ]);
                
                $status = 'accepted';
            } else {
                $status = 'declined';
            }

            // Update request status
            DB::table('friend_requests')
                ->where('id', $friendRequest->id)
                ->update([
                    'status' => $status,
                    'updated_at' => now()
                ]);

            DB::commit();
            return response()->json(['success' => true, 'action' => $status]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Could not process friend request'], 500);
        }
    }
}
