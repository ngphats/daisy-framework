<?php 

namespace Daisy\Mvc;

class ViewModel
{
	public $content;

	public function setTemplate($callback) 
	{
        $all = explode('@', $callback);
        
        $controller = strtolower(str_replace('Controller', '', $all[0]));
        $action = $all[1];

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