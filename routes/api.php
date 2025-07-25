<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\API;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
normal api_token
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/
require __DIR__.'/admin-api.php';

Route::get('category-list',[API\CategoryController::class,'getCategoryList']);
Route::get('subcategory-list',[API\SubCategoryController::class,'getSubCategoryList']);
Route::get('service-list',[API\ServiceController::class,'getServiceList']);
Route::get('type-list',[API\CommanController::class,'getTypeList']);
Route::get('blog-list',[API\BlogController::class,'getBlogList']);
Route::post('blog-detail',[API\BlogController::class,'getBlogDetail']);
Route::get('landing-page-list',[API\FrontendSettingController::class,'getLandingPageSetting']);

Route::post('country-list',[ API\CommanController::class, 'getCountryList' ]);
Route::post('state-list',[ API\CommanController::class, 'getStateList' ]);
Route::post('city-list',[ API\CommanController::class, 'getCityList' ]);
Route::get('search-list', [ API\CommanController::class, 'getSearchList' ] );
Route::get('slider-list',[ API\SliderController::class, 'getSliderList' ]);
Route::get('top-rated-service',[ API\ServiceController::class, 'getTopRatedService' ]);
Route::get('coupon-list',[ API\CouponController::class, 'getCouponList' ]);
Route::post('configurations', [ API\DashboardController::class, "configurations"]);
Route::get('firebase-detail', [ API\DashboardController::class, "firebaseDetails"]);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register',[API\User\UserController::class, 'register']);
Route::post('check-username', [API\User\UserController::class, 'checkUsername']);
Route::post('login',[API\User\UserController::class,'login']);
Route::post('forgot-password',[ API\User\UserController::class,'forgotPassword']);
Route::post('social-login',[ API\User\UserController::class, 'socialLogin' ]);
Route::post('contact-us', [ API\User\UserController::class, 'contactUs' ] );
Route::post('user-email-verify',[API\User\UserController::class,'verify']);



