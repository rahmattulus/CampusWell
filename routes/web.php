<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes (AUTH-01, AUTH-02, AUTH-03)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/login/staff', [AuthController::class, 'showStaffLoginForm'])->name('login.staff');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Pending Verification Notice (AUTH-05)
Route::get('/verification/notice', function () {
    return view('auth.pending-verification');
})->name('verification.notice')->middleware('auth');

// Protected Routes with RBAC Middleware (AUTH-04)
Route::middleware(['auth'])->group(function () {
    // Student Routes (Protected by role:student and verified_student)
    Route::prefix('student')->name('student.')->middleware(['role:student', 'verified_student'])->group(function () {
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

    // Doctor Routes (Protected by role:doctor)
    Route::prefix('doctor')->name('doctor.')->middleware(['role:doctor'])->group(function () {
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

    // Admin Routes (Protected by role:admin)
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
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
