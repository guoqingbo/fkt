<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS-admin
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * buy_customer_info_model CLASS
 *
 * 后台客源管理求租模型类
 *
 * @package         MLS-admin
 * @subpackage      Models
 * @category        Models
 * @author          kang
 */
load_m('warrant_base_model');

class contract_model extends Warrant_base_model
{
  public function __construct()
  {
    parent::__construct();
  }
}
