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
 * Perssion_menu_model CLASS
 *
 * 权限功能菜单菜单添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Permission_menu_base_model");

class Permission_menu_model extends Permission_menu_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 通过功能查找所属的子菜单
   * @param int $func_id 功能编号
   */
  public function get_by_func_id($func_id)
  {
    $menu = parent::get_by_func_id($func_id);
    $new_menu = array();
    if (is_full_array($menu)) {
      foreach ($menu as $v) {
        $new_menu[$v['id']] = $v['name'];
      }
    }
    return $new_menu;
  }
}

/* End of file permission_menu_base_model.php */
/* Location: ./app/models/permission_menu_base_model.php */
