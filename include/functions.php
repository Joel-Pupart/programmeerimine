<?php

function message ($msg, $alert) {

    if (empty($msg)) {
        return;
    }

    return '<div class="alert alert-' . $alert . '">' . $msg . '</div>';
}

function isLoggedIn () {

    if (isset($_SESSION) && $_SESSION[IS_LOGGED_IN] == true) {
        return true;
    }

    return false;
}

function checkAccess($p) {
    if (in_array($p, ['users', 'users/add', 'users/edit', 'posts', 'posts/add', 'posts/edit']) && !isLoggedIn()) {
        redirect("/");
    } elseif (isLoggedIn()) {
        if ($_SESSION['role'] === 'user' && in_array($p, ['users', 'users/add', 'users/edit'])) {
            redirect("/");
        } elseif ($_SESSION['role'] === 'moderator' && in_array($p, ['users', 'users/add', 'users/edit'])) {
            redirect("/");
        }
    }
}

function redirect ($url = "") {

    if (empty($url)) {
        return;
    }

//    header('Location: ' . $url);
    ?><meta http-equiv="refresh" content="0;url=<?php echo $url; ?>" /><?php

    exit();
}

function createPassword ($password) {
    $options = [
        'cost' => 12,
    ];
    return password_hash($password, PASSWORD_BCRYPT, $options);
}

function strLimit($value, $limit = 100, $end = '...') {
    $limit = $limit - mb_strlen($end); // Take into account $end string into the limit
    $valuelen = mb_strlen($value);
    return $limit < $valuelen ? mb_substr($value, 0, mb_strrpos($value, ' ', $limit - $valuelen)) . $end : $value;
}

function t($string, $return = false) {

    global $translations;

    if (isset($translations[$string])) {
        $out = $translations[$string];
    } else {
        $out = 'translation_missing("' . $string . '")';
    }

    if ($return) {
        return $out;
    }

    echo $out;

}

function validateInput ($data, $inputName, $errors = []) {

    if (!empty($data[$inputName])) : foreach ($data[$inputName] as $language => $row) {
        if (empty($row)) {
            array_push($errors, t('error_' . $inputName .'_empty', true) . " (" .  t($language, true) . ")");
        }
    } endif;

    return $errors;
}

function startQuery ($currentPage) {

    if (empty($currentPage) || $currentPage === 1) {
        $start = 0;
    } else {
        $start = ($currentPage - 1) * MAX_ON_PAGE;
    }

    return $start;
}

function resize_image($file, $w, $h, $type, $crop=FALSE) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }

    switch ($type) {
        case 'jpg':
        case 'jpeg':
            $src = imagecreatefromjpeg($file);
            break;
        case 'png':
            $src = imagecreatefrompng($file);
            break;
        case 'gif':
            $src = imagecreatefromgif($file);
            break;
    }

    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    
    switch ($type) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($dst, $file, 100);
            break;
        case 'png':
            imagepng($dst, $file, 100);
            break;
        case 'gif':
            imagegif($dst, $file, 100);
            break;
    }
    
    return $dst;
}

function deleteImage($obj) {
    if ($obj->image != NULL) {

        $directory = dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "img" . DIRECTORY_SEPARATOR;
        $file = explode('.', $obj->image);
        $imageTwo = $file[0] . "150." . $file[1];
        $imageThree = $file[0] . "50." . $file[1];
        
        unlink($directory . $obj->image);
        unlink($directory . $imageTwo);
        unlink($directory . $imageThree);
    } 
    
    return true;
}