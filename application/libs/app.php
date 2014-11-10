 <?php

/**
 * Loads the current controller and model, 
 */

class Application
{
        private $ucontroller = null; // controller
        private $action = null; // action/method of the controller
        private $params = []; // parameters
        
        /**
         * Constructor - initiate the application.
         * Break down the controller, action and params to call the controller/action
         * If no controller is found, error page is shown, 
         */
        public function construct()
        {
                $this->parseUrl();
                if ($this->ucontroller())
                {
                        if (is_readable('application/controller/' . $this->ucontroller . '.php'))
                        {
                                require 'application/controller/' . $this->ucontroller . '.php';
                                $this->ucontroller = new $this->ucontroller();
                                if ($this->action)
                                {
                                        if (method_exists($this->ucontroller, $this->action))
                                        {
                                                //call_user_func_array([$this->ucontroller, $this->action], $this->params);
                                                $this->ucontroller->{$this->action}($this->params);
                                        }
                                        else
                                        {
                                                header('Location:' . URL . 'error/index');
                                        }
                                }
                                else
                                {
                                        $this->ucontroller->index();
                                }
                        }
                        else
                        {
                                header('Location:' . URL . 'error/index');
                        }
                }
                else
                {
                        // no controller found - display the error page (or main index/home page)
                        require 'application/controller/index.php';
                        $error = new Index();
                        $error->index();
                }
        }

        
        /**
         * Parse/split the url.
         */
        private function parseUrl()
        {
                if (isset($_GET['url']))
                {
                        $url = filter_var($_GET['url'], FILTER_SANITIZE_URL);
                        $url = parse_url($url, PHP_URL_PATH);
                        $url = explode('/', rtrim($url, '/'));
                        
                        $i = 0;
                        $whitelist = [''.$url[0].'', ''.$url[1].''];
                        foreach ($url as $value)
                        {
                                $this->ucontroller = ($value == $url[0]) ? $value : null;
                                $this->action = ($value == $url[1]) ? $value : null;
                                
                                if (in_array($value, $whitelist))
                                {
                                        $this->params[] = $value;
                                }
                                ++$i;
                        }
                }
        }
}
