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
   * 保密信息次数验证
   *
   * @access public
   * @param  int $type
   * @param  int $row_id
   * @return void
   */
  public function check_baomi_time($company_basic_arr, $broker_info, $type, $row_id, $is_insert = false)
  {
    //$type=$this->input->get('type',TRUE);
    //$row_id=$this->input->get('row_id',TRUE);
    //查看类型：1，出售；2，出租；3，求购；4，求租
    if (!empty($type)) {
      $type = intval($type);
    }
    $broker_id = $broker_info['broker_id'];

    $insert_data = array(
      'broker_id' => $broker_id,
      'row_id' => $row_id,
      'view_type' => $type,
      'view_time' => time()
    );

    //获取当前经济人所在公司的基本设置信息
    //$company_basic_data = $this->company_basic_arr;
    $secrecy_num = intval($company_basic_arr['secret_view_num']);

    //当前经纪人当天查看总次数
    //今天的凌晨时间戳
    $today_time = strtotime(date('Y-m-d'));
    //明天的凌晨时间戳
    $tomorrow_time = strtotime(date("Y-m-d", strtotime("+1 day")));
    $where_cond = 'broker_id = "' . $broker_id . '" and view_time > "' . $today_time . '" and view_time < "' . $tomorrow_time . '"';
    $this->load->model('broker_view_secrecy_model');
    $broker_data = $this->broker_view_secrecy_model->get_broker_totay_view_num($where_cond);

    $read_secrecy_num = intval($broker_data['num']);

    if ($secrecy_num > 0) {
      if ($read_secrecy_num < $secrecy_num) {
        if ($is_insert) {
          //添加记录
          $this->broker_view_secrecy_model->insert($insert_data);
        }
        return array('status' => true);
      } else {
        return array('status' => false, 'secrecy_num' => $secrecy_num);
      }
    } else {
      if ($is_insert) {
        //添加记录
        $this->broker_view_secrecy_model->insert($insert_data);
      }
      return array('status' => true);
    }
  }
}

/* End of file Broker_model.php */
/* Location: ./app/models/Broker_model.php */
