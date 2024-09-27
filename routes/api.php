<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppMessageController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\AUTH\AuthController;
use App\Http\Controllers\AUTH\VerificationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CRatingController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FavoriteAuctionController;
use App\Http\Controllers\FavoriteClubController;
use App\Http\Controllers\HealthCareController;
use App\Http\Controllers\HRatingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OfferClubController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainerServiceController;
use App\Http\Controllers\TRatingController;
use Illuminate\Support\Facades\Route;




    Route::post('register',[AuthController::class,'register']);
    Route::post('AdminRegister',[AuthController::class,'AdminRegister']);
    Route::post('login',[AuthController::class,'login']);
    Route::post('sendAlert',[ReservationController::class,'sendAlert']);





    Route::post('/send-verification-email', [VerificationController::class, 'sendVerificationEmail']);
    Route::post('/verify', [VerificationController::class, 'verify']);
    Route::post('/send-password-reset-email', [VerificationController::class, 'sendPasswordResetEmail']);
    Route::post('/reset-password', [VerificationController::class, 'resetPassword']);
    Route::get('getAppMessage',[AppMessageController::class,'getMessage']);
    Route::post('pusher/authenticate',[MessageController::class,'authenticate']);




    Route::middleware('auth:sanctum')->group(function () {


        Route::post('logout',[AuthController::class,'logout']);
        Route::post('/change-password', [VerificationController::class, 'changePassword']);


        ############### ADMIN ROLE ###############
    Route::group(['middleware' => ['role_or_permission:ADMIN']], function () {

        Route::post('getTrainerTimes_A',[TrainerController::class,'getTrainerTimes']);

        Route::get('getUserForChart', [AdminController::class, 'getUserForChart']);
        Route::get('getUserDate', [AdminController::class, 'getUserDate']);
        Route::get('getUserCountInMonth', [AdminController::class, 'UserInMonth']);
        Route::get('getAuctionCountInMonth', [AdminController::class, 'AuctionInMonth']);
        Route::get('getInfoToAdmin', [AdminController::class, 'infoToAdmin']);
        Route::post('AdminUpdate', [AuthController::class, 'AdminUpdate']);

        //Add App Message
        Route::post('updateMessage',[AppMessageController::class,'updateMessage']);

        ///// AUCTIONS ////////
        Route::get('getPending_Auctions',[AdminController::class,'getPending_Auctions']);
        Route::post('AuctionApproval',[AdminController::class,'AuctionApproval']);


        Route::post('createCategory', [AdminController::class, 'createCategory']);
        Route::get('allCategory', [AdminController::class, 'getCategories']);
        Route::get('getCategory_A/{id}', [AdminController::class, 'getCategory']);
        Route::post('updateCategory', [AdminController::class, 'updateCategory']);
        Route::delete('deleteCategory/{id}', [AdminController::class, 'deleteCategory']);
        Route::post('getCategoryByName/{name}', [AdminController::class, 'getCategoryByName']);


        Route::post('AddClub', [AdminController::class, 'AddClub']);
        Route::get('showClubs', [AdminController::class, 'showClubs']);
        Route::delete('deleteClub/{userId}', [AdminController::class, 'deleteClub']);
        Route::get('searchClubByname_A/{name}', [AdminController::class, 'searchClubByName']);
        Route::get('searchClubByID/{id}', [AdminController::class, 'searchClubByID']);


        Route::get('allServices/{club_id}', [ServiceController::class, 'index']);
        Route::get('showService/{id}', [ServiceController::class, 'show']);
        Route::get('showServiceAdmin/{id}', [ServiceController::class, 'show']);
        ///// Health Care
        Route::post('createHealthCare', [HealthCareController::class, 'createHealthCare']);
        Route::post('editHealthCare/{id}', [HealthCareController::class, 'updateHealthCare']);
        Route::delete('deleteHealthCare/{id}', [HealthCareController::class, 'deleteHealthCare']);
        Route::get('getAllHealthCares', [HealthCareController::class, 'getAllHealthCares']);
        Route::get('getHealthCareByID/{id}', [HealthCareController::class, 'getHealthCareByID']);
        Route::get('searchHealthCareByName/{name}', [HealthCareController::class, 'searchHealthCareByName']);

        ///// Doctors
        Route::get('allDoctorsInHealthCareAdmin/{id}', [DoctorController::class, 'allDoctorsInHeaalthCare']);
        Route::get('getDoctorByIDAdmin/{id}', [DoctorController::class, 'getDoctorByID']);

        /////// Support
        Route::get('getAllSupportNotReply',[SupportController::class,'getAllSupportNotReply']);
        Route::get('replySupport/{id}',[SupportController::class,'reply']);

        ///// Rating
        Route::get('allAverageHealthRating_A/{health_id}',[HRatingController::class,'getAverageRating']);
        Route::get('getAllReviewsInHealth_A/{health_id}',[HRatingController::class,'getAllReviewsInHealth']);
        Route::get('allAverageClubRating_A/{club_id}',[CRatingController::class,'getAverageRating']);
        Route::get('getAllReviewsInClub_A/{club_id}',[CRatingController::class,'getAllReviewsInClub']);
        Route::get('allAverageTrainerRating_A/{trainer_id}',[TRatingController::class,'getAverageRating']);
        Route::get('getAllReviewsInTrainer_A/{trainer_id}',[TRatingController::class,'getAllReviewsInTrainer']);

    });


    ################ CLUB ROLE ###############
    Route::group(['middleware' => ['role:CLUB']], function () {

        Route::get('getBookingCountInMonth/{id}', [AdminController::class, 'BookingInMonth']);
        Route::get('getReservationCountInMonth/{id}', [AdminController::class, 'ReservationInMonth']);


        Route::post('editClub', [ClubController::class, 'editClub']);
        Route::get('MyClub', [ClubController::class, 'MyClub']);

        Route::get('MyTrainers', [ClubController::class, 'MyTrainers']);
        Route::post('AddTrainer', [ClubController::class, 'AddTrainer']);
        Route::delete('deleteTrainer/{id}', [ClubController::class, 'deleteTrainer']);
        Route::post('addTrainerToService', [TrainerServiceController::class, 'addTrainerToService']);
        Route::post('removeTrainerFromService', [TrainerServiceController::class, 'removeTrainerFromService']);
        Route::get('allTrainersInServiceBooking/{id}', [TrainerServiceController::class, 'allTrainersInServiceBooking']);// للحجز الفردي

        Route::get('allTrainersInServiceCourse/{id}', [TrainerController::class, 'allTrainersInServiceCourse']);
        Route::get('club_getTrainerByID/{id}', [TrainerController::class, 'getTrainerByID']);

        Route::post('/addAvailableTimes', [TrainerController::class, 'setAvailableTimes']);
        Route::post('getTrainerTimes_C',[TrainerController::class,'getTrainerTimes']);


        Route::post('createService', [ServiceController::class, 'create']);
        Route::get('allServices_C/{club_id}', [ServiceController::class, 'index']);
        Route::get('showService_C/{name}', [ServiceController::class, 'show']);
        Route::post('updateService/{id}', [ServiceController::class, 'update']);
        Route::delete('deleteService/{id}', [ServiceController::class, 'destroy']);

        Route::post('createCourse', [CourseController::class, 'createCourse']);
        Route::get('MyCourses', [CourseController::class, 'MyCourses']);
        Route::get('MyCourses2', [CourseController::class, 'MyCourses2']);
        Route::get('getSpecificCourse/{id}', [CourseController::class, 'getSpecificCourse']);
        Route::post('editCourse/{CID}', [CourseController::class, 'editCourse']);
        Route::delete('deleteCourse/{id}', [CourseController::class, 'deleteCourse']);

        Route::post('createClass', [ClassController::class, 'createClass']);
        Route::get('getCourseClasses_C/{course_id}', [ClassController::class, 'getCourseClasses']);
        Route::post('editClass/{class_id}', [ClassController::class, 'editClass']);
        Route::delete('deleteClass/{class_id}', [ClassController::class, 'deleteClass']);

        Route::get('allCategory_C', [AdminController::class, 'getCategories']);

        /// Offers
        Route::post('addOfferClub',[OfferClubController::class,'addOffer']);
        Route::delete('deleteOfferClub/{id}',[OfferClubController::class,'deleteOffer']);

        /// Rating
        Route::get('allAverageClubRating_C/{club_id}',[CRatingController::class,'getAverageRating']);
        Route::get('getAllReviewsInClub_C/{club_id}',[CRatingController::class,'getAllReviewsInClub']);


    });





    ################ TRAINER ROLE ###############
    Route::group(['middleware' => ['role:TRAINER']], function () {
        Route::post('getTrainerTimes_T',[TrainerController::class,'getTrainerTimes']);

        Route::post('editTrainer', [TrainerController::class, 'editTrainer']);
        Route::get('MyProfile', [TrainerController::class, 'MyProfile']);
        Route::get('MyCourses_T', [TrainerController::class, 'MyCourses_T']);
        Route::get('get-allUsers_T', [MessageController::class, 'getAllUser']);


        //////////// TRAINER MESSAGES  ////////////
        Route::post('sendMessage',[MessageController::class,'sendMessage']);
        Route::post('getChatMessagesT',[MessageController::class,'getChatMessages']);


        ////////////   TRAINER MESSAGES  ////////////
        Route::post('sendMessage_T',[MessageController::class,'sendMessage']);
        Route::post('getChatMessages_T',[MessageController::class,'getChatMessages']);

        /// Rating
        Route::get('allAverageTrainerRating_T/{trainer_id}',[TRatingController::class,'getAverageRating']);
        Route::get('getAllReviewsInTrainer_T/{trainer_id}',[TRatingController::class,'getAllReviewsInTrainer']);
    });


    ############### HEALTH CARE ##################
        Route::group(['middleware' => ['role_or_permission:HEALTH']], function () {

            Route::post('editHealthCareHealth/{id}', [HealthCareController::class, 'updateHealthCare']);
            Route::get('getAllHealthCaresHealth', [HealthCareController::class, 'getAllHealthCares']);
            Route::get('getHealthCareByIDHealth/{id}', [HealthCareController::class, 'getHealthCareByID']);
            Route::get('myHealth', [HealthCareController::class, 'myHealth']);
            /// Doctors
            Route::post('createDoctor', [DoctorController::class, 'createDoctor']);
            Route::post('editDoctor/{id}', [DoctorController::class, 'updateDoctor']);
            Route::delete('deleteDoctor/{id}', [DoctorController::class, 'deleteDoctor']);
            Route::get('allDoctorsInHealthCare/{id}', [DoctorController::class, 'allDoctorsInHeaalthCare']);
            Route::get('getDoctorByID/{id}', [DoctorController::class, 'getDoctorByID']);

            //// Consultation
            Route::post('replyConsultation/{id}',[ConsultationController::class,'replyConsultation']);
            Route::get('allConsultationByHealthCare/{id}',[ConsultationController::class,'allConsultationByHealthCare']);
            Route::get('allUnansweredConsultationByHealthCare/{id}',[ConsultationController::class,'allUnansweredConsultationsByHealthCare']);
            Route::get('getConsultationByID/{id}',[ConsultationController::class,'getConsultationByID']);

            /// Offers
            Route::post('addOffer',[OfferController::class,'addOffer']);
            Route::delete('deleteOffer/{id}',[OfferController::class,'deleteOffer']);

            /////// Rating
            Route::get('allAverageHealthRating_H/{health_id}',[HRatingController::class,'getAverageRating']);
            Route::get('getAllReviewsInHealth_H/{health_id}',[HRatingController::class,'getAllReviewsInHealth']);
        });

    ################## USER ROLE *******************
        Route::group(['middleware' => ['role_or_permission:USER']], function () {

            Route::post('update', [AuthController::class, 'update']);


            /////////// MESSAGES //////////
            Route::post('sendMessageU',[MessageController::class,'sendMessage']);
            Route::post('sendDoctor-Message',[MessageController::class,'send_Doctor_Message']);
            Route::post('getTrainer-ChatMessagesU',[MessageController::class,'getChatMessages']);
            Route::post('getDoctor-ChatMessagesU',[MessageController::class,'getDoctor_ChatMessages']);


            Route::get('allCategoryU', [AdminController::class, 'getCategories']);
            Route::get('getCategory', [AdminController::class, 'getCategory']);
            Route::get('getCategoryServices/{id}', [CategoryController::class, 'categoryServices']);
            Route::get('getServiceClubs/{id}', [CategoryController::class, 'serviceClubs']);
            Route::get('getClubsInCategory/{id}', [CategoryController::class, 'clubsInCategory']);

            Route::get('allTrainersInService/{id}', [TrainerController::class, 'allTrainersInService']);
            Route::get('allServicesU/{club_id}', [ServiceController::class, 'allServicesForUser']);
            Route::get('showServiceUser/{id}', [ServiceController::class, 'show']);

            Route::get('showAllClubs', [AdminController::class, 'showClubs']);
            Route::get('getTrainerByID/{id}', [TrainerController::class, 'getTrainerByID']);
            Route::get('getClubByID/{id}', [ClubController::class, 'getClubByID']);
            Route::get('searchClubByName/{name}', [AdminController::class, 'searchClubByName']);
            Route::get('GetTrainersByClub/{id}', [ClubController::class, 'GetTrainersByClub']);
            Route::get('getCourseClasses/{course_id}', [ClassController::class, 'getCourseClasses']);

            Route::get('allTrainersInServiceUserBooking/{id}', [TrainerServiceController::class, 'allTrainersInServiceBooking']);// للحجز الفردي

            Route::get('allTrainersInServiceUserCourse/{id}', [TrainerController::class, 'allTrainersInServiceCourse']);
            Route::get('getTrainerByIDUser/{id}', [TrainerController::class, 'getTrainerByID']);



            Route::post('getCoursesByUser', [CourseController::class, 'getCoursesByUser']);

            Route::get('getProfile/{id}', [ProfileController::class, 'getProfile']);

            ////// RESERVATION
        Route::post('reserve', [ReservationController::class, 'reserve']);
        Route::post('editReserve/{Rid}', [ReservationController::class, 'editReservation']);
        Route::get('Reserved_User_clubs', [ReservationController::class, 'Reserved_User_clubs']);
        Route::get('UserReservation/{id}', [ReservationController::class, 'UserReservation']);
        Route::get('TrainerReservation/{Tid}', [ReservationController::class, 'TrainerReservation']);
        Route::get('showSpecificReservation/{Rid}', [ReservationController::class, 'showSpecificReservation']);
        Route::delete('cancelReservation/{Rid}', [ReservationController::class, 'cancelReservation']);
        Route::post('isReserved', [ReservationController::class, 'isReserved']);
        Route::post('isReservedTrainer', [ReservationController::class, 'isReservedTrainer']);

            //TRating
            Route::get('allTrainerRating/{trainer_id}',[TRatingController::class,'getAllRatingInTrainer']);
            Route::get('allAverageTrainerRating/{trainer_id}',[TRatingController::class,'getAverageRating']);
            Route::get('getAllReviewsInTrainer/{trainer_id}',[TRatingController::class,'getAllReviewsInTrainer']);
            Route::post('createTrainerRating',[TRatingController::class,'createRating']);
            Route::post('updateTrainerRating',[TRatingController::class,'updateRating']);
            Route::post('deleteTrainerRating',[TRatingController::class,'deleteRating']);
            Route::post('userHasReviewInTrainer',[TRatingController::class,'userHasReviewInTrainer']);
            Route::post('getRatingIDByUserTrainer',[TRatingController::class,'getRatingIDByUser']);
            //CRating
            Route::get('allClubRating/{club_id}',[CRatingController::class,'getAllRatingInClub']);
            Route::get('allAverageClubRating/{club_id}',[CRatingController::class,'getAverageRating']);
            Route::get('getAllReviewsInClub/{club_id}',[CRatingController::class,'getAllReviewsInClub']);
            Route::post('createClubRating',[CRatingController::class,'createRating']);
            Route::post('updateClubRating',[CRatingController::class,'updateRating']);
            Route::post('deleteClubRating',[CRatingController::class,'deleteRating']);
            Route::post('userHasReviewInClub',[CRatingController::class,'userHasReviewInClub']);
            Route::post('getRatingIDByUserClub',[CRatingController::class,'getRatingIDByUser']);


            //HRating
            Route::get('allHealthRating/{health_id}',[HRatingController::class,'getAllRatingInHealth']);
            Route::get('allAverageHealthRating/{health_id}',[HRatingController::class,'getAverageRating']);
            Route::get('getAllReviewsInHealth/{health_id}',[HRatingController::class,'getAllReviewsInHealth']);
            Route::post('createHealthRating',[HRatingController::class,'createRating']);
            Route::post('updateHealthRating',[HRatingController::class,'updateRating']);
            Route::post('deleteHealthRating',[HRatingController::class,'deleteRating']);
            Route::post('userHasReviewInHealth',[HRatingController::class,'userHasReviewInHealth']);
            Route::post('getRatingIDByUserHealth',[HRatingController::class,'getRatingIDByUser']);
            Route::post('isReservedHealth',[HRatingController::class,'isReservedHealth']);

            // favorite club
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
            Route::get('getTodayAuctions',[AuctionController::class,'getTodayAuctions']);
            Route::get('upcoming',[AuctionController::class,'upcoming']);
            Route::get('upcomingToday',[AuctionController::class,'upcomingToday']);
            Route::post('upcoming1',[AuctionController::class,'upcoming2']);
            Route::get('OperationTime/{id}',[AuctionController::class,'OperationTime']);
            Route::post('addInsurance',[AuctionController::class,'addInsurance']);
            Route::get('winner/{id}',[AuctionController::class,'winner']);

            //Booking
            Route::post('addBooking',[BookingController::class,'addBooking']);
            Route::get('getAllBookingByUser/{user_id}',[BookingController::class,'getAllBookingByUser']);
            Route::get('getBookingDescription/{booking_id}',[BookingController::class,'getBooking']);
            Route::post('deleteBooking',[BookingController::class,'deleteBooking']);
            Route::post('cancelBookingTime',[BookingController::class,'cancelBookingTime']);



            // favorite Auction
            Route::post('addAuctionToFavorites',[FavoriteAuctionController::class,'addAuctionToFavorites']);
            Route::post('removeAuctionFromFavorites',[FavoriteAuctionController::class,'removeAuctionFromFavorites']);
            Route::get('getFavoriteAuctions/{user_id}',[FavoriteAuctionController::class,'getFavoriteAuctions']);



            //// Health Care
            Route::get('getAllHealthCaresUser', [HealthCareController::class, 'getAllHealthCares']);
            Route::get('getHealthCareByIDUser/{id}', [HealthCareController::class, 'getHealthCareByID']);
            Route::get('searchHealthCareByNameUser/{name}', [HealthCareController::class, 'searchHealthCareByName']);


            ///// Doctors
            Route::get('allDoctorsInHealthCareUser/{id}', [DoctorController::class, 'allDoctorsInHeaalthCare']);
            Route::get('getDoctorByIDUser/{id}', [DoctorController::class, 'getDoctorByID']);

            ///// Consultation
            Route::post('createConsultation',[ConsultationController::class,'createConsultation']);
            Route::get('allConsultationByUser/{id}',[ConsultationController::class,'allConsultationByUser']);
            Route::delete('deleteConsultation/{id}',[ConsultationController::class,'deleteConsultation']);
            Route::get('getConsultationByIDUser/{id}',[ConsultationController::class,'getConsultationByID']);

            ////// Offer Health
            Route::get('getOffersToday',[OfferController::class,'getOffersToday']);

            ////// Offer Club
            Route::get('getOffersClubToday',[OfferClubController::class,'getOffersToday']);

            /////// Support
            Route::post('createSupport',[SupportController::class,'create']);

            Route::get('chatsListTrainer/{id}',[MessageController::class,'allTrainerChatsByUser']);
            Route::get('chatsListDoctor/{id}',[MessageController::class,'allDoctorChatsByUser']);
            Route::get('isRead/{id}',[MessageController::class,'isReadTrainer']);

            Route::get('isRead_D/{id}',[MessageController::class,'isReadDoctor']);

            /// add Device Token
            Route::post('addToken',[DeviceTokenController::class,'store']);
            Route::get('getNotification',[\App\Services\Api\NotificationService::class,'index']);


            Route::post('stripe-payment', [StripeController::class,'stripePost']);
        });



        Route::group(['middleware' => ['role_or_permission:SB']], function () {

            /////////// MESSAGES //////////

            Route::post('sendMessageU',[MessageController::class,'sendMessage']);
            Route::post('getTrainer-ChatMessagesU',[MessageController::class,'getChatMessages']);

            Route::post('getDoctor-ChatMessages_D',[MessageController::class,'getDoctor_ChatMessages']);
            Route::post('sendDoctor-Message_D',[MessageController::class,'send_Doctor_Message']);
            Route::get('get-allUsers', [MessageController::class, 'getAllUser']);

        });
    });
