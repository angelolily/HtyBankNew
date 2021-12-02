<?php


class Upload extends HTY_service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sys_Model');
		$this->load->model('Jko_Model');
		$this->load->helper('tool');

	}


	private function fczOcr($imagepath)
	{
		$body = "";
		$host = "https://ocrapi-house-cert.taobao.com";
		$path = "/ocrservice/houseCert";
		$method = "POST";
		$appcode = "0bc391ba455c43b4aa09ce0b6313782c";
		$headers = array();
		array_push($headers, "Authorization:APPCODE " . $appcode);
		//根据API的要求，定义相对应的Content-Type
		array_push($headers, "Content-Type" . ":" . "application/json; charset=UTF-8");
		$IMAGE = base64_encode(file_get_contents($imagepath));
		$bodys = "{\"img\":\".$IMAGE.\",\"url\":\"\"}";
		$url = $host . $path;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		if (1 == strpos("$" . $host, "https://")) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}
		curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);

		$T = curl_exec($curl);
		$error_str = curl_error($curl);
		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == '200') {
			$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$header = substr($T, 0, $headerSize);
			$body = substr($T, $headerSize);
		}
		return $body;


	}
	private function checek_arraykey($arr, $key)
	{

		$result_value = "";

		if (array_key_exists($key, $arr)) {

			$result_value = $arr[$key];

		}


		return $result_value;


	}


	public function uploadFile($Files,$projid,$isocr)
	{

		$errorArr=[];
		$fileArr=[];
		$fc=[];
		$fc_result=[];
		$result=[];
		$msg="";

		if($Files)
		{

			$savePath="./public/".$projid;
			$dirPath="./public/".$projid."/";//保存目录
			//判断目录是否存在，如果不存在就新建
			if(is_dir($savePath) or mkdir($savePath))
			{

			}

			//$Files 是所有上传的文件集合
			foreach ($Files as $file)
			{


				$file['name']=iconv("UTF-8","GBK",$file['name']);//文件名去中文
				$fileName=rand(1111,9999).$file['name'];
				$savePath=$dirPath."/".$fileName;
				$file_tmp = $file['tmp_name'];
				$move_result = move_uploaded_file($file_tmp, $savePath);//上传文件
				if($move_result)
				{
					array_push($fileArr,$fileName);

				}
				else{

					array_push($errorArr,$file['name']);//未上传成功的文件，进行记录
				}

			}

			//判断是否需要房产证识别
			if($isocr=="1" && count($fileArr)>0)
			{
				//接收房地产证书参数
				$so = $this->fczOcr($dirPath.$fileArr[0]);//默认对第一张进行识别
				$fc_result = json_decode($so, true);
				if (count($fc_result) > 0 && array_key_exists("data", $fc_result)) {

					$fc = array();
					$fc['c_fcnum'] = $this->checek_arraykey($fc_result['data'], '房产证号');
					$fc['c_projname'] = $this->checek_arraykey($fc_result['data'], '坐落');
					$fc['c_property'] = $this->checek_arraykey($fc_result['data'], '权利人');
					$fc['c_gyqk'] = $this->checek_arraykey($fc_result['data'], '共有情况');
					$fc['c_build_Year'] = $this->checek_arraykey($fc_result['data'], '使用期限');
					$fc['c_area'] = $this->checek_arraykey($fc_result['data'], '建筑面积');

				}
			}
		}

		$result['fileList']=arrayGbkToUtf8($fileArr);
		$result['errorList']=arrayGbkToUtf8($errorArr);
		$result['fcList']=$fc;
		$result['projid']=$projid;


		return $result;

	}




}
