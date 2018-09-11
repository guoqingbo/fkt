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
class Bargain extends MY_Controller
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
     * 每页条目数
     *
     * @access private
     * @var int
     */
    private $_limit1 = 2;

    /**
     * 权证每页条目数
     *
     * @access private
     * @var int
     */
    private $_limit2 = 5;

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
     * 特殊配置选项
     * @access private
     * @var array
     */

    private $_spec_seller_lacks = [
        7 => 'id_card',
        8 => 'marry_info',
    ];

    private $_spec_buyer_lacks = [
        1 => 'id_card',
        2 => 'marry_info',
    ];

    /**
     * 解析函数
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        //加载成交模型类
        $this->load->model('bargain_model');
        //加载业绩分成模型类
        $this->load->model('bargain_divide_model');
        //加载成交跟进模型类
        $this->load->model('bargain_log_model');
        //加载实收实付MODEL
        $this->load->model('replace_payment_model');
        //加载取付款MODEL
        $this->load->model('payment_model');
        //加载成交基本配置MODEL
        $this->load->model('bargain_config_model');
        //加载经纪人MODEL
        $this->load->model('signatory_info_model');
        //加载门店MODEL
        $this->load->model('department_model');
        //加载配置项MODEL
        $this->load->model('house_config_model');
        //加载出售房源MODEL
        $this->load->model('sell_house_model');
        //加载出租房源MODEL
        $this->load->model('rent_house_model');
        //加载合作MODEL
        $this->load->model('cooperate_model');
        //加载求购MODEL
        $this->load->model('buy_customer_model');
        //加载求租MODEL
        $this->load->model('rent_customer_model');
        //加载区属MODEL
        $this->load->model('district_model');
        //加载权证MODEL
        $this->load->model('transfer_model');
        //加载成交权证MODEL
        $this->load->model('bargain_transfer_model');
        //操作日志MODEL
        $this->load->model('signatory_operate_log_model');
        //加载数据字典
        $this->load->model('dictionary_model');
        //加载预约模块
        $this->load->model('contract_model');
        //加载门店模块
        $this->load->model('agency_model');
        //银行模块
        $this->load->model('bank_account_model');
        //权限
        if (is_full_array($this->user_arr)) {
            $this->load->model('signatory_purview_model');
            $this->signatory_purview_model->set_signatory_id($this->user_arr['signatory_id'], $this->user_arr['company_id']);
        }
    }

    /**
     * 出售列表条件
     * 根据表单提交参数，获取查询条件
     */
    private function _get_cond_str($form_param)
    {
        $cond_where = '`is_del` = 0';

        $keyword_type = isset($form_param['keyword_type']) ? intval($form_param['keyword_type']) : 0;
        $keyword = isset($form_param['keyword']) ? trim($form_param['keyword']) : "";
        if ($keyword) {
            if ($keyword_type == 1) {
                //成交编号
                $cond_where .= " AND `bargain`.number like '%" . $keyword . "%'";
            } elseif ($keyword_type == 2) {
                //房源编号
                $cond_where .= " AND `bargain`.house_id like '%" . $keyword . "%'";
            } elseif ($keyword_type == 3) {
                //物业地址
                $cond_where .= " AND `bargain`.house_addr like '%" . $keyword . "%'";
            } elseif ($keyword_type == 4) {
                //楼盘名称
                $cond_where .= " AND `bargain`.block_name like '%" . $keyword . "%'";
            } elseif ($keyword_type == 5) {
                //客户姓名
                $cond_where .= " AND (`bargain`.customer like '%" . $keyword . "%' OR `bargain`.owner like '%" . $keyword . "%')";
            } elseif ($keyword_type == 6) {
                //签约人
                $cond_where .= " AND `bargain`.signatory_name like '%" . $keyword . "%'";
            } elseif ($keyword_type == 7) {
                //房客来源
                $cond_where .= " AND `bargain`.tenant_source like '%" . $keyword . "%'";
            } elseif ($keyword_type == 8) {
                //结佣方
                $cond_where .= " AND `bargain`.commission_person like '%" . $keyword . "%'";
            } elseif ($keyword_type == 9) {
                //托管编号
                $cond_where .= " AND `bargain`.trust_number like '%" . $keyword . "%'";
            }
        }
        //经纪人门店姓名
        if (isset($form_param['agency_name_a']) && $form_param['agency_name_a']) {
            $cond_where .= " AND (`bargain`.agency_name_a like'%" . $form_param['agency_name_a'] . "%') OR (`bargain`.agency_name_b like'%" . $form_param['agency_name_a'] . "%')";
        }
        //成交类别
        $bargain_type = isset($form_param['bargain_type']) ? trim($form_param['bargain_type']) : '';
        if ($bargain_type) {
            $cond_where .= " AND bargain_type = '" . $bargain_type . "'";
        }
        $is_check = isset($form_param['is_check']) ? $form_param['is_check'] : 0;
        //审核状态
        if ($is_check) {
            $cond_where .= " AND `bargain`.is_check = '" . $is_check . "'";
        }
        //时间条件
        $date_type = isset($form_param['date_type']) ? intval($form_param['date_type']) : 0;
        $start_time = isset($form_param['start_time']) && !empty($form_param['start_time']) ? strtotime($form_param['start_time'] . ' 0:0:0') : "";
        $end_time = isset($form_param['end_time']) && !empty($form_param['end_time']) ? strtotime($form_param['end_time'] . ' 23:59:59') : "";
        if ($date_type == 1) {
            //成交日
            if ($start_time) {
                $cond_where .= " AND `bargain`.signing_time >= '" . $start_time . "'";
            }
            if ($end_time) {
                $cond_where .= " AND `bargain`.signing_time <= '" . $end_time . "'";
            }
        } elseif ($date_type == 2) {
            //结佣日
            if ($start_time) {
                $cond_where .= " AND `bargain`.commission_time >= '" . $start_time . "'";
            }
            if ($end_time) {
                $cond_where .= " AND `bargain`.commission_time <= '" . $end_time . "'";
            }
        } elseif ($date_type == 3) {
            //结盘日
            if ($start_time) {
                $cond_where .= " AND `bargain`.completed_time >= '" . $start_time . "'";
            }
            if ($end_time) {
                $cond_where .= " AND `bargain`.completed_time <= '" . $end_time . "'";
            }
        } elseif ($date_type == 4) {
            //风控日
            if ($start_time) {
                $cond_where .= " AND `bargain`.risk_control_time >= '" . $start_time . "'";
            }
            if ($end_time) {
                $cond_where .= " AND `bargain`.risk_control_time <= '" . $end_time . "'";
            }
        } elseif ($date_type == 5) {
            //权证日
            if ($start_time) {
                $cond_where .= " AND `bargain`.warrant_time >= '" . $start_time . "'";
            }
            if ($end_time) {
                $cond_where .= " AND `bargain`.warrant_time <= '" . $end_time . "'";
            }
        } elseif ($date_type == 6) {
            //创建日
            if ($start_time) {
                $cond_where .= " AND `bargain`.createtime >= '" . $start_time . "'";
            }
            if ($end_time) {
                $cond_where .= " AND `bargain`.createtime <= '" . $end_time . "'";
            }
        }
        //收款状态
        $collect_status = isset($form_param['collect_status']) ? intval($form_param['collect_status']) : 0;
        if ($collect_status == 1) {
            //有效全部
        } elseif ($collect_status == 2) {
            //未结佣
            $cond_where .= " AND `bargain`.is_commission = '0'";
        } elseif ($collect_status == 3) {
            //已结佣
            $cond_where .= " AND `bargain`.is_commission = '1'";
        } elseif ($collect_status == 4) {
            //未结盘
            $cond_where .= " AND `bargain`.is_completed = '0'";
        } elseif ($collect_status == 5) {
            //已结盘
            $cond_where .= " AND `bargain`.is_completed = '1'";
        } elseif ($collect_status == 6) {
            //撤单
            $cond_where .= " AND `bargain`.is_completed = '2'";
        }
        //成交id
        $number = isset($form_param['number']) ? trim($form_param['number']) : '';
        if ($number) {
            $cond_where .= " AND number like '%" . $number . "%'";
        }

        //房源编号
        $house_id = isset($form_param['house_id']) ? trim($form_param['house_id']) : "";
        if ($house_id) {
            $house_id = substr($house_id, 2);
            $cond_where .= " AND `id` = '" . $house_id . "'";
        }

        //报备时间
        if (isset($form_param['starttime']) && $form_param['starttime']) {
            $cond_where .= " AND `bargain`.createtime >= '" . $form_param['starttime'] . "'";
        }

        if (isset($form_param['endtime']) && !empty($form_param['endtime'])) {
            $cond_where .= " AND `bargain`.createtime <= '" . $form_param['endtime'] . "'";
        }

        //楼盘
        if (isset($form_param['block_id']) && !empty($form_param['block_id'])) {
            $cond_where .= " AND `bargain`.block_id = '" . $form_param['block_id'] . "'";
        }

        //转正状态
        if (isset($form_param['status']) && !empty($form_param['status'])) {
            $cond_where .= " AND `bargain`.status = '" . $form_param['status'] . "'";
        }

        //业主姓名或客户姓名
        if (isset($form_param['owner_type']) && $form_param['owner_type']) {
            if ($form_param['owner_type'] == 1) {
                if (isset($form_param['owner_name']) && $form_param['owner_name']) {
                    $cond_where .= " AND `bargain`.owner like '%" . $form_param['owner_name'] . "%'";
                }
            } else {
                if (isset($form_param['owner_name']) && $form_param['owner_name']) {
                    $cond_where .= " AND `bargain`.customer like '%" . $form_param['owner_name'] . "%'";
                }
            }
        }

        //签约门店
        if (isset($form_param['department_id_a']) && $form_param['department_id_a']) {
            $cond_where .= " AND `bargain`.department_id_a = '" . $form_param['department_id_a'] . "'";
        }

        //签约人
        if (isset($form_param['signatory_id_a']) && $form_param['signatory_id_a']) {
            $cond_where .= " AND `bargain`.signatory_id_a = '" . $form_param['signatory_id_a'] . "'";
        }


        //录入门店
        if (isset($form_param['enter_company_id']) && $form_param['enter_company_id']) {
            $cond_where .= " AND `bargain`.enter_company_id = '" . $form_param['enter_company_id'] . "'";
        }

        //录入门店
        if (isset($form_param['enter_department_id']) && $form_param['enter_department_id']) {
            $cond_where .= " AND `bargain`.enter_department_id = '" . $form_param['enter_department_id'] . "'";
        }

        //录入人
        if (isset($form_param['enter_signatory_id']) && $form_param['enter_signatory_id']) {
            $cond_where .= " AND `bargain`.enter_signatory_id = '" . $form_param['enter_signatory_id'] . "'";
        }

        //流程完成日期
        if (!empty($form_param['stage_id'])
            && (!empty($form_param['transfer_start_time'])
                or !empty($form_param['transfer_end_time']))
        ) {
            //根据流程名id和流程完结时间查询成交id,
            $ids1 = $this->bargain_transfer_model->get_bargain_ids($form_param);
            $idstr = trim(implode(',', $ids1), ',');
            $cond_where .= " AND `bargain`.id in (" . $idstr . ")";

        }
        //付款方式,监管银行,贷款方式
        if (!empty($form_param['buy_type']) || !empty($form_param['loan_bank']) || !empty($form_param['loan_type'])) {
            $ids2 = $this->payment_model->get_payment_ids($form_param);
            $idstr = trim(implode(',', $ids2), ',');
            $cond_where .= " AND `bargain`.id in (" . $idstr . ")";
        }
        return $cond_where;
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
     * 初始化分页参数
     *
     * @access public
     * @param  int $current_page
     * @param  int $page_size
     * @return void
     */
    private function _init_pagination1($current_page = 1, $page_size = 0)
    {
        /** 当前页 */
        $this->_current_page = ($current_page && is_numeric($current_page)) ?
            intval($current_page) : 1;

        /** 每页多少项 */
        $this->_limit1 = ($page_size && is_numeric($page_size)) ?
            intval($page_size) : $this->_limit1;

        /** 偏移量 */
        $this->_offset = ($this->_current_page - 1) * $this->_limit1;

        if ($this->_offset < 0) {
            redirect(base_url());
        }
    }

    /**
     * 初始化分页参数
     *
     * @access public
     * @param  int $current_page
     * @param  int $page_size
     * @return void
     */
    private function _init_pagination2($current_page = 1, $page_size = 0)
    {
        /** 当前页 */
        $this->_current_page = ($current_page && is_numeric($current_page)) ?
            intval($current_page) : 1;

        /** 每页多少项 */
        $this->_limit2 = ($page_size && is_numeric($page_size)) ?
            intval($page_size) : $this->_limit2;

        /** 偏移量 */
        $this->_offset = ($this->_current_page - 1) * $this->_limit2;

        if ($this->_offset < 0) {
            redirect(base_url());
        }
    }

    /**
     * 成交报备
     * @access public
     * @return void
     */
    public function report($type = 1)
    {

        //模板使用数据
        $data = array();

        //树型菜单
        $data['user_tree_menu'] = $this->user_tree_menu;

        //页面标题
        $data['page_title'] = '成交报备';

        //获取成交配置信息
        $data['config'] = $this->bargain_config_model->get_config();

        //post参数
        $post_param = $this->input->post(NULL, TRUE);
        $post_param['type'] = $type;
        $data['post_param'] = $post_param;

        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
        $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
        $this->_init_pagination($page, $pagesize);

        $role_level = $this->user_arr['role_level'];
        if ($role_level < 6) //公司
        {
            //所属公司
            $post_param['enter_company_id'] = $this->user_arr['company_id'];
        } else if ($role_level < 8) //门店
        {
            //所属公司
            $post_param['enter_company_id'] = $this->user_arr['company_id'];
            //所属门店
            $post_param['enter_department_id'] = $this->user_arr['department_id'];
        } else {
            //所属公司
            $post_param['enter_company_id'] = $this->user_arr['company_id'];
            //所属门店
            $post_param['enter_department_id'] = $this->user_arr['department_id'];
            //所属经纪人
            $post_param['enter_signatory_id'] = $this->user_arr['signatory_id'];
        }

        //表单提交参数组成的查询条件
        $cond_where = $this->_get_cond_str($post_param);

        //查询交易类型 出售为1  出租为2
        //审核状态 0 未进入审核 1 未审核 2 审核通过 3 审核未通过 4 作废
        $cond_where .= " AND `type` = " . $type;
        $data['type'] = $type;

        /**
         * 数据范围
         * 1、店长以上权限看公司
         * 2、店长及店长秘书权限查看本门店
         * 3、店长秘书以下没有权限
         */
        //获取访问菜单
        $range_menu = $this->bargain_model->get_range_menu_by_role_level(
            $this->user_arr, $post_param['department_id_a']);
        //门店数据
        $data['departments'] = $range_menu['departments'];
        //经纪人数据
        $data['signatorys'] = $range_menu['signatorys'];

        $report_add_per = $this->signatory_purview_model->check('110');
        $report_edit_per = $this->signatory_purview_model->check('111');
        $report_delete_per = $this->signatory_purview_model->check('112');
        $report_report_per = $this->signatory_purview_model->check('113');
        $data['auth'] = array(
            'add' => $report_add_per, 'edit' => $report_edit_per,
            'delete' => $report_delete_per, 'report' => $report_report_per
        );
        //清除条件头尾多余空格
        $cond_where = trim($cond_where);
        //符合条件的总行数
        $this->_total_count = $this->bargain_model->count_by($cond_where);

        //计算总页数
        $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

        //获取列表内容
        $list = $this->bargain_model->get_all_by($cond_where, $this->_offset, $this->_limit);

        if (is_full_array($list)) {
            foreach ($list as $key => $val) {
                if ($type == 1) {
                    $list[$key]['house_id'] = $val['house_id'] ? format_info_id($val['house_id'], 'sell') : '—';
                } else {
                    $list[$key]['house_id'] = $val['house_id'] ? format_info_id($val['house_id'], 'rent') : '—';
                }
                $list[$key]['house_addr'] = $val['house_addr'] ? $val['house_addr'] : '—';
                $list[$key]['number'] = $val['number'] ? $val['number'] : '—';
            }
        }

        $data['list'] = $list;

        //当前页
        $data['page'] = $page;

        //分页处理
        $params = array(
            'total_rows' => $this->_total_count, //总行数
            'method' => 'post', //URL提交方式 get/html/post
            'now_page' => $this->_current_page, //当前页数
            'list_rows' => $this->_limit, //每页显示个数
        );
        //加载分页类
        $this->load->library('page_list', $params);
        //调用分页函数（不同的样式不同的函数参数）
        $data['page_list'] = $this->page_list->show('jump');

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,'
            . 'mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css,'
            . 'mls_guli/css/v1.0/bargain.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_report', $data);
    }

    /**
     * 成交报备编辑页面
     * @access public
     * @return void
     */
    public function modify_report_index($type = 1, $id = 0)
    {

        //获取成交配置信息
        $data['config'] = $this->house_config_model->get_config();

        $data['report_id'] = $id;
        $data['type'] = $type;
        //查询出报备数据
        $data['report'] = $this->bargain_model->get_by_id($id);
        $data['report']['signing_time'] = $data['report']['signing_time'] ? date('Y-m-d', $data['report']['signing_time']) : '';
        if ($type == 1) {
            $data['report']['house_id'] = $data['report']['house_id'] ? format_info_id($data['report']['house_id'], 'sell') : '';
        } else {
            $data['report']['house_id'] = $data['report']['house_id'] ? format_info_id($data['report']['house_id'], 'rent') : '';
        }

        if ($id) {
            $department_id = $data['report']['department_id_a'];
        }
        $range_menu = $this->bargain_model->get_range_menu_by_role_level(
            $this->user_arr, $department_id);
        //门店数据
        $data['departments'] = $range_menu['departments'];
        //经纪人数据
        $data['signatorys'] = $range_menu['signatorys'];

        $report_add_per = $this->signatory_purview_model->check('110');
        $report_edit_per = $this->signatory_purview_model->check('111');
        $data['auth'] = array(
            'add' => $report_add_per, 'edit' => $report_edit_per
        );

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,'
            . 'mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css,'
            . 'mls_guli/css/v1.0/bargain.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_modify_report', $data);
    }

    /**
     * 权证步骤添加页面
     * @access public
     * @return void
     */
    public function add_transfer_index($c_id = 0, $id = 0)
    {

        //获取成交配置信息
        $data['config'] = $this->house_config_model->get_config();
        //权限
        $transfer_add_per = $this->signatory_purview_model->check('68');

        $data['auth'] = array(
            'transfer_add' => $transfer_add_per
        );

        //权证配置项
        $data['stage_conf'] = $this->bargain_transfer_model->get_stage_conf();
        //权证步骤名配置
        $data['stage'] = $this->bargain_transfer_model->get_all_stage();
        //成交详情
        $data['bargain'] = $this->bargain_model->get_by_id($c_id);

        $data['transfer_info'] = $this->bargain_transfer_model->get_temp_by_id($data['bargain']['template_id']);
        $data['temp_id'] = $id;
        $data['c_id'] = $c_id;

        //步骤详情
        if ($id) {
            $data['step'] = $this->bargain_transfer_model->get_by_id($id);
            $data['step']['stage_id'] = explode(',', $data['step']['stage_id']);
            $department_id = $data['step']['remind_department_id'];
        } else {
            $data['total_step'] = $this->bargain_transfer_model->count_by(array('bargain_id' => $c_id));
            $department_id = $this->user_arr['department_id'];
        }

        $range_menu = $this->bargain_model->get_range_menu_by_role_level(
            $this->user_arr, $department_id);
        //门店数据
        $data['departments'] = $range_menu['departments'];
        //经纪人数据
        $data['signatorys'] = $range_menu['signatorys'];

        //权证步骤名配置
        $data['stage'] = $this->bargain_transfer_model->get_all_stage();
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,'
            . 'mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css,'
            . 'mls_guli/css/v1.0/bargain.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_transfer_add', $data);
    }

    /**
     * 权证步骤编辑页面
     * @access public
     * @return void
     */
    public function modify_transfer_index($c_id = 0, $id = 0)
    {

        //获取成交配置信息
        $data['config'] = $this->bargain_config_model->get_config();
        //权限
        $transfer_add_per = $this->signatory_purview_model->check('68');

        $data['auth'] = array(
            'transfer_add' => $transfer_add_per
        );

        //权证配置项
        $data['stage_conf'] = $this->bargain_transfer_model->get_stage_conf();
        //权证步骤名配置
        $data['stage'] = $this->bargain_transfer_model->get_all_stage();
        //成交详情
        $data['bargain'] = $this->bargain_model->get_by_id($c_id);

        $data['transfer_info'] = $this->bargain_transfer_model->get_temp_by_id($data['bargain']['template_id']);
        $data['temp_id'] = $id;
        $data['c_id'] = $c_id;

        //步骤详情
        if ($id) {
            $data['step'] = $this->bargain_transfer_model->get_by_id($id);
            $data['step']['stage_id'] = explode(',', $data['step']['stage_id']);
            $department_id = $data['step']['remind_department_id'];
        } else {
            $data['total_step'] = $this->bargain_transfer_model->count_by(array('bargain_id' => $c_id));
            $department_id = $this->user_arr['department_id'];
        }

        // $range_menu = $this->bargain_model->get_range_menu_by_role_level($this->user_arr, $department_id);
        //门店数据
        //$data['departments'] = $range_menu['departments'];
        //经纪人数据
        // $data['signatorys'] = $range_menu['signatorys'];
        $data['signatorys'] = $this->signatorys($this->user_arr["company_id"]);
        //权证步骤名配置
        $data['stage'] = $this->bargain_transfer_model->get_all_stage();
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,'
            . 'mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css,'
            . 'mls_guli/css/v1.0/bargain.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_transfer_modify', $data);
    }

    /**
     * 权证步骤详情页面
     * @access public
     * @return void
     */
    public function bargain_transfer_detail($id)
    {

        //权证配置项
        $data['stage_conf'] = $this->bargain_transfer_model->get_stage_conf();
        //权证步骤名配置
        $data['stage'] = $this->bargain_transfer_model->get_all_stage();

        $data['transfer_list'] = $this->bargain_transfer_model->get_by_id($id);
        //成交详情
        $data['bargain'] = $this->bargain_model->get_by_id($data['transfer_list']['bargain_id']);

        $signatory_info = $this->api_signatory_model->get_baseinfo_by_signatory_id($data['transfer_list']['signatory_id']);
        $data['transfer_list']['department_name'] = $signatory_info['department_name'];
        $data['transfer_list']['signatory_name'] = $signatory_info['truename'];

        if ($data['transfer_list']['is_remind'] == 1) {
            $signatory_info1 = $this->api_signatory_model->get_baseinfo_by_signatory_id($data['transfer_list']['remind_signatory_id']);
            $data['transfer_list']['remind_department_name'] = $signatory_info['department_name'];
            $data['transfer_list']['remind_signatory_name'] = $signatory_info['truename'];
        }

        $stage_name = explode(',', $data['transfer_list']['stage_id']);
        foreach ($stage_name as $k => $v) {
            $arr[] = $data['stage'][$v]['stage_name'];
            $data['transfer_list']['stage_name'] = implode('，', $arr);
        }
        //权证步骤名配置
        $data['stage'] = $this->bargain_transfer_model->get_all_stage();
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,'
            . 'mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css,'
            . 'mls_guli/css/v1.0/bargain.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js'
            . 'mls_guli/js/v1.0/backspace.js');
        if ($data['transfer_list']['is_remind'] == 1) {
            $this->view('/bargain/bargain_transfer_detail', $data);
        } else {
            $this->view('/bargain/bargain_transfer_detail1', $data);
        }
    }

    public function deal_manage($type = 0)
    {
        header("cache-control:no-cache,must-revalidate");
        //模板使用数据
        $data = array();
        //树型菜单
        $data['user_tree_menu'] = $this->user_tree_menu;

        //页面标题
        $data['page_title'] = '交易成交列表';

        //获取成交配置信息
        $data['config'] = $this->bargain_config_model->get_config();


        //监管银行
        $data['loan_bank'] = $this->bank_account_model->get_all_list();

        //流程名称
        $transfer_type = [
            '1' => [2],
            '2' => [1]
        ];
        $data['stage'] = $this->transfer_model->get_stages($transfer_type[$type]);
        //成交类别
        $bargain_type = $this->dictionary_model->get_all_by_dictionary_type_id(2);
        $bargain_type_arr = array();
        foreach ($bargain_type as $k => $v) {
            $bargain_type_arr[$v['id']] = $v;
        }
        $data['bargain_type'] = $bargain_type_arr;

        //post参数
        $post_param = $this->input->post(NULL, TRUE);
        //是否提交了表单数据
        $is_submit_form = false;
        if (is_full_array($post_param)) {
            $is_submit_form = true;
        }

        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
        $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
        $this->_init_pagination($page, $pagesize);
        $data['page'] = $page;

        //所属公司
        $post_param['enter_company_id'] = $this->user_arr['company_id'];

        //判断是否提交表单,设置本页搜索条件cookie
        if ($is_submit_form) {
            $deal_manage = array(
                'number' => $post_param['number'],
                'block_name' => $post_param['block_name'],
                'block_id' => $post_param['block_id'],
                'owner_type' => $post_param['owner_type'],
                'owner_name' => $post_param['owner_name'],
                'department_id_a' => $post_param['department_id_a'],
                'signatory_id_a' => $post_param['signatory_id_a'],
                'datetype' => $post_param['datetype'],
                'date' => $post_param['date'],
                'start_time' => $post_param['start_time'],
                'end_time' => $post_param['end_time'],
                'is_check' => $post_param['is_check'],
                'page' => $post_param['page'],
                'is_submit' => $post_param['is_submit'],
                'enter_company_id' => $post_param['enter_company_id'],
            );
            //区分类型 0 全部 1 一手房 2 二手房 3 托管
            if (0 == $type) {
                setcookie('deal_manage_0', serialize($deal_manage), time() + 3600 * 24 * 7, '/');
            } else if (1 == $type) {
                setcookie('deal_manage_1', serialize($deal_manage), time() + 3600 * 24 * 7, '/');
            } else if (2 == $type) {
                setcookie('deal_manage_2', serialize($deal_manage), time() + 3600 * 24 * 7, '/');
            } else if (3 == $type) {
                setcookie('deal_manage_3', serialize($deal_manage), time() + 3600 * 24 * 7, '/');
            }
        } else {
            //区分类型 0 全部 1 一手房 2 二手房 3 托管
            if (0 == $type) {
                $deal_manage_search = unserialize($_COOKIE['deal_manage0']);
            } else if (1 == $type) {
                $deal_manage_search = unserialize($_COOKIE['deal_manage_1']);
            } else if (2 == $type) {
                $deal_manage_search = unserialize($_COOKIE['deal_manage_2']);
            } else if (3 == $type) {
                $deal_manage_search = unserialize($_COOKIE['deal_manage_3']);
            }

            if (is_full_array($deal_manage_search)) {
                $post_param['number'] = $deal_manage_search['number'];
                $post_param['block_name'] = $deal_manage_search['block_name'];
                $post_param['block_id'] = $deal_manage_search['block_id'];
                $post_param['owner_type'] = $deal_manage_search['owner_type'];
                $post_param['owner_name'] = $deal_manage_search['owner_name'];
                $post_param['department_id_a'] = $deal_manage_search['department_id_a'];
                $post_param['signatory_id_a'] = $deal_manage_search['signatory_id_a'];
                $post_param['datetype'] = $deal_manage_search['datetype'];
                $post_param['date'] = $deal_manage_search['date'];
                $post_param['start_time'] = $deal_manage_search['start_time'];
                $post_param['end_time'] = $deal_manage_search['end_time'];
                $post_param['is_check'] = $deal_manage_search['is_check'];
                $post_param['page'] = $deal_manage_search['page'];
                $post_param['is_submit'] = $deal_manage_search['is_submit'];
                $post_param['enter_company_id'] = $deal_manage_search['enter_company_id'];

//                $post_param['buy_type'] = $deal_manage_search['buy_type'];
//                $post_param['loan_bank'] = $deal_manage_search['loan_bank'];
//                $post_param['loan_type'] = $deal_manage_search['loan_type'];
//                $post_param['stage_id'] = $deal_manage_search['stage_id'];
//                $post_param['transfer_start_time'] = $deal_manage_search['transfer_start_time'];
//                $post_param['transfer_end_time'] = $deal_manage_search['transfer_end_time'];
            }
        }
        $data['post_param'] = $post_param;
        //权限
        $per_num = $type == 1 ? '43' : '1';
        $bargain_add_per = $this->signatory_purview_model->check($per_num);
        $bargain_delete_per = $this->signatory_purview_model->check('3');
        $bargain_cancel_per = $this->signatory_purview_model->check('4');
        $data['auth'] = array(
            'add' => $bargain_add_per, 'edit' => $bargain_add_per,
            'delete' => $bargain_delete_per, 'cancel' => $bargain_cancel_per
        );

        //表单提交参数组成的查询条件
        $cond_where = $this->_get_cond_str($post_param);
        //$type 0全部 1一手房 2二手房 3托管
        if ($type != 0) {
            $cond_where .= " AND type = " . $type . " and is_check > 0";
        }

        $data['type'] = $type;

        //清除条件头尾多余空格
        $cond_where = trim($cond_where);

        //符合条件的总行数
        $this->_total_count = $this->bargain_model->count_by($cond_where);

        $order_key = 'id';
        $order_by = 'DESC';

        //获取列表内容
        $list = $this->bargain_model->get_all_by($cond_where, $this->_offset, $this->_limit, $order_key, $order_by);

        $data['list'] = $list;

        //分页处理
        $params = array(
            'total_rows' => $this->_total_count, //总行数
            'method' => 'post', //URL提交方式 get/html/post
            'now_page' => $this->_current_page, //当前页数
            'list_rows' => $this->_limit, //每页显示个数
        );
        //加载分页类
        $this->load->library('page_list', $params);
        //调用分页函数（不同的样式不同的函数参数）
        $data['page_list'] = $this->page_list->show('jump');

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,'
            . 'mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/deal_manage', $data);
    }

    /**
     * 录入成交报备
     * @access public
     * @return void
     */
    public function add_report()
    {
        $post_param = $this->input->post(NULL, TRUE);
        $department = $this->department_model->get_one_by(array('id' => $post_param['department_id']));
        $signatory = $this->signatory_info_model->get_by_signatory_id($post_param['signatory_id']);
        if ($post_param['id']) {
            //检查成交编号唯一性
            $where = "enter_company_id = {$this->user_arr['company_id']} and number = '{$post_param['number']}' and id != {$post_param['id']} and is_del = 0";
            $result = $this->bargain_model->get_one_by($where);
        } else {
            //检查成交编号唯一性
            $where = "enter_company_id = {$this->user_arr['company_id']} and number = '{$post_param['number']}' and is_del= 0 ";
            $result = $this->bargain_model->get_one_by($where);
        }
        if (is_full_array($result)) {
            $data['result'] = 0;
            $data['msg'] = '公司内已有该成交编号的成交！';
        } else {

            //成交添加信息数组
            $data_info = array(
                'type' => intval($post_param['type']),
                'price_type' => intval($post_param['type']) == 2 ? 1 : 0,
                'number' => trim($post_param['number']),
                'house_addr' => trim($post_param['house_addr']),
                'house_id' => $post_param['house_id'] ? substr(trim($post_param['house_id']), 2) : '',
                'block_id' => intval($post_param['block_id']),
                'block_name' => trim($post_param['block_name']),
                'remarks' => trim($post_param['remarks']),
                'signing_time' => strtotime($post_param['signing_time']),
                'company_id_a' => intval($signatory['company_id']),
                'department_id_a' => intval($post_param['department_id']),
                'department_name_a' => $department['name'],
                'signatory_id_a' => intval($post_param['signatory_id']),
                'signatory_name_a' => $signatory['truename'],
                'signatory_tel_a' => trim($post_param['phone']),
                'createtime' => time()
            );
            //根据house_id补充房源信息
            if ($data_info['house_id']) {
                if ($data_info['type'] == 1) {
                    $this->sell_house_model->set_id($data_info['house_id']);
                    $this->sell_house_model->set_search_fields(array('block_id', 'block_name', 'buildarea', 'address', 'sell_type', 'owner', 'idcare', 'telno1', 'dong', 'unit', 'door'));
                    $result = $this->sell_house_model->get_info_by_id();
                } else {
                    $this->rent_house_model->set_id($data_info['house_id']);
                    $this->rent_house_model->set_search_fields(array('block_id', 'block_name', 'buildarea', 'address', 'sell_type', 'owner', 'idcare', 'telno1', 'dong', 'unit', 'door'));
                    $result = $this->rent_house_model->get_info_by_id();
                }
                $data_info['buildarea'] = $result['buildarea'];
                $data_info['sell_type'] = $result['sell_type'];
                $data_info['owner'] = $result['owner'];
                $data_info['owner_idcard'] = $result['idcare'];
                $data_info['owner_tel'] = $result['telno1'];
            }
            if ($post_param['id']) {
                $data['result'] = $this->bargain_model->update_by_id($data_info, $post_param['id']);
                if ($data['result']) {
                    $data['msg'] = '成交报备修改成功！';
                    //操作日志
                    $add_log_param = array(
                        'company_id' => $this->user_arr['company_id'],
                        'department_id' => $this->user_arr['department_id'],
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'type' => 35,
                        'text' => '修改成交编号为' . $data_info['number'] . '的成交报备',
                        'from_system' => 1,
                        'from_ip' => get_ip(),
                        'mac_ip' => '127.0.0.1',
                        'from_host_name' => '127.0.0.1',
                        'hardware_num' => '测试硬件序列号',
                        'time' => time()
                    );
                    $this->signatory_operate_log_model->add_operate_log($add_log_param);
                } else {
                    $data['msg'] = '成交报备修改失败！';
                }
            } else {
                $data_info['enter_company_id'] = $this->user_arr['company_id'];
                $data_info['enter_department_id'] = $this->user_arr['department_id'];
                $data_info['enter_signatory_id'] = $this->user_arr['signatory_id'];
                $data['result'] = $this->bargain_model->add_info($data_info);
                if ($data['result']) {
                    $data['msg'] = '成交报备添加成功！';
                    //操作日志
                    $add_log_param = array(
                        'company_id' => $this->user_arr['company_id'],
                        'department_id' => $this->user_arr['department_id'],
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'type' => 35,
                        'text' => '新增成交编号为' . $data_info['number'] . '的成交报备',
                        'from_system' => 1,
                        'from_ip' => get_ip(),
                        'mac_ip' => '127.0.0.1',
                        'from_host_name' => '127.0.0.1',
                        'hardware_num' => '测试硬件序列号',
                        'time' => time()
                    );
                    $this->signatory_operate_log_model->add_operate_log($add_log_param);
                } else {
                    $data['msg'] = '成交报备添加失败！';
                }
            }
        }
        echo json_encode($data);
    }

    /**
     * 修改成交报备
     * @access public
     * @return void
     */
    public function edit()
    {
        $id = $this->input->get('id');
        $type = $this->input->get('type');
        $data['arr'] = $this->bargain_model->get_by_id($id);
        $data['arr']['signing_time'] = date('Y-m-d', $data['arr']['signing_time']);
        if ($type == 1) {
            $data['arr']['house_id'] = $data['arr']['house_id'] ? format_info_id($data['arr']['house_id'], 'sell') : '';
        } else {
            $data['arr']['house_id'] = $data['arr']['house_id'] ? format_info_id($data['arr']['house_id'], 'rent') : '';
        }
        $this->signatory_info_model->set_select_fields(array('signatory_id', 'truename'));
        $data['signatory_list'] = $this->signatory_info_model->get_by_department_id($data['arr']['department_id_a']);
        if (is_full_array($data['arr'])) {
            $data['result'] = 1;
        } else {
            $data['result'] = 0;
        }
        echo json_encode($data);
    }

    /**
     * 保存修改成交报备
     * @access public
     * @return void
     */
    public function save_edit_report()
    {
        $post_param = $this->input->post(NULL, TRUE);
        $department = $this->department_model->get_one_by(array('id' => $post_param['department_id']));
        $signatory = $this->signatory_info_model->get_by_signatory_id($post_param['signatory_id']);
        //检查成交编号唯一性
        $where = "enter_company_id = {$this->user_arr['company_id']} and number = '{$post_param['number']}' and id != {$post_param['id']} and is_del = 0";
        $result = $this->bargain_model->get_one_by($where);
        if (is_full_array($result)) {
            $data['result'] = 0;
            $data['msg'] = '公司内已有该成交编号的成交！';
        } else {//成交添加信息数组
            $data_info = array(
                'type' => intval($post_param['type']),
                'number' => intval($post_param['number']),
                'house_addr' => trim($post_param['house_addr']),
                'house_id' => substr(trim($post_param['house_id']), 2),
                'block_id' => intval($post_param['block_id']),
                'block_name' => trim($post_param['block_name']),
                'remarks' => trim($post_param['remarks']),
                'signing_time' => strtotime($post_param['signing_time']),
                'company_id_a' => intval($signatory['company_id']),
                'department_id_a' => intval($post_param['department_id']),
                'department_name_a' => $department['name'],
                'signatory_id_a' => intval($post_param['signatory_id']),
                'signatory_name_a' => $signatory['truename'],
                'createtime' => time()
            );
            //根据house_id补充房源信息
            if ($data_info['house_id']) {
                if ($data_info['type'] == 1) {
                    $this->sell_house_model->set_id($data_info['house_id']);
                    $this->sell_house_model->set_search_fields(array('block_id', 'block_name', 'buildarea', 'address', 'sell_type', 'owner', 'idcare', 'telno1', 'dong', 'unit', 'door'));
                    $result = $this->sell_house_model->get_info_by_id();
                } else {
                    $this->rent_house_model->set_id($data_info['house_id']);
                    $this->rent_house_model->set_search_fields(array('block_id', 'block_name', 'buildarea', 'address', 'sell_type', 'owner', 'idcare', 'telno1', 'dong', 'unit', 'door'));
                    $result = $this->rent_house_model->get_info_by_id();
                }
                $data_info['buildarea'] = $result['buildarea'];
                $data_info['sell_type'] = $result['sell_type'];
                $data_info['owner'] = $result['owner'];
                $data_info['owner_idcard'] = $result['idcare'];
                $data_info['owner_tel'] = $result['telno1'];
            }
            $data['result'] = $this->bargain_model->update_by_id($data_info, $post_param['id']);
            if ($data['result']) {
                $data['msg'] = '成交报备修改成功！';
                //操作日志
                $add_log_param = array(
                    'company_id' => $this->user_arr['company_id'],
                    'department_id' => $this->user_arr['department_id'],
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'type' => 35,
                    'text' => '修改成交编号为' . $data_info['number'] . '的成交报备',
                    'from_system' => 1,
                    'from_ip' => get_ip(),
                    'mac_ip' => '127.0.0.1',
                    'from_host_name' => '127.0.0.1',
                    'hardware_num' => '测试硬件序列号',
                    'time' => time()
                );
                $this->signatory_operate_log_model->add_operate_log($add_log_param);
            } else {
                $data['msg'] = '成交报备修改失败！';
            }
        }
        echo json_encode($data);
    }

    /**
     * 保存修改成交报备
     * @access public
     * @return void
     */
    public function update_report_status()
    {
        $id = $this->input->get('id');
        $rs = $this->bargain_model->update_by_id(array('status' => 2, 'is_check' => 1), $id);
        if ($rs) {
            //成交跟进——删除成交
            $data = array(
                'c_id' => $id,
                'type_name' => "成交录入",
                'content' => "本日对该成交信息进行转正。",
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'updatetime' => time()
            );
            $this->bargain_log_model->add_info($data);
            $this->bargain_model->set_select_fields(array('number'));
            $result = $this->bargain_model->get_by_id($id);
            //操作日志
            $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'department_id' => $this->user_arr['department_id'],
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '转正成交编号为' . $result['number'] . '的交易成交。',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
            );
            $this->signatory_operate_log_model->add_operate_log($add_log_param);
            echo json_encode(array('result' => '1', 'number' => $result['number']));
        } else {
            echo json_encode(array('result' => '0'));
        }
    }

    /**
     * 录入成交
     * @access public
     * @return void
     */
    public function bargain_add($type = 0, $id = 0)
    {
        $data = array();
        $data['type'] = $type;

        $data['specSellerLacks'] = $this->_spec_seller_lacks;
        $data['specBuyerLacks'] = $this->_spec_buyer_lacks;

        //菜单栏
        $data['user_tree_menu'] = $this->purview_tab_model->get_tree_menu('bargain', 'deal_manage');

        //获取基础配置信息
        $data['config'] = $this->bargain_config_model->get_config();

        $per_num = $type == 1 ? '43' : '1';

        $bargain_edit_per = $this->signatory_purview_model->check($per_num);
        $bargain_delete_per = $this->signatory_purview_model->check('3');
        $bargain_cancel_per = $this->signatory_purview_model->check('4');
        $data['auth'] = array(
            'edit' => $bargain_edit_per, 'delete' => $bargain_delete_per, 'cancel' => $bargain_cancel_per
        );

        //银行
        $this->bank_account_model->set_select_fields(array('id', 'card_no', 'bank_name', 'bank_deposit'));
        $data['mortgage_bank'] = $data['loan_bank'] = $data['agent_bank'] = $this->bank_account_model->get_all_list();

        //页面标题
        $data['page_title'] = '录入成交';

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/myStyle.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js'
            . 'mls_guli/js/v1.0/backspace.js');
        if ($type == 2) {  //二手房

            //获取公司下所有签约人员，权证人员
            $data['warrant_persons'] = $data['signatorys'] = $this->signatorys($this->user_arr["company_id"]);

            //配置项，理财人员
            $data['finances'] = $this->dictionary_model->get_all_by_dictionary_type_id(5);
            // 获取公司下所有财务人员
            $data['finance_persons'] = $this->signatorys($this->user_arr["company_id"], array(1, 3));

            $this->view("bargain/bargain_modify", $data);
        } elseif ($type == 1) {

            //获取公司下所有签约人员，权证人员
            $data['warrant_persons'] = $this->signatorys($this->user_arr["company_id"], array(7));

            $this->view("bargain/bargain_modify_one", $data);
        } else {

            $this->view("bargain/bargain_add", $data);
        }

    }

