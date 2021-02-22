<?php
    
    if (isset($_SESSION["alert"])) {
        echo "
        <script>
            bootbox.alert('" . $_SESSION["alert"] . "');
        </script>";
        unset($_SESSION["alert"]);
    }

    $currentPage = $ID;

    if ($currentPage == 0) {
        $currentPage = 1;
    }

    $start = startQuery($currentPage);
    $search = $_POST["search"];

    if (isset($search) && strlen($search) > 2) {
        $users = User::all($start, MAX_ON_PAGE, $search, 'auth');
        $maxUsers = count($users);
    } elseif (strlen($search) > 0 && strlen($search) < 3) {
        $tooShort = 1;
    } else {
        $users = User::all($start, MAX_ON_PAGE, $search, 'auth');
        $maxUsers = User::count();
    }
    $maxPages = ceil($maxUsers / MAX_ON_PAGE);
        
/*
    $maxUsers = User::count();
    $maxPages = ceil($maxUsers / MAX_ON_PAGE);
    
    if ($currentPage == 0) {
        $currentPage = 1;
    }
    $currentPage = $ID;
    
    $start = startQuery($currentPage);

    $users = User::all($start);*/

?>
<div class="row">
    <div class="col">
        <form method="post">
            <div class="input-group">
                <input type="text" class="form-control" placeholder=<?php echo t('search'); ?> name="search" value="<?php echo isset($search) ? $search : ""; ?>">
                <div class="input-group-prepend">
                    <button type="submit" class="btn btn-success"><?php echo t('search'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<br>
<table class="table">
    <tr>
        <th><?php t('email');?></th>
        <th><?php t('added');?></th>
        <th><?php t('edited');?></th>
        <th></th>
        <th></th>
    </tr>
<?php

$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

$oUser = User::findById ($id);

if (isset($action) && $action === 'delete') {
    USER::delete($oUser);
    redirect('/users');
}

if (!empty($users)) : foreach ($users as $user) { 
    
    if ($user->id == $_SESSION['user_id']) {
        continue;
    }
    
    ?>
    <tr>
        <td><?php echo $user->email; ?></td>
        <td><?php echo $user->added; ?></td>
        <td><?php echo $user->edited; ?></td>
        <td>
            <a class="btn btn-success" href="<?php 
                if ($_SERVER['REQUEST_URI'] == DIRECTORY_SEPARATOR . "users") {
                    echo "users" . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $user->id;
                } else {
                    echo "edit" . DIRECTORY_SEPARATOR . $user->id;
                }
            ?>"><?php t('edit');?></a>
        </td>
        <td>
            <form id="deleteForm" method="post">
                <input type="hidden" name='action' id='action' value="delete">
                <input type="hidden" name='id' id='id' value="<?php echo $user->id;?>">
                <button type="submit" name="action" value="delete" class="btn btn-danger"><?php t('delete');?></button>
            </form>
        </td>
    </tr>
    <?php } ?>
<?php else: ?>
    <div class="col">
        <div class="alert alert-info">
            <?php echo isset($search) || isset($tooShort) ? t('error_search_too_short') : t('error_no_posts'); ?></div>
    </div>
<?php endif; ?>
</table>

<?php if ($maxPages > 1) { ?>

<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
        <li class="page-item <?php echo $currentPage == 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="/users/<?php echo $currentPage-1; ?>"><?php echo t('previous'); ?></a>
        </li>
        <?php for ($i = 1; $i <= $maxPages; $i++) : ?>
            <li class="page-item"><a class="page-link" href="/users/<?php echo $i; ?>"><?php echo $i; ?></a></li>
        <?php endfor; ?>
        <li class="page-item <?php echo $currentPage+1 > $maxPages ? 'disabled' : ''; ?>">
            <a class="page-link" href="/users/<?php echo $currentPage+1; ?>"><?php echo t('next'); ?></a>
        </li>
    </ul>
</nav>
<?php } ?>