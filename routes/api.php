<?php

use App\Http\Controllers\Admin\AdminControlSubscriptionsController;
use App\Http\Controllers\Admin\AdminEditPlacesController;
use App\Http\Controllers\Admin\AdminEditRoleController;
use App\Http\Controllers\Admin\AdminTripController;
use App\Http\Controllers\Guide\GuideBookingController;
use App\Http\Controllers\Guide\GuideMangeTimeController;
use App\Http\Controllers\Guide\GuideTimeSlotsController;
use App\Http\Controllers\Owner\OwnerAdminController;
use App\Http\Controllers\Owner\OwnerReportController;
use App\Http\Controllers\User\SearchGuideController;
use App\Http\Controllers\User\UserBookingController;
use App\Http\Controllers\User\UserFavoriteAreaController;
use App\Http\Controllers\User\UserFavoriteGuideController;
use App\Http\Controllers\User\UserReportController;
use App\Http\Controllers\User\UserReviewsAreasController;
use App\Http\Controllers\User\UserReviewsGuidesController;
use App\Http\Controllers\User\UserTripController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


//Apis Auth :
Route::post('/register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::group(['middleware' => 'owner'], function () {

    // Add Admin :
    Route::post('/owner/add/admin', [OwnerAdminController::class, 'addAdmin']);

    // Delete Admin :
    Route::delete('/owner/remove/admin/{email_user}', [OwnerAdminController::class, 'removeAdmin']);

    // Show All Admin :
    Route::get('/owner/admins', [OwnerAdminController::class, 'getAllAdmins']);

    //Get All Reports :
    Route::get('/admin/reports', [OwnerReportController::class, 'getAllReports']);

    // Get Report :
    Route::get('/admin/reports/{id}', [OwnerReportController::class, 'getReport']);

    // Update Report Status :
    Route::put('/admin/reports/{id}/status', [OwnerReportController::class, 'updateReportStatus']);

    //Delete Report :
    Route::delete('/admin/reports/{id}', [OwnerReportController::class, 'deleteReport']);

});

Route::group(['middleware' => 'admin'], function () {

    //Add Country:
    Route::post('/Add/Country', [AdminEditPlacesController::class, 'AddCountry']);

    //Edit Country:
    Route::put('/Edit/Country/{idCountry}', [AdminEditPlacesController::class, 'EditCountry']);

    //Delete Country:
    Route::delete('/Delete/Country/{idCountry}', [AdminEditPlacesController::class, 'DeleteCountry']);

    //Show Country:
    Route::get('/Country', [AdminEditPlacesController::class, 'ShowCountry']);

    //Add City:
    Route::post('/Add/countries/{idCountry}/cities', [AdminEditPlacesController::class, 'AddCity']);

    //Edit City:
    Route::put('/Edit/cities/{idCity}', [AdminEditPlacesController::class, 'EditCity']);

    //Delete City:
    Route::delete('/Delete/cities/{idCity}', [AdminEditPlacesController::class, 'DeleteCity']);

    //Add Area:
    Route::post('/Add/cities/{idCity}/areas', [AdminEditPlacesController::class, 'AddArea']);

    //Edit Area:
    Route::post('/Edit/areas/{idArea}', [AdminEditPlacesController::class, 'editArea']);

    //Delete Area:
    Route::delete('/Delete/areas/{idArea}', [AdminEditPlacesController::class, 'deleteArea']);

    //Add Photo to Area:
    Route::post('/add/areas/{idArea}/photo', [AdminEditPlacesController::class, 'addPhotosToArea']);

    //Show Photos Area:
    Route::get('/add/areas/{idArea}/photo', [AdminEditPlacesController::class, 'showPhotosArea']);

    //delete Photos Area:
    Route::delete('/add/areas/photo/{photo_id}', [AdminEditPlacesController::class, 'deletePhotoFromArea']);

    //Show Area:
    Route::get('/area/{area_id}', [AdminEditPlacesController::class, 'getAreaDetails']);

    //ِAdd Guide:
    Route::post('/add/guide', [AdminEditRoleController::class, 'AddGuide']);

    //Delete Guide:
    Route::delete('/delete/guide/{email_guide}', [AdminEditRoleController::class, 'DeleteGuide']);

    //Edit City guide:
    Route::post('/edit/guide', [AdminEditRoleController::class, 'updateGuideCity']);

    // Add Trip :
    Route::post('/trip/add/areas', [AdminTripController::class, 'addTrip']);

    // Edit Trip :
    Route::put('/trip/{trip_id}/update/areas', [AdminTripController::class, 'updateTrip']);

    // Delete Trip :
    Route::delete('/trip/{trip_id}/delete', [AdminTripController::class, 'deleteTrip']);

    // View All Requests For Trip  :
    Route::get('/trip/{trip_id}/subscriptionsRequests/{Status}', [AdminControlSubscriptionsController::class, 'viewTripRequests']);

    // View All Requests (InProgress) For Trip  :
    Route::get('/trip/{trip_id}/subscriptionsRequestsInProgress', [AdminControlSubscriptionsController::class, 'viewTripRequestsInProgress']);

    // View All Requests (Completed) For Trip  :
    Route::get('/trip/{trip_id}/subscriptionsRequestsCompleted', [AdminControlSubscriptionsController::class, 'viewTripRequestsInCompleted']);

    // Approve Request To Subscription For Trip   :
    Route::post('/trip/subscriptionsRequests/{subscription_id}', [AdminControlSubscriptionsController::class, 'approveRequest']);

});


