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


/*
|--------------------------------------------------------------------------
| Upgrading
|--------------------------------------------------------------------------
|
| The upgrading process routes
|
*/
Route::group(['middleware' => ['web'], 'namespace' => 'App\Http\Controllers'], function () {
	Route::get('upgrade', 'UpgradeController@version');
});


/*
|--------------------------------------------------------------------------
| Installation
|--------------------------------------------------------------------------
|
| The installation process routes
|
*/
Route::group([
	'middleware' => ['web', 'installChecker'],
	'namespace'  => 'App\Http\Controllers',
], function () {
	Route::get('install', 'InstallController@starting');
	Route::get('install/site_info', 'InstallController@siteInfo');
	Route::post('install/site_info', 'InstallController@siteInfo');
	Route::get('install/system_compatibility', 'InstallController@systemCompatibility');
	Route::get('install/database', 'InstallController@database');
	Route::post('install/database', 'InstallController@database');
	Route::get('install/database_import', 'InstallController@databaseImport');
	Route::get('install/cron_jobs', 'InstallController@cronJobs');
	Route::get('install/finish', 'InstallController@finish');
});


/*
|--------------------------------------------------------------------------
| Back-end
|--------------------------------------------------------------------------
|
| The admin panel routes
|
*/
Route::group([
	'namespace'  => 'App\Http\Controllers\Admin',
	'middleware' => ['web', 'installChecker'],
	'prefix'     => config('larapen.admin.route_prefix', 'admin'),
], function ($router) {
	// Auth
	Route::auth();
	Route::get('logout', 'Auth\LoginController@logout');
	
	// Admin Panel Area
	Route::group([
		'middleware' => ['admin', 'clearance', 'bannedUser', 'preventBackHistory'],
	], function ($router) {
		// Dashboard
		Route::get('dashboard', 'DashboardController@dashboard');
		Route::get('/', 'DashboardController@redirect');
		
		// Extra (must be called before CRUD)
		Route::get('homepage/{action}', 'HomeSectionController@reset')->where('action', 'reset_(.*)');
		Route::get('languages/sync_files', 'LanguageController@syncFilesLines');
		Route::get('permissions/create_default_entries', 'PermissionController@createDefaultEntries');
		
		// CRUD
		CRUD::resource('advertisings', 'AdvertisingController');
		CRUD::resource('blacklists', 'BlacklistController');
		CRUD::resource('categories', 'CategoryController');
		CRUD::resource('categories/{catId}/subcategories', 'SubCategoryController');
		CRUD::resource('cities', 'CityController');
		CRUD::resource('companies', 'CompanyController');
		CRUD::resource('countries', 'CountryController');
		CRUD::resource('countries/{countryCode}/cities', 'CityController');
		CRUD::resource('countries/{countryCode}/admins1', 'SubAdmin1Controller');
		CRUD::resource('currencies', 'CurrencyController');
		CRUD::resource('genders', 'GenderController');
		CRUD::resource('homepage', 'HomeSectionController');
		CRUD::resource('admins1/{admin1Code}/cities', 'CityController');
		CRUD::resource('admins1/{admin1Code}/admins2', 'SubAdmin2Controller');
		CRUD::resource('admins2/{admin2Code}/cities', 'CityController');
		CRUD::resource('languages', 'LanguageController');
		CRUD::resource('meta_tags', 'MetaTagController');
		CRUD::resource('packages', 'PackageController');
		CRUD::resource('pages', 'PageController');
		CRUD::resource('payments', 'PaymentController');
		CRUD::resource('payment_methods', 'PaymentMethodController');
		CRUD::resource('permissions', 'PermissionController');
		CRUD::resource('pictures', 'PictureController');
		CRUD::resource('posts', 'PostController');
		CRUD::resource('p_types', 'PostTypeController');
		CRUD::resource('report_types', 'ReportTypeController');
		CRUD::resource('roles', 'RoleController');
		CRUD::resource('salary_types', 'SalaryTypeController');
		CRUD::resource('settings', 'SettingController');
		CRUD::resource('time_zones', 'TimeZoneController');
		CRUD::resource('users', 'UserController');
		CRUD::resource('withdrawals', 'WithdrawalController');
		
		// Others
		Route::get('account', 'UserController@account');
		Route::post('ajax/{table}/{field}', 'InlineRequestController@make');
		
		// Backup
		Route::get('backups', 'BackupController@index');
		Route::put('backups/create', 'BackupController@create');
		Route::get('backups/download/{file_name?}', 'BackupController@download');
		Route::delete('backups/delete/{file_name?}', 'BackupController@delete')->where('file_name', '(.*)');
		
		// Actions
		Route::get('actions/clear_cache', 'ActionController@clearCache');
		Route::get('actions/call_ads_cleaner_command', 'ActionController@callAdsCleanerCommand');
		Route::post('actions/maintenance_down', 'ActionController@maintenanceDown');
		Route::get('actions/maintenance_up', 'ActionController@maintenanceUp');
		
		// Re-send Email or Phone verification message
		Route::get('verify/user/{id}/resend/email', 'UserController@reSendVerificationEmail');
		Route::get('verify/user/{id}/resend/sms', 'UserController@reSendVerificationSms');
		Route::get('verify/post/{id}/resend/email', 'PostController@reSendVerificationEmail');
		Route::get('verify/post/{id}/resend/sms', 'PostController@reSendVerificationSms');
		
		// Plugins
		Route::get('plugins', 'PluginController@index');
		Route::post('plugins/{plugin}/install', 'PluginController@install');
		Route::get('plugins/{plugin}/install', 'PluginController@install');
		Route::get('plugins/{plugin}/uninstall', 'PluginController@uninstall');
		Route::get('plugins/{plugin}/delete', 'PluginController@delete');

		/* WITHDRAWAL ROUTES */

		Route::put('/confirm/withdrawal','WithdrawalController@confirmWithdrawal')->name('confirm.withdrawal')->middleware('auth');
		


	});
});


