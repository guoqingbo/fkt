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
 * 用户组模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          yzt
 */
class User_group_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->user_group = 'user_group';
  }

  /**
   * 获得节点
   */
  public function get_user_group($where = array(), $offset = 0, $pagesize = 0, $database = 'dbback')
  {
    $data = $this->get_data(array('form_name' => $this->user_group, 'where' => $where, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $data;
  }

  /**
   * 根据城市id获取权限节点对应的city_id
   */
  public function get_city_id_by($where = array(), $database = 'dbback')
  {
    $data = $this->get_data(array('select' => array('city_id'), 'form_name' => $this->user_group, 'where' => $where, 'group_by' => 'city_id'), $database);
    return $data;
  }

  /**
   * 根据ID获得详情
   */
  public function get_user_group_by_id($id = '', $database = 'dbback')
  {
    $wherecond = array('id' => $id);
    $userData = $this->get_data(array('form_name' => $this->user_group, 'where' => $wherecond), $database);
    return $userData;

  }

  /**
   * 获取用户组总数
   */
  function get_user_group_num($where, $database = 'dbback')
  {
    $node = $this->get_data(array('form_name' => $this->user_group, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $node[0]['num'];
  }

  /**
   * 添加用户组
   */
  function add_user_group($paramlist = array(), $database = 'db')
  {
    $result = $this->add_data($paramlist, $database, $this->user_group);
    return $result;
  }

  /**
   * 修改用户组
   */
  function modify_user_group($id, $paramlist = array(), $database = 'db')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->user_group);
    return $result;
  }

  /**
   * 删除用户组
   */
  function del_user_group($id = '')
  {
    $result = $this->del(array('id' => $id), 'db', $this->user_group);
    return $result;
  }

  /**
   * 获取城市id
   */
  public function get_cityid($database = 'dbback')
  {
    $data = $this->get_data(array('select' => array('city_id'), 'form_name' => $this->user_group, 'group_by' => 'city_id'), $database);
    return $data;
  }

  /**
   * 根据用户组获取城市id
   */
  public function get_cityid_by_groupid($where = array(), $database = 'dbback')
  {
    $data = $this->get_data(array('select' => array('city_id'), 'form_name' => $this->user_group, 'where_in' => $where, 'group_by' => 'city_id'), $database);
    return $data;
  }

}

/* End of file buy_match_model.php */
/* Location: ./application/models/buy_match_model.php */
