<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('/events', 'EventsController@index');
Route::get('/events/projects', 'EventsController@projects');
Route::get('/events/estates', 'EventsController@estates');
Route::get('/events/current-data/{id}', 'EventsController@currentData');
Route::post('/events/store', 'EventsController@store');
Route::put('/events/{event_id}/update', 'EventsController@update');
Route::delete('/events/{event_id}/destroy', 'EventsController@destroy');
