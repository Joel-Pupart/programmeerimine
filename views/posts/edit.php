<?php

if (!isLoggedIn()) {
    redirect("/");
}

$oPost = Post::findById ($ID);

if(($oPost->added_by !== $_SESSION['user_id']) && $_SESSION['role'] == 'user') {
    redirect("/");
}

if (!is_object($oPost)) {
    echo message(t('post_missing'), 'danger');
}

$titleTranslations = Translation::findForModel('Post', $oPost->id, 'title', 'et');
$titleTranslation = reset($titleTranslations);
$bodyTranslations = Translation::findForModel('Post', $oPost->id, 'body', 'et');
$bodyTranslation = reset($bodyTranslations);

$args = array(
    'title'    => array(
        'filter' => FILTER_SANITIZE_STRING,
        'flags'  => FILTER_REQUIRE_ARRAY,
    ),
    'body'    => array(
        'filter' => FILTER_SANITIZE_STRING,
        'flags'  => FILTER_REQUIRE_ARRAY,
    )
);

$data = filter_input_array(INPUT_POST, $args);
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
$deleteImage = filter_input(INPUT_POST, 'deleteimage', FILTER_SANITIZE_STRING);

if (isset($deleteImage)) {
    deleteImage($oPost);
    $post = $oPost;
    $post->image = NULL;
    $result = Post::save($post);
    $_SESSION['alert'] = t('image_delete', true);
}

if (isset($_SESSION["alert"])) {
    echo "
    <script>
        bootbox.alert('" . $_SESSION["alert"] . "');
    </script>";
    unset($_SESSION["alert"]);
}


if (isset($action) && $action === 'update') {
    $errors = validateInput($data, 'title');
    $errors = validateInput($data, 'body', $errors);

    if($_FILES["image"]["error"] == 0 && $_FILES["image"]["name"] != "") {
        $fileName = uniqid() . $_FILES["image"]["name"];
        $targetFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . basename($fileName);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check == false) {
            array_push($errors, t('not_image', true));
        }

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            array_push($errors, t('image_format', true));
        }
    }

    if (empty($errors)) {

        $post = $oPost;
        $post->title = $data['title']['en'];
        $post->body= $data['body']['en'];
        $post->edited = date("Y-m-d H:i:s");;
        $post->edited_by = $_SESSION['user_id'];

        if ($_FILES["image"]["error"] == 0 && $_FILES["image"]["name"] != "") {
            move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
            resize_image($targetFile, 1000, 1000, $imageFileType);
            $post->image = $fileName;
        }

        $result = Post::save($post);

        if (!is_object($titleTranslation)) {
            $titleTranslation = new Translation();
        }
        $titleTranslation->create('title', $data['title']['et'], 'et', $post->id, 'Post');

        if (!is_object($bodyTranslation)) {
            $bodyTranslation = new Translation();
        }
        $bodyTranslation->create('body', $data['body']['et'], 'et', $post->id, 'Post');

        if ($result['status']) {
            $_SESSION['alert'] = t('post_edit', true);
            redirect('/posts');
        } else {
            $message = $result['message'];
        }

    }
} elseif (isset($action) && $action === 'delete') {
    Post::delete($oPost, true);
    $bodyTranslations = Translation::findForModel('Post', $oPost->id, 'body', 'et');
    $titleTranslations = Translation::findForModel('Post', $oPost->id, 'title', 'et');
    Translation::delete($bodyTranslations[0]);
    Translation::delete($titleTranslations[0]);
    $_SESSION['alert'] = t('post_delete', true);
    redirect('/posts');
}
echo empty($errors)
    ? ""
    : '<div class="alert alert-danger"><ul><li>' . join("</li><li>", $errors) . '</li></ul></div>';

?>

<form id="mainForm" method="post" enctype="multipart/form-data">

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
            <div class="form-group">
                <label for="title">Title</label>
                <input
                        type="text"
                        class="form-control"
                        id="title" name="title[en]"
                        value="<?php echo is_object($oPost) ? $oPost->title : ""; ?>"
                >
            </div>
            <div class="form-group">
                <label for="body">Body</label>
                <textarea name="body[en]" rows="5" class="form-control"><?php echo $oPost->body; ?></textarea>
            </div>
        </div>
        <div class="tab-pane fade" id="et" role="tabpanel" aria-labelledby="et-tab">
            <div class="form-group">
                <label for="title">Pealkiri</label>
                <input
                        type="text"
                        class="form-control"
                        id="title" name="title[et]"
                        value="<?php echo is_object($titleTranslation) ? $titleTranslation->translation : ""; ?>"
                >
            </div>
            <div class="form-group">
                <label for="body">Sisu</label>
                <textarea name="body[et]" rows="5" class="form-control"><?php echo $bodyTranslation->translation; ?></textarea>
            </div>
        </div>
    </div>

    <?php if ($oPost->image == NULL) { ?>
        <div>
            <label for="image">Choose an image for the post:</label>
            <input type="file" id="image" name="image">
        </div>
    <?php } else { 

        $file = explode('.', $oPost->image);
        $imageFileType = $file[1];
        $targetFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $oPost->image;
        echo resize_image($targetFile , 100, 100, $imageFileType, false, true);
        echo "<span>" . $oPost->image . "</span>";
        
    } 
    ?>

    <input type="hidden" name='action' id='action' value="update">
    <button type="submit" name="action" value="update" class="btn btn-success"><?php t('edit');?></button>
</form>

<form id="deleteForm" method="post" class="mt-2">
        <input type="hidden" name='id' id='id' value="<?php echo $post->id;?>">
        <input type="hidden" name='action' id='action' value="delete">
        <button type="submit" name="action" value="delete" class="btn btn-danger"><?php t('delete');?></button>
</form>

<?php
    if ($oPost->image != NULL) {
        echo "
        <form id='deleteImageForm' method='post' class='mt-2'>
            <input type='hidden' name='deleteimage' id='deleteimage' value='deleteimage'>
            <button type='submit' name='deleteimage' value='deleteimage' class='btn btn-danger'>" . t('delete_image', true) . "</button>
        </form>";
    }
?>