<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 委托房源接口
 *
 * 用于MLS房源委托功能数据处理
 *
 *
 * @package         applications
 * @author          fisher
 * @copyright       Copyright (c) 2006 - 2015
 * @version         1.0
 */
class Xffx extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Index Page for this controller.
   */
  public function index()
  {
    $this->result(1, 'Xffx API for MLS.');
  }

  /**
   * 新房分销-发站内信
   */
  public function message()
  {
    $post_param = $this->input->post(NULL, TRUE);
    //设置城市参数
    $city = ltrim($post_param['city']);
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    $this->load->model('message_model');
    $broker_id = $post_param['ag_id'];
    $broker_name = $post_param['ag_name'];
    $case = $post_param['case'];
    $url = $post_param['cpId'];
    $n_params = array(
      'name' => $post_param['cm_name'],
      'lp' => $post_param['lp_name']
    );
    $this->load->model('message_base_model');
    echo $this->message_base_model->add_message($case, $broker_id, $broker_name, $url, $n_params);
  }

  //新房分销推送
  public function new_loupan_push()
  {
    $post_param = $this->input->post(NULL, TRUE);
    //设置城市参数
    $city = ltrim($post_param['city']);
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    $message = $post_param['message'];
    $this->load->model('broker_online_app_model');
    $brokers = $this->broker_online_app_model->get_all_by_city($city);
    $this->load->model('push_func_model');//自动采集控制器类
    if (is_full_array($brokers)) {
      foreach ($brokers as $v) {
        //发送推送消息
        $this->push_func_model->send(1, 3, 1, 0, $v['broker_id'], array(), array(), $message);
      }
    }
  }

  //报备状态变更
  public function project_change_status()
  {
    $post_param = $this->input->post(NULL, TRUE);
    //设置城市参数
    $city = ltrim($post_param['city']);
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    $broker_id = $post_param['broker_id'];
    $func = $post_param['func'];
    $msg_id = $post_param['msg_id'];
    $replace_param = $post_param['replace_param'];
    $this->load->model('push_func_model');
    //发送推送消息
    $this->push_func_model->send(1, 4, $func, 0, $broker_id, array('msg_id' => $msg_id),
      $replace_param);
  }
}

/* End of file xffx.php */
/* Location: ./application/controllers/xffx.php */
