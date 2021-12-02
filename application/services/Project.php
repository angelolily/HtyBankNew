<?php


/**
 * Class Project
 */
class Project extends HTY_service
{

	/**
	 * Project constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sys_Model');
		$this->load->model('Jko_Model');
		$this->load->helper('tool');

	}

	/**
	 * @param $pages
	 * @param $rows
	 * @param array $val
	 * @param int $type
	 * @return mixed
	 */
	public function getProjectInfo($pages, $rows, $val=[], $type=1)
	{

		$whereData=['c_isDel'=>"0"];
		$or_wherein=[];
		$likeData=[];
		$filed="c_create_time,c_projStatue,c_projname,c_property,c_province,c_city,c_county,c_cqYear,
		        c_floor,c_projtype,c_area,c_elevator,c_projid,c_fcDataPath,c_priceDocPath,c_reportPdfPath,c_priceData
		        c_Shamount,c_price,c_Ygprice,c_Shprice,c_amount,c_Ygamount,c_YgShamount,c_YgShprice,c_rate,c_lookhouse,c_housetel,c_priceSure";

		$offset=($pages-1)*$rows;//计算偏移量

		if(count($val)>0)
		{

			if($val['c_projname']!="")
			{
				$likeData['c_projname']=$val['c_projname'];
			}
			if($val['c_property']!="")
			{
				$whereData['c_property']=$val['c_property'];
			}
			if($val['c_city']!="")
			{
				$whereData['c_city']=$val['c_city'];
			}
			if($val['c_county']!="")
			{
				$whereData['c_county']=$val['c_county'];
			}
			if($val['c_cqYear']!="")
			{
				$whereData['c_cqYear']=$val['c_cqYear'];
			}
			if($val['c_areaBd']!="" && $val['c_areaEd']!="")
			{
				$whereData['c_area >=']=$val['c_areaBd'];
				$whereData['c_area <=']=$val['c_areaEd'];
			}
			if($val['c_elevator']!="")
			{
				$whereData['c_elevator >=']=$val['c_elevator'];

			}
			if($val['c_projid']!="")
			{
				$likeData['c_projid']=$val['c_projid'];

			}
			//判断是预估查询还是正式查询
			if($type==2)
			{
				if($val['c_zshpriceBd']!="" && $val['c_zshpriceEd']!="")
				{
					$whereData['c_shprice >=']=$val['c_shpriceBd'];
					$whereData['c_shprice <=']=$val['c_shpriceEd'];
				}
				if($val['c_zshamountBd']!="" && $val['c_zshamountEd']!="")
				{
					$whereData['c_shamount >=']=$val['c_shamountBd'];
					$whereData['c_shamount >=']=$val['c_shamountEd'];
				}

				$or_wherein=['开始评估','查勘中','报告生成中','评估完成'];

			}
			else{
				if($val['c_shpriceBd']!="" && $val['c_shpriceEd']!="")
				{
					$whereData['c_YgShprice >=']=$val['c_shpriceBd'];
					$whereData['c_YgShprice <=']=$val['c_shpriceEd'];
				}
				if($val['c_shamountBd']!="" && $val['c_shamountEd']!="")
				{
					$whereData['c_YgShamount >=']=$val['c_shamountBd'];
					$whereData['c_YgShamount >=']=$val['c_shamountEd'];
				}

				$likeData['c_projStatue']='询价';
			}

			if($val['c_projtype']!="")
			{
				$whereData['c_projtype >=']=$val['c_projtype'];

			}
			if($val['c_createBd']!="" && $val['c_createEd']!="")
			{
				$whereData['c_create_time >=']=$val['c_createBd'];
				$whereData['c_create_time <=']=$val['c_createEd'];
			}


			$result['total']=count($this->Sys_Model->table_seleRow("c_autoid","projinfo",$whereData,$likeData,$or_wherein,"c_projStatue"));

			$result['data']=$this->Sys_Model->table_seleRow_limit($filed,"projinfo",$whereData,$likeData,$offset,$rows,"c_create_time","DESC",array(),$or_wherein,"c_projStatue");


			return $result;



		}
		
	}

