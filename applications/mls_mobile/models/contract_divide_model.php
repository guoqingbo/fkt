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
 * contract_divide_model CLASS
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Contract_divide_base_model");

class Contract_divide_model extends Contract_divide_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->_tbl1 = 'contract_divide';
  }

  //总计数据
  public function get_total($contract_id)
  {
    //查询字段
    $this->db_city->select("SUM(price) AS price_total,SUM(percent) AS percent_total");

    $this->db_city->where(array("contract_id" => $contract_id));

    $this->db_city->from($this->_tbl1);
    //返回结果
    return $this->db_city->get()->row_array();
  }
}

/* End of file contract_divide_model.php */
/* Location: ./app/models/contract_divide_model.php */
