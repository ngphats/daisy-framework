<?php 

namespace Daisy\Mvc;

use Daisy\Mvc\Application;

class Route
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
     * Viết Route cho phương thức GET
     * @param $route
     * @param null $callback
     */
    public static function get($route, $callback = null)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            Application::route($route, $callback);
        }
    }

    /**
     * Viết Route cho phương thức POST
     * @param $route
     * @param null $callback
     */
    public static function post($route, $callback = null)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            Application::route($route, $callback);
        }
    }

    /**
     * Viết Route cho phương thức PUT
     * @param $route
     * @param null $callback
     */
    public static function put($route, $callback = null)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            Application::route($route, $callback);
        }
    }

    /**
     * Viết Route cho phương thức PATCH
     * @param $route
     * @param null $callback
     */
    public static function patch($route, $callback = null)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
            Application::route($route, $callback);
        }
    }

    /**
     * Viết Route cho phương thức DELETE
     * @param $route
     * @param null $callback
     */
    public static function delete($route, $callback = null)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            Application::route($route, $callback);
        }
    }

    /**
     * Viết Route cho phương thức bất kỳ
     * @param $route
     * @param null $callback
     */
    public static function any($route, $callback = null)
    {
        Application::route($route, $callback);
    }
}