	/**
	 * @param $url
	 * @param $fileddata
	 * @return bool|string
	 */
	public function postcurl($url, $fileddata){

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fileddata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$tmpInfo = curl_exec($ch); // 执行操作
		if (curl_errno($ch)) {
			echo 'Errno'.curl_error($ch);//捕抓异常
		}
		curl_close($ch); // 关闭CURL会话
		return $tmpInfo; // 返回数据，json格式*/

	}

	/**
	 * @param array $val
	 * @return array
	 */
	public function buildWordSign($val=[])
	{
		$docArr=[];
		$result=[];
		$simple_filename=$val['c_projid'].'_'.rand(10000,90000);
		$savepath="./public/simpledoc/".$simple_filename.'.docx';
		$te="./public/verifyte.docx";

		//获取申请人信息
		$seleval=$this->Sys_Model->table_seleRow('TemplateTitle,UserName','base_user',array('Mobile'=>$val['by']));
		if(count($seleval)>0) {

			$docArr['TemplateTitle'] = $seleval[0]['TemplateTitle'];
			$docArr['c_projname'] = $val['c_projname'];
			$docArr['c_create_time'] = $val['c_create_time'];
			$docArr['c_projid']=rand(10000,99999);//用redis做计数器
			$docArr['c_cert_number'] = "";
			$docArr['c_area'] = $val['c_area'];
			$docArr['c_property'] = $val['c_property'];
			$docArr['c_rem'] = $val['c_rem'];;
			$docArr['c_Ygprice'] = $val['c_Ygprice'];
			$docArr['c_Ygamount'] = $val['c_Ygamount'];
			$docArr['c_YgShprice'] = $val['c_YgShprice'];
			$docArr['c_YgShamount'] = $val['c_YgShamount'];
			$docArr['UserName'] = $seleval[0]['UserName'];
			setWordBuild($te, $docArr, $savepath);
			if (file_exists($savepath)) {


				//word询价报告生成后,继续生成PDF
				$pdfName=$val['c_projid'].'_'.rand(10000,90000).'.pdf';
				//路径必须使用绝对路径
				$docPath="D:\\phpStudy\\PHPTutorial\\WWW\\HtyBankNew\\public\\simpledoc\\".$simple_filename.'.docx';
				$pdfPath="D:\\phpStudy\\PHPTutorial\\WWW\\HtyBankNew\\public\\simplepdf\\".$pdfName;
				$arrReturn=docToPdf($docPath,$pdfPath);
				$pdfsuccess=$arrReturn['success'];
				$pdfmsg=$arrReturn['msg'];
				//$pdfsuccess：0，转换成功，更新pdf文件路径
				if($pdfsuccess==0){
					$result['code'] = true;
					$result['msg'] = '生成成功';
					$result['file']= $pdfName;


//					//签章
//					$js_pdfreturn=sign($pdfPath);
//					$gzInfo=json_decode($js_pdfreturn['gz'],true);//报告盖印结果
//					$sigState=$gzInfo['code'];
//					if($sigState==="0"){
//						$result['code'] = true;
//						$result['msg'] = '生成成功';
//						$result['file']= $pdfName;
//
//					}
//					else{
//						$info=explode(',',$gzInfo['message']);//获得返回信息
//						$signinfo=$info;
//						$signinfo=json_encode($signinfo,JSON_UNESCAPED_UNICODE);
//						$result['code'] = false;
//						$result['msg'] = '生成报告失败'.$signinfo;
//						$result['file']= '';
//						log_message('error', '报告签章失败，失败原因'.$signinfo);
//					}

				}
				else {
					$result['code'] = false;
					$result['msg'] = 'PDF转换失败';
					$result['file'] = '';
				}

			} else {
				$result['code'] = false;
				$result['msg'] = '生成报告失败';
				$result['file']= '';


			}

		}
		else
		{
			$result['code'] = false;
			$result['msg'] = '申请人信息获取失败';
			$result['file']= '';

		}


		return $result;



	}

