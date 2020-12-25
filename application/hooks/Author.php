<?php


/**
 * Class Author
 * api验证授权钩子
 */
class Author
{
	private static $CI;

	function __construct()
	{
		self::$CI = &get_instance();  //获取CI对象
		self::$CI->load->helper('redis');
		self::$CI->load->helper('tool');


	}

	private static function buildsign($parameter=[])
	{
		$key=self::$CI->config->item('encryption_key');
		$signStr="";
		$signArr=[];
		reset($parameter);
		if(count($parameter)>0)
		{
			while (list($k, $v) = each($parameter))
			{
				$tmp = "$k" . "$v";
				$signStr .= $tmp;
			}

			$signStr = sha1($signStr);//全部拼接key后，得到sha1
			$signStr=strtoupper($signStr);//全部转换为大写


		}

		return $signStr;


	}



	public static function verifica()
	{
    	$ss=$_SERVER['REQUEST_METHOD'];
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
			http_data(200,[],self::$CI);
		}

		$resulArr = $dataArr = $receiveArr = [];//返回信息,解码json数据，接收信息
		$statue = 0;//http状态码
		$selfSignStr="";
		$receiveArr = file_get_contents('php://input');
		if ($receiveArr) {
			$dataArr = json_decode($receiveArr, true);
			//判断接口中必传参数是否完整
			if (array_key_exists("signature", $dataArr) && array_key_exists("timestamp", $dataArr)) {
				$nowTime = time();//获取当前时间
				$intervalTime = $nowTime - $dataArr['timestamp'];
				$signatureStr = $dataArr['signature'];
				//判断接口时间是否超时
				if ($intervalTime < 3600) {
					//隔离重放攻击
					if (!(RedisGet($signatureStr))) {
						$dataArr = bykey_reitem($dataArr, "signature");
						$selfSignStr=self::buildsign($dataArr);
						if($selfSignStr!=$signatureStr)
						{
							$statue = 401;
							$resulInfo['success'] = false;
							$resulInfo['msg'] = "签名错误";
						}
						else
						{
							RedisSet($signatureStr,"1");//验证通过记录签名值，防止重放
						}


					} else {
						$statue = 401;
						$resulInfo['success'] = false;
						$resulInfo['msg'] = "重复签名";
					}

				} else {
					$statue = 401;
					$resulInfo['success'] = false;
					$resulInfo['msg'] = "接口调用超时";

				}

			}
			else
			{
				$statue = 400;
				$resulInfo['success'] = false;
				$resulInfo['msg'] = "获取参数错误";
			}

		}
		else
		{
			$statue = 400;
			$resulInfo['success'] = false;
			$resulInfo['msg'] = "获取参数错误";
		}

		if($statue!=0)
		{
			http_data($statue,$resulInfo,self::$CI);
		}




	}




}

