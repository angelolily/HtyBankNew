<?php


class ChartControl extends CI_Controller
{


	function __construct()
	{
		parent::__construct();
		$this->load->service('Charts');

		$this->load->helper('tool');

	}


	public function getPiechat()
	{
		$result = $this->charts->pieChat();
		if (count($result) > 0) {
			$resulArr = build_resulArr('P000', true, '获取成功', json_encode($result,true));
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('P004', false, '获取失败', []);
			http_data(200, $resulArr, $this);
		}

	}


	public function getMapChart()
	{
		$result = $this->charts->mapChat();
		if (count($result) > 0) {
			$resulArr = build_resulArr('P000', true, '获取成功', json_encode($result,true));
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('P004', false, '获取失败', []);
			http_data(200, $resulArr, $this);
		}

	}


	public function getHistogram()
	{
		$result = $this->charts->Histogram();
		if (count($result) > 0) {
			$resulArr = build_resulArr('P000', true, '获取成功', json_encode($result,true));
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('P004', false, '获取失败', []);
			http_data(200, $resulArr, $this);
		}

	}


	public function Distribution()
	{
		$result = $this->charts->Distribution();
		if (count($result) > 0) {
			$resulArr = build_resulArr('P000', true, '获取成功', json_encode($result,true));
			http_data(200, $resulArr, $this);
		} else {
			$resulArr = build_resulArr('P004', false, '获取失败', []);
			http_data(200, $resulArr, $this);
		}

	}






}
