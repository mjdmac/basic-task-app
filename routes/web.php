<?php

use App\Http\Controllers\ChartController;
use App\Http\Controllers\TaskController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
if (App::environment('development')) {
    URL::forceScheme('https');
}

Route::get('/', function () {
    if(Auth::check()){
        return redirect('/dashboard');
    }else{
        return Inertia::render('Auth/Login');
    }

});

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::resource('tasks', TaskController::class);

    Route::get('/tasks/{task}', [TaskController::class, 'show'])
    ->name('tasks.show')->middleware('owner');

    Route::post('/tasks/sort', [TaskController::class, 'orderByDrag']);

    Route::get('/dashboard', [TaskController::class, 'allTasks'])
    ->name('dashboard');

    Route::get('/dashboard/{task}', [TaskController::class, 'singleTask'])
    ->name('dashboard.show')->middleware('owner');

    Route::get('/recyclebin', [TaskController::class, 'recycleBin'])
    ->name('recyclebin');

    Route::post('/restore/{task}', [TaskController::class, 'restoreTask'])
    ->name('restore.task');

    Route::get('/chart', [ChartController::class, 'renderChart'])
    ->name('chart.completed');

    Route::get('/export/tasks', [TaskController::class, 'exportTasks'])
    ->name('tasks.export');

});
