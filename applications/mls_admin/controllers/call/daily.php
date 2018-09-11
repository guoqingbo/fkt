<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 隐号拨打日报表
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class Daily extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('page_helper');
        $this->load->model('call_agency_statistics_model');
    }

    /**
     * 权限菜单列表页面
     */
    public function index()
    {
        $data = ['title' => '隐号拨打日报表'];
        $data['type'] = array(
            1 => '月租费',
            2 => '通话费'
        );

        $where = "";
        //post参数
        $param = $this->input->post(NULL, TRUE);
        $data['param'] = $param;
        if (isset($param['company_name']) && !empty($param['company_name'])) {
            $where = "b.name like '%" . trim($param['company_name']) . "%'";
        }
        if (isset($param['agency_name']) && !empty($param['agency_name'])) {
            if (!empty($where)) {
                $where .= " AND ";
            }
            $where .= "c.name like '%" . trim($param['agency_name']) . "%'";
        }
        if (isset($param['start_time']) && !empty($param['start_time']) && isset($param['end_time']) && !empty($param['end_time'])) {
            $start_time = strtotime($param['start_time']);
            $end_time = strtotime($param['end_time'] . ' 23:59:59');
            if ($start_time > $end_time) {
                echo "日期开始时间不能早于结束时间";
                exit;
            }
        }
        if (isset($param['start_time']) && !empty($param['start_time'])) {
            if (!empty($where)) {
                $where .= " AND ";
            }
            $where .= "a.statis_time >= " . strtotime($param['start_time']);
        }
        if (isset($param['end_time']) && !empty($param['end_time'])) {
            if (!empty($where)) {
                $where .= " AND ";
            }
            $where .= "a.statis_time <= " . strtotime($param['end_time'] . ' 23:59:59');
        }

        //分页开始
        $db_city = $this->call_agency_statistics_model->get_db_city();
        $table = $db_city->from('call_agency_statistics AS a')->join('agency as b', 'a.company_id = b.id', 'left')->join('agency as c', 'a.agency_id = c.id', 'left');
        if (!empty($where)) {
            $table = $table->where($where);
        }
        $data['district_num'] = $table->count_all_results();
        if (isset($param['export']) && 1 == $param['export']) {//导出
            if (empty($data['district_num'])) {
                echo '没有数据可以导出，请重新筛选';
            } else {
                $table = $db_city->from('call_agency_statistics AS a')->join('agency as b', 'a.company_id = b.id', 'left')->join('agency as c', 'a.agency_id = c.id', 'left');
                if (!empty($where)) {
                    $table = $table->where($where);
                }
                $data['fee_list'] = $table->select('a.*, b.name as company_name, c.name as agency_name')->order_by('a.statis_time', 'desc')->get()->result_array();
                $this->export($data['fee_list']);
            }
            exit;
        }
        $data['pagesize'] = 10;//设定每一页显示的记录数
        $data['pages'] = $data['district_num'] ? ceil($data['district_num'] / $data['pagesize']) : 0;  //计算总页数
        $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
        $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
        $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

        //申请隐号门店列表
        if (empty($data['district_num'])) {
            $data['fee_list'] = array();
        } else {
            $table = $db_city->from('call_agency_statistics AS a')->join('agency as b', 'a.company_id = b.id', 'left')->join('agency as c', 'a.agency_id = c.id', 'left');
            if (!empty($where)) {
                $table = $table->where($where);
            }
            $data['fee_list'] = $table->select('a.*, b.name as company_name, c.name as agency_name')->order_by('a.statis_time', 'desc')->order_by('a.id', 'desc')->limit($data['pagesize'], $data['offset'])->get()->result_array();
        }

        $this->load->view('call/daily/index', $data);
    }

    public function export($data)
    {
        //调用PHPExcel第三方类库
        $this->load->library('PHPExcel.php');
        $this->load->library('PHPExcel/IOFactory');
        ini_set('memory_limit', '-1');
        //创建phpexcel对象
        $objPHPExcel = new PHPExcel();

        //设置phpexcel文件内容
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

        //设置表格导航属性
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '结算日期');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '公司名称');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '分店名称');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', '充值金额');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', '消费金额');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', '账户余额');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

        //设置表格的值
        foreach ($data as $k => $v) {
            $i = $k + 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, date('Y-m-d', $v['statis_time']));
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $v['company_name']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $v['agency_name']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $v['recharge_amount']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $v['consume_amount']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $v['balance']);
        }

        $ua = $_SERVER["HTTP_USER_AGENT"];
        $fileName = '隐号拨打日报表_' . date('Y-m-d') . '.xls';
        $fileEncodeName = urlencode('隐号拨打日报表_' . date('Y-m-d') . '.xls');
        $objPHPExcel->getActiveSheet()->setTitle('隐号拨打日报表');
        $objPHPExcel->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel;charset=utf-8');
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $fileEncodeName . '"');
        } elseif (preg_match("/Firefox/", $ua)) {
            header('Content-Disposition: attachment;filename*="utf8\'\'' . $fileName . '"');
        } else {
            header("Content-Disposition: attachment;filename=$fileName");
        }
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        //header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        // print_r($data);exit;
        $objWriter->save('php://output');
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
