<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 积分--商品管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class gift_exchange_record extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('gift_manage_model');
    $this->load->model('gift_exchange_record_base_model');
    $this->load->model('broker_info_base_model');
    $this->load->helper('page_helper');
    $this->load->model('city_model');
  }

  //商品管理页
  public function index($type)
  {
    $data_view = array();
    $pg = $this->input->post('pg');
    //礼品名称及编号的查询条件
    $search_where = $this->input->post('search_where');
    $search_where2 = $this->input->post('search_where2');
    $search_value = trim($this->input->post('search_value'));
    $search_value2 = trim($this->input->post('search_value2'));
    //订单编号
    $order = trim($this->input->post('order'));
    //时间的查询
    $time_s = strtotime($this->input->post('time_s'));
    $time_e = strtotime($this->input->post('time_e')) + 86399;
    $where = 'gift_exchange_record.id > 0 and type = ' . $type;
    if ($search_where && $search_value) {
      if ($search_where == 'product_serial_num') {
        $gift_ids = $this->gift_manage_model->get_by_product_serial_num($search_value);
        $gift_ids_string = '';
        if (is_full_array($gift_ids)) {
          foreach ($gift_ids as $k => $v) {
            $gift_ids_string .= $v['id'] . ',';
          }
          $gift_ids_string = substr($gift_ids_string, 0, -1);
        } else {
          $gift_ids_string = 0;
        }
        //print_r($gift_ids_string);exit;
        $where .= ' and gift_id in ' . "($gift_ids_string)";
      } else {
        $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
      }
    }
    if ($search_where2 && $search_value2) {
      $where .= ' and ' . $search_where2 . ' like ' . "'%$search_value2%'";
    }
    if ($order) {
      $where .= ' and gift_exchange_record.order like ' . "'%$order%'";
    }
    if ($time_s && $time_e) {
      $where .= ' and create_time >= "' . $time_s . '"';
      $where .= ' and create_time <= "' . $time_e . '"';
    }
    //echo $where;
    //条件
    $data_view['where_cond'] = array(
      'search_where' => $search_where, 'search_value' => $search_value, 'search_where2' => $search_where2, 'search_value2' => $search_value2, 'search_where_time' => $search_where_time, 'time_s' => $time_s, 'time_e' => $time_e, 'order' => $order
    );
    //print_r($data_view['where_cond']);
    //分页开始
    $data_view['count'] = $this->gift_exchange_record_base_model->count_by($where);
    $data_view['pagesize'] = 10; //设定每一页显示的记录数
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //公司列表
    $data_view['gift'] = $this->gift_exchange_record_base_model->get_all_by(
      $where, $data_view['offset'], $data_view['pagesize'], 'id', 'DESC');
    if (is_full_array($data_view['gift'])) {
      foreach ($data_view['gift'] as $key => $vo) {
        $gift_manage_info = $this->gift_manage_model->get_by_id($vo['gift_id']);
        $data_view['gift'][$key]['product_serial_num'] = $gift_manage_info['product_serial_num'];
        $data_view['gift'][$key]['product_name'] = $gift_manage_info['product_name'];
      }
    }

    $data_view['type'] = $type;
    if ($type == 1) {
      $data_view['title'] = '积分兑换';
    } else {
      $data_view['title'] = '积分抽奖';
    }
    $data_view['conf_where'] = 'index';
    $this->load->view('gift_exchange_record/index', $data_view);
  }


  /**
   * 导出礼品表数据
   * @author   wang
   */
  public function exportReport($search_where = 0, $search_value = 0, $status = 0, $search_where_time = 0, $time_s = 0, $time_e = 0, $type = 0)
  {

    ini_set('memory_limit', '-1');
    //表单提交参数组成的查询条件
    $search_where = $this->input->get('search_where', TRUE);
    $search_value = $this->input->get('search_value', TRUE);
    $search_where2 = $this->input->get('search_where2', TRUE);
    $search_value2 = $this->input->get('search_value2', TRUE);
    $type = $this->input->get('type', TRUE);
    //设置时间条件
    $time_s = strtotime($this->input->get('time_s', TRUE));
    $time_e = strtotime($this->input->get('time_e', TRUE)) + 86399;
    //$time_s = $time_s != '' ? $time_s : date('Y-m-d', strtotime('-1 day'));
    //$time_e = $time_e != '' ? $time_e : date('Y-m-d', strtotime('-1 day'));
    $where = 'gift_exchange_record.id > 0 and type = ' . $type;
    if ($search_where && $search_value) {
      $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
    }
    if ($search_where2 && $search_value2) {
      $where .= ' and ' . $search_where2 . ' like ' . "'%$search_value2%'";
    }
    if ($order) {
      $where .= ' and gift_exchange_record.order like ' . "'%$order%'";
    }
    if ($time_s && $time_e) {
      $where .= ' and create_time >= "' . $time_s . '"';
      $where .= ' and create_time <= "' . $time_e . '"';
    }
    $limit = $this->gift_exchange_record_base_model->count_by($where);
    $productlist = $this->gift_exchange_record_base_model->get_all_by($where, 0, $limit);
    $list = array();
    if (is_full_array($productlist)) {
      $this->load->model('broker_info_model');
      $this->load->model('agency_model');
      $this->load->model('district_model');
      foreach ($productlist as $key => $value) {
        $gift_manage_info = $this->gift_manage_model->get_by_id($value['gift_id']);
        $list[$key]['product_serial_num'] = $gift_manage_info['product_serial_num'];
        $list[$key]['product_name'] = $gift_manage_info['product_name'];
        $list[$key]['score_record'] = $value['score_record'];
        $list[$key]['id'] = $value['id'];
        $list[$key]['order'] = $value['order'];
        $list[$key]['truename'] = $value['truename'];
        $list[$key]['phone'] = $value['phone'];

        if ($value['create_time']) {
          $list[$key]['create_time'] = date("Y-m-d H:i:s", $value['create_time']);
        } else {
          $list[$key]['create_time'] = date("Y-m-d H:i:s", time());
        }
        $broker = $this->broker_info_model->get_by_broker_id($value['broker_id']);
        $agency = $this->agency_model->get_by_id($broker['agency_id']);
        $company = $this->agency_model->get_by_id($agency['company_id']);
        $list[$key]['company_name'] = $company['name'];
        $list[$key]['agency_name'] = $agency['name'];
        $list[$key]['district'] = $this->district_model->get_distname_by_id($agency['dist_id']);
        $list[$key]['streetname'] = $this->district_model->get_streetname_by_id($agency['street_id']);
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '序号');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '订单号');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '真实姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '手机号码');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '商品编号');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '商品名称');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '兑换积分值');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '兑换时间');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '公司名称');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '门店名称');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '区属');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '板块');
    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['id']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['order']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['truename']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['phone']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['product_serial_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['product_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['score_record']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 2]['create_time']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 2]['company_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $list[$i - 2]['agency_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $list[$i - 2]['district']);
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $list[$i - 2]['streetname']);
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

/* End of file company.php */
/* Location: ./application/mls_admin/controllers/company.php */
