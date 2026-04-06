<?php

use App\Http\Controllers\Admin\BoilerplateController;
use App\Http\Controllers\InstallScriptController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class);

// Admin GUI
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [BoilerplateController::class, 'index'])->name('index');

    Route::get('/sail-services', [BoilerplateController::class, 'sailServices'])->name('sail-services');
    Route::post('/sail-services', [BoilerplateController::class, 'storeSailService'])->name('sail-services.store');
    Route::put('/sail-services/{service}', [BoilerplateController::class, 'updateSailService'])->name('sail-services.update');
    Route::get('/sail-services/{service}/edit', [BoilerplateController::class, 'editSailService'])->name('sail-services.edit');
    Route::patch('/sail-services/{service}/toggle', [BoilerplateController::class, 'toggleSailService'])->name('sail-services.toggle');
    Route::delete('/sail-services/{service}', [BoilerplateController::class, 'destroySailService'])->name('sail-services.destroy');

    Route::get('/files', [BoilerplateController::class, 'files'])->name('files');
    Route::post('/files', [BoilerplateController::class, 'storeFile'])->name('files.store');
    Route::put('/files/{file}', [BoilerplateController::class, 'updateFile'])->name('files.update');
    Route::get('/files/{file}/edit', [BoilerplateController::class, 'editFile'])->name('files.edit');
    Route::patch('/files/{file}/toggle', [BoilerplateController::class, 'toggleFile'])->name('files.toggle');
    Route::delete('/files/{file}', [BoilerplateController::class, 'destroyFile'])->name('files.destroy');

    Route::get('/composer-packages', [BoilerplateController::class, 'composerPackages'])->name('composer-packages');
    Route::post('/composer-packages', [BoilerplateController::class, 'storeComposerPackage'])->name('composer-packages.store');
    Route::patch('/composer-packages/{package}/toggle', [BoilerplateController::class, 'toggleComposerPackage'])->name('composer-packages.toggle');
    Route::delete('/composer-packages/{package}', [BoilerplateController::class, 'destroyComposerPackage'])->name('composer-packages.destroy');

    Route::get('/npm-packages', [BoilerplateController::class, 'npmPackages'])->name('npm-packages');
    Route::post('/npm-packages', [BoilerplateController::class, 'storeNpmPackage'])->name('npm-packages.store');
    Route::patch('/npm-packages/{package}/toggle', [BoilerplateController::class, 'toggleNpmPackage'])->name('npm-packages.toggle');
    Route::delete('/npm-packages/{package}', [BoilerplateController::class, 'destroyNpmPackage'])->name('npm-packages.destroy');

    Route::get('/docker-services', [BoilerplateController::class, 'dockerServices'])->name('docker-services');
    Route::post('/docker-services', [BoilerplateController::class, 'storeDockerService'])->name('docker-services.store');
    Route::put('/docker-services/{service}', [BoilerplateController::class, 'updateDockerService'])->name('docker-services.update');
    Route::get('/docker-services/{service}/edit', [BoilerplateController::class, 'editDockerService'])->name('docker-services.edit');
    Route::patch('/docker-services/{service}/toggle', [BoilerplateController::class, 'toggleDockerService'])->name('docker-services.toggle');
    Route::delete('/docker-services/{service}', [BoilerplateController::class, 'destroyDockerService'])->name('docker-services.destroy');

    Route::get('/commands', [BoilerplateController::class, 'commands'])->name('commands');
    Route::post('/commands', [BoilerplateController::class, 'storeCommand'])->name('commands.store');
    Route::get('/commands/{command}/edit', [BoilerplateController::class, 'editCommand'])->name('commands.edit');
    Route::put('/commands/{command}', [BoilerplateController::class, 'updateCommand'])->name('commands.update');
    Route::patch('/commands/{command}/toggle', [BoilerplateController::class, 'toggleCommand'])->name('commands.toggle');
    Route::delete('/commands/{command}', [BoilerplateController::class, 'destroyCommand'])->name('commands.destroy');
});

// Install script endpoint — must be last to avoid catching other routes
Route::get('/{appName}', InstallScriptController::class)
    ->where('appName', '[a-zA-Z0-9][a-zA-Z0-9\-]*')
    ->name('install-script');
