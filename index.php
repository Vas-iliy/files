<?php

$connection = new PDO ('mysql:host=localhost; dbname=files; charset=utf8', 'root', 'root');

if (isset($_POST['go'])) {
    $fileName = $_FILES['file']['name'];
    $fileType = $_FILES['file']['type'];
    $fileTmp_name = $_FILES['file']['tmp_name'];
    $fileError = $_FILES['file']['error'];
    $fileSize = $_FILES['file']['size'];

    $fileExtension = strtolower(end(explode('.', $fileName))); //это массив, в котором будет [имя,расширение]
    $fileName = explode('.', $fileName)[0];
    $fileName = preg_replace('/[0-9]/', '', $fileName);
    $allowedExtensions = ['jpg', 'jpeg', 'png'];

    if (in_array($fileExtension, $allowedExtensions)) {
        if ($fileSize < 5000000) {
            if ($fileError == 0) {
                $connection->query("INSERT INTO images (image, extension)
                VALUES ('$fileName', '$fileExtension')");

                $lastId = $connection->query("SELECT MAX(id) FROM images");
                $lastId = $lastId->fetchAll();
                $lastId = $lastId[0][0];

                $fileNameNew = $lastId . $fileName . '.' . $fileExtension;
                $fileDestination = 'Uploads/' . $fileNameNew;
                move_uploaded_file($fileTmp_name, $fileDestination);
                echo 'Успех';

            } else {
                echo 'Что-то пошло не так';
            }
        } else {
            echo 'Большой размер файла';
        }

    } else {
        echo 'Неверный тип файла';
    }
}

$data = $connection->query("SELECT * FROM emages");


/*echo "<pre>";
var_dump($_FILES);
echo "</pre>";*/

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <input type="submit" name="go">
</form>

</body>
</html>