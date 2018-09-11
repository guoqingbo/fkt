<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS消息提醒类
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

/**
 * Basic_setting_model MODEL CLASS
 * 消息提醒
 *
 *
 * @package         MLS
 * @subpackage      MODEL
 * @category        MODEL
 * @author          lalala
 */

load_m('Checknotice_base_model');

class Checknotice_model extends checknotice_base_model
{

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }
}
