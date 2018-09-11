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
 * house_deal_model CLASS
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("House_deal_base_model");

class House_deal_model extends House_deal_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->_tbl1 = 'house_deal';
    $this->_tbl2 = 'broker_info';
    $this->_tbl3 = 'agency';
  }

  //列表数据
  public function get_list_by($where, $start = 0, $limit = 20,
                              $order_house_deal = 'id', $order_by = 'DESC')
  {
    //查询字段
    $this->dbback_city->select("{$this->_tbl1}.*,{$this->_tbl2}.truename,{$this->_tbl3}.name AS agency_name");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }

    $this->dbback_city->from($this->_tbl1);
    $this->dbback_city->join($this->_tbl2, "{$this->_tbl1}.broker_id = {$this->_tbl2}.broker_id");
    $this->dbback_city->join($this->_tbl3, "{$this->_tbl2}.agency_id = {$this->_tbl3}.id");

    //排序条件
    $this->dbback_city->order_by($this->_tbl1 . '.' . $order_house_deal, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }
}

/* End of file house_deal_model.php */
/* Location: ./app/models/house_deal_model.php */
