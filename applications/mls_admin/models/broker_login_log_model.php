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
 * Broker_login_log_model CLASS
 *
 * 经纪人登录日志
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Broker_login_log_base_model");

class Broker_login_log_model extends Broker_login_log_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }


  //登录日志
  public function get_last_log($phone, $num = 1)
  {
    return $this->get_data(array('form_name' => 'login_log', 'select' => array('dateline', 'infofrom'), 'where' => array('phone' => $phone), 'order_by' => 'dateline', 'limit' => $num), 'dbback_city');
  }
}

/* End of file Broker_login_log_model.php */
/* Location: ./app/models/Broker_login_log_model.php */
