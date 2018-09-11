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
 * Broker_sms_model CLASS
 *
 * 经纪人短信验证业务逻辑类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Broker_sms_base_model");

class Broker_sms_model extends Broker_sms_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 发送验证码
   * @param arra $data
   * @return array
   */
  public function send_sms($phone, $type)
  {
    $validcode = $this->rand_num();
    $insert_id = $this->add($phone, $validcode);
    if ($insert_id) {
      $this->load->library('Sms_codi', array('city' => 'hz', 'jid' => '3', 'template' => $type), 'sms');
      $return = $this->sms->send($phone, array('validcode' => $validcode));
      $result['status'] = $return['success'] ? 1 : 0;
      $result['msg'] = $return['success'] ? '短信发送成功' : $return['errorMessage'];
    } else {
      $result['status'] = 0;
      $result['msg'] = '短信获取失败，请重新获取';
    }
    return $result['status'];
  }
}

/* End of file Broker_sms_model.php */
/* Location: ./app/models/Broker_sms_model.php */
