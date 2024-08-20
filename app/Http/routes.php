<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Authentication Routes...
Route::get('login', 'App\Http\Controllers\Auth\AuthController@showLoginForm');
Route::post('login', 'App\Http\Controllers\Auth\AuthController@login');
Route::get('logout', 'App\Http\Controllers\Auth\AuthController@logout');

// Registration Routes...
Route::get('register', 'App\Http\Controllers\Auth\AuthController@showRegistrationForm');
Route::post('register', 'App\Http\Controllers\Auth\AuthController@register');
 
Route::get('/thankyou-signup', 'Front\ThanksignupController@index');   
Route::get('/register/confirm/{token}', 'Auth\AuthController@confirmEmail'); 
Route::get('/password/reset/send', 'Auth\PasswordController@send');
Route::any('/term-of-service','Front\CmsController@termOfService');
Route::any('/privacy-policy','Front\CmsController@privacyPolicy');
Route::get('/reactive-account/{token?}', 'Front\ReactiveAccountController@activeAccount'); 
Route::get('/notification/confirm/{token}', 'Front\NotificationController@confirmEmail');
Route::auth();

Route::group(['middleware' => 'auth'], function () { 
    

    //xdate routes
    Route::get('/', 'Front\HomeController@index');
    Route::post('/xdate/addupate', 'Front\XdateController@addUpdateXDate');
    Route::get('/get/notes/{id}', 'Front\XdateController@getAllNotes');
    Route::post('/xdatenotes/add', 'Front\XdateController@insertNote'); 
    Route::get('/search/{search?}', 'Front\HomeController@search');
	Route::post('/xdate/filter', 'Front\HomeController@filterData');
	Route::post('/xdate/skiptonext', 'Front\XdateController@getNextDate');
    Route::post('/xdate/request_update', 'Front\XdateController@status_change_request');
	//Route::get('/addxdatemodal', 'Front\HomeController@index');

    Route::get('/change-ownership/{id?}', 'Front\UserController@changeOwnerShip');
	Route::get('/myprofile', 'Front\MyprofileController@index'); 
	Route::post('/cancel-account', 'Front\MyprofileController@cancelAccount'); 
	Route::post('/remove-avatar', 'Front\MyprofileController@remove_avatar'); 
    Route::post('/updateProfile', 'Front\MyprofileController@updateProfile'); 
    Route::post('/updateUserData', 'Front\HomeController@updateUserData'); 
    Route::any('/change-password', 'Front\MyprofileController@changePassword');
    Route::any('/save-password', 'Front\MyprofileController@savePassword');  
	Route::get('/manage-customize-fields', 'Front\CustomizeController@index');
	Route::get('/default-fields', 'Front\CustomizeController@insertDefaultCustom');
    Route::get('/user-manage', 'Front\UserController@index');
    Route::get('/tell-friends', 'Front\TellFriendsController@index');
    Route::post('/tell-friends', 'Front\TellFriendsController@inviteFriends');
	Route::get('/feedback', 'Front\FeedbackController@index');
	Route::post('/feedback', 'Front\FeedbackController@addFeedback'); 
    /* notification routes */
	Route::get('/notification', 'Front\NotificationController@index');
	Route::post('/add-email', 'Front\NotificationController@addEmail');
	Route::post('/add-phone', 'Front\NotificationController@addPhone');
	Route::post('/change-status', 'Front\NotificationController@changeStatus');
	Route::post('/notification/delete', 'Front\NotificationController@delete');
	
	Route::post('/notification/resend-mail', 'Front\NotificationController@resendMail');
	Route::post('notification/changeFrequency','Front\NotificationController@updateFrequency');
	Route::post('notification/verify_number','Front\NotificationController@veriry_number');
    Route::post('notification/mobile/resend_code','Front\NotificationController@resend_code');
    /* card routes */
	Route::get('/planbill/card', 'Front\CardController@index');
	Route::post('/planbill/card', 'Front\CardController@index');
	Route::get('/planbill/getCountry/{country_id}', 'Front\CardController@getState');
	//for policy cutomize
	Route::post('/policy/lines', 'Front\PolicyController@create');
	Route::post('/policy/lines/update', 'Front\PolicyController@create');
	Route::post('/policy/industry', 'Front\PolicyController@create');
	Route::put('/policy/industry/update', 'Front\PolicyController@create');
	Route::post('/policy/personal', 'Front\PolicyController@create');
	Route::put('/policy/personal/update', 'Front\PolicyController@create');
	Route::post('/policy/commercial', 'Front\PolicyController@create');
	Route::put('/policy/commercial/update', 'Front\PolicyController@create');
	Route::delete('/policy/delete', 'Front\PolicyController@delete');
	Route::get('/policy/confirm/{id}', 'Front\PolicyController@policyRecreate');
	
	//For User
	Route::post('/user', 'Front\UserController@create');
	Route::put('/user/active', 'Front\UserController@active');
	Route::put('/user/deactive', 'Front\UserController@deactive'); 
	Route::put('/user/edit', 'Front\UserController@editUser');
	Route::delete('/user/delete', 'Front\UserController@deleteUser');
    // user plan change

    Route::post('/planbill/userPlan-change','Front\userChangePlanController@changePlan');
	Route::post('/planbill/upDownPlan','Front\userChangePlanController@upgradeDowngradePlan');
    Route::get('/planbill/change-plan','Front\userChangePlanController@index');
    Route::get('/planbill/get-plan-data','Front\userChangePlanController@getUserPlanWithAmount');
    Route::post('/planbill/apply_coupon','Front\userChangePlanController@apply_coupon');
    
    // export routes 
    Route::get('/export','Front\ExportController@index');
    Route::get('/export/data','Front\ExportController@export_sub_page');
    Route::post('/generate-csv','Front\ExportController@request_csv');
    Route::get('/download/{id}','Front\ExportController@export_sub_page');
    Route::post('export/remove','Front\ExportController@remove_expired_export');
    Route::get('planbill/invoice','Front\InvoiceController@index');
    Route::get('/generate-pdf/{id}','Front\InvoiceController@get_pdf_data');
});

