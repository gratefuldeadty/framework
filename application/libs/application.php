<?php

/**
 * Routes
 */
$routes = [
        '/index' => 'index#index',
        '/dashboard' => 'dashboard#index',
        '/error' => 'error#error',
        '/login' => 'login#index',
        '/login' => 'login#register',
        '/login' => 'login#showprofile',
        '/overview' => 'overview#index',
        '/overview' => 'overview#profile'
        ];



class Application
{
        private $controller;
        private $action;
        private $routes[];
        
        public function __construct()
        {
                $this->splitUrl();
                
                if (is_readable('application/controller/' . $this->controller . '.php')
                {
                        require 'application/controller/' . $this->controller . '.php';
                        $this->controller = new $this->controller();
                }
                else
                {
                        // showing the index of the selected controller.
                        require 'application/controller/index.php';
                        $index = new Index();
                        $index->index();
                }
        }
        
        /**
         * Setting the controller directory path.
         * @param string $path
         * @return void
         */
        private function path($path)
        {
                if (!is_dir($path))
                {
                        throw new Exception('Invalid controller path `'.$path.'`');
                }
                $this->path = $path;
        }
        
        private function splitUrl()
        {
                $url = filter_var($this->request->get('url'), FILTER_VALIDATE_URL);
                $url = parse_url($url, PHP_URL_PATH);
                $url = explode('/', rtrim($url), '/');
                
                $this->controller = (isset($url[0])) ? $url[0] : null;
                $this->action = (isset($url[1])) ? $url[1] : null;
                $this->param1 = (isset($url[2])) ? $url[2] : null;
                $this->param2 = (isset($url[3])) ? $url[3] : null;
                $this->param3 = (isset($url[4])) ? $url[4] : null;
        }
        
}
