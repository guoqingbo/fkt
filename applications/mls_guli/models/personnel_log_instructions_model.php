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
 * Personnel_log_instructions_model CLASS
 *
 * 批示日志信息类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Personnel_log_instructions_base_model");

class Personnel_log_instructions_model extends Personnel_log_instructions_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->_tbl1 = 'personnel_log_instructions';
    $this->_tbl2 = 'signatory_info';
  }

  //列表数据
  public function get_list_by($where, $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    $this->dbback_city->select("{$this->_tbl1}.content,{$this->_tbl1}.create_time,{$this->_tbl2}.truename");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }

    $this->dbback_city->from($this->_tbl1);
    $this->dbback_city->join($this->_tbl2, "{$this->_tbl1}.signatory_id = {$this->_tbl2}.signatory_id");

    //排序条件
    $this->dbback_city->order_by($this->_tbl1 . '.' . $order_key, $order_by);

    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 批量添加批示日志信息
   * @return string
   */
  public function add_batch_info($data_info)
  {
    $this->db_city->insert_batch($this->_tbl1, $data_info);
    return $this->db_city->affected_rows();
  }
}

/* End of file Personnel_log_instructions_model.php */
/* Location: ./app/models/Personnel_log_instructions_model.php */
