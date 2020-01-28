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

Route::get('/', function () {
    return view('welcome');
});

$paths = explode('/', Request::path());
$paths[0] = isset($paths[0]) ? ucfirst($paths[0]) : '';
$paths[1] = isset($paths[1]) ? ucfirst($paths[1]) : '';
$paths[2] = isset($paths[2]) ? $paths[2] : 'lessuri';
if (file_exists(app_path().'/Http/Controllers/'.$paths[0].'/'.$paths[1].'Controller.php') AND $paths[0] AND $paths[1]) {
  $controller = 'App\Http\Controllers\\'.$paths[0].'\\'.$paths[1].'Controller';
  $instance = new $controller;
  if (method_exists($instance,$paths[2])) {
    Route::group(['namespace' => $paths[0]], function () use ($paths) {
      Route::any('{directory}/{controller}/{action?}/{id?}/{five?}/{six?}/{seven?}/', $paths[1] . 'Controller@' . $paths[2]);
    });      
  }
}
