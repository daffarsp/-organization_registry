<?php

use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RegistrationController::class, 'index'])->name('home');

Route::get('/pendaftaran', [RegistrationController::class, 'create'])->name('registrations.create');
Route::post('/pendaftaran', [RegistrationController::class, 'store'])
    ->middleware('throttle:registration')
    ->name('registrations.store');

Route::get('/pendaftaran/sukses/{registration}', [RegistrationController::class, 'success'])
    ->name('registrations.success');

Route::get('/pendaftaran/status', [RegistrationController::class, 'status'])
    ->name('registrations.status');
Route::post('/pendaftaran/status', [RegistrationController::class, 'checkStatus'])
    ->name('registrations.status.check');
Route::post('/pendaftaran/logout', [RegistrationController::class, 'logoutCandidate'])
    ->name('registrations.logout');

Route::get('/pendaftaran/{registration}/pertanyaan-dasar', [RegistrationController::class, 'showBasicQuestion'])
    ->name('registrations.basic-question');
Route::post('/pendaftaran/{registration}/pertanyaan-dasar', [RegistrationController::class, 'submitBasicQuestion'])
    ->name('registrations.basic-question.submit');

Route::get('/pendaftaran/{registration}/soal', [RegistrationController::class, 'showQuiz'])
    ->name('registrations.quiz');
Route::post('/pendaftaran/{registration}/soal', [RegistrationController::class, 'submitQuiz'])
    ->name('registrations.quiz.submit');
