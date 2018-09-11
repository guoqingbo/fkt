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
 * My_log_model CLASS
 *
 * 日志信息类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lu
 */
load_m("My_notebook_base_model");

class My_notebook_model extends My_notebook_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file my_log_model.php */
/* Location: ./app/models/my_log_model.php */
