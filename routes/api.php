<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\AUTH\AuthController;
use App\Http\Controllers\AUTH\VerificationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CRatingController;
use App\Http\Controllers\FavoriteClubController;
use App\Http\Controllers\HorseController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TRatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('sendMessage',[MessageController::class,'sendMessage']);


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
        Route::get('searchClubByname/{name}', [AdminController::class, 'searchClubByName']);

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


        Route::get('allTrainersInService/{id}', [TrainerController::class, 'allTrainersInService']);
        Route::get('getTrainerByID/{id}', [TrainerController::class, 'getTrainerByID']);


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


        Route::post('createService', [ServiceController::class, 'create']);
        Route::get('allServices/{club_id}', [ServiceController::class, 'index']);
        Route::get('showService/{id}', [ServiceController::class, 'show']);
        Route::post('updateService/{id}', [ServiceController::class, 'update']);
        Route::post('deleteService/{id}', [ServiceController::class, 'destroy']);
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
            Route::get('getClubsInCategory/{id}', [CategoryController::class, 'clubsInCategory']);

            Route::get('allTrainersInService/{id}', [TrainerController::class, 'allTrainersInService']);
            Route::get('allServices/{club_id}', [ServiceController::class, 'index']);
            Route::get('showService/{id}', [ServiceController::class, 'show']);

            Route::get('showAllClubs', [AdminController::class, 'showClubs']);
            Route::get('getTrainerByID/{id}', [TrainerController::class, 'getTrainerByID']);
            Route::get('getClubByID/{id}', [ClubController::class, 'getClubByID']);
            Route::get('searchClubByName/{name}', [AdminController::class, 'searchClubByName']);
            Route::get('GetTrainersByClub/{id}', [ClubController::class, 'GetTrainersByClub']);
            Route::get('getCourseClasses/{course_id}', [ClassController::class, 'getCourseClasses']);

            Route::get('allTrainersInServiceUser/{id}', [TrainerController::class, 'allTrainersInService']);
            Route::get('getTrainerByIDUser/{id}', [TrainerController::class, 'getTrainerByID']);


////// RESERVATION
        Route::post('reserve', [ReservationController::class, 'reserve']);
        Route::post('editReserve/{Rid}', [ReservationController::class, 'editReservation']);
        Route::get('UserReservations/{Uid}', [ReservationController::class, 'UserReservations']);
        Route::get('TrainerReservation/{Tid}', [ReservationController::class, 'TrainerReservation']);
        Route::get('showSpecificReservation/{Rid}', [ReservationController::class, 'showSpecificReservation']);
        Route::delete('cancelReservation/{Rid}', [ReservationController::class, 'cancelReservation']);


            //TRating
            Route::get('allTrainerRating/{trainer_id}',[TRatingController::class,'getAllRatingInTrainer']);
            Route::get('allAverageTrainerRating/{trainer_id}',[TRatingController::class,'getAverageRating']);
            Route::get('getAllReviewsInTrainer/{trainer_id}',[TRatingController::class,'getAllReviewsInTrainer']);
            Route::post('createTrainerRating',[TRatingController::class,'createRating']);
            Route::post('updateTrainerRating',[TRatingController::class,'updateRating']);
            Route::post('deleteTrainerRating',[TRatingController::class,'deleteRating']);

            //CRating
            Route::get('allClubRating/{club_id}',[CRatingController::class,'getAllRatingInClub']);
            Route::get('allAverageClubRating/{club_id}',[CRatingController::class,'getAverageRating']);
            Route::get('getAllReviewsInClub/{club_id}',[CRatingController::class,'getAllReviewsInClub']);
            Route::post('createClubRating',[CRatingController::class,'createRating']);
            Route::post('updateClubRating',[CRatingController::class,'updateRating']);
            Route::post('deleteClubRating',[CRatingController::class,'deleteRating']);

            // favorite
            Route::post('addClubToFavorites',[FavoriteClubController::class,'addClubToFavorites']);
            Route::post('removeClubFromFavorites',[FavoriteClubController::class,'removeClubFromFavorites']);
            Route::get('getFavoriteClubs/{user_id}',[FavoriteClubController::class,'getFavoriteClubs']);

            //Available Times
            Route::post('getTrainerTimes',[TrainerController::class,'getTrainerTimes']);
            Route::post('reserveTrainerTimes',[TrainerController::class,'reserveTrainerTimes']);

            ////////// AUCTIONS ||||||||||||||
            Route::post('AddAuction',[AuctionController::class,'AddAuction']);
            Route::post('EditAuction/{id}',[AuctionController::class,'EditAuction']);
            Route::get('showHorseByID/{id}',[AuctionController::class,'showHorseByID']);
            Route::get('showAuctionByID/{id}',[AuctionController::class,'showAuctionByID']);
            Route::get('getCurrentBid/{id}',[AuctionController::class,'getCurrentBid']);
            Route::post('AddBid/{id}',[AuctionController::class,'AddBid']);
            Route::get('getBuyersIN_Auction/{id}',[AuctionController::class,'getBuyersIN_Auction']);

            Route::post('stripe-payment', [StripeController::class,'stripePost']);
        });
    });


//التحقق من فلترة المصفوفة للتخلص من الاشخاص المتشابهين
