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
        $posts = Post::all($start, MAX_ON_PAGE, $search, 'auth');
        $counter = Post::all($start, MAX_ON_PAGE, $search, 'auth', true);
        $maxPosts = count($counter);
    } elseif (strlen($search) > 0 && strlen($search) < 3) {
        $tooShort = 1;
    } elseif ($_SESSION['role'] == 'user') {
        $posts = Post::all($start, MAX_ON_PAGE, $search, 'auth');
        $counter = Post::all($start, MAX_ON_PAGE, $search, 'auth', true);
        $maxPosts = count($counter);
    } else {
        $posts = Post::all($start, MAX_ON_PAGE, $search, 'auth');  
        $maxPosts = Post::count();
    }

    $maxPages = ceil($maxPosts / MAX_ON_PAGE);

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
        <th scope="col"><?php t('title');?></th>
        <th scope="col"><?php t('body');?></th>
        <th scope="col"><?php t('added');?></th>
        <th scope="col"><?php t('edited');?></th>
        <th scope="col"></th>
        <th scope="col"></th>
    </tr>
<?php

$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

$oPost = Post::findById ($id);

if (isset($action) && $action === 'delete') {
    POST::delete($oPost, true);
    $bodyTranslations = Translation::findForModel('Post', $oPost->id, 'body', 'et');
    $titleTranslations = Translation::findForModel('Post', $oPost->id, 'title', 'et');
    Translation::delete($bodyTranslations[0]);
    Translation::delete($titleTranslations[0]);
    redirect('/posts');
}

if (!empty($posts)) : foreach ($posts as $post) { ?>
    <tr scope="row">
        <td><?php echo $post->title; ?></td>
        <td><?php echo strLimit($post->body, 30); ?></td>
        <td><?php echo $post->added; ?></td>
        <td><?php echo $post->edited; ?></td>
        <td>
            <a class="btn btn-success" href="<?php 
                if ($_SERVER['REQUEST_URI'] == DIRECTORY_SEPARATOR . "posts") {
                    echo "posts" . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $post->id;
                } else {
                    echo "edit" . DIRECTORY_SEPARATOR . $post->id;
                }
            ?>"><?php t('edit');?></a>
        </td>
        <td>
            <form id="deleteForm" method="post">
                <input type="hidden" name='id' id='id' value="<?php echo $post->id;?>">
                <input type="hidden" name='action' id='action' value="delete">
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
            <a class="page-link" href="/posts/<?php echo $currentPage-1; ?>"><?php echo t('previous'); ?></a>
        </li>
        <?php for ($i = 1; $i <= $maxPages; $i++) : ?>
            <li class="page-item"><a class="page-link" href="/posts/<?php echo $i; ?>"><?php echo $i; ?></a></li>
        <?php endfor; ?>
        <li class="page-item <?php echo $currentPage+1 > $maxPages ? 'disabled' : ''; ?>">
            <a class="page-link" href="/posts/<?php echo $currentPage+1; ?>"><?php echo t('next'); ?></a>
        </li>
    </ul>
</nav>

<?php } ?>