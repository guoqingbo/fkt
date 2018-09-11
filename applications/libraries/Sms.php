<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 发送短信
 *
 * @author user
 */
class Sms
{

  //一个手机号码一天只可接受10条短信，防止被刷
  private $_phone_limit = 10;

  //表名
  private $_tbl_sms = 'sms';

  /**
   * 短信接口
   * @var string
   */
//    private  $_url_yzm = 'http://sms.cd121.com/send/?infofrom=1&mobile={{mobile}}&msg={{msg}}&yzm={{yzm}}';
//    private  $_url_notice = 'http://sms.cd121.com/send/?infofrom=1&mobile={{mobile}}&msg={{msg}}&yzm={{yzm}}';
//    private  $_url_ad = 'http://sms.cd121.com/send/?infofrom=1&mobile={{mobile}}&msg={{msg}}';

  private $_url_yzm = '';
  private $_url_notice = '';
  private $_url_ad = '';
  //1 验证码  2 通知 3 广告
  private $_jid = ''; //条口

  private $_city = ''; //城市

  private $_template = ''; //模板

  private $_platform = '';
  private $_tel400 = '';
  //短信模板
  private $_module = array(
    '1' => array(
      'register' => '尊敬的用户，您操作的验证码为{{validcode}}，请在收到后3分钟内提交。',//经纪人注册
      'findpw' => '尊敬的用户，您操作的验证码为{{validcode}}，请在收到后3分钟内提交。',//找回密码
      'modify_phone' => '尊敬的用户，您操作的验证码为{{validcode}}，请在收到后3分钟内提交。',//找回密码
      'check_for_updates' => '亲，采集中断个数：{{num}}',//检测采集房源
    ),
    '2' => array(
      'cooperate_lol_pass' => '您好！您的合作成交资料(合同编号:{{order_sn}})已经审核通过！',
      'cooperate_lol_fail' => '您好！您的合作成交资料(合同编号:{{order_sn}})没有审核通过，请您按照活动规则重新提交相应资料。',
      'cooperate_activity_pass' => '您好！您的合作成交资料(合同编号:{{order_sn}})已经通过{{type}}{{score}}。',
      'cooperate_activity_fail' => '您好！您的合作成交资料(合同编号:{{order_sn}})没有通过{{type}}，请您按照活动规则重新提交相应资料。',
      'send_accepet_message_to_broker_a' => '您有新的合作申请尚未处理，请及时处理以免逾期。',
      'first_login' => '欢迎您使用{{platform}}，进入个人中心提交认证资料，有房源、客源、合作等更多功能！详询：{{tel400}}。',
      'auth_review_pass' => '恭喜您已成为{{platform}}认证经纪人，赶快登录{{platform}}使用吧。如有问题，请咨询：{{tel400}}。',//无人站经纪人审核通过发送短信
      'auth_review_fail' => '{{name}}您好，您的认证信息未通过审核，请登录{{platform}}重新提交。如有问题，请咨询：{{tel400}}。'//无人站经纪人审核通过发送短信
    ),
    '3' => array(
      'rent_finance' => '尊敬的用户，您操作的验证码为{{validcode}}，请在收到后3分钟内提交。'//租房分期
    ),
  );

  public function __construct($param)
  {
    $this->config =& load_class('Config', 'core');

    $this->_city = $param['city'];
    $this->_jid = $param['jid'];
    $this->_template = $param['template'];
    $this->_tel400 = $param['tel400'] ? $param['tel400'] : $this->config->item('tel400');
    $this->_platform = $param['platform'] ? $param['platform'] : $this->config->item('title');
    $this->_url_yzm = $this->config->item('sms_url_yzm');
    $this->_url_notice = $this->config->item('sms_url_notice');
    $this->_url_ad = $this->config->item('sms_url_ad');
  }

  /**
   * 检查参数的合法性
   * @return array
   */
  private function _check_init_param()
  {
    if ($this->_city == '' || !in_array($this->_jid, array(1, 2, 3))
      || $this->_template == ''
    ) {
      return array('result' => 0, 'msg' => '初始化参数有误');
    }
    return array('result' => 1);
  }

  /**
   * 获取短信发送内容
   * @param array $content 内容参数
   * @return type
   */
  private function _get_message($content = array())
  {
    $template = $this->_module[$this->_jid][$this->_template];
    $message = $template;
    if (is_full_array($content)) {
      foreach ($content as $key => $val) {
        $template = str_replace('{{' . $key . '}}', $val, $template);
      }
      $message = $template;
    }
    return $message;
  }

  /**
   * 获取请求的url地址
   * @param string $phone 手机号码
   * @param array $content 参数
   * @return string url
   */
  private function _get_send_url($phone, $content = array())
  {
    $message = $this->_get_message($content);
    $url_param = array('city' => 'hz', 'mobile' => $phone, 'msg' => $message);
    if ($this->_jid == 1) {
      $url_template = $this->_url_yzm;
    } else if ($this->_jid == 2) {
      $url_template = $this->_url_notice;
    } else if ($this->_jid == 3) {
      $url_template = $this->_url_ad;
    }
    //替换请求地址模板
    foreach ($url_param as $key => $val) {
      $url_template = str_replace('{{' . $key . '}}', $val, $url_template);
    }
    return $url_template;
  }

  /**
   * 验证短信发送总数
   * @param int $phone 手机号码
   * @param array or string $content 内容
   */
  private function _sms_count($phone, $content)
  {
    $ci = &get_instance();
    $ci->load->library('My_DB', '', 'mydb');
    $this->db = $ci->mydb->get_db_obj('db');
    $this->db->where(array('phone' => $phone, 'YMD' => date('Y-m-d')));
    $limit = $this->db->count_all_results($this->_tbl_sms);
    if ($limit >= $this->_phone_limit) //判断有没有超过上限
    {
      return true;
    } else {
      $insert_data = array(
        'phone' => $phone, 'content' => serialize($content),
        'YMD' => date('Y-m-d'), 'create_time' => time(),
        'city' => $this->_city
      );
      //插入数据
      $this->db->insert($this->_tbl_sms, $insert_data);
      return false;
    }
  }

  /**
   * 发送短信
   * @param int $phone 手机号码
   * @param array or string $content 内容
   * @param string $type 类型模板
   * @param array $name Description
   */
  public function send($phone, $content = array())
  {
    $check_param = $this->_check_init_param();
    if ($check_param['result'] == 0) {
      return $check_param;
    }
    //判断短信当天是否超过
    if ($this->_sms_count($phone, $content)) {
      return false;
    }
    $request_url = $this->_get_send_url($phone, $content);
    //echo $request_url;die();
    $ci = &get_instance();
    $ci->load->library('Curl');
    $json = Curl::curl_get_contents($request_url);
    return json_decode($json, true);
  }
}
