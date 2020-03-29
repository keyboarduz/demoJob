<?php
/** @var array $taskModel */
/** @var array $errors */

?>
<div class="container">
    <div class="row mt-2">
        <?php if ($errors): ?>
            <div class="col-sm-12 col-md-6 mx-auto">
                <div class="card shadow">
                    <div class="alert-messages alert-danger" id="formAlertMessages">
                        <div class="alert" role="alert">
                            <?php foreach ($errors as $error): ?>
                                <i class="material-icons">error</i> <?= htmlspecialchars($error) ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="row mt-2">
        <div class="col-sm-12 col-md-6 mx-auto mt-2">
            <div class="card shadow">
                <div class="card-body">
                    <form action="/task/update" method="post">
                        <input type="hidden" name="id" value="<?=$taskModel['id']?>">
                        <div class="form-group">
                            <label for="nameInput">Имя</label>
                            <input type="text" class="form-control" id="nameInput" name="name" value="<?=htmlspecialchars($taskModel['name'])?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="emailInput">E-mail</label>
                            <input type="email" class="form-control" id="emailInput" name="email" value="<?=htmlspecialchars($taskModel['email'])?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="descriptionInput">Текста задачи</label>
                            <textarea class="form-control" id="descriptionInput" rows="3" name="description" ><?=htmlspecialchars($taskModel['description'])?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary mx-auto d-block">Сохранить</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
