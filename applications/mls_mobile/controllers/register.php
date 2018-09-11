<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 注册 Class
 * 注册控制器
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Register extends MY_Controller
{

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  //注册提交
  public function signup()
  {
    $phone = $this->input->post('phone', TRUE);
    $validcode = $this->input->post('validcode', TRUE);
    $password = $this->input->post('password', TRUE);
    $city_id = $this->input->post('city_id', TRUE);
    $corpName = $this->input->post('corp_name', TRUE);
    $storeName = $this->input->post('store_name', TRUE);
    $userName = $this->input->post('user_name', TRUE);
    $type = 'register';
    //一、参数合法性
    if ($phone == '' || $validcode == '' || $password == ''
      || $city_id == ''
    ) {
      //跳转页面
      $this->result(2, '参数不合法');
      return;
    }
    //引入经纪人类
    $this->load->model('broker_model');
    //验证码是否正确，并且有效
    //引入用户SMS操作类，并初始化验证码的有效时长和类型
    $broker_sms = $this->broker_model->get_broker_sms($type);
    $validcode_id = $broker_sms->get_by_phone_validcode($phone, $validcode);
    if (!$validcode_id) //没有相关的验证码
    {
      $this->result(3, '验证码错误，请重新获取');
      return;
    }
    //看号码是否注册过
    $is_exist_phone = $this->broker_model->is_exist_by_phone($phone);
    if ($is_exist_phone) //已注册
    {
      $this->result(4, '此号码已经注册过');
      return;
    }
    //注册结果
    $insert_id = $this->broker_model->add_user($city_id, $phone, $password, '1');
    if ($insert_id > 0) //成功
    {
      //验证码已经验证过
      $this->broker_sms_model->validcode_set_esta($validcode_id);
      //根据城市编号查找相应数据库标识符
      $this->load->model('city_model');
      $city_info = $this->city_model->get_by_id($city_id);
      //初始化经纪人信息数据
      $this->config->set_item('login_city', $city_info['spell']);
      //引入经纪人详细信息类
      $this->load->model('broker_info_model');
      //$broker_info_id = $this->broker_info_model->init_broker($insert_id, $phone);
      $broker_info_id = $this->broker_info_model->init_broker($insert_id, $phone, 0, 1, $userName, $city_info['spell']);
      $ip = get_ip();
      $insertid = $this->broker_info_model->add_register($broker_info_id, $corpName, $storeName, $ip);
      if ($insertid) {
        $this->result(1, '注册成功');
      } else {
        $this->result(0, '注册失败');
      }
    } else //失败
    {
      $this->result(0, '注册失败');
    }
    //跳转到登录
  }
}
/* End of file register.php */
/* Location: ./application/mls/controllers/register.php */
