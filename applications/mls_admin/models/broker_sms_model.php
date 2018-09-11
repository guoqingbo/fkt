<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of broker_sms_model
 * 经纪人验证码
 * @author ccy
 */
class Broker_sms_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 经纪人验证码查找
   * 根据经纪人手机号码查找
   */
  function select_broker_sms($phone = '')
  {
    if ($phone == '') {
      // return $this->dbback->get(broker_sms)->result_array();
      return FALSE;
    } else {
      $this->dbback->where_in('phone ', $phone);
      $this->dbback->order_by('id', 'desc');
      $this->dbback->limit(1);
      return $this->dbback->get(broker_sms)->result_array();
    }
  }
}
