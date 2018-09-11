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
 * Signatory_operate_log_model MODEL CLASS
 *
 * 操作日志管理 控制器
 *
 * @package         MLS
 * @subpackage      MODEL
 * @category        MODEL
 * @author          yuan
 */

load_m('Signatory_operate_log_base_model');

class Signatory_operate_log_model extends Signatory_operate_log_base_model
{
  /**
   * 操作日志表名称
   * @var string
   */
  private $_tbl = 'signatory_operate_log';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    parent::set_tbl('signatory_operate_log');
  }
}
