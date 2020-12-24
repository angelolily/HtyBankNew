<?php
require_once 'vendor/autoload.php';
use \Firebase\JWT\JWT;
class HtyJwt extends HTY_service
{


	public function __construct()
	{
		parent::__construct();

	}


	//签发Token

	/**
	 * Notes:签发jwt——token
	 * User: lchangelo
	 * DateTime: 2020/12/21 16:09
	 * @param $data,需要报告在token中的不重要信息
	 * @return mixed 返回Token
	 */
	public function lssue($data)
	{
		$key=$this->config->item('encryption_key');
		$time = time(); //当前时间
		$token = [
			'iss' => 'HtyBase', //签发者 可选
			'iat' => $time, //签发时间
			'nbf' => $time+1, //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
			'exp' => $time+18000, //过期时间,这里设置5个小时
			'data' =>$data
		];

		return JWT::encode($token, $key); //返回Token
	}


	public function verification($jwtToken)
	{
		$errorStr="";
		$resutlArr=$arr=[];

		$key=$this->config->item('encryption_key'); //key要和签发的时候一样
		try {
			JWT::$leeway = 60;//当前时间减去60，把时间留点余地
			$decoded = JWT::decode($jwtToken, $key, ['HS256']); //HS256方式，这里要和签发的时候对应
			$arr = (array)$decoded->data;

		} catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
			$errorStr=$e->getMessage();
		}catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
			$errorStr=$e->getMessage();
		}catch(\Firebase\JWT\ExpiredException $e) {  // token过期
			$errorStr=$e->getMessage();
		}catch(Exception $e) {  //其他错误
			$errorStr=$e->getMessage();
		}

		if($errorStr=="")
		{

			$resutlArr['data']=$arr;
		}
		else
		{

			$resutlArr['data']=$errorStr;

		}


	}







}
