<?php

$eng = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30" width="40" height="25">
            <clipPath id="s">
                <path d="M0,0 v30 h60 v-30 z"/>
            </clipPath>
            <clipPath id="t">
                <path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z"/>
            </clipPath>
            <g clip-path="url(#s)">
                <path d="M0,0 v30 h60 v-30 z" fill="#012169"/>
                <path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6"/>
                <path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#t)" stroke="#C8102E" stroke-width="4"/>
                <path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10"/>
                <path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6"/>
            </g>
        </svg>';
$est = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="25" viewBox="0 0 33 21"> 
            <rect fill="#FFF" width="33" height="21"/> 
            <rect width="33" height="14"/> <rect fill="#0072ce" width="33" height="7"/> 
        </svg>';
$translate = filter_input(INPUT_POST, 'translate', FILTER_SANITIZE_STRING);

if (isset($translate)){
    if ($translate === 'en') {
        $_SESSION['language'] = 'en';
    } elseif ($translate === 'et') {
        $_SESSION['language'] = 'et';
    }
    
    redirect($_SERVER['REQUEST_URI']);
}
?>
<?php 
    echo '<a class="nav-link dropdown-toggle" data-toggle="dropdown">';
    echo "<span class='p-1 m-1'>" . t('language', true) . " : </span>"; 
    if ($_SESSION['language'] =='et') {
        echo $est;
    } else {
        echo $eng;
    }
    echo '</a>';
?>
<div class="dropdown-menu bg-dark" aria-labelledby="navbarDropdownMenuLink">
    <form method="post">
        <?php  
            echo '<label class="pt-1 m-1 nav-link">';
            
            echo '<input class="d-none" type="submit" name="translate" value="';
            if ($_SESSION['language'] =='et') {
                echo 'en">';
                echo "<span class='p-1 m-1'>" . t('en', true) . " : </span>";
                echo $eng;
            } else {
                echo 'et">';
                echo "<span class='p-1 m-1'>" . t('et', true) . " : </span>";
                echo $est;
            }
            echo '</label>';
        ?>
    </form>
</div>