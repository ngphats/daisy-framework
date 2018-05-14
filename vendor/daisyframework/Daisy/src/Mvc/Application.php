<?php 

namespace Daisy\Mvc;

use Daisy\Mvc\Input;

class Application 
{
    protected static $route; //Lưu trữ REQUEST_URI
    protected static $controller; //Lưu trữ tên Controller
    protected static $action; //Lưu trữ tên Action
    protected static $routeNotFound = false; //Flag sử dụng khi chuyển trang
    protected static $filters; //Bộ lọc
    protected static $salt = 'aX@8cs#M'; //Chuỗi mã hóa tự đặt
    public static 	 $request;
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
     * Khởi chạy ứng dụng
     */
    public static function start()
    {
        static::getRoute();
        static::$request = Input::getMethodsForAngularJS();
    }

    /**
     * Lấy Routes
     * @return mixed
     */
    public static function getRoute()
    {
        static::$route = (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
        static::$route = static::removeURIParentFolders(static::$route);
        return static::$route;
    }

    /**
     * Chuyển đổi Array hoặc String thành JSON
     * @param array $data
     * @return string
     */
    public static function toJson($data = [], $callName = 'callback')
    {
        $data = json_encode($data);
        if (isset($_GET[$callName]) && !empty($_GET[$callName])) {
            return "{$_GET[$callName]}($data)";
        }
        return $data;
    }

    /**
     * Lấy và chuyển đổi JSON thành Array, nhưng nếu bạn thích trả về đối tượng. Hãy thay đối số $isArray thành false.
     * @param string $json
     * @param bool $isArray
     * @return mixed
     */
    public static function getJson($json = '', $isArray = true)
    {
        return json_decode($json, $isArray);
    }

    /**
     * Lấy các biến dựa trên Route được định nghĩa
     * @param $route
     * @param $uri
     * @return array
     */
    public static function getVar($route, $uri)
    {
        $route = preg_replace('/\//', '\/', $route);
        $uri = static::filterURI($uri);
        preg_match_all('/{(.*)}/U', $route, $vars);
        $allVars = [];
        if ($vars) {
            $route = preg_replace('/{.*}/U', '(.*)', $route);
            preg_match('/^' . $route . '$/U', $uri, $values);
            if ($values) {
                unset($values[0]);
                $values = array_values($values);
                foreach ($vars[1] as $key => $var) {
                    $allVars[$var] = isset($values[$key]) ? $values[$key] : '';
                }
            }
        }
        return $allVars;
    }

    /**
     * Xóa toàn bộ Querystring trên URI
     * @param $route
     * @return string
     */
    public static function filterURI($route)
    {
        $route = preg_replace('/\/?\?.*/u', '', $route);
        return trim($route, ' /');
    }

    /**
     * Xóa thư mục cha trên REQUEST_URI
     * @param $route
     * @return string
     */
    public static function removeURIParentFolders($route)
    {
        $parentFolders = trim(preg_replace('/\/[\w]+.php/U', '', $_SERVER['PHP_SELF']), ' /');
        $route = preg_replace('/' . preg_quote($parentFolders, '/') . '/u', '', $route);
        return trim($route, ' /');
    }

    /**
     * So sánh Route và URI
     * @param string $route
     * @param string $uri
     * @return bool
     */
    public static function compareRoute($route = '', $uri = '')
    {
        $route = preg_replace('/\//', '\/', $route); //Escapsing '/'
        preg_match_all('/({.*})/U', $route, $vars); //Get All Abstract Vars
        if ($vars) {
            foreach ($vars[1] as $var) {
                $route = preg_replace('/' . preg_quote($var, '/') . '/U', '[\w-]+', $route);
            }
        }
        if (preg_match('/^' . $route . '(\?.*|\/\?.*)?$/U', $uri, $uri)) {
            return true;
        }
        return false;
    }

    /**
     * Lấy Controller và Action
     * @param $callback
     * @return bool
     */
    public static function getControllerAndAction($callback)
    {
        $all = explode('@', $callback);
        if (strpos($all[0], 'Controller')) {
            static::$controller = "\App\Controller\\".$all[0];
            static::$action = $all[1] . "Action";
            return true;
        }
        return false;
    }

    /**
     * Tạo Route - Hoạt động giống Laravel nhưng chỉ hỗ trợ Route và Filter đơn giản.
     * @param string $route
     * @param null $callback accepts Anonymous Function or Controller@Action
     */
    public static function route($route = '', $callback = null)
    {
        $route = trim($route, '/'); //Trim '/' on Route

        //Matching Route and run $callback when it is callable.
        //Dont misunderstand $route and static::$route. $route is defined by Developer and static::$route is REQUEST_URI from $_SERVER
        if (static::compareRoute($route, static::$route) 
        	&& is_callable($callback)
        ) {
            $vars = static::getVar($route, static::$route);
            echo call_user_func_array($callback, $vars);
            static::$routeNotFound = false;
            exit();
        } elseif (static::compareRoute($route, static::$route) 
        	&& is_string($callback) 
        	&& static::getControllerAndAction($callback)
        ) {
            $vars = static::getVar($route, static::$route);

            $viewModel = call_user_func_array([static::$controller, static::$action], $vars);

            if ($viewModel != '') {
            	$viewModel->setTemplate($callback)->render();
            }

            static::$routeNotFound = false;
            exit();

        } else {
            static::$routeNotFound = true;
        }
    }

    /**
     * Tạo Filter - Chạy code khi ứng dụng bắt đầu.
     * @param $routeApplied
     * @param $callback
     */
    public static function filter($routeApplied, $callback)
    {
        if (is_array($routeApplied) && is_callable($callback)) {
            foreach ($routeApplied as $route) {
                $route = preg_replace('/\//', '\/', $route);
                if (preg_match("/^$route$/U", static::$route)) {
                    if (!$callback()) {
                        exit();
                    }
                    break;
                }
            }
        } elseif (is_string($routeApplied) && is_callable($callback)) {
            $routeApplied = preg_replace('/\//', '\/', $routeApplied);
            if (preg_match("/^$routeApplied$/U", static::$route)) {
                if (!$callback()) {
                    exit();
                }
            }
        }
    }

    /**
     * Lấy chuỗi bất kỳ
     * @return string
     */
    public static function getHash()
    {
        $salt = static::$salt;
        return crypt(time(), '$1$' . static::$salt . '$');
    }

    /**
     * Mã hóa Mật khẩu
     * Bạn có thể thay đổi salt trong thuộc tính $salt
     * @param $password
     * @return string
     */
    public static function encryptPassword($password)
    {
        return crypt(md5($password), '$1$' . static::$salt . '$');
    }

    /**
     * Xác nhận Mật khẩu
     * Sử dụng phương thức này khi bạn dùng encryptPassword()
     * @param $password
     * @param $dbPassword
     * @return bool
     */
    public static function verifyPassword($password, $dbPassword)
    {
        return (static::encryptPassword($password) == $dbPassword) ? true : false;
    }

    /**
     * Kết thúc ứng dụng
     */
    public static function end()
    {
        $route = static::$route;
        if ($route && static::$routeNotFound) {
            try {
                throw new RouteException("Không tìm thấy Route!", 100, null, "Vui lòng kiểm tra lại Route \" <strong style='color: red'>$route</strong> \" và phương thức <strong style='color: red'>{$_SERVER['REQUEST_METHOD']}</strong> có được sử dụng không?");
            } catch (RouteException $e) {
                header('Content-Type: text/html');
                $e->getErrorPage();
            }
        }
    }
}