	/**
	 * @param array $val
	 * @param $by
	 * @return mixed
	 */
	public function addYgProject($val=[], $by)
	{

		$priceVal=[];

		$val['c_projid']=uniqid("HTY",4);//生成唯一ID
		$val['c_create_time']=date('Y-m-d H:i');
		$val['c_create_by']=$by;
		//判断是否需要人工询价
		if($val['c_YgShprice']=="" || $val['c_priceSure']=="1")
		{
			/*****人工询价*****/


			$val['c_projStatue']="询价中";

			//根据城市名称，转换城市id
			$city_id=transfcity($val['c_city'],2);

			//查询A、B角色估价师

			$userRole=$this->Jko_Model->table_seleRow('duty_name,duty_phone,duty_type','jko_duty_user',array('duty_city'=>$city_id),array(),array(),"","duty_type","ASC");

			//生成调度单信息
			$priceVal['c_proj_name']=$val['c_projname'];
			$priceVal['c_Residential']=$val['c_projname'];
			$priceVal['c_build_Year']="";
			$priceVal['c_projtype']=1;
			$priceVal['c_elevator']=$val['c_elevator'];
			$priceVal['c_area']=$val['c_area'];
			$priceVal['c_verify_A']=$userRole[0]['duty_name'];
			$priceVal['c_verifyA_phone']=$userRole[0]['duty_phone'];
			$priceVal['c_verify_B']=$userRole[1]['duty_name'];
			$priceVal['c_verifyB_phone']=$userRole[1]['duty_phone'];
			$priceVal['c_cqYear']=$val['c_cqYear'];
			$priceVal['c_now_Floor']=$val['c_floor'];
			$priceVal['c_phone']='15259191562';

			$url="http://oa.fjspacecloud.com//JKOffice/index.php/inquiryControlPrice/addPrice_Consol";
			//添加价格调度
			$resultConrol=$this->postcurl($url,json_encode($priceVal));
			$resultConrol=json_decode($resultConrol,true);
			if($resultConrol['code']=="1000")
			{
				$val['c_projid']=$resultConrol['prid_key'];//生成唯一ID
				$result['code'] = true;
				$result['msg'] = '添加人工询价成功';
			}
			else
			{
				$val['c_projStatue']="人工询价中";//如果失败，状态变为人工询价中，由调度员进行价格补充。
				$result['code'] = false;
				$result['msg'] = '添加人工询价失败';

			}




		}
		else
		{
			/*****自动询价*****/
			$val['c_projid']=uniqid("HTY",4);//生成唯一ID
			//生成简易询价单
			$docArr=[];
			$simple_filename=$val['c_projid'].'_'.rand(10000,90000);
			$savepath="./public/simpledoc/".$simple_filename.'.docx';
			$te="./public/verifyte.docx";

			//获取申请人信息
			$seleval=$this->Sys_Model->table_seleRow('TemplateTitle,UserName','base_user',array('Mobile'=>$by));
			if(count($seleval)>0) {

				$docArr['TemplateTitle'] = $seleval[0]['TemplateTitle'];
				$docArr['c_projname'] = $val['c_projname'];
				$docArr['c_create_time'] = $val['c_create_time'];
				$docArr['c_projid']=rand(10000,99999);//用redis做计数器
				$docArr['c_cert_number'] = "";
				$docArr['c_area'] = $val['c_area'];
				$docArr['c_property'] = $val['c_property'];
				$docArr['c_rem'] = $val['c_rate'];;
				$docArr['c_Ygprice'] = $val['c_Ygprice'];
				$docArr['c_Ygamount'] = $val['c_Ygamount'];
				$docArr['c_YgShprice'] = $val['c_YgShprice'];
				$docArr['c_YgShamount'] = $val['c_YgShamount'];
				$docArr['UserName'] = $seleval[0]['UserName'];
				setWordBuild($te, $docArr, $savepath);
				if (file_exists($savepath)) {


					//word询价报告生成后,继续生成PDF
					$pdfName=$val['c_projid'].'_'.rand(10000,90000).'.pdf';
					//路径必须使用绝对路径
					$docPath="D:\\phpStudy\\PHPTutorial\\WWW\\HtyBankNew\\public\\simpledoc\\".$simple_filename.'.docx';
					$pdfPath="D:\\phpStudy\\PHPTutorial\\WWW\\HtyBankNew\\public\\simplepdf\\".$pdfName;
					$arrReturn=docToPdf($docPath,$pdfPath);
					$pdfsuccess=$arrReturn['success'];
					$pdfmsg=$arrReturn['msg'];
					//$pdfsuccess：0，转换成功，更新pdf文件路径
					if($pdfsuccess==0){
						$val['c_priceDocPath'] = $pdfName;
//						//签章
//						$js_pdfreturn=sign($pdfPath);
//						$gzInfo=json_decode($js_pdfreturn['gz'],true);//报告盖印结果
//						$sigState=$gzInfo['code'];
//						if($sigState==="0"){
//							$val['c_priceDocPath'] = $pdfName;
//						}
//						else{
//							$info=explode(',',$gzInfo['message']);//获得返回信息
//							$signinfo=$info;
//							$signinfo=json_encode($signinfo,JSON_UNESCAPED_UNICODE);
//							log_message('error', '报告签章失败，失败原因'.$signinfo);
//						}

					}

				} else {
					$result['code'] = false;
					$result['msg'] = '生成报告失败';
					return $result;

				}

			}

			$val['c_projStatue']="询价完成";


		}


		$result=$this->Sys_Model->table_addRow("projinfo",$val,1);
		

		
		return $result;
		
	}

