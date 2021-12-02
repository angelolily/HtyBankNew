<?php


/**
 * Class Usermanage ’用户管理类
 */
class User extends HTY_service
{
	/**
	 * Dept constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sys_Model');
		$this->load->model('Jko_Model');
		$this->load->helper('tool');
		$this->load->library('encryption');

	}
	/**
	 * 部门人数加一
	 * @param $pid
	 * @param $arr
	 * @param array $tree
	 * @return array|mixed
	 */
	public function modifyDeptTree($pid, $arr, &$tree = [])
	{
		foreach ($arr as $key => $dp) {
			if ($dp['DeptId'] == $pid) {
				$dp['DeptNum'] = $dp['DeptNum']+1;
				$tree[] = $dp;
				if($dp['ParentId']!="0"){
					$dp['DeptNum'] = $dp['DeptNum']-1;//恢复
					$c=$this->modifyDeptTree($dp['ParentId'], $arr);
					foreach ($c as $b){
						$tree[] = $b;
					}
				}
			}
		}
		return $tree;
	}

	/**
	 * 部门人数减一
	 * @param $pid
	 * @param $arr
	 * @param array $tree
	 * @return array|mixed
	 */
	public function unmodifyDeptTree($pid, $arr, &$tree = [])
	{
		foreach ($arr as $key => $dp) {
			if ($dp['DeptId'] == $pid) {
				$dp['DeptNum'] = $dp['DeptNum']-1;
				$tree[] = $dp;
				if($dp['ParentId']!="0"){
					$dp['DeptNum'] = $dp['DeptNum']+1;//恢复
					$c=$this->unmodifyDeptTree($dp['ParentId'], $arr);
					foreach ($c as $b){
						$tree[] = $b;
					}
				}
			}
		}
		return $tree;

	}