Route::group(['middleware' => 'guide'], function () {

    //ِ Add Guide Availability:
    Route::post('/guide/mangeTime', [GuideMangeTimeController::class, 'addGuideAvailability']);

    //ِ Show Guide Availability:
    Route::get('/guide/mangeTime', [GuideMangeTimeController::class, 'ShowAvailability']);

    //ِ Edit Guide Availability:
    Route::post('/guide/mangeTime/{availability_id}', [GuideMangeTimeController::class, 'updateAvailability']);

    //ِ Delete Guide Availability:
    Route::delete('/guide/mangeTime/{availability_id}', [GuideMangeTimeController::class, 'deleteAvailability']);

    // Add Time Slot :
    Route::post('/guide/availabilities/{guide_availabilities_id}/add/timeSlot', [GuideTimeSlotsController::class, 'addTimeSlot']);

    // Edit Time Slot :
    Route::put('/guide/availabilities/edit/timeSlot/{time_slot_id}', [GuideTimeSlotsController::class, 'editTimeSlot']);

    // Delete Time Slot :
    Route::delete('/guide/availabilities/delete/timeSlot/{time_slot_id}', [GuideTimeSlotsController::class, 'deleteTimeSlot']);

    // Show Time Slot :
    Route::get('/guide/availabilities/{date}/timeSlot/show', [GuideTimeSlotsController::class, 'showAvailableTimeSlots']);

    // Show All Bookings :
    Route::get('/guide/bookings', [GuideBookingController::class, 'showAllBookings']);

    // Show Bookings By Status :
    Route::get('/guide/bookings/status/{status}', [GuideBookingController::class, 'showBookingsByStatus']);

    // Show Pending Bookings By Date :
    Route::get('/guide/bookings/{date}', [GuideBookingController::class, 'showPendingBookingsByDate']);

    // Show Bookings By Status And Date :
    Route::get('/guide/bookings/{date}/status/{status}', [GuideBookingController::class, 'showBookingsByStatusAndDate']);

    // Approve Booking :
    Route::post('/guide/booking/approve', [GuideBookingController::class, 'approveBooking']);


});


