<?php



$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
//$email = 'heli.kopter@ametikool.ee';
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
//$password = '12345';
$passwordAgain = filter_input(INPUT_POST, 'password_again', FILTER_SANITIZE_STRING);
//$passwordAgain = '12345';
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
//$action = 'save';
$userRole = filter_input(INPUT_POST, 'userrole', FILTER_SANITIZE_STRING);


if (isset($action) && $action === 'save') {

    $errors = [];

    //kontroll
    if (empty($email)) {
        $errors['email'] = t('email_empty', true);
    }

    //password empty
    if (empty($password)) {
        $errors['password_empty'] = t('password_empty', true);
    }

    //password match
    if ($password != $passwordAgain) {
        $errors['password_mismatch'] = t('password_mismatch', true);
    }

    //check if username exists
    if (!empty(User::findByEmail($email))) {
        $errors['user_exists'] = t('user_exists', true);
    }

    if (empty($errors)) {
        //save new user
        $user = new User();
        $user->email = $email;
        $user->password = createPassword($password);
        $user->added = date("Y-m-d H:i:s");
        $user->added_by = $_SESSION['user_id'];
        $user->edited = date("Y-m-d H:i:s");;
        $user->edited_by = $_SESSION['user_id'];
        if (!in_array($userRole, ['user','moderator', 'admin'], true )) {
            $userRole = 'user';
        }
        $user->role = $userRole;


        $result = User::save($user);

        if ($result['status']) {
            $_SESSION['alert'] = t('user_add');
            redirect('/users');
        } else {
            echo message(t('problem_creating_user'), 'danger');
        }

    }
}
echo empty($errors)
    ? ""
    : '<div class="alert alert-danger"><ul><li>' . join("</li><li>", $errors) . '</li></ul></div>';
?>

<form method="post">

    <div class="form-group">
        <label for="email"><?php t('emailaddress');?></label>
        <input
                type="email"
                class="form-control"
                id="email" name="email"
                value=""
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
        <label for="password_again"><?php t('passwordagain');?></label>
        <input
                type="password"
                class="form-control"
                id="password_again"
                name="password_again">
    </div>

    <div class="form-group">
        <label for="userrole"><?php t('userrole');?></label>
        <select name="userrole" id="userrole">
            <option value="user"><?php t('user');?></option>
            <option value="moderator"><?php t('moderator');?></option>
            <option value="admin"><?php t('admin');?></option>
        </select>
    </div>

    <button type="submit" name="action" value="save" class="btn btn-success"><?php t('save');?></button>
</form>