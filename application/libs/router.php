<?php

namespace Application\libs\Router

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

/**
 * Class to render urls to switch from clean urls to querystrings
 */
namespace application\libs\presentation;
use application\libs\router;
class Url implements UrlRenderer
{
        private $type; // the type of URI
        
        /**
         * Constructor
         * @param int $type
         */
        public function __construct($type)
        {
                $this->type = $type;
        }
        
        /**
         * Get the URI based on the type
         */
        public function get($identifier)
        {
                if ($this->type === Router::URL_REWRITE)
                {
                        return $this->getRewriteUrl($identifier);
                }
                return $this->getQueryStringUrl($identifier);
        }
        
        /**
         * Get the URI based on the rewrite scheme.
         */
         private function getRewriteUrl($identifier)
         {
                 if ($identifier === 'status')
                 {
                         return '..';
                 }
                 return '/' . $identifier;
         }
         
         /**
          * Gets the URL based on query strings
          */
          private function getQueryStringUrl($identifier)
          {
                  if ($identifier === 'status')
                  {
                          return '?';
                  }
                  return '?' . $identifier;
          }
}

/**
 * Interface for URI rendering
 */
namespace application\libs\presentation;
interface UrlRenderer
{
        /**
         * Get the URI based on the type
         * @param string $identifier
         */
        public function get($identifier)
}

namespace application\libs\presentation;
abstract class Template implements Renderer
{
        protected $template_directory;
        protected $translator;
        protected $vars = [];
        
        public function __construct($template_directory, Translator $translator)
        {
                $this->template_directory = $template_directory;
                $this->translator = $translator;
        }
        
        public function __get($key)
        {
                if (!array_key_exists($key, $this->vars))
                {
                        return null;
                }
                return $this->vars[$key];
        }
        
        public function __isset($key)
        {
                return isset($this->vars[$key]);
        }
}

namespace application\libs\presentation;
interface Renderer
{
        public function render($template, array $data = []);
}


namespace application\libs\presentation
use application\Il8n\translator
class Html extends Template
{
        private $base_template;
        private $url;
        
        public function __construct($template_directory, $base_template, Translator $translator, UrlRenderer $url)
        {
                parent::__construct($template_directory, $translator);
                $this->base_template = $base_template;
                $this->url = $url;
        }
        
        public function render($template, array $data = [])
        {
                $this->vars = $data;
                $this->vars['content'] = $this->renderTemplate($template);
                return $this->renderTemplate($this->base_template);
        }
        
        /**
         * Renders the template (view) file using output buffering.
         * @param string $template
         * @return string - rendered view/template
         */
        private function renderTemplate($template)
        {
                ob_start();
                require 'application/views/template/header.php';
                require $this->template_directory . '/' . $template;
                require 'application/views/template/footer.php';
                $content = ob_get_contents();
                ob_end_clean();
                return $content;
                
        }
}



$attack_type = isset($_GET['type']);

// Only allow 'pvp' and 'pvm' (player vs. player) and (player vs. mob)
$allowed_types = ['pvp', 'pvm'];
if (!in_array($attack_type, $allowed_types))
{
        Session::setArr('feedback_negative', 'Error:' . htmlspecialchars($attack_type, ENT_QUOTES) . ' - Invalid attack type.');
        $this->feedbackRender();
}


if ($attack_type == 'pvp')
{
        if (!isset($_POST['attacker_name']))
        {
                Session::setArr('feedback_negative', 'Error: You must enter a playername to attack!');
                $this->feedbackRender();
        }

        /**
         * Build the target fetch, 'pvp' select the user being attacked, 'pvm' select the mob.
         * @param string $type
         * @param mixed $target
         * @return fetch : bool (false)
         */
         $target_select = ($attack_type == 'pvp') ? $_POST['targetname'] : isset($_GET['mobid']);
         public function target($type, $target)
         {
                return $this->players->playerByName($target)
                // since no class is built yet for users, this is the query.
                $query = $this->dbh->prepare('SELECT `username`,`attack`,`hp`,`critical`,`block`,`rampage`
                        FROM `stats` WHERE `username` = ?');
                $query->execute(array($_POST['attacker_name']));     
         }
         
        
}


// defender (player) : defender (mob)
$target_hp = (isset($_GET['type']) == 'pvp') ? $stat['hp'] : $mob['hp'];
$target_attack = (isset($_GET['type']) == 'pvm') ? $stat['attack'] : $mob['attack'];

// Attacking loop.

$winner = null;
while ($player_hp > 0 OR $target_hp > 0 AND $winner = null)
{
        static $i = 0;
        static $attack_turn = 'player';
        if ($attack_turn == 'player')
        {
                
                if ($target_block >= rand(1,100))
                {
                        $hit_type = 'blocked';
                        $hit_image = 'blocked.jpg';
                        $player_attack = 0;
                }
                elseif ($player_critical >= rand(1,100))
                {
                        $hit_type = 'critical';
                        $hit_image = 'criticalhit.jpg'
                        $player_attack += rand(1,50);
                }
                else
                {
                        $hit_type = 'hit';
                        $hit_image = 'hit.jpg'
                }
                
                $target_hp -= $player_attack;
                $result[$i] = [
                        'output' => $playername . 'hits for' . $player_attack,
                        'attack' => $player_attack,
                        'hp' => $target_hp,
                        'image' => $hit_image, 
                        'type' => $hit_type,
                        'winner' => $winner == null ? null : (($target_hp <= 0) ? $playername : null)
                ];
                
                // if the targets hp dropped to 0 or below, log the win into the result array.
                if ($result[$i]['winner'] == $playername OR $result[$i] !== null AND $target_hp <= 0)
                {
                        ++$i;
                        $result[$i] = [
                                'output' => $playername . 'has won!',
                                'attack' => '',
                                'hp' => '',
                                'image' => 'victory.jpg',
                                'type' => '',
                                'winner' => $playername
                        ];
                        break;
                }
                else
                {
                
                        $player_attack = 0; // reset the users attack
                        $attack_turn = 'target';
                        ++$i;
                }
        }
        elseif ($attack_turn == 'target')
        {
         
                if ($player_block >= rand(1,100))
                {
                        $hit_type = 'blocked';
                        $hit_image = 'blocked.jpg';
                        $target_attack = 0;
                }
                elseif ($target_critical >= rand(1,100))
                {
                        $hit_type = 'critical';
                        $hit_image = 'criticalhit.jpg'
                        $target_attack += rand(1,50);
                }
                else
                {
                        $hit_type = 'hit';
                        $hit_image = 'hit.jpg';
                }
                
                $player_hp -= $target_attack;
                $result[$i] = [
                        $targetname . 'hits for' . $target_attack,
                        $target_attack,
                        $player_hp,
                        $hit_image,
                        $hit_type
                ];
                $target_attack = 0;
                ++$i;
        }
        
        if ($player_hp <= 0)
        {
                $winner = $targetname;
                break;
        }
}
