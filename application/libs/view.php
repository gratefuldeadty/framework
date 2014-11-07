<?php

class View
{
        /**
         * Render the page being viewed.
         * @param string $filename
         * @param bool $blank_render
         * @param array $data
         * @return require
         */
        public function render($filename, $blank_render = false, $data[])
        {
                if ($blank_render == true)
                {
                        require 'application/views/' . $filename . '.php';
                }
                else
                {
                        require 'application/views/templates/header.php';
                        require 'application/views/' . $filename . '.php';
                        require 'application/views/templates/footer.php';
                }
        }
}

