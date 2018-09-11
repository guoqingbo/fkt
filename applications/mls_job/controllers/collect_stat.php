<?php

/**
 * 每日房源统计
 * 2015.8.4
 * cc
 */
class Collect_stat extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('notice_access_model', 'na');
  }

  public function index()
  {
    $city = $this->input->get('city');
    $this->set_city($city);
    $this->load->model('collect_model');
    $starttime = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
    $endtime = $starttime + 86399;
    $data = date('Y-m-d', $starttime);
    $city_detail = $this->collect_model->collect_city_byspell($city);
    $where = array('createtime >=' => $starttime, 'createtime <=' => $endtime, 'city' => $city_detail['id']);
    $ressell = $this->collect_model->get_sell_house_collect($where);
    $resrent = $this->collect_model->get_rent_house_collect($where);
    $data = array(
      'sell_num' => count($ressell),
      'rent_num' => count($resrent),
      'ymd' => $data
    );
    $this->load->model('autocollect_model');
    $result = $this->autocollect_model->add_stat_collect($data, $database = 'db_city');
    $this->na->post_job_notice('采集量统计—sell:' . $data['sell_num'] . '_rent:' . $data['rent_num']);
    echo 'over';
  }

}
