<?php

/**
 * 每日房源发布量查询
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
    $this->load->model('stat_publish_model');
  }

  /**
   * @param string $city 城市
   */
  public function index($city = "")
  {
    $data_view = array();
      //$this->load->helper('page_helper');
      //$pg = $this->input->post('pg');
    $data_view['conf_where'] = 'index';
    $nowtime = time();
    //引入经纪人基本类库
      //$this->load->model('broker_info_model');
    $where = 'id > 0';
    //设置时间条件
    $start_time = $this->input->post('start_time');
    $end_time = $this->input->post('end_time');
      $where_arr = array();
    if ($start_time) {
        $where .= ' and ymd >= "' . strtotime($start_time . ' 0:0:0') . '"';
        $where_arr['start_time'] = strtotime($start_time . ' 0:0:0');
    } else {
        $where_arr['start_time'] = 0;
    }
    if ($end_time) {
        $where .= ' and ymd <= "' . strtotime($end_time . ' 23:59:59') . '"';
        $where_arr['end_time'] = strtotime($end_time . ' 23:59:59');
    } else {
        $where_arr['end_time'] = time();
    }
    //分页开始
//    $data_view['pagesize'] = 20; //设定每一页显示的记录数
//    $data_view['count'] = $this->stat_publish_model->count_data_by_cond($where);
//    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
//      / $data_view['pagesize']) : 0;  //计算总页数
//    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
//    $data_view['page'] = ($data_view['page'] > $data_view['pages']
//      && $data_view['pages'] != 0) ? $data_view['pages']
//      : $data_view['page'];  //判断跳转页数
//    //计算记录偏移量
//    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //经纪人列表

      $stat_publish_data = $this->stat_publish_model->get_stat_house($where_arr);
      //以门店为组，统计出租出售房源的发布量

    //搜索配置信息
    $data_view['stat_publish_data'] = $stat_publish_data;
    $this->load->view('stat/stat_publish', $data_view);
  }
}
