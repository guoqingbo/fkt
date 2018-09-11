<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Group_refresh extends My_Controller
{
  public function __construct()
  {
    parent::__construct();
    //$this->load->model('city_model');
    //$cities = $this->city_model->get_all_city();

    $city = $this->input->get('city');
// 		if(empty($city)){
// 		    $city = 'nj';
// 		}
    $this->set_city($city);
  }

  //预约刷新 定时任务
  public function demon()
  {
    $this->load->model('site_mass_model');
    $this->load->model('site_wuba_model');     //58
    $this->load->model('site_ganji_model');    //赶集
    $this->load->model('site_fang_model');     //搜房帮
    $this->load->model('group_refresh_model');

// 	    $time = strtotime('2016-6-2'); //+++++++++测试用++++++
    $time = time();
    $queue = $this->group_refresh_model->get_refresh_time($time);   //执行队列
    $mass_site = $this->site_mass_model->get_mass_site();      //站点信息

    foreach ($queue as $val) {
      $site_id = $val['site_id'];
      $broker_id = $val['broker_id'];
      $house_id = $val['house_id'];
      $act = $val['tbl'] == 1 ? 'sell' : 'rent';

      $temp_id = $val['id']; //删除用
      unset($val['id']);  //防止入库
      switch ($mass_site[$site_id]['alias']) {
        case 'ganjivip':
          $refresh = $this->site_ganji_model->refresh_vip($broker_id, $house_id, $act, $val);
          break;
        case '58W':
          $refresh = $this->site_wuba_model->refresh_vip($broker_id, $house_id, $act, $val);
          break;
        case 'fang':
          $refresh = $this->site_fang_model->refresh($broker_id, $house_id, $act, $val);
          break;
      }
      $delete = $this->group_refresh_model->delete_queue($temp_id);
    }
  }

}

