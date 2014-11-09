<?php

namespace game\network;
class Request implements RequestData
{
        private $getVariables = [];
        private $postVariables = [];
        private $serverVariables = [];
        private $pathVariables = [];
        public function __construct(array $get, array $post, array $server)
        {
                $this->getVariables    = $get;
                $this->postVariables   = $post;
                $this->serverVariables = $server;
                $this->pathVariables   = explode('/', trim($server['REQUEST_URI'], '/'));
        }
        
        public function get()
        {
                if (!$this->getVariables)
                {
                        return false;
                }
                $queryStringParams = array_keys($this->getVariables)
                return reset($queryStringParams);
        }
        
        public function path()
        {
                return end($this->pathVariables);
        }
        
        public function getVerb()
        {
                return $this->serverVariables['REQUEST_METHOD'];
        }
        
        public function post($name)
        {
                return $this->postVariables[$name];
        }
        
        public function getUrl()
        {
                $scheme = 'http';
                if (isset($this->serverVariables['HTTPS']) && $this->serverVariables['HTTPS'] === 'on')
                {
                        $scheme .= 's';
                }
                return $scheme . '://' . $this->serverVariables['HTTP_HOST'] . $this->serverVariables['REQUEST_URI'];
        }
        
        public function getIp()
        {
                return $this->serverVariables['REMOTE_ADDR'];
        }
}
