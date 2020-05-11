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

if ($_POST) {
    header("Location:index.php");
}

$data = $connection->query("SELECT * FROM images");
echo "<div style='display: flex; align-items: flex-end; flex-wrap: wrap'>";
foreach ($data as $img) {

    //а это путь до нашей картинки, который мы берем из БД
    $image = "Uploads/" . $img['id']. $img['image'] . '.' . $img['extension'];

    //если нажета кнопка с каким-то айди, значит хотят удалить эту картинку
    //идет обращение к БД и удаление от туда информации о картинке с этим айди
    $delete = "delete".$img['id'];
    if (isset($_POST[$delete])) {
        $imageId = $img['id'];
        $connection->query("DELETE FROM files.images WHERE id = '$imageId'");

        //так же мы ищем картинку с этим айди и удаляем ее с сайта
        if (file_exists($image)) {
            unlink($image);
        }
    }

   //если картинка существует, то мы ее добавляем на сайт, а так же добавляем кнопку удалить
    if (file_exists($image)) {
        echo "<div>";
        echo "<img width='150' src=$image>";
        echo "<form method='post'><input type='submit' name='delete".$img['id']."' 
                value='Удалить'></form></div>";
    }



}

echo "</div>"
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