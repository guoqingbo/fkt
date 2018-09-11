<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 登录控制器
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      fisher
 */
class User_request extends MY_Controller
{

  //设备编号
  private $deviceid = '';
  //更新在线状态时间差
  private $livetime = 20;

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('broker_model');
    $this->load->model('checknotice_model');
  }

  //心跳守护
  public function index($deviceid, $osid)
  {
    $deviceid = addslashes($deviceid);
    $osid = intval($osid);

    $this->load->model('user_request_model');
    //此处需要优化，可考虑用MC减少心跳导致的数据库查询频率
    $infoarr = $this->user_request_model->get_online_info($deviceid, $osid);
    if (isset($infoarr[0]['brokerid']) && $infoarr[0]['brokerid'] > 0) {
      $checktime = time() - $this->livetime;
      if (isset($infoarr[0]['dateline']) && $checktime > $infoarr[0]['dateline']) {
        $this->user_request_model->update_online_state($osid);
      }
      echo "hello";
    } else {
      echo "login_at_other_pc";
    }
  }

  //退出登录
  public function quit($deviceid, $osid)
  {
    $this->user_request_model->delete_online_state($deviceid, $osid);

    $this->broker_model->logout();

    echo "bye";
  }

  //傲娇的通告页
  public function checknotice()
  {
    //先做单点登录判断
    $broker_session = $this->broker_model->get_user_session();
    $deviceid = $broker_session['deviceid'];
    $osid = $broker_session['osid'];

    //web端用户无设备ID
    if ($deviceid == '' && $osid == '') {
      $infoarr[0]['brokerid'] = $broker_session['broker_id'];
    } else {
      $this->load->model('user_request_model');
      //此处需要优化，可考虑用MC减少心跳导致的数据库查询频率
      $infoarr = $this->user_request_model->get_online_info($deviceid, $osid);
    }

    if (isset($infoarr[0]['brokerid']) && $infoarr[0]['brokerid'] > 0) {
      //获取当前经济人所在公司的基本设置信息
      $company_basic_data = $this->company_basic_arr;
      $is_remind_open = $company_basic_data['is_remind_open'];
      $this_user_data = $this->user_arr;
      $this_broker_id = intval($this_user_data['broker_id']);
      //获取弹窗消息
      $pop_message = $this->checknotice_model->get_new_pop_message($this_broker_id);
      if ('1' == $is_remind_open) {
        if (!empty($pop_message)) {
          $result = array(
            'msg' => 'ok',
            'message_array' => $pop_message
          );
        } else {
          $result = array(
            'msg' => 'failed',
          );
        }
      } else {
        $result = array(
          'msg' => 'failed',
        );
      }
    } else {
      $result = array(
        'msg' => 'login_at_other_pc',
      );
    }

    echo json_encode($result);
  }

  //登录时弹出任务提醒
  public function login_checknotice()
  {
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    $is_remind_open = $company_basic_data['is_remind_open'];
    $this_user_data = $this->user_arr;
    $this_broker_id = intval($this_user_data['broker_id']);
    //获取弹窗消息
    $pop_message = $this->checknotice_model->get_new_pop_message($this_broker_id);
    if ('1' == $is_remind_open) {
      if (!empty($pop_message)) {
        $result = array(
          'msg' => 'ok',
          'message_array' => $pop_message
        );
      } else {
        $result = array(
          'msg' => 'failed',
        );
      }
    } else {
      $result = array(
        'msg' => 'failed',
      );
    }
    echo json_encode($result);
  }

  //消息不弹窗更新
  public function update_message_pop($id)
  {
    $update_array = array('is_pop_open' => 1);
    $info = $this->checknotice_model->update_by_id($update_array, $id);
    $this_user_data = $this->user_arr;
    $this_broker_id = intval($this_user_data['broker_id']);
    if ($info == 1) {
      $pop_message = $this->checknotice_model->get_new_pop_message($this_broker_id);
      if (!empty($pop_message)) {
        $result = array(
          'msg' => 'ok',
          'message_array' => $pop_message
        );
      } else {
        $result = array(
          'msg' => 'failed',
        );
      }
    } else {
      $result = array(
        'msg' => 'failed',
      );
    }
    echo json_encode($result);
  }

  public function update_all_message_pop()
  {
    $list = $this->input->get('list');
    if ($list) {
      foreach ($list as $key => $val) {
        $ids[$key] = $val['id'];
      }
      $update_array = array('is_pop_open' => 1);
      $info = $this->checknotice_model->update_by_id($update_array, $ids);
      if ($info !== 0) {
        echo "ok";
      } else {
        echo "failed";
      }
    } else {
      echo "failed";
    }
  }

  //傲娇的通告页
  public function notice($deviceid, $osid)
  {
    $deviceid = addslashes($deviceid);
    $osid = intval($osid);

    $data = array();
    $this->view('notice', $data);
  }

  //登录提交
  public function pclogin($deviceid, $verson, $phone, $password)
  {
    $deviceid = addslashes($deviceid);
    $verson = addslashes($verson);
    $phone = addslashes($phone);
    $password = addslashes($password);

    $result = $this->broker_model->md5login($phone, $password);
    if ($result === 'error_param') //参数不合法
    {
      echo 'login_failure';//echo 'error_param';
    } else if (isset($result) && isset($result['expiretime'])
      && $result['expiretime'] < time()
    ) //帐号到期
    {
      echo 'account_expired';
    } else if (isset($result) && isset($result['status']) //帐号失效
      && $result['status'] == 2
    ) {
      echo 'login_failure';//echo 'account_abate';
    } else if (isset($result) && isset($result['status'])
      && $result['status'] == 1
    ) //登录成功
    {
      $broker_id = $result['id'];
      //判断经纪人是属于哪个城市
      $this->load->model('city_model');
      $city = $this->city_model->get_by_id($result['city_id']);
      $this->config->set_item('login_city', $city['spell']);

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

      $this->load->model('user_request_model');
      $online_stat = $this->user_request_model->get_online_state($deviceid, $broker_id);
      if (isset($online_stat[0]['id']) && $online_stat[0]['id'] > 0) {
        $checktime = time() - $this->livetime;
        if (isset($online_stat[0]['dateline']) && $online_stat[0]['dateline'] > $checktime) {
          echo 'you_have_login_on_this_pc';
        } else {
          $this->user_request_model->update_online_state($online_stat[0]['id']);
          echo 'login_success##' . $online_stat[0]['id'] . '##999';//echo 'you_have_login_on_this_pc';
        }
      } else {
        $osid = $this->user_request_model->add_online_state($deviceid, $broker_id, $result['city_id']);
        echo 'login_success##' . $osid . '##999';
      }
    } else {
      echo 'login_failure';
    }
  }

  //密码未加密登录提交
  public function login($deviceid, $verson, $phone, $password)
  {
    $deviceid = addslashes($deviceid);
    $verson = addslashes($verson);
    $phone = addslashes($phone);
    $password = addslashes($password);

    $result = $this->broker_model->login($phone, $password);
    if ($result === 'error_param') //参数不合法
    {
      echo 'login_failure';//echo 'error_param';
    } else if (isset($result) && isset($result['expiretime'])
      && $result['expiretime'] < time()
    ) //帐号到期
    {
      echo 'account_expired';
    } else if (isset($result) && isset($result['status']) //帐号失效
      && $result['status'] == 2
    ) {
      echo 'login_failure';//echo 'account_abate';
    } else if (isset($result) && isset($result['status'])
      && $result['status'] == 1
    ) //登录成功
    {
      $this->load->model('user_request_model');
      $online_stat = $this->user_request_model->get_online_state($deviceid, $result['id']);
      if (isset($online_stat[0]['id']) && $online_stat[0]['id'] > 0) {
        $checktime = time() - $this->livetime;
        if (isset($online_stat[0]['dateline']) && $online_stat[0]['dateline'] > $checktime) {
          echo 'you_have_login_on_this_pc';
        } else {
          $this->user_request_model->update_online_state($online_stat[0]['id']);
          echo 'login_success##' . $online_stat[0]['id'];//echo 'you_have_login_on_this_pc';
        }
      } else {
        $osid = $this->user_request_model->add_online_state($deviceid, $result['id'], $result['city_id']);
        echo 'login_success##' . $osid;
      }
    } else {
      echo 'login_failure';
    }
  }

  //测试版登录提交
  public function testlogin($deviceid, $verson, $phone, $password)
  {
    $deviceid = addslashes($deviceid);
    $verson = addslashes($verson);
    $phone = addslashes($phone);
    $password = addslashes($password);

    $result = $this->broker_model->login($phone, $password);
    if ($result === 'error_param') //参数不合法
    {
      echo 'login_failure';//echo 'error_param';
    } else if (isset($result) && isset($result['expiretime'])
      && $result['expiretime'] < time()
    ) //帐号到期
    {
      echo 'account_expired';
    } else if (isset($result) && isset($result['status']) //帐号失效
      && $result['status'] == 2
    ) {
      echo 'login_failure';//echo 'account_abate';
    } else if (isset($result) && isset($result['status'])
      && $result['status'] == 1
    ) //登录成功
    {
      $this->load->model('user_request_model');
      $online_stat = $this->user_request_model->get_online_state($deviceid, $result['id']);
      if (isset($online_stat[0]['id']) && $online_stat[0]['id'] > 0) {
        $checktime = time() - $this->livetime;
        if (isset($online_stat[0]['dateline']) && $online_stat[0]['dateline'] > $checktime) {
          echo 'you_have_login_on_this_pc';
        } else {
          //获取帐号绑定设备ID
          $userdevice = $this->user_request_model->get_user_deviceid($result['id']);
          if ('' != $userdevice[0]['deviceid']) {
            if ($deviceid == $userdevice[0]['deviceid']) {
              $this->user_request_model->update_online_state($online_stat[0]['id']);
              echo 'login_success##' . $online_stat[0]['id'];
            } else {
              echo 'device_error';
            }
          } else {
            //记录第一登录设备并绑定设备
            $this->user_request_model->set_user_deviceid($result['id'], $deviceid);

            $this->user_request_model->update_online_state($online_stat[0]['id']);
            echo 'login_success##' . $online_stat[0]['id'];
          }
        }
      } else {
        $osid = $this->user_request_model->add_online_state($deviceid, $result['id'], $result['city_id']);
        echo 'login_success##' . $osid;
      }
    } else {
      echo 'login_failure';
    }
  }

  //登录提交
  public function unlock($deviceid, $verson, $phone, $password)
  {
    $deviceid = addslashes($deviceid);
    $verson = addslashes($verson);
    $phone = addslashes($phone);
    $password = addslashes($password);

    $result = $this->broker_model->md5login($phone, $password);
    if ($result === 'error_param') //参数不合法
    {
      echo 'login_failure';//echo 'error_param';
    } else if (isset($result) && isset($result['expiretime'])
      && $result['expiretime'] < time()
    ) //帐号到期
    {
      echo 'account_expired';
    } else if (isset($result) && isset($result['status']) //帐号失效
      && $result['status'] == 2
    ) {
      echo 'login_failure';//echo 'account_abate';
    } else if (isset($result) && isset($result['status'])
      && $result['status'] == 1
    ) //登录成功
    {
      $this->load->model('user_request_model');
      $online_stat = $this->user_request_model->get_online_state($deviceid, $result['id']);
      if (isset($online_stat[0]['id']) && $online_stat[0]['id'] > 0) {
        $this->user_request_model->update_online_state($online_stat[0]['id']);
        echo 'login_success##' . $online_stat[0]['id'];//echo 'you_have_login_on_this_pc';
      } else {
        $osid = $this->user_request_model->add_online_state($deviceid, $result['id'], $result['city_id']);
        echo 'login_success##' . $osid;
      }
    } else {
      echo 'login_failure';
    }
  }

  //验证登录
  public function signin()
  {
    $is_online = $this->broker_model->check_online();
    if ($is_online) //判断是否登录
    {
      //登录成功
      $this->jump(MLS_URL, '主人您好！');
    } else {
      $phone = $this->input->post('phone', TRUE);
      $password = $this->input->post('password', TRUE);
      $result = $this->broker_model->login($phone, $password);
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
      } else if (isset($result) && isset($result['status'])
        && $result['status'] == 1
      ) //登录成功
      {
        echo '登录成功';
        //判断经纪人是属于哪个城市，并初始化相应的数据据
        $this->load->model('city_model');
        $city = $this->city_model->get_by_id($result['city_id']);
        $init_data_session = array('broker_id' => $result['id'],
          'city_spell' => $city['spell']);
        $this->broker_model->set_user_session($init_data_session);
        //获取经纪人详细信息
        $this->load->model('broker_info_model');
          $broker_info = $this->broker_info_model->get_baseinfo_by_broker_id($result['id']);
        //重置session值
        $broker_info['city_spell'] = $city['spell'];
        $this->broker_model->set_user_session($broker_info);
        $this->jump(MLS_URL, '主人您好！');
      } else {
        echo '登录失败';
      }
    }
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
        echo '页面参数不合法，请重新注册';
        die();
      }
      $broker_sms = $this->broker_model->get_broker_sms($action);
      $validcode_id = $broker_sms->get_by_phone_validcode($phone, $validcode);
      if (!$validcode_id) //没有相关的验证码
      {
        //跳转页面
        echo '验证码错误或者过期，请重新获取';
        die();
      }
      //更新密码 成功返回受影响的行数
      $result = $this->broker_model->update_password($phone,
        $password, $verify_password);
      if ($result === 'password_not_same') //两次输入的密码不一致
      {
        //跳转页面
        echo '两次输入的密码不一致';
      } else if ($result === 'non_exist_phone') //是否存在手机号码
      {
        //跳转页面
        echo '不存在此号码';
      } else {
        $broker_sms->validcode_set_esta($validcode_id);
        //更改用户密码
        echo '更改密码成功';
      }
    } else {
      //渲染找回密码页面数据
      $this->load->view('findpw', $data_view);
    }
  }

  public function get_frame_cache($deviceid, $osid)
  {
    $deviceid = addslashes($deviceid);
    $osid = intval($osid);

    $data_view = array();

    $data_view['tel400'] = $this->config->item('tel400');
    //系统标题
    $data_view['title'] = $this->config->item('title');
    //系统菜单
    $data_view['menu'] = $this->config->item('menu');
    //登录信息配置
    $data_view['deviceid'] = $deviceid;
    $data_view['osid'] = $osid;

    $this->frame('welcome', $data_view);
  }
}
/* End of file login.php */
/* Location: ./application/mls/controllers/login.php */