//成交打印
    public function bargain_print($id = 0)
    {

        //获取成交配置信息
        $data['config'] = $this->bargain_config_model->get_config();
        //银行
        $data['mortgage_bank'] = $data['loan_bank'] = $this->dictionary_model->get_all_by_dictionary_type_id(1);
        $cond_where = "status = 1";
        $this->bank_account_model->set_select_fields(array('id', 'card_no', 'bank_name', 'bank_deposit'));
        $data['mortgage_bank'] = $data['loan_bank'] = $this->bank_account_model->get_all_list();

        //成交详情
        $bargain = $this->bargain_model->get_by_id($id);
        //取付款信息
        $where = "bargain_id = " . $id . " and status = 2";
        $paymentinfo = $this->payment_model->get_one_by_cond($where);
        if ($paymentinfo) {
            if ($paymentinfo['buy_type'] == 1) {
                $bargain['tatal_money'] = $paymentinfo['tatal_money'];
                $bargain['payment_once_time'] = $paymentinfo['payment_once_time'];
            }
            if ($paymentinfo['buy_type'] == 2) {
                $bargain['purchase_money'] = array_filter(explode(',', $paymentinfo['purchase_money']));
                $bargain['purchase_condition'] = array_filter(explode(',', $paymentinfo['purchase_condition']));
                $bargain['payment_period_time'] = $paymentinfo['payment_period_time'];
            }
            if ($paymentinfo['buy_type'] == 3) {
                $bargain['loan_type'] = $paymentinfo['loan_type'];
                $bargain['first_time'] = $paymentinfo['first_time'];
                $bargain['first_money'] = $paymentinfo['first_money'];
                $bargain['spare_money'] = $paymentinfo['spare_money'];
            }
            $bargain['buy_type'] = $paymentinfo['buy_type'];
            $bargain['loan_bank'] = $paymentinfo['loan_bank'];

            $bargain['collect_condition'] = array_filter(explode(',', $paymentinfo['collect_condition']));
            $bargain['collect_money'] = array_filter(explode(',', $paymentinfo['collect_money']));
        }
        $data['bargain'] = $bargain;

        //代收付信息
        $this->replace_payment_model->set_tbl('replace_payment');
        $data['replace_flow'] = $this->replace_payment_model->get_list_by_cond1(array('c_id' => $id));
        if (is_full_array($data['replace_flow'])) {
            foreach ($data['replace_flow'] as $key => $val) {
                if (mb_strlen($val['remark'], 'UTF8') > 8) {
                    $data['replace_flow'][$key]['remark'] = mb_substr($val['remark'], 0, 7, 'UTF8') . '...';
                }
                if ($val['replace_type'] == 1) {
                    $data['replace_flow'][$key]['collect_money'] = $val['money_number'];
                } elseif ($val['replace_type'] == 2) {
                    $data['replace_flow'][$key]['pay_money'] = $val['money_number'];
                }
            }
        }
        //收付金额
        $data['replace_collect_money_total'] = $this->replace_payment_model->get_total_money(array('c_id' => $id, 'replace_type' => 1));
        $data['replace_pay_money_total'] = $this->replace_payment_model->get_total_money(array('c_id' => $id, 'replace_type' => 2));

        //税费信息
        $this->replace_payment_model->set_tbl('replace_payment_tax');
        $data['replace_tax_flow'] = $this->replace_payment_model->get_list_by_cond1(array('c_id' => $id));
        if (is_full_array($data['replace_tax_flow'])) {
            foreach ($data['replace_tax_flow'] as $key => $val) {
                if (mb_strlen($val['remark'], 'UTF8') > 8) {
                    $data['replace_tax_flow'][$key]['remark'] = mb_substr($val['remark'], 0, 7, 'UTF8') . '...';
                }
                if ($val['replace_type'] == 1) {
                    $data['replace_tax_flow'][$key]['collect_money'] = $val['money_number'];
                } elseif ($val['replace_type'] == 2) {
                    $data['replace_tax_flow'][$key]['pay_money'] = $val['money_number'];
                }
            }
        }
        //税费合计
        $data['replace_tax_collect_money_total'] = $this->replace_payment_model->get_total_money(array('c_id' => $id, 'replace_type' => 1));
        $data['replace_tax_pay_money_total'] = $this->replace_payment_model->get_total_money(array('c_id' => $id, 'replace_type' => 2));

        //贷款银行
        $data['loan_bank'] = $this->bank_account_model->get_by_id($paymentinfo['loan_bank']);

        $data['handler'] = array("name" => $this->user_arr['truename'], "tel" => $this->user_arr['phone']);
        $this->load->view("bargain/bargain_print", $data);
    }

//客户打款明细打印
    public function cash_detail_print($id = 0)
    {

        //获取成交配置信息
        $data['config'] = $this->bargain_config_model->get_config();
        //银行
        $data['mortgage_bank'] = $data['loan_bank'] = $this->dictionary_model->get_all_by_dictionary_type_id(1);
        $cond_where = "status = 1";
        $this->bank_account_model->set_select_fields(array('id', 'card_no', 'bank_name', 'bank_deposit'));
        $data['mortgage_bank'] = $data['loan_bank'] = $this->bank_account_model->get_all_list();

        //成交详情
        $bargain = $this->bargain_model->get_by_id($id);
        //取付款信息
        $where = "bargain_id = " . $id . " and status = 2";
        $paymentinfo = $this->payment_model->get_one_by_cond($where);
        if ($paymentinfo) {
            if ($paymentinfo['buy_type'] == 1) {
                $bargain['tatal_money'] = $paymentinfo['tatal_money'];
                $bargain['payment_once_time'] = $paymentinfo['payment_once_time'];
                $data['down_payment'] = $paymentinfo['tatal_money'];
            }
            if ($paymentinfo['buy_type'] == 2) {
                $bargain['purchase_money'] = array_filter(explode(',', $paymentinfo['purchase_money']));
                $bargain['purchase_condition'] = array_filter(explode(',', $paymentinfo['purchase_condition']));
                $bargain['payment_period_time'] = $paymentinfo['payment_period_time'];
                $data['down_payment'] = $bargain['purchase_money'][0];
            }
            if ($paymentinfo['buy_type'] == 3) {
                $bargain['loan_type'] = $paymentinfo['loan_type'];
                $bargain['first_time'] = $paymentinfo['first_time'];
                $bargain['first_money'] = $paymentinfo['first_money'];
                $bargain['spare_money'] = $paymentinfo['spare_money'];
                $data['down_payment'] = $paymentinfo['first_money'];
            }
            $bargain['buy_type'] = $paymentinfo['buy_type'];
            $bargain['loan_bank'] = $paymentinfo['loan_bank'];

            $bargain['collect_condition'] = array_filter(explode(',', $paymentinfo['collect_condition']));
            $bargain['collect_money'] = array_filter(explode(',', $paymentinfo['collect_money']));
        }
        $data['bargain'] = $bargain;
        //贷款银行
        $data['loan_bank'] = $this->bank_account_model->get_by_id($paymentinfo['loan_bank']);

        $data['make_table'] = $this->user_arr['truename'];
        $this->load->view("bargain/cash_detail_print", $data);
    }

//财务打印
    public function finance_print($id = 0)
    {

        //获取成交配置信息
        $data['config'] = $this->bargain_config_model->get_config();
        //银行
        $data['mortgage_bank'] = $data['loan_bank'] = $this->dictionary_model->get_all_by_dictionary_type_id(1);
        $cond_where = "status = 1";
        $this->bank_account_model->set_select_fields(array('id', 'card_no', 'bank_name', 'bank_deposit'));
        $data['mortgage_bank'] = $data['loan_bank'] = $this->bank_account_model->get_all_list();

        //成交详情
        $bargain = $this->bargain_model->get_by_id($id);
        //取付款信息
        $where = "bargain_id = " . $id . " and status = 2";
        $paymentinfo = $this->payment_model->get_one_by_cond($where);
        if ($paymentinfo) {
            if ($paymentinfo['buy_type'] == 1) {
                $bargain['tatal_money'] = $paymentinfo['tatal_money'];
                $bargain['payment_once_time'] = $paymentinfo['payment_once_time'];
            }
            if ($paymentinfo['buy_type'] == 2) {
                $bargain['purchase_money'] = array_filter(explode(',', $paymentinfo['purchase_money']));
                $bargain['purchase_condition'] = array_filter(explode(',', $paymentinfo['purchase_condition']));
                $bargain['payment_period_time'] = $paymentinfo['payment_period_time'];
            }
            if ($paymentinfo['buy_type'] == 3) {
                $bargain['loan_type'] = $paymentinfo['loan_type'];
                $bargain['first_time'] = $paymentinfo['first_time'];
                $bargain['first_money'] = $paymentinfo['first_money'];
                $bargain['spare_money'] = $paymentinfo['spare_money'];
            }
            $bargain['buy_type'] = $paymentinfo['buy_type'];
            $bargain['loan_bank'] = $paymentinfo['loan_bank'];

            $bargain['collect_condition'] = array_filter(explode(',', $paymentinfo['collect_condition']));
            $bargain['collect_money'] = array_filter(explode(',', $paymentinfo['collect_money']));
        }
        $data['bargain'] = $bargain;

        //代收付信息
        $this->replace_payment_model->set_tbl('replace_payment');
        $data['replace_flow'] = $this->replace_payment_model->get_list_by_cond1(array('c_id' => $id));
        if (is_full_array($data['replace_flow'])) {
            foreach ($data['replace_flow'] as $key => $val) {
                if (mb_strlen($val['remark'], 'UTF8') > 8) {
                    $data['replace_flow'][$key]['remark'] = mb_substr($val['remark'], 0, 7, 'UTF8') . '...';
                }
                if ($val['replace_type'] == 1) {
                    $data['replace_flow'][$key]['collect_money'] = $val['money_number'];
                } elseif ($val['replace_type'] == 2) {
                    $data['replace_flow'][$key]['pay_money'] = $val['money_number'];
                }
            }
        }
        //收付金额
        $data['replace_collect_money_total'] = $this->replace_payment_model->get_total_money(array('c_id' => $id, 'replace_type' => 1));
        $data['replace_pay_money_total'] = $this->replace_payment_model->get_total_money(array('c_id' => $id, 'replace_type' => 2));

        //税费信息
        $this->replace_payment_model->set_tbl('replace_payment_tax');
        $data['replace_tax_flow'] = $this->replace_payment_model->get_list_by_cond1(array('c_id' => $id));
        if (is_full_array($data['replace_tax_flow'])) {
            foreach ($data['replace_tax_flow'] as $key => $val) {
                if (mb_strlen($val['remark'], 'UTF8') > 8) {
                    $data['replace_tax_flow'][$key]['remark'] = mb_substr($val['remark'], 0, 7, 'UTF8') . '...';
                }
                if ($val['replace_type'] == 1) {
                    $data['replace_tax_flow'][$key]['collect_money'] = $val['money_number'];
                } elseif ($val['replace_type'] == 2) {
                    $data['replace_tax_flow'][$key]['pay_money'] = $val['money_number'];
                }
            }
        }
        //税费合计
        $data['replace_tax_collect_money_total'] = $this->replace_payment_model->get_total_money(array('c_id' => $id, 'replace_type' => 1));
        $data['replace_tax_pay_money_total'] = $this->replace_payment_model->get_total_money(array('c_id' => $id, 'replace_type' => 2));

        //贷款银行
        $data['loan_bank'] = $this->bank_account_model->get_by_id($paymentinfo['loan_bank']);

        $data['make_table'] = $this->user_arr['truename'];
        $this->load->view("bargain/finance_print", $data);
    }

    /**
     * 权限检查
     * @access public
     * @return void
     */
    public function purview_check()
    {
        $type = intval($this->input->post('type'));
        $per_num = $type == 1 ? '43' : '1';
        $bargain_edit_per = $this->signatory_purview_model->check($per_num);

        $data['edit'] = $bargain_edit_per;
        echo json_encode($data);
    }


    /**
     * 录入和修改出售成交
     * @access public
     * @return void
     */
    public function modify_bargain($type = 0, $id = 0)
    {
        $data = array();

        $data['specSellerLacks'] = $this->_spec_seller_lacks;
        $data['specBuyerLacks'] = $this->_spec_buyer_lacks;

        //菜单栏
        $data['user_tree_menu'] = $this->purview_tab_model->get_tree_menu('bargain', 'deal_manage');
        $data['id'] = $id;

        //获取成交配置信息
        $data['config'] = $this->bargain_config_model->get_config();

        $this->bank_account_model->set_select_fields(array('id', 'card_no', 'bank_name', 'bank_deposit'));
        $data['mortgage_bank'] = $data['loan_bank'] = $data['agent_bank'] = $this->bank_account_model->get_all_list();
        //配置项，理财人员
        $data['finances'] = $this->dictionary_model->get_all_by_dictionary_type_id(5);
        //成交详情
        $bargain = $this->bargain_model->get_by_id($id);
        if ($bargain) {
            $owner_tel = explode(',', $bargain['owner_tel']);
            $bargain['owner_tel_1'] = !empty($owner_tel[0]) ? $owner_tel[0] : '';
            $bargain['owner_tel_2'] = !empty($owner_tel[1]) ? $owner_tel[1] : '';
            $bargain['owner_tel_3'] = !empty($owner_tel[2]) ? $owner_tel[2] : '';

            $customer_tel = explode(',', $bargain['customer_tel']);
            $bargain['customer_tel_1'] = !empty($customer_tel[0]) ? $customer_tel[0] : '';
            $bargain['customer_tel_2'] = !empty($customer_tel[1]) ? $customer_tel[1] : '';
            $bargain['customer_tel_3'] = !empty($customer_tel[2]) ? $customer_tel[2] : '';

            $bargain['house_id'] = $bargain['house_id'] ? format_info_id($bargain['house_id'], 'sell') : '';
            $bargain['customer_id'] = $bargain['customer_id'] ? format_info_id($bargain['customer_id'], 'buy_customer') : '';

        }
        //如果没有门店数据，默认经纪人本门店
        if (!$bargain['department_id_a']) {
            $bargain['department_id_a'] = $this->user_arr['department_id'];
        }
        if (!$bargain['department_id_b']) {
            $bargain['department_id_b'] = $this->user_arr['department_id'];
        }
        //取付款信息
        $where = "bargain_id = " . $id . " and status = 2";
        $paymentinfo = $this->payment_model->get_one_by_cond($where);
        if ($paymentinfo) {

            if ($paymentinfo['buy_type'] == 1) {
                $bargain['tatal_money'] = $paymentinfo['tatal_money'];
                $bargain['payment_once_time'] = $paymentinfo['payment_once_time'];
            }
            if ($paymentinfo['buy_type'] == 2) {
                $bargain['purchase_money'] = explode(',', $paymentinfo['purchase_money']);
                $bargain['purchase_condition'] = explode(',', $paymentinfo['purchase_condition']);
                $bargain['payment_period_time'] = $paymentinfo['payment_period_time'];
            }
            if ($paymentinfo['buy_type'] == 3) {
                $bargain['loan_type'] = $paymentinfo['loan_type'];
                $bargain['first_time'] = $paymentinfo['first_time'];
                $bargain['first_money'] = $paymentinfo['first_money'];
                $bargain['spare_money'] = $paymentinfo['spare_money'];
            }
            $bargain['buy_type'] = $paymentinfo['buy_type'];
            $bargain['loan_bank'] = $paymentinfo['loan_bank'];

            $bargain['collect_condition'] = explode(',', $paymentinfo['collect_condition']);
            $bargain['collect_money'] = explode(',', $paymentinfo['collect_money']);
        }
        $data['type'] = $type;
        $data['bargain'] = $bargain;

//     获取公司下所有财务人员
        $data['finance_persons'] = $this->signatorys($this->user_arr["company_id"], array(1, 3));

        //获取区属
        $district = $this->district_model->get_district();
        foreach ($district as $key => $val) {
            $data['district'][$val['id']] = $val;
        }

        //权限
        $per_num0 = $type == 1 ? '1' : '17';
        $replace_add_per = $this->signatory_purview_model->check($per_num0);

        $per_num = $type == 1 ? '43' : '1';
        $bargain_edit_per = $this->signatory_purview_model->check($per_num);

        $data['auth'] = array(
            'edit' => $bargain_edit_per,
            'replace_add' => $replace_add_per,
        );
        $data['user_id'] = $this->user_arr['signatory_id'];
        if ($id) {
            //页面标题
            $data['page_title'] = '修改成交';
        } else {
            //页面标题
            $data['page_title'] = '录入成交';
        }

        //需要加载的css
        $data['css'] = load_css('mls_guli/third/iconfont/iconfont.css,mls_guli/css/v1.0/base.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/myStyle.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js'
            . 'mls_guli/js/v1.0/backspace.js');
        if ($type == 1) {
            //获取公司下所有签约人员，权证人员
            $data['warrant_persons'] = $this->signatorys($this->user_arr["company_id"], array(7));
            $this->view("bargain/bargain_modify_one", $data);
        } elseif ($type == 2) {
            //获取公司下所有签约人员，权证人员
            $data['warrant_persons'] = $data['signatorys'] = $this->signatorys($this->user_arr["company_id"]);
            $this->view("bargain/bargain_modify", $data);
        }

    }

    /**
     * 录入和修改出售成交
     * @access public
     * @return void
     */
    public function bargain_view($type = 0, $id = 0)
    {
        $data = array();
        //菜单栏
        $data['user_tree_menu'] = $this->purview_tab_model->get_tree_menu('bargain', 'deal_manage');
        $data['id'] = $id;

        //获取基础配置信息
        //$data['config'] = $this->house_config_model->get_config();
        //获取成交配置信息
        $data['config'] = $this->bargain_config_model->get_config();
        //银行
        // $data['mortgage_bank'] = $data['loan_bank'] = $this->dictionary_model->get_all_by_dictionary_type_id(1);
        $cond_where = "status = 1";
        $this->bank_account_model->set_select_fields(array('id', 'card_no', 'bank_name', 'bank_deposit'));
        $data['mortgage_bank'] = $data['loan_bank'] = $this->bank_account_model->get_all_list();
        //配置项，理财人员
        $data['finances'] = $this->dictionary_model->get_all_by_dictionary_type_id(5);
        //成交详情
        $bargain = $this->bargain_model->get_by_id($id);
        if ($bargain) {
            $bargain['house_id'] = $bargain['house_id'] ? format_info_id($bargain['house_id'], 'sell') : '';
            $bargain['customer_id'] = $bargain['customer_id'] ? format_info_id($bargain['customer_id'], 'buy_customer') : '';
        }
        //如果没有门店数据，默认经纪人本门店
        if (!$bargain['department_id_a']) {
            $bargain['department_id_a'] = $this->user_arr['department_id'];
        }
        if (!$bargain['department_id_b']) {
            $bargain['department_id_b'] = $this->user_arr['department_id'];
        }
        //取付款信息
        $where = "bargain_id = " . $id . " and status = 2";
        $paymentinfo = $this->payment_model->get_one_by_cond($where);
        if ($paymentinfo) {

            if ($paymentinfo['buy_type'] == 1) {
                $bargain['tatal_money'] = $paymentinfo['tatal_money'];
                $bargain['payment_once_time'] = $paymentinfo['payment_once_time'];
            }
            if ($paymentinfo['buy_type'] == 2) {
                $bargain['purchase_money'] = explode(',', $paymentinfo['purchase_money']);
                $bargain['purchase_condition'] = explode(',', $paymentinfo['purchase_condition']);
                $bargain['payment_period_time'] = $paymentinfo['payment_period_time'];
            }
            if ($paymentinfo['buy_type'] == 3) {
                $bargain['loan_type'] = $paymentinfo['loan_type'];
                $bargain['first_time'] = $paymentinfo['first_time'];
                $bargain['first_money'] = $paymentinfo['first_money'];
                $bargain['spare_money'] = $paymentinfo['spare_money'];
            }
            $bargain['buy_type'] = $paymentinfo['buy_type'];
            $bargain['loan_bank'] = $paymentinfo['loan_bank'];

            $bargain['collect_condition'] = explode(',', $paymentinfo['collect_condition']);
            $bargain['collect_money'] = explode(',', $paymentinfo['collect_money']);
        }
        $data['type'] = $type;
        $data['bargain'] = $bargain;
        /**
         * 数据范围
         * 1、店长以上权限看公司
         * 2、店长及店长秘书权限查看本门店
         * 3、店长秘书以下没有权限
         */
        //获取访问菜单
        //$range_menu_a = $this->bargain_model->get_range_menu_by_role_level($this->user_arr, $bargain['agency_id_a']);
        //门店数据
        //$data['agencys_a'] = $range_menu_a['agencys'];
        // $data['brokers_a'] = $range_menu_a['brokers'];

        //获取访问菜单
        //$range_menu_b = $this->bargain_model->get_range_menu_by_role_level($this->user_arr, $bargain['agency_id_b']);
        //门店数据
        // $data['agencys_b'] = $range_menu_b['agencys'];
        //$data['brokers_b'] = $range_menu_b['brokers'];


        //获取公司下所有签约人员，权证人员
        $data['warrant_persons'] = $data['signatorys'] = $this->signatorys($this->user_arr["company_id"]);
//     获取公司下所有财务人员
        $data['finance_persons'] = $this->signatorys($this->user_arr["company_id"], array(1, 3));

        //获取区属
        $district = $this->district_model->get_district();
        foreach ($district as $key => $val) {
            $data['district'][$val['id']] = $val;
        }
        //获取板块
//      $street = $this->district_model->get_street();
//      foreach ($street as $key => $val) {
//          $data['street'][$val['id']] = $val;
//      }

        //权限
        // $bargain_add_per = $this->signatory_purview_model->check('1');
        $bargain_edit_per = $this->signatory_purview_model->check('2');
        // $bargain_delete_per = $this->signatory_purview_model->check('3');
        // $bargain_cancel_per = $this->signatory_purview_model->check('4');
        $data['auth'] = array(
            //'add' => $bargain_add_per,
            'edit' => $bargain_edit_per,
            //  'delete' => $bargain_delete_per,
            //  'cancel' => $bargain_cancel_per
        );
        if ($id) {
            //页面标题
            $data['page_title'] = '修改成交';
        } else {
            //页面标题
            $data['page_title'] = '录入成交';
        }

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/myStyle.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js'
            . 'mls_guli/js/v1.0/backspace.js');

        $this->view("bargain/bargain_view", $data);
    }

    /**
     * 录入和修改出售成交
     * @access public
     * @return void
     */
    public function bargain_collect_money($id = 0)
    {
        $data = array();
        $data['id'] = $id;
        $bargain = array();

        //取付款信息
        $where = "bargain_id = " . $id . " and status = 2";
        $paymentinfo = $this->payment_model->get_one_by_cond($where);
        if ($paymentinfo) {
            $bargain['collect_condition'] = explode(',', $paymentinfo['collect_condition']);
            $bargain['collect_money'] = explode(',', $paymentinfo['collect_money']);
        }
        $data['bargain'] = $bargain;
        $data['page_title'] = '成交信息';
        //权限检查
        $replace_add_per = $this->signatory_purview_model->check('17');
        $replace_edit_per = $this->signatory_purview_model->check('18');
        $replace_delete_per = $this->signatory_purview_model->check('19');
        $replace_complete_per = $this->signatory_purview_model->check('20');

        $data['auth'] = array(
            'replace_add' => $replace_add_per, 'replace_edit' => $replace_edit_per,
            'replace_delete' => $replace_delete_per, 'replace_complete' => $replace_complete_per
        );
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/myStyle.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view("bargain/bargain_collect_money", $data);
    }

    //取款信息查看
    public function bargain_collect_view($id = 0)
    {
        $data = array();
        $data['id'] = $id;
        $bargain = array();

        //取付款信息
        $where = "bargain_id = " . $id . " and status = 2";
        $paymentinfo = $this->payment_model->get_one_by_cond($where);
        if ($paymentinfo) {
            $bargain['collect_condition'] = explode(',', $paymentinfo['collect_condition']);
            $bargain['collect_money'] = explode(',', $paymentinfo['collect_money']);
        }
        $data['bargain'] = $bargain;
        $data['page_title'] = '成交信息';
        //权限检查
        $replace_add_per = $this->signatory_purview_model->check('17');
        $replace_edit_per = $this->signatory_purview_model->check('18');
        $replace_delete_per = $this->signatory_purview_model->check('19');
        $replace_complete_per = $this->signatory_purview_model->check('20');

        $data['auth'] = array(
            'replace_add' => $replace_add_per, 'replace_edit' => $replace_edit_per,
            'replace_delete' => $replace_delete_per, 'replace_complete' => $replace_complete_per
        );
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/myStyle.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view("bargain/bargain_collect_view", $data);
    }

