<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 用户详细信息类
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Group_publish_log extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  //门店管理页
  public function index()
  {
    $data_view = array();
    $this->load->helper('page_helper');
    $pg = $this->input->post('pg');
    $data_view['title'] = '经纪人群发量';
    $data_view['conf_where'] = 'index';
    //引入经纪人基本类库
    $this->load->model('broker_info_model');
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
    $data_view['count'] = 10;
    $data_view['pagesize'] = 10; //设定每一页显示的记录数
    $data_view['count'] = $this->broker_info_model->count_broker_group_publish_log($where);
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //经纪人列表
    $broker_group_publish_log = $this->broker_info_model->get_broker_group_publish_log($where, $data_view['offset'], $data_view['pagesize']);
    //搜索配置信息
    $data_view['broker_group_publish_log'] = $broker_group_publish_log;

    $this->load->view('stat/broker_group_publish_log', $data_view);
  }

  /**
   * 经纪人群发日志
   * 表 : group_publish_log
   */
  public function group_publish()
  {
    //引入经纪人群发日志
    $this->load->model('group_publish_model');
    $this->load->model('broker_info_model');
    $this->load->model('mass_site_model');
    $this->load->helper('page_helper');

    //筛选条件
    $data['where_cond'] = '';
    $sell_type = intval($this->input->post('sell_type'));
    $type = intval($this->input->post('type'));
    $phone = trim($this->input->post('phone'));
    $house = trim($this->input->post('house'));
    $block_name = trim($this->input->post('block_name'));
    $house_id = trim($this->input->post('house_id'));
    $block_id = trim($this->input->post('block_id'));
    $house = $house ? $house : $block_name;
    $house_id = $block_id ? $block_id : $house_id;
    $data['success'] = '';
    if (!empty($phone)) {
      $broker = $this->group_publish_model->get_broker_id($phone);
      $data['where_cond']['broker_id'] = $broker[0]['broker_id'];
      $data['phone'] = $phone;
      $data['success'] = '该经纪人';
    }
    $data['success'] = $data['success'] . '群发';
    if (!empty($sell_type)) {
      $data['where_cond']['sell_type'] = $sell_type;
      $data['sell_type'] = $sell_type;
      if ($sell_type == 1) {
        $data['success'] = $data['success'] . '出售';
      } else {
        $data['success'] = $data['success'] . '出租';
      }
    }
    if (!empty($sell_type) && !empty($house_id) && !empty($house)) {
      $data['where_cond']['block_id'] = $house_id;
      $data['block_name'] = $house;
      $data['house_id'] = $house_id;
      $data['success'] = $data['success'] . '该房源';
    }
    date_default_timezone_set('PRC');
    if ($this->input->post('start_time') && $this->input->post('end_time')) {
      $start_time = strtotime($this->input->post('start_time') . " 00:00");
      $end_time = strtotime($this->input->post('end_time') . " 23:59");
      if ($start_time > $end_time) {
        echo "<script>alert('您查询的开始时间不能大于结束时间！');location.href='" . MLS_ADMIN_URL . "/group_publish_log/group_publish';</script>";
      }
      if ($start_time && $end_time) {
        $data['where_cond']['ymd >='] = $start_time;
        $data['where_cond']['ymd <='] = $end_time;
        $data['success'] = $data['success'] . '在此时间段';
      }
    }
    $where = $data['where_cond'];
    $publish_num = $this->group_publish_model->count_get_group_publish($where);//总数
    $where['type'] = 1;
    $success_num = $this->group_publish_model->count_get_group_publish($where);//成功数
    if ($publish_num != 0) {
      $success_rate = $success_num / $publish_num * 100;//计算成功率
    } else {
      $success_rate = 0;
    }
    $data['success_rate'] = round($success_rate, 2) . '%';

    if (!empty($type)) {
      $data['where_cond']['type'] = $type;
      $data['type'] = $type;
    }
    $data['success'] = $data['success'] . '成功率：';
    //分页开始
    $data['publish_num'] = $this->group_publish_model->count_get_group_publish($data['where_cond']);
    $data['count'] = 10;
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['publish_num'] ? ceil($data['publish_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    $group_publish_log = $this->group_publish_model->get_group_publish($data['where_cond'], $data['offset'], $data['pagesize']);
    foreach ($group_publish_log as $key => $value) {
      //经纪人信息
      if (!empty($phone)) {
        $group_publish_log[$key]['phone'] = $broker[0]['phone'];
        $group_publish_log[$key]['name'] = $broker[0]['truename'];
      } else {
        $broker_info = $this->broker_info_model->get_by_broker_id($value['broker_id']);
        $group_publish_log[$key]['phone'] = $broker_info['phone'];
        $group_publish_log[$key]['name'] = $broker_info['truename'];
      }
      //房源信息
      $house_info = $this->group_publish_model->get_house_info_byids($value['house_id'], $value['sell_type']);
      $group_publish_log[$key]['block_name'] = $house_info[0]['block_name'];
      //群发目标网站
      $mass_site = $this->mass_site_model->getinfo_byid($value['site_id']);
      $group_publish_log[$key]['site_name'] = $mass_site[0]['name'];

      $group_publish_log[$key]['info'] = strip_tags($value['info']);
    }
    //print_r($group_publish_log);exit;
    $data['group_publish_log'] = $group_publish_log;
    $data['title'] = '经纪人群发日志';
    $this->load->view('stat/group_publish_log', $data);
  }

  /**
   * 群发房源联想
   * rent_house    sell_house
   */
  function group_publish_ajax()
  {
    $keyword = $this->input->get('keyword', TRUE);
    $this->load->model('group_publish_model');
    $this->load->model('district_model');
    $cmt_info = $this->group_publish_model->get_cmtinfo_by_kw($keyword);
    //print_r($cmt_info);
    foreach ($cmt_info as $key => $value) {
      $districtname = $this->district_model->get_district_by_id($value['dist_id']);
      $cmt_info[$key]['districtname'] = $districtname[0]['district'];
    }
    if (empty($cmt_info)) {
      $cmt_info[0]['cmt_name'] = '暂无信息';
    }
    echo json_encode($cmt_info);
  }

  //导出
  public function export()
  {
    $data_view = array();
    $this->load->helper('page_helper');
    $pg = $this->input->post('pg');
    $data_view['title'] = '经纪人群发日志';
    $data_view['conf_where'] = 'index';
    //引入经纪人基本类库
    $this->load->model('broker_info_model');
    $start_time = $this->input->post('start_time');
    $end_time = $this->input->post('end_time');

    //经纪人列表
    $list = $this->broker_info_model->get_all_group_log($start_time, $end_time);

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
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '公司');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '门店');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '经纪人');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '类型');
    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {
      $type = $list[$i - 2]['sell_type'] == 1 ? '出售' : '出租';
      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['ymd']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['acname']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['agname']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['truename']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $type);
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

/* End of file Broker_group_publish_log.php */
/* Location: ./application/mls_admin/controllers/Broker_group_publish_log.php */
