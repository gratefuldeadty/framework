<?php

class Request
{
        /**
         * Check if the server request method is indeed post.
         */
        public function isPost()
        {
                return (($_SERVER['REQUEST_METHOD']) == 'POST') ? true : false;
        }
        
        /**
         * Check if the server request method is indeed get
         */
        public function isGet()
        {
                return (($_SERVER['REQUEST_METHOD']) == 'GET') ? true : false;
        }
        
        /**
         * Post
         * @param string $key
         * @return $_POST : false
         */
        public function post($key)
        {
                if ($this->isPost())
                {
                        return (isset($_POST[$key])) ? $_POST[$key] : false;
                }
        }
        
        /**
         * Get
         * @param string $key
         * @return $_GET : false
         */
        public function get($key)
        {
                if ($this->isGet())
                {
                        return (isset($_GET[$key])) ? $_GET[$key] : false;
                }
        }
        
        /**
         * Get all $_POST and $_GET params
         */
        public function allParams()
        {
                if ($this->isPost())
                {
                        return $_POST;
                }
                elseif ($this->isGet())
                {
                        return $_GET;
                }
        }
}
