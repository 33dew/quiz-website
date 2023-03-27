<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $variables['title'] ?? "Podstrona" ?></title>
    <?= getBootstrap() ?>
    <link rel="stylesheet" href="/styles/style.css">
</head>
<body>
 <?php \Helper\Functions::renderPage($path, $variables) ?>
</body>
</html>