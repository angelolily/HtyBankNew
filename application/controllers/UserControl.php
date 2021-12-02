<?php


class UserControl extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('encryption');
		$this->load->helper('tool');
		$this->load->model('Sys_Model');
		$this->load->service('User');

	}

	//用户登陆
	public function userlogin()
	{

		$statue=0;
		$receiveArr = file_get_contents('php://input');
		$resulArr=[];
		if ($receiveArr) {

			$dataArr = json_decode($receiveArr, true);
			//判断接口中必传参数是否完整
			if (array_key_exists("Mobile", $dataArr) && array_key_exists("UserPassword", $dataArr)) {

				try{
					$userDataArr=$this->Sys_Model->table_seleRow('Userid,c_prolx,c_dkbank,c_kdfb,UserStatus,UserName,Mobile,UserPassword,UserRol,Sex,IsAdmin,DeptId,UserPost',
						                                       'base_user',array('Mobile'=>$dataArr['Mobile']));
					if(count($userDataArr)>0){
						if($userDataArr[0]['UserStatus']==1)
						{

							$pwd=$this->encryption->decrypt($userDataArr[0]['UserPassword']);

							if($dataArr['UserPassword']==$pwd) {
								$resulArr['code']="L0000";
								$resulArr['success'] = true;
								$resulArr['msg'] = "登陆成功";
								$resulArr['data'] = [
									'UserName'=>$userDataArr[0]['UserName'],
									'Userid'=>$userDataArr[0]['Userid'],
									'Mobile'=>$userDataArr[0]['Mobile'],
									'UserRol'=>$userDataArr[0]['UserRol'],
									'Sex'=>$userDataArr[0]['Sex'],
									'IsAdmin'=>$userDataArr[0]['IsAdmin'],
									'DeptId'=>$userDataArr[0]['DeptId'],
									'UserPost'=>$userDataArr[0]['UserPost'],
									'c_prolx'=>$userDataArr[0]['c_prolx'],
									'c_dkbank'=>$userDataArr[0]['c_dkbank'],
									'c_kdfb'=>$userDataArr[0]['c_kdfb']
								];
								$statue=200;

							}
							else
							{
								$resulArr['code']="L0003";
								$resulArr['success'] = false;
								$resulArr['msg'] = "密码错误";
								$resulArr['data'] = "";
								$statue=200;


							}

						}
						else
						{
							$resulArr['code']="L0001";
							$resulArr['success'] = false;
							$resulArr['msg'] = "登陆失败，用户已被禁用";
							$resulArr['data'] = "";
							$statue=200;


						}

					}
					else{
						$resulArr['code']="L0002";
						$resulArr['success'] = false;
						$resulArr['msg'] = "用户不存在";
						$resulArr['data'] = "";
						$statue=200;

					}


				}catch (Exception $e){

					log_message('error',$e->getMessage());
				}

			}
			else
			{
				$resulArr['code']="L0003";
				$resulArr['success'] = false;
				$resulArr['msg'] = "参数获取失败";
				$resulArr['data'] = "";
				$statue=200;
			}



		}

		http_data($statue,$resulArr,$this);


	}
	//添加用户
	public function newUserRow()
	{

		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$userData=$DataArr['create_by'];
//		$DataArr = bykey_reitem($DataArr, 'timestamp');
//		$DataArr = bykey_reitem($DataArr, 'signature');
		$DataArr = bykey_reitem($DataArr, 'create_by');

		$keys="username,Mobile,UserEmail,c_prolx,c_dkbank,c_kdfb,TemplateTitle";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{

			$result = $this->user->addData($DataArr, $userData);
			if ($result['isOk']) {
				$resulArr = build_resulArr('U000', true, $result['msg'], []);
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('U002', false, '插入失败', []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('P001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}

	}
	public function updateUserInfo()
	{
		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$userData=$DataArr['update_by'];
//		$DataArr = bykey_reitem($DataArr, 'timestamp');
//		$DataArr = bykey_reitem($DataArr, 'signature');
		$DataArr = bykey_reitem($DataArr, 'update_by');
		$keys="username,Sex,UserEmail,c_prolx,c_dkbank,c_kdfb,TemplateTitle";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{
			$result = $this->user->SimpleModifyUser($DataArr, $userData);
			if ($result['isOk']) {
				$resulArr = build_resulArr('U000', true, $result['msg'], []);
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('U002', false, $result['msg'], []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('P001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}

	}
	public function enableUser()
	{
		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$userData=$DataArr['update_by'];
//		$DataArr = bykey_reitem($DataArr, 'timestamp');
//		$DataArr = bykey_reitem($DataArr, 'signature');
		$DataArr = bykey_reitem($DataArr, 'update_by');
		$keys="UserStatus,Userid";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{
			$result = $this->user->SimpleModifyUser($DataArr, $userData);
			if ($result['isOk']) {
				$resulArr = build_resulArr('U000', true, $result['msg'], []);
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('U002', false, $result['msg'], []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('P001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}

	}
	public function restUserPwd()
	{
		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$userData=$DataArr['update_by'];
//		$DataArr = bykey_reitem($DataArr, 'timestamp');
//		$DataArr = bykey_reitem($DataArr, 'signature');
		$DataArr = bykey_reitem($DataArr, 'update_by');
		$keys="Userid";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{
			$result = $this->user->resetPassword($DataArr, $userData);
			if ($result>0) {
				$resulArr = build_resulArr('U000', true, "重置成功", []);
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('U002', false, "重置失败", []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('P001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}

	}
	public function modifyUserPwd()
	{
		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$userData=$DataArr['update_by'];
		$originalPwd=$DataArr['originalPwd'];
//		$DataArr = bykey_reitem($DataArr, 'timestamp');
//		$DataArr = bykey_reitem($DataArr, 'signature');
		$DataArr = bykey_reitem($DataArr, 'update_by');
		$DataArr = bykey_reitem($DataArr, 'originalPwd');
		$keys="Userid,UserPassword";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{
			$result = $this->user->modifyPwd($DataArr,$userData,$originalPwd);
			if ($result['isOk']) {
				$resulArr = build_resulArr('U000', true, $result['msg'], []);
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('U002', false, $result['msg'], []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('P001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}

	}
	public function getUserInfo()
	{
		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$keys="username,Mobile,UserStatus,c_prolx,c_dkbank,c_kdfb";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{

			$result = $this->user->getUser($DataArr);
			if (count($result)>0) {
				$resulArr = build_resulArr('U000', true, "获取成功", $result);
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('U002', false, '获取失败', []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('P001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}
	}
	public function getBank()
	{
		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$keys="c_banktype,c_bankappend";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{

			$result = $this->user->searchBank($DataArr['c_banktype'],$DataArr['c_bankappend']);
			if (count($result)>0) {
				$resulArr = build_resulArr('U000', true, "获取成功", $result);
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('U002', false, '获取失败', []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('P001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}
	}

}
