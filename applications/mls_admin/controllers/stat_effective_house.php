<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 用户详细信息类
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Stat_effective_house extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
//        $this->load->model('stat_effective_house_model');
        $this->load->model('sell_house_model');
        $this->load->model('rent_house_model');
    }

    //有效房源统计
    public function index()
    {
        $data_view = array();
        $this->load->helper('page_helper');
        $pg = $this->input->post('pg');
        $data_view['title'] = '有效房源统计';
        $data_view['conf_where'] = 'index';
        $nowtime = time();
        //设置查询条件
        $search_where = $this->input->post('search_where');
        $search_value = $this->input->post('search_value');
        $search_status = $this->input->post('search_status');
        if (!$search_status || $search_status == 99) {
            $search_status = 99;
            $where = 'status <> 0';
        } else if ($search_status == 1) {
            $where = 'status = ' . $search_status . ' and expiretime >= ' . $nowtime;
        } else {
            $where = 'status = ' . $search_status;
        }
        //引入经纪人基本类库
        $this->load->model('broker_info_model');

        $search_broker_base = false;
        if ($search_where && $search_value) {
            $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
            $search_broker_base = true;
        }

        //公司和门店
        $company_id = $this->input->post('company_id');
        $company_name = $this->input->post('company_name');
        $agency_id = $this->input->post('agency_id');
        if ($company_id || $agency_id) {
            $this->load->model('agency_model');
            $agencys = $this->agency_model->get_children_by_company_id($company_id);
        }
        if ($agency_id) {
            $where .= ' and agency_id = ' . $agency_id;
            $data_view['agencys'] = $agencys;
        } else if ($company_id) {
            if (is_full_array($agencys)) {
                $agency_id = array();
                foreach ($agencys as $v) {
                    $agency_id[] = $v['id'];
                }
                $agency_ids = implode(',', $agency_id);
                $where .= ' and agency_id in(' . $agency_ids . ')';
            }
        }

        //记录搜索过的条件
        $data_view['where_cond'] = array(
            'search_where' => $search_where, 'search_value' => $search_value,
            'agency_id' => $agency_id, 'search_status' => $search_status,
            'company_name' => $company_name, 'company_id' => $company_id,
        );
        //分页开始
        $data_view['count'] = 10;
        $data_view['pagesize'] = 20; //设定每一页显示的记录数
        $data_view['count'] = $this->broker_info_model->count_by($where);
        $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
            / $data_view['pagesize']) : 0;  //计算总页数
        $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
        $data_view['page'] = ($data_view['page'] > $data_view['pages']
            && $data_view['pages'] != 0) ? $data_view['pages']
            : $data_view['page'];  //判断跳转页数
        //计算记录偏移量
        $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
        //经纪人列表
        $broker_info = $this->broker_info_model->get_all_by($where, $data_view['offset'], $data_view['pagesize']);
        $data_view['where_config'] = $this->broker_info_model->get_where_config();

        if (is_full_array($broker_info)) {
            $this->load->model('agency_model');
            $this->load->model('sell_house_model');
            $this->load->model('rent_house_model');
            $agencyList = $companyList = [];
            foreach ($broker_info as $key => $value) {
                //有效出售房源
                $effective_sell_num = $this->sell_house_model->stat_effective_house($value['id']);
                $broker_info[$key]['effective_sell_num'] = $effective_sell_num;

                //出售房源总量
                $tatal_sell_num = $this->sell_house_model->get_tatal_house_num($value['id']);
                $broker_info[$key]['tatal_sell_num'] = $tatal_sell_num;

                //有效出租发布房源
                $effective_rent_num = $this->rent_house_model->stat_effective_house($value['id']);
                $broker_info[$key]['effective_rent_num'] = $effective_rent_num;

                //出售房源总量
                $tatal_rent_num = $this->rent_house_model->get_tatal_house_num($value['id']);
                $broker_info[$key]['tatal_rent_num'] = $tatal_rent_num;

                //积分
                if (!isset($agencyList[$value['agency_id']])) {
                    $agencyList[$value['agency_id']] = $this->agency_model->get_by_id($value['agency_id']);
                }
                if (!isset($companyList[$value['company_id']])) {
                    $companyList[$value['company_id']] = $this->agency_model->get_by_id($value['company_id']);
                }
                $broker_info[$key]['agency_name'] = $agencyList[$value['agency_id']]['name'];
                $broker_info[$key]['company_name'] = $companyList[$value['company_id']]['name'];
                if (empty($broker_info[$key]['agency_name'])) {
                    $register_info = $this->broker_info_model->get_register_info_by_brokerid($value['id']);
                    if (!empty($register_info)) {
                        $broker_info[$key]['agency_name'] = $register_info['storename'];
                        $broker_info[$key]['company_name'] = $register_info['corpname'];
                    }
                }
            }
        }

        $this->load->helper('common_load_source_helper');
        $data_view['css'] = load_css('mls/css/v1.0/base.css,'
            . 'mls/third/iconfont/iconfont.css,'
            . 'mls/css/v1.0/guest_disk.css,'
            . 'mls/css/v1.0/myStyle.css,'
            . 'mls/css/v1.0/house_manage.css,'
            . 'mls/css/v1.0/autocomplete.css');
        //需要加载的JS
        $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
            . 'common/third/swf/swfupload.js,'
            . 'mls/js/v1.0/uploadpic.js,'
            . 'mls/js/v1.0/cooperate_common.js,'
            . 'common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls/js/v1.0/openWin.js,'
            . 'mls/js/v1.0/house_list.js,'
            . 'mls/js/v1.0/jquery.validate.min.js,'
            . 'mls/js/v1.0/house.js');
        $data_view['broker_info'] = $broker_info;
        $this->load->view('stat/stat_effective_house', $data_view);
    }

    /**
     * 导出统计报表
     * @author   wang
     */
