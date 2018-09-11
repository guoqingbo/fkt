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
 * Broker_model CLASS
 *
 * 经纪人业务逻辑类 提供用户在线、注册、登录、修改密码、登出、Session相关
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Broker_base_model");

class Broker_model extends Broker_base_model
{

  /**
   * 用户注册，找回密码验证码有效时长为60s
   * @var int
   */
  public $validcode_expiretime = 60;

  /**
   * 类初始化
   */
  public function __construct()
  {
    $this->load->library('verify');
    parent::__construct();
  }


  /**
   * 获取短信类
   * @param string 验证码类型
   * @return broker_sms_model class
   */
  public function get_broker_sms($type)
  {
    //引入用户SMS操作类，并初始化验证码的有效时长
    $this->load->model('broker_sms_model');
    //验证码过期时间
    $this->broker_sms_model->expiretime = $this->validcode_expiretime;
    //验证码类型
    $this->broker_sms_model->type = $type;
    return $this->broker_sms_model;
  }

  /**
   * 设置用户key
   * @param int $uid 用户编号
   * @param string $city_spell 城市拼音
   * @param int $city_id 城市编号
   */
  public function set_user_key($param)
  {
    return $this->verify->user_enrypt($param);
  }

  /**
   * 检查用户是否在线
   * @return boolean true 登入 false 未登入
   */
  public function check_online($scode)
  {
    if ($scode == '') {
      return false;
    }
    $check_online = $this->verify->user_decrypt($scode);
    if ($check_online['result'] == 1) //验证成功
    {
      $this->config->set_item('login_city', $check_online['data'][1]);
    }
    return $check_online;
  }

  /**
   * 检查设备号是否满足单点登录条件
   */
  public function check_by_brokerid_deviceid($broker_id, $deviceid)
  {
    $this->load->model('user_request_model');
    $device = $this->user_request_model->get_online_state_app($deviceid, $broker_id);
    return count($device);
  }
}

/* End of file Broker_model.php */
/* Location: ./app/models/Broker_model.php */
