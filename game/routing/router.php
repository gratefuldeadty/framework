<?php

class Router
{
    public static $verbs = array('GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS');
    
    public function construct()
    {
        
    }
    
    public function get($uri, $action)
    {
        return $this->addRoute(['GET', 'HEAD'], $uri, $action);
    }
    
    public function post($uri, $action)
    {
        return $this->addRoute('POST', $uri, $action);
    }
    
    public function any($uri, $action)
    {
        $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATH', 'DELETE'];
        return $this->addRoute($verbs, $uri, $action);
    }
    
    public function match($methods, $uri, $action)
    {
        return $this->addRoute(array_map('strtoupper', (array) $methods), $uri, $action);
    }
    
    public function controllers(array $controllers)
    {
        foreach ($controllers as $uri => $name)
        {
            $this->controller($uri, $name);
        }
    }
    
    public function controller($uri, $controller, $names = array())
    {
        $prepended = $controller;
        
        // Check if controller prefix has been registered in the route group.
        // Than prefix it before passing into the class instance.
        if (!empty($this->groupStack))
        {
            $prepended = $this->prependGroupUses($controller);
        }
        $routable = (new ControllerInspector)->getRoutable($prepended, $uri);
        
        foreach ($routable as $method => $routes)
        {
            foreach ($routes as $route)
            {
                $this->registerInspected($route, $controller, $method, $names);
            }
        }
        $this->addFallthroughRoute($controller, $uri);
    }

    protected function registerInspected($route, $controller, $method, &$names)
    {
        $actions = ['uses' => $controller.'@'.$method];
        $action['as'] = array_get($names, $method);
        $this->{$route['verb']}($route['uri'], $action);
    }

	protected function addFallthroughRoute($controller, $uri)
	{
	    $missing = $this->any($uri.'/{_missing}', $controller.'@missingMethod');
	    $missing->where('_missing', '(.*)');
	}

    public function resource($name, $controller, array $options = array())
	{
		(new ResourceRegistrar($this))->register($name, $controller, $options);
	}
	
	
	public function currentRouteAction()
	{
	    if (!$this->current())
	        return;

	   $action = $this->current()->getAction();
	   return isset($action['controller']) ? $action['controller'] : null;
	}
	
	public function getRoutes()
	{
		return $this->routes;
	}

