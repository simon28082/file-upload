<?php
if (isset($_POST) && !empty($_POST)) {

    require '../vendor/autoload.php';

    $config = require '../config/upload.php';

    $config = new \Illuminate\Config\Repository(['upload'=>$config]);

    $upload = new \CrCms\Upload\FileUpload($config);

    $file = $upload->upload();
    var_dump($file);
}
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
<form action="./upload.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="test" value="1" >
    <input type="file" name="file">
    <button type="submit"> submit </button>
</form>
</body>
</html>
