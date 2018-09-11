<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 用户详细信息类
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Broker_login_log extends MY_Controller
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
    $data_view['title'] = '经纪人登录日志';
    $data_view['conf_where'] = 'index';
    $nowtime = time();
    //设置查询条件
    $search_phone = $this->input->post('search_phone');
    //引入经纪人基本类库
    $this->load->model('broker_info_model');
    $where = 'id > 0';
    //设置时间条件
    $start_time = $this->input->post('start_time');
    $end_time = $this->input->post('end_time');
    if ($search_phone) {
      $where .= ' and phone = ' . $search_phone;
    }
    if ($start_time && $end_time) {
      $start_time_format = strtotime($start_time . ' 00:00:00');
      $end_time_format = strtotime($end_time . ' 23:59:59');
      $where .= ' and dateline >= ' . $start_time_format . ' and dateline <= ' . $end_time_format;
    }

    //记录搜索过的条件
    $data_view['where_cond'] = array(
      'search_phone' => $search_phone, 'start_time' => $start_time, 'end_time' => $end_time
    );
    //分页开始
    $data_view['count'] = 10;
    $data_view['pagesize'] = 10; //设定每一页显示的记录数
    $data_view['count'] = $this->broker_info_model->count_broker_login_log($where);
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //经纪人列表
    $broker_login_log = $this->broker_info_model->get_broker_login_log($where, $data_view['offset'], $data_view['pagesize']);
    //搜索配置信息
    $data_view['broker_login_log'] = $broker_login_log;
    $this->load->view('broker_login_log/index', $data_view);
  }

  //导出表格
  public function exportReport()
  {
    $data_view = array();
    //设置查询条件
    $search_phone = $this->input->get('search_phone');
    //引入经纪人基本类库
    $this->load->model('broker_info_model');
    $where = 'id > 0';
    //设置时间条件
    $start_time = $this->input->get('start_time');
    $end_time = $this->input->get('end_time');
    if ($search_phone) {
      $where .= ' and phone = ' . $search_phone;
    }
    if ($start_time && $end_time) {
      $start_time_format = strtotime($start_time . ' 00:00:00');
      $end_time_format = strtotime($end_time . ' 23:59:59');
      $where .= ' and dateline >= ' . $start_time_format . ' and dateline <= ' . $end_time_format;
    }

    //记录搜索过的条件
    $data_view['where_cond'] = array(
      'search_phone' => $search_phone, 'start_time' => $start_time, 'end_time' => $end_time
    );
    //经纪人列表
    $broker_login_log = $this->broker_info_model->get_broker_login_log($where, 0, 0);
    if (is_full_array($broker_login_log)) {
      foreach ($broker_login_log as $key => $value) {
        $broker_login_log[$key]['dateline'] = date('Y-m-d H:i:s', $value['dateline']);
      }
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '序号');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '登录帐号');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '登录设备');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '登录IP');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '登录时间');
    //设置表格的值
    for ($i = 2; $i <= count($broker_login_log) + 1; $i++) {
      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $broker_login_log[$i - 2]['id']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $broker_login_log[$i - 2]['phone']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $broker_login_log[$i - 2]['deviceid']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $broker_login_log[$i - 2]['ip']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $broker_login_log[$i - 2]['dateline']);
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

/* End of file Broker_info.php */
/* Location: ./application/mls_admin/controllers/Broker_info.php */
