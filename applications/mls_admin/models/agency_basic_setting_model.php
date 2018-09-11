<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统业务类
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

/**
 * Agency_basic_setting_model MODEL CLASS
 * 基本设置
 *
 *
 * @package         MLS
 * @subpackage      MODEL
 * @category        MODEL
 */

load_m('Agency_basic_setting_base_model');

class Agency_basic_setting_model extends Agency_basic_setting_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    parent::set_bs_tbl('agency_basic_setting');
  }
}
