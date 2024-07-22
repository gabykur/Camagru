<?php

use App\Routes\Router;

global $pdo;

$router = new Router($pdo);

$router->get('/', 'HomeController@index');

// User routes
$router->get('/user/account', 'UserController@account');
$router->post('/user/account', 'UserController@account');

$router->get('/user/modifyPassword', 'UserController@modifyPassword');
$router->post('/user/modifyPassword', 'UserController@modifyPassword');

$router->get('/user/deletePhotos', 'UserController@deletePhotos');
$router->post('/user/deletePhotos', 'UserController@deletePhotos');

$router->get('/user/deleteAccount', 'UserController@deleteAccount');
$router->post('/user/deleteAccount', 'UserController@deleteAccount');

$router->get('/user/notifications', 'UserController@notifications');
$router->post('/user/notifications', 'UserController@notifications');

$router->get('/user/verifyEmail', 'UserController@verifyEmail');

// Auth routes
$router->get('/auth/login', 'AuthController@login');
$router->post('/auth/login', 'AuthController@login');
$router->get('/auth/register', 'AuthController@register');
$router->post('/auth/register', 'AuthController@register');
$router->get('/auth/logout', 'AuthController@logout');
$router->get('/auth/forgotPassword', 'AuthController@forgotPassword');
$router->post('/auth/forgotPassword', 'AuthController@forgotPassword');
$router->get('/auth/resetPassword', 'AuthController@resetPassword');
$router->post('/auth/resetPassword', 'AuthController@resetPassword');
$router->get('/auth/activationAccount', 'AuthController@activationAccount');

// Photo routes
$router->get('/photo/addLikeComment', 'PhotoController@addLikeComment');
$router->post('/photo/addLikeComment', 'PhotoController@addLikeComment');
$router->get('/photo/upload', 'PhotoController@upload');  // Added GET route for upload
$router->post('/photo/upload', 'PhotoController@upload');
$router->get('/photo/camera', 'PhotoController@camera');  // Added GET route for camera
$router->post('/photo/camera', 'PhotoController@camera');

// Dispatch the route
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($uri, $method);
