<?php

/**
 * 每日房源发布量查询
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      yuan
 */
class Stat_signing extends MY_Controller
{
    /**
     * 解析函数
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('bargain_model');
    }

    /**
     * @param string $city 城市
     */
//经纪人统计表的页面
    public function index()
    {
        $data_view = array();
        $params = $this->input->post(NULL, TRUE);

        $this->load->helper('page_helper');
        $pg = $params['pg'];
        //查询条件
        $search_where = $params['search_where'];
        $search_value = $params['search_value'];
        //设置时间条件
        $start_time = $params['start_time'];
        $end_time = $params['end_time'];

        $where = 'id > 0';
        if ($search_where && $search_value) {
            $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
        }
        if ($start_time) {
            $where .= ' and signing_time >= "' . strtotime($start_time . " 00:00:00") . '"';
        }
        if ($end_time) {
            $where .= ' and signing_time <= "' . strtotime($end_time . " 23.59.59") . '"';
        }
        //搜索条件
        $data_view['where_cond'] = array(
            'search_where' => $search_where,
            'search_value' => $search_value
        );

        $data_view['start_time'] = $start_time;
        $data_view['end_time'] = $end_time;




        //分页开始
//        $data_view['pagesize'] = 20; //设定每一页显示的记录数
//        $data_view['count'] = $this->bargain_model->signing_count_by($where);
//        $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
//            / $data_view['pagesize']) : 0;  //计算总页数
//        $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
//        $data_view['page'] = ($data_view['page'] > $data_view['pages']
//            && $data_view['pages'] != 0) ? $data_view['pages']
//            : $data_view['page'];  //判断跳转页数
//        //计算记录偏移量
//        $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
        //经纪人列表
        //$signing_data = $this->bargain_model->get_signing_data_by($where, $data_view['offset'], $data_view['pagesize']);
        $signing_data = $this->bargain_model->get_signing_data_by($where);

        //搜索配置信息
        $data_view['stat_signing_data'] = $signing_data;
        $this->load->view('stat/stat_signing', $data_view);
    }

    /**
     * 导出签约成交统计报表
     * @author   wang
     */
    public function exportReport($search_where = 0, $search_value = 0, $start_time = 0, $end_time = 0)
    {

        ini_set('memory_limit', '-1');
        //表单提交参数组成的查询条件
        $search_where = $this->input->get('search_where', TRUE);
        $search_value = $this->input->get('search_value', TRUE);
        //设置时间条件
        $start_time = $this->input->get('start_time', TRUE);
        $end_time = $this->input->get('end_time', TRUE);
//        $start_time = $start_time != '' ? $start_time." 00:00:00" : date('Y-m-d', strtotime('-1 day'));
//        $end_time = $end_time != '' ? $end_time." 23:59:59" : date('Y-m-d', strtotime('-1 day'));

        $where = 'id > 0';
        if ($search_where && $search_value) {
            $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
        }
        if ($start_time) {
            $where .= ' and signing_time >= "' . strtotime($start_time . " 00:00:00") . '"';
        }
        if ($end_time) {
            $where .= ' and signing_time <= "' . strtotime($end_time . " 23.59.59") . '"';
        }
        $list = $this->bargain_model->get_signing_data_by($where);
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
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '签约次数');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '时间段');
        //设置表格的值
        for ($i = 2; $i <= count($list) + 1; $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['agency_name_a']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['signing_num']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $start_time . "至" . $end_time);
        }

        $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
        //$fileName = iconv("utf-8", "gb2312", $fileName);

        $objPHPExcel->getActiveSheet()->setTitle('stat_signing_nums');
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
