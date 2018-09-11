<?php

/**
 * 统计每日房源发布量
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      yuan
 */
class Stat_publish extends MY_Controller
{
  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('notice_access_model', 'na');
  }

  /**
   * @param string $city 城市
   */
  public function index()
  {
    $city = $this->input->get('city');
    $city = trim($city);
    if (!empty($city) && is_string($city)) {
      $this->set_city($city);
      $this->load->model('stat_publish_model');//出售房源
      $start_time = date("Y-m-d", strtotime("-1 day"));
      $start_time_format = strtotime($start_time . ' 00:00:00');
      $end_time_format = strtotime($start_time . ' 23:59:59');
      $where_cond = 'createtime >= ' . $start_time_format . ' and createtime <= ' . $end_time_format;
      $sell_num = $this->stat_publish_model->get_num_for_sell($where_cond);
      $rent_num = $this->stat_publish_model->get_num_for_rent($where_cond);

      $data = array(
        'sell_num' => $sell_num,
        'rent_num' => $rent_num,
        'ymd' => $start_time
      );
      $this->stat_publish_model->add_daily_count($data);
      $this->na->post_job_notice('录入房源统计—sell:' . $sell_num . '_rent:' . $rent_num);
      echo 'over';
    }
  }
}