	/**
	 * 更新预估项目
	 * @param array $val
	 * @param $c_projid 项目编号
	 * @param $by 更新提交人
	 * @return int
	 */
	public function updateYgProject($val=[], $c_projid, $by)
	{
		//生成简易询价单
		$val['c_update_by']=$by;
		$val['c_update_time']=date('Y-m-d H:i');

		$result=$this->Sys_Model->table_updateRow("projinfo",$val,array('c_projid'=>$c_projid));
		if($result>0)
		{
			$docArr=[];
			$simple_filename=$val['c_projid'].'_'.rand(10000,90000);
			$savepath="./public/simpledoc/".$simple_filename.'.docx';
			$te="./public/verifyte.docx";
			//获取申请人信息
			$seleval=$this->Sys_Model->table_seleRow('TemplateTitle,UserName','base_user',array('Mobile'=>$by));
			if(count($seleval)>0) {
				$docArr['TemplateTitle'] = $seleval[0]['TemplateTitle'];
				$docArr['c_projname'] = $val['c_projname'];
				$docArr['c_update_time'] = $val['c_update_time'];
				$docArr['c_cert_number'] = "";
				$docArr['c_area'] = $val['c_area'];
				$docArr['c_property'] = $val['c_property'];
				$docArr['c_rem'] = $val['c_rate'];;
				$docArr['c_Ygprice'] = $val['c_Ygprice'];
				$docArr['c_Ygamount'] = $val['c_Ygamount'];
				$docArr['c_YgShprice'] = $val['c_YgShprice'];
				$docArr['c_YgShamount'] = $val['c_YgShamount'];
				$docArr['UserName'] = $seleval[0]['UserName'];
				setWordBuild($te, $docArr, $savepath);
				if (file_exists($savepath)) {

					$val['c_priceDocPath'] = $simple_filename;

				} else {

					return -1;

				}

			}

		}
		else{
			return -2;
		}

		return $result;
		
	}

	/**
	 * 删除预估项目
	 * @param $c_projid 项目编号
	 * @return mixed
	 */
	public function deleteYgProject($c_projid)
	{
		$result=$this->Sys_Model->table_updateRow("projinfo",array('c_isDel'=>"1"),array('c_projid'=>$c_projid));

		return $result;

	}

