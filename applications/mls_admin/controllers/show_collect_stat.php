<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * 二手房采集内容管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class Show_collect_stat extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('show_collect_stat_model');
  }

  /**
   * 二手房采集内容列表页面
   */
  public function index()
  {
    $data_view = array();
    $pg = $this->input->post('pg');
    $data_view['title'] = '房源采集量统计';
    $data_view['conf_where'] = 'index';
    $where = 'id > 0';
    //设置时间条件
    $start_time = $this->input->post('start_time');
    $end_time = $this->input->post('end_time');
    if ($start_time && $end_time == null) {
      $where .= " and ymd >= '" . $start_time . "'";
    } else if ($start_time == null && $end_time) {
      $where .= " and ymd <= '" . $end_time . "'";
    } else if ($start_time && $end_time) {
      $where .= " and ymd >= '" . $start_time . "' and ymd <= '" . $end_time . "'";
    }
    //记录搜索过的条件
    $data_view['where_cond'] = array(
      'start_time' => $start_time, 'end_time' => $end_time
    );
    //分页开始
    $data_view['count'] = 20;
    $data_view['pagesize'] = 20; //设定每一页显示的记录数
    $data_view['count'] = $this->show_collect_stat_model->show_collect_stat_sum($where);
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count'] / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages'] && $data_view['pages'] != 0) ? $data_view['pages'] : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //经纪人列表
    $show_collect_stat = $this->show_collect_stat_model->get_show_collect_stat($where, $data_view['offset'], $data_view['pagesize']);
    //搜索配置信息
    $data_view['show_collect_stat'] = $show_collect_stat;

    $this->load->view('stat/show_collect_stat', $data_view);
  }

}

/* End of file sell_house_collect.php */
/* Location: ./application/controllers/sell_house_collect.php */
