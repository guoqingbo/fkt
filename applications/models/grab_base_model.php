<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * grab_base_model CLASS
 *
 * 抢房/客源model
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Lion
 */
class Grab_base_model extends MY_Model
{

  private $_tbl = 'grab_house';
  private $ent_tbl = '';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }


  public function grap($insert_data, $grab_times, $tab)
  {
    $insert_id = $this->insert($insert_data);
    if ($insert_id) {
      $update_arr = array('grab_times' => $grab_times + 1);
      $this->ent_tbl = $tab;
      $update = $this->update_by_id($update_arr, $insert_data['ent_id']);
    }
    return $update;
  }

  public function get_list_by($id, $tab)
  {
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($tab)->row_array();
  }

  public function get_broker_times($broker_id, $type)
  {
    //获取当天开始和结束的时间戳
    $t = time();
    $start = mktime(0, 0, 0, date("m", $t), date("d", $t), date("Y", $t));
    $end = mktime(23, 59, 59, date("m", $t), date("d", $t), date("Y", $t));
    $this->dbback_city->where('broker_id', $broker_id);
    $this->dbback_city->where('type', $type);
    $this->dbback_city->where('createtime >=', $start);
    $this->dbback_city->where('createtime <=', $end);
    return $this->dbback_city->count_all_results($this->_tbl);
  }

  //根据条件委托编号、类型，获得数据
  function get_data_by_id_type($ent_id = 0, $type = 0)
  {
    if ($ent_id > 0 && $type > 0) {
      $this->dbback_city->where('ent_id', $ent_id);
      $this->dbback_city->where('type', $type);
      return $this->dbback_city->get($this->_tbl)->result_array();
    } else {
      return false;
    }
  }

  public function is_grab($broker_id, $id, $type)
  {
    $this->dbback_city->where('type', $type);
    $this->dbback_city->where('broker_id', $broker_id);
    $this->dbback_city->where('ent_id', $id);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  private function insert($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  /**
   * 更新委托表抢拍次数
   * @param array $update_data 更新的数据源数组
   * @param int $id 编号
   * @return int 成功后返回受影响的行数
   */
  private function update_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->ent_tbl, $update_data);
    } else {
      $this->db_city->update($this->ent_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }
}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

