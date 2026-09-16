<?php

use App\Core\Auth;
use App\Core\Router;

$router->get('/', [App\Controllers\HomeController::class, 'home']);
$router->get('/about', [App\Controllers\HomeController::class, 'about']);
$router->get('/academics', [App\Controllers\HomeController::class, 'academics']);
$router->get('/admissions', [App\Controllers\HomeController::class, 'admissions']);
$router->get('/gallery', [App\Controllers\HomeController::class, 'gallery']);
$router->get('/notices', [App\Controllers\HomeController::class, 'notices']);
$router->get('/contact', [App\Controllers\HomeController::class, 'contact']);
$router->post('/contact', [App\Controllers\MessageController::class, 'store']);
$router->post('/admissions/apply', [App\Controllers\AdmissionController::class, 'store']);

// ---------------- Admin: authentication ----------------
$router->get('/admin/login', [App\Controllers\Admin\AuthController::class, 'showLogin']);
$router->post('/admin/login', [App\Controllers\Admin\AuthController::class, 'login']);
$router->post('/admin/logout', [App\Controllers\Admin\AuthController::class, 'logout']);

// ---------------- Admin: dashboard ----------------
$router->get('/admin', [App\Controllers\Admin\DashboardController::class, 'index']);

// ---------------- Admin: content management ----------------
$router->get('/admin/settings', [App\Controllers\Admin\SettingController::class, 'edit']);
$router->post('/admin/settings', [App\Controllers\Admin\SettingController::class, 'update']);

$router->get('/admin/sliders', [App\Controllers\Admin\SliderController::class, 'index']);
$router->get('/admin/sliders/create', [App\Controllers\Admin\SliderController::class, 'create']);
$router->post('/admin/sliders', [App\Controllers\Admin\SliderController::class, 'store']);
$router->get('/admin/sliders/{id}/edit', [App\Controllers\Admin\SliderController::class, 'edit']);
$router->post('/admin/sliders/{id}', [App\Controllers\Admin\SliderController::class, 'update']);
$router->post('/admin/sliders/{id}/delete', [App\Controllers\Admin\SliderController::class, 'destroy']);

$router->get('/admin/teachers', [App\Controllers\Admin\TeacherController::class, 'index']);
$router->get('/admin/teachers/create', [App\Controllers\Admin\TeacherController::class, 'create']);
$router->post('/admin/teachers', [App\Controllers\Admin\TeacherController::class, 'store']);
$router->get('/admin/teachers/{id}/edit', [App\Controllers\Admin\TeacherController::class, 'edit']);
$router->post('/admin/teachers/{id}', [App\Controllers\Admin\TeacherController::class, 'update']);
$router->post('/admin/teachers/{id}/delete', [App\Controllers\Admin\TeacherController::class, 'destroy']);

$router->get('/admin/notices', [App\Controllers\Admin\NoticeController::class, 'index']);
$router->get('/admin/notices/create', [App\Controllers\Admin\NoticeController::class, 'create']);
$router->post('/admin/notices', [App\Controllers\Admin\NoticeController::class, 'store']);
$router->get('/admin/notices/{id}/edit', [App\Controllers\Admin\NoticeController::class, 'edit']);
$router->post('/admin/notices/{id}', [App\Controllers\Admin\NoticeController::class, 'update']);
$router->post('/admin/notices/{id}/delete', [App\Controllers\Admin\NoticeController::class, 'destroy']);

$router->get('/admin/events', [App\Controllers\Admin\EventController::class, 'index']);
$router->get('/admin/events/create', [App\Controllers\Admin\EventController::class, 'create']);
$router->post('/admin/events', [App\Controllers\Admin\EventController::class, 'store']);
$router->get('/admin/events/{id}/edit', [App\Controllers\Admin\EventController::class, 'edit']);
$router->post('/admin/events/{id}', [App\Controllers\Admin\EventController::class, 'update']);
$router->post('/admin/events/{id}/delete', [App\Controllers\Admin\EventController::class, 'destroy']);

