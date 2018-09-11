<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 城市 Class
 *
 * 城市控制器
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author
 */
class Signing_notice extends MY_Controller
{
    /**
     * 城市参数
     *
     * @access private
     * @var string
     */
    protected $_city = 'hz';


    /**
     * 当前页码
     *
     * @access private
     * @var string
     */
    private $_current_page = 1;

    /**
     * 每页条目数
     *
     * @access private
     * @var int
     */
    private $_limit = 15;

    /**
     * 偏移
     *
     * @access private
     * @var int
     */
    private $_offset = 0;

    /**
     * 条目总数
     *
     * @access private
     * @var int
     */
    private $_total_count = 0;


    /**
     * 解析函数
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('page_helper');
        $this->load->model('signing_notice_model');//消息、公告模型类
        $this->load->model('bargain_config_model');//选项配置模块
        $this->load->model('agency_model');//门店模块
        $this->load->model('district_model');//区域模块
        $this->load->model('department_model');//签约部门模块
//    $this->load->model('company_employee_model');
    }


    /**
     * 公告管理
     * @access public
     * @return void
     */
    public function index($page = 1)
    {
        $data = array();
        $post_param = $this->input->post(NULL, TRUE);
        //发布部门
        if (!empty($post_param['department_id'])) {
            $data['where_cond']['department_id'] = $post_param['department_id'];
        }
        //操作日期
        if (!empty($post_param['start_time'])) {
            $data['where_cond']['start_time'] = $post_param['start_time'];
        }
        if (!empty($post_param['end_time'])) {
            $data['where_cond']['end_time'] = $post_param['end_time'];
        }
        //分类
        if (!empty($post_param['notice_type'])) {
            $data['where_cond']['notice_type'] = $post_param['notice_type'];
        }
        //关键字类型
        if (!empty($post_param['notice_keyword_type'])) {
            $data['where_cond']['notice_keyword_type'] = $post_param['notice_keyword_type'];
            //关键字
            if (!empty($post_param['keyword'])) {
                $data['where_cond']['keyword'] = $post_param['keyword'];
            }
        }

        //获取公司下的部门
        $data['departments'] = $this->department_model->get_all_by_company_id(1);
        //模板使用数据
        $data['user_menu'] = $this->user_menu;
        $data['user_func_menu'] = $this->user_func_menu;
        $data['config'] = $this->bargain_config_model->get_config();

        $post_param = $this->input->post(NULL, TRUE);
        $data['post_param'] = $post_param;
        //print_R($post_param);exit;

        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
        $this->_init_pagination($page, $this->_limit);
        //获取门店信息

        $agency_info = $this->agency_model->get_one_by("id = " . $this->user_arr['agency_id']);
        //查询条件
        $cond_where = "company_id = 1 ";
        //收件对象为所有
        $cond_where .= " AND receipt_object_type='all'";
        //收件对象为区域
        $cond_where .= " OR receipt_object_district=" . $agency_info['dist_id'];
        //收件对象为门店
        $cond_where .= " OR receipt_object_agency=" . $this->user_arr['agency_id'];

        if (isset($data['where_cond']['department_id']) && !empty($data['where_cond']['department_id'])) {
            $cond_where .= " AND department_id = '" . intval($data['where_cond']['department_id']) . "' ";
        }
        if (isset($data['where_cond']['start_time']) && !empty($data['where_cond']['start_time'])) {
            $start_time = strtotime($data['where_cond']['start_time']);
            $cond_where .= " AND createtime >= '" . $start_time . "' ";
        }
        if (isset($data['where_cond']['end_time']) && !empty($data['where_cond']['end_time'])) {
            $end_time = strtotime($data['where_cond']['end_time']) + 24 * 3600;
            $cond_where .= " AND createtime <= '" . $end_time . "' ";
        }
        if (isset($data['where_cond']['notice_type']) && !empty($data['where_cond']['notice_type'])) {
            $cond_where .= " AND notice_type = '" . intval($data['where_cond']['notice_type']) . "' ";
        }
        if (isset($data['where_cond']['notice_keyword_type']) && !empty($data['where_cond']['notice_keyword_type']) && isset($data['where_cond']['keyword']) && !empty($data['where_cond']['keyword'])) {

            if ($data['where_cond']['notice_keyword_type'] == 1) {//标题
                $cond_where .= " AND title like '%" . $data['where_cond']['keyword'] . "%'";
            }
            if ($data['where_cond']['notice_keyword_type'] == 2) {//文号
                $cond_where .= " AND notice_number like '%" . $data['where_cond']['keyword'] . "%'";
            }
            if ($data['where_cond']['notice_keyword_type'] == 3) {//发布人
                $cond_where .= " AND signatory_name like '%" . $data['where_cond']['keyword'] . "%'";
            }
        }

        //符合条件的总行数
        $this->_total_count = $this->signing_notice_model->get_count_by_cond($cond_where);
        $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

        //获取列表内容
        $list = $this->signing_notice_model->get_signing_notice_by($cond_where, $this->_offset, $this->_limit);
        foreach ($list as $k => $vo) {
            $vo['contents'] = trim(strip_tags($vo['contents']));
            $list[$k]['contents'] = mb_substr($vo['contents'], 0, 30, 'utf-8');
//      $broker_info = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
            if (mb_strlen($vo['contents']) > 30) {
                $list[$k]['contents'] .= '...';
            }
        }
        $data['list'] = $list;
        //print_r($list);exit;

        //分页处理
        $params = array(
            'total_rows' => $this->_total_count, //总行数
            'method' => 'post', //URL提交方式 get/html/post
            'now_page' => $this->_current_page,//当前页数
            'list_rows' => $this->_limit,//每页显示个数
        );
        //加载分页类
        $this->load->library('page_list', $params);
        //调用分页函数（不同的样式不同的函数参数）
        $data['page_list'] = $this->page_list->show('jump');

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,'
            . 'mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/dtreeck.css,'
            . 'mls_guli/css/v1.0/notice.css');

        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,'
            . 'common/js/dtreeck.js,'
            . 'common/js/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/house_list.js,'
            . 'mls_guli/js/v1.0/openWin.js,'
            . 'mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/message.js,'
            . 'mls_guli/js/v1.0/personal_center.js'
        //. 'mls_guli/js/v1.0/broker_common.js'
        );
