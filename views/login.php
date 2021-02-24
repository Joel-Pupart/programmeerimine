<?php

if (isLoggedIn()) {
    redirect('/');
}


$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

if (isset($action) && $action === 'login') {
    $errors = [];

    if (empty($email)) {
        array_push($errors, t('email_empty', true));
    }

    if (empty($password)) {
        array_push($errors, t('password_empty', true));
    }

    if (empty($errors)) {

        $user = User::findByEmail($email);

        if (is_object($user)) {

            if (password_verify($password, $user->password)) {
                $_SESSION[IS_LOGGED_IN] = true;
                $_SESSION['user_id'] = $user->id;
                $_SESSION['role'] = $user->role;
                redirect('/');
            }
        }
        array_push($errors, t('username_password_mismatch', true));
    }
}
echo empty($errors)
    ? ""
    : '<div class="alert alert-danger"><ul><li>' . join("</li><li>", $errors) . '</li></ul></div>';
?>
<form method="post" action="/login">

    <div class="form-group">
        <label for="email"><?php t('emailaddress'); ?></label>
        <input
            type="email"
            class="form-control"
            id="email" name="email"
            value=""
        >
    </div>
    <div class="form-group">
        <label for="password"><?php t('password'); ?></label>
        <input
            type="password"
            class="form-control"
            id="password"
            name="password">
    </div>

    <button type="submit" name="action" value="login" class="btn btn-success"><?php t('login'); ?></button>
</form>