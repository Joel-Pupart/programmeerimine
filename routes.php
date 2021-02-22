<?php

return [
    'users' => [
        'title' => 'user_list',
        'file_location' => __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'list.php',
        'auth' => true
    ],
    'users/add' => [
        'title' => 'add_user',
        'file_location' => __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'add.php',
        'auth' => true
    ],
    'users/edit' => [
        'title' => 'edit_user',
        'file_location' => __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'edit.php',
        'auth' => true
    ],
    '404' => [
        'title' => '404',
        'file_location' => __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . '404.php'
    ],
    'blog' => [
        'title' => 'blog',
        'file_location' => __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'blog' . DIRECTORY_SEPARATOR . 'list.php',
        'auth' => false
    ],
    'post' => [
        'title' => '',
        'file_location' => __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'blog' . DIRECTORY_SEPARATOR . 'post.php',
        'auth' => false
    ],
    'login' => [
        'title' => 'login',
        'file_location' => __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'login.php'
    ],
    'logout' => [
        'title' => '',
        'file_location' => __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'logout.php'
    ],
    'translation' => [
        'title' => '',
        'file_location' => __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'translation.php'
    ],
    'posts' => [
        'title' => 'post_list',
        'file_location' => __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'posts' . DIRECTORY_SEPARATOR . 'list.php',
        'auth' => true
    ],
    'posts/add' => [
        'title' => 'add_post',
        'file_location' => __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'posts' . DIRECTORY_SEPARATOR . 'add.php',
        'auth' => true
    ],
    'posts/edit' => [
        'title' => 'edit_list',
        'file_location' => __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'posts' . DIRECTORY_SEPARATOR . 'edit.php',
        'auth' => true
    ],
];