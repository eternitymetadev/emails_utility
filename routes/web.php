<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmployeeController;

Route::get('/', [EmployeeController::class, 'showHomePage']);

Route::get('/upload', [EmployeeController::class, 'showUploadForm'])->name('employees.upload');
Route::post('/import', [EmployeeController::class, 'importEmployees'])->name('employees.import');