Route::get('service-rating-list',[API\ServiceController::class,'getServiceRating']);
Route::get('user-detail',[API\User\UserController::class, 'userDetail']);
Route::post('service-detail', [ API\ServiceController::class, 'getServiceDetail' ] );
Route::get('user-list',[API\User\UserController::class, 'userList']);
Route::get('booking-status', [ API\BookingController::class, 'bookingStatus' ] );
Route::post('handyman-reviews',[API\User\UserController::class, 'handymanReviewsList']);
Route::post('service-reviews', [ API\ServiceController::class, 'serviceReviewsList' ] );
Route::get('post-job-status', [ API\PostJobRequestController::class, 'postRequestStatus' ] );
// Route::get('booking-list', [ API\BookingController::class, 'getBookingList' ] );
Route::get('dashboard-detail',[ API\DashboardController::class, 'dashboardDetail' ]);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('service-save', [ App\Http\Controllers\ServiceController::class, 'store' ] );
    //Route::post('service-save', [ App\Http\Controllers\ServiceController::class, 'store' ] );
    Route::post('service-delete/{id}', [ App\Http\Controllers\ServiceController::class, 'destroy' ] );
    Route::post('booking-save', [ App\Http\Controllers\BookingController::class, 'store' ] );
    Route::post('get-payment-method', [ App\Http\Controllers\BookingController::class, 'getPaymentMethod' ] );
    Route::post('create-stripe-payment', [ App\Http\Controllers\BookingController::class, 'createStripePayment' ] );


    
    Route::post('booking-update', [ API\BookingController::class, 'bookingUpdate' ] );
    Route::get('provider-dashboard',[ API\DashboardController::class, 'providerDashboard' ]);
    Route::get('admin-dashboard',[ API\DashboardController::class, 'adminDashboard' ]);
    Route::get('booking-list', [ API\BookingController::class, 'getBookingList' ] );
    Route::post('booking-detail', [ API\BookingController::class, 'getBookingDetail' ] );
    Route::post('save-booking-rating', [ API\BookingController::class, 'saveBookingRating' ] );
    Route::post('save-booking-rating-by-admin', [ API\BookingController::class, 'saveBookingRatingByAdmin' ] )->name('save.booking.rating.by.admin');
    Route::post('delete-booking-rating', [ API\BookingController::class, 'deleteBookingRating' ] );
    Route::get('get-user-ratings', [ API\BookingController::class, 'getUserRatings' ] );
    //Route::get('earning-breakdown', [ API\BookingController::class, 'getEarningsBreakdown' ] );


    Route::post('save-favourite',[ API\ServiceController::class, 'saveFavouriteService' ]);
    Route::post('delete-favourite',[ API\ServiceController::class, 'deleteFavouriteService' ]);
    Route::get('user-favourite-service',[ API\ServiceController::class, 'getUserFavouriteService' ]);

    Route::post('booking-action',[ API\BookingController::class, 'action' ] );

    Route::post('booking-assigned',[ App\Http\Controllers\BookingController::class,'bookingAssigned'] );

    Route::post('user-update-status',[API\User\UserController::class, 'userStatusUpdate']);
    Route::post('change-password',[API\User\UserController::class, 'changePassword']);
    Route::post('update-profile',[API\User\UserController::class,'updateProfile']);
    Route::post('notification-list',[API\NotificationController::class,'notificationList']);
    Route::post('remove-file', [ App\Http\Controllers\HomeController::class, 'removeFile' ] );
    Route::get('logout',[ API\User\UserController::class, 'logout' ]);
    Route::post('save-payment',[API\PaymentController::class, 'savePayment']);

    Route::get('payment-list',[API\PaymentController::class, 'paymentList']);
    Route::post('transfer-payment',[API\PaymentController::class, 'transferPayment']);
    Route::get('payment-history',[API\PaymentController::class, 'paymentHistory']);
    Route::get('cash-detail',[API\PaymentController::class, 'paymentDetail']);
    Route::get('user-bank-detail',[API\CommanController::class, 'getBankList']);
    Route::post('default-bank',[API\CommanController::class, 'defaultBank']);

    Route::post('save-bank',[App\Http\Controllers\BankController::class, 'store']);
    Route::post('delete-bank/{id}',[App\Http\Controllers\BankController::class, 'destroy']);
    Route::post('provider-payout',[App\Http\Controllers\ProviderPayoutController::class, 'store']);
    Route::get('handyman-earning-list',[ App\Http\Controllers\EarningController::class, 'handymanEarningData' ]);

    Route::post('handyman-payout-save',[ App\Http\Controllers\HandymanPayoutController::class, 'store' ]);


    Route::post('save-provideraddress', [ App\Http\Controllers\ProviderAddressMappingController::class, 'store' ]);
    Route::get('provideraddress-list', [ API\ProviderAddressMappingController::class, 'getProviderAddressList' ]);
    Route::post('provideraddress-delete/{id}', [ App\Http\Controllers\ProviderAddressMappingController::class, 'destroy' ]);
    Route::post('save-handyman-rating', [ API\BookingController::class, 'saveHandymanRating' ] );
    Route::post('delete-handyman-rating', [ API\BookingController::class, 'deleteHandymanRating' ] );

    Route::get('document-list', [ API\DocumentsController::class, 'getDocumentList' ] );
    Route::get('provider-document-list', [ API\ProviderDocumentController::class, 'getProviderDocumentList' ] );
    Route::post('provider-document-save', [ App\Http\Controllers\ProviderDocumentController::class, 'store' ] );
    Route::post('provider-document-delete/{id}', [ App\Http\Controllers\ProviderDocumentController::class, 'destroy' ] );
    Route::post('provider-document-action',[ App\Http\Controllers\ProviderDocumentController::class, 'action' ]);

    Route::get('tax-list',[ API\CommanController::class, 'getProviderTax' ]);
    Route::get('handyman-dashboard',[ API\DashboardController::class, 'handymanDashboard' ]);

    Route::post('customer-booking-rating',[ API\BookingController::class, 'bookingRatingByCustomer' ]);
    Route::post('handyman-delete/{id}',[ App\Http\Controllers\HandymanController::class, 'destroy' ]);
    Route::post('handyman-action',[ App\Http\Controllers\HandymanController::class, 'action' ]);

    Route::get('provider-payout-list', [ API\PayoutController::class, 'providerPayoutList' ] );
    Route::get('handyman-payout-list', [ API\PayoutController::class, 'handymanPayoutList' ] );

    Route::get('plan-list', [ API\PlanController::class, 'planList' ] );
    Route::post('save-subscription', [ API\SubscriptionController::class, 'providerSubscribe' ] );
    Route::post('cancel-subscription', [ API\SubscriptionController::class, 'cancelSubscription' ] );
    Route::get('subscription-history', [ API\SubscriptionController::class, 'getHistory' ] );
    Route::get('wallet-history', [ API\WalletController::class, 'getHistory' ] );
    Route::post('wallet-top-up', [ API\WalletController::class, 'walletTopup' ] );

    Route::post('save-service-proof', [ API\BookingController::class, 'uploadServiceProof' ] );
    Route::post('handyman-update-available-status',[API\User\UserController::class, 'handymanAvailable']);
    Route::post('delete-user-account',[API\User\UserController::class, 'deleteUserAccount']);
    Route::post('delete-account',[API\User\UserController::class, 'deleteAccount']);

    Route::post('save-post-job',[ App\Http\Controllers\PostJobRequestController::class, 'store' ]);
    Route::post('post-job-delete/{id}', [ App\Http\Controllers\PostJobRequestController::class, 'destroy' ]);

    Route::get('get-post-job',[ API\PostJobRequestController::class, 'getPostRequestList' ]);
    Route::post('get-post-job-detail',[ API\PostJobRequestController::class, 'getPostRequestDetail' ]);

    Route::post('save-bid',[  App\Http\Controllers\PostJobBidController::class, 'store' ]);
    Route::get('get-bid-list',[  API\PostJobBidController::class, 'getPostBidList' ]);


    Route::post('save-provider-slot', [ App\Http\Controllers\ProviderSlotController::class, 'store'] );
    Route::get('get-provider-slot', [API\ProviderSlotController::class, 'getProviderSlot' ] );


    Route::post('package-save',[  App\Http\Controllers\ServicePackageController::class, 'store' ]);
    Route::get('package-list',[API\ServicePackageController::class,'getServicePackageList']);
    Route::post('package-delete/{id}', [ App\Http\Controllers\ServicePackageController::class, 'destroy' ] );



    Route::post('blog-save', [ App\Http\Controllers\BlogController::class, 'store' ] );
    Route::post('blog-delete/{id}', [ App\Http\Controllers\BlogController::class, 'destroy' ] );
    Route::post('blog-action',[ App\Http\Controllers\BlogController::class, 'action' ]);


    Route::post('save-favourite-provider',[ API\ProviderFavouriteController::class, 'saveFavouriteProvider' ]);
    Route::post('delete-favourite-provider',[ API\ProviderFavouriteController::class, 'deleteFavouriteProvider' ]);
    Route::get('user-favourite-provider',[ API\ProviderFavouriteController::class, 'getUserFavouriteProvider' ]);
    Route::post('download-invoice',[API\CommanController::class,'downloadInvoice']);
    Route::get('user-wallet-balance',[API\User\UserController::class,'userWalletBalance']);
    Route::get('get-recently-viewed',[App\Http\Controllers\FrontendSettingController::class,'recentlyViewedGet' ]);

    Route::post('service-addon-save', [ App\Http\Controllers\ServiceAddonController::class, 'store' ] );
    Route::post('service-addon-delete/{id}', [ App\Http\Controllers\ServiceAddonController::class, 'destroy' ] );
    Route::get('service-addon-list', [ API\ServiceAddonController::class, 'getServiceAddonList' ] );

    Route::post('get-wallet-payment-method', [ App\Http\Controllers\WalletController::class, 'getWalletPaymentMethod' ] );
    Route::post('create-wallet-stripe-payment', [ App\Http\Controllers\WalletController::class, 'createWalletStripePayment' ] );
    Route::get('payment-gateway-list',[API\FrontendSettingController::class,'getPaymentGatewayList']);
    Route::get('payment-gateways',[API\PaymentController::class, 'paymentGateways']);

    Route::post('update-location',[API\BookingController::class, 'updateLocation']);
    Route::get('get-location',[API\BookingController::class, 'getLocation']);
    Route::post('withdraw-money',[API\WalletController::class, 'withdrawMoney']);


    Route::Post('switch-language', [API\User\UserController::class,'SwitchLang']);
    Route::get('helpdesk-list', [ API\HelpDeskController::class, 'getHelpDeskList' ] );
    Route::Post('helpdesk-save', [ App\Http\Controllers\HelpDeskController::class, 'store' ] );
    Route::Post('helpdesk-closed/{id}', [ App\Http\Controllers\HelpDeskController::class, 'closed' ] );
    Route::get('helpdesk-detail', [ API\HelpDeskController::class, 'getHelpDeskDetail' ] );
    Route::post('helpdesk-activity-save/{id}', [App\Http\Controllers\HelpDeskController::class, 'activity']);

    Route::middleware('auth:sanctum')->post('/update-last-activity', function (Request $request) {
        $user = Auth::user();
        if ($user) {
            $user->last_active_at = Carbon::now();
            $user->save();
            return response()->json(['message' => 'Activity updated']);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    });
});




