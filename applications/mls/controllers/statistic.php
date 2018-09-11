<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统控制器
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

/**
 * Customer Controller CLASS
 *
 * 统计模块控制器
 *
 * @package         MLS
 * @subpackage      Controllers
 * @category        Controllers
 * @author          xz
 */
class Statistic extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
  }


  //全城统计
  public function city_statistic()
  {
    $field = $this->input->post('field', TRUE);//统计字段
    $type = $this->input->post('type', TRUE); //类型
    $unit = $this->input->post('unit', TRUE);//单位
    $start_day = $this->input->post('start_day', TRUE);//开始时间
    $end_day = $this->input->post('end_day', TRUE);//结束时间

    $data = array();
    //查询条件
    $data['type'] = !empty($type) ? $type : 1;
    $data['unit'] = !empty($unit) ? $unit : 1;
    $data['field'] = !empty($field) ? $field : 'add_num';
    $data['start_day'] = !empty($start_day) ? $start_day : date('Y-m-d', strtotime('-1 day'));
    $data['end_day'] = !empty($end_day) ? $end_day : $data['start_day'];

    //统计MODEL
    $this->load->model('city_statistic_model');

    //配置文件
    $config = $this->city_statistic_model->get_base_conf();
    $data['config'] = $config;

    //展示数据
    $show_suffix = $config['field_suffix'][$data['type']][$data['field']];
    $show_type = $config['type'][$data['type']];
    $show_field = $config['fields'][$data['field']];
    $show_unit = $config['unit'][$data['unit']];
    $show_count_type_operate = $config['count_type'][$data['field']]['operate'];
    $show_count_type_name = $config['count_type'][$data['field']]['name'];
    if ($data['unit'] == 1) {
      $this->load->model('district_model'); //区属信息
      $unit_data_arr = $this->district_model->get_district();
    } else if ($data['unit'] == 2) {
      $unit_data_arr = array(
        array('id' => 1, 'name' => '住宅'),
        array('id' => 2, 'name' => '别墅'),
        array('id' => 3, 'name' => '商铺'),
        array('id' => 4, 'name' => '写字楼'),
        array('id' => 5, 'name' => '厂房'),
        array('id' => 6, 'name' => '仓库'),
        array('id' => 7, 'name' => '车库'),
      );
    }

    $dist_num = count($unit_data_arr);

    //全城统计区属数据
    if ($data['unit'] == 1) {
      $statistic_data = $this->city_statistic_model->get_city_statistic_dist_data($data['type'], $data['field'],
        $data['start_day'], $data['end_day']);
    } else if ($data['unit'] == 2) {
      $statistic_data = $this->city_statistic_model->get_build_type_statistic_dist_data($data['type'], $data['field'],
        $data['start_day'], $data['end_day']);
    }

    $statistic_data_temp = array();
    $data_num = count($statistic_data);

    $dist_avg_num = array();//每个区属非零统计的个数
    $avg_num = 0;   //单项添加非零个数
    $total_add = 0; //单项添加之和
    for ($i = 0; $i < $data_num; $i++) {
      //循环区属累加各区属总数据
      for ($j = 0; $j < $dist_num; $j++) {
        if ($data['unit'] == 1) {
          $str = 'dist_id';
        } else if ($data['unit'] == 2) {
          $str = 'build_type';
        }
        if ($statistic_data[$i][$str] == $unit_data_arr[$j]['id']) {
          $statistic_data_temp[$unit_data_arr[$j]['id']] =
            !empty($statistic_data_temp[$unit_data_arr[$j]['id']]) ?
              ($statistic_data_temp[$unit_data_arr[$j]['id']] + $statistic_data[$i][$data['field']]) :
              $statistic_data[$i][$data['field']];

          //如果是平均值计算方式，则需统计各区属统计日期内不为零数据的个数
          if ($statistic_data[$i][$data['field']] > 0) {
            $dist_avg_num[$unit_data_arr[$j]['id']] = !empty($dist_avg_num[$unit_data_arr[$j]['id']]) ?
              ($dist_avg_num[$unit_data_arr[$j]['id']] + 1) : 1;
          } else {
            $dist_avg_num[$unit_data_arr[$j]['id']] = 0;
          }
          break;
        }
      }

      //统计所有区属总量/统计所有区属的平均值
      if ($show_count_type_operate == 'sum') {
        $total_add += $statistic_data[$i][$data['field']];  //新增总数
      } else if ($show_count_type_operate == 'avg') {
        if ($statistic_data[$i][$data['field']] != 0) {
          $total_add += $statistic_data[$i][$data['field']];
          $avg_num++;
        }
      }
    }

    if ($show_count_type_operate == 'sum') {
      $total_add = round($total_add);//新增总数
    } else if ($show_count_type_operate == 'avg') {
      $total_add = $avg_num > 0 ? round($total_add / $avg_num) : 0;//新增平均值

      //如果是平均值的计算方式，
      //则需要根据每个区属的总量除以每个区属不为零数据的个数重新计算平均值
      $data_num = count($statistic_data_temp);
      if ($data_num > 0) {
        foreach ($statistic_data_temp as $key => $value) {
          $statistic_data_temp[$key] = intval($dist_avg_num[$key]) > 0 ?
            $statistic_data_temp[$key] / intval($dist_avg_num[$key]) : 0;
        }
      }
    }

    for ($i = 0; $i < $dist_num; $i++) {
      $x_data_arr[$i] = !empty($statistic_data_temp[$unit_data_arr[$i]['id']]) ?
        intval($statistic_data_temp[$unit_data_arr[$i]['id']]) : 0; //X轴
      if ($data['unit'] == 1) {
        $y_data_arr[$i] = $unit_data_arr[$i]['district'];    //Y轴
      } else if ($data['unit'] == 2) {
        $y_data_arr[$i] = $unit_data_arr[$i]['name'];    //Y轴
      }
    }
    //X轴、Y轴数据
    $data['y_json'] = json_encode($x_data_arr);
    $data['x_json'] = json_encode($y_data_arr);
    //Y轴单位
    $data['show_suffix'] = $show_suffix;

    //标题
    $data['chart_title'] = $show_type . '房源' . $show_field . $show_unit . '单项统计(单位:' . $show_suffix . ')';
    $data['item_title'] = '全城' . $show_type . '房源' . $show_field . $show_unit . '统计（' . $show_count_type_name . '：' . $total_add . ' ' . $show_suffix . '）';

    //功能菜单
    $data['user_menu'] = $this->user_menu;

    //页面标题
    $data['ge_title'] = '全城统计-单项统计';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/highcharts.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/disk.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('statistic/statistic_city', $data);
  }


  //趋势统计
  public function trend_statistic()
  {
    $data = array();
    $form_param = $this->input->post(NULL, TRUE);//表单数据

    //区属板块信息
    $this->load->model('district_model');
    $data['district_arr'] = $this->district_model->get_district();

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');

    //获取房源信息基本配置资料
    $data['conf_house'] = $this->house_config_model->get_config();

    $field = $form_param['field'];//统计字段
    $type = $form_param['type']; //类型
    $unit_trend = $form_param['unit_trend'];//单位
    $count_year = $form_param['count_year'];//统计年份
    $count_month = isset($form_param['count_month']) ? $form_param['count_month'] : '';//统计月份
    $district = $form_param['district'];//区属
    $buildarearea = $form_param['buildarearea'];//面积
    $price = $form_param['price'];//价格
    $infotype = $form_param['infotype'];//物业类型
    $fitment = $form_param['fitment'];//装修程度
    $room_type = $form_param['room_type'];//户型
    $avgprice = $form_param['avgprice'];//均价

    //查询条件
    $data['now_year'] = date('Y');
    $data['type'] = !empty($type) ? $type : 1;
    $data['unit_trend'] = !empty($unit_trend) ? $unit_trend : 'day';
    $data['field'] = !empty($field) ? $field : 'add_num';
    $data['count_year'] = !empty($count_year) ? $count_year : $data['now_year'];
    $data['count_month'] = !empty($count_month) ? $count_month : $data['unit_trend'] == 'day' ? date('n') : '';
    $data['district'] = !empty($district) ? $district : $data['district_arr'][0]['id'];
    $data['buildarearea'] = !empty($buildarearea) ? $buildarearea : 0;
    $data['price'] = !empty($price) ? $price : 0;
    $data['infotype'] = !empty($infotype) ? $infotype : 0;
    $data['fitment'] = !empty($fitment) ? $fitment : 0;
    $data['room_type'] = !empty($room_type) ? $room_type : 0;
    $data['avgprice'] = !empty($avgprice) ? $avgprice : 0;

    //是否隐藏多余的查询条件
    if ($data['infotype'] > 0 || $data['fitment'] > 0 || $data['room_type'] > 0 || $data['avgprice'] > 0) {
      $data['cond_show'] = '';
    } else {
      $data['cond_show'] = 'hide';
    }

    //统计MODEL
    $this->load->model('city_statistic_model');
    $config = $this->city_statistic_model->get_base_conf();//配置文件
    $data['config'] = $config;

    //展示数据
    $show_suffix = $config['field_suffix'][$data['type']][$data['field']];
    $data['show_suffix'] = $show_suffix;//后缀单位
    $show_type = $config['type'][$data['type']];//信息类型
    $show_field = $config['fields'][$data['field']];//统计字段
    $show_unit = $config['unit_trend'][$data['unit_trend']];//统计维度
    $show_count_type_operate = $config['count_type'][$data['field']]['operate'];//操作方式
    $show_count_type_name = $config['count_type'][$data['field']]['name'];//操作方式名称

    //查询条件
    $cond_where = $this->_get_trend_cond_sell($data);
    $statistic_data = $this->city_statistic_model->get_city_statistic_trend_data($data['type'],
      $data['field'], $data['unit_trend'], $data['count_year'], $cond_where);

    $statistic_num = count($statistic_data);
    $statistic_data_temp = array();
    $total_add = 0;
    $avg_num = 0;
    for ($i = 0; $i < $statistic_num; $i++) {
      $statistic_data_temp[$statistic_data[$i][$data['unit_trend']]] = $statistic_data[$i][$data['field']];

      //累加总和，计算非0个数
      if ($statistic_data[$i][$data['field']] != 0) {
        $total_add += $statistic_data[$i][$data['field']];
        $avg_num++;
      }
    }

    if ($show_count_type_operate == 'sum') {
      $total_add = round($total_add);//新增总数
    } else if ($show_count_type_operate == 'avg') {
      $total_add = $avg_num > 0 ? round($total_add / $avg_num) : 0;//新增平均值
    }

    if ($data['unit_trend'] == 'month') {
      for ($i = 1; $i <= 12; $i++) {
        $y_data_arr[] = !empty($statistic_data_temp[$i]) ? intval($statistic_data_temp[$i]) : 0;
        $x_month_arr[] = $i . '月';
      }
    } else if ($data['unit_trend'] == 'day') {
      //选中月份的天数
      $day_num = date('t', strtotime($data['count_year'] . "-" . $data['count_month'] . "-01"));

      for ($i = 1; $i <= $day_num; $i++) {
        $y_data_arr[] = !empty($statistic_data_temp[$i]) ? intval($statistic_data_temp[$i]) : 0;
        $x_month_arr[] = $i . '日';
      }
    }

    //X轴、Y轴数据
    $data['y_json'] = json_encode($y_data_arr);
    $data['x_json'] = json_encode($x_month_arr);

    //标题
    $data['chart_title'] = $show_type . '房源' . $show_field . $show_unit . '趋势分析(单位:' . $show_suffix . ')';
    $data['item_title'] = '全城' . $show_type . '房源' . $show_field . $show_unit . '统计（' . $show_count_type_name . '：' . $total_add . ' ' . $show_suffix . '）';

    //功能菜单
    $data['user_menu'] = $this->user_menu;

    //页面标题
    $data['ge_title'] = '全城统计-趋势统计';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/highcharts.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/disk.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('statistic/statistic_trend', $data);
  }


  //趋势统计查询条件
  public function _get_trend_cond_sell($form_param, $type = '1')
  {
    $cond_where = '';

    //区属条件
    if (isset($form_param['district']) && $form_param['district'] > 0) {
      $dist_id = intval($form_param['district']);
      $cond_where .= "district_id = '" . $dist_id . "' ";
    }

    //面积条件
    $buildarearea = intval($form_param['buildarearea']);
    switch ($buildarearea) {
      case '1':
        $cond_where .= " AND buildarea < '60' ";
        break;
      case '2':
        $cond_where .= " AND buildarea >= '60' AND buildarea <= '90' ";
        break;
      case '3':
        $cond_where .= " AND buildarea >= '90' AND buildarea <= '120' ";
        break;
      case '4':
        $cond_where .= " AND buildarea >= '120' AND buildarea <= '150' ";
        break;
      case '5':
        $cond_where .= " AND buildarea >= '150' AND buildarea <= '180' ";
        break;
      case '6':
        $cond_where .= " AND buildarea >= '180' AND buildarea <= '240' ";
        break;
      case '7':
        $cond_where .= " AND buildarea >= '240' AND buildarea <= '320' ";
        break;
      case '8':
        $cond_where .= " AND buildarea >= '320' AND buildarea <= '600' ";
        break;
      case '9':
        $cond_where .= " AND buildarea > '600' ";
        break;
    }

    //价格条件
    $price = intval($form_param['price']);
    if ($type == 1) {
      switch ($price) {
        case '1':
          $cond_where .= " AND price <= '30' ";
          break;
        case '2':
          $cond_where .= " AND price >= '30' AND price <= '50' ";
          break;
        case '3':
          $cond_where .= " AND price >= '50' AND price <= '70' ";
          break;
        case '4':
          $cond_where .= " AND price >= '70' AND price <= '90' ";
          break;
        case '5':
          $cond_where .= " AND price >= '90' AND price <= '120' ";
          break;
        case '6':
          $cond_where .= " AND price >= '120' AND price <= '150' ";
          break;
        case '7':
          $cond_where .= " AND price >= '150' AND price <= '200' ";
          break;
        case '8':
          $cond_where .= " AND price >= '200' AND price <= '400' ";
          break;
        case '9':
          $cond_where .= " AND price >= '400' ";
          break;
      }
    } else if ($type == 2) {
      switch ($price) {
        case '1':
          $cond_where .= " AND price <= '500' ";
          break;
        case '2':
          $cond_where .= " AND price >= '500' AND price <= '800' ";
          break;
        case '3':
          $cond_where .= " AND price >= '800' AND price <= '1200' ";
          break;
        case '4':
          $cond_where .= " AND price >= '1200' AND price <= '1800' ";
          break;
        case '5':
          $cond_where .= " AND price >= '1800' AND price <= '2600' ";
          break;
        case '6':
          $cond_where .= " AND price >= '2600' AND price <= '3200' ";
          break;
        case '7':
          $cond_where .= " AND price >= '3200' AND price <= '4200' ";
          break;
        case '8':
          $cond_where .= " AND price >= '4200' AND price <= '8000' ";
          break;
        case '9':
          $cond_where .= " AND price >= '8000' ";
          break;
      }
    }

    //物业类型
    if (isset($form_param["infotype"]) && $form_param['infotype'] > 0) {
      $infotype = intval($form_param["infotype"]);
      $cond_where .= " AND infotype = '" . $infotype . "' ";
    }

    //装修
    if (isset($form_param["fitment"]) && $form_param['fitment'] > 0) {
      $fitment = intval($form_param["fitment"]);
      $cond_where .= " AND fitment = '" . $fitment . "' ";
    }

    //户型条件
    if (isset($form_param["room_type"]) && $form_param["room_type"] > 0) {
      $room_type = intval($form_param["room_type"]);
      $cond_where .= " AND  room = '" . $room_type . "' ";
    }

    //均价
    $avgprice = intval($form_param['avgprice']);
    if ($type == 1) {
      switch ($avgprice) {
        case '1':
          $cond_where .= " AND avgprice < '3000' ";
          break;
        case '2':
          $cond_where .= " AND avgprice >= '3000' AND avgprice <= '4000' ";
          break;
        case '3':
          $cond_where .= " AND avgprice >= '4000' AND avgprice <= '5000' ";
          break;
        case '4':
          $cond_where .= " AND avgprice >= '5000' AND avgprice <= '6000' ";
          break;
        case '5':
          $cond_where .= " AND avgprice >= '6000' AND avgprice <= '7000' ";
          break;
        case '6':
          $cond_where .= " AND avgprice >= '7000' AND avgprice <= '9000' ";
          break;
        case '7':
          $cond_where .= " AND avgprice >= '9000' AND avgprice <= '12000' ";
          break;
        case '8':
          $cond_where .= " AND avgprice >= '12000' AND avgprice <= '20000' ";
          break;
        case '9':
          $cond_where .= " AND avgprice > '20000' ";
          break;
      }
    } else if ($type == 2) {
      switch ($avgprice) {
        case '1':
          $cond_where .= " AND avgprice < '20' ";
          break;
        case '2':
          $cond_where .= " AND avgprice >= '20' AND avgprice <= '50' ";
          break;
        case '3':
          $cond_where .= " AND avgprice >= '50' AND avgprice <= '80' ";
          break;
        case '4':
          $cond_where .= " AND avgprice >= '80' AND avgprice <= '110' ";
          break;
        case '5':
          $cond_where .= " AND avgprice >= '110' AND avgprice <= '140' ";
          break;
        case '6':
          $cond_where .= " AND avgprice >= '140' AND avgprice <= '170' ";
          break;
        case '7':
          $cond_where .= " AND avgprice >= '170' AND avgprice <= '200' ";
          break;
        case '8':
          $cond_where .= " AND avgprice >= '200' AND avgprice <= '400' ";
          break;
        case '9':
          $cond_where .= " AND avgprice > '400' ";
          break;
      }
    }

    //月份条件
    if (isset($form_param['count_month']) && $form_param['count_month'] > 0) {
      $count_month = intval($form_param['count_month']);
      $cond_where .= " AND month = '" . $count_month . "' ";
    }

    return $cond_where;
  }

  /************************************统计部分******************************************/
  /**
   * 当前页码
   *
   * @access private
   * @var string
   */
  private $_current_page = 1;

  /**
   * 每页条目数
   *
   * @access private
   * @var int
   */
  private $_limit = 10;

  /**
   * 偏移
   *
   * @access private
   * @var int
   */
  private $_offset = 0;

  /**
   * 条目总数
   *
   * @access private
   * @var int
   */
  private $_total_count = 0;

  /**
   * 初始化分页参数
   *
   * @access public
   * @param  int $current_page
   * @param  int $page_size
   * @return void
   */
  private function _init_pagination($current_page = 1, $page_size = 0)
  {
    /** 当前页 */
    $this->_current_page = ($current_page && is_numeric($current_page)) ?
      intval($current_page) : 1;

    /** 每页多少项 */
    $this->_limit = ($page_size && is_numeric($page_size)) ?
      intval($page_size) : $this->_limit;

    /** 偏移量 */
    $this->_offset = ($this->_current_page - 1) * $this->_limit;

    if ($this->_offset < 0) {
      redirect(base_url());
    }
  }

  //信息录入统计
  public function msg_entering_statistic()
  {
    $data = array();
    $this->load->model('statistic_analysis_model');
    $this->load->model('warrant_model');

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    if ($post_param['agency_id'] > 0) {
      $data['brokers'] = $this->warrant_model->get_broker_by_agencyid($post_param['agency_id']);
    }

    //房源查询条件
    $where = "";
    $con_house_ext = $this->_get_house_con($post_param);
    //print_r($con_customer_ext);
    $where .= $con_house_ext;
    //清除条件头尾多余的“AND”和空格
    $where = trim($where);
    $where = trim($where, "AND");
    $where = trim($where);

    //客源查询条件
    $con = "";
    $con_customer_ext = $this->_get_customer_con($post_param);
    //print_r($con_customer_ext);
    $con .= $con_customer_ext;
    //清除条件头尾多余的“AND”和空格
    $con = trim($con);
    $con = trim($con, "AND");
    $con = trim($con);

    //获取门店配置信息
    $data['agency_conf'] = $this->warrant_model->get_agency_conf();

    //获取出售房源所有条数
    $sell_house_count = $this->statistic_analysis_model->get_count($where, 'sell_house');
    //根据信息来源分组查询出售房源数据
    $sell_house_groups = $this->statistic_analysis_model->get_group_data_by_con($where, 'infofrom', 'sell_house');
    $sell_house_g = array();
    foreach ($sell_house_groups as $k => $v) {
      $sell_house_g[$v['infofrom']] = $v;
      $sell_house_g[$v['infofrom']]['ratio'] = round(($v['count(*)'] / $sell_house_count * 100), 2);
    }
    $data['sell_house_groups'] = $sell_house_g;


    //获取出租房源所有条数
    $rent_house_count = $this->statistic_analysis_model->get_count($where, 'rent_house');
    //根据信息来源分组查询出租房源数据
    $rent_house_groups = $this->statistic_analysis_model->get_group_data_by_con($where, 'infofrom', 'rent_house');
    $rent_house_g = array();
    foreach ($rent_house_groups as $k => $v) {
      $rent_house_g[$v['infofrom']] = $v;
      $rent_house_g[$v['infofrom']]['ratio'] = round(($v['count(*)'] / $rent_house_count * 100), 2);
    }
    $data['rent_house_groups'] = $rent_house_g;
    //print_r($rent_house_g);exit;

    //获取出售客源所有条数
    $sell_customer_count = $this->statistic_analysis_model->get_count($con, 'buy_customer');
    //根据信息来源分组查询出售房租数据
    $sell_customer_groups = $this->statistic_analysis_model->get_group_data_by_con($con, 'infofrom', 'buy_customer');
    $sell_customer_g = array();
    foreach ($sell_customer_groups as $k => $v) {
      $sell_customer_g[$v['infofrom']] = $v;
      $sell_customer_g[$v['infofrom']]['ratio'] = round(($v['count(*)'] / $sell_customer_count * 100), 2);
    }
    $data['sell_customer_groups'] = $sell_customer_g;

    //获取出售客源所有条数
    $rent_customer_count = $this->statistic_analysis_model->get_count($con, 'rent_customer');
    //根据信息来源分组查询出售房租数据
    $rent_customer_groups = $this->statistic_analysis_model->get_group_data_by_con($con, 'infofrom', 'rent_customer');
    $rent_customer_g = array();
    foreach ($rent_customer_groups as $k => $v) {
      $rent_customer_g[$v['infofrom']] = $v;
      $rent_customer_g[$k]['ratio'] = round(($v['count(*)'] / $rent_customer_count * 100), 2);
    }
    $data['rent_customer_groups'] = $rent_customer_g;

    $this->load->view('statistic/msg_entering_statistic', $data);
  }

  //租赁成交统计
  public function lease_statistic()
  {
    //成交值status是3
    $data = array();
    $this->load->model('statistic_analysis_model');
    $this->load->model('warrant_model');

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    if ($post_param['agency_id'] > 0) {
      $data['brokers'] = $this->warrant_model->get_broker_by_agencyid($post_param['agency_id']);
    }

    //获取门店配置信息
    $data['agency_conf'] = $this->warrant_model->get_agency_conf();

    //租赁房源查询条件
    $con = "";
    $cond_where_ext = $this->_get_house_str($post_param);
    $con .= $cond_where_ext;
    //清除条件头尾多余的“AND”和空格
    $con = trim($con);
    $con = trim($con, "AND");
    $con = trim($con);

    //租赁客源查询条件
    $con1 = "";
    $cond_where_ext1 = $this->_get_customer_str($post_param);
    $con1 .= $cond_where_ext1;
    //清除条件头尾多余的“AND”和空格
    $con1 = trim($con1);
    $con1 = trim($con1, "AND");
    $con1 = trim($con1);

    //房源成交查询条件
    $where = "status = 3";
    $where .= $cond_where_ext;

    //客源成交查询条件
    $where1 = "status = 3";
    $where1 .= $cond_where_ext1;

    //获取出租房源总条数
    $rent_house_count = $this->statistic_analysis_model->get_count($con, 'rent_house');
    //查询出租房源总数据
    $rent_house_data = $this->statistic_analysis_model->get_group_data_by_con($con, 'infofrom', 'rent_house');
    $rent_house_d = array();
    foreach ($rent_house_data as $k => $v) {
      $rent_house_d[$v['infofrom']] = $v;
      $rent_house_d[$v['infofrom']]['ratio'] = round(($v['count(*)'] / $rent_house_count * 100), 2);
    }
    $data['rent_house_data'] = $rent_house_d;
    //print_r($rent_house_d);exit;


    //获取出租成交房源总数
    $deal_house_count = $this->statistic_analysis_model->get_count($where, 'rent_house');
    //根据成交分组查询出租房源数据
    $rent_house_groups = $this->statistic_analysis_model->get_group_data_by_con($where, 'infofrom', 'rent_house');
    $rent_house_g = array();
    foreach ($rent_house_groups as $k => $v) {
      $rent_house_g[$v['infofrom']] = $v;
      $rent_house_g[$v['infofrom']]['ratio'] = round(($v['count(*)'] / $deal_house_count * 100), 2);
    }
    $data['rent_house_groups'] = $rent_house_g;


    //获取求租客源所有条数
    $rent_customer_count = $this->statistic_analysis_model->get_count($con1, 'rent_customer');
    //查询求租客源总数据
    $rent_customer_data = $this->statistic_analysis_model->get_group_data_by_con($con1, 'infofrom', 'rent_customer');
    $rent_customer_d = array();
    foreach ($rent_customer_data as $k => $v) {
      $rent_customer_d[$v['infofrom']] = $v;
      $rent_customer_d[$v['infofrom']]['ratio'] = round(($v['count(*)'] / $rent_customer_count * 100), 2);
    }
    $data['rent_customer_data'] = $rent_customer_d;

    //获取求租客源成交总数
    $deal_customer_count = $this->statistic_analysis_model->get_count($where1, 'rent_customer');
    //根据成交分组查询求租客源数据
    $rent_customer_groups = $this->statistic_analysis_model->get_group_data_by_con($where1, 'infofrom', 'rent_customer');
    $rent_customer_g = array();
    foreach ($rent_customer_groups as $k => $v) {
      $rent_customer_g[$v['infofrom']] = $v;
      $rent_customer_g[$v['infofrom']]['ratio'] = round(($v['count(*)'] / $deal_customer_count * 100), 2);
    }
    $data['rent_customer_groups'] = $rent_customer_g;

    $this->load->view('statistic/lease_statistic', $data);
  }

  //买卖成交统计
  public function buy_sell_statistic()
  {
    //成交值status是3
    $data = array();
    $this->load->model('statistic_analysis_model');
    $this->load->model('warrant_model');

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    if ($post_param['agency_id'] > 0) {
      $data['brokers'] = $this->warrant_model->get_broker_by_agencyid($post_param['agency_id']);
    }

    //获取门店配置信息
    $data['agency_conf'] = $this->warrant_model->get_agency_conf();

    //买卖查询条件
    $con = "";
    $cond_where_ext = $this->_get_cond_str($post_param);
    $con .= $cond_where_ext;
    //清除条件头尾多余的“AND”和空格
    $con = trim($con);
    $con = trim($con, "AND");
    $con = trim($con);

    //成交查询条件
    $where = "status = 3";
    $where .= $cond_where_ext;

    //获取出售房源总条数
    $sell_house_count = $this->statistic_analysis_model->get_count($con, 'sell_house');
    //查询出售房源总数据
    $sell_house_data = $this->statistic_analysis_model->get_group_data_by_con($con, 'infofrom', 'sell_house');
    $sell_house_d = array();
    foreach ($sell_house_data as $k => $v) {
      $sell_house_d[$v['infofrom']] = $v;
      $sell_house_d[$v['infofrom']]['ratio'] = round(($v['count(*)'] / $sell_house_count * 100), 2);
    }
    $data['sell_house_data'] = $sell_house_d;


    //获取出售房源成交总数
    $deal_house_count = $this->statistic_analysis_model->get_count($where, 'sell_house');
    //根据成交分组查询出租房源数据
    $sell_house_groups = $this->statistic_analysis_model->get_group_data_by_con($where, 'infofrom', 'sell_house');
    $sell_house_g = array();
    foreach ($sell_house_groups as $k => $v) {
      $sell_house_g[$v['infofrom']] = $v;
      $sell_house_g[$v['infofrom']]['ratio'] = round(($v['count(*)'] / $deal_house_count * 100), 2);
    }
    $data['sell_house_groups'] = $sell_house_g;


    //获取求购客源所有条数
    $buy_customer_count = $this->statistic_analysis_model->get_count($con, 'buy_customer');
    //查询求购客源总数据
    $buy_customer_data = $this->statistic_analysis_model->get_group_data_by_con($con, 'infofrom', 'buy_customer');
    $buy_customer_d = array();
    foreach ($buy_customer_data as $k => $v) {
      $buy_customer_d[$v['infofrom']] = $v;
      $buy_customer_d[$v['infofrom']]['ratio'] = round(($v['count(*)'] / $buy_customer_count * 100), 2);
    }
    $data['buy_customer_data'] = $buy_customer_d;

    //获取求购客源成交总数
    $deal_customer_count = $this->statistic_analysis_model->get_count($where, 'buy_customer');
    //根据成交分组查询求购客源数据
    $buy_customer_groups = $this->statistic_analysis_model->get_group_data_by_con($where, 'infofrom', 'buy_customer');
    $buy_customer_g = array();
    foreach ($buy_customer_groups as $k => $v) {
      $buy_customer_g[$v['infofrom']] = $v;
      $buy_customer_g[$v['infofrom']]['ratio'] = round(($v['count(*)'] / $deal_customer_count * 100), 2);
    }
    $data['buy_customer_groups'] = $buy_customer_g;

    $this->load->view('statistic/buy_sell_statistic', $data);

  }

  //业绩排行统计
  public function performance_rank()
  {
    $data = array();
    $this->load->model('statistic_analysis_model');
    $this->load->model('warrant_model');

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    if ($post_param['agency_id'] > 0) {
      $data['brokers'] = $this->warrant_model->get_broker_by_agencyid($post_param['agency_id']);
    }

    //获取门店配置信息
    $data['agency_conf'] = $this->warrant_model->get_agency_conf();

    //查询条件
    $where = "";
    $cond_where_ext = $this->_get_performance_rank_con($post_param);
    $where .= $cond_where_ext;
    //清除条件头尾多余的“AND”和空格
    $where = trim($where);
    $where = trim($where, "AND");
    $where = trim($where);

    //符合条件的总行数
    $this->_total_count =
      $this->statistic_analysis_model->get_broker_commission_count($where);

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);
    //获取提成信息
    //先联表获取经纪人信息和买卖业绩，并根据总业绩排序
    $files = "a.broker_id,a.truename,b.name,sum(c.price) all_price";
    //$where = "";
    $order_key = 'sum(c.price)';
    $order_by = 'DESC';
    $rows = $this->statistic_analysis_model->get_broker_commission($files, $where, $this->_offset, $this->_limit, $order_key, $order_by);

    foreach ($rows as $k => $v) {
      //设置排行
      $rows[$k]['rank'] = ($page - 1) * 10 + ($k + 1);
      //添加买卖提成
      $sell_file = "sum(c.price) sell_price";
      $sell_where = "d.type = 1 AND c.broker_id = " . $v['broker_id'];
      $sell_price = $this->statistic_analysis_model->get_broker_commission($sell_file, $sell_where, '', '', '', '');
      $rows[$k]['sell_price'] = isset($sell_price[0]['sell_price']) ? $sell_price[0]['sell_price'] : '';
      //添加租赁提成
      $rent_file = "sum(c.price) rent_price";
      $rent_where = "d.type = 2 AND c.broker_id = " . $v['broker_id'];
      $rent_price = $this->statistic_analysis_model->get_broker_commission($rent_file, $rent_where, '', '', '', '');
      $rows[$k]['rent_price'] = isset($rent_price[0]['rent_price']) ? $rent_price[0]['rent_price'] : '';
    }
    //print_r($rows);exit;
    $data['performances'] = $rows;

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '业绩排行';

    $this->load->view('statistic/performance_rank', $data);
  }

  //业绩明细统计
  public function performance_detail()
  {
    $data = array();
    $this->load->model('statistic_analysis_model');
    $this->load->model('warrant_model');
    $this->load->model('contract_config_model');

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    if ($post_param['agency_id'] > 0) {
      $data['brokers'] = $this->warrant_model->get_broker_by_agencyid($post_param['agency_id']);
    }

    //获取门店配置信息
    $data['agency_conf'] = $this->warrant_model->get_agency_conf();

    //获取合同配置信息
    $data['contract_config'] = $this->contract_config_model->get_config();

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //获取条件
    $con = "b.is_completed = 1 AND b.status = 2";  //已办结的合同
    $cond_where_ext = $this->_get_performance_detail_con($post_param);
    $con .= $cond_where_ext;

    //符合条件的总行数
    $this->_total_count =
      $this->statistic_analysis_model->get_performance_count($con);

    //排序
    $order_key = 'b.completed_time';
    $order_by = 'ASC';

    $data['rows'] = $this->statistic_analysis_model->get_performance($con, $this->_offset, $this->_limit, $order_key, $order_by);

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '业绩明细';

    $this->load->view('/statistic/performance_detail', $data);
  }

  //员工行为统计
  public function broker_action()
  {
    $data = array();
    $this->load->model('statistic_analysis_model');
    $this->load->model('warrant_model');

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    if ($post_param['agency_id'] > 0) {
      $data['brokers'] = $this->warrant_model->get_broker_by_agencyid($post_param['agency_id']);
    }

    //获取门店配置信息
    $data['agency_conf'] = $this->warrant_model->get_agency_conf();

    //获取今日时间区间
    $today_start = strtotime(date("Y-m-d") . " 00:00:00");
    $today_end = strtotime(date("Y-m-d") . " 23:59:59");

    //查询（登记信息）今日出售房源的条数
    $where = "createtime >=" . $today_start . " AND createtime <=" . $today_end;
    $cond_where_ext = $this->_get_house_str($post_param);
    $where .= $cond_where_ext;
    $data['create_sell_house'] = $this->statistic_analysis_model->get_count($where, 'sell_house');
    //查询（登记信息）今日出租房源的条数
    $data['create_rent_house'] = $this->statistic_analysis_model->get_count($where, 'rent_house');
    //查询（登记信息）今日求购客源的条数
    $where1 = "creattime >=" . $today_start . " AND creattime <=" . $today_end;
    $cond_where_ext1 = $this->_get_customer_str($post_param);
    $where1 .= $cond_where_ext1;
    $data['create_buy_customer'] = $this->statistic_analysis_model->get_count($where1, 'buy_customer');
    //查询（登记信息）今日求购客源的条数
    $data['create_rent_customer'] = $this->statistic_analysis_model->get_count($where1, 'rent_customer');
    //登记总数
    $data['create_total'] = $data['create_sell_house'] + $data['create_rent_house'] + $data['create_buy_customer'] + $data['create_rent_customer'];

    //查询（修改信息）今日出售房源的条数
    if (!empty($post_param['start_time']) || !empty($post_param['end_time'])) {
      $upd_con = "";
      $cond_where_ext2 = $this->_get_cond_upd($post_param);
      $upd_con .= $cond_where_ext2;
      //清除条件头尾多余的“AND”和空格
      $upd_con = trim($upd_con);
      $upd_con = trim($upd_con, "AND");
      $upd_con = trim($upd_con);
    } else {
      $upd_con = "updatetime >=" . $today_start . " AND updatetime <=" . $today_end;
    }
    $data['update_sell_house'] = $this->statistic_analysis_model->get_count($upd_con, 'sell_house');
    //查询（修改信息）今日出租房源的条数
    $data['update_rent_house'] = $this->statistic_analysis_model->get_count($upd_con, 'rent_house');
    //查询（修改信息）今日求购客源的条数
    $data['update_buy_customer'] = $this->statistic_analysis_model->get_count($upd_con, 'buy_customer');
    //查询（修改信息）今日求购客源的条数
    $data['update_rent_customer'] = $this->statistic_analysis_model->get_count($upd_con, 'rent_customer');
    //登记总数
    $data['update_total'] = $data['update_sell_house'] + $data['update_rent_house'] + $data['update_buy_customer'] + $data['update_rent_customer'];


    $stime = date("Y-m-d") . " 00:00:00";
    $etime = date("Y-m-d") . " 23:59:59";

    //查询（新增跟进）今日出售房源的条数（有待用group_by优化）
    /*$follow_con1 = "type = 1 AND date >='".$stime."' AND date <='".$etime."'";
    $data['follow_sell_house'] = $this->statistic_analysis_model->get_count($follow_con1,'detailed_follow');
    //查询（新增跟进）今日出租房源的条数
    $follow_con2 = "type = 2 AND date >='".$stime."' AND date <='".$etime."'";
    $data['follow_rent_house'] = $this->statistic_analysis_model->get_count($follow_con2,'detailed_follow');
    //查询（新增跟进）今日求购客源的条数
    $follow_con3 = "type = 3 AND date >='".$stime."' AND date <='".$etime."'";
    $data['follow_buy_customer'] = $this->statistic_analysis_model->get_count($follow_con3,'detailed_follow');
    //查询（新增跟进）今日求购客源的条数
    $follow_con4 = "type = 4 AND date >='".$stime."' AND date <='".$etime."'";
    $data['follow_rent_customer'] = $this->statistic_analysis_model->get_count($follow_con4,'detailed_follow');
    //登记总数
    $data['follow_total'] = $data['follow_sell_house'] + $data['follow_rent_house'] + $data['follow_buy_customer'] + $data['follow_rent_customer'];
*/
    //查询（新增跟进）今日出售房源的条数
    if (!empty($post_param['start_time']) || !empty($post_param['end_time'])) {
      $follow_con = "";
      $cond_where_ext3 = $this->_get_cond_follow($post_param);
      $follow_con .= $cond_where_ext3;
      //清除条件头尾多余的“AND”和空格
      $follow_con = trim($follow_con);
      $follow_con = trim($follow_con, "AND");
      $follow_con = trim($follow_con);
    } else {
      $follow_con = "date >='" . $stime . "' AND date <='" . $etime . "'";
    }
    $follow = $this->statistic_analysis_model->get_follow_log($follow_con);
    //定义一个数组防止type数据不存在
    $arr = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);
    foreach ($follow as $k => $v) {
      $arr[$v['type']] = $v['num'];
      $arr[5] += $v['num'];
    }
    $data['follows'] = $arr;

    //查询（新增约看）今日出售房源的条数
    if (!empty($post_param['start_time']) || !empty($post_param['end_time'])) {
      $view_con = "";
      $cond_where_ext4 = $this->_get_cond_view($post_param);
      $view_con .= $cond_where_ext4;
      //清除条件头尾多余的“AND”和空格
      $view_con = trim($view_con);
      $view_con = trim($view_con, "AND");
      $view_con = trim($view_con);
    } else {
      $view_con = "datetime >=" . $today_start . " AND datetime <=" . $today_end;
    }
    $data['view_sell_house'] = $this->statistic_analysis_model->get_count($view_con, 'sell_view_log');
    //查询（新增约看）今日出租房源的条数
    $data['view_rent_house'] = $this->statistic_analysis_model->get_count($view_con, 'rent_view_log');
    //查询（新增约看）今日求购客源的条数
    $data['view_buy_customer'] = $this->statistic_analysis_model->get_count($view_con, 'customer_view_log');
    //查询（新增约看）今日求购客源的条数
    $data['view_rent_customer'] = $this->statistic_analysis_model->get_count($view_con, 'rent_customer_view_log');
    //登记总数
    $data['view_total'] = $data['view_sell_house'] + $data['view_rent_house'] + $data['view_buy_customer'] + $data['view_rent_customer'];

    //查询（新增成交）今日出售房源的条数
    if (!empty($post_param['start_time']) || !empty($post_param['end_time'])) {
      $deal_con = "status = 3";
      $cond_where_ext5 = $this->_get_house_str($post_param);
      $deal_con .= $cond_where_ext5;
    } else {
      $deal_con = "status = 3 AND createtime >=" . $today_start . " AND createtime <=" . $today_end;
    }
    $data['deal_sell_house'] = $this->statistic_analysis_model->get_count($deal_con, 'sell_house');
    //查询（新增成交）今日出租房源的条数
    $data['deal_rent_house'] = $this->statistic_analysis_model->get_count($deal_con, 'rent_house');
    //查询（新增成交）今日求购客源的条数
    if (!empty($post_param['start_time']) || !empty($post_param['end_time'])) {
      $deal_con1 = "status = 3";
      $cond_where_ext6 = $this->_get_customer_str($post_param);
      $deal_con1 .= $cond_where_ext6;
    } else {
      $deal_con1 = "status = 3 AND creattime >=" . $today_start . " AND creattime <=" . $today_end;
    }
    $data['deal_buy_customer'] = $this->statistic_analysis_model->get_count($deal_con1, 'buy_customer');
    //查询（新增成交）今日求购客源的条数
    $data['deal_rent_customer'] = $this->statistic_analysis_model->get_count($deal_con1, 'rent_customer');
    //登记总数
    $data['deal_total'] = $data['deal_sell_house'] + $data['deal_rent_house'] + $data['deal_buy_customer'] + $data['deal_rent_customer'];

    //查询（分配任务）今日房客源的条数
    if (!empty($post_param['start_time']) || !empty($post_param['end_time'])) {
      $task_con = "";
      $cond_where_ext7 = $this->_get_cond_task_insert($post_param);
      $task_con .= $cond_where_ext7;
      //清除条件头尾多余的“AND”和空格
      $task_con = trim($task_con);
      $task_con = trim($task_con, "AND");
      $task_con = trim($task_con);
    } else {
      $task_con = "insert_date >=" . $today_start . " AND insert_date <=" . $today_end;
    }
    $tasks = $this->statistic_analysis_model->get_task_log($task_con);
    $arr1 = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);
    foreach ($tasks as $k => $v) {
      $arr1[$v['task_style']] = $v['num'];
      $arr1[5] += $v['num'];
    }
    $data['tasks'] = $arr1;

    //查询（处理任务）今日房客源的条数
    if (!empty($post_param['start_time']) || !empty($post_param['end_time'])) {
      $task_con1 = "";
      $cond_where_ext8 = $this->_get_cond_task_end($post_param);
      $task_con1 .= $cond_where_ext8;
      //清除条件头尾多余的“AND”和空格
      $task_con1 = trim($task_con1);
      $task_con1 = trim($task_con1, "AND");
      $task_con1 = trim($task_con1);
    } else {
      $task_con1 = "end_date >=" . $today_start . " AND end_date <=" . $today_end;
    }
    $end_tasks = $this->statistic_analysis_model->get_task_log($task_con1);
    $arr2 = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);
    foreach ($end_tasks as $k => $v) {
      $arr2[$v['task_style']] = $v['num'];
      $arr2[5] += $v['num'];
    }
    $data['end_tasks'] = $arr2;

    //合计
    $data['row_one'] = $data['create_sell_house'] + $data['update_sell_house'] + $data['follows'][1] + $data['view_sell_house'] + $data['deal_sell_house'] + $data['tasks'][1] + $data['end_tasks'][1];
    $data['row_two'] = $data['create_rent_house'] + $data['update_rent_house'] + $data['follows'][2] + $data['view_rent_house'] + $data['deal_rent_house'] + $data['tasks'][2] + $data['end_tasks'][2];
    $data['row_three'] = $data['create_buy_customer'] + $data['update_buy_customer'] + $data['follows'][3] + $data['view_buy_customer'] + $data['deal_buy_customer'] + $data['tasks'][3] + $data['end_tasks'][3];
    $data['row_four'] = $data['create_rent_customer'] + $data['update_rent_customer'] + $data['follows'][4] + $data['view_rent_customer'] + $data['deal_rent_customer'] + $data['tasks'][4] + $data['end_tasks'][4];
    $data['total'] = $data['create_total'] + $data['update_total'] + $data['follows'][5] + $data['view_total'] + $data['deal_total'] + $data['tasks'][5] + $data['end_tasks'][5];

    $this->load->view("/statistic/broker_action", $data);
  }

  /**
   * 信息录入统计客源条件
   * @param $form_param  条件参数
   * @return string
   */
  private function _get_customer_con($form_param)
  {

    //查询条件
    $con = "";
    //分店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $con .= " AND agency_id = '" . $agency_id . "'";
    }
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($agency_id) {
      $con .= " AND broker_id = '" . $broker_id . "'";
    }

    //时间类型
    $data_type = isset($form_param['date_type']) ? intval($form_param['date_type']) : 0;
    $s_time = $form_param['s_time'] ? strtotime($form_param['s_time'] . " 00:00:00") : "";
    $e_time = $form_param['e_time'] ? strtotime($form_param['e_time'] . " 23:59:59") : "";
    if ($data_type == 1) {

      if ($s_time) {
        $con .= " AND creattime >= '" . $s_time . "'";
      }
      if ($e_time) {
        $con .= " AND creattime <= '" . $e_time . "'";
      }
    }
    if ($data_type == 2) {

      if ($s_time) {
        $con .= " AND updatetime >= '" . $s_time . "'";
      }
      if ($e_time) {
        $con .= " AND updatetime <= '" . $e_time . "'";
      }

    }

    return $con;
  }

  /**
   * 信息录入统计房源条件
   * @param $form_param  条件参数
   * @return string
   */
  private function _get_house_con($form_param)
  {

    //查询条件
    $con = "";
    //分店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $con .= " AND agency_id = '" . $agency_id . "'";
    }
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($broker_id) {
      $con .= " AND broker_id = '" . $broker_id . "'";
    }

    //时间类型
    $data_type = isset($form_param['date_type']) ? intval($form_param['date_type']) : 0;
    $s_time = $form_param['s_time'] ? strtotime($form_param['s_time'] . " 00:00:00") : "";
    $e_time = $form_param['e_time'] ? strtotime($form_param['e_time'] . " 23:59:59") : "";
    if ($data_type == 1) {

      if ($s_time) {
        $con .= " AND createtime >= '" . $s_time . "'";
      }
      if ($e_time) {
        $con .= " AND createtime <= '" . $e_time . "'";
      }
    }
    if ($data_type == 2) {

      if ($s_time) {
        $con .= " AND updatetime >= '" . $s_time . "'";
      }
      if ($e_time) {
        $con .= " AND updatetime <= '" . $e_time . "'";
      }

    }

    return $con;
  }

  /**
   * 业绩排行条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_performance_rank_con($form_param)
  {
    $cond_where = "";
    //分店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND a.agency_id = '" . $agency_id . "'";
    }
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND a.broker_id = '" . $broker_id . "'";
    }
    //开始时间 结束时间
    $start_time = $form_param['start_time'] ? strtotime($form_param['start_time'] . " 00:00:00") : "";
    $end_time = $form_param['end_time'] ? strtotime($form_param['end_time'] . " 23:59:59") : "";
    if ($start_time && $end_time && $start_time > $end_time) {
      $this->jump(MLS_URL . '/statistic/', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    if ($start_time) {
      $cond_where .= " AND c.updatetime >= '" . $start_time . "'";
    }
    if ($end_time) {
      $cond_where .= " AND c.updatetime <= '" . $end_time . "'";
    }


    return $cond_where;
  }

  /**
   * 业绩明细条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_performance_detail_con($form_param)
  {
    $cond_where = "";
    if ($form_param['agency_id'] && $form_param['broker_id']) {
      //员工
      $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
      if ($broker_id) {
        $cond_where .= " AND a.broker_id = '" . $broker_id . "'";
      }
    }

    if ($form_param['agency_id'] && $form_param['broker_id'] == 0) {
      //分店
      $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
      if ($agency_id) {
        $cond_where .= " AND a.agency_id = '" . $agency_id . "'";
      }
    }

    //开始时间 结束时间
    $start_time = $form_param['start_time'] ? strtotime($form_param['start_time'] . " 00:00:00") : "";
    $end_time = $form_param['end_time'] ? strtotime($form_param['end_time'] . " 23:59:59") : "";
    if ($start_time && $end_time && $start_time > $end_time) {
      $this->jump(MLS_URL . '/statistic/', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    if ($start_time) {
      $cond_where .= " AND b.completed_time >= '" . $start_time . "'";
    }
    if ($end_time) {
      $cond_where .= " AND b.completed_time <= '" . $end_time . "'";
    }


    return $cond_where;

  }

  /**
   * 条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = "";
    //分店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    //开始时间 结束时间
    $start_time = $form_param['start_time'] ? strtotime($form_param['start_time'] . " 00:00:00") : "";
    $end_time = $form_param['end_time'] ? strtotime($form_param['end_time'] . " 23:59:59") : "";
    if ($start_time && $end_time && $start_time > $end_time) {
      $this->jump(MLS_URL . '/statistic/', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    if ($start_time) {
      $cond_where .= " AND createtime >= '" . $start_time . "'";
    }
    if ($end_time) {
      $cond_where .= " AND createtime <= '" . $end_time . "'";
    }


    return $cond_where;

  }

  /**
   * 员工行为登记信息中的房源条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_house_str($form_param)
  {
    $cond_where = "";
    //分店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    //开始时间 结束时间
    $start_time = $form_param['start_time'] ? strtotime($form_param['start_time'] . " 00:00:00") : "";
    $end_time = $form_param['end_time'] ? strtotime($form_param['end_time'] . " 23:59:59") : "";
    if ($start_time && $end_time && $start_time > $end_time) {
      $this->jump(MLS_URL . '/statistic/', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    if ($start_time) {
      $cond_where .= " AND createtime >= '" . $start_time . "'";
    }
    if ($end_time) {
      $cond_where .= " AND createtime <= '" . $end_time . "'";
    }


    return $cond_where;

  }

  /**
   * 员工行为登记信息中的客源条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_customer_str($form_param)
  {
    $cond_where = "";
    //分店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    //开始时间 结束时间
    $start_time = $form_param['start_time'] ? strtotime($form_param['start_time'] . " 00:00:00") : "";
    $end_time = $form_param['end_time'] ? strtotime($form_param['end_time'] . " 23:59:59") : "";
    if ($start_time && $end_time && $start_time > $end_time) {
      $this->jump(MLS_URL . '/statistic/', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    if ($start_time) {
      $cond_where .= " AND creattime >= '" . $start_time . "'";
    }
    if ($end_time) {
      $cond_where .= " AND creattime <= '" . $end_time . "'";
    }


    return $cond_where;

  }

  /**
   * 员工行为修改信息中的客源条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_upd($form_param)
  {
    $cond_where = "";
    //分店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    //开始时间 结束时间
    $start_time = $form_param['start_time'] ? strtotime($form_param['start_time'] . " 00:00:00") : "";
    $end_time = $form_param['end_time'] ? strtotime($form_param['end_time'] . " 23:59:59") : "";
    if ($start_time && $end_time && $start_time > $end_time) {
      $this->jump(MLS_URL . '/statistic/', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    if ($start_time) {
      $cond_where .= " AND updatetime >= '" . $start_time . "'";
    }
    if ($end_time) {
      $cond_where .= " AND updatetime <= '" . $end_time . "'";
    }


    return $cond_where;

  }

  /**
   * 员工行为新增跟进中的条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_follow($form_param)
  {
    $cond_where = "";
    //分店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    //开始时间 结束时间
    $start_time = $form_param['start_time'] ? ($form_param['start_time'] . " 00:00:00") : "";
    $end_time = $form_param['end_time'] ? ($form_param['end_time'] . " 23:59:59") : "";
    if ($start_time && $end_time && $start_time > $end_time) {
      $this->jump(MLS_URL . '/statistic/', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    if ($start_time) {
      $cond_where .= " AND date >= '" . $start_time . "'";
    }
    if ($end_time) {
      $cond_where .= " AND date <= '" . $end_time . "'";
    }

    return $cond_where;

  }

  /**
   * 员工行为新增约看中的条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_view($form_param)
  {
    $cond_where = "";
    //分店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    //开始时间 结束时间
    $start_time = $form_param['start_time'] ? strtotime($form_param['start_time'] . " 00:00:00") : "";
    $end_time = $form_param['end_time'] ? strtotime($form_param['end_time'] . " 23:59:59") : "";
    if ($start_time && $end_time && $start_time > $end_time) {
      $this->jump(MLS_URL . '/statistic/', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    if ($start_time) {
      $cond_where .= " AND datetime >= '" . $start_time . "'";
    }
    if ($end_time) {
      $cond_where .= " AND datetime <= '" . $end_time . "'";
    }


    return $cond_where;

  }

  /**
   * 员工行为分配任务中的条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_task_insert($form_param)
  {
    $cond_where = "";
    //分店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    //开始时间 结束时间
    $start_time = $form_param['start_time'] ? strtotime($form_param['start_time'] . " 00:00:00") : "";
    $end_time = $form_param['end_time'] ? strtotime($form_param['end_time'] . " 23:59:59") : "";
    if ($start_time && $end_time && $start_time > $end_time) {
      $this->jump(MLS_URL . '/statistic/', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    if ($start_time) {
      $cond_where .= " AND insert_date >= '" . $start_time . "'";
    }
    if ($end_time) {
      $cond_where .= " AND insert_date <= '" . $end_time . "'";
    }


    return $cond_where;

  }

  /**
   * 员工行为处理任务中的条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_task_end($form_param)
  {
    $cond_where = "";
    //分店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    //开始时间 结束时间
    $start_time = $form_param['start_time'] ? strtotime($form_param['start_time'] . " 00:00:00") : "";
    $end_time = $form_param['end_time'] ? strtotime($form_param['end_time'] . " 23:59:59") : "";
    if ($start_time && $end_time && $start_time > $end_time) {
      $this->jump(MLS_URL . '/statistic/', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    if ($start_time) {
      $cond_where .= " AND end_date >= '" . $start_time . "'";
    }
    if ($end_time) {
      $cond_where .= " AND end_date <= '" . $end_time . "'";
    }

    return $cond_where;

  }


}

/* End of file statistic.php */
/* Location: ./applications/mls/applications/statistic.php */
