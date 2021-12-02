<?php


class Charts extends HTY_service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sys_Model');
		$this->load->helper('tool');

	}





	public function pieChat()
	{

		$resultArr=[];
		//得到前六个月的月份日期
		$headMonthArr=to_six_month();
		$type1=['抵押',rand(1,60),rand(1,60),rand(1,60),rand(1,60),rand(1,60),rand(1,60)];
		$type2=['按揭',rand(1,60),rand(1,60),rand(1,60),rand(1,60),rand(1,60),rand(1,60)];
		$type3=['公积金',rand(1,60),rand(1,60),rand(1,60),rand(1,60),rand(1,60),rand(1,60)];
		array_unshift($headMonthArr,"年月");
		array_push($resultArr,$headMonthArr);
		array_push($resultArr,$type1);
		array_push($resultArr,$type2);
		array_push($resultArr,$type3);


		return $resultArr;



	}
	public function Histogram()
	{

		$resultArr=[];
		$yAxis=['type:'=>'category','data'=>to_six_month()];
		//得到前六个月的月份日期

		$resultArr['yAxis']=$yAxis;


		$resultArr['series']=[[
			"name"=>"预估单",
			"type"=>"bar",
			"stack"=>"total",
			"label"=>json_encode(["show"=>true]),
			"emphasis"=>json_encode(["focus"=>'series']),
			"data"=>[rand(1,60),rand(1,60),rand(1,60),rand(1,60),rand(1,60),rand(1,60)]
		],[
			"name"=>"正式单",
			"type"=>"bar",
			"stack"=>"total",
			"label"=>json_encode(["show"=>true]),
			"emphasis"=>json_encode(["focus"=>'series']),
			"data"=>[rand(1,60),rand(1,60),rand(1,60),rand(1,60),rand(1,60),rand(1,60)]
		]];




		return $resultArr;



	}
	public function mapChat()
	{

		$resultArr=[];

		$city=['闽清县','永泰县','闽侯县','福清市','鼓楼区','仓山区','台江区','马尾区','晋安区','罗源县','连江县','长乐区','平潭县'];


		foreach ($city as $row)
		{
			$tmp=[];
			$tmp['name']=$row;
			$tmp['value']=rand(1,200);
			array_push($resultArr,json_encode($tmp,JSON_UNESCAPED_UNICODE));
		}




		return $resultArr;



	}

	public function Distribution()
	{

		$resultArr=[];
		//得到前六个月的月份日期
		$headMonthArr=to_six_month();
		$type1=[rand(1,60),rand(1,60),rand(1,60),rand(1,60),rand(1,60),rand(1,60)];
		$type2=[rand(1,60),rand(1,60),rand(1,60),rand(1,60),rand(1,60),rand(1,60)];

		array_push($resultArr,$headMonthArr);
		array_push($resultArr,$type1);
		array_push($resultArr,$type2);



		return $resultArr;



	}












}
