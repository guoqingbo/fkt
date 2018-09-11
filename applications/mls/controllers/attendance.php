<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * 考勤
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Attendance extends MY_Controller
{

  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'sh';

  /**
   * 录入经纪人id
   *
   * @access private
   * @var int
   */
  private $_boker_id = 0;

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

  public function __construct()
  {
    parent::__construct();
    $this->load->model('api_broker_model');
    $this->load->model('attendance_model');
  }

  public function index()
  {
    $this->_limit = 4;

    $this->load->model('broker_info_model');
    //模板使用数据
    $data = array();

    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;

    //查询房源条件
    $cond_where = "";

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    //权限条件($func_area  1 本人  2 门店  3公司)
    $func_area = $this->user_func_permission['area'] ? $this->user_func_permission['area'] : 1;
    $data['func_area'] = $func_area;

    if ($func_area == 1) {
      $broker_id = $this->user_arr['broker_id'];
      //权限条件
      $cond_where .= "broker_id = {$broker_id}";

      $data['attendance_name'] = $this->user_arr['truename'];
    } else if ($func_area == 2) {
      $agency_id = $this->user_arr['agency_id'];
      //权限条件
      $cond_where .= "agency_id = {$agency_id}";

      //获取经纪人列表数组
      $this->load->model('api_broker_model');
      $brokers = $this->api_broker_model->get_brokers_agency_id($agency_id);
      $data['brokers'] = $brokers;

      $agency_info = $this->api_broker_model->get_by_agency_id($agency_id);
      $data['attendance_name'] = $agency_info['name'];
    } else if ($func_area == 3) {
      $company_id = $this->user_arr['company_id'];
      //权限条件
      $cond_where .= "company_id = {$company_id}";

      //获取所有分公司数组
      $this->load->model('api_broker_model');
      $agencys = $this->api_broker_model->get_agencys_by_company_id($company_id);
      $data['agencys'] = $agencys;

      $company_info = $this->api_broker_model->get_by_agency_id($company_id);
      $data['attendance_name'] = $company_info['name'];

      //门店
      $agency_id = isset($post_param['agency_id']) ? intval($post_param['agency_id']) : 0;
      if ($agency_id) {
        //获取经纪人列表数组
        $this->load->model('api_broker_model');
        $brokers = $this->api_broker_model->get_brokers_agency_id($agency_id);
        $data['brokers'] = $brokers;
      }
    }

    $cond_where .= " AND status = 1 ";


    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;

    //符合条件的总行数
    $this->_total_count = $this->broker_info_model->count_by($cond_where);

    //时间
    $date = $post_param['date'] ? trim($post_param['date']) : date("Y-m", time());
    $data['date'] = $date;

    $date_str = date("Y年m月", strtotime($date));
    $data['date_str'] = $date_str;

    //给定月份所应有的天数
    $date_t = date("t", strtotime($date));
    $data['date_t'] = $date_t;

    $date_array = array();
    for ($i = 1; $i <= $date_t; $i++) {
      //考勤(1表示正常;-1空表示没有考勤记录0表示早退/迟到2表示请假)
      $date_array[$i] = -1;
    }

    //获取列表内容
    $ids_arr = array();
    $list = array();
    $this->broker_info_model->set_select_fields(array('broker_id', 'truename'));
    $broker_list = $this->broker_info_model->get_all_by($cond_where, $this->_offset, $this->_limit);
    foreach ($broker_list as $key => $val) {
      $ids_arr[] = $val['broker_id'];
      $list[$val['broker_id']] = $val;
      $list[$val['broker_id']]["am"] = $date_array;
      $list[$val['broker_id']]["pm"] = $date_array;
    }

    if (is_full_array($ids_arr)) {
      $ids_str = implode(",", $ids_arr);
      //上午上班
      $cond_where1 = "broker_id in ({$ids_str}) AND type = 1 AND datetime1 like '{$date}-%'";
      $this->attendance_model->set_select_fields(array('broker_id', 'datetime1', 'status'));
      $am = $this->attendance_model->get_all_by($cond_where1, -1, 0, "datetime1", "ASC");
      foreach ($am as $key => $val) {
        $date_j = date("j", strtotime($val['datetime1']));
        if ($val['status'] == 0) {
          //是否请假
          $num = $this->attendance_model->count_by("type = 3 AND datetime1 <= '" . $val['datetime1'] . "' AND datetime2 >= '" . $val['datetime1'] . "'");
          if ($num > 0) {
            $list[$val['broker_id']]["am"][$date_j] = 2;
          } else {
            $list[$val['broker_id']]["am"][$date_j] = $val['status'];
          }
        } else {
          $list[$val['broker_id']]["am"][$date_j] = $val['status'];
        }
      }
      //下午下班
      $cond_where2 = "broker_id in ({$ids_str}) AND type = 2 AND datetime1 like '{$date}-%'";
      $this->attendance_model->set_select_fields(array('broker_id', 'datetime1', 'status'));
      $pm = $this->attendance_model->get_all_by($cond_where2, -1, 0, "datetime1", "ASC");
      foreach ($pm as $key => $val) {
        $date_j = date("j", strtotime($val['datetime1']));
        if ($val['status'] == 0) {
          //是否请假
          $num = $this->attendance_model->count_by("type = 3 AND datetime1 <= '" . $val['datetime1'] . "' AND datetime2 >= '" . $val['datetime1'] . "'");
          if ($num > 0) {
            $list[$val['broker_id']]["pm"][$date_j] = 2;
          } else {
            $list[$val['broker_id']]["pm"][$date_j] = $val['status'];
          }
        } else {
          $list[$val['broker_id']]["pm"][$date_j] = $val['status'];
        }
      }
      //获取公司上下班时间
      $company_id = $this->user_arr['company_id'];
      $this->load->model('attendance_set_model');
      $this->attendance_set_model->set_select_fields(array("start_time", "end_time"));
      $set_info = $this->attendance_set_model->get_one_by("company_id = {$company_id}");
      $start_time = $set_info["start_time"] ? $set_info["start_time"] : "09:00:00";
      $end_time = $set_info["end_time"] ? $set_info["end_time"] : "17:00:00";

      //请假
      $cond_where3 = "broker_id in ({$ids_str}) AND type = 3 AND status = 1 AND (datetime1 like '{$date}-%' OR datetime2 like '{$date}-%')";
      $this->attendance_model->set_select_fields(array('broker_id', 'datetime1', 'datetime2'));
      $qj = $this->attendance_model->get_all_by($cond_where3, -1, 0, "datetime1", "ASC");
      foreach ($qj as $key => $val) {
        if ($val['datetime1'] > $date . "-01 00:00:00") {
          $date_j1 = date("j", strtotime($val['datetime1']));
        } else {
          $date_j1 = 1;
        }
        if ($val['datetime2'] < $date . "-" . $date_t . " 23:59:59") {
          $date_j2 = date("j", strtotime($val['datetime1']));
        } else {
          $date_j2 = $date_t;
        }
        for ($i = $date_j1; $i <= $date_j2; $i++) {
          $datetime_am = $date . "-" . sprintf("%02d", $i) . " " . $start_time;
          if ($datetime_am >= $val['datetime1'] && $datetime_am <= $val['datetime2']) {
            if ($list[$val['broker_id']]["am"][$i] == -1) {
              $list[$val['broker_id']]["am"][$i] = 2;
            }
          }
          $datetime_pm = $date . "-" . sprintf("%02d", $i) . " " . $end_time;
          if ($datetime_pm >= $val['datetime1'] && $datetime_pm <= $val['datetime2']) {
            if ($list[$val['broker_id']]["pm"][$i] == -1) {
              $list[$val['broker_id']]["pm"][$i] = 2;
            }
          }
        }
      }
    }
    $data['list'] = $list;

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
    $data['page_title'] = '考勤统计';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/scrollPic.js');

    $this->view('attendance/list', $data);
  }


  /**
   * 出售列表条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = '';
    //门店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($broker_id) {
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    return $cond_where;
  }

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

  //员工管理-考勤管理
  public function manage()
  {
    //模板使用数据
    $data = array();

    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    //查询条件
    $cond_where = "";

    //权限条件($func_area  1 本人  2 门店  3公司)
    $func_area = $this->user_func_permission['area'] ? $this->user_func_permission['area'] : 1;
    $data['func_area'] = $func_area;

    if ($func_area == 1) {
    } else if ($func_area == 2) {
      $agency_id = $this->user_arr['agency_id'];
      //获取经纪人列表数组
      $brokers = $this->api_broker_model->get_brokers_agency_id($agency_id);
      $data['brokers'] = $brokers;
    } else if ($func_area == 3) {
      $company_id = $this->user_arr['company_id'];
      //获取所有分公司数组
      $agencys = $this->api_broker_model->get_agencys_by_company_id($company_id);
      $data['agencys'] = $agencys;

      //门店
      $agency_id = isset($post_param['agency_id']) ? intval($post_param['agency_id']) : 0;
      if ($agency_id) {
        //获取经纪人列表数组
        $brokers = $this->api_broker_model->get_brokers_agency_id($agency_id);
        $data['brokers'] = $brokers;
      }
    }

    //员工
    $broker_id = $post_param['broker_id'] ? intval($post_param['broker_id']) : $this->user_arr['broker_id'];
    if ($broker_id) {
      $cond_where .= "broker_id = '" . $broker_id . "'";

      //经纪人基本信息
      $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
      $broker_name = $broker_info['truename'];
      $data['broker_name'] = $broker_name;
    }

    //时间
    $date = $post_param['date'] ? trim($post_param['date']) : date("Y-m", time());
    $data['date'] = $date;
    $cond_where .= " AND datetime1 like '{$date}-%' ";

    $date_str = date("Y年m月", strtotime($date));
    $data['date_str'] = $date_str;

    //给定月份所应有的天数
    $date_t = date("t", strtotime($date));
    $data['date_t'] = $date_t;

    $date_array = array();
    for ($i = 1; $i <= $date_t; $i++) {
      $date_array[$i] = array();
      $date1 = $date . "-" . sprintf("%02d", $i);
      //日期
      $date_array[$i]['date'] = $date1;
      //星期中的第几天
      $date_array[$i]['week'] = date("w", strtotime($date1));
      //考勤
      $date_array[$i]['list'] = array();
    }

    $list = $this->attendance_model->get_all_by($cond_where, -1, 0, "datetime1", "ASC");
    foreach ($list as $key => $val) {
      $date_j = date("j", strtotime($val['datetime1']));
      $date_array[$date_j]['list'][] = $val;
    }

    $data['date_array'] = $date_array;

    $config = $this->attendance_model->get_config();
    $data['config'] = $config;

    //页面标题
    $data['page_title'] = '考勤管理';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/scrollPic.js');

    $this->view('attendance/manage', $data);
  }

  //添加考勤
  public function add_attendance()
  {
    //模板使用数据
    $data = array();

    $data['user_arr'] = $this->user_arr;

    //权限条件($func_area  1 本人  2 门店  3公司)
    $func_area = $this->user_func_permission['area'] ? $this->user_func_permission['area'] : 1;
    $data['func_area'] = $func_area;

    if ($func_area == 1) {
    } else if ($func_area == 2) {
      $agency_id = $this->user_arr['agency_id'];
      //获取经纪人列表数组
      $brokers = $this->api_broker_model->get_brokers_agency_id($agency_id);
      $data['brokers'] = $brokers;
    } else if ($func_area == 3) {
      $company_id = $this->user_arr['company_id'];
      //获取所有分公司数组
      $agencys = $this->api_broker_model->get_agencys_by_company_id($company_id);
      $data['agencys'] = $agencys;
    }

    //页面标题
    $data['page_title'] = '添加考勤';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/scrollPic.js');

    $this->view('attendance/add_attendance', $data);
  }

  //添加考勤ajax
  public function add_attendance_ajax()
  {
    //post参数
    $post_param = $this->input->post(NULL, TRUE);

    //考勤添加信息数组
    $type = intval($post_param['type']);
    $datainfo = array(
      'type' => $type,
      'agency_id' => intval($this->user_arr['agency_id']),
      'broker_id' => intval($this->user_arr['broker_id']),
      'remarks' => trim($post_param['remarks'])
    );
    if ($type == 1) {
      $broker_id = intval($this->user_arr['broker_id']);
      $date = date("Y-m-d");
      $num = $this->attendance_model->count_by("type = 1 AND broker_id = {$broker_id} AND datetime1 like '{$date}%'");
      if ($num > 0) {
        echo json_encode(array('result' => 'no', "msg" => "当天已有上班考勤"));
        exit;
      }

      $company_id = $this->user_arr['company_id'];
      //获取公司上班时间
      $this->load->model('attendance_set_model');
      $this->attendance_set_model->set_select_fields(array("start_time"));
      $set_info = $this->attendance_set_model->get_one_by("company_id = {$company_id}");
      //上班时间
      $start_time = $set_info['start_time'] ? trim($set_info['start_time']) : "";
      $time = date("H:i:s");
      if ($start_time && $time <= $start_time) {
        $datainfo["status"] = 1;
      } else {
        $datainfo["status"] = 0;
      }
      $datainfo["datetime1"] = date("Y-m-d H:i:s");
    } elseif ($type == 2) {
      $broker_id = intval($this->user_arr['broker_id']);
      $date = date("Y-m-d");
      $num = $this->attendance_model->count_by("type = 2 AND broker_id = {$broker_id} AND datetime1 like '{$date}%'");
      if ($num > 0) {
        echo json_encode(array('result' => 'no', "msg" => "当天已有下班考勤"));
        exit;
      }

      $company_id = $this->user_arr['company_id'];
      //获取公司下班时间
      $this->load->model('attendance_set_model');
      $this->attendance_set_model->set_select_fields(array("end_time"));
      $set_info = $this->attendance_set_model->get_one_by("company_id = {$company_id}");
      //下班时间
      $end_time = $set_info['end_time'] ? trim($set_info['end_time']) : "";
      $time = date("H:i:s");
      if ($end_time && $time >= $end_time) {
        $datainfo["status"] = 1;
      } else {
        $datainfo["status"] = 0;
      }
      $datainfo["datetime1"] = date("Y-m-d H:i:s");
    } elseif ($type == 3) {
      $datetime1 = trim($post_param['datetime1']);
      $datetime2 = trim($post_param['datetime2']);
      if ($datetime1 >= $datetime2) {
        echo json_encode(array('result' => 'no', "msg" => "开始时间不能小于结束时间"));
        exit;
      }
      $datainfo["datetime1"] = $datetime1;
      if ($datetime2) {
        $datainfo["datetime2"] = $datetime2;
        $datainfo["status"] = 1;
      } else {
        $datainfo["status"] = 0;
      }
    } else {
      $datetime1 = trim($post_param['datetime1']);
      $datainfo["datetime1"] = $datetime1;
      $datainfo["status"] = 0;
    }
    //添加
    $id = $this->attendance_model->add_info($datainfo);
    if ($id) {
      echo json_encode(array('result' => 'ok'));
      exit;
    } else {
      echo json_encode(array('result' => 'no', "msg" => "提交失败"));
      exit;
    }

  }

  //添加考勤ajax1
  public function add_attendance_ajax1()
  {
    //post参数
    $post_param = $this->input->post(NULL, TRUE);

    //考勤添加信息数组
    $type = intval($post_param['type']);
    $datainfo = array(
      'type' => $type,
      'agency_id' => intval($post_param['agency_id']),
      'broker_id' => intval($post_param['broker_id']),
      'datetime1' => trim($post_param['datetime1']),
      'remarks' => trim($post_param['remarks'])
    );
    if ($type == 1) {
      $broker_id = intval($this->user_arr['broker_id']);
      $date = substr(trim($post_param['datetime1']), 0, 10);
      $num = $this->attendance_model->count_by("type = 1 AND broker_id = {$broker_id} AND datetime1 like '{$date}%'");
      if ($num > 0) {
        echo json_encode(array('result' => 'no', "msg" => "当天已有上班考勤"));
        exit;
      }

      $company_id = $this->user_arr['company_id'];
      //获取公司上班时间
      $this->load->model('attendance_set_model');
      $this->attendance_set_model->set_select_fields(array("start_time"));
      $set_info = $this->attendance_set_model->get_one_by("company_id = {$company_id}");
      //上班时间
      $start_time = $set_info['start_time'] ? trim($set_info['start_time']) : "";
      $time = substr(trim($post_param['datetime1']), 11);
      if ($start_time && $time <= $start_time) {
        $datainfo["status"] = 1;
      } else {
        $datainfo["status"] = 0;
      }
    } elseif ($type == 2) {
      $broker_id = intval($this->user_arr['broker_id']);
      $date = substr(trim($post_param['datetime1']), 0, 10);
      $num = $this->attendance_model->count_by("type = 2 AND broker_id = {$broker_id} AND datetime1 like '{$date}%'");
      if ($num > 0) {
        echo json_encode(array('result' => 'no', "msg" => "当天已有下班考勤"));
        exit;
      }

      $company_id = $this->user_arr['company_id'];
      //获取公司下班时间
      $this->load->model('attendance_set_model');
      $this->attendance_set_model->set_select_fields(array("end_time"));
      $set_info = $this->attendance_set_model->get_one_by("company_id = {$company_id}");
      //下班时间
      $end_time = $set_info['end_time'] ? trim($set_info['end_time']) : "";
      $time = substr(trim($post_param['datetime1']), 11);
      if ($end_time && $time >= $end_time) {
        $datainfo["status"] = 1;
      } else {
        $datainfo["status"] = 0;
      }
    } elseif ($type == 3) {
      $datetime1 = trim($post_param['datetime1']);
      $datetime2 = trim($post_param['datetime2']);
      if ($datetime1 >= $datetime2) {
        echo json_encode(array('result' => 'no', "msg" => "开始时间不能小于结束时间"));
        exit;
      }
      if ($datetime2) {
        $datainfo["datetime2"] = $datetime2;
        $datainfo["status"] = 1;
      } else {
        $datainfo["status"] = 0;
      }
    } else {
      $datainfo["status"] = 0;
    }
    //添加
    $id = $this->attendance_model->add_info($datainfo);
    if ($id) {
      echo json_encode(array('result' => 'ok'));
      exit;
    } else {
      echo json_encode(array('result' => 'no', "msg" => "添加考勤失败"));
      exit;
    }

  }

  //添加外出ajax
  public function add_out()
  {
    //post参数
    $post_param = $this->input->post(NULL, TRUE);

    //考勤添加信息数组
    $datainfo = array(
      'type' => 4,
      'agency_id' => intval($post_param['agency_id']),
      'broker_id' => intval($post_param['broker_id']),
      'datetime1' => trim($post_param['datetime1']),
      'remarks' => trim($post_param['remarks']),
      'status' => 0,
    );
    //添加
    $id = $this->attendance_model->add_info($datainfo);
    if ($id) {
      echo json_encode(array('result' => 'ok'));
      exit;
    } else {
      echo json_encode(array('result' => 'no'));
      exit;
    }

  }

  //修改考勤
  public function update_attendance($id)
  {
    //模板使用数据
    $data = array();

    $info = $this->attendance_model->get_info_by_id($id);
    $data['info'] = $info;

    $config = $this->attendance_model->get_config();
    $data['config'] = $config;

    //权限条件($func_area  1 本人  2 门店  3公司)
    $func_area = $this->user_func_permission['area'] ? $this->user_func_permission['area'] : 3;
    $data['func_area'] = $func_area;

    //页面标题
    $data['page_title'] = '修改考勤';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/scrollPic.js');

    $this->view('attendance/update_attendance', $data);
  }

  //修改考勤ajax
  public function update_attendance_ajax($id)
  {
    //post参数
    $post_param = $this->input->post(NULL, TRUE);

    $info = $this->attendance_model->get_info_by_id($id);


    $type = $info["type"];
    //考勤修改信息数组
    $datainfo = array(
      'datetime1' => trim($post_param['datetime1']),
      'remarks' => trim($post_param['remarks']),
      'explain' => trim($post_param['explain'])
    );
    if ($type == 1) {
      $company_id = $this->user_arr['company_id'];
      //获取公司上班时间
      $this->load->model('attendance_set_model');
      $this->attendance_set_model->set_select_fields(array("start_time"));
      $set_info = $this->attendance_set_model->get_one_by("company_id = {$company_id}");
      //上班时间
      $start_time = $set_info['start_time'] ? trim($set_info['start_time']) : "";
      $time = substr(trim($post_param['datetime1']), 11);
      if ($start_time && $time <= $start_time) {
        $datainfo["status"] = 1;
      } else {
        $datainfo["status"] = 0;
      }
    } elseif ($type == 2) {
      $company_id = $this->user_arr['company_id'];
      //获取公司下班时间
      $this->load->model('attendance_set_model');
      $this->attendance_set_model->set_select_fields(array("end_time"));
      $set_info = $this->attendance_set_model->get_one_by("company_id = {$company_id}");
      //下班时间
      $end_time = $set_info['end_time'] ? trim($set_info['end_time']) : "";
      $time = substr(trim($post_param['datetime1']), 11);
      if ($end_time && $time >= $end_time) {
        $datainfo["status"] = 1;
      } else {
        $datainfo["status"] = 0;
      }
    }
    if (in_array($type, array(3, 4))) {
      $datetime2 = trim($post_param['datetime2']);
      if ($datetime2) {
        $datetime1 = trim($post_param['datetime1']);
        if ($datetime1 >= $datetime2) {
          echo json_encode(array('result' => 'no', "msg" => "开始时间不能小于结束时间"));
          exit;
        }
        $datainfo["datetime2"] = $datetime2;
        $datainfo["status"] = 1;
      } else {
        $datainfo["status"] = 0;
      }
    }
    //修改
    $rs = $this->attendance_model->update_by_id($datainfo, $id);
    if ($rs) {
      echo json_encode(array('result' => 'ok'));
      exit;
    } else {
      echo json_encode(array('result' => 'no', "msg" => "修改失败"));
      exit;
    }
  }

  public function out()
  {
    //模板使用数据
    $data = array();

    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;

    $company_id = $this->user_arr['company_id'];

    //获取所有分公司数组
    $this->load->model('api_broker_model');
    $agencys = $this->api_broker_model->get_agencys_by_company_id($company_id);
    $data['agencys1'] = $agencys;

    $data['user_arr'] = $this->user_arr;

    //查询房源条件
    $cond_where = "";

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    //权限条件($func_area  1 本人  2 门店  3公司)
    $func_area = $this->user_func_permission['area'] ? $this->user_func_permission['area'] : 1;
    $data['func_area'] = $func_area;

    if ($func_area == 1) {
      $broker_id = $this->user_arr['broker_id'];
      //权限条件
      $cond_where .= "`attendance`.broker_id = {$broker_id}";
    } else if ($func_area == 2) {
      $agency_id = $this->user_arr['agency_id'];
      //权限条件
      $cond_where .= "`attendance`.agency_id = {$agency_id}";

      //获取经纪人列表数组
      $this->load->model('api_broker_model');
      $brokers = $this->api_broker_model->get_brokers_agency_id($agency_id);
      $data['brokers'] = $brokers;
    } else if ($func_area == 3) {
      $company_id = $this->user_arr['company_id'];

      //获取所有分公司数组
      $this->load->model('api_broker_model');
      $agencys = $this->api_broker_model->get_agencys_by_company_id($company_id);
      $data['agencys'] = $agencys;

      //所有分公司id数组
      $agency_ids = array();
      foreach ($agencys as $k => $v) {
        $agency_ids[] = $v['agency_id'];
      }
      if (is_full_array($agency_ids)) {
        $agency_ids_str = implode(",", $agency_ids);
        //权限条件
        if (empty($post_param['agency_id'])) {
          $cond_where .= "`attendance`.agency_id in({$agency_ids_str})";
        }
      }

      //门店
      $agency_id = isset($post_param['agency_id']) ? intval($post_param['agency_id']) : 0;
      if ($agency_id) {
        //获取经纪人列表数组
        $this->load->model('api_broker_model');
        $brokers = $this->api_broker_model->get_brokers_agency_id($agency_id);
        $data['brokers'] = $brokers;
      }
    }

    $cond_where .= " AND `attendance`.type = 4";

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str1($post_param);
    $cond_where .= $cond_where_ext;

    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);

    //符合条件的总行数
    $this->_total_count =
      $this->attendance_model->count_by($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->attendance_model->get_list_by($cond_where, $this->_offset, $this->_limit);
    $data['list'] = $list;

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
    $data['page_title'] = '外出管理';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,' . 'mls/css/v1.0/myStyle.css,' . 'mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/radio_checkbox_mod.js');
    $this->view('attendance/out', $data);
  }

  private function _get_cond_str1($form_param)
  {
    $cond_where = '';
    //门店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND `attendance`.agency_id = '" . $agency_id . "'";
    }
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($broker_id) {
      $cond_where .= " AND `attendance`.broker_id = '" . $broker_id . "'";
    }
    //外出时间
    $start_time1 = isset($form_param['start_time1']) ? trim($form_param['start_time1']) : "";
    $end_time1 = isset($form_param['end_time1']) ? trim($form_param['end_time1']) : "";
    if ($start_time1 && $end_time1 && $start_time1 > $end_time1) {
      $this->jump(MLS_URL . '/attendance/out_list', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    if ($start_time1) {
      $start_time1 = $start_time1 . " 00:00:00";
      $cond_where .= " AND `attendance`.datetime1 >= '" . $start_time1 . "'";
    }
    if ($end_time1) {
      $end_time1 = $end_time1 . " 23:59:59";
      $cond_where .= " AND `attendance`.datetime1 <= '" . $end_time1 . "'";
    }

    //返回时间
    $start_time2 = isset($form_param['start_time2']) ? trim($form_param['start_time2']) : "";
    $end_time2 = isset($form_param['end_time2']) ? trim($form_param['end_time2']) : "";
    if ($start_time2 && $end_time2 && $start_time2 > $end_time2) {
      $this->jump(MLS_URL . '/attendance/out_list', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    if ($start_time2) {
      $start_time2 = $start_time2 . " 00:00:00";
      $cond_where .= " AND `attendance`.datetime2 >= '" . $start_time2 . "'";
    }
    if ($end_time2) {
      $end_time2 = $end_time2 . " 23:59:59";
      $cond_where .= " AND `attendance`.datetime2 <= '" . $end_time2 . "'";
    }
    return $cond_where;
  }

  /**
   * 删除
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function del($del_id = 0)
  {
    $isajax = $this->input->get('isajax', TRUE);
    if ($isajax) {
      $str = $this->input->get('str', TRUE);
    } else {
      $str = $del_id;
    }

    $str = trim($str);
    $str = trim($str, ',');
    if ($str) {
      $ids = explode(',', $str);
      $rs = $this->attendance_model->del_by_id($ids);
      if ($rs) {
        if ($isajax) {
          echo json_encode(array('result' => 'ok'));
        } else {
          $this->jump(MLS_URL . '/attendance/', '删除成功');
        }
      } else {
        if ($isajax) {
          echo json_encode(array('result' => 'no'));
        } else {
          $this->jump(MLS_URL . '/attendance/', '删除失败');
        }
      }
    }

  }

}
