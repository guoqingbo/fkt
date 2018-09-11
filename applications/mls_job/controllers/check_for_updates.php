<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * 自动采集检测
 * 2016.3.2
 * cc
 */
class Check_for_updates extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('check_for_updates_model'); //自动采集控制器类
  }

  /**
   * 2016.3.2
   * cc
   */
  public function index()
  {
    //遍历城市，同时采集数据
    $res = $this->check_for_updates_model->collect_city();
    $result = '';
    foreach ($res as $val) {
      $result .= $this->check_for_updates_model->check_for_updates($val['id']);//传城市id
    }
    if ($result != '') {
      $result = substr($result, 0, strlen($result) - 1);
      $num = count(explode("*", $result));
      $data = array(
        'error' => $result,
        'createtime' => time()
      );
      $id = $this->check_for_updates_model->add_error_collect($data);
//      $this->load->library('Sms_codi', array('city' => 'nj', 'jid' => '1', 'template' => 'check_for_updates'), 'sms');
//            $result = $this->sms->send('15951634202',array('num'=>$num. MLS_JOB_URL . "/look_error_collect/info/".$id));//王欣
//      $result = $this->sms->send('15951987586', array('num' => $num . MLS_JOB_URL . "/look_error_collect/info/" . $id));//陈超
//            $result = $this->sms->send('13851894682',array('num'=>$num. MLS_JOB_URL . "/look_error_collect/info/".$id));//孙兴华
    }
  }

}