// Product API Routes

Route::post('/product-enquiry', [API\EnquiryController::class, 'store']);

Route::get('product-category-list',[API\ProductCategoryController::class,'getCategoryList']);
Route::get('product-subcategory-list',[API\ProductSubCategoryController::class,'getSubCategoryList']);
Route::get('product-list',[API\ProductController::class,'getServiceList']);
Route::get('top-rated-product',[ API\ProductController::class, 'getTopRatedService' ]);
Route::get('product-rating-list',[API\ProductController::class,'getServiceRating']);
Route::post('product-detail', [ API\ProductController::class, 'getServiceDetail' ] );
Route::post('product-reviews', [ API\ProductController::class, 'serviceReviewsList' ] );
Route::get('product-search-list', [ API\ProductCommanController::class, 'getSearchList' ] );

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('product-save', [ App\Http\Controllers\ProductController::class, 'store' ] );
    //Route::post('product-save', [ App\Http\Controllers\ProductController::class, 'store' ] );
    Route::post('product-delete/{id}', [ App\Http\Controllers\ProductController::class, 'destroy' ] );

    Route::post('save-favourite-product',[ API\ProductController::class, 'saveFavouriteService' ]);
    Route::post('delete-favourite-product',[ API\ProductController::class, 'deleteFavouriteService' ]);
    Route::get('user-favourite-product',[ API\ProductController::class, 'getUserFavouriteService' ]);

    Route::post('product-package-save',[  App\Http\Controllers\ProductPackageController::class, 'store' ]);
    Route::get('product-package-list',[API\ProductPackageController::class,'getServicePackageList']);
    Route::post('product-package-delete/{id}', [ App\Http\Controllers\ProductPackageController::class, 'destroy' ] );
    
    Route::post('product-addon-save', [ App\Http\Controllers\ProductAddonController::class, 'store' ] );
    Route::post('product-addon-delete/{id}', [ App\Http\Controllers\ProductAddonController::class, 'destroy' ] );
    Route::get('product-addon-list', [ API\ProductAddonController::class, 'getServiceAddonList' ] );
    
});

Route::post('/upload-document', [ API\DocumentUploadController::class, 'upload']);
Route::get('/document/{order_id}', [ API\DocumentUploadController::class, 'show']);
Route::get('/active-document-names', [ API\DocumentUploadController::class, 'getActiveDocumentNames'])->name('active.document.names');