//底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js');

        //页面标题
        $data['page_title'] = '系统权限---公司公告';
        $this->view('signing_notice/signing_notice.php', $data);
    }

    public function notice_modify($id)
    {
        //模板使用数据
        $data = array();
        //$id>0,表示修改
        if ($id > 0) {
            $data['notice_detail'] = $this->signing_notice_model->get_detail_by_id($id);
            $data['id'] = $id;
//            $this->upload_attachment($id);
        }

        //菜单生成
        $this->load->model('permission_tab_model');
        $data['user_menu'] = $this->user_menu = $this->permission_tab_model->get_tab('signing_notice', 'index');
        $data['user_func_menu'] = $this->user_func_menu;
        $data['config'] = $this->bargain_config_model->get_config();
        //获取所有区域
        $district = $this->district_model->get_district("is_show = 1");
        $district_id_str = "";
        if (is_array($district)) {
            foreach ($district as $key => $val) {
                $district_id_str .= $val["id"] . ",";
            }
            $district_id_str = trim($district_id_str, ",");
        }
        //根据区域获取所有门店
//      $district_agency=$this->agency_model->get_all_by_district("status = 1 and company_id > 0",$district_id_str);
        //获取所有门店
        $agencys = $this->agency_model->get_all_agency("status = 1 and company_id > 0");
        $district_agency = array();
        //获取所有区域
        $district = $this->district_model->get_district("is_show = 1");
        $district_agency = array();
        if (is_array($agencys) && is_array($district)) {
            foreach ($agencys as $agency_key => $agency_val) {
                foreach ($district as $key => $val) {
                    if ($agency_val['dist_id'] == $val["id"]) {
                        $district_agency[$val["district"]]["district_id"] = $val["id"];
                        $district_agency[$val["district"]]["district_name"] = $val["district"];
                        $district_agency[$val["district"]]["agency"] = $agency_val;
                    }
                }
            }
        }
        $data['agencys'] = $agencys;
        $data['district_agency'] = $district_agency;

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,'
            . 'mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/dtreeck.css,'
            . 'mls_guli/css/v1.0/notice.css');

        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,'
            . 'common/js/dtreeck.js,'
            . 'common/js/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/house_list.js,'
            . 'mls_guli/js/v1.0/openWin.js,'
            . 'mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/message.js,'
            . 'mls_guli/js/v1.0/personal_center.js'
        //. 'mls_guli/js/v1.0/broker_common.js'
        );
        //页面标题
        $data['page_title'] = '公告管理';
        $this->view('signing_notice/signing_notice_modify.php', $data);
    }

    /**
     * 公司公告详情company_notice
     * @access  public
     * @return  json
     */
    public function detail()
    {
        $this->load->model('broker_model');
        $broker_info = array();
        $broker_info = $this->user_arr;
        $broker_id = intval($broker_info['broker_id']);

        $id = $this->input->post('id', TRUE);
        $detail = $this->signing_notice_model->get_detail_by_id($id);

        $detail['createtime'] = date('Y-m-d H:i:s', $detail['createtime']);
        //print_r($detail);die;
        echo json_encode($detail);

    }


    /**
     * 添加
     * @access  public
     * @return  json
     */
    public function add_notice()
    {
        $data = array();
        $get_param = $this->input->post(NULL);
        //$this->load->model('signatory_model');
        $broker_info = array();
        $signatory_info = $this->user_arr;
//    $broker_id = intval($broker_info['broker_id']);
//    $agency_id = intval($broker_info['agency_id']);
//    $company_id = intval($broker_info['company_id']);

        $is_pop = $get_param['is_pop'];
        if (!$is_pop) {
            $is_pop = 0;
        }
        $data = array(
            'notice_type' => $get_param['notice_type'],
            'receipt_object_name' => $get_param['receipt_object_name'],
            'receipt_object_type' => $get_param['receipt_object_type'],
            'receipt_object_district' => $get_param['receipt_object_district'],
            'receipt_object_agency' => $get_param['receipt_object_agency'],
            'notice_number' => $get_param['notice_number'],
            'title' => $get_param['title'],
            'color' => $get_param['color'],
            'top_rank' => $get_param['top_rank'],
            'top_rank_deadline' => $get_param['top_rank_deadline'],

            'contents' => $get_param['contents'],
            'is_pop' => $is_pop,
            'updatetime' => time(),
            'signatory_id' => $signatory_info['signatory_id'],
            'signatory_name' => $signatory_info['truename'],
            'department_id' => $signatory_info["department_id"],
            'department_name' => $signatory_info["department_name"],
            'company_id' => $signatory_info['company_id'],
        );
        if ($get_param['id'] > 0) {
            $result = $this->signing_notice_model->update_notice_broker($get_param['id'], $data);
        } else {
            $data['createtime'] = time();
            $result = $this->signing_notice_model->add_notice($data);
        }

        if ($result > 0) {
            $data['result'] = 'ok';
            $data['id'] = $result;
        }
        echo json_encode($data);
    }

    /**
     * 修改
     * @access  public
     * @return  json
     */
    public function update_notice()
    {
        $data = array();
        $get_param = $this->input->post(NULL);
        $title = $get_param['title'];
        $color = $get_param['color'];
        $contents = $get_param['contents']; //print_r($contents);die;
        $is_pop = $get_param['is_pop'];
        if (!$is_pop) {
            $is_pop = 0;
        }
        $data = array('title' => $title, 'color' => $color, 'contents' => $contents, 'is_pop' => $is_pop, 'createtime' => time());
        $result = $this->signing_notice_model->update_notice_broker($get_param['id'], $data);
        if ($result > 0) {
            $data['result'] = 'ok';
        }
        echo json_encode($data);
    }

    /**
     * 删除
     * @access  public
     * @return  json
     */
    public function del()
    {
        $ids = $this->input->post('id', TRUE);
        $result = 0;
        if (is_numeric($ids)) {
            $insert_id = $this->signing_notice_model->company_notice_del('id = ' . $ids . '');
            if ($insert_id) {
                $result++;
            }
        } else {
            foreach ($ids as $vo) {
                $insert_id = $this->signing_notice_model->company_notice_del('id = ' . $vo . '');
                if ($insert_id) {
                    $result++;
                }
            }
        }
        if ($result == count($ids)) {
            $res['result'] = 'ok';
        } else {
            $res['result'] = '';
        }
        echo json_encode($res);
    }


    /**
     * 获取排序参数
     * @access private
     * @param  int $order_val
     * @return void
     */
    private function _get_orderby_arr($order_val)
    {
        $arr_order = array();

        switch ($order_val) {
            case 1:
                $arr_order['order_key'] = 'updatetime';
                $arr_order['order_by'] = 'DESC';
                break;
            case 2:
                $arr_order['order_key'] = 'updatetime';
                $arr_order['order_by'] = 'ASC';
                break;
            case 3:
                $arr_order['order_key'] = 'createtime';
                $arr_order['order_by'] = 'DESC';
                break;
            case 4:
                $arr_order['order_key'] = 'createtime';
                $arr_order['order_by'] = 'ASC';
                break;
            default:
                $arr_order['order_key'] = 'updatetime';
                $arr_order['order_by'] = 'DESC';
        }

        return $arr_order;
    }

    /**
     * 初始化分页参数
     *
     * @access public
     * @param  int $current_page
     * @param  int $page_size
     * @return void
     */
    private function _init_pagination($current_page = 1, $page_size = 0)
    {
        /** 当前页 */
        $this->_current_page = ($current_page && is_numeric($current_page)) ?
            intval($current_page) : 1;

        /** 每页多少项 */
        $this->_limit = ($page_size && is_numeric($page_size)) ?
            intval($page_size) : $this->_limit;

        /** 偏移量 */
        $this->_offset = ($this->_current_page - 1) * $this->_limit;

        if ($this->_offset < 0) {
            redirect(base_url());
        }
    }


    //截取中英文字符串超过固定长度为省略号
    function substr_for_string($sourcestr, $cutlength)
    {
        $returnstr = "";
        $i = 0;
        $n = 0;
        $str_length = strlen($sourcestr);    //字符串的字节数
        while (($n < $cutlength) and ($i <= $str_length)) {
            $temp_str = substr($sourcestr, $i, 1);
            $ascnum = Ord($temp_str); //得到字符串中第$i位字符的ascii码
            if ($ascnum >= 224) //如果ASCII位高与224，
            {
                $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
                $i = $i + 3; //实际Byte计为3
                $n++; //字串长度计1
            } elseif ($ascnum >= 192)//如果ASCII位高与192，
            {
                $returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
                $i = $i + 2; //实际Byte计为2
                $n++; //字串长度计1
            } elseif ($ascnum >= 65 && $ascnum <= 90) //如果是大写字母，
            {
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1; //实际的Byte数仍计1个
                $n++; //但考虑整体美观，大写字母计成一个高位字符
            } else //其他情况下，包括小写字母和半角标点符号，
            {
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1;    //实际的Byte数计1个
                $n = $n + 0.5;    //小写字母和半角标点等与半个高位字符宽…
            }
        }
        if ($str_length > $cutlength) {
            $returnstr = $returnstr . "...";    //超过长度时在尾处加上省略号
        }
        return $returnstr;
    }

    //上传附件
    public function upload_attachment($id)
    {
        if ($id <= 0) {
            $id = $this->input->post('id', TRUE);
        }
        $detail = $this->signing_notice_model->get_detail_by_id($id);

        $config['upload_path'] = str_replace("\\", "/", UPLOADS . DIRECTORY_SEPARATOR . 'file');

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
        $config['allowed_types'] = 'txt|rar|zip|doc|docx|xls|xlsx|ppt|pptx|et|pdf';
        $config['max_size'] = "3068";
        $this->load->library('upload', $config);
        //打印成功或错误的信息
        if ($this->upload->do_upload('attachment')) {
            $upload_data = $this->upload->data();
            $data = array(
                'attachment' => "http://" . MLS_FILE_SERVER_NAME . "/file/" . $upload_data['file_name'],
                'attachment_name' => $upload_data['client_name'],
            );
            $pathinfo = pathinfo($config['upload_path']);
            unlink($pathinfo['basename'] . DIRECTORY_SEPARATOR . $detail["attachment"]); //删除文件
            $result = $this->signing_notice_model->update_notice_broker($id, $data);
            //     unlink($data['upload_data']['full_path']); //删除文件
            $res['result'] = 'ok';
        } else {
            $res['result'] = '';
        }
        echo json_encode($res);
    }

    //上传文件
    public function import()
    {
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
                //新权限
                //范围（1公司2门店3个人）
                $view_import_house = $this->broker_permission_model->check('127');
                //上传的文件名称
                $broker_info = $this->user_arr;
                $this->load->model('read_model');
                $result = $this->read_model->read('sell_model', $broker_info, $data['upload_data'], 7, 1, $view_import_house);
                unlink($data['upload_data']['full_path']); //删除文件
            } else {
                $result = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css"></head><body style="background:#F2F2F2;"><p class="up_m_b_date_up" style="text-align: center;"><span class="up_e">上传失败</span>，请选择文件上传</p></body></html>';
            }
            echo $result;

        }
    }
}
/* End of file message.php */
/* Location: ./application/mls_guli/controllers/message.php */
