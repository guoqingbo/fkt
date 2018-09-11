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
 * operator_log_model CLASS
 *
 * 操作日志类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Operator_log_base_model");

class Operator_log_model extends Operator_log_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->_tbl1 = 'operator_log';
    $this->_tbl2 = 'broker_info';
    $this->_tbl3 = 'agency';
    $this->_tbl4 = 'operator_type';
  }

  //列表数据
  public function get_list_by($where, $start = 0, $limit = 20,
                              $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    $this->dbback_city->select("{$this->_tbl1}.*,{$this->_tbl2}.truename,{$this->_tbl3}.name AS agency_name,{$this->_tbl4}.name AS operator_name");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }

    $this->dbback_city->from($this->_tbl1);
    $this->dbback_city->join($this->_tbl2, "{$this->_tbl1}.broker_id = {$this->_tbl2}.broker_id");
    $this->dbback_city->join($this->_tbl3, "{$this->_tbl1}.agency_id = {$this->_tbl3}.id");
    $this->dbback_city->join($this->_tbl4, "{$this->_tbl1}.type_id = {$this->_tbl4}.id");

    //排序条件
    $this->dbback_city->order_by($this->_tbl1 . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }
}

/* End of file operator_log_model.php */
/* Location: ./app/models/operator_log_model.php */
