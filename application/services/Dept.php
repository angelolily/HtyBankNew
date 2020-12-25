<?php


/**
 * Class Dept ’部门类
 */
class Dept extends HTY_service
{
	/**
	 * Dept constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sys_Model');
		$this->load->helper('tool');

	}


	/**
	 * Notes:新增部门数据
	 * User: angelo
	 * DateTime: 2020/12/25 14:16
	 * @param array $indData 部门信息
	 * @param $by /添加人员
	 * @return mixed
	 */
	public function addData($indData=[], $by)
	{

		$indData['DeptId']=uniqid("HTY",4);//生成唯一部门ID
		$indData['CREATED_BY']=$by;
		$indData['CREATED_TIME']=date('Y-m-d H:i');

		$result=$this->Sys_Model->table_addRow("base_dept",$indData,1);


		return $result;



	}

	/**
	 * Notes: 获取部门信息
	 * User: angelo
	 * DateTime: 2020/12/25 14:16
	 * @param array $searchWhere ‘查询条件
	 * @return array|mixed
	 */
	public function getDept($searchWhere=[])
	{
		$where=[];
		$like=[];
		$resultDept=[];
		if(count($searchWhere)<0)
		{

			switch ($searchWhere)
			{
				case $searchWhere['DeptName']!='':$like['DeptName']=$searchWhere['DeptName'];break;
				case $searchWhere['Status']!='':$where['Status']=$searchWhere['Status'];break;
			}


		}

		$deptArr=$this->Sys_Model->table_seleRow('DeptId,ParentId,DeptName,OrderNum,Leader,Phone,Email',"base_dept",$where,$like);


		if(count($deptArr)>0)
		{

			$resultDept=$this->getDeptTree($deptArr);

		}


		return $resultDept;


	}

	/**
	 * Notes: 递归获取部门层级数组
	 * User: angelo
	 * DateTime: 2020/12/25 14:16
	 * @param $pid '父ID
	 * @param $arr 'tree数组
	 * @param array $tree
	 * @return array|mixed
	 */
	private function getDeptTree($pid , $arr, &$tree = [])
	{

		foreach ( $arr as $key => $dp ){
			if( $dp['ParentId'] == $pid ){
				$c= getDeptTree( $dp['DeptId'] ,$arr );
				if( $c ){
					$dp['children'] = $c;
				}
				$tree[] = $dp;
			}
		}
		return $tree;

	}

	/**
	 * Notes: 删除部门数据
	 * User: angelo
	 * DateTime: 2020/12/25 14:16
	 * @param array $deptId '部门ID
	 * @return mixed
	 */
	public function delDept($deptId=[])
	{


		$restulNum=$this->Sys_Model->table_delRow('base_dept',array('DeptId'=>$deptId['DeptId']));

		return $restulNum;

	}





}
