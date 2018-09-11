<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of push_unicast_base_model
 *
 * @author user
 */
load_m("push_record_base_model");

class Push_broadcast_record_base_model extends Push_record_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    $this->set_result_tbl('push_broadcast_record_result');
    parent::__construct();
  }
}
