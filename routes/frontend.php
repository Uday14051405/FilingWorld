<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\FrontendSettingController;
use App\Http\Controllers\CustomerController;
use App\Http\Middleware\CheckInstallation;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__.'/auth.php';



Route::middleware([CheckInstallation::class])->group(function () {
    Route::get('/', [FrontendController::class, 'index'])->name('frontend.index');
    Route::get('/login-page', [FrontendController::class, 'userLoginView'])->name('user.login');
    Route::post('/user-login', [CustomerController::class, 'userLogin'])->name('user.user_login');
    Route::get('/register-page', [FrontendController::class, 'userRegistrationView'])->name('user.register');
    Route::get('/provider-register', [FrontendController::class, 'partnerRegistrationView'])->name('partner.register');
    Route::get('/forgotpassword-page', [FrontendController::class, 'forgotPassword'])->name('user.forgot_password');

    Route::get('/menu-fetch', [FrontendController::class, 'getMenuCategories'])->name('menu.fetch');
    Route::get('/category-fetch', [FrontendController::class, 'getCategories'])->name('category.fetch');
    Route::get('/category-menu-fetch', [FrontendController::class, 'getCategoryMenu'])->name('category.menu.fetch');
    Route::get('/product-category-menu-fetch', [FrontendController::class, 'getProductCategoryMenu'])->name('product.category.menu.fetch');
    Route::get('/service-menu-fetch', [FrontendController::class, 'getServiceMenu'])->name('service.menu.fetch');
    Route::get('/product-subcategory-fetch', [FrontendController::class, 'getProductSubcategoryMenu'])->name('product.subcategory.fetch');
    Route::get('/product-menu-fetch', [FrontendController::class, 'getProductMenuCategories'])->name('product.menu.fetch');
    Route::get('/product-category-fetch', [FrontendController::class, 'getProductCategoryCategories'])->name('product.category.fetch');
    Route::get('/category-list', [FrontendController::class, 'catgeoryList'])->name('category.list');
    Route::post('/filtered-category-list', [FrontendController::class, 'categoryListFilter'])->name('category.list.filter');
    Route::post('/filtered-product-category-list', [FrontendController::class, 'productCategoryListFilter'])->name('product.category.list.filter');
    Route::get('/subcategory-list', [FrontendController::class, 'subCatgeoryList'])->name('subcategory.list');
    Route::get('/Product-subcategory-list', [FrontendController::class, 'productSubCatgeoryList'])->name('product.subcategory.list');
    Route::get('/service-list', [FrontendController::class, 'serviceList'])->name('service.list');
    Route::get('/product-list', [FrontendController::class, 'productList'])->name('product.list');
    Route::get('/blog-list', [FrontendController::class, 'blogList'])->name('blog.list');
    Route::get('/provider-list', [FrontendController::class, 'providerList'])->name('frontend.provider');

    Route::get('/category-details/{id}', [FrontendController::class, 'categoryDetail'])->name('category.detail');
    Route::get('/sub-category-details/{id}', [FrontendController::class, 'subCategoryDetail'])->name('sub.category.detail');
    Route::get('/product-category-details/{id}', [FrontendController::class, 'productCategoryDetail'])->name('product.category.detail');
    Route::get('/blog-details/{id}', [FrontendController::class, 'blogDetail'])->name('blog.detail');
    Route::get('/provider-detail/{id}', [FrontendController::class, 'providerDetail'])->name('provider.detail');
    Route::get('/handyman-detail/{id}', [FrontendController::class, 'handymanDetail'])->name('handyman-detail');
    Route::get('/service-detail/{id}', [FrontendController::class, 'serviceDetail'])->name('service.detail');
    Route::get('/product-detail/{id}', [FrontendController::class, 'productDetail'])->name('product.detail');

    Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('user.privacy_policy');
    Route::get('/term-conditions', [FrontendController::class, 'termConditions'])->name('user.term_conditions');
    Route::get('/about-us', [FrontendController::class, 'aboutus'])->name('user.about_us');
    Route::get('/refund-policy', [FrontendController::class, 'refundPolicy'])->name('user.refund_policy');
    Route::get('/help-support', [FrontendController::class, 'helpSupport'])->name('user.help_support');
    Route::get('/data-deletion-request', [FrontendController::class, 'DataDeletion'])->name('user.data_deletion_request');

    Route::get('/favourite-service', [FrontendController::class, 'favouriteServiceList'])->name('favourite.service');
    Route::get('/service-packages', [FrontendController::class, 'servicePackageList'])->name('service.package');
    Route::get('/product-packages', [FrontendController::class, 'productPackageList'])->name('product.package');
    Route::get('/book-service', [FrontendController::class, 'bookServiceView'])->name('book.service');
    Route::get('/book-product', [FrontendController::class, 'bookProductView'])->name('book.product');
    Route::get('/rating-all', [FrontendController::class, 'ratingList'])->name('rating.all');
    Route::get('/booking-detail/{id}', [FrontendController::class, 'bookingDetail'])->name('booking.detail');
});



Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/helpdesk-list', [FrontendController::class, 'helpdeskList'])->name('helpdesk.list');
    Route::get('/helpdesk-datatable', [FrontendController::class, 'helpdeskDatatable'])->name('helpdesk.data');
    Route::get('/helpdesk-detail/{id}', [FrontendController::class, 'helpdeskDetail'])->name('helpdesk.detail');
    Route::get('/booking-list', [FrontendController::class, 'bookingList'])->name('booking.list');
    Route::get('/post-job-list', [FrontendController::class, 'postJobList'])->name('post.job.list');
    Route::post('save-favourite',[ServiceController::class, 'saveFavouriteService' ])->name('save-favourite');
    Route::post('delete-favourite',[ServiceController::class, 'deleteFavouriteService' ])->name('delete-favourite');
    Route::post('save-booking-rating', [BookingController::class, 'saveBookingRating' ] )->name('save-booking-rating');
    Route::post('save-recently-viewed/{serviceId}',[FrontendSettingController::class,'recentlyViewedStore' ])->name('save-recently-viewed');
    Route::get('get-recently-viewed',[FrontendSettingController::class,'recentlyViewedGet' ])->name('get-recently-viewed');
});
Route::get('/category-datatable', [FrontendController::class, 'categoryDatatable'])->name('category.data');
Route::get('/product-category-datatable', [FrontendController::class, 'productCategoryDatatable'])->name('product.category.data');
Route::get('/subcategory-datatable', [FrontendController::class, 'subCategoryDatatable'])->name('subcategory.data');
Route::get('/product-subcategory-datatable', [FrontendController::class, 'productSubCategoryDatatable'])->name('product.subcategory.data');
Route::get('/service-datatable', [FrontendController::class, 'serviceDatatable'])->name('service.data');
Route::get('/blog-datatable', [FrontendController::class, 'blogDatatable'])->name('blog.data');
Route::get('/provider-datatable', [FrontendController::class, 'providerDatatable'])->name('provider.data');
Route::get('/booking-datatable', [FrontendController::class, 'bookingDatatable'])->name('booking.data');
Route::get('/post-job-datatable', [FrontendController::class, 'postJobDatatable'])->name('post.job.data');
Route::get('/favouriteservice-datatable', [FrontendController::class, 'favouriteServiceDatatable'])->name('favouriteservice.data');
Route::get('/rating-datatable', [FrontendController::class, 'ratingDatatable'])->name('rating.data');
Route::post('/user-subscribe', [FrontendController::class, 'userSubscribe'])->name('user.subscribe');



Route::get('/product-datatable', [FrontendController::class, 'productDatatable'])->name('product.data');








