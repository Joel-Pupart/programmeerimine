<?php

if ($currentPage == 0) {
    $currentPage = 1;
}

$counter = 0;

$start = startQuery($currentPage);
$search = $_POST["search"];

if (isset($search) && strlen($search) > 2) {
    $posts = Post::all($start, MAX_ON_PAGE, $search);
    $maxPosts = count($posts);
} elseif (strlen($search) > 0 && strlen($search) < 3) {
    $tooShort = 1;
} else {
    $posts = Post::all($start, MAX_ON_PAGE);
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
<div class="row">
<?php if (!empty($posts)) : foreach ($posts as $post) { 
        $counter += 1;   
?>

        <div class="card mb-4 w-100">
            <?php
                if($post->image != NULL) {

                    $file = explode('.', $post->image);
                    $imageFileType = $file[1];
                    $targetFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $post->image;
                    echo resize_image($targetFile , 500, 500, $imageFileType, false, true);
                    
                }

            ?>
            <div class="card-body">
            <?php if ($_SESSION["language"] == "en") { ?>
                <h2 class="card-title"><?php echo $post->title; ?></h2>
                <p class="card-text"><?php echo strLimit($post->body); ?></p>
            <?php } else { ?>
                <h2 class="card-title">
                <?php 
                    $titleTranslations = Translation::findForModel('Post', $post->id, 'title', 'et');
                    $titleTranslation = reset($titleTranslations);
                    echo is_object($titleTranslation) ? $titleTranslation->translation : ""; 
                ?>
                </h2>
                <p class="card-text">
                <?php 
                $bodyTranslations = Translation::findForModel('Post', $post->id, 'body', 'et');
                $bodyTranslation = reset($bodyTranslations);
                echo strLimit($bodyTranslation->translation);
                ?>
                </p>
            <?php }?>
    
            <a href="/post/<?php echo $post->id; ?>" class="btn btn-primary"><?php echo t('read_more'); ?> &rarr;</a>

            </div>
            <div class="card-footer text-muted">
                <?php echo t('posted_on') . ' ' . $post->added; ?></div>
        </div>
    
<?php } ?>
<?php else: ?>
    <div class="col">
        <div class="alert alert-info">
            <?php echo isset($search) || isset($tooShort) ? t('error_search_too_short') : t('error_no_posts'); ?></div>
    </div>
<?php endif; ?>
</div>

<?php if ($maxPages > 1) { ?>

<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center mb-4">
        <li class="page-item <?php echo $currentPage == 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="/<?php echo $currentPage-1; ?>"><?php echo t('previous'); ?></a>
        </li>
        <?php for ($i = 1; $i <= $maxPages; $i++) : ?>
            <li class="page-item"><a class="page-link" href="/<?php echo $i; ?>"><?php echo $i; ?></a></li>
        <?php endfor; ?>
        <li class="page-item <?php echo $currentPage+1 > $maxPages ? 'disabled' : ''; ?>">
            <a class="page-link" href="/<?php echo $currentPage+1; ?>"><?php echo t('next'); ?></a>
        </li>
    </ul>
</nav>

<?php } ?>