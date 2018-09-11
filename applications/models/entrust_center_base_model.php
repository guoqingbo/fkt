<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2015
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Entrust_base_model CLASS
 *
 * 营销中心业主委托
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
class Entrust_center_base_model extends MY_Model
{

  private $entrust_sell = 'ent_sell';
  private $entrust_rent = 'ent_rent';
  private $community_tbl = 'community';  //楼盘
  private $grab_house = 'grab_house';  //抢拍表


  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 出售房源总行数
   * @param string $where 查询条件
   * @return int
   */
  public function entrust_count_by_sell($where)
  {
    $join_tbl = $this->community_tbl;
    $this->dbback_city->from($this->entrust_sell);
    $this->dbback_city->where($where);
    $this->dbback_city->join($join_tbl, $this->entrust_sell . '.comt_id = ' . $join_tbl . '.id ');
    return $this->dbback_city->count_all_results();
  }

  /**
   * 出售抢拍房源总行数
   * @param string $where 查询条件
   * @return int
   */
  public function entrust_count_by_sell_grab($where)
  {
    $join_tbl = $this->community_tbl;
    $this->dbback_city->from($this->entrust_sell);
    $this->dbback_city->where($where);
    $this->dbback_city->join($join_tbl, $this->entrust_sell . '.comt_id = ' . $join_tbl . '.id ');
    $this->dbback_city->join($this->grab_house, $this->entrust_sell . '.id = ' . $this->grab_house . '.ent_id ');
    return $this->dbback_city->count_all_results();
  }

  /**
   * 出租房源总行数
   * @param string $where 查询条件
   * @return int
   */
  public function entrust_count_by_rent($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->entrust_rent);
  }


  /**
   * 获取委托出售房源列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条委托房源记录组成的二维数组
   */
  public function get_all_entrust_by_sell($where, $start = 0, $limit = 10,
                                          $order_key = 'id', $order_by = 'DESC')
  {
    $join_tbl = $this->community_tbl;
    //排序条件
    $this->dbback_city->order_by($this->entrust_sell . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //查询条件
    $this->dbback_city->select("{$this->entrust_sell}.*,{$this->community_tbl}.dist_id,{$this->community_tbl}.streetid,{$this->community_tbl}.status AS com_status");
    $this->dbback_city->where($where);
    $this->dbback_city->join($join_tbl, $this->entrust_sell . '.comt_id = ' . $join_tbl . '.id ');
    return $this->dbback_city->get($this->entrust_sell)->result_array();
  }

  /**
   * 获取委托出售抢拍房源列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条委托房源记录组成的二维数组
   */
  public function get_all_entrust_by_sell_grab($where, $start = 0, $limit = 10,
                                               $order_key = 'id', $order_by = 'DESC')
  {
    $join_tbl = $this->community_tbl;
    //排序条件
    $this->dbback_city->order_by($this->entrust_sell . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //查询条件
    $this->dbback_city->select("{$this->entrust_sell}.*,{$this->community_tbl}.dist_id,{$this->community_tbl}.streetid,{$this->grab_house}.createtime");
    $this->dbback_city->where($where);
    $this->dbback_city->join($join_tbl, $this->entrust_sell . '.comt_id = ' . $join_tbl . '.id ');
    $this->dbback_city->join($this->grab_house, $this->entrust_sell . '.id = ' . $this->grab_house . '.ent_id ');
    //返回结果
    return $this->dbback_city->get($this->entrust_sell)->result_array();
  }

  /**
   * 获取委托出租房源列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条委托房源记录组成的二维数组
   */
  public function get_all_entrust_by_rent($where, $start = 0, $limit = 10,
                                          $order_key = 'id', $order_by = 'DESC')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get($this->entrust_rent)->result_array();
  }


  //根据房源ID获取委托出售信息
  public function get_sell_by_id($id)
  {
    $this->dbback_city->select('*');
    if ($id > 0) {
      $cond_where = "id = '" . $id . "'";
      $this->dbback_city->where($cond_where);
    }
    //查询
    $return_arr = $this->dbback_city->get($this->entrust_sell)->row_array();
    //返回结果
    return $return_arr;
  }

  //根据房源ID获取委托出租信息
  public function get_rent_by_id($id)
  {
    $this->dbback_city->select('*');
    if ($id > 0) {
      $cond_where = "id = '" . $id . "'";
      $this->dbback_city->where($cond_where);
    }
    //查询
    $return_arr = $this->dbback_city->get($this->entrust_rent)->row_array();
    //返回结果
    return $return_arr;
  }


  //更新委托出售房源
  public function update_sell_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->entrust_sell, $update_data);
    } else {
      $this->db_city->update($this->entrust_sell, $update_data);
    }
    //返回受影响行数
    return $this->db_city->affected_rows();
  }

  //更新委托出售房源
  public function update_rent_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->entrust_rent, $update_data);
    } else {
      $this->db_city->update($this->entrust_rent, $update_data);
    }
    //返回受影响行数
    return $this->db_city->affected_rows();
  }

  //获取当天更新房源
  public function get_today_total_num($tbl)
  {
    //获取当天开始和结束的时间戳
    $t = time();
    $start = mktime(0, 0, 0, date("m", $t), date("d", $t), date("Y", $t));
    $end = mktime(23, 59, 59, date("m", $t), date("d", $t), date("Y", $t));
    $this->dbback_city->where('updatetime >=', $start);
    $this->dbback_city->where('updatetime <=', $end);
    return $this->dbback_city->count_all_results($tbl);
  }

}

/* End of file entrust_base_model.php */
/* Location: ./application/models/entrust_base_model.php */
