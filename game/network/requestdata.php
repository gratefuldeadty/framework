<?php

namespace game\network;
interface RequestData
{
        public function get();
        public function path();
        public function getVerb();
        public function post($name);
        public function getUrl();
        public function getIp();
}