//添加成交记录
    public function save_add_bargain()
    {
        $post_param = $this->input->post(NULL, TRUE);
        //成交添加信息数组
        $type = intval($post_param['type']);//0:全部 1:一手房 2.二手房 3.托管
        //成交添加信息数组
        $department_a = $this->department_model->get_one_by(array('id' => $post_param['department_id_a']));
        $department_b = $this->department_model->get_one_by(array('id' => $post_param['department_id_b']));
        $signatory = $this->signatory_info_model->get_by_signatory_id($this->user_arr['signatory_id']);

        $datainfo = array(
            'enter_company_id' => $this->user_arr['company_id'],
            'enter_department_id' => $this->user_arr['department_id'],
            'enter_signatory_id' => $this->user_arr['signatory_id'],
            'signatory_name' => trim($signatory['truename']),

            'type' => $type,
            'bargain_type' => intval($post_param['bargain_type']),
            'block_name' => trim($post_param['block_name']),
            'number' => trim($post_param['number']),
            'house_addr' => trim($post_param['house_addr']),
            'signing_time' => strtotime($post_param['signing_time']),
            'agency_id_a' => intval($post_param['agency_id_a']),
            'agency_name_a' => trim($post_param['agency_name_a']),
            'owner' => trim($post_param['owner']),
            'owner_idcard' => trim($post_param['owner_idcard']),
            'owner_tel' => trim($post_param['owner_tel']),
            'agency_id_b' => intval($post_param['agency_id_b']),
            'agency_name_b' => trim($post_param['agency_name_b']),
            'customer' => trim($post_param['customer']),
            'customer_idcard' => trim($post_param['customer_idcard']),
            'customer_tel' => trim($post_param['customer_tel']),
            'bargain_status' => 1,//0：作废 1：办理中2：结案
            'status' => 2,
            'is_check' => 1,
            'is_del' => 0,
            'createtime' => time(),
        );

        $submit_flag = $post_param['submit_flag'];
        if ($submit_flag == "add") {
            //检查成交编号唯一性
            $where = "enter_company_id = {$this->user_arr['company_id']} and number = '{$post_param['number']}' and is_del = 0";
            $result = $this->bargain_model->get_one_by($where);
            if (is_full_array($result)) {
                $data['result'] = 'no';
                $data['msg'] = '公司内已有该成交编号的成交！';
                echo json_encode($data);
                exit();
            }
            //添加
            $id = $this->bargain_model->add_info($datainfo);
            if ($id) {
                //操作日志
                $add_log_param = array(
                    'company_id' => $this->user_arr['company_id'],
                    'department_id' => $this->user_arr['department_id'],
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'type' => 35,
                    'text' => '录入成交编号为' . $datainfo['number'] . '的交易成交。',
                    'from_system' => 1,
                    'from_ip' => get_ip(),
                    'mac_ip' => '127.0.0.1',
                    'from_host_name' => '127.0.0.1',
                    'hardware_num' => '测试硬件序列号',
                    'time' => time()
                );
                $this->signatory_operate_log_model->add_operate_log($add_log_param);
                echo json_encode(array('result' => 'ok', "msg" => "成交录入成功"));
                exit;
            } else {
                echo json_encode(array('result' => 'ok', "msg" => "成交录入失败"));
                exit;
            }
        }
    }

    //添加付款记录
    public function add_payment($paymentinfo)
    {
        //根据成交记录id检查是否存在该记录
        $where = 'bargain_id = ' . $paymentinfo['bargain_id'] . ' and status=2';
        $count_num = $this->payment_model->count_by($where);
        if ($count_num > 0) {
            //更新记录
            $update_result = $this->payment_model->update_info_by_cond($paymentinfo, $where);
            return $update_result;
        } else {
            $add_result = $this->payment_model->add_flow($paymentinfo);
            return $add_result;
        }


    }

    //编辑成交记录
    public function save_modify_bargain()
    {
        //成交添加信息数组
        $post_param = $this->input->post(NULL, TRUE);

        $type = intval($post_param['type']);//0:全部 1:一手房 2.二手房 3.托管
        if ($type == 2) {

            //经纪人信息
            $agency_a = $this->agency_model->get_one_by(array('id' => $post_param['agency_id_a']));


            //成交信息
            $datainfo = array(
                'enter_company_id' => $this->user_arr['company_id'],
                'enter_department_id' => $this->user_arr['department_id'],
                'enter_signatory_id' => $this->user_arr['signatory_id'],
                'type' => $type,
                'number' => trim($post_param['number']),
                'certificate_number' => trim($post_param['certificate_number']),
                'land_nature' => intval($post_param['land_nature']),
                'signing_time' => strtotime($post_param['signing_time']),
                'house_time' => trim($post_param['house_time']),
                'house_addr' => trim($post_param['house_addr']),
                'buildarea' => sprintf('%.2f', $post_param['buildarea']),
                'price' => sprintf('%.2f', $post_param['price']),
                'decoration_price' => sprintf('%.2f', $post_param['decoration_price']),
                'is_mortgage' => intval($post_param['is_mortgage']),
                'mortgage_thing' => intval($post_param['is_mortgage']) == 1 ? trim($post_param['mortgage_thing']) : "",
                'mortgage_bank' => trim($post_param['mortgage_bank']),
                'bargain_status' => intval($post_param['bargain_status']),
                'signatory_id' => trim($post_param['signatory_id']),
                'signatory_name' => trim($post_param['signatory_name']),
                'warrant_type' => trim($post_param['warrant_type']),
                'warrant_inside' => trim($post_param['warrant_inside']),
                'warrant_inside_name' => trim($post_param['warrant_inside']) > 0 ? trim($post_param['warrant_inside_name']) : "",
                'finance_id' => trim($post_param['finance_id']),
                'finance_name' => trim($post_param['finance_id']) > 0 ? trim($post_param['finance_name']) : "",
                'district_id' => trim($post_param['district_id']),
                'district_name' => trim($post_param['district_id']) > 0 ? trim($post_param['district_name']) : "",
                'house_type' => trim($post_param['house_type']),

                'tracker' => trim($post_param['tracker']),
                'is_evaluate' => intval($post_param['is_evaluate']),
                'evaluate_charges' => sprintf('%.2f', $post_param['evaluate_charges']),
                'signatory_company' => trim($post_param['signatory_company']),
                'tax_pay_tatal' => sprintf('%.2f', $post_param['tax_pay_tatal']),
                'tax_pay_appoint' => intval($post_param['tax_pay_type']) == 4 ? trim($post_param['tax_pay_appoint']) : "",
                'tax_pay_type' => intval($post_param['tax_pay_type']),
                'note_other' => intval($post_param['note_other']),
                'note_belong' => trim($post_param['note_belong']),
                'owner' => trim($post_param['owner']),
                'owner_idcard' => trim($post_param['owner_idcard']),
                'owner_tel' => trim($post_param['owner_tel']),
                'signing_fee_type' => trim($post_param['signing_fee_type']),
                'signing_fee' => trim($post_param['signing_fee']),

                'company_id_a' => $agency_a['company_id'],
                'agency_id_a' => intval($post_param['agency_id_a']),
                'agency_name_a' => trim($post_param['agency_name_a']),
                'trust_name_a' => trim($post_param['trust_name_a']),
                'show_trust_a' => trim($post_param['show_trust_a']),
                'trust_idcard_a' => trim($post_param['trust_idcard_a']),
                'broker_id_a' => trim($post_param['broker_id_a']),
                'broker_tel_a' => trim($post_param['broker_tel_a']),
                'broker_name_a' => trim($post_param['broker_name_a']),

                'company_id_b' => $agency_b['company_id'],
                'agency_id_b' => intval($post_param['agency_id_b']),
                'agency_name_b' => trim($post_param['agency_name_b']),
                'trust_name_b' => trim($post_param['trust_name_b']),
                'show_trust_b' => trim($post_param['show_trust_b']),
                'trust_idcard_b' => trim($post_param['trust_idcard_b']),
                'broker_id_b' => trim($post_param['broker_id_b']),
                'broker_tel_b' => trim($post_param['broker_tel_b']),
                'broker_name_b' => trim($post_param['broker_name_b']),

                'customer' => trim($post_param['customer']),
                'customer_idcard' => trim($post_param['customer_idcard']),
                'customer_tel' => trim($post_param['customer_tel']),
                'bargain_type' => 1,
                'status' => 2,
                'is_check' => 1,
                'remarks' => $post_param['remarks'],
                'undertake_remarks' => $post_param['undertake_remarks'],
                'createtime' => time(),
                'seller_lacks' => json_encode($post_param['seller_lacks']),
                'buyer_lacks' => json_encode($post_param['buyer_lacks']),
                'seller_lacks_others' => $post_param['seller_lacks_others'],
                'buyer_lacks_others' => $post_param['buyer_lacks_others'],
                'seller_id_card' => $post_param['seller_id_card'],
                'buyer_id_card' => $post_param['buyer_id_card'],
                'seller_marry_info' => $post_param['seller_marry_info'],
                'buyer_marry_info' => $post_param['buyer_marry_info'],
            );
            $submit_flag = $post_param['submit_flag'];
            if ($submit_flag == "modify") {
                //检查成交编号唯一性
                $where = "enter_company_id = {$this->user_arr['company_id']} and number = '{$post_param['number']}' and id != {$post_param['id']} and is_del = 0";
                $result = $this->bargain_model->get_one_by($where);
                if (is_full_array($result)) {
                    $resdata['result'] = 'no';
                    $resdata['msg'] = '公司内已有该成交编号';
                    echo json_encode($resdata);
                    exit();
                }
                //成交详情
                $data1 = $this->get_detail($post_param['id']);

                //修改
                $rs = $this->bargain_model->update_by_id($datainfo, $post_param['id']);
                if ($rs) {
                    $paymentinfo = array(
                        'company_id' => $this->user_arr['company_id'],
                        'department_id' => $this->user_arr['department_id'],
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'bargain_id' => intval($post_param['id']),
                        'buy_type' => intval($post_param['buy_type']),
                        'loan_bank' => trim($post_param['loan_bank']),
                        'tatal_money' => trim($post_param['tatal_money']),
                        'purchase_money' => implode(",", $post_param['purchase_money']),
                        'purchase_condition' => implode(",", $post_param['purchase_condition']),
                        'loan_type' => intval($post_param['loan_type']),
                        'payment_period_time' => strtotime($post_param['payment_period_time']),
                        'payment_once_time' => strtotime($post_param['payment_once_time']),
                        'first_time' => strtotime($post_param['first_time']),
                        'first_money' => trim($post_param['first_money']),
                        'spare_money' => trim($post_param['spare_money']),
//                      'collect_money' => implode(",", $post_param['collect_money']),
//                      'collect_condition' => implode(",", $post_param['collect_condition']),
                        'status' => 2,
                        'create_time' => time()
                    );
                    $data2 = $this->add_payment($paymentinfo);
                    if ($data2 > 0) {
                        $content = $this->modify_match($datainfo, $data1, $data1['config']);
                        if ($content) {
                            //成交跟进——添加业绩分成
                            $add_data = array(
                                'c_id' => $post_param['id'],
                                'type_name' => "成交修改",
                                'content' => "本日对该成交信息进行修改。" . $content,
                                'signatory_id' => $this->user_arr['signatory_id'],
                                'signatory_name' => $this->user_arr['truename'],
                                'updatetime' => time()
                            );
                            $this->bargain_log_model->add_info($add_data);
                        }
                        //操作日志
                        $add_log_param = array(
                            'company_id' => $this->user_arr['company_id'],
                            'department_id' => $this->user_arr['department_id'],
                            'signatory_id' => $this->user_arr['signatory_id'],
                            'signatory_name' => $this->user_arr['truename'],
                            'type' => 35,
                            'text' => '修改成交编号为' . $datainfo['number'] . '的交易成交。' . $content,
                            'from_system' => 1,
                            'from_ip' => get_ip(),
                            'mac_ip' => '127.0.0.1',
                            'from_host_name' => '127.0.0.1',
                            'hardware_num' => '测试硬件序列号',
                            'time' => time()
                        );

                        $this->signatory_operate_log_model->add_operate_log($add_log_param);
                        echo json_encode(array('result' => 'ok', "msg" => "成交修改成功"));
                        exit;
                    } else {
                        echo json_encode(array('result' => 'no', "msg" => "成交修改失败"));
                        exit;
                    }

                } else {
                    echo json_encode(array('result' => 'no', "msg" => "成交修改失败"));
                    exit;
                }
            }
            if ($submit_flag == "add") {
                //检查成交编号唯一性
                $where = "enter_company_id = {$this->user_arr['company_id']} and number = '{$post_param['number']}' and is_del = 0";
                $result = $this->bargain_model->get_one_by($where);
                if (is_full_array($result)) {
                    $data['result'] = 'no';
                    $data['msg'] = '公司内已有该成交编号的成交！';
                    echo json_encode($data);
                    exit();
                }
                //添加
                $id = $this->bargain_model->add_info($datainfo);
                if ($id) {
                    $paymentinfo = array(
                        'company_id' => $this->user_arr['company_id'],
                        'department_id' => $this->user_arr['department_id'],
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'bargain_id' => $id,
                        'buy_type' => intval($post_param['buy_type']),
                        'loan_bank' => trim($post_param['loan_bank']),
                        'tatal_money' => trim($post_param['tatal_money']),
                        'purchase_money' => implode(",", $post_param['purchase_money']),
                        'purchase_condition' => implode(",", $post_param['purchase_condition']),
                        'loan_type' => intval($post_param['loan_type']),
                        'payment_period_time' => strtotime($post_param['payment_period_time']),
                        'payment_once_time' => strtotime($post_param['payment_once_time']),
                        'first_time' => strtotime($post_param['first_time']),
                        'first_money' => trim($post_param['first_money']),
                        'spare_money' => trim($post_param['spare_money']),
                        'collect_money' => implode(",", $post_param['collect_money']),
                        'collect_condition' => implode(",", $post_param['collect_condition']),
                        'status' => 2,
                        'create_time' => time()
                    );
                    $data = $this->add_payment($paymentinfo);

                    //添加二手房过户流程
                    //生成成交过户流程
                    $template_id = 1;//系统模板
                    $up_num = $this->select_transfer_template($template_id, $id);
                    //操作日志
                    $add_log_param = array(
                        'company_id' => $this->user_arr['company_id'],
                        'department_id' => $this->user_arr['department_id'],
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'type' => 35,
                        'text' => '录入成交编号为' . $datainfo['number'] . '的交易成交。',
                        'from_system' => 1,
                        'from_ip' => get_ip(),
                        'mac_ip' => '127.0.0.1',
                        'from_host_name' => '127.0.0.1',
                        'hardware_num' => '测试硬件序列号',
                        'time' => time()
                    );
                    $this->signatory_operate_log_model->add_operate_log($add_log_param);
                    echo json_encode(array('result' => 'ok', "msg" => "成交录入成功", "bargain_id" => $id));
                    exit;
                } else {
                    echo json_encode(array('result' => 'ok', "msg" => "成交录入失败"));
                    exit;
                }
            }
        } elseif ($type == 1) {
            $submit_flag = $post_param['submit_flag'];
            $datainfo = array(
                'enter_company_id' => $this->user_arr['company_id'],
                'enter_department_id' => $this->user_arr['department_id'],
                'enter_signatory_id' => $this->user_arr['signatory_id'],
                'type' => $type,
                'number' => trim($post_param['number']),
                'receipt_time' => strtotime($post_param['receipt_time']),
                'warrant_inside' => trim($post_param['warrant_inside']),
                'warrant_inside_name' => trim($post_param['warrant_inside']) > 0 ? trim($post_param['warrant_inside_name']) : "",
                'bargain_status' => intval($post_param['bargain_status']),
                'block_name' => trim($post_param['block_name']),
                'house_addr' => trim($post_param['house_addr']),
                'district_id' => trim($post_param['district_id']),
                'district_name' => trim($post_param['district_id']) > 0 ? trim($post_param['district_name']) : "",
                'agent_bank' => trim($post_param['agent_bank']),
                'agent_type' => trim($post_param['agent_type']),
                'agent_company' => trim($post_param['agent_company']),
                'developer' => trim($post_param['developer']),
                'customer' => trim($post_param['customer']),
                'customer_idcard' => trim($post_param['customer_idcard']),
                'customer_tel' => trim($post_param['customer_tel']),
                'bargain_type' => 2,
                'status' => 2,
                'is_check' => 1,
                'undertake_remarks' => $post_param['undertake_remarks'],
                'createtime' => time(),
            );
            if ($submit_flag == "add") {
                //检查成交编号唯一性
                $where = "enter_company_id = {$this->user_arr['company_id']} and number = '{$post_param['number']}' and is_del = 0";
                $result = $this->bargain_model->get_one_by($where);
                if (is_full_array($result)) {
                    $data['result'] = 'no';
                    $data['msg'] = '公司内已有该成交编号的成交！';
                    echo json_encode($data);
                    exit();
                }
                //添加
                $id = $this->bargain_model->add_info($datainfo);
                if ($id) {
                    //操作日志
                    $add_log_param = array(
                        'company_id' => $this->user_arr['company_id'],
                        'department_id' => $this->user_arr['department_id'],
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'type' => 35,
                        'text' => '录入成交编号为' . $datainfo['number'] . '的交易成交。',
                        'from_system' => 1,
                        'from_ip' => get_ip(),
                        'mac_ip' => '127.0.0.1',
                        'from_host_name' => '127.0.0.1',
                        'hardware_num' => '测试硬件序列号',
                        'time' => time()
                    );
                    $this->signatory_operate_log_model->add_operate_log($add_log_param);
                    echo json_encode(array('result' => 'ok', "msg" => "成交录入成功", "bargain_id" => $id));
                    exit;
                } else {
                    echo json_encode(array('result' => 'ok', "msg" => "成交录入失败"));
                    exit;
                }
            } elseif ($submit_flag == "modify") {
                //检查成交编号唯一性
                $where = "enter_company_id = {$this->user_arr['company_id']} and number = '{$post_param['number']}' and id != {$post_param['id']} and is_del = 0";
                $result = $this->bargain_model->get_one_by($where);
                if (is_full_array($result)) {
                    $resdata['result'] = 'no';
                    $resdata['msg'] = '公司内已有该成交编号';
                    echo json_encode($resdata);
                    exit();
                }
                //成交详情
                $data = $this->get_detail($post_param['id']);

                //修改
                $rs = $this->bargain_model->update_by_id($datainfo, $post_param['id']);
                if ($rs) {
                    $content = $this->modify_match($datainfo, $data, $data['config']);
                    if ($content) {
                        //成交跟进——添加业绩分成
                        $add_data = array(
                            'c_id' => $post_param['id'],
                            'type_name' => "成交修改",
                            'content' => "本日对该成交信息进行修改。" . $content,
                            'signatory_id' => $this->user_arr['signatory_id'],
                            'signatory_name' => $this->user_arr['truename'],
                            'updatetime' => time()
                        );
                        $this->bargain_log_model->add_info($add_data);
                    }
                    //操作日志
                    $add_log_param = array(
                        'company_id' => $this->user_arr['company_id'],
                        'department_id' => $this->user_arr['department_id'],
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'type' => 35,
                        'text' => '修改成交编号为' . $datainfo['number'] . '的交易成交。' . $content,
                        'from_system' => 1,
                        'from_ip' => get_ip(),
                        'mac_ip' => '127.0.0.1',
                        'from_host_name' => '127.0.0.1',
                        'hardware_num' => '测试硬件序列号',
                        'time' => time()
                    );

                    $this->signatory_operate_log_model->add_operate_log($add_log_param);
                    echo json_encode(array('result' => 'ok', "msg" => "成交修改成功"));
                    exit;
                } else {
                    echo json_encode(array('result' => 'no', "msg" => "成交修改失败"));
                    exit;
                }
            }
        }
    }

//编辑成交记录
    public function bargain_completed()
    {
        $post_param = $this->input->post(NULL, TRUE);
        $bargain_id = intval($post_param['bargain_id']);
        //成交添加信息数组
        $datainfo = array(
            'bargain_status' => 2,
            'completed_time' => time(),
            'is_completed' => 1,
        );
        //修改
        if ($bargain_id > 0) {
            $rs = $this->bargain_model->update_by_id($datainfo, $bargain_id);
            if ($rs) {
                //操作日志
                $add_log_param = array(
                    'company_id' => $this->user_arr['company_id'],
                    'department_id' => $this->user_arr['department_id'],
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'type' => 35,
                    'text' => '成交编号为' . $datainfo['number'] . '的交易结案。',
                    'from_system' => 1,
                    'from_ip' => get_ip(),
                    'mac_ip' => '127.0.0.1',
                    'from_host_name' => '127.0.0.1',
                    'hardware_num' => '测试硬件序列号',
                    'time' => time()
                );

                $this->signatory_operate_log_model->add_operate_log($add_log_param);
                echo json_encode(array('result' => 'ok', "msg" => "结案成功！"));
                exit;
            } else {
                echo json_encode(array('result' => 'no', "msg" => "结案失败！"));
                exit;
            }
        }
    }

