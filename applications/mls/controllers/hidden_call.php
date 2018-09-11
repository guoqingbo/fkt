<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 城市 Class
 *
 * 城市控制器
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      Lion
 */
class hidden_call extends MY_Controller
{
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
        //加载楼盘模型类
        $this->load->model('hidden_call_model');
        //出售房源类
        $this->load->model('sell_house_model');
        //出租房源类
        $this->load->model('rent_house_model');
        //公司门店类
        $this->load->model('agency_model');
        //经纪人类
        $this->load->model('broker_info_model');
    }
    /**
     * 隐号拨打检测
     *
     * @access  public
     * @param  void
     * @return  void
     * $type 0 判断余额是否小于5元，1判断是否还有5天要交月租
     */
    public function call_check($type = 0)
    {
        //模板使用数据
        $data = array();
        //post参数
        $post_param = $this->input->post(NULL, TRUE);
        // 1.检查门店是否开通引号拨打
        // 2.检查号码是否停用
        // 3.检查门店开通号码是否全被占用
        // 4.检查账户余额是否小于20元
        // 5.检查门店是否还有5天要交月租
        // 6.引号正常则调用三方接口，生成虚拟号，显示余额
        $agency_id = $this->user_arr['agency_id'];
        $phone = $this->hidden_call_model->get_phone_num_by_agencyid($agency_id);//获取门店虚拟号使用情况
        //计算门店通话时长总剩余时间
        $tatal_talk_time = $phone['balance'] / 0.08;
        //计算门店人均通话时长剩余时间
        $per_talk_time = $tatal_talk_time / $phone['phone_num'];
        $per_talk_time = number_format($per_talk_time, 2);

        $busy_phone_num = $this->hidden_call_model->get_busyphone_by_agencyid($agency_id);
        if ($type < 2) {//是否跳过的判断
            $monthly_rent = $this->hidden_call_model->get_monthly_rent_by_agencyid($agency_id);
            //月租数据重构
            if (is_array($monthly_rent) && !empty($monthly_rent)) {
                $interval_day_arr = array();
                $now_time = strtotime(date('Y-m-d'));
                $rent_monthly_tip = '';
                foreach ($monthly_rent as $key => $val) {
                    $interval_day = ceil(($val['end_time'] - $now_time) / 60 / 60 / 24);

                    if ($interval_day_arr[$interval_day]['interval_day'] == $interval_day) {
                        $interval_day_arr[$interval_day]['phone_num'] += $val['phone_num'];
                    } else {
                        $interval_day_arr[$interval_day]['interval_day'] = $interval_day;
                        $interval_day_arr[$interval_day]['phone_num'] = $val['phone_num'];
                    }
                }
                foreach ($interval_day_arr as $key => $val) {
                    $rent_monthly_tip .= "温馨提示：您有" . $val['phone_num'] . "个号码还有" . $val['interval_day'] . "天，要交月租了</br>";
                }
                $rent_monthly_tip .= "请确保您的账户余额充足，以免影响您的正常使用</br>";
            }
        }

        if (empty($phone)) {//未开通隐号拨打
            $data['status'] = 'no_enable';
        } elseif ($phone['phone_num'] <= 0 || $phone['balance'] <= 0) {//隐号拨打停用
            $data['status'] = 'disable';
        } elseif ($phone['phone_num'] > 0 && $phone['phone_num'] <= $busy_phone_num) {//门店虚拟号申请数量全部占用
            $data['status'] = 'busy';
        } elseif ($type < 1 && $phone['balance'] < 20) {//余额小于20元时，提醒
            $data['status'] = 'balance_not_enough';
        } else {
            $effective_phone = $this->hidden_call_model->get_effective_phone();//获得虚拟号
            if (is_array($effective_phone) && !empty($effective_phone)) {

                $data['virtual_phone'] = $effective_phone['virtual_phone'];
                $bind_time = $this->hidden_call_model->get_bind_time();//获取虚拟号绑定时间
                $bind_time_str = "请使用此号码拨打，该号码将保留{$bind_time}分钟，{$bind_time}分钟后未使用，则自动失效，需重新点击获取";
                $data['blind_time_str'] = $bind_time_str;

                //插入绑定虚拟号
                $bind_phone_arr = array(
                    'broker_id' => $this->user_arr['broker_id'],
                    'agency_id' => $this->user_arr['agency_id'],
                    'company_id' => $this->user_arr['company_id'],
                    'broker_phone' => $this->user_arr['phone'],
                    'house_id' => $post_param['house_id'],
                    'owner_phone' => $post_param['telno1'],
                    'virtual_phone' => $effective_phone['virtual_phone'],
                    'deadline' => time() + $bind_time * 60,
                    'create_time' => time()
                );

                //调用三方虚拟号绑定接口
                $vpost_arr = $bind_phone_arr;
                $vpost_arr['credit'] = $per_talk_time > 1 ? $per_talk_time * 60 : 60;//通话时长是否大于一分钟
                $bind_result = $this->hidden_call_model->vpost_bindnumber($vpost_arr);
                if ($bind_result['errorcode'] === 0) {
                    $bind_phone_arr['deadline'] = time() + $bind_time * 60;
                    $bind_phone_arr['create_time'] = time();
                    $bind_phone_arr['bindid'] = $bind_result['bindid'];
                    $this->hidden_call_model->insert_bind_phone($bind_phone_arr);
                    //更新虚拟号状态
                    $update_data = array(
                        'status' => 1,//0.未使用 1.绑定 2.通话中
                        'bind_time' => time(),
                        'broker_id' => $this->user_arr['broker_id'],
                        'agency_id' => $this->user_arr['agency_id'],
                        'company_id' => $this->user_arr['company_id'],
                        'bindid' => $bind_result['bindid']
                    );
                    $this->hidden_call_model->update_bind_phone("virtual_phone = {$effective_phone['virtual_phone']}", $update_data);
                    $data['status'] = 'normal';
                } else {
                    $data['status'] = 'fail_bind';
                }
            } else {
                $data['status'] = 'no_effective_phone';
            }
        }
        $data['rent_monthly_tip'] = $rent_monthly_tip;
        $data['balance'] = $phone['balance'];
        $data['per_talk_time'] = $per_talk_time;
        $data['bindid'] = $bind_result['bindid'];
        echo json_encode($data);
    }

    /**
     * 解除绑定
     *
     * @access  public
     * @param  void
     * @return  void
     */
    public function unbindnumber()
    {
        $post_param = $this->input->post(NULL, TRUE);
        //获取虚拟号绑定关系
        $cond_where = "bindid = '{$post_param['bindid']}'";
        $bind_info = $this->hidden_call_model->get_one_call_bind_phone($cond_where);
        if (!empty($bind_info)) {
            //调用解除第三方的绑定
            $result = $this->hidden_call_model->vpost_unbindnumber($post_param);
            if (!empty($result)) {
                //更新虚拟号状态
                $update_data = array(
                    'status' => 1,//0.未使用 1.绑定2.通话中
                    'bind_time' => 0,
                    'broker_id' => "",
                    'agency_id' => "",
                    'company_id' => "",
                    'bindid' => "",
                );
                $update_result = $this->hidden_call_model->update_bind_phone("bindid = '{$post_param['bindid']}'", $update_data);
                $result['status'] = 'success';
            } else {
                $result['status'] = 'unbind_fail';
            }
            echo json_encode($result);
        }
    }

    public function call_record()
    {
        $broker_info = $this->user_arr;
        $broker_id = $this->user_arr['broker_id'];
        //模板使用数据
        $data = array();
        $data['user_menu'] = $this->user_menu;
        //根据经济人总公司编号获取全部分店信息
        $company_id = intval($broker_info['company_id']);//获取总公司编号
        $agency_id = intval($broker_info['agency_id']);//获取总公司编号
        $data['company_id'] = $company_id;
        //获取当前经纪人在官网注册时的公司和门店名
        //$register_info = $this->broker_info_model->get_register_info_by_brokerid(intval($broker_info['id']));
        //$data['register_info'] = $register_info;
        //post参数
        $post_param = $this->input->post(NULL, TRUE);
        //根据权限role_id获得当前经纪人的角色，判断店长
        $role_level = intval($this->user_arr['role_level']);
        //店长
        if (is_int($role_level) && $role_level == 6) {
            $agency = $this->agency_model->get_by_id($agency_id);
            $agency_name = $agency['name'];
            $data['agency_list'] = array(
                array(
                    'agency_id' => $agency['id'],
                    'agency_name' => $agency['name']
                )
            );
            if ($post_param['post_agency_id']) {  //根据门店编号获取经纪人列表数组
                $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($post_param['post_agency_id']);
            }
            //店长以上的获取全部分公司信息
        } else {
            //根据数据范围，获得门店数据
            $this->load->model('agency_permission_model');
            $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
            $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_review_daily');
            $all_access_agency_ids = '';
            if (is_full_array($access_agency_ids_data)) {
                foreach ($access_agency_ids_data as $k => $v) {
                    $all_access_agency_ids .= $v['sub_agency_id'] . ',';
                }
                $all_access_agency_ids .= $this->user_arr['agency_id'];
                $all_access_agency_ids = trim($all_access_agency_ids, ',');
            } else {
                $all_access_agency_ids = $this->user_arr['agency_id'];
            }
            // $cond_where .= " AND agency_id in (" . $all_access_agency_ids . ")";

            $this->load->model('agency_model');
            $data['agency_list'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
            if ($post_param['post_agency_id']) {
                $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($post_param['post_agency_id']);
            }
        }
        //在没有post_broker_id时，应该设置为'0'
        if (!isset($post_param['post_broker_id'])) {
            //$post_param['post_broker_id'] = $this->user_arr['broker_id'];
            $post_param['post_broker_id'] = '0';
        }
        if (!isset($post_param['post_agency_id'])) {
            $post_param['post_agency_id'] = $this->user_arr['agency_id'];
        }
        //默认公司
        $post_param['post_company_id'] = $this->user_arr['company_id'];
        $data['post_param'] = $post_param;
        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
        $this->_init_pagination($page);
        //查询通话录音条件
        $cond_where = "create_time > 0 and end_ticket != ''";
        //表单提交参数组成的查询条件
        $cond_where_ext = $this->_get_cond_str($post_param);
        $cond_where .= $cond_where_ext;
        //符合条件的总行数
        $this->_total_count =
            $this->hidden_call_model->bind_count_by($cond_where);
        //计算总页数
        $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

        //获取列表内容
        $list = $this->hidden_call_model->get_call_bind($cond_where, $this->_offset, $this->_limit);
        if (is_full_array($list)) {
            $hidden_call = $this->config->item('hidden_call');
            foreach ($list as $k => $v) {
                $broker = $this->broker_info_model->get_by_broker_id($v['broker_id']);
                $agency = $this->agency_model->get_by_id($v['agency_id']);
                $end_ticket = json_decode($v['end_ticket'], true);
                $list[$k]['broker_name'] = $broker['truename'];
                $list[$k]['agency_name'] = $agency['agency_name'];
                $list[$k]['record_url'] = $hidden_call['record_prefix'] . $end_ticket['rec_path'];
                unset($end_ticket);
            }
        }
        $data['list'] = $list;

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

        //页面标题
        $data['page_title'] = '通话录音列表';

        //需要加载的css
        $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
            . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css,mls/css/v1.0/myStyle.css');
        //需要加载的JS
        $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls/js/v1.0/jquery.validate.min.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
            . 'mls/js/v1.0/backspace.js');
        //print_r($data);
        $this->view('hidden_call/call_record', $data);
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

    /**
     * 出售列表条件
     * 根据表单提交参数，获取查询条件
     */
    private function _get_cond_str($form_param)
    {
        $cond_where = '';
        //时间条件
        if (isset($form_param['start_date_begin']) && $form_param['start_date_begin']) {
            $start_time = strtotime($form_param['start_date_begin'] . " 00:00:00");
            $cond_where .= " AND create_time >= '" . $start_time . "'";
        }

        if (isset($form_param['start_date_end']) && $form_param['start_date_end']) {
            $end_time = strtotime($form_param['start_date_end'] . " 23:59:59");
            $cond_where .= " AND create_time <= '" . $end_time . "'";
        }
        if (isset($form_param['comment']) && $form_param['comment']) {
            if ($form_param['comment'] == 1) {
                $cond_where .= " AND comment_broker_id = 0";
            } else {
                $cond_where .= " AND comment_broker_id > 0";
            }
        }
        //经纪人
        if (!empty($form_param['post_broker_id']) && $form_param['post_broker_id'] != '') {
            $broker_id = intval($form_param['post_broker_id']);
            $cond_where .= " AND broker_id = '" . $broker_id . "'";
        }
        if (!empty($form_param['post_agency_id']) && $form_param['post_agency_id'] != '') {
            $agency_id = intval($form_param['post_agency_id']);
            $cond_where .= " AND agency_id = '" . $agency_id . "'";
        }
        if (!empty($form_param['post_company_id']) && $form_param['post_company_id'] != '') {
            $company_id = intval($form_param['post_company_id']);
            $cond_where .= " AND company_id = '" . $company_id . "'";
        }
        return $cond_where;
    }
}
/* End of file abraod.php */
/* Location: ./application/mls/controllers/abraod.php */
