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
/* Cron Job Url */
/* Route::match(['get','post'],'/generateHourlyReport', 'GenerateHourlyReportController');
Route::match(['get','post'],'/reset_max_rpm', 'CronController@reset_max_rpm');
Route::match(['get','post'],'/delete_5min_record', 'CronController@delete_5min_record');
Route::match(['get','post'],'/delete_1hour_record', 'CronController@delete_1hour_record');
Route::match(['get','post'],'/send_sms_for_disconnected_machines', 'CronController@send_sms_for_disconnected_machines');
Route::match(['get','post'],'/send_hourly_stop_machine_notification', 'CronController@send_hourly_stop_machine_notification');
Route::match(['get','post'],'/send_12hour_report_notification', 'CronController@send_12hour_report_notification'); */


/*******************************************************************
 *                                                                 *
 * Customers Routes with Different Controllers and Actions/Methods *
 *                                                                 *
 *******************************************************************/
Route::match(['get','post'],'/', 'User\MachinesController@dashboard')->name('customer.dashboard');
// Route::get('/dashboard', 'User\MachinesController@dashboard');
Auth::routes();

// Customer
Route::match(['get','post'],'/profile/about/', 'User\CustomersController@about')->name('customers.profile.about');
Route::match(['get','post'],'/profile/change_password/', 'User\CustomersController@change_password')->name('customers.profile.change.password');

// Machines
Route::match(['get','post'],'/machines/list/', 'User\MachinesController@index')->name('machines.list');
Route::match(['get','post'],'/machines/disconnected/', 'User\MachinesController@disconnected')->name('machines.list.disconnected');
Route::match(['get','post'],'/machines/update/{id}', 'User\MachinesController@update')->name('machines.update');
Route::match(['get','post'],'/machines/settings/buzzer/{id}', 'User\MachinesController@setting_buzzer')->name('machines.settings.buzzer');
Route::post('machines/change-machine-group', 'User\MachinesController@change_machine_group')->name('change.machine.group');
Route::post('machines/change-machine-worker/{shift?}', 'User\MachinesController@change_machine_worker')->name('change.machine.worker');
Route::post('machines/change-machine-name', 'User\MachinesController@change_machine_name')->name('change.machine.name');
Route::post('machines/change-machine-number', 'User\MachinesController@change_machine_number')->name('change.machine.number');
Route::post('machines/get-machines-list-by-group', 'User\MachinesController@get_machines_by_group')->name('machines.list.by.group');

// Bonus - Fixed
Route::match(['get','post'],'/bonuses/fixed/', 'User\BonusesController@index_fixed')->name('bonuses.fixed.list');
Route::match(['get','post'],'/bonuses/fixed/create/', 'User\BonusesController@create_fixed')->name('bonuses.fixed.create');
Route::match(['get','post'],'/bonuses/fixed/update/{id}', 'User\BonusesController@update_fixed')->name('bonuses.fixed.update');
Route::get('/bonuses/fixed/delete/{id}', 'User\BonusesController@delete_fixed')->name('bonuses.fixed.delete');

// Bonus - Range Wise
Route::match(['get','post'],'/bonuses/range-wise/', 'User\BonusesController@index_range_wise')->name('bonuses.range.wise.list');
Route::match(['get','post'],'/bonuses/range-wise/create/', 'User\BonusesController@create_range_wise')->name('bonuses.range.wise.create');
Route::match(['get','post'],'/bonuses/range-wise/update/{id}', 'User\BonusesController@update_range_wise')->name('bonuses.range.wise.update');
Route::get('/bonuses/range-wise/delete/{id}', 'User\BonusesController@delete_range_wise')->name('bonuses.range.wise.delete');


// Worker
Route::match(['get','post'],'/workers/list/', 'User\WorkersController@index')->name('workers.list');
Route::match(['get','post'],'/workers/add', 'User\WorkersController@create')->name('workers.add');
Route::match(['get','post'],'/workers/update/{id}', 'User\WorkersController@update')->name('workers.update');
Route::get('/workers/change-status/{customer_id}/{status_id}/{inactivate_reason?}', 'User\WorkersController@change_status')->name('workers.status.change');

