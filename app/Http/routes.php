<?php

/*
 * Api Routing
 * */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'Merobot\Http\Controllers'], function ($api) {

    /*Store*/
    $api->post('shop', 'Api\V1\ShopController@setShop');
    $api->get('shop/{id}', 'Api\V1\ShopController@getShop');
    $api->delete('shop/{id}', 'Api\V1\ShopController@deleteShop');

    /*Robots*/
    $api->post('shop/{id}/robot', 'Api\V1\RobotController@setRobot');
    $api->put('shop/{id}/robot/{rid}', 'Api\V1\RobotController@updateRobot');
    $api->delete('shop/{id}/robot/{rid}', 'Api\V1\RobotController@deleteRobot');

    /*Execute*/
    $api->post('shop/{id}/execute', 'Api\V1\RunnerController@execute');

    //Ends/
});

/*
 * Home Page
 * */
Route::get('/', function () {
   return view('welcome');
});

Route::get('/api', function () {
    return view('api');
});