/*
|--------------------------------------------------------------------------
| Front-end
|--------------------------------------------------------------------------
|
| The not translated front-end routes
|
*/
Route::group([
	'middleware' => ['web', 'installChecker'],
	'namespace'  => 'App\Http\Controllers',
], function ($router) {
	// SEO
	Route::get('sitemaps.xml', 'SitemapsController@index');
	
	// Impersonate (As admin user, login as an another user)
	Route::group(['middleware' => 'auth'], function ($router) {
		Route::impersonate();
	});
});


/*
|--------------------------------------------------------------------------
| Front-end
|--------------------------------------------------------------------------
|
| The translated front-end routes
|
*/
Route::group([
	'prefix'     => LaravelLocalization::setLocale(),
	'middleware' => ['local'],
	'namespace'  => 'App\Http\Controllers',
], function ($router) {
	Route::group(['middleware' => ['web', 'installChecker']], function ($router) {
		// HOMEPAGE
		Route::get('/', 'HomeController@index');
		Route::get(LaravelLocalization::transRoute('routes.countries'), 'CountriesController@index');
		
		
		// AUTH
		Route::group(['middleware' => ['guest', 'preventBackHistory']], function () {
			// Registration Routes...
			Route::get(LaravelLocalization::transRoute('routes.register'), 'Auth\RegisterController@showRegistrationForm');
			Route::post(LaravelLocalization::transRoute('routes.register'), 'Auth\RegisterController@register');
			Route::get('register/finish', 'Auth\RegisterController@finish');
			
			// Authentication Routes...
			Route::get(LaravelLocalization::transRoute('routes.login'), 'Auth\LoginController@showLoginForm');
			Route::post(LaravelLocalization::transRoute('routes.login'), 'Auth\LoginController@login');
			
			// Forgot Password Routes...
			Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
			Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
			
			// Reset Password using Token
			Route::get('password/token', 'Auth\ForgotPasswordController@showTokenRequestForm');
			Route::post('password/token', 'Auth\ForgotPasswordController@sendResetToken');
			
			// Reset Password using Link (Core Routes...)
			Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
			Route::post('password/reset', 'Auth\ResetPasswordController@reset');
			
			// Social Authentication
			Route::get('auth/facebook', 'Auth\SocialController@redirectToProvider');
			Route::get('auth/facebook/callback', 'Auth\SocialController@handleProviderCallback');
			Route::get('auth/google', 'Auth\SocialController@redirectToProvider');
			Route::get('auth/google/callback', 'Auth\SocialController@handleProviderCallback');
			Route::get('auth/twitter', 'Auth\SocialController@redirectToProvider');
			Route::get('auth/twitter/callback', 'Auth\SocialController@handleProviderCallback');
		});
		
		// Email Address or Phone Number verification
		$router->pattern('field', 'email|phone');
		Route::get('verify/user/{id}/resend/email', 'Auth\RegisterController@reSendVerificationEmail');
		Route::get('verify/user/{id}/resend/sms', 'Auth\RegisterController@reSendVerificationSms');
		Route::get('verify/user/{field}/{token?}', 'Auth\RegisterController@verification');
		Route::post('verify/user/{field}/{token?}', 'Auth\RegisterController@verification');
		
		// User Logout
		Route::get(LaravelLocalization::transRoute('routes.logout'), 'Auth\LoginController@logout');
		
		
		// POSTS
		Route::group(['namespace' => 'Post'], function ($router) {
			$router->pattern('id', '[0-9]+');
			// $router->pattern('slug', '.*');
			$router->pattern('slug', '^(?=.*)((?!\/).)*$');
			
			Route::get('posts/create/{tmpToken?}', 'CreateController@getForm');
			Route::post('posts/create', 'CreateController@postForm');
			Route::put('posts/create/{tmpToken}', 'CreateController@postForm');
			Route::get('posts/create/{tmpToken}/payment', 'PaymentController@getForm');
			Route::post('posts/create/{tmpToken}/payment', 'PaymentController@postForm');
			Route::get('posts/create/{tmpToken}/finish', 'CreateController@finish');
			
			// Payment Gateway Success & Cancel
			Route::get('posts/create/{tmpToken}/payment/success', 'PaymentController@paymentConfirmation');
			Route::get('posts/create/{tmpToken}/payment/cancel', 'PaymentController@paymentCancel');
			
			// Email Address or Phone Number verification
			$router->pattern('field', 'email|phone');
			Route::get('verify/post/{id}/resend/email', 'CreateController@reSendVerificationEmail');
			Route::get('verify/post/{id}/resend/sms', 'CreateController@reSendVerificationSms');
			Route::get('verify/post/{field}/{token?}', 'CreateController@verification');
			Route::post('verify/post/{field}/{token?}', 'CreateController@verification');
			
			Route::group(['middleware' => 'auth'], function ($router) {
				$router->pattern('id', '[0-9]+');
				
				Route::get('posts/{id}/edit', 'EditController@getForm');
				Route::put('posts/{id}/edit', 'EditController@postForm');
				Route::get('posts/{id}/payment', 'PaymentController@getForm');
				Route::post('posts/{id}/payment', 'PaymentController@postForm');
				
				// Payment Gateway Success & Cancel
				Route::get('posts/{id}/payment/success', 'PaymentController@paymentConfirmation');
				Route::get('posts/{id}/payment/cancel', 'PaymentController@paymentCancel');
			});
			
			// Post's Details
			Route::get(LaravelLocalization::transRoute('routes.post'), 'DetailsController@index');
			
			// Contact Job's Author
			Route::post('posts/{id}/contact', 'DetailsController@sendMessage');
			
			// Send report abuse
			Route::get('posts/{id}/report', 'ReportController@showReportForm');
			Route::post('posts/{id}/report', 'ReportController@sendReport');
		});
		Route::post('send-by-email', 'Search\SearchController@sendByEmail');
		
		
		// ACCOUNT
		Route::group(['middleware' => ['auth', 'bannedUser', 'preventBackHistory'], 'namespace' => 'Account'], function ($router) {
			$router->pattern('id', '[0-9]+');
			
			// Users
			Route::get('account', 'EditController@index');
			Route::group(['middleware' => 'impersonate.protect'], function () {
				Route::put('account', 'EditController@updateDetails');
				Route::put('account/settings', 'EditController@updateSettings');
				Route::put('account/preferences', 'EditController@updatePreferences');
			});
			Route::get('account/close', 'CloseController@index');
			Route::group(['middleware' => 'impersonate.protect'], function () {
				Route::post('account/close', 'CloseController@submit');
			});
			
			// Companies
			Route::get('account/companies', 'CompanyController@index');
			Route::get('account/companies/create', 'CompanyController@create');
			Route::post('account/companies', 'CompanyController@store');
			Route::get('account/companies/{id}', 'CompanyController@show');
			Route::get('account/companies/{id}/edit', 'CompanyController@edit');
			Route::put('account/companies/{id}', 'CompanyController@update');
			Route::get('account/companies/{id}/delete', 'CompanyController@destroy');
			Route::post('account/companies/delete', 'CompanyController@destroy');
			
			// Resumes
			Route::get('account/resumes', 'ResumeController@index');
			Route::get('account/resumes/create', 'ResumeController@create');
			Route::post('account/resumes', 'ResumeController@store');
			Route::get('account/resumes/{id}', 'ResumeController@show');
			Route::get('account/resumes/{id}/edit', 'ResumeController@edit');
			Route::put('account/resumes/{id}', 'ResumeController@update');
			Route::get('account/resumes/{id}/delete', 'ResumeController@destroy');
			Route::post('account/resumes/delete', 'ResumeController@destroy');
			
			// Posts
			Route::get('account/saved-search', 'PostsController@getSavedSearch');
			$router->pattern('pagePath', '(my-posts|archived|favourite|pending-approval|saved-search)+');
			Route::get('account/{pagePath}', 'PostsController@getPage');
			Route::get('account/{pagePath}/{id}/repost', 'PostsController@getArchivedPosts');
			Route::get('account/{pagePath}/{id}/delete', 'PostsController@destroy');
			Route::post('account/{pagePath}/delete', 'PostsController@destroy');
			
			// Conversations
			Route::get('account/conversations', 'ConversationsController@index');
			Route::get('account/conversations/{id}/delete', 'ConversationsController@destroy');
			Route::post('account/conversations/delete', 'ConversationsController@destroy');
			Route::post('account/conversations/{id}/reply', 'ConversationsController@reply');
			$router->pattern('msgId', '[0-9]+');
			Route::get('account/conversations/{id}/messages', 'ConversationsController@messages');
			Route::get('account/conversations/{id}/messages/{msgId}/delete', 'ConversationsController@destroyMessages');
			Route::post('account/conversations/{id}/messages/delete', 'ConversationsController@destroyMessages');
			
			// Transactions
			Route::get('account/transactions', 'TransactionsController@index')->name('account.transactions');
			
			
			/*	ADD CREDIT ROUTES	*/
		Route::get('/addcredit/{method_id?}', 'AddCreditController@addCreditForm')->name('add.credit')->middleware('auth');
		
		Route::post('/addcredit', 'AddCreditController@depositRequest')->name('post.credit')->middleware('auth');
		Route::get('/wallet/get/{id}', 'AddCreditController@wallet')->middleware('auth');
		
		
		/*	DEPOSITS ROUTES	*/
		Route::get('/mydeposits','DepositController@myDeposits')->name('mydeposits')->middleware('auth');
		Route::put('/confirm/deposit','DepositController@confirmDeposit')->name('confirm.deposit')->middleware('auth');

		/* send money routes*/
		
		Route::get('/sendmoney', 'MoneyTransferController@sendMoneyForm')->name('sendMoneyForm')->middleware('auth');
		Route::post('/sendmoney', 'MoneyTransferController@sendMoney')->name('sendMoney')->middleware('auth');

		Route::post('/sendMoneyConfirm', 'MoneyTransferController@sendMoneyConfirm')->name('sendMoneyConfirm')->middleware('auth');
		
		/* EXCHANGE ROUTES */
		Route::get('/exchange/first/{first_id?}/second/{second_id?}', 'ExchangeController@getExchangeRequestForm')->name('exchange.form')->middleware('auth');
		Route::post('/exchange/', 'ExchangeController@exchange')->name('post.exchange')->middleware('auth');
			
			
		});
		
		    

		
		
		
		

		
		
		// AJAX
		Route::group(['prefix' => 'ajax'], function ($router) {
			Route::get('countries/{countryCode}/admins/{adminType}', 'Ajax\LocationController@getAdmins');
			Route::get('countries/{countryCode}/admins/{adminType}/{adminCode}/cities', 'Ajax\LocationController@getCities');
			Route::get('countries/{countryCode}/cities/{id}', 'Ajax\LocationController@getSelectedCity');
			Route::post('countries/{countryCode}/cities/autocomplete', 'Ajax\LocationController@searchedCities');
			Route::post('countries/{countryCode}/admin1/cities', 'Ajax\LocationController@getAdmin1WithCities');
			Route::post('category/sub-categories', 'Ajax\CategoryController@getSubCategories');
			Route::post('save/post', 'Ajax\PostController@savePost');
			Route::post('save/search', 'Ajax\PostController@saveSearch');
			Route::post('post/phone', 'Ajax\PostController@getPhone');
			Route::post('messages/check', 'Ajax\ConversationController@checkNewMessages');
		});
		
		
		// FEEDS
		Route::feeds();
		
		
		// Country Code Pattern
		$countryCodePattern = implode('|', array_map('strtolower', array_keys(getCountries())));
		$router->pattern('countryCode', $countryCodePattern);
		
		
		// XML SITEMAPS
		Route::get('{countryCode}/sitemaps.xml', 'SitemapsController@site');
		Route::get('{countryCode}/sitemaps/pages.xml', 'SitemapsController@pages');
		Route::get('{countryCode}/sitemaps/categories.xml', 'SitemapsController@categories');
		Route::get('{countryCode}/sitemaps/cities.xml', 'SitemapsController@cities');
		Route::get('{countryCode}/sitemaps/posts.xml', 'SitemapsController@posts');
		
		
		// STATICS PAGES
		Route::get(LaravelLocalization::transRoute('routes.page'), 'PageController@index');
		Route::get(LaravelLocalization::transRoute('routes.contact'), 'PageController@contact');
		Route::post(LaravelLocalization::transRoute('routes.contact'), 'PageController@contactPost');
		Route::get(LaravelLocalization::transRoute('routes.sitemap'), 'SitemapController@index');
		Route::get(LaravelLocalization::transRoute('routes.companies-list'), 'Search\CompanyController@index');
		
		
		// DYNAMIC URL PAGES
		$router->pattern('id', '[0-9]+');
		$router->pattern('username', '[a-zA-Z0-9]+');
		Route::get(LaravelLocalization::transRoute('routes.search'), 'Search\SearchController@index');
		Route::get(LaravelLocalization::transRoute('routes.search-user'), 'Search\UserController@index');
		Route::get(LaravelLocalization::transRoute('routes.search-username'), 'Search\UserController@profile');
		Route::get(LaravelLocalization::transRoute('routes.search-company'), 'Search\CompanyController@profile');
		Route::get(LaravelLocalization::transRoute('routes.search-tag'), 'Search\TagController@index');
		Route::get(LaravelLocalization::transRoute('routes.search-city'), 'Search\CityController@index');
		Route::get(LaravelLocalization::transRoute('routes.search-subCat'), 'Search\CategoryController@index');
		Route::get(LaravelLocalization::transRoute('routes.search-cat'), 'Search\CategoryController@index');
	});
});
