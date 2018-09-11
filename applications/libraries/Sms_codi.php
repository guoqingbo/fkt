<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 发送短信
 *
 * @author user
 */
class Sms_codi
{

  //一个手机号码一天只可接受10条短信，防止被刷
  private $_phone_limit = 10;

  //表名
  private $_tbl_sms = 'sms';

  /**
   * 短信接口
   * @var string
   */
//  private $_url_yzm = SMS_URL;
//  private $_url_notice = SMS_URL;
//  private $_url_ad = SMS_URL;

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
      'register' => array(
        'code' => 'SMS_003',
        'template' => '尊敬的用户，您操作的验证码为${validcode}，请在收到后3分钟内提交。' //经纪人注册
      ),
      'findpw' => array(
        'code' => 'SMS_003',
        'template' => '尊敬的用户，您操作的验证码为${validcode}，请在收到后3分钟内提交。' //找回密码
      ),
      'modify_phone' => array(
        'code' => 'SMS_003',
        'template' => '尊敬的用户，您操作的验证码为${validcode}，请在收到后3分钟内提交。' //修改手机号
      ),
      'check_for_updates' => array(
        'code' => 'SMS_004',
        'template' => '亲，采集中断个数：${num}' //检测采集房源
      )
    ),
    '2' => array(
      'cooperate_lol_pass' => array(
        'code' => 'SMS_005',
        'template' => '您好！您的合作成交资料(合同编号:${order_sn})已经审核通过！'
      ),
      'cooperate_lol_fail' => array(
        'code' => 'SMS_006',
        'template' => '您好！您的合作成交资料(合同编号:${order_sn})没有审核通过，请您按照活动规则重新提交相应资料。'
      ),
      'cooperate_activity_pass' => array(
        'code' => 'SMS_007',
        'template' => '您好！您的合作成交资料(合同编号:${order_sn})已经通过${type}${score}。'
      ),
      'cooperate_activity_fail' => array(
        'code' => 'SMS_008',
        'template' => '您好！您的合作成交资料(合同编号:${order_sn})没有通过${type}，请您按照活动规则重新提交相应资料。'
      ),
      'send_accepet_message_to_broker_a' => array(
        'code' => 'SMS_009',
        'template' => '您有新的合作申请尚未处理，请及时处理以免逾期。'
      ),
      'first_login' => array(
        'code' => 'SMS_010',
        'template' => '欢迎您使用${platform}，进入个人中心提交认证资料，有房源、客源、合作等更多功能！详询：${tel400}。'
      ),
      'auth_review_pass' => array(
        'code' => 'SMS_011',
        'template' => '恭喜您已成为${platform}认证经纪人，赶快登录${platform}使用吧。如有问题，请咨询：${tel400}。' //无人站经纪人审核通过发送短信
      ),
      'auth_review_fail' => array(
        'code' => 'SMS_012',
        'template' => '${name}您好，您的认证信息未通过审核，请登录${platform}重新提交。如有问题，请咨询：${tel400}。' //无人站经纪人审核通过发送短信
      ),
//      'transfer_stage_n' => array(
//        'code' => 'SMS_013',
//        'template' => '您好！合同编号:(${order_sn})，${stage}已完成！'
//      ),
        'transfer_stage_n' => array(
            'code' => 'SMS_013',
            'template' => '您好！合同编号:${order_sn}，物业地址：${house_addr}，${stage}已完成！'
        ),
      'contract_stage_n' => array(
        'code' => 'SMS_014',
        'template' => '您好！您的预约${stage}，预约编号:${order_sn}，预约时间:${signing_time}。如有问题，请咨询：${tel400}。！'
      )
    ),
    '3' => array(
      'rent_finance' => array(
        'code' => 'SMS_003',
        'template' => '尊敬的用户，您操作的验证码为${validcode}，请在收到后3分钟内提交。'//租房分期
      )
    )
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
   * @deprecated 废弃不用
   * @param array $content 内容参数
   * @return type
   */
  private function _get_message($content = array())
  {
    $template = $this->_module[$this->_jid][$this->_template]['template'];
    $message = $template;
    if (is_full_array($content)) {
      foreach ($content as $key => $val) {
        $template = str_replace('${' . $key . '}', $val, $template);
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
  private function _get_template_url($content = array())
  {
    if ($this->_jid == 1) {
      return $this->_url_yzm;
    } else if ($this->_jid == 2) {
      return $this->_url_notice;
    } else if ($this->_jid == 3) {
      return $this->_url_ad;
    }
  }

  /**
   * 获取短信发送Code
   * @param array $content 内容参数
   * @return type
   */
  private function _get_template_code()
  {
    if ($this->_module[$this->_jid][$this->_template])
      return $this->_module[$this->_jid][$this->_template]['code'];
  }

  /**
   * 获取请求的url地址
   * @param string $phone 手机号码
   * @param array $content 参数
   * @return string url
   */
  private function _get_template_content($phone, $content = array())
  {
    /*    $url_param = array();
        if ($this->_jid == 1) {
          $url_param['validcode'] = $content['validcode'];
        }*/

    $content['tel400'] = $this->_tel400;
    $content['platform'] = $this->_platform;

    return $content;
  }

  /**
   * 获取请求模板
   * @param string $phone 手机号码
   * @param array $content 参数
   * @return string url
   */
  private function _get_send_template($phone, $content = array())
  {
    $url = $this->_get_template_url();
    $template_code = $this->_get_template_code();
    $template_content = $this->_get_template_content($phone, $content);
    $template = array(
      'url' => $url,
      'templateCode' => $template_code,
      'mobile' => $phone,
      'content' => $template_content
    );
    return $template;
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
          return array('success' => false, 'errorMessage' => '短信发送过于频繁，已超过当日限制');
      }

    $template = $this->_get_send_template($phone, $content);
    $url = $template['url'];

    $params = array(
      'phoneNum' => $template['mobile'],
      'messageText' => '【' . $this->_platform . '】' .$this->_get_message($template['content']),
      'fun' => get_ip(),
    );

    $ci = &get_instance();
    $ci->load->library('Curl');
    // 暂时关闭发送通道，直接返回成功
    if (SMS_SEND) {
        $output = $this->vpost($url, http_build_query($params));
        $result = preg_split("/[,\r\n]/", $output);
        if ($result[1] == 0) {
            return array('success' => true);
        } else {
            return array('success' => false, 'errorMessage' => '短信发送失败');
        }
    } else {
      // 暂时关闭发送通道，直接返回成功
      $json = array('success' => true);
      return $json;
    }
  }

    function vpost($post_url, $post_fields, $cookie = '')
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $post_url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields); // Post提交的数据包
        //curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file); // 读取上面所储存的Cookie信息
//        curl_setopt($curl, CURLOPT_PROXY, "118.178.229.226");//短信代理ip
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);
        }
        curl_close($curl); // 关键CURL会话
        return $tmpInfo; // 返回数据
    }
}
