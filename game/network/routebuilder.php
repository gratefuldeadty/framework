<?php

namespace game\network;
interface RouteBuilder
{
        public function build($identifier, $verb, callable $callback);
}
