<?php

$post = Post::findById($ID);

if (!is_object($post)) {
    echo message(t('post_missing'), 'danger');
}

$titleTranslations = Translation::findForModel('Post', $post->id, 'title', 'et');
$titleTranslation = reset($titleTranslations);
$bodyTranslations = Translation::findForModel('Post', $post->id, 'body', 'et');
$bodyTranslation = reset($bodyTranslations);

?>
<div class="card">
    <?php
        if ($post->image != NULL) {

            echo '<img src="/img/' . $post->image . '" class="card-img-top">';
            
        } 
    ?> 
    <?php if ($_SESSION['language'] == 'et') { ?>
        <div class="card-body">
            <h2 class="card-title"><?php echo is_object($titleTranslation) ? $titleTranslation->translation : ""; ?></h2>
            <p class="card-text"><?php echo $bodyTranslation->translation; ?></p>
        </div>
    <?php } else { ?>
        <div class="card-body">
            <h2 class="card-title"><?php echo $post->title; ?></h2>
            <p class="card-text"><?php echo $post->body; ?></p>
        </div>
    <?php  } ?>
</div>