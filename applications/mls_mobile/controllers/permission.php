<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统控制器
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 */

/**
 * Permission Controller CLASS
 *
 * 员工角色、权限管理功能 控制器
 *
 * @package         MLS
 * @subpackage      Controllers
 * @category        Controllers
 * @author          sun
 */
class Permission extends MY_Controller
{
  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function validate()
  {
    $func = $this->input->get('func');
    if ($func == '') {
      $this->result(0, '参数不能为空');
      die();
    }
    $arr_func = explode('/', $func);
    $func_permission = $this->get_func_permission($arr_func[0], $arr_func[1]);
    if (isset($func_permission['auth']) && $func_permission['auth'] == 1) {
      $this->result(1, '查询成功');
    } else if (isset($func_permission['auth']) && $func_permission['auth'] == 0) {
      $this->result(-1, '没有权限访问此页面');
    } else {
      $this->result(0, '查询失败');
    }
  }
}
