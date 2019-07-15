<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Check Login
Route::post('v1/login','Auth\LoginController@login');
Route::post('v1/logout','Auth\LoginController@userLogout');

Route::post('test_ios_notify', 'API\NotificationController@test_ios_notify');
Route::post('test_android_notify', 'API\NotificationController@test_ios_notify');

Route::middleware('auth:api')->prefix('v1')->group(function() {
	

	Route::post('settings/common', 'API\CustomersAPIController@common_settings');
    Route::post('settings/color-range', 'API\CustomersAPIController@color_range');
    
    // Worker
	Route::get('workers/get', 'API\WorkerAPIController@getWorkers');
	Route::post('workers/getWorker', 'API\WorkerAPIController@getWorker');
	Route::post('workers/create', 'API\WorkerAPIController@createWorker');
	Route::post('workers/update', 'API\WorkerAPIController@updateWorker');
	// Route::post('workers/delete/{id}', 'API\WorkerAPIController@deleteWorker');
    Route::post('workers/change-status', 'API\WorkerAPIController@change_status');
    
    // Machine
	Route::post('machines/get', 'API\MachinesAPIController@getMachines');
	Route::post('machines/changeWorker', 'API\MachinesAPIController@changeWorker');
	// Route::post('machines/settings/buzzer', 'API\MachinesAPIController@save_machine_buzzer_setting');
	Route::post('machines/machines-by-group', 'API\MachinesAPIController@getMachinesByGroup');
	Route::get('machines/getUnassignedMachine', 'API\MachinesAPIController@getUnassignedMachine');
    // Route::post('machines/group-by-customer', 'API\MachinesAPIController@get_group_by_cust');
    
    // Reports
	Route::post('reports/production', 'API\ReportAPIController@productionReport');
	Route::post('reports/average', 'API\ReportAPIController@averageReport');
	Route::post('reports/average-weekly', 'API\ReportAPIController@averageWeeklyReport');
	Route::post('reports/worker/total', 'API\ReportAPIController@workerTotalReport');
	Route::post('reports/worker/salary', 'API\ReportAPIController@workerSalaryReport');

	// Bonus - Fixed
	// Route::post('/bonuses/fixed/', 'User\BonusesController@index_fixed')->name('bonuses.fixed.list');
	// Route::post('/bonuses/fixed/create/', 'User\BonusesController@create_fixed')->name('bonuses.fixed.create');
	// Route::post('/bonuses/fixed/update/{id}', 'User\BonusesController@update_fixed')->name('bonuses.fixed.update');
	// Route::post('/bonuses/fixed/delete/{id}', 'User\BonusesController@delete_fixed')->name('bonuses.fixed.delete');

	// Bonus - Range Wise
	// Route::post('/bonuses/range-wise/', 'User\BonusesController@index_range_wise')->name('bonuses.range.wise.list');
	// Route::post('/bonuses/range-wise/1/', 'User\BonusesController@create_range_wise')->name('bonuses.range.wise.create');
	// Route::post('/bonuses/range-wise/update/{id}', 'User\BonusesController@update_range_wise')->name('bonuses.range.wise.update');
	// Route::post('/bonuses/range-wise/delete/{id}', 'User\BonusesController@delete_range_wise')->name('bonuses.range.wise.delete');
});
