<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Customer_demand_base_model extends MY_Model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }


  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by($where, $tbl)
  {
    if ($where) {
      $this->dbback_city->where($where);
    }
    $count_num = $this->dbback_city->count_all_results($tbl);
    return intval($count_num);
  }

  /**
   * 获取求购信息
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @return array 返回多条求购记录组成的二维数组
   */
  public function get_all_by($where, $tbl, $start = 0, $limit = 10)
  {
    if ($where) {
      $where = "where " . $where;
    }
    $sql = "select {$tbl}.id as id,realname,phone,larea,harea,lprice,hprice,district,room,toilet,hall,grab_times,ctime from {$tbl} 
               left join district d on d.id ={$tbl}.district_id {$where} order by {$tbl}.ctime desc limit {$start},{$limit}";
    return $this->dbback_city->query($sql)->result_array();
  }

  public function get_list_by($id, $tbl)
  {
    $sql = "select s.id as id,realname,phone,larea,harea,lprice,hprice,district,room,toilet,hall,grab_times,ctime from {$tbl} s 
                 left join district d on d.id =s.district_id where s.id = {$id}";
    $result = $this->dbback_city->query($sql)->row_array();
    if ($result['is_look'] == 1) {
      $sql = "update {$tbl} set is_look = 2 where id = {$id}";
      $this->db_city->query($sql);
    }
    return $result;
  }

  //获取当前城市所以区域
  public function get_all_district()
  {
    return $this->dbback_city->get("district")->result_array();
  }

  //获取房源类型
  public function get_all_type()
  {
    $this->dbback->where('type', 'fang100');
    $result = $this->dbback->get('house_config')->result_array();
    $data = array();
    foreach ($result as $key => $val) {
      $data[$key]["id"] = $key + 1;
      $data[$key]["name"] = $val['name'];
    }
    return $data;
  }

  public function get_all_room()
  {
    $this->dbback->where('type', 'room');
    $result = $this->dbback->get('house_config')->result_array();
    foreach ($result as $key => $val) {
      $result[$key]['id'] = $key + 1;
    }
    return $result;
  }

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