// Customers
Route::match(['get','post'],'/supervisors/list/', 'User\SupervisorsController@index')->name('supervisors.list');
Route::match(['get','post'],'/supervisors/add', 'User\SupervisorsController@create')->name('supervisors.add');
Route::match(['get','post'],'/supervisors/update/{id}', 'User\SupervisorsController@update')->name('supervisors.update');
Route::post('/supervisors/delete/{id}', 'User\SupervisorsController@delete')->name('supervisors.delete');
Route::match(['get','post'],'/supervisors/{id}/permission', 'User\SupervisorsController@getPermission');
Route::match(['get','post'],'/supervisors/permission/update/{id}', 'User\SupervisorsController@updatePermission');
Route::get('/supervisors/change-status/{customer_id}/{status_id}/{inactivate_reason?}', 'User\SupervisorsController@change_status')->name('supervisors.status.change');


// Machine Groups
Route::match(['get','post'],'/machine-groups/list/', 'User\MachineGroupsController@index')->name('machine.groups.list');
Route::match(['get','post'],'/machine-groups/add', 'User\MachineGroupsController@create')->name('machine.groups.add');
Route::match(['get','post'],'/machine-groups/update/{id}', 'User\MachineGroupsController@update')->name('machine.groups.update');
Route::match(['get','post'],'/machine-groups/delete/{id}', 'User\MachineGroupsController@delete')->name('machine.groups.delete');

// Settings
Route::match(['get','post'],'/settings/', 'User\SettingsController@general')->name('settings.general');
Route::match(['get','post'],'/settings/color-ranges/', 'User\SettingsController@color_range')->name('settings.color.range');
Route::match(['get','post'],'/settings/supervisor-permissions/', 'User\SettingsController@supervisor_permissions')->name('settings.supervisor.permissions');
Route::match(['get','post'],'/settings/machine_setting/','User\SettingsController@machineSetting')->name('settings.machine');

// Password
Route::match(['get','post'],'/password/change_password/','User\PasswordController@changePassword')->name('password.change_password');
Route::post('/forgot_password/','Auth\User\ForgotPasswordController@forgotPassword')->name('login.forgot_password');

// Reports
Route::match(['get','post'],'/reports/production', 'User\ReportsController@production')->name('reports.production');
Route::match(['get','post'],'/reports/production/export', 'User\ReportsController@production_export')->name('reports.production.export');
Route::match(['get','post'],'/reports/average', 'User\ReportsController@average')->name('reports.average');
Route::match(['get','post'],'/reports/average/export', 'User\ReportsController@average_export')->name('reports.average.export');
Route::match(['get','post'],'/reports/average_weekly', 'User\ReportsController@average_weekly')->name('reports.average_weekly');
Route::match(['get','post'],'/reports/average_weekly/export', 'User\ReportsController@average_weekly_export')->name('reports.average_weekly.export');
Route::match(['get','post'],'/reports/worker/total', 'User\ReportsController@worker_total')->name('reports.worker.total');
Route::match(['get','post'],'/reports/worker/total/export', 'User\ReportsController@worker_total_export')->name('reports.worker.total.export');
Route::match(['get','post'],'/reports/worker/salary', 'User\ReportsController@worker_salary')->name('reports.worker.salary');
Route::match(['get','post'],'/reports/worker/salary/export', 'User\ReportsController@worker_salary_export')->name('reports.worker.salary.export');
Route::match(['get','post'],'/graphs/production', 'User\GraphsController@production')->name('graphs.production');
Route::match(['get','post'],'/graphs/production/export', 'User\GraphsController@production_export')->name('graphs.production.export');
Route::match(['get','post'],'/graphs/average', 'User\GraphsController@average')->name('graphs.average');
Route::match(['get','post'],'/graphs/average/export', 'User\GraphsController@average_export')->name('graphs.average.export');
Route::match(['get','post'],'/graphs/average_weekly', 'User\GraphsController@average_weekly')->name('graphs.average_weekly');
Route::match(['get','post'],'/graphs/average_weekly/export', 'User\GraphsController@average_weekly_export')->name('graphs.average_weekly.export');

// Manual Reading
Route::match(['get','post'],'/reading/production', 'User\ReadingController@production')->name('reading.production');
Route::post('/reading/update_reading', 'User\ReadingController@updateReading')->name('reading.update_reading');

/****************************************************************
 *                                                              *
 * Admins Routes with Different Controllers and Actions/Methods *
 *                                                              *
 ****************************************************************/
