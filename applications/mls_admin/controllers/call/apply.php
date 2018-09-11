<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 门店隐号拨打管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class Apply extends MY_Controller
{
    protected $pre_month_fee = 10;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('page_helper');
        $this->load->model('call_agency_model');
        $this->load->model('call_phone_apply_model');
    }

    /**
     * 权限菜单列表页面
     */
    public function index()
    {
        $data = array('title' => '使用号码个数管理');
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
        $data['status'] = array(
            0 => '待扣款',
            1 => '正常',
            2 => '停用',
            -1 => '减少'
        );
        $where = array('company_id' => $agency['company_id'], 'agency_id' => $agency['agency_id']);
        //post参数
        $param = $this->input->post(NULL, TRUE);
        $data['param'] = $param;
        if (isset($param['status']) && $param['status'] !== '') {
            $where['status'] = intval($param['status']);
            $data['param']['status'] = intval($param['status']);
        }

        //分页开始
        $data['district_num'] = $this->call_phone_apply_model->count_by($where);
        $data['pagesize'] = 10;//设定每一页显示的记录数
        $data['pages'] = $data['district_num'] ? ceil($data['district_num'] / $data['pagesize']) : 0;  //计算总页数
        $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
        $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
        $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

        //申请隐号门店列表
        if (empty($data['district_num'])) {
            $data['agency_list'] = array();
        } else {
            $data['agency_list'] = $this->call_phone_apply_model->get_all_by($where, $data['offset'], $data['pagesize']);
        }

        $this->load->view('call/apply/index', $data);
    }

    /**
     * 添加权限菜单
     */
    public function add()
    {
        $data = array('title' => '增加号码个数');
        $data['pre_month_fee'] = $this->pre_month_fee;

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

        $data['addResult'] = '';
        $submit_flag = $this->input->post('submit_flag');
        if ('add' == $submit_flag) {
            $param = $this->input->post(NULL, TRUE);
            if (!preg_match("/^[1-9]\d*$/", $param['phone_num'])) {
                $data['mess_error'] = '使用号码个数必须是数字';
            } else {
                $db_city = $this->call_phone_apply_model->get_db_city();
                $db_city->trans_start();
                $apply = array(
                    'company_id' => $agency['company_id'],
                    'agency_id' => $agency['agency_id'],
                    'phone_num' => trim($param['phone_num']),
                    'monthly_fee' => trim($param['phone_num']) * $data['pre_month_fee'],
                    'status' => 0,
                    'create_time' => time()
                );
                $db_city->insert('call_phone_apply', $apply);

                $sql = 'update call_agency set all_phone_num = all_phone_num + ' . $param['phone_num'] . ', monthly_fee = monthly_fee + ' . (trim($param['phone_num']) * $data['pre_month_fee']) . ', update_time = ' . time() . ' where id = ' . $agency['id'];
                $db_city->query($sql);
                //$data['addResult'] = $this->call_phone_apply_model->insert($apply);
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
        $data['formUrl'] = MLS_ADMIN_URL . '/call/apply/index?id=' . $id;

        $this->load->view('call/apply/add', $data);
    }

    /**
     * 减少号码个数
     */
    public function reduce()
    {
        $data = array('title' => '减少号码个数');
        $data['pre_month_fee'] = $this->pre_month_fee;

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

        $data['addResult'] = '';
        $submit_flag = $this->input->post('submit_flag');
        if ('add' == $submit_flag) {
            $param = $this->input->post(NULL, TRUE);
            if (!preg_match("/^[1-9]\d*$/", $param['phone_num'])) {
                $data['mess_error'] = '减少号码个数必须是数字';
            } else {
                $apply = array(
                    'company_id' => $agency['company_id'],
                    'agency_id' => $agency['agency_id'],
                    'phone_num' => trim($param['phone_num']),
                    'monthly_fee' => 0.00,
                    'status' => -1,
                    'create_time' => time()
                );
                $data['addResult'] = $this->call_phone_apply_model->insert($apply);
            }
        }
        $this->load->helper('common_load_source_helper');
        $data['css'] = load_css('mls/css/v1.0/autocomplete.css');
        //需要加载的JS
        $data['js'] = load_js('common/third/jquery-ui-1.9.2.custom.min.js');
        $data['formUrl'] = MLS_ADMIN_URL . '/call/apply/index?id=' . $id;

        $this->load->view('call/apply/reduce', $data);
    }

    /**
     * 修改
     */
    public function edit()
    {
        $data = array('title' => '修改');
        $data['pre_month_fee'] = $this->pre_month_fee;
        $data['addResult'] = '';
        $get = $this->input->get(NULL, TRUE);
        if (!isset($get['id']) || empty($get['id'])) {
            echo '非法操作';
            exit;
        }
        $id = $get['id'];
        $apply = $this->call_phone_apply_model->get_one_by(array('id' => $id));
        if (empty($apply)) {
            echo '非法操作';
            exit;
        }
        $data['apply'] = $apply;

        $agency = $this->call_agency_model->get_one_by(array('company_id' => $apply['company_id'], 'agency_id' => $apply['agency_id']));
        if (empty($agency)) {
            echo '非法操作';
            exit;
        }
        $data['agency'] = $agency;

        $submit_flag = $this->input->post('submit_flag');
        if ('edit' == $submit_flag) {
            $param = $this->input->post(NULL, TRUE);
            if (!preg_match("/^[1-9]\d*$/", $param['phone_num'])) {
                $data['mess_error'] = '使用号码个数必须是数字';
            } else {
                $db_city = $this->call_phone_apply_model->get_db_city();
                $db_city->trans_start();

                $sql = 'update call_phone_apply set phone_num = ' . $param['phone_num'] . ', monthly_fee = ' . (trim($param['phone_num']) * $data['pre_month_fee']) . ', update_time = ' . time() . ' where id = ' . $id;
                $db_city->query($sql);

                $sql = 'select sum(phone_num) as phone_num from call_phone_apply where company_id = ' . $apply['company_id'] . ' and agency_id = ' . $apply['agency_id'] . ' and status <> -1';
                $phone_num = $db_city->query($sql)->row_array();
                //var_dump($phone_num);exit;
                $phone_num = empty($phone_num['phone_num']) ? 0 : $phone_num['phone_num'];
                $sql = 'update call_agency set all_phone_num = ' . $phone_num . ', monthly_fee = ' . ($phone_num * $data['pre_month_fee']) . ', update_time = ' . time() . ' where id = ' . $agency['id'];
                $db_city->query($sql);

                /*$update = [
                    'phone_num' => $param['phone_num'],
                    'monthly_fee' => trim($param['phone_num']) * $data['pre_month_fee']
                ];
                $data['addResult'] = $this->call_phone_apply_model->update_by_id($id, $update);*/
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
        $data['formUrl'] = MLS_ADMIN_URL . '/call/apply/index?id=' . $agency['id'];

        $this->load->view('call/apply/edit', $data);
    }

    /**
     * 删除
     */
    public function delete()
    {
        $data = array('title' => '修改');
        $get = $this->input->get(NULL, TRUE);
        if (!isset($get['id']) || empty($get['id'])) {
            echo '非法操作';
            exit;
        }
        $id = $get['id'];
        $apply = $this->call_phone_apply_model->get_one_by(array('id' => $id));
        if (empty($apply)) {
            echo '非法操作';
            exit;
        }
        $allowDelete = array(-1, 0);
        if (!in_array($apply['status'], $allowDelete)) {
            echo '当前状态下无法删除记录';
            exit;
        }

        $agency = $this->call_agency_model->get_one_by(array('company_id' => $apply['company_id'], 'agency_id' => $apply['agency_id']));
        if (empty($agency)) {
            echo '非法操作';
            exit;
        }

        $db_city = $this->call_phone_apply_model->get_db_city();
        $db_city->trans_start();

        $db_city->where(array('id' => $id))->delete('call_phone_apply');

        $sql = 'select sum(phone_num) as phone_num from call_phone_apply where company_id = ' . $apply['company_id'] . ' and agency_id = ' . $apply['agency_id'] . ' and status <> -1';
        $phone_num = $db_city->query($sql)->row_array();
        $phone_num = empty($phone_num['phone_num']) ? 0 : $phone_num['phone_num'];
        $sql = 'update call_agency set all_phone_num = ' . $phone_num . ', monthly_fee = ' . ($phone_num * $this->pre_month_fee) . ', update_time = ' . time() . ' where id = ' . $agency['id'];
        $db_city->query($sql);

        $db_city->trans_complete();
        if ($db_city->trans_status() === FALSE) {
            echo "删除失败";
            exit;
        } else {
            header("Location: " . MLS_ADMIN_URL . '/call/apply/index?id=' . $agency['id']);
            exit;
        }
        /*if ($db_city->where(['id' => $id])->delete('call_phone_apply')) {
            header("Location: " . MLS_ADMIN_URL . '/call/apply/index?id=' . $agency['id']);
            exit;
        } else {
            echo "删除失败";
            exit;
        }*/
    }

    /**
     * 扣月租费
     */
    public function charge()
    {
        $data = array('title' => '询问');
        $data['pre_month_fee'] = $this->pre_month_fee;
        $data['addResult'] = '';
        $get = $this->input->get(NULL, TRUE);
        if (!isset($get['id']) || empty($get['id'])) {
            echo '非法操作';
            exit;
        }
        $id = $get['id'];
        $apply = $this->call_phone_apply_model->get_one_by(array('id' => $id));
        if (empty($apply)) {
            echo '非法操作';
            exit;
        }

        $agency = $this->call_agency_model->get_one_by(array('company_id' => $apply['company_id'], 'agency_id' => $apply['agency_id']));
        if (empty($agency)) {
            echo '非法操作';
            exit;
        }

        $submit_flag = $this->input->post('submit_flag');
        if ('charge' == $submit_flag) {
            if ($agency['balance'] < $apply['monthly_fee']) {
                $data['mess_error'] = '账户余额不足，扣减月租费用失败';
            } elseif ($apply['status'] != 0) {
                $data['mess_error'] = '只有在未开通状态才能扣月租费';
            } else {
                $db_city = $this->call_agency_model->get_db_city();
                //$db_city->db_debug = true;
                $time = time();
                $balance = array(
                    'company_id' => intval($apply['company_id']),
                    'agency_id' => intval($apply['agency_id']),
                    'fee' => '-' . $apply['monthly_fee'],
                    'type' => 3,
                    'balance' => $agency['balance'] - $apply['monthly_fee'],
                    'create_time' => $time
                );

                $db_city->trans_start();
                $sql = 'update call_agency set balance = balance - ' . $apply['monthly_fee'] . ', phone_num = phone_num + ' . $apply['phone_num'] . ', update_time = ' . $time . ' where id = ' . $agency['id'] . ' and balance >= ' . $apply['monthly_fee'];
                $db_city->query($sql);

                $start_time = strtotime(date('Y-m-d'));
                $end_time = strtotime("+30 days", strtotime(date('Y-m-d 23:59:59')));
                $sql = 'update call_phone_apply set status = 1, start_time = ' . $start_time . ', end_time = ' . $end_time . ', update_time = ' . $time . ' where id = ' . $apply['id'];
                $db_city->query($sql);

                $sql = "insert into call_fee(`apply_id`, `company_id`, `agency_id`, `broker_id`, `virtual_phone`, `type`, `phone_duration`, `fee`, `phone_start`, `create_time`) values (" . $apply['id'] . ", " . $agency['company_id'] . ", " . $agency['agency_id'] . ", 0, '', 1, 0, " . $apply['monthly_fee'] . ", 0, " . $time . ")";
                $db_city->query($sql);

                $db_city->insert('call_agency_balance', $balance);

                $db_city->trans_complete();
                if ($db_city->trans_status() === FALSE) {
                    // 生成一条错误信息... 或者使用 log_message() 函数来记录你的错误信息
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
        $data['formUrl'] = MLS_ADMIN_URL . '/call/apply/index?id=' . $agency['id'];

        $this->load->view('call/apply/charge', $data);
    }

    /**
     * 停用后启用
     */
    public function recover()
    {
        $data = array('title' => '询问');
        $data['pre_month_fee'] = $this->pre_month_fee;
        $data['addResult'] = '';
        $get = $this->input->get(NULL, TRUE);
        if (!isset($get['id']) || empty($get['id'])) {
            echo '非法操作';
            exit;
        }
        $id = $get['id'];
        $apply = $this->call_phone_apply_model->get_one_by(array('id' => $id));
        if (empty($apply)) {
            echo '非法操作';
            exit;
        }

        $agency = $this->call_agency_model->get_one_by(array('company_id' => $apply['company_id'], 'agency_id' => $apply['agency_id']));
        if (empty($agency)) {
            echo '非法操作';
            exit;
        }

        $submit_flag = $this->input->post('submit_flag');
        if ('recover' == $submit_flag) {
            $time = time();
            if ($agency['balance'] <= 0) {
                $data['mess_error'] = '账户余额不足';
            } elseif ($time < $apply['end_time']) {
                $db_city = $this->call_agency_model->get_db_city();
                $db_city->trans_start();
                $sql = 'update call_agency set phone_num = phone_num + ' . $apply['phone_num'] . ', update_time = ' . $time . ' where id = ' . $agency['id'];
                $db_city->query($sql);

                $sql = 'update call_phone_apply set status = 1, update_time = ' . $time . ' where id = ' . $apply['id'];
                $db_city->query($sql);

                $db_city->trans_complete();
                if ($db_city->trans_status() === FALSE) {
                    $data['addResult'] = 0;
                } else {
                    $data['addResult'] = 1;
                }
            /*} elseif ($agency['balance'] < $apply['monthly_fee']) {
                $data['mess_error'] = '账户余额不足，扣减月租费用失败';*/
            } elseif ($apply['status'] != 2) {
                $data['mess_error'] = '只有在停用状态才能启用';
            } else {
                $db_city = $this->call_agency_model->get_db_city();
                $reduceList = $db_city->from('call_phone_apply')->where("status = -1")->order_by('id', 'asc')->get()->result_array();
                if (!empty($reduceList)) {
                    $addNum = $apply['phone_num'];
                    $db_city->trans_start();

                    foreach ($reduceList as $v) {
                        if ($addNum <= $v['phone_num']) {
                            $db_city->where('id', $apply['id'])->delete('call_phone_apply');
                            $reduceNum = $v['phone_num'] - $addNum;
                            if (0 == $reduceNum) {
                                $db_city->where('id', $v['id'])->delete('call_phone_apply');
                            } else {
                                $db_city->where('id', $v['id'])->update('call_phone_apply', array('phone_num' => $reduceNum, 'update_time' => $time));
                            }
                            $addNum = 0;
                            break;
                        } else {
                            $db_city->where('id', $v['id'])->delete('call_phone_apply');
                            $addNum -= $v['phone_num'];
                        }
                    }
                    if ($addNum > 0) {
                        if ($agency['balance'] >= $data['pre_month_fee'] * $addNum) {
                            $sql = 'update call_agency set balance = balance - ' . ($data['pre_month_fee'] * $addNum) . ', phone_num = phone_num + ' . $addNum . ', update_time = ' . $time . ' where id = ' . $agency['id'];
                            $db_city->query($sql);

                            $start_time = strtotime(date('Y-m-d'));
                            $end_time = strtotime("+30 days", strtotime(date('Y-m-d 23:59:59')));
                            $sql = 'update call_phone_apply set status = 1, phone_num = ' . $addNum . ', monthly_fee = ' . ($addNum * $data['pre_month_fee']) . ', start_time = ' . $start_time . ', end_time = ' . $end_time . ', update_time = ' . $time . ' where id = ' . $apply['id'];
                            $db_city->query($sql);

                            $sql = "insert into call_fee(`apply_id`, `company_id`, `agency_id`, `broker_id`, `virtual_phone`, `type`, `phone_duration`, `fee`, `phone_start`, `create_time`) values (" . $apply['id'] . ", " . $agency['company_id'] . ", " . $agency['agency_id'] . ", 0, '', 1, 0, " . ($data['pre_month_fee'] * $addNum) . ", 0, " . $time . ")";
                            $db_city->query($sql);

                            $balance = array(
                                'company_id' => $apply['company_id'],
                                'agency_id' => $apply['agency_id'],
                                'fee' => '-' . ($data['pre_month_fee'] * $addNum),
                                'type' => 3,
                                'balance' => $agency['balance'] - ($data['pre_month_fee'] * $addNum),
                                'create_time' => $time
                            );
                            $db_city->insert('call_agency_balance', $balance);
                        } else {
                            $db_city->where('id', $apply['id'])->update('call_phone_apply', array('phone_num' => $addNum, 'monthly_fee' => $addNum * $data['pre_month_fee'], 'update_time' => $time));
                        }
                    }

                    $sql = 'select sum(phone_num) as phone_num from call_phone_apply where company_id = ' . $apply['company_id'] . ' and agency_id = ' . $apply['agency_id'] . ' and status <> -1';
                    $phone_num = $db_city->query($sql)->row_array();
                    $phone_num = empty($phone_num['phone_num']) ? 0 : $phone_num['phone_num'];
                    $sql = 'update call_agency set all_phone_num = ' . $phone_num . ', monthly_fee = ' . ($phone_num * $data['pre_month_fee']) . ', update_time = ' . time() . ' where id = ' . $agency['id'];
                    $db_city->query($sql);

                    $db_city->trans_complete();
                    if ($db_city->trans_status() === FALSE) {
                        $data['addResult'] = 0;
                    } else {
                        if ($addNum > 0 && $agency['balance'] < $data['pre_month_fee'] * $addNum) {
                            $data['mess_error'] = '账户余额不足';
                        } else {
                            $data['addResult'] = 1;
                        }
                    }
                } elseif ($agency['balance'] < $apply['monthly_fee']) {
                    $data['mess_error'] = '账户余额不足，扣减月租费用失败';
                } else {
                    $db_city->trans_start();

                    $sql = 'update call_agency set balance = balance - ' . $apply['monthly_fee'] . ', phone_num = phone_num + ' . $apply['phone_num'] . ', update_time = ' . $time . ' where id = ' . $agency['id'] . ' and balance >= ' . $apply['monthly_fee'];
                    $db_city->query($sql);

                    $start_time = strtotime(date('Y-m-d'));
                    $end_time = strtotime("+30 days", strtotime(date('Y-m-d 23:59:59')));
                    $sql = 'update call_phone_apply set status = 1, start_time = ' . $start_time . ', end_time = ' . $end_time . ', update_time = ' . $time . ' where id = ' . $apply['id'];
                    $db_city->query($sql);

                    $sql = "insert into call_fee(`apply_id`, `company_id`, `agency_id`, `broker_id`, `virtual_phone`, `type`, `phone_duration`, `fee`, `phone_start`, `create_time`) values (" . $apply['id'] . ", " . $agency['company_id'] . ", " . $agency['agency_id'] . ", 0, '', 1, 0, " . $apply['monthly_fee'] . ", 0, " . $time . ")";
                    $db_city->query($sql);

                    $balance = array(
                        'company_id' => $apply['company_id'],
                        'agency_id' => $apply['agency_id'],
                        'fee' => '-' . $apply['monthly_fee'],
                        'type' => 3,
                        'balance' => $agency['balance'] - $apply['monthly_fee'],
                        'create_time' => $time
                    );
                    $db_city->insert('call_agency_balance', $balance);

                    $db_city->trans_complete();
                    if ($db_city->trans_status() === FALSE) {
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
        $data['formUrl'] = MLS_ADMIN_URL . '/call/apply/index?id=' . $agency['id'];

        $this->load->view('call/apply/recover', $data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
