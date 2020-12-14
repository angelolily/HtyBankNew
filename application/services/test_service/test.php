<?php


class test extends HTY_service
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('testmodel');//调用Model
	}
	public function tests($d){
		return "HGKJHKL";
	}


}
