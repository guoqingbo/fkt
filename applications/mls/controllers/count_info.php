<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 统计分析
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Count_info extends MY_Controller
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

  public function __construct()
  {
    parent::__construct();
    $this->load->model('api_broker_model');
    $this->load->model('agency_model');
    $this->load->model('check_work_model');
    $this->load->model('company_employee_model');
    $this->load->model('contract_divide_model');
    //加载合同模型类
    $this->load->model('contract_model');
    //加载托管合同模型类
    $this->load->model('collocation_contract_model');
    //加载托管出租合同模型类
    $this->load->model('collocation_rent_contract_model');
    //站点发布统计模型类
    $this->load->model('publish_count_num_model');
    //站点刷新log模型类
    $this->load->model('group_refresh_model');
    //群发网站站点模型类
    $this->load->model('site_model');
  }

  public function index($count_type = 0)
  {
    if ($count_type == 1) {
      $this->_house_count();//房源统计
    } elseif ($count_type == 2) {
      $this->_customer_count();//客源统计
    } else {
      $this->_index();//工作统计
    }
  }

  private function _index()
  {
    $this->load->model('count_log_model');
    $this->load->model('count_num_model');

    $data_view = array();
    $data_view['user_menu'] = $this->user_menu;
    $broker_info = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);
    $pg = $post_param['page'];

    $where = $broker_info['company_id'] > 0 ? 'company_id = ' . $broker_info['company_id'] : 'company_id = 0';

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    $role_id = $broker_info['role_id'];
    $this->load->model('permission_company_group_model');
    $role_data = $this->permission_company_group_model->get_one_by(array('id' => $role_id));
    if (is_full_array($role_data)) {
      $system_group_id = intval($role_data['system_group_id']);
    }
    $role_level = intval($broker_info['role_level']);
    //武汉站站门店下来逻辑独立，新角色能看到所有门店
    if ('37' == $broker_info['city_id'] && 12 == $role_level) {
      $is_other_agency = false;
    } else {
      $is_other_agency = true;
    }
    //店长以下的经纪人 获取部门
    if (is_int($role_level) && $role_level > 7 && $is_other_agency) {//店长以下
      $data_view['agency_info1'] = array('agency_name' => $broker_info['agency_name'], 'broker_name' => $broker_info['truename']);
    } elseif (is_int($role_level) && ($role_level == 6 || $role_level == 7)) {//店长
      $data_view['agency_info2'] = array('agency_name' => $broker_info['agency_name'], 'agency_id' => $broker_info['agency_id']);
    } else {//店长以上
      //根据数据范围，获得门店数据
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_work_count');
      $all_access_agency_ids = '';
      if (is_full_array($access_agency_ids_data)) {
        foreach ($access_agency_ids_data as $k => $v) {
          $all_access_agency_ids .= $v['sub_agency_id'] . ',';
        }
        $all_access_agency_ids .= $this->user_arr['agency_id'];
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      } else {
        $all_access_agency_ids = $this->user_arr['agency_id'];
      }
      $data_view['agency_info'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
    }

    //开始时间
    if (!isset($post_param['start_date_begin'])) {
      $post_param['start_date_begin'] = date('Y-m-d', strtotime("-6 day"));
    }
    if ($post_param['start_date_begin']) {
      $where .= ' and YMD >= "' . $post_param['start_date_begin'] . '"';
    }
    //截止时间
    if (!isset($post_param['start_date_end'])) {
      $post_param['start_date_end'] = date('Y-m-d');
    }
    if ($post_param['start_date_end']) {
      $where .= ' and YMD <= "' . $post_param['start_date_end'] . '"';
    }
    //门店
    if (!isset($post_param['agency_id'])) {
      $post_param['agency_id'] = $broker_info['agency_id'];
    }
    if ($post_param['agency_id']) {
      $where .= ' and agency_id = ' . $post_param['agency_id'];
      $broker = $this->api_broker_model->get_brokers_agency_id($post_param['agency_id']);
      $data_view['broker'] = $broker;
      //用于图表 显示全部数据
      $offset = -1;
      $limit = 10;
    } else {
      //用于图表 只显示10条数据
      $offset = 0;
      $limit = 10;
    }
    //print_r($broker);
    //经纪人
    if (!isset($post_param['broker_id'])) {
      $post_param['broker_id'] = $broker_info['broker_id'];
    }
    if ($post_param['broker_id']) {
      $where .= ' and broker_id = ' . $post_param['broker_id'];
    }

    $data_view['post_param'] = $post_param;
    $where .= ' group by broker_id';
    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);
    $this->_total_count = $this->count_num_model->group_broker_count_by($where);
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data_view['page_list'] = $this->page_list->show('jump');
    //用于分页的信息
    if (is_full_array($post_param['state'])) {
      $count_num = '';
      foreach ($post_param['state'] as $k => $v) {
        if ($k > 0) {
          $count_num .= '+';
        }
        switch ($v) {
          case 1:
            $count_num .= 'insert_num';
            break;
          case 2:
            $count_num .= 'modify_num';
            break;
          case 3:
            $count_num .= 'upload_num';
            break;
          case 4:
            $count_num .= 'look_num';
            break;
          case 5:
            $count_num .= 'looked_num';
            break;
          case 6:
            $count_num .= 'key_num';
            break;
          case 7:
            $count_num .= 'video_num';
            break;
          case 8:
            $count_num .= 'secret_num';
            break;
          case 9:
            $count_num .= 'follow_num';
            break;
        }
      }
    } else {
      $count_num = 'insert_num+modify_num+upload_num+look_num+looked_num+key_num+video_num+secret_num+follow_num';
    }
    // 2017-03-06 alphabeta  将 array('*',...)改为 array('broker_id',...), 不知道对不对
    $select_fields = array('broker_id', 'sum(' . $count_num . ') as sum_num', 'sum(insert_num) as insert_num', 'sum(modify_num) as modify_num', 'sum(upload_num) as upload_num', 'sum(look_num) as look_num', 'sum(looked_num) as looked_num', 'sum(key_num) as key_num', 'sum(video_num) as video_num', 'sum(secret_num) as secret_num', 'sum(follow_num) as follow_num');
    $this->count_num_model->set_select_fields($select_fields);
    $count_num_info = $this->count_num_model->get_all_by($where, $this->_offset, $this->_limit, 'sum_num');
    if (is_full_array($count_num_info)) {
      foreach ($count_num_info as $key => $value) {
        $count_num_info[$key]['insert_num'] = $value['insert_num'] ? $value['insert_num'] : 0;
        $count_num_info[$key]['modify_num'] = $value['modify_num'] ? $value['modify_num'] : 0;
        $count_num_info[$key]['upload_num'] = $value['upload_num'] ? $value['upload_num'] : 0;
        $count_num_info[$key]['look_num'] = $value['look_num'] ? $value['look_num'] : 0;
        $count_num_info[$key]['looked_num'] = $value['looked_num'] ? $value['looked_num'] : 0;
        $count_num_info[$key]['key_num'] = $value['key_num'] ? $value['key_num'] : 0;
        $count_num_info[$key]['video_num'] = $value['video_num'] ? $value['video_num'] : 0;
        $count_num_info[$key]['secret_num'] = $value['secret_num'] ? $value['secret_num'] : 0;
        $count_num_info[$key]['follow_num'] = $value['follow_num'] ? $value['follow_num'] : 0;
        $count_num_info[$key]['sun_num'] = $value['sun_num'] ? $value['sun_num'] : 0;
        $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);
        $count_num_info[$key]['truename'] = $brokers['truename'];
      }
    }
    $data_view['count_num_info'] = $count_num_info;

    //用于图表展示的信息-当门店不限的时候 最多显示10条否则显示全部
    $chat_data = $this->count_num_model->get_all_by($where, $offset, $limit, 'sum_num');
    if (is_full_array($chat_data)) {
      foreach ($chat_data as $key => $value) {
        $chat_data[$key]['insert_num'] = $value['insert_num'] ? $value['insert_num'] : 0;
        $chat_data[$key]['modify_num'] = $value['modify_num'] ? $value['modify_num'] : 0;
        $chat_data[$key]['upload_num'] = $value['upload_num'] ? $value['upload_num'] : 0;
        $chat_data[$key]['look_num'] = $value['look_num'] ? $value['look_num'] : 0;
        $chat_data[$key]['looked_num'] = $value['looked_num'] ? $value['looked_num'] : 0;
        $chat_data[$key]['key_num'] = $value['key_num'] ? $value['key_num'] : 0;
        $chat_data[$key]['video_num'] = $value['video_num'] ? $value['video_num'] : 0;
        $chat_data[$key]['secret_num'] = $value['secret_num'] ? $value['secret_num'] : 0;
        $chat_data[$key]['follow_num'] = $value['follow_num'] ? $value['follow_num'] : 0;
        $chat_data[$key]['sun_num'] = $value['sun_num'] ? $value['sun_num'] : 0;
        $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);
        $chat_data[$key]['truename'] = $brokers['truename'];
      }
    }
    $data_view['chat_data'] = $chat_data;

    //页面标题
    $data_view['page_title'] = '工作统计';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/date_analysis.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/highcharts.js');
    //底部JS
    $data_view['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js');

    $this->view('count_info/index', $data_view);
  }

  /**
   * 详情弹框
   * $state:1信息录入2信息修改3图片上传4堪房5带看6钥匙提交7视频上传8查看保密信息9普通跟进
   * $type:1出售2出租3求购4求租
   */
  public function detail($broker_id, $state, $type)
  {
    $this->load->model('count_log_model');

    $data_view = array();
    $pg = $this->input->post('page');

    $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
    $data_view['truename'] = $brokers['truename'];
    $data_view['broker_id'] = $broker_id;
    $data_view['state'] = $state;
    $data_view['type'] = $type;

    switch ($type) {
      case 1:
        $group_id = 'house_id';
        $model_name = 'sell_house_model';
        if ($state == 5) {
          $this->load->model('buy_customer_model');
        }
        break;
      case 2:
        $group_id = 'house_id';
        $model_name = 'rent_house_model';
        if ($state == 5) {
          $this->load->model('rent_customer_model');
        }
        break;
      case 3:
        $group_id = 'customer_id';
        $model_name = 'buy_customer_model';
        if ($state == 5) {
          $this->load->model('sell_house_model');
        }
        break;
      case 4:
        $group_id = 'customer_id';
        $model_name = 'rent_customer_model';
        if ($state == 5) {
          $this->load->model('rent_house_model');
        }
        break;
    }
    $this->load->model($model_name);

    $this->count_log_model->set_select_fields(array($group_id));
    $where = 'broker_id = ' . $broker_id . ' and state = ' . $state . ' and type = ' . $type;
    //开始时间
    $start_date_begin = $this->input->get('start_date_begin', true);
    $start_date_end = $this->input->get('start_date_end', true);
    if ($start_date_begin) {
      $where .= ' and YMD >= "' . $start_date_begin . '"';
    }
    //截止时间
    if ($start_date_end) {
      $where .= ' and YMD <= "' . $start_date_end . '"';
    }
    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);

    $query = $this->$model_name->query('SELECT COUNT(DISTINCT ' . $group_id . ') as num FROM (`count_log`) WHERE ' . $where);
    $result_array = $query->result();
    $this->_total_count = $result_array[0]->num;

    $where .= ' group by ' . $group_id;

    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data_view['page_list'] = $this->page_list->show('jump');
    $select_fields = array('*', 'count(id)');
    $this->count_log_model->set_select_fields($select_fields);
    $count_log_info = $this->count_log_model->get_all_by($where, $this->_offset, $this->_limit);
    if (is_full_array($count_log_info)) {
      foreach ($count_log_info as $key => $value) {
        if ($state == 5) {//带看
          if ($type == 1 or $type == 3) {
            $this->sell_house_model->set_id($value['house_id']);
            $count_log_info[$key]['house_info'] = $this->sell_house_model->get_info_by_id();
            $this->buy_customer_model->set_id($value['customer_id']);
            $count_log_info[$key]['customer_info'] = $this->buy_customer_model->get_info_by_id();
          } else {
            $this->rent_house_model->set_id($value['house_id']);
            $count_log_info[$key]['house_info'] = $this->rent_house_model->get_info_by_id();
            $this->rent_customer_model->set_id($value['customer_id']);
            $count_log_info[$key]['customer_info'] = $this->rent_customer_model->get_info_by_id();
          }
        } else {
          $this->$model_name->set_id($value[$group_id]);
          $count_log_info[$key]['data_array'] = $this->$model_name->get_info_by_id();
        }
      }
    }
    $data_view['count_log_info'] = $count_log_info;

    if ($type == 1 or $type == 2) {
      $this->load->model('house_config_model');
      //获取出售信息基本配置资料
      $data_view['config'] = $this->house_config_model->get_config();
    } else {
      $conf_customer = $this->$model_name->get_base_conf();
      $data_view['conf_customer'] = $conf_customer;
    }

    //获取区属
    $this->load->model('district_model');
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $data_view['district'][$val['id']] = $val;
    }
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $data_view['street'][$val['id']] = $val;
    }
    //print_r($data_view);exit;
    //页面标题
    $data_view['page_title'] = '统计分析详情';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/date_analysis.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    /***
     * $data_view['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/backspace.js,mls/js/v1.0/customer_list.js');**/
    if ($type == 1 || $type == 2) {
      $data_view['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/backspace.js');
    } else {
      $data_view['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/customer_list.js,mls/js/v1.0/backspace.js');
    }
    $this->view('count_info/detail' . $state, $data_view);
  }

  /**
   * 工作统计-导出到excel表中
   */
  public function export()
  {
    $this->load->model('count_num_model');
    $broker_info = $this->user_arr;
    $where = $broker_info['company_id'] > 0 ? 'company_id = ' . $broker_info['company_id'] : 'broker_id = ' . $broker_info['broker_id'];
    $post_param = $this->input->post(NULL, TRUE);
    if ($post_param['start_date_begin']) {
      $where .= ' and YMD >= "' . $post_param['start_date_begin'] . '"';
    }
    if ($post_param['start_date_end']) {
      $where .= ' and YMD <= "' . $post_param['start_date_end'] . '"';
    }
    //门店
    if (!isset($post_param['agency_id'])) {
      $post_param['agency_id'] = $broker_info['agency_id'];
    }
    if ($post_param['agency_id']) {
      $where .= ' and agency_id = ' . $post_param['agency_id'];
    }
    //经纪人
    if (!isset($post_param['broker_id'])) {
      $post_param['broker_id'] = $broker_info['broker_id'];
    }
    if ($post_param['broker_id']) {
      $where .= ' and broker_id = ' . $post_param['broker_id'];
    }
    $where .= ' group by broker_id';
    $select_fields = array('*', 'sum(insert_num) as insert_num', 'sum(modify_num) as modify_num', 'sum(upload_num) as upload_num', 'sum(look_num) as look_num', 'sum(looked_num) as looked_num', 'sum(key_num) as key_num', 'sum(video_num) as video_num', 'sum(secret_num) as secret_num', 'sum(follow_num) as follow_num');
    $this->count_num_model->set_select_fields($select_fields);
    $count_num_info = $this->count_num_model->get_all_by($where, -1);
    if (is_full_array($count_num_info)) {
      $sum_num = array();
      foreach ($count_num_info as $key => $value) {
        $count_num_info[$key]['insert_num'] = $value['insert_num'] ? $value['insert_num'] : 0;
        $count_num_info[$key]['modify_num'] = $value['modify_num'] ? $value['modify_num'] : 0;
        $count_num_info[$key]['upload_num'] = $value['upload_num'] ? $value['upload_num'] : 0;
        $count_num_info[$key]['look_num'] = $value['look_num'] ? $value['look_num'] : 0;
        $count_num_info[$key]['looked_num'] = $value['looked_num'] ? $value['looked_num'] : 0;
        $count_num_info[$key]['key_num'] = $value['key_num'] ? $value['key_num'] : 0;
        $count_num_info[$key]['video_num'] = $value['video_num'] ? $value['video_num'] : 0;
        $count_num_info[$key]['secret_num'] = $value['secret_num'] ? $value['secret_num'] : 0;
        $count_num_info[$key]['follow_num'] = $value['follow_num'] ? $value['follow_num'] : 0;
        $count_num_info[$key]['sum_num'] = $value['insert_num'] + $value['modify_num'] + $value['upload_num'] + $value['look_num'] + $value['looked_num'] + $value['key_num'] + $value['video_num'] + $value['secret_num'] + $value['follow_num'];

        $sum_num[] = $count_num_info[$key]['sum_num'];
        $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);
        $count_num_info[$key]['truename'] = $brokers['truename'];
        $agencys = $this->api_broker_model->get_by_agency_id($value['agency_id']);
        $count_num_info[$key]['agency_name'] = $agencys['name'];

      }
      array_multisort($sum_num, SORT_DESC, $count_num_info);//二维数组排序
    }

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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '门店名');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '用户名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '信息录入');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '信息修改');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '图片上传');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '堪房');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '带看');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '钥匙提交');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '视频上传');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '查看保密信息');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '普通跟进');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '总量');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '日期');

    if (is_full_array($count_num_info)) {
      foreach ($count_num_info as $key => $value) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($key + 2), $value['agency_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . ($key + 2), $value['truename']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . ($key + 2), $value['insert_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . ($key + 2), $value['modify_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . ($key + 2), $value['upload_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . ($key + 2), $value['look_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . ($key + 2), $value['looked_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . ($key + 2), $value['key_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . ($key + 2), $value['video_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . ($key + 2), $value['secret_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . ($key + 2), $value['follow_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . ($key + 2), $value['sum_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . ($key + 2), $value['YMD']);
      }
    }
    $fileName = time() . "_excel.xls";
    $objPHPExcel->getActiveSheet()->setTitle('工作统计');
    $objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');
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
    // print_r($data);exit;
    $objWriter->save('php://output');
    exit;
  }

  /**
   * 房源统计
   *
   */
  private function _house_count()
  {
    $data_view = array();
    $data_view['user_menu'] = $this->user_menu;
    $broker_info = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);
    $pg = $post_param['page'];

    $where = $broker_info['company_id'] > 0 ? 'company_id = ' . $broker_info['company_id'] : 'company_id = 0';

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    $role_id = $broker_info['role_id'];
    $this->load->model('permission_company_group_model');
    $role_data = $this->permission_company_group_model->get_one_by(array('id' => $role_id));
    if (is_full_array($role_data)) {
      $system_group_id = intval($role_data['system_group_id']);
    }

    $role_level = intval($broker_info['role_level']);
    //武汉站站门店下来逻辑独立，新角色能看到所有门店
    if ('37' == $broker_info['city_id'] && 12 == $role_level) {
      $is_other_agency = false;
    } else {
      $is_other_agency = true;
    }
    //店长以下的经纪人 获取部门
    if (is_int($role_level) && $role_level > 7 && $is_other_agency) {//店长以下
      $data_view['agency_info1'] = array('agency_name' => $broker_info['agency_name'], 'broker_name' => $broker_info['truename']);
    } elseif (is_int($role_level) && ($role_level == 6 || $role_level == 7)) {//店长
      $data_view['agency_info2'] = array('agency_name' => $broker_info['agency_name'], 'agency_id' => $broker_info['agency_id']);
    } else {//店长以上
      //根据数据范围，获得门店数据
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_house_count');
      $all_access_agency_ids = '';
      if (is_full_array($access_agency_ids_data)) {
        foreach ($access_agency_ids_data as $k => $v) {
          $all_access_agency_ids .= $v['sub_agency_id'] . ',';
        }
        $all_access_agency_ids .= $this->user_arr['agency_id'];
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      } else {
        $all_access_agency_ids = $this->user_arr['agency_id'];
      }
      $data_view['agency_info'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
    }

    //config
    if (!isset($post_param['config'])) {
      $post_param['config'] = 1;
    }

    //门店
    if (!isset($post_param['agency_id'])) {
      $post_param['agency_id'] = $broker_info['agency_id'];
    }
    if ($post_param['agency_id']) {
      $where .= ' and agency_id = ' . $post_param['agency_id'];
      $broker = $this->api_broker_model->get_brokers_agency_id($post_param['agency_id']);
      $data_view['broker'] = $broker;
    }
    //经纪人
    if (!isset($post_param['broker_id'])) {
      $post_param['broker_id'] = $broker_info['broker_id'];
    }
    if ($post_param['broker_id']) {
      $where .= ' and broker_id = ' . $post_param['broker_id'];
    }
    //开始时间
    if ($post_param['start_date_begin']) {
      $where .= ' and createtime >= ' . strtotime($post_param['start_date_begin'] . ' 00:00:00');
    }
    //截止时间
    if ($post_param['start_date_end']) {
      $where .= ' and createtime <= ' . strtotime($post_param['start_date_end'] . ' 23:59:59');
    }

    if (!isset($post_param['type'])) {
      $post_param['type'] = 0;
    }
    if ($post_param['config'] == 5 && !$post_param['type']) {
      $post_param['type'] = 1;
    }

    $data_view['post_param'] = $post_param;

    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);

    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $config = $this->house_config_model->get_config();
    //图表数据
    $yAxis = array();
    switch ($post_param['type']) {
      case 0:
        $this->load->model('sell_house_model');
        $this->load->model('rent_house_model');

        $query_rent = $this->sell_house_model->query('SELECT COUNT(DISTINCT broker_id) as sum_num FROM (SELECT DISTINCT broker_id FROM sell_house where ' . $where . ' UNION ALL SELECT DISTINCT broker_id FROM rent_house where ' . $where . ') AS new_table');
        $result_array = $query_rent->result();
        $this->_total_count = $result_array[0]->sum_num;
        $broker_house = array();
        $query = $this->sell_house_model->query('SELECT broker_id,count(broker_id) as sum_num FROM (SELECT broker_id FROM sell_house where ' . $where . ' UNION ALL SELECT broker_id FROM rent_house where ' . $where . ') AS new_table group by broker_id order by sum_num desc limit ' . $this->_offset . ',' . $this->_limit);
        $broker_array = $query->result();
        if (is_full_array($broker_array)) {
          foreach ($broker_array as $key => $value) {
            $broker_house[$key]['broker_id'] = $value->broker_id;
          }
        }
        switch ($post_param['config']) {
          case 1://房源状态：1有效2预定3成交4无效5注销6暂不售/暂不租
            $config['status'][6] = '暂不售/租';
            $data_view['xAxis'] = $config['status'];
            foreach ($data_view['xAxis'] as $key => $value) {
              $status_num_sell = $this->sell_house_model->get_count_by_cond($where . ' and status = ' . $key);
              $status_num_rent = $this->rent_house_model->get_count_by_cond($where . ' and status = ' . $key);
              $yAxis[] = $status_num_sell + $status_num_rent;
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $status_num_sell_broker = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                  $status_num_rent_broker = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                  $broker_house[$k]['status_' . $key] = $status_num_sell_broker + $status_num_rent_broker;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
          case 2://房源来源：1店面2老客户3广告4社区推广5网络6其他
            $data_view['xAxis'] = $config['infofrom'];
            foreach ($data_view['xAxis'] as $key => $value) {
              $infofrom_num_sell = $this->sell_house_model->get_count_by_cond($where . ' and infofrom = ' . $key);
              $infofrom_num_rent = $this->rent_house_model->get_count_by_cond($where . ' and infofrom = ' . $key);
              $yAxis[] = $infofrom_num_sell + $infofrom_num_rent;
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $infofrom_num_sell_broker = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                  $infofrom_num_rent_broker = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                  $broker_house[$k]['infofrom_' . $key] = $infofrom_num_sell_broker + $infofrom_num_rent_broker;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
          case 3://房源区属：
            //获取区属
            $this->load->model('district_model');
            $district = $this->district_model->get_district();
            foreach ($district as $k => $v) {
              $data_view['xAxis'][$v['id']] = $v['district'];
            }
            foreach ($data_view['xAxis'] as $key => $value) {
              $district_num_sell = $this->sell_house_model->get_count_by_cond($where . ' and district_id = ' . $key);
              $district_num_rent = $this->rent_house_model->get_count_by_cond($where . ' and district_id = ' . $key);
              $yAxis[] = $district_num_sell + $district_num_rent;
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $district_num_sell_broker = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and district_id = ' . $key);
                  $district_num_rent_broker = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and district_id = ' . $key);
                  $broker_house[$k]['district_' . $key] = $district_num_sell_broker + $district_num_rent_broker;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
          case 4://物业类型：1住宅2别墅3商铺4写字楼5厂房6仓库7车库
            $data_view['xAxis'] = $config['sell_type'];
            foreach ($data_view['xAxis'] as $key => $value) {
              $sell_type_num_sell = $this->sell_house_model->get_count_by_cond($where . ' and sell_type = ' . $key);
              $sell_type_num_rent = $this->rent_house_model->get_count_by_cond($where . ' and sell_type = ' . $key);
              $yAxis[] = $sell_type_num_sell + $sell_type_num_rent;
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $sell_type_num_sell_broker = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and sell_type = ' . $key);
                  $sell_type_num_rent_broker = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and sell_type = ' . $key);
                  $broker_house[$k]['sell_type_' . $key] = $sell_type_num_sell_broker + $sell_type_num_rent_broker;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
        }
        break;
      case 1://出售
        $this->load->model('sell_house_model');
        $query = $this->sell_house_model->query('SELECT COUNT(DISTINCT broker_id) as num FROM (`sell_house`) WHERE ' . $where);
        $result_array = $query->result();
        $this->_total_count = $result_array[0]->num;

        //分组获取分页列表信息
        $this->sell_house_model->set_search_fields(array('broker_id', 'count(id) as sum_num'));
        $broker_house = $this->sell_house_model->get_list_by_cond($where . ' group by broker_id', $this->_offset, $this->_limit, 'sum_num');
        switch ($post_param['config']) {
          case 1://房源状态：1有效2预定3成交4无效5注销6暂不售/暂不租
            $config['status'][6] = '暂不售';
            $data_view['xAxis'] = $config['status'];
            foreach ($data_view['xAxis'] as $key => $value) {
              //获取图标纵坐标的值
              $yAxis[] = $this->sell_house_model->get_count_by_cond($where . ' and status = ' . $key);
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $status_num = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                  $broker_house[$k]['status_' . $key] = $status_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
          case 2://房源来源：1店面2老客户3广告4社区推广5网络6其他
            $data_view['xAxis'] = $config['infofrom'];
            foreach ($data_view['xAxis'] as $key => $value) {
              $yAxis[] = $this->sell_house_model->get_count_by_cond($where . ' and infofrom = ' . $key);
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $infofrom_num = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                  $broker_house[$k]['infofrom_' . $key] = $infofrom_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
          case 3://房源区属：
            //获取区属
            $this->load->model('district_model');
            $district = $this->district_model->get_district();
            foreach ($district as $k => $v) {
              $data_view['xAxis'][$v['id']] = $v['district'];
            }
            foreach ($data_view['xAxis'] as $key => $value) {
              $yAxis[] = $this->sell_house_model->get_count_by_cond($where . ' and district_id = ' . $key);
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $district_num = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and district_id = ' . $key);
                  $broker_house[$k]['district_' . $key] = $district_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
          case 4://物业类型：1住宅2别墅3商铺4写字楼5厂房6仓库7车库
            $data_view['xAxis'] = $config['sell_type'];
            foreach ($data_view['xAxis'] as $key => $value) {
              $yAxis[] = $this->sell_house_model->get_count_by_cond($where . ' and sell_type = ' . $key);
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $sell_type_num = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and sell_type = ' . $key);
                  $broker_house[$k]['sell_type_' . $key] = $sell_type_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
          case 5://委托类型：1独家代理2多家登记3免责代理4限时5包销6无差
            $data_view['xAxis'] = $config['entrust'];
            foreach ($data_view['xAxis'] as $key => $value) {
              $yAxis[] = $this->sell_house_model->get_count_by_cond($where . ' and entrust = ' . $key);
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $entrust_num = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and entrust = ' . $key);
                  $broker_house[$k]['entrust_' . $key] = $entrust_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
        }
        break;
      case 2://出租
        $this->load->model('rent_house_model');
        $query = $this->rent_house_model->query('SELECT COUNT(DISTINCT broker_id) as num FROM (`rent_house`) WHERE ' . $where);
        $result_array = $query->result();
        $this->_total_count = $result_array[0]->num;
        //分组获取分页列表信息
        $this->rent_house_model->set_search_fields(array('broker_id', 'count(id) as sum_num'));
        $broker_house = $this->rent_house_model->get_list_by_cond($where . ' group by broker_id', $this->_offset, $this->_limit, 'sum_num');
        switch ($post_param['config']) {
          case 1://房源状态：1有效2预定3成交4无效5注销6暂不售/暂不租
            $config['status'][6] = '暂不租';
            $data_view['xAxis'] = $config['status'];
            foreach ($data_view['xAxis'] as $key => $value) {
              $yAxis[] = $this->rent_house_model->get_count_by_cond($where . ' and status = ' . $key);
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $status_num = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                  $broker_house[$k]['status_' . $key] = $status_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
          case 2://房源来源：1店面2老客户3广告4社区推广5网络6其他
            $data_view['xAxis'] = $config['infofrom'];
            foreach ($data_view['xAxis'] as $key => $value) {
              $yAxis[] = $this->rent_house_model->get_count_by_cond($where . ' and infofrom = ' . $key);
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $infofrom_num = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                  $broker_house[$k]['infofrom_' . $key] = $infofrom_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
          case 3://房源区属：
            //获取区属
            $this->load->model('district_model');
            $district = $this->district_model->get_district();
            foreach ($district as $k => $v) {
              $data_view['xAxis'][$v['id']] = $v['district'];
            }
            foreach ($data_view['xAxis'] as $key => $value) {
              $yAxis[] = $this->rent_house_model->get_count_by_cond($where . ' and district_id = ' . $key);
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $district_num = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and district_id = ' . $key);
                  $broker_house[$k]['district_' . $key] = $district_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
          case 4://物业类型：1住宅2别墅3商铺4写字楼5厂房6仓库7车库
            $data_view['xAxis'] = $config['sell_type'];
            foreach ($data_view['xAxis'] as $key => $value) {
              $yAxis[] = $this->rent_house_model->get_count_by_cond($where . ' and sell_type = ' . $key);
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $sell_type_num = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and sell_type = ' . $key);
                  $broker_house[$k]['sell_type_' . $key] = $sell_type_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
          case 5://委托类型：1独家代理2多家登记3免责代理4限时5包销6无差
            $data_view['xAxis'] = $config['rententrust'];
            foreach ($data_view['xAxis'] as $key => $value) {
              $yAxis[] = $this->rent_house_model->get_count_by_cond($where . ' and rententrust = ' . $key);
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $entrust_num = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and rententrust = ' . $key);
                  $broker_house[$k]['entrust_' . $key] = $entrust_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
        }
        break;
    }
    $data_view['yAxis'] = $yAxis;
    $data_view['broker_house'] = $broker_house;

    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data_view['page_list'] = $this->page_list->show('jump');

    //print_r($data_view['yAxis']);exit;
    //页面标题
    $data_view['page_title'] = '房源统计';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/date_analysis.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/highcharts.js');
    //底部JS
    $data_view['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js');

    $this->view('count_info/house_count', $data_view);
  }

  /**
   * 房源统计-导出到excel
   */
  public function house_export($type, $config, $agency_id, $broker_id)
  {
    $broker_info = $this->user_arr;
    $where = $broker_info['company_id'] > 0 ? 'company_id = ' . $broker_info['company_id'] : 'company_id = 0';

    //门店
    if ($agency_id) {
      $where .= ' and agency_id = ' . $agency_id;
    }
    //经纪人
    if ($broker_id) {
      $where .= ' and broker_id = ' . $broker_id;
    }

    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $house_config = $this->house_config_model->get_config();

    switch ($type) {
      case 0:
        $this->load->model('sell_house_model');
        $this->load->model('rent_house_model');

        $broker_house = array();
        $query = $this->sell_house_model->query('SELECT broker_id,count(broker_id) as sum_num FROM (SELECT broker_id FROM sell_house where ' . $where . ' UNION ALL SELECT broker_id FROM rent_house where ' . $where . ') AS new_table group by broker_id order by sum_num desc limit ' . $this->_offset . ',' . $this->_limit);
        $broker_array = $query->result();
        if (is_full_array($broker_array)) {
          foreach ($broker_array as $key => $value) {
            $broker_house[$key]['broker_id'] = $value->broker_id;
          }
        }
        switch ($config) {
          case 1://房源状态：1有效2预定3成交4无效5注销6暂不售/暂不租
            $house_config['status'][6] = '暂不售/租';
            $xAxis = $house_config['status'];
            foreach ($xAxis as $key => $value) {
              $status_num_sell = $this->sell_house_model->get_count_by_cond($where . ' and status = ' . $key);
              $status_num_rent = $this->rent_house_model->get_count_by_cond($where . ' and status = ' . $key);
              $yAxis[] = $status_num_sell + $status_num_rent;
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $status_num_sell_broker = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                  $status_num_rent_broker = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                  $broker_house[$k]['status_' . $key] = $status_num_sell_broker + $status_num_rent_broker;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                  $broker_house[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
          case 2://房源来源：1店面2老客户3广告4社区推广5网络6其他
            $xAxis = $house_config['infofrom'];
            foreach ($xAxis as $key => $value) {
              $infofrom_num_sell = $this->sell_house_model->get_count_by_cond($where . ' and infofrom = ' . $key);
              $infofrom_num_rent = $this->rent_house_model->get_count_by_cond($where . ' and infofrom = ' . $key);
              $yAxis[] = $infofrom_num_sell + $infofrom_num_rent;
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $infofrom_num_sell_broker = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                  $infofrom_num_rent_broker = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                  $broker_house[$k]['infofrom_' . $key] = $infofrom_num_sell_broker + $infofrom_num_rent_broker;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                  $broker_house[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
          case 3://房源区属：
            //获取区属
            $this->load->model('district_model');
            $district = $this->district_model->get_district();
            foreach ($district as $k => $v) {
              $xAxis[$v['id']] = $v['district'];
            }
            foreach ($xAxis as $key => $value) {
              $district_num_sell = $this->sell_house_model->get_count_by_cond($where . ' and district_id = ' . $key);
              $district_num_rent = $this->rent_house_model->get_count_by_cond($where . ' and district_id = ' . $key);
              $yAxis[] = $district_num_sell + $district_num_rent;
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $district_num_sell_broker = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and district_id = ' . $key);
                  $district_num_rent_broker = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and district_id = ' . $key);
                  $broker_house[$k]['district_' . $key] = $district_num_sell_broker + $district_num_rent_broker;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                  $broker_house[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
          case 4://物业类型：1住宅2别墅3商铺4写字楼5厂房6仓库7车库
            $xAxis = $house_config['sell_type'];
            foreach ($xAxis as $key => $value) {
              $sell_type_num_sell = $this->sell_house_model->get_count_by_cond($where . ' and sell_type = ' . $key);
              $sell_type_num_rent = $this->rent_house_model->get_count_by_cond($where . ' and sell_type = ' . $key);
              $yAxis[] = $sell_type_num_sell + $sell_type_num_rent;
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $sell_type_num_sell_broker = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and sell_type = ' . $key);
                  $sell_type_num_rent_broker = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and sell_type = ' . $key);
                  $broker_house[$k]['sell_type_' . $key] = $sell_type_num_sell_broker + $sell_type_num_rent_broker;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                  $broker_house[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
        }
        break;
      case 1://出售
        $this->load->model('sell_house_model');
        //分组获取分页列表信息
        $this->sell_house_model->set_search_fields(array('broker_id', 'count(id) as sum_num'));
        $broker_house = $this->sell_house_model->get_list_by_cond($where . ' group by broker_id', -1, '', 'sum_num');
        switch ($config) {
          case 1://房源状态：1有效2预定3成交4无效5注销6暂不售/暂不租
            $house_config['status'][6] = '暂不售';
            $xAxis = $house_config['status'];
            foreach ($xAxis as $key => $value) {
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $status_num = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                  $broker_house[$k]['status_' . $key] = $status_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                  $broker_house[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
          case 2://房源来源：1店面2老客户3广告4社区推广5网络6其他
            $xAxis = $house_config['infofrom'];
            foreach ($xAxis as $key => $value) {
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $infofrom_num = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                  $broker_house[$k]['infofrom_' . $key] = $infofrom_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                  $broker_house[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
          case 3://房源区属：
            //获取区属
            $this->load->model('district_model');
            $district = $this->district_model->get_district();
            foreach ($district as $k => $v) {
              $xAxis[$v['id']] = $v['district'];
            }
            foreach ($xAxis as $key => $value) {
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $district_num = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and district_id = ' . $key);
                  $broker_house[$k]['district_' . $key] = $district_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                  $broker_house[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
          case 4://物业类型：1住宅2别墅3商铺4写字楼5厂房6仓库7车库
            $xAxis = $house_config['sell_type'];
            foreach ($xAxis as $key => $value) {
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $sell_type_num = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and sell_type = ' . $key);
                  $broker_house[$k]['sell_type_' . $key] = $sell_type_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                  $broker_house[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
          case 5://委托类型：1独家代理2多家登记3免责代理4限时5包销6无差
            $xAxis = $house_config['entrust'];
            foreach ($xAxis as $key => $value) {
              $yAxis[] = $this->sell_house_model->get_count_by_cond($where . ' and entrust = ' . $key);
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $entrust_num = $this->sell_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and entrust = ' . $key);
                  $broker_house[$k]['entrust_' . $key] = $entrust_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
        }
        break;
      case 2://出租
        $this->load->model('rent_house_model');
        //分组获取分页列表信息
        $this->rent_house_model->set_search_fields(array('broker_id', 'count(id) as sum_num'));
        $broker_house = $this->rent_house_model->get_list_by_cond($where . ' group by broker_id', $this->_offset, $this->_limit, 'sum_num');
        switch ($config) {
          case 1://房源状态：1有效2预定3成交4无效5注销6暂不售/暂不租
            $house_config['status'][6] = '暂不租';
            $xAxis = $house_config['status'];
            foreach ($xAxis as $key => $value) {
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $status_num = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                  $broker_house[$k]['status_' . $key] = $status_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                  $broker_house[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
          case 2://房源来源：1店面2老客户3广告4社区推广5网络6其他
            $xAxis = $house_config['infofrom'];
            foreach ($xAxis as $key => $value) {
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $infofrom_num = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                  $broker_house[$k]['infofrom_' . $key] = $infofrom_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                  $broker_house[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
          case 3://房源区属：
            //获取区属
            $this->load->model('district_model');
            $district = $this->district_model->get_district();
            foreach ($district as $k => $v) {
              $xAxis[$v['id']] = $v['district'];
            }
            foreach ($xAxis as $key => $value) {
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $district_num = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and district_id = ' . $key);
                  $broker_house[$k]['district_' . $key] = $district_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                  $broker_house[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
          case 4://物业类型：1住宅2别墅3商铺4写字楼5厂房6仓库7车库
            $xAxis = $house_config['sell_type'];
            foreach ($xAxis as $key => $value) {
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $sell_type_num = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and sell_type = ' . $key);
                  $broker_house[$k]['sell_type_' . $key] = $sell_type_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                  $broker_house[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
          case 5://委托类型：1独家代理2多家登记3免责代理4限时5包销6无差
            $xAxis = $house_config['rententrust'];
            foreach ($xAxis as $key => $value) {
              $yAxis[] = $this->rent_house_model->get_count_by_cond($where . ' and rententrust = ' . $key);
              //获取分页列表的值
              if (is_full_array($broker_house)) {
                foreach ($broker_house as $k => $v) {
                  $entrust_num = $this->rent_house_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and rententrust = ' . $key);
                  $broker_house[$k]['entrust_' . $key] = $entrust_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_house[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
        }
        break;
    }

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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '门店名');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '用户名');
    if (is_full_array($xAxis)) {
      foreach ($xAxis as $key => $value) {
        $x_key = $this->key_value($key - 1, 1);
        $objPHPExcel->getActiveSheet()->setCellValue($x_key, $value);
      }
    }
    if (is_full_array($broker_house)) {
      foreach ($broker_house as $key => $value) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($key + 2), $value['agency_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . ($key + 2), $value['truename']);
        if ($xAxis) {
          foreach ($xAxis as $k => $v) {
            $x_value = $this->key_value($k - 1, $key + 2);
            switch ($config) {
              case 1:
                $y_value = $value['status_' . $k];
                break;
              case 2:
                $y_value = $value['infofrom_' . $k];
                break;
              case 3:
                $y_value = $value['district_' . $k];
                break;
              case 4:
                $y_value = $value['sell_type_' . $k];
                break;
              case 5:
                $y_value = $value['entrust_' . $k];
                break;
            }
            $objPHPExcel->getActiveSheet()->setCellValue($x_value, $y_value);
          }

        }
      }
    }
    $fileName = time() . "_excel.xls";
    $objPHPExcel->getActiveSheet()->setTitle('房源统计');
    $objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');
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
    // print_r($data);exit;
    $objWriter->save('php://output');
    exit;
  }

  /**
   * 客源统计
   *
   */
  private function _customer_count()
  {
    $data_view = array();
    $data_view['user_menu'] = $this->user_menu;
    $broker_info = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);
    $pg = $post_param['page'];

    $where = $broker_info['company_id'] > 0 ? 'company_id = ' . $broker_info['company_id'] : 'company_id = 0';

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    $role_id = $broker_info['role_id'];
    $this->load->model('permission_company_group_model');
    $role_data = $this->permission_company_group_model->get_one_by(array('id' => $role_id));
    if (is_full_array($role_data)) {
      $system_group_id = intval($role_data['system_group_id']);
    }

    //店长以下的经纪人 获取部门
    $role_level = intval($broker_info['role_level']);
    //武汉站站门店下来逻辑独立，新角色能看到所有门店
    if ('37' == $broker_info['city_id'] && 12 == $role_level) {
      $is_other_agency = false;
    } else {
      $is_other_agency = true;
    }
    if (is_int($role_level) && $role_level > 7 && $is_other_agency) {//店长以下
      $data_view['agency_info1'] = array('agency_name' => $broker_info['agency_name'], 'broker_name' => $broker_info['truename']);
    } elseif (is_int($role_level) && ($role_level == 6 || $role_level == 7)) {//店长
      $data_view['agency_info2'] = array('agency_name' => $broker_info['agency_name'], 'agency_id' => $broker_info['agency_id']);
    } else {//店长以上
      //根据数据范围，获得门店数据
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_customer_count');
      $all_access_agency_ids = '';
      if (is_full_array($access_agency_ids_data)) {
        foreach ($access_agency_ids_data as $k => $v) {
          $all_access_agency_ids .= $v['sub_agency_id'] . ',';
        }
        $all_access_agency_ids .= $this->user_arr['agency_id'];
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      } else {
        $all_access_agency_ids = $this->user_arr['agency_id'];
      }
      $data_view['agency_info'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
    }

    //config
    if (!isset($post_param['config'])) {
      $post_param['config'] = 1;
    }

    //门店
    if (!isset($post_param['agency_id'])) {
      $post_param['agency_id'] = $broker_info['agency_id'];
    }
    if ($post_param['agency_id']) {
      $where .= ' and agency_id = ' . $post_param['agency_id'];
      $broker = $this->api_broker_model->get_brokers_agency_id($post_param['agency_id']);
      $data_view['broker'] = $broker;
    }
    //经纪人
    if (!isset($post_param['broker_id'])) {
      $post_param['broker_id'] = $broker_info['broker_id'];
    }
    if ($post_param['broker_id']) {
      $where .= ' and broker_id = ' . $post_param['broker_id'];
    }
    //开始时间
    if ($post_param['start_date_begin']) {
      $where .= ' and creattime >= ' . strtotime($post_param['start_date_begin'] . ' 00:00:00');
    }
    //截止时间
    if ($post_param['start_date_end']) {
      $where .= ' and creattime <= ' . strtotime($post_param['start_date_end'] . ' 23:59:59');
    }

    if (!isset($post_param['type'])) {
      $post_param['type'] = 0;
    }

    $data_view['post_param'] = $post_param;

    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);

    //图表数据
    $yAxis = array();
    switch ($post_param['type']) {
      case 0:
        $this->load->model('buy_customer_model');
        $this->load->model('rent_customer_model');
        $config = $this->buy_customer_model->get_base_conf();

        $query_rent = $this->buy_customer_model->query('SELECT COUNT(DISTINCT broker_id) as sum_num FROM (SELECT DISTINCT broker_id FROM buy_customer where ' . $where . ' UNION ALL SELECT DISTINCT broker_id FROM rent_customer where ' . $where . ') AS new_table');
        $result_array = $query_rent->result();
        $this->_total_count = $result_array[0]->sum_num;
        $broker_customer = array();
        $query = $this->buy_customer_model->query('SELECT broker_id,count(broker_id) as sum_num FROM (SELECT broker_id FROM buy_customer where ' . $where . ' UNION ALL SELECT broker_id FROM rent_customer where ' . $where . ') AS new_table group by broker_id order by sum_num desc limit ' . $this->_offset . ',' . $this->_limit);
        $broker_array = $query->result();
        if (is_full_array($broker_array)) {
          foreach ($broker_array as $key => $value) {
            $broker_customer[$key]['broker_id'] = $value->broker_id;
          }
        }
        switch ($post_param['config']) {
          case 1://客源状态：1有效2预定3成交4无效5注销
            $data_view['xAxis'] = $config['status'];
            if (is_array($data_view['xAxis']) && !empty($data_view['xAxis']))
              foreach ($data_view['xAxis'] as $key => $value) {
                $status_num_buy = $this->buy_customer_model->get_count_by_cond($where . ' and status = ' . $key);
                $status_num_rent = $this->rent_customer_model->get_count_by_cond($where . ' and status = ' . $key);
                $yAxis[] = $status_num_buy + $status_num_rent;
                //获取分页列表的值
                if (is_full_array($broker_customer)) {
                  foreach ($broker_customer as $k => $v) {
                    $status_num_buy_broker = $this->buy_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                    $status_num_rent_broker = $this->rent_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                    $broker_customer[$k]['status_' . $key] = $status_num_buy_broker + $status_num_rent_broker;
                    $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                    $broker_customer[$k]['truename'] = $brokers['truename'];
                  }
                }
              }
            break;
          case 2://客源来源：1店面2老客户3广告4社区推广5网络6其他
            $data_view['xAxis'] = $config['infofrom'];
            if (is_array($data_view['xAxis']) && !empty($data_view['xAxis']))
              foreach ($data_view['xAxis'] as $key => $value) {
                $infofrom_num_buy = $this->buy_customer_model->get_count_by_cond($where . ' and infofrom = ' . $key);
                $infofrom_num_rent = $this->rent_customer_model->get_count_by_cond($where . ' and infofrom = ' . $key);
                $yAxis[] = $infofrom_num_buy + $infofrom_num_rent;
                //获取分页列表的值
                if (is_full_array($broker_customer)) {
                  foreach ($broker_customer as $k => $v) {
                    $infofrom_num_buy_broker = $this->buy_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                    $infofrom_num_rent_broker = $this->rent_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                    $broker_customer[$k]['infofrom_' . $key] = $infofrom_num_buy_broker + $infofrom_num_rent_broker;
                    $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                    $broker_customer[$k]['truename'] = $brokers['truename'];
                  }
                }
              }
            break;
        }
        break;
      case 1://求购
        $this->load->model('buy_customer_model');
        //获取求购信息基本配置资料
        $config = $this->buy_customer_model->get_base_conf();

        $query = $this->buy_customer_model->query('SELECT COUNT(DISTINCT broker_id) as num FROM (`buy_customer`) WHERE ' . $where);
        $result_array = $query->result();
        $this->_total_count = $result_array[0]->num;

        //分组获取分页列表信息
        $this->buy_customer_model->set_search_fields(array('broker_id', 'count(id) as sum_num'));
        $broker_customer = $this->buy_customer_model->get_list_by_cond($where . ' group by broker_id', $this->_offset, $this->_limit, 'sum_num');
        switch ($post_param['config']) {
          case 1://客源状态：1有效2预定3成交4无效5注销
            $data_view['xAxis'] = $config['status'];
            if (is_array($data_view['xAxis']) && !empty($data_view['xAxis']))
              foreach ($data_view['xAxis'] as $key => $value) {
                //获取图标纵坐标的值
                $yAxis[] = $this->buy_customer_model->get_count_by_cond($where . ' and status = ' . $key);
                //获取分页列表的值
                if (is_full_array($broker_customer)) {
                  foreach ($broker_customer as $k => $v) {
                    $status_num = $this->buy_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                    $broker_customer[$k]['status_' . $key] = $status_num;
                    $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                    $broker_customer[$k]['truename'] = $brokers['truename'];
                  }
                }
              }
            break;
          case 2://客源来源：1店面2老客户3广告4社区推广5网络6其他
            $data_view['xAxis'] = $config['infofrom'];
            if (is_array($data_view['xAxis']) && !empty($data_view['xAxis']))
              foreach ($data_view['xAxis'] as $key => $value) {
                $yAxis[] = $this->buy_customer_model->get_count_by_cond($where . ' and infofrom = ' . $key);
                //获取分页列表的值
                if (is_full_array($broker_customer)) {
                  foreach ($broker_customer as $k => $v) {
                    $infofrom_num = $this->buy_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                    $broker_customer[$k]['infofrom_' . $key] = $infofrom_num;
                    $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                    $broker_customer[$k]['truename'] = $brokers['truename'];
                  }
                }
              }
            break;
        }
        break;
      case 2://求租
        $this->load->model('rent_customer_model');
        //获取求租信息基本配置资料
        $config = $this->rent_customer_model->get_base_conf();

        $query = $this->rent_customer_model->query('SELECT COUNT(DISTINCT broker_id) as num FROM (`rent_house`) WHERE ' . $where);
        $result_array = $query->result();
        $this->_total_count = $result_array[0]->num;
        //分组获取分页列表信息
        $this->rent_customer_model->set_search_fields(array('broker_id', 'count(id) as sum_num'));
        $broker_customer = $this->rent_customer_model->get_list_by_cond($where . ' group by broker_id', $this->_offset, $this->_limit, 'sum_num');
        switch ($post_param['config']) {
          case 1://客源状态：1有效2预定3成交4无效5注销
            $data_view['xAxis'] = $config['status'];
            foreach ($data_view['xAxis'] as $key => $value) {
              $yAxis[] = $this->rent_customer_model->get_count_by_cond($where . ' and status = ' . $key);
              //获取分页列表的值
              if (is_full_array($broker_customer)) {
                foreach ($broker_customer as $k => $v) {
                  $status_num = $this->rent_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                  $broker_customer[$k]['status_' . $key] = $status_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_customer[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
          case 2://客源来源：1店面2老客户3广告4社区推广5网络6其他
            $data_view['xAxis'] = $config['infofrom'];
            foreach ($data_view['xAxis'] as $key => $value) {
              $yAxis[] = $this->rent_customer_model->get_count_by_cond($where . ' and infofrom = ' . $key);
              //获取分页列表的值
              if (is_full_array($broker_customer)) {
                foreach ($broker_customer as $k => $v) {
                  $infofrom_num = $this->rent_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                  $broker_customer[$k]['infofrom_' . $key] = $infofrom_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_customer[$k]['truename'] = $brokers['truename'];
                }
              }
            }
            break;
        }
        break;
    }
    $data_view['yAxis'] = $yAxis;
    $data_view['broker_customer'] = $broker_customer;

    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data_view['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data_view['page_title'] = '房源统计';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/date_analysis.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/highcharts.js');
    //底部JS
    $data_view['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js');

    $this->view('count_info/customer_count', $data_view);
  }

  /**
   * 客源统计-导出到excel
   */
  public function customer_export($type, $config, $agency_id, $broker_id)
  {
    $broker_info = $this->user_arr;
    $where = $broker_info['company_id'] > 0 ? 'company_id = ' . $broker_info['company_id'] : 'company_id = 0';

    //门店
    if ($agency_id) {
      $where .= ' and agency_id = ' . $agency_id;
    }
    //经纪人
    if ($broker_id) {
      $where .= ' and broker_id = ' . $broker_id;
    }

    switch ($type) {
      case 0:
        $this->load->model('buy_customer_model');
        $this->load->model('rent_customer_model');
        $config_customer = $this->buy_customer_model->get_base_conf();

        $broker_customer = array();
        $query = $this->buy_customer_model->query('SELECT broker_id,count(broker_id) as sum_num FROM (SELECT broker_id FROM buy_customer where ' . $where . ' UNION ALL SELECT broker_id FROM rent_customer where ' . $where . ') AS new_table group by broker_id order by sum_num desc limit ' . $this->_offset . ',' . $this->_limit);
        $broker_array = $query->result();
        if (is_full_array($broker_array)) {
          foreach ($broker_array as $key => $value) {
            $broker_customer[$key]['broker_id'] = $value->broker_id;
          }
        }
        switch ($config) {
          case 1://客源状态：1有效2预定3成交4无效5注销
            $xAxis = $config_customer['status'];
            foreach ($xAxis as $key => $value) {
              $status_num_buy = $this->buy_customer_model->get_count_by_cond($where . ' and status = ' . $key);
              $status_num_rent = $this->rent_customer_model->get_count_by_cond($where . ' and status = ' . $key);
              $yAxis[] = $status_num_buy + $status_num_rent;
              //获取分页列表的值
              if (is_full_array($broker_customer)) {
                foreach ($broker_customer as $k => $v) {
                  $status_num_buy_broker = $this->buy_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                  $status_num_rent_broker = $this->rent_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                  $broker_customer[$k]['status_' . $key] = $status_num_buy_broker + $status_num_rent_broker;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_customer[$k]['truename'] = $brokers['truename'];
                  $broker_customer[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
          case 2://客源来源：1店面2老客户3广告4社区推广5网络6其他
            $xAxis = $config_customer['infofrom'];
            foreach ($xAxis as $key => $value) {
              $infofrom_num_buy = $this->buy_customer_model->get_count_by_cond($where . ' and infofrom = ' . $key);
              $infofrom_num_rent = $this->rent_customer_model->get_count_by_cond($where . ' and infofrom = ' . $key);
              $yAxis[] = $infofrom_num_buy + $infofrom_num_rent;
              //获取分页列表的值
              if (is_full_array($broker_customer)) {
                foreach ($broker_customer as $k => $v) {
                  $infofrom_num_buy_broker = $this->buy_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                  $infofrom_num_rent_broker = $this->rent_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                  $broker_customer[$k]['infofrom_' . $key] = $infofrom_num_buy_broker + $infofrom_num_rent_broker;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_customer[$k]['truename'] = $brokers['truename'];
                  $broker_customer[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
        }
        break;
      case 1://出租
        $this->load->model('buy_customer_model');
        $customer_config = $this->buy_customer_model->get_base_conf();
        //分组获取分页列表信息
        $this->buy_customer_model->set_search_fields(array('broker_id', 'count(id) as sum_num'));
        $broker_customer = $this->buy_customer_model->get_list_by_cond($where . ' group by broker_id', -1, '', 'sum_num');
        switch ($config) {
          case 1://客源状态：1有效2预定3成交4无效5注销
            $xAxis = $customer_config['status'];
            foreach ($xAxis as $key => $value) {
              if (is_full_array($broker_customer)) {
                foreach ($broker_customer as $k => $v) {
                  $status_num = $this->buy_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                  $broker_customer[$k]['status_' . $key] = $status_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_customer[$k]['truename'] = $brokers['truename'];
                  $broker_customer[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
          case 2://客源来源：1店面2老客户3广告4社区推广5网络6其他
            $xAxis = $customer_config['infofrom'];
            foreach ($xAxis as $key => $value) {
              if (is_full_array($broker_customer)) {
                foreach ($broker_customer as $k => $v) {
                  $infofrom_num = $this->buy_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                  $broker_customer[$k]['infofrom_' . $key] = $infofrom_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_customer[$k]['truename'] = $brokers['truename'];
                  $broker_customer[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
        }
        break;
      case 2://出租
        $this->load->model('rent_customer_model');
        $customer_config = $this->rent_customer_model->get_base_conf();
        //分组获取分页列表信息
        $this->rent_customer_model->set_search_fields(array('broker_id', 'count(id) as sum_num'));
        $broker_customer = $this->rent_customer_model->get_list_by_cond($where . ' group by broker_id', $this->_offset, $this->_limit, 'sum_num');
        switch ($config) {
          case 1://房源状态：1有效2预定3成交4无效5注销
            $xAxis = $customer_config['status'];
            foreach ($xAxis as $key => $value) {
              if (is_full_array($broker_customer)) {
                foreach ($broker_customer as $k => $v) {
                  $status_num = $this->rent_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and status = ' . $key);
                  $broker_customer[$k]['status_' . $key] = $status_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_customer[$k]['truename'] = $brokers['truename'];
                  $broker_customer[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
          case 2://房源来源：1店面2老客户3广告4社区推广5网络6其他
            $xAxis = $customer_config['infofrom'];
            foreach ($xAxis as $key => $value) {
              if (is_full_array($broker_customer)) {
                foreach ($broker_customer as $k => $v) {
                  $infofrom_num = $this->rent_customer_model->get_count_by_cond($where . ' and broker_id = ' . $v['broker_id'] . ' and infofrom = ' . $key);
                  $broker_customer[$k]['infofrom_' . $key] = $infofrom_num;
                  $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
                  $broker_customer[$k]['truename'] = $brokers['truename'];
                  $broker_customer[$k]['agency_name'] = $brokers['agency_name'];
                }
              }
            }
            break;
        }
        break;
    }

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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '门店名');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '用户名');
    if (is_full_array($xAxis)) {
      foreach ($xAxis as $key => $value) {
        $x_key = $this->key_value($key - 1, 1);
        $objPHPExcel->getActiveSheet()->setCellValue($x_key, $value);
      }
    }

    if (is_full_array($broker_customer)) {
      foreach ($broker_customer as $key => $value) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($key + 2), $value['agency_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . ($key + 2), $value['truename']);
        if ($xAxis) {
          foreach ($xAxis as $k => $v) {
            $x_value = $this->key_value($k - 1, $key + 2);
            switch ($config) {
              case 1:
                $y_value = $value['status_' . $k];
                break;
              case 2:
                $y_value = $value['infofrom_' . $k];
                break;
            }
            $objPHPExcel->getActiveSheet()->setCellValue($x_value, $y_value);
          }

        }
      }
    }
    $fileName = time() . "_excel.xls";
    $objPHPExcel->getActiveSheet()->setTitle('客源统计');
    $objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');
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
    // print_r($data);exit;
    $objWriter->save('php://output');
    exit;
  }


  /**
   * 业绩排行
   *
   */
  public function performance_count($count_type = 0)
  {
    if ($count_type == 1) {
      $this->_contract_count();//合同统计
    } elseif ($count_type == 2) {
      $this->_contract_divide_count();//分成统计
    } else {
      $perfortype = $this->input->post('perfortype');
      if ($perfortype) {
        $this->_agency_count();//门店业绩
      } else {
        $this->_broker_count();//员工业绩
      }
    }
  }

  private function _broker_count()
  {
    $data_view = array();
    $data_view['user_menu'] = $this->user_menu;
    $broker_info = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);
    //$pg = $post_param['page'];

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    //店长以下的经纪人 获取部门
    $role_level = intval($broker_info['role_level']);
    //武汉站站门店下来逻辑独立，新角色能看到所有门店
    if ('37' == $broker_info['city_id'] && 12 == $role_level) {
      $is_other_agency = false;
    } else {
      $is_other_agency = true;
    }
    if (is_int($role_level) && $role_level > 7 && $is_other_agency) {//店长以下
      $data_view['agency_info1'] = array('agency_name' => $broker_info['agency_name'], 'broker_name' => $broker_info['truename']);
    } elseif (is_int($role_level) && ($role_level == 6 || $role_level == 7)) {//店长
      $data_view['agency_info2'] = array('agency_name' => $broker_info['agency_name'], 'agency_id' => $broker_info['agency_id']);
    } else {//店长以上
      //根据数据范围，获得门店数据
      $company_info = $this->agency_model->get_by_id($this->user_arr['company_id']);
      //门店信息
      $all_access_agency_ids = '';
      $agency_info_array = $this->agency_model->get_children_by_company_id($company_info['id']);
      //print_r($agency_info_array);
      if (is_full_array($agency_info_array)) {
        foreach ($agency_info_array as $k => $v) {
          $all_access_agency_ids .= $v['id'] . ',';
        }
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      }
      $data_view['agency_info'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
    }

    //门店
    if (!isset($post_param['agency_id'])) {
      $post_param['agency_id'] = $broker_info['agency_id'];
    }

    if (!isset($post_param['type'])) {
      $post_param['type'] = 0;
    }

    //查询员工的条件
    $time = time();
    $cond_where = " where expiretime >= {$time} and status = 1 ";
    $cond_where .= " and agency_id = " . $post_param['agency_id'];
    //符合条件的总行数
    $this->_total_count = $this->check_work_model->count_by($cond_where);
    //排序
    $cond_where .= " order by id DESC ";

    //获取员工列表内容
    $broker_all_info = $this->check_work_model->get_all_by($cond_where, 0, 0);
    //$page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    //$this->_init_pagination($page,$this->_limit);
    //获取员工业绩数据
    if ($post_param['start_date']) {
      $start_date = strtotime($post_param['start_date']);
    } else {
      $year = date("Y");
      $month = date("m");
      $allday = date("t");
      $start_date = strtotime($year . '-' . $month . '-01');
      $post_param['start_date'] = $year . '-' . $month . '-01';
    }

    if ($post_param['end_date']) {
      $end_date = strtotime($post_param['end_date'] . ' 23:59:59');
    } else {
      $end_date = time();
    }

    $data_view['post_param'] = $post_param;

    $where = "contract_divide.entry_time >= " . $start_date . " and contract_divide.entry_time <=" . $end_date;
    //处理经纪人业绩数据累加
    foreach ($broker_all_info as $key => $vo) {
      $broker_info_one = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
      $agency_info = $this->agency_model->get_by_id($broker_info_one['agency_id']);
      $broker_all_info[$key]['agency_name'] = $agency_info['agency_name'] ? $agency_info['agency_name'] : $agency_info['name'];
      $broker_all_info[$key]['broker_name'] = $broker_info_one['truename'];
      //提成
      $cond_divide_sell = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and type = 1 ';
      $cond_divide_rent = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and type = 2 ';
      $cond_divide_total = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'];
      $divide_price_sell = $this->contract_divide_model->get_total_count($cond_divide_sell);
      $divide_price_rent = $this->contract_divide_model->get_total_count($cond_divide_rent);
      $divide_price_total = $this->contract_divide_model->get_total_count($cond_divide_total);
      $broker_all_info[$key]['divide_price_sell'] = sprintf('%.2f', $divide_price_sell['price_total']);
      $broker_all_info[$key]['divide_price_rent'] = sprintf('%.2f', $divide_price_rent['price_total']);
      $broker_all_info[$key]['divide_price_total'] = sprintf('%.2f', $divide_price_total['price_total']);
      //业绩即佣金总额
      /****
       * $cond_perfor_sell = $where." and contract_divide.achieve_broker_id_b = ".$vo['broker_id'].' and contract_divide.type = 1 ';
       * $cond_perfor_rent = $where." and contract_divide.achieve_broker_id_b = ".$vo['broker_id'].' and contract_divide.type = 2 ';
       * $cond_perfor_total = $where." and contract_divide.achieve_broker_id_b = ".$vo['broker_id'];
       * $perfor_sell = $this->contract_divide_model->get_all_by_count($cond_perfor_sell,0,0);
       * $perfor_rent = $this->contract_divide_model->get_all_by_count($cond_perfor_rent,0,0);
       * $perfor_total = $this->contract_divide_model->get_all_by_count($cond_perfor_total,0,0);
       * $broker_all_info[$key]['perfor_sell'] = 0;
       * $broker_all_info[$key]['perfor_rent'] = 0;
       * $broker_all_info[$key]['perfor_total'] = 0;
       * foreach($perfor_sell as $k=>$v){
       * $broker_all_info[$key]['perfor_sell'] += $v['commission_total'];
       * }
       * foreach($perfor_rent as $k=>$v){
       * $broker_all_info[$key]['perfor_rent'] += $v['commission_total'];
       * }
       * foreach($perfor_total as $k=>$v){
       * $broker_all_info[$key]['perfor_total'] += $v['commission_total'];
       * }
       * $broker_all_info[$key]['perfor_sell'] = sprintf('%.2f',$broker_all_info[$key]['perfor_sell']);
       * $broker_all_info[$key]['perfor_rent'] = sprintf('%.2f',$broker_all_info[$key]['perfor_rent']);
       * $broker_all_info[$key]['perfor_total'] = sprintf('%.2f',$broker_all_info[$key]['perfor_total']);
       ***/
      if ($post_param['type'] == 1) {
        $broker_all_info[$key]['divide_price_total'] = sprintf('%.2f', $broker_all_info[$key]['divide_price_sell']);
        //$broker_all_info[$key]['perfor_total'] = sprintf('%.2f',$broker_all_info[$key]['perfor_sell']);
      } elseif ($post_param['type'] == 2) {
        $broker_all_info[$key]['divide_price_total'] = sprintf('%.2f', $broker_all_info[$key]['divide_price_rent']);
        //$broker_all_info[$key]['perfor_total'] = sprintf('%.2f',$broker_all_info[$key]['perfor_rent']);
      }
    }
    //print_r($broker_all_info);exit;
    $broker_info_new = array();
    switch ($post_param['type']) {
      case 0:
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide_price_total');
        break;
      case 1://买卖出售
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide_price_sell');
        break;
      case 2://租赁出租
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide_price_rent');
        break;
    }
    //图表数据
    $yAxis = array();
    $xinhao = '******************';
    //$broker_info_two = array();
    $perfor_total = 0;
    $divide_price_total = 0;
    if (is_full_array($broker_info_new)) {
      foreach ($broker_info_new as $key => $vo) {
        $broker_info_new[$key]['rank'] = $key + 1;
        if ($key <= 9) {
          $xAxis[$key + 1] = $vo['broker_name'];
          switch ($post_param['type']) {
            case 0:
              $yAxis[] = $vo['divide_price_total'];
              break;
            case 1:
              $yAxis[] = $vo['divide_price_sell'];
              break;
            case 2:
              $yAxis[] = $vo['divide_price_rent'];
              break;
          }
        }
        //$perfor_total += $vo['perfor_total'];
        $divide_price_total += $vo['divide_price_total'];
        /*if($key>=$this->_offset && $key<($this->_offset + $this->_limit)){
                    $broker_info_two[$key] = $broker_info_new[$key];
                }*/
      }
    }
    //$perfor_total = sprintf('%.2f',$perfor_total);
    $divide_price_total = sprintf('%.2f', $divide_price_total);
    //print_r($broker_info_two);exit;
    $data_view['role_level'] = $role_level;
    if ($role_level > 7) {
      foreach ($broker_info_new as $key => $vo) {
        $broker_info_new[$key]['divide_price_sell'] = substr($xinhao, 0, strlen(floor($vo['divide_price_sell'])));
        $broker_info_new[$key]['divide_price_rent'] = substr($xinhao, 0, strlen(floor($vo['divide_price_rent'])));
        $broker_info_new[$key]['divide_price_total'] = substr($xinhao, 0, strlen(floor($vo['divide_price_total'])));
        /***
         * $broker_info_new[$key]['perfor_sell'] = substr($xinhao,0,strlen(floor($vo['perfor_sell'])));
         * $broker_info_new[$key]['perfor_rent'] = substr($xinhao,0,strlen(floor($vo['perfor_rent'])));
         * $broker_info_new[$key]['perfor_total'] = substr($xinhao,0,strlen(floor($vo['perfor_total'])));
         ***/
      }
      //$perfor_total = substr($xinhao,0,strlen(floor($perfor_total))?strlen(floor($perfor_total)):1);
      $divide_price_total = substr($xinhao, 0, strlen(floor($divide_price_total)) ? strlen(floor($divide_price_total)) : 1);
    }
    //print_r($yAxis);
    $data_view['xAxis'] = $xAxis;
    $data_view['yAxis'] = $yAxis;
    //$data_view['perfor_total'] = $perfor_total;
    $data_view['divide_price_total'] = $divide_price_total;
    $data_view['broker_info_new'] = $broker_info_new;

    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data_view['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data_view['page_title'] = '员工业绩排行榜';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/date_analysis.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/highcharts.js');
    //底部JS
    $data_view['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js');

    $this->view('count_info/_broker_count', $data_view);
  }

  private function _agency_count()
  {
    $data_view = array();
    $data_view['user_menu'] = $this->user_menu;
    $broker_info = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);
    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    //店长以下的经纪人 获取部门
    $role_level = intval($broker_info['role_level']);
    //根据数据范围，获得门店数据
    $company_info = $this->agency_model->get_by_id($this->user_arr['company_id']);
    //门店信息
    $all_access_agency_ids = '';
    $agency_info_array = $this->agency_model->get_children_by_company_id($company_info['id']);
    //print_r($agency_info_array);
    if (is_full_array($agency_info_array)) {
      foreach ($agency_info_array as $k => $v) {
        $all_access_agency_ids .= $v['id'] . ',';
      }
      $all_access_agency_ids = trim($all_access_agency_ids, ',');
    }
    $agency_info = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);

    if (!isset($post_param['type'])) {
      $post_param['type'] = 0;
    }

    $data_view['post_param'] = $post_param;

    //获取员工业绩数据
    if ($post_param['start_date']) {
      $start_date = strtotime($post_param['start_date']);
    } else {
      $year = date("Y");
      $month = date("m");
      $allday = date("t");
      $start_date = strtotime($year . "-" . $month . "-01");
    }
    if ($post_param['end_date']) {
      $end_date = strtotime($post_param['end_date']);
    } else {
      $end_date = time();
    }
    $where = "contract_divide.entry_time >= " . $start_date . " and contract_divide.entry_time <=" . $end_date;
    //处理经纪人业绩数据累加
    foreach ($agency_info as $key => $vo) {
      //提成
      $cond_divide_sell = $where . ' and achieve_agency_id_b = ' . $vo['agency_id'] . ' and type = 1 ';
      $cond_divide_rent = $where . ' and achieve_agency_id_b = ' . $vo['agency_id'] . ' and type = 2 ';
      $cond_divide_total = $where . ' and achieve_agency_id_b = ' . $vo['agency_id'];
      $divide_price_sell = $this->contract_divide_model->get_total_count($cond_divide_sell);
      $divide_price_rent = $this->contract_divide_model->get_total_count($cond_divide_rent);
      $divide_price_total = $this->contract_divide_model->get_total_count($cond_divide_total);
      $agency_info[$key]['divide_price_sell'] = sprintf('%.2f', $divide_price_sell['price_total']);
      $agency_info[$key]['divide_price_rent'] = sprintf('%.2f', $divide_price_rent['price_total']);
      $agency_info[$key]['divide_price_total'] = sprintf('%.2f', $divide_price_total['price_total']);
      //业绩即佣金总额
      /***
       * $cond_perfor_sell = $where." and contract_divide.achieve_agency_id_b = ".$vo['agency_id'].' and contract_divide.type = 1 ';
       * $cond_perfor_rent = $where." and contract_divide.achieve_agency_id_b = ".$vo['agency_id'].' and contract_divide.type = 2 ';
       * $cond_perfor_total = $where." and contract_divide.achieve_agency_id_b = ".$vo['agency_id'];
       * $perfor_sell = $this->contract_divide_model->get_all_by_count($cond_perfor_sell,0,0);
       * $perfor_rent = $this->contract_divide_model->get_all_by_count($cond_perfor_rent,0,0);
       * $perfor_total = $this->contract_divide_model->get_all_by_count($cond_perfor_total,0,0);
       * $agency_info[$key]['perfor_sell'] = 0;
       * $agency_info[$key]['perfor_rent'] = 0;
       * $agency_info[$key]['perfor_total'] = 0;
       * foreach($perfor_sell as $k=>$v){
       * $agency_info[$key]['perfor_sell'] += $v['commission_total'];
       * }
       * foreach($perfor_rent as $k=>$v){
       * $agency_info[$key]['perfor_rent'] += $v['commission_total'];
       * }
       * foreach($perfor_total as $k=>$v){
       * $agency_info[$key]['perfor_total'] += $v['commission_total'];
       * }
       * $agency_info[$key]['perfor_sell'] = sprintf('%.2f',$agency_info[$key]['perfor_sell']);
       * $agency_info[$key]['perfor_rent'] = sprintf('%.2f',$agency_info[$key]['perfor_rent']);
       * $agency_info[$key]['perfor_total'] = sprintf('%.2f',$agency_info[$key]['perfor_total']);
       ***/
      if ($post_param['type'] == 1) {
        $agency_info[$key]['divide_price_total'] = sprintf('%.2f', $agency_info[$key]['divide_price_sell']);
        //$agency_info[$key]['perfor_total'] = sprintf('%.2f',$agency_info[$key]['perfor_sell']);
      } elseif ($post_param['type'] == 2) {
        $agency_info[$key]['divide_price_total'] = sprintf('%.2f', $agency_info[$key]['divide_price_rent']);
        //$agency_info[$key]['perfor_total'] = sprintf('%.2f',$agency_info[$key]['perfor_rent']);
      }
    }
    //print_r($agency_info);exit;
    $agency_info_new = array();
    switch ($post_param['type']) {
      case 0:
        $agency_info_new = $this->multi_array_sort($agency_info, 'divide_price_total');
        break;
      case 1://买卖出售
        $agency_info_new = $this->multi_array_sort($agency_info, 'divide_price_sell');
        break;
      case 2://租赁出租
        $agency_info_new = $this->multi_array_sort($agency_info, 'divide_price_rent');
        break;
    }
    //图表数据
    $yAxis = array();
    //$perfor_total = 0;
    $divide_price_total = 0;
    $xinhao = '********************';
    foreach ($agency_info_new as $key => $vo) {
      $agency_info_new[$key]['rank'] = $key + 1;
      if ($key <= 9) {
        $xAxis[$key + 1] = $vo['agency_name'];
        switch ($post_param['type']) {
          case 0:
            $yAxis[] = $vo['divide_price_total'];
            break;
          case 1:
            $yAxis[] = $vo['divide_price_sell'];
            break;
          case 2:
            $yAxis[] = $vo['divide_price_rent'];
            break;
        }
      }
      //$perfor_total += $vo['perfor_total'];
      $divide_price_total += $vo['divide_price_total'];
    }
    $perfor_total = sprintf('%.2f', $perfor_total);
    $divide_price_total = sprintf('%.2f', $divide_price_total);
    $data_view['role_level'] = $role_level;
    $data_view['agency_name'] = $broker_info['agency_name'];
    if ($role_level > 7) {
      foreach ($agency_info_new as $key => $vo) {
        $agency_info_new[$key]['divide_price_sell'] = substr($xinhao, 0, strlen(floor($vo['divide_price_sell'])));
        $agency_info_new[$key]['divide_price_rent'] = substr($xinhao, 0, strlen(floor($vo['divide_price_rent'])));
        $agency_info_new[$key]['divide_price_total'] = substr($xinhao, 0, strlen(floor($vo['divide_price_total'])));
        /***
         * $agency_info_new[$key]['perfor_sell'] = substr($xinhao,0,strlen(floor($vo['perfor_sell'])));
         * $agency_info_new[$key]['perfor_rent'] = substr($xinhao,0,strlen(floor($vo['perfor_rent'])));
         * $agency_info_new[$key]['perfor_total'] = substr($xinhao,0,strlen(floor($vo['perfor_total'])));
         ***/
      }
      //$perfor_total = substr($xinhao,0,strlen(floor($perfor_total))?strlen(floor($perfor_total)):1);
      $divide_price_total = substr($xinhao, 0, strlen(floor($divide_price_total)) ? strlen(floor($divide_price_total)) : 1);
    } elseif ($role_level == 6 || $role_level == 7) {
      foreach ($agency_info_new as $key => $vo) {
        if ($vo['agency_id'] != $broker_info['agency_id']) {
          $agency_info_new[$key]['divide_price_sell'] = substr($xinhao, 0, strlen(floor($vo['divide_price_sell'])));
          $agency_info_new[$key]['divide_price_rent'] = substr($xinhao, 0, strlen(floor($vo['divide_price_rent'])));
          $agency_info_new[$key]['divide_price_total'] = substr($xinhao, 0, strlen(floor($vo['divide_price_total'])));
          /***
           * $agency_info_new[$key]['perfor_sell'] = substr($xinhao,0,strlen(floor($vo['perfor_sell'])));
           * $agency_info_new[$key]['perfor_rent'] = substr($xinhao,0,strlen(floor($vo['perfor_rent'])));
           * $agency_info_new[$key]['perfor_total'] = substr($xinhao,0,strlen(floor($vo['perfor_total'])));
           ***/
        }
      }
      //$perfor_total = substr($xinhao,0,strlen(floor($perfor_total))?strlen(floor($perfor_total)):1);
      $divide_price_total = substr($xinhao, 0, strlen(floor($divide_price_total)) ? strlen(floor($divide_price_total)) : 1);
    }
    //print_r($broker_info_two);exit;
    $data_view['xAxis'] = $xAxis;
    $data_view['yAxis'] = $yAxis;
    //$data_view['perfor_total'] = $perfor_total;
    $data_view['divide_price_total'] = $divide_price_total;
    $data_view['agency_info_new'] = $agency_info_new;

    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data_view['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data_view['page_title'] = '门店业绩排行榜';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/date_analysis.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/highcharts.js');
    //底部JS
    $data_view['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js');

    $this->view('count_info/_agency_count', $data_view);
  }

  private function _contract_count()
  {
    $data_view = array();
    $data_view['user_menu'] = $this->user_menu;
    $broker_info = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);
    $pg = $post_param['page'];

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    //店长以下的经纪人 获取部门
    $role_level = intval($broker_info['role_level']);
    //武汉站站门店下来逻辑独立，新角色能看到所有门店
    if ('37' == $broker_info['city_id'] && 12 == $role_level) {
      $is_other_agency = false;
    } else {
      $is_other_agency = true;
    }
    if (is_int($role_level) && $role_level > 7 && $is_other_agency) {//店长以下
      $data_view['agency_info1'] = array('agency_name' => $broker_info['agency_name'], 'broker_name' => $broker_info['truename']);
    } elseif (is_int($role_level) && ($role_level == 6 || $role_level == 7)) {//店长
      $data_view['agency_info2'] = array('agency_name' => $broker_info['agency_name'], 'agency_id' => $broker_info['agency_id']);
    } else {//店长以上
      //根据数据范围，获得门店数据
      $company_info = $this->agency_model->get_by_id($this->user_arr['company_id']);
      //门店信息
      $all_access_agency_ids = '';
      $agency_info_array = $this->agency_model->get_children_by_company_id($company_info['id']);
      //print_r($agency_info_array);
      if (is_full_array($agency_info_array)) {
        foreach ($agency_info_array as $k => $v) {
          $all_access_agency_ids .= $v['id'] . ',';
        }
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      }
      $data_view['agency_info'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
    }

    //开始时间
    if (!isset($post_param['start_date'])) {
      $post_param['start_date'] = date('Y-m-d', strtotime("-6 day"));
    }
    //截止时间
    if (!isset($post_param['end_date'])) {
      $post_param['end_date'] = date('Y-m-d');
    }


    if (!isset($post_param['type'])) {
      $post_param['type'] = 0;
    }
    //查询员工的条件
    $time = time();
    $cond_where = " where expiretime >= {$time} and status = 1 and company_id = " . $broker_info['company_id'];
    //门店
    if ($post_param['agency_id'] == '') {
      $post_param['agency_id'] = $broker_info['agency_id'];
      $cond_where .= " and agency_id = " . $post_param['agency_id'];
      $broker = $this->api_broker_model->get_brokers_agency_id($post_param['agency_id']);
      $data_view['broker'] = $broker;
    } elseif ($post_param['agency_id']) {
      $cond_where .= " and agency_id = " . $post_param['agency_id'];
      $broker = $this->api_broker_model->get_brokers_agency_id($post_param['agency_id']);
      $data_view['broker'] = $broker;
    }

    //经纪人
    if ($post_param['broker_id'] == '') {
      $post_param['broker_id'] = $broker_info['broker_id'];
      $cond_where .= " and broker_id = " . $post_param['broker_id'];
    } elseif ($post_param['broker_id']) {
      $cond_where .= " and broker_id = " . $post_param['broker_id'];
    }
    //符合条件的总行数
    $this->_total_count = $this->check_work_model->count_by($cond_where);
    //排序
    $cond_where .= " order by id DESC ";
    //获取员工列表内容
    $broker_all_info = $this->check_work_model->get_all_by($cond_where, 0, 0);
    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page, $this->_limit);
    //获取员工业绩数据
    $start_date = strtotime($post_param['start_date'] . " 00:00:00 ");
    $end_date = strtotime($post_param['end_date'] . " 23:59:59 ");

    $where = "check_time >= " . $start_date . " and check_time <=" . $end_date;
    //处理经纪人业绩数据累加
    foreach ($broker_all_info as $key => $vo) {
      $broker_info_one = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
      $agency_info = $this->agency_model->get_by_id($broker_info_one['agency_id']);
      $broker_all_info[$key]['agency_name'] = $agency_info['agency_name'] ? $agency_info['agency_name'] : $agency_info['name'];
      $broker_all_info[$key]['broker_name'] = $broker_info_one['truename'];
      //合同出售
      $cond_contract_sell = $where . ' and broker_id_a = ' . $vo['broker_id'] . ' and is_check = 2 and type = 1';
      //合同出租
      $cond_contract_rent = $where . ' and broker_id_a = ' . $vo['broker_id'] . ' and is_check = 2 and type = 2';
      //托管合同
      $cond_collocation_contract = $where . ' and broker_id = ' . $vo['broker_id'] . ' and status = 2 ';
      //托管出租合同
      $cond_collocation_rent = $where . ' and broker_id = ' . $vo['broker_id'] . ' and status = 2 ';
      $contract_sell_total = $this->contract_model->count_by($cond_contract_sell);
      $contract_rent_total = $this->contract_model->count_by($cond_contract_rent);
      $collocation_contract_total = $this->collocation_contract_model->count_by($cond_collocation_contract);
      $collocation_rent_total = $this->collocation_contract_model->count_by_tab($cond_collocation_rent, $tab = '4');
      $broker_all_info[$key]['contract_sell_total'] = $contract_sell_total;
      $broker_all_info[$key]['contract_rent_total'] = $contract_rent_total;
      $broker_all_info[$key]['collocation_contract_total'] = $collocation_contract_total;
      $broker_all_info[$key]['collocation_rent_total'] = $collocation_rent_total;
      $broker_all_info[$key]['total_total'] = $contract_sell_total + $contract_rent_total + $collocation_contract_total + $collocation_rent_total;
    }
    //print_r($broker_all_info);exit;
    $broker_info_new = array();
    switch ($post_param['type']) {
      case 0://总计
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'total_total');
        break;
      case 1://合同出售
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'contract_sell_total');
        break;
      case 2://合同出租
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'contract_rent_total');
        break;
      case 3://托管合同
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'collocation_contract_total');
        break;
      case 4://托管出租合同
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'collocation_rent_total');
        break;
    }
    //图数据
    $yAxis = array();
    $broker_info_new_image = $this->multi_array_sort($broker_all_info, 'total_total');
    if (is_full_array($broker_info_new_image)) {
      foreach ($broker_info_new_image as $key => $vo) {
        if ($key <= 9) {
          $xAxis[$key + 1] = $vo['broker_name'];
          $yAxis[] = $vo['total_total'];
        }
      }
    }
    $data_view['xAxis'] = $xAxis;
    $data_view['yAxis'] = $yAxis;
    //表数据
    $broker_info_two = array();
    if (is_full_array($broker_info_new)) {
      foreach ($broker_info_new as $key => $vo) {
        $broker_info_new[$key]['rank'] = $key + 1;
        if ($key >= $this->_offset && $key < ($this->_offset + $this->_limit)) {
          $broker_info_two[$key] = $broker_info_new[$key];
        }
      }
    }
    $data_view['broker_info_new'] = $broker_info_two;

    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //查询数据返回
    $data_view['post_param'] = $post_param;
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data_view['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data_view['page_title'] = '合同统计';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/date_analysis.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/highcharts.js');
    //底部JS
    $data_view['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js');

    $this->view('count_info/_contract_count', $data_view);
  }

  private function _contract_divide_count()
  {
    $data_view = array();
    $data_view['user_menu'] = $this->user_menu;
    $broker_info = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);
    $pg = $post_param['page'];

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    //店长以下的经纪人 获取部门
    $role_level = intval($broker_info['role_level']);
    //武汉站站门店下来逻辑独立，新角色能看到所有门店
    if ('37' == $broker_info['city_id'] && 12 == $role_level) {
      $is_other_agency = false;
    } else {
      $is_other_agency = true;
    }
    if (is_int($role_level) && $role_level > 7 && $is_other_agency) {//店长以下
      $data_view['agency_info1'] = array('agency_name' => $broker_info['agency_name'], 'broker_name' => $broker_info['truename']);
    } elseif (is_int($role_level) && ($role_level == 6 || $role_level == 7)) {//店长
      $data_view['agency_info2'] = array('agency_name' => $broker_info['agency_name'], 'agency_id' => $broker_info['agency_id']);
    } else {//店长以上
      //根据数据范围，获得门店数据
      $company_info = $this->agency_model->get_by_id($this->user_arr['company_id']);
      //门店信息
      $all_access_agency_ids = '';
      $agency_info_array = $this->agency_model->get_children_by_company_id($company_info['id']);
      //print_r($agency_info_array);
      if (is_full_array($agency_info_array)) {
        foreach ($agency_info_array as $k => $v) {
          $all_access_agency_ids .= $v['id'] . ',';
        }
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      }
      $data_view['agency_info'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
    }

    //开始时间
    if (!isset($post_param['start_date'])) {
      $post_param['start_date'] = date('Y-m-d', strtotime("-6 day"));
    }
    //截止时间
    if (!isset($post_param['end_date'])) {
      $post_param['end_date'] = date('Y-m-d');
    }


    if (!isset($post_param['type'])) {
      $post_param['type'] = 0;
    }

    //查询员工的条件
    $time = time();
    $cond_where = " where expiretime >= {$time} and status = 1 and company_id = " . $broker_info['company_id'];
    //门店
    if ($post_param['agency_id'] == '') {
      $post_param['agency_id'] = $broker_info['agency_id'];
      $cond_where .= " and agency_id = " . $post_param['agency_id'];
      $broker = $this->api_broker_model->get_brokers_agency_id($post_param['agency_id']);
      $data_view['broker'] = $broker;
    } elseif ($post_param['agency_id']) {
      $cond_where .= " and agency_id = " . $post_param['agency_id'];
      $broker = $this->api_broker_model->get_brokers_agency_id($post_param['agency_id']);
      $data_view['broker'] = $broker;
    }
    //经纪人
    if ($post_param['broker_id'] == '') {
      $post_param['broker_id'] = $broker_info['broker_id'];
      $cond_where .= " and broker_id = " . $post_param['broker_id'];
    } elseif ($post_param['broker_id']) {
      $cond_where .= " and broker_id = " . $post_param['broker_id'];
    }
    //符合条件的总行数
    $this->_total_count = $this->check_work_model->count_by($cond_where);
    //排序
    $cond_where .= " order by id DESC ";

    //获取员工列表内容
    $broker_all_info = $this->check_work_model->get_all_by($cond_where, 0, 0);
    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page, $this->_limit);
    //获取员工业绩数据
    $start_date = strtotime($post_param['start_date'] . " 00:00:00 ");
    $end_date = strtotime($post_param['end_date'] . " 23:59:59 ");

    $where = "contract_divide.entry_time >= " . $start_date . " and contract_divide.entry_time <=" . $end_date;
    //处理经纪人业绩数据累加
    foreach ($broker_all_info as $key => $vo) {
      $broker_info_one = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
      $agency_info = $this->agency_model->get_by_id($broker_info_one['agency_id']);
      $broker_all_info[$key]['agency_name'] = $agency_info['agency_name'] ? $agency_info['agency_name'] : $agency_info['name'];
      $broker_all_info[$key]['broker_name'] = $broker_info_one['truename'];
      //提成
      $cond_divide1 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 1 ';
      $cond_divide2 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 2 ';
      $cond_divide3 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 3 ';
      $cond_divide4 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 4 ';
      $cond_divide5 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 5 ';
      $cond_divide6 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 6 ';
      $cond_divide7 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 7 ';
      $cond_divide8 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 8 ';
      $cond_divide9 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 9 ';
      $cond_divide10 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 10 ';
      $cond_divide_total = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'];
      $divide1 = $this->contract_divide_model->get_total_count($cond_divide1);
      $divide2 = $this->contract_divide_model->get_total_count($cond_divide2);
      $divide3 = $this->contract_divide_model->get_total_count($cond_divide3);
      $divide4 = $this->contract_divide_model->get_total_count($cond_divide4);
      $divide5 = $this->contract_divide_model->get_total_count($cond_divide5);
      $divide6 = $this->contract_divide_model->get_total_count($cond_divide6);
      $divide7 = $this->contract_divide_model->get_total_count($cond_divide7);
      $divide8 = $this->contract_divide_model->get_total_count($cond_divide8);
      $divide9 = $this->contract_divide_model->get_total_count($cond_divide9);
      $divide10 = $this->contract_divide_model->get_total_count($cond_divide10);
      $divide_total = $this->contract_divide_model->get_total_count($cond_divide_total);
      $broker_all_info[$key]['divide1'] = $divide1['total'];
      $broker_all_info[$key]['divide2'] = $divide2['total'];
      $broker_all_info[$key]['divide3'] = $divide3['total'];
      $broker_all_info[$key]['divide4'] = $divide4['total'];
      $broker_all_info[$key]['divide5'] = $divide5['total'];
      $broker_all_info[$key]['divide6'] = $divide6['total'];
      $broker_all_info[$key]['divide7'] = $divide7['total'];
      $broker_all_info[$key]['divide8'] = $divide8['total'];
      $broker_all_info[$key]['divide9'] = $divide9['total'];
      $broker_all_info[$key]['divide10'] = $divide10['total'];
      $broker_all_info[$key]['divide_total'] = $divide_total['total'];
    }
    //print_r($broker_all_info);exit;
    $broker_info_new = array();
    switch ($post_param['type']) {
      case 0:
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide_total');
        break;
      case 1://1房源
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide1');
        break;
      case 2://2客源
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide2');
        break;
      case 3://3钥匙
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide3');
        break;
      case 4://4独家
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide4');
        break;
      case 5://5签合同
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide5');
        break;
      case 6://6转介绍
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide6');
        break;
      case 7://7收房
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide7');
        break;
      case 8://8勘察
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide8');
        break;
      case 9://9代办贷款
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide9');
        break;
      case 10://10其他
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide10');
        break;
    }
    //图表数据
    $yAxis = array();
    $broker_info_two = array();
    $perfor_total = 0;
    $divide_price_total = 0;
    if (is_full_array($broker_info_new)) {
      foreach ($broker_info_new as $key => $vo) {
        $broker_info_new[$key]['rank'] = $key + 1;
        if ($key <= 9) {
          $xAxis[$key + 1] = $vo['broker_name'];
          switch ($post_param['type']) {
            case 0:
              $yAxis[] = $vo['divide_total'];
              break;
            case 1:
              $yAxis[] = $vo['divide1'];
              break;
            case 2:
              $yAxis[] = $vo['divide2'];
              break;
            case 3:
              $yAxis[] = $vo['divide3'];
              break;
            case 4:
              $yAxis[] = $vo['divide4'];
              break;
            case 5:
              $yAxis[] = $vo['divide5'];
              break;
            case 6:
              $yAxis[] = $vo['divide6'];
              break;
            case 7:
              $yAxis[] = $vo['divide7'];
              break;
            case 8:
              $yAxis[] = $vo['divide8'];
              break;
            case 9:
              $yAxis[] = $vo['divide9'];
              break;
            case 10:
              $yAxis[] = $vo['divide10'];
              break;
          }
        }
        if ($key >= $this->_offset && $key < ($this->_offset + $this->_limit)) {
          $broker_info_two[$key] = $broker_info_new[$key];
        }
      }
    }
    //print_r($broker_info_two);exit;
    //print_r($yAxis);
    $data_view['xAxis'] = $xAxis;
    $data_view['yAxis'] = $yAxis;
    $data_view['broker_info_new'] = $broker_info_two;

    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //查询数据返回
    $data_view['post_param'] = $post_param;
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data_view['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data_view['page_title'] = '分成统计';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/date_analysis.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/highcharts.js');
    //底部JS
    $data_view['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js');

    $this->view('count_info/_contract_divide_count', $data_view);
  }

  /**
   * 业绩排行-导出到excel
   */
  public function performance_count_export($count_type = 0, $agency_id, $type, $start_date = 0, $end_date = 0, $broker_id = 0)
  {
    if ($count_type == 1) {
      $this->_agency_count_export($type, $start_date, $end_date);//门店业绩
    } elseif ($count_type == 2) {
      $this->_contract_count_export($agency_id, $type, $start_date, $end_date, $broker_id);//合同统计
    } elseif ($count_type == 3) {
      $this->_contract_divide_count_export($agency_id, $type, $start_date, $end_date, $broker_id);//分成统计
    } else {
      $this->_broker_count_export($agency_id, $type, $start_date, $end_date);//员工业绩
    }
  }

  private function _broker_count_export($agency_id, $type, $start_date, $end_date)
  {
    $broker_info = $this->user_arr;
    //$post_param = $this->input->get( NULL , TRUE );

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    //店长以下的经纪人 获取部门
    $role_level = intval($broker_info['role_level']);

    //门店
    if (!isset($agency_id)) {
      $agency_id = $broker_info['agency_id'];
    }

    if (!isset($type)) {
      $type = 0;
    }


    //查询员工的条件
    $time = time();
    $cond_where = " where expiretime >= {$time} and status = 1 ";
    $cond_where .= " and agency_id = " . $agency_id;
    //符合条件的总行数
    $this->_total_count = $this->check_work_model->count_by($cond_where);
    //排序
    $cond_where .= " order by id DESC ";

    //获取员工列表内容
    $broker_all_info = $this->check_work_model->get_all_by($cond_where, 0, 0);
    //获取员工业绩数据
    if ($start_date) {
      $start_date = strtotime($start_date);
    } else {
      $year = date("Y");
      $month = date("m");
      $allday = date("t");
      $start_date = strtotime($year . "-" . $month . "-01");
    }
    if ($end_date) {
      $end_date = strtotime($end_date);
    } else {
      $end_date = time();
    }
    $where = "contract_divide.entry_time >= " . $start_date . " and contract_divide.entry_time <=" . $end_date;
    //处理经纪人业绩数据累加
    foreach ($broker_all_info as $key => $vo) {
      $broker_info_one = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
      $agency_info = $this->agency_model->get_by_id($broker_info_one['agency_id']);
      $broker_all_info[$key]['agency_name'] = $agency_info['agency_name'] ? $agency_info['agency_name'] : $agency_info['name'];
      $broker_all_info[$key]['broker_name'] = $broker_info_one['truename'];
      //提成
      $cond_divide_sell = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and type = 1 ';
      $cond_divide_rent = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and type = 2 ';
      $cond_divide_total = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'];
      $divide_price_sell = $this->contract_divide_model->get_total_count($cond_divide_sell);
      $divide_price_rent = $this->contract_divide_model->get_total_count($cond_divide_rent);
      $divide_price_total = $this->contract_divide_model->get_total_count($cond_divide_total);
      $broker_all_info[$key]['divide_price_sell'] = sprintf('%.2f', $divide_price_sell['price_total']);
      $broker_all_info[$key]['divide_price_rent'] = sprintf('%.2f', $divide_price_rent['price_total']);
      $broker_all_info[$key]['divide_price_total'] = sprintf('%.2f', $divide_price_total['price_total']);
      //业绩即佣金总额
      /***
       * $cond_perfor_sell = $where." and contract_divide.achieve_broker_id_b = ".$vo['broker_id'].' and contract_divide.type = 1 ';
       * $cond_perfor_rent = $where." and contract_divide.achieve_broker_id_b = ".$vo['broker_id'].' and contract_divide.type = 2 ';
       * $cond_perfor_total = $where." and contract_divide.achieve_broker_id_b = ".$vo['broker_id'];
       * $perfor_sell = $this->contract_divide_model->get_all_by_count($cond_perfor_sell,0,0);
       * $perfor_rent = $this->contract_divide_model->get_all_by_count($cond_perfor_rent,0,0);
       * $perfor_total = $this->contract_divide_model->get_all_by_count($cond_perfor_total,0,0);
       * $broker_all_info[$key]['perfor_sell'] = 0;
       * $broker_all_info[$key]['perfor_rent'] = 0;
       * $broker_all_info[$key]['perfor_total'] = 0;
       * foreach($perfor_sell as $k=>$v){
       * $broker_all_info[$key]['perfor_sell'] += $v['commission_total'];
       * }
       * foreach($perfor_rent as $k=>$v){
       * $broker_all_info[$key]['perfor_rent'] += $v['commission_total'];
       * }
       * foreach($perfor_total as $k=>$v){
       * $broker_all_info[$key]['perfor_total'] += $v['commission_total'];
       * }
       * $broker_all_info[$key]['perfor_sell'] = sprintf('%.2f',$broker_all_info[$key]['perfor_sell']);
       * $broker_all_info[$key]['perfor_rent'] = sprintf('%.2f',$broker_all_info[$key]['perfor_rent']);
       * $broker_all_info[$key]['perfor_total'] = sprintf('%.2f',$broker_all_info[$key]['perfor_total']);
       ***/
      if ($type == 1) {
        $broker_all_info[$key]['divide_price_total'] = sprintf('%.2f', $broker_all_info[$key]['divide_price_sell']);
        //$broker_all_info[$key]['perfor_total'] = sprintf('%.2f',$broker_all_info[$key]['perfor_sell']);
      } elseif ($type == 2) {
        $broker_all_info[$key]['divide_price_total'] = sprintf('%.2f', $broker_all_info[$key]['divide_price_rent']);
        //$broker_all_info[$key]['perfor_total'] = sprintf('%.2f',$broker_all_info[$key]['perfor_rent']);
      }
    }
    $broker_info_new = array();
    switch ($type) {
      case 0:
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide_price_total');
        break;
      case 1://买卖出售
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide_price_sell');
        break;
      case 2://租赁出租
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide_price_rent');
        break;
    }
    //图表数据
    $perfor_total = 0;
    $divide_price_total = 0;
    $xinhao = '****************';
    foreach ($broker_info_new as $key => $vo) {
      $broker_info_new[$key]['rank'] = $key + 1;
      //$perfor_total += $vo['perfor_total'];
      $divide_price_total += $vo['divide_price_total'];
    }

    if ($role_level > 7) {
      foreach ($broker_info_new as $key => $vo) {
        $broker_info_new[$key]['divide_price_sell'] = substr($xinhao, 0, strlen(floor($vo['divide_price_sell'])));
        $broker_info_new[$key]['divide_price_rent'] = substr($xinhao, 0, strlen(floor($vo['divide_price_rent'])));
        $broker_info_new[$key]['divide_price_total'] = substr($xinhao, 0, strlen(floor($vo['divide_price_total'])));
        /***
         * $broker_info_new[$key]['perfor_sell'] = substr($xinhao,0,strlen(floor($vo['perfor_sell'])));
         * $broker_info_new[$key]['perfor_rent'] = substr($xinhao,0,strlen(floor($vo['perfor_rent'])));
         * $broker_info_new[$key]['perfor_total'] = substr($xinhao,0,strlen(floor($vo['perfor_total'])));
         ***/
      }
      //$perfor_total = substr($xinhao,0,strlen(floor($perfor_total))?strlen(floor($perfor_total)):1);
      $divide_price_total = substr($xinhao, 0, strlen(floor($divide_price_total)) ? strlen(floor($divide_price_total)) : 1);
    }

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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '排名');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '所属部门');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '员工');
    /***
     * $objPHPExcel->getActiveSheet()->setCellValue('D1', '总业绩');
     * $objPHPExcel->getActiveSheet()->setCellValue('E1', '买卖业绩');
     * $objPHPExcel->getActiveSheet()->setCellValue('F1', '租赁业绩');
     ***/
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '买卖业绩');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '租赁业绩');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '总业绩');

    if (is_full_array($broker_info_new)) {
      $i = 2;
      foreach ($broker_info_new as $key => $value) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($key + 2), $value['rank']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . ($key + 2), $value['agency_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . ($key + 2), $value['broker_name']);
        /***
         * $objPHPExcel->getActiveSheet()->setCellValue('D'.($key+2),$value['perfor_total']);
         * $objPHPExcel->getActiveSheet()->setCellValue('E'.($key+2),$value['perfor_sell']);
         * $objPHPExcel->getActiveSheet()->setCellValue('F'.($key+2),$value['perfor_rent']);
         **/
        $objPHPExcel->getActiveSheet()->setCellValue('D' . ($key + 2), $value['divide_price_sell']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . ($key + 2), $value['divide_price_rent']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . ($key + 2), $value['divide_price_total']);
        $i++;
      }
      $objPHPExcel->getActiveSheet()->setCellValue('A' . ($i), '');
      $objPHPExcel->getActiveSheet()->setCellValue('B' . ($i), '合计');
      /***
       * $objPHPExcel->getActiveSheet()->setCellValue('C'.($i),$perfor_total);
       * $objPHPExcel->getActiveSheet()->setCellValue('D'.($i),'');
       * $objPHPExcel->getActiveSheet()->setCellValue('E'.($i),'');
       ***/
      $objPHPExcel->getActiveSheet()->setCellValue('C' . ($i), '');
      //$objPHPExcel->getActiveSheet()->setCellValue('D'.($i),'合计');
      $objPHPExcel->getActiveSheet()->setCellValue('D' . ($i), $divide_price_total);
    }
    $fileName = time() . "_excel.xls";
    $objPHPExcel->getActiveSheet()->setTitle('员工业绩排行榜');
    $objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');
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
    // print_r($data);exit;
    $objWriter->save('php://output');
    exit;
  }

  private function _agency_count_export($type, $start_date, $end_date)
  {
    $broker_info = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    //店长以下的经纪人 获取部门
    $role_level = intval($broker_info['role_level']);
    //根据数据范围，获得门店数据
    $company_info = $this->agency_model->get_by_id($this->user_arr['company_id']);
    //门店信息
    $all_access_agency_ids = '';
    $agency_info_array = $this->agency_model->get_children_by_company_id($company_info['id']);
    //print_r($agency_info_array);
    if (is_full_array($agency_info_array)) {
      foreach ($agency_info_array as $k => $v) {
        $all_access_agency_ids .= $v['id'] . ',';
      }
      $all_access_agency_ids = trim($all_access_agency_ids, ',');
    }
    $agency_info = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);

    if (!isset($type)) {
      $type = 0;
    }


    //获取员工业绩数据
    if ($start_date) {
      $start_date = strtotime($start_date);
    } else {
      $year = date("Y");
      $month = date("m");
      $allday = date("t");
      $start_date = strtotime($year . "-" . $month . "-01");
    }
    if ($end_date) {
      $end_date = strtotime($end_date);
    } else {
      $end_date = time();
    }
    $where = "contract_divide.entry_time >= " . $start_date . " and contract_divide.entry_time <=" . $end_date;
    //处理经纪人业绩数据累加
    foreach ($agency_info as $key => $vo) {
      //提成
      $cond_divide_sell = $where . ' and achieve_agency_id_b = ' . $vo['agency_id'] . ' and type = 1 ';
      $cond_divide_rent = $where . ' and achieve_agency_id_b = ' . $vo['agency_id'] . ' and type = 2 ';
      $cond_divide_total = $where . ' and achieve_agency_id_b = ' . $vo['agency_id'];
      $divide_price_sell = $this->contract_divide_model->get_total_count($cond_divide_sell);
      $divide_price_rent = $this->contract_divide_model->get_total_count($cond_divide_rent);
      $divide_price_total = $this->contract_divide_model->get_total_count($cond_divide_total);
      $agency_info[$key]['divide_price_sell'] = sprintf('%.2f', $divide_price_sell['price_total']);
      $agency_info[$key]['divide_price_rent'] = sprintf('%.2f', $divide_price_rent['price_total']);
      $agency_info[$key]['divide_price_total'] = sprintf('%.2f', $divide_price_total['price_total']);
      //业绩即佣金总额
      /***
       * $cond_perfor_sell = $where." and contract_divide.achieve_agency_id_b = ".$vo['agency_id'].' and contract_divide.type = 1 ';
       * $cond_perfor_rent = $where." and contract_divide.achieve_agency_id_b = ".$vo['agency_id'].' and contract_divide.type = 2 ';
       * $cond_perfor_total = $where." and contract_divide.achieve_agency_id_b = ".$vo['agency_id'];
       * $perfor_sell = $this->contract_divide_model->get_all_by_count($cond_perfor_sell,0,0);
       * $perfor_rent = $this->contract_divide_model->get_all_by_count($cond_perfor_rent,0,0);
       * $perfor_total = $this->contract_divide_model->get_all_by_count($cond_perfor_total,0,0);
       * $agency_info[$key]['perfor_sell'] = 0;
       * $agency_info[$key]['perfor_rent'] = 0;
       * $agency_info[$key]['perfor_total'] = 0;
       * foreach($perfor_sell as $k=>$v){
       * $agency_info[$key]['perfor_sell'] += $v['commission_total'];
       * }
       * foreach($perfor_rent as $k=>$v){
       * $agency_info[$key]['perfor_rent'] += $v['commission_total'];
       * }
       * foreach($perfor_total as $k=>$v){
       * $agency_info[$key]['perfor_total'] += $v['commission_total'];
       * }
       * $agency_info[$key]['perfor_sell'] = sprintf('%.2f',$agency_info[$key]['perfor_sell']);
       * $agency_info[$key]['perfor_rent'] = sprintf('%.2f',$agency_info[$key]['perfor_rent']);
       * $agency_info[$key]['perfor_total'] = sprintf('%.2f',$agency_info[$key]['perfor_total']);
       **/
      if ($type == 1) {
        $agency_info[$key]['divide_price_total'] = sprintf('%.2f', $agency_info[$key]['divide_price_sell']);
        //$agency_info[$key]['perfor_total'] = sprintf('%.2f',$agency_info[$key]['perfor_sell']);
      } elseif ($type == 2) {
        $agency_info[$key]['divide_price_total'] = sprintf('%.2f', $agency_info[$key]['divide_price_rent']);
        //$agency_info[$key]['perfor_total'] = sprintf('%.2f',$agency_info[$key]['perfor_rent']);
      }
    }
    //print_r($agency_info);exit;
    $agency_info_new = array();
    switch ($type) {
      case 0:
        $agency_info_new = $this->multi_array_sort($agency_info, 'divide_price_total');
        break;
      case 1://买卖出售
        $agency_info_new = $this->multi_array_sort($agency_info, 'divide_price_sell');
        break;
      case 2://租赁出租
        $agency_info_new = $this->multi_array_sort($agency_info, 'divide_price_rent');
        break;
    }
    //图表数据
    //$perfor_total = 0;
    $divide_price_total = 0;
    $xinhao = '**********************';
    foreach ($agency_info_new as $key => $vo) {
      $agency_info_new[$key]['rank'] = $key + 1;
      //$perfor_total += $vo['perfor_total'];
      $divide_price_total += $vo['divide_price_total'];
    }
    if ($role_level > 7) {
      foreach ($agency_info_new as $key => $vo) {
        $agency_info_new[$key]['divide_price_sell'] = substr($xinhao, 0, strlen(floor($vo['divide_price_sell'])));
        $agency_info_new[$key]['divide_price_rent'] = substr($xinhao, 0, strlen(floor($vo['divide_price_rent'])));
        $agency_info_new[$key]['divide_price_total'] = substr($xinhao, 0, strlen(floor($vo['divide_price_total'])));
        /***
         * $agency_info_new[$key]['perfor_sell'] = substr($xinhao,0,strlen(floor($vo['perfor_sell'])));
         * $agency_info_new[$key]['perfor_rent'] = substr($xinhao,0,strlen(floor($vo['perfor_rent'])));
         * $agency_info_new[$key]['perfor_total'] = substr($xinhao,0,strlen(floor($vo['perfor_total'])));
         ***/
      }
      //$perfor_total = substr($xinhao,0,strlen(floor($perfor_total))?strlen(floor($perfor_total)):1);
      $divide_price_total = substr($xinhao, 0, strlen(floor($divide_price_total)) ? strlen(floor($divide_price_total)) : 1);
    } elseif ($role_level == 6 || $role_level == 7) {
      foreach ($agency_info_new as $key => $vo) {
        if ($vo['agency_id'] != $broker_info['agency_id']) {
          $agency_info_new[$key]['divide_price_sell'] = substr($xinhao, 0, strlen(floor($vo['divide_price_sell'])));
          $agency_info_new[$key]['divide_price_rent'] = substr($xinhao, 0, strlen(floor($vo['divide_price_rent'])));
          $agency_info_new[$key]['divide_price_total'] = substr($xinhao, 0, strlen(floor($vo['divide_price_total'])));
          /***
           * $agency_info_new[$key]['perfor_sell'] = substr($xinhao,0,strlen(floor($vo['perfor_sell'])));
           * $agency_info_new[$key]['perfor_rent'] = substr($xinhao,0,strlen(floor($vo['perfor_rent'])));
           * $agency_info_new[$key]['perfor_total'] = substr($xinhao,0,strlen(floor($vo['perfor_total'])));
           ***/
        }
      }
      //$perfor_total = substr($xinhao,0,strlen(floor($perfor_total))?strlen(floor($perfor_total)):1);
      $divide_price_total = substr($xinhao, 0, strlen(floor($divide_price_total)) ? strlen(floor($divide_price_total)) : 1);
    }
    //print_r($broker_info_two);exit;

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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '排名');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '所属部门');
    /***
     * $objPHPExcel->getActiveSheet()->setCellValue('C1', '总业绩');
     * $objPHPExcel->getActiveSheet()->setCellValue('D1', '买卖业绩');
     * $objPHPExcel->getActiveSheet()->setCellValue('E1', '租赁业绩');**/

    $objPHPExcel->getActiveSheet()->setCellValue('C1', '买卖业绩');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '租赁业绩');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '总业绩');

    if (is_full_array($agency_info_new)) {
      $i = 2;
      foreach ($agency_info_new as $key => $value) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($key + 2), $value['rank']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . ($key + 2), $value['agency_name']);
        /**
         * $objPHPExcel->getActiveSheet()->setCellValue('C'.($key+2),$value['perfor_total']);
         * $objPHPExcel->getActiveSheet()->setCellValue('D'.($key+2),$value['perfor_sell']);
         * $objPHPExcel->getActiveSheet()->setCellValue('E'.($key+2),$value['perfor_rent']);
         **/
        $objPHPExcel->getActiveSheet()->setCellValue('C' . ($key + 2), $value['divide_price_sell']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . ($key + 2), $value['divide_price_rent']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . ($key + 2), $value['divide_price_total']);
        $i++;
      }
      $objPHPExcel->getActiveSheet()->setCellValue('A' . ($i), '');
      $objPHPExcel->getActiveSheet()->setCellValue('B' . ($i), '合计');
      /***
       * $objPHPExcel->getActiveSheet()->setCellValue('C'.($i),$perfor_total);
       * $objPHPExcel->getActiveSheet()->setCellValue('D'.($i),'');
       * $objPHPExcel->getActiveSheet()->setCellValue('E'.($i),'');
       ***/
      $objPHPExcel->getActiveSheet()->setCellValue('C' . ($i), '');
      //$objPHPExcel->getActiveSheet()->setCellValue('D'.($i),'合计');
      $objPHPExcel->getActiveSheet()->setCellValue('D' . ($i), $divide_price_total);
    }
    $fileName = time() . "_excel.xls";
    $objPHPExcel->getActiveSheet()->setTitle('员工业绩排行榜');
    $objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');
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
    // print_r($data);exit;
    $objWriter->save('php://output');
    exit;
  }

  private function _contract_count_export($agency_id, $type, $start_date, $end_date, $broker_id)
  {
    $broker_info = $this->user_arr;

    //门店
    if (!isset($agency_id)) {
      $agency_id = $broker_info['agency_id'];
    }

    if (!isset($type)) {
      $type = 0;
    }

    //开始时间
    if (!isset($start_date)) {
      $start_date = date('Y-m-d', strtotime("-6 day"));
    }
    //截止时间
    if (!isset($end_date)) {
      $end_date = date('Y-m-d');
    }


    //查询员工的条件
    $time = time();
    $cond_where = " where expiretime >= {$time} and status = 1 and company_id =" . $broker_info['company_id'];
    if ($agency_id) {
      $cond_where .= " and agency_id = " . $agency_id;
    }

    //经纪人
    if ($broker_id) {
      $cond_where .= " and broker_id = " . $broker_id;
    }
    //排序
    $cond_where .= " order by id DESC ";

    //获取员工列表内容
    $broker_all_info = $this->check_work_model->get_all_by($cond_where, 0, 0);
    //获取员工业绩数据
    $start_date = strtotime($start_date . " 00:00:00 ");
    $end_date = strtotime($end_date . " 23:59:59 ");

    $where = "check_time >= " . $start_date . " and check_time <=" . $end_date;
    //处理经纪人业绩数据累加
    foreach ($broker_all_info as $key => $vo) {
      $broker_info_one = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
      $agency_info = $this->agency_model->get_by_id($broker_info_one['agency_id']);
      $broker_all_info[$key]['agency_name'] = $agency_info['agency_name'] ? $agency_info['agency_name'] : $agency_info['name'];
      $broker_all_info[$key]['broker_name'] = $broker_info_one['truename'];
      //合同出售
      $cond_contract_sell = $where . ' and broker_id_a = ' . $vo['broker_id'] . ' and is_check = 2 and type = 1';
      //合同出租
      $cond_contract_rent = $where . ' and broker_id_a = ' . $vo['broker_id'] . ' and is_check = 2 and type = 2';
      //托管合同
      $cond_collocation_contract = $where . ' and broker_id = ' . $vo['broker_id'] . ' and status = 2 ';
      //托管出租合同
      $cond_collocation_rent = $where . ' and broker_id = ' . $vo['broker_id'] . ' and status = 2 ';
      $contract_sell_total = $this->contract_model->count_by($cond_contract_sell);
      $contract_rent_total = $this->contract_model->count_by($cond_contract_rent);
      $collocation_contract_total = $this->collocation_contract_model->count_by($cond_collocation_contract);
      $collocation_rent_total = $this->collocation_contract_model->count_by_tab($cond_collocation_rent, $tab = '4');
      $broker_all_info[$key]['contract_sell_total'] = $contract_sell_total;
      $broker_all_info[$key]['contract_rent_total'] = $contract_rent_total;
      $broker_all_info[$key]['collocation_contract_total'] = $collocation_contract_total;
      $broker_all_info[$key]['collocation_rent_total'] = $collocation_rent_total;
      $broker_all_info[$key]['total_total'] = $contract_sell_total + $contract_rent_total + $collocation_contract_total + $collocation_rent_total;
    }

    $broker_info_new = array();
    switch ($type) {
      case 0://总计
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'total_total');
        break;
      case 1://合同出售
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'contract_sell_total');
        break;
      case 2://合同出租
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'contract_rent_total');
        break;
      case 3://托管合同
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'collocation_contract_total');
        break;
      case 4://托管出租合同
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'collocation_rent_total');
        break;
    }
    //图表数据
    foreach ($broker_info_new as $key => $vo) {
      $broker_info_new[$key]['rank'] = $key + 1;
    }
    //print_r($broker_info_two);exit;
    //print_r($yAxis);

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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '排名');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '用户名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '二手房签单');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '租房签单');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '托管签单');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '托管出租签单');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '总计');

    if (is_full_array($broker_info_new)) {
      $i = 2;
      foreach ($broker_info_new as $key => $value) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($key + 2), $value['rank']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . ($key + 2), $value['broker_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . ($key + 2), $value['contract_sell_total']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . ($key + 2), $value['contract_rent_total']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . ($key + 2), $value['collocation_contract_total']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . ($key + 2), $value['collocation_rent_total']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . ($key + 2), $value['total_total']);
        $i++;
      }
    }
    $fileName = time() . "_excel.xls";
    $objPHPExcel->getActiveSheet()->setTitle('合同统计');
    $objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');
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
    // print_r($data);exit;
    $objWriter->save('php://output');
    exit;
  }

  private function _contract_divide_count_export($agency_id, $type, $start_date, $end_date, $broker_id)
  {
    $broker_info = $this->user_arr;

    //门店
    if (!isset($agency_id)) {
      $agency_id = $broker_info['agency_id'];
    }

    if (!isset($type)) {
      $type = 0;
    }

    //开始时间
    if (!isset($start_date)) {
      $start_date = date('Y-m-d', strtotime("-6 day"));
    }
    //截止时间
    if (!isset($end_date)) {
      $end_date = date('Y-m-d');
    }


    //查询员工的条件
    $time = time();
    $cond_where = " where expiretime >= {$time} and status = 1 and company_id = " . $broker_info['company_id'];
    if ($agency_id) {
      $cond_where .= " and agency_id = " . $agency_id;
    }

    //经纪人
    if ($broker_id) {
      $cond_where .= " and broker_id = " . $broker_id;
    }
    //排序
    $cond_where .= " order by id DESC ";

    //获取员工列表内容
    $broker_all_info = $this->check_work_model->get_all_by($cond_where, 0, 0);
    //获取员工业绩数据
    $start_date = strtotime($start_date . " 00:00:00 ");
    $end_date = strtotime($end_date . " 23:59:59 ");

    $where = "contract_divide.entry_time >= " . $start_date . " and contract_divide.entry_time <=" . $end_date;
    //处理经纪人业绩数据累加
    foreach ($broker_all_info as $key => $vo) {
      $broker_info_one = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
      $agency_info = $this->agency_model->get_by_id($broker_info_one['agency_id']);
      $broker_all_info[$key]['agency_name'] = $agency_info['agency_name'] ? $agency_info['agency_name'] : $agency_info['name'];
      $broker_all_info[$key]['broker_name'] = $broker_info_one['truename'];
      //提成
      $cond_divide1 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 1 ';
      $cond_divide2 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 2 ';
      $cond_divide3 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 3 ';
      $cond_divide4 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 4 ';
      $cond_divide5 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 5 ';
      $cond_divide6 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 6 ';
      $cond_divide7 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 7 ';
      $cond_divide8 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 8 ';
      $cond_divide9 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 9 ';
      $cond_divide10 = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'] . ' and divide_type = 10 ';
      $cond_divide_total = $where . ' and achieve_broker_id_b = ' . $vo['broker_id'];
      $divide1 = $this->contract_divide_model->get_total_count($cond_divide1);
      $divide2 = $this->contract_divide_model->get_total_count($cond_divide2);
      $divide3 = $this->contract_divide_model->get_total_count($cond_divide3);
      $divide4 = $this->contract_divide_model->get_total_count($cond_divide4);
      $divide5 = $this->contract_divide_model->get_total_count($cond_divide5);
      $divide6 = $this->contract_divide_model->get_total_count($cond_divide6);
      $divide7 = $this->contract_divide_model->get_total_count($cond_divide7);
      $divide8 = $this->contract_divide_model->get_total_count($cond_divide8);
      $divide9 = $this->contract_divide_model->get_total_count($cond_divide9);
      $divide10 = $this->contract_divide_model->get_total_count($cond_divide10);
      $divide_total = $this->contract_divide_model->get_total_count($cond_divide_total);
      $broker_all_info[$key]['divide1'] = $divide1['total'];
      $broker_all_info[$key]['divide2'] = $divide2['total'];
      $broker_all_info[$key]['divide3'] = $divide3['total'];
      $broker_all_info[$key]['divide4'] = $divide4['total'];
      $broker_all_info[$key]['divide5'] = $divide5['total'];
      $broker_all_info[$key]['divide6'] = $divide6['total'];
      $broker_all_info[$key]['divide7'] = $divide7['total'];
      $broker_all_info[$key]['divide8'] = $divide8['total'];
      $broker_all_info[$key]['divide9'] = $divide9['total'];
      $broker_all_info[$key]['divide10'] = $divide10['total'];
      $broker_all_info[$key]['divide_total'] = $divide_total['total'];
    }

    $broker_info_new = array();
    switch ($type) {
      case 0:
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide_total');
        break;
      case 1://1房源
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide1');
        break;
      case 2://2客源
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide2');
        break;
      case 3://3钥匙
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide3');
        break;
      case 4://4独家
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide4');
        break;
      case 5://5签合同
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide5');
        break;
      case 6://6转介绍
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide6');
        break;
      case 7://7收房
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide7');
        break;
      case 8://8勘察
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide8');
        break;
      case 9://9代办贷款
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide9');
        break;
      case 10://10其他
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'divide10');
        break;
    }
    //图表数据
    foreach ($broker_info_new as $key => $vo) {
      $broker_info_new[$key]['rank'] = $key + 1;
    }
    //print_r($broker_info_two);exit;
    //print_r($yAxis);

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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '排名');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '用户名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '房源');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '客源');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '钥匙');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '独家');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '签合同');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '转介绍');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '收房');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '勘察');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '代办贷款');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '其他');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '总计');

    if (is_full_array($broker_info_new)) {
      $i = 2;
      foreach ($broker_info_new as $key => $value) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($key + 2), $value['rank']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . ($key + 2), $value['broker_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . ($key + 2), $value['divide1']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . ($key + 2), $value['divide2']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . ($key + 2), $value['divide3']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . ($key + 2), $value['divide4']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . ($key + 2), $value['divide5']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . ($key + 2), $value['divide6']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . ($key + 2), $value['divide7']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . ($key + 2), $value['divide8']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . ($key + 2), $value['divide9']);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . ($key + 2), $value['divide10']);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . ($key + 2), $value['divide_total']);
        $i++;
      }
    }
    $fileName = time() . "_excel.xls";
    $objPHPExcel->getActiveSheet()->setTitle('分成统计');
    $objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');
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
    // print_r($data);exit;
    $objWriter->save('php://output');
    exit;
  }

  /**
   * 群发统计
   *
   */
  public function mass_count($count_type = 0)
  {
    if ($count_type == 1) {
      $this->_group_publish_count();//站点发布
    } elseif ($count_type == 2) {
      $this->_group_refresh_count();//站点刷新
    } else {
      $this->_mass_count();//综合
    }
  }

  private function _mass_count()
  {
    $data_view = array();
    $data_view['user_menu'] = $this->user_menu;
    $broker_info = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);
    $pg = $post_param['page'];

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    //店长以下的经纪人 获取部门
    $role_level = intval($broker_info['role_level']);
    //武汉站站门店下来逻辑独立，新角色能看到所有门店
    if ('37' == $broker_info['city_id'] && 12 == $role_level) {
      $is_other_agency = false;
    } else {
      $is_other_agency = true;
    }
    if (is_int($role_level) && $role_level > 7 && $is_other_agency) {//店长以下
      $data_view['agency_info1'] = array('agency_name' => $broker_info['agency_name'], 'broker_name' => $broker_info['truename']);
    } elseif (is_int($role_level) && ($role_level == 6 || $role_level == 7)) {//店长
      $data_view['agency_info2'] = array('agency_name' => $broker_info['agency_name'], 'agency_id' => $broker_info['agency_id']);
    } else {//店长以上
      //根据数据范围，获得门店数据
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_house_count');
      $all_access_agency_ids = '';
      if (is_full_array($access_agency_ids_data)) {
        foreach ($access_agency_ids_data as $k => $v) {
          $all_access_agency_ids .= $v['sub_agency_id'] . ',';
        }
        $all_access_agency_ids .= $this->user_arr['agency_id'];
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      } else {
        $all_access_agency_ids = $this->user_arr['agency_id'];
      }
      $data_view['agency_info'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
    }

    //开始时间
    if (!isset($post_param['start_date'])) {
      $post_param['start_date'] = date('Y-m-d', strtotime("-6 day"));
    }
    //截止时间
    if (!isset($post_param['end_date'])) {
      $post_param['end_date'] = date('Y-m-d');
    }


    //复选框值
    if (!isset($post_param['state'])) {
      $post_param['state'] = array(1, 2, 3);
    }
    if (!isset($post_param['type']) || !in_array($post_param['type'], $post_param['state'])) {
      $post_param['type'] = $post_param['state'][0];
    }

    //查询员工的条件
    $time = time();
    $broker_info['company_id'] = ($broker_info['company_id'] ? $broker_info['company_id'] : '0');
    $cond_where = " where expiretime >= {$time} and status = 1 and company_id = " . $broker_info['company_id'];
    //门店
    if ($post_param['agency_id'] == '') {
      $post_param['agency_id'] = $broker_info['agency_id'];
      $cond_where .= " and agency_id = " . $post_param['agency_id'];
      $broker = $this->api_broker_model->get_brokers_agency_id($post_param['agency_id']);
      $data_view['broker'] = $broker;
    } elseif ($post_param['agency_id']) {
      $cond_where .= " and agency_id = " . $post_param['agency_id'];
      $broker = $this->api_broker_model->get_brokers_agency_id($post_param['agency_id']);
      $data_view['broker'] = $broker;
    }
    //经纪人
    if ($post_param['broker_id'] == '') {
      $post_param['broker_id'] = $broker_info['broker_id'];
      $cond_where .= " and broker_id = " . $post_param['broker_id'];
    } elseif ($post_param['broker_id']) {
      $cond_where .= " and broker_id = " . $post_param['broker_id'];
    }
    //符合条件的总行数
    $this->_total_count = $this->check_work_model->count_by($cond_where);
    //排序
    $cond_where .= " order by id DESC ";

    //获取员工列表内容
    $broker_all_info = $this->check_work_model->get_all_by($cond_where, 0, 0);
    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page, $this->_limit);
    //获取员工综合数据
    $where = 'YMD >= "' . $post_param['start_date'] . '" and YMD <= "' . $post_param['end_date'] . '" and agency_id = ' . $post_param['agency_id'];

    //处理经纪人业绩数据累加
    foreach ($broker_all_info as $key => $vo) {
      $broker_info_one = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
      $agency_info = $this->agency_model->get_by_id($broker_info_one['agency_id']);
      $broker_all_info[$key]['agency_name'] = $agency_info['agency_name'] ? $agency_info['agency_name'] : $agency_info['name'];
      $broker_all_info[$key]['broker_name'] = $broker_info_one['truename'];

      $cond_publish = $where . ' and broker_id = ' . $vo['broker_id'];
      $cond_refresh = $where . ' and broker_id = ' . $vo['broker_id'];
      $cond_site = "broker_id = " . $vo['broker_id'] . " and status = 1 ";
      /**发布和刷新数据搜索条件开始**/
      $count_num = 'wuba_num+wuba_vip_num+ganji_num+ganji_vip_num+anjuke_num+fang_num+taofang_num';
      $select_fields = array('sum(' . $count_num . ') as sum_num');
      $this->publish_count_num_model->set_select_fields($select_fields);
      $publish_num = $this->publish_count_num_model->get_all_by($cond_publish, 0, 0, 'sum_num');
      $this->group_refresh_model->set_select_fields($select_fields);
      $refresh_num = $this->group_refresh_model->get_all_by($cond_refresh, 0, 0, 'sum_num');
      $site_num = $this->site_model->get_site_broker_num($cond_site);

      $broker_all_info[$key]['publish_num'] = $publish_num[0]['sum_num'] ? $publish_num[0]['sum_num'] : 0;
      $broker_all_info[$key]['refresh_num'] = $refresh_num[0]['sum_num'] ? $refresh_num[0]['sum_num'] : 0;
      $broker_all_info[$key]['site_num'] = $site_num ? $site_num : 0;

    }
    //print_r($broker_all_info);exit;
    $broker_info_new = array();
    switch ($post_param['type']) {
      case 1://1发布量
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'publish_num');
        break;
      case 2://2刷新量
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'refresh_num');
        break;
      case 3://3站点数
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'site_num');
        break;
    }
    //图表数据
    $yAxis = array();
    $broker_info_two = array();
    if (is_full_array($broker_info_new)) {
      foreach ($broker_info_new as $key => $vo) {
        $broker_info_new[$key]['rank'] = $key + 1;
        if ($key <= 9) {
          $xAxis[$key + 1] = $vo['broker_name'];
          $yAxis[1][] = $vo['publish_num'];
          $yAxis[2][] = $vo['refresh_num'];
          $yAxis[3][] = $vo['site_num'];
        }
        if ($key >= $this->_offset && $key < ($this->_offset + $this->_limit)) {
          $broker_info_two[$key] = $broker_info_new[$key];
        }
      }
    }
    //print_r($broker_info_two);exit;
    //print_r($yAxis);
    $data_view['xAxis'] = $xAxis;
    $data_view['yAxis'] = $yAxis;
    $data_view['broker_info_new'] = $broker_info_two;

    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //查询数据返回
    $data_view['post_param'] = $post_param;
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data_view['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data_view['page_title'] = '综合统计';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/date_analysis.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/highcharts.js');
    //底部JS
    $data_view['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js');

    $this->view('count_info/_mass_count', $data_view);
  }

  private function _group_publish_count()
  {
    $data_view = array();
    $data_view['user_menu'] = $this->user_menu;
    $broker_info = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);
    $pg = $post_param['page'];

    $where = $broker_info['company_id'] > 0 ? 'company_id = ' . $broker_info['company_id'] : 'company_id = 0';

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    $role_id = $broker_info['role_id'];
    $this->load->model('permission_company_group_model');
    $role_data = $this->permission_company_group_model->get_one_by(array('id' => $role_id));
    if (is_full_array($role_data)) {
      $system_group_id = intval($role_data['system_group_id']);
    }
    $role_level = intval($broker_info['role_level']);
    //武汉站站门店下来逻辑独立，新角色能看到所有门店
    if ('37' == $broker_info['city_id'] && 12 == $role_level) {
      $is_other_agency = false;
    } else {
      $is_other_agency = true;
    }
    //店长以下的经纪人 获取部门
    if (is_int($role_level) && $role_level > 7 && $is_other_agency) {//店长以下
      $data_view['agency_info1'] = array('agency_name' => $broker_info['agency_name'], 'broker_name' => $broker_info['truename']);
    } elseif (is_int($role_level) && ($role_level == 6 || $role_level == 7)) {//店长
      $data_view['agency_info2'] = array('agency_name' => $broker_info['agency_name'], 'agency_id' => $broker_info['agency_id']);
    } else {//店长以上
      //根据数据范围，获得门店数据
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_work_count');
      $all_access_agency_ids = '';
      if (is_full_array($access_agency_ids_data)) {
        foreach ($access_agency_ids_data as $k => $v) {
          $all_access_agency_ids .= $v['sub_agency_id'] . ',';
        }
        $all_access_agency_ids .= $this->user_arr['agency_id'];
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      } else {
        $all_access_agency_ids = $this->user_arr['agency_id'];
      }
      $data_view['agency_info'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
    }


    //开始时间
    if (!isset($post_param['start_date_begin'])) {
      $post_param['start_date_begin'] = date('Y-m-d', strtotime("-6 day"));
    }
    if ($post_param['start_date_begin']) {
      $where .= ' and YMD >= "' . $post_param['start_date_begin'] . '"';
    }
    //截止时间
    if (!isset($post_param['start_date_end'])) {
      $post_param['start_date_end'] = date('Y-m-d');
    }
    if ($post_param['start_date_end']) {
      $where .= ' and YMD <= "' . $post_param['start_date_end'] . '"';
    }

    //门店
    if (!isset($post_param['agency_id'])) {
      $post_param['agency_id'] = $broker_info['agency_id'];
    }
    if ($post_param['agency_id']) {
      $where .= ' and agency_id = ' . $post_param['agency_id'];
      $broker = $this->api_broker_model->get_brokers_agency_id($post_param['agency_id']);
      $data_view['broker'] = $broker;
    }
    //print_r($broker);
    //经纪人
    if (!isset($post_param['broker_id'])) {
      $post_param['broker_id'] = $broker_info['broker_id'];
    }
    if ($post_param['broker_id']) {
      $where .= ' and broker_id = ' . $post_param['broker_id'];
    }

    $data_view['post_param'] = $post_param;
    //获取百分比的where条件
    $where_rate = $where;
    $where .= ' group by broker_id';
    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);
    $this->_total_count = $this->publish_count_num_model->group_broker_count_by($where);
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data_view['page_list'] = $this->page_list->show('jump');
    $count_num = 'wuba_num+wuba_vip_num+ganji_num+ganji_vip_num+anjuke_num+fang_num+taofang_num';
    $select_fields = array('*', 'sum(' . $count_num . ') as sum_num', 'sum(wuba_num) as wuba_num', 'sum(wuba_vip_num) as wuba_vip_num', 'sum(ganji_num) as ganji_num', 'sum(ganji_vip_num) as ganji_vip_num', 'sum(anjuke_num) as anjuke_num', 'sum(fang_num) as fang_num', 'sum(taofang_num) as taofang_num');
    $this->publish_count_num_model->set_select_fields($select_fields);
    $count_num_info = $this->publish_count_num_model->get_all_by($where, $this->_offset, $this->_limit, 'sum_num');
    if (is_full_array($count_num_info)) {
      foreach ($count_num_info as $key => $value) {
        $count_num_info[$key]['wuba_num'] = $value['wuba_num'] ? $value['wuba_num'] : 0;
        $count_num_info[$key]['wuba_vip_num'] = $value['wuba_vip_num'] ? $value['wuba_vip_num'] : 0;
        $count_num_info[$key]['ganji_num'] = $value['ganji_num'] ? $value['ganji_num'] : 0;
        $count_num_info[$key]['ganji_vip_num'] = $value['ganji_vip_num'] ? $value['ganji_vip_num'] : 0;
        $count_num_info[$key]['anjuke_num'] = $value['anjuke_num'] ? $value['anjuke_num'] : 0;
        $count_num_info[$key]['fang_num'] = $value['fang_num'] ? $value['fang_num'] : 0;
        $count_num_info[$key]['taofang_num'] = $value['taofang_num'] ? $value['taofang_num'] : 0;
        $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);
        $count_num_info[$key]['truename'] = $brokers['truename'];
      }
    }
    $data_view['count_num_info'] = $count_num_info;
    //用于图表展示的信息-当门店不限的时候 最多显示10条否则显示全部
    $chat_data = $this->publish_count_num_model->get_all_by($where, $offset, $limit, 'sum_num');
    $top_data = array('sum_num' => array(), 'truename' => array());
    if (is_full_array($chat_data)) {
      foreach ($chat_data as $key => $value) {
        $top_data['sum_num'][$key] = (int)($value['sum_num'] ? $value['sum_num'] : 0);
        $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);
        $top_data['truename'][$key] = $brokers['truename'];
      }
    }
    $data_view['top_data'] = $top_data;
    //用于占比各大网站群发的
    $select_fields = array('sum(wuba_num) as wuba_num', 'sum(wuba_vip_num) as wuba_vip_num', 'sum(ganji_num) as ganji_num', 'sum(ganji_vip_num) as ganji_vip_num', 'sum(anjuke_num) as anjuke_num', 'sum(fang_num) as fang_num', 'sum(taofang_num) as taofang_num');
    $this->publish_count_num_model->set_select_fields($select_fields);
    $public_data = $this->publish_count_num_model->get_all_by($where_rate, -1);
    $public_format_data = $public_data[0];
    $public_sum = 0;
    foreach ($public_format_data as $value) {
      $public_sum += $value;
    }
    $new_public_data = array();
    $rate = 0;
    foreach ($public_format_data as $key => $value) {
      if ($public_sum != 0) {
        $rate = strip_end_0($value / $public_sum * 100, 2);
      }
      if ($key == 'wuba_num') {
        $new_public_data[] = array('58同城 ' . $rate . '%', $rate);
      } else if ($key == 'wuba_vip_num') {
        $new_public_data[] = array('58网邻通 ' . $rate . '%', $rate);
      } else if ($key == 'ganji_num') {
        $new_public_data[] = array('赶集网 ' . $rate . '%', $rate);
      } else if ($key == 'ganji_vip_num') {
        $new_public_data[] = array('赶集VIP ' . $rate . '%', $rate);
      } else if ($key == 'anjuke_num') {
        $new_public_data[] = array('安居客 ' . $rate . '%', $rate);
      } else if ($key == 'fang_num') {
        $new_public_data[] = array('房天下 ' . $rate . '%', $rate);
      } else if ($key == 'taofang_num') {
        $new_public_data[] = array('365淘房 ' . $rate . '%', $rate);
      }
    }
    //占比图形
    $data_view['new_public_data'] = $new_public_data;

    //页面标题
    $data_view['page_title'] = '站点发布';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/date_analysis.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/highcharts.js,mls/js/v1.0/exporting.js');
    //底部JS
    $data_view['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js');
    $this->view('count_info/group_publish_count', $data_view);
  }

  private function _group_refresh_count()
  {
    $data_view = array();
    $data_view['user_menu'] = $this->user_menu;
    $broker_info = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);
    $pg = $post_param['page'];

    $where = $broker_info['company_id'] > 0 ? 'company_id = ' . $broker_info['company_id'] : 'company_id = 0';

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    $role_id = $broker_info['role_id'];
    $this->load->model('permission_company_group_model');
    $role_data = $this->permission_company_group_model->get_one_by(array('id' => $role_id));
    if (is_full_array($role_data)) {
      $system_group_id = intval($role_data['system_group_id']);
    }
    $role_level = intval($broker_info['role_level']);
    //武汉站站门店下来逻辑独立，新角色能看到所有门店
    if ('37' == $broker_info['city_id'] && 12 == $role_level) {
      $is_other_agency = false;
    } else {
      $is_other_agency = true;
    }
    //店长以下的经纪人 获取部门
    if (is_int($role_level) && $role_level > 7 && $is_other_agency) {//店长以下
      $data_view['agency_info1'] = array('agency_name' => $broker_info['agency_name'], 'broker_name' => $broker_info['truename']);
    } elseif (is_int($role_level) && ($role_level == 6 || $role_level == 7)) {//店长
      $data_view['agency_info2'] = array('agency_name' => $broker_info['agency_name'], 'agency_id' => $broker_info['agency_id']);
    } else {//店长以上
      //根据数据范围，获得门店数据
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_work_count');
      $all_access_agency_ids = '';
      if (is_full_array($access_agency_ids_data)) {
        foreach ($access_agency_ids_data as $k => $v) {
          $all_access_agency_ids .= $v['sub_agency_id'] . ',';
        }
        $all_access_agency_ids .= $this->user_arr['agency_id'];
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      } else {
        $all_access_agency_ids = $this->user_arr['agency_id'];
      }
      $data_view['agency_info'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
    }


    //开始时间
    if (!isset($post_param['start_date_begin'])) {
      $post_param['start_date_begin'] = date('Y-m-d', strtotime("-6 day"));
    }
    if ($post_param['start_date_begin']) {
      $where .= ' and YMD >= "' . $post_param['start_date_begin'] . '"';
    }
    //截止时间
    if (!isset($post_param['start_date_end'])) {
      $post_param['start_date_end'] = date('Y-m-d');
    }
    if ($post_param['start_date_end']) {
      $where .= ' and YMD <= "' . $post_param['start_date_end'] . '"';
    }

    //门店
    if (!isset($post_param['agency_id'])) {
      $post_param['agency_id'] = $broker_info['agency_id'];
    }
    if ($post_param['agency_id']) {
      $where .= ' and agency_id = ' . $post_param['agency_id'];
      $broker = $this->api_broker_model->get_brokers_agency_id($post_param['agency_id']);
      $data_view['broker'] = $broker;
    }
    //print_r($broker);
    //经纪人
    if (!isset($post_param['broker_id'])) {
      $post_param['broker_id'] = $broker_info['broker_id'];
    }
    if ($post_param['broker_id']) {
      $where .= ' and broker_id = ' . $post_param['broker_id'];
    }

    $data_view['post_param'] = $post_param;
    //获取百分比的where条件
    $where_rate = $where;
    $where .= ' group by broker_id';
    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);
    $this->_total_count = $this->group_refresh_model->group_broker_count_by($where);
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data_view['page_list'] = $this->page_list->show('jump');
    $count_num = 'wuba_num+wuba_vip_num+ganji_num+ganji_vip_num+anjuke_num+fang_num+taofang_num';
    $select_fields = array('*', 'sum(' . $count_num . ') as sum_num', 'sum(wuba_num) as wuba_num', 'sum(wuba_vip_num) as wuba_vip_num', 'sum(ganji_num) as ganji_num', 'sum(ganji_vip_num) as ganji_vip_num', 'sum(anjuke_num) as anjuke_num', 'sum(fang_num) as fang_num', 'sum(taofang_num) as taofang_num');
    $this->group_refresh_model->set_select_fields($select_fields);
    $count_num_info = $this->group_refresh_model->get_all_by($where, $this->_offset, $this->_limit, 'sum_num');
    if (is_full_array($count_num_info)) {
      foreach ($count_num_info as $key => $value) {
        $count_num_info[$key]['wuba_num'] = $value['wuba_num'] ? $value['wuba_num'] : 0;
        $count_num_info[$key]['wuba_vip_num'] = $value['wuba_vip_num'] ? $value['wuba_vip_num'] : 0;
        $count_num_info[$key]['ganji_num'] = $value['ganji_num'] ? $value['ganji_num'] : 0;
        $count_num_info[$key]['ganji_vip_num'] = $value['ganji_vip_num'] ? $value['ganji_vip_num'] : 0;
        $count_num_info[$key]['anjuke_num'] = $value['anjuke_num'] ? $value['anjuke_num'] : 0;
        $count_num_info[$key]['fang_num'] = $value['fang_num'] ? $value['fang_num'] : 0;
        $count_num_info[$key]['taofang_num'] = $value['taofang_num'] ? $value['taofang_num'] : 0;
        $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);
        $count_num_info[$key]['truename'] = $brokers['truename'];
      }
    }
    $data_view['count_num_info'] = $count_num_info;
    //用于图表展示的信息-当门店不限的时候 最多显示10条否则显示全部
    $chat_data = $this->group_refresh_model->get_all_by($where, $offset, $limit, 'sum_num');
    $top_data = array('sum_num' => array(), 'truename' => array());
    if (is_full_array($chat_data)) {
      foreach ($chat_data as $key => $value) {
        $top_data['sum_num'][$key] = (int)($value['sum_num'] ? $value['sum_num'] : 0);
        $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);
        $top_data['truename'][$key] = $brokers['truename'];
      }
    }
    $data_view['top_data'] = $top_data;
    //用于占比各大网站群发的
    $select_fields = array('sum(wuba_num) as wuba_num', 'sum(wuba_vip_num) as wuba_vip_num', 'sum(ganji_num) as ganji_num', 'sum(ganji_vip_num) as ganji_vip_num', 'sum(anjuke_num) as anjuke_num', 'sum(fang_num) as fang_num', 'sum(taofang_num) as taofang_num');
    $this->group_refresh_model->set_select_fields($select_fields);
    $public_data = $this->group_refresh_model->get_all_by($where_rate, -1);
    $public_format_data = $public_data[0];
    $public_sum = 0;
    foreach ($public_format_data as $value) {
      $public_sum += $value;
    }
    $new_public_data = array();
    $rate = 0;
    foreach ($public_format_data as $key => $value) {
      if ($public_sum != 0) {
        $rate = strip_end_0($value / $public_sum * 100, 2);
      }
      if ($key == 'wuba_num') {
        $new_public_data[] = array('58同城 ' . $rate . '%', $rate);
      } else if ($key == 'wuba_vip_num') {
        $new_public_data[] = array('58网邻通 ' . $rate . '%', $rate);
      } else if ($key == 'ganji_num') {
        $new_public_data[] = array('赶集网 ' . $rate . '%', $rate);
      } else if ($key == 'ganji_vip_num') {
        $new_public_data[] = array('赶集VIP ' . $rate . '%', $rate);
      } else if ($key == 'anjuke_num') {
        $new_public_data[] = array('安居客 ' . $rate . '%', $rate);
      } else if ($key == 'fang_num') {
        $new_public_data[] = array('房天下 ' . $rate . '%', $rate);
      } else if ($key == 'taofang_num') {
        $new_public_data[] = array('365淘房 ' . $rate . '%', $rate);
      }
    }
    //占比图形
    $data_view['new_public_data'] = $new_public_data;

    //页面标题
    $data_view['page_title'] = '站点刷新';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/date_analysis.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/highcharts.js,mls/js/v1.0/exporting.js');
    //底部JS
    $data_view['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js');
    $this->view('count_info/_group_refresh_count', $data_view);
  }

  public function export_count($count_type)
  {
    if ($count_type == 1) {
      $this->_export_publish_count();//站点发布
    } elseif ($count_type == 2) {
      $this->_export_refresh_count();//站点刷新
    } else {
      $this->_export_mass_count();//综合统计
    }
  }

  public function _export_mass_count()
  {
    $broker_info = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);


    //开始时间
    if (!isset($post_param['start_date'])) {
      $post_param['start_date'] = date('Y-m-d', strtotime("-6 day"));
    }
    //截止时间
    if (!isset($post_param['end_date'])) {
      $post_param['end_date'] = date('Y-m-d');
    }

    //复选框值
    if (!isset($post_param['state'])) {
      $post_param['state'] = array(1, 2, 3);
    }
    if (!isset($post_param['type']) || !in_array($post_param['type'], $post_param['state'])) {
      $post_param['type'] = $post_param['state'][0];
    }

    //查询员工的条件
    $time = time();
    $cond_where = " where expiretime >= {$time} and status = 1 and company_id = " . $broker_info['company_id'];
    //门店
    if ($post_param['agency_id'] == '') {
      $post_param['agency_id'] = $broker_info['agency_id'];
      $cond_where .= " and agency_id = " . $post_param['agency_id'];
    } elseif ($post_param['agency_id']) {
      $cond_where .= " and agency_id = " . $post_param['agency_id'];
    }
    $cond_where .= " and agency_id = " . $post_param['agency_id'];
    //经纪人
    if ($post_param['broker_id'] == '') {
      $post_param['broker_id'] = $broker_info['broker_id'];
      $cond_where .= " and broker_id = " . $post_param['broker_id'];
    } elseif ($post_param['broker_id']) {
      $cond_where .= " and broker_id = " . $post_param['broker_id'];
    }
    //排序
    $cond_where .= " order by id DESC ";

    //获取员工列表内容
    $broker_all_info = $this->check_work_model->get_all_by($cond_where, 0, 0);
    //获取员工综合数据
    $where = 'YMD >= "' . $post_param['start_date'] . '" and YMD <= "' . $post_param['end_date'] . '" and agency_id = ' . $post_param['agency_id'];

    //处理经纪人业绩数据累加
    foreach ($broker_all_info as $key => $vo) {
      $broker_info_one = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
      $agency_info = $this->agency_model->get_by_id($broker_info_one['agency_id']);
      $broker_all_info[$key]['agency_name'] = $agency_info['agency_name'] ? $agency_info['agency_name'] : $agency_info['name'];
      $broker_all_info[$key]['broker_name'] = $broker_info_one['truename'];

      $cond_publish = $where . ' and broker_id = ' . $vo['broker_id'];
      $cond_refresh = $where . ' and broker_id = ' . $vo['broker_id'];
      $cond_site = "broker_id = " . $vo['broker_id'] . " and status = 1 ";
      /**发布和刷新数据搜索条件开始**/
      $count_num = 'wuba_num+wuba_vip_num+ganji_num+ganji_vip_num+anjuke_num+fang_num+taofang_num';
      $select_fields = array('sum(' . $count_num . ') as sum_num');
      $this->publish_count_num_model->set_select_fields($select_fields);
      $publish_num = $this->publish_count_num_model->get_all_by($cond_publish, 0, 0, 'sum_num');
      $this->group_refresh_model->set_select_fields($select_fields);
      $refresh_num = $this->group_refresh_model->get_all_by($cond_refresh, 0, 0, 'sum_num');
      $site_num = $this->site_model->get_site_broker_num($cond_site);

      $broker_all_info[$key]['publish_num'] = $publish_num[0]['sum_num'] ? $publish_num[0]['sum_num'] : 0;
      $broker_all_info[$key]['refresh_num'] = $refresh_num[0]['sum_num'] ? $refresh_num[0]['sum_num'] : 0;
      $broker_all_info[$key]['site_num'] = $site_num ? $site_num : 0;

    }
    //print_r($broker_all_info);exit;
    $broker_info_new = array();
    switch ($post_param['type']) {
      case 1://1发布量
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'publish_num');
        break;
      case 2://2刷新量
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'refresh_num');
        break;
      case 3://3站点数
        $broker_info_new = $this->multi_array_sort($broker_all_info, 'site_num');
        break;
    }
    foreach ($broker_info_new as $key => $vo) {
      $broker_info_new[$key]['rank'] = $key + 1;
    }
    $count_num_info = $broker_info_new;

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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '排名');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '用户名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '发布量');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '刷新量');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '站点数');

    if (is_full_array($count_num_info)) {
      foreach ($count_num_info as $key => $value) {
        $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);
        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($key + 2), $key + 1);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . ($key + 2), $brokers['truename']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . ($key + 2), $value['publish_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . ($key + 2), $value['refresh_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . ($key + 2), $value['site_num']);
      }
      $fileName = time() . "_excel.xls";
      $objPHPExcel->getActiveSheet()->setTitle('群发统计');
      $objPHPExcel->setActiveSheetIndex(0);
      // Redirect output to a client’s web browser (Excel5)
      header('Content-Type: application/vnd.ms-excel;charset=utf-8');
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
      // print_r($data);exit;
      $objWriter->save('php://output');
      exit;
    }
  }

  public function _export_publish_count()
  {
    $this->load->model('publish_count_num_model');
    $data_view = array();
    $broker_info = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);
    $where = $broker_info['company_id'] > 0 ? 'company_id = ' . $broker_info['company_id'] : 'company_id = 0';
    //开始时间
    if ($post_param['start_date_begin']) {
      $where .= ' and YMD >= "' . $post_param['start_date_begin'] . '"';
    }
    //截止时间
    if ($post_param['start_date_end']) {
      $where .= ' and YMD <= "' . $post_param['start_date_end'] . '"';
    }
    //门店
    if (!isset($post_param['agency_id'])) {
      $post_param['agency_id'] = $broker_info['agency_id'];
    }
    if ($post_param['agency_id']) {
      $where .= ' and agency_id = ' . $post_param['agency_id'];
    }
    //print_r($broker);
    //经纪人
    if (!isset($post_param['broker_id'])) {
      $post_param['broker_id'] = $broker_info['broker_id'];
    }
    if ($post_param['broker_id']) {
      $where .= ' and broker_id = ' . $post_param['broker_id'];
    }
    $where .= ' group by broker_id';
    $count_num = 'wuba_num+wuba_vip_num+ganji_num+ganji_vip_num+anjuke_num+fang_num+taofang_num';
    $select_fields = array('*', 'sum(' . $count_num . ') as sum_num', 'sum(wuba_num) as wuba_num', 'sum(wuba_vip_num) as wuba_vip_num', 'sum(ganji_num) as ganji_num', 'sum(ganji_vip_num) as ganji_vip_num', 'sum(anjuke_num) as anjuke_num', 'sum(fang_num) as fang_num', 'sum(taofang_num) as taofang_num');
    $this->publish_count_num_model->set_select_fields($select_fields);
    $count_num_info = $this->publish_count_num_model->get_all_by($where, -1, 20, 'sum_num');

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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '排名');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '用户名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '58同城');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '58网邻通');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '赶集网');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '赶集VIP');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '安居客');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '365淘房');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '房天下');

    if (is_full_array($count_num_info)) {
      foreach ($count_num_info as $key => $value) {
        $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);
        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($key + 2), $key + 1);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . ($key + 2), $brokers['truename']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . ($key + 2), $value['wuba_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . ($key + 2), $value['wuba_vip_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . ($key + 2), $value['ganji_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . ($key + 2), $value['ganji_vip_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . ($key + 2), $value['anjuke_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . ($key + 2), $value['taofang_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . ($key + 2), $value['fang_num']);
      }
      $fileName = time() . "_excel.xls";
      $objPHPExcel->getActiveSheet()->setTitle('群发统计');
      $objPHPExcel->setActiveSheetIndex(0);
      // Redirect output to a client’s web browser (Excel5)
      header('Content-Type: application/vnd.ms-excel;charset=utf-8');
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
      // print_r($data);exit;
      $objWriter->save('php://output');
      exit;
    }
  }

  public function _export_refresh_count()
  {
    $data_view = array();
    $broker_info = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);
    $where = $broker_info['company_id'] > 0 ? 'company_id = ' . $broker_info['company_id'] : 'company_id = 0';
    //开始时间
    if ($post_param['start_date_begin']) {
      $where .= ' and YMD >= "' . $post_param['start_date_begin'] . '"';
    }
    //截止时间
    if ($post_param['start_date_end']) {
      $where .= ' and YMD <= "' . $post_param['start_date_end'] . '"';
    }
    //门店
    if (!isset($post_param['agency_id'])) {
      $post_param['agency_id'] = $broker_info['agency_id'];
    }
    if ($post_param['agency_id']) {
      $where .= ' and agency_id = ' . $post_param['agency_id'];
    }
    //print_r($broker);
    //经纪人
    if (!isset($post_param['broker_id'])) {
      $post_param['broker_id'] = $broker_info['broker_id'];
    }
    if ($post_param['broker_id']) {
      $where .= ' and broker_id = ' . $post_param['broker_id'];
    }
    $where .= ' group by broker_id';
    $count_num = 'wuba_num+wuba_vip_num+ganji_num+ganji_vip_num+anjuke_num+fang_num+taofang_num';
    $select_fields = array('*', 'sum(' . $count_num . ') as sum_num', 'sum(wuba_num) as wuba_num', 'sum(wuba_vip_num) as wuba_vip_num', 'sum(ganji_num) as ganji_num', 'sum(ganji_vip_num) as ganji_vip_num', 'sum(anjuke_num) as anjuke_num', 'sum(fang_num) as fang_num', 'sum(taofang_num) as taofang_num');
    $this->group_refresh_model->set_select_fields($select_fields);
    $count_num_info = $this->group_refresh_model->get_all_by($where, -1, 20, 'sum_num');

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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '排名');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '用户名');
    //$objPHPExcel->getActiveSheet()->setCellValue('C1', '58同城');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '58网邻通');
    //$objPHPExcel->getActiveSheet()->setCellValue('E1', '赶集网');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '赶集VIP');
    //$objPHPExcel->getActiveSheet()->setCellValue('G1', '安居客');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '365淘房');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '房天下');

    if (is_full_array($count_num_info)) {
      foreach ($count_num_info as $key => $value) {
        $brokers = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);
        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($key + 2), $key + 1);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . ($key + 2), $brokers['truename']);
        //$objPHPExcel->getActiveSheet()->setCellValue('C'.($key+2),$value['wuba_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('c' . ($key + 2), $value['wuba_vip_num']);
        //$objPHPExcel->getActiveSheet()->setCellValue('E'.($key+2),$value['ganji_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . ($key + 2), $value['ganji_vip_num']);
        //$objPHPExcel->getActiveSheet()->setCellValue('G'.($key+2),$value['anjuke_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . ($key + 2), $value['taofang_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . ($key + 2), $value['fang_num']);
      }
      $fileName = time() . "_excel.xls";
      $objPHPExcel->getActiveSheet()->setTitle('群发统计');
      $objPHPExcel->setActiveSheetIndex(0);
      // Redirect output to a client’s web browser (Excel5)
      header('Content-Type: application/vnd.ms-excel;charset=utf-8');
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
      // print_r($data);exit;
      $objWriter->save('php://output');
      exit;
    }
  }


  //重新排列二维数组
  public function multi_array_sort($multi_array, $sort_key, $sort = SORT_DESC)
  {
    if (is_full_array($multi_array)) {
      foreach ($multi_array as $row_array) {
        if (is_full_array($row_array)) {
          $key_array[] = $row_array[$sort_key];
        } else {
          return false;
        }
      }
    } else {
      return false;
    }
    array_multisort($key_array, $sort, $multi_array);
    return $multi_array;
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

  /**
   * 通过key值转成excel表横坐标值
   */
  private function key_value($key, $num)
  {
    switch ($key) {
      case 0:
        $x = 'C' . $num;
        break;
      case 1:
        $x = 'D' . $num;
        break;
      case 2:
        $x = 'E' . $num;
        break;
      case 3:
        $x = 'F' . $num;
        break;
      case 4:
        $x = 'G' . $num;
        break;
      case 5:
        $x = 'H' . $num;
        break;
      case 6:
        $x = 'I' . $num;
        break;
      case 7:
        $x = 'J' . $num;
        break;
      case 8:
        $x = 'K' . $num;
        break;
      case 9:
        $x = 'L' . $num;
        break;
      case 10:
        $x = 'M' . $num;
        break;
      case 11:
        $x = 'N' . $num;
        break;
      case 12:
        $x = 'O' . $num;
        break;
      case 13:
        $x = 'P' . $num;
        break;
      case 14:
        $x = 'Q' . $num;
        break;
      case 15:
        $x = 'R' . $num;
        break;
      case 16:
        $x = 'S' . $num;
        break;
      case 17:
        $x = 'T' . $num;
        break;
      case 18:
        $x = 'U' . $num;
        break;
      case 19:
        $x = 'V' . $num;
        break;
      case 20:
        $x = 'W' . $num;
        break;
      case 21:
        $x = 'X' . $num;
        break;
      case 22:
        $x = 'Y' . $num;
        break;
      case 23:
        $x = 'Z' . $num;
        break;
      default:
        '数据超出';
        exit;
    }
    return $x;
  }
}
/* End of file count_info.php */
/* Location: ./applications/mls/controllers/count_info.php */
