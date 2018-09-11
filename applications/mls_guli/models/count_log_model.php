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
 * Count_log_model CLASS
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Count_log_base_model");

class Count_log_model extends Count_log_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file Count_log_model.php */
/* Location: ./application/mls_guli/models/Count_log_model.php */
