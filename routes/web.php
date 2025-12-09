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

// ---
## 🚀 Role-Based Admin Routes

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ====================================================================
        // 🔑 Group 1: Access for Super-Admin AND Staff (Index and Create)
        // This includes all view/read routes and creation routes.
        // ====================================================================

        Route::middleware(['role:super-admin,staff'])->group(function () {

            // --- Student Routes ---
            // Staff can view the index and special routes
            Route::get('students/ex', [StudentController::class, 'exStudents'])->name('students.ex');
            Route::post('students/move-to-ex', [StudentController::class, 'moveToEx'])->name('students.moveToEx');
            
            // Resource: Index and Create/Store for Students
            Route::get('students', [StudentController::class, 'index'])->name('students.index');
            Route::get('students/create', [StudentController::class, 'create'])->name('students.create');
            Route::post('students', [StudentController::class, 'store'])->name('students.store');
            
            Route::get('students/{student}/pdf', [StudentController::class, 'generatePdf'])->name('students.pdf');

            // --- Payment Routes (Index and Create) ---
            Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store']);
            
            // Special payment actions (accessible to both)
            Route::get('payments/search-students', [PaymentController::class, 'searchStudents'])->name('payments.searchStudents');
            Route::match(['get', 'post'], 'payments/save-pdf', [PaymentController::class, 'storeAndPrintPdf'])->name('payments.savePdf');
            Route::get('payments/months/{student}', [PaymentController::class, 'getMonths'])->name('payments.months');
            
            // --- Batch Management (Assuming both roles need to view/create these) ---
            Route::resource('batches', BatchController::class)->only(['index', 'create', 'store']);
            Route::resource('batch-days', BatchDayController::class)->only(['index', 'create', 'store']);
            Route::resource('batch-times', BatchTimeController::class)->only(['index', 'create', 'store']);
        });

        // ====================================================================
        // 🔒 Group 2: Access ONLY for Super-Admin (Show, Edit, Update, Destroy)
        // This includes all modification and deletion routes.
        // ====================================================================

        Route::middleware(['role:super-admin'])->group(function () {
            
            // --- Student Routes (Remaining Resource Actions) ---
            Route::resource('students', StudentController::class)->except(['index', 'create', 'store']);
            
            // --- Payment Routes (Remaining Resource Actions) ---
            Route::resource('payments', PaymentController::class)->except(methods: ['index', 'create', 'store']);
            
            // Bulk delete payment (Delete action)
            Route::delete('payments/bulk-delete', [PaymentController::class, 'bulkDelete'])->name('payments.bulkDelete');

            // --- Batch Management (Remaining Resource Actions) ---
            Route::resource('batches', BatchController::class)->except(['index', 'create', 'store']);
            Route::resource('batch-days', BatchDayController::class)->except(['index', 'create', 'store']);
            Route::resource('batch-times', BatchTimeController::class)->except(['index', 'create', 'store']);
        });

    });

// ✅ Authentication routes
require __DIR__ . '/auth.php';