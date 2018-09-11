<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 后台发布消息管理
 *
 * @package      mls_admin
 * @subpackage   Controllers
 * @category     Controllers
 * @author       angel_in_us
 * @date         2015-08-05
 */
class House_read_stat extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->helper('user_helper');
    $this->load->model('house_read_stat_model');
  }

  /**
   * 房源查看量统计
   */
  public function index()
  {
    $data['title'] = "房源查看量统计";
    $data['conf_where'] = 'index';

    $data['where_cond'] = array();
    date_default_timezone_set('PRC');
    if ($this->input->post('start_time') && $this->input->post('end_time')) {
      $start_time = $this->input->post('start_time');
      $end_time = $this->input->post('end_time');
      if ($start_time > $end_time) {
        echo "<script>alert('您查询的开始时间不能大于结束时间！');location.href='" . MLS_ADMIN_URL . "/house_read_stat/index';</script>";
      }
      if ($start_time && $end_time) {
        $data['where_cond'] = array('ymd >=' => $start_time, "ymd <=" => $end_time);
      }
    }

    //分页开始
    $data['view_house_num'] = $this->house_read_stat_model->get_num($data['where_cond']);
    $data['pagesize'] = 20;//设定每一页显示的记录数
    $data['pages'] = $data['view_house_num'] ? ceil($data['view_house_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['issue_msg'] = $this->house_read_stat_model->get_issue_msg($data['where_cond'], $data['offset'], $data['pagesize']);
    $this->load->view('stat/house_read_log', $data);
  }
}
