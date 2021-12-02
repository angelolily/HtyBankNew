<?php


class DeptControl extends CI_Controller
{
	private $dataArr = [];//操作数据
	private $userArr = [];//用户数据

	function __construct()
	{
		parent::__construct();
		$this->load->service('Dept');
		$this->load->helper('tool');



	}



	/**
	 * Notes:前置验证，将用户信息与数据分离
	 * User: lchangelo
	 * DateTime: 2020/12/24 14:39
	 */
	private function hedVerify()
	{
		$receiveArr = file_get_contents('php://input');
		if ($receiveArr) {
			$OldDataArr = json_decode($receiveArr, true);
			if (count($OldDataArr) > 0) {
				if(array_key_exists('username',$OldDataArr)){
					$this->userArr['Mobile'] =$OldDataArr['username'];
					$OldDataArr= bykey_reitem($OldDataArr, 'username');
				}

				$this->dataArr=$OldDataArr;
//				$this->dataArr = bykey_reitem($OldDataArr, 'timestamp');
//				$this->dataArr = bykey_reitem($this->dataArr, 'signature');
			} else {
				$resulArr = build_resulArr('S002', false, '无接收', []);
				http_data(200, $resulArr, $this);
			}
		} else {
			$resulArr = build_resulArr('S002', false, '无接收', []);
			http_data(200, $resulArr, $this);

		}
	}


	/**
	 * Notes:部门新增记录
	 * User: lchangelo
	 * DateTime: 2020/12/24 14:41
	 */
	public function newRow()
	{
		$this->hedVerify();
		$resultNum = $this->dept->addData($this->dataArr, $this->userArr['Mobile']);
		if ($resultNum > 0) {
			$resulArr = build_resulArr('D000', true, '插入成功', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D002', false, '插入失败', []);
			http_data(200, $resulArr, $this);
		}


	}


	/**
	 * Notes:获取部门信息
	 * User: angelo
	 * DateTime: 2020/12/25 10:01
	 */
	public function getRow()
	{

		$this->hedVerify();
		$result = $this->dept->getDept($this->dataArr);
		if (count($result) >= 0) {
			$resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '获取失败', []);
			http_data(200, $resulArr, $this);
		}


	}


	public function delRow()
	{
		$this->hedVerify();
		$result = $this->dept->delDept($this->dataArr, $this->userArr['Mobile']);
		if (count($result) > 0) {
			$resulArr = build_resulArr('D000', true, '删除成功', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '删除失败', []);
			http_data(200, $resulArr, $this);
		}


	}
	public function modifyRow()
	{
		$this->hedVerify();
		$result = $this->dept->modifyDept($this->dataArr, $this->userArr['Mobile']);
		if (count($result) > 0) {
			$resulArr = build_resulArr('D000', true, '修改成功', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '修改失败', []);
			http_data(200, $resulArr, $this);
		}


	}
	public function moveRow()//显示下拉列表成功或者失败
	{
		$this->hedVerify();
		$result = $this->dept->moveDept($this->dataArr);
		if (count($result) > 0) {
			$resulArr = build_resulArr('D000', true, '有接收',json_encode($result));
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '无接收', []);
			http_data(200, $resulArr, $this);
		}


	}

	public function statusRow()
	{
		$this->hedVerify();
		$result = $this->dept->statusDept($this->dataArr);
		if ($result > 0) {
			$resulArr = build_resulArr('D000', true, '成功', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '失败', []);
			http_data(200, $resulArr, $this);
		}


	}

}
