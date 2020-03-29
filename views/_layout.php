<?php
/** @var string $content */

use App\Model\UserModel;
use Framework\Http\Helper\Flash;

?>
<!doctype html>
<html lang="ru">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!--  Material Icons  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <title>Задачник</title>
</head>
<body>

<!--  Nav Bar  -->
<nav id="headerNavbar" class="navbar navbar-expand-lg navbar-light bg-light shadow">
    <a class="navbar-brand" href="/">Задачник</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

        </ul>
        <div class="my-2 my-lg-0">
            <?php if (UserModel::isGuest()): ?>
                <a href="/login" class="btn btn-outline-primary">Вход</a>
            <?php else: ?>
                <form action="/logout" method="post">
                    <button class="btn btn-outline-primary"><?= htmlspecialchars(UserModel::getUserById(1)['username']) ?> | Выход</button>
                </form>
            <?php endif; ?>

        </div>
    </div>
</nav>
    <?php if (Flash::has()): ?>
        <?php
        $flash = Flash::get();
        ?>
        <div class="container">
            <div class="row mt-2">
                <div class="col-sm-12 col-md-6">
                    <div class="alert-messages alert-<?=$flash['type']?> shadow">
                        <div class="alert" role="alert">
                            <?= htmlspecialchars($flash['content']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?= $content ?>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>


</body>
</html>
