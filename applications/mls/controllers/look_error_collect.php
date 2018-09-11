<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * 自动采集检测
 * 2016.3.2
 * cc
 */
class Look_error_collect extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('look_error_collect_model'); //自动采集控制器类
  }

  public function info($id)
  {
    $result = $this->look_error_collect_model->get_error_collect($id);
    echo str_replace("*", " * ", $result[0]['error']) . " —[" . date('Y-m-d H:i:s', $result[0]['createtime']) . "]";
  }

}
