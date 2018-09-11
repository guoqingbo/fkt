<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * stat_group_publish controller CLASS
 *
 * 自动采集控制器类
 *
 * @package         datacenter
 * @subpackage      controllers
 * @category        controllers
 * @author          lu
 */
class Stat_group_publish extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $city = $this->input->get('city');
    $this->set_city($city);
    $start_time = date("Y-m-d", strtotime("-1 day"));
    $start_time_format = strtotime($start_time . ' 00:00:00');
    $end_time_format = strtotime($start_time . ' 23:59:59');
    $where_cond = 'ymd >= ' . $start_time_format . ' and ymd <= ' . $end_time_format;
    $rent_where = $where_cond . " and sell_type = 2";
    $sell_where = $where_cond . " and sell_type = 1";
    $this->load->model('notice_access_model', 'na');
    $this->load->model('stat_group_publish_model');
    $sell_num = $this->stat_group_publish_model->get_num_for_sell($sell_where);
    if (!$sell_num) {
      $sell_num = 0;
    }
    $rent_num = $this->stat_group_publish_model->get_num_for_rent($rent_where);
    if (!$rent_num) {
      $rent_num = 0;
    }
    $data = array(
      'sell_num' => $sell_num,
      'rent_num' => $rent_num,
      'ymd' => $start_time
    );
    $this->stat_group_publish_model->add_daily_count($data);
    $this->na->post_job_notice('群发量统计-sell:' . $sell_num . '_rent:' . $rent_num);
    echo 'over';
  }
}
/* End of file stat_group_publish.php */
/* Location: ./application/mls_job/controllers/stat_group_publish.php */
