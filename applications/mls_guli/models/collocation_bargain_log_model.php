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
 * bargain_log_model CLASS
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("collocation_bargain_log_base_model");

class Collocation_bargain_log_model extends Collocation_bargain_log_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->_tbl1 = 'collocation_bargain_log';
  }

  /**
   * 批量添加成交跟进信息
   * @return string
   */
  public function add_batch_info($data_info)
  {
    $this->db_city->insert_batch($this->_tbl1, $data_info);
    return $this->db_city->affected_rows();
  }
}

/* End of file bargain_log_model.php */
/* Location: ./app/models/bargain_log_model.php */
