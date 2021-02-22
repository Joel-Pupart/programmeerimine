<?php
session_start();
require_once 'autoload.php';

$p = trim($_SERVER['REQUEST_URI'], '/');
$currentPage = 0;

if (is_numeric($p)) {
    $currentPage = $p;
    $p = 'blog';
}

//print_r($_SESSION);


$pos = strpos($p, '?search');

if ($pos !== false) {
    $explode = explode("=", $p);
    $search = $explode[1];
    $p = 'blog';
}

checkAccess($p);

?>

<!doctype html>
<html>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
    <title>PHP Projekt</title>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
        <span class="navbar-brand">Ametikool CMD</span>
        
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="/"><?php t('home'); ?></a>
            </li>
        <?php if (isLoggedIn()) : ?>
            <?php if ($_SESSION['role'] === 'admin') :?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="/users" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php t('users'); ?></a>
                    <div class="dropdown-menu bg-dark" aria-labelledby="navbarDropdownMenuLink">
                        <a class="nav-link" href="/users"><?php t('list'); ?></a>
                        <a class="nav-link" href="/users/add"><?php t('add'); ?></a>
                    </div>
                </li>
            <?php endif; ?>
                
            </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="/posts" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php t('posts'); ?></a>
                        <div class="dropdown-menu bg-dark" aria-labelledby="navbarDropdownMenuLink">
                            <a class="nav-link" href="/posts"><?php t('list'); ?></a>
                            <a class="nav-link" href="/posts/add"><?php t('add'); ?></a>
                        </div>
                    </li>

                    <li class="nav-item"><a class="nav-link" href="/logout"><?php t('logout'); ?></a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/login"><?php t('login'); ?></a></li>
                <?php endif; ?>
            <li>  
                <?php require_once 'views' . DIRECTORY_SEPARATOR . 'translation.php';?>
            </li>
            </ul>
            
        </div>
        </div>
    </nav>

<div class="container pt-5 mt-5 d-flex justify-content-center">
    
        <div class="col-md-8">

            <?php
            if (empty($p)) {
                echo "<h3>";
                echo t($routes['blog']['title']) ;
                echo "</h3>";
                require_once $routes['blog']['file_location'];
            } elseif (isset($routes[$p])) {
                echo '<h3>';
                echo t($routes[$p]['title']);
                echo '</h3>';
                require_once $routes[$p]['file_location'];
            } else {

                $explode = explode("/", $p);

                if (!empty($explode) && count($explode) > 1) {

                    $ID = $explode[count($explode)-1];

                    unset($explode[count($explode)-1]);

                    $p = join("/", $explode);
                    if (isset($routes[$p])) {
                        echo '<h3>';
                        echo t($routes[$p]['title']);
                        echo '</h3>';
                        require_once $routes[$p]['file_location'];
                    } else {
                        require_once $routes[404]['file_location'];
                    }
                } else {
                    require_once $routes[404]['file_location'];
                }
            }

            ?>
        </div>

</div>

    

<!-- Footer 
<footer class="py-3 bg-dark footer">
    <div class="container">
      <p class="m-0 text-center text-white">Joel Pupart 2021</p>
    </div>
</footer>
-->

<!-- Optional JavaScript; choose one of the two! -->

<!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
<!--
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>-->


<script>
    $('#mainForm').submit(function(e) {
        var currentForm = this;
        e.preventDefault();
        bootbox.confirm('<?php t('you_sure') ?>', function(result) {
            if (result) {
                currentForm.submit();
            }
        });
    });

    $('#deleteForm').submit(function(e) {
        var currentForm = this;
        e.preventDefault();
        bootbox.confirm('<?php t('you_sure') ?>', function(result) {
            if (result) {
                currentForm.submit();
            }
        });
    });

    $('#deleteImageForm').submit(function(e) {
        var currentForm = this;
        e.preventDefault();
        bootbox.confirm('<?php t('you_sure') ?>', function(result) {
            if (result) {
                currentForm.submit();
            }
        });
    });
</script>
<!-- Option 2: jQuery, Popper.js, and Bootstrap JS
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
-->

</body>
</html>