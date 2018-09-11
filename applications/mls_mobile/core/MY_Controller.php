<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS房源管理系统
 *
 * 基于Codeigniter的经纪人房源管理系统
 *
 * MLS房源管理系统是服务于房产经纪人的后台房源管理系统
 *
 *
 * @package         applications
 * @author          xz
 * @copyright       Copyright (c) 2006 - 2012.
 * @version         4.0
 */

// ------------------------------------------------------------------------

/**
 * 逻辑控制器类
 * （Codeigniter所有的控制器都必须继承CI_Controller类，但CI_Controller类位于system目录下，
 *  不方便修改，所以创建MY_Controller，用来继承CI_Controller）
 *
 * 所有的控制器都继承MY_Controller， MY_Controller加载一些公用帮助函数、公用类库等
 *
 * @package         admincp
 * @subpackage      core
 * @category        MY_Controller
 * @author          xz
 */
class MY_Controller extends CI_Controller
{
  protected $user_arr = array();     //已登录用户信息

  protected $user_permission = array(); //用户权限

  protected $company_basic_arr = array(); //当前经济人所在公司基本设置信息

  private function __html()
  {
    //$this->load->library('log/Log');
    if ($this->input->get_post('scode')) {
      $data = array(
        'scode' => $this->input->get_post('scode'),
        'deviceid' => $this->input->get_post('deviceid'),
        'token' => $this->input->get_post('token'),
      );
      //Log::record('写入cookie', $data, 'my_controller');
      //$this->session->set_userdata($data);
      $this->input->set_cookie("scode", $data['scode'], 7200);
      $this->input->set_cookie("deviceid", $data['deviceid'], 7200);
      $this->input->set_cookie("token", $data['token'], 7200);
      unset($data);
    } else {
      $_GET['scode'] = $_POST['scode'] = $this->input->cookie('scode');
      $_GET['deviceid'] = $_POST['deviceid'] = $this->input->cookie('deviceid');
      $_GET['token'] = $_POST['token'] = $this->input->cookie('token');

      //Log::record('读取cookie get', $_GET, 'my_controller');
      //Log::record('读取cookie post', $_POST, 'my_controller');
    }
  }

  private function error($result, $msg = '', $data = '')
  {
    $rtn_data = array();
    $rtn_data['result'] = $result;
    $rtn_data['msg'] = $msg;
    if (is_array($data)) {
      $rtn_data['data'] = $data;
    } else {
      $rtn_data['data'] = array();
    }
    $rtn_data['data'] = json_encode($rtn_data['data']);
    $this->load->view('wap/error', $rtn_data);
    $this->load->output->_display();
    exit;
  }

  /**
   * Constructor
   * @param string $type 区分不同模块来加载不同的共用函数、类库（备用扩展）
   */
  public function __construct($type = NULL)
  {

    parent::__construct();

    if ('html' == $type) {
      $this->__html();
    }
    //判断是否在线，非在线状态将自动跳转登录
    $this->_check_online($type);

    //加载memcached
    $this->load->library('My_memcached', '', 'mc');

    if (is_full_array($this->user_arr)) {
//            权限功能api
//            $this->load->model('api_broker_permission_model');
//            初始化用户的菜单
//            $this->_init_menu_permission();
//            判断用户单点登录
//
      //初始化当前经纪人所在公司的基本设置信息
      $this->_init_company_info();
    }
  }

  /**
   * Init user info, this function is just let us get user's info easier
   */
  private function _init_user_info($broker)
  {
    if (is_full_array($broker)) {
      //把用户信息赋给user_arr，方便使用
      $this->load->model('api_broker_model');

      $broker_info = $this->api_broker_model->
      get_baseinfo_by_broker_id($broker[0]);
      $broker_info['city_spell'] = $broker[1];
      $broker_info['city_id'] = $broker[2];
      $this->config->set_item('broker_info', $broker_info);
      $this->user_arr = $broker_info;
    }
  }

  /**
   * 初始化当前经纪人所在公司的基本设置
   */
  private function _init_company_info()
  {
    $this_broker_data = $this->user_arr;
    $agency_id = intval($this_broker_data['agency_id']);
    if (is_int($agency_id) && !empty($agency_id)) {
      $this->load->model('agency_basic_setting_model');
      $company_basic_data = $this->agency_basic_setting_model->get_data_by_agency_id($agency_id);
      if (is_array($company_basic_data) && !empty($company_basic_data)) {
        $this->company_basic_arr = $company_basic_data[0];
      } else {
        $company_basic_default_data = $this->agency_basic_setting_model->get_default_data();
        if (is_array($company_basic_default_data) && !empty($company_basic_default_data)) {
          $this->company_basic_arr = $company_basic_default_data[0];
        }
      }
    }

  }

  /**
   * 判断功能是否有权限
   * @param string $class 类名
   * @param string $method 方法名
   * @return ['area' : 1(1本人，2门店，3公司), 'auth' => 1（1有权限 0 无权限）]
   */
  public function get_func_permission($class, $method)
  {
    return array('area' => 1, 'auth' => 1);//$this->api_broker_permission_model->get_func_permission($class, $method);
  }

