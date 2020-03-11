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
Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function () {

    Route::post('login', 'AuthController@login');
    Route::get('logout', 'AuthController@logout');
    Route::get('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');

});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// }); 
Route::middleware('auth:api')->group(function () {
   //---------------------------------- REFERENCES -----------------------------------------------
   //DASHBOARD
   Route::get('dashboard/index/{payment_type}', 'References\DashboardController@index');
   Route::get('dashboard/payment/{payment_type}/{is_string}', 'References\DashboardController@getPaymentLine');
   //END DASHBOARD



   Route::get('categories', 'References\CategoriesController@index');
   //List single category
   Route::get('category/{id}', 'References\CategoriesController@show');
   //Create new category
   Route::post('category', 'References\CategoriesController@create');
   //Update category
   Route::put('category/{id}', 'References\CategoriesController@update');
   //Delete category
   Route::put('category/delete/{id}', 'References\CategoriesController@delete');
   //Check if category was used
   Route::get('categorycheck/{id}', 'References\CategoriesController@checkIfUsed');
   // END categories


     //---------------------------------- UTILITIES ------------------------------------------------
    //USER
    //List users
    Route::get('users', 'Utilities\UsersController@index');
    //Profile
    Route::get('user/profile', 'Utilities\UsersController@Profile');
    Route::post('user/profile/update', 'Utilities\UsersController@SaveProfile');
    //List single user
    Route::get('user/{id}', 'Utilities\UsersController@show');
    //Create new user
    Route::post('user', 'Utilities\UsersController@create');
    //Update user
    Route::put('user/{id}', 'Utilities\UsersController@update');
    //Delete user
    Route::put('user/delete/{id}', 'Utilities\UsersController@delete');
    // END USER

    //List user groups
    Route::get('usergroups', 'Utilities\UserGroupsController@index');
    Route::get('group_rights/{user_group_id}', 'Utilities\UserGroupsController@getGroupRights');
    //List single user group
    Route::get('usergroup/{id}', 'Utilities\UserGroupsController@show');
    //Create new user group
    Route::post('usergroup', 'Utilities\UserGroupsController@create');
    Route::post('usergroup/rights/{id}', 'Utilities\UserGroupsController@createGroupRights');
    //Update user group
    Route::put('usergroup/{id}', 'Utilities\UserGroupsController@update');
    //Delete user group
    Route::put('usergroup/delete/{id}', 'Utilities\UserGroupsController@delete');
    //Check if user group was used
    Route::get('usergroupcheck/{id}', 'Utilities\UserGroupsController@checkIfUsed');
    Route::get('modules', 'Utilities\UserGroupsController@getModuleList');
    // END user groups

    //COMPANY
    //List company
    Route::get('companysettings', 'Utilities\CompanySettingsController@index');
    Route::post('companysetting/notes', 'Utilities\CompanySettingsController@insertNotes');
    Route::get('csdepartments', 'Utilities\CompanySettingsController@departments');
    //List single company
    Route::get('companysetting/{id}', 'Utilities\CompanySettingsController@show');
    Route::get('companysettingnotes', 'Utilities\CompanySettingsController@showNotes');
    //Create new company
    Route::post('companysetting', 'Utilities\CompanySettingsController@create');
    //Update company
    Route::put('companysetting/{id}', 'Utilities\CompanySettingsController@update');
    //Delete company
    Route::put('companysetting/delete/{id}', 'Utilities\CompanySettingsController@delete');
    // END COMPANY
     //---------------------------------- UTILITIES ------------------------------------------------

    //---------------------------------- SESSIONS -------------------------------------------------
    Route::get('session/rights', 'SessionController@getRights');
    //---------------------------------- FILE UPLOAD ----------------------------------------------

});