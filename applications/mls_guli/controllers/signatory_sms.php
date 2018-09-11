<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 经纪sms操作 Class
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Signatory_sms extends MY_Controller
{

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('signatory_sms_model');
  }

  //经纪人获取短信验证码
  public function index()
  {
    //获取手机号码和获取验证码类型
    $phone = $this->input->get('phone', TRUE);
    $type = ltrim($this->input->get('type', TRUE));
    $jid = ltrim($this->input->get('jid', TRUE));
    if ($jid == '')//默认验证码
    {
      $jid = 1;
    }
    //返回结果数组
    $result = array('status' => 1, 'msg' => '');
    if ($phone == '') //验证手机号码是否为空
    {
      $result['status'] = 2;
      $result['msg'] = '手机号码不能为空';
    } else if ($type == '') {
      $result['status'] = 2;
      $result['msg'] = '验证码类型不能为空';
    } else if ($type == 'register' || $type == 'findpw'
      || $type == 'modify_phone'
    ) //注册和找回密码
    {
      //经纪人类
      $this->load->model('signatory_model');
      //号码是否已经被注册过
      $is_exist_phone = $this->signatory_model->is_exist_by_phone($phone);
      //注册帐号时判断号码是否已经注册过
      if (($type == 'register' || $type == 'modify_phone') && $is_exist_phone) {
        $result['status'] = 3;
        $result['msg'] = '此号码已经被注册过';
      }
      //只有注册用户才可找回密码
      if ($type == 'findpw' && !$is_exist_phone) {
        $result['status'] = 5;
        $result['msg'] = '号码有误！';
      }
    }
    if ($result['status'] == 1) //前面的都成功
    {
      $this->signatory_sms_model->type = $type;
      $is_repeate = $this->signatory_sms_model->is_expire_by_phone($phone);
      /* if ($is_repeate) //重复获取
       {
         $result['status'] = 4;
         $result['msg'] = '请不要在一分钟之内重复获取验证码';
       } else {*/
      $city_spell = 'hz'; //默认杭州
      //获取城市
      if ($type == 'register') {
        $city_id = ltrim($this->input->get('city_id', TRUE));
      } else if ($type == 'findpw' || $type == 'modify_phone') {
        //根据手机号码获取
        if ($type == 'findpw') {
          $signatory = $this->signatory_model->get_by_phone($phone);
        } else {
          $old_phone = $this->input->get('old_phone');
          $signatory = $this->signatory_model->get_by_phone($old_phone);
        }
        if (is_full_array($signatory)) {
          $city_id = $signatory['city_id'];
        }
      }
      if ($city_id) {
        //城市类
        $this->load->model('city_model');
        $city = $this->city_model->get_by_id($city_id);
        $city_spell = $city['spell'];
      }
      //验证码
      $validcode = $this->signatory_sms_model->rand_num();
      //插入相应的数据 3 注册
      $insert_id = $this->signatory_sms_model->add($phone, $validcode);
      if ($insert_id) //成功后发送相应短信
      {
        //引入SMS类库，并发送短信
        $this->load->library('Sms_codi', array('city' => $city_spell, 'jid' => $jid, 'template' => $type), 'sms');
        $return = $this->sms->send($phone, array('validcode' => $validcode));
        $result['status'] = $return['success'] ? 1 : 0;
        $result['msg'] = $return['success'] ? '短信发送成功' : $return['errorMessage'];
      } else {
        $result['status'] = 0;
        $result['msg'] = '短信获取失败，请重新获取';
      }

      /*      }*/
    }
    echo json_encode($result);
  }
}
/* End of file signatory_sms.php */
/* Location: ./application/mls_guli/controllers/signatory_sms.php */
