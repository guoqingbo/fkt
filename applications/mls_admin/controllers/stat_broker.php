<?php

/**
 * 经纪人数据统计查询
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      wang
 */
class Stat_broker extends MY_Controller
{
  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
    protected $_city = 'hz';


  /**
   * 录入经纪人id
   *
   * @access private
   * @var int
   */
  private $_broker_id = 0;

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
  private $_limit = 15;

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
    $this->load->model('stat_broker_model');
  }

  //经纪人统计表的页面
  public function index()
  {
    $data_view = array();
    $this->load->helper('page_helper');
    $pg = $this->input->post('pg');

    $nowtime = time();
    $search_where = $this->input->post('search_where');
    $search_value = $this->input->post('search_value');

    if ($search_where == 'company' && $search_value != '') {
      $this->load->model('agency_model');
      $data_view['agencys'] = $this->agency_model->get_agency_by_companyname($search_value);
    }

    $where = 'id > 0';
    if ($search_where && $search_value) {
      $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
    }
    //条件
    $data_view['where_cond'] = array(
      'search_where' => $search_where, 'search_value' => $search_value
    );
    //引入经纪人基本类库
    $this->load->model('broker_info_model');

    //设置时间条件
    $start_time = $this->input->post('start_time');
    $end_time = $this->input->post('end_time');
    $data_view['start_time'] = $start_time = $start_time != '' ? $start_time : date('Y-m-d', strtotime('-1 day'));
    $data_view['end_time'] = $end_time = $end_time != '' ? $end_time : date('Y-m-d', strtotime('-1 day'));
    if ($start_time) {
      $where .= ' and ymd >= "' . $start_time . '"';
    }
    if ($end_time) {
      $where .= ' and ymd <= "' . $end_time . '"';
    }

    $agency_id = $this->input->post('agency_id');
    if ($agency_id > 0) {
      $where .= ' and agency_id = "' . $agency_id . '"';
      $data_view['agency_id'] = $agency_id;
    }

    //查询这个城市的客户经理数据
    $this->load->model('user_model');
    $masters = $this->user_model->get_user_by_cityid($_SESSION[WEB_AUTH]["city_id"]);
    $data_view['masters'] = $masters;

    //判断当前经纪人是否为客户经理
    $this_user_id = intval($_SESSION[WEB_AUTH]['uid']);
    $data_view['this_user_id'] = $this_user_id;
    $data_view['this_user_name'] = $_SESSION[WEB_AUTH]['truename'];
    if ($this_user_id > 0) {
      $this_user_data = $this->user_model->getuserByid($this_user_id);
      if (is_full_array($this_user_data[0])) {
        $am_cityid = intval($this_user_data[0]['am_cityid']);
      }
    }
    if (isset($am_cityid) && $am_cityid > 0) {
      $data_view['is_user_manager'] = true;
      $where .= ' and master = ' . $this_user_id;
    } else {
      $data_view['is_user_manager'] = false;
      //客户经理
      $data_view['master_id'] = $master_id = $this->input->post('master_id', true);
      if ($master_id) {
        $where .= ' and master = "' . $master_id . '"';
      }
    }

    //分页开始
    $data_view['pagesize'] = 20; //设定每一页显示的记录数
    $data_view['count'] = $this->stat_broker_model->count_data_by_cond($where);
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //经纪人列表
    $stat_broker_data = $this->stat_broker_model->get_data_by_cond($where, $data_view['offset'], $data_view['pagesize']);

    //搜索配置信息
    $data_view['stat_broker_data'] = $stat_broker_data;
    $this->load->view('stat/stat_broker', $data_view);
  }

  //刷入数据：经纪人号码、出售等级2房源、出租等级2房源
  public function add_data($start = 0, $limit = 0)
  {
    ini_set("memory_limit", "80M");
    $this->load->model('broker_info_model');
    $this->load->model('sell_house_model');
    $this->load->model('rent_house_model');
    $all_data = $this->stat_broker_model->get_data_by_cond(array(), intval($start), intval($limit));
    if (is_full_array($all_data)) {
      foreach ($all_data as $k => $v) {
        $id = $v['id'];
        //查找号码
        $broker_id = $v['broker_id'];
        $where_cond = array(
          'broker_id' => intval($broker_id)
        );
        $broker_info = $this->broker_info_model->get_one_by($where_cond);
        $broker_phone = 0;
        if (is_full_array($broker_info)) {
          $broker_phone = $broker_info['phone'];
        }

        //查找出售等级2房源量
        $where_cond_level = array(
          'broker_id' => intval($broker_id),
          'house_level' => 2,
          'is_outside' => 1
        );
        $sell_level2 = $this->sell_house_model->get_sell_house_num_by_cond($where_cond_level);
        $rent_level2 = $this->rent_house_model->get_rent_house_num_by_cond($where_cond_level);
        $update_arr = array(
          'phone' => $broker_phone,
          'sell_level_2_num' => $sell_level2,
          'rent_level_2_num' => $rent_level2
        );
        $update_where = array(
          'id' => $id
        );
        $update_result = $this->stat_broker_model->update_data($update_arr, $update_where);
        echo '经纪人id:' . $broker_id . ',更新结果：' . $update_result;
        echo '--<br>';
      }
    }
  }


  /**
   * 查询经纪人列表条件
   * 根据表单提交参数，获取查询条件
   *
   * private function _get_cond_str( $form_param )
   * {
   *
   * //查询条件  公司名或经纪人名
   * if(isset($form_param['search_where']) && isset($form_param['search_value']))
   * {
   * $search_where = intval($form_param['search_where']);
   * $search_value = intval($form_param['search_value']);
   * $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
   * }
   * //开始时间  结束时间
   * if(isset($form_param['start_time'])){
   * $start_time = $form_param['start_time'];
   * $where .= ' and ymd >= "'.$start_time.'"';
   * }
   * if(isset($form_param['end_time'])){
   * $start_time = $form_param['end_time'];
   * $where .= ' and ymd >= "'.$end_time.'"';
   * }
   * return $where;
   * }*/
  /**
   * 导出经纪人统计报表
   * @author   wang
   */
  public function exportReport($search_where = 0, $search_value = 0, $start_time = 0, $end_time = 0)
  {

    ini_set('memory_limit', '-1');
    //表单提交参数组成的查询条件
    $search_where = $this->input->get('search_where', TRUE);
    $search_value = $this->input->get('search_value', TRUE);
    //设置时间条件
    $start_time = $this->input->get('start_time', TRUE);
    $end_time = $this->input->get('end_time', TRUE);
    $start_time = $start_time != '' ? $start_time : date('Y-m-d', strtotime('-1 day'));
    $end_time = $end_time != '' ? $end_time : date('Y-m-d', strtotime('-1 day'));

    $where = 'id > 0';
    if ($search_where && $search_value) {
      $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
    }

    if ($start_time) {
      $where .= ' and ymd >= "' . $start_time . '"';
    }
    if ($end_time) {
      $where .= ' and ymd <= "' . $end_time . '"';
    }

    $master_id = $this->input->get('master_id', true);
    if ($master_id > 0) {
      $where .= ' and master = "' . $master_id . '"';
    }

    $limit = $this->stat_broker_model->count_data_by_cond($where);
    $brokerlist = $this->stat_broker_model->get_data_by_cond($where, 0, $limit);

    $total = $this->input->get('total', TRUE);
    $list = array();

    //查询这个城市的客户经理数据
    $this->load->model('user_model');
    $masters = $this->user_model->get_user_by_cityid($_SESSION[WEB_AUTH]["city_id"]);
    $data_view['masters'] = $masters;

    //引入经纪人基本类库
    $this->load->model('broker_info_model');
    $this->broker_info_model->set_select_fields(array('broker_id', 'phone'));
    $brokerarr = $this->broker_info_model->get_all_by(array('id >' => 0), 0, 100000);
    $brokerinfo = array();
    if (is_full_array($brokerarr)) {
      foreach ($brokerarr as $value) {
        $brokerinfo[$value['broker_id']] = $value['phone'];
      }
    }

    if ($total == 1 && is_full_array($brokerlist)) {
      $this->load->model('broker_login_log_model');
      $nowtime = time();//$temp=0;

      foreach ($brokerlist as $broker) {
        $list[$broker['broker_id']]['broker_id'] = $broker['broker_id'];
        $list[$broker['broker_id']]['truename'] = $broker['truename'];//$temp++;
        //echo $broker['broker_id'].'-'.$brokerinfo[$broker['broker_id']];echo '<br />';
        $list[$broker['broker_id']]['phone'] = $brokerinfo[$broker['broker_id']];//if($temp >= 50) exit;
        $list[$broker['broker_id']]['company'] = $broker['company'];
        $list[$broker['broker_id']]['agency'] = $broker['agency'];
        $list[$broker['broker_id']]['login_num'] = isset($list[$broker['broker_id']]['login_num']) ? $list[$broker['broker_id']]['login_num'] + $broker['login_num'] : $broker['login_num'];
        $list[$broker['broker_id']]['sell_publish_num'] = isset($list[$broker['broker_id']]['sell_publish_num']) ? $list[$broker['broker_id']]['sell_publish_num'] + $broker['sell_publish_num'] : $broker['sell_publish_num'];
        $list[$broker['broker_id']]['rent_publish_num'] = isset($list[$broker['broker_id']]['rent_publish_num']) ? $list[$broker['broker_id']]['rent_publish_num'] + $broker['rent_publish_num'] : $broker['rent_publish_num'];
        $list[$broker['broker_id']]['sell_collect_view_num'] = isset($list[$broker['broker_id']]['sell_collect_view_num']) ? $list[$broker['broker_id']]['sell_collect_view_num'] + $broker['sell_collect_view_num'] : $broker['sell_collect_view_num'];
        $list[$broker['broker_id']]['rent_collect_view_num'] = isset($list[$broker['broker_id']]['rent_collect_view_num']) ? $list[$broker['broker_id']]['rent_collect_view_num'] + $broker['rent_collect_view_num'] : $broker['rent_collect_view_num'];
        $list[$broker['broker_id']]['sell_group_publish_num'] = isset($list[$broker['broker_id']]['sell_group_publish_num']) ? $list[$broker['broker_id']]['sell_group_publish_num'] + $broker['sell_group_publish_num'] : $broker['sell_group_publish_num'];
        $list[$broker['broker_id']]['rent_group_publish_num'] = isset($list[$broker['broker_id']]['rent_group_publish_num']) ? $list[$broker['broker_id']]['rent_group_publish_num'] + $broker['rent_group_publish_num'] : $broker['rent_group_publish_num'];
        $list[$broker['broker_id']]['sell_outside_num'] = $broker['sell_outside_num'];
        $list[$broker['broker_id']]['rent_outside_num'] = $broker['rent_outside_num'];
        $list[$broker['broker_id']]['sell_level_2_num'] = $broker['sell_level_2_num'];
        $list[$broker['broker_id']]['rent_level_2_num'] = $broker['rent_level_2_num'];
        $list[$broker['broker_id']]['sell_level_3_num'] = $broker['sell_level_3_num'];
        $list[$broker['broker_id']]['rent_level_3_num'] = $broker['rent_level_3_num'];
        $list[$broker['broker_id']]['sell_num'] = $broker['sell_num'];
        $list[$broker['broker_id']]['rent_num'] = $broker['rent_num'];
        $list[$broker['broker_id']]['sell_cooperate_num'] = $broker['sell_cooperate_num'];
        $list[$broker['broker_id']]['rent_cooperate_num'] = $broker['rent_cooperate_num'];
        $list[$broker['broker_id']]['app_access_num'] = isset($list[$broker['broker_id']]['app_access_num']) ? $list[$broker['broker_id']]['app_access_num'] + $broker['app_access_num'] : $broker['app_access_num'];
        $list[$broker['broker_id']]['sell_video_num'] = $broker['sell_video_num'];
        $list[$broker['broker_id']]['rent_video_num'] = $broker['rent_video_num'];

        $list[$broker['broker_id']]['ymd'] = $start_time . '至' . $end_time;
        $list[$broker['broker_id']]['master'] = $data_view['masters'][$broker['master']]['truename'];

        $last_login = $this->broker_login_log_model->get_last_log($broker['phone']);
        $list[$broker['broker_id']]['last_login'] = $last_login[0]['dateline'] > 0 ? date('Y-m-d H:i:s', $last_login[0]['dateline']) : '尚未登录';
        $list[$broker['broker_id']]['no_login_days'] = $last_login[0]['dateline'] > 0 ? floor(($nowtime - $last_login[0]['dateline']) / 86400) : '尚未登录';
        $list[$broker['broker_id']]['infofrom'] = '';
        if ($last_login[0]['dateline'] > 0) {
          $list[$broker['broker_id']]['infofrom'] = $last_login[0]['infofrom'] == 1 ? 'PC' : 'APP';
        }
        $list[$broker['broker_id']]['dist'] = $broker['dist'];
      }

      $list = array_values($list);
    } else {
      foreach ($brokerlist as $key => $broker) {
        $list[$key]['broker_id'] = $broker['broker_id'];
        $list[$key]['truename'] = $broker['truename'];
        $list[$key]['phone'] = $brokerinfo[$broker['broker_id']];
        $list[$key]['company'] = $broker['company'];
        $list[$key]['agency'] = $broker['agency'];
        $list[$key]['login_num'] = $broker['login_num'];
        $list[$key]['sell_publish_num'] = $broker['sell_publish_num'];
        $list[$key]['rent_publish_num'] = $broker['rent_publish_num'];
        $list[$key]['sell_collect_view_num'] = $broker['sell_collect_view_num'];
        $list[$key]['rent_collect_view_num'] = $broker['rent_collect_view_num'];
        $list[$key]['sell_group_publish_num'] = $broker['sell_group_publish_num'];
        $list[$key]['rent_group_publish_num'] = $broker['rent_group_publish_num'];
        $list[$key]['sell_outside_num'] = $broker['sell_outside_num'];
        $list[$key]['rent_outside_num'] = $broker['rent_outside_num'];
        $list[$key]['sell_level_2_num'] = $broker['sell_level_2_num'];
        $list[$key]['rent_level_2_num'] = $broker['rent_level_2_num'];
        $list[$key]['sell_level_3_num'] = $broker['sell_level_3_num'];
        $list[$key]['rent_level_3_num'] = $broker['rent_level_3_num'];
        $list[$key]['sell_num'] = $broker['sell_num'];
        $list[$key]['rent_num'] = $broker['rent_num'];
        $list[$key]['sell_cooperate_num'] = $broker['sell_cooperate_num'];
        $list[$key]['rent_cooperate_num'] = $broker['rent_cooperate_num'];
        $list[$key]['app_access_num'] = $broker['app_access_num'];
        $list[$key]['sell_video_num'] = $broker['sell_video_num'];
        $list[$key]['rent_video_num'] = $broker['rent_video_num'];
        $list[$key]['ymd'] = $broker['ymd'];
        $list[$key]['master'] = $data_view['masters'][$broker['master']]['truename'];
        $list[$key]['dist'] = $broker['dist'];
      }

      $list = array_values($list);
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '经纪人ID');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '经纪人姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '经纪人电话');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '公司名');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '门店名');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '登录次数');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'ERP出售新增量');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'ERP出租新增量');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '出售采集查看量');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '出租采集查看量');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '出售群发新增量');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '出租群发新增量');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '外网出售总量');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', '外网出租总量');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', '外网出售有图房源总量');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', '外网出租有图房源总量');
    $objPHPExcel->getActiveSheet()->setCellValue('Q1', '外网出售多图房源总量');
    $objPHPExcel->getActiveSheet()->setCellValue('R1', '外网出租多图房源总量');
    $objPHPExcel->getActiveSheet()->setCellValue('S1', 'ERP出售总量');
    $objPHPExcel->getActiveSheet()->setCellValue('T1', 'ERP出租总量');
    $objPHPExcel->getActiveSheet()->setCellValue('U1', '合作出售房源总量');
    $objPHPExcel->getActiveSheet()->setCellValue('V1', '合作出租房源总量');
    $objPHPExcel->getActiveSheet()->setCellValue('W1', 'APP使用量');
    $objPHPExcel->getActiveSheet()->setCellValue('X1', '出售视频房源总量');
    $objPHPExcel->getActiveSheet()->setCellValue('Y1', '出租视频房源总量');
    $objPHPExcel->getActiveSheet()->setCellValue('Z1', '统计时间');
    $objPHPExcel->getActiveSheet()->setCellValue('AA1', '客户经理');
    if ($total == 1) {
      $objPHPExcel->getActiveSheet()->setCellValue('AB1', '最后登录时间');
      $objPHPExcel->getActiveSheet()->setCellValue('AC1', '距今未登录天数');
      $objPHPExcel->getActiveSheet()->setCellValue('AD1', '最后登录来源');
      $objPHPExcel->getActiveSheet()->setCellValue('AE1', '区属');
    } else {
      $objPHPExcel->getActiveSheet()->setCellValue('AB1', '区属');
    }


    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['broker_id']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['truename']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['phone']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['company']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['agency']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['login_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['sell_publish_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 2]['rent_publish_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 2]['sell_collect_view_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $list[$i - 2]['rent_collect_view_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $list[$i - 2]['sell_group_publish_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $list[$i - 2]['rent_group_publish_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $list[$i - 2]['sell_outside_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $list[$i - 2]['rent_outside_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $list[$i - 2]['sell_level_2_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('P' . $i, $list[$i - 2]['rent_level_2_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('Q' . $i, $list[$i - 2]['sell_level_3_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('R' . $i, $list[$i - 2]['rent_level_3_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('S' . $i, $list[$i - 2]['sell_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('T' . $i, $list[$i - 2]['rent_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('U' . $i, $list[$i - 2]['sell_cooperate_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('V' . $i, $list[$i - 2]['rent_cooperate_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('W' . $i, $list[$i - 2]['app_access_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('X' . $i, $list[$i - 2]['sell_video_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('Y' . $i, $list[$i - 2]['rent_video_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('Z' . $i, $list[$i - 2]['ymd']);
      $objPHPExcel->getActiveSheet()->setCellValue('AA' . $i, $list[$i - 2]['master']);

      if ($total == 1) {
        $objPHPExcel->getActiveSheet()->setCellValue('AB' . $i, $list[$i - 2]['last_login']);
        $objPHPExcel->getActiveSheet()->setCellValue('AC' . $i, $list[$i - 2]['no_login_days']);
        $objPHPExcel->getActiveSheet()->setCellValue('AD' . $i, $list[$i - 2]['infofrom']);
        $objPHPExcel->getActiveSheet()->setCellValue('AE' . $i, $list[$i - 2]['dist']);
      } else {
        $objPHPExcel->getActiveSheet()->setCellValue('AB' . $i, $list[$i - 2]['dist']);
      }

    }

    $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('stat_broker_nums');
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


?>
