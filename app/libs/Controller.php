<?php

class Controller
{
        function __construct()
        {
                Session::init();
                try
                {
                        $this->dbh = new Database();
                }
                catch (PDOException $e)
                {
                        die('Database connection could not be established!')
                }
                $this->view = new View(); // load the view object.
        }
        
        /**
         * Load the model
         * @param string $name
         * @return object
         */
        public function loadModel($name)
        {
                $path = 'application/models/' . $name . '.model.php';
                if (is_readable($path))
                {
                        require $path;
                        $model = $name . 'Model';
                        return new $model($this->dbh); // load the new model object
                }
        }
}

