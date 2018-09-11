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
 * personnel_log_see_model CLASS
 *
 * 查看日志信息类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Personnel_log_see_base_model");

class Personnel_log_see_model extends Personnel_log_see_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->_tbl1 = 'personnel_log_see';
  }

  /**
   * 批量添加查看日志信息
   * @return string
   */
  public function add_batch_info($data_info)
  {
    $this->db_city->insert_batch($this->_tbl1, $data_info);
    return $this->db_city->affected_rows();
  }
}

/* End of file personnel_log_see_model.php */
/* Location: ./app/models/personnel_log_see_model.php */
