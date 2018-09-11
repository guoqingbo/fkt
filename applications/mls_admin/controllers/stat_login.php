<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 用户详细信息类
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Stat_login extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('stat_login_model');
  }

  //门店管理页
  public function index()
  {
    $data_view = array();
      //$this->load->helper('page_helper');
      //$pg = $this->input->post('pg');
    $data_view['title'] = '经纪人登录量';
    $data_view['conf_where'] = 'index';
    $nowtime = time();

    $where = 'id > 0';
    //设置时间条件
    $start_time = $this->input->post('start_time');
    $end_time = $this->input->post('end_time');

      $where_arr = array();
      if ($start_time) {
          $start_time = $start_time . ' 00:00:00';
          $where_arr['start_time'] = strtotime($start_time);
      } else {
          $where_arr['start_time'] = 0;
      }
      if ($end_time) {
      $end_time = $end_time . ' 23:59:59';
          $where_arr['end_time'] = strtotime($end_time);
//      $where .= ' and ymd >= "' . $start_time . '" and ymd <= "' . $end_time . '"';
      } else {
          $where_arr['end_time'] = time();
    }

    //记录搜索过的条件
    $data_view['where_cond'] = array(
      'start_time' => $start_time, 'end_time' => $end_time
    );
    //分页开始
      //$data_view['pagesize'] = 20; //设定每一页显示的记录数
      //$data_view['count'] = $this->stat_login_model->count_by($where);
      //$data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      //  / $data_view['pagesize']) : 0;  //计算总页数
      //$data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
      //$data_view['page'] = ($data_view['page'] > $data_view['pages']
      //  && $data_view['pages'] != 0) ? $data_view['pages']
      //  : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
      //$data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //访问量列表
      $data_view['stat_login'] = $this->stat_login_model->get_login_num($where_arr);

    $this->load->view('stat/stat_login', $data_view);
  }


  //每天登录人数
  public function stat_login_day()
  {
    $data_view['title'] = '经纪人登录使用量';

    $nowtime = date('Y-m-d');

    $stat_time = $this->input->post('stat_time');
    $data_view['stat_time'] = $stat_time = $stat_time > 0 ? $stat_time : $nowtime;
    $data_view['num'] = $this->stat_login_model->get_day_num($stat_time);

    $nowtime = strtotime(date('Y-m-d'));
    $data_view['timearr'] = $timearr = array('1' => $nowtime, '3' => $nowtime - 86400 * 2, '7' => $nowtime - 86400 * 6, '10' => $nowtime - 86400 * 9, '15' => $nowtime - 86400 * 14, '20' => $nowtime - 86400 * 19, '30' => $nowtime - 86400 * 29);
    $data_view['arr'] = $this->stat_login_table($timearr);

    $this->load->view('stat/stat_login_day', $data_view);
  }

  //登录信息表
  public function stat_login_table($timearr)
  {
    $data_view['title'] = '经纪人登录量综合数据表';

    $cityid = $_SESSION[WEB_AUTH]['city_id'];

    $arr = $this->stat_login_model->get_broker_online_dateline($cityid);

    $this->load->model('broker_info_model');
    $this->broker_info_model->set_select_fields(array('broker_id', 'agency_id'));
    $brokerarr = $this->broker_info_model->get_all_by(array('agency_id <> ' => '0'), 0, count($arr));

    $brokernum = $brokernum2 = $agencynum = $agencyarr = $agencytotalarr = array();

    if (is_full_array($arr) && is_full_array($brokerarr)) {
      foreach ($brokerarr as $key => $broker) {
        $brokerarr[$broker['broker_id']] = $broker;
        unset($brokerarr[$key]);
      }

      foreach ($arr as $key => $value) {
        $brokermax = $value['pc'] >= $value['app'] ? $value['pc'] : $value['app'];
        $agencyid = $brokerarr[$value['id']]['agency_id'];
        if ($agencyid > 0) {
          $agencytotalarr[$agencyid] = isset($agencytotalarr[$agencyid]) ? $agencytotalarr[$agencyid] + 1 : 1;
        }

        foreach ($timearr as $kt => $time) {
          if ($brokermax > $time) {

            if ($agencyid > 0) {
              $brokernum[$kt] = isset($brokernum[$kt]) ? $brokernum[$kt] + 1 : 1;
              $agencyarr[$agencyid][$kt] = isset($agencyarr[$agencyid][$kt]) ? $agencyarr[$agencyid][$kt] + 1 : 1;
            } else {
              $brokernum2[$kt] = isset($brokernum2[$kt]) ? $brokernum2[$kt] + 1 : 1;
            }
            //break;
          }
        }
      }

      if (is_full_array($agencyarr)) {
        foreach ($agencyarr as $agency) {
          foreach ($timearr as $kt => $time) {
            if (isset($agency[$kt])) {
              $agencynum[$kt] = isset($agencynum[$kt]) ? $agencynum[$kt] + 1 : 1;
              //break;
            }
          }
        }
      }

      ksort($brokernum);
      ksort($agencynum);
    }

    return array('brokernum' => $brokernum, 'brokernum2' => $brokernum2, 'agencynum' => $agencynum, 'broker_total' => count($arr), 'broker_agency_total' => count($brokerarr), 'agency_total' => count($agencytotalarr));
  }
}

/* End of file Broker_info.php */
/* Location: ./application/mls_admin/controllers/Broker_info.php */
