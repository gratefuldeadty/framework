<?php

class Router
{
        private $routes = [];

        function __construct()
        {
                $this->routes = [
                        '/user/:userid' => 'user:viewProfile'
                ];
        }
        
        function route()
        {
                // parse the url, splitting it.
                $url = filter_var($_GET['url'], FILTER_VALIDATE_URL);
                $path = parse_url($url, PHP_URL_PATH);
                $url = explode('/', rtrim($path, '/'));
                
                $path_params = $url;
                
                // default, index.
                if (count($path_params == 1))
                {
                        $this->controllerAction('index', 'index', [], []);
                }
                
                // loop through all routes, try to find a match.
                foreach ($this->routes as $route => $controller)
                {
                        $route_params = explode('/', $route);
                        $action = 'index';
                        $i = 0;
                        $objs = [];
                        $valid_route = true;
                        $path_params = array_pad($path_params, count($route_params), '');
                        $params = [];
                        
                        // handle routes that call a specific action.
                        $controller_action = explode(':', $controller);
                        $controller = $controller_action[0];
                        if (count($controller_action) == 2)
                        {
                                $action = $controller_action[1];
                        }
                        
                        // loop through each component of this route until a non-match is found, or url is done.
                        foreach ($route_params as $route_param)
                        {
                                // named parameter route
                                if (substr($route_param, 0, 1) == ':')
                                {
                                        $params[substr($route_param, 1)] = $path_params[$i];
                                }
                                elseif ($route_component == '[action]') // action route
                                {
                                        if (isset($path_params[$i])
                                        {
                                                $action = str_replace('-','_', $path_params[$i]);
                                        }
                                }
                                elseif (substr($route_param, 0, 1) == '(' AND substr($route_param, -1, 1) == ')')
                                {
                                        // create the object for the action
                                        $reflection_obj = new ReflectionClass(substr($route_component, 1, strlen($route_component) - 2));)
                                        $obj = $reflection_obj->newInstanceArgs([$path_params[$i]]);
                                        $objs[] = $obj;
                                }
                                elseif ($route_param != $path_param[$i] AND str_replace('-','_', $route_param) != $path_param[$i])
                                {
                                        $valid_route = false;
                                        break;
                                }
                                ++$i;
                        }
                        
                        // route is a match, create the controller object.
                        if ($valid_route AND ($i >= count($path_params) OR !isset($path_params[$i])))
                        {
                                $this->controllerAction($controller, $action, $objs, $parameters);
                        }
                }
                // display an error
                $this->controllerAction('index', 'error', [], []);
        }
        
        /**
         * Search for a controller file that matches the request, than load a view.
         */
        public function controllerAction($controller, $action, $objects, $parameters)
        {
                $action = ($action == 'new') ? 'edit' : $action;
                
                // look for the controller
                $controller_path = 'application/controllers/' . $controller . '.php';
                if (is_readable($controller_path))
                {
                        require_once $controller_path;
                        $components = explode('/', $controller);
                        $class = $components[count($components) - 1];
                        $controller_class = $class . 'Controller';
                        if (!method_exists($controller_class, $action))
                        {
                                if ($this->render($controller, $action))
                                {
                                        exit;
                                }
                                else
                                {
                                        echo $controller_class.' could not respond to '.$action;
                                        exit;
                                }
                        }
                        $controller_new = new $controller_class();
                        $controller_new->parameters = $parameters;
                        call_user_func_array([$controller_new, $action], $objects);
                        exit;
                }
                if ($this->render($controller, $action)) // no controller found, look for view instead!
                {
                        exit;
                }
        }
        
        public function render($controller, $action)
        {
                $path = 'application/views/' . $controller . '/' . $action . '.php';
                if (is_readable($path))
                {
                        $controller_new = new Controller();
                        require_once $path;
                        return true;
                }
                return false; // default return.
        }
}

// -- controller examples -->>

class Controller
{
        private $dbh;
        public $parameters = [];
        
        function __construct()
        {
                Session::init(); // starts a session.
                try
                {
                        $this->dbh = new Database(); // creates a new PDO connection object handler.
                }
                catch (PDOException $e)
                {
                        die('Error: Could not establish a database connection!');
                }
        }
}

class UserController extends Controller
{
        function __construct()
        {
                parent::__construct();
        }
        
        /**
         * Get a user profile, based off $_GET / userid (stored in the routes.)
         * @return void
         */
        function viewProfile()
        {
                $users = new Users($this->dbh);
                $user = $users->userData($this->parameters['userid']);
                require_once 'views/templates/header.php';
                require_once 'views/user/profile.php';
                require_once 'views/templates/footer.php';
                exit;
        }
}

// -- end controller examples -->>

// -- model examples -->>

class Model
{
        private $dbh;
        public $is_valud = false;
        
        function __construct(Database $dbh)
        {
                $this->dbh = $dbh;
                $this->is_valid = false;
        }
}

class User extends Model
{
        function __construct()
        {
                parent::__construct();
        }
        
        public function userData($userid = '')
        {
                if (isset($userid))
                {
                        if (is_numeric($userid))
                        {
                                $query = $this->dbh->prepare('SELECT `username` FROM `users`
                                        WHERE `userid` = ?');
                                $query->execute(array($userid));
                                $user = $query->fetch();
                                $this->is_valid = true;
                        }
                }
        }
}

// -- end model examples -->>
