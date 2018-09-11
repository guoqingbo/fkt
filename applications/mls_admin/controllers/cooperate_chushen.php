<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cooperate_chushen extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('common_load_source_helper');
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->helper('community_helper');
    $this->load->model('cooperate_model');//合作模型类
    $this->load->model('cooperate_chushen_model');//合作模型类
    $this->load->model('api_broker_base_model');
    $this->load->model('message_model');//二手房源模型类
    $this->load->library('form_validation');//表单验证
  }

  /**
   * 根据表单提交参数，获取查询条件
   * @param array
   *
   */
  private function _get_cond_str($form_param)
  {
    //订单编号
    if (isset($form_param['order_sn']) && !empty($form_param['order_sn'])) {
      $cond_where .= " and order_sn = " . $form_param['order_sn'];
    }
    //甲方经纪人
    if (isset($form_param['broker_name_a']) && !empty($form_param['broker_name_a'])) {
      $cond_where .= " and broker_name_a LIKE '%" . $form_param['broker_name_a'] . "%'";
    }

    //甲方公司名
    if (isset($form_param['company_name_a']) && !empty($form_param['company_name_a'])) {
      $cond_where .= " and company_name_a LIKE '%" . $form_param['company_name_a'] . "%'";
    }

    //甲方门店名
    if (isset($form_param['agency_name_a']) && !empty($form_param['agency_name_a'])) {
      $cond_where .= " and agency_name_a LIKE '%" . $form_param['agency_name_a'] . "%'";
    }

    //乙方经纪人
    if (isset($form_param['broker_name_b']) && !empty($form_param['broker_name_b'])) {
      $cond_where .= " and broker_name_b LIKE '%" . $form_param['broker_name_b'] . "%'";
    }

    //乙方公司名
    if (isset($form_param['company_name_b']) && !empty($form_param['company_name_b'])) {
      $cond_where .= " and company_name_b LIKE '%" . $form_param['company_name_b'] . "%'";
    }

    //乙方门店名
    if (isset($form_param['agency_name_b']) && !empty($form_param['agency_name_b'])) {
      $cond_where .= " and agency_name_b LIKE '%" . $form_param['agency_name_b'] . "%'";
    }

    //卖方姓名
    if (isset($form_param['seller_owner']) && !empty($form_param['seller_owner'])) {
      $cond_where .= " and seller_owner LIKE '%" . $form_param['seller_owner'] . "%'";
    }

    //卖方电话
    if (isset($form_param['seller_telno']) && !empty($form_param['seller_telno'])) {
      $cond_where .= " and seller_telno LIKE '%" . $form_param['seller_telno'] . "%'";
    }

    //买方姓名
    if (isset($form_param['buy_owner']) && !empty($form_param['buy_owner'])) {
      $cond_where .= " and buy_owner LIKE '%" . $form_param['buy_owner'] . "%'";
    }

    //买方电话
    if (isset($form_param['buy_telno']) && !empty($form_param['buy_telno'])) {
      $cond_where .= " and buy_telno LIKE '%" . $form_param['buy_telno'] . "%'";
    }

    //买方电话
    if (isset($form_param['status'])) {
      $cond_where .= " and status LIKE '%" . $form_param['status'] . "%'";
    }
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, 'and');
    $cond_where = trim($cond_where);
    return $cond_where;
  }

  //门店管理页
  public function index()
  {
    $data['title'] = '合作审核资料审核';
    //筛选条件
    $data['param_array'] = $this->input->post(NULL, TRUE);
    $cond_where = $this->_get_cond_str($data['param_array']);
    //分页开始
    $data['num'] = $this->cooperate_chushen_model->get_cooperate_chushen_num($cond_where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['num'] ? ceil($data['num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $list = $this->cooperate_chushen_model->get_cooperate_chushen_list($cond_where, $data['offset'], $data['pagesize']);
    $data['list'] = $list;
    $this->load->view('cooperate_chushen/index', $data);
  }

  public function modify($id)
  {
    $data['title'] = '合作初审资料审核';
    $data['modifyResult'] = '';
    $submit_flag = $this->input->post('submit_flag');
    $data['success_applay'] = $this->cooperate_chushen_model->get_cooperate_chushen_by_id($id);
    if ($submit_flag == 'modify') {
      if ($data['success_applay']['status'] != 0) {
        echo '此记录已经审核过';
      } else {
        $status = $this->input->post('status');

        if ($data['success_applay']['apply_type'] == 1) {
          $house_broker = $data['success_applay']['brokerid_a'];
          $customer_broker = $data['success_applay']['brokerid_b'];
          $house_broker_name = $data['success_applay']['broker_name_a'];
          $customer_broker_name = $data['success_applay']['broker_name_b'];
          $house_url = '/cooperate/accept_order_list/';
          $customer_url = '/cooperate/send_order_list/';
        } else {
          $house_broker = $data['success_applay']['brokerid_b'];
          $customer_broker = $data['success_applay']['brokerid_a'];
          $house_broker_name = $data['success_applay']['broker_name_b'];
          $customer_broker_name = $data['success_applay']['broker_name_a'];
          $house_url = '/cooperate/send_order_list/';
          $customer_url = '/cooperate/accept_order_list/';
        }
        if ($status == 1) {
          $this->load->library('Sms_codi', array('city' => 'hz', 'jid' => '2', 'template' => 'cooperate_lol_pass'), 'sms');
          $return_a = $this->sms->send($data['success_applay']['phone_a'], array('order_sn' => $data['success_applay']['order_sn']));
          $result_a['status'] = $return_a['success'] ? 1 : 0;
          $result_a['msg'] = $return_a['success'] ? '短信发送成功' : $return_a['errorMessage'];
          $return_b = $this->sms->send($data['success_applay']['phone_b'], array('order_sn' => $data['success_applay']['order_sn']));
          $result_b['status'] = $return_b['success'] ? 1 : 0;
          $result_b['msg'] = $return_b['success'] ? '短信发送成功' : $return_b['errorMessage'];

          if ($data['success_applay']['companyid_a'] == $data['success_applay']['companyid_b'] && $data['success_applay']['agency_type_a'] == 1 && $data['success_applay']['agency_type_b'] == 1) {
            $this->message_model->add_message('1-49-3', $house_broker, $house_broker_name, $house_url, array('order_sn' => $data['success_applay']['order_sn']));
            $this->message_model->add_message('1-49-4', $customer_broker, $customer_broker_name, $customer_url, array('order_sn' => $data['success_applay']['order_sn']));
          } else {
            $this->message_model->add_message('1-49-1', $house_broker, $house_broker_name, '/gift_exchange/index', array('order_sn' => $data['success_applay']['order_sn']));
            $this->message_model->add_message('1-49-2', $customer_broker, $customer_broker_name, '/gift_exchange/index', array('order_sn' => $data['success_applay']['order_sn']));
          }
        } else if ($status == 2) {
          $this->load->library('Sms_codi', array('city' => 'hz', 'jid' => '2', 'template' => 'cooperate_lol_fail'), 'sms');
          $return_a = $this->sms->send($data['success_applay']['phone_a'], array('order_sn' => $data['success_applay']['order_sn']));
          $result_a['status'] = $return_a['success'] ? 1 : 0;
          $result_a['msg'] = $return_a['success'] ? '短信发送成功' : $return_a['errorMessage'];
          $return_b = $this->sms->send($data['success_applay']['phone_b'], array('order_sn' => $data['success_applay']['order_sn']));
          $result_b['status'] = $return_b['success'] ? 1 : 0;
          $result_b['msg'] = $return_b['success'] ? '短信发送成功' : $return_b['errorMessage'];

          $this->message_model->add_message('1-50', $house_broker, $house_broker_name, $house_url, array('order_sn' => $data['success_applay']['order_sn']));
        }
        $result = $this->cooperate_chushen_model->update_cooperate_chushen_status($id, $data['success_applay']['c_id'], $status);
        //合作成交审核通过加积分
        $this->load->model('api_broker_credit_model');
        $this->api_broker_credit_model->set_broker_param('', 1);
        $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($data['success_applay']['c_id']);
        $this->api_broker_credit_model->cooperate_confirm_deal($cooperate_info);
        //合作成交审核通过加等级分值
        $this->load->model('api_broker_level_base_model');
        $this->api_broker_level_base_model->set_broker_param('', 1);
        $this->api_broker_level_base_model->cooperate_confirm_deal($cooperate_info);

        if ($result) {
          $data['modifyResult'] = 'success';
        } else {
          $data['modifyResult'] = 'false';
        }
      }
    }
    $this->load->view('cooperate_chushen/modify', $data);
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
    $cond_where = $this->_get_cond_str($data['param_array']);
    //分页开始
    $data['num'] = $this->cooperate_chushen_model->get_cooperate_chushen_num();
    $list = $this->cooperate_chushen_model->get_cooperate_chushen_list($cond_where, 0, $data['num']);
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '甲方');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '甲方');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '甲方');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '甲方');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '乙方');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '乙方');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '乙方');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '乙方');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', '');

    $objPHPExcel->getActiveSheet()->setCellValue('A2', '序号');
    $objPHPExcel->getActiveSheet()->setCellValue('B2', '合同内部编号');
    $objPHPExcel->getActiveSheet()->setCellValue('C2', '业主姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('D2', '业主电话');
    $objPHPExcel->getActiveSheet()->setCellValue('E2', '公司');
    $objPHPExcel->getActiveSheet()->setCellValue('F2', '门店');
    $objPHPExcel->getActiveSheet()->setCellValue('G2', '经纪人');
    $objPHPExcel->getActiveSheet()->setCellValue('H2', '手机');
    $objPHPExcel->getActiveSheet()->setCellValue('I2', '公司');
    $objPHPExcel->getActiveSheet()->setCellValue('J2', '门店');
    $objPHPExcel->getActiveSheet()->setCellValue('K2', '经纪人');
    $objPHPExcel->getActiveSheet()->setCellValue('L2', '手机');
    $objPHPExcel->getActiveSheet()->setCellValue('M2', '创建时间');
    $objPHPExcel->getActiveSheet()->setCellValue('N2', '操作');
    //设置表格的值
    for ($i = 3; $i <= count($list) + 2; $i++) {

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 3]['id']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 3]['order_sn']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 3]['seller_owner']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 3]['seller_telno']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 3]['company_name_a']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 3]['agency_name_a']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 3]['broker_name_a']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 3]['phone_a']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 3]['company_name_b']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $list[$i - 3]['agency_name_b']);
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $list[$i - 3]['broker_name_b']);
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $list[$i - 3]['phone_b']);
      $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, date('Y-m-d', $list[$i - 3]['create_time']));
      if ($list[$i - 3]['status'] == 1) {
        $list[$i - 3]['status'] = '审核通过';
      } else if ($list[$i - 3]['status'] == 2) {
        $list[$i - 3]['status'] = '驳回';
      } else {
        $list[$i - 3]['status'] = '审核中';
      }
      $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $list[$i - 3]['status']);
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
