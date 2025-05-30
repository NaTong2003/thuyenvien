<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\TestController as AdminTestController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Seafarer\DashboardController as SeafarerDashboardController;
use App\Http\Controllers\Seafarer\TestController as SeafarerTestController;
use App\Http\Controllers\Seafarer\ProfileController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Seafarer\CertificateController as SeafarerCertificateController;
use App\Http\Controllers\Admin\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Trang chủ
Route::get('/', function () {
    return view('welcome');
});

// Routes xác thực
Route::get('/register', [AuthController::class, 'registerForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/login', [AuthController::class, 'loginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes cho Admin
Route::prefix('admin')->middleware(['auth', 'isAdmin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Quản lý người dùng
    Route::resource('users', UserController::class);
    
    // Quản lý câu hỏi
    Route::resource('questions', QuestionController::class);
    Route::get('/questions/count/ajax', [QuestionController::class, 'count'])->name('questions.count');
    Route::get('/questions/export/template', [QuestionController::class, 'exportTemplate'])->name('questions.export.template');
    Route::post('/questions/import', [QuestionController::class, 'import'])->name('questions.import');
    
    // Quản lý bài kiểm tra
    Route::resource('tests', AdminTestController::class);
    Route::get('/tests/{test}/questions', [AdminTestController::class, 'questions'])->name('tests.questions');
    Route::post('/tests/{test}/questions', [AdminTestController::class, 'storeQuestions'])->name('tests.questions.store');
    Route::get('/tests/{test}/preview', [AdminTestController::class, 'preview'])->name('tests.preview');
    Route::get('/tests/{test}/statistics', [AdminTestController::class, 'statistics'])->name('tests.statistics');
    Route::patch('/tests/{test}/toggle', [AdminTestController::class, 'toggle'])->name('tests.toggle');
    
    // Tạo bài kiểm tra ngẫu nhiên
    Route::get('/tests/random/create', [AdminTestController::class, 'createRandom'])->name('tests.random.create');
    Route::post('/tests/random/store', [AdminTestController::class, 'storeRandom'])->name('tests.random.store');
    
    // Báo cáo và thống kê
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/performance', [ReportController::class, 'performance'])->name('reports.performance');
    Route::get('/reports/test/{test}', [ReportController::class, 'testReport'])->name('reports.test');
    Route::get('/reports/seafarer/{user}', [ReportController::class, 'seafarerReport'])->name('reports.seafarer');
    Route::get('/reports/attempt/{testAttempt}', [ReportController::class, 'attemptReport'])->name('reports.attempt');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/search', [ReportController::class, 'search'])->name('reports.search');
    
    // Quản lý danh mục
    Route::resource('categories', CategoryController::class);
    
    // Chấm điểm bài thi
    Route::get('/marking', [ReportController::class, 'markingPage'])->name('reports.marking');
    Route::get('/marking/{attempt}', [ReportController::class, 'markAttempt'])->name('reports.mark.attempt');
    Route::post('/marking/{attempt}', [ReportController::class, 'saveMarking'])->name('reports.save.marking');
    
    // Quản lý chứng chỉ
    Route::resource('certificates', CertificateController::class);
    Route::get('/certificates/create-from/{attempt}', [CertificateController::class, 'createFromAttempt'])->name('certificates.create.from.attempt');
    Route::post('/certificates/store-from/{attempt}', [CertificateController::class, 'storeFromAttempt'])->name('certificates.store.from.attempt');
    Route::get('/certificates/{certificate}/pdf', [CertificateController::class, 'generatePdf'])->name('certificates.pdf');
    Route::get('/test-history/{user}', [CertificateController::class, 'testHistory'])->name('certificates.test.history');
});

// Routes cho Thuyền viên
Route::prefix('seafarer')->middleware(['auth', 'isSeafarer'])->name('seafarer.')->group(function () {
    Route::get('/dashboard', [SeafarerDashboardController::class, 'index'])->name('dashboard');
    
    // Làm bài kiểm tra
    Route::get('/tests', [SeafarerTestController::class, 'index'])->name('tests.index');
    Route::get('/tests/{test}', [SeafarerTestController::class, 'show'])->name('tests.show');
    Route::get('/tests/{test}/start', [SeafarerTestController::class, 'start'])->name('tests.start');
    Route::post('/tests/{test}/submit', [SeafarerTestController::class, 'submit'])->name('tests.submit');
    Route::get('/tests/{testAttempt}/result', [SeafarerTestController::class, 'result'])->name('tests.result');
    
    // Quản lý chứng chỉ
    Route::get('/certificates', [SeafarerCertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/{certificate}', [SeafarerCertificateController::class, 'show'])->name('certificates.show');
    Route::get('/certificates/{certificate}/download', [SeafarerCertificateController::class, 'download'])->name('certificates.download');
    
    // Quản lý hồ sơ
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