$router->get('/admin/gallery', [App\Controllers\Admin\GalleryController::class, 'index']);
$router->post('/admin/gallery/albums', [App\Controllers\Admin\GalleryController::class, 'storeAlbum']);
$router->post('/admin/gallery/albums/{id}/delete', [App\Controllers\Admin\GalleryController::class, 'destroyAlbum']);
$router->post('/admin/gallery/albums/{id}/photos', [App\Controllers\Admin\GalleryController::class, 'addPhotos']);
$router->post('/admin/gallery/photos/{id}/delete', [App\Controllers\Admin\GalleryController::class, 'destroyPhoto']);

$router->get('/admin/programs', [App\Controllers\Admin\ProgramController::class, 'index']);
$router->get('/admin/programs/create', [App\Controllers\Admin\ProgramController::class, 'create']);
$router->post('/admin/programs', [App\Controllers\Admin\ProgramController::class, 'store']);
$router->get('/admin/programs/{id}/edit', [App\Controllers\Admin\ProgramController::class, 'edit']);
$router->post('/admin/programs/{id}', [App\Controllers\Admin\ProgramController::class, 'update']);
$router->post('/admin/programs/{id}/delete', [App\Controllers\Admin\ProgramController::class, 'destroy']);

$router->get('/admin/facilities', [App\Controllers\Admin\ProgramController::class, 'facilityIndex']);
$router->get('/admin/facilities/create', [App\Controllers\Admin\ProgramController::class, 'facilityCreate']);
$router->post('/admin/facilities', [App\Controllers\Admin\ProgramController::class, 'facilityStore']);
$router->get('/admin/facilities/{id}/edit', [App\Controllers\Admin\ProgramController::class, 'facilityEdit']);
$router->post('/admin/facilities/{id}', [App\Controllers\Admin\ProgramController::class, 'facilityUpdate']);
$router->post('/admin/facilities/{id}/delete', [App\Controllers\Admin\ProgramController::class, 'facilityDestroy']);

$router->get('/admin/messages', [App\Controllers\Admin\MessageAdminController::class, 'index']);

// ---------------- Admin: admission enquiries ----------------
$router->get('/admin/admissions', [App\Controllers\Admin\AdmissionAdminController::class, 'index']);
$router->post('/admin/admissions/{id}/status', [App\Controllers\Admin\AdmissionAdminController::class, 'status']);
$router->post('/admin/admissions/{id}/delete', [App\Controllers\Admin\AdmissionAdminController::class, 'destroy']);
$router->post('/admin/messages/{id}/read', [App\Controllers\Admin\MessageAdminController::class, 'markRead']);
$router->post('/admin/messages/{id}/unread', [App\Controllers\Admin\MessageAdminController::class, 'markUnread']);
$router->post('/admin/messages/{id}/delete', [App\Controllers\Admin\MessageAdminController::class, 'destroy']);

$router->get('/admin/users', [App\Controllers\Admin\UserController::class, 'index']);
$router->get('/admin/users/create', [App\Controllers\Admin\UserController::class, 'create']);
$router->post('/admin/users', [App\Controllers\Admin\UserController::class, 'store']);
$router->post('/admin/users/{id}/delete', [App\Controllers\Admin\UserController::class, 'destroy']);

$router->get('/admin/profile', [App\Controllers\Admin\UserController::class, 'profile']);
$router->post('/admin/profile', [App\Controllers\Admin\UserController::class, 'updateProfile']);

// ---------------- Admin: Backup & Restore ----------------
$router->get('/admin/backup', [App\Controllers\Admin\BackupController::class, 'index']);
$router->post('/admin/backup/create', [App\Controllers\Admin\BackupController::class, 'create']);
$router->get('/admin/backup/download/{file}', [App\Controllers\Admin\BackupController::class, 'download']);
$router->post('/admin/backup/restore', [App\Controllers\Admin\BackupController::class, 'restore']);
$router->post('/admin/backup/{file}/delete', [App\Controllers\Admin\BackupController::class, 'destroy']);