Route::group(['middleware' => 'auth'], function () {

    // Search Guide By City :
    Route::get('/search/city/{nameCity}/guide',[SearchGuideController::class,'searchGuidesByCity']);

    // search Available Guides By City And Date And Time:
    Route::get('/search/city/{city_name}/{date}/{start_time}/{end_time}/guide',[SearchGuideController::class,'searchAvailableGuides']);

    // search Available Guides By City And Date :
    Route::get('/search/city/{city_name}/{date}/guide',[SearchGuideController::class,'searchAvailableGuidesByCityAndDate']);

    // get Top 5 Guides By City :
    Route::get('/search/city/{nameCity}/top5guide',[SearchGuideController::class,'getTop5GuidesByCity']);

    // get Top 5 Guides By City (Rate) :
    Route::get('/search/city/{nameCity}/top5guideRate',[SearchGuideController::class,'getTop5GuidesByCityRate']);

    // get Available Times :
    Route::get('/guides/{guide_id}/availability/{date}', [SearchGuideController::class, 'getAvailableTimes']);

    // Create Booking :
    Route::post('/bookings/guides/{guide_id}', [UserBookingController::class, 'createBooking']);

    // show All User Bookings:
    Route::get('/bookings/user', [UserBookingController::class, 'showAllUserBookings']);

    // show user Bookings By Status:
    Route::get('/bookings/user/{status}', [UserBookingController::class, 'showUserBookingsByStatus']);

    // add Review Guide :
    Route::post('/review/guides/{guide_id}', [UserReviewsGuidesController::class, 'addReviewGuide']);

    // Show User Reviews Guides:
    Route::get('/review/guides', [UserReviewsGuidesController::class, 'showUserReviewsGuide']);

    // Update Review Guide:
    Route::put('/review/{review_id}/guides', [UserReviewsGuidesController::class, 'updateReview']);

    // Delete Review Guide:
    Route::delete('/review/{review_id}/guides', [UserReviewsGuidesController::class, 'deleteReview']);

    // add Review Area :
    Route::post('/review/area/{area_id}', [UserReviewsAreasController::class, 'addReviewArea']);

    // Show User Reviews Areas:
    Route::get('/review/area', [UserReviewsAreasController::class, 'showUserReviewsArea']);

    // Update Review Area:
    Route::put('/review/{review_id}/areas', [UserReviewsAreasController::class, 'updateReviewArea']);

    // Delete Review Area:
    Route::delete('/review/{review_id}/areas', [UserReviewsAreasController::class, 'deleteReviewArea']);

    // Show Trip By Area:
    Route::get('/trip/areas/{area_id}', [UserTripController::class, 'ShowTripByArea']);

    // Add Subscription :
    Route::post('/trip/subscription', [UserTripController::class, 'subscribeToTrip']);

    // delete Subscription :
    Route::delete('/trip/{trip_id}/subscription', [UserTripController::class, 'cancelSubscription']);

    // Get All User Subscriptions :
    Route::get('/trip/USubscription/', [UserTripController::class, 'getUserSubscriptions']);

    // Get All User Subscriptions (  In Progress ) :
    Route::get('/trip/USubscriptionInProgress', [UserTripController::class, 'getUserSubscriptionsInProgress']);

    // Get All User Subscriptions ( completed ) :
    Route::get('/trip/USubscriptionCompleted', [UserTripController::class, 'getUserSubscriptionsCompleted']);

    // Add Area To Favorite :
    Route::post('/favorite/area', [UserFavoriteAreaController::class, 'addAreaToFavorites']);

    // Remove Area From Favorite  :
    Route::delete('/favorite/area/{area_id}', [UserFavoriteAreaController::class, 'removeAreaFromFavorites']);

    // View Favorite Areas :
    Route::get('/favorite/area', [UserFavoriteAreaController::class, 'viewFavoriteAreas']);

    // Add Guide To Favorite :
    Route::post('/favorite/guide', [UserFavoriteGuideController::class, 'addGuideToFavorites']);

    // Remove Guide From Favorite  :
    Route::delete('/favorite/guide/{guide_id}', [UserFavoriteGuideController::class, 'removeGuideFromFavorites']);

    // View Favorite Guides :
    Route::get('/favorite/guide', [UserFavoriteGuideController::class, 'viewFavoriteGuides']);

    // Create Report :
    Route::post('/user/report', [UserReportController::class, 'createReport']);

});
