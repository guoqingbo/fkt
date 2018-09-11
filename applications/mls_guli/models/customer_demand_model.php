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
 * customer_demand_model CLASS
 *
 * 客户需求
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
//加载父类文件
load_m('Customer_demand_base_model');

class Customer_demand_model extends Customer_demand_base_model
{


  public function __construct()
  {
    parent::__construct();
  }


}
