<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type: text/html; charset=utf-8");
/**
 * 系统基础函数文件
 *
 * @package     CodeIgniter
 * @subpackage  Helpers
 * @category    Helpers
 */


/**
 * 自动加载model类库
 * @param   string $model model类名
 * @update  2014/6/6 esf dev team
 */
//if( !function_exists('user_login') )
//{
//	function user_login($d) {
//  	$ci = & get_instance();
//  	$ci->load->model('datacenter_model');
//		$rs = $ci->datacenter_model->getadmin($d);
//		if ($rs["uid"] == '')
//			return 0; //登录名密码不对
//
//		if ($rs["status"] != "1")
//			return 2; //帐号被禁用
//
//		$_SESSION[WEB_AUTH]["uid"] = $rs["uid"];
//		$_SESSION[WEB_AUTH]["username"] = $rs["username"];
//		$_SESSION[WEB_AUTH]["truename"] = $rs["truename"];
//		$_SESSION[WEB_AUTH]["telno"] = $rs["telno"];
//		$_SESSION[WEB_AUTH]["password"] = $rs["password"];
//
//		init_user_auth($rs["purview"], $rs["role"]);//初始化用户权限
//
//		//user_login_note($d['username']); //记录登录的相关信息
//
//		return 1;
//	}
//}

/**
 * 初始化用户权限
 */
function init_user_auth($purview, $role) {
    $global_purview = array("super"=>1,"admin"=>2,"base"=>3);

	$_SESSION[WEB_AUTH]["purview_arr"] = $purview != "" ? unserialize($purview) : array();
	$_SESSION[WEB_AUTH]["open_citys"] = array();
    //角色加载默认权限
	if( isset($global_purview) && $global_purview[$role] != "")
	{
		$_SESSION[WEB_AUTH]["purview_arr"] = array_merge(array($role=>$global_purview[$role]), $_SESSION[WEB_AUTH]["purview_arr"]);
	}

	if(is_array($_SESSION[WEB_AUTH]["purview_arr"]) && !empty($_SESSION[WEB_AUTH]["purview_arr"]))
	{
		foreach($_SESSION[WEB_AUTH]["purview_arr"] as $key=>$val)
		{
			$_SESSION[WEB_AUTH]["open_citys"][] = $key;
		}
	}

	//把第一个城市保存
	$_SESSION[WEB_AUTH]["now_city"] = $_SESSION[WEB_AUTH]["open_citys"][0];
}


/**
* gotoUrl($url)
*
* @param str $url
*/
function gotoUrl($url, $msg='') {
	echo "<p>".$msg."</p>";
	echo "<script>";
	echo "setTimeout(function(){if(parent.window.location.href != ''){parent.window.location.href='".$url."';} window.location.href='".$url."';}, 1000);";
	echo "</script>";
	exit;
}

/*
 * 获取该分类下的权限
*/
function get_group_purview($groupid, $needdelcache = false)
{
    $ci = & get_instance();
    $ci->load->model('datacenter_model');
    $now_purview_arr = $ci->datacenter_model->getadmingroup($groupid);

	return $now_purview_arr;
}

/**
 * frame_menu_create($user_auth)
 *
 * 生成cp后台左侧菜单
 * @param    $page_grp
 * @update   2012-9-8 fisher
*/
function frame_menu_create($user_auth) {
	$page_menu = $auth_arr = array();
	$page_purview_arr = get_page_purview();
	if(is_array($page_purview_arr[0]) && !empty($page_purview_arr[0]))
	{
		foreach($page_purview_arr[0] as $key => $grp)
		{
			if(in_array($key, $user_auth))
			{
				$page_menu[$key]['menu'] = $grp['title'];

				if(is_array($page_purview_arr[$key]) && !empty($page_purview_arr[$key]))
				{
					foreach($page_purview_arr[$key] as $p)
					{
						if($p['fid'] == $key && in_array($p['id'], $user_auth))
						{
							$auth_arr = array(
								"title" => $p['title'],
								"path"	=> MLS_URL.'/module'.$p['path']
							);
							$page_menu[$key]['pages'][] = $auth_arr;
						}
					}
				}

			}
		}
	}

    return $page_menu;
}

/**
 * 初始化用户权限
 */
function get_page_purview($type = 0)
{
	//等于1的为普通权限读取
	$sswhere = $type == 1 ? " id > 9 " : " 1 ";
	$params = " * ";
	$memkey = WEB_AUTH."_page_purview_".$type;
	$memsec = '600';
	$delcache = false;//改为TRUE可立即触发菜单修改 2013-11-28

	$page_purview_arr = $return  = array();

	//$page_purview_arr = $dao_sys->getAll("admin_pages", $sswhere, $params, $memkey, $memsec, $delcache);
    $ci = & get_instance();
    $ci->load->model('datacenter_model');
    $page_purview_arr = $ci->datacenter_model->get_page_purview($type);

	if(is_array($page_purview_arr) && !empty($page_purview_arr))
	{
		foreach($page_purview_arr as $val)
		{
			if(isset($val['status']) && $val['status'] == 1)
			{
				$purview['id'] = $val['id'];
				$purview['fid'] = $val['fid'];
				$purview['title'] = $val['title'];
				$purview['path'] = $val['path'];

				$return[$val['fid']][$purview['id']] = $purview;
			}
		}
	}

	return $return;
}

/**
 * 清空用户权限
 */
function auth_init()
{
	GLOBAL $web_auth;

	unset($_SESSION[$web_auth]);
}

//format_str_1("a",4) 	返回 &nbsp;&nbsp;&nbsp;a
function fmt_str_1($str, $strlen_total = 2, $fill_in = "&nbsp;", $direction = "before") {
    $str = strval(trim($str));
    for ($i = strlen($str); $i < $strlen_total; $i++)
        if ($direction == "before") //确定是在前面(before)加，还是在后面(after)加 //2001.10.9修改
            $str = $fill_in . $str;
        else
            $str = $str . $fill_in;

    return $str;
}


/* End of file user_helper.php */
/* Location: ./applications/helpers/user_helper.php */
