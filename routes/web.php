<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\WeekPlanController;
use Illuminate\Support\Facades\Route;

// Public routes with no auth check
Route::get('/', function () {
    return view('homepage');
})->name('homepage');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/weekplan', [PlanController::class, 'index'])->name('weekplan');
    Route::post('/plans/check-day', [PlanController::class, 'checkDay'])->name('plans.checkDay');
    
    Route::get('/settings', [UserController::class, 'settings'])->name('settings');
    Route::post('/settings/password', [UserController::class, 'updatePassword'])->name('settings.password');

    Route::get('/friends/search', [FriendController::class, 'search'])->name('friends.search');
    Route::post('/friends/add', [FriendController::class, 'addFriend'])->name('friends.add');
    Route::delete('/friends/{id}/remove', [FriendController::class, 'removeFriend'])->name('friends.remove');
    Route::post('/lock-workout-schedule', [FriendController::class, 'lockWorkoutSchedule'])
    ->middleware('auth')->name('lock.schedule');

    Route::get('/weekplan', [WeekPlanController::class, 'index'])->name('weekplan');
    Route::post('/weekplan/update', [WeekPlanController::class, 'update']);
    Route::post('/weekplan/lock', [WeekPlanController::class, 'lock']);

    Route::get('/workout/current-day', [WeekPlanController::class, 'getCurrentDayWorkout']);
    Route::post('/workout/complete', [WeekPlanController::class, 'completeWorkout']);
    Route::get('/workout/day-status/{day}', [WeekPlanController::class, 'getDayStatus']);

    // Admin routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/adminspage', [AdminController::class, 'dashboard'])->name('adminspage');
        Route::delete('/adminspage/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
        Route::patch('/adminspage/users/{id}/name', [AdminController::class, 'updateUserName'])->name('admin.updateName');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/friends', [FriendController::class, 'index'])->name('friends');
        Route::get('/search-users', [UserController::class, 'searchUsers'])->name('users.search');
        Route::post('/friends/add', [FriendController::class, 'addFriend'])->name('friends.add');
    });
});