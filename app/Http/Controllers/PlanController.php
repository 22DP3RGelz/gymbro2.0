<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $plan = Plan::where('user_id', $user->id)->first();
        $friends = $user->friends ?? collect([]);
        
        return view('plans.weekplanpage', compact('plan', 'friends'));
    }

    /**
     * Store a new gym plan or update an existing one.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'week' => 'required|array',
            'week.*' => 'boolean', // Each day should be a boolean
        ]);

        $plan = Plan::updateOrCreate(
            ['user_id' => $validated['user_id']],
            ['week' => $validated['week'], 'locked' => true]
        );

        return response()->json(['message' => 'Plan saved successfully', 'plan' => $plan]);
    }

    /**
     * Check a specific day in the gym plan and update the streak.
     */
    public function checkDay(Request $request, $id)
    {
        $validated = $request->validate([
            'day_index' => 'required|integer|min:0|max:6', // Ensure valid day index (0-6)
        ]);

        $plan = Plan::findOrFail($id);
        $user = User::findOrFail($plan->user_id);

        // Check if the day is already marked as completed
        if ($plan->week[$validated['day_index']] === true) {
            return response()->json(['message' => 'Day already checked'], 400);
        }

        // Mark the day as completed
        $plan->week[$validated['day_index']] = true;
        $plan->save();

        // Update the user's streak
        if (in_array(false, $plan->week)) {
            $user->streak = 0; // Reset streak if any day is missed
        } else {
            $user->streak += 1; // Increment streak if all days are completed
        }
        $user->save();

        return response()->json(['message' => 'Day checked successfully', 'streak' => $user->streak]);
    }
}
