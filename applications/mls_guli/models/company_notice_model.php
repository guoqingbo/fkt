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
 * Company_notice_model CLASS
 *
 * 公司公告类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @date            2015-01-19
 * @author          angel_in_us
 */
load_m("Company_notice_base_model");

class Company_notice_model extends Company_notice_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file message_model.php */
/* Location: ./application/mls_guli/models/message_model.php */
