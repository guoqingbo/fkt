<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller
{

  /**
   * 解析函数
   *
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();

    $submit_flag = $this->input->get_post('submit_flag');

    //================ 判断登录 ================//
    if ($submit_flag != 'logout') {
      if (isset($_SESSION[WEB_AUTH]["uid"]) && $_SESSION[WEB_AUTH]["uid"] > 0) {
        header("Location: " . FRAME_MAIN);
        exit;
      }
    }

    $this->load->helper('user_helper');
    $this->load->model('datacenter_model');
  }

  /**
   * 登录
   */
  public function index()
  {
    $data['title'] = '登录';
    $data['mess_error'] = '';
    $submit_flag = trim($this->input->get_post('submit_flag'));
    $username = trim($this->input->post('username'));
    $password = trim($this->input->post('password'));
    $parmList = array(
      'username' => $username,
      'password' => $password,
      'submit_flag' => $submit_flag
    );

    if ('login' == $submit_flag) {
      if (!empty($parmList['username']) && !empty($parmList['password'])) {
        $remember = $this->input->post('remember');//记住密码
        $result = $this->datacenter_model->getadmin($parmList);
        if ('noResult' == $result) {
          $data['mess_error'] = '<FONT color=red>登录失败！请检查您的登录名和密码。</FONT>';
        } else {
          //判断帐号是否有效
          if ($result['status'] != '1') {
            $data['mess_error'] = '<FONT color=red>帐号失效！</FONT>';
            session_destroy();
          } else {
            if ('Remember Me' == $remember) {
              setcookie('mls_admin_name', $username, time() + 60 * 60 * 24 * 7);
              setcookie('mls_admin_password', $password, time() + 60 * 60 * 24 * 7);
              setcookie('mls_admin_remember', $remember, time() + 60 * 60 * 24 * 7);
            } else {
              setcookie('mls_admin_name');
              setcookie('mls_admin_password');
              setcookie('mls_admin_remember');
            }
            echo gotoUrl(FRAME_MAIN, "登录成功,请稍候......");
          }
        }
      } else {
        $data['mess_error'] = '<FONT color=red>登录失败！用户名/密码不能为空</FONT>';
      }
    } else if ('logout' == $submit_flag) {
      session_destroy();
      echo gotoUrl(FRAME_LOGIN, "你已安全退出～");
      exit;
    }

    $this->load->view('login3', $data);
  }
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */
