<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 登录控制器
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Login extends MY_Controller
{

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('broker_model');
  }

  //验证登录
  public function signin()
  {
    $devicetype = $this->input->post('api_key', TRUE);
    $phone = $this->input->get_post('phone', TRUE);
    $password = $this->input->get_post('password', TRUE);
    $result = $this->broker_model->login($phone, $password);
    if ($result === 'error_param') //参数不合法
    {
      $this->result(2, '参数不合法');
    } else if (isset($result) && isset($result['expiretime'])
      && $result['expiretime'] < time()
    ) //帐号到期
    {
      $this->result(3, '帐号到期');
    } else if (isset($result) && isset($result['status']) //帐号失效
      && $result['status'] == 2
    ) {
      $this->result(4, '帐号失效');
    } else if (isset($result) && isset($result['status'])
      && $result['status'] == 1
    ) //登录成功
    {
      $broker_id = $result['id'];

      //判断经纪人是属于哪个城市，并初始化相应的数据据
      $this->load->model('city_model');
      $city = $this->city_model->get_by_id($result['city_id']);
      //设置当前登录的城市
      $this->config->set_item('login_city', $city['spell']);
//      $this->config->set_item('abroad', $city['abroad']);
//      $this->config->set_item('tourism', $city['tourism']);

      //操作日志
      $deviceid = $this->input->post('deviceid', TRUE);
      $this->load->model('operate_log_model');

      //更新登录时间
      $this->load->model('broker_info_model');
      $broker_info = $this->broker_info_model->get_by_broker_id($broker_id);
      if (empty($broker_info['login_time'])) {//首次登录
        //引入SMS类库，并发送短信
        $this->load->library('Sms_codi', array('city' => $city['spell'], 'jid' => '2', 'template' => 'first_login'), 'sms');
        $return = $this->sms->send($phone);
        $result['status'] = $return['success'] ? 1 : 0;
        $result['msg'] = $return['success'] ? '短信发送成功' : $return['errorMessage'];
        $this->load->model('message_base_model');
        $this->message_base_model->add_message('8-52', $broker_id, $broker_info['truename'], '/my_info/');
      }
      $this->broker_info_model->update_by_broker_id(array('login_time' => time()), $broker_id);

      $add_log_param = array();
      if (is_full_array($broker_info)) {
        $add_log_param['company_id'] = $broker_info['company_id'];
        $add_log_param['agency_id'] = $broker_info['agency_id'];
        $add_log_param['broker_id'] = $broker_id;
        $add_log_param['broker_name'] = $broker_info['truename'];
        $add_log_param['type'] = 1;
        $add_log_param['text'] = '成功登录';
        if ($devicetype == 'android') {
          $add_log_param['from_system'] = 2;
        } else {
          $add_log_param['from_system'] = 3;
        }
        $add_log_param['device_id'] = $deviceid;
        $add_log_param['from_ip'] = get_ip();
        $add_log_param['mac_ip'] = get_ip();
        $add_log_param['from_host_name'] = get_ip();
        $add_log_param['hardware_num'] = '测试硬件序列号';
        $add_log_param['time'] = time();
      }
      $this->operate_log_model->add_operate_log($add_log_param);

      //加密参数
      $param = array(
        'broker_id' => $broker_id, 'city_spell' => $city['spell'],
        'city_id' => $result['city_id'], 'login_time' => time(),
      );
      $scode = $this->broker_model->set_user_key($param);
      //插入相关的设备登录日志
      $devicetype = $this->input->post('api_key', TRUE);
      $app_id = $this->input->post('app_id', TRUE);
      if (intval($app_id) <= 0) {
        $app_id = 0;
      } //没有设置app_id，用于支持旧版本
      $this->load->model('broker_online_app_model');
      $add_login_record = array(
        'deviceid' => $deviceid, 'devicetype' => $devicetype,
        'broker_id' => $broker_id, 'scode' => $scode,
        'dateline' => time(), 'city' => $city['spell'],
        'app_id' => $app_id
      );
      $this->broker_online_app_model->add_login_record($add_login_record);
      $this->load->model('broker_login_log_model');
      $ip = get_ip();
      $this->broker_login_log_model->insert_login_log($broker_id, $ip, $deviceid, $phone, 2);
      //判断是否上线积分功能
      $version = $this->input->post('version', true);
      $open_credit = 1;
      if ($devicetype == 'iPhone') {
        //if ($version == '1.0.4') {$open_credit = 0;}
      } else {
        //if ($version == '1.0.2') {$open_credit = 0;}
      }
        if (in_array($city['spell'], array('hz', 'sz', 'km', 'cq', 'cd'))) {
        $open_credit = 0;
      }
      //判断是否上线抽奖功能
      $open_reward = 0;
//      $open_city = array('nj', 'hrb', 'lanzhou');
//      if (in_array($city['spell'], $open_city)) {
//        $open_reward = 1;
//      }
      //获取当天登录次数
      $login_num = $this->broker_login_log_model->get_count_day_login($broker_id, 2);
      $finance_ad = array('status' => 0, 'big_url' => '', 'small_url' => '', 'href' => '');
      if ($login_num == 1 || $phone == '17412361236') //第一次登录帐号显示广告
      {
        if ($city['spell'] == 'sz' || $city['spell'] == 'km') {
          $finance_ad['status'] = 1;
          $finance_ad['href'] = MLS_MOBILE_URL . '/wap/pledge/ad/';
          $finance_ad['big_url'] = MLS_SOURCE_URL . '/finance/wap/images/finance_ad_big_1.png';
          $finance_ad['small_url'] = MLS_SOURCE_URL . '/finance/wap/images/finance_ad_small_1.png';
        }
      }
      //合作成功率平均值
      $this->load->model('api_broker_sincere_model');
      $trust_appraise_count = $this->api_broker_sincere_model->get_trust_appraise_count($broker_id);
      //没有好评率时
      if ($trust_appraise_count['good_rate'] == '') {
        $trust_appraise_count['good_rate'] = 0;
      }
      $this->load->model('cooperate_suc_ratio_base_model');
      $cop_succ_ratio_info = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_id);
      if ($cop_succ_ratio_info['cop_succ_ratio'] == '') {
        $cop_succ_ratio_info['cop_succ_ratio'] = 0;
      }
      //获取当前登录经纪人信息
      $this->load->model('api_broker_model');
      $broker_info = $this->api_broker_model->get_by_broker_id($broker_id);
      //获取当前经济人所在公司的基本设置信息
      $company_basic_data = $this->company_basic_arr;
      //增加积分
      $this->load->model('api_broker_credit_model');
      $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_id));
      $credit_result = $this->api_broker_credit_model->sign();
      $credit_tip = '';//积分提示语
      if (is_full_array($credit_result) && $credit_result['status'] == 1) {
        $credit_tip .= ',+' . $credit_result['score'] . '积分';
      }
      //增加等级分值
      $this->load->model('api_broker_level_model');
      $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_id));
      $level_result = $this->api_broker_level_model->sign();
      if (is_full_array($level_result) && $level_result['status'] == 1) {
        $credit_tip .= ',+' . $level_result['score'] . '成长值';
      }
      //获取IM聊天token值
      //判断token是否存在如果存在刚获取，反之请求融云服务器
