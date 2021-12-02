<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Login';
$route['test']['put'] = 'test/ss';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['login'] = 'UserControl/userlogin';//登陆自定义路由
$route['us/new'] = 'UserControl/newUserRow';//登陆自定义路由
$route['us/upd'] = 'UserControl/updateUserInfo';//登陆自定义路由
$route['us/repwd'] = 'UserControl/restUserPwd';//登陆自定义路由
$route['us/getData'] = 'UserControl/getUserInfo';//登陆自定义路由
$route['us/enable'] = 'UserControl/enableUser';//登陆自定义路由
$route['us/getBank'] = 'UserControl/getBank';//登陆自定义路由
$route['us/mdPwd'] = 'UserControl/modifyUserPwd';//登陆自定义路由
$route['pj/ygGet'] = 'ProjectControl/getRow';//获取询价项目
$route['pj/ygAdd'] = 'ProjectControl/newRow';//刷新菜单信息
$route['pj/ygupd'] = 'ProjectControl/updateRow';//更新菜单信息
$route['pj/fuzz'] = 'ProjectControl/getFuzzyMatching';//模糊匹配项目地址
$route['pj/price'] = 'ProjectControl/getPrice';//获得价格
$route['pj/del'] = 'ProjectControl/delRow';//获得价格
$route['pj/upload'] = 'ProjectControl/uploadFile';//上传文件
$route['pj/formal'] = 'ProjectControl/buildFormalReport';//上传文件pj/ygAdd
$route['dp/new'] = 'DeptControl/newRow';//新增部门
$route['dp/get'] = 'DeptControl/getRow';//获取部门信息
$route['dp/delete'] = 'DeptControl/delRow';//删除部门信息
$route['dp/modify'] = 'DeptControl/modifyRow';//修改部门信息
$route['dp/move'] = 'DeptControl/moveRow';//显示可移动部门信息
$route['dp/status'] = 'DeptControl/statusRow';//修改部门状态
$route['chart/pie'] = 'ChartControl/getPiechat';//修改部门状态
$route['chart/map'] = 'ChartControl/getMapChart';//修改部门状态
$route['chart/Histogram'] = 'ChartControl/getHistogram';//修改部门状态
$route['chart/Distribution'] = 'ChartControl/Distribution';//修改部门状态
$route['pj/getTl'] = 'ProjectControl/getTimeline';//修改部门状态
$route['pj/bsd'] = 'ProjectControl/buildSimpleDoc';//修改部门状态
$route['pj/getYg'] = 'ProjectControl/buildYgReport';//获取预估单



