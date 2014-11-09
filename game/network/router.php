<?php

namespace game\network;
class Router
{
        const URL_REWRITE = 1;
        const QUERY_STRING = 2;
        
        private $request;
        private $route_factory;
        private $identifier_type;
        private $routes = [];
        
        public function constuct(RequestData $request, RouteBuilder $route_factory, $identifier_type = self::URL_REWRITE)
        {
                $this->request = $request;
                $this->route_factory = $route_factory;
                $this->identifier_type = $identifier_type;
        }
        
        /**
         * Create a Post route
         */
        public function post($identifier, callable $callback)
        {
                $this->routes[] = $this->route_factory->build($identifier, 'POST', $callback);
        }
        
        /**
         * Create a Get route
         */
        public function get($identifier, callable $callback)
        {
                $this->routes[] = $this->route_factory->build($identifier, 'GET', $callback);
        }
        
        /**
         * Find matching route - run callback
         */
        public function ruin()
        {
                foreach ($this->routes as $route)
                {
                        if (!$route->matchesRequest($this->getIdentifier(), $this->request->getVerb()))
                        {
                                continue;
                        }
                        return $route->run();
                }
                return 'No matches found.';
        }
        
        /**
         * Get the identifier of current request.
         */
        public function getIdentifier()
        {
                if ($this->identifierType === self::URL_REWRITE)
                {
                        return $this->request->path();
                }
                return $this->request->get();
        }
}
