<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 上传房源列表
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 */
class Index extends MY_Controller
{
    protected $pre_month_fee = 10;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('page_helper');
        $this->load->model('sell_house_upload_model');
    }

    /**
     * 权限菜单列表页面
     */
    public function index()
    {
        $data['title'] = '上传房源列表';

        $where = "";
        //post参数
        $param = $this->input->post(NULL, TRUE);
        $data['param'] = $param;
        if (isset($param['telephone']) && !empty($param['telephone'])) {
            $where = "telephone like '%" . $param['telephone'] . "%'";
        }
        if (isset($param['status']) && $param['status'] !== '') {
            if (!empty($where)) {
                $where .= " AND ";
            }
            $where .= "status = '" . $param['status'] . "'";
        }

        //分页开始
        $data['district_num'] = $this->sell_house_upload_model->count_by($where);
        if (isset($param['export']) && 1 == $param['export']) {//导出
            if (empty($data['district_num'])) {
                echo '没有数据可以导出，请重新筛选';
            } else {
                $db_city = $this->sell_house_upload_model->get_db_city();
                $houseList = $db_city->from('sell_house_upload')->select('*')->order_by('id', 'desc')->get()->result_array();
                $this->export($houseList);
            }
            exit;
        }
        $data['pagesize'] = 10;//设定每一页显示的记录数
        $data['pages'] = $data['district_num'] ? ceil($data['district_num'] / $data['pagesize']) : 0;  //计算总页数
        $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
        $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
        $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

        if (empty($data['district_num'])) {
            $data['house_list'] = array();
        } else {
            $data['house_list'] = $this->sell_house_upload_model->get_all_by($where, $data['offset'], $data['pagesize']);
        }

        $data['status'] = [
            0 => '未处理',
            1 => '上架',
            -1 => '无效'
        ];

        $this->load->view('userhouse/index/index', $data);
    }

    /**
     * 客服电话
     */
    public function serverphone()
    {
        $data = array('title' => '设置客服电话');
        $this->load->model('sell_house_upload_phone_model');
        $phone = $this->sell_house_upload_phone_model->getPhone();
        $data['phone'] = $phone;

        $submit_flag = $this->input->post('submit_flag');
        if ('edit' == $submit_flag) {
            $param = $this->input->post(NULL, TRUE);
            $serverphone = trim($param['phone']);
            if (empty($serverphone)) {
                $data['mess_error'] = '电话不能为空';
            } else {
                $db_city = $this->sell_house_upload_phone_model->get_db_city();

                if (empty($phone)) {
                    $data = array(
                        'phone' => $serverphone,
                        'create_time' => time(),
                    );
                    $db_city->insert('sell_house_upload_phone', $data);
                } else {
                    $update_arr = [
                        'phone' => $serverphone,
                        'update_time' => time(),
                    ];
                    $db_city->where('id', $phone['id']);
                    $db_city->update('sell_house_upload_phone', $update_arr);
                }
                header('Location: ' . MLS_ADMIN_URL . '/userhouse/index/index');
                exit;
            }
        }
        $this->load->helper('common_load_source_helper');
        $data['css'] = load_css('mls/css/v1.0/autocomplete.css');
        //需要加载的JS
        $data['js'] = load_js('common/third/jquery-ui-1.9.2.custom.min.js');
        $data['formUrl'] = MLS_ADMIN_URL . '/userhouse/index/serverphone';

        $this->load->view('userhouse/index/serverphone', $data);
    }

    /**
     * 取消
     */
    public function cancel()
    {
        $data = array('title' => '设置房源为无效');
        $get = $this->input->get(NULL, TRUE);
        if (!isset($get['id']) || empty($get['id'])) {
            echo '非法操作';
            exit;
        }
        $id = $get['id'];
        $house = $this->sell_house_upload_model->get_one_by(array('id' => $id));
        if (empty($house) || $house['status'] <> 0) {
            echo '非法操作';
            exit;
        }
        $data['house'] = $house;

        $submit_flag = $this->input->post('submit_flag');
        if ('edit' == $submit_flag) {
            $param = $this->input->post(NULL, TRUE);
            $remark = trim($param['remark']);
            if (empty($remark)) {
                $data['mess_error'] = '备注不能为空';
            } else {
                $db_city = $this->sell_house_upload_model->get_db_city();

                //$sql = 'update sell_house_upload set status = -1, remark = ' . trim($param['remark']) . ', update_time = ' . time() . ' where id = ' . $id;
                //$db_city->query($sql);

                $update_arr = [
                    'status' => -1,
                    'remark' => $remark,
                    'update_time' => time(),
                ];
                $db_city->where('id', $id);
                $db_city->update('sell_house_upload', $update_arr);
                $up_num = $db_city->affected_rows();
                if ($up_num > 0) {
                    header('Location: ' . MLS_ADMIN_URL . '/userhouse/index/index');
                    exit;
                } else {
                    $data['mess_error'] = '提交失败';
                }
            }
        }
        $this->load->helper('common_load_source_helper');
        $data['css'] = load_css('mls/css/v1.0/autocomplete.css');
        //需要加载的JS
        $data['js'] = load_js('common/third/jquery-ui-1.9.2.custom.min.js');
        $data['formUrl'] = MLS_ADMIN_URL . '/userhouse/index/cancel?id=' . $house['id'];

        $this->load->view('userhouse/index/cancel', $data);
    }

    /**
     * 编辑
     */
    public function edit()
    {
        //模板使用数据
        $data = array();
        $get = $this->input->get(NULL, TRUE);
        if (!isset($get['id']) || empty($get['id'])) {
            echo '非法操作';
            exit;
        }
        $id = $get['id'];
        $house = $this->sell_house_upload_model->get_one_by(array('id' => $id));
        if (empty($house) || $house['status'] <> 0) {
            echo '非法操作';
            exit;
        }
        $data['house'] = $house;

        $data['is_property_publish'] = 1;
        //是否开启合作中心
        $data['open_cooperate'] = 1;
        //是否开启合作审核
        $data['check_cooperate'] = 1;
        //新增房源是否默认私盘
        $data['is_house_private'] = 1;
        //房源必须同步
        $data['is_fang100_insert'] = 1;

        //加载出售基本配置MODEL
        $this->load->model('house_config_model');

        //获取出售信息基本配置资料
        $house_config = $this->house_config_model->get_config();
        //基本信息‘状态’数据处理
        if (!empty($house_config['status']) && is_array($house_config['status'])) {
            foreach ($house_config['status'] as $k => $v) {
                if ('暂不售（租）' == $v) {
                    $house_config['status'][$k] = '暂不售';
                }
            }
        }
        $data['config'] = $house_config;

        //加载区属模型类
        $this->load->model('district_model');
        //获取区属
        $data['district'] = $this->district_model->get_district();

        $this->load->model('sell_house_field_agency_model');
        $db_city = $this->sell_house_field_agency_model->get_db_city();
        $defaultList = $db_city->from('sell_house_field_agency')->where("agency_id = 0")->order_by('id', 'asc')->get()->result_array();
        $lists = [];
        foreach ($defaultList as $v) {
            $lists[$v['sell_type']][$v['field_name']] = $v;
        }
        ksort($lists);
        //print_r($lists);
        $data['lists'] = json_encode($lists);

        $this->load->helper('common_load_source_helper');
        $data['css'] = load_css('mls/css/v1.0/base.css,'
            . 'mls/third/iconfont/iconfont.css,'
            . 'mls/css/v1.0/house_manage.css,'
            . 'mls/css/v1.0/myStyle.css'
            . 'mls/css/v1.0/autocomplete.css');
        //需要加载的JS
        $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
            . 'common/third/swf/swfupload.js,'
            . 'mls/js/v1.0/uploadpic2.js,'
            . 'mls/js/v1.0/cooperate_common.js,'
            . 'common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls/js/v1.0/group_publish.js'
            . 'mls/js/v1.0/openWin.js,mls/js/v1.0/jquery.validate.min.js,'
            . 'mls/js/v1.0/verification.js,mls/js/v1.0/radio_checkbox_mod2.js,mls/js/v1.0/backspace.js,mls/js/v1.0/house_title_template.js,mls/js/v1.0/house_content_template.js');

        //加载发布页面模板
        $this->load->view('userhouse/index/edit', $data);
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
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '用户联系方式');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '称呼');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '小区名称');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', '发布状态');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', '发布时间');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', '处理时间');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', '备注');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);

        $status = [
            0 => '未处理',
            1 => '上架',
            -1 => '无效'
        ];
        //设置表格的值
        foreach ($data as $k => $v) {
            $i = $k + 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $v['telephone']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $v['user_name']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $v['block_name']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, isset($status[$v['status']]) ? $status[$v['status']] : '');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, date('Y-m-d', $v['create_time']));
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, empty($v['update_time']) ? '' : date('Y-m-d', $v['update_time']));
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $v['remark']);
            $objPHPExcel->getActiveSheet()->getStyle('G' . $i)->getAlignment()->setWrapText(true);
        }

        $ua = $_SERVER["HTTP_USER_AGENT"];
        $fileName = '上传房源列表_' . date('Y-m-d') . '.xls';
        $fileEncodeName = urlencode('上传房源列表_' . date('Y-m-d') . '.xls');
        $objPHPExcel->getActiveSheet()->setTitle('上传房源列表');
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

    //发布房源
    public function add()
    {
        $this->_add();
    }

    /**
     * 添加出售信息
     *
     * @access  public
     * @param  void
     * @return  void
     */
    private function _add()
    {
        $sell_house_upload_id = $this->input->post('sell_house_upload_id', TRUE);
        $uploadHouse = $this->sell_house_upload_model->get_one_by(array('id' => $sell_house_upload_id));
        if (empty($uploadHouse) || $uploadHouse['status'] <> 0) {
            $result = array(
                'error' => '该上传房源已处理，请勿重复处理'
            );
            echo json_encode($result);
            exit;
        }
        //加载客户MODEL
        $this->load->model('sell_house_model');
        $this->load->model('operate_log_model');
        $this->load->model('broker_info_model');

        //添加出售信息
        $datainfo = array();

        $broker_id = $this->input->post('broker_id', TRUE);
        if (empty($broker_id)) {
            $result = array(
                'error' => '请选择要分配的经纪人'
            );
            echo json_encode($result);
            exit;
        }
        $broker_info = $this->broker_info_model->get_one_by(array('id' => $broker_id));
        $broker_name = strip_tags($broker_info['truename']);
        $agency_id = intval($broker_info['agency_id']);
        $company_id = intval($broker_info['company_id']);
        $credit_score = '';
        $level_score = '';
        //获取当前经济人所在门店的基本设置信息
        $company_basic_data = [];
        if (is_int($agency_id) && !empty($agency_id)) {
            $this->load->model('agency_basic_setting_model');
            $company_basic_data = $this->agency_basic_setting_model->get_data_by_agency_id($agency_id);
            if (is_array($company_basic_data) && !empty($company_basic_data)) {
                $company_basic_data = $company_basic_data[0];
            } else {
                $company_basic_default_data = $this->agency_basic_setting_model->get_default_data();
                if (is_array($company_basic_default_data) && !empty($company_basic_default_data)) {
                    $company_basic_data = $company_basic_default_data[0];
                }
            }
        } elseif (is_int($company_id) && !empty($company_id)) {
            $this->load->model('agency_basic_setting_model');
            $company_basic_data = $this->agency_basic_setting_model->get_data_by_company_id($company_id);
            if (is_array($company_basic_data) && !empty($company_basic_data)) {
                $company_basic_data = $company_basic_data[0];
            } else {
                $company_basic_default_data = $this->agency_basic_setting_model->get_default_data();
                if (is_array($company_basic_default_data) && !empty($company_basic_default_data)) {
                    $company_basic_data = $company_basic_default_data[0];
                }
            }
        }
        if (is_full_array($company_basic_data)) {
            $house_customer_system = intval($company_basic_data['house_customer_system']);
            $sell_house_private_num = intval($company_basic_data['sell_house_private_num']);
        } else {
            $house_customer_system = $sell_house_private_num = 0;
        }

        if ($broker_id == 0) {
            //遗留 退出系统
        }

        $house_id = $this->input->post('house_id', TRUE);
        if (empty($house_id)) {
            $datainfo['broker_id'] = $broker_id;
            $datainfo['broker_name'] = $broker_name;
            $datainfo['agency_id'] = $agency_id;
            $datainfo['company_id'] = $company_id;
            $datainfo['createtime'] = time();
            $datainfo['ip'] = get_ip();
        }

        $action_type = $this->input->post('action_type', TRUE);//区分发布、修改页面
        $datainfo['sell_type'] = $this->input->post('sell_type', TRUE);
        $datainfo['block_name'] = $this->input->post('block_name', TRUE);
        $datainfo['block_id'] = $this->input->post('block_id', TRUE);
        $datainfo['district_id'] = $this->input->post('district_id', TRUE);
        $datainfo['street_id'] = $this->input->post('street_id', TRUE);
        $datainfo['address'] = $this->input->post('address', TRUE);
        $datainfo['dong'] = $this->input->post('dong', TRUE);
        $datainfo['unit'] = $this->input->post('unit', TRUE);
        $datainfo['door'] = $this->input->post('door', TRUE);
        $datainfo['owner'] = $this->input->post('owner', TRUE);
        $datainfo['idcare'] = $this->input->post('idcare', TRUE);
        $datainfo['telno1'] = $this->input->post('telno1', TRUE);
        $datainfo['telno2'] = $this->input->post('telno2', TRUE);
        $datainfo['telno3'] = $this->input->post('telno3', TRUE);
        $datainfo['house_grade'] = $this->input->post('house_grade', TRUE);
        if ('2' == $datainfo['house_grade']) {
            $datainfo['is_sticky'] = 1;
        } else {
            $datainfo['is_sticky'] = 0;
        }
        $datainfo['house_structure'] = $this->input->post('house_structure', TRUE);
        $datainfo['read_time'] = $this->input->post('read_time', TRUE);
        //$datainfo['proof'] = $this->input->post('proof' , TRUE);
        //$datainfo['mound_num'] = $this->input->post('mound_num' , TRUE);
        //$datainfo['record_num'] = $this->input->post('record_num' , TRUE);
        $datainfo['status'] = $this->input->post('status', TRUE);
        $datainfo['nature'] = $this->input->post('nature', TRUE);
        //酒店式公寓和别墅相同处理逻辑
        if ($datainfo['sell_type'] > 2 && $datainfo['sell_type'] != 8) {
            $datainfo['room'] = 0;
            $datainfo['hall'] = 0;
            $datainfo['toilet'] = 0;
        } else {
            $datainfo['room'] = $this->input->post('room', TRUE);
            $datainfo['hall'] = $this->input->post('hall', TRUE);
            $datainfo['toilet'] = $this->input->post('toilet', TRUE);
        }
        $datainfo['kitchen'] = $this->input->post('kitchen', TRUE);
        $datainfo['balcony'] = $this->input->post('balcony', TRUE);
        $datainfo['isshare'] = $this->input->post('isshare', TRUE);
        $datainfo['is_outside'] = $this->input->post('is_outside', TRUE);
        if ('1' == $datainfo['is_outside']) {
            $datainfo['is_outside_time'] = time();
        }

        $datainfo['isshare_friend'] = $this->input->post('isshare_friend', TRUE);
        $isshare_back = $this->input->post('isshare_back', TRUE);
        $reward_type = $this->input->post('reward_type', TRUE);//奖励方式

        //修改页面
        if ('modify' == $action_type) {
            if (intval($isshare_back) > 0) {
                $datainfo['isshare'] = $isshare_back;
            }
        } else if ('add' == $action_type) {
            //未开启合作审核，选择设置奖金，合作状态改成3
            if ('1' == $datainfo['isshare'] && '2' == $reward_type) {
                $datainfo['isshare'] = 3;
            }
        }
        //设置合作时间
        if ('1' == $datainfo['isshare'] || '2' == $datainfo['isshare'] || '3' == $datainfo['isshare']) {
            $datainfo['set_share_time'] = time();
        }

        if (isset($datainfo['isshare']) && !empty($datainfo['isshare'])) {
            if ('2' == $reward_type) {
                $datainfo['reward_type'] = 2;
                $datainfo['cooperate_reward'] = $this->input->post('shangjin', TRUE);
                //设置悬赏经纪人id
                $datainfo['set_reward_broker_id'] = $broker_id;
            } else if ('1' == $reward_type) {
                $datainfo['reward_type'] = 1;
                $datainfo['commission_ratio'] = $this->input->post('commission_ratio', TRUE);
                $datainfo['cooperate_reward'] = 0;
                $datainfo['set_reward_broker_id'] = 0;
            }
        } else {
            $datainfo['cooperate_reward'] = 0;
            $datainfo['set_reward_broker_id'] = 0;
        }

        $datainfo['is_publish'] = $this->input->post('is_publish', TRUE);
        $datainfo['floor_type'] = $this->input->post('floor_type', TRUE);
        $datainfo['floor'] = $this->input->post('floor', TRUE);
        $datainfo['title'] = $this->input->post('title', TRUE);
        $datainfo['bewrite'] = $this->input->post('bewrite');
        if ($datainfo['floor_type'] == 2) {
            $datainfo['floor'] = $this->input->post('floor2', TRUE);
        }
        $datainfo['subfloor'] = $this->input->post('subfloor', TRUE);
        $datainfo['totalfloor'] = $this->input->post('totalfloor', TRUE);
        if (!empty($datainfo['totalfloor'])) {
            $datainfo['floor_scale'] = $datainfo['floor'] / $datainfo['totalfloor'];
        }
        //酒店式公寓和别墅相同处理逻辑
        if ($datainfo['sell_type'] < 5 || $datainfo['sell_type'] == 8) {
            $datainfo['forward'] = $this->input->post('forward', TRUE);
            $datainfo['fitment'] = $this->input->post('fitment', TRUE);
        }
        $datainfo['buildyear'] = $this->input->post('buildyear', TRUE);
        $datainfo['buildarea'] = $this->input->post('buildarea', TRUE);
        $datainfo['usage_area'] = $this->input->post('usage_area', TRUE);

        $datainfo['loft_area'] = $this->input->post('loft_area', TRUE);
        $datainfo['garage_area'] = $this->input->post('garage_area', TRUE);
        $datainfo['price'] = $this->input->post('price', TRUE);
        $datainfo['lowprice'] = $this->input->post('lowprice', TRUE);
        $datainfo['avgprice'] = $this->input->post('avgprice', TRUE);
        $datainfo['taxes'] = $this->input->post('taxes', TRUE);
        $datainfo['keys'] = $this->input->post('keys', TRUE);
        if ($datainfo['keys']) {
            $datainfo['key_number'] = $this->input->post('key_number', TRUE);
        }
        //$datainfo['pact'] = $this->input->post('pact' , TRUE);
        $datainfo['entrust'] = $this->input->post('entrust', TRUE);
        $datainfo['house_type'] = $this->input->post('house_type', TRUE);
        //$datainfo['struct'] = $this->input->post('struct' , TRUE);
        //$datainfo['pay_type'] = $this->input->post('pay_type' , TRUE);
        $datainfo['property'] = $this->input->post('property', TRUE);
        //$datainfo['rebate_type'] = $this->input->post('rebate_type' , TRUE);
        //$datainfo['look'] = $this->input->post('look' , TRUE);
        $datainfo['current'] = $this->input->post('current', TRUE);
        $datainfo['infofrom'] = $this->input->post('infofrom', TRUE);
        //$datainfo['paperwork'] = $this->input->post('paperwork' , TRUE);
        $equipment = $this->input->post('equipment', TRUE);
        if ($equipment) {
            $datainfo['equipment'] = implode(',', $equipment);
        } else {
            $datainfo['equipment'] = '';
        }
        $setting = $this->input->post('setting', TRUE);
        if ($setting) {
            $datainfo['setting'] = implode(',', $setting);
        } else {
            $datainfo['setting'] = '';
        }
        //标签
        $sell_tag = $this->input->post('sell_tag', TRUE);
        if ($sell_tag) {
            $datainfo['sell_tag'] = implode(',', $sell_tag);
        } else {
            $datainfo['sell_tag'] = '';
        }
        $datainfo['strata_fee'] = $this->input->post('strata_fee', TRUE);
        $datainfo['costs_type'] = $this->input->post('costs_type', TRUE);
        $datainfo['pay_date'] = $this->input->post('pay_date', TRUE);
        $datainfo['remark'] = $this->input->post('remark', TRUE);
        $datainfo['updatetime'] = time();

        //别墅
        //酒店式公寓和别墅相同处理逻辑
        if ($datainfo['sell_type'] == 2 || $datainfo['sell_type'] == 8) {
            $datainfo['villa_type'] = $this->input->post('villa_type', TRUE);
            $datainfo['hall_struct'] = $this->input->post('hall_struct', TRUE);
            $datainfo['park_num'] = $this->input->post('park_num', TRUE);
            $datainfo['garden_area'] = $this->input->post('garden_area', TRUE);
            $datainfo['floor_area'] = $this->input->post('floor_area', TRUE);
            $datainfo['light_type'] = $this->input->post('light_type', TRUE);
        }

        //商铺
        if ($datainfo['sell_type'] == 3) {
            $datainfo['shop_type'] = $this->input->post('shop_type', TRUE);
            $shop_trade = $this->input->post('shop_trade', TRUE);
            if ($shop_trade) {
                $datainfo['shop_trade'] = implode(',', $shop_trade);
            } else {
                $datainfo['shop_trade'] = '';
            }
            $datainfo['division'] = $this->input->post('division', TRUE);
        }

        //写字楼
        if ($datainfo['sell_type'] == 4) {
            $datainfo['division'] = $this->input->post('division2', TRUE);
            $datainfo['office_trade'] = $this->input->post('office_trade', TRUE);
            $datainfo['office_type'] = $this->input->post('office_type', TRUE);
        }

        $block_id = $datainfo['block_id'];
        $door = $datainfo['door'];
        $unit = $datainfo['unit'];
        $dong = $datainfo['dong'];

        //录入房源唯一性验证
        if (isset($datainfo['sell_type']) && intval($datainfo['sell_type']) < 5) {
            $house_num = $this->check_house($block_id, $door, $unit, $dong);
        } else {
            $house_num = 0;
        }

        $housee_id = '';
        $house_add_arr = array();

        //获取当前经纪人发布悬赏房源的数量
        $reward_where_cond = 'set_reward_broker_id = "' . $broker_id . '"' . ' and isshare !=0 and status = 1 and cooperate_reward > 0';
        $cooperate_reward_num = $this->sell_house_model->get_housenum_by_cond($reward_where_cond);

        if (empty($house_id) && $house_num == 0) {
            $house_num_check = true;

            //基本设置，房客源制判断
            $house_private_check = true;
            //公盘私客制
            if (2 == $house_customer_system) {
                if ('1' == $datainfo['nature']) {
                    $house_private_check = false;
                    $house_private_check_text = '当前门店基本设置为公盘私客制';
                }
            } else if (3 == $house_customer_system) {
                //公盘制 获得当前经纪人的私盘数量
                $private_where_cond = 'broker_id = "' . $broker_id . '"' . ' and status = 1 and nature = 1';
                $private_num = $this->sell_house_model->get_housenum_by_cond($private_where_cond);
                if ('1' == $datainfo['nature'] && $private_num >= $sell_house_private_num) {
                    $house_private_check = false;
                    $house_private_check_text = '当前门店基本设置为公盘制';
                }
            } else {
                $house_private_check = true;
            }

            //发布悬赏房源个数限制
            if (isset($datainfo['isshare']) && 3 == $datainfo['isshare'] && '2' == $reward_type) {
                if (is_int($cooperate_reward_num) && $cooperate_reward_num < 5) {
                    $is_reward = true;
                } else {
                    $is_reward = false;
                }
            } else {
                $is_reward = true;
            }
            $is_reward = true;//去除悬赏限制提示

            //委托协议书、卖家身份证、房产证
            $pics['p_filename3'] = $this->input->post('p_filename3', TRUE);
            $pics['p_filename4'] = $this->input->post('p_filename4', TRUE);
            $pics['p_filename5'] = $this->input->post('p_filename5', TRUE);

            //根据合作资料，判断是否发送审核
            //if(is_full_array($pics['p_filename3']) && is_full_array($pics['p_filename4'])){
            //    $datainfo['cooperate_check'] = 2;
            //}

            $coo_ziliao_check_3 = true;
            if (intval($datainfo['isshare']) > 0) {
                $coo_ziliao_check_1 = true;
                $coo_ziliao_check_2 = true;
                //委托协议书、卖家身份证、房产证验证 $coo_ziliao_check_1：悬赏合作必须三证齐全。$coo_ziliao_check_2：佣金悬赏必须传两证或者三证齐全或者不传。
                if ('2' == $reward_type) {
                    $coo_ziliao_check_1 = true;
                    $datainfo['isshare'] = 1;
                } else if ('1' == $reward_type) {
                    $datainfo['house_degree'] = 1;
                    if (is_full_array($pics['p_filename4']) && is_full_array($pics['p_filename5'])) {
                        $coo_ziliao_check_2 = true;
                        $datainfo['cooperate_check'] = 2;
                    } else {
                        if (empty($pics['p_filename3']) && empty($pics['p_filename4']) && empty($pics['p_filename5'])) {
                            $coo_ziliao_check_2 = true;
                        } else {
                            $coo_ziliao_check_2 = false;
                        }
                    }
                }
            } else {
                $coo_ziliao_check_1 = true;
                $coo_ziliao_check_2 = true;
            }

            if ($is_reward && $coo_ziliao_check_1 && $coo_ziliao_check_2 && $house_private_check) {
                $housee_id = $this->sell_house_model->add_sell_house_info($datainfo);
            }

            if ($housee_id > 0) {
                //操作日志
                $add_log_param = array();
                $add_log_param['company_id'] = $broker_info['company_id'];
                $add_log_param['agency_id'] = $broker_info['agency_id'];
                $add_log_param['broker_id'] = $broker_id;
                $add_log_param['broker_name'] = $broker_info['truename'];
                $add_log_param['type'] = 2;
                $add_log_param['text'] = '出售房源 ' . 'CS' . $housee_id;
                $add_log_param['from_system'] = 1;
                $add_log_param['from_ip'] = get_ip();
                $add_log_param['mac_ip'] = '127.0.0.1';
                $add_log_param['from_host_name'] = '127.0.0.1';
                $add_log_param['hardware_num'] = '测试硬件序列号';
                $add_log_param['time'] = time();

                $this->operate_log_model->add_operate_log($add_log_param);

                $msg = '房源录入成功.';
                //添加钥匙
                if ($datainfo['keys'] && $datainfo['key_number']) {
                    $this->add_key($housee_id, $datainfo['key_number'], 'add');
                    //出售房源录入成功记录工作统计日志-钥匙提交
                    $this->info_count($housee_id, 6);
                }

                //设置合作添加佣金比例
                if ($datainfo['isshare'] == 1) {
                    $a_ratio = $this->input->post('a_ratio', TRUE);//甲方佣金分成比例
                    $b_ratio = $this->input->post('b_ratio', TRUE);//已方佣金分成比例
                    $buyer_ratio = $this->input->post('buyer_ratio', TRUE);//买方支付佣金比例
                    $seller_ratio = $this->input->post('seller_ratio', TRUE);//卖方支付佣金比例

                    $this->load->model('sell_house_share_ratio_model');
                    $this->sell_house_share_ratio_model->add_house_cooperate_ratio($housee_id,
                        $seller_ratio, $buyer_ratio, $a_ratio, $b_ratio);
                    //增加积分
                    $this->load->model('api_broker_credit_model');
                    $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_id));
                    $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $housee_id), 1);
                    //判断积分是否增加成功
                    if (is_full_array($credit_result) && $credit_result['status'] == 1) {
                        $credit_score += $credit_result['score'];
                    }
                    //增加等级分值
                    $this->load->model('api_broker_level_model');
                    $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_id));
                    $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $housee_id), 1);
                    //判断成长值是否增加成功
                    if (is_full_array($level_result) && $level_result['status'] == 1) {
                        $level_score += $level_result['score'];
                    }
                }
                $need__info = $this->user_arr;
                //添加房源日志录入
                $this->load->model('follow_model');
                $needarr = array();
                $needarr['broker_id'] = $broker_id;
                $needarr['type'] = 1;
                $needarr['agency_id'] = $need__info['agency_id'];//门店ID
                $needarr['company_id'] = $need__info['company_id'];//总公司id
                $needarr['house_id'] = $housee_id;
                $bool = $this->follow_model->house_inster($needarr);
                //判断该房源是否设置了合作
                if ('1' == $datainfo['isshare'] || '2' == $datainfo['isshare']) {
                    $follow_text = '';
                    if ('1' == $datainfo['isshare']) {
                        $follow_text = '是否合作:否>>是';
                    } else if ('2' == $datainfo['isshare']) {
                        $follow_text = '是否合作:否>>审核中';
                    }
                    $needarrt = array();
                    $needarrt['broker_id'] = $broker_id;
                    $needarrt['type'] = 1;
                    $needarrt['agency_id'] = $need__info['agency_id'];//门店ID
                    $needarrt['company_id'] = $need__info['company_id'];//总公司id
                    $needarrt['house_id'] = $housee_id;
                    $needarrt['text'] = $follow_text;
                    $boolt = $this->follow_model->house_inster_share($needarrt);
                }

                $url_manage = '/sell/lists/';
                $page_text = '发布成功';

                $cid = $this->input->post('cid', TRUE);
                if ($cid > 0) {
                    //$this->load->model('collections_model_new');
                    $this->collections_model_new->change_house_status_byid($cid, $broker_id, 'sell_house_collect');
                    $this->collections_model_new->add_sell_house_sub($housee_id, $cid);
                }

                //出售房源录入成功记录工作统计日志
                $this->info_count($housee_id, 1);
            }
            $house_add_arr['modify'] = 0;
        } else {
            $house_num_check = false;
            $url_manage = '/sell/publish/';
            $page_text = '发布失败,该房源已经存在';
        }

        $result = '';
        $sell_backinfo = array();
        $sell_dataifno = array();

        /*if (!empty($house_id)) {
            //修改房源唯一性验证
            $house_check = $this->check_house_modify($block_id, $door, $unit, $dong, $house_id);

            if ($house_check) {
                $house_num_check = true;
                $this->sell_house_model->set_search_fields(array());
                $this->sell_house_model->set_id($house_id);
                $sell_backinfo = $this->sell_house_model->get_info_by_id();//修改前的信息
                //判断原来的是否为合作
                if ('1' == $sell_backinfo['isshare'] || '2' == $sell_backinfo['isshare']) {
                    $datainfo['set_share_time'] = $sell_backinfo['set_share_time'];
                }

                //基本设置，房客源制判断
                $house_private_check = true;
                //公盘私客制
                if (2 == $house_customer_system) {
                    if ('2' == $sell_backinfo['nature'] && '1' == $datainfo['nature']) {
                        $house_private_check = false;
                        $house_private_check_text = '当前门店基本设置为公盘私客制';
                    }
                } else if (3 == $house_customer_system) {
                    //公盘制 获得当前经纪人的私盘数量
                    $private_where_cond = 'broker_id = "' . $broker_id . '"' . ' and status = 1 and nature = 1';
                    $private_num = $this->sell_house_model->get_housenum_by_cond($private_where_cond);
                    if ('2' == $sell_backinfo['nature'] && '1' == $datainfo['nature'] && $private_num >= $sell_house_private_num) {
                        $house_private_check = false;
                        $house_private_check_text = '当前门店基本设置为公盘制';
                    }
                } else {
                    $house_private_check = true;
                }

                $is_reward = true;  //发布悬赏房源个数限制
                $is_reward_plus = true; //悬赏增幅限制

                if ('2' == $reward_type && $datainfo['cooperate_reward'] > 0 && $sell_backinfo['cooperate_reward'] != $datainfo['cooperate_reward']) {
                    //旧值是否为空
                    if (empty($sell_backinfo['cooperate_reward'])) {
                        if (is_int($cooperate_reward_num) && $cooperate_reward_num < 5) {
                            $is_reward = true;
                        } else {
                            $is_reward = false;
                        }
                    } else {
                        $reward_add = intval($datainfo['cooperate_reward']) - intval($sell_backinfo['cooperate_reward']);
                        if (is_int($reward_add) && $reward_add < 100) {
                            $is_reward_plus = false;
                        }
                    }
                }
                $is_reward = true;  //发布悬赏房源个数限制去除

                $this->load->model('pic_model');
                $data['picinfo'] = $this->pic_model->find_house_pic_by_ids($sell_backinfo['pic_tbl'], $sell_backinfo['pic_ids']);
                $id_str = trim($sell_backinfo['pic_ids'], ',');
                $arr = explode(',', $id_str);
                $old_pic_inside_room = array();//室内图+户型图
                $picinfo3 = array();#委托协议书
                $picinfo4 = array();#身份证
                $picinfo5 = array();#房产证

                //房源图片数据重构
                foreach ($arr as $k => $v) {
                    if (is_full_array($data['picinfo'])) {
                        foreach ($data['picinfo'] as $key => $value) {
                            if ($value['id'] == $v && ($value['type'] == 1 || $value['type'] == 2)) {
                                $old_pic_inside_room[] = $value['url'];
                            } else if ($value['id'] == $v && $value['type'] == 3) {
                                $picinfo3[] = $value;
                            } else if ($value['id'] == $v && $value['type'] == 4) {
                                $picinfo4[] = $value;
                            } else if ($value['id'] == $v && $value['type'] == 5) {
                                $picinfo5[] = $value;
                            }
                        }
                    }
                }
                //委托协议书、卖家身份证
                $pics['p_filename3'] = $this->input->post('p_filename3', TRUE);
                $pics['p_filename4'] = $this->input->post('p_filename4', TRUE);
                $pics['p_filename5'] = $this->input->post('p_filename5', TRUE);
                $pic3_back_str_0 = '';
                $pic3_back_str_1 = '';
                $pic3_back_str_2 = '';

                $pic4_back_str = '';
                $pic5_back_str = '';
                if (is_full_array($picinfo3[0])) {
                    $pic3_back_str_0 = $picinfo3[0]['url'];
                }
                if (is_full_array($picinfo3[1])) {
                    $pic3_back_str_1 = $picinfo3[1]['url'];
                }
                if (is_full_array($picinfo3[2])) {
                    $pic3_back_str_2 = $picinfo3[2]['url'];
                }

                if (is_full_array($picinfo4[0])) {
                    $pic4_back_str = $picinfo4[0]['url'];
                }
                if (is_full_array($picinfo5[0])) {
                    $pic5_back_str = $picinfo5[0]['url'];
                }
                if (is_full_array($pics['p_filename3'])) {
                    $pic3_str_0 = $pics['p_filename3'][0];
                    $pic3_str_1 = '';
                    $pic3_str_2 = '';
                    if (isset($pics['p_filename3'][1]) && !empty($pics['p_filename3'][1])) {
                        $pic3_str_1 = $pics['p_filename3'][1];
                    }
                    if (isset($pics['p_filename3'][2]) && !empty($pics['p_filename3'][2])) {
                        $pic3_str_2 = $pics['p_filename3'][2];
                    }
                }
                if (is_full_array($pics['p_filename4'])) {
                    $pic4_str = $pics['p_filename4'][0];
                }
                if (is_full_array($pics['p_filename5'])) {
                    $pic5_str = $pics['p_filename5'][0];
                }

                //根据合作资料，判断是否发送审核
                if (($pic3_back_str_0 != $pic3_str_0) || ($pic3_back_str_1 != $pic3_str_1) || ($pic3_back_str_2 != $pic3_str_2) || ($pic4_back_str != $pic4_str) || ($pic5_back_str != $pic5_str)) {
                    $is_pic_change = true;
                    $datainfo['cooperate_check'] = 2;
                    if (1 == $datainfo['isshare'] && '2' == $reward_type) {
                        $datainfo['isshare'] = 3;
                    }
                } else {
                    $is_pic_change = false;
                }

                //奖金方式，合作状态从否变成是、资料不变，提示重新上传资料。
                if ('0' == $sell_backinfo['isshare'] && intval($datainfo['isshare']) > 0 && 2 == intval($sell_backinfo['reward_type']) && 2 == intval($datainfo['reward_type']) && !$is_pic_change) {
                    $coo_ziliao_check_3 = false;
                } else {
                    $coo_ziliao_check_3 = true;
                }

                if (intval($datainfo['isshare']) > 0) {
                    $coo_ziliao_check_1 = true;
                    $coo_ziliao_check_2 = true;
                    //委托协议书、卖家身份证、房产证验证 $coo_ziliao_check_1：悬赏合作必须三证齐全。$coo_ziliao_check_2：佣金悬赏必须传两证或者三证齐全。
                    if ('2' == $reward_type) {
                        if (is_full_array($pics['p_filename3']) && is_full_array($pics['p_filename4']) && is_full_array($pics['p_filename5'])) {
                            $coo_ziliao_check_1 = true;
                        } else {
                            $coo_ziliao_check_1 = false;
                        }
                    } else if ('1' == $reward_type) {
                        if (is_full_array($pics['p_filename4']) && is_full_array($pics['p_filename5'])) {
                            $coo_ziliao_check_2 = true;
                        } else {
                            if (empty($pics['p_filename3']) && empty($pics['p_filename4']) && empty($pics['p_filename5'])) {
                                $coo_ziliao_check_2 = true;
                            } else {
                                $coo_ziliao_check_2 = false;
                            }
                        }
                        //审核失败状态，未修改资料图片状态，验证不通过
                        //if('4'==$sell_backinfo['cooperate_check'] && $pic3_back_str==$pic3_str && //$pic4_back_str==$pic4_str){
                        //    $coo_ziliao_check_1 = false;
                        //}
                    }
                } else {
                    $coo_ziliao_check_1 = true;
                    $coo_ziliao_check_2 = true;
                }

                //价格变动改变状态
                if ($datainfo['price'] && $sell_backinfo['price'] != $datainfo['price']) {
                    if ($sell_backinfo['price'] < $datainfo['price']) {
                        $datainfo['price_change'] = 1;
                    } else {
                        $datainfo['price_change'] = 2;
                    }
                }

                if ($is_reward && $is_reward_plus && $coo_ziliao_check_1 && $coo_ziliao_check_2 && $coo_ziliao_check_3 && $house_private_check) {
                    $old_bewrite = trim(strip_tags($sell_backinfo['bewrite']));
                    //正则匹配，去掉‘&nbsp;’和空格
                    $pattern = '/(\s|&nbsp;)+/';
                    $old_bewrite2 = preg_replace($pattern, '', $old_bewrite);
                    if (!empty($old_bewrite2)) {
                        $sell_backinfo['bewrite'] = mb_substr($old_bewrite2, 0, 20) . '...';
                    } else {
                        $sell_backinfo['bewrite'] = '';
                    }
                    $result = $this->sell_house_model->update_info_by_id($datainfo);
                    $sell_dataifno = $this->sell_house_model->get_info_by_id();//修改过后信息

                    $new_bewrite = trim(strip_tags($sell_dataifno['bewrite']));
                    //正则匹配，去掉‘&nbsp;’和空格
                    $pattern = '/(\s|&nbsp;)+/';
                    $new_bewrite_2 = preg_replace($pattern, '', $new_bewrite);
                    if (!empty($new_bewrite_2)) {
                        $sell_dataifno['bewrite'] = mb_substr($new_bewrite_2, 0, 20) . '...';
                    } else {
                        $sell_dataifno['bewrite'] = '';
                    }

                    //添加钥匙
                    if (!$sell_backinfo['key_number'] && $sell_dataifno['keys'] && $sell_dataifno['key_number']) {
                        $this->add_key($house_id, $sell_dataifno['key_number'], 'update');
                        //出售房源钥匙提交记录工作统计日志
                        $this->info_count($house_id, 6);
                    }

                    //从有效状态改成其它状态，终止房源合作
                    $current_status = $this->input->post('current_status', TRUE);
                    if ($current_status == 1 && $datainfo['status'] != 1) {
                        $stop_reason = '';

                        switch ($datainfo['status']) {
                            case '2':
                                $stop_reason = 'reserve_house';
                                break;
                            case '3':
                                $stop_reason = 'deal_house';
                                break;
                            case '4':
                                $stop_reason = 'invalid_house';
                                break;
                        }

                        $this->load->model('cooperate_model');
                        $this->cooperate_model->stop_cooperate($house_id, 'sell', $stop_reason);
                    }
                    //终止房源合作

                    $msg = '房源修改成功！';
                    $aa = '';
                    //设置合作添加佣金比例
                    if ($datainfo['isshare'] == 1) {
                        $a_ratio = $this->input->post('a_ratio', TRUE);//甲方佣金分成比例
                        $b_ratio = $this->input->post('b_ratio', TRUE);//已方佣金分成比例
                        $buyer_ratio = $this->input->post('buyer_ratio', TRUE);//买方支付佣金比例
                        $seller_ratio = $this->input->post('seller_ratio', TRUE);//卖方支付佣金比例

                        $this->load->model('sell_house_share_ratio_model');
                        $sell_backinfo_ratio = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($house_id);
                        $sell_backinfo['a_ratio'] = $sell_backinfo_ratio['a_ratio'];
                        $sell_backinfo['b_ratio'] = $sell_backinfo_ratio['b_ratio'];
                        $sell_backinfo['buyer_ratio'] = $sell_backinfo_ratio['buyer_ratio'];
                        $sell_backinfo['seller_ratio'] = $sell_backinfo_ratio['seller_ratio'];
                        $this->sell_house_share_ratio_model->update_house_ratio_by_rowid($house_id, $seller_ratio, $buyer_ratio, $a_ratio, $b_ratio);
                        $sell_dataifno_ratio = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($house_id);
                        $sell_dataifno['a_ratio'] = $sell_dataifno_ratio['a_ratio'];
                        $sell_dataifno['b_ratio'] = $sell_dataifno_ratio['b_ratio'];
                        $sell_dataifno['buyer_ratio'] = $sell_dataifno_ratio['buyer_ratio'];
                        $sell_dataifno['seller_ratio'] = $sell_dataifno_ratio['seller_ratio'];
                        //增加积分
                        if ($sell_backinfo['isshare'] != $datainfo['isshare']) {
                            //增加积分
                            $this->load->model('api_broker_credit_model');
                            $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_id));
                            $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $house_id), 1);
                            //判断积分是否增加成功
                            if (is_full_array($credit_result) && $credit_result['status'] == 1) {
                                $credit_score += $credit_result['score'];
                            }
                            //增加等级分值
                            $this->load->model('api_broker_level_model');
                            $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_id));
                            $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $house_id), 1);
                            //判断成长值是否增加成功
                            if (is_full_array($level_result) && $level_result['status'] == 1) {
                                $level_score += $level_result['score'];
                            }
                        }
                    }
                    //记录房源修改前的图片 比较图片的改过情况
                    $new_inside = $this->input->post("p_filename2");
                    $new_room = $this->input->post("p_filename1");
                    if (!$new_inside) {
                        $new_inside = array();
                    }
                    if (!$new_room) {
                        $new_room = array();
                    }
                    $new_pic_inside_room = array_merge($new_inside, $new_room);
                    $sell_backinfo['pic_inside_room'] = $old_pic_inside_room;
                    $sell_dataifno['pic_inside_room'] = $new_pic_inside_room;
                    $sell_cont = $this->insetmatch($sell_backinfo, $sell_dataifno);
                    //修改房源日志录入
                    $need__info = $this->user_arr;
                    $this->load->model('follow_model');
                    $needarrt = array();
                    $needarrt['broker_id'] = $broker_id;
                    $needarrt['type'] = 1;
                    $needarrt['agency_id'] = $need__info['agency_id'];//门店ID
                    $needarrt['company_id'] = $need__info['company_id'];//总公司id
                    $needarrt['house_id'] = $house_id;
                    $needarrt['text'] = $sell_cont;
                    if (!empty($sell_cont)) {
                        $boolt = $this->follow_model->house_save($needarrt);
                        if (is_int($boolt) && $boolt > 0) {
                            //判断该跟进距离上一次是否已超过基本设置天数，录入出售房源附表
                            //获得基本设置房源跟进的天数
                            //获取当前经济人所在公司的基本设置信息
                            $this->load->model('house_customer_sub_model');
                            $company_basic_data = $this->company_basic_arr;
                            $house_follow_day = intval($company_basic_data['house_follow_spacing_time']);

                            $select_arr = array('id', 'house_id', 'date');
                            $this->follow_model->set_select_fields($select_arr);
                            $where_cond = 'house_id = "' . $house_id . '" and follow_type != 2 and type = 1';
                            $last_follow_data = $this->follow_model->get_lists($where_cond, 0, 2, 'date');
                            if (count($last_follow_data) == 2) {
                                $time1 = $last_follow_data[0]['date'];
                                $time2 = $last_follow_data[1]['date'];
                                $date1 = date('Y-m-d', strtotime($time1));
                                $date2 = date('Y-m-d', strtotime($time2));
                                $differ_day = (strtotime($date1) - strtotime($date2)) / (24 * 3600);
                                if ($differ_day > $house_follow_day) {
                                    $this->house_customer_sub_model->add_sell_house_sub($house_id, 1);
                                } else {
                                    $this->house_customer_sub_model->add_sell_house_sub($house_id, 0);
                                }
                            }
                        }
                    }
                    $refer = $this->input->post('refer', TRUE);
                    $pos = strpos($refer, 'group_publish');

                    if ($pos) {
                        $url_manage = $refer;
                    } else {
                        $url_manage = '/sell/lists';
                    }

                    if ($result) {
                        //操作日志
                        $add_log_param = array();
                        $add_log_param['company_id'] = $broker_info['company_id'];
                        $add_log_param['agency_id'] = $broker_info['agency_id'];
                        $add_log_param['broker_id'] = $broker_id;
                        $add_log_param['broker_name'] = $broker_info['truename'];
                        $add_log_param['type'] = 3;
                        $add_log_param['text'] = '出售房源 ' . 'CS' . $house_id . ' ' . $sell_cont;
                        $add_log_param['from_system'] = 1;
                        $add_log_param['from_ip'] = get_ip();
                        $add_log_param['mac_ip'] = '127.0.0.1';
                        $add_log_param['from_host_name'] = '127.0.0.1';
                        $add_log_param['hardware_num'] = '测试硬件序列号';
                        $add_log_param['time'] = time();

                        $this->operate_log_model->add_operate_log($add_log_param);

                        $page_text = "修改成功";

                        //添加价格变动
                        if ($sell_backinfo['price'] != $sell_dataifno['price']) {
                            $add_price_data = array(
                                'house_id' => $sell_backinfo['id'],
                                'price' => $sell_backinfo['price'],
                                'createtime' => time()
                            );
                            $this->house_modify_history_model->add_sell_house_modify_history($add_price_data);
                        }
                    } else {
                        $page_text = "修改失败";
                    }

                    $house_add_arr['modify'] = 1;

                    //出售房源修改工作统计日志
                    if ($sell_cont) {
                        $this->info_count($house_id, 2);
                    }
                }
            } else {
                $house_num_check = false;
                $url_manage = '/sell/publish/';
                $page_text = '修改失败,该房源已经存在';
            }


        }*/

        if ($house_id > 0 || $housee_id > 0) {
            $house_id = $house_id > 0 ? $house_id : $housee_id;
            $this->sell_house_model->set_id($house_id);
            $pics = $picinfo = array();
            //室内图、户型图
            $pics['p_filename2'] = $this->input->post('p_filename2', TRUE);
            $pics['p_fileids2'] = $this->input->post('p_fileids2', TRUE);
            $pics['add_pic'] = $this->input->post('add_pic', TRUE);
            $pics['p_filename1'] = $this->input->post('p_filename1', TRUE);
            $pics['p_fileids1'] = $this->input->post('p_fileids1', TRUE);

            //委托协议书、卖家身份证、房产证
            $pics['p_filename3'] = $this->input->post('p_filename3', TRUE);
            $pics['p_fileids3'] = $this->input->post('p_fileids3', TRUE);
            $pics['p_filename4'] = $this->input->post('p_filename4', TRUE);
            $pics['p_fileids4'] = $this->input->post('p_fileids4', TRUE);
            $pics['p_filename5'] = $this->input->post('p_filename5', TRUE);
            $pics['p_fileids5'] = $this->input->post('p_fileids5', TRUE);


            //根据上传图片情况，分类房源等级
            if (is_full_array($pics['p_filename2']) && is_full_array($pics['p_filename1'])) {
                $house_level = count($pics['p_filename2']) >= 3 ? 3 : 2;
            } else if (!is_full_array($pics['p_filename2']) && !is_full_array($pics['p_filename1'])) {
                $house_level = 0;
            } else {
                $house_level = 1;
            }

            if ($coo_ziliao_check_1 && $coo_ziliao_check_2 && $coo_ziliao_check_3) {
                $this->sell_house_model->set_id($house_id);
                $this->sell_house_model->update_info_by_id(array('house_level' => $house_level));

                $picinfo = $this->sell_house_model->insert_house_pic($pics, 'sell_house', $house_id, $datainfo['block_id']);

                if (is_full_array($pics['p_fileids2'])) {
                    foreach ($pics['p_fileids2'] as $value) {
                        //出售房源图片上传记录工作统计日志
                        if ($value == 0) {
                            $this->info_count($house_id, 3);
                        }
                    }
                }
                if (is_full_array($pics['p_fileids1'])) {
                    foreach ($pics['p_fileids1'] as $value) {
                        //出售房源图片上传记录工作统计日志
                        if ($value == 0) {
                            $this->info_count($house_id, 3);
                        }
                    }
                }
                if (is_full_array($pics['p_fileids3'])) {
                    foreach ($pics['p_fileids3'] as $value) {
                        //出售房源图片上传记录工作统计日志
                        if ($value == 0) {
                            $this->info_count($house_id, 3);
                        }
                    }
                }
                if (is_full_array($pics['p_fileids4'])) {
                    foreach ($pics['p_fileids4'] as $value) {
                        //出售房源图片上传记录工作统计日志
                        if ($value == 0) {
                            $this->info_count($house_id, 3);
                        }
                    }
                }
                if (is_full_array($pics['p_fileids5'])) {
                    foreach ($pics['p_fileids5'] as $value) {
                        //出售房源图片上传记录工作统计日志
                        if ($value == 0) {
                            $this->info_count($house_id, 3);
                        }
                    }
                }

                //删除 修改去掉的图片
                $pic_ids = $this->input->post('pic_ids', TRUE);
                if ($pic_ids != $picinfo['pic_ids']) {
                    if ($pic_ids) {
                        $before_arr = explode(',', trim($pic_ids, ','));
                        $after_arr = explode(',', trim($picinfo['pic_ids'], ','));
                        $left = '';

                        foreach ($before_arr as $val) {
                            if (!in_array($val, $after_arr)) {
                                $left .= $val . ',';
                            }
                        }
                        $this->load->model('pic_model');
                        $this->pic_model->del_pic_by_ids($left, $picinfo['pic_tbl']);
                    }
                }

                //设置封面
                if (is_full_array($pics['p_filename1']) || is_full_array($pics['p_filename2']) || is_full_array($pics['p_filename3']) || is_full_array($pics['p_filename4']) || is_full_array($pics['p_filename5'])) {
                    if ($pics['add_pic']) {
                        $picinfo['pic'] = $pics['add_pic'];
                    } elseif ($pics['p_filename2']) //无选择，默认第一张为封面
                    {
                        $picinfo['pic'] = $pics['p_filename2'][0];
                    }
                    $this->sell_house_model->update_info_by_id($picinfo);
                }
            }
        }
        if ($datainfo['is_outside'] == 1) {
            $datainfo['id'] = $house_id;
            $city_spell = $this->user_arr['city_spell'];
            /*if($city_spell == 'cd'){
                      $this->load->model('pic_model');
                      //统计室内图的数量
                      $where = array('tbl'=>'sell_house','type'=>1,'rowid'=>$house_id);
                      $num1 = $this->pic_model->count_house_pic_by_cond($where);
                      //统计户型图的数量
                      $where = array('tbl'=>'sell_house','type'=>2,'rowid'=>$house_id);
                      $num2 = $this->pic_model->count_house_pic_by_cond($where);
                      if($num1 >= 5 && $num2 >= 1){
                          $this->load->model('pinganhouse_model');
                          $add_data = array('house_id'=>$house_id,'outside_time'=>time());
                          $this->pinganhouse_model->add_house($add_data);
                      }
                  }*/
            $this->load->model('api_broker_credit_model');
            $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
            $credit_result = $this->api_broker_credit_model->rsync_fang100($datainfo, 1);
            /*if($city_spell =='sz' || $city_spell =='km'){
                      $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
                      $credit_result1 = $this->api_broker_credit_model->fang100_activity($datainfo, 1);
                      //判断积分是否增加成功
                      if (is_full_array($credit_result1) && $credit_result1['status'] == 1)
                      {
                          $credit_score +=$credit_result1['score'];
                      }
                  }*/
            $this->load->model('api_broker_level_model');
            $this->api_broker_level_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
            $level_result = $this->api_broker_level_model->rsync_fang100($datainfo, 1);
            //判断积分是否增加成功
            if (is_full_array($credit_result) && $credit_result['status'] == 1) {
                $credit_score += $credit_result['score'];
            }
            //判断成长值是否增加成功
            if (is_full_array($level_result) && $level_result['status'] == 1) {
                $level_score += $level_result['score'];
            }
        }
        if ($credit_score) {
            $msg .= '+' . $credit_score . '积分';
        }
        if ($level_score) {
            $msg .= '+' . $level_score . '成长值';
        }
        $house_add_arr['msg'] = $msg;
        $house_add_arr['hosue_id'] = $housee_id;
        $house_add_arr['result'] = $result;
        $house_add_arr['is_reward'] = $is_reward;
        $house_add_arr['is_reward_plus'] = true;
        $house_add_arr['house_num_check'] = $house_num_check;
        $house_add_arr['coo_ziliao_check_1'] = $coo_ziliao_check_1;
        $house_add_arr['coo_ziliao_check_2'] = $coo_ziliao_check_2;
        $house_add_arr['coo_ziliao_check_3'] = $coo_ziliao_check_3;
        $house_add_arr['house_private_check'] = $house_private_check;
        $house_add_arr['house_private_check_text'] = $house_private_check_text;


        if ($housee_id > 0) {
            $db_city = $this->sell_house_upload_model->get_db_city();
            $update_arr = [
                'status' => 1,
                'sell_house_id' => $housee_id,
                'update_time' => time(),
            ];
            $db_city->where('id', $sell_house_upload_id);
            $db_city->update('sell_house_upload', $update_arr);
        }

        echo json_encode($house_add_arr);
    }

    //判断房源是否重复
    public function check_house($block_id, $door, $unit, $dong)
    {
        $this->load->model('agency_model');
        $this->load->model('api_broker_model');
        $this->load->model('sell_house_model');

        //经纪人信息
        $broker_id = $this->input->post('broker_id', TRUE);
        $broker_info = $this->broker_info_model->get_one_by(array('id' => $broker_id));
        //根据经济人总公司编号获取全部分店信息
        $company_id = intval($broker_info['company_id']);//获取总公司编号
        $agency_id = intval($broker_info['agency_id']);//门店编号
        //判断经纪人当前门店类型，直营or加盟
        $this->agency_model->set_select_fields(array('id', 'agency_type'));
        $this_agency_data = $this->agency_model->get_by_id($agency_id);
        if (is_full_array($this_agency_data)) {
            $agency_type = $this_agency_data['agency_type'];
        }
        //加盟店，去重范围只在自己门店。
        if (isset($agency_type) && '2' == $agency_type) {
            $agency_ids = $agency_id;
            //直营店，去重范围，当前公司下的所有直营店。
        } else {
            //获取当前公司下的所有直营店
            $agency_type_1_list = $this->api_broker_model->get_type_1_agencys_by_company_id($company_id);
            if (is_full_array($agency_type_1_list)) {
                $arr_agency_id = array();
                foreach ($agency_type_1_list as $key => $val) {
                    $arr_agency_id[] = $val['agency_id'];
                }
                $agency_ids = implode(',', $arr_agency_id);
            } else {
                $agency_ids = $agency_id;
            }
        }
        $cond_where = "status != 5 and agency_id in (" . $agency_ids . ") and block_id = '$block_id' and door = '$door' and unit = '$unit' and dong = '$dong' ";
        $tbl = "sell_house";
        $this->sell_house_model->set_tbl($tbl);
        $house_num = $this->sell_house_model->get_housenum_by_cond($cond_where);
        return $house_num;
    }

    /*工作统计日志
     * type:1出售2出租3求购4求租
     * $state：1信息录入2信息修改3图片上传4堪房5带看6钥匙提交
     */
    private function info_count($house_id, $state, $customer_id = 0)
    {
        $this->load->model('count_log_model');
        $this->load->model('count_num_model');
        $broker_id = $this->input->post('broker_id', TRUE);
        $broker_info = $this->broker_info_model->get_one_by(array('id' => $broker_id));
        $insert_log_data = array(
            'company_id' => $broker_info['company_id'],
            'agency_id' => $broker_info['agency_id'],
            'broker_id' => $broker_info['broker_id'],
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'state' => $state,
            'type' => 1,
            'house_id' => $house_id,
            'customer_id' => $customer_id
        );
        $insert_id = $this->count_log_model->insert($insert_log_data);
        if ($insert_id) {
            $count_num_info = $this->count_num_model->get_one_by('broker_id = ' . $broker_info['broker_id'] . ' and YMD = ' . "'" . date('Y-m-d') . "'");
            if (is_full_array($count_num_info)) {
                //修改数据
                switch ($state) {
                    case 1://信息录入
                        $update_data = array(
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'insert_num' => $count_num_info['insert_num'] + 1
                        );
                        break;
                    case 2://信息修改
                        $update_data = array(
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'modify_num' => $count_num_info['modify_num'] + 1
                        );
                        break;
                    case 3://图片上传
                        $update_data = array(
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'upload_num' => $count_num_info['upload_num'] + 1
                        );
                        break;
                    case 4://堪房
                        $update_data = array(
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'look_num' => $count_num_info['look_num'] + 1
                        );
                        break;
                    case 5://带看
                        $update_data = array(
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'looked_num' => $count_num_info['looked_num'] + 1
                        );
                        break;
                    case 6://钥匙提交
                        $update_data = array(
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'key_num' => $count_num_info['key_num'] + 1
                        );
                        break;
                    case 7://视频上传数
                        $update_data = array(
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'video_num' => $count_num_info['video_num'] + 1
                        );
                        break;
                    case 8://查看保密信息
                        $update_data = array(
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'secret_num' => $count_num_info['secret_num'] + 1
                        );
                        break;
                    case 9://普通跟进
                        $update_data = array(
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'follow_num' => $count_num_info['follow_num'] + 1
                        );
                        break;
                }
                $row = $this->count_num_model->update_by_id($update_data, $count_num_info['id']);
                if ($row) {
                    return 'success';
                } else {
                    return 'error';
                }
            } else {
                //添加数据
                switch ($state) {
                    case 1://信息录入
                        $insert_num_data = array(
                            'company_id' => $broker_info['company_id'],
                            'agency_id' => $broker_info['agency_id'],
                            'broker_id' => $broker_info['broker_id'],
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'insert_num' => 1
                        );
                        break;
                    case 2://信息修改
                        $insert_num_data = array(
                            'company_id' => $broker_info['company_id'],
                            'agency_id' => $broker_info['agency_id'],
                            'broker_id' => $broker_info['broker_id'],
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'modify_num' => 1
                        );
                        break;
                    case 3://图片上传
                        $insert_num_data = array(
                            'company_id' => $broker_info['company_id'],
                            'agency_id' => $broker_info['agency_id'],
                            'broker_id' => $broker_info['broker_id'],
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'upload_num' => 1
                        );
                        break;
                    case 4://堪房
                        $insert_num_data = array(
                            'company_id' => $broker_info['company_id'],
                            'agency_id' => $broker_info['agency_id'],
                            'broker_id' => $broker_info['broker_id'],
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'look_num' => 1
                        );
                        break;
                    case 5://带看
                        $insert_num_data = array(
                            'company_id' => $broker_info['company_id'],
                            'agency_id' => $broker_info['agency_id'],
                            'broker_id' => $broker_info['broker_id'],
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'looked_num' => 1
                        );
                        break;
                    case 6://钥匙提交
                        $insert_num_data = array(
                            'company_id' => $broker_info['company_id'],
                            'agency_id' => $broker_info['agency_id'],
                            'broker_id' => $broker_info['broker_id'],
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'key_num' => 1
                        );
                        break;
                    case 7://视频上传数
                        $insert_num_data = array(
                            'company_id' => $broker_info['company_id'],
                            'agency_id' => $broker_info['agency_id'],
                            'broker_id' => $broker_info['broker_id'],
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'video_num' => 1
                        );
                        break;
                    case 8://查看保密信息
                        $insert_num_data = array(
                            'company_id' => $broker_info['company_id'],
                            'agency_id' => $broker_info['agency_id'],
                            'broker_id' => $broker_info['broker_id'],
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'secret_num' => 1
                        );
                        break;
                    case 9://普通跟进
                        $insert_num_data = array(
                            'company_id' => $broker_info['company_id'],
                            'agency_id' => $broker_info['agency_id'],
                            'broker_id' => $broker_info['broker_id'],
                            'dateline' => time(),
                            'YMD' => date('Y-m-d'),
                            'follow_num' => 1
                        );
                        break;
                }
                $insert_num_id = $this->count_num_model->insert($insert_num_data);
                if ($insert_num_id) {
                    return 'success';
                } else {
                    return 'error';
                }
            }
        } else {
            return 'error';
        }
    }

    /**
     * 根据门店获取经纪人信息
     */
    public function get_broker_info_by_agency()
    {
        $agency_id = $this->input->get('agency_id', TRUE);
        $company_id = $this->input->get('company_id', TRUE);
        if (empty($agency_id) || empty($agency_id)) {
            $result = [
                'error' => '非法操作',
            ];
            echo json_encode($result);
            exit;
        }
        $this->load->model('broker_info_model');
        $brokerList = $this->broker_info_model->get_all_by(array('agency_id' => $agency_id, 'company_id' => $company_id, 'status' => 1));
        $html = '<option value="">请选择</option>';
        if (empty($brokerList)) {
            $result = [
                'error' => '',
                'html' => $html
            ];
            echo json_encode($result);
            exit;
        }
        foreach ($brokerList as $v) {
            $html .= '<option value="' . $v['broker_id'] . '">' . $v['truename'] . '</option>';
        }
        $result = [
            'error' => '',
            'html' => $html
        ];
        echo json_encode($result);
    }
}
