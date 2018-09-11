<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * buy_customer_model CLASS
 *
 * 成交权证管理类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          kang
 */
load_m('Transfer_base_model');

class Transfer_model extends Transfer_base_model
{

  public function __construct()
  {
    parent::__construct();
  }
}
