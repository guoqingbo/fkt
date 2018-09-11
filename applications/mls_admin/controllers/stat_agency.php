<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 用户详细信息类
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Stat_agency extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('stat_agency_model');
    }

    //门店管理页
    public function index()
    {
        $data_view = array();
        $data_view['title'] = '门店数据统计';
        $data_view['conf_where'] = 'index';
        $start_time = $this->input->post('start_time');
        $end_time = $this->input->post('end_time');
        $where_arr = array();
        if ($start_time) {
            $start_time = $start_time . ' 00:00:00';
            $where_arr['start_time'] = strtotime($start_time);
        } else {
            $where_arr['start_time'] = 0;
        }
        if ($end_time) {
            $end_time = $end_time . ' 23:59:59';
            $where_arr['end_time'] = strtotime($end_time);
        } else {
            $where_arr['end_time'] = time();
        }

        $result = $this->stat_agency_model->stat_agency($where_arr);//门店统计量

        $data_view['result'] = $result;
        $this->load->view('stat/stat_agency', $data_view);
    }

    /**
     * 导出统计报表
     * @author   wang
     */
    public function exportReport()
    {

        ini_set('memory_limit', '-1');
        //表单提交参数组成的查询条件
        //$search_where = $this->input->get('search_where', TRUE);
        //$search_value = $this->input->get('search_value', TRUE);
        //设置时间条件
        $start_time = $this->input->get('start_time', TRUE);
        $end_time = $this->input->get('end_time', TRUE);
        $where_arr = array();
        if ($start_time) {
            $where_arr['start_time'] = strtotime($start_time . " 00:00:00");
        } else {
            $where_arr['start_time'] = 0;
        }
        if ($end_time) {
            $where_arr['end_time'] = strtotime($end_time . " 23.59.59");
        } else {
            $where_arr['end_time'] = time();
        }
        $datalist = $this->stat_agency_model->stat_agency($where_arr);//门店统计量
        $list = $datalist['list'];
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
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '门店名称');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '登录次数');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '出售房源发布量');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', '出租房源发布量');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', '签约成交量');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', '门店地址');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', '日期');
        //设置表格的值
        for ($i = 2; $i <= count($list) + 1; $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['agency_name']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['login_num']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['sell_num']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['rent_num']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['signing_num']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['agency_addr']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $start_time . " / " . $end_time);
        }

        $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
        //$fileName = iconv("utf-8", "gb2312", $fileName);

        $objPHPExcel->getActiveSheet()->setTitle('stat_agency');
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
