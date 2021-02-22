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
    

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="en-tab" data-toggle="tab" href="#en" role="tab" aria-controls="en" aria-selected="true"><?php t('en'); ?></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#et" role="tab" aria-controls="et" aria-selected="false"><?php t('et'); ?></a>
        </li>
    </ul>
    <br>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="en" role="tabpanel" aria-labelledby="en-tab">
            <div class="card-body">
                <h2 class="card-title"><?php echo $post->title; ?></h2>
                <p class="card-text"><?php echo $post->body; ?></p>
            </div>
        </div>
        <div class="tab-pane fade" id="et" role="tabpanel" aria-labelledby="et-tab">
            <div class="card-body">
                <h2 class="card-title"><?php echo is_object($titleTranslation) ? $titleTranslation->translation : ""; ?></h2>
                <p class="card-text"><?php echo $bodyTranslation->translation; ?></p>
            </div>
        </div>
    </div>
</div>