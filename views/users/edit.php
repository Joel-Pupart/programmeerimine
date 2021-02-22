<?php

if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    redirect("/");
}

$oUser = User::findById ($ID);

if (!is_object($oUser)) {
    echo message(t('user_missing'), 'danger');
}

//print_r($ID);
//print_r($oUser);

//print_r($_POST);
//print_r($_GET);

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
$userRole = filter_input(INPUT_POST, 'userrole', FILTER_SANITIZE_STRING);


if (isset($action) && $action === 'update') {
    $errors = [];

    //kontroll
    if (empty($email)) {
        $errors['email'] = t('email_empty', true);
    }

     //kontroll
     if (empty($password)) {
        $errors['password'] = t('password_empty', true);
    }

    if (empty($errors)) {
        //save new user
        $user = $oUser;
        $user->email = $email;
        $user->password = createPassword($password);
        $user->edited = date("Y-m-d H:i:s");;
        $user->edited_by = $_SESSION['user_id'];
        if (!in_array($userRole, ['user','moderator', 'admin'], true )) {
            $userRole = 'user';
        }
        $user->role = $userRole;

        $result = User::save($user);

        if ($result['status']) {
            $_SESSION['alert'] = t('user_edit', true);
            redirect('/users');
        } else {
            $message = $result['message'];
        }
    }
} elseif (isset($action) && $action === 'delete') {
    USER::delete($oUser);
    $_SESSION['alert'] = t('user_delete', true);
    redirect('/users');
}

echo empty($errors)
    ? ""
    : '<div class="alert alert-danger"><ul><li>' . join("</li><li>", $errors) . '</li></ul></div>';

?>

<form id="mainForm" method="post">

    <div class="form-group">
        <label for="email"><?php t('emailaddress');?></label>
        <input
            type="email"
            class="form-control"
            id="email" name="email"
            value="<?php echo is_object($oUser) ? $oUser->email : ""; ?>"
        >
    </div>
    <div class="form-group">
        <label for="password"><?php t('password');?></label>
        <input
            type="password"
            class="form-control"
            id="password"
            name="password">
    </div>

    <div class="form-group">
        <label for="userrole"><?php t('userrole');?></label>
        <select name="userrole" id="userrole">
            <option value="user" <?php if ($oUser->role == 'user') { echo "selected"; }?>><?php t('user');?></option>
            <option value="moderator" <?php if ($oUser->role == 'moderator') { echo "selected"; }?>><?php t('moderator');?></option>
            <option value="admin" <?php if ($oUser->role == 'admin') { echo "selected"; }?>><?php t('admin');?></option>
        </select>
    </div>

    <input type="hidden" name='action' id='action' value="update">
    <button type="submit" name="action" value="update" class="btn btn-success"><?php t('edit');?></button>
</form>

<form id="deleteForm" method="post" class="mt-2">
        <input type="hidden" name='id' id='id' value="<?php echo $user->id;?>">
        <input type="hidden" name='action' id='action' value="delete">
        <button type="submit" name="action" value="delete" class="btn btn-danger"><?php t('delete');?></button>
</form>