<?php

namespace App\Libs\Translators;

class FileTranslator implements Translator
{
        private $texts;
        
        public function __construct($directory, $language)
        {
                $file = $directory . '/' . $language . '.php';
                if (!is_readable($file))
                {
                        throw new \Exception('Unsupported language (`' . $language .'`).');
                }
                require $file;
                if (!isset($texts))
                {
                        throw new \Exception ('Translation file (`' . $file .'`) has an invalid format.');
                }
                $this->texts = $texts;
        }
        
        public function translate($key)
        {
                if (array_key_exists($key, $this->texts))
                {
                        return $this->texts[$key];
                }
                return '{{' . $key . '}}';
        }
}
