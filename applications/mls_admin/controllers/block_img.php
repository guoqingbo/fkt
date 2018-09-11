<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Block_img extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('block_img_model');//楼盘模型类
    $this->load->helper('user_helper');
  }

  /**
   *
   */
  public function index()
  {
    $img_data = $this->block_img_model->get_img(130000, 5000);
    $cmt_img_arr = array();
    foreach ($img_data as $k => $v) {
      $arr['id'] = '"' . $v['id'] . '"';
      $arr['cmt_id'] = '"' . $v['bi_blockid'] . '"';
      $arr['image'] = '"' . $v['bi_img'] . '"';
      $arr['big_image'] = '"' . $v['bi_big_img'] . '"';
      $arr['title'] = '"' . $v['bi_title'] . '"';
      $arr['pic_type'] = '"' . $v['bi_type'] . '"';
      $arr['is_surface'] = 0;
      $arr['room'] = '"' . $v['bi_room'] . '"';
      $arr['hall'] = '"' . $v['bi_hall'] . '"';
      $arr['toilet'] = '"' . $v['bi_toilet'] . '"';
      $arr['remark'] = '"' . $v['remark'] . '"';
      $arr['creattime'] = '"' . $v['bi_add_time'] . '"';
      $arr['uid'] = '"' . $v['brokeruid'] . '"';
      $cmt_img_arr[] = $arr;
    }
    foreach ($cmt_img_arr as $k => $v) {
      $_sql = 'INSERT INTO `cmt_img` (' . implode(",", array_keys($v)) . ') VALUES (' . implode(",", array_values($v)) . ');';
      echo $_sql . '<br>';
      $_sql = '';
    }
    //print_r($cmt_img_arr);
  }

}
