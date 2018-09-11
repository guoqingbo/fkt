<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 门店消费记录
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class Consume extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('page_helper');
        $this->load->model('call_fee_model');
    }

    /**
     * 权限菜单列表页面
     */
    public function index()
    {
        $data = array('title' => '门店消费记录');
        $data['type'] = array(
            1 => '月租费',
            2 => '通话费'
        );

        $where = "";
        //post参数
        $param = $this->input->post(NULL, TRUE);
        $data['param'] = $param;
        if (isset($param['company_name']) && !empty($param['company_name'])) {
            $where = "b.name like '%" . $param['company_name'] . "%'";
        }
        if (isset($param['agency_name']) && !empty($param['agency_name'])) {
            if (!empty($where)) {
                $where .= " AND ";
            }
            $where .= "c.name like '%" . $param['agency_name'] . "%'";
        }
        if (isset($param['type']) && !empty($param['type'])) {
            if (!empty($where)) {
                $where .= " AND ";
            }
            $where .= "a.type = " . $param['type'];
        }

        //分页开始
        $db_city = $this->call_fee_model->get_db_city();
        $table = $db_city->from('call_fee AS a')->join('agency as b', 'a.company_id = b.id', 'left')->join('agency as c', 'a.agency_id = c.id', 'left')->join('broker_info as d', 'a.broker_id = d.broker_id', 'left');
        if (!empty($where)) {
            $table = $table->where($where);
        }
        $data['district_num'] = $table->count_all_results();
        $data['pagesize'] = 10;//设定每一页显示的记录数
        $data['pages'] = $data['district_num'] ? ceil($data['district_num'] / $data['pagesize']) : 0;  //计算总页数
        $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
        $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
        $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

        //申请隐号门店列表
        if (empty($data['district_num'])) {
            $data['fee_list'] = array();
        } else {
            $table = $db_city->from('call_fee AS a')->join('agency as b', 'a.company_id = b.id', 'left')->join('agency as c', 'a.agency_id = c.id', 'left')->join('broker_info as d', 'a.broker_id = d.broker_id', 'left');
            if (!empty($where)) {
                $table = $table->where($where);
            }
            $data['fee_list'] = $table->select('a.*, b.name as company_name, c.name as agency_name, d.truename')->order_by('a.id', 'desc')->limit($data['pagesize'], $data['offset'])->get()->result_array();
        }

        $this->load->view('call/consume/index', $data);
    }

    /**
     * 充值
     */
    public function add()
    {
        $data = array('title' => '充值');
        $data['addResult'] = '';
        $submit_flag = $this->input->post('submit_flag');
        if ('add' == $submit_flag) {
            $param = $this->input->post(NULL, TRUE);
            if (empty($param['company_id']) || empty($param['company_name'])) {
                $data['mess_error'] = '请输入公司名称';
            } elseif (empty($param['agency_id']) || empty($param['agency_name'])) {
                $data['mess_error'] = '请输入门店名称';
            } elseif (!preg_match("/^[0-9]+(\.[0-9]{1,2})?$/", $param['fee'])) {
                $data['mess_error'] = '充值金额必须是数字，小数点后最多两位';
            } elseif (in_array(abs($param['fee']), array(0, 0.0, 0.00))) {
                $data['mess_error'] = '充值金额不能为0';
            } else {
                $this->load->model('call_agency_model');
                $agency = $this->call_agency_model->get_one_by(array('company_id' => $param['company_id'], 'agency_id' => $param['agency_id']));
                if (empty($agency)) {
                    echo '门店还没加入隐号拨打';
                    exit;
                }

                $time = time();
                $recharge = array(
                    'order_no' => date('Ymd') . substr($time, -6),
                    'company_id' => intval($param['company_id']),
                    'company_name' => trim($param['company_name']),
                    'agency_id' => intval($param['agency_id']),
                    'agency_name' => trim($param['agency_name']),
                    'fee' => $param['fee'],
                    'type' => 1,
                    'remark' => '',
                    'create_time' => $time
                );
                $db_city = $this->call_recharge_model->get_db_city();
                $db_city->trans_start();
                $db_city->insert('call_recharge', $recharge);

                $sql = 'update call_agency set balance = balance + ' . $param['fee'] . ', update_time = ' . $time . ' where id = ' . $agency['id'];
                $db_city->query($sql);

                $db_city->trans_complete();
                if ($db_city->trans_status() === FALSE) {
                    $data['addResult'] = 0;
                } else {
                    $data['addResult'] = 1;
                }
            }
        }
        $this->load->helper('common_load_source_helper');
        $data['css'] = load_css('mls/css/v1.0/autocomplete.css');
        //需要加载的JS
        $data['js'] = load_js('common/third/jquery-ui-1.9.2.custom.min.js');
        $data['formUrl'] = MLS_ADMIN_URL . '/call/recharge';

        $this->load->view('call/recharge/add', $data);
    }

    /**
     * 调账
     */
    public function edit()
    {
        $data = array('title' => '调账');
        $data['addResult'] = '';
        $submit_flag = $this->input->post('submit_flag');
        if ('edit' == $submit_flag) {
            $param = $this->input->post(NULL, TRUE);
            if (empty($param['company_id']) || empty($param['company_name'])) {
                $data['mess_error'] = '请输入公司名称';
            } elseif (empty($param['agency_id']) || empty($param['agency_name'])) {
                $data['mess_error'] = '请输入门店名称';
            } elseif (!preg_match("/^\-?[0-9]+(\.[0-9]{1,2})?$/", $param['fee'])) {
                $data['mess_error'] = '调账金额必须是数字，小数点后最多两位，可以是负数';
            } elseif (in_array(abs($param['fee']), array(0, 0.0, 0.00))) {
                $data['mess_error'] = '调账金额不能为0';
            } elseif (empty($param['remark'])) {
                $data['mess_error'] = '调账说明不能为空';
            } else {
                $this->load->model('call_agency_model');
                $agency = $this->call_agency_model->get_one_by(array('company_id' => $param['company_id'], 'agency_id' => $param['agency_id']));
                if (empty($agency)) {
                    echo '门店还没加入隐号拨打';
                    exit;
                }

                $time = time();
                $recharge = array(
                    'order_no' => date('Ymd') . substr($time, -6),
                    'company_id' => intval($param['company_id']),
                    'company_name' => trim($param['company_name']),
                    'agency_id' => intval($param['agency_id']),
                    'agency_name' => trim($param['agency_name']),
                    'fee' => $param['fee'],
                    'type' => 2,
                    'remark' => trim($param['remark']),
                    'create_time' => $time
                );
                $db_city = $this->call_recharge_model->get_db_city();
                $db_city->db_debug = true;
                $db_city->trans_start();
                $db_city->insert('call_recharge', $recharge);

                $symbol = $param['fee'] > 0 ? '+' : '-';
                $sql = "update call_agency set balance = balance $symbol " . abs($param['fee']) . ', update_time = ' . $time . ' where id = ' . $agency['id'];
                $db_city->query($sql);

                $db_city->trans_complete();
                if ($db_city->trans_status() === FALSE) {
                    $data['addResult'] = 0;
                } else {
                    $data['addResult'] = 1;
                }
            }
        }
        $this->load->helper('common_load_source_helper');
        $data['css'] = load_css('mls/css/v1.0/autocomplete.css');
        //需要加载的JS
        $data['js'] = load_js('common/third/jquery-ui-1.9.2.custom.min.js');
        $data['formUrl'] = MLS_ADMIN_URL . '/call/recharge';

        $this->load->view('call/recharge/edit', $data);
    }

    /**
     * 根据关键字获取门店
     */
    public function get_company_by_kw()
    {
        $keyword = $this->input->get('keyword', TRUE);
        $db_city = $this->call_recharge_model->get_db_city();

        //$select_fields = array('id', 'name', 'agency_type');
        //$this->agency_model->set_select_fields($select_fields);
        //$cmt_info = $this->agency_model->get_agencys_by_kw($keyword, 'id', 'ASC', $company_id);

        $cmt_info = $db_city->select('company_id as id, company_name as name')->where("`company_name` LIKE '%" . $keyword . "%'")->get('call_agency');
        $cmt_info = !empty($cmt_info) ? $cmt_info->result_array() : array();

        foreach ($cmt_info as $key => $value) {
            $cmt_info[$key]['label'] = $value['name'];
        }

        if (empty($cmt_info)) {
            $cmt_info[0]['id'] = 0;
            $cmt_info[0]['label'] = '暂无门店';
        }

        echo json_encode($cmt_info);
    }

    /**
     * 根据关键字获取门店
     */
    public function get_agency_info_by_kw()
    {
        $keyword = $this->input->get('keyword', TRUE);
        $company_id = $this->input->get('company_id', TRUE);
        if (empty($company_id)) {
            $cmt_info[0]['id'] = 0;
            $cmt_info[0]['label'] = '请先选择公司';
            echo json_encode($cmt_info);
        }
        $db_city = $this->call_recharge_model->get_db_city();

        $cmt_info = $db_city->select('agency_id as id, agency_name as name')->where("`agency_name` LIKE '%" . $keyword . "%' and company_id = $company_id")->get('call_agency');
        $cmt_info = !empty($cmt_info) ? $cmt_info->result_array() : array();

        foreach ($cmt_info as $key => $value) {
            $cmt_info[$key]['label'] = $value['name'];
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
