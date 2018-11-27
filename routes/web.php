<?php

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

use Illuminate\Http\Request;

Route::get('/', function () {
    //return view('welcome');
	return redirect()->route('user_overview');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('foo/name/{name?}', function ($name = '') {
		return 'Hello World '.$name;
	});
	Route::get('db/users', function () {
		config(['adminlte.plugins.datatables' => true]);
		return view('dashboard');
	})->name('user_overview');
	Route::get('db/stats', function () {
		config(['adminlte.plugins.datatables' => true]);
		return view('stats');
	})->name('user_stats');
	Route::get('db/comments', function () {
		config(['adminlte.plugins.datatables' => true]);
		return view('comments');
	})->name('user_comments');
	Route::get('db/requests', function () {
		config(['adminlte.plugins.datatables' => true]);
		return view('requests');
	})->name('user_requests');
	Route::get('db/reports', function () {
		config(['adminlte.plugins.datatables' => true]);
		return view('reports');
	})->name('user_reports');
	Route::get('db/errors', function () {
		config(['adminlte.plugins.datatables' => true]);
		return view('errors');
	})->name('user_errors');
	Route::get('db/links', function () {
		config(['adminlte.plugins.datatables' => true]);
		return view('links');
	})->name('links');
	Route::get('db/emails', function () {
		config(['adminlte.plugins.datatables' => true]);
		return view('emails');
	})->name('emails');
	Route::get('db/likes', function () {
		config(['adminlte.plugins.datatables' => true]);
		return view('likes');
	})->name('likes');
	Route::get('activity/api', function () {
		return view('activity-api');
	})->name('activity-api');
	
	// actions 
	
	// restore item after it's reported
    Route::get('fix/revert-report/{item_id}', function ($item_id = 0) {
		// mark item as ACTIVE and hide the reports 
		$items = DB::connection("aurora")->update('UPDATE item SET item_active = 1 WHERE item_id = ? LIMIT 1', [$item_id]);
		$items = DB::connection("aurora")->update('UPDATE report_item SET report_item_active = 0 WHERE item_id = ?', [$item_id]);
		return redirect()->route('user_reports');
	})->where('item_id', '[0-9]+');
    
	// mark comment as read 
	Route::get('fix/comment/{contact_id}', function ($contact_id = 0) {
		// mark comment as read 
		$items = DB::connection("aurora")->update('UPDATE contact SET contact_active = 0 WHERE contact_id = ? LIMIT 1', [$contact_id]);
		return redirect()->route('user_comments');
	})->where('contact_id', '[0-9]+');
    
	// mark retailer request as read 
	Route::get('fix/request/{retailer_request_id}', function ($retailer_request_id = 0) {
		// mark request as read 
		$items = DB::connection("aurora")->update('UPDATE retailer_request SET retailer_request_active = 0 WHERE retailer_request_id = ? LIMIT 1', [$retailer_request_id]);
		return redirect()->route('user_requests');
	})->where('retailer_request_id', '[0-9]+');
    
	// add tracking link 
	Route::post('links/add', function (Request $request) {
		// store data 
		$items = DB::insert('INSERT INTO short_link (short_link_name, short_link_url, short_link_comment) VALUES (?, ?, ?)', 
			[$request->short_link_name, $request->short_link_url, $request->short_link_comment]);
		return redirect()->route('links');
	});
	
	// toggle the sidebar for the session 
	Route::get('toggleSidebar', function (Request $request) {
		$value = session('hideSidebar');
		if($value == 1){
			session(['hideSidebar' => 0]);
		}else{
			session(['hideSidebar' => 1]);
		}
		return redirect('db');
	});
});

Auth::routes();

Route::get('/data/agg_users', 'DataTablesController@agg_users');
Route::get('/data/agg_comments', 'DataTablesController@agg_comments');
Route::get('/data/agg_requests', 'DataTablesController@agg_requests');
Route::get('/data/agg_reports', 'DataTablesController@agg_reports');
Route::get('/data/agg_errors', 'DataTablesController@agg_errors');
Route::get('/data/agg_links', 'DataTablesController@agg_links');
Route::get('/data/agg_likes', 'DataTablesController@agg_likes');
Route::get('/data/emails', 'DataTablesController@emails');

Route::get('/data/categories', 'DataController@categories');
Route::get('/data/stats', 'DataController@stats');
Route::get('/data/activity-api', 'DataController@activity_api');
Route::post('/fix/category', 'DataController@change_category');
Route::get('/fix/valid-report/{item_id}', 'DataController@valid_report')->where('item_id', '[0-9]+');
Route::get('/emails/item/{item_id}', 'DataController@matched_users')->where('item_id', '[0-9]+');
Route::post('/emails/new', 'DataController@email_matched_users');
Route::get('/refresh/item', 'DataController@refresh_item');
