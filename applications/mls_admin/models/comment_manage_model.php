<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Comment_manage_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  private $_tbl1 = "sell_talk";

  /*
   * 获取所有二手房的评论
   * 返回的是评论数组
   */
  /**
   * 获取符合条件的信息列表
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   合作列表数组
   */
  public function get_list_by_cond($cond_where, $tbl, $offset = 0, $limit = 10, $order_key = "status", $order_by = "ASC")
  {

    //查询条件
    if ($cond_where) {
      $this->dbback_city->where($cond_where);
    }
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);

    //查询
    $arr_data = $this->dbback_city->get($tbl, $limit, $offset)->result_array();
    return $arr_data;
  }

  /**
   * 获取符合条件的房源数量
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_talk_num_by($cond_where, $tbl)
  {
    //出售名称
    if ($cond_where) {
      $this->dbback_city->where($cond_where);
    }
    $count_num = $this->dbback_city->count_all_results($tbl);
    return intval($count_num);
  }

  /*
   * 获取所有租房的评论
   * 返回的是评论数组
   */
  public function rent_house_comment()
  {
    return $this->dbback_city->get($this->_tbl1)->result_array();
  }

  //更新评论的审核情况
  public function update_status_by($id, $status, $tbl)
  {
    $this->db_city->where_in('id', $id);
    $this->db_city->update($tbl, array('status' => $status));
  }

  //删除评论
  public function del_by($id, $tbl)
  {
    $this->db_city->where_in('id', $id);
    $this->db_city->delete($tbl);
    return $this->db_city->affected_rows();
  }
}
