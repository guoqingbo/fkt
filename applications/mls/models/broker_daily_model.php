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
 * Broker_daily_model CLASS
 *
 * 经纪人工作日报
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          LION
 */

//加载父类文件
load_m('Broker_daily_base_model');

class Broker_daily_model extends Broker_daily_base_model
{


  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file Broker_daily_model.php */
/* Location: ./applications/mls/models/Broker_daily_model.php */
