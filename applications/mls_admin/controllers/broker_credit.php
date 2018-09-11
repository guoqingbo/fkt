<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 积分--用户积分明细
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Broker_credit extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('credit_record_base_model');
    $this->load->model('broker_info_base_model');
    $this->load->model('agency_base_model');
    $this->load->helper('page_helper');
  }

  //用户积分明细页
  public function index()
  {
    $data_view = array();
    $pg = $this->input->post('pg');
    $search_where = $this->input->post('search_where');
    $search_value = trim($this->input->post('search_value'));
    $infofrom = $this->input->post('infofrom');
    $score = $this->input->post('score');
    $type = $this->input->post('type');
    $time_s = strtotime($this->input->post('time_s'));
    $time_e = strtotime($this->input->post('time_e')) + 86399;
    $where = 'id > 0';
    if ($search_where && $search_value) {
      $broker = $this->broker_info_base_model->get_all_by($search_where . " like '%" . $search_value . "%'", 0, 0);
      $broker_id_array = array();
      if (is_full_array($broker)) {
        foreach ($broker as $key => $vo) {
          $broker_id_array[] = $vo['broker_id'];
        }
      }
      $broker_id_string = implode(',', $broker_id_array);
      $where .= ' and broker_id in (' . $broker_id_string . ')';
    }
    if ($infofrom) {
      $where .= ' and infofrom =' . $infofrom;
    }
    if ($score == 'score1') {
      $where .= ' and score > 0';
    } elseif ($score == 'score2') {
      $where .= ' and score < 0';
    }
    if ($time_s && $time_e) {
      $where .= ' and create_time>= "' . $time_s . '"';
      $where .= ' and create_time<= "' . $time_e . '"';
    }
    //公司和门店
    $company_id = $this->input->post('company_id');
    $company_name = $this->input->post('company_name');
    $agency_id = $this->input->post('agency_id');
    if ($company_id) {
      $broker_ = $this->broker_info_base_model->get_one_by($data = array('company_id' => $company_id));
      $where .= ' and broker_id=' . $broker_['broker_id'];
    }
    if ($agency_id) {
      $broker_1 = $this->broker_info_base_model->get_one_by($data = array('agency_id' => $agency_id));
      $where .= ' and broker_id=' . $broker_1['broker_id'];
    }
    //操作行为
    if (isset($type) && $type) {
      $where .= ' and type=' . $type;
    }
    //获取总的积分获取值
    if (isset($score) && $score == 'score1') {
      $data_view['score_get'] = $this->credit_record_base_model->sum_score_by($where);
      $data_view['score_consume'] = 0;
    } elseif (isset($score) && $score == 'score2') {
      $data_view['score_get'] = 0;
      $data_view['score_consume'] = $this->credit_record_base_model->sum_score_by($where);
    } else {
      $where1 = $where . ' and score > 0';
      $data_view['score_get'] = $this->credit_record_base_model->sum_score_by($where1);
      //echo $data_view['score_get'].'<br />';
      $where2 = $where . ' and score < 0';
      $data_view['score_consume'] = $this->credit_record_base_model->sum_score_by($where2);
      //echo $data_view['score_consume'];
    }
    //条件
    $data_view['where_cond'] = array(
      'search_where' => $search_where, 'search_value' => $search_value, 'infofrom' => $infofrom, 'time_s' => $time_s, 'time_e' => $time_e, 'company_id' => $company_id,
      'agency_id' => $agency_id, 'company_name' => $company_name, 'score' => $score, 'type' => $type
    );
    //分页开始
    $data_view['count'] = $this->credit_record_base_model->count_by($where);

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
    $data_view['broker_credit'] = $this->credit_record_base_model->get_all_by(
      $where, $data_view['offset'], $data_view['pagesize']);
    //引入积分规则类
    $this->load->model('api_broker_credit_model');
    $credit_way = $this->api_broker_credit_model->get_way();
    //通过broker_id获取经纪人的手机号码，姓名，所属公司及门店
    foreach ($data_view['broker_credit'] as $k => $v) {
      $broker_info = $this->broker_info_base_model->get_by_broker_id($v['broker_id']);
      //获取phone及手机号码
      $data_view['broker_credit'][$k]['phone'] = $broker_info['phone'];
      $data_view['broker_credit'][$k]['truename'] = $broker_info['truename'];
      $data_view['broker_credit'][$k]['credit_way'] = $credit_way[$v['type']];
      //获取所属公司及门店
      //$agency_name = $this->agency_base_model->get_by_id($broker_info['agency_id']);

    }
    //echo '<pre>';print_r($data_view['broker_credit']);die;
    $data_view['credit_way'] = $credit_way;
    $data_view['title'] = '用户积分明细';
    $data_view['conf_where'] = 'index';
    $this->load->helper('common_load_source_helper');
    $data_view['css'] = load_css('mls/css/v1.0/autocomplete.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic.js,'
      . 'mls/js/v1.0/cooperate_common.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    $this->load->view('broker_credit/index', $data_view);
  }

  /**
   * 导出用户积分数据
   * @author   wang
   */
  public function exportReport($search_where = 0, $search_value = 0, $company_id = 0, $agency_id = 0, $infofrom = 0, $score = 0, $time_s = 0, $time_e = 0)
  {

    ini_set('memory_limit', '-1');
    //表单提交参数组成的查询条件
    $search_where = $this->input->get('search_where', TRUE);
    $search_value = $this->input->get('search_value', TRUE);
    $infofrom = $this->input->get('infofrom', TRUE);
    $score = $this->input->get('score', TRUE);
    $type = $this->input->get('type', TRUE);
    //设置时间条件
    $time_s = strtotime($this->input->get('time_s', TRUE));
    $time_e = strtotime($this->input->get('time_e', TRUE)) + 86399;
    //$time_s = $time_s != '' ? $time_s : date('Y-m-d', strtotime('-1 day'));
    //$time_e = $time_e != '' ? $time_e : date('Y-m-d', strtotime('-1 day'));

    $where = 'id > 0';
    if ($search_where && $search_value) {
      $broker = $this->broker_info_base_model->get_one_by($data = array($search_where => $search_value));
      $where .= ' and broker_id=' . $broker['broker_id'];
    }
    if ($infofrom) {
      $where .= ' and infofrom =' . $infofrom;
    }
    if ($score == 'score1') {
      $where .= ' and score > 0';
    }
    if ($time_s && $time_e) {
      $where .= ' and create_time>= "' . $time_s . '"';
      $where .= ' and create_time<= "' . $time_e . '"';
    }
    //公司和门店
    $company_id = $this->input->get('company_id', TRUE);
    //$company_name = $this->input->get('company_name', TRUE);
    $agency_id = $this->input->get('agency_id', TRUE);
    if ($company_id) {
      $broker_ = $this->broker_info_base_model->get_one_by($data = array('company_id' => $company_id));
      $where .= ' and broker_id=' . $broker_['broker_id'];
    }
    if ($agency_id) {
      $broker_1 = $this->broker_info_base_model->get_one_by($data = array('agency_id' => $agency_id));
      $where .= ' and broker_id=' . $broker_1['broker_id'];
    }
    //操作行为
    if (isset($type) && $type) {
      $where .= ' and type=' . $type;
    }
    $limit = $this->credit_record_base_model->count_by($where);
    //print_r($limit);die;
    $creditlist = $this->credit_record_base_model->get_all_by($where, 0, $limit);
    //引入积分规则类
    $this->load->model('api_broker_credit_model');
    $credit_way = $this->api_broker_credit_model->get_way();
    $list = array();
    if (is_full_array($creditlist)) {
      foreach ($creditlist as $key => $value) {

        $broker_info = $this->broker_info_base_model->get_by_broker_id($value['broker_id']);

        $list[$key]['id'] = $value['id'];
        $list[$key]['phone'] = $broker_info['phone'];
        $list[$key]['truename'] = $broker_info['truename'];
        $list[$key]['create_time'] = date("Y-m-d H:i:s", $value['create_time']);
        $list[$key]['type'] = $credit_way[$value['type']]['action'];
        $list[$key]['score'] = $value['score'];
        $list[$key]['remark'] = $value['remark'];
        $list[$key]['credit'] = $value['credit'];
        $this->load->model('agency_model');
        $this->load->model('district_model');
        $agency = $this->agency_model->get_by_id($broker_info['agency_id']);
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
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '手机号码');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '真实姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '时间');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '行为');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '积分');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '备注');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '剩余积分');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '公司名称');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '门店名称');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '区属');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '板块');
    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['id']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['phone']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['truename']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['create_time']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['type']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['score']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['remark']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 2]['credit']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 2]['company_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $list[$i - 2]['agency_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $list[$i - 2]['district']);
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $list[$i - 2]['streetname']);
    }

    $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('credit_nums');
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


  //刷经纪人注册登录成长值
  public function level_job_broker()
  {
    $this->load->model('api_broker_level_base_model');

    $this->load->model('level_base_model');

    $data = $this->level_base_model->get_broker_arr();

    if (is_full_array($data)) {
      foreach ($data as $value) {
        //增加注册成长值
        $this->api_broker_level_base_model->set_broker_param(array('broker_id' => $value['broker_id']), 1);
        $this->api_broker_level_base_model->register();
        if ($value['group_id'] == 2) {
          //增加认证成长值
          $this->api_broker_level_base_model->ident_cert();
        }

      }
    }

    echo 'over';
  }

  //刷出售房源成长值
  public function level_job_sell_house()
  {
    $this->load->model('api_broker_level_base_model');

    $this->load->model('level_base_model');

    $data = $this->level_base_model->get_sell_house_outside();

    if (is_full_array($data)) {
      foreach ($data as $value) {
        //增加注册成长值
        $this->api_broker_level_base_model->set_broker_param(array('broker_id' => $value['broker_id']), 1);
        $this->api_broker_level_base_model->rsync_fang100_shua($value, 1);
      }
    }

    echo 'over';
  }

  //刷出租房源成长值
  public function level_job_rent_house()
  {
    $this->load->model('api_broker_level_base_model');

    $this->load->model('level_base_model');

    $data = $this->level_base_model->get_rent_house_outside();

    if (is_full_array($data)) {
      foreach ($data as $value) {
        //增加注册成长值
        $this->api_broker_level_base_model->set_broker_param(array('broker_id' => $value['broker_id']), 1);
        $this->api_broker_level_base_model->rsync_fang100_shua($value, 2);

      }
    }

    echo 'over';
  }

  //刷合做订单成长值
  public function level_job_cooperate()
  {
    $this->load->model('api_broker_level_base_model');

    $this->load->model('level_base_model');
    $this->load->model('cooperate_model');

    $data = $this->level_base_model->get_cooperate();

    if (is_full_array($data)) {
      foreach ($data as $value) {
        $cooperate_info = $this->cooperate_model->get_cooperate_by_cid($value['c_id']);
        $this->api_broker_level_base_model->set_broker_param('', 1);
        $this->api_broker_level_base_model->cooperate_confirm_deal($cooperate_info);

      }
    }

    echo 'over';
  }
}

/* End of file company.php */
/* Location: ./application/mls_admin/controllers/company.php */
