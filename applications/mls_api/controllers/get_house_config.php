<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Get_house_config extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $city = $_GET['city'];
    //设置成熟参数
    $this->set_city($city);
    //加载区属板块模型类
    $this->load->model('get_house_config_model');
    $data = $this->get_house_config_model->get_config_xffx();
    echo serialize($data);
  }
}
