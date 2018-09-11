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
 * 采集模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @date      2015-01-19
 * @author          angel_in_us
 */
load_m("Message_base_model");

class Message_model extends Message_base_model
{

  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file message_model.php */
/* Location: ./application/mls_api/models/message_model.php */
