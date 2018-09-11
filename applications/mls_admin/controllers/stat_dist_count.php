<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 用户详细信息类
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Stat_dist_count extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('stat_dist_count_model');
  }

  //门店管理页
  public function index()
  {
    $data_view = array();
    $this->load->helper('page_helper');
    $pg = $this->input->post('pg');
    $data_view['title'] = '核心区域数据统计';
    $data_view['conf_where'] = 'index';
    $nowtime = time();

    $where = 'city = ' . $_SESSION['esfdatacenter']['city_id'];
    //设置时间条件
    $start_time = $this->input->post('start_time');
    $end_time = $this->input->post('end_time');

    if ($start_time && $end_time) {
      $where .= ' and ymd >= "' . $start_time . '" and ymd <= "' . $end_time . '"';
    }

    //记录搜索过的条件
    $data_view['where_cond'] = array(
      'start_time' => $start_time, 'end_time' => $end_time
    );
    //分页开始
    $data_view['pagesize'] = 20; //设定每一页显示的记录数
    $data_view['count'] = $this->stat_dist_count_model->count_by($where);
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //访问量列表
    $dataarr = $this->stat_dist_count_model->get_all_by($where, $data_view['offset'], $data_view['pagesize']);

    if (is_full_array($dataarr)) {
      $district = array();
      $this->load->model('district_model');
      $distarr = $this->district_model->get_district();
      if (is_full_array($distarr)) {
        foreach ($distarr as $dist) {
          $district[$dist['id']] = $dist['district'];
        }
      }

      $data_view['district'] = $district;

      foreach ($dataarr as $value) {
        $data = json_decode($value['data'], TRUE);

        $dist = array();

        $dist['ymd'] = $data['ymd'];

        foreach ($data['agency_num'] as $val) {
          $dist['agency_num'][$val['dist_id']] = $val['num'];
        }


        foreach ($data['rz_jjr_num'] as $val) {
          $dist['rz_jjr_num'][$val['dist_id']] = $val['num'];
        }


        foreach ($data['total_jjr_num'] as $val) {
          $dist['total_jjr_num'][$val['dist_id']] = $val['num'];
        }


        foreach ($data['app_jjr_num'] as $val) {
          $dist['app_jjr_num'][$val['dist_id']] = $val['num'];
        }


        foreach ($data['sell_erp_num'] as $val) {
          $dist['sell_erp_num'][$val['dist_id']] = $val['num'];
          $dist['sell_erp_total'] += $val['num'];
        }


        foreach ($data['rent_erp_num'] as $val) {
          $dist['rent_erp_num'][$val['dist_id']] = $val['num'];
          $dist['rent_erp_total'] += $val['num'];
        }


        foreach ($data['sell_fang100_num'] as $val) {
          $dist['sell_fang100_num'][$val['dist_id']] = $val['num'];
          $dist['sell_fang100_total'] += $val['num'];
        }


        foreach ($data['rent_fang100_num'] as $val) {
          $dist['rent_fang100_num'][$val['dist_id']] = $val['num'];
          $dist['rent_fang100_total'] += $val['num'];
        }


        foreach ($data['sell_coop_num'] as $val) {
          $dist['sell_coop_num'][$val['dist_id']] = $val['num'];
          $dist['sell_coop_total'] += $val['num'];
        }


        foreach ($data['rent_coop_num'] as $val) {
          $dist['rent_coop_num'][$val['dist_id']] = $val['num'];
          $dist['rent_coop_total'] += $val['num'];
        }


        foreach ($data['sell_erp_agency_num'] as $val) {
          $dist['sell_erp_agency_num'][$val['dist_id']] = $val['num'];
        }


        foreach ($data['rent_erp_agency_num'] as $val) {
          $dist['rent_erp_agency_num'][$val['dist_id']] = $val['num'];
        }


        foreach ($data['sell_fang100_agency_num'] as $val) {
          $dist['sell_fang100_agency_num'][$val['dist_id']] = $val['num'];
        }


        foreach ($data['rent_fang100_agency_num'] as $val) {
          $dist['rent_fang100_agency_num'][$val['dist_id']] = $val['num'];
        }


        foreach ($data['sell_coop_agency_num'] as $val) {
          $dist['sell_coop_agency_num'][$val['dist_id']] = $val['num'];
        }


        foreach ($data['rent_coop_agency_num'] as $val) {
          $dist['rent_coop_agency_num'][$val['dist_id']] = $val['num'];
        }


        foreach ($data['coop_cj_agency_num'] as $val) {
          $dist['coop_cj_agency_num'][$val['dist_id']] = $val['num'];
        }


        foreach ($data['coop_cj_broker_num'] as $val) {
          $dist['coop_cj_broker_num'][$val['dist_id']] = $val['num'];
        }


        foreach ($data['coop_cj_new_num'] as $val) {
          $dist['coop_cj_new_num'][$val['dist_id']] = $val['num'];
          $dist['coop_cj_new_total'] += $val['num'];
        }


        $dist['keyarr'][] = array(0, 'agency_num', '各区域合作门店总量');
        $dist['keyarr'][] = array(0, 'total_jjr_num', '各区域经纪人总量');
        $dist['keyarr'][] = array(1, array('rz_jjr_num', 'total_jjr_num'), '各区域经纪人认证占比<br />各区域认证经纪人数量 / 各区域经纪人总量');
        $dist['keyarr'][] = array(1, array('app_jjr_num', 'total_jjr_num'), '各区域使用APP经纪人数量占比<br />各区域使用APP经纪人数量 / 各区域经纪人总量');
        $dist['keyarr'][] = array(2, array('sell_erp_num', 'sell_erp_total'), '各区域ERP出售占比<br />各区域ERP出售总量 / ERP出售总量');
        $dist['keyarr'][] = array(2, array('rent_erp_num', 'rent_erp_total'), '各区域ERP出租占比<br />各区域ERP出租总量 / ERP出租总量');
        $dist['keyarr'][] = array(1, array('sell_fang100_num', 'sell_erp_num'), '各区域出售外网占比<br />各区域出售外网量 / 各区域ERP出售总量');
        $dist['keyarr'][] = array(1, array('rent_fang100_num', 'rent_erp_num'), '各区域出租外网占比<br />各区域出租外网量 / 各区域ERP出租总量');
        $dist['keyarr'][] = array(1, array('sell_coop_num', 'sell_erp_num'), '各区域出售合作占比<br />各区域出售合作房源量 / 各区域ERP出售总量');
        $dist['keyarr'][] = array(1, array('rent_coop_num', 'rent_erp_num'), '各区域出租合作占比<br />各区域出租合作房源量 / 各区域ERP出租总量');
        $dist['keyarr'][] = array(1, array('sell_erp_agency_num', 'agency_num'), '各区域ERP出售覆盖门店占比<br />各区域ERP出售覆盖门店量 / 各区域合作门店总量');
        $dist['keyarr'][] = array(1, array('rent_erp_agency_num', 'agency_num'), '各区域ERP出租门店占比<br />各区域ERP出租覆盖门店量 / 各区域合作门店总量');
        $dist['keyarr'][] = array(1, array('sell_fang100_agency_num', 'agency_num'), '各区域出售外网覆盖门店占比<br />各区域出售外网覆盖门店量 / 各区域合作门店总量');
        $dist['keyarr'][] = array(1, array('rent_fang100_agency_num', 'agency_num'), '各区域出租外网覆盖门店占比<br />各区域出租外网覆盖门店量 / 各区域合作门店总量');
        $dist['keyarr'][] = array(1, array('sell_coop_agency_num', 'agency_num'), '各区域出售合作覆盖门店占比<br />各区域出售合作覆盖门店量 / 各区域合作门店总量');
        $dist['keyarr'][] = array(1, array('rent_coop_agency_num', 'agency_num'), '各区域出租合作覆盖门店占比<br />各区域出租合作覆盖门店量 / 各区域合作门店总量');
        $dist['keyarr'][] = array(1, array('coop_cj_agency_num', 'agency_num'), '各区域成交覆盖门店占比<br />各区域成交覆盖门店量 / 各区域合作门店总量');
        $dist['keyarr'][] = array(1, array('coop_cj_broker_num', 'total_jjr_num'), '各区域成交覆盖经纪人占比<br />各区域成交覆盖经纪人量 / 各区域经纪人总量');
        $dist['keyarr'][] = array(2, array('coop_cj_new_num', 'coop_cj_new_total'), '各区域新增成交房源占比<br />各区域新增成交房源量 / 新增成交房源总量');

        $data_view['dataarr'][] = $dist;
      }
    }

    $this->load->view('stat/stat_dist_count', $data_view);
  }


  //每天登录人数
  public function stat_login_day()
  {
    $data_view['title'] = '经纪人登录量';
    $nowtime = date('Y-m-d');
    $stat_time = $this->input->post('stat_time');
    $data_view['stat_time'] = $stat_time = $stat_time > 0 ? $stat_time : $nowtime;
    $data_view['num'] = $this->stat_login_model->get_day_num($stat_time);

    $this->load->view('stat/stat_login_day', $data_view);
  }
}

/* End of file Broker_info.php */
/* Location: ./application/mls_admin/controllers/Broker_info.php */
