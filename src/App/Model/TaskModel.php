<?php


namespace App\Model;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as V;

class TaskModel
{
    const STATUS_NEW = 1;
    const STATUS_SUCCESS = 2;

    public $id;
    public $name;
    public $email;
    public $description;
    public $status;
    public $editedBy;
    public $_oldAttributes = [];

    private $pdo;
    private $validateErrors;

    public function __construct(\PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function validate(array $attributes = []): bool
    {
        $validator = V::create();
        if (!$attributes) {
            $validator = $validator::attribute('name', V::stringType()->length(1)->setName('имя пользователя'))
                ->attribute('email', V::email()->setName('E-mail'))
                ->attribute('description', V::stringType()->length(1)->setName('текст задачи'));
        } else {
            if (in_array('name', $attributes)) {
                $validator = $validator::attribute('name', V::stringType()->length(1)->setName('имя пользователя'));
            }
            if (in_array('email', $attributes)) {
                $validator = $validator::attribute('email', V::email()->setName('E-mail'));
            }
            if (in_array('description', $attributes)) {
                $validator = $validator::attribute('description', V::stringType()->length(1)->setName('текст задачи'));
            }
        }

        try {
            $validator->assert($this);
            return true;
        } catch (NestedValidationException $e) {
            $e->findMessages([
                'имя пользователя' => 'Введите {{name}}.',
                'текст задачи' => 'Введите {{name}}.',
                'E-mail' => 'Неправильный адрес электронной почты.',
            ]);

            $this->validateErrors = $e->getMessages();
            return false;
        }
    }

    public function updateDescription($id, bool $validate = true): bool
    {
        if ($validate && !$this->validate(['description'])) {
            return false;
        }

        if ($this->description === $this->_oldAttributes['description']) {
            return true;
        }

        $this->editedBy = ($user = UserModel::getIdentity()) === null ? 0 : $user['id'];

        $stmt = $this->pdo->prepare('
            UPDATE task
            SET description = :newDescription,
                editedBy = :editedBy
            WHERE id = :taskId
        ');
        $stmt->bindParam(':newDescription', $this->description);
        $stmt->bindParam(':taskId', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':editedBy', $this->editedBy, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateStatus(int $id,int $newStatus, bool $validate = true): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE task
            SET status = :newStatus
            WHERE id = :taskId
        ');
        $stmt->bindParam(':newStatus', $newStatus);
        $stmt->bindParam(':taskId', $id, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function findById(int $id): ?array
    {
        if (! V::intVal()->validate($id)) {
            return null;
        }

        $stmt = $this->pdo->prepare('
            SELECT * FROM task
            WHERE id=:taskId
        ');
        $stmt->bindParam(':taskId', $id, \PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($result) {
            $this->_oldAttributes['id'] = $result['id'];
            $this->_oldAttributes['name'] = $result['name'];
            $this->_oldAttributes['email'] = $result['email'];
            $this->_oldAttributes['description'] = $result['description'];
            $this->_oldAttributes['status'] = $result['status'];

            return $result;
        }

        return null ;
    }

    public function save(bool $validate = true): bool
    {
        if ($validate && !$this->validate()) {
            return false;
        }

        $stmt = $this->pdo->prepare('
            INSERT INTO task (name, email, description, status)
            VALUES (:name, :email, :description, 1)
        ');
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':description', $this->description);

        return $stmt->execute();
    }

    public function search(array $queryParams, int $itemsPerPage): ?array
    {
        $page = intval($queryParams['page'] ?? 1);
        $sortAttribute = $queryParams['sort'] ?? null;

        $startingLimit = ($page-1)*$itemsPerPage;

        switch($sortAttribute) {
            case 'name':
                $orderBy = 'name ASC'; break;
            case '-name':
                $orderBy = 'name DESC'; break;
            case 'email':
                $orderBy = 'email ASC'; break;
            case '-email':
                $orderBy = 'email DESC'; break;
            case 'status':
                $orderBy = 'status ASC'; break;
            case '-status':
                $orderBy = 'status DESC'; break;
            default:
                $orderBy = 'id DESC';
        }

        $stmt = $this->pdo->prepare("
            SELECT * FROM task
            ORDER BY {$orderBy}
            LIMIT :itemsPerPage
            OFFSET :startingLimit
        ");
        $stmt->bindParam(':startingLimit', $startingLimit, \PDO::PARAM_INT);
        $stmt->bindParam(':itemsPerPage', $itemsPerPage, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getCountTask(): ?string
    {
        $stmt = $this->pdo->query('SELECT count(*) FROM task');
        return $stmt->fetchColumn() ?: 0;
    }

    public function getErrors() {
        return $this->validateErrors;
    }
}