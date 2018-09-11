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
 * Basic_setting_model MODEL CLASS
 * 基本设置
 *
 *
 * @package         MLS
 * @subpackage      MODEL
 * @category        MODEL
 */

load_m('Basic_setting_base_model');

class Basic_setting_model extends Basic_setting_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    parent::set_bs_tbl('basic_setting');
  }
}
