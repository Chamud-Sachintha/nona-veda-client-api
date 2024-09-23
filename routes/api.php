<?php

use App\Http\Controllers\ClientInfoController;
use App\Http\Controllers\QuestionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('add-new-client', [ClientInfoController::class, 'addNewClinetInfo']);
Route::post('get-question-list', [QuestionController::class, 'getAllQuestionList']);
Route::post('submit-results', [QuestionController::class, 'submitQuestionResponse']);
