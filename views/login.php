<?php

/** @var array $errors */

?>

<div class="container">
    <div class="row mt-2">
        <div class="col-sm-12 col-md-6 mx-auto">
            <div class="card shadow-sm p-3 mb-5 bg-white shadow">
                <div class="card-body">

                    <?php if ($errors): ?>
                        <div class="alert-messages">
                            <div class="alert alert-danger" role="alert">
                                <i class="material-icons">error_outline</i>
                                <?= htmlspecialchars($errors[0]) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="/login">
                        <div class="form-group">
                            <label for="inputUsername">Логин</label>
                            <input type="text" class="form-control" id="inputUsername" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword">Пароль</label>
                            <input type="password" class="form-control" id="inputPassword" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Войти</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
