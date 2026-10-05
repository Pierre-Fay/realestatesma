<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Agent\LeadController;
use App\Http\Controllers\Agent\PropertyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyBrowseController;
use App\Http\Controllers\PropertyInquiryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/properties', [PropertyBrowseController::class, 'index'])->name('properties.index');
Route::get('/properties/{property:slug}', [PropertyBrowseController::class, 'show'])->name('properties.show');
Route::post('/properties/{property:slug}/inquiries', [PropertyInquiryController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('properties.inquiries.store');

Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::view('/about', 'pages.about')->name('about');
Route::view('/legal', 'pages.legal')->name('legal');
Route::view('/privacy', 'pages.privacy')->name('privacy');

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

    Route::get('/admin/leads', [AdminLeadController::class, 'index'])->name('admin.leads.index');
    Route::patch('/admin/leads/{lead}/assignment', [AdminLeadController::class, 'assign'])->name('admin.leads.assign');

    Route::get('/admin/properties', [AdminPropertyController::class, 'index'])->name('admin.properties.index');
    Route::patch('/admin/properties/{property}/approve', [AdminPropertyController::class, 'approve'])->name('admin.properties.approve');
    Route::patch('/admin/properties/{property}/unpublish', [AdminPropertyController::class, 'unpublish'])->name('admin.properties.unpublish');
    Route::delete('/admin/properties/{property}', [AdminPropertyController::class, 'destroy'])->name('admin.properties.destroy');
});

Route::middleware(['auth', 'verified', 'agent'])->group(function () {
    Route::get('/listings', [PropertyController::class, 'index'])->name('listings.index');
    Route::get('/listings/create', [PropertyController::class, 'create'])->name('listings.create');
    Route::post('/listings', [PropertyController::class, 'store'])->name('listings.store');
    Route::get('/listings/{property}/edit', [PropertyController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{property}', [PropertyController::class, 'update'])->name('listings.update');

    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::patch('/leads/{lead}/status', [LeadController::class, 'status'])->name('leads.status');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
