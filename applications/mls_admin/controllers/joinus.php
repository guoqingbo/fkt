<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class joinus extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->model('cooperate_model');//合作模型类
    $this->load->model('joinus_model');//合作模型类
    $this->load->library('form_validation');//表单验证
  }


  //加盟客户列表页
  public function index($status = 0)
  {
    //筛选条件
    $data['param_array'] = $this->input->post(NULL, TRUE);

    $data['title'] = '加盟客户列表页';

    $data['status_type'] = $status;
    $cond_where = '';
    //$cond_where = $this->_get_cond_str($data['param_array']);
    //分页开始
    $data['num'] = $this->joinus_model->get_joinus_num($cond_where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['num'] ? ceil($data['num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $list = $this->joinus_model->get_joinus_list($cond_where, $data['offset'], $data['pagesize']);
    foreach ($list as $key => $vo) {
      $province = $this->joinus_model->get_province_city_by_id($vo['province']);
      $city = $this->joinus_model->get_province_city_by_id($vo['city']);
      $list[$key]['province'] = $province['name'];
      $list[$key]['city'] = $city['name'];
    }
    $data['list'] = $list;
    $this->load->view('joinus/index', $data);
  }

  //加盟客户列表页
  public function detail($id)
  {
    //筛选条件
    $data['title'] = '加盟客户详情页';
    $list = $this->joinus_model->get_joinus_by_id($id);
    $province = $this->joinus_model->get_province_city_by_id($list['province']);
    $city = $this->joinus_model->get_province_city_by_id($list['city']);
    $list['province'] = $province['name'];
    $list['city'] = $city['name'];
    $data['list'] = $list;
    $this->load->view('joinus/detail', $data);
  }

  /**
   * 导出表数据
   * @author   wang
   */
  public function exportReport()
  {

    ini_set('memory_limit', '-1');
    //筛选条件
    $data['param_array'] = $this->input->get(NULL, TRUE);
    $cond_where = '';
    //分页开始
    $data['num'] = $this->joinus_model->get_joinus_num($cond_where);
    $list = $this->joinus_model->get_joinus_list($cond_where, 0, $data['num']);
    foreach ($list as $key => $vo) {
      $province = $this->joinus_model->get_province_city_by_id($vo['province']);
      $city = $this->joinus_model->get_province_city_by_id($vo['city']);
      $list[$key]['province'] = $province['name'];
      $list[$key]['city'] = $city['name'];
      $list[$key]['createtime'] = date('Y-m-d H:i:s', $vo['createtime']);
    }
    //print_r($list);exit;

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
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '手机');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '电子邮箱');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '申请省份');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '申请市区');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '公司名称');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '公司地址');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '公司电话');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '申请时间');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '公司介绍');
    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['id']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['name']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['phone']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['email']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['province']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['city']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['company_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 2]['address']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 2]['company_phone']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $list[$i - 2]['createtime']);
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $list[$i - 2]['remark']);
    }

    $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('product_nums');
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

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
