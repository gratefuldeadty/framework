<?php

use app\libs\translator;

class Html extends Template
{
        private $template; // base page where all templates get rendered.
        private $url; // instance of URI renderer.
        
        public function __construct($directory, $template, Translator $translator, UrlRenderer $url)
        {
                parent::__construct($directory, $translator);
                $this->template = $template;
                $this->url = $url;
        }
        
        /**
         * Render a template
         * @param string $template
         * @param array $data
         */
        public function render($template, array $data = [])
        {
                $this->variables = $data;
                $this->variables['content'] = $this->renderTemplate($template);
                return $this->renderTemplate($this->template);
        }
        
        /**
         * Return of the rendered template.
         * @param string $template
         * @return 'rendered template - output buffered'
         */
        private function renderTemplate($template)
        {
                ob_start();
                require $this->directory . '/' . $template;
                $content = ob_get_contents();
                ob_end_clean();
                return $content;
        }
        
        public function view($filename)
        {
                require 'application/views/templates/header.php';
                require 'application/views/' . $filename .'.php';
                require 'application/views/template/footer.php';
        }
        
        /**
         * Render feedback messages, ie: errors, messages ect.
         * @return void
         */
        public function renderFeedback()
        {
                require 'application/views/templates/feedback.php';
                Session::set('feedback_positive', null);
                Session::set('feedback_negative', null);
        }

