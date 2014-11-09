<?php

namespace game\network;

class RouteFactory implements RouteBuilder
{
        public function build($identifier, $verb, callable $callback)
        {
                return new Route($identifier, $verb, $callback);
        }
}
