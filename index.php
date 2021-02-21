<?php 

use Daisy\Mvc\Application;
use Daisy\Mvc\Route;

// require composer autoload
require __DIR__.'/vendor/autoload.php';

// application config
define("APP_VIEW", __DIR__ . "/app/view/");

Application::start();

Route::get('/', function() {
	echo 'Welcome to Daisy Framework';
});

Route::get('/index', 'IndexController@index');
Route::get('/index2', 'IndexController@index2');
Route::get('/index3', 'IndexController@index3');

Application::end();
