<?php

namespace game\network;

class Route
{
        private $identifier;
        private $verb;
        private $callback;
        
        public function __construct($identifier, $verb, callable $callback)
        {
                $this->identifier = $identifier;
                $this->verb = $verb;
                $this->callback = $callback;
        }
        
        /**
         * Check whether the current request matches the route.
         */
        public function matchesRequest($identifier, $verb)
        {
                return $this->identifier === $identifier && $this->verb === $verb;
        }
         
        /**
         * Run the route.
         */
        public function run()
        {
                $callback = $this->callback;
                return $callback();
        }
        
}
