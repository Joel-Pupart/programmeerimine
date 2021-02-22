<?php

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

if (isset($action) && $action === 'save') {
    
    $errors = [];
    $titleValidate = validateInput($data, 'title');
    $bodyValidate = validateInput($data, 'body');
    
    if(!empty($titleValidate)) {
        foreach ($titleValidate as $val) {
            array_push($errors, $val);
        }
    }
   
    if(!empty($bodyValidate)) {
        foreach ($bodyValidate as $val) {
            array_push($errors, $val);
        }
    }

    if (!empty(Post::findByTitle($data['title']['en']))) {
        $errors['post_exists'] = t('post_exists', true);
    }

    if($_FILES["image"]["error"] == 0) {
        $fileName = uniqid() . $_FILES["image"]["name"];
        $targetFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . basename($fileName);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check == false) {
            array_push($errors, t('not_image', true));
        }

        if ($_FILES["image"]["size"] > 500000) {
            array_push($errors, t('image_large', true));
        }

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            array_push($errors, t('image_format', true));
        }
    }

    if (empty($errors)) {

        $post = new Post();
        $post->title = $data['title']['en'];
        $post->body= $data['body']['en'];
        $post->status= 'draft';
        $post->added = date("Y-m-d H:i:s");
        $post->added_by = $_SESSION['user_id'];
        $post->edited = date("Y-m-d H:i:s");;
        $post->edited_by = $_SESSION['user_id'];
        
        if ($_FILES["image"]["error"] == 0) {
            move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
            $newTarget = implode('.', explode('.', $targetFile, -1));
            $secondFile = $newTarget . "150." . $imageFileType;
            $thirdFile = $newTarget . "50." . $imageFileType;
            
            copy($targetFile, $newTarget . "150." . $imageFileType);
            resize_image($secondFile, 150, 150, $imageFileType);

            copy($targetFile, $newTarget . "50." . $imageFileType);
            resize_image($thirdFile, 50, 50, $imageFileType);

            $post->image = $fileName;
        }
        
        $result = Post::save($post);


        if ($result['status']) {

            $post->id = $result['id'];
            $titleTranslation = new Translation();
            $titleTranslation->create('title', $data['title']['et'], 'et', $post->id, 'Post');
        
            $bodyTranslation = new Translation();
            $bodyTranslation->create('body', $data['body']['et'], 'et', $post->id, 'Post');
            $_SESSION['alert'] = t('post_add', true);
            redirect('/posts');

        } else {
            echo $result['message'];
            echo message(t('problem_creating_post'), 'danger');
        }

    }
}
echo empty($errors)
    ? ""
    : '<div class="alert alert-danger"><ul><li>' . join("</li><li>", $errors) . '</li></ul></div>';
?>

<form method="post" enctype="multipart/form-data">
    
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
                        value=""
                >
            </div>
            <div class="form-group">
                <label for="body">Body</label>
                <textarea name="body[en]" rows="5" class="form-control"></textarea>
            </div>
        </div>
        <div class="tab-pane fade" id="et" role="tabpanel" aria-labelledby="et-tab">
            <div class="form-group">
                <label for="title">Pealkiri</label>
                <input
                        type="text"
                        class="form-control"
                        id="title" name="title[et]"
                        value=""
                >
            </div>
            <div class="form-group">
                <label for="body">Sisu</label>
                <textarea name="body[et]" rows="5" class="form-control"></textarea>
            </div>
        </div>
    </div>
    <div>
        <label for="image">Choose an image for the post:</label>
        <input type="file" id="image" name="image">
    </div>

    <button type="submit" name="action" value="save" class="btn btn-success">Save</button>
</form>