// setting page 
//Route::any('/settings','Front\SettingController@index');

// /admin all routes

Route::get('/admin/login','Admin\Auth\AuthController@showLoginForm');
Route::post('/admin/login','Admin\Auth\AuthController@login');
Route::post('/admin/password/reset','Admin\Auth\PasswordController@reset');
Route::get('/admin/password/reset/send','Admin\Auth\PasswordController@send');
Route::get('/admin/password/reset/{token?}','Admin\Auth\PasswordController@showResetForm');
Route::get('/admin/confirm/{token?}','Admin\Auth\AuthController@confirmEmail');
Route::post('/admin/password/email','Admin\Auth\PasswordController@sendResetLinkEmail');
Route::get('auth/google/signin','Auth\AuthController@redirectToProvider');
Route::get('auth/google/callback', 'Auth\AuthController@handleProviderCallback');
Route::get('admin/trial-extend','Admin\TrialExtendController@index');
Route::group(['middleware' => ['admin']], function () {
    //Login Routes...
    Route::get('/admin/logout','Admin\Auth\AuthController@logout');	
    // Registration Routes...
    //Route::get('admin/register', 'Admin\Auth\AuthController@showRegistrationForm');
    //Route::post('admin/register', 'Admin\Auth\AuthController@register');

    Route::get('/admin', 'Admin\HomeController@index');	
    Route::post('/admin/extend-trial', 'Admin\UserDetailController@requestExtendTrial');
    Route::post('/admin/field-restore-mail', 'Admin\UserDetailController@restore_mail');
    Route::get('/admin/trial/confirm/{token?}', 'Admin\UserDetailController@approveTrial');	
    Route::post('/admin/add-note', 'Admin\UserDetailController@addNote');	
    Route::post('/admin/index', 'Admin\HomeController@index');	
    Route::post('/admin/filterData', 'Admin\HomeController@filterData');
    Route::get('/admin/search/{search?}', 'Admin\HomeController@search');
    Route::get('/admin/user/{id}', 'Admin\UserDetailController@userDetails');
    Route::post('/admin/password-reset-link','Admin\UserDetailController@sendResetLinkEmail');
	Route::get('/admin/myprofile', 'Admin\MyprofileController@index'); 
    Route::post('/admin/updateProfile', 'Admin\MyprofileController@updateProfile'); 
    Route::any('/admin/change-password', 'Admin\MyprofileController@changePassword');
    Route::any('/admin/save-password', 'Admin\MyprofileController@savePassword');
	
	// setting page 
	Route::any('/admin/manage-variables','Admin\SettingController@index');  
	// admin user
        Route::any('admin/user-manage','Admin\adminUserController@index');
        Route::post('/admin/user', 'Admin\adminUserController@create');
	    Route::put('/admin/active', 'Admin\adminUserController@active');
	    Route::put('/admin/deactive', 'Admin\adminUserController@deactive');
	    Route::put('/admin/edit', 'Admin\adminUserController@editUser');
	    Route::delete('/admin/delete', 'Admin\adminUserController@deleteUser');
       // plan 
        Route::get('admin/manage-plans', 'Admin\adminManagePlanController@index');
        Route::post('/admin/plan-create', 'Admin\adminManagePlanController@create');
        Route::put('/admin/plan-edit', 'Admin\adminManagePlanController@editPlan');
        Route::delete('/admin/plan-delete','Admin\adminManagePlanController@deletePlan');
        Route::put('/admin/plan/active','Admin\adminManagePlanController@activePlan');
        Route::put('/admin/plan/deactive','Admin\adminManagePlanController@deactivePlan');

         // Coupan
        Route::get('/admin/coupon-manage', 'Admin\adminCouponController@index');
        Route::post('/admin/coupon-create', 'Admin\adminCouponController@create');
        Route::put('/admin/coupon/active', 'Admin\adminCouponController@activeCoupon');
        Route::put('/admin/coupon/deactive', 'Admin\adminCouponController@deactiveCoupon');
        Route::delete('/admin/coupon-delete', 'Admin\adminCouponController@deleteCoupon');
        Route::put('/admin/coupon-edit', 'Admin\adminCouponController@editCoupon');
        Route::get('admin/generate-pdf/{id}','Admin\invoiceGeneratorController@get_pdf_data');
        Route::post('admin/login_as_customer','Admin\UserDetailController@login_as_customer');
        
        Route::post('admin/extend_trial_approve','Admin\TrialExtendController@approveTrial');
        Route::post('admin/extend_trial_decline','Admin\TrialExtendController@declineTrial');
});

/** cron routes */
	Route::get('cron/xdate/run', 'Cron\UserNotificationCronController@sendXdateFollowUp');
    Route::get('cron/trial/run', 'Cron\TrialExpire@trialExpireData');
    Route::get('cron/bill/run', 'Cron\Billing@dua_amount_users');
    Route::get('cron/invoice/run', 'Cron\InvoiceGenerate@generate_invoice');
    Route::get('cron/most-referral/run','Cron\findMostReferral@find_most_referral');
    Route::get('cron/trial-extend-notification/run', 'Cron\TrialExtendNotification@get_trial_extend_request_data');
    Route::get('cron/cancel_account/run', 'Cron\CancelAccountCron@get_users');
    Route::get('cron/coupon_expire/run', 'Cron\CouponExpireCron@get_expired_coupon');
