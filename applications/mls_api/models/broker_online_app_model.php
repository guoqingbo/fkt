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
 * Broker_online_app_model CLASS
 *
 * 登录在线通信业务逻辑类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models broker_online_pc_state
 * @author          fisher
 */
load_m("Broker_online_app_base_model");

class Broker_online_app_model extends Broker_online_app_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file Broker_online_app.php */
/* Location: ./app/models/Broker_online_app.php */
