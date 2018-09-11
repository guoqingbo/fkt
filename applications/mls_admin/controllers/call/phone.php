<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 隐号拨打日报表
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class Phone extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('page_helper');
        $this->load->model('call_phone_model');
    }

    /**
     * 权限菜单列表页面
     */
    public function index()
    {
        $data = ['title' => '虚拟号码管理'];

        $param = $this->input->post(NULL, TRUE);

        //分页开始
        $data['district_num'] = $this->call_phone_model->count_by();
        if (isset($param['export']) && 1 == $param['export']) {//导出
            if (empty($data['district_num'])) {
                echo '没有数据可以导出，请重新筛选';
            } else {
                $db_city = $this->call_phone_model->get_db_city();

                $list = $db_city->from('call_phone')->order_by('id', 'desc')->get()->result_array();
                $this->export($list);
            }
            exit;
        }
        $data['pagesize'] = 10;//设定每一页显示的记录数
        $data['pages'] = $data['district_num'] ? ceil($data['district_num'] / $data['pagesize']) : 0;  //计算总页数
        $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
        $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
        $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

        if (empty($data['district_num'])) {
            $data['phone_list'] = array();
        } else {
            $data['phone_list'] = $this->call_phone_model->get_all_by('', $data['offset'], $data['pagesize']);
        }

        $this->load->view('call/phone/index', $data);
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
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '序号');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '虚拟号码');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '添加时间');

        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);//改变此处设置的长度数值
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        //$objPHPExcel->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

        //设置表格的值
        foreach ($data as $k => $v) {
            $i = $k + 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $v['id']);
            //$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, ' ' . $v['virtual_phone']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $v['virtual_phone'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, date('Y-m-d H:i:s', $v['create_time']));
        }

        $ua = $_SERVER["HTTP_USER_AGENT"];
        $fileName = "虚拟号码列表_" . date('Y-m-d') . ".xls";
        $fileEncodeName = urlencode("虚拟号码列表_" . date('Y-m-d') . ".xls");
        $objPHPExcel->getActiveSheet()->setTitle('虚拟号码');
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

    /**
     * 导入虚拟号码
     */
    public function import()
    {
        $data = array();
        $this->load->view('call/phone/import', $data);
    }

    public function importExcel()
    {
        //不为空
        if (!empty($_POST['sub'])) {
            $config['upload_path'] = str_replace("\\", "/", UPLOADS . DIRECTORY_SEPARATOR . 'temp');
            //目录不存在则创建目录
            if (!file_exists($config['upload_path'])) {
                $aryDirs = explode("/", substr($config['upload_path'], 0, strlen($config['upload_path'])));
                $strDir = "";
                foreach ($aryDirs as $value) {
                    $strDir .= $value . "/";
                    if (!@file_exists($strDir)) {
                        if (!@mkdir($strDir, 0777)) {
                            return "mkdirError";
                        }
                    }
                }
            }

            $config['file_name'] = date('YmdHis', time()) . rand(1000, 9999);
            $config['allowed_types'] = 'xlsx|xls';
            $config['max_size'] = "2000";
            $this->load->library('upload', $config);
            //打印成功或错误的信息
            if ($this->upload->do_upload('upfile')) {
                $data = array("upload_data" => $this->upload->data());
                $result = $this->community_read($data['upload_data']['file_name']);
                unlink($data['upload_data']['full_path']); //删除文件
            } else {
                $result = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css"></head><body style="background:#F2F2F2;"><p class="up_m_b_date_up" style="text-align: center;"><span class="up_e">上传失败</span>，请选择文件上传</p></body></html>';
            }
            echo $result;
        }
    }

    public function community_read($file_name)
    {
        ini_set("memory_limit", "1024M"); //excel文件大小
        //$filename = 'temp/xq.xls';//excel文件名
        $filename = UPLOADS . '/temp/' . $file_name;//excel文件名
        $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
        $objReader = IOFactory::createReaderForFile($filename);
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);//指定的文件
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow(); // 取得总行数
        //echo $highestRow."||1||";
        $valid_num = intval($highestRow);
        $highestColumn = $objWorksheet->getHighestColumn();// 取得总列数
        //echo $highestColumn."||2||";
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        //echo $highestColumnIndex."||3||";
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        //print_r($excelData);
        $sccessCount = 0;
        $time = time();
        for ($k = 2; $k <= $highestRow; $k++) {
            $phone = $excelData[$k][0];
            if (empty($phone)) {
                continue;
            }
            $is_exist = $this->call_phone_model->get_one_by(['virtual_phone' => $phone]);
            if (!$is_exist) {
                $phoneData = [
                    'virtual_phone' => $phone,
                    'create_time' => $time
                ];
                $this->call_phone_model->insert($phoneData);
                $sccessCount++;
            }
        }
        return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center;line-height: 34px;">' . '<span class="up_s">上传成功</span>，共上传' . $sccessCount . '条信息。</p>'
            . '</body></html>';
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
