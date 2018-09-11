<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of relation_tp_street
 *
 * @author 365
 */
class Relation_tp_street extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('relation_tp_street_model');
  }

  /**
   * 更新第三方板块数据id
   * type 1五八2赶集
   * PD搞 关联58赶集（用于群发）
   */
  function relation_street($type = 1)
  {
    $tp_street = $this->relation_tp_street_model->select_tp_street(array('tp_type' => $type));
    //print_r($tp_street);
    foreach ($tp_street as $value) {
      $result = $this->relation_tp_street_model->select_street($value['street_name']);
      if ($type == 1) {
        $data = array('wuba_dist_id' => $value['dist_id'], 'wuba_street_id' => $value['street_id']);//58
      } else {
        $data = array('ganji_dist_id ' => $value['dist_id'], 'ganji_street_id ' => $value['street_id']);//gj
      }
      if ($result) {
        $this->relation_tp_street_model->update_street($result[0]['id'], $data);
      }
    }
  }
  //复制板块数据到关联表中
  /**
   * 采集完区属板块后执行
   */
  function relation_tp_street()
  {
    for ($i = 1; $i <= 79; $i++) {
      $street = $this->relation_tp_street_model->select_streets($i);
      $data['street_id'] = $street['id'];
      $data['street_name'] = $street['streetname'];
      $data['dist_id'] = $street['dist_id'];
      $this->relation_tp_street_model->add_relation_street($data);
    }
  }
}