//    public function exportReport()
//    {
//
//        ini_set('memory_limit', '-1');
//        //表单提交参数组成的查询条件
//        //$search_where = $this->input->get('search_where', TRUE);
//        //$search_value = $this->input->get('search_value', TRUE);
//        //设置时间条件
//        $start_time = $this->input->get('start_time', TRUE);
//        $end_time = $this->input->get('end_time', TRUE);
//        $where_arr = array();
//        if ($start_time) {
//            $where_arr['start_time'] = strtotime($start_time . " 00:00:00");
//        } else {
//            $where_arr['start_time'] = 0;
//        }
//        if ($end_time) {
//            $where_arr['end_time'] = strtotime($end_time . " 23.59.59");
//        }
//        $list = $this->stat_agency_model->stat_agency($where_arr);//门店统计量
//        //调用PHPExcel第三方类库
//        $this->load->library('PHPExcel.php');
//        $this->load->library('PHPExcel/IOFactory');
//        //创建phpexcel对象
//        $objPHPExcel = new PHPExcel();
//        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); // 用于 2007 格式
//        $objWriter->setOffice2003Compatibility(true);
//
//        //设置phpexcel文件内容
//        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
//            ->setLastModifiedBy("Maarten Balliauw")
//            ->setTitle("Office 2007 XLSX Test Document")
//            ->setSubject("Office 2007 XLSX Test Document")
//            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
//            ->setKeywords("office 2007 openxml php")
//            ->setCategory("Test result file");
//
//        //设置表格导航属性
//        $objPHPExcel->getActiveSheet()->setCellValue('A1', '门店名称');
//        $objPHPExcel->getActiveSheet()->setCellValue('B1', '登录次数');
//        $objPHPExcel->getActiveSheet()->setCellValue('C1', '出售房源发布量');
//        $objPHPExcel->getActiveSheet()->setCellValue('D1', '出租房源发布量');
//        $objPHPExcel->getActiveSheet()->setCellValue('E1', '签约成交量');
//        $objPHPExcel->getActiveSheet()->setCellValue('F1', '门店地址');
//        $objPHPExcel->getActiveSheet()->setCellValue('G1', '日期');
//        //设置表格的值
//        for ($i = 2; $i <= count($list) + 1; $i++) {
//            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['agency_name']);
//            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['login_num']);
//            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['sell_num']);
//            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['rent_num']);
//            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['signing_num']);
//            $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['agency_addr']);
//            $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $start_time . " / " . $end_time);
//        }
//
//        $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
//        //$fileName = iconv("utf-8", "gb2312", $fileName);
//
//        $objPHPExcel->getActiveSheet()->setTitle('stat_agency');
//        $objPHPExcel->setActiveSheetIndex(0);
//
//        //header("Content-type: text/csv");//重要
//        // Redirect output to a client’s web browser (Excel5)
//        header('Content-Type: application/vnd.ms-excel;charset=utf-8');   //excel 2003
//        //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');   //excel 2007
//        //header('Content-Disposition: attachment;filename="求购客源.xls"');
//        header("Content-Disposition: attachment;filename=\"$fileName\"");
//        header('Cache-Control: max-age=0');
//        // If you're serving to IE 9, then the following may be needed
//        header('Cache-Control: max-age=1');
//
//        // If you're serving to IE over SSL, then the following may be needed
//        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
//        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
//        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
//        header('Pragma: public'); // HTTP/1.0
//
//        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
//        $objWriter->save('php://output');
//        exit;
//    }
}

/* End of file Broker_info.php */
/* Location: ./application/mls_admin/controllers/Broker_info.php */
