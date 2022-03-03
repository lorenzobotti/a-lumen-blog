<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return [
        "doesItWork" => "yes",
    ];

    //return $router->app->version();
});

// area protetta
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->post('/posts', ['uses' => 'PostController@createPost']);
    $router->delete('/posts/{id}', ['uses' => 'PostController@deletePost']);

    $router->post('/comments', ['uses' => 'CommentController@createComment']);
    $router->put('/comments/{id}', ['uses' => 'CommentController@editComment']);
    $router->delete('/comments/{id}', ['uses' => 'CommentController@deleteComment']);
});

$router->get('/users/', ['uses' => 'UserController@showAllUsers']);
$router->get('/users/{id}', ['uses' => 'UserController@getById']);
$router->post('/users/', ['uses' => 'UserController@create']);

$router->get('/posts/', ['uses' => 'PostController@allPosts']);
$router->get('/posts/{id}', ['uses' => 'PostController@getPostById']);
$router->get('/posts/{id}/user', ['uses' => 'PostController@getUserByPostId']);

$router->get('/comments/{id}', ['uses' => 'CommentController@getCommentById']);

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/login', ['uses' => 'UserController@login']);
    $router->post('/register', ['uses' => 'UserController@create']);
});