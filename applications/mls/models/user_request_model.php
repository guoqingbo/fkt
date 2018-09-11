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
 * User_request_model CLASS
 *
 * 登录在线通信业务逻辑类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models broker_online_pc_state
 * @author          fisher
 */
class User_request_model extends MY_Model
{

  private $allow_devicetype_arr = array('web', 'pc', 'ios', 'android');
  private $devicetype = 'pc';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 写入在线状态
   * @param string 验证码类型
   * @return broker_sms_model class
   */
  public function set_devicetype($type = 'pc')
  {
    $this->devicetype = in_array($type, $allow_devicetype_arr) ? $type : 'other';
  }


  /**
   * 读取在线帐号信息
   * @param string 设备ID
   * @param int 经纪人ID
   * @return broker_sms_model class
   */
  public function get_online_info($deviceid, $id)
  {
    return $this->get_data(array('select' => array('brokerid', 'dateline'), 'form_name' => 'broker_online_pc_state', 'where' => array('id' => $id, 'deviceid' => $deviceid), 'limit' => 1), 'db');
  }


  /**
   * 读取在线状态
   * @param string 设备ID
   * @param int 经纪人ID
   * @return broker_sms_model class
   */
  public function get_online_state($deviceid, $brokerid)
  {
    return $this->get_data(array('select' => array('id', 'dateline'), 'form_name' => 'broker_online_pc_state', 'where' => array('deviceid' => $deviceid, 'brokerid' => $brokerid), 'limit' => 1), 'db');
  }


  /**
   * 读取在线状态
   * @param int 状态记录ID
   * @return broker_sms_model class
   */
  public function update_online_state($stateid)
  {
    $dateline = time();

    return $this->modify_data(array('id' => $stateid), array('dateline' => $dateline), 'db', 'broker_online_pc_state');
  }

  /**
   * 删除在线状态
   * @param int 状态记录ID
   */
  public function delete_online_state($deviceid, $stateid)
  {
    $this->del(array('id' => $stateid, 'deviceid' => $deviceid), 'db', 'broker_online_pc_state');
  }


  /**
   * 写入在线状态
   * @param string 设备ID
   * @param int 经纪人ID
   * @param int 城市ID
   * @return broker_sms_model class
   */
  public function add_online_state($deviceid, $brokerid, $city_id)
  {
    $dateline = time();

    $this->del(array('brokerid' => $brokerid), 'db', 'broker_online_pc_state');

    return $this->add_data(array('deviceid' => $deviceid, 'brokerid' => $brokerid, 'city_id' => $city_id, 'dateline' => $dateline), 'db', 'broker_online_pc_state');
  }

  /**
   * 写入在线移动客户端状态
   * @param string 设备ID
   * @param int 经纪人ID
   * @param int 城市ID
   * @return broker_sms_model class
   */
  public function add_online_state_app($deviceid, $devicetype, $brokerid, $city_id)
  {
    $dateline = time();
    $this->del(array('brokerid' => $brokerid), 'db', 'broker_online_app_state');
    return $this->add_data(array('deviceid' => $deviceid, 'devicetype' => $devicetype, 'brokerid' => $brokerid, 'city_id' => $city_id, 'dateline' => $dateline), 'db', 'broker_online_app_state');
  }

  /**
   * 读取在线状态
   * @param string 设备ID
   * @param int 经纪人ID
   * @return broker_sms_model class
   */
  public function get_online_state_app($deviceid, $brokerid)
  {
    return $this->get_data(array('select' => array('id', 'dateline'), 'form_name' => 'broker_online_app_state', 'where' => array('deviceid' => $deviceid, 'brokerid' => $brokerid), 'limit' => 1), 'db');
  }


  /**
   * 写入帐号绑定设备编号
   * @param string 设备ID
   * @param int 经纪人ID
   * @return broker_sms_model class
   */
  public function set_user_deviceid($brokerid, $deviceid)
  {
    $this->del(array('brokerid' => $brokerid), 'db', 'broker_deviceid');
    return $this->add_data(array('brokerid' => $brokerid, 'deviceid' => $deviceid), 'db', 'broker_deviceid');
  }

  /**
   * 读取帐号绑定设备编号
   * @param string 设备ID
   * @param int 经纪人ID
   * @return broker_sms_model class
   */
  public function get_user_deviceid($brokerid)
  {
    return $this->get_data(array('select' => array('deviceid'), 'form_name' => 'broker_deviceid', 'where' => array('brokerid' => $brokerid), 'limit' => 1), 'db');
  }
}

/* End of file Broker_model.php */
/* Location: ./app/models/Broker_model.php */
