<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cooperate_friends_apply extends My_Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  //朋友圈申请时限72小时，超过判定过期
  public function refresh()
  {
    $city = $this->input->get('city');

    $this->load->model('city_model');//房源查看模型类
    $citydata = $this->city_model->get_city_by_spell($city);
    $cityid = intval($citydata['id']);
    unset($citydata);

    if ($cityid > 0) {
      $this->set_city($city);
      $this->load->model('cooperate_friends_base_model');
      $now_time = time();
      $apply_arr = $this->cooperate_friends_base_model->get_apply_list_by_cond(array('status' => 0), 0, 0);
      //print_r($apply_arr);exit;
      if (is_full_array($apply_arr)) {
        foreach ($apply_arr as $key => $vo) {
          if (($now_time - $vo['createtime']) > 3600 * 24 * 3) {
            $this->cooperate_friends_base_model->update_apply(array('status' => 3, 'updatetime' => time()), array('id' => $vo['id']));
            $this->cooperate_friends_base_model->update_friend_message(array('status' => 3, 'updatetime' => time()), array('id' => $vo['msg_f_id']));
          }
        }
      }
    }

  }

}

