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
 * Admin_operate_log_model CLASS
 * 记录运营后台行为轨迹
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Admin_operate_log_model extends MY_Model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->_tbl = 'admin_operate_log';
  }

  public function add_log()
  {
    //用户信息
    $user_session = $_SESSION['esfdatacenter'];
    //用户访问地址
    $router = where_am_i();
    //拼装数据
    $insert_data = array(
      'user_id' => $user_session['uid'],
      'city_id' => $user_session['city_id'],
      'y' => date('Y'), 'm' => date('m'),
      'd' => date('d'), 'time' => date('Hi'),
      'ip' => get_ip(), 'class' => $router['class'],
      'method' => $router['method'], 'get_param' => serialize($_GET),
      'post_param' => serialize($_POST), 'url' => $_SERVER['REQUEST_URI'],
      'create_time' => time()
    );
    //插入数据
    $this->db->insert($this->_tbl, $insert_data);
    return $this->db->affected_rows();
  }
}

/* End of file Attendance_config_model.php */
/* Location: ./app/models/Attendance_config_model.php */
