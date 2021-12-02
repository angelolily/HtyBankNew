<?php


class Price extends HTY_service
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sys_Model');
		$this->load->helper('tool');

	}
	private static $userId ='5f1e450705681';    //配置用户keyid
	//post请求操作
	public function postcurl($url,$fileddata){

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

	//关联数组删除key
	private function bykey_reitem($arr, $key){
		if(!array_key_exists($key, $arr)){
			return $arr;
		}
		$keys = array_keys($arr);
		$index = array_search($key, $keys);
		if($index !== FALSE){
			array_splice($arr, $index, 1);
		}
		return $arr;

	}

	//模糊地址匹配
	public function getFuzzyMatching($info=[]){
		$tmpHouse1=array();
		$tmpHouse2=array();
		$housename=array();
		$assdata=array();

		if(count($info)>0){

			$LikeHousesName=$info['LikeHousesName'];
			$info['MaxSearchCount']=50;
			$so=json_encode($info);
			//先是使用，自建库
			$housename=$this->Sys_Model->table_seleRow('c_houseid as HousesInfoId,c_housename as HousesShowName','tbl_price',array(),array('c_housename'=>$LikeHousesName));

			if(count($housename)>0){

				$assdata=$housename;

			}
			//然后使用第三方渠道
			else{
				$info=$this->postcurl("http://eping.hdzxpg.cn/HDEP/interface_Hdprice/getHDFuzzyMatching",$so);
				$assdata=json_decode($info,true);
			}


			if(count($assdata)>0){
				$march="/^".$LikeHousesName.".*/";
				foreach ($assdata as $row){

					if(preg_match($march,$row['HousesShowName'])){

						array_push($tmpHouse1,$row);


					}
					else{
						array_push($tmpHouse2,$row);
					}
				}
				foreach ($tmpHouse2 as $tmprow){

					array_push($tmpHouse1,$tmprow);

				}
				$assdata=$tmpHouse1;

			}

		}

		return $assdata;

	}

	//获取询价信息
	public function getEvaluate($info=[]){

		$assdata=array();
		if (array_key_exists("CarportFloor", $info)  && array_key_exists("address", $info) && array_key_exists("cityCode", $info) && array_key_exists("HousesInfoId", $info) && array_key_exists("LegalUsage", $info) && array_key_exists("PropertyRegFullTwoYear", $info) && array_key_exists("HasLift", $info) && array_key_exists("BuildingArea", $info)) {

			//先判断是否使用自建库
			if(strpos($info['HousesInfoId'],'-')){

				$housename=$this->Sys_Model->table_seleRow('c_avg_Pricee','tbl_price',array('c_houseid'=>$info['HousesInfoId']),array());

				if(count($housename)>0){
					//判断是否有电梯
					$area=$info['BuildingArea'];
					$unit_price=$housename[0]['c_avg_Pricee'];//挂牌均价（单价）

					//4000元的调整
					if($unit_price>=40000){
						$unit_price=round($unit_price*0.8);
					}
					else{
						$unit_price=round($unit_price*0.9);
					}

					if($info['HasLift']==1){
						switch ($area){
							case $area<=60:$unit_price=round($unit_price*1.08);break;
							case ($area>60 && $area<=90):$unit_price=round($unit_price*1.05);break;
							case ($area>100 && $area<=150):$unit_price=round($unit_price*0.95);break;
							case ($area>150 && $area<=200):$unit_price=round($unit_price*0.9);break;
						}

					}
					else{
						switch ($area){
							case $area<=60:$unit_price=round($unit_price*1.1);break;
							case ($area>60 && $area<=90):$unit_price=round($unit_price*1.05);break;
							case ($area>100 && $area<=150):$unit_price=round($unit_price*0.95);break;
							case ($area>150 && $area<=200):$unit_price=round($unit_price*0.9);break;
						}

					}


					$unit_price=$unit_price*0.9;
					$total_price=round($unit_price*$area/10000);
					//税后计算
					if($info['LegalUsage']=='住宅'){
						if($info['PropertyRegFullTwoYear']==1){
							$sfxs=0.99;
						}
						else{
							$sfxs=0.933;
						}
					}
					else{
						$sfxs=1;
					}
					$assdata['EvaluateUnitPrice']=$unit_price;
					$assdata['EvaluateAllPrice']=$total_price;
					$assdata['EvaluateunitPriceAT']=round($unit_price*$sfxs);
					$assdata['EvaluateAllPriceAT']=round($unit_price*$sfxs*$area/10000);

				}

			}
			else{

				$info['appkey']=self::$userId;
				$res_tmp=$info;
				$so=json_encode($info);
				$info=$this->postcurl("http://eping.hdzxpg.cn/HDEP/interface_Hdprice/IF_getEvaluate",$so);
				$result=json_decode($info,true);
				if(count($result)>0){

					$assdata=$result['Data'];


				}

			}


		}


		return $assdata;

	}




}
