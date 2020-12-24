<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

function build_resulArr($code,$success,$msg,$data)
{
	$resulArr['code']=$code;
	$resulArr['success'] = $success;
	$resulArr['msg'] = $msg;
	$resulArr['data'] =$data;

	return $resulArr;

}


function http_data($statue,$HttpData=[],$CI)
{
	$CI->output
		->set_header('access-control-allow-headers: Accept,Authorization,Cache-Control,Content-Type,DNT,If-Modified-Since,Keep-Alive,Origin,User-Agent,X-Mx-ReqToken,X-Requested-With')
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
