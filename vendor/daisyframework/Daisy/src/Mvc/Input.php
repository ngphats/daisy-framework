<?php

namespace Daisy\Mvc;

use Daisy\Mvc\Application;

class Input
{
    protected static $instance;

    protected function __construct() {}

    protected function __clone() {}

    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Khởi tạo các phương thức cần thiết cho AngularJS
     */
    public static function getMethodsForAngularJS()
    {
        global $_PUT, $_PATCH, $_DELETE;

        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET)) {
            return $_GET;
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            return $_POST;
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)) {
            return $_POST = json_decode(file_get_contents('php://input'), true);
        } elseif ($_SERVER['REQUEST_METHOD'] == 'PUT' && empty($_PUT)) {
            return $_PUT = json_decode(file_get_contents('php://input'), true);
        } elseif ($_SERVER['REQUEST_METHOD'] == 'PATCH' && empty($_PATCH)) {
            return $_PATCH = json_decode(file_get_contents('php://input'), true);
        } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE' && empty($_DELETE)) {
            return $_DELETE = true;
        }
        return false;
    }

    /**
     * Kiểm tra có phải phương thức GET không
     * @return bool
     */
    public static function isGet()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'GET') ? true : false;
    }

    /**
     * Cách tắt khi dùng isset() & !empty() trong Array/Object
     * @param string $input
     * @param $offset
     * @return bool
     */
    public static function isExists($input = '', $offset)
    {
        $flag = false;
        if (is_array($input)) {
            $flag = (isset($input[$offset]) && !empty($input[$offset])) ? true : false;
        } elseif (is_object($input)) {
            $flag = (isset($input->$offset) && !empty($input->$offset)) ? true : false;
        }
        return $flag;
    }

    /**
     * Lấy đối số tương ứng trong Request
     * @param string $key
     * @return bool
     */
    public static function get($key = null)
    {
        if (!is_null($key) && isset(Application::$request[$key]) && !empty(Application::$request[$key])) {
            return Application::$request[$key];
        }
        if (is_null($key) && isset(Application::$request) && !empty(Application::$request)) {
            return Application::$request;
        }
        return false;
    }
}