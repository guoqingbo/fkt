<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 营销中心-业主预约
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class check_work extends MY_Controller
{
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
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('agency_model');
    $this->load->model('check_work_model');
    $this->load->model('company_employee_model');
    $this->load->model('organization_model');
    //加载房源标题模板类
    $this->load->model('broker_info_model');
    $this->load->model('api_broker_model');

    //权限
    if (is_full_array($this->user_arr)) {
      $this->load->model('broker_permission_model');
      $this->broker_permission_model->set_broker_id($this->user_arr['broker_id'], $this->user_arr['company_id']);
    }
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

  public function index()
  {
    //模板使用数据
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    //获取系统默认基本设置
    $this->load->model('agency_basic_setting_model');
    $default_base_data = $this->agency_basic_setting_model->get_default_data();
    //获取当前门店基本设置
    $agency_base_data = $this->agency_basic_setting_model->get_data_by_agency_id($broker_info['agency_id']);

    if (!empty($agency_base_data["0"])) {
      $agency_base_data["0"]['work_day'] = explode(',', $agency_base_data["0"]['work_day']);
      $agency_setting = $agency_base_data["0"];
    } else {
      $default_base_data["0"]['work_day'] = explode(',', $default_base_data["0"]['work_day']);
      $agency_setting = $default_base_data["0"];
    }

    //工作日
    $workdays = $agency_setting['work_day'];
    foreach ($workdays as $key => $vo) {
      if ($vo == 7) {
        $workdays[$key] = 0;
      }
    }
    //是否早晚打卡
    $is_check_work = $agency_setting['is_check_work'];


    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    //print_r($post_param);
    //表单提交参数组成的查询条件
    if ($post_param['agency_id']) {
      $data['agency_id'] = $post_param['agency_id'];
    } else {
      $data['agency_id'] = $broker_info['agency_id'];
    }
    if ($post_param['broker_id']) {
      $cond_where .= 'broker_id = ' . $post_param['broker_id'];
      $data['broker_id'] = $post_param['broker_id'];
    } else {
      $broker_all_data = $this->broker_info_model->get_by_agency_id($data['agency_id']);
      $broker_ids = array();
      foreach ($broker_all_data as $key => $vo) {
        $broker_ids[] = $vo['broker_id'];
      }
      if (is_full_array($broker_ids)) {
        $cond_where .= 'broker_id in (' . implode(',', $broker_ids) . ')';
      } else {
        $cond_where .= 'broker_id > 0';
      }
    }
    $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($data['agency_id']);
    $company_info = $this->agency_model->get_by_id($broker_info['company_id']);
    //门店信息
    $all_access_agency_ids = '';
    if (in_array($broker_info['role_level'], array(1, 2, 3, 4, 5))) {
      $agency_info_array = $this->agency_model->get_children_by_company_id($company_info['id']);
      //print_r($agency_info_array);
      if (is_full_array($agency_info_array)) {
        foreach ($agency_info_array as $k => $v) {
          $all_access_agency_ids .= $v['id'] . ',';
        }
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      }
    } else {
      $all_access_agency_ids = $broker_info['agency_id'];
    }
    $data['agency_list'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
    //构建日历
    if ($post_param['year'] && $post_param['month']) {
      $year = $post_param['year'];
      $month = $post_param['month'];
    } else {
      $year = date('Y', time());
      $month = intval(date('m', time()));
    }
    $daysInMonth = date("t", mktime(0, 0, 0, $month, 1, $year)); //获得当月的总天数
    $firstDay = date("w", mktime(0, 0, 0, $month, 1, $year));  //获得每个月的第一天
    //$firstDay -= 1;
    $tempDays = $firstDay + $daysInMonth; //计算数组中的日历表格数
    $weeksInMonth = ceil($tempDays / 7); //获得表格行数
    //创建一个二维数组用来存放日期信息
    for ($j = 0; $j < $weeksInMonth; $j++) {
      for ($i = 0; $i < 7; $i++) {
        $counter++;
        $week[$j][$i] = $counter;
        //日期偏移量
        $week[$j][$i] -= $firstDay;
        if (($week[$j][$i] < 1) || ($week[$j][$i] > $daysInMonth)) {
          $week [$j] [$i] = "";
        }
      }
    }
    $data['year'] = $year;
    $data['month'] = $month;
    //计算当月实际上班天数
    $worknum = 0;
    foreach ($week as $ke => $val) {
      for ($i = 0; $i < 7; $i++) {
        if ($val[$i] && in_array($i, $workdays)) {
          $worknum++;
        }
      }
    }
    //当月实际已过日期
    if (date('Y', time()) != $year || intval(date('m', time())) != $month) {
      $date_past = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
      $date_past_day = $worknum;
    } else {
      $date_past = date('Y-m-d', time() - 3600 * 24);
      $date_past_day = date('d', time() - 3600 * 24);
    }
    //表单提交参数组成的查询条件
    $cond_where .= ' AND year = ' . $year . ' AND month = ' . $month;

    $list_1 = array();
    //获取考勤打卡数据列表内容
    $list = $this->check_work_model->get_list_by($cond_where, 0, 0, 'createtime', 'ASC');
    //print_r($list);exit;
    foreach ($list as $key => $vo) {
      $list_1[$vo['broker_id']][] = $list[$key];
    }
    //print_r($list_1);exit;
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $this->_init_pagination($page, $this->_limit);
    $time = time();
    //查询消息的条件
    $cond_where1 = " where expiretime >= {$time} and status = 1 ";
    $cond_where1 .= " and agency_id = " . $data['agency_id'];
    if ($post_param['broker_id']) {
      $cond_where1 .= ' and broker_id = ' . $post_param['broker_id'];
    }
    //符合条件的总行数
    $this->_total_count = $this->check_work_model->count_by($cond_where1);
    //排序
    $cond_where1 = $cond_where1 . " order by id DESC ";

    //获取员工列表内容
    $broker_all_info = $this->check_work_model->get_all_by($cond_where1, $this->_offset, $this->_limit);
    foreach ($broker_all_info as $key => $vo) {
      $broker_info_one = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
      $agency_info = $this->agency_model->get_by_id($broker_info_one['agency_id']);
      $broker_all_info[$key]['agency_name'] = $agency_info['agency_name'];
      $broker_all_info[$key]['broker_name'] = $broker_info_one['truename'];
      $broker_all_info[$key]['s1'] = 0; //上班打卡
      $broker_all_info[$key]['s2'] = 0; //下班打卡
      $broker_all_info[$key]['s3'] = 0; //请假打卡
      $broker_all_info[$key]['s4'] = 0; //外出打卡
      $broker_all_info[$key]['s5'] = 0; //迟到打卡
      $broker_all_info[$key]['s6'] = 0; //早退打卡
      if ($year == date('Y', time()) && $month > date('m', time())) {
        $broker_all_info[$key]['s7'] = 0;
      } else {
        if ($is_check_work) {
          $broker_all_info[$key]['s7'] = $date_past_day * 2; //未打卡
        } else {
          $broker_all_info[$key]['s7'] = $date_past_day; //未打卡
        }
      }
      foreach ($list_1 as $kk => $vv) {
        if ($is_check_work) {
          if ($kk == $vo['broker_id']) {
            foreach ($vv as $k => $v) {
              if ($v['status'] == 1) {
                $broker_all_info[$key]['s1']++;
              } elseif ($v['status'] == 2) {
                $broker_all_info[$key]['s2']++;
              } elseif ($v['status'] == 5) {
                $broker_all_info[$key]['s5']++;
              } elseif ($v['status'] == 6) {
                $broker_all_info[$key]['s6']++;
              } elseif ($v['status'] == 4) {
                if (date('H:i:s', $v['ltime_down']) < '12:00:00') {
                  $broker_all_info[$key]['s4'] += 0.5;
                } elseif (date('H:i:s', $v['ltime_up']) > '12:00:00') {
                  $broker_all_info[$key]['s4'] += 0.5;
                } else {
                  $broker_all_info[$key]['s4'] += 1;
                }
              } elseif ($v['status'] == 3) {
                $up = explode('-', date('Y-m-d', $v['ltime_up']));
                $down = explode('-', date('Y-m-d', $v['ltime_down']));
                if ($up[0] == $v['year'] && intval($up[1]) == $v['month'] && intval($up[2]) == $v['day'] && $down[0] == $v['year'] && intval($down[1]) == $v['month'] && intval($down[2]) == $v['day']) {
                  if (date('H:i:s', $v['ltime_down']) < '12:00:00') {
                    $broker_all_info[$key]['s3'] += 0.5;
                  } elseif (date('H:i:s', $v['ltime_up']) > '12:00:00') {
                    $broker_all_info[$key]['s3'] += 0.5;
                  } else {
                    $broker_all_info[$key]['s3'] += 1;
                  }
                } elseif ($up[0] == $v['year'] && intval($up[1]) == $v['month'] && intval($up[2]) == $v['day'] && (intval($down[1]) != $v['month'] || intval($down[2]) != $v['day'])) {
                  if (date('H:i:s', $v['ltime_up']) > '12:00:00') {
                    $broker_all_info[$key]['s3'] += 0.5;
                  } else {
                    $broker_all_info[$key]['s3'] += 1;
                  }
                } elseif ($down[0] == $v['year'] && intval($down[1]) == $v['month'] && intval($down[2]) == $v['day'] && (intval($up[1]) != $v['month'] || intval($up[2]) != $v['day'])) {
                  if (date('H:i:s', $v['ltime_down']) < '12:00:00') {
                    $broker_all_info[$key]['s3'] += 0.5;
                  } else {
                    $broker_all_info[$key]['s3'] += 1;
                  }
                } else {
                  $broker_all_info[$key]['s3'] += 1;
                }
              }
            }
          }
          $broker_all_info[$key]['s7'] = $date_past_day * 2 - $broker_all_info[$key]['s1'] * 0.5 - $broker_all_info[$key]['s2'] * 0.5 - $broker_all_info[$key]['s3'] - $broker_all_info[$key]['s4'] - $broker_all_info[$key]['s5'] * 0.5 - $broker_all_info[$key]['s6'] * 0.5;
          if ($broker_all_info[$key]['s7'] < 0) {
            $broker_all_info[$key]['s7'] = 0;
          }
        } else {
          if ($kk == $vo['broker_id']) {
            foreach ($vv as $k => $v) {
              if ($v['status'] == 1) {
                $broker_all_info[$key]['s1']++;
              } elseif ($v['status'] == 5) {
                $broker_all_info[$key]['s5']++;
              } elseif ($v['status'] == 4) {
                if (date('H:i:s', $v['ltime_down']) < '12:00:00') {
                  $broker_all_info[$key]['s4'] += 0.5;
                } elseif (date('H:i:s', $v['ltime_up']) > '12:00:00') {
                  $broker_all_info[$key]['s4'] += 0.5;
                } else {
                  $broker_all_info[$key]['s4'] += 1;
                }
              } elseif ($v['status'] == 3) {
                $up = explode('-', date('Y-m-d', $v['ltime_up']));
                $down = explode('-', date('Y-m-d', $v['ltime_down']));
                if ($up[0] == $v['year'] && intval($up[1]) == $v['month'] && intval($up[2]) == $v['day'] && $down[0] == $v['year'] && intval($down[1]) == $v['month'] && intval($down[2]) == $v['day']) {
                  if (date('H:i:s', $v['ltime_down']) < '12:00:00') {
                    $broker_all_info[$key]['s3'] += 0.5;
                  } elseif (date('H:i:s', $v['ltime_up']) > '12:00:00') {
                    $broker_all_info[$key]['s3'] += 0.5;
                  } else {
                    $broker_all_info[$key]['s3'] += 1;
                  }
                } elseif ($up[0] == $v['year'] && intval($up[1]) == $v['month'] && intval($up[2]) == $v['day'] && (intval($down[1]) != $v['month'] || intval($down[2]) != $v['day'])) {
                  if (date('H:i:s', $v['ltime_up']) > '12:00:00') {
                    $broker_all_info[$key]['s3'] += 0.5;
                  } else {
                    $broker_all_info[$key]['s3'] += 1;
                  }
                } elseif ($down[0] == $v['year'] && intval($down[1]) == $v['month'] && intval($down[2]) == $v['day'] && (intval($up[1]) != $v['month'] || intval($up[2]) != $v['day'])) {
                  if (date('H:i:s', $v['ltime_down']) < '12:00:00') {
                    $broker_all_info[$key]['s3'] += 0.5;
                  } else {
                    $broker_all_info[$key]['s3'] += 1;
                  }
                } else {
                  $broker_all_info[$key]['s3'] += 1;
                }
              }
            }
          }
          $broker_all_info[$key]['s7'] = $date_past_day - $broker_all_info[$key]['s1'] - $broker_all_info[$key]['s3'] - $broker_all_info[$key]['s4'] - $broker_all_info[$key]['s5'];
          if ($broker_all_info[$key]['s7'] < 0) {
            $broker_all_info[$key]['s7'] = 0;
          }
        }
      }
    }
    $data['broker_info'] = $broker_all_info;
    //print_r($data['broker_info']);exit;
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
    $data['page_title'] = '考勤管理表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/guest_disk.css,mls/css/v1.0/house_manage.css'
      . ',mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/scrollPic.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house.js');

    $data['post_param'] = $post_param;

    $this->view('check_work/check_work', $data);
  }


  public function details($broker_id, $year, $month, $none, $late, $early, $leave)
  {
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['year'] = $year;
    $data['month'] = $month;
    $data['none'] = $none;
    $data['late'] = $late;
    $data['early'] = $early;
    $data['leave'] = $leave;
    //个人基本信息
    $broker_info = $this->company_employee_model->get_broker_by_id($broker_id);
    $agency_info = $this->agency_model->get_by_id($broker_info['agency_id']);
    $broker_info['agency_name'] = $agency_info['name'];
    $data['broker_info'] = $broker_info;

    //获取系统默认基本设置
    $default_base_data = $this->agency_basic_setting_model->get_default_data();
    //获取当前门店基本设置
    $agency_base_data = $this->agency_basic_setting_model->get_data_by_agency_id($broker_info['agency_id']);

    if (!empty($agency_base_data["0"])) {
      $agency_base_data["0"]['work_day'] = explode(',', $agency_base_data["0"]['work_day']);
      $agency_setting = $agency_base_data["0"];
    } else {
      $default_base_data["0"]['work_day'] = explode(',', $default_base_data["0"]['work_day']);
      $agency_setting = $default_base_data["0"];
    }

    //工作日
    $workdays = $agency_setting['work_day'];
    //是否早晚打卡
    $is_check_work = $agency_setting['is_check_work'];

    //当月实际已过天数和日期
    if (date('Y', time()) != $year || intval(date('m', time())) != $month) {
      $date_past = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
      $date_past_day = date("t", mktime(0, 0, 0, $month, 1, $year));
    } else {
      //$date_past = date('Y-m-d',time()-3600*24);
      //$date_past_day = date('d',time()-3600*24);
      $date_past = date('Y-m-d', time() - 3600 * 24);
      $date_past_day = date('d', time());
    }
    //构建当前月数据
    $work_info = array();
    $weekArr = array("周一", "周二", "周三", "周四", "周五", "周六", "周日");
    if (mktime(0, 0, 0, $month, 0, $year) <= mktime(0, 0, 0, date("m", time()), 0, date("Y", time()))) {
      for ($i = 1; $i <= $date_past_day; $i++) {
        $ww = date("w", mktime(0, 0, 0, $month, $i, $year));
        if ($ww == '0') {
          $ww = 7;
        }
        if (in_array($ww, $workdays)) {
          $work_info[$i - 1]['day'] = $i;
          $work_info[$i - 1]['month'] = $month;
          $work_info[$i - 1]['year'] = $year;
          $work_info[$i - 1]['week'] = $weekArr[$ww - 1];
        }
      }
    }
    //处理添加每天打卡数据
    foreach ($work_info as $key => $vo) {
      $cond_where = 'broker_id = ' . $broker_id . ' AND year = ' . $year . ' AND month = ' . $month . ' AND day = ' . $vo['day'];
      $list = $this->check_work_model->get_list_by($cond_where, 0, 0, 'createtime', 'ASC');
      if (is_full_array($list)) {
        foreach ($list as $k => $v) {
          if ($is_check_work) {//判断是否早晚打卡

          }
          if ($v['status'] == 1) {
            $work_info[$key]['cktime1'] = date('H:i:s', $v['clocktime']);
          } elseif ($v['status'] == 2) {
            $work_info[$key]['cktime2'] = date('H:i:s', $v['clocktime']);
          } elseif ($v['status'] == 5) {
            $work_info[$key]['cktime5'] = date('H:i:s', $v['clocktime']);
          } elseif ($v['status'] == 6) {
            $work_info[$key]['cktime6'] = date('H:i:s', $v['clocktime']);
          } elseif ($v['status'] == 3) {
            if (date('Y-m-d H:i:s', $v['ltime_up']) > date('Y-m-d H:i:s', $v['ltime_down'])) {
              $work_info[$key]['lup3'] = $agency_setting['work_day_up_time'];
            } else {
              $work_info[$key]['lup3'] = date('H:i:s', $v['ltime_up']);
            }
            if (date('Y-m-d', $v['ltime_down']) > date('Y-m-d', mktime(0, 0, 0, $v['month'], $v['day'], $v['year']))) {
              $work_info[$key]['ldown3'] = $agency_setting['work_day_down_time'];
            } else {
              $work_info[$key]['ldown3'] = date('H:i:s', $v['ltime_down']);
            }
          } elseif ($v['status'] == 4) {
            $work_info[$key]['lup4'] = date('H:i:s', $v['ltime_up']);
            $work_info[$key]['ldown4'] = date('H:i:s', $v['ltime_down']);
          }
        }
      } else {
        if (mktime(0, 0, 0, $month, $vo['day'], $year) < mktime(0, 0, 0, date("m", time()), date("d", time()), date("Y", time()))) {
          $work_info[$key]['status'] = 7;//未打卡
        }
      }
    }

    // 分页参数
    $this->_limit = 6;
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $this->_init_pagination($page, $this->_limit);
    //重构分页数据
    $work_info_new = array();
    $work_info_new2 = array();
    foreach ($work_info as $key => $vo) {
      $work_info_new[] = $work_info[$key];
    }
    foreach ($work_info_new as $key => $vo) {
      if ($key >= $this->_offset && $key < ($this->_offset + $this->_limit)) {
        $work_info_new2[$key] = $work_info_new[$key];
      }
    }

    //echo count($work_info);
    //print_r($work_info);exit;

    //符合条件的总行数
    $this->_total_count = count($work_info);
    $data['work_info'] = $work_info_new2;
    $data['is_check_work'] = $is_check_work;
    $data['date_past'] = $date_past;
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
    $data['page_title'] = '考勤管理详情页';

    $data['post_param'] = $post_param;

    $this->view('check_work/check_work_details', $data);

  }

  /*public function index() 暂误删后期可能会用到
  {
       //模板使用数据
      $data = array();
      $data['user_menu'] =$this->user_menu;
      $data['user_func_menu'] =$this->user_func_menu;
      $broker_info = $this->user_arr;
      $broker_id = intval($broker_info['broker_id']);

      //获取系统默认基本设置
      $default_base_data=$this->agency_basic_setting_model->get_default_data();
      //获取当前门店基本设置
      $agency_base_data = $this->agency_basic_setting_model->get_data_by_agency_id($broker_info['agency_id']);

      if(!empty($agency_base_data["0"])){
          $agency_base_data["0"]['work_day'] = explode(',',$agency_base_data["0"]['work_day']);
          $agency_setting=$agency_base_data["0"];
      }else{
          $default_base_data["0"]['work_day'] = explode(',',$default_base_data["0"]['work_day']);
          $agency_setting=$default_base_data["0"];
      }

      //工作日
      $data['workdays'] = $agency_setting['work_day'];
      //是否早晚打卡
      $data['is_check_work'] = $agency_setting['is_check_work'];

      //post参数
      $post_param = $this->input->post( NULL , TRUE );
      //print_r($post_param);

      //表单提交参数组成的查询条件
      if($post_param['agency_id'] && $post_param['broker_id']){
          $data['agency_id'] = $post_param['agency_id'];
          $data['broker_id'] = $post_param['broker_id'];
          $cond_where .= 'broker_id = '.$post_param['broker_id'];
          $broker_info = $this->company_employee_model->get_broker_by_id($post_param['broker_id']);
          $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($post_param['agency_id']);
      }else{
          $data['agency_id'] = $broker_info['agency_id'];
          $data['broker_id'] = $broker_info['broker_id'];
          $cond_where .= 'broker_id = '.$broker_info['broker_id'];
          $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($broker_info['agency_id']);
      }
      $company_info = $this->agency_model->get_by_id($broker_info['company_id']);
      $agency_info = $this->agency_model->get_by_id($broker_info['agency_id']);
      $broker_info['company_name'] = $company_info['name'];
      $broker_info['agency_name'] = $agency_info['name'];
      $data['broker_info'] = $broker_info;

      //门店信息
      $all_access_agency_ids = '';
      if(in_array($broker_info['role_level'],array(1,2,3,4,5))){
          $agency_info_array = $this->agency_model->get_children_by_company_id($company_info['id']);
          //print_r($agency_info_array);
          if(is_full_array($agency_info_array)){
              foreach($agency_info_array as $k => $v){
                  $all_access_agency_ids .= $v['id'].',';
              }
              $all_access_agency_ids = trim($all_access_agency_ids , ',');
          }
      }else{
          $all_access_agency_ids = $broker_info['agency_id'];
      }
      $data['agency_list'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);

      //构建日历
      if($post_param['year'] && $post_param['month']){
          $year = $post_param['year'];
          $month = $post_param['month'];
      }else{
          $year = date('Y',time());
          $month = intval(date('m',time()));
      }
      $daysInMonth = date("t",mktime(0,0,0,$month,1,$year)); //获得当月的总天数
      $firstDay = date("w", mktime(0,0,0,$month,1,$year));  //获得每个月的第一天
      //$firstDay -= 1;
      $tempDays = $firstDay + $daysInMonth; //计算数组中的日历表格数
      $weeksInMonth = ceil($tempDays/7); //获得表格行数
      //创建一个二维数组用来存放日期信息
      for($j=0;$j<$weeksInMonth;$j ++) {
          for($i=0;$i<7;$i++) {
              $counter++;
              $week[$j][$i] = $counter;
              //日期偏移量
              $week[$j][$i] -= $firstDay;
              if (($week[$j][$i] < 1) || ($week[$j][$i] > $daysInMonth)) {
                  $week [$j] [$i] = "";
              }
          }
      }
      //print_r($week);
      $data['year'] = $year;
      $data['month'] = $month;
      $data['week'] = $week;
      //当月实际已过日期
      if(date('Y',time()) != $year || intval(date('m',time())) != $month){
          $data['date_past'] = date('Y-m-d',mktime(0,0,0,$month,1,$year));
      }else{
          $data['date_past'] = date('Y-m-d',time());
      }

      //$cond_where .= ' AND year = '.$year.' AND month = '.$month.' AND day <= '.date('d',time());
      $cond_where .= ' AND year = '.$year.' AND month = '.$month;
      //表单提交参数组成的查询条件


      $day_data_new = array();   //具有数据的天
      //获取考勤打卡数据列表内容
      $list = $this->check_work_model->get_list_by($cond_where,0,0,'createtime','ASC');
      foreach($list as $key=>$vo){
          if($vo['status'] == 1){
              $data1[$vo['day']]['cktime1'] = date('H:i:s',$vo['clocktime']);
          }elseif($vo['status'] == 2){
              $data1[$vo['day']]['cktime2'] = date('H:i:s',$vo['clocktime']);
          }elseif($vo['status'] == 5){
              $data1[$vo['day']]['cktime5'] = date('H:i:s',$vo['clocktime']);
          }elseif($vo['status'] == 6){
              $data1[$vo['day']]['cktime6'] = date('H:i:s',$vo['clocktime']);
          }elseif($vo['status'] == 3){
              if(date('H:i:s',$vo['ltime_up']) > date('H:i:s',$vo['ltime_down'])){
                  $data1[$vo['day']]['lup3'] = $agency_setting['work_day_up_time'];
              }else{
                  $data1[$vo['day']]['lup3'] = date('H:i:s',$vo['ltime_up']);
              }
              if(date('d',$vo['ltime_down']) > $vo['day']){
                  $data1[$vo['day']]['ldown3'] = $agency_setting['work_day_down_time'];
              }else{
                  $data1[$vo['day']]['ldown3'] = date('H:i:s',$vo['ltime_down']);
              }
          }elseif($vo['status'] == 4){
              $data1[$vo['day']]['lup4'] = date('H:i:s',$vo['ltime_up']);
              $data1[$vo['day']]['ldown4'] = date('H:i:s',$vo['ltime_down']);
          }
          $day_data_new[] = $vo['day'];
      }
      //print_r($data1);exit;
      $data['data1'] = $data1;
      $data['day_data_new'] = array_unique($day_data_new);

      //页面标题
      $data['page_title'] = '考勤管理表';

      //需要加载的css
      $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
                      .',mls/css/v1.0/guest_disk.css,mls/css/v1.0/house_manage.css'
                      .',mls/css/v1.0/myStyle.css');
      //需要加载的JS
      $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
      //底部JS
      $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/scrollPic.js,'
                          . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house.js');

      $data['post_param']=$post_param;

      $this->view('check_work/check_work',$data);
  }*/


  //根据门店id获取经纪人
  public function broker_list()
  {
    $agency_id = $this->input->get('agency_id', TRUE);
    $agency_id = intval($agency_id);
    $this->load->model('api_broker_model');
    $agency_arr = $this->api_broker_model->get_brokers_agency_id($agency_id);
    echo json_encode($agency_arr);
  }

  /**
   * 考勤详情
   *
   * @access  public
   * @param  void
   * @return  void
   */
  function work_detail()
  {
    $post_param = $this->input->post(NULL, TRUE);
    if ($post_param['is_check_work']) {
      $where = 'broker_id =' . $post_param['broker_id'] . ' and year=' . $post_param['year'] . ' and month=' . $post_param['month'] . ' and day=' . $post_param['day'];
    } else {
      $where = 'broker_id =' . $post_param['broker_id'] . ' and year=' . $post_param['year'] . ' and month=' . $post_param['month'] . ' and day=' . $post_param['day'] . ' and status in (1,3,4,5)';
    }
    $work_info = $this->check_work_model->get_list_by($where, 0, 0, 'createtime', 'ASC');
    foreach ($work_info as $key => $vo) {
      $work_info[$key]['createtime'] = date("H:i", $vo['createtime']);
      $work_info[$key]['ltime_up'] = date("Y-m-d H:i:s", $vo['ltime_up']);
      $work_info[$key]['ltime_down'] = date("Y-m-d H:i:s", $vo['ltime_down']);
    }
    $data['work_info'] = $work_info;
    $data['year'] = $post_param['year'];
    $data['month'] = $post_param['month'];
    $data['day'] = $post_param['day'];

    echo json_encode($data);
  }

}
/* End of file entrust.php */
/* Location: ./applications/mls/controllers/entrust.php */
