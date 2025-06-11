<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutLock extends Model
{
    protected $table = 'workout_locks';

    protected $fillable = [
        'user_id',
        'schedule',
        'locked_days',
    ];

    protected $casts = [
        'schedule' => 'array',
        'locked_days' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
