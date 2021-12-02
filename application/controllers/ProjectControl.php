<?php


class ProjectControl extends CI_Controller
{
	private $dataArr = [];//操作数据
	private $userArr = [];//用户数据

	function __construct()
	{
		parent::__construct();
		$this->load->service('Project');
		$this->load->service('Price');
		$this->load->service('Upload');
		$this->load->helper('tool');

	}



	public function getFuzzyMatching()
	{
		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$keys="cityCode,LikeHousesName,MaxSearchCount";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{

			$resultNum = $this->price->getFuzzyMatching($DataArr);
			if (count($resultNum) > 0) {
				$resulArr = build_resulArr('P000', true, '查询成功', $resultNum);
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('P002', true, '无查询数据', []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('P001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}


	}

	public function getPrice()
	{
		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$val=[];
		$keys="cityCode,HousesInfoId,c_cqYear,c_elevator,c_area,c_projname";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{
			$val['HousesInfoId']=$DataArr['HousesInfoId'];
			$val['PropertyRegFullTwoYear']=$DataArr['c_cqYear'];
			$val['HasLift']=$DataArr['c_elevator'];
			$val['BuildingArea']=$DataArr['c_area'];
			$val['address']=$DataArr['c_projname'];
			$val['CarportFloor']="";
			$val['cityCode']=$DataArr['cityCode'];
			$val['LegalUsage']="住宅";
			$resultNum = $this->price->getEvaluate($val);
			if (count($resultNum) > 0) {
				$resulArr = build_resulArr('P000', true, '查询成功', $resultNum);
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('P002', true, '无查询数据', []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('P001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}


	}

	public function newRow()
	{

		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$userData=$DataArr['username'];
		$DataArr = bykey_reitem($DataArr, 'username');

		$keys="c_Ygprice,c_Ygamount,c_YgShprice,c_YgShamount,c_province,c_city,c_county,c_projname,c_priceSure,c_property,c_cqYear,c_area,c_elevator,c_floor,c_rate,c_projType";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{

			$resultNum = $this->project->addYgProject($DataArr, $userData);
			if ($resultNum > 0) {
				$resulArr = build_resulArr('P000', true, '插入成功', []);
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('P002', false, '插入失败', []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('P001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}

	}

	public function updateRow()
	{

		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$userData=$DataArr['username'];
//		$DataArr = bykey_reitem($DataArr, 'timestamp');
//		$DataArr = bykey_reitem($DataArr, 'signature');
		$DataArr = bykey_reitem($DataArr, 'username');

		$keys="c_projid,c_province,c_city,c_county,c_projname,c_property,c_cqYear,c_area,c_elevator,c_floor,c_rate,c_projType";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{
			$result = $this->project->updateYgProject($DataArr,$DataArr['c_projid'],$userData);
			if ($result > 0) {
				$resulArr = build_resulArr('P000', true, '修改成功',[] );
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('P003', false, '修改失败', []);
				http_data(200, $resulArr, $this);
			}

		}
		else {
			$resulArr = build_resulArr('P001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}


	}

	public function delRow()
	{

		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$keys="c_projid";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{
			$result = $this->project->deleteYgProject($DataArr['c_projid']);
			if (count($result) > 0) {
				$resulArr = build_resulArr('P000', true, '删除成功',[] );
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('P003', false, '删除失败', []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('M001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}



	}

	public function getRow()
	{

		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);

//		$DataArr = bykey_reitem($DataArr, 'timestamp');
//		$DataArr = bykey_reitem($DataArr, 'signature');
		$keys="pages,rows,c_projname,c_property,c_city,c_county,c_cqYear,c_cqYear,c_areaBd,c_elevator,c_projid,c_shpriceBd,c_shpriceEd,c_shamountBd,c_shamountEd,c_projtype";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{
			if(array_key_exists("type",$DataArr))
			{
				$type=2;
			}
			else
			{
				$type=1;
			}

			$result = $this->project->getProjectInfo($DataArr['pages'],$DataArr['rows'],$DataArr,$type);
			if (count($result) > 0) {
//			$strResult=strtolower(json_encode($result));
				$resulArr = build_resulArr('P000', true, '获取成功', json_encode($result,true));
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('P004', false, '获取失败', []);
				http_data(200, $resulArr, $this);
			}

		}
		else
		{
			$resulArr = build_resulArr('P005', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}





	}

	public function buildFormalReport()
	{


		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$type=$DataArr['type'];
		$DataArr = bykey_reitem($DataArr, 'type');
		$keys="c_projid,c_projname,c_lookhouse,c_housetel,username,Mobile,c_prolx,c_dkbank,c_kdfb,c_rate,c_area";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{
			$resultNum = $this->project->formalReport($DataArr,$type);
			if ($resultNum > 0) {
				$resulArr = build_resulArr('P000', true, '插入成功', []);
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('P002', false, '插入失败', []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('P001', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}

	}

	public function uploadFile()
	{

		$file=$_FILES;
		$resulArr=[];
		$isocr=$this->input->post('isocr');
		$projid=$this->input->post('c_projid');


		if(!($projid))
		{
			$projid=uniqid("HTY",4);//生成唯一部门ID
		}



		if($projid && $file)
		{

			$resulData=$this->upload->uploadFile($file,$projid,$isocr);

			$resulArr = build_resulArr('P000', true, '上传成功', $resulData);
			http_data(200, $resulArr, $this);


		}


	}

	public function getTimeline(){
		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$keys="c_projid";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{
			$result = $this->project->Timeline($DataArr['c_projid']);
			if (count($result) > 0) {
				$resulArr = build_resulArr('P000', true, '获取成功', json_encode($result,true));
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('P004', false, '获取失败', []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('P005', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}




	}


	//获取预估报告
	public function buildYgReport()
	{
		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$keys="project_id";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{
			$result = $this->project->buildYgReport($DataArr);
			if ($result != "") {
				$resulArr = build_resulArr('YG000', true, '预估报告成功', json_encode($result,true));
				http_data(200, $resulArr, $this);
			} else {
				$resulArr = build_resulArr('YG004', false, '预估报告生成失败', []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('P005', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}

	}



	public function buildSimpleDoc()
	{
		$receiveArr = file_get_contents('php://input');
		$DataArr = json_decode($receiveArr, true);
		$keys="c_projname,c_create_time,c_area,c_property,c_rem,c_Ygprice,c_Ygamount,c_YgShprice,c_YgShamount,by";
		$errorKey=existsArrayKey($keys,$DataArr);
		if($errorKey=="")
		{
			$result = $this->project->buildWordSign($DataArr);
			if (count($result) > 0) {
				if($result['code'])
				{
					$resulArr = build_resulArr('P000', true, '获取成功', $result['file']);
					http_data(200, $resulArr, $this);
				}
				else
				{
					$resulArr = build_resulArr('P000', true, '获取失败', $result['msg']);
					http_data(200, $resulArr, $this);

				}

			} else {
				$resulArr = build_resulArr('P004', false, '获取失败', []);
				http_data(200, $resulArr, $this);
			}
		}
		else
		{
			$resulArr = build_resulArr('P005', false, $errorKey.'这些参数未传', []);
			http_data(200, $resulArr, $this);
		}


	}





}
