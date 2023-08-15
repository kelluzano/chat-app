<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

Route::get('login', function () {
    return redirect('/');
});
Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'authenticate'])->name('login');
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'destroy'])->name('logout')->middleware('auth');


Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    });

    Route::get('/sessions', [\App\Http\Controllers\SessionController::class, 'list'])->name('sessions.list');

    Route::group(['prefix' => 'messages'], function (){
        Route::get('/', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
       
    });

    Route::group([
        'prefix' => 'system-settings',
        'middleware' => ['role:admin']
    ], function () {
        Route::get('users', [\App\Http\Controllers\UsersController::class, 'index'])->name('users.index');
        Route::get('users/create', [\App\Http\Controllers\UsersController::class, 'create'])->name('users.create');
        Route::get('users/edit/{id}', [\App\Http\Controllers\UsersController::class, 'edit'])->name('users.edit');
        Route::post('users/update/{id}', [\App\Http\Controllers\UsersController::class, 'update'])->name('users.update');
        Route::get('users/delete/{user}', [\App\Http\Controllers\UsersController::class, 'destroy'])->name('users.destroy');

        Route::get('roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [\App\Http\Controllers\RoleController::class, 'create'])->name('roles.create');
        Route::post('roles/save', [\App\Http\Controllers\RoleController::class, 'save'])->name('roles.save');
        Route::get('roles/edit/{id}', [\App\Http\Controllers\RoleController::class, 'edit'])->name('roles.edit');
        Route::post('roles/update/{id}', [\App\Http\Controllers\RoleController::class, 'update'])->name('roles.update');
        Route::get('roles/delete/{id}', [\App\Http\Controllers\RoleController::class, 'destroy'])->name('roles.destroy');

        Route::get('permissions', [\App\Http\Controllers\PermissionController::class, 'index'])->name('permissions.index');
        Route::get('permissions/create', [\App\Http\Controllers\PermissionController::class, 'create'])->name('permissions.create');
        Route::post('permissions/save', [\App\Http\Controllers\PermissionController::class, 'save'])->name('permissions.save');
        Route::get('permissions/edit/{id}', [\App\Http\Controllers\PermissionController::class, 'edit'])->name('permissions.edit');
        Route::post('permissions/update/{id}', [\App\Http\Controllers\PermissionController::class, 'update'])->name('permissions.update');
        Route::get('permissions/delete/{id}', [\App\Http\Controllers\PermissionController::class, 'destroy'])->name('permissions.destroy');
    });
});
