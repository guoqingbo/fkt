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
 * @copyright       Copyright (c) 2006 - 2012, HOUSE365.com.
 * @link            http://nj.zsb.house365.com/
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
  /**
   * Constructor
   * @param string $type 区分不同模块来加载不同的共用函数、类库（备用扩展）
   */
  public function __construct()
  {
    parent::__construct();
  }

  //设置城市参数
  public function set_city($city)
  {
    //$this->check_city_parameter($city);

    $this->config->set_item('login_city', $city);
  }


  //检查设置城市参数
  public function check_city_parameter($city)
  {
    if ($city == '') {
      $this->result(0, 'You need set the city parameter.');
      exit;
    }

    $allow_citys = $this->config->item('allow_citys');
    if (!in_array($city, $allow_citys)) {
      $this->result(0, 'You are not allowed to visit the city.');
      exit;
    }
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
    }
    echo json_encode($rtn_data);
  }

  public function sendsms($telno, $msg)
  {
    $request_url = 'http://sms.fang100.com/send/?mobile=' . $telno . '&msg=' . $msg . '&yzm=100000';

    $ci = &get_instance();
    $ci->load->library('Curl');
    $json = Curl::curl_get_contents($request_url);
    return json_decode($json, true);
  }
}
/* End of file MY_Controller.php */
/* Location: ./applications/core/MY_Controller.php */
