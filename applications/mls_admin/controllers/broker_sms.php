<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of broker_sms
 * 经纪人验证码
 * @author ccy
 */
class Broker_sms extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('broker_sms_model');
  }

  /**
   * 经纪人验证码查找
   */
  function index()
  {
    $phone = $this->input->post('phone');
    if (!empty($phone)) {
      $data['broker_sms'] = $this->broker_sms_model->select_broker_sms($phone);
    } else {
      //  $data['broker_sms'] = $this->broker_sms_model->select_broker_sms();
    }
    // print_r($data['broker_sms']);
    $this->load->view('broker_sms/index', $data);
  }
}