//编辑成交记录
    public function save_collect_money()
    {
        $post_param = $this->input->post(NULL, TRUE);

        $paymentinfo = array(
            'bargain_id' => intval($post_param['id']),
            'collect_money' => implode(",", $post_param['collect_money']),
            'collect_condition' => implode(",", $post_param['collect_condition']),
        );
        $rs = $this->add_payment($paymentinfo);
        if ($rs) {
            //操作日志
            $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'department_id' => $this->user_arr['department_id'],
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '修改成交编号为' . $datainfo['number'] . '的交易成交。' . $content,
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
            );
            $this->signatory_operate_log_model->add_operate_log($add_log_param);
            echo json_encode(array('result' => 'ok', "msg" => "收款信息添加成功"));
            exit;
        } else {
            echo json_encode(array('result' => 'no', "msg" => "收款信息添加失败"));
            exit;
        }
    }

    public function bargain_detail($id = 0)
    {
        header('Cache-control: private, must-revalidate');
        $data = array();
        $data['id'] = $id;
//    银行
        $this->bank_account_model->set_select_fields(array('id', 'card_no', 'bank_name', 'bank_deposit'));
        $data['mortgage_bank'] = $data['loan_bank'] = $this->bank_account_model->get_all_list();
        //获取公司下所有签约人员，权证人员
        $data['warrant_persons'] = $data['signatorys'] = $this->signatorys($this->user_arr["company_id"]);
//     获取公司下所有财务人员
        $data['finance_persons'] = $this->signatorys($this->user_arr["company_id"], array(1, 3));
        //配置项，理财人员
        $data['finances'] = $this->dictionary_model->get_all_by_dictionary_type_id(5);

        //菜单栏
        $data['user_tree_menu'] = $this->purview_tab_model->get_tree_menu('bargain', 'deal_manage');
        //标题
        $data['page_title'] = '成交详情';
        //$data['show_type'] = $type;
        //获取基本配置信息
        $data['base_config'] = $this->house_config_model->get_config();
        //获取成交配置信息
        $data['config'] = $this->bargain_config_model->get_config();

        //权证步骤名配置
        $data['stage'] = $this->bargain_transfer_model->get_all_stage();

        $bargain = $this->bargain_model->get_by_id($id);
        if ($bargain) {

            $bargain['house_id'] = $bargain['house_id'] ? format_info_id($bargain['house_id'], 'sell') : '';
            $bargain['customer_id'] = $bargain['customer_id'] ? format_info_id($bargain['customer_id'], 'buy_customer') : '';

            $signatory_info = $this->api_signatory_model->get_baseinfo_by_signatory_id($bargain['check_signatory_id']);
            $bargain['check_department'] = $signatory_info['department_name'];
            $bargain['check_signatory'] = $signatory_info['truename'];
        }
        //取付款信息
        $where = "bargain_id = " . $id . " and status = 2";
        $paymentinfo = $this->payment_model->get_one_by_cond($where);
        if ($paymentinfo) {
            if ($paymentinfo['buy_type'] == 1) {
                $bargain['tatal_money'] = $paymentinfo['tatal_money'];
                $bargain['payment_once_time'] = $paymentinfo['payment_once_time'];
            }
            if ($paymentinfo['buy_type'] == 2) {
                $bargain['purchase_money'] = array_filter(explode(',', $paymentinfo['purchase_money']));
                $bargain['purchase_condition'] = array_filter(explode(',', $paymentinfo['purchase_condition']));
                $bargain['payment_period_time'] = $paymentinfo['payment_period_time'];
            }
            if ($paymentinfo['buy_type'] == 3) {
                $bargain['loan_type'] = $paymentinfo['loan_type'];
                $bargain['first_time'] = $paymentinfo['first_time'];
                $bargain['first_money'] = $paymentinfo['first_money'];
                $bargain['spare_money'] = $paymentinfo['spare_money'];
            }
            $bargain['buy_type'] = $paymentinfo['buy_type'];
            $bargain['loan_bank'] = $paymentinfo['loan_bank'];

            $bargain['collect_condition'] = array_filter(explode(',', $paymentinfo['collect_condition']));
            $bargain['collect_money'] = array_filter(explode(',', $paymentinfo['collect_money']));
        }
        $bargain['complete_signatory_id'] = $this->user_arr['signatory_id'];
        $bargain['complete_signatory_name'] = $this->user_arr['truename'];
        $data['bargain'] = $bargain;

        /**
         * 数据范围
         * 1、店长以上权限看公司
         * 2、店长及店长秘书权限查看本门店
         * 3、店长秘书以下没有权限
         */
        //获取访问菜单
//    $range_menu = $this->bargain_model->get_range_menu_by_role_level(
//      $this->user_arr, $this->user_arr['department_id']);
        //部门数据
        //$data['departments'] = $range_menu['departments'];
        //经纪人数据
        //$data['signatorys'] = $range_menu['signatorys'];

        // $data['signatorys'] = $this->signatorys($this->user_arr["company_id"]);
        $divide_add_per = $this->signatory_purview_model->check('');

        $replace_add_tax_per = $this->signatory_purview_model->check('17');

        $replace_add_per = $this->signatory_purview_model->check('17');

        $bargain_edit_per = $this->signatory_purview_model->check('2');

        $data['auth'] = array(
            'edit' => $bargain_edit_per, 'divide_add' => $divide_add_per, 'replace_tax_add' => $replace_add_per, 'replace_add' => $replace_add_per
        );

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view("bargain/bargain_detail", $data);
    }

    //查看成交信息
    public function bargain_look($id = 0)
    {
        header('Cache-control: private, must-revalidate');
        $data = array();
        $data['id'] = $id;

        //菜单栏
        $data['user_tree_menu'] = $this->purview_tab_model->get_tree_menu('bargain', 'deal_manage');

        //标题
        $data['page_title'] = '成交详情';

        //获取成交配置信息
        $data['config'] = $this->bargain_config_model->get_config();

        $bargain = $this->bargain_model->get_by_id($id);

        //取付款信息
        $where = "bargain_id = " . $id . " and status = 2";
        $paymentinfo = $this->payment_model->get_one_by_cond($where);
        if ($paymentinfo) {
            if ($paymentinfo['buy_type'] == 1) {
                $bargain['tatal_money'] = $paymentinfo['tatal_money'];
                $bargain['payment_once_time'] = $paymentinfo['payment_once_time'];
            }
            if ($paymentinfo['buy_type'] == 2) {
                $bargain['purchase_money'] = array_filter(explode(',', $paymentinfo['purchase_money']));
                $bargain['purchase_condition'] = array_filter(explode(',', $paymentinfo['purchase_condition']));
                $bargain['payment_period_time'] = $paymentinfo['payment_period_time'];
            }
            if ($paymentinfo['buy_type'] == 3) {
                $bargain['loan_type'] = $paymentinfo['loan_type'];
                $bargain['first_time'] = $paymentinfo['first_time'];
                $bargain['first_money'] = $paymentinfo['first_money'];
                $bargain['spare_money'] = $paymentinfo['spare_money'];
            }
            $bargain['buy_type'] = $paymentinfo['buy_type'];
            $bargain['loan_bank'] = $paymentinfo['loan_bank'];
        }

        $data['bargain'] = $bargain;

        $per_num = $bargain['type'] == 1 ? '43' : '1';
        $bargain_edit_per = $this->signatory_purview_model->check($per_num);

        $data['auth'] = array(
            'edit' => $bargain_edit_per
        );
        $data['user_id'] = $this->user_arr['signatory_id'];
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        if ($bargain['type'] == 2) {
            //贷款银行
            $data['loan_bank'] = $this->bank_account_model->get_by_id($paymentinfo['loan_bank']);
            $this->view("bargain/bargain_look", $data);

        } elseif ($bargain['type'] == 1) {
            $data['agent_bank'] = $this->bank_account_model->get_by_id($bargain['agent_bank']);
            $this->view("bargain/bargain_look_one", $data);

        }
    }

    //过户流程
    public function transfer_process($id = 0)
    {
        header('Cache-control: private, must-revalidate');
        $data = array();
        $data['id'] = $id;

        //菜单栏
        $data['user_tree_menu'] = $this->purview_tab_model->get_tree_menu('bargain', 'deal_manage');
        //标题

        $bargain = $this->bargain_model->get_by_id($id);

        $bargain['complete_signatory_id'] = $this->user_arr['signatory_id'];
        $bargain['complete_signatory_name'] = $this->user_arr['truename'];
        $data['bargain'] = $bargain;

        $replace_add_per = $this->signatory_purview_model->check('17');

        $bargain_edit_per = $this->signatory_purview_model->check('2');

        $data['auth'] = array(
            'edit' => $bargain_edit_per, 'replace_tax_add' => $replace_add_per, 'replace_add' => $replace_add_per
        );

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view("bargain/transfer_process", $data);
    }

    //财务管理
    public function finance_manage($id = 0)
    {
        header('Cache-control: private, must-revalidate');
        $data = array();
        $data['id'] = $id;
//    银行
        $this->bank_account_model->set_select_fields(array('id', 'card_no', 'bank_name', 'bank_deposit'));
        $data['mortgage_bank'] = $data['loan_bank'] = $this->bank_account_model->get_all_list();
        //获取公司下所有签约人员，权证人员
        $data['warrant_persons'] = $data['signatorys'] = $this->signatorys($this->user_arr["company_id"]);
//     获取公司下所有财务人员
        $data['finance_persons'] = $this->signatorys($this->user_arr["company_id"], array(1, 3));
        //配置项，理财人员
        $data['finances'] = $this->dictionary_model->get_all_by_dictionary_type_id(5);

        //菜单栏
        $data['user_tree_menu'] = $this->purview_tab_model->get_tree_menu('bargain', 'deal_manage');
        //标题
        $data['page_title'] = '成交详情';
        //$data['show_type'] = $type;
        //获取基本配置信息
        $data['base_config'] = $this->house_config_model->get_config();
        //获取成交配置信息
        $data['config'] = $this->bargain_config_model->get_config();

        //权证步骤名配置
        $data['stage'] = $this->bargain_transfer_model->get_all_stage();

        $bargain = $this->bargain_model->get_by_id($id);
        if ($bargain) {

            $bargain['house_id'] = $bargain['house_id'] ? format_info_id($bargain['house_id'], 'sell') : '';
            $bargain['customer_id'] = $bargain['customer_id'] ? format_info_id($bargain['customer_id'], 'buy_customer') : '';

            $signatory_info = $this->api_signatory_model->get_baseinfo_by_signatory_id($bargain['check_signatory_id']);
            $bargain['check_department'] = $signatory_info['department_name'];
            $bargain['check_signatory'] = $signatory_info['truename'];
        }
        //取付款信息
        $where = "bargain_id = " . $id . " and status = 2";
        $paymentinfo = $this->payment_model->get_one_by_cond($where);
        if ($paymentinfo) {
            if ($paymentinfo['buy_type'] == 1) {
                $bargain['tatal_money'] = $paymentinfo['tatal_money'];
                $bargain['payment_once_time'] = $paymentinfo['payment_once_time'];
            }
            if ($paymentinfo['buy_type'] == 2) {
                $bargain['purchase_money'] = array_filter(explode(',', $paymentinfo['purchase_money']));
                $bargain['purchase_condition'] = array_filter(explode(',', $paymentinfo['purchase_condition']));
                $bargain['payment_period_time'] = $paymentinfo['payment_period_time'];
            }
            if ($paymentinfo['buy_type'] == 3) {
                $bargain['loan_type'] = $paymentinfo['loan_type'];
                $bargain['first_time'] = $paymentinfo['first_time'];
                $bargain['first_money'] = $paymentinfo['first_money'];
                $bargain['spare_money'] = $paymentinfo['spare_money'];
            }
            $bargain['buy_type'] = $paymentinfo['buy_type'];
            $bargain['loan_bank'] = $paymentinfo['loan_bank'];

            $bargain['collect_condition'] = array_filter(explode(',', $paymentinfo['collect_condition']));
            $bargain['collect_money'] = array_filter(explode(',', $paymentinfo['collect_money']));
        }
        $bargain['complete_signatory_id'] = $this->user_arr['signatory_id'];
        $bargain['complete_signatory_name'] = $this->user_arr['truename'];
        $data['bargain'] = $bargain;

        /**
         * 数据范围
         * 1、店长以上权限看公司
         * 2、店长及店长秘书权限查看本门店
         * 3、店长秘书以下没有权限
         */
        //获取访问菜单
//    $range_menu = $this->bargain_model->get_range_menu_by_role_level(
//      $this->user_arr, $this->user_arr['department_id']);
        //部门数据
        //$data['departments'] = $range_menu['departments'];
        //经纪人数据
        //$data['signatorys'] = $range_menu['signatorys'];

        // $data['signatorys'] = $this->signatorys($this->user_arr["company_id"]);
        $divide_add_per = $this->signatory_purview_model->check('');

        $replace_add_tax_per = $this->signatory_purview_model->check('17');

        $replace_add_per = $this->signatory_purview_model->check('17');

        $bargain_edit_per = $this->signatory_purview_model->check('2');

        $data['auth'] = array(
            'edit' => $bargain_edit_per, 'divide_add' => $divide_add_per, 'replace_tax_add' => $replace_add_per, 'replace_add' => $replace_add_per
        );

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view("bargain/finance_manage", $data);
    }

    /**
     * 选择房源
     * @access public
     * @return array
     */
    public function get_house($type = 1, $house_id = '')
    {
        //获取经纪人列表数组
        $this->load->model('api_signatory_model');

        //模板使用数据
        $data = array();
        $data['type'] = $type;
        $data['house_id'] = $house_id;

        //post参数
        $post_param = $this->input->post(NULL, TRUE);
        if (!isset($post_param['status'])) {
            $post_param['status'] = 1;
        }
        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
        $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
        $this->_init_pagination($page, $pagesize);

        //获取当前经纪人所在门店的数据范围
        $this->load->model('department_purview_model');
        $this->department_purview_model->set_department_id($this->user_arr['department_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
        $access_department_ids_data = $this->department_purview_model->get_department_id_by_main_id_access($this->user_arr['department_id'], 'is_view_house');
        $all_access_department_ids = '';
        if (is_full_array($access_department_ids_data)) {
            foreach ($access_department_ids_data as $k => $v) {
                $all_access_department_ids .= $v['sub_department_id'] . ',';
            }
            $all_access_department_ids .= $this->user_arr['department_id'];
            $all_access_department_ids = trim($all_access_department_ids, ',');
        } else {
            $all_access_department_ids = $this->user_arr['department_id'];
        }

        $cond_where = "`id` > 0 AND `department_id` in (" . $all_access_department_ids . ")";
        //默认公司
        $post_param['company_id'] = $this->user_arr['company_id'];
        $view_other_per_data = $this->signatory_purview_model->check('1');
        $view_other_per = $view_other_per_data['auth'];
        if ($view_other_per) {
            //如果有权限，赋予初始查询条件
            if (!isset($post_param['department_id'])) {
                $post_param['department_id'] = $this->user_arr['department_id'];
            }
            if (!isset($post_param['signatory_id'])) {
                $post_param['signatory_id'] = $this->user_arr['signatory_id'];
            }

            $data['departments'] = $this->department_model->get_all_by_department_id($all_access_department_ids);
            if ($post_param['department_id']) {
                $data['signatorys'] = $this->api_signatory_model->get_signatorys_department_id($post_param['department_id']);
            } else {
                $data['signatorys'] = array();
            }

        } else {
            //本人
            $post_param['signatory_id'] = $this->user_arr['signatory_id'];
            $data['departments'] = $this->department_model->get_all_by_department_id($this->user_arr['department_id']);
            $data['signatorys'] = $this->signatory_info_model->get_by_signatory_id(array('signatory_id' => $post_param['signatory_id']));
        }
        array_unshift($data['departments'], array('department_id' => '', 'department_name' => '不限'));
        array_unshift($data['signatorys'], array('signatory_id' => '', 'truename' => '不限'));
        //表单提交参数组成的查询条件
        $cond_where_ext = $this->_get_cond_str1($post_param);
        $cond_where .= $cond_where_ext;
        $cond_where = trim($cond_where);
        $cond_where = trim($cond_where, 'AND');
        $cond_where = trim($cond_where);

        if ($type == 1) {
            //符合条件的总行数
            $this->_total_count = $this->sell_house_model->get_count_by_cond($cond_where);

            //获取列表内容
            $list = $this->sell_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit);
        } else {
            //符合条件的总行数
            $this->_total_count = $this->rent_house_model->get_count_by_cond($cond_where);

            //获取列表内容
            $list = $this->rent_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit);
        }

        if (is_full_array($list)) {
            foreach ($list as $key => $val) {
                $signatoryinfo = $this->api_signatory_model->get_baseinfo_by_signatory_id($val['signatory_id']);
                $list[$key]['department_name'] = $signatoryinfo['department_name'];
                if ($type == 1) {
                    $list[$key]['house_id'] = format_info_id($val['id'], 'sell');
                } else {
                    $list[$key]['house_id'] = format_info_id($val['id'], 'rent');
                }
            }
        }
        $data['list'] = $list;
        $data['post_param'] = $post_param;
        //分页处理
        $params = array(
            'total_rows' => $this->_total_count, //总行数
            'method' => 'post', //URL提交方式 get/html/post
            'now_page' => $this->_current_page, //当前页数
            'list_rows' => $this->_limit, //每页显示个数
        );
        //加载分页类
        $this->load->library('page_list', $params);
        //调用分页函数（不同的样式不同的函数参数）
        $data['page_list'] = $this->page_list->show('jump');;

        //获取出售信息基本配置资料
        $data['config'] = $this->house_config_model->get_config();
        if ($type == 1) {
            $data['config']['status'][6] = '暂不售';
        } else {
            $data['config']['status'][6] = '暂不租';
        }
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view("bargain/bargain_choose_house", $data);
    }

    /**
     * 选择房源列表条件
     * 根据表单提交参数，获取查询条件
     */
    private function _get_cond_str1($form_param)
    {
        $cond_where = '';
        //状态
        $status = isset($form_param['status']) ? intval($form_param['status']) : 0;
        if ($status) {
            $cond_where .= " AND `status` = " . $status;
        }

        //楼盘
        $block_id = isset($form_param['block_id']) ? intval($form_param['block_id']) : 0;
        if ($block_id) {
            $cond_where .= " AND `block_id` = '" . $block_id . "'";
        }

        //房源编号
        $house_id = isset($form_param['house_id']) ? trim($form_param['house_id']) : "";
        if ($house_id) {
            $house_id = substr($house_id, 2);
            $cond_where .= " AND `id` = " . $house_id;
        }

        //公司
        $company_id = isset($form_param['company_id']) ? intval($form_param['company_id']) : 0;
        if ($company_id) {
            $cond_where .= " AND `company_id` = '" . $company_id . "'";
        }

        //门店
        $department_id = isset($form_param['department_id']) ? intval($form_param['department_id']) : 0;
        if ($department_id) {
            $cond_where .= " AND `department_id` = '" . $department_id . "'";
        }

        //员工
        $signatory_id = isset($form_param['signatory_id']) ? intval($form_param['signatory_id']) : 0;
        if ($signatory_id) {
            $cond_where .= " AND `signatory_id` = '" . $signatory_id . "'";
        }

        //姓名
        $truename = isset($form_param['truename']) ? trim($form_param['truename']) : "";
        if ($truename) {
            $cond_where .= " AND truename LIKE '%" . $truename . "%'";
        }

        //客户编号
        $customer_id = isset($form_param['customer_id']) ? trim($form_param['customer_id']) : "";
        if ($customer_id) {
            $customer_id = substr($customer_id, 2);
            $cond_where .= " AND id = '" . $customer_id . "'";
        }

        return $cond_where;
    }

    public function _get_cond_str2($form_param)
    {

        $cond_where = " AND `esta` in (4,7)";
        //状态
        if (isset($form_param['esta']) && !empty($form_param['esta'])) {
            $cond_where .= " AND esta = " . $form_param['esta'];
        }

        //成交编号
        if (isset($form_param['block_name']) && !empty($form_param['block_name'])) {
            $cond_where .= " AND block_name = '" . $form_param['block_name'] . "'";
        }

        //所属门店
        if (isset($form_param['department_id']) && !empty($form_param['department_id'])) {
            $cond_where .= " AND (agentid_a = {$form_param['department_id']} or agentid_b = {$form_param['department_id']})";
        }

        //所属经纪人
        if (isset($form_param['signatory_id']) && !empty($form_param['signatory_id'])) {
            $cond_where .= " AND (signatoryid_a = {$form_param['signatory_id']} or signatoryid_b = {$form_param['signatory_id']})";
        }

        return $cond_where;
    }

    /**
     * 选择客源
     * @access public
     * @return array
     */
    public function get_cooperate($type = 1, $order_sn = '')
    {

        $data['type'] = $type;
        $data['order_sn'] = $order_sn;

        //表单传递参数
        $post_param = $this->input->post(NULL, true);
        $data['post_param'] = $post_param;
        if ($type == 1) {
            $cond_where = "tbl = 'sell'";
        } else {
            $cond_where = "tbl = 'rent'";
        }
        //门店权限
        $level = $this->user_arr['role_level'];
        $data['level'] = $level;
        if ($level < 6) {
            //获取该公司下的所有门店
            $data['department'] = $this->department_model->get_children_by_company_id($this->user_arr['company_id']);

            //条件-分店
            $department_id = isset($post_param['department_id']) ? intval($post_param['department_id']) : 0;
            if ($department_id) {
                //获取经纪人列表数组
                $this->load->model('api_signatory_model');
                $signatorys = $this->api_signatory_model->get_signatorys_department_id($department_id);
                $data['signatorys'] = $signatorys;
            }

            if (is_full_array($data['department'])) {
                foreach ($data['department'] as $key => $val) {
                    $departmentid_arr[] = $val['id'];
                }
                $department_str = implode(',', $departmentid_arr);
            }
            $cond_where .= " and (agentid_a in ({$department_str}) or agentid_b in ({$department_str}))";
        } elseif ($level == 6) {
            $data['department'][0] = $this->department_model->get_by_id($this->user_arr['department_id']);
            $data['signatorys'] = $this->api_signatory_model->get_signatorys_department_id($this->user_arr['department_id']);
            $cond_where .= " and (agentid_a = {$this->user_arr['department_id']} or agentid_b = {$this->user_arr['department_id']})";
        }
        //表单提交参数组成的查询条件
        $cond_where_ext = $this->_get_cond_str2($post_param);
        $cond_where .= $cond_where_ext;

        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
        $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
        $this->_init_pagination($page, $pagesize);

        //符合条件的总行数
        $this->_total_count = $this->cooperate_model->get_cooperate_num_by_cond($cond_where);

        //计算总页数
        $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

        $select_field = array('order_sn', 'esta', 'rowid', 'signatory_name_a', 'signatory_name_b', 'department_name_a', 'department_name_b');
        //获取列表内容
        $list = $this->cooperate_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit);
        if (is_full_array($list)) {
            $select_field = array('district_id', 'street_id', 'price', 'block_name', 'room', 'hall', 'toilet', 'fitment', 'forward', 'buildarea');
            if ($type == 1) {
                foreach ($list as $key => $val) {
                    $this->department_model->set_select_fields(array('name'));
                    $department_a = $this->department_model->get_by_id($val['agentid_a']);
                    $list[$key]['agent_name_a'] = $department_a['name'];
                    $department_b = $this->department_model->get_by_id($val['agentid_b']);
                    $list[$key]['agent_name_b'] = $department_b['name'];

                    $this->sell_house_model->set_search_fields($select_field);
                    $this->sell_house_model->set_id($val['rowid']);
                    $list[$key]['house_info'] = $this->sell_house_model->get_info_by_id();
                }
            } else {
                foreach ($list as $key => $val) {
                    $this->department_model->set_select_fields(array('name'));
                    $department_a = $this->department_model->get_by_id($val['agentid_a']);
                    $list[$key]['agent_name_a'] = $department_a['name'];
                    $department_b = $this->department_model->get_by_id($val['agentid_b']);
                    $list[$key]['agent_name_b'] = $department_b['name'];

                    $this->rent_house_model->set_search_fields($select_field);
                    $this->rent_house_model->set_id($val['rowid']);
                    $list[$key]['house_info'] = $this->rent_house_model->get_info_by_id();
                }
            }
        }
        $data['list'] = $list;

        //获得区属和板块的二维数组
        $data['district_arr'] = $this->district_model->get_all_district_street();

        //状态数组
        $base_conf = $this->cooperate_model->get_base_conf();
        $esta_conf = array('4' => $base_conf['esta'][4], '7' => $base_conf['esta'][7]);
        $data['esta_conf'] = $esta_conf;

        //分页处理
        $params = array(
            'total_rows' => $this->_total_count, //总行数
            'method' => 'post', //URL提交方式 get/html/post
            'now_page' => $this->_current_page, //当前页数
            'list_rows' => $this->_limit, //每页显示个数
        );
        //加载分页类
        $this->load->library('page_list', $params);
        //调用分页函数（不同的样式不同的函数参数）
        $data['page_list'] = $this->page_list->show('jump');


        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,' . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view("bargain/bargain_choose_cooperate", $data);
    }

    /**
     * 选择客源
     * @access public
     * @return array
     */
    public function get_customer($type = 1, $customer_id = '')
    {
        //获取经纪人列表数组
        $this->load->model('api_signatory_model');

        //模板使用数据
        $data = array();

        $data['type'] = $type;
        $data['customer_id'] = $customer_id;
        //post参数
        $post_param = $this->input->post(NULL, TRUE);
        if (!isset($post_param['status'])) {
            $post_param['status'] = 1;
        }
        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
        $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
        $this->_init_pagination($page, $pagesize);

        //获取当前经纪人所在门店的数据范围
        $this->load->model('department_purview_model');
        $this->department_purview_model->set_department_id($this->user_arr['department_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
        $access_department_ids_data = $this->department_purview_model->get_department_id_by_main_id_access($this->user_arr['department_id'], 'is_view_house');
        $all_access_department_ids = '';
        if (is_full_array($access_department_ids_data)) {
            foreach ($access_department_ids_data as $k => $v) {
                $all_access_department_ids .= $v['sub_department_id'] . ',';
            }
            $all_access_department_ids .= $this->user_arr['department_id'];
            $all_access_department_ids = trim($all_access_department_ids, ',');
        } else {
            $all_access_department_ids = $this->user_arr['department_id'];
        }

        $cond_where = "`id` > 0 AND `department_id` in (" . $all_access_department_ids . ")";
        //默认公司
        $post_param['company_id'] = $this->user_arr['company_id'];
        $view_other_per_data = $this->signatory_purview_model->check('1');
        $view_other_per = $view_other_per_data['auth'];
        if ($view_other_per) {
            //如果有权限，赋予初始查询条件
            if (!isset($post_param['department_id'])) {
                $post_param['department_id'] = $this->user_arr['department_id'];
            }
            if (!isset($post_param['signatory_id'])) {
                $post_param['signatory_id'] = $this->user_arr['signatory_id'];
            }

            $data['departments'] = $this->department_model->get_all_by_department_id($all_access_department_ids);
            if ($post_param['department_id']) {
                $data['signatorys'] = $this->api_signatory_model->get_signatorys_department_id($post_param['department_id']);
            }

        } else {
            //本人
            $post_param['signatory_id'] = $this->user_arr['signatory_id'];
            $data['departments'] = $this->department_model->get_all_by_department_id($this->user_arr['department_id']);
            $data['signatorys'] = $this->signatory_info_model->get_by_signatory_id(array('signatory_id' => $post_param['signatory_id']));
        }
        array_unshift($data['departments'], array('department_id' => '0', 'department_name' => '不限'));
        array_unshift($data['signatorys'], array('signatory_id' => '0', 'truename' => '不限'));

        //表单提交参数组成的查询条件
        $cond_where_ext = $this->_get_cond_str1($post_param);
        $cond_where .= $cond_where_ext;
        $cond_where = trim($cond_where);
        $cond_where = trim($cond_where, 'AND');
        $cond_where = trim($cond_where);

        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
        $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
        $this->_init_pagination($page, $pagesize);

        if ($type == 1) {

            //获取求购信息基本配置资料
            $config = $this->buy_customer_model->get_base_conf();
            $data['config'] = $config;

            //符合条件的总行数
            $this->_total_count = $this->buy_customer_model->get_buynum_by_cond($cond_where);

            //计算总页数
            $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
            //获取列表内容
            $list = $this->buy_customer_model->get_buylist_by_cond($cond_where, $this->_offset, $this->_limit);
            $tbl = 'buy_customer';
        } else {
            //获取求租信息基本配置资料
            $config = $this->rent_customer_model->get_base_conf();
            $data['config'] = $config;

            //符合条件的总行数
            $this->_total_count = $this->rent_customer_model->get_rentnum_by_cond($cond_where);

            //计算总页数
            $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
            //获取列表内容
            $list = $this->rent_customer_model->get_rentlist_by_cond($cond_where, $this->_offset, $this->_limit);
            $tbl = 'rent_customer';
        }
        if ($list) {
            foreach ($list as $key => $val) {
                $signatoryinfo = $this->api_signatory_model->get_baseinfo_by_signatory_id($val['signatory_id']);
                $list[$key]['signatory_name'] = $signatoryinfo['truename'];
                $list[$key]['department_name'] = $signatoryinfo['department_name'];
                $list[$key]['customer_id'] = format_info_id($val['id'], $tbl);
            }
        }
        $data['list'] = $list;
        $data['post_param'] = $post_param;
        //获得区属和板块的二维数组
        $data['district_arr'] = $this->district_model->get_all_district_street();


        //分页处理
        $params = array(
            'total_rows' => $this->_total_count, //总行数
            'method' => 'post', //URL提交方式 get/html/post
            'now_page' => $this->_current_page, //当前页数
            'list_rows' => $this->_limit, //每页显示个数
        );
        //加载分页类
        $this->load->library('page_list', $params);
        //调用分页函数（不同的样式不同的函数参数）
        $data['page_list'] = $this->page_list->show('jump');


        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,' . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view("bargain/bargain_choose_customer", $data);
    }

    //修改成交内容匹配
    public function modify_match($data1, $data2, $config)
    {
        $data = array_diff_assoc($data1, $data2);
        $str = '';
        $type = intval($data2['type']);

        $base_config = $this->house_config_model->get_config();
        $config = $this->bargain_config_model->get_config();
        foreach ($data as $key => $val) {
            $this->load->model('api_signatory_model');
            switch ($key) {
                case 'signing_time':
                    $str .= "“签约日期”由“{$data2['signing_time']}”改为“{$data1['signing_time']}”；";
                    break;
                case 'buildarea':
                    $str .= "“面积”由“" . strip_end_0($data2['buildarea']) . "m²”改为“" . strip_end_0($data1['buildarea']) . "m²”；";
                    break;

                case 'house_addr':
                    $str .= "“房源地址”由“" . strip_end_0($data2['buildarea']) . "”改为“" . strip_end_0($data1['buildarea']) . "”；";
                    break;
                case 'block_name':
                    $str .= "“楼盘”由“" . strip_end_0($data2['block_name']) . "”改为“" . strip_end_0($data1['block_name']) . "”；";
                    break;
                case 'sell_type':
                    $str .= "“物业类型”由“" . $base_config['sell_type'][$data2['sell_type']] . "”改为“" . $base_config['sell_type'][$data2['sell_type']] . "”；";
                    break;
                case 'is_cooperdate':
                    $str .= "“是否合作”由“" . $config['is_cooperate'][$data2['is_cooperate']] . "”改为“" . $config['is_cooperate'][$data2['is_cooperate']] . "”；";
                    break;
                case 'order_sn':
                    $str .= "“合作编号”由“" . $data2['order_sn'] . "”改为“" . $data2['order_sn'] . "”；";
                    break;
                case 'owner':
                    $str .= "“业主姓名”由“{$data2['owner']}”改为“{$data1['owner']}”；";
                    break;
                case 'owner_idcard':
                    $str .= "“业主身份证号”由“{$data2['idcard_a']}”改为“{$data1['idcard_a']}”；";
                    break;
                case 'owner_tel':
                    $str .= "“业主联系方式”由“{$data2['tel_a']}”改为“{$data1['tel_a']}”；";
                    break;
                case 'signatory_id_a':
                    $info1 = $this->api_signatory_model->get_baseinfo_by_signatory_id($data1['signatory_id_a']);
                    $info2 = $this->api_signatory_model->get_baseinfo_by_signatory_id($data2['signatory_id_a']);
                    $str .= "“卖方签约人”由“" . $info2['department_name'] . "-" . $info2['truename'] . "”改为“" . $info1['department_name'] . "-" . $info1['truename'] . "”；";
                    break;
                case 'signatory_tel_a':
                    $str .= "“卖方签约人电话”由“{$data2['signatory_tel_a']}”改为“{$data1['signatory_tel_a']}”；";
                    break;
                case 'customer_id':
                    if ($type == 1) {
                        $str1 = "QG";
                    } elseif ($type == 2) {
                        $str1 = "QZ";
                    }
                    $str .= "“客户编号”由“{$str1}{$data2['customer_id']}”改为“{$str1}{$data1['customer_id']}”；";
                    break;
                case 'customer':
                    $str .= "“客户姓名”由“{$data2['customer']}”改为“{$data1['customer']}”；";
                    break;
                case 'customer_idcard':
                    $str .= "“客户身份证号”由“{$data2['customer_idcard']}”改为“{$data1['customer_idcard']}”；";
                    break;
                case 'customer_tel':
                    $str .= "“客户联系方式”由“{$data2['customer_tel']}”改为“{$data1['customer_tel']}”；";
                    break;
                case 'signatory_id_b':
                    $info1 = $this->api_signatory_model->get_baseinfo_by_signatory_id($data1['signatory_id_b']);
                    $info2 = $this->api_signatory_model->get_baseinfo_by_signatory_id($data2['signatory_id_b']);
                    $str .= "“买方签约人”由“" . $info2['department_name'] . "-" . $info2['truename'] . "”改为“" . $info1['department_name'] . "-" . $info1['truename'] . "”；";
                    break;
                case 'signatory_tel_b':
                    $str .= "“买方签约人电话”由“{$data2['signatory_tel_b']}”改为“{$data1['signatory_tel_b']}”；";
                    break;
                case 'remarks':
                    $str .= "“成交备注”由“{$data2['remarks']}”改为“{$data1['remarks']}”；";
                    break;
            }
            if ($type == 1) {
                switch ($key) {
                    case 'house_card':
                        $str .= "“签约日期”由“{$data2['house_card']}”改为“{$data1['house_card']}”；";
                        break;
                    case 'price':
                        $str .= "“成交金额”由“" . strip_end_0($data2['price']) . "万元”改为“" . strip_end_0($data1['price']) . "万元”；";
                        break;
                    case 'avgprice':
                        $str .= "“成交单价”由“" . strip_end_0($data2['avgprice']) . "元/m²”改为“" . strip_end_0($data1['avgprice']) . "元/m²”；";
                        break;
                    case 'buy_type':
                        $str .= "“购买方式”由“" . $config['buy_type'][$data2['buy_type']] . "”改为“" . $config['buy_type'][$data1['buy_type']] . "”；";
                        break;
                    case 'shoufu':
                        $str .= "“客户首付金额”由“" . strip_end_0($data2['shoufu']) . "元”改为“" . strip_end_0($data1['shoufu']) . "元”；";
                        break;
                    case 'loan':
                        $str .= "“客户贷款金额”由“" . strip_end_0($data2['loan']) . "元”改为“" . strip_end_0($data1['loan']) . "元”；";
                        break;
                    case 'business_tax':
                        $str .= "“营业税”由“" . $data2['business_tax'] ? "有" : "无" . "”改为“" . $data1['business_tax'] ? "有" : "无" . "”；";
                        break;
                    case 'tax':
                        $str .= "“个税”由“" . $data2['tax'] ? "有" : "无" . "”改为“" . $data1['tax'] ? "有" : "无" . "”；";
                        break;
                    case 'tax_pay_type':
                        $str .= "“购买方式”由“" . $config['tax_pay_type'][$data2['tax_pay_type']] . "”改为“" . $config['tax_pay_type'][$data1['tax_pay_type']] . "”；";
                        break;
                    case 'owner_tax_total':
                        $str .= "“业主税费合计”由“" . strip_end_0($data2['owner_tax_total']) . "元”改为“" . strip_end_0($data1['owner_tax_total']) . "元”；";
                        break;
                    case 'customer_tax_total':
                        $str .= "“客户税费合计”由“" . strip_end_0($data2['customer_tax_total']) . "元”改为“" . strip_end_0($data1['customer_tax_total']) . "元”；";
                        break;
                    case 'owner_commission':
                        $str .= "“业主应付佣金”由“" . strip_end_0($data2['owner_commission']) . "元”改为“" . strip_end_0($data1['owner_commission']) . "元”；";
                        break;
                    case 'customer_commission':
                        $str .= "“客户应付佣金”由“" . strip_end_0($data2['customer_commission']) . "元”改为“" . strip_end_0($data1['customer_commission']) . "元”；";
                        break;
                    case 'other_income':
                        $str .= "“其它收入”由“" . strip_end_0($data2['other_income']) . "元”改为“" . strip_end_0($data1['other_income']) . "元”；";
                        break;
                    case 'commission_total':
                        $str .= "“佣金收入总计”由“" . strip_end_0($data2['commission_total']) . "元”改为“" . strip_end_0($data1['commission_total']) . "元”；";
                        break;
                    case 'divide_percent':
                        $str .= "“合作分佣比例”由“" . $data2['divide_percent'] . "%”改为“" . $data1['divide_percent'] . "%”；";
                        break;
                    case 'divide_money':
                        $str .= "“合作分佣金额”由“" . strip_end_0($data2['divide_money']) . "元”改为“" . strip_end_0($data1['divide_money']) . "元”；";
                        break;
                }
            } elseif ($type == 2) {
                switch ($key) {
                    case 'start_time':
                        $str .= "“起租时间”由“" . $data2['start_time'] . "”改为“" . $data1['start_time'] . "”；";
                        break;
                    case 'end_time':
                        $str .= "“到期时间”由“" . $data2['end_time'] . "”改为“" . $data1['end_time'] . "”；";
                        break;
                    case 'latest_pay_time':
                        $str .= "“最迟付款时间”由“" . $data2['latest_pay_time'] . "”改为“" . $data1['latest_pay_time'] . "”；";
                        break;
                    case 'deposit':
                        $str .= "“押金”由“" . strip_end_0($data2['deposit']) . "元”改为“" . strip_end_0($data1['deposit']) . "元”；";
                        break;
                    case 'other_fees':
                        $str .= "“其它费用”由“" . strip_end_0($data2['other_fees']) . "元”改为“" . strip_end_0($data1['other_fees']) . "元”；";
                        break;
                    case 'pay_type':
                        $str .= "“付款方式”由“" . $config['pay_type2'][$data2['pay_type']] . "”改为“" . $config['pay_type2'][$data1['pay_type']] . "”；";
                        break;
                    case 'list_items':
                        $str .= "“物品清单”由“" . $data2['list_items'] . "”改为“" . $data1['list_items'] . "”；";
                        break;
                    case 'hydropower':
                        $str .= "“水电抄表”由“" . $data2['hydropower'] . "”改为“" . $data1['hydropower'] . "”；";
                        break;
                    case 'owner_commission':
                        $str .= "“出租应付佣金”由“" . strip_end_0($data2['owner_commission']) . "元”改为“" . strip_end_0($data1['owner_commission']) . "元”；";
                        break;
                    case 'customer_commission':
                        $str .= "“求租应付佣金”由“" . strip_end_0($data2['customer_commission']) . "元”改为“" . strip_end_0($data1['customer_commission']) . "元”；";
                        break;
                    case 'other_income':
                        $str .= "“其它收入”由“" . strip_end_0($data2['other_income']) . "元”改为“" . strip_end_0($data1['other_income']) . "元”；";
                        break;
                    case 'commission_total':
                        $str .= "“佣金收入总计”由“" . strip_end_0($data2['commission_total']) . "元”改为“" . strip_end_0($data1['commission_total']) . "元”；";
                        break;
                }
            }
        }

        return $str;
    }

    /**
     * 删除
     *
     * @access  public
     * @param  void
     * @return  void
     */
    public function del()
    {
        $id = $this->input->get('id');
        $rs = $this->bargain_model->update_by_id(array('is_del' => 1, 'is_template' => 0), $id);
        if ($rs) {
            //成交跟进——删除成交
            $data = array(
                'c_id' => $id,
                'type_name' => "成交删除",
                'content' => "本日对该成交信息进行删除。",
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'updatetime' => time()
            );
            $this->bargain_log_model->add_info($data);
            //操作日志
            $info = $this->bargain_model->get_by_id($id);
            $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'department_id' => $this->user_arr['department_id'],
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '删除成交编号为' . $info['number'] . '的交易成交。',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
            );
            $this->signatory_operate_log_model->add_operate_log($add_log_param);
            //删除成交相关的权证，业绩分成、应收应付，实收实付
            $this->bargain_divide_model->del_by_cid($id);//业绩
            $this->bargain_log_model->del_by_cid($id);//跟进
            $this->replace_payment_model->set_tbl('replace_payment');
            $this->replace_payment_model->del_by_cid($id);//实收实付
            $this->replace_payment_model->set_tbl('bargain_should_flow');
            $this->replace_payment_model->del_by_cid($id);//应收应付
            $this->bargain_transfer_model->del_by_cid($id);//权证
            echo json_encode(array('result' => '1'));
        } else {
            echo json_encode(array('result' => '0'));
        }
    }

    /**
     * 删除
     *
     * @access  public
     * @param  void
     * @return  void
     */
    public function cancel()
    {
        $id = $this->input->get('id');
        $rs = $this->bargain_model->update_by_id(array('is_check' => 4), $id);
        if ($rs) {
            //成交跟进——删除成交
            $data = array(
                'c_id' => $id,
                'type_name' => "成交作废",
                'content' => "对该成交进行作废，成交已终止。",
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'updatetime' => time()
            );
            $this->bargain_log_model->add_info($data);
            //操作日志
            $info = $this->bargain_model->get_by_id($id);
            $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'department_id' => $this->user_arr['department_id'],
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '作废成交编号为' . $info['number'] . '的交易成交。',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
            );
            $this->signatory_operate_log_model->add_operate_log($add_log_param);
            echo json_encode(array('result' => '1'));
        } else {
            echo json_encode(array('result' => '0'));
        }
    }

    /**
     * 成交审核
     * @access  public
     * @param   void
     * @return  void
     */
    public function bargain_review($type = 1)
    {
        //模板使用数据
        $data = array();

        //树型菜单
        $data['user_tree_menu'] = $this->user_tree_menu;

        //页面标题
        $data['page_title'] = '交易成交审核';

        //获取成交配置信息
        $data['config'] = $this->bargain_config_model->get_config();

        //post参数
        $post_param = $this->input->post(NULL, TRUE);
        $data['post_param'] = $post_param;

        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
        $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
        $this->_init_pagination($page, $pagesize);
        $data['page'] = $page;

        $role_level = $this->user_arr['role_level'];
        if ($role_level < 6) //公司
        {
            //所属公司
            $post_param['enter_company_id'] = $this->user_arr['company_id'];
        } else if ($role_level < 8) //门店
        {
            //所属公司
            $post_param['enter_company_id'] = $this->user_arr['company_id'];
            //所属门店
            $post_param['enter_department_id'] = $this->user_arr['department_id'];
        } else {
            //所属公司
            $post_param['enter_company_id'] = $this->user_arr['company_id'];
            //所属门店
            $post_param['enter_department_id'] = $this->user_arr['department_id'];
            //所属经纪人
            $post_param['enter_signatory_id'] = $this->user_arr['signatory_id'];
        }
        /**
         * 数据范围
         * 1、店长以上权限看公司
         * 2、店长及店长秘书权限查看本门店
         * 3、店长秘书以下没有权限
         */
        //获取访问菜单
        $range_menu = $this->bargain_model->get_range_menu_by_role_level(
            $this->user_arr, $post_param['department_id_a']);
        //门店数据
        $data['departments'] = $range_menu['departments'];
        //经纪人数据
        $data['signatorys'] = $range_menu['signatorys'];

        //权限
        $bargain_review_per = $this->signatory_purview_model->check('118');
        $bargain_fanreview_per = $this->signatory_purview_model->check('119');
        $data['auth'] = array(
            'review' => $bargain_review_per, 'fanreview' => $bargain_fanreview_per
        );
        //表单提交参数组成的查询条件
        $cond_where = $this->_get_cond_str($post_param);
        //查询交易类型 出售为1  出租为2
        //审核状态 0 未进入审核 1 未审核 2 审核通过 3 审核未通过 4 作废
        $cond_where .= " AND type = " . $type . " AND is_check > 0 AND is_check < 4";
        $data['type'] = $type;

        //清除条件头尾多余空格
        $cond_where = trim($cond_where);

        //符合条件的总行数
        $this->_total_count = $this->bargain_model->count_by($cond_where);

        //计算总页数
        $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

        //获取列表内容
        $list = $this->bargain_model->get_all_by($cond_where, $this->_offset, $this->_limit, $order_key = 'is_check', $order_by = 'ASC');

        $data['list'] = $list;
        //查询所有待审核成交的数量
        $cond_where1 = $cond_where . ' AND is_check = 1';
        //符合条件的总行数
        $data['total'] = $this->bargain_model->count_by($cond_where1);
        //分页处理
        $params = array(
            'total_rows' => $this->_total_count, //总行数
            'method' => 'post', //URL提交方式 get/html/post
            'now_page' => $this->_current_page, //当前页数
            'list_rows' => $this->_limit, //每页显示个数
        );
        //加载分页类
        $this->load->library('page_list', $params);
        //调用分页函数（不同的样式不同的函数参数）
        $data['page_list'] = $this->page_list->show('jump');

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,'
            . 'mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_review', $data);
    }

    /**
     * 成交审核
     */
    public function sure_review()
    {
        $bargain_id = $this->input->post('id');
        $review_type = intval($this->input->post('review_type'));
        $review_remark = trim($this->input->post('review_remark'));

        $update_data = array(
            'is_check' => $review_type,
            'check_time' => time(),
            'check_remark' => $review_remark,
            'check_department_id' => $this->user_arr['department_id'],
            'check_signatory_id' => $this->user_arr['signatory_id']
        );
        //更改成交表中成交状态
        $result = $this->bargain_model->update_by_id($update_data, $bargain_id);
        //审核通过后发送消息
        if ($result) {
            //记入日志
            if ($review_type == 2) {
                $add_data = array(
                    'c_id' => $bargain_id,
                    'type_name' => "成交审核",
                    'content' => "批准了该成交，成交审核通过。",
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'updatetime' => time()
                );
            } elseif ($review_type == 3) {
                $add_data = array(
                    'c_id' => $bargain_id,
                    'type_name' => "成交审核",
                    'content' => "拒绝该成交，成交审核未通过。",
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'updatetime' => time()
                );
            }
            $this->bargain_log_model->add_info($add_data);
            //操作日志
            $info = $this->bargain_model->get_by_id($bargain_id);
            $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'department_id' => $this->user_arr['department_id'],
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '审核成交编号为' . $info['number'] . '的交易成交。',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
            );
            $this->signatory_operate_log_model->add_operate_log($add_log_param);
            $return_data['result'] = 'ok';
        } else {
            $return_data['result'] = 'no';
        }
        echo json_encode($return_data);
    }

    /**
     * 成交审核
     */
    public function cancel_review()
    {
        $bargain_id = $this->input->post('id');
        $update_data = array(
            'is_check' => 1
        );
        //更改成交表中成交状态
        $result = $this->bargain_model->update_by_id($update_data, $bargain_id);
        //审核通过后发送消息
        if ($result) {
            //记入日志
            $add_data = array(
                'c_id' => $bargain_id,
                'type_name' => "成交审核",
                'content' => "对该成交进行反审核操作。",
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'updatetime' => time()
            );
            $this->bargain_log_model->add_info($add_data);
            //操作日志
            $info = $this->bargain_model->get_by_id($bargain_id);
            $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'department_id' => $this->user_arr['department_id'],
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '反审核成交编号为' . $info['number'] . '的交易成交。',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
            );
            $this->signatory_operate_log_model->add_operate_log($add_log_param);
            $return_data['result'] = 'ok';
        } else {
            $return_data['result'] = 'no';
        }
        echo json_encode($return_data);
    }

    /**
     * 获取成交详情
     * @access public
     * @return array
     */
    public function get_detail($id)
    {
        $data = $this->bargain_model->get_info_by_id($id);
        if (is_full_array($data)) {
            $config = $this->bargain_config_model->get_config();
            $data['config'] = $config;
        }
        return $data;
    }

    /**
     * 获取模板详情
     * @access public
     * @return array
     */
    public function get_transfer()
    {
        $id = $this->input->post(id);
        //权证流程
        $transfer_step = $this->bargain_transfer_model->get_all_by_cid($id);
        //权证配置项
        $stage_conf = $this->bargain_transfer_model->get_stage_conf();
        //权证步骤名配置
        $data['stage'] = $this->bargain_transfer_model->get_all_stage();
        if (is_full_array($transfer_step)) {
            foreach ($transfer_step as $key => $val) {
                $arr = array();
                $stage_name = explode(',', $val['stage_id']);
                foreach ($stage_name as $k => $v) {
                    $arr[] = $data['stage'][$v]['stage_name'];
                    $transfer_step[$key]['stage_name1'] = implode('，', $arr);
                    if (count($arr) > 1) {
                        $transfer_step[$key]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'] . '...';
                    } else {
                        $transfer_step[$key]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'];
                    }
                }
                $signatory_info = $this->api_signatory_model->get_baseinfo_by_signatory_id($val['signatory_id']);
                $transfer_step[$key]['signatory_name'] = $signatory_info['truename'];
                $transfer_step[$key]['department_name'] = $signatory_info['department_name'];
                $signatory_info1 = $this->api_signatory_model->get_baseinfo_by_signatory_id($val['complete_signatory_id']);
                $transfer_step[$key]['complete_signatory_name'] = $signatory_info1['truename'];
                $transfer_step[$key]['complete_department_name'] = $signatory_info1['department_name'];
                $transfer_step[$key]['key'] = $key + 1;
                $transfer_step[$key]['step'] = $stage_conf[$key + 1]['text'];
            }

            $return_data['transfer_list'] = $transfer_step;
        }
        $return_data['result'] = 'ok';
        echo json_encode($return_data);
    }

    /**
     * 结佣操作
     * @access public
     * @return array
     */
    public function confirm_all_commission()
    {
        $c_id = $this->input->post('c_id');
        $get_total = $this->bargain_divide_model->get_total($c_id);
        if ($get_total['percent_total'] == 100) {
            $result = $this->bargain_divide_model->update_complete($c_id);
            $update_data = array('is_commission' => 1, 'commission_time' => time());
            $this->bargain_model->update_by_id($update_data, $c_id);
            $add_data = array(
                'c_id' => $c_id,
                'type_name' => "业绩分成",
                'content' => "对所有业绩分成完成结佣。",
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'updatetime' => time()
            );
            $this->bargain_log_model->add_info($add_data);
            $return_data['msg'] = "已完成结佣！";
            $return_data['result'] = 'ok';
            //操作日志
            $info = $this->bargain_model->get_by_id($bargain_id);
            $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'department_id' => $this->user_arr['department_id'],
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '对成交编号为' . $info['number'] . '的交易成交进行结佣。',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
            );
            $this->signatory_operate_log_model->add_operate_log($add_log_param);
        } else {
            $return_data['msg'] = "您还有剩余的业绩未分配！";
            $return_data['result'] = 'no';
        }
        echo json_encode($return_data);
    }

    /**
     * 获取分佣详情
     * @access public
     * @return array
     */
    public function divide_detail()
    {
        $id = $this->input->post('id');
        $data['divide_list'] = $this->bargain_divide_model->get_by_id($id);
        $this->signatory_info_model->set_select_fields(array('signatory_id', 'truename'));
        $data['signatory_list'] = $this->signatory_info_model->get_by_department_id($data['divide_list']['department_id']);
        $data['achieve_signatory_list_a'] = $this->signatory_info_model->get_by_department_id($data['divide_list']['achieve_department_id_a']);
        $data['achieve_signatory_list_b'] = $this->signatory_info_model->get_by_department_id($data['divide_list']['achieve_department_id_b']);
        if ($data['divide_list']) {
            $data['result'] = 1;
        } else {
            $data['result'] = 0;
        }
        echo json_encode($data);
    }

    /**
     * 添加业绩分成
     * @access public
     * @param  int $bargain_id
     * @param  int $is_ajax
     * @return void
     */
    public function divide_manage()
    {
        $post_param = $this->input->post(NULL, TRUE);
        //业绩id
        $divide_id = $post_param['divide_id'];
        $c_id = $post_param['c_id'];
        //获取成交配置项
        $config = $this->bargain_config_model->get_config();

        $info1 = $this->api_signatory_model->get_baseinfo_by_signatory_id($post_param['signatory_id']);
        $info2 = $this->api_signatory_model->get_baseinfo_by_signatory_id($post_param['achieve_signatory_id_a']);
        $info3 = $this->api_signatory_model->get_baseinfo_by_signatory_id($post_param['achieve_signatory_id_b']);
        $info4 = $this->api_signatory_model->get_baseinfo_by_signatory_id($this->user_arr['signatory_id']);
        $datainfo = array(
            "c_id" => $c_id,
            "company_id" => intval($info1['company_id']),
            "department_id" => intval($post_param['department_id']),
            "department_name" => trim($info1['department_name']),
            "signatory_id" => intval($post_param['signatory_id']),
            "signatory_name" => trim($info1['truename']),
            "divide_price" => trim($post_param['divide_price']),
            "percent" => sprintf('%.2f', $post_param['divide_percent']),
            "divide_type" => intval($post_param['divide_type']),
            "achieve_company_id" => $this->user_arr['company_id'],
            "achieve_department_id_a" => $post_param['achieve_department_id_a'],
            "achieve_department_name_a" => $info2['department_name'],
            "achieve_signatory_id_a" => $post_param['achieve_signatory_id_a'],
            "achieve_signatory_name_a" => $info2['truename'],
            "achieve_department_id_b" => $post_param['achieve_department_id_b'],
            "achieve_department_name_b" => $info3['department_name'],
            "achieve_signatory_id_b" => $post_param['achieve_signatory_id_b'],
            "achieve_signatory_name_b" => $info3['truename'],
            "entry_time" => time(),
            'entry_company_id' => $this->user_arr['company_id'],
            'entry_department_id' => $this->user_arr['department_id'],
            'entry_department_name' => $info4['department_name'],
            "entry_signatory_id" => $this->user_arr['signatory_id'],
            "entry_signatory_name" => $this->user_arr['truename']
        );
        if ($divide_id) {
            $old_data = $this->bargain_divide_model->get_by_id($divide_id);
            //修改
            $rs = $this->bargain_divide_model->update_by_id($datainfo, $divide_id);
            $old_total = $this->bargain_divide_model->get_total($c_id);
            $old_total['percent_total'] = $old_total['percent_total'] ? $old_total['percent_total'] : '0';
            $new_total = intval($old_total['percent_total'] + $datainfo['percent'] - 100);
            if ($new_total < 100) {
                if ($rs) {
                    $str = $this->modify_divide_match($datainfo, $old_data);
                    $add_data = array(
                        'c_id' => $c_id,
                        'type_name' => "业绩分成",
                        'content' => "修改业绩分成，" . $str,
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'updatetime' => time()
                    );
                    $this->bargain_log_model->add_info($add_data);
                    $return_data['msg'] = '修改业绩分成成功';
                    $return_data['result'] = 'ok';
                    //操作日志
                    $info = $this->bargain_model->get_by_id($c_id);
                    $add_log_param = array(
                        'company_id' => $this->user_arr['company_id'],
                        'department_id' => $this->user_arr['department_id'],
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'type' => 35,
                        'text' => '修改成交编号为' . $info['number'] . '的交易成交的业绩分成。' . $str,
                        'from_system' => 1,
                        'from_ip' => get_ip(),
                        'mac_ip' => '127.0.0.1',
                        'from_host_name' => '127.0.0.1',
                        'hardware_num' => '测试硬件序列号',
                        'time' => time()
                    );
                    $this->signatory_operate_log_model->add_operate_log($add_log_param);
                } else {
                    $return_data['msg'] = '修改业绩分成失败';
                    $return_data['result'] = 'no';
                }
            } else {
                $return_data['msg'] = '分配比例总和不能超过100%';
                $return_data['result'] = 'no';
            }
        } else {
            $bargain = $this->bargain_model->get_by_id($c_id);
            $datainfo['type'] = $bargain['type'];
            $old_total = $this->bargain_divide_model->get_total($c_id);
            $old_total['percent_total'] = $old_total['percent_total'] ? $old_total['percent_total'] : '0';
            $new_total = intval($old_total['percent_total'] + $datainfo['percent'] - 100);
            if ($new_total < 100) {
                //添加
                $id = $this->bargain_divide_model->add_info($datainfo);
                if ($id) {
                    //成交跟进——添加业绩分成
                    $add_data = array(
                        'c_id' => $c_id,
                        'type_name' => "业绩分成",
                        'content' => "添加业绩分成，归属人" . $datainfo['signatory_name'] . ',占比' . $datainfo['percent_total'] . "%。",
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'updatetime' => time()
                    );
                    $this->bargain_log_model->add_info($add_data);
                    $return_data['msg'] = '添加业绩分成成功';
                    $return_data['result'] = 'ok';
                    $return_data['num'] = $this->bargain_divide_model->count_by(array('c_id' => $c_id));
                    //操作日志
                    $info = $this->bargain_model->get_by_id($c_id);
                    $add_log_param = array(
                        'company_id' => $this->user_arr['company_id'],
                        'department_id' => $this->user_arr['department_id'],
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'type' => 35,
                        'text' => '添加成交编号为' . $info['number'] . '的交易成交的业绩分成。',
                        'from_system' => 1,
                        'from_ip' => get_ip(),
                        'mac_ip' => '127.0.0.1',
                        'from_host_name' => '127.0.0.1',
                        'hardware_num' => '测试硬件序列号',
                        'time' => time()
                    );
                    $this->signatory_operate_log_model->add_operate_log($add_log_param);
                } else {
                    $return_data['msg'] = '添加业绩分成失败';
                    $return_data['result'] = 'no';
                }
            } else {
                $return_data['msg'] = '当前最大可填最大比例为' . sprintf('%.2f', (100 - $old_data['percent_total'])) . '%';
                $return_data['result'] = 'no';
            }
        }
        echo json_encode($return_data);
    }


    /**
     * 删除业绩分成
     *
     * @access  public
     * @param  void
     * @return  void
     */
    public function divide_del()
    {
        $id = $this->input->post('id');
        $c_id = $this->input->post('c_id');
        $data = $this->bargain_divide_model->get_by_id($id);
        $result = $this->bargain_divide_model->del_by_id($id);
        if (!empty($result) && is_int($result)) {
            $add_data = array(
                'c_id' => $c_id,
                'type_name' => "'业绩分成",
                'content' => "'删除业绩分成，归属人，{$data['signatory_name']}。",
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'updatetime' => time()
            );
            $return_data['msg'] = '删除业绩分成成功！';
            $this->bargain_log_model->add_info($add_data);
            $return_data['result'] = 'ok';
            $return_data['num'] = $this->bargain_divide_model->count_by(array('c_id' => $c_id));
            //操作日志
            $info = $this->bargain_model->get_by_id($c_id);
            $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'department_id' => $this->user_arr['department_id'],
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '删除成交编号为' . $info['number'] . '的交易成交的业绩分成',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
            );
            $this->signatory_operate_log_model->add_operate_log($add_log_param);
        } else {
            $return_data['msg'] = '删除业绩分成失败！';
            $return_data['result'] = 'no';
        }
        $total = $this->bargain_divide_model->get_total($c_id);
        $datainfo['divide_percent_total'] = $total['percent_total'];
        $datainfo['divide_price_total'] = $total['price_total'];
        $return_data['divide_list'] = $datainfo;
        $add_data['updatetime'] = date('Y-m-d', $add_data['updatetime']);
        $return_data['follow_list'] = $add_data;
        echo json_encode($return_data);
    }

    /* ----------------------------------------------------------------------------------------------- */
    /* -------------------------------权证模板流程部分-------------------------------------------------- */

    //模板详情
    public function template_detail()
    {
        $id = $this->input->post('id');
        $key = $this->input->post('key');
        //权证配置项
        $stage_conf = $this->bargain_transfer_model->get_stage_conf();
        //权证步骤名配置
        $stage = $this->bargain_transfer_model->get_all_stage();
        $data['key'] = $stage_conf[$key]['text'];
        $data['transfer_list'] = $this->transfer_model->get_step_by_id($id);
        $data['transfer_list']['stage_id'] = explode(',', $data['transfer_list']['stage_id']);
        foreach ($data['transfer_list']['stage_id'] as $key => $val) {
            $stage_name[] = $stage[$val]['stage_name'];
        }
        $data['transfer_list']['stage_name'] = implode(',', $stage_name);
        if ($data['transfer_list']) {
            $data['result'] = 1;
        } else {
            $data['result'] = 0;
        }
        echo json_encode($data);
    }

    //权证详情的权证模板步骤添加
    public function transfer_template_add($id = 1, $key1 = 1, $c_id = 0)
    {
        $data['page_title'] = '编辑权证流程模板';
        $data['stage'] = $this->transfer_model->get_all_stage();
        $post_param = $this->input->post(null, true);
        //key为传参，决定页面执行不同的js
        $data['key1'] = $key1;
        //该模板id
        $data['id'] = $id;
        //成交id
        $data['c_id'] = $c_id;
        //获得该模板下所有步骤
        $template_info = $this->transfer_model->get_temp_by_id($id);

        //根据模板的ID读取步骤
        $steps = $this->transfer_model->get_step_by_template_id($id);

        //权证配置项
        $data['stage_conf'] = $this->bargain_transfer_model->get_stage_conf();
        //权证步骤名配置
        $data['stage'] = $this->bargain_transfer_model->get_all_stage();

        $new_step = array();
        if (is_full_array($steps)) {
            foreach ($steps as $k => $v) {
                $arr = array();
                $stage_name = explode(',', $v['stage_id']);
                foreach ($stage_name as $k1 => $v1) {
                    $arr[] = $data['stage'][$v1]['stage_name'];
                    $steps[$k]['stage_name1'] = implode('，', $arr);
                    if (count($arr) > 1) {
                        $steps[$k]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'] . '...';
                    } else {
                        $steps[$k]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'];
                    }
                    $signatory_info = $this->api_signatory_model->get_baseinfo_by_signatory_id($v['signatory_id']);
                    $steps[$k]['signatory_name'] = $signatory_info['truename'];
                    $steps[$k]['department_name'] = $signatory_info['department_name'];
                }
            }
        }
        $template_info['step'] = $steps;
        $data['template'] = $template_info;
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/transfer_add_template', $data);
    }


    public function transfer_template($id = 1)
    {
        $data['page_title'] = '选择权证流程模板';
        $data['stage'] = $this->transfer_model->get_all_stage();
        $post_param = $this->input->post(null, true);

        $where = "(type = 0 and company_id = 0) or (type = 1 and company_id = {$this->user_arr['company_id']})";

        //获得公司下所有模板
        $data['sys_temps'] = $this->transfer_model->get_all_temps($where, $this->_offset, $this->_limit, 'id', 'ASC');

        //根据模板的ID读取步骤
        $steps = $this->transfer_model->get_step_by_template_id($id);

        //权证配置项
        $data['stage_conf'] = $this->bargain_transfer_model->get_stage_conf();
        //权证步骤名配置
        $data['stage'] = $this->bargain_transfer_model->get_all_stage();

        $new_step = array();
        if (is_full_array($steps)) {
            foreach ($steps as $k => $v) {
                $arr = array();
                $stage_name = explode(',', $v['stage_id']);
                foreach ($stage_name as $k1 => $v1) {
                    $arr[] = $data['stage'][$v1]['stage_name'];
                    $steps[$k]['stage_name1'] = implode('，', $arr);
                    if (count($arr) > 1) {
                        $steps[$k]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'] . '...';
                    } else {
                        $steps[$k]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'];
                    }
                    $signatory_info = $this->api_signatory_model->get_baseinfo_by_signatory_id($v['signatory_id']);
                    $steps[$k]['signatory_name'] = $signatory_info['truename'];
                    $steps[$k]['department_name'] = $signatory_info['department_name'];
                }
            }
        }
        $data['template_steps'] = $steps;
        $data['id'] = $id;
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_template', $data);
    }

    /** 过户流程撤销 */
    public function cancel_temp_step()
    {
        $stage_id = $this->input->post('stage_id');
        $c_id = $this->input->post('c_id');
        //权证步骤名配置
        $stage = $this->bargain_transfer_model->get_all_stage();

        $data = $this->bargain_transfer_model->get_by_id($stage_id);//获取原来的数据

        $stageid_arr = explode(',', $data['stage_id']);
        foreach ($stageid_arr as $key => $val) {
            $stage_name1[] = $stage[$val]['stage_name'];
        }
        $stage_name = implode(',', $stage_name1);

        //权证配置项
        $stage_conf = $this->bargain_transfer_model->get_stage_conf();
        //更新
        $canceldata = array();
        $canceldata['isComplete'] = 0;  //修改属性和值
        $canceldata['complete_department_id'] = "";
        $canceldata['complete_signatory_id'] = "";
        $canceldata['complete_signatory_name'] = "";
        $canceldata['complete_time'] = "";

        $rs = $this->bargain_transfer_model->modify_data($stage_id, $canceldata);
        if ($rs) {
            $add_data = array(
                'c_id' => $c_id,
                'type_name' => "权证流程",
                'content' => "撤销权证流程步骤，{$stage_conf[$data['step_id']]['text']}：{$stage_name}。",
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'updatetime' => time()
            );
            $return_data['msg'] = "步骤：{$stage_name}已撤销！";
            $this->bargain_log_model->add_info($add_data);
            $return_data['result'] = 'ok';

            //操作日志
            $info = $this->bargain_model->get_by_id($c_id);
            $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'department_id' => $this->user_arr['department_id'],
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '撤销编号为' . $info['number'] . '的交易成交的权限流程',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
            );
            $this->signatory_operate_log_model->add_operate_log($add_log_param);
        } else {
            $return_data['msg'] = "步骤：{$stage_name}撤销失败！";
            $return_data['result'] = 'no';
        }
        echo json_encode($return_data);
    }

    /**
     * 选择模板操作
     */
    public function select_transfer_template($template_id, $bargain_id)
    {
//        $template_id = $this->input->post('template_id');
//        $bargain_id = $this->input->post('bargain_id');
        $this->load->model('transfer_model');
        $steps = $this->transfer_model->get_step_by_template_id($template_id);//获取模板步骤信息
        //权证配置项
        $this->load->model('bargain_transfer_model');
        $stage_conf = $this->bargain_transfer_model->get_stage_conf();
        //权证步骤名配置
        $stage = $this->bargain_transfer_model->get_all_stage();
        $num = 0;
        if (is_full_array($steps)) {
            foreach ($steps as $key => $val) {
                $add_data['bargain_id'] = $bargain_id;
                $add_data['stage_id'] = $val['stage_id'];
                $add_data['step_id'] = $key + 1;
                $add_data['signatory_id'] = $this->user_arr['signatory_id'];
                $add_data['department_id'] = $this->user_arr['department_id'];
                $add_data['company_id'] = $this->user_arr['company_id'];
                $add_data['createtime'] = time();
                //依次将新步骤添加到步骤表
                $rs = $this->bargain_transfer_model->insert_data($add_data);
                if ($rs) {
                    $stage_id = explode(',', $add_data['stage_id']);
                    foreach ($stage_id as $k => $v) {
                        $arr[] = $stage[$v]['stage_name'];
                    }
                    $stage_name = implode('，', $arr);
                    $follow_data = array(
                        'c_id' => $bargain_id,
                        'type_name' => "权证流程步骤",
                        'content' => "添加权证流程步骤，{$stage_conf[$key+1]['text']}：{$stage_name}。",
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'updatetime' => time()
                    );
                    $this->bargain_log_model->add_info($follow_data);
                    //操作日志
                    $info = $this->bargain_model->get_by_id($bargain_id);
                    $add_log_param = array(
                        'company_id' => $this->user_arr['company_id'],
                        'department_id' => $this->user_arr['department_id'],
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'type' => 35,
                        'text' => '添加成交编号为' . $info['result'] . '的交易成交的权限流程。',
                        'from_system' => 1,
                        'from_ip' => get_ip(),
                        'mac_ip' => '127.0.0.1',
                        'from_host_name' => '127.0.0.1',
                        'hardware_num' => '测试硬件序列号',
                        'time' => time()
                    );
                    $this->signatory_operate_log_model->add_operate_log($add_log_param);
                }
                $num++;
            }
        }
        $total = $this->transfer_model->get_count('transfer_template_step', array('template_id' => $template_id));
        if ($num == $total) {
            $this->bargain_model->update_by_id(array('template_id' => $template_id, 'is_template' => 1), $bargain_id);
            $json_data['status'] = 1;
        } else {
            $json_data['status'] = 0;
        }
        return $json_data;
    }

    /**
     * 过户流程
     */
    public function bargain_transfer_manage($id)
    {
        $data['bargain'] = $this->bargain_model->get_by_id($id);
        //获取配置项
        $data['config'] = $this->bargain_config_model->get_config();
        //权证配置项
        $data['stage_conf'] = $this->bargain_transfer_model->get_stage_conf();
        //权证步骤名配置
        $data['stage'] = $this->bargain_transfer_model->get_all_stage();
        //过户流程初始化
        $data['transfer_step_total'] = $this->bargain_transfer_model->count_by(array('bargain_id' => $id));
        if ($data['transfer_step_total'] <= 0) {//如果没有选择模板，默认选择系统模板
            if ($data['bargain']['type'] == 2) {//二手房
                $template_id = 1;//系统模板
                $this->select_transfer_template($template_id, $id);
                $data['transfer_step_total'] = $this->bargain_transfer_model->count_by(array('bargain_id' => $id));
            } elseif ($data['bargain']['type'] == 1) {//一手房
                if ($data['bargain']['agent_type'] == 3 || $data['bargain']['agent_type'] == 2) { //期房转现房或现房
                    $template_id = 2;//期房转现房或现房模板
                    $this->select_transfer_template($template_id, $id);
                    $data['transfer_step_total'] = $this->bargain_transfer_model->count_by(array('bargain_id' => $id));
                } elseif ($data['bargain']['agent_type'] == 1) {//预告登记
                    $template_id = 3;//预告登记模板
                    $this->select_transfer_template($template_id, $id);
                    $data['transfer_step_total'] = $this->bargain_transfer_model->count_by(array('bargain_id' => $id));
                }
            }

        }
        //权证流程
        $transfer_step = $this->bargain_transfer_model->get_all_by_cid($id);
        if (is_full_array($transfer_step)) {
            foreach ($transfer_step as $key => $val) {
                $arr = array();
                $stage_name = explode(',', $val['stage_id']);
                foreach ($stage_name as $k => $v) {
                    $arr[] = $data['stage'][$v]['stage_name'];
                    $transfer_step[$key]['stage_name1'] = implode('，', $arr);
                    if (count($arr) > 1) {
                        $transfer_step[$key]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'] . '...';
                    } else {
                        $transfer_step[$key]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'];
                    }
                }
//                $signatory_info = $this->api_signatory_model->get_baseinfo_by_signatory_id($val['signatory_id']);
//                $transfer_step[$key]['signatory_name'] = $signatory_info['truename'];
//                $transfer_step[$key]['department_name'] = $signatory_info['department_name'];
                $signatory_info1 = $this->api_signatory_model->get_baseinfo_by_signatory_id($val['complete_signatory_id']);
                $transfer_step[$key]['complete_signatory_name'] = $signatory_info1['truename'];
                $transfer_step[$key]['complete_department_name'] = $signatory_info1['department_name'];
                if (mb_strlen($val['remarks'], 'UTF8') > 10) {
                    $transfer_step['should_flow'][$key]['remarks'] = mb_substr($val['remarks'], 0, 9, 'utf-8') . '...';
                }
            }
        }
        $data['transfer_step'] = $transfer_step;

        $transfer_auth = array(
            'transfer_add' => '12', 'transfer_edit' => '13', 'transfer_delete' => '14', 'transfer_complete' => '15', 'transfer_complete_all' => '16',
            'transfer_stage1_edit' => '21', 'transfer_stage2_edit' => '22', 'transfer_stage3_edit' => '23', 'transfer_stage4_edit' => '24',
            'transfer_stage5_edit' => '25', 'transfer_stage6_edit' => '26', 'transfer_stage7_edit' => '27', 'transfer_stage8_edit' => '28',
            'transfer_stage9_edit' => '29', 'transfer_stage10_edit' => '30', 'transfer_stage11_edit' => '31', 'transfer_stage12_edit' => '32',
            'transfer_stage13_edit' => '33', 'transfer_stage14_edit' => '34', 'transfer_stage15_edit' => '35', 'transfer_stage16_edit' => '36',
            'transfer_stage17_edit' => '39', 'transfer_stage18_edit' => '40', 'transfer_stage19_edit' => '41', 'transfer_stage20_edit' => '42',
            'transfer_stage21_edit' => '44', 'transfer_stage22_edit' => '45', 'transfer_stage23_edit' => '46'
        );

        foreach ($transfer_auth as $key => $val) {
            $data['auth'][$key] = $this->signatory_purview_model->check($val);
        }
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_transfer_manage', $data);
    }

    /**
     * 跳转成交实收实付的权证页面
     */
    public function bargain_replace_manage($id)
    {
        //获取成交详情
        $data['bargain'] = $this->bargain_model->get_by_id($id);
        //获取配置项
        $data['config'] = $this->bargain_config_model->get_config();
        //代收付
        $this->replace_payment_model->set_tbl('replace_payment');
        $data['replace_flow'] = $this->replace_payment_model->get_list_by_cond1(array('c_id' => $id));
        if (is_full_array($data['replace_flow'])) {
            foreach ($data['replace_flow'] as $key => $val) {
                if (mb_strlen($val['remark'], 'UTF8') > 8) {
                    $data['replace_flow'][$key]['remark'] = mb_substr($val['remark'], 0, 7, 'UTF8') . '...';
                }
                if ($val['replace_type'] == 1) {
                    $data['replace_flow'][$key]['collect_money'] = $data['replace_flow'][$key]['money_number'];
                } elseif ($val['replace_type'] == 2) {
                    $data['replace_flow'][$key]['pay_money'] = $data['replace_flow'][$key]['money_number'];
                }
            }
        }
        //收付金额
        $data['replace_collect_money_total'] = $this->replace_payment_model->get_total_money(array('c_id' => $id, 'replace_type' => 1));
        $data['replace_pay_money_total'] = $this->replace_payment_model->get_total_money(array('c_id' => $id, 'replace_type' => 2));


        $replace_add_per = $this->signatory_purview_model->check('17');
        $replace_edit_per = $this->signatory_purview_model->check('18');
        $replace_delete_per = $this->signatory_purview_model->check('19');
        $replace_complete_per = $this->signatory_purview_model->check('20');

        $data['auth'] = array(
            'replace_add' => $replace_add_per, 'replace_edit' => $replace_edit_per,
            'replace_delete' => $replace_delete_per, 'replace_complete' => $replace_complete_per
        );

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_replace_manage', $data);
    }

    /**
     * 跳转成交实收实付的权证页面
     */
    public function bargain_replace_tax_manage($id)
    {
        //获取成交详情
        $data['bargain'] = $this->bargain_model->get_by_id($id);
        //获取配置项
        $data['config'] = $this->bargain_config_model->get_config();
        //实收实付
        $this->replace_payment_model->set_tbl('replace_payment_tax');
        $data['replace_flow'] = $this->replace_payment_model->get_list_by_cond1(array('c_id' => $id));
        if (is_full_array($data['replace_flow'])) {
            foreach ($data['replace_flow'] as $key => $val) {
                if (mb_strlen($val['remark'], 'UTF8') > 8) {
                    $data['replace_flow'][$key]['remark'] = mb_substr($val['remark'], 0, 7, 'UTF8') . '...';
                }
                if ($val['replace_type'] == 1) {
                    $data['replace_flow'][$key]['collect_money'] = $data['replace_flow'][$key]['money_number'];
                } elseif ($val['replace_type'] == 2) {
                    $data['replace_flow'][$key]['pay_money'] = $data['replace_flow'][$key]['money_number'];
                }
            }
        }
        //收付金额
        $data['replace_collect_money_total'] = $this->replace_payment_model->get_total_money(array('c_id' => $id, 'replace_type' => 1));
        $data['replace_pay_money_total'] = $this->replace_payment_model->get_total_money(array('c_id' => $id, 'replace_type' => 2));


        $replace_add_per = $this->signatory_purview_model->check('17');
        $replace_edit_per = $this->signatory_purview_model->check('18');
        $replace_delete_per = $this->signatory_purview_model->check('19');
        $replace_complete_per = $this->signatory_purview_model->check('20');
        $data['auth'] = array(
            'replace_tax_add' => $replace_add_per, 'replace_tax_edit' => $replace_edit_per,
            'replace_tax_delete' => $replace_delete_per, 'replace_tax_complete' => $replace_complete_per
        );

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_replace_tax_manage', $data);
    }

    /**
     * 跳转成交签约费页面
     */
    public function bargain_replace_signing_manage($id)
    {
        //获取成交详情
        $data['bargain'] = $this->bargain_model->get_by_id($id);
        //获取配置项
        $data['config'] = $this->bargain_config_model->get_config();
        //签约费
        $this->replace_payment_model->set_tbl('replace_payment_signing');
        $data['replace_flow'] = $this->replace_payment_model->get_list_by_cond1(array('c_id' => $id));
        if (is_full_array($data['replace_flow'])) {
            foreach ($data['replace_flow'] as $key => $val) {
                if (mb_strlen($val['remark'], 'UTF8') > 8) {
                    $data['replace_flow'][$key]['remark'] = mb_substr($val['remark'], 0, 7, 'UTF8') . '...';
                }
                if ($val['replace_type'] == 1) {
                    $data['replace_flow'][$key]['collect_money'] = $data['replace_flow'][$key]['money_number'];
                } elseif ($val['replace_type'] == 2) {
                    $data['replace_flow'][$key]['pay_money'] = $data['replace_flow'][$key]['money_number'];
                }
            }
        }
        //收付金额
        $data['replace_collect_money_total'] = $this->replace_payment_model->get_total_money(array('c_id' => $id, 'replace_type' => 1));
        $data['replace_pay_money_total'] = $this->replace_payment_model->get_total_money(array('c_id' => $id, 'replace_type' => 2));


        $replace_add_per = $this->signatory_purview_model->check('17');
        $replace_edit_per = $this->signatory_purview_model->check('18');
        $replace_delete_per = $this->signatory_purview_model->check('19');
        $replace_complete_per = $this->signatory_purview_model->check('20');
        $data['auth'] = array(
            'replace_add' => $replace_add_per, 'replace_edit' => $replace_edit_per,
            'replace_delete' => $replace_delete_per, 'replace_complete' => $replace_complete_per
        );

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_replace_signing_manage', $data);
    }

    /**
     * 跳转成交代收付编辑页面
     */
    public function bargain_replace_modify($c_id = 0, $id = 0)
    {
        $data['id'] = $id;
        //获取成交详情
        $data['c_id'] = $c_id;
        $data['bargain'] = $this->bargain_model->get_by_id($c_id);
        //买卖方姓名
        $target_name = array();
        $target_name[1] = $data['bargain'] ['customer'];
        $target_name[2] = $data['bargain'] ['owner'];
        $data['target_name'] = $target_name;

        $data['config'] = $this->bargain_config_model->get_config();

        $tbl = 'replace_payment';
        $this->replace_payment_model->set_tbl($tbl);
        if ($id) {
            $data['flow_list'] = $this->replace_payment_model->get_by_id($id);
            $department_id = $data['flow_list']['flow_department_id'];
        }
        /**
         * 数据范围
         * 1、店长以上权限看公司
         * 2、店长及店长秘书权限查看本门店
         * 3、店长秘书以下没有权限
         */
        //获取访问菜单
        //$range_menu = $this->bargain_model->get_range_menu_by_role_level(
        // $this->user_arr, $department_id);
        //门店数据
        // $data['departments'] = $range_menu['departments'];

        //经纪人数据
        // $data['signatorys'] = $range_menu['signatorys'];
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_replace_modify', $data);
    }

    /**
     * 跳转成交代收付编辑页面
     */
    public function bargain_replace_tax_modify($c_id = 0, $id = 0)
    {
        $data['id'] = $id;
        $data['c_id'] = $c_id;
        $data['config'] = $this->bargain_config_model->get_config();
        $data['bargain'] = $this->bargain_model->get_by_id($c_id);
        //买卖方姓名
        $target_name = array();
        $target_name[1] = $data['bargain'] ['customer'];
        $target_name[2] = $data['bargain'] ['owner'];
        $data['target_name'] = $target_name;
        //买卖方身份证号
        $target_idcard = array();
        $target_idcard[1] = $data['bargain'] ['customer_idcard'];
        $target_idcard[2] = $data['bargain'] ['owner_idcard'];
        $data['target_idcard'] = $target_idcard;

        $tbl = 'replace_payment_tax';
        $this->replace_payment_model->set_tbl($tbl);
        if ($id) {
            $data['flow_list'] = $this->replace_payment_model->get_by_id($id);
            $department_id = $data['flow_list']['flow_department_id'];
        }
        /**
         * 数据范围
         * 1、店长以上权限看公司
         * 2、店长及店长秘书权限查看本门店
         * 3、店长秘书以下没有权限
         */
        //获取访问菜单
        $range_menu = $this->bargain_model->get_range_menu_by_role_level(
            $this->user_arr, $department_id);
        //门店数据
        $data['departments'] = $range_menu['departments'];

        //经纪人数据
        $data['signatorys'] = $range_menu['signatorys'];
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_replace_tax_modify', $data);
    }

    /**
     * 跳转成交签约费编辑页面
     */
    public function bargain_replace_signing_modify($c_id = 0, $id = 0)
    {
        $data['id'] = $id;
        $data['c_id'] = $c_id;
        $data['config'] = $this->bargain_config_model->get_config();
        $data['bargain'] = $this->bargain_model->get_by_id($c_id);
        //买卖方姓名
        $target_name = array();
        $target_name[1] = $data['bargain'] ['customer'];
        $target_name[2] = $data['bargain'] ['owner'];
        $data['target_name'] = $target_name;
        //买卖方身份证号
        $target_idcard = array();
        $target_idcard[1] = $data['bargain'] ['customer_idcard'];
        $target_idcard[2] = $data['bargain'] ['owner_idcard'];
        $data['target_idcard'] = $target_idcard;

        $tbl = 'replace_payment_signing';
        $this->replace_payment_model->set_tbl($tbl);
        if ($id) {
            $data['flow_list'] = $this->replace_payment_model->get_by_id($id);
            $department_id = $data['flow_list']['flow_department_id'];
        }
        /**
         * 数据范围
         * 1、店长以上权限看公司
         * 2、店长及店长秘书权限查看本门店
         * 3、店长秘书以下没有权限
         */
        //获取访问菜单
//        $range_menu = $this->bargain_model->get_range_menu_by_role_level(
//            $this->user_arr, $department_id);
        //门店数据
//        $data['departments'] = $range_menu['departments'];

        //经纪人数据
//        $data['signatorys'] = $range_menu['signatorys'];
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_replace_signing_modify', $data);
    }

    /**
     * 跳转税费详情页面
     */
    public function bargain_replace_tax_detail($id = 0)
    {
        $data['id'] = $id;
        $data['config'] = $this->bargain_config_model->get_config();

        $tbl = 'replace_payment_tax';
        $this->replace_payment_model->set_tbl($tbl);
        $data['detail'] = $this->replace_payment_model->get_by_id($id);
        $data['bargain'] = $this->bargain_model->get_by_id($data['detail']['c_id']);

        $signatory_info = $this->api_signatory_model->get_baseinfo_by_signatory_id($data['detail']['enter_signatory_id']);
        $data['detail']['enter_department_name'] = $signatory_info['department_name'];
        $data['detail']['enter_signatory_name'] = $signatory_info['truename'];
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_replace_tax_detail', $data);
    }

    public function bargain_replace_detail($id = 0)
    {
        $data['id'] = $id;
        $data['config'] = $this->bargain_config_model->get_config();

        $tbl = 'replace_payment';
        $this->replace_payment_model->set_tbl($tbl);
        $data['detail'] = $this->replace_payment_model->get_by_id($id);
        $data['bargain'] = $this->bargain_model->get_by_id($data['detail']['c_id']);

        $signatory_info = $this->api_signatory_model->get_baseinfo_by_signatory_id($data['detail']['enter_signatory_id']);
        $data['detail']['enter_department_name'] = $signatory_info['department_name'];
        $data['detail']['enter_signatory_name'] = $signatory_info['truename'];
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_replace_detail', $data);
    }

    /**
     * 跳转成交应收应付编辑页面
     */
    public function bargain_should_modify($c_id = 0, $id = 0)
    {
        $data['id'] = $id;
        $data['c_id'] = $c_id;
        $data['config'] = $this->bargain_config_model->get_config();

        $tbl = 'bargain_should_flow';
        $this->replace_payment_model->set_tbl($tbl);
        if ($id) {
            $data['flow_list'] = $this->replace_payment_model->get_by_id($id);
            $department_id = $data['flow_list']['flow_department_id'];
        } else {
            $department_id = $this->user_arr['department_id'];
        }
        /**
         * 数据范围
         * 1、店长以上权限看公司
         * 2、店长及店长秘书权限查看本门店
         * 3、店长秘书以下没有权限
         */
        //获取访问菜单
        $range_menu = $this->bargain_model->get_range_menu_by_role_level(
            $this->user_arr, $department_id);
        //门店数据
        $data['departments'] = $range_menu['departments'];
        //经纪人数据
        $data['signatorys'] = $range_menu['signatorys'];
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_should_modify', $data);
    }

    /**
     * 跳转成交实收实详情页面
     */
    public function bargain_should_detail($id = 0)
    {
        $data['id'] = $id;
        $data['config'] = $this->bargain_config_model->get_config();

        $tbl = 'bargain_should_flow';
        $this->replace_payment_model->set_tbl($tbl);
        $data['detail'] = $this->replace_payment_model->get_by_id($id);
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_should_detail', $data);
    }

    /**
     * 跳转成交业绩分成编辑页面
     */
    public function bargain_divide_modify($c_id = 0, $id = 0)
    {
        $data['id'] = $id;
        $data['c_id'] = $c_id;
        $total = $this->bargain_divide_model->get_total($c_id);
        $data['config'] = $this->bargain_config_model->get_config();
        if ($id) {
            $data['divide_list'] = $this->bargain_divide_model->get_by_id($id);
            $total['percent_total'] = $total['percent_total'] - $data['divide_list']['percent'];
            $department_id = $data['divide_list']['department_id'];
            $department_id1 = $data['divide_list']['achieve_department_id_a'];
        } else {
            $total['percent_total'] = $total['percent_total'] ? $total['percent_total'] : '0';
            $department_id = $this->user_arr['department_id'];
            $department_id1 = $this->user_arr['department_id'];
        }
        $data['divide_total'] = $total;
        //获取所有一级门店（区域）
        //$data['department_first'] = $this->department_model->get_department_1_by_company_id($this->user_arr['company_id']);

        //获取所有一级门店（区域）
        //$data['signatory_first'] = $this->signatory_info_model->get_by_department_id($data['divide_list']['department_id']);

        $data['bargain'] = $this->bargain_model->get_by_id($c_id);
        /**
         * 数据范围
         * 1、店长以上权限看公司
         * 2、店长及店长秘书权限查看本门店
         * 3、店长秘书以下没有权限
         */
        //获取访问菜单
        $range_menu = $this->bargain_model->get_range_menu_by_role_level(
            $this->user_arr, $department_id);
        $range_menu1 = $this->bargain_model->get_range_menu_by_role_level(
            $this->user_arr, $department_id1);
        //归属门店数据
        $data['departments'] = $range_menu['departments'];
        //归属经纪人数据
        $data['signatorys'] = $range_menu['signatorys'];
        //门店业绩门店数据
        $data['departments1'] = $range_menu1['departments'];
        //门店业绩经纪人数据
        $data['signatorys1'] = $range_menu1['signatorys'];
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls_guli/js/v1.0/jquery.validate.min.js,'
            . 'mls_guli/js/v1.0/verification_bargain.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_divide_modify', $data);
    }

    /**
     * 跳转成交业绩分成编辑页面
     */
    public function bargain_divide_detail($id)
    {
        $data['config'] = $this->bargain_config_model->get_config();
        $data['divide_list'] = $this->bargain_divide_model->get_by_id($id);
        $data['bargain'] = $this->bargain_model->get_by_id($data['divide_list']['c_id']);
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_divide_detail', $data);
    }

    /**
     * 跳转成交应收应付的权证页面
     */
    public function bargain_should_manage($id)
    {
        //获取配置项
        $data['config'] = $this->bargain_config_model->get_config();
        $data['bargain'] = $this->bargain_model->get_by_id($id);
        //应收应付
        $this->replace_payment_model->set_tbl('bargain_should_flow');
        $data['should_flow'] = $this->replace_payment_model->get_list_by_cond1(array('c_id' => $id));
        $get_total1 = $this->replace_payment_model->get_total(array('c_id' => $id));
        $data['should_collect_money_total'] = $get_total1['collect_money_total'];
        $data['should_pay_money_total'] = $get_total1['pay_money_total'];

        if (is_full_array($data['should_flow'])) {
            foreach ($data['should_flow'] as $key => $val) {
                if (mb_strlen($val['remark'], 'UTF8') > 8) {
                    $data['should_flow'][$key]['remark'] = mb_substr($val['remark'], 0, 7, 'UTF8') . '...';
                }
            }
        }

        $should_add_per = $this->signatory_purview_model->check('55');
        $should_edit_per = $this->signatory_purview_model->check('56');
        $should_delete_per = $this->signatory_purview_model->check('57');

        $data['auth'] = array(
            'should_add' => $should_add_per, 'should_edit' => $should_edit_per,
            'should_delete' => $should_delete_per,
        );
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_should_manage', $data);
    }

    /**
     * 跳转成交跟进的权证页面
     */
    public function bargain_follow_manage($id = 0)
    {
        $post_param = $this->input->post(null, true);
        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
        $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
        $this->_init_pagination($page, $pagesize);
        $where = "c_id = " . $id;
        //符合条件的总行数
        $this->_total_count = $this->bargain_log_model->count_by($where);

        //计算总页数
        $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
        //跟进记录
        $data['follow'] = $this->bargain_log_model->get_all_by_id($id, $this->_offset, $this->_limit);
        if (is_full_array($data['follow'])) {
            foreach ($data['follow'] as $key => $val) {
                $data['follow'][$key]['content1'] = $val['content'];
                if (mb_strlen($val['content'], 'UTF8') > 30) {
                    $data['follow'][$key]['content'] = mb_substr($val['content'], 0, 29, 'UTF8') . '...';
                }
            }
        }
        //分页处理
        $params = array(
            'total_rows' => $this->_total_count, //总行数
            'method' => 'post', //URL提交方式 get/html/post
            'now_page' => $this->_current_page, //当前页数
            'list_rows' => $this->_limit, //每页显示个数
        );
        //加载分页类
        $this->load->library('page_list', $params);
        //调用分页函数（不同的样式不同的函数参数）
        $data['page_list'] = $this->page_list->show('jump');

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_follow_manage', $data);
    }

    /**
     * 跳转成交应收应付的权证页面
     */
    public function bargain_divide_manage($id)
    {
        $data['bargain'] = $this->bargain_model->get_by_id($id);
        //获取配置项
        $data['config'] = $this->bargain_config_model->get_config();
        //业绩分成
        $data['divide_list'] = $this->bargain_divide_model->get_all_by(array('c_id' => $id));
        $total = $this->bargain_divide_model->get_total($id);
        $data['divide_total'] = $total;

        //根据成交id统计分成条数
        $data['divide_num'] = $this->bargain_divide_model->count_by(array('c_id' => $id));
        $divide_add_per = $this->signatory_purview_model->check('51');
        $divide_edit_per = $this->signatory_purview_model->check('52');
        $divide_delete_per = $this->signatory_purview_model->check('53');
        $divide_complete_per = $this->signatory_purview_model->check('54');

        $data['auth'] = array(
            'divide_add' => $divide_add_per, 'divide_edit' => $divide_edit_per,
            'divide_delete' => $divide_delete_per, 'divide_complete' => $divide_complete_per);
        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_divide_manage', $data);
    }

    /**
     * 权证流程
     */
    public function transfer_list()
    {
        //模板使用数据
        $data = array();

        //树型菜单
        $data['user_tree_menu'] = $this->user_tree_menu;

        //页面标题
        $data['page_title'] = '权证流程';

        //post参数
        $post_param = $this->input->post(NULL, TRUE);
        $data['post_param'] = $post_param;
        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
        $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
        $this->_init_pagination2($page, $pagesize);
        $role_level = $this->user_arr['role_level'];
        if ($role_level < 6) //公司
        {
            //所属公司
            $post_param['enter_company_id'] = $this->user_arr['company_id'];
        } else if ($role_level < 8) //门店
        {
            //所属公司
            $post_param['enter_company_id'] = $this->user_arr['company_id'];
            //所属门店
            $post_param['enter_department_id'] = $this->user_arr['department_id'];
        } else {
            //所属公司
            $post_param['enter_company_id'] = $this->user_arr['company_id'];
            //所属门店
            $post_param['enter_department_id'] = $this->user_arr['department_id'];
            //所属经纪人
            $post_param['enter_signatory_id'] = $this->user_arr['signatory_id'];
        }
        /**
         * 数据范围
         * 1、店长以上权限看公司
         * 2、店长及店长秘书权限查看本门店
         * 3、店长秘书以下没有权限
         */
        //获取访问菜单
        $range_menu = $this->bargain_model->get_range_menu_by_role_level(
            $this->user_arr, $post_param['department_id_a']);
        //门店数据
        $data['departments'] = $range_menu['departments'];
        //经纪人数据
        $data['signatorys'] = $range_menu['signatorys'];

        //表单提交参数组成的查询条件
        $cond_where = $this->_get_cond_str($post_param);

        //查询条件是否创建权证步骤 0 否 1 是
        $cond_where .= " AND `is_template` = 1";

        //清除条件头尾多余空格
        $cond_where = trim($cond_where);
        //符合条件的总行数
        $this->_total_count = $this->bargain_model->count_by($cond_where);

        //计算总页数
        $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit2) : 0;
        //权证配置项
        $data['stage_conf'] = $this->bargain_transfer_model->get_stage_conf();
        //权证步骤名配置
        $data['stage'] = $this->bargain_transfer_model->get_all_stage();
        //获取列表内容
        $list = $this->bargain_model->get_all_by($cond_where, $this->_offset, $this->_limit2);
        if (is_full_array($list)) {
            foreach ($list as $key => $val) {
                //权证流程
                $transfer_step = $this->bargain_transfer_model->get_all_by_cid($val['id']);
                if (is_full_array($transfer_step)) {
                    foreach ($transfer_step as $k => $v) {
                        $arr = array();
                        $stage_name = explode(',', $v['stage_id']);
                        foreach ($stage_name as $k1 => $v1) {
                            $arr[] = $data['stage'][$v1]['stage_name'];
                            $transfer_step[$k]['stage_name1'] = implode('，', $arr);
                            if (count($arr) > 1) {
                                $transfer_step[$k]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'] . '...';
                            } else {
                                $transfer_step[$k]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'];
                            }
                        }
                        $signatory_info = $this->api_signatory_model->get_baseinfo_by_signatory_id($v['signatory_id']);
                        $transfer_step[$k]['signatory_name'] = $signatory_info['truename'];
                        $transfer_step[$k]['department_name'] = $signatory_info['department_name'];
                        $signatory_info1 = $this->api_signatory_model->get_baseinfo_by_signatory_id($v['complete_signatory_id']);
                        $transfer_step[$k]['complete_signatory_name'] = $signatory_info1['truename'];
                        $transfer_step[$k]['complete_department_name'] = $signatory_info1['department_name'];
                    }
                }
                $list[$key]['transfer_list'] = $transfer_step;
                //房源地址截取
                if (mb_strlen($val['house_addr'], 'UTF8') > 20) {
                    $list[$key]['house_addr'] = mb_substr($val['house_addr'], 0, 20, 'utf-8') . '...';
                }
            }
        }
        $data['list'] = $list;

        $default_temp = $this->transfer_model->get_default_temps();
        //根据模板的ID读取步骤
        $steps = $this->transfer_model->get_step_by_template_id($default_temp['id']);
        $new_step = array();
        if (is_full_array($steps)) {
            foreach ($steps as $k => $v) {
                $arr = array();
                $stage_name = explode(',', $v['stage_id']);
                foreach ($stage_name as $k1 => $v1) {
                    $arr[] = $data['stage'][$v1]['stage_name'];
                    $new_step[$k]['stage_name1'] = implode('，', $arr);
                    if (count($arr) > 1) {
                        $new_step[$k]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'] . '...';
                    } else {
                        $new_step[$k]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'];
                    }
                }
            }
        }
        $default_temp['steps'] = $new_step;
        $data['default_temp'] = $default_temp;

        //分页处理
        $params = array(
            'total_rows' => $this->_total_count, //总行数
            'method' => 'post', //URL提交方式 get/html/post
            'now_page' => $this->_current_page, //当前页数
            'list_rows' => $this->_limit2, //每页显示个数
        );
        //加载分页类
        $this->load->library('page_list', $params);
        //调用分页函数（不同的样式不同的函数参数）
        $data['page_list'] = $this->page_list->show('jump');

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,'
            . 'mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,'
            . 'mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
        //底部JS
        $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_transfer_list', $data);
    }

    //判断公司是否创建模板
    public function judge_company_template()
    {
        $total = $this->transfer_model->get_count('transfer_template', array('company_id' => $this->user_arr['company_id']));
        if ($total > 0) {
            $return_data['result'] = 'ok';
        } else {
            $return_data['result'] = 'no';
        }
        echo json_encode($return_data);
    }

    /**
     * 选择模板界面
     */
    public function get_all_template($id = 0)
    {

        $data['page_title'] = '选择权证流程模板';
        $data['c_id'] = $id;
        $data['stage'] = $this->transfer_model->get_all_stage();
        $post_param = $this->input->post(null, true);
        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
        $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
        $this->_init_pagination1($page, $pagesize);

        $where = "((type = 0 and company_id = 0) or (type = 1 and company_id = {$this->user_arr['company_id']})) and is_addstep = 1";

        //符合条件的总行数
        $this->_total_count = $this->transfer_model->get_count('transfer_template', $where);
        //计算总页数
        $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit1) : 0;
        //获得公司下所有模板
        $sys_temps = $this->transfer_model->get_all_temps($where, $this->_offset, $this->_limit1, 'id', 'ASC');

        foreach ($sys_temps as $key => $val) {
            //根据模板的ID读取步骤
            $steps = $this->transfer_model->get_step_by_template_id($val['id']);
            $new_step = array();
            if (is_full_array($steps)) {
                foreach ($steps as $k => $v) {
                    $arr = array();
                    $stage_name = explode(',', $v['stage_id']);
                    foreach ($stage_name as $k1 => $v1) {
                        $arr[] = $data['stage'][$v1]['stage_name'];
                        $new_step[$k]['stage_name1'] = implode('，', $arr);
                        if (count($arr) > 1) {
                            $new_step[$k]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'] . '...';
                        } else {
                            $new_step[$k]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'];
                        }
                    }
                }
            }
            $sys_temps[$key]['steps'] = $new_step;
        }
        $data['template_temps'] = $sys_temps;

        //分页处理
        $params = array(
            'total_rows' => $this->_total_count, //总行数
            'method' => 'post', //URL提交方式 get/html/post
            'now_page' => $this->_current_page, //当前页数
            'list_rows' => $this->_limit1, //每页显示个数
        );
        //加载分页类
        $this->load->library('page_list', $params);
        //调用分页函数（不同的样式不同的函数参数）
        $data['page_list'] = $this->page_list->show('jump');

        //需要加载的css
        $data['css'] = load_css('mls_guli/css/v1.0/base.css,mls_guli/third/iconfont/iconfont.css,'
            . 'mls_guli/css/v1.0/house_manage.css,mls_guli/css/v1.0/bargain_manage.css');
        //需要加载的JS
        $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js');
        //底部JS
        $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
            . 'mls_guli/js/v1.0/backspace.js');
        $this->view('bargain/bargain_choose_template', $data);
    }

    /**
     * 选择模板后的操作
     */
    public function sel_choose()
    {
        $template_id = $this->input->post('template_id');
        $bargain_id = $this->input->post('bargain_id');
        $steps = $this->transfer_model->get_step_by_template_id($template_id);//获取模板步骤信息
        //权证配置项
        $stage_conf = $this->bargain_transfer_model->get_stage_conf();
        //权证步骤名配置
        $stage = $this->bargain_transfer_model->get_all_stage();
        $num = 0;
        if (is_full_array($steps)) {
            foreach ($steps as $key => $val) {
                $add_data['bargain_id'] = $bargain_id;
                $add_data['stage_id'] = $val['stage_id'];
                $add_data['step_id'] = $key + 1;
                $add_data['signatory_id'] = $this->user_arr['signatory_id'];
                $add_data['department_id'] = $this->user_arr['department_id'];
                $add_data['company_id'] = $this->user_arr['company_id'];
                $add_data['createtime'] = time();
                //依次将新步骤添加到步骤表
                $rs = $this->bargain_transfer_model->insert_data($add_data);
                if ($rs) {
                    $stage_id = explode(',', $add_data['stage_id']);
                    foreach ($stage_id as $k => $v) {
                        $arr[] = $stage[$v]['stage_name'];
                    }
                    $stage_name = implode('，', $arr);
                    $follow_data = array(
                        'c_id' => $bargain_id,
                        'type_name' => "权证流程步骤",
                        'content' => "添加权证流程步骤，{$stage_conf[$key+1]['text']}：{$stage_name}。",
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'updatetime' => time()
                    );
                    $this->bargain_log_model->add_info($follow_data);
                    //操作日志
                    $info = $this->bargain_model->get_by_id($bargain_id);
                    $add_log_param = array(
                        'company_id' => $this->user_arr['company_id'],
                        'department_id' => $this->user_arr['department_id'],
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'type' => 35,
                        'text' => '添加成交编号为' . $info['result'] . '的交易成交的权限流程。',
                        'from_system' => 1,
                        'from_ip' => get_ip(),
                        'mac_ip' => '127.0.0.1',
                        'from_host_name' => '127.0.0.1',
                        'hardware_num' => '测试硬件序列号',
                        'time' => time()
                    );
                    $this->signatory_operate_log_model->add_operate_log($add_log_param);
                }
                $num++;
            }
        }
        $total = $this->transfer_model->get_count('transfer_template_step', array('template_id' => $template_id));
        if ($num == $total) {
            $this->bargain_model->update_by_id(array('template_id' => $template_id, 'is_template' => 1), $bargain_id);
            $json_data['status'] = 1;
        } else {
            $json_data['status'] = 0;
        }
        echo json_encode($json_data);
    }

    //新建模版，
    public function save_template()
    {
        $template_name = $this->input->post('template_name');
        if (mb_strlen($template_name, 'UTF8') > 16) {
            $return_data['result'] = 'no';
            $return_data['msg'] = '模板名称不能超过16个字！';
            echo json_encode($return_data);
            exit();
        }

        if ($template_name == "") {
            $return_data['result'] = 'no';
            $return_data['msg'] = '模板名称不能为空！';
            echo json_encode($return_data);
            exit();
        }
        //先判断该模板名在同公司是否已被建立
        $where = "template_name = '{$template_name}' and company_id = {$this->user_arr['company_id']}";
        $result = $this->transfer_model->get_temp_by_cond($where);
        if ($result) {
            $return_data['result'] = 'no';
            $return_data['msg'] = '公司下已有同名的权证模板！';
        } else {
            $total = $this->transfer_model->get_count('transfer_template', array('company_id' => $this->user_arr['company_id']));
            if ($total >= 5) {
                $return_data['result'] = 'no';
                $return_data['msg'] = '公司下最多建立5个模板！';
            } else {
                $add_data = array(
                    'template_name' => $template_name,
                    'company_id' => $this->user_arr['company_id'],
                    'department_id' => $this->user_arr['department_id'],
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'type' => 1
                );
                $insert_id = $this->bargain_transfer_model->add_new_template($add_data);
                if ($insert_id) {
                    //记录跟进信息
                    $follow_data = array(
                        'c_id' => 0,
                        'type_name' => '权证流程',
                        'content' => '添加权证流程模板，"' . $template_name . '"。',
                        'updatetime' => time(),
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename']
                    );
                    $this->bargain_log_model->add_info($follow_data);
                    $return_data['result'] = 'ok';
                    $return_data['msg'] = '权证流程模板添加成功！';
                    $return_data['data'] = $insert_id;
                    //操作日志
                    $add_log_param = array(
                        'company_id' => $this->user_arr['company_id'],
                        'department_id' => $this->user_arr['department_id'],
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'type' => 35,
                        'text' => '添加名称为"' . $template_name . '"的权限流程模板。',
                        'from_system' => 1,
                        'from_ip' => get_ip(),
                        'mac_ip' => '127.0.0.1',
                        'from_host_name' => '127.0.0.1',
                        'hardware_num' => '测试硬件序列号',
                        'time' => time()
                    );
                    $this->signatory_operate_log_model->add_operate_log($add_log_param);
                } else {
                    $return_data['result'] = 'no';
                    $return_data['msg'] = '权证流程模板添加失败！';
                }
            }
        }
        echo json_encode($return_data);
    }

    //修改公司模板名称
    public function save_template_name()
    {
        $template_name = $this->input->post('template_name');
        $template_id = $this->input->post('template_id');
        $len = mb_strlen($template_name, 'UTF8');
        if ($len > 16) {
            $return_data['result'] = 'no';
            $return_data['msg'] = '模板名称不能超过16个字！';
            echo json_encode($return_data);
            exit();
        }

        if ($template_name == "") {
            $return_data['result'] = 'no';
            $return_data['msg'] = '模板名称不能为空！';
            echo json_encode($return_data);
            exit();
        }
        //检查其他模板名是否同名
        $where = "template_name = '{$template_name}' and company_id = {$this->user_arr['company_id']} and id !={$template_id}";
        $result = $this->transfer_model->get_temp_by_cond($where);
        if ($result) {
            $return_data['result'] = 'no';
            $return_data['msg'] = '公司下已有同名的权证模板！';
        } else {
            $paramArray = array(
                'template_name' => $template_name
            );
            $this->transfer_model->modify_data('transfer_template', $paramArray, array('id' => $template_id)); //数据入库
            $return_data['msg'] = '修改权证流程模板名称成功！';
            $return_data['result'] = 'ok';
        }
        echo json_encode($return_data);
    }

    /** 流程模板修改步骤 */
    public function save_template_step()
    {
        $template_id = $this->input->post('template_id');
        $stage_id = $this->input->post('stage_id');
        $stage_arr = $this->input->post('stage');
        $paramArray = array(
            'stage_id' => implode(',', $stage_arr)
        );
        if ($stage_id) {
            $old_data = $this->transfer_model->get_step_by_id($stage_id);
            $update_result = $this->transfer_model->modify_data('transfer_template_step', $paramArray, array('id' => $stage_id)); //数据入库
            if (!empty($update_result) && is_int($update_result)) {
                $return_data['msg'] = '修改权证流程模板步骤成功！';
                $return_data['result'] = 'ok';
            } else {
                $return_data['msg'] = '修改权证流程模板步骤失败！';
                $return_data['result'] = 'no';
            }
        } else {
            $total_step_old = $this->transfer_model->get_count('transfer_template_step', array('template_id' => $template_id));
            $paramArray['company_id'] = $this->user_arr['company_id'];
            $paramArray['department_id'] = $this->user_arr['department_id'];
            $paramArray['signatory_id'] = $this->user_arr['signatory_id'];
            $paramArray['template_id'] = $template_id;
            $paramArray['step_id'] = $total_step_old + 1;
            if ($total_step_old < 20) {
                //如果之前没有添加过步骤，则改变成交创建模版的状态
                if ($total_step_old == 0) {
                    $this->transfer_model->modify_data('transfer_template', array('is_addstep' => 1), array('id' => $template_id));
                }
                $add_result = $this->transfer_model->insert_data('transfer_template_step', $paramArray); //数据入库
                if (!empty($add_result) && is_int($add_result)) {
                    $return_data['msg'] = '添加权证流程模板步骤成功！';
                    $return_data['result'] = 'ok';
                } else {
                    $return_data['msg'] = '添加权证流程模板步骤失败！';
                    $return_data['result'] = 'no';
                }
            } else {
                $return_data['msg'] = '权证流程模板步骤最多可添加20步！';
                $return_data['result'] = 'no';
            }
        }
        echo json_encode($return_data);
    }

    //新建模板后成交确认使用该模板
    function save_transfer_step()
    {
        $template_id = $this->input->post('template_id');
        $c_id = $this->input->post('bargain_id');
        $template_name = $this->input->post('template_name');
        //权证步骤名配置
        $stage = $this->bargain_transfer_model->get_all_stage();
        //权证配置项
        $stage_conf = $this->bargain_transfer_model->get_stage_conf();
        //检查其他模板名是否同名
        $where = "template_name = '{$template_name}' and company_id = {$this->user_arr['company_id']} and id !={$template_id}";
        $result = $this->transfer_model->get_temp_by_cond($where);
        if ($result) {
            $return_data['result'] = 'no';
            $return_data['msg'] = '公司下已有同名的权证模板！';
        } else {
            $this->transfer_model->modify_data('transfer_template', array('template_name' => $template_name), array('id' => $template_id));
            $steps = $this->transfer_model->get_step_by_template_id($template_id);
            if (is_full_array($steps)) {
                foreach ($steps as $k => $v) {
                    $add_data['bargain_id'] = $c_id;
                    $add_data['stage_id'] = $v['stage_id'];
                    $add_data['step_id'] = $k + 1;
                    $add_data['signatory_id'] = $this->user_arr['signatory_id'];
                    $add_data['department_id'] = $this->user_arr['department_id'];
                    $add_data['company_id'] = $this->user_arr['company_id'];
                    $add_data['createtime'] = time();
                    //依次将新步骤添加到步骤表
                    $rs = $this->bargain_transfer_model->add_info($add_data);
                    if ($rs) {
                        $stage_id = explode(',', $add_data['stage_id']);
                        $arr = array();
                        foreach ($stage_id as $k => $v) {
                            $arr[] = $stage[$v]['stage_name'];
                        }
                        $stage_name = implode('，', $arr);
                        $follow_data = array(
                            'c_id' => $c_id,
                            'type_name' => "权证流程",
                            'content' => "添加权证流程步骤，{$stage_conf[$k+1]['text']}：{$stage_name}。",
                            'signatory_id' => $this->user_arr['signatory_id'],
                            'signatory_name' => $this->user_arr['truename'],
                            'updatetime' => time()
                        );
                        $this->bargain_log_model->add_info($follow_data);
                        //操作日志
                        $info = $this->bargain_model->get_by_id($c_id);
                        $add_log_param = array(
                            'company_id' => $this->user_arr['company_id'],
                            'department_id' => $this->user_arr['department_id'],
                            'signatory_id' => $this->user_arr['signatory_id'],
                            'signatory_name' => $this->user_arr['truename'],
                            'type' => 35,
                            'text' => '新增编号为' . $info['number'] . '的交易成交的权限流程。',
                            'from_system' => 1,
                            'from_ip' => get_ip(),
                            'mac_ip' => '127.0.0.1',
                            'from_host_name' => '127.0.0.1',
                            'hardware_num' => '测试硬件序列号',
                            'time' => time()
                        );
                        $this->signatory_operate_log_model->add_operate_log($add_log_param);
                    }
                }
            }
            $return_data['result'] = 'ok';
            $return_data['msg'] = '保存成功';
        }
        echo json_encode($return_data);
    }


    public function transfer_detail()
    {
        $id = $this->input->post('id');
        //权证配置项
        $stage_conf = $this->bargain_transfer_model->get_stage_conf();
        //权证步骤名配置
        $stage = $this->bargain_transfer_model->get_all_stage();
        $data['transfer_list'] = $this->bargain_transfer_model->get_by_id($id);
        $data['transfer_list']['stage_id'] = explode(',', $data['transfer_list']['stage_id']);
        foreach ($data['transfer_list']['stage_id'] as $key => $val) {
            $stage_name[] = $stage[$val]['stage_name'];
        }
        $data['transfer_list']['stage_name'] = implode(',', $stage_name);
        $data['transfer_list']['step_name'] = $stage_conf[$data['transfer_list']['step_id']]['text'];
        if ($data['transfer_list']) {
            $data['result'] = 1;
        } else {
            $data['result'] = 0;
        }
        echo json_encode($data);
    }

    /** 流程模板添加步骤 */
    public function add_temp_step()
    {
        $c_id = $this->input->post('c_id');
        $stage_id = $this->input->post('stage_id');
        $post_param = $this->input->post(NULL, true);
        //权证步骤名配置
        $stage = $this->bargain_transfer_model->get_all_stage();
        //权证配置项
        $stage_conf = $this->bargain_transfer_model->get_stage_conf();
        $paramArray = array(
            'bargain_id' => intval($c_id),
            'stage_id' => implode(',', $post_param['stage']),
            'remark' => trim($post_param['transfer_remark']),
            'isComplete' => 0
        );
        $is_remind = $this->input->post('is_remind');
        if ($is_remind == 1) {
            $paramArray['remind_department_id'] = intval($this->input->post('remind_department_id'));
            $paramArray['remind_signatory_id'] = intval($this->input->post('remind_signatory_id'));
            $paramArray['remind_remark'] = trim($this->input->post('remind_remark'));
            $paramArray['remind_time'] = $this->input->post('remind_time');
            $paramArray['is_remind'] = $this->input->post('is_remind');
        }
        if ($stage_id) {
            $old_data = $this->bargain_transfer_model->get_by_id($stage_id);
            $update_result = $this->bargain_transfer_model->modify_data($stage_id, $paramArray); //数据入库
            if (!empty($update_result) && is_int($update_result)) {
                //发送消息
                $this->load->model('message_base_model');
                if ($is_remind == 1) {
                    $signatory_info = $this->api_signatory_model->get_baseinfo_by_signatory_id($paramArray['remind_signatory_id']);
                    $bargain = $this->bargain_model->get_by_id($c_id);
                    $params = array('number' => $bargain['number'],
                        'remind_remark' => $paramArray['remind_remark'],
                        'remind_time' => $paramArray['remind_time']);
                    $this->message_base_model->add_message('8-50', $paramArray['remind_signatory_id'], $signatory_info['truename'], '/bargain/bargain_detail/' . $c_id, $params);
                }
                $str = $this->modify_step_match($paramArray, $old_data);
                $add_data = array(
                    'c_id' => $c_id,
                    'type_name' => "权证流程",
                    'content' => "修改权证流程步骤，{$stage_conf[$old_data['step_id']]['text']}：" . $str,
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'updatetime' => time()
                );
                $return_data['msg'] = '修改权证流程步骤成功！';
                $this->bargain_log_model->add_info($add_data);
                $return_data['result'] = 'ok';

                //操作日志
                $info = $this->bargain_model->get_by_id($c_id);
                $add_log_param = array(
                    'company_id' => $this->user_arr['company_id'],
                    'department_id' => $this->user_arr['department_id'],
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'type' => 35,
                    'text' => '修改编号为' . $info['number'] . '的交易成交的权限流程。' . $str,
                    'from_system' => 1,
                    'from_ip' => get_ip(),
                    'mac_ip' => '127.0.0.1',
                    'from_host_name' => '127.0.0.1',
                    'hardware_num' => '测试硬件序列号',
                    'time' => time()
                );
                $this->signatory_operate_log_model->add_operate_log($add_log_param);
            } else {
                $return_data['msg'] = '修改权证流程步骤失败！';
                $return_data['result'] = 'no';
            }
        } else {
            $total_step_old = $this->bargain_transfer_model->count_by(array('bargain_id' => $c_id));
            $paramArray['company_id'] = $this->user_arr['company_id'];
            $paramArray['department_id'] = $this->user_arr['department_id'];
            $paramArray['signatory_id'] = $this->user_arr['signatory_id'];
            $paramArray['createtime'] = time();
            $paramArray['step_id'] = $total_step_old + 1;
            if ($total_step_old < 10) {
                //如果之前没有添加过步骤，则改变成交创建模版的状态
                if ($total_step_old == 0) {
                    $this->bargain_model->update_by_id(array('is_template' => 1), $c_id);
                }
                $add_result = $this->bargain_transfer_model->insert_data($paramArray); //数据入库
                $total_step = $this->bargain_transfer_model->count_by(array('bargain_id' => $c_id));
                foreach ($post_param['stage'] as $k => $v) {
                    $arr[] = $stage[$v]['stage_name'];
                }
                $stage_name = implode('，', $arr);
                if (!empty($add_result) && is_int($add_result)) {
                    //发送消息
                    $this->load->model('message_base_model');
                    if ($is_remind == 1) {
                        $signatory_info = $this->api_signatory_model->get_baseinfo_by_signatory_id($paramArray['remind_signatory_id']);
                        $bargain = $this->bargain_model->get_by_id($c_id);
                        $params = array('number' => $bargain['number'],
                            'remind_remark' => $paramArray['remind_remark'],
                            'remind_time' => $paramArray['remind_time']);
                        $this->message_base_model->add_message('8-50', $paramArray['remind_signatory_id'], $signatory_info['truename'], '/bargain/bargain_detail/' . $c_id, $params);
                    }
                    $add_data = array(
                        'c_id' => $c_id,
                        'type_name' => "权证流程",
                        'content' => "添加权证流程步骤，{$stage_conf[$total_step]['text']}：{$stage_name}。",
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'updatetime' => time()
                    );
                    $return_data['msg'] = '添加权证流程步骤成功！';
                    $this->bargain_log_model->add_info($add_data);
                    $return_data['result'] = 'ok';
                    //操作日志
                    $info = $this->bargain_model->get_by_id($c_id);
                    $add_log_param = array(
                        'company_id' => $this->user_arr['company_id'],
                        'department_id' => $this->user_arr['department_id'],
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'type' => 35,
                        'text' => '新增编号为' . $info['number'] . '的交易成交的权限流程。',
                        'from_system' => 1,
                        'from_ip' => get_ip(),
                        'mac_ip' => '127.0.0.1',
                        'from_host_name' => '127.0.0.1',
                        'hardware_num' => '测试硬件序列号',
                        'time' => time()
                    );
                    $this->signatory_operate_log_model->add_operate_log($add_log_param);
                } else {
                    $return_data['msg'] = '添加权证流程步骤失败！';
                    $return_data['result'] = 'no';
                }
            } else {
                $return_data['msg'] = '权证流程步骤最多可添加10步！';
                $return_data['result'] = 'no';
            }
        }
        echo json_encode($return_data);
    }

    /** 流程模板修改步骤 */
    public function modify_temp_step()
    {
        $c_id = $this->input->post('c_id');
        $stage_id = $this->input->post('stage_id');
        $post_param = $this->input->post(NULL, true);
        //权证步骤名配置
        $stage = $this->bargain_transfer_model->get_all_stage();
        //权证配置项
        $stage_conf = $this->bargain_transfer_model->get_stage_conf();
        $paramArray = array(
            'bargain_id' => intval($c_id),
//      'stage_id' => implode(',', $post_param['stage']),
            'remark' => trim($post_param['transfer_remark']),
            'isComplete' => 0,
            'stage_type' => trim($post_param['stage_type']),
            'number_days' => trim($post_param['number_days']),
            'start_time' => strtotime($post_param['start_time']),
            'complete_time' => strtotime($post_param['complete_time']),
            'complete_signatory_id' => trim($post_param['complete_signatory_id']),
        );
        $is_remind = $this->input->post('is_remind');
        if ($is_remind == 1) {
            $paramArray['remind_department_id'] = intval($this->input->post('remind_department_id'));
            $paramArray['remind_signatory_id'] = intval($this->input->post('remind_signatory_id'));
            $paramArray['remind_remark'] = trim($this->input->post('remind_remark'));
            $paramArray['remind_time'] = $this->input->post('remind_time');
            $paramArray['is_remind'] = $this->input->post('is_remind');
        }
        if ($stage_id) {
            $old_data = $this->bargain_transfer_model->get_by_id($stage_id);
            $update_result = $this->bargain_transfer_model->modify_data($stage_id, $paramArray); //数据入库
            if (!empty($update_result) && is_int($update_result)) {
                //发送消息
                $this->load->model('message_base_model');
                if ($is_remind == 1) {
                    $signatory_info = $this->api_signatory_model->get_baseinfo_by_signatory_id($paramArray['remind_signatory_id']);
                    $bargain = $this->bargain_model->get_by_id($c_id);
                    $params = array('number' => $bargain['number'],
                        'remind_remark' => $paramArray['remind_remark'],
                        'remind_time' => $paramArray['remind_time']);
                    $this->message_base_model->add_message('8-50', $paramArray['remind_signatory_id'], $signatory_info['truename'], '/bargain/bargain_detail/' . $c_id, $params);
                }
                $str = $this->modify_step_match($paramArray, $old_data);
                $add_data = array(
                    'c_id' => $c_id,
                    'type_name' => "权证流程",
                    'content' => "修改权证流程步骤，{$stage_conf[$old_data['step_id']]['text']}：" . $str,
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'updatetime' => time()
                );
                $return_data['msg'] = '修改权证流程步骤成功！';
                $this->bargain_log_model->add_info($add_data);
                $return_data['result'] = 'ok';

                //操作日志
                $info = $this->bargain_model->get_by_id($c_id);
                $add_log_param = array(
                    'company_id' => $this->user_arr['company_id'],
                    'department_id' => $this->user_arr['department_id'],
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'type' => 35,
                    'text' => '修改编号为' . $info['number'] . '的交易成交的权限流程。' . $str,
                    'from_system' => 1,
                    'from_ip' => get_ip(),
                    'mac_ip' => '127.0.0.1',
                    'from_host_name' => '127.0.0.1',
                    'hardware_num' => '测试硬件序列号',
                    'time' => time()
                );
                $this->signatory_operate_log_model->add_operate_log($add_log_param);
            } else {
                $return_data['msg'] = '修改权证流程步骤失败！';
                $return_data['result'] = 'no';
            }
        } else {
            $total_step_old = $this->bargain_transfer_model->count_by(array('bargain_id' => $c_id));
            $paramArray['company_id'] = $this->user_arr['company_id'];
            $paramArray['department_id'] = $this->user_arr['department_id'];
            $paramArray['signatory_id'] = $this->user_arr['signatory_id'];
            $paramArray['createtime'] = time();
            $paramArray['step_id'] = $total_step_old + 1;
            if ($total_step_old < 10) {
                //如果之前没有添加过步骤，则改变成交创建模版的状态
                if ($total_step_old == 0) {
                    $this->bargain_model->update_by_id(array('is_template' => 1), $c_id);
                }
                $add_result = $this->bargain_transfer_model->insert_data($paramArray); //数据入库
                $total_step = $this->bargain_transfer_model->count_by(array('bargain_id' => $c_id));
                foreach ($post_param['stage'] as $k => $v) {
                    $arr[] = $stage[$v]['stage_name'];
                }
                $stage_name = implode('，', $arr);
                if (!empty($add_result) && is_int($add_result)) {
                    //发送消息
                    $this->load->model('message_base_model');
                    if ($is_remind == 1) {
                        $signatory_info = $this->api_signatory_model->get_baseinfo_by_signatory_id($paramArray['remind_signatory_id']);
                        $bargain = $this->bargain_model->get_by_id($c_id);
                        $params = array('number' => $bargain['number'],
                            'remind_remark' => $paramArray['remind_remark'],
                            'remind_time' => $paramArray['remind_time']);
                        $this->message_base_model->add_message('8-50', $paramArray['remind_signatory_id'], $signatory_info['truename'], '/bargain/bargain_detail/' . $c_id, $params);
                    }
                    $add_data = array(
                        'c_id' => $c_id,
                        'type_name' => "权证流程",
                        'content' => "添加权证流程步骤，{$stage_conf[$total_step]['text']}：{$stage_name}。",
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'updatetime' => time()
                    );
                    $return_data['msg'] = '添加权证流程步骤成功！';
                    $this->bargain_log_model->add_info($add_data);
                    $return_data['result'] = 'ok';
                    //操作日志
                    $info = $this->bargain_model->get_by_id($c_id);
                    $add_log_param = array(
                        'company_id' => $this->user_arr['company_id'],
                        'department_id' => $this->user_arr['department_id'],
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'type' => 35,
                        'text' => '新增编号为' . $info['number'] . '的交易成交的权限流程。',
                        'from_system' => 1,
                        'from_ip' => get_ip(),
                        'mac_ip' => '127.0.0.1',
                        'from_host_name' => '127.0.0.1',
                        'hardware_num' => '测试硬件序列号',
                        'time' => time()
                    );
                    $this->signatory_operate_log_model->add_operate_log($add_log_param);
                } else {
                    $return_data['msg'] = '添加权证流程步骤失败！';
                    $return_data['result'] = 'no';
                }
            } else {
                $return_data['msg'] = '权证流程步骤最多可添加10步！';
                $return_data['result'] = 'no';
            }
        }
        echo json_encode($return_data);
    }

    //删除流程模板
    public function delete_template()
    {
        $template_id = $this->input->post('template_id');
        $rs = $this->transfer_model->delete_data('transfer_template', array('id' => $template_id));
        if ($rs) {
            $this->transfer_model->delete_data('transfer_template_step', array('template_id' => $template_id));
            $json_data['result'] = 'ok';
            $json_data['msg'] = '删除模板成功！';
        } else {
            $json_data['result'] = 'no';
            $json_data['msg'] = '删除模板失败！';
        }
        echo json_encode($json_data);
    }

    //删除流程模板步骤
    public function delete_template_step()
    {
        $stage_id = $this->input->post('stage_id');
        $template_id = $this->input->post('template_id');
        //权证步骤名配置
        $stage = $this->bargain_transfer_model->get_all_stage();

        $data = $this->transfer_model->get_step_by_id($stage_id);//获取原来的数据

        $stageid_arr = explode(',', $data['stage_id']);
        foreach ($stageid_arr as $key => $val) {
            $stage_name1[] = $stage[$val]['stage_name'];
        }
        $stage_name = implode(',', $stage_name1);

        $rs = $this->transfer_model->delete_data('transfer_template_step', array('id' => $stage_id));
        if ($rs) {
            $total = $this->transfer_model->get_count('transfer_template_step', array('template_id' => $template_id));
            if ($total == 0) {
                $this->transfer_model->modify_data('transfer_template', array('is_addstep' => 0), array('id' => $template_id));
            }
            //取出这步之后的步骤
            $steps = $this->transfer_model->get_step_by_con(array('template_id' => $template_id, 'step_id >' => $data['step_id']));
            if (is_full_array($steps)) {
                foreach ($steps as $key => $val) {
                    $this->transfer_model->update_step_status(array('step_id' => $val['step_id'] - 1), $val['id']);
                }
            }
            $return_data['msg'] = "步骤：{$stage_name}已删除！";
            $return_data['result'] = 'ok';
        } else {
            $return_data['msg'] = "步骤：{$stage_name}删除失败！";
            $return_data['result'] = 'no';
        }
        echo json_encode($return_data);
    }

    /** 确定完成实际步骤 */
    public function confirm_complete()
    {
        $c_id = $this->input->post('bargain_id');

        // $department_id = $this->input->post('department_id');
        $stage_id = $this->input->post('stage_id');
        $department_id = $this->user_arr['department_id'];
        // $signatory_id = $this->input->post('signatory_id');

        $complete_signatory_id = $this->input->post('complete_signatory_id');
        $complete_signatory_name = $this->input->post('complete_signatory_name');
        $contirm_time = $this->input->post('confirm_time');
        //权证步骤名配置
        $stage = $this->bargain_transfer_model->get_all_stage();
        //权证配置项
        $stage_conf = $this->bargain_transfer_model->get_stage_conf();
        //获取该步骤数据
        $data = $this->bargain_transfer_model->get_by_id($stage_id);
        $stage_id1 = explode(',', $data['stage_id']);
        foreach ($stage_id1 as $key => $val) {
            $stage_name[] = $stage[$val]['stage_name'];
        }
        $stage_name = implode(',', $stage_name);
        //如果步骤为第一步，责不需要判断上一步是否完成
        $is_free = true;
        if ($is_free) {
            $data['isComplete'] = 1;  //修改属性和值
            $data['complete_department_id'] = $department_id;
            $data['complete_signatory_id'] = $complete_signatory_id;
            $data['complete_signatory_name'] = $complete_signatory_name;
//            $data['complete_time'] = time();
            $data['complete_time'] = strtotime($contirm_time);

            $rs = $this->bargain_transfer_model->modify_data($stage_id, $data);
            if ($rs == 1) {
                $add_data = array(
                    'c_id' => $c_id,
                    'type_name' => "权证流程",
                    'content' => "完成权证流程步骤，{$stage_conf[$data['step_id']]['text']}：{$stage_name}。",
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'updatetime' => time()
                );

                $this->bargain_log_model->add_info($add_data);
                $json_data['msg'] = "步骤：{$stage_name}已完成！";
                $json_data['result'] = 'ok';

                $info = $this->bargain_model->get_by_id($c_id);

                //给金品app推送消息
                $text = '推送金品签约消息失败';
                if ($info['broker_id_a'] > 0) {
                    //获取农税受理状态
                    $nongShuiIscomplete = 0;
                    $isConfirmCollectMoney = 0;
                    $nongShuidata = $this->bargain_transfer_model->get_by_cond('bargain_id = ' . $data['bargain_id'] . ' and stage_id >= 10');
                    if (!empty($nongShuidata)) {
                        foreach ($nongShuidata as $key => $val) {
                            if ($val['stage_id'] == 10 && $val['operate'] == 1) {
                                $isConfirmCollectMoney = 1;
                            }
                            if ($val['stage_id'] > 9 && $val['isComplete'] == 1) {
                                $nongShuiIscomplete = 1;
                            }
                        }
                    }
                    $this->load->library('Curl');
                    $url = JINPIN_URL . '/sign/setSignMsg';//金品url
                    //生成加密签名
                    $this->load->library('DES3');
                    $time = time();
                    $sign = $this->des3->encrypt($info['broker_id_a'] . $time);

                    $params = array(
                        'signId' => $data['bargain_id'],
                        'stageId' => $data['stage_id'],
                        'brokerId' => $info['broker_id_a'],
                        'blockName' => $info['block_name'],
                        'houseId' => $info['house_id'],
                        'nongShuiIscomplete' => $nongShuiIscomplete,
                        'isConfirmCollectMoney' => $isConfirmCollectMoney,
                        'time' => $time,
                        'sign' => $sign
                    );
                    $output = $this->curl->httpRequstPost($url, http_build_query($params));
                    $output = json_decode($output, true);
                    if ($output['success']) {
                        //添加推送日志
                        $text = '推送金品签约消息成功';
                    }
                } else {
                    $text = '签约未选经纪人，推送金品签约消息失败';
                }
                //操作日志
                $add_log_param = array(
                    'company_id' => $this->user_arr['company_id'],
                    'department_id' => $this->user_arr['department_id'],
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'type' => 35,
                    'text' => '完成编号为' . $info['number'] . '的交易成交的权限流程。' . '并且' . $text,
                    'from_system' => 1,
                    'from_ip' => get_ip(),
                    'mac_ip' => '127.0.0.1',
                    'from_host_name' => '127.0.0.1',
                    'hardware_num' => '测试硬件序列号',
                    'time' => time()
                );
                $this->signatory_operate_log_model->add_operate_log($add_log_param);
            } else {
                $json_data['msg'] = "步骤：{$stage_name}完成失败！";
                $json_data['result'] = 'no';
            }
        }
        echo json_encode($json_data);
    }

    /** 确定完成，发送短信 */
    public function send_complete_massage()
    {
        $c_id = $this->input->post('bargain_id');
        $info = $this->bargain_model->get_by_id($c_id);

        $stage_id = $this->input->post('stage_id');

        $contirm_time = $this->input->post('confirm_time');
        //权证步骤名配置
        $stage = $this->bargain_transfer_model->get_all_stage();
        //权证配置项
        $stage_conf = $this->bargain_transfer_model->get_stage_conf();
        //获取该步骤数据
        $data = $this->bargain_transfer_model->get_by_id($stage_id);
        $stage_id1 = explode(',', $data['stage_id']);
        foreach ($stage_id1 as $key => $val) {
            $stage_name[] = $stage[$val]['stage_name'];
        }
        $stage_name = implode(',', $stage_name);


        //发送短信

        $this->load->library('Sms_codi', array('city' => "hz", 'jid' => '2', 'template' => 'transfer_stage_n'), 'sms');
        $phone = $info["broker_tel_a"];
        $rs = $this->sms->send($phone, array('order_sn' => $info["number"], 'house_addr' => $info["house_addr"], 'stage' => $stage_name));
        if ($rs["success"]) {
            $json_data['result'] = 'ok';
            $json_data['msg'] = '短信发送成功！';
        } else {
            $json_data['result'] = 'no';
            $json_data['msg'] = '短信发送失败！' . $rs["errorMessage"];
        }
        echo json_encode($json_data);
    }

    /** 已办结 */
    public function confirm_all_complete()
    {
        $num = 0;
        $bargain_id = $this->input->post('bargain_id');
        //查询出该成交的未完成步骤
        $where = "bargain_id = " . $bargain_id . " and isComplete = 0";
        $total = $this->bargain_transfer_model->count_by($where);
        $result = $this->bargain_transfer_model->get_by_cond($where);
        $update_data = array(
            'isComplete' => 1,
            'complete_signatory_id' => $this->user_arr['signatory_id'],
            'complete_department_id' => $this->user_arr['department_id'],
            'complete_time' => date('Y-m-d', time())
        );
        foreach ($result as $key => $val) {
            $rs = $this->bargain_transfer_model->modify_data($val['id'], $update_data);
            if ($rs) {
                $num++;
            }
        }
        if ($total == $num) {
            //设置成交办结状态属性
            $data['is_completed'] = 1;
            $data['completed_time'] = time();
            //修改成交表中该成交的相应字段
            $rs = $this->bargain_model->update_by_id($data, $bargain_id);
            if ($rs) {
                $add_data = array(
                    'c_id' => $bargain_id,
                    'type_name' => "权证流程",
                    'content' => "对该成交进行结盘操作，完成全部权证流程步骤。",
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'updatetime' => time()
                );
                $this->bargain_log_model->add_info($add_data);
                $json_data['msg'] = '该成交权证流程已办结！';

                //操作日志
                $info = $this->bargain_model->get_by_id($bargain_id);
                $add_log_param = array(
                    'company_id' => $this->user_arr['company_id'],
                    'department_id' => $this->user_arr['department_id'],
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'type' => 35,
                    'text' => '对编号为' . $info['number'] . '的交易成交的权限流程进行结盘。',
                    'from_system' => 1,
                    'from_ip' => get_ip(),
                    'mac_ip' => '127.0.0.1',
                    'from_host_name' => '127.0.0.1',
                    'hardware_num' => '测试硬件序列号',
                    'time' => time()
                );
                $this->signatory_operate_log_model->add_operate_log($add_log_param);
                $json_data['result'] = 'ok';  //成功
            } else {
                $json_data['msg'] = '该成交权证流程办结失败！';
                $json_data['result'] = 'no';  //失败
            }
        } else {
            $json_data['msg'] = '该成交权证流程办结失败！';
            $json_data['result'] = 'no';   //无法办结
        }
        echo json_encode($json_data);
    }

    /** 删除成交模板步骤 */
    public function delete_temp_step()
    {
        $stage_id = $this->input->post('stage_id');
        $c_id = $this->input->post('c_id');
        //权证步骤名配置
        $stage = $this->bargain_transfer_model->get_all_stage();

        $data = $this->bargain_transfer_model->get_by_id($stage_id);//获取原来的数据

        $stageid_arr = explode(',', $data['stage_id']);
        foreach ($stageid_arr as $key => $val) {
            $stage_name1[] = $stage[$val]['stage_name'];
        }
        $stage_name = implode(',', $stage_name1);
        //权证配置项
        $stage_conf = $this->bargain_transfer_model->get_stage_conf();

        $rs = $this->bargain_transfer_model->delete_data($stage_id);
        if ($rs) {
            //取出这步之后的步骤,更改步数
            $steps = $this->bargain_transfer_model->get_by_cond(array('bargain_id' => $c_id, 'step_id >' => $data['step_id']));
            if (is_full_array($steps)) {
                foreach ($steps as $key => $val) {
                    $this->bargain_transfer_model->modify_data($val['id'], array('step_id' => $val['step_id'] - 1));
                }
            }
            $steps_total = $this->bargain_transfer_model->count_by(array('bargain_id' => $c_id));
            if ($steps_total == 0) {
                $this->bargain_model->update_by_id(array('is_template' => 0, 'template_id' => 0), $c_id);
            }
            $add_data = array(
                'c_id' => $c_id,
                'type_name' => "权证流程",
                'content' => "删除权证流程步骤，{$stage_conf[$data['step_id']]['text']}：{$stage_name}。",
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'updatetime' => time()
            );
            $return_data['msg'] = "步骤：{$stage_name}已删除！";
            $this->bargain_log_model->add_info($add_data);
            $return_data['result'] = 'ok';

            //操作日志
            $info = $this->bargain_model->get_by_id($c_id);
            $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'department_id' => $this->user_arr['department_id'],
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '删除编号为' . $info['number'] . '的交易成交的权限流程',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
            );
            $this->signatory_operate_log_model->add_operate_log($add_log_param);
        } else {
            $return_data['msg'] = "步骤：{$stage_name}删除失败！";
            $return_data['result'] = 'no';
        }
        echo json_encode($return_data);
    }

    /** 完成步骤判断 */
    public function sure_temp_judge()
    {
        $c_id = $this->input->post('bargain_id');
        $stage_id = $this->input->post('stage_id');
        //获取该步骤数据
        $data = $this->bargain_transfer_model->get_by_id($stage_id);
        if ($data['step_id'] == 1) {
            $json_data['result'] = 'ok';
        } else {
            //先查询该成交下该步骤的上一步是否已经完成
//      $prev_data = $this->bargain_transfer_model->get_by_cond(array('bargain_id' => $c_id, 'step_id' => $data['step_id'] - 1));
//      $status = $prev_data[0]['isComplete'];
            $status = 1;
            if ($status == 1) {
                $json_data['result'] = 'ok';
            } else {
                $json_data['msg'] = "请先完成上一步！";
                $json_data['result'] = 'no';   //上一步未完成
            }
        }
        echo json_encode($json_data);
    }

    /** 完成步骤判断 */
    public function add_template_judge()
    {
        $total = $this->transfer_model->get_count('transfer_template', array('company_id' => $this->user_arr['company_id']));
        if ($total >= 5) {
            $return_data['result'] = 'no';
            $return_data['msg'] = '公司下最多建立5个模板！';
        } else {
            $return_data['result'] = 'ok';
        }
        echo json_encode($return_data);
    }

