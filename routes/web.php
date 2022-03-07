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


// scommenta per ricevere info sulle query eseguite nelle risposte
$router->group(['middleware' => 'queries'], function () use ($router) {

// area protetta
$router->group(['middleware' => ['auth', 'banned']], function () use ($router) {
    $router->post('/posts', ['uses' => 'PostController@createPost']);
    $router->delete('/posts/{id}', ['uses' => 'PostController@deletePost']);
    $router->delete('/posts/{id}', ['uses' => 'PostController@deletePost']);

    $router->post('/posts/{id}/likes', ['uses' => 'PostController@likePost']);
    $router->delete('/posts/{id}/likes', ['uses' => 'PostController@unlikePost']);

    $router->post('/comments/{id}/likes', ['uses' => 'CommentController@likeComment']);
    $router->delete('/comments/{id}/likes', ['uses' => 'CommentController@unlikeComment']);

    $router->post('/comments', ['uses' => 'CommentController@createComment']);

    // area premium
    $router->group(['middleware' => 'premium'], function () use ($router) {
        $router->put('/comments/{id}', ['uses' => 'CommentController@editComment']);
        $router->delete('/comments/{id}', ['uses' => 'CommentController@deleteComment']);
    });
});

$router->get('/users/', ['uses' => 'UserController@showAllUsers']);
$router->get('/users/{id}', ['uses' => 'UserController@getById']);
$router->delete('/users/{id}', ['uses' => 'UserController@banUser']);
//$router->post('/users/', ['uses' => 'UserController@create']);

$router->get('/posts/', ['uses' => 'PostController@allPosts']);
$router->get('/posts/{id}', ['uses' => 'PostController@getPostById']);
$router->get('/posts/{id}/user', ['uses' => 'PostController@getUserByPostId']);
$router->get('/posts/{id}/likes', ['uses' => 'PostController@getLikesByPostId']);

$router->get('/comments/{id}', ['uses' => 'CommentController@getCommentById']);

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/login', ['uses' => 'UserController@login']);
    $router->post('/register', ['uses' => 'UserController@create']);

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->post('refresh', ['uses' => 'UserController@newToken']);
    });
});
});

