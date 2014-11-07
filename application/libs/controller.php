<?php

class Controller
{
        public $dbh = null;

        /**
         * Constructor
         * Start the session, (if not existing)
         * Create a PDO connection
         * Create a view object.
         */
        function __construct()
        {
                Session::init();
                try
                {
                        $this->databaseConnect();
                }
                catch (PDOException $e)
                {
                        die('Error: Database connection error.');
                }
                $this->view = new View(); // create the view object
        }
        
        /**
         * Create a PDO connection.
         * In Question: Database class instead?
         */
        private function databaseConnect()
        {
                $options = [
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
                ];
                $this->dbh = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS, $options);
        }
        
        /**
         * Load the model.
         * @param string $model
         * @return object (the model)
         */
        public function loadModel($model)
        {
                $path = 'application/models/' . strtolower($model) . '.model.php';
                if (is_readable($path))
                {
                        require $path;
                }
                return new $model($this->dbh);
        }
}
