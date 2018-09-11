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
 * 用户数据模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          yzt
 */
class User_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->admin_user = 'admin_user';
    $this->admin_user_group = 'admin_user_group';
    $this->admin_pages = 'admin_pages';
  }

  /**
   * 获得所有用户
   */
  public function getuser($where_cond, $offset = 0, $pagesize = 0, $where_in = array(), $database = 'dbback')
  {
    $user = $this->get_data(array('where' => $where_cond, 'where_in' => $where_in, 'form_name' => $this->admin_user, 'limit' => $offset, 'offset' => $pagesize, 'order_by' => array('uid', 'asc')), $database);
    return $user;
  }

  /**
   * 获得所有用户
   */
  public function getuser_like($where_cond, $like = array(), $like_arr = array(), $offset = 0, $pagesize = 0, $where_in = array(), $database = 'dbback')
  {
    $user = $this->get_data(array('where' => $where_cond, 'like' => $like, 'or_like_arr' => $like_arr, 'where_in' => $where_in, 'form_name' => $this->admin_user, 'limit' => $offset, 'offset' => $pagesize, 'order_by' => array('uid', 'asc')), $database);
    return $user;
  }

  /**
   * 筛选
   */
  public function get_user_where_in($where_in, $offset = 0, $pagesize = 0, $database = 'dbback')
  {
    $user = $this->get_data(array('where_in' => $where_in, 'form_name' => $this->admin_user, 'limit' => $offset, 'offset' => $pagesize, 'order_by' => array('uid', 'asc')), $database);
    return $user;
  }

  /**
   * 根据用户ID获得详情
   */
  public function getuserByid($uid = '', $database = 'dbback')
  {
    $wherecond = array('uid' => $uid);
    $userData = $this->get_data(array('form_name' => $this->admin_user, 'where' => $wherecond), $database);
    return $userData;
  }

  /**
   * 根据用户名获得判断是否存在该用户
   */
  public function getuserByname($username = '', $database = 'dbback')
  {
    $wherecond = array('username' => $username);
    $userData = $this->get_data(array('form_name' => $this->admin_user, 'where' => $wherecond), $database);
    if (is_array($userData) && !empty($userData)) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * 获取用户总数
   */
  function getusernum($where_cond = array(), $where_in = array(), $database = 'dbback')
  {
    $user = $this->get_data(array('where' => $where_cond, 'where_in' => $where_in, 'form_name' => $this->admin_user, 'select' => array('count(*) as num')), $database);
    return $user[0]['num'];
  }

  /**
   * 获取用户总数
   */
  function getusernum_like($where_cond = array(), $like = array(), $like_arr = array(), $where_in = array(), $database = 'dbback')
  {
    $user = $this->get_data(array('where' => $where_cond, 'like' => $like, 'or_like_arr' => $like_arr, 'where_in' => $where_in, 'form_name' => $this->admin_user, 'select' => array('count(*) as num')), $database);
    return $user[0]['num'];
  }

  /**
   * 添加用户
   */
  function adduser($paramlist = array(), $database = 'db')
  {
    $result = $this->add_data($paramlist, $database, $this->admin_user);
    return $result;
  }

  /**
   * 修改用户
   */
  function modifyuser($uid, $paramlist = array(), $database = 'db')
  {
    $result = $this->modify_data(array('uid' => $uid), $paramlist, $database, $this->admin_user);
    return $result;
  }

  /**
   * 删除用户
   */
  function deluser($uid = '')
  {
    $result = $this->del(array('uid' => $uid), 'db', $this->admin_user);
    return $result;
  }

  /**
   * 删除用户
   */
  function get_user_by_cityid($cityid = '')
  {
    $master = array();

    $wherecond = array('am_cityid' => $cityid);
    $arr = $this->get_data(array('form_name' => $this->admin_user, 'where' => $wherecond), 'dbback');

    if (is_full_array($arr)) {
      foreach ($arr as $value) {
        $master[$value['uid']] = array('uid' => $value['uid'], 'truename' => $value['truename']);
      }
    }

    return $master;
  }
}

/* End of file buy_match_model.php */
/* Location: ./application/models/buy_match_model.php */
