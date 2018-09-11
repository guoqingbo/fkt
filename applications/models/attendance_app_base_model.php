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
 * attendance_app_base_model CLASS
 *
 * APP考勤
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
class Attendance_app_base_model extends MY_Model
{

  private $_tbl = 'attendance_app';


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
  public function count_by_sell($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl_apnt);
    $this->dbback_city->join($this->_tbl_sell_house, "{$this->_tbl_apnt}.house_id = {$this->_tbl_sell_house}.id");
    $this->dbback_city->join($this->_tbl_broker, "{$this->_tbl_sell_house}.broker_id = {$this->_tbl_broker}.broker_id");
    return $this->dbback_city->count_all_results();
  }


  //考勤
  public function get_list_by($where, $start = -1, $limit = 20, $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    $this->dbback_city->select("*");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl);

    //排序条件
    $this->dbback_city->order_by($this->_tbl . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    $result = $this->dbback_city->get();
    if ($result) {
    	return $result->result_array();
    }else{
    	return $result;
    }
  }


  public function get_one_by($where)
  {
    //查询字段
    $this->dbback_city->select("*");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl);

    //返回结果
    $result = $this->dbback_city->get();
    if ($result) {
    	return $result->row_array();
    }else{
    	return $result;
    }
  }

  /**
   * 获取员工信息
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条员工记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20)
  {
    //排序条件
    if ($start >= 0 && $limit > 0) {
      $where = $where . " limit " . $start . "," . $limit;
    }

    $sql = "select broker_id from broker_info " . $where;
    $result = $this->dbback_city->query($sql);
    if ($result) {
    	$result = $result->result_array();
    }else{
    	return $result;
    }
    return $result;
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by($where)
  {
    $sql = "select count(*) as number from broker_info " . $where;
    $result = $this->dbback_city->query($sql);
    if ($result) {
    	$result = $result->row_array();
    }else{
    	return $result;
    }
    return $result['number'];
  }

  /*******************************************************************************************/
}

/* End of file attendance_app_base_model.php */
/* Location: ./application/models/attendance_app_base_model.php */
