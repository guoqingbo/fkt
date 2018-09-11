<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Group_publish_model
 *
 * @author 365
 */
class Group_publish_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 获取群发日志所有数据
   * group_publish_log
   */
  public function get_group_publish($where = '', $offset = 0, $pagesize = 0)
  {
    $data = $this->get_data(array('form_name' => 'group_publish_log', 'where' => $where, 'order_by' => 'id', 'limit' => $offset, 'offset' => $pagesize), 'dbback_city');
    return $data;
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_get_group_publish($where = '')
  {
    $node = $this->get_data(array('form_name' => 'group_publish_log', 'where' => $where, 'select' => array('count(*) as num')), 'dbback_city');
    return $node[0]['num'];
  }

  /**
   * 根据id 查询房源信息
   * rent_house    sell_house
   */
  public function get_house_info_byids($id = '', $type = '')
  {
    $where = array('id' => $id);
    if ($type == 1) {
      $data = $this->get_data(array('form_name' => 'sell_house', 'where' => $where), 'dbback_city');
    } else {
      $data = $this->get_data(array('form_name' => 'rent_house', 'where' => $where), 'dbback_city');
    }
    return $data;
  }

  /**
   * 根据条件查询经纪人id
   * broker_info
   */
  public function get_broker_id($phone)
  {
    $where = array('phone' => $phone);
    $data = $this->get_data(array('form_name' => 'broker_info', 'where' => $where), 'dbback_city');
    return $data;
  }

  /**
   * 根据关键字获取群发房源名称
   * community "楼盘表"
   */
  public function get_cmtinfo_by_kw($keyword, $database = 'dbback_city')
  {
    $cmt_info = array();
    $keyword = trim(strip_tags($keyword));
    if ($keyword != '') {
      //查询字段
      $select = array('id', 'cmt_name', 'dist_id', 'address');
      $where = "(`cmt_name` LIKE '%" . $keyword . "%' )";
      //查询
      $cmt_info = $this->get_data(array('form_name' => 'community', 'where' => $where, 'select' => $select, 'order_by' => 'id', 'limit' => 10), $database);
    }
    return $cmt_info;
  }
}
