<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 门店隐号拨打管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class Index extends MY_Controller
{
    protected $pre_month_fee = 10;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('page_helper');
        $this->load->model('call_agency_model');
    }

    /**
     * 权限菜单列表页面
     */
    public function index()
    {
        $data['title'] = '门店隐号拨打管理';

        $where = "";
        //post参数
        $param = $this->input->post(NULL, TRUE);
        $data['param'] = $param;
        if (isset($param['company_name']) && !empty($param['company_name'])) {
            $where = "company_name like '%" . $param['company_name'] . "%'";
        }
        if (isset($param['agency_name']) && !empty($param['agency_name'])) {
            if (!empty($where)) {
                $where .= " AND ";
            }
            $where .= "agency_name like '%" . $param['agency_name'] . "%'";
        }

        //分页开始
        $data['district_num'] = $this->call_agency_model->count_by($where);
        $data['pagesize'] = 10;//设定每一页显示的记录数
        $data['pages'] = $data['district_num'] ? ceil($data['district_num'] / $data['pagesize']) : 0;  //计算总页数
        $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
        $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
        $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

        //申请隐号门店列表
        if (empty($data['district_num'])) {
            $data['agency_list'] = array();
        } else {
            $data['agency_list'] = $this->call_agency_model->get_all_by($where, $data['offset'], $data['pagesize']);
        }

        $this->load->view('call/index/index', $data);
    }

    /**
     * 添加权限菜单
     */
    public function add()
    {
        $data['title'] = '添加隐号拨打';
        $data['pre_month_fee'] = $this->pre_month_fee;
        $data['addResult'] = '';
        $submit_flag = $this->input->post('submit_flag');
        if ('add' == $submit_flag) {
            $param = $this->input->post(NULL, TRUE);
            if (empty($param['company_id']) || empty($param['company_name'])) {
                $data['mess_error'] = '请输入公司名称';
            } elseif (empty($param['agency_id']) || empty($param['agency_name'])) {
                $data['mess_error'] = '请输入门店名称';
            } elseif (!preg_match("/^[1-9]\d*$/", $param['phone_num'])) {
                $data['mess_error'] = '使用号码个数必须是数字';
            } else {
                $is_exist = $this->call_agency_model->get_one_by(array('company_id' => $param['company_id'], 'agency_id' => $param['agency_id']));
                if ($is_exist) {
                    $data['mess_error'] = '该门店已加入隐号拨打';
                } else {//添加
                    $time = time();
                    $db_city = $this->call_agency_model->get_db_city();
                    $company = $db_city->where(array('id' => $param['company_id']))->get('agency')->row_array();
                    $agencyInfo = $db_city->where(array('id' => $param['agency_id']))->get('agency')->row_array();
                    $agency = array(
                        'company_id' => intval($param['company_id']),
                        'company_name' => trim($company['name']),
                        'agency_id' => intval($param['agency_id']),
                        'agency_name' => trim($agencyInfo['name']),
                        'telno' => $agencyInfo['telno'],
                        'phone_num' => 0,//扣款后再增加
                        'all_phone_num' => trim($param['phone_num']),
                        'balance' => 0.00,
                        'monthly_fee' => trim($param['phone_num']) * $data['pre_month_fee'],
                        'create_time' => $time
                    );
                    $apply = array(
                        'company_id' => intval($param['company_id']),
                        'agency_id' => intval($param['agency_id']),
                        'phone_num' => trim($param['phone_num']),
                        'monthly_fee' => trim($param['phone_num']) * $data['pre_month_fee'],
                        'status' => 0,
                        'create_time' => $time
                    );
                    //$db_city->db_debug = true;
                    $db_city->trans_start();
                    $db_city->insert('call_agency', $agency);
                    $db_city->insert('call_phone_apply', $apply);
                    $db_city->trans_complete();
                    if ($db_city->trans_status() === FALSE) {
                        // 生成一条错误信息... 或者使用 log_message() 函数来记录你的错误信息
                        $data['addResult'] = 0;
                    } else {
                        $data['addResult'] = 1;
                    }
                }
            }
        }
        $this->load->helper('common_load_source_helper');
        $data['css'] = load_css('mls/css/v1.0/autocomplete.css');
        //需要加载的JS
        $data['js'] = load_js('common/third/jquery-ui-1.9.2.custom.min.js');

        $this->load->view('call/index/add', $data);
    }

    public function balance()
    {
        $data = array('title' => '门店资金明细');
        $get = $this->input->get(NULL, TRUE);
        if (!isset($get['id']) || empty($get['id'])) {
            echo '非法操作';
            exit;
        }
        $id = $get['id'];
        $agency = $this->call_agency_model->get_one_by(array('id' => $id));
        if (empty($agency)) {
            echo '门店不存在';
            exit;
        }
        $data['agency'] = $agency;
        $data['type'] = array(
            1 => '充值',
            2 => '调账',
            3 => '月租费',
            4 => '通话费'
        );
        $where = array('company_id' => $agency['company_id'], 'agency_id' => $agency['agency_id']);
        //post参数
        $param = $this->input->post(NULL, TRUE);
        $data['param'] = $param;

        //分页开始
        $this->load->model('call_agency_balance_model');
        $data['district_num'] = $this->call_agency_balance_model->count_by($where);
        $data['pagesize'] = 10;//设定每一页显示的记录数
        $data['pages'] = $data['district_num'] ? ceil($data['district_num'] / $data['pagesize']) : 0;  //计算总页数
        $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
        $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
        $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

        //申请隐号门店列表
        if (empty($data['district_num'])) {
            $data['balance_list'] = array();
        } else {
            $data['balance_list'] = $this->call_agency_balance_model->get_all_by($where, $data['offset'], $data['pagesize'], 'create_time', 'DESC');
        }

        $this->load->view('call/index/balance', $data);
    }

    /**
     * 根据关键字获取门店
     */
    public function get_agency_info_by_kw()
    {
        $keyword = $this->input->get('keyword', TRUE);
        $company_id = $this->input->get('company_id', TRUE);
        $this->load->model('agency_model');
        $select_fields = array('id', 'name', 'agency_type');
        $this->agency_model->set_select_fields($select_fields);
        $cmt_info = $this->agency_model->get_agencys_by_kw($keyword, 'id', 'ASC', $company_id);

        foreach ($cmt_info as $key => $value) {
            if ($value['agency_type'] == 3) {
                $cmt_info[$key]['label'] = $value['name'] . "（合作）";
            } else {
                $cmt_info[$key]['label'] = $value['name'];
            }
        }

        if (empty($cmt_info)) {
            $cmt_info[0]['id'] = 0;
            $cmt_info[0]['label'] = '暂无门店';
        }

        echo json_encode($cmt_info);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
