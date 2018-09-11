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

  //注册页
  public function index()
  {
    $data_view = array();
    //获取省份
    $this->load->model('city_model');
    $data_view['tel400'] = $this->config->item('tel400');
    $data_view['province'] = $this->city_model->get_province();
    //渲染注册页面数据
    $this->load->view('register', $data_view);
  }

  //注册提交
  public function signup()
  {
    $phone = $this->input->post('phone', TRUE);
    $validcode = $this->input->post('validcode', TRUE);
    $password = $this->input->post('password', TRUE);
    $city_id = $this->input->post('city_id', TRUE);
    $corpName = $this->input->post('corpName', TRUE);
    $storeName = $this->input->post('name2', TRUE);
    $userName = $this->input->post('userName', TRUE);
    //引入经纪人类
    $this->load->model('broker_model');
    $city_spell = $this->broker_model->get_city_spell($city_id);
    $type = 'register';
    $this->config->set_item('login_city', $city_spell);
    //一、参数合法性
    if ($phone == '' || $validcode == '' || $password == ''
      || $city_id == ''
    ) {
      //跳转页面
      echo json_encode(array('result' => 'register_error'));
      return false;
    }

    //验证码是否正确，并且有效
    //引入用户SMS操作类，并初始化验证码的有效时长和类型
    $broker_sms = $this->broker_model->get_broker_sms($type);
    $validcode_id = $broker_sms->get_by_phone_validcode($phone, $validcode);
    if (!$validcode_id) //没有相关的验证码
    {
      echo json_encode(array('result' => 'validcode_error'));
      return false;
    }
    //看号码是否注册过
    $is_exist_phone = $this->broker_model->is_exist_by_phone($phone);
    if ($is_exist_phone) //已注册
    {
      echo json_encode(array('result' => 'had_register'));
      return false;
    } else {
      //根据城市编号查找相应数据库标识符
      $this->load->model('broker_info_model');
        $insert_id = $this->broker_model->add_user($city_id, $phone, $password, '1');//插入数据返回id
      if ($insert_id > 0) {
          $broker_info_id = $this->broker_info_model->init_broker($insert_id, $phone, 0, 1, $userName, $city_spell, 11);
        $ip = get_ip();
        $insertid = $this->broker_info_model->add_register($broker_info_id, $corpName, $storeName, $ip);
        if ($insertid > 0) //成功
        {
          echo json_encode(array('result' => 'register_success'));
          return false;
        } else //失败
        {
          echo json_encode(array('result' => 'register_error'));
          return false;
        }
      } else {
        echo json_encode(array('result' => 'register_error'));
        return false;
      }
    }
  }
}
/* End of file register.php */
/* Location: ./application/mls/controllers/register.php */
