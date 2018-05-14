<?php 

namespace App\Controller;

use Daisy\Mvc\ViewModel;

class IndexController
{
	public function indexAction() 
	{
		return new ViewModel();
	}

	public function index2Action()
	{
		echo 'Done!';
		return;
	}
}