//添加税费
    public function add_tax_flow()
    {
        //收付类型 replace 实收实付 should 应收应付
        $flow_type = $this->input->get('flow_type');
        $id = intval($this->input->get('id'));
        //获取成交配置项
        $config = $this->bargain_config_model->get_config();
        $paramArray = array(
            'c_id' => trim($this->input->get('c_id')),
            'money_type' => trim($this->input->get('money_type')),
            'collect_type' => trim($this->input->get('collect_type')),
            'collect_money' => sprintf('%.2f', $this->input->get('collect_money')),
            'pay_type' => trim($this->input->get('pay_type')),
            'pay_money' => sprintf('%.2f', $this->input->get('pay_money')),
            'flow_time' => $this->input->get('flow_time'),
            'remark' => trim($this->input->get('remark')),
            'target_type' => trim($this->input->get('target_type')),
            'target_name' => trim($this->input->get('target_name')),
            'target_idcard' => trim($this->input->get('target_idcard')),
            'bank_account' => trim($this->input->get('bank_account')),
            'collect_person' => trim($this->input->get('collect_person')),
            'replace_type' => trim($this->input->get('replace_type')),
            'certificate_number' => trim($this->input->get('certificate_number')),
            'money_name' => trim($this->input->get('money_name')),
            'money_number' => trim($this->input->get('money_number'))
        );
        if ($flow_type == "replace_tax") {
            $paramArray['payment_method'] = trim($this->input->get('payment_method'));
            $paramArray['flow_department_id'] = trim($this->input->get('flow_department_id'));
            $paramArray['flow_signatory_id'] = trim($this->input->get('flow_signatory_id'));
            $paramArray['counter_fee'] = sprintf('%.2f', $this->input->get('counter_fee'));
            $paramArray['docket'] = trim($this->input->get('docket'));
            $paramArray['docket_type'] = intval($this->input->get('docket_type'));
            $paramArray['is_flow'] = 0;
            $this->replace_payment_model->set_tbl('replace_payment_tax');
        }

        if ($id) {
            $old_data = $this->replace_payment_model->get_by_id($id);
            $update_result = $this->replace_payment_model->flow_update($id, $paramArray); //数据入库
            if ($update_result) {
                $str = $this->modify_flow_match($paramArray, $old_data, $flow_type);
                if ($flow_type == 'replace_tax') {
                    $add_data = array(
                        'c_id' => $paramArray['c_id'],
                        'type_name' => "税费",
                        'content' => "修改税费，款类：{$config['money_type'][$paramArray['money_type']]}。" . $str,
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'updatetime' => time()
                    );
                    $return_data['msg'] = '修改实收实付成功！';
                }
                $this->bargain_log_model->add_info($add_data);

                $return_data['result'] = 'ok';

                //操作日志
                $info = $this->bargain_model->get_by_id($paramArray['c_id']);
                $add_log_param = array(
                    'company_id' => $this->user_arr['company_id'],
                    'department_id' => $this->user_arr['department_id'],
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'type' => 35,
                    'from_system' => 1,
                    'from_ip' => get_ip(),
                    'mac_ip' => '127.0.0.1',
                    'from_host_name' => '127.0.0.1',
                    'hardware_num' => '测试硬件序列号',
                    'time' => time()
                );
                if ($flow_type == 'replace') {
                    $add_log_param['text'] = '修改编号为' . $info['number'] . '的交易成交的实收实付。' . $str;
                }
                $this->signatory_operate_log_model->add_operate_log($add_log_param);

            } else {
                if ($flow_type == 'replace_tax') {
                    $return_data['msg'] = '修改税费失败！';
                }
                $return_data['result'] = 'no';
            }
        } else {
            $paramArray['entry_company_id'] = $this->user_arr['company_id'];
            $paramArray['entry_department_id'] = $this->user_arr['department_id'];
            $paramArray['entry_signatory_id'] = $this->user_arr['signatory_id'];
            $paramArray['entry_time'] = time();
            $info = $this->api_signatory_model->get_baseinfo_by_signatory_id($paramArray['entry_signatory_id']);
            $paramArray['entry_department_name'] = $info['department_name'];
            $paramArray['entry_signatory_name'] = $info['truename'];
            //获取成交详情
            $bargain = $this->bargain_model->get_by_id($paramArray['c_id']);
            $paramArray['type'] = $bargain['type'];
            $add_result = $this->replace_payment_model->add_flow($paramArray); //数据入库
            if (!empty($add_result) && is_int($add_result)) {
                if ($flow_type == 'replace_tax') {
                    $add_data = array(
                        'c_id' => $paramArray['c_id'],
                        'type_name' => "税费",
                        'content' => "添加税费，款类：{$config['money_type'][$paramArray['money_type']]}。",
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'updatetime' => time()
                    );
                    $return_data['msg'] = '添加税费成功！';
                }
                $this->bargain_log_model->add_info($add_data);
                $return_data['result'] = 'ok';
                $return_data['num'] = $this->replace_payment_model->count_by(array('c_id' => $paramArray['c_id']));

                //操作日志
                $info = $this->bargain_model->get_by_id($paramArray['c_id']);
                $add_log_param = array(
                    'company_id' => $this->user_arr['company_id'],
                    'department_id' => $this->user_arr['department_id'],
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'type' => 35,
                    'from_system' => 1,
                    'from_ip' => get_ip(),
                    'mac_ip' => '127.0.0.1',
                    'from_host_name' => '127.0.0.1',
                    'hardware_num' => '测试硬件序列号',
                    'time' => time()
                );
                if ($flow_type == 'replace') {
                    $add_log_param['text'] = '新增编号为' . $info['number'] . '的交易成交的实收实付。';
                } else {
                    $add_log_param['text'] = '新增编号为' . $info['number'] . '的交易成交的应收应付。';
                }
                $this->signatory_operate_log_model->add_operate_log($add_log_param);
            } else {
                if ($flow_type == 'replace_tax') {
                    $return_data['msg'] = '添加税费失败！';
                }
                $return_data['result'] = 'no';
            }
        }
        echo json_encode($return_data);
    }

    //添加代收付
    public function add_signing_flow()
    {
        //收付类型 replace 实收实付 should 应收应付
        $flow_type = $this->input->get('flow_type');
        $id = intval($this->input->get('id'));
        //获取成交配置项
        $config = $this->bargain_config_model->get_config();
        $paramArray = array(
            'c_id' => trim($this->input->get('c_id')),
            'money_type' => trim($this->input->get('money_type')),
            'collect_type' => trim($this->input->get('collect_type')),
            'collect_money' => sprintf('%.2f', $this->input->get('collect_money')),
            'pay_type' => trim($this->input->get('pay_type')),
            'pay_money' => sprintf('%.2f', $this->input->get('pay_money')),
            'flow_time' => $this->input->get('flow_time'),
            'remark' => trim($this->input->get('remark')),
            'target_type' => trim($this->input->get('target_type')),
            'target_name' => trim($this->input->get('target_name')),
            'target_idcard' => trim($this->input->get('target_idcard')),
            'bank_account' => trim($this->input->get('bank_account')),
            'collect_person' => trim($this->input->get('collect_person')),
            'replace_type' => trim($this->input->get('replace_type')),
            'certificate_number' => trim($this->input->get('certificate_number')),
            'money_name' => trim($this->input->get('money_name')),
            'money_number' => trim($this->input->get('money_number'))
        );
        if ($flow_type == "replace_signing") {
            $paramArray['payment_method'] = trim($this->input->get('payment_method'));
            $paramArray['flow_department_id'] = trim($this->input->get('flow_department_id'));
            $paramArray['flow_signatory_id'] = trim($this->input->get('flow_signatory_id'));
            $paramArray['counter_fee'] = sprintf('%.2f', $this->input->get('counter_fee'));
            $paramArray['docket'] = trim($this->input->get('docket'));
            $paramArray['docket_type'] = intval($this->input->get('docket_type'));
            $paramArray['is_flow'] = 0;
            $this->replace_payment_model->set_tbl('replace_payment_signing');
        }

        if ($id) {
            $old_data = $this->replace_payment_model->get_by_id($id);
            $update_result = $this->replace_payment_model->flow_update($id, $paramArray); //数据入库
            if ($update_result) {
                $str = $this->modify_flow_match($paramArray, $old_data, $flow_type);
                if ($flow_type == 'replace_signing') {
                    $add_data = array(
                        'c_id' => $paramArray['c_id'],
                        'type_name' => "签约费",
                        'content' => "修改签约费，款类：{$config['money_type'][$paramArray['money_type']]}。" . $str,
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'updatetime' => time()
                    );
                    $return_data['msg'] = '修改签约费成功！';
                }
                $this->bargain_log_model->add_info($add_data);

                $return_data['result'] = 'ok';

                //操作日志
                $info = $this->bargain_model->get_by_id($paramArray['c_id']);
                $add_log_param = array(
                    'company_id' => $this->user_arr['company_id'],
                    'department_id' => $this->user_arr['department_id'],
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'type' => 35,
                    'from_system' => 1,
                    'from_ip' => get_ip(),
                    'mac_ip' => '127.0.0.1',
                    'from_host_name' => '127.0.0.1',
                    'hardware_num' => '测试硬件序列号',
                    'time' => time()
                );
                if ($flow_type == 'replace') {
                    $add_log_param['text'] = '修改编号为' . $info['number'] . '的交易成交的实收实付。' . $str;
                }
                $this->signatory_operate_log_model->add_operate_log($add_log_param);

            } else {
                if ($flow_type == 'replace_signing') {
                    $return_data['msg'] = '修改签约费失败！';
                }
                $return_data['result'] = 'no';
            }
        } else {
            $paramArray['entry_company_id'] = $this->user_arr['company_id'];
            $paramArray['entry_department_id'] = $this->user_arr['department_id'];
            $paramArray['entry_signatory_id'] = $this->user_arr['signatory_id'];
            $paramArray['entry_time'] = time();
            $info = $this->api_signatory_model->get_baseinfo_by_signatory_id($paramArray['entry_signatory_id']);
            $paramArray['entry_department_name'] = $info['department_name'];
            $paramArray['entry_signatory_name'] = $info['truename'];
            //获取成交详情
            $bargain = $this->bargain_model->get_by_id($paramArray['c_id']);
            $paramArray['type'] = $bargain['type'];
            $add_result = $this->replace_payment_model->add_flow($paramArray); //数据入库
            if (!empty($add_result) && is_int($add_result)) {
                if ($flow_type == 'replace_signing') {
                    $add_data = array(
                        'c_id' => $paramArray['c_id'],
                        'type_name' => "签约费",
                        'content' => "添加签约费，款类：{$config['money_type'][$paramArray['money_type']]}。",
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'updatetime' => time()
                    );
                    $return_data['msg'] = '添加签约费成功！';
                }
                $this->bargain_log_model->add_info($add_data);
                $return_data['result'] = 'ok';
                $return_data['num'] = $this->replace_payment_model->count_by(array('c_id' => $paramArray['c_id']));

                //操作日志
                $info = $this->bargain_model->get_by_id($paramArray['c_id']);
                $add_log_param = array(
                    'company_id' => $this->user_arr['company_id'],
                    'department_id' => $this->user_arr['department_id'],
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'type' => 35,
                    'from_system' => 1,
                    'from_ip' => get_ip(),
                    'mac_ip' => '127.0.0.1',
                    'from_host_name' => '127.0.0.1',
                    'hardware_num' => '测试硬件序列号',
                    'time' => time()
                );
                if ($flow_type == 'replace') {
                    $add_log_param['text'] = '新增编号为' . $info['number'] . '的交易成交的实收实付。';
                } else {
                    $add_log_param['text'] = '新增编号为' . $info['number'] . '的交易成交的应收应付。';
                }
                $this->signatory_operate_log_model->add_operate_log($add_log_param);
            } else {
                if ($flow_type == 'replace_signing') {
                    $return_data['msg'] = '添加税费失败！';
                }
                $return_data['result'] = 'no';
            }
        }
        echo json_encode($return_data);
    }

    //添加代收付
    public function add_flow()
    {
        //收付类型 replace 实收实付 should 应收应付
        $flow_type = $this->input->get('flow_type');
        $id = intval($this->input->get('id'));
        //获取成交配置项
        $config = $this->bargain_config_model->get_config();
        $paramArray = array(
            'c_id' => trim($this->input->get('c_id')),
            'money_type' => trim($this->input->get('money_type')),
            'collect_type' => trim($this->input->get('collect_type')),
            'collect_money' => sprintf('%.2f', $this->input->get('collect_money')),
            'money_number' => sprintf('%.2f', $this->input->get('money_number')),
            'pay_type' => trim($this->input->get('pay_type')),
            'pay_money' => sprintf('%.2f', $this->input->get('pay_money')),
            'flow_time' => $this->input->get('flow_time'),
            'remark' => trim($this->input->get('remark')),
            'target_type' => trim($this->input->get('target_type')),
            'target_name' => trim($this->input->get('target_name')),
            'replace_type' => trim($this->input->get('replace_type')),
            'money_name' => trim($this->input->get('money_name'))
        );
        if ($flow_type == "replace") {
            $paramArray['payment_method'] = trim($this->input->get('payment_method'));
            $paramArray['flow_department_id'] = trim($this->input->get('flow_department_id'));
            $paramArray['flow_signatory_id'] = trim($this->input->get('flow_signatory_id'));
            $paramArray['counter_fee'] = sprintf('%.2f', $this->input->get('counter_fee'));
            $paramArray['docket'] = trim($this->input->get('docket'));
            $paramArray['docket_type'] = intval($this->input->get('docket_type'));
            $paramArray['is_flow'] = 0;
            $this->replace_payment_model->set_tbl('replace_payment');
        }

        if ($id) {
            $old_data = $this->replace_payment_model->get_by_id($id);
            $update_result = $this->replace_payment_model->flow_update($id, $paramArray); //数据入库

            if ($update_result) {
                $str = $this->modify_flow_match($paramArray, $old_data, $flow_type);
                if ($flow_type == 'replace') {
                    $add_data = array(
                        'c_id' => $paramArray['c_id'],
                        'type_name' => "代收付",
                        'content' => "修改代收付，款类：{$config['money_type'][$paramArray['money_type']]}。" . $str,
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'updatetime' => time()
                    );
                    $return_data['msg'] = '修改代收付成功！';
                }
                $this->bargain_log_model->add_info($add_data);

                $return_data['result'] = 'ok';

                //操作日志
                $info = $this->bargain_model->get_by_id($paramArray['c_id']);
                $add_log_param = array(
                    'company_id' => $this->user_arr['company_id'],
                    'department_id' => $this->user_arr['department_id'],
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'type' => 35,
                    'from_system' => 1,
                    'from_ip' => get_ip(),
                    'mac_ip' => '127.0.0.1',
                    'from_host_name' => '127.0.0.1',
                    'hardware_num' => '测试硬件序列号',
                    'time' => time()
                );
                if ($flow_type == 'replace') {
                    $add_log_param['text'] = '修改编号为' . $info['number'] . '的交易成交的代收付。' . $str;
                }
                $this->signatory_operate_log_model->add_operate_log($add_log_param);
            } else {
                if ($flow_type == 'replace') {
                    $return_data['msg'] = '修改实收实付失败！';
                }
                $return_data['result'] = 'no';
            }
        } else {
            $paramArray['entry_company_id'] = $this->user_arr['company_id'];
            $paramArray['entry_department_id'] = $this->user_arr['department_id'];
            $paramArray['entry_signatory_id'] = $this->user_arr['signatory_id'];
            $paramArray['entry_time'] = time();
            $info = $this->api_signatory_model->get_baseinfo_by_signatory_id($paramArray['entry_signatory_id']);
            $paramArray['entry_department_name'] = $info['department_name'];
            $paramArray['entry_signatory_name'] = $info['truename'];
            //获取成交详情
            $bargain = $this->bargain_model->get_by_id($paramArray['c_id']);
            $paramArray['type'] = $bargain['type'];
            $add_result = $this->replace_payment_model->add_flow($paramArray); //数据入库
            if (!empty($add_result) && is_int($add_result)) {
                if ($flow_type == 'replace') {
                    $add_data = array(
                        'c_id' => $paramArray['c_id'],
                        'type_name' => "代收付",
                        'content' => "添加代收付，款类：{$config['money_type'][$paramArray['money_type']]}。",
                        'signatory_id' => $this->user_arr['signatory_id'],
                        'signatory_name' => $this->user_arr['truename'],
                        'updatetime' => time()
                    );
                    $return_data['msg'] = '添加代收付付成功！';
                }

                $this->bargain_log_model->add_info($add_data);
                $return_data['result'] = 'ok';
                $return_data['num'] = $this->replace_payment_model->count_by(array('c_id' => $paramArray['c_id']));

                //操作日志
                $info = $this->bargain_model->get_by_id($paramArray['c_id']);
                $add_log_param = array(
                    'company_id' => $this->user_arr['company_id'],
                    'department_id' => $this->user_arr['department_id'],
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'type' => 35,
                    'from_system' => 1,
                    'from_ip' => get_ip(),
                    'mac_ip' => '127.0.0.1',
                    'from_host_name' => '127.0.0.1',
                    'hardware_num' => '测试硬件序列号',
                    'time' => time()
                );
                if ($flow_type == 'replace') {
                    $add_log_param['text'] = '新增编号为' . $info['number'] . '的交易成交的实收实付。';
                } else {
                    $add_log_param['text'] = '新增编号为' . $info['number'] . '的交易成交的应收应付。';
                }
                $this->signatory_operate_log_model->add_operate_log($add_log_param);
            } else {
                if ($flow_type == 'replace') {
                    $return_data['msg'] = '添加代收付失败！';
                }
                $return_data['result'] = 'no';
            }
        }
        echo json_encode($return_data);
    }

    function flow_del()
    {
        $id = $this->input->get('id');
        $c_id = $this->input->get('c_id');
        $flow_type = $this->input->get('flow_type');
        //获取成交配置项
        $config = $this->bargain_config_model->get_config();

        $data = $this->replace_payment_model->get_by_id($id);
        if ($flow_type == 'replace') {
            $this->replace_payment_model->set_tbl('replace_payment');
        } else {
            $this->replace_payment_model->set_tbl('replace_payment_tax');
        }
        $result = $this->replace_payment_model->del_by_id($id);
        if (!empty($result) && is_int($result)) {
            if ($flow_type == 'replace') {
                $add_data = array(
                    'c_id' => $c_id,
                    'type_name' => "代收付",
                    'content' => "删除代收付，款类：{$config['money_type'][$data['money_type']]}。",
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'updatetime' => time()
                );
                $return_data['msg'] = '删除代收付成功！';
            }
            $return_data['result'] = 'ok';
            $return_data['num'] = $this->replace_payment_model->count_by(array('c_id' => $c_id));
            //操作日志
            $info = $this->bargain_model->get_by_id($c_id);
            $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'department_id' => $this->user_arr['department_id'],
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'type' => 35,
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
            );
            if ($flow_type == 'replace') {
                $add_log_param['text'] = '删除编号为' . $info['number'] . '的交易成交的实收实付。';
            }
            $this->signatory_operate_log_model->add_operate_log($add_log_param);
        } else {
            if ($flow_type == 'replace') {
                $return_data['msg'] = '删除代收付失败！';
            }
            $return_data['result'] = 'no';
        }

        echo json_encode($return_data);
    }

    function flow_tax_del()
    {
        $id = $this->input->get('id');
        $c_id = $this->input->get('c_id');
        $flow_type = $this->input->get('flow_type');
        //获取成交配置项
        $config = $this->bargain_config_model->get_config();

        $data = $this->replace_payment_model->get_by_id($id);
        if ($flow_type == 'replace_tax') {
            $this->replace_payment_model->set_tbl('replace_payment_tax');
        } else {
            $this->replace_payment_model->set_tbl('bargain_should_flow');
        }
        $result = $this->replace_payment_model->del_by_id($id);
        if (!empty($result) && is_int($result)) {
            if ($flow_type == 'replace_tax') {
                $add_data = array(
                    'c_id' => $c_id,
                    'type_name' => "税费",
                    'content' => "删除税费，款类：{$config['money_type'][$data['money_type']]}。",
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'updatetime' => time()
                );
                $return_data['msg'] = '删除税费成功！';
            }
            $return_data['result'] = 'ok';
            $return_data['num'] = $this->replace_payment_model->count_by(array('c_id' => $c_id));
            //操作日志
            $info = $this->bargain_model->get_by_id($c_id);
            $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'department_id' => $this->user_arr['department_id'],
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'type' => 35,
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
            );
            if ($flow_type == 'replace_tax') {
                $add_log_param['text'] = '删除编号为' . $info['number'] . '的交易成交的实收实付。';
            }
            $this->signatory_operate_log_model->add_operate_log($add_log_param);
        } else {
            if ($flow_type == 'replace_tax') {
                $return_data['msg'] = '删除税费失败！';
            }
            $return_data['result'] = 'no';
        }

        echo json_encode($return_data);
    }

    function flow_signing_del()
    {
        $id = $this->input->get('id');
        $c_id = $this->input->get('c_id');
        $flow_type = $this->input->get('flow_type');
        //获取成交配置项
        $config = $this->bargain_config_model->get_config();

        $data = $this->replace_payment_model->get_by_id($id);
        if ($flow_type == 'replace_signing') {
            $this->replace_payment_model->set_tbl('replace_payment_signing');
        } else {
            $this->replace_payment_model->set_tbl('bargain_should_flow');
        }
        $result = $this->replace_payment_model->del_by_id($id);
        if (!empty($result) && is_int($result)) {
            if ($flow_type == 'replace_signing') {
                $add_data = array(
                    'c_id' => $c_id,
                    'type_name' => "签约费",
                    'content' => "删除签约费，款类：{$config['money_type'][$data['money_type']]}。",
                    'signatory_id' => $this->user_arr['signatory_id'],
                    'signatory_name' => $this->user_arr['truename'],
                    'updatetime' => time()
                );
                $return_data['msg'] = '删除签约费成功！';
            }
            $return_data['result'] = 'ok';
            $return_data['num'] = $this->replace_payment_model->count_by(array('c_id' => $c_id));
            //操作日志
            $info = $this->bargain_model->get_by_id($c_id);
            $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'department_id' => $this->user_arr['department_id'],
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'type' => 35,
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
            );
            if ($flow_type == 'replace_tax') {
                $add_log_param['text'] = '删除编号为' . $info['number'] . '的交易成交的实收实付。';
            }
            $this->signatory_operate_log_model->add_operate_log($add_log_param);
        } else {
            if ($flow_type == 'replace_signing') {
                $return_data['msg'] = '删除签约费失败！';
            }
            $return_data['result'] = 'no';
        }

        echo json_encode($return_data);
    }

    function flow_sure()
    {
        //获取成交配置项
        $config = $this->bargain_config_model->get_config();
        $id = $this->input->post('id');
        $c_id = $this->input->post('c_id');
        $update_data = array('is_flow' => 1);
        $this->replace_payment_model->set_tbl('replace_payment');
        $data = $this->replace_payment_model->get_by_id($id);
        $rs = $this->replace_payment_model->modify_data($id, $update_data);
        if ($rs) {
            $add_data = array(
                'c_id' => $c_id,
                'type_name' => "实收实付",
                'content' => "实收实付确认收付，款类：{$config['money_type'][$data['money_type']]}。",
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'updatetime' => time()
            );
            $this->bargain_log_model->add_info($add_data);
            $return_data['result'] = 'ok';
            $return_data['msg'] = '该实收实付已确认！';
            //返回的跟进数据
            $return_data['follow_list'] = $add_data;
            $return_data['follow_list']['updatetime'] = date('Y-m-d', $add_data['updatetime']);

            //操作日志
            $info = $this->bargain_model->get_by_id($bargain_id);
            $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'department_id' => $this->user_arr['department_id'],
                'signatory_id' => $this->user_arr['signatory_id'],
                'signatory_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '确认编号为' . $info['number'] . '的交易成交的实收实付的支付。',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
            );
            $this->signatory_operate_log_model->add_operate_log($add_log_param);
        } else {
            $return_data['result'] = 'no';
            $return_data['msg'] = '该实收实付确认失败！';
        }
        echo json_encode($return_data);
    }

    function flow_detail()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('flow_type');
        if ($type == 'replace') {
            $tbl = 'replace_payment';
        } else {
            $tbl = 'bargain_should_flow';
        }
        $this->replace_payment_model->set_tbl($tbl);
        $data['flow_list'] = $this->replace_payment_model->get_by_id($id);
        if ($type == 'replace') //实付
        {
            $this->signatory_info_model->set_select_fields(array('signatory_id', 'truename'));
            $data['signatory_list'] = $this->signatory_info_model->get_by_department_id($data['flow_list']['flow_department_id']);
        }
        if ($data['flow_list']) {
            $data['result'] = 1;
        } else {
            $data['result'] = 0;
        }
        echo json_encode($data);
    }

    //修改权证步骤内容匹配
    public function modify_step_match($data1, $data2)
    {
        $data = array_diff_assoc($data1, $data2);
        //权证步骤名配置
        $stage = $this->bargain_transfer_model->get_all_stage();
        $stage_id1 = explode(',', $data1['stage_id']);
        foreach ($stage_id1 as $key => $val) {
            $stage_name1[] = $stage[$val]['stage_name'];
        }
        $stage_name1 = implode(',', $stage_name1);
        $stage_id2 = explode(',', $data2['stage_id']);
        foreach ($stage_id2 as $key => $val) {
            $stage_name2[] = $stage[$val]['stage_name'];
        }
        $stage_name2 = implode(',', $stage_name2);
        if (array_key_exists("stage_id", $data)) {
            $str .= "{$stage_name2}->{$stage_name1}";
        } else {
            $str .= "{$stage_name1}";
        }

        return $str;
    }

    //修改业绩分成内容匹配
    public function modify_divide_match($data1, $data2)
    {
        $data = array_diff_assoc($data1, $data2);
        if (array_key_exists("signatory_name", $data)) {
            $str .= "归属人，" . $data2['signatory_name'] . '->' . $data1['signatory_name'] . "。";
        } else {
            $str .= "归属人，" . $data2['signatory_name'] . '。';
        }
        foreach ($data as $key => $val) {
            switch ($key) {
                case 'percent':
                    $str .= "占比" . $data2['percent'] . '->' . $data1['percent'] . "；";
                    break;
                case 'divide_price':
                    $str .= "实际分成金额" . $data2['divide_price'] . '->' . $data1['divide_price'] . "；";
                    break;
                case 'achieve_department_name_a':
                    $str .= "门店业绩归属" . $data2['achieve_department_name_b'] . '-' . $data2['achieve_signatory_name_b'] . '->' . $data1['achieve_department_name_b'] . '-' . $data1['achieve_signatory_name_b'] . "；";
                    break;
            }
        }
        return $str;
    }

    //修改成交内容匹配
    public function modify_flow_match($data1, $data2, $type)
    {
        $data = array_diff_assoc($data1, $data2);

        foreach ($data as $key => $val) {
            $this->load->model('api_signatory_model');
            switch ($key) {
                case 'collect_type':
                    $str .= "收方{$config['collect_type'][$data2['collect_type']]}->{$config['collect_type'][$data1['collect_type']]}；";
                    break;
                case 'money_type':
                    $str .= "款类{$config['money_type'][$data2['money_type']]}->{$config['money_typee'][$data1['money_type']]}；";
                    break;
                case 'collect_money':
                    $str .= "应收金额{$data2['money_type']}->{$data1['money_type']}；";
                    break;
                case 'pay_type':
                    $str .= "付方{$config['pay_type'][$data2['pay_type']]}->{$config['pay_type'][$data1['pay_type']]}；";
                    break;
                case 'pay_money':
                    $str .= "应付金额{$data2['money_type']}->{$data1['money_type']}；";
                    break;
                case 'flow_time':
                    $str .= "收付时间" . date('Y-m-d', $data2['flow_time']) . "->" . date('Y-m-d', $data1['flow_time']) . "；";
                    break;
                case 'remark':
                    $str .= "收付说明{$data2['remark']}->{$data1['remark']}；";
                    break;
            }

            if ($type == 'replace') {
                switch ($key) {
                    case 'flow_signatory_id':
                        $info1 = $this->api_signatory_model->get_baseinfo_by_signatory_id($data1['signatory_id_a']);
                        $info2 = $this->api_signatory_model->get_baseinfo_by_signatory_id($data2['signatory_id_a']);
                        $str .= "收付人{$info2['department_name']} {$info2['truename']}->{$info1['department_name']} {$info1['truename']}；";
                        break;
                    case 'payment_method':
                        $str .= "收付方式{$config['payment_method'][$data2['payment_method']]}->{$config['payment_method'][$data1['payment_method']]}；";
                        break;
                    case 'counter_fee':
                        $str .= "刷卡手续费{$data2['counter_fee']}->{$data1['counter_fee']}；";
                        break;
                    case 'docket':
                        $str .= "单据{$data2['docket']}->{$data1['docket']}；";
                        break;
                    case 'docket_type':
                        $str .= "单据类型{$config['docket_type'][$data2['docket_type']]}->{$config['docket_type'][$data1['docket_type']]}；";
                        break;
                }
            }
        }

        return $str;
    }

    //根据门店id获取经纪人
    public function get_signatory_info()
    {
        $signatory_id = $this->input->get('signatory_id', TRUE);
        $this->signatory_info_model->set_select_fields(array('phone'));
        $data['data'] = $this->signatory_info_model->get_by_signatory_id($signatory_id);
        if (is_full_array($data['data'])) {
            $data['result'] = 1;
            $data['msg'] = '查询成功';
        } else {
            $data['result'] = 0;
            $data['msg'] = '查询失败';
        }
        echo json_encode($data);
    }

    //检查当前页是否有数据，如果没有则刷新
    public function check_list()
    {
        // 分页参数
        $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
        $type = $this->input->post('type');
        $this->_init_pagination($page);

        $cond_where .= " type = " . $type . " and is_check = 0";
        $cond_where = $this->_get_cond_str($post_param);
        //获取列表内容
        $list = $this->bargain_model->get_all_by($cond_where, $this->_offset, $this->_limit);
        if (is_full_array($list)) {
            echo 1;
        } else {
            echo 0;
        }
    }

    //检查当前页是否有数据，如果没有则刷新
    public function check_list1()
    {
        // 分页参数
        $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
        $type = $this->input->post('type');
        $this->_init_pagination($page);

        $cond_where .= " type = " . $type . " and is_check > 0";
        $cond_where = $this->_get_cond_str($post_param);
        //获取列表内容
        $list = $this->bargain_model->get_all_by($cond_where, $this->_offset, $this->_limit);
        if (is_full_array($list)) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function get_info()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        if ($type == 1) {
            $this->sell_house_model->set_id($id);
            $this->sell_house_model->set_search_fields(array('block_id', 'block_name', 'buildarea', 'address', 'sell_type', 'owner', 'idcare', 'telno1', 'dong', 'unit', 'door'));
            $result = $this->sell_house_model->get_info_by_id();
            $result['house_id'] = format_info_id($id, 'sell');
        } else {
            $this->rent_house_model->set_id($id);
            $this->rent_house_model->set_search_fields(array('block_id', 'block_name', 'buildarea', 'address', 'sell_type', 'owner', 'idcare', 'telno1', 'dong', 'unit', 'door'));
            $result = $this->rent_house_model->get_info_by_id();
            $result['house_id'] = format_info_id($id, 'rent');
        }

        echo json_encode($result);
    }

    public function get_customer_info()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        if ($type == 1) {
            $this->buy_customer_model->set_id($id);
            $this->buy_customer_model->set_search_fields(array('truename', 'telno1', 'idno'));
            $result = $this->buy_customer_model->get_info_by_id();
            $result['customer_id'] = format_info_id($id, 'buy_customer');
        } else {
            $this->rent_customer_model->set_id($id);
            $this->rent_customer_model->set_search_fields(array('truename', 'telno1', 'idno'));
            $result = $this->rent_customer_model->get_info_by_id();
            $result['customer_id'] = format_info_id($id, 'rent_customer');
        }

        echo json_encode($result);
    }

    public function get_cooperate_info()
    {
        $id = $this->input->post('id');
        $this->cooperate_model->set_select_fields(array('order_sn'));
        $result = $this->cooperate_model->get_cooperate_baseinfo_by_cid($id);
        echo json_encode($result);
    }

    /**
     * 导出成交报备数据
     * @author   wang
     */
    public function exportReport($type)
    {
        ini_set('memory_limit', '-1');
        $post_param = $this->input->post(NULL, true);
        $config = $this->bargain_config_model->get_config();

        $role_level = $this->user_arr['role_level'];
        if ($role_level < 6) //公司
        {
            //所属公司
            $post_param['enter_company_id'] = $this->user_arr['company_id'];
        } else if ($role_level < 8) //门店
        {
            //所属公司
            $post_param['enter_company_id'] = $this->user_arr['company_id'];
            //所属门店
            $post_param['enter_department_id'] = $this->user_arr['department_id'];
        } else {
            //所属公司
            $post_param['enter_company_id'] = $this->user_arr['company_id'];
            //所属门店
            $post_param['enter_department_id'] = $this->user_arr['department_id'];
            //所属经纪人
            $post_param['enter_signatory_id'] = $this->user_arr['signatory_id'];
        }

        //表单提交参数组成的查询条件
        $cond_where = $this->_get_cond_str($post_param);

        //查询交易类型 出售为1  出租为2
        $cond_where .= " AND `type` = " . $type;

        //清除条件头尾多余空格
        $cond_where = trim($cond_where);
        //符合条件的总行数
        $this->_limit = $this->bargain_model->count_by($cond_where);

        $productlist = $this->bargain_model->get_all_by($cond_where, $this->_offset, $this->_limit);
        $list = array();
        if (is_full_array($productlist)) {
            foreach ($productlist as $key => $value) {
                $list[$key]['number'] = $value['number'];
                if ($type == 1) {
                    $list[$key]['house_id'] = format_info_id($value['house_id'], 'sell');
                } else {
                    $list[$key]['house_id'] = format_info_id($value['house_id'], 'rent');
                }
                $list[$key]['house_addr'] = $value['house_addr'];
                $list[$key]['signing_time'] = date('Y-m-d', $value['signing_time']);
                $list[$key]['department_name'] = $value['department_name_a'];
                $list[$key]['signatory_name'] = $value['signatory_name_a'];
                $list[$key]['status'] = $config['report_status'][$value['status']];
            }
            $list = array_values($list);
        }
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
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '成交编号');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '房源编号');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '房源地址');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', '签约日');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', '签约门店');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', '签约人');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', '状态');
        //设置表格的值
        for ($i = 2; $i <= count($list) + 1; $i++) {
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $i, $list[$i - 2]['number'], PHPExcel_Cell_DataType::TYPE_STRING);
            //$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['number']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['house_id']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['house_addr']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['signing_time']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['department_name']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['signatory_name']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['status']);
        }

        $fileName = 'hetongbaobei' . strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
        //$fileName = iconv("utf-8", "gb2312", $fileName);

        $objPHPExcel->getActiveSheet()->setTitle('成交报备列表');
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

    /**
     * 导出成交数据
     * @author   wang
     */
    public function exportbargain($type)
    {
        ini_set('memory_limit', '-1');
        $post_param = $this->input->post(NULL, true);
        $config = $this->bargain_config_model->get_config();
        //所属公司
        $post_param['enter_company_id'] = $this->user_arr['company_id'];
        //表单提交参数组成的查询条件
        $cond_where = $this->_get_cond_str($post_param);
        //查询交易类型0全部 1一手房 2二手房 3托管
        if ($type == 0) {
            $cond_where .= " AND `is_check` >0";
        } else {
            $cond_where .= " AND `type` = " . $type . " AND `is_check` >0";
        }


        //清除条件头尾多余空格
        $cond_where = trim($cond_where);
        //符合条件的总行数
        $this->_limit = $this->bargain_model->count_by($cond_where);

        $productlist = $this->bargain_model->get_all_by($cond_where, $this->_offset, $this->_limit);
        $typeArr = [
            '0' => '其他',
            '1' => '一手房',
            '2' => '二手房',
            '3' => '托管',
        ];
        $list = array();
        if (is_full_array($productlist)) {
            foreach ($productlist as $key => $value) {
                $list[$key]['number'] = $value['number'];
                $list[$key]['type'] = $typeArr[$value['type']];
                $list[$key]['house_addr'] = $value['house_addr'];
                $list[$key]['owner'] = $value['owner'];
                $list[$key]['buildarea'] = $value['buildarea'];
                $list[$key]['price'] = $value['price'];
                $list[$key]['department_name_a'] = $value['department_name_a'];
                $list[$key]['signatory_name_a'] = $value['signatory_name_a'];
                $list[$key]['signing_time'] = date('Y-m-d', $value['signing_time']);
                $list[$key]['is_check'] = $config['cont_status'][$value['is_check']];
                //获取应收应付合计
                $this->replace_payment_model->set_tbl('bargain_should_flow');
                $should_total = $this->replace_payment_model->get_total("c_id = {$value['id']} AND status < 2");
                $list[$key]['should_total'] = $should_total['collect_money_total'];
                if ($should_total['collect_money_total']) {
                    //获取实收实付合计
                    $this->replace_payment_model->set_tbl('replace_payment');
                    $replace_total = $this->replace_payment_model->get_total("c_id = {$value['id']} AND status < 2");
                    $list[$key]['replace_total'] = $replace_total['collect_money_total'] ? $replace_total['collect_money_total'] : '0.00';
                }
                $list[$key]['remain_total'] = sprintf('%.2f', (floatval($should_total['collect_money_total']) - floatval($replace_total['collect_money_total'])));
                if ($list[$key]['remain_total'] <= 0) {
                    $list[$key]['remain_total'] = "";
                }
                if ($value['commission_time']) {
                    $list[$key]['commission_time'] = date('Y-m-d', $value['commission_time']);
                }
                if ($value['completed_time']) {
                    $list[$key]['completed_time'] = date('Y-m-d', $value['completed_time']);
                }
            }
            $list = array_values($list);
        }
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
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '成交编号');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '交易类型');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '房源地址');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', '面积(㎡)');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', '成交价(W)');
//        if ($house_type == 1) {
//            $objPHPExcel->getActiveSheet()->setCellValue('E1', '成交价(W)');
//        } else {
//            $objPHPExcel->getActiveSheet()->setCellValue('E1', '租金(元/月)');
//        }

        $objPHPExcel->getActiveSheet()->setCellValue('F1', '签约门店');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', '签约人');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', '签约日');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', '状态');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', '应收总计');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', '实收总计');
        $objPHPExcel->getActiveSheet()->setCellValue('L1', '未收总计');
        $objPHPExcel->getActiveSheet()->setCellValue('M1', '结佣日');
        $objPHPExcel->getActiveSheet()->setCellValue('N1', '结盘日');
        //设置表格的值
        for ($i = 2; $i <= count($list) + 1; $i++) {

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['number']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['type']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['house_addr']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['buildarea']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['price']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['department_name_a']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['signatory_name_a']);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 2]['signing_time']);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 2]['is_check']);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $list[$i - 2]['should_total']);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $list[$i - 2]['replace_total']);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $list[$i - 2]['remain_total']);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $list[$i - 2]['commission_time']);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $list[$i - 2]['completed_time']);
        }

        $fileName = 'hetong' . strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
        //$fileName = iconv("utf-8", "gb2312", $fileName);

        $objPHPExcel->getActiveSheet()->setTitle('成交列表');
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

