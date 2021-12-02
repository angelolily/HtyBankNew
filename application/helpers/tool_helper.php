<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'vendor/autoload.php';
use PhpOffice\PhpWord\TemplateProcessor;

//关联数组删除key
function bykey_reitem($arr, $key){
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

//城市转换 $serachtype:1 传入城市id，获取城市名称,2:传入城市名称，返回城市id
function transfcity($city,$serachtype=1){
	$cityidnamd=array('350100'=>'福州市','350200'=>'厦门市','350300'=>'莆田市','350400'=>'三明市','350500'=>'泉州市','350600'=>'漳州市','350700'=>'南平市','350800'=>'龙岩市','350900'=>'宁德市');

	if($serachtype==1){
		return $cityidnamd[$city];

	}
	else
	{
		$cityidnamd=array_flip($cityidnamd);
		return $cityidnamd[$city];
	}

}

function build_resulArr($code,$success,$msg,$data)
{
	$resulArr['code']=$code;
	$resulArr['success'] = $success;
	$resulArr['msg'] = $msg;
	$resulArr['data'] =$data;

	return $resulArr;

}

function arrayGbkToUtf8($val=[])
{
	$result=[];
	foreach ($val as $row)
	{
		$row=iconv("GBK","UTF-8",$row);
		array_push($result,$row);
	}
	return $result;
}

function http_data($statue,$HttpData=[],$CI)
{
	$ss=json_encode($HttpData,JSON_UNESCAPED_UNICODE);
	$CI->output
		->set_header('access-control-allow-headers: transformrequest,Accept,Authorization,Cache-Control,Content-Type,DNT,If-Modified-Since,Keep-Alive,Origin,User-Agent,X-Mx-ReqToken,X-Requested-With')
		->set_header('access-control-allow-methods: GET, POST, PUT, DELETE, HEAD, OPTIONS')
		->set_header('access-control-allow-credentials: true')
		->set_header('access-control-allow-origin: *')
		->set_header('X-Powered-By: WAF/2.0')
		->set_status_header($statue)
		->set_content_type('application/json', 'utf-8')
		->set_output(json_encode($HttpData))
		->_display();
	exit;
}


function unmodifyDeptTree($pid, $arr, &$tree = [])
{
	foreach ($arr as $key => $dp) {
		if ($dp['DeptId'] == $pid) {
			$tree[] = $dp;
			if($dp['ParentId']!="0"){
				$c=$this->unmodifyDeptTree($dp['ParentId'], $arr);
				foreach ($c as $b){
					$tree[] = $b;
				}
			}
		}
	}
	return $tree;

}

function existsArrayKey($keys,$arr=[])
{

	$arrkeys=[];
	$errorKeys="";
	$arrkeys=explode(",",$keys);
	foreach ($arrkeys as $row)
	{
		if(!(array_key_exists($row,$arr))){

			$errorKeys.=",".$row;

		}
	}

	return $errorKeys;

}

//设置word模版
function setWord($docpath,$values,$imgkey,$newPath,$imgconfig){
	$templateProcessor = new TemplateProcessor($docpath);
	if(is_array($values) && count($values)>0){

		foreach ($values as $key=>$value){

			$templateProcessor->setValue($key,$value);

		}
	}
	$tes=$templateProcessor->setImageValue($imgkey,$imgconfig);


	$templateProcessor->saveAs($newPath);





}

function setWordBuild($docpath,$values,$newPath){
	if(is_array($values) && count($values)>0){
		$templateProcessor = new TemplateProcessor($docpath);
		foreach ($values as $key=>$value){

			$templateProcessor->setValue($key,$value);

		}
	}

	$templateProcessor->saveAs($newPath);

}

//保存base64文件
function base64_file_content($base64_image_content,$path,$name){
	//匹配出文件的格式
	if (preg_match('/^(data:\s*application\/(\w+);base64,)/', $base64_image_content, $result)){
		$type = $result[2];
		$new_file = $path;
		$new_file = $new_file.'/'.$name.".{$type}";
		if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
			return  $new_file;
		}else{
			return "";
		}
	}else{
		return "";
	}
}

