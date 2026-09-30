<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Agent\PropertyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyBrowseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/properties', [PropertyBrowseController::class, 'index'])->name('properties.index');
Route::get('/properties/{property:slug}', [PropertyBrowseController::class, 'show'])->name('properties.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::patch('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');

    Route::get('/admin/agents', [AgentController::class, 'index'])->name('admin.agents.index');
    Route::get('/admin/agents/create', [AgentController::class, 'create'])->name('admin.agents.create');
    Route::post('/admin/agents', [AgentController::class, 'store'])->name('admin.agents.store');
});

Route::middleware(['auth', 'verified', 'agent'])->group(function () {
    Route::get('/listings', [PropertyController::class, 'index'])->name('listings.index');
    Route::get('/listings/create', [PropertyController::class, 'create'])->name('listings.create');
    Route::post('/listings', [PropertyController::class, 'store'])->name('listings.store');
    Route::get('/listings/{property}/edit', [PropertyController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{property}', [PropertyController::class, 'update'])->name('listings.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