	/**
	 * Notes:新增用户数据
	 * User: angelo
	 * DateTime: 2021/1/11 14:42
	 * @param array $indData 用户信息
	 * @param $by /添加人员
	 * @return mixed
	 */
	public function addData($indData = [], $by)
	{
		$indData['CREATED_BY'] = $by;
		$indData['CREATED_TIME'] = date('Y-m-d H:i');
		$postname=$this->Sys_Model->table_seleRow('Userid',"base_user",array('Mobile'=>$indData['Mobile']), $like=array());
		if ($postname){
			$result['isOk'] = false;
			$result['msg'] = "手机号重复注册";

		}else{
			$indData['UserPassword']=$this->encryption->encrypt("123456");//加密
			$indData['UserStatus']="2";//默认状态：未验证
			$resultNum = $this->Sys_Model->table_addRow("base_user", $indData, 1);
			if($resultNum>0)
			{
				$result['isOk'] = true;
				$result['msg'] = "添加成功";
			}
			else{
				$result['isOk'] = false;
				$result['msg'] = "添加失败，请联系管理员";
			}

		}

		return $result;
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
				foreach($c as $b){

					$tree[] = $b;
				}
				$tree[] = $dp;
			}
		}
		return $tree;

	}


	public function testRole()
	{

		//1、获取用户的所有角色以及菜单





	}










	/**
	 * Notes: 获取用户信息或者刷新
	 * User: junxiong
	 * DateTime: 2021/1/11 15:04
	 * @param array $searchWhere ‘查询条件
	 * @return array|mixed
	 */
	public function getUser($searchWhere = [])
	{
		$where="1=1 ";
		$like="";
		$curr=$searchWhere['pages'];
		$limit=$searchWhere['rows'];
		$deptname=[];
		$userinfo=[];
		if($searchWhere['username']!="")
		{
			$like=" and UserName like '%{$searchWhere['username']}%'";
		}
		if($searchWhere['Mobile']!="")
		{
			$like=" and Mobile like '%{$searchWhere['Mobile']}%'";
		}
		if($searchWhere['UserStatus']!="")
		{
			$where=$where." and UserStatus='{$searchWhere['UserStatus']}'";
		}
		if($searchWhere['c_prolx']!="")
		{
			$where=$where." and c_prolx='{$searchWhere['c_prolx']}'";
		}
		if($searchWhere['c_dkbank']!="")
		{
			$where=$where." and c_dkbank='{$searchWhere['c_dkbank']}'";
		}
		if($searchWhere['c_kdfb']!="")
		{
			$where=$where." and c_kdfb='{$searchWhere['c_kdfb']}'";
		}


		$bankDataArr=$this->Jko_Model->table_seleRow('c_bankname,c_bankid','jko_bank');

		$items=$this->Sys_Model->get_userdata($curr,$limit,$where,$like);

		if(count($bankDataArr)>0)
		{
			$tmpBankAll=[];
			foreach ($bankDataArr as $bankRow)
			{

				$tmpBankAll[$bankRow['c_bankid']]=$bankRow['c_bankname'];

			}
		}



		if(count($items)>0)
		{
			foreach ($items['data'] as $row)
			{
				if(!(empty($row['c_prolx'])))
				{
					$row['c_prolx_name']=$tmpBankAll[$row['c_prolx']];
				}
				if(!(empty($row['c_dkbank'])))
				{
					$row['c_dkbank_name']=$tmpBankAll[$row['c_dkbank']];
				}
				if(!(empty($row['c_kdfb'])))
				{
					$row['c_kdfb_name']=$tmpBankAll[$row['c_kdfb']];
				}
				array_push($userinfo,$row);
			}
			$items['data']=$userinfo;
		}


 		return $items;
	}
	/**
	 * Notes: 批量删除用户数据
	 * User: angelo
	 * DateTime: 2021/1/19 11:49
	 * @param array $Userid '一个用户ID或多个
	 * @return mixed
	 */
	public function delUser($values)
	{
		if(count($values)>=1){
			$Userid = explode(',',$values['Userid']);
			$uname="'".$Userid[0]."'";
			for($i=1;$i<count($Userid);$i++){
				$uname=$uname.",'".$Userid[$i]."'";
			}
			$the_uname ="Userid in(".$uname.")";
			$del_sql = "delete from base_user where ".$the_uname;
		}
		else{
			$restulNum=[];
			return $restulNum;
		}
		$restulNum = $this->Sys_Model->execute_sql($del_sql, 2);
		$DeptId = explode(',',$values['DeptId']);
		foreach ($DeptId as $row){
			$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptNum', "base_dept", array('DelFlag'=>'1'), $like=array());
			$Minusone=$this->unmodifyDeptTree($row, $deptArr);//原部门人数减一
			if($Minusone){
				$this->Sys_Model->table_updateBatchRow('base_dept', $Minusone, 'DeptId');
			}
		}


		return $restulNum;
	}

	/**
	 * * Notes: 修改用户数据
	 * User: junxiong
	 * DateTime: 2021/1/19 10:10
	 * @param array $values
	 * @return mixed
	 */
	public function modifyUser($values,$by)
	{
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');
		$postname=$this->Sys_Model->table_seleRow('Userid',"base_user",array('Mobile'=>$values['Mobile']), $like=array());
		if ($postname){//如果存在前端传来的手机号即进行判断
			$restulNum = [];
			if(!isset($values['DeptId'])){//是否传DeptID 如果传了说明 不是修改状态的接口
				$restulNum = $this->Sys_Model->table_updateRow('base_user', $values, array('Userid' => $values['Userid']));
				return $restulNum;
			}
			$DeptId=$this->Sys_Model->table_seleRow('Userid,DeptId',"base_user",array('Userid'=>$values['Userid']), $like=array());//查找被修改的用户的部门ID
			if($postname[0]['Userid']==$values['Userid']){//若前端传来的用户ID跟查询到的用户ID一致则进行修改
				$restulNum = $this->Sys_Model->table_updateRow('base_user', $values, array('Userid' => $values['Userid']));
				if($DeptId[0]['DeptId']==$values['DeptId']){//若前端传来的部门ID与查询到的部门ID一致 则直接返回结果
					return $restulNum;
				}
				$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptNum', "base_dept", array('DelFlag'=>'1'), $like=array());
				$Minusone=$this->unmodifyDeptTree($DeptId[0]['DeptId'], $deptArr);//原部门人数减一
				if($Minusone){
					$this->Sys_Model->table_updateBatchRow('base_dept', $Minusone, 'DeptId');
				}
				$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptNum', "base_dept", array('DelFlag'=>'1'), $like=array());
				$addonetenth=$this->modifyDeptTree($values['DeptId'], $deptArr);//新部门人数加一
				if($addonetenth){
					$this->Sys_Model->table_updateBatchRow('base_dept', $addonetenth, 'DeptId');
				}
				return $restulNum;
			}
			return $restulNum;
		}else{
			$restulNum = $this->Sys_Model->table_updateRow('base_user', $values, array('Userid' => $values['Userid']));
			$DeptId=$this->Sys_Model->table_seleRow('Userid,DeptId',"base_user",array('Userid'=>$values['Userid']), $like=array());//查找被修改的用户的部门ID
			if($DeptId[0]['DeptId']==$values['DeptId']){
				return $restulNum;
			}
			$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptNum', "base_dept", array('DelFlag'=>'1'), $like=array());
			$Minusone=$this->unmodifyDeptTree($DeptId[0]['DeptId'], $deptArr);//原部门人数减一
			if($Minusone){
				$this->Sys_Model->table_updateBatchRow('base_dept', $Minusone, 'DeptId');
			}
			$deptArr = $this->Sys_Model->table_seleRow('DeptId,ParentId,DeptNum', "base_dept", array('DelFlag'=>'1'), $like=array());
			$addonetenth=$this->modifyDeptTree($values['DeptId'], $deptArr);//新部门人数加一
			if($addonetenth){
				$this->Sys_Model->table_updateBatchRow('base_dept', $addonetenth, 'DeptId');
			}
			return $restulNum;

		}
	}

	
	public function SimpleModifyUser($values,$by)
	{
		$result=[];
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');
		$restulNum = $this->Sys_Model->table_updateRow('base_user', $values, array('Userid' => $values['Userid']));
		if($restulNum>0)
		{
			$result['isOk'] = true;
			$result['msg'] = "修改成功";
		}
		else{
			$result['isOk'] = false;
			$result['msg'] = "修改失败，请联系管理员";
		}

		return $result;


	}

	/**
	 * * Notes: 重置用户密码
	 * User: junxiong
	 * DateTime: 2021/1/19 11:57
	 * @param array $values
	 * @return mixed
	 */
	public function resetPassword($values,$by){
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');
		if(count($values)>0){
			$Originalpassword="123456";
			$values['UserPassword']=$this->encryption->encrypt($Originalpassword);
			$restulNum = $this->Sys_Model->table_updateRow('base_user', $values, array('Userid' => $values['Userid']));
			return $restulNum;
		}
		$restulNum=0;
		return $restulNum;
	}

	/**
	 * * Notes: 查找银行列表
	 * User: junxiong
	 * DateTime: 2021/1/19 11:57
	 * @param array $values
	 * @return mixed
	 */
	public function searchBank($ctype,$bapp="0"){
		$where=array();
		$where['c_banktype']=$ctype;
		$where['c_bankappend']=$bapp;
		$rows=$this->Jko_Model->searcheBank($where);
		return $rows;


	}

	public function modifyPwd($values,$by,$originalPwd)
	{
		$result=[];
		$values['UPDATED_BY'] = $by;
		$values['UPDATED_TIME'] = date('Y-m-d H:i');

		$userDataArr=$this->Sys_Model->table_seleRow('UserPassword','base_user',array('Userid' => $values['Userid']));
		$pwd=$this->encryption->decrypt($userDataArr[0]['UserPassword']);

		if($originalPwd==$pwd)
		{
			$values['UserPassword']=$this->encryption->encrypt($values['UserPassword']);//加密
			$restulNum = $this->Sys_Model->table_updateRow('base_user', $values, array('Userid' => $values['Userid']));
			if($restulNum>0){
				$result['isOk'] = true;
				$result['msg'] = "修改成功";
			}
			else
			{
				$result['isOk'] = false;
				$result['msg'] = "修改失败，请联系管理员";
			}
		}
		else{
			$result['isOk'] = false;
			$result['msg'] = "原密码与输入的密码不一致";
		}

		return $result;
	}
	
	
	
}







