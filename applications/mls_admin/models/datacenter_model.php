<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Buy_match_model CLASS
 *
 * 会员中心数据后台
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          yzt
 */
class Datacenter_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->admin_user = 'admin_user';
    $this->admin_user_group = 'admin_user_group';
    $this->admin_pages = 'admin_pages';
    $this->load->model('city_model');
    $this->load->model('user_model');
    $this->load->model('user_group_model');
  }

  /**
   * 获取管理员用户
   * @param type $d
   * @return array
   */
  function getadmin($d)
  {
    //获得管理员
    $user = $this->get_data(array('form_name' => $this->admin_user, 'where' => array('username' => $d["username"], 'password' => md5($d["password"]))));
    //$now_city = $this->city_model->get_first_by();


    if (!empty($user[0]) && is_array($user[0])) {
      $user_data = $this->user_model->getuserByid($user[0]["uid"]);
      $city_ids_arr = array();
      $city_ids_arr_new = array();
      $user_group_id_str = $user_data[0]['user_group_ids'];//echo($user_group_id_str);
      if (!empty($user_group_id_str)) {
        $user_group_id_arr = array_filter(explode(',', $user_group_id_str));
        //根据id获得当前城市的用户组信息
        $user_group_arr = array();
        foreach ($user_group_id_arr as $k => $vo) {
          $city_ids = $this->user_group_model->get_city_id_by("id = " . $vo);
          if (!empty($city_ids)) {
            $city_ids_arr[] = $city_ids['0']['city_id'];
          }
        }
        foreach ($city_ids_arr as $k => $vo) {
          if ($this->city_model->get_by_id($vo)) {
            $city_ids_arr_new[] = $vo;
          }
        }
      }
      //当前城市
      $now_city = $this->city_model->get_by_id($city_ids_arr_new['0']);

      $_SESSION[WEB_AUTH]["uid"] = $user[0]["uid"];
      $_SESSION[WEB_AUTH]["username"] = $user[0]["username"];
      $_SESSION[WEB_AUTH]["truename"] = $user[0]["truename"];
      $_SESSION[WEB_AUTH]["telno"] = $user[0]["telno"];
      $_SESSION[WEB_AUTH]["password"] = $user[0]["password"];
      $_SESSION[WEB_AUTH]["role"] = $user[0]["role"];
      $_SESSION[WEB_AUTH]["city"] = $now_city['spell'];
      $_SESSION[WEB_AUTH]["city_id"] = $now_city['id'];
      return $user[0];
    } else {
      return 'noResult';
    }
  }

  /**
   * 获取管理员组
   * @param type $d
   * @return array
   */
  function getadmingroup($groupid)
  {
    //获得管理员
    $user = $this->get_data(array('form_name' => $this->admin_user_group, 'where' => array('id' => $groupid), 'select' => array('auth')), 'esfadmin');
    return $user[0];
  }

  /**
   * 获得左侧菜单
   * @param type $d
   * @return array
   */
  function get_page_purview($type)
  {
    //获得左侧菜单
    if ($type == 1) {
      $user = $this->get_data(array('form_name' => $this->admin_pages, 'where' => array('id >' => 9)), 'esfadmin');
    } else {
      $user = $this->get_data(array('form_name' => $this->admin_pages, 'where' => array('id' => 1)), 'esfadmin');
    }
    return $user;
  }

}

/* End of file buy_match_model.php */
/* Location: ./application/models/buy_match_model.php */
