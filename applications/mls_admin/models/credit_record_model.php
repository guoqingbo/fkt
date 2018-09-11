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
 * Credit_record_model CLASS
 *
 * 积分操作记录管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Credit_record_base_model");

class Credit_record_model extends Credit_record_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file Credit_record_model.php */
/* Location: ./app/models/Credit_record_model.php */
