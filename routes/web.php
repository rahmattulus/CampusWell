<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes (AUTH-01)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes (Placeholders for role redirection)
Route::middleware('auth')->group(function () {
    // Student Routes
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/home', function () {
            return view('student.home');
        })->name('home');

        Route::get('/history', function () {
            return view('student.history');
        })->name('history');

        Route::get('/consultation', function () {
            return view('student.consultation');
        })->name('consultation');

        Route::get('/profile', function () {
            return view('student.profile');
        })->name('profile');
    });

    // Doctor Routes
    Route::prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', function () {
            return view('doctor.dashboard');
        })->name('dashboard');

        Route::get('/requests', function () {
            return view('doctor.requests');
        })->name('requests');

        Route::get('/consultation', function () {
            return view('doctor.consultation');
        })->name('consultation');

        Route::get('/patients', function () {
            return view('doctor.patients');
        })->name('patients');

        Route::get('/schedule', function () {
            return view('doctor.schedule');
        })->name('schedule');
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('/users', function () {
            return view('admin.users');
        })->name('users');

        Route::get('/cds-rules', function () {
            return view('admin.cds-rules');
        })->name('cds-rules');

        Route::get('/audit-log', function () {
            return view('admin.audit-log');
        })->name('audit-log');

        Route::get('/system', function () {
            return view('admin.system');
        })->name('system');
    });
});