  /**
   * 批量判断功能是否有权限
   * @param array $arr_func 功能数组 array(array('class' => 'sell', 'method' => 'add')))
   * @return [['area' : 1(1本人，2门店，3公司), 'auth' => 1（1有权限 0 无权限）, 'class' => 'sell', 'method' : 'list'],
   * ['area' : 1(1本人，2门店，3公司), 'auth' => 1（1有权限 0 无权限）, 'class' => 'sell', 'method' : 'add']]
   */
  public function get_batch_func_permission($arr_func)
  {
    return $this->api_broker_permission_model->get_batch_func_permission($arr_func);
  }

  /**
   * Check online function
   * If you want to make someone pass this check all the time,
   * you just need to add it in the config array.
   */
  private function _check_online($type = null)
  {
    //定位
    $router = where_am_i();
    $class = $router['class'];
    $method = $router['method'];
    //加载配置
    $no_login_check_arr = $this->config->item('no_login_check');
    //加载broker模型类
    $this->load->model('broker_model');
    //是否需要登录
    $is_check_func = isset($no_login_check_arr[$class])
      && is_array($no_login_check_arr[$class])
      && !empty($no_login_check_arr[$class])
      && in_array($method, $no_login_check_arr[$class]);
    $require_method = $_SERVER['REQUEST_METHOD'];
    $scode = '';
    $deviceid = '';
    $token = '';
    if ($require_method == 'GET') {
      $scode = $this->input->get('scode', true);
      $deviceid = $this->input->get('deviceid', true);
      $token = $this->input->get('token', true);
    } else if ($require_method == 'POST') {
      $scode = $this->input->post('scode', true);
      $deviceid = $this->input->post('deviceid', true);
      $token = $this->input->get('token', true);
    }
    if ($class == 'help' && $method == 'check_version') {
      $is_check_func = true;
    }
    //判断是否在线
    $check_online = $is_check_func ? array('result' => 1)
      : $this->broker_model->check_online($scode);
    //不在线跳出登录
    if ($check_online['result'] == 0) {
      //跳转到登录页面
      if ('html' == $type) {
        $this->error(0, '尚未登录');
        exit;
      } else {
        $this->result(0, '尚未登录');
        exit;
      }
    } else {
      if (!$is_check_func) {
        //为user_arr属性复制
        $this->_init_user_info($check_online['data']);
        $this->load->model('broker_online_app_model');
        $broker_id = $this->user_arr['broker_id'];
        $device = $this->broker_online_app_model->get_by_broker_id($broker_id);
        if (!is_full_array($device) || $device[0]['scode'] != $scode) {

          if ('html' == $type) {
            $this->error(-2, '该帐号已在其它地方登录');
            die();
          } else {
            $this->result(-2, '该帐号已在其它地方登录');
            die();
          }
        } else {
          $dateline = time();

          //更新token值
          if ($token && $device[0]['token'] == '') {
            $this->broker_online_app_model->update_token_by_broker_id($broker_id, $token, $dateline);
          }

          //app access log
          $ymd = date('Y-m-d');
          $y = date('Y');
          $h = date('H');
          $cityid = 0;
          $dateline = time();

          if ($device[0]['city'] != '') {
            $this->load->model('city_model');
            $citydata = $this->city_model->get_city_by_spell($device[0]['city']);
            $cityid = intval($citydata['id']);
          }

          $devicetype = $device[0]['devicetype'] == 'iPhone' ? 1 : 0;
          $data = array('ymd' => $ymd, 'city' => $cityid, 'broker_id' => $device[0]['broker_id'], 'devicetype' => $devicetype, 'deviceid' => $device[0]['deviceid'], 'y' => $y, 'h' => $h, 'dateline' => $dateline);

          $this->broker_online_app_model->add_access_log($data);


          //记录每日访问设备基础数据
          $this_broker_data = $this->user_arr;
          $company_id = intval($this_broker_data['company_id']);
          $agency_id = intval($this_broker_data['agency_id']);
          $data = array('ymd' => $ymd, 'city' => $cityid, 'deviceid' => $device[0]['deviceid'],
            'devicetype' => $devicetype, 'broker_id' => $device[0]['broker_id'], 'dateline' => $dateline,
            'company_id' => $company_id, 'agency_id' => $agency_id, 'year' => $y);
          $this->broker_online_app_model->add_broker_app_daily($data);
        }
      }
    }
  }

  /**
   * 返回没有登录的数据结构
   */
  public function no_login_result()
  {
    $this->result(0, '尚未登录');
  }

  /**
   * 结果返回
   * @param int $status 状态
   * @param array $data 数据
   * @param string $msg 描述
   */
  public function result($result, $msg = '', $data = '')
  {
    $rtn_data = array();
    $rtn_data['result'] = $result;
    $rtn_data['msg'] = $msg;
    if (is_array($data)) {
      $rtn_data['data'] = $data;
    } else {
      $rtn_data['data'] = array();
    }
      echo json_encode($rtn_data, JSON_UNESCAPED_UNICODE);
  }
}
/* End of file MY_Controller.php */
/* Location: ./applications/core/MY_Controller.php */
