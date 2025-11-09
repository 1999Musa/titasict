<?php

use Illuminate\Support\Facades\Route;

// ✅ Import all controllers
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\BatchDayController;
use App\Http\Controllers\Admin\BatchTimeController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\PaymentController;

// ✅ Default routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// ✅ Single unified admin route group
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {


        Route::get('/students/ex', [StudentController::class, 'exStudents'])->name('students.ex');
        Route::post('/students/move-to-ex', [StudentController::class, 'moveToEx'])->name('students.moveToEx');

        Route::delete('/payments/bulk-delete', [PaymentController::class, 'bulkDelete'])
            ->name('payments.bulkDelete');
        Route::get('/payments/search-students', [PaymentController::class, 'searchStudents'])
            ->name('payments.searchStudents');

        Route::match(['get', 'post'], 'payments/save-pdf', [PaymentController::class, 'storeAndPrintPdf'])
            ->name('payments.savePdf');

        // Batch Management
        Route::resource('batches', \App\Http\Controllers\Admin\BatchController::class);
        Route::resource('batch-days', \App\Http\Controllers\Admin\BatchDayController::class);
        Route::resource('batch-times', \App\Http\Controllers\Admin\BatchTimeController::class);
        Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);

        Route::resource('payments', App\Http\Controllers\Admin\PaymentController::class)->except(['show']);

        // Admin group (inside ->group(function () { ... }))
        Route::get('payments/months/{student}', [PaymentController::class, 'getMonths'])
            ->name('admin.payments.months');
        Route::resource('/admin/payments', App\Http\Controllers\Admin\PaymentController::class);




        Route::get('/students/{student}/pdf', [StudentController::class, 'generatePdf'])
            ->name('students.pdf');

    });


// ✅ Authentication routes
require __DIR__ . '/auth.php';
