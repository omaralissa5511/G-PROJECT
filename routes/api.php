<?php


use App\Http\Controllers\AdminController;
use App\Http\Controllers\AUTH\AuthController;
use App\Http\Controllers\AUTH\VerificationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\TrainerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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


    ############### ADMIN ROLE ###############
    Route::group(['middleware' => ['role_or_permission:ADMIN']], function () {

        Route::post('AdminUpdate', [AuthController::class, 'AdminUpdate']);


        Route::post('createCategory', [AdminController::class, 'createCategory']);
        Route::get('allCategory', [AdminController::class, 'getCategories']);
        Route::get('getCategory', [AdminController::class, 'getCategory']);
        Route::post('updateCategory', [AdminController::class, 'updateCategory']);
        Route::delete('deleteCategory', [AdminController::class, 'deleteCategory']);


        Route::post('AddClub', [AdminController::class, 'AddClub']);
        Route::get('showClubs', [AdminController::class, 'showClubs']);
        Route::delete('deleteClub/{userId}', [AdminController::class, 'deleteClub']);
        Route::get('searchClub/{clubID}', [AdminController::class, 'searchClub']);

        Route::get('allServices/{club_id}', [ServiceController::class, 'index']);
        Route::get('showService/{id}', [ServiceController::class, 'show']);
    });


    ################ CLUB ROLE ###############
    Route::group(['middleware' => ['role:CLUB']], function () {

        Route::post('editClub', [ClubController::class, 'editClub']);
        Route::get('MyClub', [ClubController::class, 'MyClub']);

        Route::get('MyTrainers', [ClubController::class, 'MyTrainers']);
        Route::post('AddTrainer', [ClubController::class, 'AddTrainer']);
        Route::delete('deleteTrainer/{id}', [ClubController::class, 'deleteTrainer']);

        Route::post('createService', [ServiceController::class, 'create']);
        Route::get('allServices/{club_id}', [ServiceController::class, 'index']);
        Route::get('showService/{id}', [ServiceController::class, 'show']);
        Route::post('updateService/{id}', [ServiceController::class, 'update']);
        Route::delete('deleteService/{id}', [ServiceController::class, 'destroy']);

        Route::post('createCourse', [CourseController::class, 'createCourse']);
        Route::get('MyCourses', [CourseController::class, 'MyCourses']);
        Route::get('getSpecificCourse/{id}', [CourseController::class, 'getSpecificCourse']);
        Route::post('editCourse/{CID}', [CourseController::class, 'editCourse']);
        Route::delete('deleteCourse/{id}', [CourseController::class, 'deleteCourse']);

        Route::post('createClass', [ClassController::class, 'createClass']);
        Route::get('getCourseClasses/{course_id}', [ClassController::class, 'getCourseClasses']);
        Route::post('editClass/{class_id}', [ClassController::class, 'editClass']);
        Route::delete('deleteClass/{id}', [ClassController::class, 'deleteClass']);



    });


    ################ TRAINER ROLE ###############
    Route::group(['middleware' => ['role:TRAINER']], function () {

        Route::post('editTrainer', [TrainerController::class, 'editTrainer']);
        Route::get('MyProfile', [TrainerController::class, 'MyProfile']);
    });


    ################## USER ROLE *******************
    Route::group(['middleware' => ['role_or_permission:USER']], function () {

        Route::get('allCategory', [AdminController::class, 'getCategories']);
        Route::get('getCategory', [AdminController::class, 'getCategory']);
        Route::get('getCategoryServices/{id}', [CategoryController::class, 'categoryServices']);
        Route::get('getServiceClubs/{id}', [CategoryController::class, 'serviceClubs']);

        Route::get('allServices/{club_id}', [ServiceController::class, 'index']);
        Route::get('showService/{id}', [ServiceController::class, 'show']);


        Route::post('reserve', [ReservationController::class, 'reserve']);

    });

});


