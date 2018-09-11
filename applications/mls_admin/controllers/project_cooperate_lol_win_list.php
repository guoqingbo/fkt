<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 权限菜单管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class project_cooperate_lol_win_list extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('project_cooperate_lol_model');
    $this->load->helper('user_helper');
  }

  /**
   * 中奖名单页面
   */
  public function index()
  {
    //模块
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $where = '';
    if ($post_param['broker_name']) {
      if (!empty($where)) {
        $where .= " AND ";
      }
      $where .= "broker_name like '%" . $post_param['broker_name'] . "%'";
    }
    if ($post_param['phone']) {
      if (!empty($where)) {
        $where .= " AND ";
      }
      $where .= "phone like '%" . $post_param['phone'] . "%'";
    }
    $data['post_param'] = $post_param;
    //分页开始
    $data['win_num'] = $this->project_cooperate_lol_model->get_cooperate_win_num($where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['win_num'] ? ceil($data['win_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['reward_type'] = $this->project_cooperate_lol_model->get_cooperate_reward_type();
    $win_list = $this->project_cooperate_lol_model->get_cooperate_win_list($where, $data['offset'], $data['pagesize']);
    foreach ($win_list as $key => $vo) {
      $city_arr = $this->project_cooperate_lol_model->get_city_by_broker_id($vo['broker_id']);
      $win_list[$key]['cityname'] = $city_arr['cityname'];
    }
    $data['win_list'] = $win_list;
    $this->load->view('project/cooperate/lol/win_list', $data);
  }


  /**
   * 导出礼品表数据
   * @author   wang
   */
  public function exportReport($broker_name = 0, $phone = 0)
  {

    ini_set('memory_limit', '-1');
    //表单提交参数组成的查询条件
    $broker_name = $this->input->get('broker_name', TRUE);
    $phone = $this->input->get('phone', TRUE);

    $where = 'id > 0';
    if ($broker_name) {
      $where .= ' and broker_name like ' . "'%$broker_name%'";
    }
    if ($phone) {
      $where .= ' and phone like ' . "'%$phone%'";
    }
    $limit = $this->project_cooperate_lol_model->get_cooperate_win_num($where);
    $productlist = $this->project_cooperate_lol_model->get_cooperate_win_list($where, 0, $limit);
    $reward_type = $this->project_cooperate_lol_model->get_cooperate_reward_type();
    $list = array();
    if (is_full_array($productlist)) {
      foreach ($productlist as $key => $value) {
        $list[$key]['id'] = $value['id'];
        $list[$key]['broker_name'] = $value['broker_name'];
        $list[$key]['phone'] = $value['phone'];
        $city_arr = $this->project_cooperate_lol_model->get_city_by_broker_id($value['broker_id']);
        $list[$key]['cityname'] = $city_arr['cityname'];
        foreach ($reward_type as $k => $val) {
          if ($val['id'] == $value['reward_type']) {
            $list[$key]['type_name'] = $val['name'];
          }
        }

        $list[$key]['create_time'] = date("Y-m-d", $value['create_time']);

      }

      $list = array_values($list);
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
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '中奖者名单');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '中奖者电话');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '奖品类型');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '中奖者所在城市');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '中将时间');
    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['id']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['broker_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['phone']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['type_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['cityname']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['create_time']);
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
