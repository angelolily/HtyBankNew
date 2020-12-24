<?php


class Dept extends HTY_service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sys_Model');

	}



	public function addData($indData=[],$by)
	{

		$indData['DeptId']=uniqid("HTY",4);//生成唯一部门ID
		$indData['CREATED_BY']=$by;
		$indData['CREATED_TIME']=date('Y-m-d H:i');

		$result=$this->Sys_Model->table_addRow("base_dept",$indData,1);


		return $result;



	}







}
