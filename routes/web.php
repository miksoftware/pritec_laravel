<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleTypeController;
use App\Http\Controllers\VehicleSectionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ExpertiseController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\MigrationController;
use Illuminate\Support\Facades\Route;

// Installer routes (no auth, no install check)
Route::withoutMiddleware(\App\Http\Middleware\CheckInstalled::class)->group(function () {
    Route::get('/install', [InstallController::class, 'index'])->name('install.index');
    Route::post('/install/test-connection', [InstallController::class, 'testConnection'])->name('install.test');
    Route::post('/install/process', [InstallController::class, 'process'])->name('install.process');
});

// Redirect root to dashboard or login
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.process');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users CRUD
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Vehicle Types CRUD
    Route::get('/vehicle-types', [VehicleTypeController::class, 'index'])->name('vehicle-types.index');
    Route::get('/vehicle-types/create', [VehicleTypeController::class, 'create'])->name('vehicle-types.create');
    Route::post('/vehicle-types', [VehicleTypeController::class, 'store'])->name('vehicle-types.store');
    Route::get('/vehicle-types/{vehicleType}/edit', [VehicleTypeController::class, 'edit'])->name('vehicle-types.edit');
    Route::put('/vehicle-types/{vehicleType}', [VehicleTypeController::class, 'update'])->name('vehicle-types.update');
    Route::delete('/vehicle-types/{vehicleType}', [VehicleTypeController::class, 'destroy'])->name('vehicle-types.destroy');
    Route::post('/vehicle-types/{vehicleType}/toggle-status', [VehicleTypeController::class, 'toggleStatus'])->name('vehicle-types.toggle-status');

    // Vehicle Sections & Pieces
    Route::get('/vehicle-types/{vehicleType}/sections', [VehicleSectionController::class, 'index'])->name('vehicle-types.sections');
    Route::post('/vehicle-sections/upload-image', [VehicleSectionController::class, 'uploadImage'])->name('vehicle-sections.upload-image');
    Route::get('/vehicle-sections/{section}/pieces', [VehicleSectionController::class, 'pieces'])->name('vehicle-sections.pieces');
    Route::post('/vehicle-sections/add-piece', [VehicleSectionController::class, 'addPiece'])->name('vehicle-sections.add-piece');
    Route::post('/vehicle-sections/update-piece', [VehicleSectionController::class, 'updatePiece'])->name('vehicle-sections.update-piece');
    Route::post('/vehicle-sections/update-piece-position', [VehicleSectionController::class, 'updatePiecePosition'])->name('vehicle-sections.update-piece-position');
    Route::post('/vehicle-sections/delete-piece', [VehicleSectionController::class, 'deletePiece'])->name('vehicle-sections.delete-piece');
    Route::post('/vehicle-sections/clear-pieces', [VehicleSectionController::class, 'clearPieces'])->name('vehicle-sections.clear-pieces');

    // Clients CRUD
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::get('/clients/export', [ClientController::class, 'export'])->name('clients.export');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::post('/clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])->name('clients.toggle-status');

    // Expertise (Peritajes)
    Route::get('/expertise', [ExpertiseController::class, 'index'])->name('expertise.index');
    Route::get('/expertise/create', [ExpertiseController::class, 'create'])->name('expertise.create');
    Route::post('/expertise', [ExpertiseController::class, 'store'])->name('expertise.store');
    Route::get('/expertise/{expertise}/step/{step}', [ExpertiseController::class, 'step'])->name('expertise.step');
    Route::post('/expertise/{expertise}/step/{step}/save', [ExpertiseController::class, 'saveStep'])->name('expertise.save-step');
    Route::post('/expertise/{expertise}/complete', [ExpertiseController::class, 'complete'])->name('expertise.complete');
    Route::get('/expertise/{expertise}/pdf', [ExpertiseController::class, 'generatePdf'])->name('expertise.pdf');
    Route::get('/expertise/{expertise}', [ExpertiseController::class, 'show'])->name('expertise.show');
    Route::delete('/expertise/{expertise}', [ExpertiseController::class, 'destroy'])->name('expertise.destroy');

    // Expertise AJAX
    Route::get('/expertise-ajax/search-clients', [ExpertiseController::class, 'searchClients'])->name('expertise.search-clients');
    Route::get('/expertise-ajax/search-vehicle-types', [ExpertiseController::class, 'searchVehicleTypes'])->name('expertise.search-vehicle-types');
    Route::get('/expertise-ajax/get-pieces', [ExpertiseController::class, 'getPieces'])->name('expertise.get-pieces');
    Route::get('/expertise-ajax/get-concepts', [ExpertiseController::class, 'getConcepts'])->name('expertise.get-concepts');

    // Migration (admin only)
    Route::get('/migration', [MigrationController::class, 'index'])->name('migration.index');
    Route::post('/migration/process', [MigrationController::class, 'process'])->name('migration.process');
});
