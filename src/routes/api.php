<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(LoginController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/registrasi', 'register');
});

Route::middleware(['auth:sanctum', 'role:'.RoleEnum::ADMIN->value])->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/admin/logout', 'logout');
    });

    Route::controller(UserController::class)->group(function () {
        Route::post('/admin/registrasi', 'register');
        Route::get('/admin/all', 'all');
        Route::patch('/admin/user/{id}/edit/password', 'updatePassword');
        Route::patch('/admin/user/{id}/edit/role', 'updateRole');
    });
});

Route::middleware(['auth:sanctum', 'role:'.RoleEnum::VERIFIKATOR->value])->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/verifikator/logout', 'logout');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/verifikator/all', 'all');
        Route::patch('/verifikator/user/{id}/verifikasi', 'verifikasi');
    });

    Route::controller(PermissionController::class)->group(function () {
       Route::get('/verifikator/pengajuan', 'allVerifikator');
       Route::get('/verifikator/pengajuan/{id}/detail', 'detailVerifikator');
       Route::patch('/verifikator/pengajuan/{id}/edit', 'updateVerifikator');
    });
});

Route::middleware(['auth:sanctum', 'verified', 'role:'.RoleEnum::USER->value])->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/user/logout', 'logout');
    });

    Route::controller(UserController::class)->group(function () {
        Route::patch('/user/{id}/edit/password', 'updatePassword');
    });

    Route::controller(PermissionController::class)->group(function () {
       Route::get('/pengajuan', 'all');
       Route::post('/pengajuan/add', 'add');
       Route::get('/pengajuan/{id}/detail', 'detail');
       Route::patch('/pengajuan/{id}/edit', 'update');
       Route::delete('/pengajuan/{id}/delete', 'delete');
    });
});
