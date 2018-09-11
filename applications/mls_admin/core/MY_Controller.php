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
 * @copyright       Copyright (c) 2006 - 2012
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

  /**
   * Constructor
   * @param string $type 区分不同模块来加载不同的共用函数、类库（备用扩展）
   */
  public function __construct($type = NULL)
  {
    parent::__construct();
    $this->checkAuth();

    //加载memcached
    $this->load->library('My_memcached', '', 'mc');
    //记录用户登录以后的行为轨迹
    $this->load->model('admin_operate_log_model');
    $this->admin_operate_log_model->add_log();
  }

  /**
   * 页面跳转中间页
   * @param string $url 跳转URL地址
   * @param string $msg 页面方案
   * @param int $time 间隔时间点
   */
  public function jump($url, $msg = '', $time = 2000)
  {
    $time = intval($time);
    $domain = str_replace("http://", "", MLS_ADMIN_URL);
    $goto_url = '' != $url ? $url : $domain;

    if ('' != $msg && $time > 0) {
      $data_arr['url'] = $goto_url;
      $data_arr['msg'] = $msg;
      $data_arr['time'] = $time;
      //调用跳转页面
      $this->load->view('jump', $data_arr);
    } else {
      redirect($goto_url);
    }
  }

  /**
   * gotoUrl($url)
   *
   * @param str $url
   */
  public function gotoUrl($url, $msg = '')
  {
    echo "<p>" . $msg . "</p>";
    echo "<script>";
    echo "setTimeout(function(){if(parent.window.location.href != ''){parent.window.location.href='" . $url . "';} window.location.href='" . $url . "';}, 1000);";
    echo "</script>";
    exit;
  }

  /**
   * checkAuth($pageid)
   *
   * @param int $pageid
   */
  public function checkAuth()
  {
    if (!isset($_SESSION[WEB_AUTH]["uid"])) {
      $this->gotoUrl(FRAME_LOGIN, "请先登录！");
      exit;
    }
  }

}
/* End of file MY_Controller.php */
/* Location: ./applications/core/MY_Controller.php */
