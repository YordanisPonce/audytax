<?php

use App\Http\Controllers\ChartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppsController;
use App\Http\Controllers\AuditoryTypeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\WidgetsController;
use App\Http\Controllers\SetLocaleController;
use App\Http\Controllers\ComponentsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GeneralSettingController;
use App\Http\Controllers\FaseController;
use App\Http\Controllers\QualityControlController;

require __DIR__ . '/auth.php';

Route::get('/', function () {
    return to_route('login');
});

Route::group(['middleware' => ['auth', 'verified']], function () {
    // Dashboards
    Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard.index');
    // Locale
    Route::get('setlocale/{locale}', SetLocaleController::class)->name('setlocale');

    // User
    Route::resource('users', UserController::class);
    // autitoryType
    Route::resource('auditoryTypes', AuditoryTypeController::class);
    Route::controller(AuditoryTypeController::class)->prefix('auditoryTypes')->as('auditoryTypes.')->group(function () {
        Route::get('fases/{id}', 'getFases')->name('fases');
    });

    // fase
    Route::resource('fases', FaseController::class);
    // document
    Route::resource('documents', DocumentController::class);
    Route::controller(DocumentController::class)->prefix('documents')->as('documents.')->group(function () {
        Route::get('download/{document}', 'download')->name('download');
        Route::get('by-fase/{faseId}', 'getDocumentsByFaseId')->name('by-fase');
        Route::get('mark-as-complete/{document}', 'markAsComplete')->name('mark-as-complete');
        Route::get('cancel-document/{document}', 'cancelDocument')->name('cancel-document');
        Route::get('approve-document/{document}', 'approveDocument')->name('approve-document');
        Route::get('reject-document/{document}', 'rejectDocument')->name('reject-document');
        Route::post('by-fase/{faseId}', 'saveFiles')->name('save-files');
    });

    // qualityControl
    Route::resource('qualityControls', QualityControlController::class);
    Route::controller(QualityControlController::class)->prefix('documents')->as('qualityControls.')->group(function () {
        Route::get('qualityControls/{qualityControl}', 'getDetails')->name('details');
    });
    // Permission
    Route::resource('permissions', PermissionController::class)->except(['show']);
    // Roles
    Route::resource('roles', RoleController::class);
    // Profiles
    Route::resource('profiles', ProfileController::class)->only(['index', 'update'])->parameter('profiles', 'user');
    // Env
    Route::singleton('general-settings', GeneralSettingController::class);
    Route::post('general-settings-logo', [GeneralSettingController::class, 'logoUpdate'])->name('general-settings.logo');

    // Database Backup
    Route::resource('database-backups', DatabaseBackupController::class);
    Route::get('database-backups-download/{fileName}', [DatabaseBackupController::class, 'databaseBackupDownload'])->name('database-backups.download');

    // Comments
    Route::resource('comments', CommentController::class);
    Route::controller(CommentController::class)->prefix('comments')->as('comments.')->group(function () { 
        Route::get('comments-by-fase/{fase}', 'getCommentsByFase')->name('get-by-fase');
    });
});
