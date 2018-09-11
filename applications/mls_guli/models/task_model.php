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
 * Task_model CLASS
 *
 * 任务增删改查功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Task_base_model");

class Task_model extends Task_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

}

/* End of file task_model.php */
/* Location: ./applications/mls_guli/models/task_model.php */
