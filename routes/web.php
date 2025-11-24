<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolDocumentController;
use App\Http\Controllers\SchoolFacilityController;
use App\Http\Controllers\SchoolStudentStatController;
use App\Http\Controllers\SchoolTeacherController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\SchoolVisitController;
use App\Http\Controllers\AdminVisitController;
use App\Http\Controllers\PengawasVisitController;
use App\Http\Controllers\PengawasEvaluationController;
use App\Http\Controllers\SchoolEvaluationReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengawasController;
use App\Http\Controllers\AdminAccountController; // <--- ini yang baru
use Illuminate\Support\Facades\Route;


Route::get('/', fn () => view('welcome'));

Route::middleware(['auth'])->group(function (){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {

    // --- PROFILE (umum) ---
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================
    // ROLE: SEKOLAH
    // =========================
    Route::middleware('role:sekolah')->group(function () {
        // Panel sekolah & profil
        Route::get('/sekolah/panel', [SchoolController::class,'mySchool'])->name('schools.my');
        Route::match(['PUT','POST'], '/sekolah/profile', [SchoolController::class,'updateProfile'])
            ->name('schools.profile.update');

        // Data guru
        Route::post('/sekolah/teachers',                [SchoolTeacherController::class,'store'])->name('teachers.store');
        Route::put('/sekolah/teachers/{teacher}',       [SchoolTeacherController::class,'update'])->name('teachers.update');
        Route::delete('/sekolah/teachers/{teacher}',    [SchoolTeacherController::class,'destroy'])->name('teachers.destroy');
        Route::get('/sekolah/teachers', fn () => redirect()->route('schools.my'));

        // Statistik siswa
        Route::post('/sekolah/student-stats', [SchoolStudentStatController::class,'store'])->name('student-stats.store');
        Route::get('/sekolah/student-stats', fn () => redirect()->route('schools.my')); // alias GET

        // Dokumen & sarpras
        Route::post('/sekolah/documents',                       [SchoolDocumentController::class,'store'])->name('documents.store');
        Route::delete('/sekolah/documents/{document}',          [SchoolDocumentController::class,'destroy'])->name('documents.destroy');
        Route::post('/sekolah/facilities',                      [SchoolFacilityController::class,'store'])->name('facilities.store');
        Route::delete('/sekolah/facilities/{facility}',         [SchoolFacilityController::class,'destroy'])->name('facilities.destroy');

        // Pengajuan / daftar visitasi sekolah
        Route::get('/sekolah/visitasi',  [SchoolVisitController::class,'index'])->name('schools.visits.index');
        Route::post('/sekolah/visitasi', [SchoolVisitController::class,'store'])->name('schools.visits.store');

        // Laporan penilaian (role sekolah)
        Route::get('/sekolah/laporan-visitasi',                 [SchoolEvaluationReportController::class, 'me'])->name('schools.report.me');
        Route::get('/sekolah/laporan-visitasi/{evaluation}',    [SchoolEvaluationReportController::class, 'showByEvaluation'])->name('schools.report.by_evaluation');
    });

    // =========================
    // ROLE: PENGAWAS
    // =========================
    Route::middleware('role:pengawas')->group(function () {

        // Visitasi pengawas (jadwal, terima/tolak, selesai)
        Route::get('/pengawas/visitasi',                        [PengawasVisitController::class,'index'])->name('pengawas.visits.index');
        Route::post('/pengawas/visitasi/{visit}/accept',        [PengawasVisitController::class,'accept'])->name('pengawas.visits.accept');
        Route::post('/pengawas/visitasi/{visit}/decline',       [PengawasVisitController::class,'decline'])->name('pengawas.visits.decline');
        Route::post('/pengawas/visitasi/{visit}/complete',      [PengawasVisitController::class,'complete'])->name('pengawas.visits.complete');

        // Penilaian (form, simpan, laporan)
        Route::get('/pengawas/penilaian/{school}',              [PengawasEvaluationController::class,'create'])->name('pengawas.evaluations.create');
        Route::post('/pengawas/penilaian/{school}',             [PengawasEvaluationController::class,'store'])->name('pengawas.evaluations.store');
        Route::get('/pengawas/penilaian/{school}/laporan',      [PengawasEvaluationController::class,'report'])->name('pengawas.evaluations.report');
    });

    // =========================
    // ROLE: ADMIN
    // =========================
    Route::middleware('role:admin')->group(function () {
        // Manajemen user
        Route::get('/admin/users',                [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::post('/admin/users',               [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::delete('/admin/users/{user}',      [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

        // Manajemen data pengawas
        Route::get('/admin/pengawas',                [PengawasController::class, 'index'])->name('admin.pengawas.index');
        Route::post('/admin/pengawas',               [PengawasController::class, 'store'])->name('admin.pengawas.store');
        Route::delete('/admin/pengawas/{pengawas}',  [PengawasController::class, 'destroy'])->name('admin.pengawas.destroy');

        // Manajemen visitasi
        Route::get('/admin/visitasi',                     [AdminVisitController::class,'index'])->name('admin.visits.index');
        Route::post('/admin/visitasi/{visit}/schedule',   [AdminVisitController::class,'schedule'])->name('admin.visits.schedule');
        Route::post('/admin/visitasi/{visit}/reject',     [AdminVisitController::class,'reject'])->name('admin.visits.reject');

        // Manajemen akun pengawas & sekolah (CRUD akun: tambah, simpan, edit, hapus)
        Route::get('/admin/accounts', [AdminAccountController::class, 'index'])
            ->name('admin.accounts.index');
        Route::post('/admin/accounts', [AdminAccountController::class, 'store'])
            ->name('admin.accounts.store');
        Route::get('/admin/accounts/{user}/edit', [AdminAccountController::class, 'edit'])
            ->name('admin.accounts.edit');
        Route::put('/admin/accounts/{user}', [AdminAccountController::class, 'update'])
            ->name('admin.accounts.update');
        Route::delete('/admin/accounts/{user}', [AdminAccountController::class, 'destroy'])
            ->name('admin.accounts.destroy');
    });

    Route::middleware('role:admin|pengawas')->group(function () {
        Route::get('/pengawas/sekolah',                 [SchoolController::class,'index'])->name('pengawas.schools.index');
        Route::get('/pengawas/sekolah/{school}',        [SchoolController::class,'show'])->name('pengawas.schools.show');
        Route::post('/pengawas/sekolah/{school}/approve',[VerificationController::class,'approve'])->name('pengawas.schools.approve');
        Route::post('/pengawas/sekolah/{school}/reject',[VerificationController::class,'reject'])->name('pengawas.schools.reject');
    });

});

require __DIR__.'/auth.php';
