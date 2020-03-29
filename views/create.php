<?php
/** @var $task \App\Model\TaskModel */

?>
<div class="container">
    <?php if ($errors = $task->getErrors()): ?>
        <div class="row mt-2">
            <div class="col-md-6 col-sm-12 mx-auto">
                <div class="alert-messages alert-danger shadow">
                    <div class="alert" role="alert">
                        <?php foreach ($errors as $error): ?>
                            <i class="material-icons">error</i> <?= htmlspecialchars($error) ?><br>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="row mt-2">
        <div class="col-md-6 col-sm-12 mx-auto">
                <div class="card card-body shadow">
                    <form action="/task/create" method="post" name="createTaskForm">
                        <div class="form-group row">
                            <label for="inputName" class="col-sm-3 col-form-label">Имя</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="inputName" name="name" value="<?= htmlspecialchars($task->name) ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputEmail" class="col-sm-3 col-form-label">E-mail</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="inputEmail" name="email" value="<?= htmlspecialchars($task->email) ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputTextarea" class="col-sm-3 col-form-label">Текста задачи</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" id="inputTextarea" rows="3" name="description"><?= htmlspecialchars($task->description) ?></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="offset-sm-6"></div>
                            <div class="col-sm-6">
                                <button class="btn btn-outline-primary" type="submit">Отправить</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
