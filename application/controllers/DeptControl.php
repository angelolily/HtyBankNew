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
		$this->load->service('HtyJwt');

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
				if (array_key_exists("ud", $OldDataArr)) {
					$userInfo = $this->htyjwt->verification($OldDataArr['jwt']);
					$this->userArr = $this->$userInfo['data']['data'];
					$this->dataArr = bykey_reitem($OldDataArr, 'jwt');
					$this->dataArr = bykey_reitem($OldDataArr, 'timestamp');
					$this->dataArr = bykey_reitem($OldDataArr, 'signature');
				} else {
					$resulArr = build_resulArr('S001', false, '无jwt参数', []);
					http_data(505, $resulArr, $this);

				}
			} else {
				$resulArr = build_resulArr('S002', false, '无接收', []);
				http_data(505, $resulArr, $this);
			}
		} else {
			$resulArr = build_resulArr('S002', false, '无接收', []);
			http_data(505, $resulArr, $this);

		}
	}


	/**
	 * Notes:部门新增记录
	 * User: Administrator
	 * DateTime: 2020/12/24 14:41
	 */
	public function newRow()
	{

		$this->hedVerify();

		$resultNum = $this->Dept->addData($this->dataArr, $this->userArr['Mobile']);
		if ($resultNum > 0) {
			$resulArr = build_resulArr('D000', true, '插入成功', []);
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D002', false, '插入失败', []);
			http_data(505, $resulArr, $this);
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

		$result = $this->Dept->getDept($this->dataArr);
		if (count($result) > 0) {
			$resulArr = build_resulArr('D000', true, '获取成功', json_encode($result));
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '获取失败', []);
			http_data(505, $resulArr, $this);
		}


	}


	public function delRow()
	{
		$this->hedVerify();
		$result = $this->Dept->delDept($this->dataArr);
		if (count($result) > 0) {
			$resulArr = build_resulArr('D000', true, '删除成功', );
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('D003', false, '获取失败', []);
			http_data(505, $resulArr, $this);
		}


	}


}
