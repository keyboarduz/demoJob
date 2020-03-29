<?php


namespace Framework\Http\Helper;


class Flash
{
    private static $flashContent = '__flashContent';
    private static $flashTypeKey = '__flashType';

    private static function openSession(): bool
    {
        if (session_status() == PHP_SESSION_NONE) {
            return session_start();
        }

        return true;
    }

    public static function set(string $type, string $content): void
    {
        self::openSession();

        $_SESSION[self::$flashContent] = $content;
        $_SESSION[self::$flashTypeKey] = $type;
    }

    public static function get(): array
    {
        $result = [];
        $result['type'] = $_SESSION[self::$flashTypeKey];
        $result['content'] = $_SESSION[self::$flashContent];

        unset($_SESSION[self::$flashTypeKey]);
        unset($_SESSION[self::$flashContent]);

        return $result;
    }

    public static function has(): bool
    {
        self::openSession();

        return isset($_SESSION[self::$flashContent]) && isset($_SESSION[self::$flashTypeKey]);
    }


}