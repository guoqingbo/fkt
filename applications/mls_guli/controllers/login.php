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
    $this->load->model('signatory_model');
  }

  //登录页
  public function index($nojump = 0, $new = 0, $version = '')
  {

    //系统标题
    $data['title'] = $this->config->item('title');

    $is_online = $this->signatory_model->check_online();
    if ($is_online) //判断是否登录
    {
      //登录成功
        $this->jump(MLS_SIGN_URL, '数据加载中。。。');
    } else {
      //$data['nojump'] = $nojump;
      $data['nojump'] = 1;
      if ($new == 1) {
        $data['update'] = $this->update_pc_client($version);
        $this->load->view('pclogin', $data);
      } else {
        $this->load->view('login', $data);
      }
    }
  }

  //升级提示
  public function update_pc_client($version)
  {
    $nowversion = md5("fktpc_2.0.1");

    return $version != $nowversion ? true : false;
  }


  //验证登录
  public function signin($key = 0)
  {
    if ($key == 1) {
      $time = 0;
      $sql_str = '';
      $qs_arr = $this->signatory_model->session->userdata("query_sql");
      $num = is_array($qs_arr) && !empty($qs_arr) ? count($qs_arr) : 0;
      echo "<a href='" . MLS_SIGN_URL . "/login/signin/1/'>刷新</a>";
      if ($num) {
        echo $num . "=======";
        foreach ((array)$qs_arr as $arr) {
          $sql_str .= "<table border='1'><tr><td style='width:120px;'>" . $arr['hostname'] . "</td><td style='width:800px;'>" . $arr['sql'] . "</td><td style='width:150px;'>" . $arr['time'] . "</td></tr></table>";

          $time += $arr['time'];
        }

        echo $time . "<br />" . $sql_str;
      }
    } else {
      $is_online = $this->signatory_model->check_online();
      $action = $this->input->post('action', TRUE);
      if ($is_online) //判断是否登录
      {
        //登录成功
        $this->jump(MLS_SIGN_URL);
      } else if ($action == 'pcsignin') {
        $phone = $this->input->post('phone', TRUE);
        $password = $this->input->post('password', TRUE);
        $result = $this->signatory_model->login($phone, $password);

        if ($result === 'error_param') //参数不合法
        {
          echo json_encode(array('result' => 0, 'msg' => '帐号密码不能为空', 'data' => ''));
        } else if (isset($result) && isset($result['expiretime'])
          && $result['expiretime'] < time()
        ) //帐号到期
        {
          echo json_encode(array('result' => 0, 'msg' => '帐号到期', 'data' => ''));
        } else if (isset($result) && isset($result['status']) //帐号失效
          && $result['status'] == 2
        ) {
          echo json_encode(array('result' => 0, 'msg' => '帐号失效', 'data' => ''));
        } else if (isset($result) && isset($result['status'])
          && $result['status'] == 1
        ) //登录成功
        {
          $this->del_search_list_cookie();
          //echo '登录成功';

          //判断经纪人是属于哪个城市，并初始化相应的数据据
          $this->load->model('city_model');
          $city = $this->city_model->get_by_id($result['city_id']);
          $init_data_session = array(
            'signatory_id' => $result['id'],
            'cityname' => $city['cityname'],
            'city_spell' => $city['spell'],
            'city_id' => $result['city_id'],
            'deviceid' => '',  //单点登录用
            'osid' => 0      //单点登录用
          );
          $this->signatory_model->set_user_session($init_data_session);

          //操作日志
          $this->config->set_item('login_city', $city['spell']);
          $this->load->model('signatory_operate_log_model');
          $this->load->model('signatory_info_model');
          //引入登录日志的model
          //$this->load->model('signatory_login_log_model');

          $signatory_id = $result['id'];
          $signatory_info = $this->signatory_info_model->get_by_signatory_id($signatory_id);
          $add_log_param = array();
          if (is_full_array($signatory_info)) {
            $add_log_param['company_id'] = $signatory_info['company_id'];
            $add_log_param['department_id'] = $signatory_info['department_id'];
            $add_log_param['signatory_id'] = $signatory_id;
            $add_log_param['signatory_name'] = $signatory_info['truename'];
            $add_log_param['type'] = 1;
            $add_log_param['text'] = '成功登录';
            $add_log_param['from_system'] = 1;
            $add_log_param['from_ip'] = get_ip();
            $add_log_param['mac_ip'] = get_ip();
            $add_log_param['from_host_name'] = get_ip();
            $add_log_param['hardware_num'] = '测试硬件序列号';
            $add_log_param['time'] = time();
          }
          $add_result = $this->signatory_operate_log_model->add_operate_log($add_log_param);
          //参数$signatory_id, $ip, $deviceid, $phone, $infofrom = 1
          //$add_login_log = $this->signatory_login_log_model->insert_login_log($add_log_param['signatory_id'],$add_log_param['from_ip'],'网页',$signatory_info['phone'],1);

          echo json_encode(array('result' => 1, 'msg' => '登录成功', 'data' => MLS_SIGN_URL . '/welcome/index/1'));
        } else {
          echo json_encode(array('result' => 0, 'msg' => '用户名或者密码错误', 'data' => ''));
        }
      } else {
        $phone = $this->input->post('phone', TRUE);
        $password = $this->input->post('password', TRUE);
        $result = $this->signatory_model->login($phone, $password);

        if ($result === 'error_param') //参数不合法
        {
          echo '参数不合法';
        } else if (isset($result) && isset($result['expiretime'])
          && $result['expiretime'] < time()
        ) //帐号到期
        {
          echo '帐号到期';
        } else if (isset($result) && isset($result['status']) //帐号失效
          && $result['status'] == 2
        ) {
          echo '帐号失效';
        } else if (isset($result) && isset($result['status']) && $result['status'] == 1) //登录成功
        {
          $this->del_search_list_cookie();
          //echo '登录成功';

          //判断经纪人是属于哪个城市，并初始化相应的数据据
          $this->load->model('city_model');
          $city = $this->city_model->get_by_id($result['city_id']);
          $init_data_session = array(
            'signatory_id' => $result['id'],
            'cityname' => $city['cityname'],
            'city_spell' => $city['spell'],
            'city_id' => $result['city_id'],
            'deviceid' => '',  //单点登录用
            'osid' => 0      //单点登录用
          );
          $this->signatory_model->set_user_session($init_data_session);

          //操作日志
          $this->config->set_item('login_city', $city['spell']);
          $this->load->model('signatory_operate_log_model');
          $this->load->model('signatory_info_model');
          //引入登录日志的model
          //$this->load->model('signatory_login_log_model');

          $signatory_id = $result['id'];
          $signatory_info = $this->signatory_info_model->get_by_signatory_id($signatory_id);
          $add_log_param = array();
          if (is_full_array($signatory_info)) {
            $add_log_param['company_id'] = $signatory_info['company_id'];
            $add_log_param['department_id'] = $signatory_info['department_id'];
            $add_log_param['signatory_id'] = $signatory_id;
            $add_log_param['signatory_name'] = $signatory_info['truename'];
            $add_log_param['type'] = 1;
            $add_log_param['text'] = '成功登录';
            $add_log_param['from_system'] = 1;
            $add_log_param['from_ip'] = get_ip();
            $add_log_param['mac_ip'] = get_ip();
            $add_log_param['from_host_name'] = get_ip();
            $add_log_param['hardware_num'] = '测试硬件序列号';
            $add_log_param['time'] = time();
          }
          //在这里添加日志
          $add_result = $this->signatory_operate_log_model->add_operate_log($add_log_param);
          //参数$signatory_id, $ip, $deviceid, $phone, $infofrom = 1
          //$add_login_log = $this->signatory_login_log_model->insert_login_log($add_log_param['signatory_id'],$add_log_param['from_ip'],'网页',$signatory_info['phone'],1);

          $this->jump(MLS_SIGN_URL, '数据加载中。。。');

        } else {
          echo '登录失败，用户名或者密码错误';
        }
      }
    }
  }

  //退出登录
  public function quit()
  {
    setcookie('mortgage', '0', time() - 3600, '/');
    $this->signatory_model->logout();

    $this->jump(MLS_SIGN_URL . "/login/", '数据加载中。。。');

  }

  private function del_search_list_cookie()
  {
    setcookie('sell_list', '', time() - 1, '/');
    setcookie('rent_list', '', time() - 1, '/');
    setcookie('customer_manage', '', time() - 1, '/');
    setcookie('rent_customer_manage', '', time() - 1, '/');
    setcookie('sell_lists_pub', '', time() - 1, '/');
    setcookie('rent_lists_pub', '', time() - 1, '/');
    setcookie('customer_manage_pub', '', time() - 1, '/');
    setcookie('rent_customer_manage_pub', '', time() - 1, '/');
  }

  //找回密码
  public function findpw()
  {
    $data_view = array();
    $data_view['tel400'] = $this->config->item('tel400');

    $action = $this->input->post('action', TRUE);
    if (isset($action) && $action == 'findpw') {
      $phone = $this->input->post('phone', TRUE);
      $validcode = $this->input->post('validcode', TRUE);
      $password = ltrim($this->input->post('password', TRUE));
      $verify_password = trim($this->input->post('verify_password', TRUE));
      if ($phone == '' || $validcode == '' || $password == ''
        || $verify_password == ''
      ) {
        //跳转页面
        echo json_encode(array('result' => 'findpw_error'));
        return false;
      }
      $signatory_sms = $this->signatory_model->get_signatory_sms($action);
      $validcode_id = $signatory_sms->get_by_phone_validcode($phone, $validcode);
      if (!$validcode_id) //没有相关的验证码
      {
        //跳转页面
        echo json_encode(array('result' => 'validcode_error'));
        return false;
      }
      //更新密码 成功返回受影响的行数
      $result = $this->signatory_model->update_password($phone,
        $password, $verify_password);
      if ($result === 'password_not_same') //两次输入的密码不一致
      {
        //跳转页面
        echo json_encode(array('result' => 'password_error'));
        return false;
      } else if ($result === 'non_exist_phone') //是否存在手机号码
      {
        //跳转页面
        echo json_encode(array('result' => 'no_user'));
        return false;
      } else {
        $signatory_sms->validcode_set_esta($validcode_id);
        //更改用户密码
        echo json_encode(array('result' => 'findpw_success'));
        return false;
      }
    } else {
      //渲染找回密码页面数据
      $this->load->view('findpw', $data_view);
    }
  }
}
/* End of file login.php */
/* Location: ./application/mls_guli/controllers/login.php */
