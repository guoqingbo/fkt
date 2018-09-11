<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */
// ------------------------------------------------------------------------

/**
 * Sell_house_collect_model CLASS
 *
 * 二手房采集内容数据模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          yzt
 */
class Show_collect_stat_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function show_collect_stat_sum($where = '')
  {
    $this->dbback_city->select('count(*) as nums');
    $this->dbback_city->from('stat_collect');
    $this->dbback_city->where($where);
    $result = $this->dbback_city->get()->row_array();
    return $result['nums'];
  }

  public function get_show_collect_stat($where, $start = 0, $limit = 20, $order_key = 'ymd', $order_by = 'DESC')
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('stat_collect');
    $this->dbback_city->where($where);
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

}

/* End of file sell_house_collect_model.php */
/* Location: ./application/models/sell_house_collect_model.php */