//      $im_token = '';
//      if (isset($broker_info['im_token']) && $broker_info['im_token'] != '') {
//        $im_token = $broker_info['im_token'];
//      } else {
//        if ($broker_info['broker_id'] && $broker_info['truename'] && $broker_info['photo']) {
//          $this->load->library('im/RongCloudApi', '', 'rca');
//          $r = $this->rca->getToken($broker_info['broker_id'], $broker_info['truename'], $broker_info['photo']);
//          $r_arr = json_decode($r, true);
//          if ($r_arr['code'] == 200) {
//            $im_token = $r_arr['token'];
//            $this->broker_info_model->update_by_broker_id(array('im_token' => $im_token), $broker_info['broker_id']);
//          }
//        }
//      }
      //获取广告图片数据
      $new_advert = array();
      $this->load->model('advert_app_manage_model');
      $advert = $this->advert_app_manage_model->get_all_by();
      if (is_full_array($advert)) {
        foreach ($advert as $v) {
          if ($v['type'] != 6) {
            continue;
          }
          $extra = unserialize($v['extra']);
          $new_advert[] = array('pic' => str_replace('/thumb', '', $v['pic']),
            'url' => $extra['url'], 'title' => $extra['title']);
        }
      }

      //获取金融开关
      $finance = 'off';
      if ($city['mortgage'] || $city['pledge'] || $city['rental']) {
        $finance = 'on';
      }

      //获取指定类型的未读消息
      $this->load->model('message_model');
      $unread_message_num = $this->message_model->get_count_by_cond(array('broker_id' => $broker_id, 'is_read' => 0));

      $this->result(1, '登录成功' . $credit_tip,
        array(
          'scode' => $scode,
          'broker_id' => $broker_id,
          'cop_succ_ratio' => $cop_succ_ratio_info['cop_succ_ratio'],
          'good_rate' => $trust_appraise_count['good_rate'],
          'group_id' => $broker_info['group_id'],
          'open_cooperate' => $company_basic_data['open_cooperate'],
          'open_credit' => $open_credit,
//          'im_token' => $im_token,
          'advert' => $new_advert,
//          'open_abroad_tourism' => 0,
//          'open_abroad' => $city['abroad'],
//          'open_tourism' => $city['tourism'],
          'show_brokerage' => 0,
          'open_reward' => $open_reward,
          'finance' => $finance,
          'mortgage' => $city['mortgage'],
          'pledge' => $city['pledge'],
          'rental' => $city['rental'],
          'app_version' => $city['app_version'],
          'exist_unread_message' => $unread_message_num > 0 ? 1 : 0,
          'finance_ad' => $finance_ad,
          'update_lead_tip' => 0,
        )
      );
    } else {
      $this->result(0, '登录失败,用户名或密码不正确');
    }
  }

  //找回密码
  public function findpw()
  {

    $phone = $this->input->post('phone', TRUE);
    $validcode = $this->input->post('validcode', TRUE);
    $password = ltrim($this->input->post('password', TRUE));
    $verify_password = trim($this->input->post('verify_password', TRUE));
    if ($phone == '' || $validcode == '' || $password == ''
      || $verify_password == ''
    ) //参数不对
    {
      $this->result(2, '参数不合法');
      return;
    }
    $broker_sms = $this->broker_model->get_broker_sms('findpw');
    $validcode_id = $broker_sms->get_by_phone_validcode($phone, $validcode);
    if (!$validcode_id) //没有相关的验证码
    {
      //跳转页面
      $this->result(3, '验证码错误或者失效，请重新获取！');
      return;
    }
    //更新密码 成功返回受影响的行数
    $result = $this->broker_model->update_password($phone,
      $password, $verify_password);
    if ($result === 'password_not_same') //两次输入的密码不一致
    {
      //跳转页面
      $this->result(4, '两次输入的密码不一致，请重新输入！');
      return;
    } else if ($result === 'non_exist_phone') //是否存在手机号码
    {
      //跳转页面
      $this->result(5, '您输入的号码未注册哦，请重新输入！');
      return;
    } else {
      $broker_sms->validcode_set_esta($validcode_id);
      //更改用户密码
      $this->result(1, '成功找回密码');
      return;
    }
  }

  //ios 注册推送
  public function push_token()
  {
    //$broker_id = $this->input->get('broker_id', TRUE);
    //$device_token = $this->input->get('token', TRUE);
    //$this->load->model('broker_online_app_model');
    //$this->broker_online_app_model->update_push_token($broker_id, $device_token);
    $this->result(1, '请求成功');
  }

  //退出帐号
  public function quit()
  {
    $broker_id = $this->user_arr['broker_id'];
    $deviceid = $this->input->get('deviceid', TRUE);
    if ($broker_id == '') //参数不对
    {
      $this->result(2, '参数不合法');
      return;
    }
    $data = array('broker_id' => $broker_id, 'deviceid' => $deviceid);
    $this->load->model('broker_online_app_model');
    $loginout = $this->broker_online_app_model->delete($data);
    if ($loginout) {
      $this->result(1, '退出成功');
    } else {
      $this->result(0, '退出失败，请重试');
    }
  }
}
/* End of file login.php */
/* Location: ./application/mls/controllers/login.php */
