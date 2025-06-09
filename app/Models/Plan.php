<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['user_id', 'name', 'description'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'week' => 'array', // Cast the week attribute to an array
    ];

    /**
     * Get the user that owns the plan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
