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
   * 设置用户SESSION数据
   * @access  public
   * @return  array   用户SESSION数据
   */
  public function get_user_session()
  {
    $app_key = $this->get_appkey();
    return $this->session->userdata($app_key);
  }


  /**
   * 获取APP KEY
   * @access  public
   * @return  string   APPKEY
   */
  public function get_appkey()
  {
    return USER_SESSION_KEY;
  }


  /**
   * 设置用户SESSION数据
   * @access  public
   * @param  array $session_data 用户数据
   * @return  void
   */
  public function set_user_session($session_data = array())
  {
    $app_key = $this->get_appkey();
    //把用户SESSION数据存在App key内
    $this->session->set_userdata(array($app_key => $session_data));
  }


  /**
   * 重置用户SESSION数据
   */
  public function reset_user_session()
  {
    $app_key = $this->get_appkey();
    $session_data = $this->get_user_session();
    //把用户SESSION数据存在App key内
    $this->session->set_userdata(array($app_key => $session_data));
  }


  /**
   * 检查用户是否在线
   * @return boolean true 登入 false 未登入
   */
  public function check_online()
  {
    $u_session = $this->get_user_session();

      //在线用户SESSION数据有值且id大于0且处于系统防护时间内
//      $guard_time = $_SESSION['guard_time'] * 60;
//      $last_access = $u_session['last_access'];
//      if ($guard_time > 0 && $last_access > 0 && (time() > ($guard_time + $last_access))) {
//          $this->logout();
//          unset(
//              $_SESSION['guard_time']
//          );
//          return false;
//      }
    if (isset($u_session['broker_id']) && intval($u_session['broker_id']) > 0) {
      $this->config->set_item('login_city', $u_session['city_spell']);
      return true;
    } else {
      return false;
    }
  }

  /**
   * 用户登出
   */
  public function logout()
  {
    $app_key = $this->get_appkey();
    //清空SESSION数据
    $this->session->unset_userdata($app_key);
  }


  public function get_city_spell($city_id)
  {
    $this->dbback->select('spell');
    $this->dbback->where('id', $city_id);
    $res = $this->dbback->get('city')->row_array();
    return $res['spell'];
  }
}

/* End of file Broker_model.php */
/* Location: ./app/models/Broker_model.php */