//根据门店id获取经纪人
    public function signatory_list()
    {
        $department_id = $this->input->get('department_id', TRUE);

        $this->signatory_info_model->set_select_fields(array('signatory_id', 'truename'));
        $data['list'] = $this->signatory_info_model->get_by_department_id($department_id);
        if (is_full_array($data['list'])) {
            $data['result'] = 1;
            $data['msg'] = '查询成功';
        } else {
            $data['result'] = 0;
            $data['msg'] = '查询失败';
        }
        echo json_encode($data);
    }

    //根据公司id获取经纪人
    public function company_all_signatorys($company_id)
    {

        $this->signatory_info_model->set_select_fields(array('signatory_id', 'truename'));
        $signatorys = $this->signatory_info_model->get_by_company_id($company_id);
        return $signatorys;
    }

    //根据公司id获取经纪人,签约人role_level,获取signatory
    public function signatorys($company_id, $role_level = array(1, 2))
    {
        $this->signatory_info_model->set_select_fields(array('signatory_id', 'truename'));
        $signatorys = $this->signatory_info_model->get_signatory_by_role_level($company_id, $role_level);
        return $signatorys;
    }

    /**
     * 根据关键词获取门店信息
     *
     * @access public
     * @param  void
     * @return json
     */
    public function get_agency_info_by_kw()
    {
        //当前用户等级
        //$role_level = intval($this->user_arr['role_level']);

        //根据角色，决定搜索范围
//        $search_arr = array(
//            'role_level' => $role_level
//        );
        $search_arr = array();
        $keyword = $this->input->get('keyword', TRUE);
        $this->load->model('agency_model');
        $select_fields = array('id', 'name', 'agency_type');
        $this->agency_model->set_select_fields($select_fields);
        $cmt_info = $this->agency_model->get_agencys_by_kw($keyword);

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

    /**
     * 根据关键词获取经纪人信息
     *
     * @access public
     * @param  void
     * @return json
     */
    public function get_broker_info_by_kw()
    {
        //当前用户等级
        $agency_id = $this->input->get('agency_id', TRUE);

        //当前用户等级
        // $role_level = intval($this->user_arr['role_level']);

        //根据角色，决定搜索范围
        $search_arr = array(
            'agency_id' => $agency_id
        );

        $keyword = $this->input->get('keyword', TRUE);
        $this->load->model('agency_model');
        $select_fields = array('id', 'truename as name', 'phone', 'company_id', 'agency_id');
        $this->agency_model->set_select_fields($select_fields);
        $cmt_info = $this->agency_model->get_brokers_by_kw($keyword, $search_arr);
        foreach ($cmt_info as $key => $value) {
            $cmt_info[$key]['label'] = $value['name'];
        }

        if (empty($cmt_info)) {
            $cmt_info[0]['id'] = 0;
            $cmt_info[0]['label'] = '暂无经纪人';
        }

        echo json_encode($cmt_info);
    }
    /**
     * 根据关键词获取门店信息
     *
     * @access public
     * @param  void
     * @return json
     */
    public function get_agency_info_by_agencyid_companyid()
    {
        //当前用户等级
        $role_level = intval($this->user_arr['role_level']);

        //根据角色，决定搜索范围
        $search_arr = array(
            'role_level' => $role_level
        );

        $agency_id = intval($this->input->get('agency_id', TRUE));
        $company_id = intval($this->input->get('company_id', TRUE));

        $this->load->model('agency_model');
        $select_fields = array('id', 'name', 'agency_type');
        $this->agency_model->set_select_fields($select_fields);
        $cmt_info = $this->agency_model->get_by_agencyid_companyid($agency_id, $company_id);

        echo json_encode($cmt_info);
    }

    public function vpost($post_url, $post_fields, $cookie = '')
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $post_url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields); // Post提交的数据包
        //curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file); // 读取上面所储存的Cookie信息
//        curl_setopt($curl, CURLOPT_PROXY, "118.178.229.226");//短信代理ip
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);
        }
        curl_close($curl); // 关键CURL会话
        return $tmpInfo; // 返回数据
    }
}

/* End of file bargain.php */
/* Location: ./application/mls_guli/controllers/bargain.php */
