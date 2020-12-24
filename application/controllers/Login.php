<?php


class Login extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('encryption');
		$this->load->helper('tool');
		$this->load->service('HtyJwt');
		$this->load->model('Sys_Model');

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
					$userDataArr=$this->Sys_Model->table_seleRow('UserStatus,UserName,Mobile,UserPassword,UserRol,Sex,IsAdmin,DeptId,UserPost',
						                                       'base_user',array('Mobile'=>$dataArr['Mobile']));
					if(count($userDataArr)>0){
						if($userDataArr[0]['UserStatus']==1)
						{
							$tmp=$this->encryption->encrypt("123456");
							$pwd=$this->encryption->decrypt($userDataArr[0]['UserPassword']);

							if($dataArr['UserPassword']==$pwd) {
								$jwtUserInfo=[
									'UserName'=>$userDataArr[0]['UserName'],
									'Mobile'=>$userDataArr[0]['Mobile'],
									'UserRol'=>$userDataArr[0]['UserRol'],
									'Sex'=>$userDataArr[0]['Sex'],
									'IsAdmin'=>$userDataArr[0]['IsAdmin'],
									'DeptId'=>$userDataArr[0]['DeptId'],
									'UserPost'=>$userDataArr[0]['UserPost']
								];
								$jwtStr=$this->htyjwt->lssue($jwtUserInfo);

								$resulArr['code']="L0000";
								$resulArr['success'] = true;
								$resulArr['msg'] = "登陆成功";
								$resulArr['data'] = $jwtStr;
								$statue=200;

							}

						}
						else
						{
							$resulArr['code']="L0001";
							$resulArr['success'] = false;
							$resulArr['msg'] = "登陆失败，用户已被禁用";
							$resulArr['data'] = "";
							$statue=401;


						}

					}
					else{
						$resulArr['code']="L0002";
						$resulArr['success'] = false;
						$resulArr['msg'] = "用户不存在";
						$resulArr['data'] = "";
						$statue=401;

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
				$statue=401;
			}



		}

		http_data($statue,$resulArr,$this);


	}

}
