<?php 

namespace Daisy\Mvc;

class ViewModel
{
	protected $content = '';
	protected $variables = [];

	function __construct(Array $variables) 
	{
		if (null !== $variables) {
			$this->setVariables($variables);
		}
	}

	function __get($name) {
		if ($this->__isset($name) == true) {
			return $this->variables[$name];
		}
	}

	function __isset($name) {
		return isset($this->variables[$name]);
	}

	public function setVariables($variables) {
		if (! empty($variables)) {
			foreach($variables as $key => $val) {
				$this->variables[$key] = $val;
			}
		}
	}

    /**
     * Set helper plugin manager instance
     *
     * @param  string|HelperPluginManager $helpers
     * @return PhpRenderer
     * @throws Exception\InvalidArgumentException
     */
    public function setHelperPluginManager($helpers) {}

    /**
     * Get helper plugin manager instance
     *
     * @return HelperPluginManager
     */
    public function getHelperPluginManager() {}

    /**
     * Get plugin instance
     *
     * @param  string     $name Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return AbstractHelper
     */
    public function plugin($name, array $options = null) {}

	public function setTemplate($callback) 
	{
		// get controller and action
        $all = explode('@', $callback);
        $controller = strtolower(str_replace('Controller', '', $all[0]));
        $action = $all[1];

        // extract variables
        if (!empty($this->variables)) {
        	extract($this->variables);
        }
 
        // Chuyển nội dung view thành biến thay vì in ra bằng cách dùng ab_start()
        ob_start();
        include APP_VIEW . $controller . '/' . $action . '.php';
        $content = ob_get_contents();
        ob_end_clean();

        // Gán nội dung vào danh sách view đã load
        $this->content = $content;
        return $this;
	}
	
	public function getLayout() {}

	public function render() 
	{
		include APP_VIEW . "layouts/layout.php";
	}
}