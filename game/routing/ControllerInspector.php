<?php

class ControllerInspector 
{

    protected $verbs = [
            'any', 'get', 'post', 'put', 'path',
            'delete', 'head', 'options'
        ];
    
    /**
     * Get routable methods for a controller.
     * @param string $controller
     * @param string $prefix
     * @return array
     */
    public function getRoutable($controller, $prefix)
    {
        $routable = array();
        $reflection = new ReflectionClass($controller);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        
        foreach ($methods as $method)
        {
            if ($this->isRoutable($method))
            {
                $data = $this->getMethodData($method, $prefix);
                $routable[$method->name][] = $data;
                if ($data['plain'] == $prefix.'/index')
                {
                    $routable[$method->name][] = $this->getIndexData($data, $prefix);
                }
            }
        }
        return $routable;
    }
    
    /**
     * Check if the controller method is routable.
     * @param object $method
     * @return bool
     */
    public function isRoutable(ReflectionMethod $method)
    {
        if ($method->class == 'Elitewars\Routing\Controller')
            return false;
            return starts_with($method->name, $this->verbs);
    }
    
    /**
     * Get the method data for method.
     * @param object $method
     * @param string $prefix
     * @return array
     */
    public function getMethodData(ReflectionMethod $method, $prefix)
    {
        $verb = $this->getVerb($name = $method->name);
        $uri = $this->addUriWildcards($plain = $this->getPlainUri($name, $prefix));
        return compact('verb', 'plain', 'uri');
    }
    
    /**
     * Get routable data for an index method.
     * @param array $data
     * @param string $prefix
     * @return array
     */
    protected function getIndexData($data, $prefix)
    {
        return ['verb' => $data['verb'], 'plain' => $prefix, 'uri' => $prefix];
    }
    
    /**
     * Extract verb from a controller's action
     * @param string $name
     * @return string
     */
    public function getVerb($name)
    {
        return head(explode('_', snake_case($name)));
    }
    
    /**
     * Determine the URI from the method name.
     * @param string $name
     * @param string $prefix
     * @return string
     */
    public function getPlainUri($name, $prefix)
    {
        return $prefix . '/' . implode('-', array_slice(explode('_', snake_case($name)), 1));   
    }
    
    /**
     * Add wildcard(s) to URI
     * @param string $uri
     * @return string
     */
    public function addUriWildcards($uri)
    {
        return $uri . '/{one?}/{two?}/{three?}/{four?}/{five?}';
    }
}
