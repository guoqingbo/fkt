<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 用户详细信息类
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Stat_app_count extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('stat_app_count_model');
  }

  //门店管理页
  public function index()
  {
    $data_view = array();
    $this->load->helper('page_helper');
    $pg = $this->input->post('pg');
    $data_view['title'] = 'APP安装量统计';
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
    $data_view['count'] = $this->stat_app_count_model->count_by($where);
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //访问量列表
    $dataarr = $this->stat_app_count_model->get_all_by($where, $data_view['offset'], $data_view['pagesize']);

    if (is_full_array($dataarr)) {
      $this->load->model('broker_info_model');
      $this->load->model('agency_model');

      foreach ($dataarr as $value) {
        $temp = array();
        $this->broker_info_model->set_select_fields(array('truename', 'company_id', 'agency_id'));
        $broker_info = $this->broker_info_model->get_by_broker_id($value['broker_id']);

        //获取经纪人所属公司名称
        $company_one = $broker_info['company_id'] > 0 ? $this->agency_model->get_by_id($broker_info['company_id']) : array();
        $company_name = is_full_array($company_one) ? $company_one['name'] : '';

        //获取经纪人所属门店名称
        $agency_one = $broker_info['agency_id'] > 0 ? $this->agency_model->get_by_id($broker_info['agency_id']) : array();
        $agency_name = is_full_array($agency_one) ? $agency_one['name'] : '';

        $temp['broker_name'] = $broker_info['truename'];
        $temp['company_name'] = $company_name;
        $temp['agency_name'] = $agency_name;
        $temp['devicetype'] = $value['devicetype'] == 1 ? 'IPHONE' : 'ANDROID';
        $temp['dateline'] = $value['dateline'];
        $temp['deviceid'] = $value['deviceid'];

        $data_view['dataarr'][] = $temp;
      }
    }

    $this->load->view('stat/stat_app_count', $data_view);
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

  //门店管理页
  public function export()
  {
    $data_view = array();
    $this->load->helper('page_helper');
    $pg = $this->input->post('pg');
    $data_view['title'] = 'APP安装量统计';
    $data_view['conf_where'] = 'index';
    $nowtime = time();

    $where = 'city = ' . $_SESSION['esfdatacenter']['city_id'];
    //设置时间条件
    $start_time = $this->input->post('start_time');
    $end_time = $this->input->post('end_time');

    if ($start_time && $end_time) {
      $where .= ' and ymd >= "' . $start_time . '" and ymd <= "' . $end_time . '"';
    }
    //访问量列表
    $lists = $this->stat_app_count_model->get_all_by($where, -1);

    $this->load->model('broker_info_model');
    $this->load->model('agency_model');
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '经纪人姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '手机号码');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '所属公司');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '所属门店');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '设备类型');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '设备号');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '日期');
    //设置表格的值
    for ($i = 2; $i <= count($lists) + 1; $i++) {

      $this->broker_info_model->set_select_fields(array('truename', 'phone', 'company_id', 'agency_id'));
      $broker_info = $this->broker_info_model->get_by_broker_id($lists[$i - 2]['broker_id']);

      //获取经纪人所属公司名称
      $company_one = $broker_info['company_id'] > 0 ? $this->agency_model->get_by_id($broker_info['company_id']) : array();
      $company_name = is_full_array($company_one) ? $company_one['name'] : '';

      //获取经纪人所属门店名称
      $agency_one = $broker_info['agency_id'] > 0 ? $this->agency_model->get_by_id($broker_info['agency_id']) : array();
      $agency_name = is_full_array($agency_one) ? $agency_one['name'] : '';

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $broker_info['truename']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $broker_info['phone']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $company_name);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $agency_name);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $lists[$i - 2]['devicetype'] == 1 ? 'IPHONE' : 'ANDROID');
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $lists[$i - 2]['deviceid']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, date('Y-m-d H:i:s', $lists[$i - 2]['dateline']));
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
