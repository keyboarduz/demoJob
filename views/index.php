<?php
/** @var array $tasks */
/** @var string $sortAttribute */
/** @var \JasonGrimes\Paginator $pagination */

use App\Model\UserModel;
use App\Model\TaskModel;

?>

<div class="container">
    <div class="row mt-2">
        <div class="col-sm-12">
            <a href="/task/create" class="btn btn-primary">
                <i class="material-icons">add</i>
            </a>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">
                            Имени пользователя
                            <?php if ($tasks): ?>
                                <a href="?page=<?= $pagination->getCurrentPage() ?>&sort=name"><i class="material-icons">keyboard_arrow_up</i></a><a href="?page=<?= $pagination->getCurrentPage() ?>&sort=-name"><i class="material-icons">keyboard_arrow_down</i></a>
                            <?php endif; ?>
                        </th>
                        <th scope="col">
                            E-mail
                            <?php if ($tasks): ?>
                                <a href="?page=<?= $pagination->getCurrentPage() ?>&sort=email"><i class="material-icons">keyboard_arrow_up</i></a><a href="?page=<?= $pagination->getCurrentPage() ?>&sort=-email"><i class="material-icons">keyboard_arrow_down</i></a>
                            <?php endif; ?>
                        </th>
                        <th scope="col">
                            Текста задачи
                        </th>
                        <th scope="col">
                            Статус
                            <?php if ($tasks): ?>
                                <a href="?page=<?= $pagination->getCurrentPage() ?>&sort=status"><i class="material-icons">keyboard_arrow_up</i></a><a href="?page=<?= $pagination->getCurrentPage() ?>&sort=-status"><i class="material-icons">keyboard_arrow_down</i></a>
                            <?php endif; ?>
                        </th>
                        <?php if(!UserModel::isGuest()): ?>
                            <th scope="col">
                                Действие
                            </th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody id="taskData">
                    <?php if ($tasks): ?>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td><?= htmlspecialchars($task['name']) ?></td>
                                <td><?= htmlspecialchars($task['email']) ?></td>
                                <td><?= htmlspecialchars($task['description']) ?></td>
                                <td>
                                    <?= $task['status'] == TaskModel::STATUS_SUCCESS ?
                                        '<span class="badge badge-success ">Выполнено</span>' :
                                        '<span class="badge badge-primary ">Обработке</span>' ?>
                                    <?php if ($task['editedBy'] != 0): ?>
                                        <br>
                                        <span class="badge badge-info  mt-1">отредактировано администратором</span>
                                    <?php endif; ?>
                                </td>
                                <?php if(!UserModel::isGuest()): ?>
                                    <td>
                                        <?php if ($task['status'] == TaskModel::STATUS_NEW): ?>
                                            <form action="/task/update/status" method="post">
                                                <input type="hidden" name="id" value="<?=$task['id']?>">
                                                <button type="submit" class="btn btn-success btn-sm rounded-circle shadow"><span class="material-icons">done_outline</span></button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="/task/update?id=<?=$task['id']?>" class="btn btn-warning btn-sm rounded-circle shadow"><span class="material-icons">edit</span></a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Нет данных</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <nav aria-label="...">
        <ul class="pagination">
            <?php if ($pagination->getPrevUrl()): ?>
                <li class="page-item">
                    <a href="<?php echo $pagination->getPrevUrl(); ?>" class="page-link" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php foreach ($pagination->getPages() as $page): ?>
                <?php if ($page['url']): ?>
                    <li <?php echo $page['isCurrent'] ? 'class="active page-item"' : ''; ?>>
                        <a href="<?php echo $page['url']; ?>" class="page-link"><?php echo $page['num']; ?></a>
                    </li>
                <?php else: ?>
                    <li class="disabled page-item"><span><?php echo $page['num']; ?></span></li>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($pagination->getNextUrl()): ?>
                <li class="page-item">
                    <a href="<?php echo $pagination->getNextUrl(); ?>" class="page-link" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>