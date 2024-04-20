<?php

use App\Http\Controllers\ADMIN\AdminController;
use App\Http\Controllers\AUTH\AuthController;
use App\Http\Controllers\AUTH\VerificationController;
use App\Http\Controllers\TESTcontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
 // correct way to use middleware auth
//Route::middleware('auth:sanctum')->get('test', [TESTController::class, 'test']);
//Route::get('test', [TESTController::class, 'test'])->middleware('auth:sanctum');


// correct way to use spatie by permissions
//    Route::prefix('spatie')->controller(TESTcontroller::class)
//        ->group(function (){
//
//            Route::get('/test','test')
//                ->middleware('can:delete-user');
//        });


// correct way to use spatie by roles
//Route::group(['middleware' => ['role:Super Admin']], function () {
//
//    Route::get('test',[TESTcontroller::class,'test']);
//});

// correct way to use spatie by permission
//Route::group(['middleware' => ['permission:create-user']], function (){
//    Route::get('test',[TESTcontroller::class,'test']);
//}
//);


// correct way to use spatie by permission and role
//    Route::group(['middleware' => ['role_or_permission:delete-user']], function () {
//        Route::get('test',[TESTcontroller::class,'test']);
//    });




// correct way to use spatie by multiple permission and role
//    Route::group(['middleware' => ['role_or_permission:delete-user|Admin']], function () {
//        Route::get('test',[TESTcontroller::class,'test']);
//    });






Route::post('register',[AuthController::class,'register']);
Route::post('AdminRegister',[AuthController::class,'AdminRegister']);
Route::post('login',[AuthController::class,'login']);
Route::post('AdminLogin',[AuthController::class,'AdminLogin']);



//لارسال رمز التحقق
Route::post('/send-verification-email', [VerificationController::class, 'sendVerificationEmail']);
//للتحقق من رمز التحقق
Route::post('/verify', [VerificationController::class, 'verify']);

Route::post('/send-password-reset-email', [VerificationController::class, 'sendPasswordResetEmail']);
Route::post('/reset-password', [VerificationController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/change-password', [VerificationController::class, 'changePassword']);
});

Route::middleware('auth:sanctum')->group(function () {


    Route::group(['middleware' => ['role_or_permission:Admin|Normal User']], function () {

        Route::post('update',[AuthController::class,'update']);
    });





    ################ ADMIN ROUTE ###############
    Route::group(['middleware' => ['role:Super Admin']], function () {
        Route::post('AdminUpdate',[AuthController::class,'AdminUpdate']);
        Route::post('AddClub',[AdminController::class,'AddClub']);
        Route::post('AddHealthCare',[AdminController::class,'AddHealthCare']);
        Route::post('editClub',[AdminController::class,'editClub']);
        Route::post('editHealth_care',[AdminController::class,'editHealth_care']);


    });

});


