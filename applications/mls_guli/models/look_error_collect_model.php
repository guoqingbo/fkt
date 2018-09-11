<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Look_error_collect_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->error_collect = 'error_collect'; //采集中断列表详情
  }

  //获取失败信息
  public function get_error_collect($id)
  {
    $where = array('id' => $id);
    $result = $this->get_data(array('form_name' => $this->error_collect, 'where' => $where), 'db');
    return $result;
  }
}
