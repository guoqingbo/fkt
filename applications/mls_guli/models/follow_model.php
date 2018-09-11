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
 * follow_model CLASS
 *
 * 跟进信息的删除 修改 查询方法。
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          LION
 */
load_m('Follow_base_model');

class Follow_model extends Follow_base_model
{
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file follow_model.php */
/* Location: ./application/mls_guli/models/follow_model.php */
