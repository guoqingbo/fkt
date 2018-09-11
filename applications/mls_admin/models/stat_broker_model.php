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
 * Stat_broker_model CLASS
 *
 * 经纪人数据统计
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          wang
 */
class Stat_broker_model extends MY_Model
{
  private $count_tbl = 'stat_broker';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function get_data_by_cond($where, $start = 0, $limit = 20,
                                   $order_key = 'id', $order_by = 'ASC')
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from($this->count_tbl);
    $this->dbback_city->where($where);
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  public function count_data_by_cond($where = '')
  {
    $this->dbback_city->select('count(*) as nums');
    $this->dbback_city->from($this->count_tbl);
    $this->dbback_city->where($where);
    $result = $this->dbback_city->get()->row_array();
    return $result['nums'];
  }

  function update_data($update_arr, $cond_where, $escape = TRUE)
  {

    if ($this->count_tbl == '' || empty($update_arr) || $cond_where == '') {
      return FALSE;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($this->count_tbl);

    return $this->db_city->affected_rows();
  }

}

/* End of file stat_group_publish_model.php */
/* Location: ./applications/mls_job/models/stat_group_publish_model.php */
