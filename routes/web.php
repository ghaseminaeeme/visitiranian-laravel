<?php

use App\Http\Controllers\Web\AppointmentController;
use App\Http\Controllers\Web\CityController;
use App\Http\Controllers\Web\DoctorController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\RobotsController;
use App\Http\Controllers\Web\ShortLinkController;
use App\Http\Controllers\Web\SpecialtyController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
Route::get('/d/{doctor:slug}', [DoctorController::class, 'show'])->name('doctors.show');

Route::get('/t/{specialty:slug}', [SpecialtyController::class, 'show'])->name('specialties.show');

Route::get('/sh/{city:slug}', [CityController::class, 'show'])->name('cities.show');
Route::get('/sh/{city:slug}/t/{specialty:slug}', [CityController::class, 'specialty'])->name('cities.specialty');

Route::get('/p/{page:slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/s/{code}', [ShortLinkController::class, 'redirect'])->name('short-links.redirect');

Route::get('/peygiri', [AppointmentController::class, 'peygiriForm'])->name('peygiri');
Route::post('/peygiri', [AppointmentController::class, 'peygiri'])->name('peygiri.submit');
Route::get('/n/{trackingCode}', [AppointmentController::class, 'trackByCode'])->name('appointments.track.code');

Route::prefix('appointments')->name('appointments.')->group(function (): void {
    Route::get('/track', [AppointmentController::class, 'trackForm'])->name('track');
    Route::post('/track', [AppointmentController::class, 'track'])->name('track.submit');
    Route::post('/cancel', [AppointmentController::class, 'cancel'])->name('cancel');
    Route::get('/peygiri', fn () => redirect()->route('peygiri'));
});

Route::post('/d/{doctor:slug}/appointments', [AppointmentController::class, 'book'])->name('appointments.book');
Route::get('/d/{doctor:slug}/appointments/slots', [AppointmentController::class, 'slots'])->name('appointments.slots');

Route::get('/robots.txt', RobotsController::class)->name('robots');
