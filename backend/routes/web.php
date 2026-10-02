<?php

use App\Http\Controllers\Admin as Admin;
use App\Http\Controllers\ApplicationRemarkController;
use App\Http\Controllers\Citizen as Citizen;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('citizen.dashboard');
    }
    return redirect()->route('login');
})->name('welcome');

// Public Application & Certificate Verification Page by ID (KABD203, GOV-...)
Route::get('/verify', [Citizen\BranchController::class, 'verify'])->name('public.verify');

/*
|--------------------------------------------------------------------------
| Role-based Dashboard Redirect
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('citizen.dashboard');
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Application Remarks & Conversation (AJAX)
    Route::get('/applications/{application}/remarks', [ApplicationRemarkController::class, 'index'])->name('applications.remarks.index');
    Route::post('/applications/{application}/remarks', [ApplicationRemarkController::class, 'store'])->name('applications.remarks.store');
});

/*
|--------------------------------------------------------------------------
| Admin Module Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('departments', Admin\DepartmentController::class);
        Route::resource('services', Admin\ServiceController::class);

        Route::get('/applications', [Admin\ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}', [Admin\ApplicationController::class, 'show'])->name('applications.show');
        Route::patch('/applications/{application}/status', [Admin\ApplicationController::class, 'updateStatus'])->name('applications.status');
        Route::post('/applications/{application}/documents/{document}/request-replacement', [Admin\ApplicationController::class, 'requestDocumentReplacement'])->name('applications.documents.request-replacement');

        Route::resource('qr-codes', Admin\QrCodeController::class)->names('qr-codes');
        Route::patch('/qr-codes/{qrCode}/toggle-status', [Admin\QrCodeController::class, 'toggleStatus'])->name('qr-codes.toggle-status');

        Route::get('/citizens', [Admin\CitizenController::class, 'index'])->name('citizens.index');
        Route::get('/citizens/{citizen}', [Admin\CitizenController::class, 'show'])->name('citizens.show');

        Route::resource('notices', Admin\NoticeController::class);

        Route::get('/feedback', [Admin\FeedbackController::class, 'index'])->name('feedback.index');
        Route::get('/feedback/{feedback}', [Admin\FeedbackController::class, 'show'])->name('feedback.show');
        Route::patch('/feedback/{feedback}/reply', [Admin\FeedbackController::class, 'reply'])->name('feedback.reply');
    });

/*
|--------------------------------------------------------------------------
| Citizen Module Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'citizen', 'citizen.verified'])
    ->prefix('citizen')
    ->name('citizen.')
    ->group(function () {
        Route::get('/dashboard', [Citizen\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/services', [Citizen\ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/{service}', [Citizen\ServiceController::class, 'show'])->name('services.show');

        // Branch Explorer & Yojana Guidance Routes
        Route::get('/branches', [Citizen\BranchController::class, 'index'])->name('branches.index');
        Route::get('/branches/{department}', [Citizen\BranchController::class, 'show'])->name('branches.show');

        // Branch & Application Verification Portal
        Route::get('/verify', [Citizen\BranchController::class, 'verify'])->name('verify.index');

        Route::resource('applications', Citizen\ApplicationController::class);
        Route::post('/applications/{application}/documents/{document}/replace', [Citizen\ApplicationController::class, 'replaceDocument'])->name('applications.documents.replace');

        Route::get('/payments/{application}', [Citizen\PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments/{application}', [Citizen\PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{application}/receipt', [Citizen\PaymentController::class, 'receipt'])->name('payments.receipt');

        Route::resource('feedback', Citizen\FeedbackController::class)->only(['index', 'create', 'store']);

        // Approved Documents & Certificates Repository
        Route::get('/approved-documents', [Citizen\ApprovedDocumentController::class, 'index'])->name('approved-documents.index');
        Route::get('/approved-documents/{application}/view', [Citizen\ApprovedDocumentController::class, 'viewDocument'])->name('approved-documents.view');
        Route::get('/approved-documents/{application}/download', [Citizen\ApprovedDocumentController::class, 'download'])->name('approved-documents.download');
    });

require __DIR__.'/auth.php';