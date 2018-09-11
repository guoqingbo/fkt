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
 * tourism_report_model CLASS
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lalala
 */
class Tourism_report_model extends MY_model
{

  /*
   * 海外报备表
   */
  private $_tbl = 'tourism_apnt';


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
  public function count_by($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_tourism->where($where);
    }
    return $this->dbback_tourism->count_all_results($this->_tbl);
  }

  /**
   * 获取海外报备列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by($where, $start = 0, $limit = 20,
                             $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_tourism->select($this->_select_fields);
    }
    if ($where) {
      //查询条件
      $this->dbback_tourism->where($where);
    }
    //排序条件
    $this->dbback_tourism->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_tourism->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_tourism->get($this->_tbl)->result_array();
  }

  /**
   * 添加报备信息
   * @return string
   */
  public function add_info($data_info)
  {
    $this->db_tourism->insert($this->_tbl, $data_info);
    return $this->db_tourism->affected_rows() >= 1 ? $this->db_tourism->insert_id() : 0;
  }

}
