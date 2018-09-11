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
 * Perssion_module_model CLASS
 *
 * 权限模块添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          LION
 */
load_m("Group_refresh_base_model");

class Group_refresh_model extends Group_refresh_base_model
{
  private $group_refresh_tbl = 'group_refresh_log';
  private $mass_site = 'mass_site';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file group_refresh_model.php */
/* Location: ./applications/mls/models/group_refresh_model.php */
