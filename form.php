<?php

    $errors = [];
    $uploadFile = '';
    $name = '';
    $age = '';
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $inputs = array_map('trim', $_POST);

        if (empty($inputs['firstname'])) {
            $errors[] = 'Firstname is required';
        }

        if (empty($inputs['lastname'])) {
            $errors[] = 'Lastname is required';
        }

        if (empty($inputs['age'])) {
            $errors[] = 'Age is required';
        } elseif (!is_numeric($inputs['age']) ||  $inputs['age'] <= 0) {
            $errors[] = 'Age must be a number above 0';
        }
        
        if (file_exists($_FILES['photo-id']['tmp_name'])) {
            $uploadDir = 'uploads/';
            $randName = uniqid(rand(0, 100), true) . basename($_FILES['photo-id']['name']);
            $uploadFile = $uploadDir . $randName;
            $authorizedMimes = ['image/jpeg','image/png', 'image/webp', 'image/gif'];
            $maxFileSize = 1000000;

            if (!in_array(mime_content_type($_FILES['photo-id']['tmp_name']), $authorizedMimes)) {
                $errors[] = 'Image must be a jpeg, png or webp file';
            }

            if (filesize($_FILES['photo-id']['tmp_name']) > $maxFileSize) {
                $errors[] = 'Image file must be less than ' . $maxFileSize / 1000000 . 'MB';
            }
        } else {
            $errors[] = 'Image is missing';
        }

        if (empty($errors)) {
            move_uploaded_file($_FILES['photo-id']['tmp_name'], $uploadFile);
            $name = $inputs['firstname'] . ' ' . $inputs['lastname'];
            $age = $inputs['age'];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Drivers license</title>
</head>
<body>
    <?php if (!empty($errors)) :?>
        <ul>
    <?php foreach ($errors as $error) :?>
        <li><?= $error ?></li>
    <?php endforeach;?>
        </ul>
    <?php endif;?>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="firstname">Firstname:</label>
        <input type="text" name="firstname" id="firstname">
        <label for="lastname">Lastname:</label>
        <input type="text" name="lastname" id="lastname">
        <label for="age">Age:</label>
        <input type="number" name="age" id="age">
        <label for="photo-id">Photo id: </label>
        <input type="file" name="photo-id" id="photo-id">
        <button>Send</button>
    </form>
    <?php if ($_SERVER['REQUEST_METHOD'] === "POST" && empty($errors)) :?>
        <h1>SPRINGFIELD, IL</h1>
        <div class="wrapper">
            <img src="<?= $uploadFile ?>" alt="">
            <div class="details">
                <h2>Drivers License</h2>
                <p><?= $name ?></p>
                <p>Age: <?= $age ?></p>
            </div>
        </div>
    <?php endif;?>
</body>
</html>