	/**
	 * 生成正式报告
	 * @param array $val 正式报告的数据
	 * @param int $type 1：直接新增正式报告，2：预估单转正式报告
	 * @return mixed
	 */
	public function formalReport($val=[], $type=1)
	{

		$project=[];//调度项目数据
		$timeline=[];//时间轴数据
		$pval['c_projStatue']="开始评估";

		if($type==2) //直接新增正式报告
		{
			$val['c_projid']=uniqid("HTY",4);//生成唯一部门ID
			$pval['c_projid']=$val['c_projid'];
			$pval['c_lookhouse']=$val['c_lookhouse'];
			$pval['c_projname']=$val['c_projname'];
			$pval['c_projType']=$val['c_projType'];
			$pval['c_create_time']=date('Y-m-d h:i');
			$pval['c_create_by']=$val['Mobile'];
			$timeline['c_projid']=$val['c_projid'];

			$result=$this->Sys_Model->table_addRow("projinfo",$pval);

		}
		else
		{
			$result=$this->Sys_Model->table_updateRow("projinfo",$pval,array('c_projid'=>$val['c_projid']));
		}




		if($result>0)
		{


			//插入调度

//			$project['c_projid']=$val['c_projid'];
//			$project['c_zcdate']=date('Y-m-d h:i');
//			$project['c_projname']=$val['c_projname'];
//			$project['c_lookhouse']=$val['c_lookhouse'];
//			$project['c_housetel']=$val['c_housetel'];
//			$project['c_projmd']=$val['c_projType'];
//			$project['c_projstate']="业务立项";
//			$project['c_kdjl']=$val['username'];
//			$project['c_kdjltel']=$val['Mobile'];
//			$project['c_prolx']=$val['c_prolx'];
//			$project['c_dkbank']=$val['c_dkbank'];
//			$project['c_kdfb']=$val['c_kdfb'];
//			$project['c_rem']=$val['c_rate'];
//			$project['c_fcz']=$val['c_fcDataPath'];
//			$project['c_property']=$val['c_property'];
//			$project['c_yscharge']=1;//银行系统转入的正式报告标识符
//			$project['c_control_id']="13850156038";


			//漳州测试

			$project['c_projid']=$val['c_projid'];
			$project['c_zcdate']=date('Y-m-d h:i');
			$project['c_projname']=$val['c_projname'];
			$project['c_lookhouse']=$val['c_lookhouse'];
			$project['c_housetel']=$val['c_housetel'];
			$project['c_projmd']=$val['c_projType'];
			$project['c_projstate']="业务立项";
			$project['c_kdjl']=$val['username'];
			$project['c_kdjltel']=$val['Mobile'];
			$project['c_prolx']=$val['c_prolx'];
			$project['c_dkbank']=$val['c_dkbank'];
			$project['c_kdfb']=$val['c_kdfb'];
			$project['c_rem']=$val['c_rate'];
			$project['c_fcz']=$val['c_fcDataPath'];
			$project['c_property']=$val['c_property'];
			$project['c_yscharge']=1;//银行系统转入的正式报告标识符
			$project['c_jgID']="zz";//银行系统转入的正式报告标识符
			$project['c_ywemp']="13959636344";

			$result=$this->Jko_Model->table_addRow("jko_projinfotb",$project);
			if($result>0)
			{
				$timeline['c_title']="开始评估";
				$timeline['c_operatTime']=date('Y-m-d h:i');
				$timeline['create_by']=$val['username'];
				$timeline['create_time']=date('Y-m-d h:i');
				$timeline['c_projid']=$val['c_projid'];
				$resultTime=$this->Sys_Model->table_addRow("Timestamp",$timeline);
			}
		}


		return $result;
		
	}

	/**
	 * 获取时间轴信息
	 * @param $projid 项目ID
	 * @return mixed
	 */
	public function Timeline($projid)
	{

		$sqlSel="select c_operatTime,c_title,create_by  from Timestamp where c_projid='".$projid."' order by c_operatTime DESC";
		$seleval=$this->Sys_Model->execute_sql($sqlSel);
		return $seleval;

	}


	/**
	 * 生成预估报告
	 * @param $val 参数值
	 * @return mixed
	 */
	public function buildYgReport($val=[]){

		$simple_filename=$val['project_id'].'_'.rand(10000,90000);
		$savepath="./public/simpledoc/".$simple_filename.'.docx';
		$te="./public/verifyte.docx";

		//来自于项目主表信息
		$docArr['TemplateTitle'] = $val['DeptName'];//询价委托人
		$docArr['c_rem'] = $val['project_rate'];//备注
		$docArr['UserName'] = $val['DeptName'];

		switch ($val['project_user_type']){
			case "住宅":
				$docArr['c_property'] = $val['property_pepole'];//权利人
				$docArr['c_projname'] = $val['project_address']; //座 落
				$docArr['c_create_time'] = date('Y-m-d H:i');;//询价时点
				$docArr['c_projid'] = time();;//编号
				$docArr['c_area'] = $val['build_area'];//建筑面积（㎡）
				$docArr['c_Ygprice'] = $val['forecast_price'];
				$docArr['c_Ygamount'] = $val['forecast_total_price'];
				break;


		}


		setWordBuild($te, $docArr, $savepath);

		if (file_exists($savepath)) {

			$return_path="http://oa.fjspacecloud.com/HtyBankNew".substr($savepath,1);

			return $return_path;

		} else {

			return "";

		}






	}





}
