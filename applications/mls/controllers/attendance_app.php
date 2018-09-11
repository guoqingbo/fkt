<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 管理功能-APP考勤
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      ysz
 */
class attendance_app extends MY_Controller
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
    $this->load->model('attendance_app_model');
    $this->load->model('company_employee_model');
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
    // $data['user_func_menu'] = $this->user_func_menu;
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

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
    if ($post_param['year'] && $post_param['month'] && $post_param['day']) {
      $year = $post_param['year'];
      $month = $post_param['month'];
      $day = $post_param['day'];
    } else {
      $year = date('Y', time());
      $month = intval(date('m', time()));
      $day = intval(date('d', time()));
    }

    $data['year'] = $year;
    $data['month'] = $month;
    $data['day'] = $day;
    //日期查询条件
    $start_date = strtotime($year.'-'.$month.'-'.$day.' 00:00:00');
    $end_date = strtotime($year.'-'.$month.'-'.$day.' 23:59:59');
    //表单提交参数组成的查询条件
    $cond_where .= ' AND datetime >= ' . $start_date . ' AND datetime <= ' . $end_date;

    //获取考勤打卡数据列表内容
    $list = $this->attendance_app_model->get_list_by($cond_where, 0, 0, 'datetime', 'DESC');

    // print_r($list);exit;
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
    $this->_total_count = $this->attendance_app_model->count_by($cond_where1);
    //排序
    $cond_where1 = $cond_where1 . " order by id DESC ";

    //获取员工列表内容
    $broker_all_info = $this->attendance_app_model->get_all_by($cond_where1, $this->_offset, $this->_limit);
    foreach ($broker_all_info as $key => $vo) {
      $broker_info_one = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
      $agency_info = $this->agency_model->get_by_id($broker_info_one['agency_id']);
      $broker_all_info[$key]['agency_name'] = $agency_info['agency_name'];
      $broker_all_info[$key]['broker_name'] = $broker_info_one['truename'];
      $broker_all_info[$key]['attendance_am'] = '-';
      $broker_all_info[$key]['attendance_pm'] = '-';
      $broker_all_info[$key]['position_am'] = '-';
      $broker_all_info[$key]['position_pm'] = '-';
      $broker_all_info[$key]['remarks_am'] = '-';
      $broker_all_info[$key]['remarks_pm'] = '-';

      foreach ($list as $kk => $vv) {
      	if ($vo['broker_id'] == $vv['broker_id']) {
      		if ($vv['type'] == 'am') {
      			$broker_all_info[$key]['attendance_am'] = date('Y-m-d H:i:s',$vv['datetime']);
      			$broker_all_info[$key]['position_am'] = $vv['position'];
      			$broker_all_info[$key]['remarks_am'] = $vv['remarks'];
      		}
      		if ($vv['type'] == 'pm') {
      			$broker_all_info[$key]['attendance_pm'] = date('Y-m-d H:i:s',$vv['datetime']);
      			$broker_all_info[$key]['position_pm'] = $vv['position'];
      			$broker_all_info[$key]['remarks_pm'] = $vv['remarks'];
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
    $data['page_title'] = 'App考勤表';

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

    $this->view('attendance_app/attendance_app', $data);
  }

  public function details($broker_id, $year, $month)
  {
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['year'] = $year;
    $data['month'] = $month;

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
    // $is_check_work = $agency_setting['is_check_work'];

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
    $attendance_info = array();
    $weekArr = array("周一", "周二", "周三", "周四", "周五", "周六", "周日");
    if (mktime(0, 0, 0, $month, 0, $year) <= mktime(0, 0, 0, date("m", time()), 0, date("Y", time()))) {
      for ($i = 1; $i <= $date_past_day; $i++) {
        $ww = date("w", mktime(0, 0, 0, $month, $i, $year));
        if ($ww == '0') {
          $ww = 7;
        }
        if (in_array($ww, $workdays)) {
          $attendance_info[$i - 1]['day'] = $i;
          $attendance_info[$i - 1]['month'] = $month;
          $attendance_info[$i - 1]['year'] = $year;
          $attendance_info[$i - 1]['week'] = $weekArr[$ww - 1];
        }
      }
    }
    rsort($attendance_info);

    //处理添加每天打卡数据
    foreach ($attendance_info as $key => $vo) {
      //日期查询条件
      $start_date = strtotime($vo['year'].'-'.$vo['month'].'-'.$vo['day'].' 00:00:00');
      $end_date = strtotime($vo['year'].'-'.$vo['month'].'-'.$vo['day'].' 23:59:59');
      //表单提交参数组成的查询条件
      $cond_where = 'broker_id = ' . $broker_id . ' AND datetime >= ' . $start_date . ' AND datetime <= ' . $end_date;
      $list = $this->attendance_app_model->get_list_by($cond_where, 0, 0, 'datetime', 'DESC');
      if (is_full_array($list)) {
        //设置默认打卡时间
        $attendance_info[$key]['attendance_am'] = '-';
        $attendance_info[$key]['attendance_pm'] = '-';
        foreach ($list as $k => $v) {
          if ($v['type'] == 'am') {
            $attendance_info[$key]['attendance_am'] = date('H:i',$v['datetime']);
            // $attendance_info[$key]['position_am'] = $v['position'];
            // $attendance_info[$key]['remarks_am'] = $v['remarks'];
          }
          if ($v['type'] == 'pm') {
            $attendance_info[$key]['attendance_pm'] = date('H:i',$v['datetime']);
            // $attendance_info[$key]['position_pm'] = $v['position'];
            // $attendance_info[$key]['remarks_pm'] = $v['remarks'];
          }          
        }
      } else {
        $attendance_info[$key]['attendance_am'] = '未打卡';
        $attendance_info[$key]['attendance_pm'] = '未打卡';
      }
    }

    // 分页参数
    $this->_limit = 6;
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $this->_init_pagination($page, $this->_limit);
    //重构分页数据
    $attendance_info_new = array();
    $attendance_info_new2 = array();
    foreach ($attendance_info as $key => $vo) {
      $attendance_info_new[] = $attendance_info[$key];
    }
    foreach ($attendance_info_new as $key => $vo) {
      if ($key >= $this->_offset && $key < ($this->_offset + $this->_limit)) {
        $attendance_info_new2[$key] = $attendance_info_new[$key];
      }
    }
    // print_r($attendance_info);exit;

    //符合条件的总行数
    $this->_total_count = count($attendance_info);
    $data['attendance_info'] = $attendance_info_new2;
    // $data['is_check_work'] = $is_check_work;
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
    $data['page_title'] = 'App考勤详情页';

    $data['post_param'] = $post_param;

    $this->view('attendance_app/attendance_app_details', $data);

  }

  /**
   * 导出某一天考勤数据
   * @author   ysz
   */
  public function exportAttendance()
  {
    ini_set('memory_limit', '-1');
    $data = array();
    //post参数
    $post_param = $this->input->post(NULL, true);
    $broker_info = $this->user_arr;

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

    if ($post_param['year'] && $post_param['month'] && $post_param['day']) {
      $year = $post_param['year'];
      $month = $post_param['month'];
      $day = $post_param['day'];
    } else {
      $year = date('Y', time());
      $month = intval(date('m', time()));
      $day = intval(date('d', time()));
    }

    //日期查询条件
    $start_date = strtotime($year.'-'.$month.'-'.$day.' 00:00:00');
    $end_date = strtotime($year.'-'.$month.'-'.$day.' 23:59:59');
    //表单提交参数组成的查询条件
    $cond_where .= ' AND datetime >= ' . $start_date . ' AND datetime <= ' . $end_date;

    //获取考勤打卡数据列表内容
    $list = $this->attendance_app_model->get_list_by($cond_where, 0, 0, 'datetime', 'DESC');
    // print_r($list);die;
    $time = time();
    //查询消息的条件
    $cond_where1 = " where expiretime >= {$time} and status = 1 ";
    $cond_where1 .= " and agency_id = " . $data['agency_id'];
    if ($post_param['broker_id']) {
      $cond_where1 .= ' and broker_id = ' . $post_param['broker_id'];
    }
    //排序
    $cond_where1 = $cond_where1 . " order by id DESC ";

    //获取员工列表内容
    $lists = array();
    $broker_all_info = $this->attendance_app_model->get_all_by($cond_where1, $this->_offset, $this->_limit);
    if (!empty($broker_all_info)) {
    	foreach ($broker_all_info as $key => $vo) {
			$broker_info_one = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
			$agency_info = $this->agency_model->get_by_id($broker_info_one['agency_id']);
			$broker_all_info[$key]['agency_name'] = $agency_info['agency_name'];
			$broker_all_info[$key]['broker_name'] = $broker_info_one['truename'];
			$broker_all_info[$key]['attendance_am'] = '-';
			$broker_all_info[$key]['attendance_pm'] = '-';
			$broker_all_info[$key]['position_am'] = '-';
			$broker_all_info[$key]['position_pm'] = '-';
			$broker_all_info[$key]['remarks_am'] = '-';
			$broker_all_info[$key]['remarks_pm'] = '-';

			foreach ($list as $kk => $vv) {
				if ($vo['broker_id'] == $vv['broker_id']) {
					if ($vv['type'] == 'am') {
						$broker_all_info[$key]['attendance_am'] = date('Y-m-d H:i:s',$vv['datetime']);
						$broker_all_info[$key]['position_am'] = $vv['position'];
						$broker_all_info[$key]['remarks_am'] = $vv['remarks'];
					}
					if ($vv['type'] == 'pm') {
						$broker_all_info[$key]['attendance_pm'] = date('Y-m-d H:i:s',$vv['datetime']);
						$broker_all_info[$key]['position_pm'] = $vv['position'];
						$broker_all_info[$key]['remarks_pm'] = $vv['remarks'];
					}
				}
			}
		}
		$lists = array_values($broker_all_info);
    }else{
    	return false;
    }
    // print_r($lists);die;
    //调用PHPExcel第三方类库
    $this->load->library('PHPExcel.php');
    $this->load->library('PHPExcel/IOFactory');
    //创建phpexcel对象
    $objPHPExcel = new PHPExcel();
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); // 用于 2007 格式
    $objWriter->setOffice2003Compatibility(true);

    //设置phpexcel文件内容
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
      ->setLastModifiedBy("Maarten Balliauw")
      ->setTitle("Office 2007 XLSX Test Document")
      ->setSubject("Office 2007 XLSX Test Document")
      ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
      ->setKeywords("office 2007 openxml php")
      ->setCategory("Test result file");

    //设置表格导航属性
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '编号');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '所在部门');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '上午打卡');
	$objPHPExcel->getActiveSheet()->setCellValue('E1', '定位位置');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '备注');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '下午打卡');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '定位位置');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '备注');
    //设置表格的值
    for ($i = 2; $i <= count($lists) + 1; $i++) {

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $i - 1);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $lists[$i - 2]['broker_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $lists[$i - 2]['agency_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $lists[$i - 2]['attendance_am']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $lists[$i - 2]['position_am']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $lists[$i - 2]['remarks_am']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $lists[$i - 2]['attendance_pm']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $lists[$i - 2]['position_pm']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $lists[$i - 2]['remarks_pm']);
    }

    $fileName = 'kaoqin-' . date('Ymd') . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('考勤列表');
    $objPHPExcel->setActiveSheetIndex(0);

    //header("Content-type: text/csv");//重要
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');   //excel 2003
    //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');   //excel 2007
    //header('Content-Disposition: attachment;filename="求购客源.xls"');
    header("Content-Disposition: attachment;filename=\"$fileName\"");
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  }

  /**
   * 导出某一月所有考勤数据
   * @author   ysz
   */
  public function exportAllAttendance()
  {
    ini_set('memory_limit', '-1');
    $data = array();
    //post参数
    $post_param = $this->input->post(NULL, true);
    $broker_info = $this->user_arr;

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

    if ($post_param['year'] && $post_param['month'] && $post_param['day']) {
      $year = $post_param['year'];
      $month = $post_param['month'];
      $day = $post_param['day'];
    } else {
      $year = date('Y', time());
      $month = intval(date('m', time()));
      $day = date('t',strtotime($year.'-'.$month));
    }

    //日期查询条件
    $start_date = strtotime($year.'-'.$month.'-'.'01'.' 00:00:00');
    $end_date = strtotime($year.'-'.$month.'-'.$day.' 23:59:59');
    //表单提交参数组成的查询条件
    $cond_where .= ' AND datetime >= ' . $start_date . ' AND datetime <= ' . $end_date;

    //获取考勤打卡数据列表内容
    $list = $this->attendance_app_model->get_list_by($cond_where, 0, 0, 'datetime', 'DESC');
    // print_r($list);die;
    $time = time();
    //查询消息的条件
    $cond_where1 = " where expiretime >= {$time} and status = 1 ";
    $cond_where1 .= " and agency_id = " . $data['agency_id'];
    if ($post_param['broker_id']) {
      $cond_where1 .= ' and broker_id = ' . $post_param['broker_id'];
    }
    //排序
    $cond_where1 = $cond_where1 . " order by id DESC ";

    //获取员工列表内容
    $lists = array();
    $broker_all_info = $this->attendance_app_model->get_all_by($cond_where1);
    // print_r($broker_all_info);die;
    if (!empty($broker_all_info)) {
      foreach ($broker_all_info as $key => $vo) {
        $broker_info_one = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
        $agency_info = $this->agency_model->get_by_id($broker_info_one['agency_id']);
        $broker_all_info[$key]['agency_name'] = $agency_info['name'];
        $broker_all_info[$key]['broker_name'] = $broker_info_one['truename'];
      }
      // $broker_all_info = array_values($broker_all_info);
      $i=1;
      while ( $i<=$day ) {
        $brokerInfo = array();
        foreach($broker_all_info as $key => $vo) {
          $brokerInfo[$key]['date'] = $year.'-'.$month.'-'.(($i<10)?('0'.$i):$i);
          $brokerInfo[$key]['agency_name'] = $vo['agency_name'];
          $brokerInfo[$key]['broker_name'] = $vo['broker_name'];
          $brokerInfo[$key]['attendance_am'] = '-';
          $brokerInfo[$key]['attendance_pm'] = '-';
          $brokerInfo[$key]['position_am'] = '-';
          $brokerInfo[$key]['position_pm'] = '-';
          $brokerInfo[$key]['remarks_am'] = '-';
          $brokerInfo[$key]['remarks_pm'] = '-'; 

          // 设置该日时间范围
          $day_start = strtotime($year.'-'.$month.'-'.$i.' 00:00:00');
          $day_end = strtotime($year.'-'.$month.'-'.$i.' 23:59:59');

          if ($list) {
            foreach ($list as $kk => $vv) {
              if ($vo['broker_id'] == $vv['broker_id']) {
                if ($vv['type'] == 'am'&&$vv['datetime'] >= $day_start&&$vv['datetime'] <= $day_end) {
                  $brokerInfo[$key]['attendance_am'] = date('Y-m-d H:i:s',$vv['datetime']);
                  $brokerInfo[$key]['position_am'] = $vv['position'];
                  $brokerInfo[$key]['remarks_am'] = $vv['remarks'];
                }
                if ($vv['type'] == 'pm'&&$vv['datetime'] >= $day_start&&$vv['datetime'] <= $day_end) {
                  $brokerInfo[$key]['attendance_pm'] = date('Y-m-d H:i:s',$vv['datetime']);
                  $brokerInfo[$key]['position_pm'] = $vv['position'];
                  $brokerInfo[$key]['remarks_pm'] = $vv['remarks'];
                }
              }
            }
          } 
          $brokerInfo = array_values($brokerInfo); 
        }
        // print_r($brokerInfo);die;
        $lists = array_merge($lists,$brokerInfo);
        $i++;
      }
    }else{
      return false;
    }
    // print_r(json_encode($lists));die;
    //调用PHPExcel第三方类库
    $this->load->library('PHPExcel.php');
    $this->load->library('PHPExcel/IOFactory');
    //创建phpexcel对象
    $objPHPExcel = new PHPExcel();
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); // 用于 2007 格式
    $objWriter->setOffice2003Compatibility(true);

    //设置phpexcel文件内容
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
      ->setLastModifiedBy("Maarten Balliauw")
      ->setTitle("Office 2007 XLSX Test Document")
      ->setSubject("Office 2007 XLSX Test Document")
      ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
      ->setKeywords("office 2007 openxml php")
      ->setCategory("Test result file");

    //设置表格导航属性
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '日期');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '所在部门');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '上午打卡');
  $objPHPExcel->getActiveSheet()->setCellValue('E1', '定位位置');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '备注');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '下午打卡');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '定位位置');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '备注');
    //设置表格的值
    for ($i = 2; $i <= count($lists) + 1; $i++) {

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $lists[$i - 2]['date']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $lists[$i - 2]['broker_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $lists[$i - 2]['agency_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $lists[$i - 2]['attendance_am']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $lists[$i - 2]['position_am']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $lists[$i - 2]['remarks_am']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $lists[$i - 2]['attendance_pm']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $lists[$i - 2]['position_pm']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $lists[$i - 2]['remarks_pm']);
    }

    $fileName = 'kaoqin-' . date('Ymd') . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('考勤列表');
    $objPHPExcel->setActiveSheetIndex(0);

    //header("Content-type: text/csv");//重要
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');   //excel 2003
    //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');   //excel 2007
    //header('Content-Disposition: attachment;filename="求购客源.xls"');
    header("Content-Disposition: attachment;filename=\"$fileName\"");
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  }

}
/* End of file attendance_app.php */
/* Location: ./applications/mls/controllers/attendance_app.php */