Route::get('check_site_status',function(){
	return response()->json(["status"=>1],200);
});
Route::get('/customers/login', 'Auth\CustomerLoginController')->name('customers.login');
Route::prefix('admin')->group(function (){
	
	// Login
	Route::get('login', 'Auth\Admin\LoginController@showLoginForm')->name('admin.login');
	Route::post('login', 'Auth\Admin\LoginController@login');
	Route::get('/', 'Admin\AdminController@index')->name('admin.dashboard');

	// Permission
	Route::resource('/permission','Admin\PermissionController');

	// Setting
	Route::match(['get','post'],'/setting/head_setting','Admin\SettingController@setHead');
	Route::match(['get','post'],'/setting/machine_setting','Admin\SettingController@machineSetting');
	Route::match(['get','post'],'/setting/shift_setting','Admin\SettingController@shiftSetting');
	Route::get('/setting/getMachines/{id}','Admin\SettingController@getMachines');

	// Customers
	Route::match(['get','post'],'/customers/list/', 'Admin\CustomersController@index')->name('admin.customers.list');
	Route::match(['get','post'],'/customers/add', 'Admin\CustomersController@create')->name('admin.customers.add');
	Route::match(['get','post'],'/customers/update/{id}', 'Admin\CustomersController@update')->name('admin.customers.update');
	Route::post('/customers/delete/{id}', 'Admin\CustomersController@delete')->name('admin.customers.delete');
	Route::match(['get','post'],'/customers/settings/general_settings/{id}', 'Admin\CustomersController@general_settings')->name('admin.customers.settings.general_settings');
	Route::match(['get','post'],'/customers/settings/sms_recharge/{id}', 'Admin\CustomersController@sms_recharge')->name('admin.customers.settings.sms_recharge');
	Route::match(['get','post'],'/customers/settings/shift-change/{id}', 'Admin\CustomersController@shift_change')->name('admin.customers.settings.shift.change');
	Route::match(['get','post'],'/customers/settings/reports_settings/{id}', 'Admin\CustomersController@reports_settings')->name('admin.customers.settings.reports_settings');
	Route::get('/customers/change-status/{customer_id}/{status_id}/{inactivate_reason?}', 'Admin\CustomersController@change_status')->name('admin.customers.status.change');
	Route::post('/customers/sendOTP', 'Admin\CustomersController@sendOTP');

	// Machines
	Route::match(['get','post'],'/machines/list/', 'Admin\MachinesController@index')->name('admin.machines.list');
	Route::match(['get','post'],'/machines/create', 'Admin\MachinesController@create')->name('admin.machines.create');
	Route::match(['get','post'],'/machines/update/{id}', 'Admin\MachinesController@update')->name('admin.machines.update');
	Route::post('/machines/delete/{id}', 'Admin\MachinesController@delete')->name('admin.machines.delete');
	Route::get('/machines/change-status/{machine_id}/{status_id}/{inactivate_reason?}', 'Admin\MachinesController@change_status')->name('admin.machines.status.change');
	Route::post('/machines/change-rpm-cal', 'Admin\MachinesController@change_rpm_cal')->name('admin.machines.change.rpm.cal');
	Route::match(['get','post'],'/device-calibration/', 'Admin\MachinesController@calibration')->name('admin.machines.calibration');
	Route::match(['get','post'],'/save-device-calibration/', 'Admin\MachinesController@save_calibration')->name('admin.machines.calibration.save');
	

	// Profile
	Route::match(['get','post'],'/profile/about/', 'Admin\AdminController@about')->name('admin.profile.about');
	Route::match(['get','post'],'/profile/change-password/', 'Admin\AdminController@change_password')->name('admin.profile.change.password');

});


/****************************************************************
 *                                                              *
 * API V1 Routes with Different Controllers and Actions/Methods *
 *                                                              *
 ****************************************************************/

/* Route::prefix('api/v1')->group(function (){

	// Check Login
	Route::post('login', 'API\CustomersAPIController@login');
	Route::post('settings/common', 'API\CustomersAPIController@common_settings');
	Route::post('settings/color-range', 'API\CustomersAPIController@color_range');

	// Worker
	Route::post('workers/get', 'API\WorkerAPIController@list');
	Route::post('workers/create', 'API\WorkerAPIController@create');
	Route::post('workers/change-status', 'API\WorkerAPIController@change_status');
	
	// Machine
	Route::post('machines/get', 'API\MachinesAPIController@get');
	Route::post('machines/settings/buzzer', 'API\MachinesAPIController@save_machine_buzzer_setting');
	Route::post('machines/machines-by-group', 'API\MachinesAPIController@get_machines_by_group');
	Route::post('machines/group-by-customer', 'API\MachinesAPIController@get_group_by_cust');

	// Reports
	Route::post('reports/production', 'API\ReportAPIController@production');
	Route::post('reports/average', 'API\ReportAPIController@average');
	Route::post('reports/worker/total', 'API\ReportAPIController@worker_total');
	Route::post('reports/worker/salary', 'API\ReportAPIController@worker_salary');
}); */

