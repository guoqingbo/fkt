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
 * Secret_follow_process_model MODEL CLASS
 *
 * 区属板块管理 控制器
 *
 * @package         MLS
 * @subpackage      MODEL
 * @category        MODEL
 * @author          yuan
 */

load_m('Secret_follow_process_base_model');

class Secret_follow_process_model extends Secret_follow_process_base_model
{
  /**
   *
   * @var string
   */
  private $_tbl = '';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    parent::set_tbl('secret_follow_process');
  }
}
