<?php


namespace App\Model;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as V;

class UserModel
{
    private static $users = [
        [
            'id' => 1,
            'username' => 'admin',
            'password' => '123',
        ]
    ];
    private static $sessionKey = '_id';

    private static $username;
    private static $password;
    public static $errors = [];

    private static $_user;

    public static function login(): bool
    {
        if (!self::validate() && !self::$_user) {
            return false;
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION[self::$sessionKey] = self::$_user['id'];

        return true;
    }

    public static function logout(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION[self::$sessionKey]);
    }

    public static function isGuest(): bool
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        return !isset($_SESSION[self::$sessionKey]);
    }

    public static function load($postData): bool
    {
        self::$username = isset($postData['username']) ? $postData['username'] : null;
        self::$password = isset($postData['password']) ? $postData['password'] : null;

        return self::$username !== null && self::$password !== null;
    }

    public static function validate(): bool
    {
        $userValidator = new \stdClass();
        $userValidator->username = self::$username;
        $userValidator->password = self::$password;

        $validator = V::attribute('username', V::stringType()->length(1)->setName('логин'))
            ->attribute('password', V::stringType()->length(1)->setName('пароль'));

        try {
            $validator->assert($userValidator);

            if ( ($user = self::getUserByUsername($userValidator->username)) !== null && $user['password'] === $userValidator->password) {
                self::$_user = $user;
                return true;
            }

            throw new NestedValidationException('Неверный логин или пароль.');
        } catch (NestedValidationException $e) {
            $e->findMessages([
                'пароль' => 'Неверный логин или пароль.',
            ]);

            self::$errors = $e->getMessages();

            return false;
        }
    }

    public static function getUserByUsername(string $username): ?array
    {
        foreach (self::$users as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }

        return null;
    }

    public static function getIdentity(): ?array
    {
        if (self::isGuest()) {
            return null;
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        return self::getUserById($_SESSION[self::$sessionKey]);
    }

    public static function getUserById(int $id): ?array
    {
        foreach (self::$users as $user) {
            if ($user['id'] === $id) {
                return $user;
            }
        }

        return null;
    }

    public static function getErrors() {
        return self::$errors;
    }
}