//新word转换PDF
function docToPdf($filepath,$pdfpath){
	$srcfilename = $filepath;
	$destfilename =$pdfpath;
	$re=array();//返回值

	$encode = stristr(PHP_OS, 'WIN') ? 'GBK' : 'UTF-8';
	$srcfilename = iconv('UTF-8', $encode, $srcfilename);
	$destfilename= iconv('UTF-8', $encode, $destfilename);



	try {
		if(!file_exists($srcfilename)){

			$re=array('success' =>1,'msg'=>'转换PDF失败','errorMsg'=>"文件不存在");;
			return  $re;
		}

		$word = new \COM("word.application") or die("Can't start Word!");
		$word->Visible=0;
		$word->DisplayAlerts = 0;
		$word->Documents->Open($srcfilename, false, false, false, "1", "1", true);



		$word->ActiveDocument->final = false;
		$word->ActiveDocument->Saved = true;
		$word->ActiveDocument->ExportAsFixedFormat(
			$destfilename,                 // wdExportFormatPDF
			17,
			false,                      // open file after export
			0,                          // wdExportOptimizeForPrint
			3,                          // wdExportFromTo
			1,                          // begin page
			5000,                       // end page
			7,                          // wdExportDocumentWithMarkup
			true,                       // IncludeDocProps
			true,                       // KeepIRM
			1                           // WdExportCreateBookmarks
		);
		$word->ActiveDocument->Close();
		$word->Quit();
		$re=array('success' =>0,'msg'=>'转换PDF成功');
	} catch (\Exception $e) {
		$errmsg= iconv($encode,'UTF-8', $e->getMessage());
		$re=array('success' =>1,'msg'=>'转换PDF失败','errorMsg'=>$errmsg);;
		if (method_exists($word, "Quit")){
			$word->Quit();
		}

	}
	return $re;
}

//签章
function  sign($pdfpath){

	$gfcertPas="wofTLS";
	$gzFile="D:\\hty\\hty.pfx";
	$gzkeyword="公章盖章处";
	$gzImage="D:\\hty\\hty.bmp";

	$signinfo=array("orginalFile"=>array("filename"=>"12345.pdf","localPath"=>$pdfpath,"sourceType"=>"1"),
		"sealCertList"=>array(array("cert"=>array(
			"certPass"=>$gfcertPas,"localPath"=>"$gzFile","sourceType"=>"1"),
			"positionParam"=>array("keyWord"=>array("keyIndex"=>0,"keyWord"=>$gzkeyword,"leftOrRight"=>"100","pageNum"=>"0"),
				"positionType"=>1),
			"sealCertDat"=>null,"sealImg"=>array("imgType"=>"1","localPath"=>$gzImage,"sourceType"=>"1"),"sourceType"=>"1")),
		"signedFile"=>array("fileName"=>"12345.pdf","saveType"=>"1","signedFileData"=>"","signedFileUrl"=>"","signedPath"=>$pdfpath));


	$jsons=json_encode($signinfo);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8080/PDFSignServer/websign/single/sign");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsons);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json; charset=utf-8",
			"Content-Length: " . strlen($jsons))
	);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$signInfo['gz'] = curl_exec($ch); // 执行操作
	if (curl_errno($ch)) {
		echo 'Errno'.curl_error($ch);//捕抓异常
	}
	curl_close($ch); // 关闭CURL会话

	return $signInfo; // 返回数据，json格式



}

function to_six_month(){
	$today = date("Y-m-d");
	$arr = array();
	$old_time = strtotime('-6 month',strtotime($today));
	for($i = 0;$i <= 5; ++$i){
		$t = strtotime("+$i month",$old_time);
		$arr[]=date('Y-m',$t);
	}
	return $arr;
}

//post请求操作
function postcurl($url,$fileddata){

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
