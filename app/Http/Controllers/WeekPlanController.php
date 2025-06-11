<?php

namespace App\Http\Controllers;

use App\Models\WorkoutLock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WeekPlanController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $schedule = DB::table('workout_schedules')
            ->where('user_id', $userId)
            ->first();

        return view('weekplan', compact('schedule'));
    }

    public function update(Request $request)
    {
        $userId = Auth::id();
        
        // Check if schedule is locked
        $schedule = DB::table('workout_schedules')
            ->where('user_id', $userId)
            ->first();
            
        if ($schedule && $schedule->is_locked) {
            return response()->json(['error' => 'Schedule is locked'], 403);
        }

        $days = $request->only(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
        
        DB::table('workout_schedules')->updateOrInsert(
            ['user_id' => $userId],
            array_merge($days, ['updated_at' => now()])
        );

        return response()->json(['success' => true]);
    }

    public function lock(Request $request)
    {
        $userId = Auth::id();
        $days = $request->only(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
        $lockedDays = $request->input('locked_days', []);

        $workoutLock = WorkoutLock::updateOrCreate(
            ['user_id' => $userId],
            [
                'schedule' => $days,
                'locked_days' => $lockedDays,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Workout schedule locked successfully'
        ]);
    }

    public function getLockedDays()
    {
        $userId = Auth::id();
        $workoutLock = WorkoutLock::where('user_id', $userId)->first();
        
        return response()->json([
            'success' => true,
            'locked_days' => $workoutLock ? $workoutLock->locked_days : []
        ]);
    }

    public function getCurrentDayWorkout()
    {
        $userId = Auth::id();
        $currentDay = strtolower(Carbon::now()->format('l')); // Gets current day name
        
        $schedule = DB::table('workout_schedules')
            ->where('user_id', $userId)
            ->first();

        if (!$schedule || !$schedule->$currentDay) {
            return response()->json([
                'hasWorkout' => false,
                'day' => $currentDay,
                'completed' => false
            ]);
        }

        return response()->json([
            'hasWorkout' => true,
            'day' => $currentDay,
            'completed' => $schedule->{$currentDay . '_completed'}
        ]);
    }

    public function completeWorkout(Request $request)
    {
        $userId = Auth::id();
        $day = strtolower($request->input('day'));
        $completionColumn = $day . '_completed';

        if (!in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])) {
            return response()->json(['error' => 'Invalid day'], 400);
        }

        DB::table('workout_schedules')
            ->where('user_id', $userId)
            ->update([
                $completionColumn => true,
                'updated_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    public function getDayStatus($day)
    {
        $userId = Auth::id();
        $day = strtolower($day);
        
        if (!in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])) {
            return response()->json(['error' => 'Invalid day'], 400);
        }

        $schedule = DB::table('workout_schedules')
            ->where('user_id', $userId)
            ->first();

        return response()->json([
            'hasWorkout' => $schedule && $schedule->$day,
            'completed' => $schedule ? $schedule->{$day . '_completed'} : false
        ]);
    }
}
