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
	public function addData($indData = [], $by)
	{

		$indData['DeptId'] = uniqid("HTY", 4);//生成唯一部门ID
		$indData['CREATED_BY'] = $by;
		$indData['CREATED_TIME'] = date('Y-m-d H:i');

		$result = $this->Sys_Model->table_addRow("base_dept", $indData, 1);


		return $result;


	}

	/**
	 * Notes: 获取部门信息
	 * User: angelo
	 * DateTime: 2020/12/25 14:16
	 * @param array $searchWhere ‘查询条件
	 * @return array|mixed
	 */
	public function getDept($searchWhere = [])
	{
		$where = [];
		$like = [];
		$resultDept = [];
		if (count($searchWhere) > 0) {

			if($searchWhere['DeptName'] != '')
			{
				$like['DeptName'] = $searchWhere['DeptName'];
					}
			if($searchWhere['Status'] != ''){
					$where['Status'] = $searchWhere['Status'];
//				case $searchWhere['DelFlag'] != '1':
//					$where['DelFlag'] = $searchWhere['DelFlag'];
			}


		}
		$where['DelFlag']='1';
		$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptName,DeptNum,Leader,Phone,Email,Status,DelFlag,Display,DeptIcon', "base_dept", $where, $like);


		if (count($deptArr) > 0) {
//			if ($searchWhere['type']==0){
			$resultDept = $this->getDeptTree('0', $deptArr);
			if (count($resultDept)==0){
				return $deptArr;
			}else{
				return $resultDept;
			}
//				$resultDept=$this->getDeptTree('0',$deptArr);
//			}else{
//				$deptAll=[];
//				foreach ($deptArr as $item) {
//					unset($item['OrderNum']);
//					unset($item['Leader']);
//					unset($item['Phone']);
//					unset($item['Email']);
//					unset($item['Status']);
//					array_push($deptAll,$item);
//
//				}
//				$resultDept=$this->getDeptTree('0',$deptAll);
//				$deptAll_top=[];
//				$deptAll_under=[];
//				foreach ($resultDept as $item) {
//					unset($item['ParentId']);
//					foreach ($item['children'] as $items) {
//						unset($items['ParentId']);
//						array_push($deptAll_top,$items);
//
//					}
//					unset($item['children']);
//					$item['children']=$deptAll_top;
//					array_push($deptAll_under,$item);


		}else{
			return $deptArr;
		}
//				$deptAll_under=[];
//				foreach ($deptAll_top['children'] as $item) {
//					unset($item['ParentId']);
//					array_push($deptAll_under,$item);
//
//				}
//				$resultDept=$deptAll_under;
//
//			}
//
//
//
//		}





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
	public function getDeptTree($pid , $arr, &$tree = [])
	{

		foreach ( $arr as $key => $dp ){
			if( $dp['ParentId'] == $pid ){
				$c= $this->getDeptTree( $dp['DeptId'] ,$arr );
				if( $c ){

					$dp['children'] = $c;
				}
				$tree[] = $dp;
			}
		}
		return $tree;

	}
//	public function getDeptTree($pid, $arr, &$tree = [])
//	{
//		foreach ($arr as $key => $dp) {
//			if ($dp['ParentId'] == $pid) {
//				$c=$this->getDeptTree($dp['DeptId'], $arr);
//				if($c){
//					$dp['chirdren']=$c;
//				}
//				$a['chirdren']=$dp;
//			}
//			if($dp['DeptId']==$pid){
//				$a['DeptId']=$dp['DeptId'];
//				$a['ParentId']=$dp['ParentId'];
//			}
//
//		}
//		if($a){
//			$tree[]=$a;
//		}
//		return $tree;
//	}

	/**
	 * 停用部门
	 * @param $pid
	 * @param $arr
	 * @param array $tree
	 * @return array|mixed
	 */
	public function modifyDeptTree($pid, $arr, &$tree = [])
	{
		foreach ($arr as $key => $dp) {
			if ($dp['ParentId'] == $pid) {
				$c = $this->modifyDeptTree($dp['DeptId'], $arr);
				if ($c) {
					foreach ($c as $b){
						$b['Status'] = '1';
						$tree[]= $b;
						$dp['Status'] = '1';
					}

				}
				else{
					$dp['Status'] = '1';
				}
				$tree[] = $dp;
			}
		}
		return $tree;

	}

	/**
	 * 启用部门
	 * @param $pid
	 * @param $arr
	 * @param array $tree
	 * @return array|mixed
	 */
	public function unmodifyDeptTree($pid, $arr, &$tree = [])
	{
		foreach ($arr as $key => $dp) {
			if ($dp['ParentId'] == $pid) {
				$c = $this->unmodifyDeptTree($dp['DeptId'], $arr);
				if ($c) {
					foreach ($c as $b){
						$b['Status'] = '0';
						$tree[]= $b;
						$dp['Status'] = '0';
					}

				}
				else{
					$dp['Status'] = '0';
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
	public function delDept($deptId = [],$by)
	{

		$deptid = array('DeptId'=>$deptId['DeptId']);
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');
//		$del_sql = "delete from base_dept where DeptId ='" . $deptid . "' or ParentId ='" . $deptid . "'";
//		$restulNum = $this->Sys_Model->execute_sql($del_sql, 2);
		$values['DelFlag']='2';
		$result = $this->Sys_Model->table_updateRow('base_dept', $values, $deptid);

		return $result;

	}

	/**
	 * * Notes: 修改部门数据
	 * User: junxiong
	 * DateTime: 2020/12/25 17:00
	 * @param array $values
	 * @return mixed
	 */
	public function modifyDept($values,$by)
	{
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');
		$restulNum = $this->Sys_Model->table_updateRow('base_dept', $values, array('DeptId' => $values['DeptId']));
		return $restulNum;
	}

	/**
	 * * * Notes: 移动部门 (下拉)
	 * User: junxiong
	 * DateTime: 2020/12/25 17:29
	 * @param array $values
	 * @return mixed
	 */
	public function moveDept($values)
	{
//		$where=array('DeptId'=>$values['DeptId']);
		$deptArr = $this->Sys_Model->table_seleRow('ParentId', "base_dept", array('DeptId' => $values['DeptId']), $like = array());
		$where_parent['DeptId'] = $deptArr[0]['ParentId'];
		$deptArr_sec = $this->Sys_Model->table_seleRow('ParentId', "base_dept", $where_parent, $like = array());
		$where_parent_sec = array('Status' => '0');
		$where_parent_sec['ParentId'] = $deptArr_sec[0]['ParentId'];
		$deptArr_th = $this->Sys_Model->table_seleRow('DeptId,DeptName', "base_dept", $where_parent_sec, $like = array());
		return $deptArr_th;

	}

	/**
	 * * * Notes: 停用启用部门
	 * User: junxiong
	 * DateTime: 2020/12/29 15:08
	 * @param $values
	 * @return mixed
	 */
	public function statusDept($values,$by)
	{

		$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,Status', "base_dept", array('DelFlag'=>'1'), $like=array());
		if (count($deptArr) > 0) {
			if($values['Status']=='1'){//停用
				$resultDept = $this->modifyDeptTree($values['DeptId'], $deptArr);

			}
			else{//启用
				$is_array=$this->Sys_Model->table_seleRow('ParentId,Status', "base_dept", array('DeptId'=>$values['DeptId']), $like=array());
				$is_array=$this->Sys_Model->table_seleRow('Status', "base_dept", array('DeptId'=>$is_array[0]['ParentId']), $like=array());
				if($is_array[0]['Status']=='1'){
					$result=[];
					return $result;
				}
				$resultDept = $this->unmodifyDeptTree($values['DeptId'], $deptArr);
			}

		}
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');
		$resultDept[]=$values;
		$User=[];
		foreach ($resultDept as $item){
			$resultUser['DeptId']=$item['DeptId'];
			if($item['Status']=="0"){//判断部门是否启用
				$resultUser['UserStatus']="1";//启用部门下用户
			}else{
				$resultUser['UserStatus']="0";//停用部门下用户
			}
			array_push($User,$resultUser);
		}
		$this->Sys_Model->table_updateBatchRow('base_user', $User, 'DeptId');
		$result_dept=$this->Sys_Model->table_updateBatchRow('base_dept', $resultDept, 'DeptId');

//		if($result>0)
//		{
//			//同步禁用部门下的人员
//			if($values['Status']=='1'){//停用
//
//			}
//		}




		return $result_dept;
	}

//		$restulNum=[];
//		if ($children) {
//			$where['ParentId'] = $values['DeptId'];
//			$where['Status']='0';
//			$deptArr = $this->Sys_Model->table_seleRow('DeptId,Status', "base_dept", $where, $like = array());
//			if (count($deptArr) < 0) {
//				foreach ($deptArr as $item) {
//						$item['Status'] = '1';
////						$this->Sys_Model->table_updateRow('base_dept', $item, array('DeptId' => $item['DeptId']));
//					array_push( $resuls,$item);
//				}
//				$this->Sys_Model->table_updateBatchRow('base_dept', $resuls, $resuls['DeptId']);
//			}else{
//				$this->statusDept($deptArr, $children = ture);
//				$restulNum = $this->Sys_Model->table_updateRow('base_dept', $values, array('DeptId' => $values['DeptId']));
//				return $restulNum;
//			}
//
//
//		} else {
//
//			return $restulNum;
//		}
//
//	}
}







