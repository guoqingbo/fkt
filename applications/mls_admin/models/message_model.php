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
 * Message_model CLASS
 *
 * 消息模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          angel_in_us
 */
load_m("message_base_model");

class Message_model extends Message_base_model
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
/* Location: ./application/mls_admin/models/message_model.php */
