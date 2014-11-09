 */
protected $controller = 'home';
/**
* Stores the method from the split URL
* @var string
*/
protected $method = 'index';
/**
* Stores the parameters from the split URL
* @var array
*/
protected $params = [];
public function __construct()
{
// Get broken up URL
$url = $this->parseUrl();
// Does the requested controller exist?
// If so, set it and unset from URL array
if (file_exists('../app/controllers/' . ucfirst($url[0]) . '.php')) {
$this->controller = $url[0];
unset($url[0]);
}
require_once '../app/controllers/' . ucfirst($this->controller) . '.php';
$this->controller = new $this->controller();
// Has a second parameter been passed?
// If so, it might be the requested method
if (isset($url[1])) {
if (method_exists($this->controller, $url[1])) {
$this->method = $url[1];
unset($url[1]);
}
}
// Set parameters to either the array values or an empty array
$this->params = $url ? array_values($url) : [];
// Call the chosen method on the chosen controller, passing
// in the parameters array (or empty array if above was false)
call_user_func_array([$this->controller, $this->method], $this->params);
}
