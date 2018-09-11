<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

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
class Signing extends MY_Controller
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
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
//加载合同模型类
    $this->load->model('payment_model');
    //加载合同模型类
    $this->load->model('signing_model');
    //加载业绩分成模型类
   // $this->load->model('contract_divide_model');
    //加载合同跟进模型类
   // $this->load->model('contract_log_model');
    //加载实收实付MODEL
   // $this->load->model('contract_flow_model');
    //加载合同基本配置MODEL
    $this->load->model('contract_config_model');
    //加载合同基本配置MODEL
    $this->load->model('bargain_config_model');
    //加载经纪人MODEL
    $this->load->model('broker_info_model');
    //加载签约人模块
    $this->load->model('signatory_info_model');
    //加载门店MODEL
    $this->load->model('agency_model');
    //加载配置项MODEL
    $this->load->model('house_config_model');
    //加载出售房源MODEL
    $this->load->model('sell_house_model');
    //加载出租房源MODEL
    $this->load->model('rent_house_model');
    //加载合作MODEL
   // $this->load->model('cooperate_model');
    //加载求购MODEL
    $this->load->model('buy_customer_model');
    //加载求租MODEL
    $this->load->model('rent_customer_model');
    //加载区属MODEL
    $this->load->model('district_model');
    //加载权证MODEL
   // $this->load->model('warrant_model');
    //加载合同权证MODEL
   // $this->load->model('contract_warrant_model');
    //操作日志MODEL
    $this->load->model('operate_log_model');
    //成交记录模块
    $this->load->model('bargain_model');
    //过户流程模块
    $this->load->model('bargain_transfer_model');
    //权限
    if (is_full_array($this->user_arr)) {
      $this->load->model('broker_permission_model');
      $this->broker_permission_model->set_broker_id($this->user_arr['broker_id'], $this->user_arr['company_id']);
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
        //业主姓名
        $cond_where .= " AND owner = '" . $keyword . "'";
      } elseif ($keyword_type == 2) {
        //客户姓名
        $cond_where .= " AND customer = '" . $keyword . "'";
      }
    }
    //合同id
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
      $cond_where .= " AND createtime >= '" . $form_param['starttime'] . "'";
    }

    if (isset($form_param['endtime']) && !empty($form_param['endtime'])) {
      $cond_where .= " AND createtime <= '" . $form_param['endtime'] . "'";
    }

    //楼盘
    if (isset($form_param['block_id']) && !empty($form_param['block_id'])) {
      $cond_where .= " AND block_id = '" . $form_param['block_id'] . "'";
    }

    //转正状态
    if (isset($form_param['status']) && !empty($form_param['status'])) {
      $cond_where .= " AND status = '" . $form_param['status'] . "'";
    }

    $is_check = isset($form_param['is_check']) ? $form_param['is_check'] : 0;
    //状态
    if ($is_check) {
      if (in_array($is_check, array(1, 2, 3, 4))) {
        $cond_where .= " AND is_check = '" . $is_check . "'";
      } elseif ($is_check == 6) {
        $cond_where .= " AND is_completed = '1'";
      } elseif ($is_check == 5) {
        $cond_where .= " AND is_commission = '1'";
      }
    }

    //业主姓名或客户姓名
    if (isset($form_param['owner_type']) && $form_param['owner_type']) {
      if ($form_param['owner_type'] == 1) {
        if (isset($form_param['owner_name']) && $form_param['owner_name']) {
          $cond_where .= " AND owner like '%" . $form_param['owner_name'] . "%'";
        }
      } else {
        if (isset($form_param['owner_name']) && $form_param['owner_name']) {
          $cond_where .= " AND customer like '%" . $form_param['owner_name'] . "%'";
        }
      }
    }

    //签约门店
    if (isset($form_param['agency_id_a']) && $form_param['agency_id_a']) {
      $cond_where .= " AND agency_id_a = '" . $form_param['agency_id_a'] . "'";
    }

    //签约人
    if (isset($form_param['broker_id_a']) && $form_param['broker_id_a']) {
      $cond_where .= " AND broker_id_a = '" . $form_param['broker_id_a'] . "'";
    }

    //时间条件
    $date_type = isset($form_param['datetype']) ? intval($form_param['datetype']) : 0;
    $date = isset($form_param['date']) ? intval($form_param['date']) : 0;
    if ($date == 1) {
      //今天
      $start_time = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
      $end_time = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
    } elseif ($date == 2) {
      //本周
      $start_time = mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y"));
      $end_time = mktime(23, 59, 59, date("m"), date("d") - date("w") + 7, date("Y"));
    } elseif ($date == 3) {
      //本月
      $start_time = mktime(0, 0, 0, date('n'), 1, date('Y'));
      $end_time = mktime(23, 59, 59, date('n'), date('t'), date('Y'));
    } elseif ($date == 4) {
      //三个月
      $start_time = mktime(0, 0, 0, date('n'), 1, date('Y'));
      $end_time = mktime(23, 59, 59, date('n') + 3, date('t'), date('Y'));
    } else {
      //自定义
      $start_time = isset($form_param['start_time']) && !empty($form_param['start_time']) ? strtotime($form_param['start_time'] . ' 0:0:0') : "";
      $end_time = isset($form_param['end_time']) && !empty($form_param['end_time']) ? strtotime($form_param['end_time'] . ' 23:59:59') : "";
    }
    if ($date_type == 3) {
      //结盘日期
      if ($start_time) {
        $cond_where .= " AND completed_time >= '" . $start_time . "'";
      }
      if ($end_time) {
        $cond_where .= " AND completed_time <= '" . $end_time . "'";
      }
    } elseif ($date_type == 2) {
      //结佣日期
      if ($start_time) {
        $cond_where .= " AND commission_time >= '" . $start_time . "'";
      }
      if ($end_time) {
        $cond_where .= " AND commission_time <= '" . $end_time . "'";
      }
    } else {
      //签约日期
      if ($start_time) {
        $cond_where .= " AND signing_time >= '" . $start_time . "'";
      }
      if ($end_time) {
        $cond_where .= " AND signing_time <= '" . $end_time . "'";
      }
    }
    //经纪人所在门店
    if (isset($form_param['enter_company_id']) && $form_param['enter_company_id']) {
      $cond_where .= " AND enter_company_id = '" . $form_param['enter_company_id'] . "'";
    }

    //经纪人所在门店
    if (isset($form_param['enter_agency_id']) && $form_param['enter_agency_id']) {
      $cond_where .= " AND enter_agency_id = '" . $form_param['enter_agency_id'] . "'";
    }

    //录入经纪人
    if (isset($form_param['enter_broker_id']) && $form_param['enter_broker_id']) {
      $cond_where .= " AND enter_broker_id = '" . $form_param['enter_broker_id'] . "'";
    }

    return $cond_where;
  }

  /**
   * 出售列表条件
   * 根据表单提交参数，获取查询条件
   */
  private function _select_cond_str($form_param)
  {
    $cond_where = '`is_del` = 0';

    $keyword_type = isset($form_param['keyword_type']) ? intval($form_param['keyword_type']) : 0;
    $keyword = isset($form_param['keyword']) ? trim($form_param['keyword']) : "";
    if ($keyword) {
      if ($keyword_type == 1) {
        //业主姓名
        $cond_where .= " AND `bargain`.owner = '" . $keyword . "'";
      } elseif ($keyword_type == 2) {
        //客户姓名
        $cond_where .= " AND `bargain`.customer = '" . $keyword . "'";
      }
    }
    //合同id
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

    $is_check = isset($form_param['is_check']) ? $form_param['is_check'] : 0;
    //状态
    if ($is_check) {
      if (in_array($is_check, array(1, 2, 3, 4))) {
        $cond_where .= " AND `bargain`.is_check = '" . $is_check . "'";
      } elseif ($is_check == 6) {
        $cond_where .= " AND `bargain`.is_completed = '1'";
      } elseif ($is_check == 5) {
        $cond_where .= " AND `bargain`.is_commission = '1'";
      }
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
    //房源方或客源方经纪人
    //经纪人所在公司
    if ((isset($form_param['company_id_a']) && $form_param['company_id_a']) || (isset($form_param['company_id_b']) && $form_param['company_id_b'])) {
      $cond_where .= " AND (`bargain`.company_id_a = '" . $form_param['company_id_a'] . "' OR `bargain`.company_id_b = '" . $form_param['company_id_b'] . "')";
    }
    //经纪人所在门店
    if ((isset($form_param['agency_id_a']) && $form_param['agency_id_a']) || (isset($form_param['agency_id_b']) && $form_param['agency_id_b'])) {
      $cond_where .= " AND (`bargain`.agency_id_a = '" . $form_param['agency_id_a'] . "' OR `bargain`.agency_id_a = '" . $form_param['agency_id_a'] . "')";
    }

    //经纪人
    if ((isset($form_param['broker_id_a']) && $form_param['broker_id_a']) || (isset($form_param['broker_id_b']) && $form_param['broker_id_b'])) {
      $cond_where .= " AND (`bargain`.broker_id_a = '" . $form_param['broker_id_a'] . "' OR `bargain`.broker_id_b = '" . $form_param['broker_id_a'] . "')";
    }

    //时间条件
    $date_type = isset($form_param['datetype']) ? intval($form_param['datetype']) : 0;
    $date = isset($form_param['date']) ? intval($form_param['date']) : 0;
    if ($date == 1) {
      //今天
      $start_time = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
      $end_time = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
    } elseif ($date == 2) {
      //本周
      $start_time = mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y"));
      $end_time = mktime(23, 59, 59, date("m"), date("d") - date("w") + 7, date("Y"));
    } elseif ($date == 3) {
      //本月
      $start_time = mktime(0, 0, 0, date('n'), 1, date('Y'));
      $end_time = mktime(23, 59, 59, date('n'), date('t'), date('Y'));
    } elseif ($date == 4) {
      //三个月
      $start_time = mktime(0, 0, 0, date('n'), 1, date('Y'));
      $end_time = mktime(23, 59, 59, date('n') + 3, date('t'), date('Y'));
    } else {
      //自定义
      $start_time = isset($form_param['start_time']) && !empty($form_param['start_time']) ? strtotime($form_param['start_time'] . ' 0:0:0') : "";
      $end_time = isset($form_param['end_time']) && !empty($form_param['end_time']) ? strtotime($form_param['end_time'] . ' 23:59:59') : "";
    }
    if ($date_type == 3) {
      //结盘日期
      if ($start_time) {
        $cond_where .= " AND `bargain`.completed_time >= '" . $start_time . "'";
      }
      if ($end_time) {
        $cond_where .= " AND `bargain`.completed_time <= '" . $end_time . "'";
      }
    } elseif ($date_type == 2) {
      //结佣日期
      if ($start_time) {
        $cond_where .= " AND `bargain`.commission_time >= '" . $start_time . "'";
      }
      if ($end_time) {
        $cond_where .= " AND `bargain`.commission_time <= '" . $end_time . "'";
      }
    } else {
      //签约日期
      if ($start_time) {
        $cond_where .= " AND `bargain`.signing_time >= '" . $start_time . "'";
      }
      if ($end_time) {
        $cond_where .= " AND `bargain`.signing_time <= '" . $end_time . "'";
      }
    }
    //经纪人所在门店
    if (isset($form_param['enter_company_id']) && $form_param['enter_company_id']) {
      $cond_where .= " AND `bargain`.enter_company_id = '" . $form_param['enter_company_id'] . "'";
    }

    //经纪人所在门店
    if (isset($form_param['enter_agency_id']) && $form_param['enter_agency_id']) {
      $cond_where .= " AND `bargain`.enter_agency_id = '" . $form_param['enter_agency_id'] . "'";
    }

    //录入经纪人
    if (isset($form_param['enter_broker_id']) && $form_param['enter_broker_id']) {
      $cond_where .= " AND `bargain`.enter_broker_id = '" . $form_param['enter_broker_id'] . "'";
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
   * 预约签约
   * @access public
   * @return void
   */
  public function report($type = 0)
  {

    //模板使用数据
    $data = array();

    //树型菜单
  // $data['user_tree_menu'] = $this->user_tree_menu;
      //树形菜单栏
    $data['user_tree_menu'] = $this->permission_tab_model->get_tree_menu('signing', 'report');

    //页面标题
    $data['page_title'] = '预约签约';

    //获取合同配置信息
    $data['config'] = $this->contract_config_model->get_config();

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
      $post_param['enter_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['enter_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['enter_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['enter_broker_id'] = $this->user_arr['broker_id'];
    }

    //表单提交参数组成的查询条件
    $cond_where = $this->_get_cond_str($post_param);

    //查询交易类型 出售为1  出租为2
    //审核状态 0 未进入审核 1 未审核 2 审核通过 3 审核未通过 4 作废
//    $cond_where .= " AND `type` = " . $type;
    $data['type'] = $type;

    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->signing_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['agency_id_a']);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];

      $report_add_per = $this->broker_permission_model->check('145');
      $report_edit_per = $this->broker_permission_model->check('145');
      $report_delete_per = $this->broker_permission_model->check('145');
      $report_report_per = $this->broker_permission_model->check('145');
    $data['auth'] = array(
      'add' => $report_add_per, 'edit' => $report_edit_per,
      'delete' => $report_delete_per, 'report' => $report_report_per
    );
    //清除条件头尾多余空格
    $cond_where = trim($cond_where);
    //符合条件的总行数
    $this->_total_count = $this->signing_model->count_by($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->signing_model->get_all_by($cond_where, $this->_offset, $this->_limit);

    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        if ($type == 0) {
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
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_signing.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/signing_report', $data);
  }

  /**
   * 合同报备编辑页面
   * @access public
   * @return void
   */
  public function modify_report_index($type = 0, $id = 0)
  {

    //获取合同配置信息
    $data['config'] = $this->house_config_model->get_config();
    $data['report_id'] = $id;
    $data['type'] = $type;
    //查询出报备数据
    $data['report'] = $this->signing_model->get_by_id($id);
      //检查取消预约后是否有其他人预约该房源
      if ($data['report']['status'] == 5) {
          $where = "id != {$id} and house_id = '{$data['report']['house_id']}' and status != 5 ";
          $result = $this->signing_model->get_one_by($where);
          if (!empty($result)) {
              $data['has_other_report'] = 1;
          }
      }
    $data['report']['signing_time'] = $data['report']['signing_time'] ? date('Y-m-d H', $data['report']['signing_time']) : '';
    if ($type == 0) {
      $data['report']['house_id'] = $data['report']['house_id'] ? format_info_id($data['report']['house_id'], 'sell') : '';
    } else {
      $data['report']['house_id'] = $data['report']['house_id'] ? format_info_id($data['report']['house_id'], 'rent') : '';
    }

    if ($id) {
      $agency_id = $data['report']['agency_id_a'];
    }
    $range_menu = $this->signing_model->get_range_menu_by_role_level(
      $this->user_arr, $agency_id);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];

    $report_add_per = $this->broker_permission_model->check('110');
    $report_edit_per = $this->broker_permission_model->check('111');
    $data['auth'] = array(
      'add' => $report_add_per, 'edit' => $report_edit_per
    );

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_signing.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/appointment_modify_report', $data);
  }

    /**
     * 合同报备编辑页面
     * @access public
     * @return void
     */
    public function modify_report_check()
    {
        $data = array();
        //查询出报备数据
        $id = $this->input->get('id');
        $report = $this->signing_model->get_by_id($id);
        //检查取消预约后是否有其他人预约该房源
        if ($report['status'] == 5) {
            $where = "id != {$id} and house_id = '{$report['house_id']}' and status != 5 ";
            $result = $this->signing_model->get_one_by($where);
            if (!empty($result)) {
                $data['has_other_report'] = 1;
            }
        }
        echo json_encode($data);
    }

  /**
   * 权证步骤编辑页面
   * @access public
   * @return void
   */
  public function modify_warrant_index($c_id = 0, $id = 0)
  {

    //获取合同配置信息
    $data['config'] = $this->house_config_model->get_config();
    //权限
    $warrant_add_per = $this->broker_permission_model->check('68');

    $data['auth'] = array(
      'warrant_add' => $warrant_add_per
    );

    //权证配置项
    $data['stage_conf'] = $this->contract_warrant_model->get_stage_conf();
    //权证步骤名配置
    $data['stage'] = $this->contract_warrant_model->get_all_stage();
    //合同详情
    $data['contract'] = $this->signing_model->get_by_id($c_id);

    $data['warrant_info'] = $this->contract_warrant_model->get_temp_by_id($data['contract']['template_id']);
    $data['temp_id'] = $id;
    $data['c_id'] = $c_id;

    //步骤详情
    if ($id) {
      $data['step'] = $this->contract_warrant_model->get_by_id($id);
      $data['step']['stage_id'] = explode(',', $data['step']['stage_id']);
      $agency_id = $data['step']['remind_agency_id'];
    } else {
      $data['total_step'] = $this->contract_warrant_model->count_by(array('contract_id' => $c_id));
      $agency_id = $this->user_arr['agency_id'];
    }

    $range_menu = $this->signing_model->get_range_menu_by_role_level(
      $this->user_arr, $agency_id);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];

    //权证步骤名配置
    $data['stage'] = $this->contract_warrant_model->get_all_stage();
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_signing.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_warrant_modify', $data);
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

    $data['warrant_list'] = $this->bargain_transfer_model->get_by_id($id);
    //合同详情
    $data['contract'] = $this->bargain_model->get_by_id($data['warrant_list']['bargain_id']);

    $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($data['warrant_list']['broker_id_a']);
    $data['warrant_list']['agency_name'] = $broker_info['agency_name'];
    $data['warrant_list']['broker_name'] = $broker_info['truename'];

    $signatory_info = $this->signatory_info_model->get_by_id($data['warrant_list']['complete_signatory_id']);
    $data['warrant_list']['complete_signatory_name'] = $signatory_info['truename'];

    if ($data['warrant_list']['is_remind'] == 1) {
      $broker_info1 = $this->api_broker_model->get_baseinfo_by_broker_id($data['warrant_list']['remind_broker_id']);
      $data['warrant_list']['remind_agency_name'] = $broker_info['agency_name'];
      $data['warrant_list']['remind_broker_name'] = $broker_info['truename'];
    }

    $stage_name = explode(',', $data['warrant_list']['stage_id']);
    foreach ($stage_name as $k => $v) {
      $arr[] = $data['stage'][$v]['stage_name'];
      $data['warrant_list']['stage_name'] = implode('，', $arr);
    }
    //权证步骤名配置
    $data['stage'] = $this->bargain_transfer_model->get_all_stage();
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
      . 'mls/js/v1.0/backspace.js');
    if ($data['warrant_list']['is_remind'] == 1) {
      $this->view('/signing/bargain_transfer_detail', $data);
    } else {
      $this->view('/signing/bargain_transfer_detail1', $data);
    }
  }

  /**
   * 权证步骤详情页面
   * @access public
   * @return void
   */
  public function contract_warrant_detail($id)
  {

    //权证配置项
    $data['stage_conf'] = $this->contract_warrant_model->get_stage_conf();
    //权证步骤名配置
    $data['stage'] = $this->contract_warrant_model->get_all_stage();

    $data['warrant_list'] = $this->contract_warrant_model->get_by_id($id);
    //合同详情
    $data['contract'] = $this->signing_model->get_by_id($data['warrant_list']['contract_id']);

    $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($data['warrant_list']['broker_id']);
    $data['warrant_list']['agency_name'] = $broker_info['agency_name'];
    $data['warrant_list']['broker_name'] = $broker_info['truename'];

    if ($data['warrant_list']['is_remind'] == 1) {
      $broker_info1 = $this->api_broker_model->get_baseinfo_by_broker_id($data['warrant_list']['remind_broker_id']);
      $data['warrant_list']['remind_agency_name'] = $broker_info['agency_name'];
      $data['warrant_list']['remind_broker_name'] = $broker_info['truename'];
    }

    $stage_name = explode(',', $data['warrant_list']['stage_id']);
    foreach ($stage_name as $k => $v) {
      $arr[] = $data['stage'][$v]['stage_name'];
      $data['warrant_list']['stage_name'] = implode('，', $arr);
    }
    //权证步骤名配置
    $data['stage'] = $this->contract_warrant_model->get_all_stage();
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
      . 'mls/js/v1.0/backspace.js');
    if ($data['warrant_list']['is_remind'] == 1) {
      $this->view('/signing/contract_warrant_detail', $data);
    } else {
      $this->view('/signing/contract_warrant_detail1', $data);
    }
  }

  public function contract_list($type = 1)
  {
    header("cache-control:no-cache,must-revalidate");
    //模板使用数据
    $data = array();
    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;

    //页面标题
    $data['page_title'] = '交易合同列表';

    //获取合同配置信息
    $data['config'] = $this->contract_config_model->get_config();

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
      $post_param['enter_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['enter_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['enter_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['enter_broker_id'] = $this->user_arr['broker_id'];
    }

    //判断是否提交表单,设置本页搜索条件cookie
    if ($is_submit_form) {
      $contract_list = array(
        'number' => $post_param['number'],
        'block_name' => $post_param['block_name'],
        'block_id' => $post_param['block_id'],
        'owner_type' => $post_param['owner_type'],
        'owner_name' => $post_param['owner_name'],
        'agency_id_a' => $post_param['agency_id_a'],
        'broker_id_a' => $post_param['broker_id_a'],
        'datetype' => $post_param['datetype'],
        'date' => $post_param['date'],
        'start_time' => $post_param['start_time'],
        'end_time' => $post_param['end_time'],
        'is_check' => $post_param['is_check'],
        'page' => $post_param['page'],
        'is_submit' => $post_param['is_submit'],
        'orderby_id' => $post_param['orderby_id'],
        'enter_company_id' => $post_param['enter_company_id']
      );
      //区分出售出租
      if (0 == $type) {
        setcookie('contract_list_1', serialize($contract_list), time() + 3600 * 24 * 7, '/');
      } else if (2 == $type) {
        setcookie('contract_list_2', serialize($contract_list), time() + 3600 * 24 * 7, '/');
      }
    } else {
      //区分出售出租
      if (0 == $type) {
        $contract_list_search = unserialize($_COOKIE['contract_list_1']);
      } else if (1 == $type) {
        $contract_list_search = unserialize($_COOKIE['contract_list_1']);
      } else if (2 == $type) {
        $contract_list_search = unserialize($_COOKIE['contract_list_2']);
      }

      if (is_full_array($contract_list_search)) {
        $post_param['number'] = $contract_list_search['number'];
        $post_param['block_name'] = $contract_list_search['block_name'];
        $post_param['block_id'] = $contract_list_search['block_id'];
        $post_param['owner_type'] = $contract_list_search['owner_type'];
        $post_param['owner_name'] = $contract_list_search['owner_name'];
        $post_param['agency_id_a'] = $contract_list_search['agency_id_a'];
        $post_param['broker_id_a'] = $contract_list_search['broker_id_a'];
        $post_param['datetype'] = $contract_list_search['datetype'];
        $post_param['date'] = $contract_list_search['date'];
        $post_param['start_time'] = $contract_list_search['start_time'];
        $post_param['end_time'] = $contract_list_search['end_time'];
        $post_param['is_check'] = $contract_list_search['is_check'];
        $post_param['page'] = $contract_list_search['page'];
        $post_param['is_submit'] = $contract_list_search['is_submit'];
        $post_param['orderby_id'] = $contract_list_search['orderby_id'];
        $post_param['enter_company_id'] = $contract_list_search['enter_company_id'];
      }
    }
    $data['post_param'] = $post_param;

    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->signing_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['agency_id_a']);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];

    //权限
    $contract_add_per = $this->broker_permission_model->check('114');
    $contract_edit_per = $this->broker_permission_model->check('115');
    $contract_delete_per = $this->broker_permission_model->check('116');
    $contract_cancel_per = $this->broker_permission_model->check('117');
    $data['auth'] = array(
      'add' => $contract_add_per, 'edit' => $contract_edit_per,
      'delete' => $contract_delete_per, 'cancel' => $contract_cancel_per
    );

    //表单提交参数组成的查询条件
    $cond_where = $this->_get_cond_str($post_param);
    //查询交易类型 出售为1  出租为2
    //审核状态 0 未进入审核 1 未审核 2 审核通过 3 审核未通过 4 作废
    $cond_where .= " AND type = " . $type . " and is_check > 0";
    $data['type'] = $type;
    //清除条件头尾多余空格
    $cond_where = trim($cond_where);

    //符合条件的总行数
    $this->_total_count = $this->signing_model->count_by($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    if ($post_param['orderby_id'] == 1) {
      $order_key = 'price';
      $order_by = 'ASC';
    } elseif ($post_param['orderby_id'] == 2) {
      $order_key = 'price';
      $order_by = 'DESC';
    } else {
      $order_key = 'id';
      $order_by = 'DESC';
    }
    //获取列表内容
    $list = $this->signing_model->get_all_by($cond_where, $this->_offset, $this->_limit, $order_key, $order_by);

    foreach ($list as $key => $val) {
      //所有的价格和面积末位清零
      $list[$key]['buildarea'] = strip_end_0($val['buildarea']);
      $list[$key]['price'] = strip_end_0($val['price']);
      //获取应收应付合计
      $this->contract_flow_model->set_tbl('contract_should_flow');
      $should_total = $this->contract_flow_model->get_total("c_id = {$val['id']} AND status < 2");
      $list[$key]['should_total'] = strip_end_0($should_total['collect_money_total']);
      if ($should_total['collect_money_total']) {
        //获取实收实付合计
        $this->contract_flow_model->set_tbl('contract_actual_flow');
        $actual_total = $this->contract_flow_model->get_total("c_id = {$val['id']} AND status < 2");
        $list[$key]['actual_total'] = $actual_total['collect_money_total'] ? strip_end_0($actual_total['collect_money_total']) : '0';
      }
      $list[$key]['remain_total'] = strip_end_0(floatval($should_total['collect_money_total']) - floatval($actual_total['collect_money_total']));
      if (mb_strlen($val['house_addr'], 'UTF8') > 50) {
        $list[$key]['house_addr'] = mb_substr($val['house_addr'], 0, 40, 'utf-8') . '...';
      }
      if (mb_strlen($val['number'], 'UTF8') > 10) {
        $list[$key]['number'] = mb_substr($val['number'], 0, 9, 'utf-8') . '...';
      }
    }
    $data['list'] = $list;

    $list1 = $this->signing_model->get_all_by($cond_where, 0, $this->_total_count);
    foreach ($list1 as $key => $val) {
      $ids_arr[] = $val['id'];
    }
    if (is_full_array($ids_arr)) {
      $ids_str = implode(",", $ids_arr);

      //应收总计
      $this->contract_flow_model->set_tbl('contract_should_flow');
      $should_all_total = $this->contract_flow_model->get_total("c_id in ({$ids_str}) AND status < 2");
      $data['should_money_total'] = $should_all_total['collect_money_total'] ? strip_end_0($should_all_total['collect_money_total']) : '0';

      //获取实收实付合计
      $this->contract_flow_model->set_tbl('contract_actual_flow');
      $actual_all_total = $this->contract_flow_model->get_total("c_id in ({$ids_str}) AND status < 2");
      //实收总计
      $data['actual_money_total'] = $actual_all_total['collect_money_total'] ? strip_end_0($actual_all_total['collect_money_total']) : '0';
      $data['remain_money_total'] = strip_end_0(floatval($data['should_money_total']) - floatval($data['actual_money_total']));
    } else {
      $data['commission_total_total'] = "0";
      $data['collect_money_total'] = "0";
      $data['remain_money_total'] = "0";
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
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_signing.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_list', $data);
  }

  public function transfer_list($type = 0)
  {
    header("cache-control:no-cache,must-revalidate");
    //模板使用数据
    $data = array();
    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;

    //页面标题
    $data['page_title'] = '交易合同列表';

    //获取合同配置信息
    $data['config'] = $this->bargain_config_model->get_config();

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

    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //房源方或客源方
      //所属公司
      $post_param['company_id_a'] = $post_param['company_id_b'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //房源方或客源方
      //所属公司
      $post_param['company_id_a'] = $post_param['company_id_b'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id_a'] = $post_param['agency_id_a'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['company_id_a'] = $post_param['company_id_b'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id_a'] = $post_param['agency_id_b'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['broker_id_a'] = $post_param['broker_id_b'] = $this->user_arr['broker_id'];
    }

    //判断是否提交表单,设置本页搜索条件cookie
    if ($is_submit_form) {
      $contract_list = array(
        'number' => $post_param['number'],
        'block_name' => $post_param['block_name'],
        'block_id' => $post_param['block_id'],
        'owner_type' => $post_param['owner_type'],
        'owner_name' => $post_param['owner_name'],
        'agency_id_a' => $post_param['agency_id_a'],
        'broker_id_a' => $post_param['broker_id_a'],
        'datetype' => $post_param['datetype'],
        'date' => $post_param['date'],
        'start_time' => $post_param['start_time'],
        'end_time' => $post_param['end_time'],
        'is_check' => $post_param['is_check'],
        'page' => $post_param['page'],
        'is_submit' => $post_param['is_submit'],
        'orderby_id' => $post_param['orderby_id'],
        'enter_company_id' => $post_param['enter_company_id']
      );
      //区分出售出租
      if (0 == $type) {
        setcookie('contract_list_1', serialize($contract_list), time() + 3600 * 24 * 7, '/');
      } else if (2 == $type) {
        setcookie('contract_list_2', serialize($contract_list), time() + 3600 * 24 * 7, '/');
      }
    } else {
      //区分出售出租
      if (0 == $type) {
        $contract_list_search = unserialize($_COOKIE['contract_list_0']);
      } else if (1 == $type) {
        $contract_list_search = unserialize($_COOKIE['contract_list_1']);
      } else if (2 == $type) {
        $contract_list_search = unserialize($_COOKIE['contract_list_2']);
      }

      if (is_full_array($contract_list_search)) {
        $post_param['number'] = $contract_list_search['number'];
        $post_param['block_name'] = $contract_list_search['block_name'];
        $post_param['block_id'] = $contract_list_search['block_id'];
        $post_param['owner_type'] = $contract_list_search['owner_type'];
        $post_param['owner_name'] = $contract_list_search['owner_name'];
        $post_param['agency_id_a'] = $contract_list_search['agency_id_a'];
        $post_param['broker_id_a'] = $contract_list_search['broker_id_a'];
        $post_param['datetype'] = $contract_list_search['datetype'];
        $post_param['date'] = $contract_list_search['date'];
        $post_param['start_time'] = $contract_list_search['start_time'];
        $post_param['end_time'] = $contract_list_search['end_time'];
        $post_param['is_check'] = $contract_list_search['is_check'];
        $post_param['page'] = $contract_list_search['page'];
        $post_param['is_submit'] = $contract_list_search['is_submit'];
        $post_param['orderby_id'] = $contract_list_search['orderby_id'];
        $post_param['enter_company_id'] = $contract_list_search['enter_company_id'];
      }
    }
    $data['post_param'] = $post_param;

    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->signing_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['agency_id_a']);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];

    //权限
    $contract_add_per = $this->broker_permission_model->check('114');
    $contract_edit_per = $this->broker_permission_model->check('115');
    $contract_delete_per = $this->broker_permission_model->check('116');
    $contract_cancel_per = $this->broker_permission_model->check('117');
    $data['auth'] = array(
      'add' => $contract_add_per, 'edit' => $contract_edit_per,
      'delete' => $contract_delete_per, 'cancel' => $contract_cancel_per
    );

    //表单提交参数组成的查询条件
    $cond_where = $this->_select_cond_str($post_param);
    //查询交易类型 出售为1  出租为2
    //审核状态 0 未进入审核 1 未审核 2 审核通过 3 审核未通过 4 作废
    if ($type != 0) {
      $cond_where .= " and type = " . $type . " and is_check > 0";
    } else {
      $cond_where .= " and is_check > 0";
    }

    $data['type'] = $type;
    //清除条件头尾多余空格
    $cond_where = trim($cond_where);

    //符合条件的总行数
    $this->_total_count = $this->bargain_model->count_by($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    if ($post_param['orderby_id'] == 1) {
      $order_key = 'price';
      $order_by = 'ASC';
    } elseif ($post_param['orderby_id'] == 2) {
      $order_key = 'price';
      $order_by = 'DESC';
    } else {
      $order_key = 'id';
      $order_by = 'DESC';
    }
    //获取列表内容
    $list = $this->bargain_model->get_all_by($cond_where, $this->_offset, $this->_limit, $order_key, $order_by);

    foreach ($list as $key => $val) {
      //所有的价格和面积末位清零
      $list[$key]['buildarea'] = strip_end_0($val['buildarea']);
      $list[$key]['price'] = strip_end_0($val['price']);
      //获取应收应付合计
//      $this->contract_flow_model->set_tbl('contract_should_flow');
//      $should_total = $this->contract_flow_model->get_total("c_id = {$val['id']} AND status < 2");
//      $list[$key]['should_total'] = strip_end_0($should_total['collect_money_total']);
//      if ($should_total['collect_money_total']) {
        //获取实收实付合计
//        $this->contract_flow_model->set_tbl('contract_actual_flow');
//        $actual_total = $this->contract_flow_model->get_total("c_id = {$val['id']} AND status < 2");
//        $list[$key]['actual_total'] = $actual_total['collect_money_total'] ? strip_end_0($actual_total['collect_money_total']) : '0';
//      }
//      $list[$key]['remain_total'] = strip_end_0(floatval($should_total['collect_money_total']) - floatval($actual_total['collect_money_total']));
      if (mb_strlen($val['house_addr'], 'UTF8') > 50) {
        $list[$key]['house_addr'] = mb_substr($val['house_addr'], 0, 40, 'utf-8') . '...';
      }
      if (mb_strlen($val['number'], 'UTF8') > 10) {
        $list[$key]['number'] = mb_substr($val['number'], 0, 9, 'utf-8') . '...';
      }
    }
    $data['list'] = $list;

    $list1 = $this->bargain_model->get_all_by($cond_where, 0, $this->_total_count);
    foreach ($list1 as $key => $val) {
      $ids_arr[] = $val['id'];
    }
//    if (is_full_array($ids_arr)) {
//      $ids_str = implode(",", $ids_arr);

      //应收总计
//      $this->contract_flow_model->set_tbl('contract_should_flow');
//      $should_all_total = $this->contract_flow_model->get_total("c_id in ({$ids_str}) AND status < 2");
//      $data['should_money_total'] = $should_all_total['collect_money_total'] ? strip_end_0($should_all_total['collect_money_total']) : '0';

      //获取实收实付合计
//      $this->contract_flow_model->set_tbl('contract_actual_flow');
//      $actual_all_total = $this->contract_flow_model->get_total("c_id in ({$ids_str}) AND status < 2");
      //实收总计
//      $data['actual_money_total'] = $actual_all_total['collect_money_total'] ? strip_end_0($actual_all_total['collect_money_total']) : '0';
//      $data['remain_money_total'] = strip_end_0(floatval($data['should_money_total']) - floatval($data['actual_money_total']));
//    } else {
//      $data['commission_total_total'] = "0";
//      $data['collect_money_total'] = "0";
//      $data['remain_money_total'] = "0";
//    }
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
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_signing.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/transfer_list', $data);
  }

  /**
   * 录入预约
   * @access public
   * @return void
   */
  public function add_report()
  {
    $post_param = $this->input->post(NULL, TRUE);
    $agency = $this->agency_model->get_one_by(array('id' => $post_param['agency_id']));
    $broker = $this->broker_info_model->get_by_broker_id($post_param['broker_id']);

      $date = $post_param['signing_time'];
      //预约添加信息数组
      $data_info = array(
        'type' => intval($post_param['type']),
        'price_type' => intval($post_param['type']) == 2 ? 1 : 0,
        'house_addr' => trim($post_param['house_addr']),
        'house_id' => $post_param['house_id'] ? substr(trim($post_param['house_id']), 2) : '',
        'block_id' => intval($post_param['block_id']),
        'block_name' => trim($post_param['block_name']),
        'remarks' => trim($post_param['remarks']),
        'signing_time' => strtotime($date),
        'company_id_a' => intval($broker['company_id']),
        'agency_id_a' => intval($post_param['agency_id']),
        'agency_name_a' => $agency['name'],
        'broker_id_a' => intval($post_param['broker_id']),
        'broker_name_a' => $broker['truename'],
        'broker_tel_a' => trim($post_param['phone']),
        'createtime' => time(),
        'status' => 1
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
        $data['result'] = $this->signing_model->update_by_id($data_info, $post_param['id']);
        if ($data['result']) {
          $data['msg'] = '预约修改成功！';
          //操作日志
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '修改预约编号为' . $data_info['number'] . '的预约',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);
        } else {
          $data['msg'] = '预约修改失败！';
        }
      } else {
          //判断是否重复添加
          $where = "house_id = {$data_info['house_id']}";
          $reportInfo = $this->signing_model->get_one_by($where);
          if (!empty($reportInfo)) {
              $data['msg'] = '该房源已被预约，请不要重复预约！';
              echo json_encode($data);
              return;
          }
          $data_info['number'] = $number = $this->buildUniqidNo();
        $data_info['enter_company_id'] = $this->user_arr['company_id'];
        $data_info['enter_agency_id'] = $this->user_arr['agency_id'];
        $data_info['enter_broker_id'] = $this->user_arr['broker_id'];

        $data['result'] = $this->signing_model->add_info($data_info);
        if ($data['result']) {

          $data['msg'] = '预约添加成功！';
          //操作日志
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '新增预约编号为' . $data_info['number'] . '的预约',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);
          // 发送短信
          $this->load->library('Sms_codi', array('city' => "hz", 'jid' => '2', 'template' => 'contract_stage_n'), 'sms');
          $phone = $data_info["broker_tel_a"];
          $this->sms->send($phone, array('stage' => '申请已提交，请等待审核', 'order_sn' => $data_info["number"], 'signing_time' => date('Y-m-d H:i:s', $data_info['signing_time'])));
        } else {
          $data['msg'] = '预约新增失败！';
        }
      }
    echo json_encode($data);
  }

    /**
     * 生成唯一编号
     */
    protected function buildUniqidNo()
    {
        $no = date('YmdHis') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);;
        //检测是否存在
        $where = "number = '{$no}'";
        $info = $this->signing_model->get_one_by($where);;
        (!empty($info)) && $no = $this->build_order_no();
        return $no;
    }
  /**
   * 修改预约
   * @access public
   * @return void
   */
  public function edit()
  {
    $id = $this->input->get('id');
    $type = $this->input->get('type');
    $data['arr'] = $this->signing_model->get_by_id($id);
    $data['arr']['signing_time'] = date('Y-m-d H', $data['arr']['signing_time']);
      if ($type == 0) {
      $data['arr']['house_id'] = $data['arr']['house_id'] ? format_info_id($data['arr']['house_id'], 'sell') : '';
    } else {
      $data['arr']['house_id'] = $data['arr']['house_id'] ? format_info_id($data['arr']['house_id'], 'rent') : '';
    }
    $this->broker_info_model->set_select_fields(array('broker_id', 'truename'));
    $data['broker_list'] = $this->broker_info_model->get_by_agency_id($data['arr']['agency_id_a']);
    if (is_full_array($data['arr'])) {
      $data['result'] = 1;
    } else {
      $data['result'] = 0;
    }
    echo json_encode($data);
  }

  /**
   * 保存修改预约
   * @access public
   * @return void
   */
  public function save_edit_report()
  {
    $post_param = $this->input->post(NULL, TRUE);
    $id = $post_param['id'];
    $agency = $this->agency_model->get_one_by(array('id' => $post_param['agency_id']));
    $broker = $this->broker_info_model->get_by_broker_id($post_param['broker_id']);

    $date = $post_param['signing_time'];
    //检查预约编号唯一性
//    $where = "enter_company_id = {$this->user_arr['company_id']} and number = '{$post_param['number']}' and id != {$post_param['id']} and is_del = 0";
//    $result = $this->signing_model->get_one_by($where);
//    if (is_full_array($result)) {
//      $data['result'] = 0;
//      $data['msg'] = '公司内已有该预约编号！';
//    } else {//预约添加信息数组
    $data_info = array(
      'type' => intval($post_param['type']),
      'house_addr' => trim($post_param['house_addr']),
      'house_id' => substr(trim($post_param['house_id']), 2),
      'block_id' => intval($post_param['block_id']),
      'block_name' => trim($post_param['block_name']),
      'remarks' => trim($post_param['remarks']),
      'signing_time' => strtotime($date),
      'company_id_a' => intval($broker['company_id']),
      'agency_id_a' => intval($post_param['agency_id']),
      'agency_name_a' => $agency['name'],
      'broker_id_a' => intval($post_param['broker_id']),
      'broker_name_a' => $broker['truename'],
        'status' => 1,
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
    $data['result'] = $this->signing_model->update_by_id($data_info, $id);
    if ($data['result']) {
      $data['msg'] = '预约修改成功！';
      //操作日志
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'text' => '修改预约编号为' . $data_info['number'] . '的预约',
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      $this->operate_log_model->add_operate_log($add_log_param);
    } else {
      $data['msg'] = '预约修改失败！';
    }
//    }
    echo json_encode($data);
  }

  /**
   * 保存修改预约
   * @access public
   * @return void
   */
  public function update_report_status($status)
  {
    $id = $this->input->get('id');
    $info = $this->signing_model->get_by_id($id);
    $rs = $this->signing_model->update_by_id(array('status' => $status, 'is_check' => 1), $id);

    if ($rs) {
      //合同跟进——删除合同
      $data = array(
        'c_id' => $id,
        'type_name' => "预约签约",
        'content' => "本日对该预约信息进行处理。",
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'updatetime' => time()
      );
//      $this->contract_log_model->add_info($data);
//      $this->signing_model->set_select_fields(array('number'));
//      $result = $this->signing_model->get_by_id($id);
//      //操作日志
//      $add_log_param = array(
//        'company_id' => $this->user_arr['company_id'],
//        'agency_id' => $this->user_arr['agency_id'],
//        'broker_id' => $this->user_arr['broker_id'],
//        'broker_name' => $this->user_arr['truename'],
//        'type' => 35,
//        'text' => '转正合同编号为' . $result['number'] . '的交易合同。',
//        'from_system' => 1,
//        'from_ip' => get_ip(),
//        'mac_ip' => '127.0.0.1',
//        'from_host_name' => '127.0.0.1',
//        'hardware_num' => '测试硬件序列号',
//        'time' => time()
//      );
//      $this->operate_log_model->add_operate_log($add_log_param);

      //操作日志
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'text' => '预约编号为' . $info['number'] . '的预约，状态变更为' . $status . '。',
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      $this->operate_log_model->add_operate_log($add_log_param);
      echo json_encode(array('result' => '1', 'id' => $id));
    } else {
      echo json_encode(array('result' => '0'));
    }
  }

  /**
   * 录入和修改出售合同
   * @access public
   * @return void
   */
  public function modify_contract($type = 1, $id = 0)
  {
    $data = array();
    //菜单栏
    $data['user_tree_menu'] = $this->permission_tab_model->get_tree_menu('contract', 'contract_list');
    $data['id'] = $id;

    //获取基础配置信息
    $data['config'] = $this->house_config_model->get_config();
    //获取合同配置信息
    $data['contract_config'] = $this->contract_config_model->get_config();
    //合同详情
    $contract = $this->signing_model->get_by_id($id);
    if ($contract) {
      if ($type == 1) {
        $contract['house_id'] = $contract['house_id'] ? format_info_id($contract['house_id'], 'sell') : '';
        $contract['customer_id'] = $contract['customer_id'] ? format_info_id($contract['customer_id'], 'buy_customer') : '';
      } else {
        if ($contract['price_type'] == 2) {
          $contract['price'] = sprintf('%.2f', ($contract['price'] * 12));
        } elseif ($contract['price_type'] == 3) {
          $contract['price'] = sprintf('%.2f', ($contract['price'] / $contract['buildarea']));
        } elseif ($contract['price_type'] == 4) {
          $contract['price'] = sprintf('%.2f', ($contract['price'] / $contract['buildarea'] / 30));
        }
        $contract['house_id'] = $contract['house_id'] ? format_info_id($contract['house_id'], 'rent') : '';
        $contract['customer_id'] = $contract['customer_id'] ? format_info_id($contract['customer_id'], 'rent_customer') : '';
      }
    }
    //如果没有门店数据，默认经纪人本门店
    if (!$contract['agency_id_a']) {
      $contract['agency_id_a'] = $this->user_arr['agency_id'];
    }
    if (!$contract['agency_id_b']) {
      $contract['agency_id_b'] = $this->user_arr['agency_id'];
    }
    $data['type'] = $type;
    $data['contract'] = $contract;
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu_a = $this->signing_model->get_range_menu_by_role_level(
      $this->user_arr, $contract['agency_id_a']);
    //门店数据
    $data['agencys_a'] = $range_menu_a['agencys'];
    $data['brokers_a'] = $range_menu_a['brokers'];

    //获取访问菜单
    $range_menu_b = $this->signing_model->get_range_menu_by_role_level(
      $this->user_arr, $contract['agency_id_b']);
    //门店数据
    $data['agencys_b'] = $range_menu_b['agencys'];
    $data['brokers_b'] = $range_menu_b['brokers'];

    if ($id) {
      //页面标题
      $data['page_title'] = '修改合同';
    } else {
      //页面标题
      $data['page_title'] = '录入合同';
    }

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_signing.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
      . 'mls/js/v1.0/backspace.js');
    if ($type == 1) {
      $this->view("signing/contract_sell_add", $data);
    } else {
      $this->view("signing/contract_rent_add", $data);
    }
  }

  public function save_contract()
  {
    $post_param = $this->input->post(NULL, TRUE);
    //合同添加信息数组
    $type = intval($post_param['type']);
    $agency_a = $this->agency_model->get_one_by(array('id' => $post_param['agency_id_a']));
    $broker_a = $this->broker_info_model->get_by_broker_id($post_param['broker_id_a']);
    $agency_b = $this->agency_model->get_one_by(array('id' => $post_param['agency_id_b']));
    $broker_b = $this->broker_info_model->get_by_broker_id($post_param['broker_id_b']);
    $datainfo = array(
      'type' => $type,
      'number' => trim($post_param['number']),
      'signing_time' => strtotime($post_param['signing_time']),
      'house_addr' => trim($post_param['house_addr']),
      'house_id' => substr(trim($post_param['house_id']), 2),
      'buildarea' => sprintf('%.2f', $post_param['buildarea']),
      'price' => sprintf('%.2f', $post_param['price']),
      'sell_type' => intval($post_param['sell_type']),
      'is_cooperate' => intval($post_param['is_cooperate']),
      'order_sn' => trim($post_param['order_sn']),
      'sell_type' => intval($post_param['sell_type']),
      'block_id' => intval($post_param['block_id']),
      'block_name' => trim($post_param['block_name']),
      'owner' => trim($post_param['owner']),
      'owner_idcard' => trim($post_param['owner_idcard']),
      'owner_tel' => trim($post_param['owner_tel']),
      'company_id_a' => intval($broker_a['company_id']),
      'agency_id_a' => intval($post_param['agency_id_a']),
      'agency_name_a' => trim($agency_a['name']),
      'broker_id_a' => intval($post_param['broker_id_a']),
      'broker_name_a' => trim($broker_a['truename']),
      'broker_tel_a' => trim($post_param['broker_tel_a']),
      'customer_id' => substr(trim($post_param['customer_id']), 2),
      'customer' => trim($post_param['customer']),
      'customer_idcard' => trim($post_param['customer_idcard']),
      'customer_tel' => trim($post_param['customer_tel']),
      'company_id_b' => intval($broker_b['company_id']),
      'agency_id_b' => intval($post_param['agency_id_b']),
      'agency_name_b' => trim($agency_b['name']),
      'broker_id_b' => intval($post_param['broker_id_b']),
      'broker_name_b' => trim($broker_b['truename']),
      'broker_tel_b' => trim($post_param['broker_tel_b']),
      'owner_commission' => sprintf('%.2f', $post_param['owner_commission']),
      'customer_commission' => sprintf('%.2f', $post_param['customer_commission']),
      'other_income' => sprintf('%.2f', $post_param['other_income']),
      'commission_total' => sprintf('%.2f', $post_param['commission_total']),
      'status' => 2,
      'is_check' => 1,
      'remarks' => $post_param['remarks'],
      'createtime' => time()
    );
    if ($datainfo['is_cooperate'] == 0) {
      $datainfo['order_sn'] = "";
    }
    if ($type == 1) {
      $datainfo['buy_type_s'] = intval($post_param['buy_type']);
      $datainfo['shoufu'] = sprintf('%.2f', $post_param['shoufu']);
      $datainfo['loan'] = sprintf('%.2f', $post_param['loan']);
      $datainfo['business_tax'] = $post_param['business_tax'];
      $datainfo['tax'] = $post_param['tax'];
      $datainfo['tax_pay_type'] = intval($post_param['tax_pay_type']);
      $datainfo['owner_tax_total'] = sprintf('%.2f', $post_param['owner_tax_total']);
      $datainfo['customer_tax_total'] = sprintf('%.2f', $post_param['customer_tax_total']);
      $datainfo['divide_percent'] = sprintf('%.1f', $post_param['divide_percent']);
      $datainfo['divide_money'] = sprintf('%.2f', $post_param['divide_money']);
    } elseif ($type == 2) {
      $datainfo['buy_type_r'] = intval($post_param['buy_type']);
      $datainfo['price_type'] = intval($post_param['price_type']);
      if ($datainfo['price_type'] == 2) {
        $datainfo['price'] = sprintf('%.2f', ($post_param['price'] / 12));
      } elseif ($datainfo['price_type'] == 3) {
        $datainfo['price'] = sprintf('%.2f', ($post_param['price'] * $post_param['buildarea']));
      } elseif ($datainfo['price_type'] == 4) {
        $datainfo['price'] = sprintf('%.2f', ($post_param['price'] * $post_param['buildarea'] * 30));
      }
      $datainfo['start_time'] = $post_param['start_time'];
      $datainfo['end_time'] = $post_param['end_time'];
      $datainfo['deposit'] = sprintf('%.2f', $post_param['deposit']);
      $datainfo['list_items'] = trim($post_param['list_items']);
      $datainfo['hydropower'] = trim($post_param['hydropower']);
    }
    $submit_flag = $post_param['submit_flag'];
    if ($submit_flag == "add") {
      //检查合同编号唯一性
      $where = "enter_company_id = {$this->user_arr['company_id']} and number = '{$post_param['number']}' and is_del = 0";
      $result = $this->signing_model->get_one_by($where);
      if (is_full_array($result)) {
        $data['result'] = 'no';
        $data['msg'] = '公司内已有该合同编号的合同！';
        echo json_encode($data);
        exit();
      }
      $datainfo['enter_company_id'] = $this->user_arr['company_id'];
      $datainfo['enter_agency_id'] = $this->user_arr['agency_id'];
      $datainfo['enter_broker_id'] = $this->user_arr['broker_id'];
      //添加
      $id = $this->signing_model->add_info($datainfo);
      if ($id) {
        //合同跟进——添加业绩分成
        $add_data = array(
          'c_id' => $id,
          'type_name' => "合同录入",
          'content' => "本日对该合同进行录入。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $this->contract_log_model->add_info($add_data);
        //操作日志
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '录入合同编号为' . $datainfo['number'] . '的交易合同。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
        echo json_encode(array('result' => 'ok', "msg" => "合同录入成功"));
        exit;
      } else {
        echo json_encode(array('result' => 'ok', "msg" => "合同录入失败"));
        exit;
      }
    } else {
      //检查合同编号唯一性
      $where = "enter_company_id = {$this->user_arr['company_id']} and number = '{$post_param['number']}' and id != {$post_param['id']} and is_del = 0";
      $result = $this->signing_model->get_one_by($where);
      if (is_full_array($result)) {
        $data['result'] = 'no';
        $data['msg'] = '公司内已有该合同编号的合同';
        echo json_encode($data);
        exit();
      }
      //合同详情
      $data = $this->get_detail($post_param['id']);
      //添加
      $rs = $this->signing_model->update_by_id($datainfo, $post_param['id']);
      if ($rs) {
        $content = $this->modify_match($datainfo, $data, $data['config']);
        if ($content) {
          //合同跟进——添加业绩分成
          $add_data = array(
            'c_id' => $post_param['id'],
            'type_name' => "合同修改",
            'content' => "本日对该合同信息进行修改。" . $content,
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $this->contract_log_model->add_info($add_data);
        }
        //操作日志
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '修改合同编号为' . $datainfo['number'] . '的交易合同。' . $content,
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
        echo json_encode(array('result' => 'ok', "msg" => "合同修改成功"));
        exit;
      } else {
        echo json_encode(array('result' => 'no', "msg" => "合同修改失败"));
        exit;
      }
    }
  }

  public function contract_detail($id = 0)
  {
    header('Cache-control: private, must-revalidate');
    $data = array();
    //菜单栏
    $data['user_tree_menu'] = $this->permission_tab_model->get_tree_menu('contract', 'contract_list');
    //标题
    $data['page_title'] = '合同详情';
    $data['show_type'] = $type;
    //获取基本配置信息
    $data['base_config'] = $this->house_config_model->get_config();
    //获取合同配置信息
    $data['config'] = $this->contract_config_model->get_config();

    //权证步骤名配置
    $data['stage'] = $this->contract_warrant_model->get_all_stage();

    $contract = $this->signing_model->get_by_id($id);
    if ($contract) {
      if ($contract['type'] == 1) {
        $contract['house_id'] = $contract['house_id'] ? format_info_id($contract['house_id'], 'sell') : '';
        $contract['customer_id'] = $contract['customer_id'] ? format_info_id($contract['customer_id'], 'buy_customer') : '';
      } else {
        if ($contract['price_type'] == 2) {
          $contract['price'] = sprintf('%.2f', ($contract['price'] * 12));
        } elseif ($contract['price_type'] == 3) {
          $contract['price'] = sprintf('%.2f', ($contract['price'] / $contract['buildarea']));
        } elseif ($contract['price_type'] == 4) {
          $contract['price'] = sprintf('%.2f', ($contract['price'] / $contract['buildarea'] / 12));
        }
        $contract['house_id'] = $contract['house_id'] ? format_info_id($contract['house_id'], 'rent') : '';
        $contract['customer_id'] = $contract['customer_id'] ? format_info_id($contract['customer_id'], 'rent_customer') : '';
      }
      $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($contract['check_broker_id']);
      $contract['check_agency'] = $broker_info['agency_name'];
      $contract['check_broker'] = $broker_info['truename'];
    }
    $data['contract'] = $contract;

    //应收应付总数，如果未添加应收应付，则不显示添加实收按钮
    $this->contract_flow_model->set_tbl('contract_should_flow');
    $data['should_num'] = $this->contract_flow_model->count_by(array('c_id' => $id));
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->signing_model->get_range_menu_by_role_level(
      $this->user_arr, $this->user_arr['agency_id']);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];


    $divide_add_per = $this->broker_permission_model->check('51');

    $should_add_per = $this->broker_permission_model->check('55');

    $actual_add_per = $this->broker_permission_model->check('61');

    $contract_edit_per = $this->broker_permission_model->check('115');

    $data['auth'] = array(
      'edit' => $contract_edit_per, 'divide_add' => $divide_add_per, 'should_add' => $should_add_per, 'actual_add' => $actual_add_per
    );

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_signing.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    if ($contract['type'] == 1) {
      $this->view("signing/contract_sell_detail", $data);
    } else {
      $this->view("signing/contract_rent_detail", $data);
    }
  }

  public function transfer_detail($id = 0)
  {
    header('Cache-control: private, must-revalidate');
    $data = array();
    //菜单栏
    $data['user_tree_menu'] = $this->permission_tab_model->get_tree_menu('signing', 'transfer_list');
    //标题
    $data['page_title'] = '成交详情';
    //获取基本配置信息
    $data['base_config'] = $this->house_config_model->get_config();
    //获取合同配置信息
    $data['config'] = $this->contract_config_model->get_config();

    //权证步骤名配置
    $data['stage'] = $this->bargain_transfer_model->get_all_stage();

    $bargain = $this->bargain_model->get_by_id($id);
    if ($bargain) {

      $bargain['house_id'] = $bargain['house_id'] ? format_info_id($bargain['house_id'], 'sell') : '';
      $bargain['customer_id'] = $bargain['customer_id'] ? format_info_id($bargain['customer_id'], 'buy_customer') : '';

      $bargain['house_id'] = $bargain['house_id'] ? format_info_id($bargain['house_id'], 'rent') : '';
      $bargain['customer_id'] = $bargain['customer_id'] ? format_info_id($bargain['customer_id'], 'rent_customer') : '';
    }
    //$broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($bargain['check_broker_id']);
    // $bargain['check_agency'] = $broker_info['agency_name'];
    //  $bargain['check_broker'] = $broker_info['truename'];
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

    //应收应付总数，如果未添加应收应付，则不显示添加实收按钮
//    $this->contract_flow_model->set_tbl('contract_should_flow');
   // $data['should_num'] = $this->contract_flow_model->count_by(array('c_id' => $id));
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->bargain_model->get_range_menu_by_role_level(
      $this->user_arr, $this->user_arr['agency_id']);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];


    $divide_add_per = $this->broker_permission_model->check('51');

    $should_add_per = $this->broker_permission_model->check('55');

    $actual_add_per = $this->broker_permission_model->check('61');

    $contract_edit_per = $this->broker_permission_model->check('115');

    $data['auth'] = array(
      'edit' => $contract_edit_per, 'divide_add' => $divide_add_per, 'should_add' => $should_add_per, 'actual_add' => $actual_add_per
    );

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_signing.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');

    $this->view("signing/transfer_detail", $data);
  }

  /**
   * 选择房源
   * @access public
   * @return array
   */
  public function get_house($type = 1, $house_id = '')
  {
    //获取经纪人列表数组
    $this->load->model('api_broker_model');

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
    $this->load->model('agency_permission_model');
    $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_view_house');
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

    $cond_where = "`id` > 0 AND `agency_id` in (" . $all_access_agency_ids . ")";
    //默认公司
    $post_param['company_id'] = $this->user_arr['company_id'];
    $view_other_per_data = $this->broker_permission_model->check('1');
    $view_other_per = $view_other_per_data['auth'];
    if ($view_other_per) {
      //如果有权限，赋予初始查询条件
      if (!isset($post_param['agency_id'])) {
        $post_param['agency_id'] = $this->user_arr['agency_id'];
      }
      if (!isset($post_param['broker_id'])) {
        $post_param['broker_id'] = $this->user_arr['broker_id'];
      }

      $data['agencys'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
      if ($post_param['agency_id']) {
        $data['brokers'] = $this->api_broker_model->get_brokers_agency_id($post_param['agency_id']);
      } else {
        $data['brokers'] = array();
      }

    } else {
      //本人
      $post_param['broker_id'] = $this->user_arr['broker_id'];
      $data['agencys'] = $this->agency_model->get_all_by_agency_id($this->user_arr['agency_id']);
      $data['brokers'] = $this->broker_info_model->get_by_broker_id(array('broker_id' => $post_param['broker_id']));
    }
    array_unshift($data['agencys'], array('agency_id' => '', 'agency_name' => '不限'));
    array_unshift($data['brokers'], array('broker_id' => '', 'truename' => '不限'));
    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str1($post_param);
      //判断门店是否加入区域公盘
      $cond_where .= $cond_where_ext;
//获取当前门店所在公盘的所有门店
      $this->load->model('cooperate_district_model');
      $agency_indistrict = $this->cooperate_district_model->get_one_by_agency_id($this->user_arr['agency_id']);
      if (!empty($agency_indistrict)) {
          $cond_where_ext3 = $this->_get_cond_str3($post_param);//区域公盘房源查询时间
          $agency_ids = $this->cooperate_district_model->get_agency_by_agency_id($this->user_arr['agency_id']);
          $cond_where_ext3 .= " and agency_id in ($agency_ids)";
          $cond_where = "($cond_where) or ($cond_where_ext3)";
      }
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, 'AND');
    $cond_where = trim($cond_where);

    if ($type == 0) {
      //符合条件的总行数
      $this->_total_count = $this->sell_house_model->get_count_by_cond($cond_where);

      //获取列表内容
      $list = $this->sell_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit);

    } elseif ($type == 1) {
      //符合条件的总行数
      $this->_total_count = $this->rent_house_model->get_count_by_cond($cond_where);

      //获取列表内容
      $list = $this->rent_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit);
    } else {
      //符合条件的总行数
      $this->_total_count = $this->rent_house_model->get_count_by_cond($cond_where);

      //获取列表内容
      $list = $this->rent_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit);
    }

    if (is_full_array($list)) {
      foreach ($list as $key => $val) {

          if ($val['isshare_district'] == 1) {
              $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['district_broker_id']);
              $list[$key]['district_agency_name'] = $brokerinfo['agency_name'];
          } else {
              $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
              $list[$key]['agency_name'] = $brokerinfo['agency_name'];
          }
        if ($type == 1) {
          $list[$key]['house_id'] = format_info_id($val['id'], 'sell');
        } elseif ($type == 0) {
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
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
      $this->view("signing/signing_choose_house", $data);
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
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND `agency_id` = '" . $agency_id . "'";
    }

    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($broker_id) {
      $cond_where .= " AND `broker_id` = '" . $broker_id . "'";
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

    //合同编号
    if (isset($form_param['block_name']) && !empty($form_param['block_name'])) {
      $cond_where .= " AND block_name = '" . $form_param['block_name'] . "'";
    }

    //所属门店
    if (isset($form_param['agency_id']) && !empty($form_param['agency_id'])) {
      $cond_where .= " AND (agentid_a = {$form_param['agency_id']} or agentid_b = {$form_param['agency_id']})";
    }

    //所属经纪人
    if (isset($form_param['broker_id']) && !empty($form_param['broker_id'])) {
      $cond_where .= " AND (brokerid_a = {$form_param['broker_id']} or brokerid_b = {$form_param['broker_id']})";
    }

    return $cond_where;
  }

    public function _get_cond_str3($form_param)
    {

        $cond_where = '`isshare_district` = 1';
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

//        //公司
//        $company_id = isset($form_param['company_id']) ? intval($form_param['company_id']) : 0;
//        if ($company_id) {
//            $cond_where .= " AND `company_id` = '" . $company_id . "'";
//        }
//
//        //门店
//        $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
//        if ($agency_id) {
//            $cond_where .= " AND `agency_id` = '" . $agency_id . "'";
//        }
//
//        //员工
//        $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
//        if ($broker_id) {
//            $cond_where .= " AND `broker_id` = '" . $broker_id . "'";
//        }
//
//        //姓名
//        $truename = isset($form_param['truename']) ? trim($form_param['truename']) : "";
//        if ($truename) {
//            $cond_where .= " AND truename LIKE '%" . $truename . "%'";
//        }
//
//        //客户编号
//        $customer_id = isset($form_param['customer_id']) ? trim($form_param['customer_id']) : "";
//        if ($customer_id) {
//            $customer_id = substr($customer_id, 2);
//            $cond_where .= " AND id = '" . $customer_id . "'";
//        }

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
      $data['agency'] = $this->agency_model->get_children_by_company_id($this->user_arr['company_id']);

      //条件-分店
      $agency_id = isset($post_param['agency_id']) ? intval($post_param['agency_id']) : 0;
      if ($agency_id) {
        //获取经纪人列表数组
        $this->load->model('api_broker_model');
        $brokers = $this->api_broker_model->get_brokers_agency_id($agency_id);
        $data['brokers'] = $brokers;
      }

      if (is_full_array($data['agency'])) {
        foreach ($data['agency'] as $key => $val) {
          $agencyid_arr[] = $val['id'];
        }
        $agency_str = implode(',', $agencyid_arr);
      }
      $cond_where .= " and (agentid_a in ({$agency_str}) or agentid_b in ({$agency_str}))";
    } elseif ($level == 6) {
      $data['agency'][0] = $this->agency_model->get_by_id($this->user_arr['agency_id']);
      $data['brokers'] = $this->api_broker_model->get_brokers_agency_id($this->user_arr['agency_id']);
      $cond_where .= " and (agentid_a = {$this->user_arr['agency_id']} or agentid_b = {$this->user_arr['agency_id']})";
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

    $select_field = array('order_sn', 'esta', 'rowid', 'broker_name_a', 'broker_name_b', 'agency_name_a', 'agency_name_b');
    //获取列表内容
    $list = $this->cooperate_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit);
    if (is_full_array($list)) {
      $select_field = array('district_id', 'street_id', 'price', 'block_name', 'room', 'hall', 'toilet', 'fitment', 'forward', 'buildarea');
      if ($type == 1) {
        foreach ($list as $key => $val) {
          $this->agency_model->set_select_fields(array('name'));
          $agency_a = $this->agency_model->get_by_id($val['agentid_a']);
          $list[$key]['agent_name_a'] = $agency_a['name'];
          $agency_b = $this->agency_model->get_by_id($val['agentid_b']);
          $list[$key]['agent_name_b'] = $agency_b['name'];

          $this->sell_house_model->set_search_fields($select_field);
          $this->sell_house_model->set_id($val['rowid']);
          $list[$key]['house_info'] = $this->sell_house_model->get_info_by_id();
        }
      } else {
        foreach ($list as $key => $val) {
          $this->agency_model->set_select_fields(array('name'));
          $agency_a = $this->agency_model->get_by_id($val['agentid_a']);
          $list[$key]['agent_name_a'] = $agency_a['name'];
          $agency_b = $this->agency_model->get_by_id($val['agentid_b']);
          $list[$key]['agent_name_b'] = $agency_b['name'];

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
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,' . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view("signing/contract_choose_cooperate", $data);
  }

  /**
   * 选择客源
   * @access public
   * @return array
   */
  public function get_customer($type = 1, $customer_id = '')
  {
    //获取经纪人列表数组
    $this->load->model('api_broker_model');

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
    $this->load->model('agency_permission_model');
    $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_view_house');
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

    $cond_where = "`id` > 0 AND `agency_id` in (" . $all_access_agency_ids . ")";
    //默认公司
    $post_param['company_id'] = $this->user_arr['company_id'];
    $view_other_per_data = $this->broker_permission_model->check('1');
    $view_other_per = $view_other_per_data['auth'];
    if ($view_other_per) {
      //如果有权限，赋予初始查询条件
      if (!isset($post_param['agency_id'])) {
        $post_param['agency_id'] = $this->user_arr['agency_id'];
      }
      if (!isset($post_param['broker_id'])) {
        $post_param['broker_id'] = $this->user_arr['broker_id'];
      }

      $data['agencys'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);
      if ($post_param['agency_id']) {
        $data['brokers'] = $this->api_broker_model->get_brokers_agency_id($post_param['agency_id']);
      }

    } else {
      //本人
      $post_param['broker_id'] = $this->user_arr['broker_id'];
      $data['agencys'] = $this->agency_model->get_all_by_agency_id($this->user_arr['agency_id']);
      $data['brokers'] = $this->broker_info_model->get_by_broker_id(array('broker_id' => $post_param['broker_id']));
    }
    array_unshift($data['agencys'], array('agency_id' => '0', 'agency_name' => '不限'));
    array_unshift($data['brokers'], array('broker_id' => '0', 'truename' => '不限'));

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
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
        $list[$key]['broker_name'] = $brokerinfo['truename'];
        $list[$key]['agency_name'] = $brokerinfo['agency_name'];
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
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,' . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view("signing/contract_choose_customer", $data);
  }

  //修改合同内容匹配
  public function modify_match($data1, $data2, $config)
  {
    $data = array_diff_assoc($data1, $data2);
    $str = '';
    $type = intval($data2['type']);
    if ($type == 2) {
      if (array_key_exists("price", $data) || array_key_exists("price_type", $data)) {
        $str .= "“成交租金”由“" . strip_end_0($data2['price']) . $config['price_type'][$data2['price_type']] . "”改为“" . strip_end_0($data1['price']) . $config['price_type'][$data1['price_type']] . "”；";
      }
    }
    $base_config = $this->house_config_model->get_config();
    $config = $this->contract_config_model->get_config();
    foreach ($data as $key => $val) {
      $this->load->model('api_broker_model');
      switch ($key) {
        case 'signing_time':
          $str .= "“签约日期”由“{$data2['signing_time']}”改为“{$data1['signing_time']}”；";
          break;
        case 'buildarea':
          $str .= "“面积”由“" . strip_end_0($data2['buildarea']) . "m²”改为“" . strip_end_0($data1['buildarea']) . "m²”；";
          break;
        case 'house_id':
          if ($type == 1) {
            $str1 = "CS";
          } elseif ($type == 2) {
            $str1 = "CZ";
          }
          $str .= "“房源编号”由“{$str1}{$data2['house_id']}”改为“{$str1}{$data1['house_id']}”；";
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
        case 'broker_id_a':
          $info1 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['broker_id_a']);
          $info2 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['broker_id_a']);
          $str .= "“卖方签约人”由“" . $info2['agency_name'] . "-" . $info2['truename'] . "”改为“" . $info1['agency_name'] . "-" . $info1['truename'] . "”；";
          break;
        case 'broker_tel_a':
          $str .= "“卖方签约人电话”由“{$data2['broker_tel_a']}”改为“{$data1['broker_tel_a']}”；";
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
        case 'broker_id_b':
          $info1 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['broker_id_b']);
          $info2 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['broker_id_b']);
          $str .= "“买方签约人”由“" . $info2['agency_name'] . "-" . $info2['truename'] . "”改为“" . $info1['agency_name'] . "-" . $info1['truename'] . "”；";
          break;
        case 'broker_tel_b':
          $str .= "“买方签约人电话”由“{$data2['broker_tel_b']}”改为“{$data1['broker_tel_b']}”；";
          break;
        case 'remarks':
          $str .= "“合同备注”由“{$data2['remarks']}”改为“{$data1['remarks']}”；";
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
   * 取消预约
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function cancel_report()
  {
    return $this->update_report_status(5);
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
    $rs = $this->signing_model->update_by_id(array('is_del' => 1, 'is_template' => 0), $id);
    if ($rs) {
      //合同跟进——取消合同
      $data = array(
        'c_id' => $id,
        'type_name' => "合同删除",
        'content' => "本日对该合同信息进行删除。",
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'updatetime' => time()
      );
      $this->contract_log_model->add_info($data);
      //操作日志
      $info = $this->signing_model->get_by_id($id);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'text' => '删除合同编号为' . $info['number'] . '的交易合同。',
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      $this->operate_log_model->add_operate_log($add_log_param);
      //删除合同相关的权证，业绩分成、应收应付，实收实付
      $this->contract_divide_model->del_by_cid($id);//业绩
      $this->contract_log_model->del_by_cid($id);//跟进
      $this->contract_flow_model->set_tbl('contract_actual_flow');
      $this->contract_flow_model->del_by_cid($id);//实收实付
      $this->contract_flow_model->set_tbl('contract_should_flow');
      $this->contract_flow_model->del_by_cid($id);//应收应付
      $this->contract_warrant_model->del_by_cid($id);//权证
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
    $rs = $this->signing_model->update_by_id(array('is_check' => 4), $id);
    if ($rs) {
      //合同跟进——删除合同
      $data = array(
        'c_id' => $id,
        'type_name' => "合同作废",
        'content' => "对该合同进行作废，合同已终止。",
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'updatetime' => time()
      );
      $this->contract_log_model->add_info($data);
      //操作日志
      $info = $this->signing_model->get_by_id($id);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'text' => '作废合同编号为' . $info['number'] . '的交易合同。',
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      $this->operate_log_model->add_operate_log($add_log_param);
      echo json_encode(array('result' => '1'));
    } else {
      echo json_encode(array('result' => '0'));
    }
  }

  /**
   * 合同审核
   * @access  public
   * @param   void
   * @return  void
   */
  public function contract_review($type = 1)
  {
    //模板使用数据
    $data = array();

    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;

    //页面标题
    $data['page_title'] = '交易合同审核';

    //获取合同配置信息
    $data['config'] = $this->contract_config_model->get_config();

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
      $post_param['enter_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['enter_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['enter_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['enter_broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->signing_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['agency_id_a']);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];

    //权限
    $contract_review_per = $this->broker_permission_model->check('118');
    $contract_fanreview_per = $this->broker_permission_model->check('119');
    $data['auth'] = array(
      'review' => $contract_review_per, 'fanreview' => $contract_fanreview_per
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
    $this->_total_count = $this->signing_model->count_by($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->signing_model->get_all_by($cond_where, $this->_offset, $this->_limit, $order_key = 'is_check', $order_by = 'ASC');

    $data['list'] = $list;
    //查询所有待审核合同的数量
    $cond_where1 = $cond_where . ' AND is_check = 1';
    //符合条件的总行数
    $data['total'] = $this->signing_model->count_by($cond_where1);
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
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_signing.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_review', $data);
  }

  /**
   * 合同审核
   */
  public function sure_review()
  {
    $contract_id = $this->input->post('id');
    $review_type = intval($this->input->post('review_type'));
    $review_remark = trim($this->input->post('review_remark'));

    $update_data = array(
      'is_check' => $review_type,
      'check_time' => time(),
      'check_remark' => $review_remark,
      'check_agency_id' => $this->user_arr['agency_id'],
      'check_broker_id' => $this->user_arr['broker_id']
    );
    //更改合同表中合同状态
    $result = $this->signing_model->update_by_id($update_data, $contract_id);
    //审核通过后发送消息
    if ($result) {
      //记入日志
      if ($review_type == 2) {
        $add_data = array(
          'c_id' => $contract_id,
          'type_name' => "合同审核",
          'content' => "批准了该合同，合同审核通过。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
      } elseif ($review_type == 3) {
        $add_data = array(
          'c_id' => $contract_id,
          'type_name' => "合同审核",
          'content' => "拒绝该合同，合同审核未通过。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
      }
      $this->contract_log_model->add_info($add_data);
      //操作日志
      $info = $this->signing_model->get_by_id($contract_id);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'text' => '审核合同编号为' . $info['number'] . '的交易合同。',
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      $this->operate_log_model->add_operate_log($add_log_param);
      $return_data['result'] = 'ok';
    } else {
      $return_data['result'] = 'no';
    }
    echo json_encode($return_data);
  }

  /**
   * 合同审核
   */
  public function cancel_review()
  {
    $contract_id = $this->input->post('id');
    $update_data = array(
      'is_check' => 1
    );
    //更改合同表中合同状态
    $result = $this->signing_model->update_by_id($update_data, $contract_id);
    //审核通过后发送消息
    if ($result) {
      //记入日志
      $add_data = array(
        'c_id' => $contract_id,
        'type_name' => "合同审核",
        'content' => "对该合同进行反审核操作。",
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'updatetime' => time()
      );
      $this->contract_log_model->add_info($add_data);
      //操作日志
      $info = $this->signing_model->get_by_id($contract_id);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'text' => '反审核合同编号为' . $info['number'] . '的交易合同。',
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      $this->operate_log_model->add_operate_log($add_log_param);
      $return_data['result'] = 'ok';
    } else {
      $return_data['result'] = 'no';
    }
    echo json_encode($return_data);
  }

  /**
   * 获取合同详情
   * @access public
   * @return array
   */
  public function get_detail($id)
  {
    $data = $this->signing_model->get_info_by_id($id);
    if (is_full_array($data)) {
      $config = $this->contract_config_model->get_config();
      $data['config'] = $config;
    }
    return $data;
  }

  /**
   * 获取模板详情
   * @access public
   * @return array
   */
  public function get_warrant()
  {
    $id = $this->input->post(id);
    //权证流程
    $warrant_step = $this->contract_warrant_model->get_all_by_cid($id);
    //权证配置项
    $stage_conf = $this->contract_warrant_model->get_stage_conf();
    //权证步骤名配置
    $data['stage'] = $this->contract_warrant_model->get_all_stage();
    if (is_full_array($warrant_step)) {
      foreach ($warrant_step as $key => $val) {
        $arr = array();
        $stage_name = explode(',', $val['stage_id']);
        foreach ($stage_name as $k => $v) {
          $arr[] = $data['stage'][$v]['stage_name'];
          $warrant_step[$key]['stage_name1'] = implode('，', $arr);
          if (count($arr) > 1) {
            $warrant_step[$key]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'] . '...';
          } else {
            $warrant_step[$key]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'];
          }
        }
        $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
        $warrant_step[$key]['broker_name'] = $broker_info['truename'];
        $warrant_step[$key]['agency_name'] = $broker_info['agency_name'];
        $broker_info1 = $this->api_broker_model->get_baseinfo_by_broker_id($val['complete_broker_id']);
        $warrant_step[$key]['complete_broker_name'] = $broker_info1['truename'];
        $warrant_step[$key]['complete_agency_name'] = $broker_info1['agency_name'];
        $warrant_step[$key]['key'] = $key + 1;
        $warrant_step[$key]['step'] = $stage_conf[$key + 1]['text'];
      }

      $return_data['warrant_list'] = $warrant_step;
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
    $get_total = $this->contract_divide_model->get_total($c_id);
    if ($get_total['percent_total'] == 100) {
      $result = $this->contract_divide_model->update_complete($c_id);
      $update_data = array('is_commission' => 1, 'commission_time' => time());
      $this->signing_model->update_by_id($update_data, $c_id);
      $add_data = array(
        'c_id' => $c_id,
        'type_name' => "业绩分成",
        'content' => "对所有业绩分成完成结佣。",
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'updatetime' => time()
      );
      $this->contract_log_model->add_info($add_data);
      $return_data['msg'] = "已完成结佣！";
      $return_data['result'] = 'ok';
      //操作日志
      $info = $this->signing_model->get_by_id($contract_id);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'text' => '对合同编号为' . $info['number'] . '的交易合同进行结佣。',
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      $this->operate_log_model->add_operate_log($add_log_param);
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
    $data['divide_list'] = $this->contract_divide_model->get_by_id($id);
    $this->broker_info_model->set_select_fields(array('broker_id', 'truename'));
    $data['broker_list'] = $this->broker_info_model->get_by_agency_id($data['divide_list']['agency_id']);
    $data['achieve_broker_list_a'] = $this->broker_info_model->get_by_agency_id($data['divide_list']['achieve_agency_id_a']);
    $data['achieve_broker_list_b'] = $this->broker_info_model->get_by_agency_id($data['divide_list']['achieve_agency_id_b']);
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
   * @param  int $contract_id
   * @param  int $is_ajax
   * @return void
   */
  public function divide_manage()
  {
    $post_param = $this->input->post(NULL, TRUE);
    //业绩id
    $divide_id = $post_param['divide_id'];
    $c_id = $post_param['c_id'];
    //获取合同配置项
    $config = $this->contract_config_model->get_config();

    $info1 = $this->api_broker_model->get_baseinfo_by_broker_id($post_param['broker_id']);
    $info2 = $this->api_broker_model->get_baseinfo_by_broker_id($post_param['achieve_broker_id_a']);
    $info3 = $this->api_broker_model->get_baseinfo_by_broker_id($post_param['achieve_broker_id_b']);
    $info4 = $this->api_broker_model->get_baseinfo_by_broker_id($this->user_arr['broker_id']);
    $datainfo = array(
      "c_id" => $c_id,
      "company_id" => intval($info1['company_id']),
      "agency_id" => intval($post_param['agency_id']),
      "agency_name" => trim($info1['agency_name']),
      "broker_id" => intval($post_param['broker_id']),
      "broker_name" => trim($info1['truename']),
      "divide_price" => trim($post_param['divide_price']),
      "percent" => sprintf('%.2f', $post_param['divide_percent']),
      "divide_type" => intval($post_param['divide_type']),
      "achieve_company_id" => $this->user_arr['company_id'],
      "achieve_agency_id_a" => $post_param['achieve_agency_id_a'],
      "achieve_agency_name_a" => $info2['agency_name'],
      "achieve_broker_id_a" => $post_param['achieve_broker_id_a'],
      "achieve_broker_name_a" => $info2['truename'],
      "achieve_agency_id_b" => $post_param['achieve_agency_id_b'],
      "achieve_agency_name_b" => $info3['agency_name'],
      "achieve_broker_id_b" => $post_param['achieve_broker_id_b'],
      "achieve_broker_name_b" => $info3['truename'],
      "entry_time" => time(),
      'entry_company_id' => $this->user_arr['company_id'],
      'entry_agency_id' => $this->user_arr['agency_id'],
      'entry_agency_name' => $info4['agency_name'],
      "entry_broker_id" => $this->user_arr['broker_id'],
      "entry_broker_name" => $this->user_arr['truename']
    );
    if ($divide_id) {
      $old_data = $this->contract_divide_model->get_by_id($divide_id);
      //修改
      $rs = $this->contract_divide_model->update_by_id($datainfo, $divide_id);
      $old_total = $this->contract_divide_model->get_total($c_id);
      $old_total['percent_total'] = $old_total['percent_total'] ? $old_total['percent_total'] : '0';
      $new_total = intval($old_total['percent_total'] + $datainfo['percent'] - 100);
      if ($new_total < 100) {
        if ($rs) {
          $str = $this->modify_divide_match($datainfo, $old_data);
          $add_data = array(
            'c_id' => $c_id,
            'type_name' => "业绩分成",
            'content' => "修改业绩分成，" . $str,
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $this->contract_log_model->add_info($add_data);
          $return_data['msg'] = '修改业绩分成成功';
          $return_data['result'] = 'ok';
          //操作日志
          $info = $this->signing_model->get_by_id($c_id);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '修改合同编号为' . $info['number'] . '的交易合同的业绩分成。' . $str,
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);
        } else {
          $return_data['msg'] = '修改业绩分成失败';
          $return_data['result'] = 'no';
        }
      } else {
        $return_data['msg'] = '分配比例总和不能超过100%';
        $return_data['result'] = 'no';
      }
    } else {
      $contract = $this->signing_model->get_by_id($c_id);
      $datainfo['type'] = $contract['type'];
      $old_total = $this->contract_divide_model->get_total($c_id);
      $old_total['percent_total'] = $old_total['percent_total'] ? $old_total['percent_total'] : '0';
      $new_total = intval($old_total['percent_total'] + $datainfo['percent'] - 100);
      if ($new_total < 100) {
        //添加
        $id = $this->contract_divide_model->add_info($datainfo);
        if ($id) {
          //合同跟进——添加业绩分成
          $add_data = array(
            'c_id' => $c_id,
            'type_name' => "业绩分成",
            'content' => "添加业绩分成，归属人" . $datainfo['broker_name'] . ',占比' . $datainfo['percent_total'] . "%。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $this->contract_log_model->add_info($add_data);
          $return_data['msg'] = '添加业绩分成成功';
          $return_data['result'] = 'ok';
          $return_data['num'] = $this->contract_divide_model->count_by(array('c_id' => $c_id));
          //操作日志
          $info = $this->signing_model->get_by_id($c_id);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '添加合同编号为' . $info['number'] . '的交易合同的业绩分成。',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);
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
    $data = $this->contract_divide_model->get_by_id($id);
    $result = $this->contract_divide_model->del_by_id($id);
    if (!empty($result) && is_int($result)) {
      $add_data = array(
        'c_id' => $c_id,
        'type_name' => "'业绩分成",
        'content' => "'删除业绩分成，归属人，{$data['broker_name']}。",
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'updatetime' => time()
      );
      $return_data['msg'] = '删除业绩分成成功！';
      $this->contract_log_model->add_info($add_data);
      $return_data['result'] = 'ok';
      $return_data['num'] = $this->contract_divide_model->count_by(array('c_id' => $c_id));
      //操作日志
      $info = $this->signing_model->get_by_id($c_id);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'text' => '删除合同编号为' . $info['number'] . '的交易合同的业绩分成',
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      $this->operate_log_model->add_operate_log($add_log_param);
    } else {
      $return_data['msg'] = '删除业绩分成失败！';
      $return_data['result'] = 'no';
    }
    $total = $this->contract_divide_model->get_total($c_id);
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
    $stage_conf = $this->contract_warrant_model->get_stage_conf();
    //权证步骤名配置
    $stage = $this->contract_warrant_model->get_all_stage();
    $data['key'] = $stage_conf[$key]['text'];
    $data['warrant_list'] = $this->warrant_model->get_step_by_id($id);
    $data['warrant_list']['stage_id'] = explode(',', $data['warrant_list']['stage_id']);
    foreach ($data['warrant_list']['stage_id'] as $key => $val) {
      $stage_name[] = $stage[$val]['stage_name'];
    }
    $data['warrant_list']['stage_name'] = implode(',', $stage_name);
    if ($data['warrant_list']) {
      $data['result'] = 1;
    } else {
      $data['result'] = 0;
    }
    echo json_encode($data);
  }

  //权证详情的权证模板步骤添加
  public function warrant_template_add($id = 1, $key1 = 1, $c_id = 0)
  {
    $data['page_title'] = '编辑权证流程模板';
    $data['stage'] = $this->warrant_model->get_all_stage();
    $post_param = $this->input->post(null, true);
    //key为传参，决定页面执行不同的js
    $data['key1'] = $key1;
    //该模板id
    $data['id'] = $id;
    //合同id
    $data['c_id'] = $c_id;
    //获得该模板下所有步骤
    $template_info = $this->warrant_model->get_temp_by_id($id);

    //根据模板的ID读取步骤
    $steps = $this->warrant_model->get_step_by_template_id($id);

    //权证配置项
    $data['stage_conf'] = $this->contract_warrant_model->get_stage_conf();
    //权证步骤名配置
    $data['stage'] = $this->contract_warrant_model->get_all_stage();

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
          $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
          $steps[$k]['broker_name'] = $broker_info['truename'];
          $steps[$k]['agency_name'] = $broker_info['agency_name'];
        }
      }
    }
    $template_info['step'] = $steps;
    $data['template'] = $template_info;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/warrant_add_template', $data);
  }


  public function warrant_template($id = 1)
  {
    $data['page_title'] = '选择权证流程模板';
    $data['stage'] = $this->warrant_model->get_all_stage();
    $post_param = $this->input->post(null, true);

    $where = "(type = 0 and company_id = 0) or (type = 1 and company_id = {$this->user_arr['company_id']})";

    //获得公司下所有模板
    $data['sys_temps'] = $this->warrant_model->get_all_temps($where, $this->_offset, $this->_limit, 'id', 'ASC');

    //根据模板的ID读取步骤
    $steps = $this->warrant_model->get_step_by_template_id($id);

    //权证配置项
    $data['stage_conf'] = $this->contract_warrant_model->get_stage_conf();
    //权证步骤名配置
    $data['stage'] = $this->contract_warrant_model->get_all_stage();

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
          $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
          $steps[$k]['broker_name'] = $broker_info['truename'];
          $steps[$k]['agency_name'] = $broker_info['agency_name'];
        }
      }
    }
    $data['template_steps'] = $steps;
    $data['id'] = $id;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_template', $data);
  }

  /**
   * 跳转合同详情的权证页面
   */
  public function contract_warrant_manage($id)
  {
    $data['contract'] = $this->signing_model->get_by_id($id);
    //获取配置项
    $data['config'] = $this->contract_config_model->get_config();
    //权证流程
    $warrant_step = $this->contract_warrant_model->get_all_by_cid($id);
    //权证配置项
    $data['stage_conf'] = $this->contract_warrant_model->get_stage_conf();
    //权证步骤名配置
    $data['stage'] = $this->contract_warrant_model->get_all_stage();
    if (is_full_array($warrant_step)) {
      foreach ($warrant_step as $key => $val) {
        $arr = array();
        $stage_name = explode(',', $val['stage_id']);
        foreach ($stage_name as $k => $v) {
          $arr[] = $data['stage'][$v]['stage_name'];
          $warrant_step[$key]['stage_name1'] = implode('，', $arr);
          if (count($arr) > 1) {
            $warrant_step[$key]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'] . '...';
          } else {
            $warrant_step[$key]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'];
          }
        }
        $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
        $warrant_step[$key]['broker_name'] = $broker_info['truename'];
        $warrant_step[$key]['agency_name'] = $broker_info['agency_name'];
        $broker_info1 = $this->api_broker_model->get_baseinfo_by_broker_id($val['complete_broker_id']);
        $warrant_step[$key]['complete_broker_name'] = $broker_info1['truename'];
        $warrant_step[$key]['complete_agency_name'] = $broker_info1['agency_name'];
        if (mb_strlen($val['remarks'], 'UTF8') > 10) {
          $warrant_step['should_flow'][$key]['remarks'] = mb_substr($val['remarks'], 0, 9, 'utf-8') . '...';
        }
      }
    }
    $data['warrant_step'] = $warrant_step;
    $data['warrant_step_total'] = $this->contract_warrant_model->count_by(array('contract_id' => $id));

    $warrant_add_per = $this->broker_permission_model->check('68');
    $warrant_edit_per = $this->broker_permission_model->check('69');
    $warrant_delete_per = $this->broker_permission_model->check('70');
    $warrant_complete_per = $this->broker_permission_model->check('71');
    $warrant_complete_all_per = $this->broker_permission_model->check('71');

    $data['auth'] = array(
      'warrant_add' => $warrant_add_per, 'warrant_edit' => $warrant_edit_per,
      'warrant_delete' => $warrant_delete_per, 'warrant_complete' => $warrant_complete_per,
      'warrant_complete_all' => $warrant_complete_all_per
    );

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_warrant_manage', $data);
  }

  /**
   * 跳转合同详情的权证页面
   */
  public function transfer_manage($id)
  {
    $data['contract'] = $this->bargain_model->get_by_id($id);
    //获取配置项
    $data['config'] = $this->bargain_config_model->get_config();
    //权证流程
    $warrant_step = $this->bargain_transfer_model->get_all_by_cid($id);
    //权证配置项
    $data['stage_conf'] = $this->bargain_transfer_model->get_stage_conf();
    //权证步骤名配置
    $data['stage'] = $this->bargain_transfer_model->get_all_stage();
    if (is_full_array($warrant_step)) {
      foreach ($warrant_step as $key => $val) {
        $arr = array();
        $stage_name = explode(',', $val['stage_id']);
        foreach ($stage_name as $k => $v) {
          $arr[] = $data['stage'][$v]['stage_name'];
          $warrant_step[$key]['stage_name1'] = implode('，', $arr);
          if (count($arr) > 1) {
            $warrant_step[$key]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'] . '...';
          } else {
            $warrant_step[$key]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'];
          }
        }

//        $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
//        $warrant_step[$key]['broker_name'] = $broker_info['truename'];
//        $warrant_step[$key]['agency_name'] = $broker_info['agency_name'];
//        $broker_info1 = $this->api_broker_model->get_baseinfo_by_broker_id($val['complete_broker_id']);
//        $warrant_step[$key]['complete_broker_name'] = $broker_info1['truename'];
//        $warrant_step[$key]['complete_agency_name'] = $broker_info1['agency_name'];

        $signatory_info = $this->signatory_info_model->get_one_by(array('signatory_id'=>$val['complete_signatory_id']));
        $warrant_step[$key]['complete_signatory_name'] = $signatory_info['truename'];
        if (mb_strlen($val['remarks'], 'UTF8') > 10) {
          $warrant_step['should_flow'][$key]['remarks'] = mb_substr($val['remarks'], 0, 9, 'utf-8') . '...';
        }
      }
    }
    $data['warrant_step'] = $warrant_step;
    $data['warrant_step_total'] = $this->bargain_transfer_model->count_by(array('bargain_id' => $id));

    $warrant_add_per = $this->broker_permission_model->check('68');
    $warrant_edit_per = $this->broker_permission_model->check('69');
    $warrant_delete_per = $this->broker_permission_model->check('70');
    $warrant_complete_per = $this->broker_permission_model->check('71');
    $warrant_complete_all_per = $this->broker_permission_model->check('71');

    $data['auth'] = array(
      'warrant_add' => $warrant_add_per, 'warrant_edit' => $warrant_edit_per,
      'warrant_delete' => $warrant_delete_per, 'warrant_complete' => $warrant_complete_per,
      'warrant_complete_all' => $warrant_complete_all_per
    );

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/transfer_manage', $data);
  }

  /**
   * 跳转合同实收实付的权证页面
   */
  public function contract_actual_manage($id)
  {
    //获取合同详情
    $data['contract'] = $this->signing_model->get_by_id($id);
    //获取配置项
    $data['config'] = $this->contract_config_model->get_config();
    //实收实付
    $this->contract_flow_model->set_tbl('contract_actual_flow');
    $data['actual_flow'] = $this->contract_flow_model->get_list_by_cond1(array('c_id' => $id));
    if (is_full_array($data['actual_flow'])) {
      foreach ($data['actual_flow'] as $key => $val) {
        if (mb_strlen($val['remark'], 'UTF8') > 8) {
          $data['actual_flow'][$key]['remark'] = mb_substr($val['remark'], 0, 7, 'UTF8') . '...';
        }
      }
    }
    //收付金额
    $get_total2 = $this->contract_flow_model->get_total(array('c_id' => $id));
    $data['actual_collect_money_total'] = $get_total2['collect_money_total'];
    $data['actual_pay_money_total'] = $get_total2['pay_money_total'];


    $actual_add_per = $this->broker_permission_model->check('61');
    $actual_edit_per = $this->broker_permission_model->check('62');
    $actual_delete_per = $this->broker_permission_model->check('63');
    $actual_complete_per = $this->broker_permission_model->check('67');

    $data['auth'] = array(
      'actual_add' => $actual_add_per, 'actual_edit' => $actual_edit_per,
      'actual_delete' => $actual_delete_per, 'actual_complete' => $actual_complete_per
    );

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_actual_manage', $data);
  }


  /**
   * 跳转合同实收实编辑页面
   */
  public function contract_actual_modify($c_id = 0, $id = 0)
  {
    $data['id'] = $id;
    $data['c_id'] = $c_id;
    $data['config'] = $this->contract_config_model->get_config();

    $tbl = 'contract_actual_flow';
    $this->contract_flow_model->set_tbl($tbl);
    if ($id) {
      $data['flow_list'] = $this->contract_flow_model->get_by_id($id);
      $agency_id = $data['flow_list']['flow_agency_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->signing_model->get_range_menu_by_role_level(
      $this->user_arr, $agency_id);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];

    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_signing.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_actual_modify', $data);
  }

  /**
   * 跳转合同实收实详情页面
   */
  public function contract_actual_detail($id = 0)
  {
    $data['id'] = $id;
    $data['config'] = $this->contract_config_model->get_config();

    $tbl = 'contract_actual_flow';
    $this->contract_flow_model->set_tbl($tbl);
    $data['detail'] = $this->contract_flow_model->get_by_id($id);
    $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($data['detail']['flow_broker_id']);
    $data['detail']['flow_agency_name'] = $broker_info['agency_name'];
    $data['detail']['flow_broker_name'] = $broker_info['truename'];
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_actual_detail', $data);
  }


  /**
   * 跳转合同应收应付编辑页面
   */
  public function contract_should_modify($c_id = 0, $id = 0)
  {
    $data['id'] = $id;
    $data['c_id'] = $c_id;
    $data['config'] = $this->contract_config_model->get_config();

    $tbl = 'contract_should_flow';
    $this->contract_flow_model->set_tbl($tbl);
    if ($id) {
      $data['flow_list'] = $this->contract_flow_model->get_by_id($id);
      $agency_id = $data['flow_list']['flow_agency_id'];
    } else {
      $agency_id = $this->user_arr['agency_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->signing_model->get_range_menu_by_role_level(
      $this->user_arr, $agency_id);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_signing.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_should_modify', $data);
  }

  /**
   * 跳转合同实收实详情页面
   */
  public function contract_should_detail($id = 0)
  {
    $data['id'] = $id;
    $data['config'] = $this->contract_config_model->get_config();

    $tbl = 'contract_should_flow';
    $this->contract_flow_model->set_tbl($tbl);
    $data['detail'] = $this->contract_flow_model->get_by_id($id);
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_should_detail', $data);
  }

  /**
   * 跳转合同业绩分成编辑页面
   */
  public function contract_divide_modify($c_id = 0, $id = 0)
  {
    $data['id'] = $id;
    $data['c_id'] = $c_id;
    $total = $this->contract_divide_model->get_total($c_id);
    $data['config'] = $this->contract_config_model->get_config();
    if ($id) {
      $data['divide_list'] = $this->contract_divide_model->get_by_id($id);
      $total['percent_total'] = $total['percent_total'] - $data['divide_list']['percent'];
      $agency_id = $data['divide_list']['agency_id'];
      $agency_id1 = $data['divide_list']['achieve_agency_id_a'];
    } else {
      $total['percent_total'] = $total['percent_total'] ? $total['percent_total'] : '0';
      $agency_id = $this->user_arr['agency_id'];
      $agency_id1 = $this->user_arr['agency_id'];
    }
    $data['divide_total'] = $total;
    //获取所有一级门店（区域）
    //$data['agency_first'] = $this->agency_model->get_agency_1_by_company_id($this->user_arr['company_id']);

    //获取所有一级门店（区域）
    //$data['broker_first'] = $this->broker_info_model->get_by_agency_id($data['divide_list']['agency_id']);

    $data['contract'] = $this->signing_model->get_by_id($c_id);
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->signing_model->get_range_menu_by_role_level(
      $this->user_arr, $agency_id);
    $range_menu1 = $this->signing_model->get_range_menu_by_role_level(
      $this->user_arr, $agency_id1);
    //归属门店数据
    $data['agencys'] = $range_menu['agencys'];
    //归属经纪人数据
    $data['brokers'] = $range_menu['brokers'];
    //门店业绩门店数据
    $data['agencys1'] = $range_menu1['agencys'];
    //门店业绩经纪人数据
    $data['brokers1'] = $range_menu1['brokers'];
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_signing.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_divide_modify', $data);
  }

  /**
   * 跳转合同业绩分成编辑页面
   */
  public function contract_divide_detail($id)
  {
    $data['config'] = $this->contract_config_model->get_config();
    $data['divide_list'] = $this->contract_divide_model->get_by_id($id);
    $data['contract'] = $this->signing_model->get_by_id($data['divide_list']['c_id']);
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_divide_detail', $data);
  }

  /**
   * 跳转合同应收应付的权证页面
   */
  public function contract_should_manage($id)
  {
    //获取配置项
    $data['config'] = $this->contract_config_model->get_config();
    $data['contract'] = $this->signing_model->get_by_id($id);
    //应收应付
    $this->contract_flow_model->set_tbl('contract_should_flow');
    $data['should_flow'] = $this->contract_flow_model->get_list_by_cond1(array('c_id' => $id));
    $get_total1 = $this->contract_flow_model->get_total(array('c_id' => $id));
    $data['should_collect_money_total'] = $get_total1['collect_money_total'];
    $data['should_pay_money_total'] = $get_total1['pay_money_total'];

    if (is_full_array($data['should_flow'])) {
      foreach ($data['should_flow'] as $key => $val) {
        if (mb_strlen($val['remark'], 'UTF8') > 8) {
          $data['should_flow'][$key]['remark'] = mb_substr($val['remark'], 0, 7, 'UTF8') . '...';
        }
      }
    }

    $should_add_per = $this->broker_permission_model->check('55');
    $should_edit_per = $this->broker_permission_model->check('56');
    $should_delete_per = $this->broker_permission_model->check('57');

    $data['auth'] = array(
      'should_add' => $should_add_per, 'should_edit' => $should_edit_per,
      'should_delete' => $should_delete_per,
    );
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_should_manage', $data);
  }

  /**
   * 跳转合同跟进的权证页面
   */
  public function contract_follow_manage($id = 0)
  {
    $post_param = $this->input->post(null, true);
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);
    $where = "c_id = " . $id;
    //符合条件的总行数
    $this->_total_count = $this->contract_log_model->count_by($where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    //跟进记录
    $data['follow'] = $this->contract_log_model->get_all_by_id($id, $this->_offset, $this->_limit);
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
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_follow_manage', $data);
  }

  /**
   * 跳转合同应收应付的权证页面
   */
  public function contract_divide_manage($id)
  {
    $data['contract'] = $this->signing_model->get_by_id($id);
    //获取配置项
    $data['config'] = $this->contract_config_model->get_config();
    //业绩分成
    $data['divide_list'] = $this->contract_divide_model->get_all_by(array('c_id' => $id));
    $total = $this->contract_divide_model->get_total($id);
    $data['divide_total'] = $total;

    //根据合同id统计分成条数
    $data['divide_num'] = $this->contract_divide_model->count_by(array('c_id' => $id));
    $divide_add_per = $this->broker_permission_model->check('51');
    $divide_edit_per = $this->broker_permission_model->check('52');
    $divide_delete_per = $this->broker_permission_model->check('53');
    $divide_complete_per = $this->broker_permission_model->check('54');

    $data['auth'] = array(
      'divide_add' => $divide_add_per, 'divide_edit' => $divide_edit_per,
      'divide_delete' => $divide_delete_per, 'divide_complete' => $divide_complete_per);
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_divide_manage', $data);
  }

  /**
   * 权证流程
   */
  public function warrant_list()
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
      $post_param['enter_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['enter_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['enter_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['enter_broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->signing_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['agency_id_a']);
    //门店数据
    $data['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $data['brokers'] = $range_menu['brokers'];

    //表单提交参数组成的查询条件
    $cond_where = $this->_get_cond_str($post_param);

    //查询条件是否创建权证步骤 0 否 1 是
    $cond_where .= " AND `is_template` = 1";

    //清除条件头尾多余空格
    $cond_where = trim($cond_where);
    //符合条件的总行数
    $this->_total_count = $this->signing_model->count_by($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit2) : 0;
    //权证配置项
    $data['stage_conf'] = $this->contract_warrant_model->get_stage_conf();
    //权证步骤名配置
    $data['stage'] = $this->contract_warrant_model->get_all_stage();
    //获取列表内容
    $list = $this->signing_model->get_all_by($cond_where, $this->_offset, $this->_limit2);
    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        //权证流程
        $warrant_step = $this->contract_warrant_model->get_all_by_cid($val['id']);
        if (is_full_array($warrant_step)) {
          foreach ($warrant_step as $k => $v) {
            $arr = array();
            $stage_name = explode(',', $v['stage_id']);
            foreach ($stage_name as $k1 => $v1) {
              $arr[] = $data['stage'][$v1]['stage_name'];
              $warrant_step[$k]['stage_name1'] = implode('，', $arr);
              if (count($arr) > 1) {
                $warrant_step[$k]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'] . '...';
              } else {
                $warrant_step[$k]['stage_name2'] = $data['stage'][$stage_name[0]]['stage_name'];
              }
            }
            $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($v['broker_id']);
            $warrant_step[$k]['broker_name'] = $broker_info['truename'];
            $warrant_step[$k]['agency_name'] = $broker_info['agency_name'];
            $broker_info1 = $this->api_broker_model->get_baseinfo_by_broker_id($v['complete_broker_id']);
            $warrant_step[$k]['complete_broker_name'] = $broker_info1['truename'];
            $warrant_step[$k]['complete_agency_name'] = $broker_info1['agency_name'];
          }
        }
        $list[$key]['warrant_list'] = $warrant_step;
        //房源地址截取
        if (mb_strlen($val['house_addr'], 'UTF8') > 50) {
          $list[$key]['house_addr'] = mb_substr($val['house_addr'], 0, 40, 'utf-8') . '...';
        }
      }
    }
    $data['list'] = $list;

    $default_temp = $this->warrant_model->get_default_temps();
    //根据模板的ID读取步骤
    $steps = $this->warrant_model->get_step_by_template_id($default_temp['id']);
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
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_warrant_list', $data);
  }

  //判断公司是否创建模板
  public function judge_company_template()
  {
    $total = $this->warrant_model->get_count('warrant_template', array('company_id' => $this->user_arr['company_id']));
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
    $data['stage'] = $this->warrant_model->get_all_stage();
    $post_param = $this->input->post(null, true);
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination1($page, $pagesize);

    $where = "((type = 0 and company_id = 0) or (type = 1 and company_id = {$this->user_arr['company_id']})) and is_addstep = 1";

    //符合条件的总行数
    $this->_total_count = $this->warrant_model->get_count('warrant_template', $where);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit1) : 0;
    //获得公司下所有模板
    $sys_temps = $this->warrant_model->get_all_temps($where, $this->_offset, $this->_limit1, 'id', 'ASC');

    foreach ($sys_temps as $key => $val) {
      //根据模板的ID读取步骤
      $steps = $this->warrant_model->get_step_by_template_id($val['id']);
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
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('signing/contract_choose_template', $data);
  }

  /**
   * 选择模板后的操作
   */
  public function sel_choose()
  {
    $template_id = $this->input->post('template_id');
    $contract_id = $this->input->post('contract_id');
    $steps = $this->warrant_model->get_step_by_template_id($template_id);//获取模板步骤信息
    //权证配置项
    $stage_conf = $this->contract_warrant_model->get_stage_conf();
    //权证步骤名配置
    $stage = $this->contract_warrant_model->get_all_stage();
    $num = 0;
    if (is_full_array($steps)) {
      foreach ($steps as $key => $val) {
        $add_data['contract_id'] = $contract_id;
        $add_data['stage_id'] = $val['stage_id'];
        $add_data['step_id'] = $key + 1;
        $add_data['broker_id'] = $this->user_arr['broker_id'];
        $add_data['agency_id'] = $this->user_arr['agency_id'];
        $add_data['company_id'] = $this->user_arr['company_id'];
        $add_data['createtime'] = time();
        //依次将新步骤添加到步骤表
        $rs = $this->contract_warrant_model->insert_data($add_data);
        if ($rs) {
          $stage_id = explode(',', $add_data['stage_id']);
          foreach ($stage_id as $k => $v) {
            $arr[] = $stage[$v]['stage_name'];
          }
          $stage_name = implode('，', $arr);
          $follow_data = array(
            'c_id' => $contract_id,
            'type_name' => "权证流程步骤",
            'content' => "添加权证流程步骤，{$stage_conf[$key+1]['text']}：{$stage_name}。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $this->contract_log_model->add_info($follow_data);
          //操作日志
          $info = $this->signing_model->get_by_id($contract_id);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '添加合同编号为' . $info['result'] . '的交易合同的权限流程。',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);
        }
        $num++;
      }
    }
    $total = $this->warrant_model->get_count('warrant_template_step', array('template_id' => $template_id));
    if ($num == $total) {
      $this->signing_model->update_by_id(array('template_id' => $template_id, 'is_template' => 1), $contract_id);
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
    $result = $this->warrant_model->get_temp_by_cond($where);
    if ($result) {
      $return_data['result'] = 'no';
      $return_data['msg'] = '公司下已有同名的权证模板！';
    } else {
      $total = $this->warrant_model->get_count('warrant_template', array('company_id' => $this->user_arr['company_id']));
      if ($total >= 5) {
        $return_data['result'] = 'no';
        $return_data['msg'] = '公司下最多建立5个模板！';
      } else {
        $add_data = array(
          'template_name' => $template_name,
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'type' => 1
        );
        $insert_id = $this->contract_warrant_model->add_new_template($add_data);
        if ($insert_id) {
          //记录跟进信息
          $follow_data = array(
            'c_id' => 0,
            'type_name' => '权证流程',
            'content' => '添加权证流程模板，"' . $template_name . '"。',
            'updatetime' => time(),
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename']
          );
          $this->contract_log_model->add_info($follow_data);
          $return_data['result'] = 'ok';
          $return_data['msg'] = '权证流程模板添加成功！';
          $return_data['data'] = $insert_id;
          //操作日志
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '添加名称为"' . $template_name . '"的权限流程模板。',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);
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
    $result = $this->warrant_model->get_temp_by_cond($where);
    if ($result) {
      $return_data['result'] = 'no';
      $return_data['msg'] = '公司下已有同名的权证模板！';
    } else {
      $paramArray = array(
        'template_name' => $template_name
      );
      $this->warrant_model->modify_data('warrant_template', $paramArray, array('id' => $template_id)); //数据入库
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
      $old_data = $this->warrant_model->get_step_by_id($stage_id);
      $update_result = $this->warrant_model->modify_data('warrant_template_step', $paramArray, array('id' => $stage_id)); //数据入库
      if (!empty($update_result) && is_int($update_result)) {
        $return_data['msg'] = '修改权证流程模板步骤成功！';
        $return_data['result'] = 'ok';
      } else {
        $return_data['msg'] = '修改权证流程模板步骤失败！';
        $return_data['result'] = 'no';
      }
    } else {
      $total_step_old = $this->warrant_model->get_count('warrant_template_step', array('template_id' => $template_id));
      $paramArray['company_id'] = $this->user_arr['company_id'];
      $paramArray['agency_id'] = $this->user_arr['agency_id'];
      $paramArray['broker_id'] = $this->user_arr['broker_id'];
      $paramArray['template_id'] = $template_id;
      $paramArray['step_id'] = $total_step_old + 1;
      if ($total_step_old < 10) {
        //如果之前没有添加过步骤，则改变合同创建模版的状态
        if ($total_step_old == 0) {
          $this->warrant_model->modify_data('warrant_template', array('is_addstep' => 1), array('id' => $template_id));
        }
        $add_result = $this->warrant_model->insert_data('warrant_template_step', $paramArray); //数据入库
        if (!empty($add_result) && is_int($add_result)) {
          $return_data['msg'] = '添加权证流程模板步骤成功！';
          $return_data['result'] = 'ok';
        } else {
          $return_data['msg'] = '添加权证流程模板步骤失败！';
          $return_data['result'] = 'no';
        }
      } else {
        $return_data['msg'] = '权证流程模板步骤最多可添加10步！';
        $return_data['result'] = 'no';
      }
    }
    echo json_encode($return_data);
  }

  //新建模板后合同确认使用该模板
  function save_warrant_step()
  {
    $template_id = $this->input->post('template_id');
    $c_id = $this->input->post('contract_id');
    $template_name = $this->input->post('template_name');
    //权证步骤名配置
    $stage = $this->contract_warrant_model->get_all_stage();
    //权证配置项
    $stage_conf = $this->contract_warrant_model->get_stage_conf();
    //检查其他模板名是否同名
    $where = "template_name = '{$template_name}' and company_id = {$this->user_arr['company_id']} and id !={$template_id}";
    $result = $this->warrant_model->get_temp_by_cond($where);
    if ($result) {
      $return_data['result'] = 'no';
      $return_data['msg'] = '公司下已有同名的权证模板！';
    } else {
      $this->warrant_model->modify_data('warrant_template', array('template_name' => $template_name), array('id' => $template_id));
      $steps = $this->warrant_model->get_step_by_template_id($template_id);
      if (is_full_array($steps)) {
        foreach ($steps as $k => $v) {
          $add_data['contract_id'] = $c_id;
          $add_data['stage_id'] = $v['stage_id'];
          $add_data['step_id'] = $k + 1;
          $add_data['broker_id'] = $this->user_arr['broker_id'];
          $add_data['agency_id'] = $this->user_arr['agency_id'];
          $add_data['company_id'] = $this->user_arr['company_id'];
          $add_data['createtime'] = time();
          //依次将新步骤添加到步骤表
          $rs = $this->contract_warrant_model->add_info($add_data);
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
              'broker_id' => $this->user_arr['broker_id'],
              'broker_name' => $this->user_arr['truename'],
              'updatetime' => time()
            );
            $this->contract_log_model->add_info($follow_data);
            //操作日志
            $info = $this->signing_model->get_by_id($c_id);
            $add_log_param = array(
              'company_id' => $this->user_arr['company_id'],
              'agency_id' => $this->user_arr['agency_id'],
              'broker_id' => $this->user_arr['broker_id'],
              'broker_name' => $this->user_arr['truename'],
              'type' => 35,
              'text' => '新增编号为' . $info['number'] . '的交易合同的权限流程。',
              'from_system' => 1,
              'from_ip' => get_ip(),
              'mac_ip' => '127.0.0.1',
              'from_host_name' => '127.0.0.1',
              'hardware_num' => '测试硬件序列号',
              'time' => time()
            );
            $this->operate_log_model->add_operate_log($add_log_param);
          }
        }
      }
      $return_data['result'] = 'ok';
      $return_data['msg'] = '保存成功';
    }
    echo json_encode($return_data);
  }

  public function stage_detail()
  {
    $id = $this->input->post('id');
    //权证配置项
    $stage_conf = $this->bargain_transfer_model->get_stage_conf();
    //权证步骤名配置
    $stage = $this->bargain_transfer_model->get_all_stage();
    $data['warrant_list'] = $this->bargain_transfer_model->get_by_id($id);
    $data['warrant_list']['stage_id'] = explode(',', $data['warrant_list']['stage_id']);
    foreach ($data['warrant_list']['stage_id'] as $key => $val) {
      $stage_name[] = $stage[$val]['stage_name'];
    }
    $data['warrant_list']['stage_name'] = implode(',', $stage_name);
    $data['warrant_list']['step_name'] = $stage_conf[$data['warrant_list']['step_id']]['text'];
    if ($data['warrant_list']) {
      $data['result'] = 1;
    } else {
      $data['result'] = 0;
    }
    echo json_encode($data);
  }

  public function warrant_detail()
  {
    $id = $this->input->post('id');
    //权证配置项
    $stage_conf = $this->contract_warrant_model->get_stage_conf();
    //权证步骤名配置
    $stage = $this->contract_warrant_model->get_all_stage();
    $data['warrant_list'] = $this->contract_warrant_model->get_by_id($id);
    $data['warrant_list']['stage_id'] = explode(',', $data['warrant_list']['stage_id']);
    foreach ($data['warrant_list']['stage_id'] as $key => $val) {
      $stage_name[] = $stage[$val]['stage_name'];
    }
    $data['warrant_list']['stage_name'] = implode(',', $stage_name);
    $data['warrant_list']['step_name'] = $stage_conf[$data['warrant_list']['step_id']]['text'];
    if ($data['warrant_list']) {
      $data['result'] = 1;
    } else {
      $data['result'] = 0;
    }
    echo json_encode($data);
  }

  /** 流程模板修改步骤 */
  public function modify_temp_step()
  {
    $c_id = $this->input->post('c_id');
    $stage_id = $this->input->post('stage_id');
    $post_param = $this->input->post(NULL, true);
    //权证步骤名配置
    $stage = $this->contract_warrant_model->get_all_stage();
    //权证配置项
    $stage_conf = $this->contract_warrant_model->get_stage_conf();
    $paramArray = array(
      'contract_id' => intval($c_id),
      'stage_id' => implode(',', $post_param['stage']),
      'remark' => trim($post_param['warrant_remark']),
      'isComplete' => 0
    );
    $is_remind = $this->input->post('is_remind');
    if ($is_remind == 1) {
      $paramArray['remind_agency_id'] = intval($this->input->post('remind_agency_id'));
      $paramArray['remind_broker_id'] = intval($this->input->post('remind_broker_id'));
      $paramArray['remind_remark'] = trim($this->input->post('remind_remark'));
      $paramArray['remind_time'] = $this->input->post('remind_time');
      $paramArray['is_remind'] = $this->input->post('is_remind');
    }
    if ($stage_id) {
      $old_data = $this->contract_warrant_model->get_by_id($stage_id);
      $update_result = $this->contract_warrant_model->modify_data($stage_id, $paramArray); //数据入库
      if (!empty($update_result) && is_int($update_result)) {
        //发送消息
        $this->load->model('message_base_model');
        if ($is_remind == 1) {
          $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($paramArray['remind_broker_id']);
          $contract = $this->signing_model->get_by_id($c_id);
          $params = array('number' => $contract['number'],
            'remind_remark' => $paramArray['remind_remark'],
            'remind_time' => $paramArray['remind_time']);
          $this->message_base_model->add_message('8-50', $paramArray['remind_broker_id'], $broker_info['truename'], '/signing/contract_detail/' . $c_id, $params);
        }
        $str = $this->modify_step_match($paramArray, $old_data);
        $add_data = array(
          'c_id' => $c_id,
          'type_name' => "权证流程",
          'content' => "修改权证流程步骤，{$stage_conf[$old_data['step_id']]['text']}：" . $str,
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $return_data['msg'] = '修改权证流程步骤成功！';
        $this->contract_log_model->add_info($add_data);
        $return_data['result'] = 'ok';

        //操作日志
        $info = $this->signing_model->get_by_id($c_id);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '修改编号为' . $info['number'] . '的交易合同的权限流程。' . $str,
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
      } else {
        $return_data['msg'] = '修改权证流程步骤失败！';
        $return_data['result'] = 'no';
      }
    } else {
      $total_step_old = $this->contract_warrant_model->count_by(array('contract_id' => $c_id));
      $paramArray['company_id'] = $this->user_arr['company_id'];
      $paramArray['agency_id'] = $this->user_arr['agency_id'];
      $paramArray['broker_id'] = $this->user_arr['broker_id'];
      $paramArray['createtime'] = time();
      $paramArray['step_id'] = $total_step_old + 1;
      if ($total_step_old < 10) {
        //如果之前没有添加过步骤，则改变合同创建模版的状态
        if ($total_step_old == 0) {
          $this->signing_model->update_by_id(array('is_template' => 1), $c_id);
        }
        $add_result = $this->contract_warrant_model->insert_data($paramArray); //数据入库
        $total_step = $this->contract_warrant_model->count_by(array('contract_id' => $c_id));
        foreach ($post_param['stage'] as $k => $v) {
          $arr[] = $stage[$v]['stage_name'];
        }
        $stage_name = implode('，', $arr);
        if (!empty($add_result) && is_int($add_result)) {
          //发送消息
          $this->load->model('message_base_model');
          if ($is_remind == 1) {
            $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($paramArray['remind_broker_id']);
            $contract = $this->signing_model->get_by_id($c_id);
            $params = array('number' => $contract['number'],
              'remind_remark' => $paramArray['remind_remark'],
              'remind_time' => $paramArray['remind_time']);
            $this->message_base_model->add_message('8-50', $paramArray['remind_broker_id'], $broker_info['truename'], '/signing/contract_detail/' . $c_id, $params);
          }
          $add_data = array(
            'c_id' => $c_id,
            'type_name' => "权证流程",
            'content' => "添加权证流程步骤，{$stage_conf[$total_step]['text']}：{$stage_name}。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $return_data['msg'] = '添加权证流程步骤成功！';
          $this->contract_log_model->add_info($add_data);
          $return_data['result'] = 'ok';
          //操作日志
          $info = $this->signing_model->get_by_id($c_id);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '新增编号为' . $info['number'] . '的交易合同的权限流程。',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);
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
    $rs = $this->warrant_model->delete_data('warrant_template', array('id' => $template_id));
    if ($rs) {
      $this->warrant_model->delete_data('warrant_template_step', array('template_id' => $template_id));
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
    $stage = $this->contract_warrant_model->get_all_stage();

    $data = $this->warrant_model->get_step_by_id($stage_id);//获取原来的数据

    $stageid_arr = explode(',', $data['stage_id']);
    foreach ($stageid_arr as $key => $val) {
      $stage_name1[] = $stage[$val]['stage_name'];
    }
    $stage_name = implode(',', $stage_name1);

    $rs = $this->warrant_model->delete_data('warrant_template_step', array('id' => $stage_id));
    if ($rs) {
      $total = $this->warrant_model->get_count('warrant_template_step', array('template_id' => $template_id));
      if ($total == 0) {
        $this->warrant_model->modify_data('warrant_template', array('is_addstep' => 0), array('id' => $template_id));
      }
      //取出这步之后的步骤
      $steps = $this->warrant_model->get_step_by_con(array('template_id' => $template_id, 'step_id >' => $data['step_id']));
      if (is_full_array($steps)) {
        foreach ($steps as $key => $val) {
          $this->warrant_model->update_step_status(array('step_id' => $val['step_id'] - 1), $val['id']);
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
    $c_id = $this->input->post('contract_id');
    $stage_id = $this->input->post('stage_id');
    $agency_id = $this->input->post('agency_id');
    $broker_id = $this->input->post('broker_id');
    $contirm_time = $this->input->post('confirm_time');
    //权证步骤名配置
    $stage = $this->contract_warrant_model->get_all_stage();
    //权证配置项
    $stage_conf = $this->contract_warrant_model->get_stage_conf();
    //获取该步骤数据
    $data = $this->contract_warrant_model->get_by_id($stage_id);
    $stage_id1 = explode(',', $data['stage_id']);
    foreach ($stage_id1 as $key => $val) {
      $stage_name[] = $stage[$val]['stage_name'];
    }
    $stage_name = implode(',', $stage_name);
    //如果步骤为第一步，责不需要判断上一步是否完成
    if ($data['step_id'] == 1) {
      $data['isComplete'] = 1;  //修改属性和值
      $data['complete_agency_id'] = $agency_id;
      $data['complete_broker_id'] = $broker_id;
      $data['complete_time'] = $contirm_time;

      $rs = $this->contract_warrant_model->modify_data($stage_id, $data);
      if ($rs == 1) {
        $add_data = array(
          'c_id' => $c_id,
          'type_name' => "权证流程",
          'content' => "完成权证流程步骤，{$stage_conf[$data['step_id']]['text']}：{$stage_name}。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $this->contract_log_model->add_info($add_data);
        $json_data['msg'] = "步骤：{$stage_name}已完成！";
        $json_data['result'] = 'ok';
        //操作日志
        $info = $this->signing_model->get_by_id($c_id);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '完成编号为' . $info['number'] . '的交易合同的权限流程。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
      } else {
        $json_data['msg'] = "步骤：{$stage_name}完成失败！";
        $json_data['result'] = 'no';
      }
    } else {
      //先查询该合同下该步骤的上一步是否已经完成
      $prev_data = $this->contract_warrant_model->get_by_cond(array('contract_id' => $c_id, 'step_id' => $data['step_id'] - 1));
      $status = $prev_data[0]['isComplete'];
      if ($status == 1) {
        //如果该步骤的上一步已经完成，责该步骤也能完成，否则将不能完成该步骤
        $data['isComplete'] = 1;  //修改属性和值
        $data['complete_agency_id'] = $agency_id;
        $data['complete_broker_id'] = $broker_id;
        $data['complete_time'] = $contirm_time;
        $rs = $this->contract_warrant_model->modify_data($stage_id, $data);
        if ($rs == 1) {
          $add_data = array(
            'c_id' => $c_id,
            'type_name' => "权证流程",
            'content' => "完成权证流程步骤，{$stage_conf[$data['step_id']]['text']}：{$stage_name}。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $this->contract_log_model->add_info($add_data);
          $json_data['msg'] = "步骤：{$stage_name}已完成！";

          $json_data['result'] = 'ok';

          //操作日志
          $info = $this->signing_model->get_by_id($c_id);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '完成编号为' . $info['number'] . '的交易合同的权限流程。',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);
        } else {
          $json_data['msg'] = "步骤：{$stage_name}完成失败！";
          $json_data['result'] = 'no';
        }
      } else {
        $json_data['msg'] = "请先完成上一步！";
        $json_data['result'] = 'no';   //上一步未完成
      }
    }
    echo json_encode($json_data);
  }

  /** 已办结 */
  public function confirm_all_complete()
  {
    $num = 0;
    $contract_id = $this->input->post('contract_id');
    //查询出该合同的未完成步骤
    $where = "contract_id = " . $contract_id . " and isComplete = 0";
    $total = $this->contract_warrant_model->count_by($where);
    $result = $this->contract_warrant_model->get_by_cond($where);
    $update_data = array(
      'isComplete' => 1,
      'complete_broker_id' => $this->user_arr['broker_id'],
      'complete_agency_id' => $this->user_arr['agency_id'],
      'complete_time' => date('Y-m-d', time())
    );
    foreach ($result as $key => $val) {
      $rs = $this->contract_warrant_model->modify_data($val['id'], $update_data);
      if ($rs) {
        $num++;
      }
    }
    if ($total == $num) {
      //设置合同办结状态属性
      $data['is_completed'] = 1;
      $data['completed_time'] = time();
      //修改合同表中该合同的相应字段
      $rs = $this->signing_model->update_by_id($data, $contract_id);
      if ($rs) {
        $add_data = array(
          'c_id' => $contract_id,
          'type_name' => "权证流程",
          'content' => "对该合同进行结盘操作，完成全部权证流程步骤。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $this->contract_log_model->add_info($add_data);
        $json_data['msg'] = '该合同权证流程已办结！';

        //操作日志
        $info = $this->signing_model->get_by_id($contract_id);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '对编号为' . $info['number'] . '的交易合同的权限流程进行结盘。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
        $json_data['result'] = 'ok';  //成功
      } else {
        $json_data['msg'] = '该合同权证流程办结失败！';
        $json_data['result'] = 'no';  //失败
      }
    } else {
      $json_data['msg'] = '该合同权证流程办结失败！';
      $json_data['result'] = 'no';   //无法办结
    }
    echo json_encode($json_data);
  }

  /** 删除合同模板步骤 */
  public function delete_temp_step()
  {
    $stage_id = $this->input->post('stage_id');
    $c_id = $this->input->post('c_id');
    //权证步骤名配置
    $stage = $this->contract_warrant_model->get_all_stage();

    $data = $this->contract_warrant_model->get_by_id($stage_id);//获取原来的数据

    $stageid_arr = explode(',', $data['stage_id']);
    foreach ($stageid_arr as $key => $val) {
      $stage_name1[] = $stage[$val]['stage_name'];
    }
    $stage_name = implode(',', $stage_name1);
    //权证配置项
    $stage_conf = $this->contract_warrant_model->get_stage_conf();

    $rs = $this->contract_warrant_model->delete_data($stage_id);
    if ($rs) {
      //取出这步之后的步骤,更改步数
      $steps = $this->contract_warrant_model->get_by_cond(array('contract_id' => $c_id, 'step_id >' => $data['step_id']));
      if (is_full_array($steps)) {
        foreach ($steps as $key => $val) {
          $this->contract_warrant_model->modify_data($val['id'], array('step_id' => $val['step_id'] - 1));
        }
      }
      $steps_total = $this->contract_warrant_model->count_by(array('contract_id' => $c_id));
      if ($steps_total == 0) {
        $this->signing_model->update_by_id(array('is_template' => 0, 'template_id' => 0), $c_id);
      }
      $add_data = array(
        'c_id' => $c_id,
        'type_name' => "权证流程",
        'content' => "删除权证流程步骤，{$stage_conf[$data['step_id']]['text']}：{$stage_name}。",
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'updatetime' => time()
      );
      $return_data['msg'] = "步骤：{$stage_name}已删除！";
      $this->contract_log_model->add_info($add_data);
      $return_data['result'] = 'ok';

      //操作日志
      $info = $this->signing_model->get_by_id($contract_id);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'text' => '删除编号为' . $info['number'] . '的交易合同的权限流程',
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      $this->operate_log_model->add_operate_log($add_log_param);
    } else {
      $return_data['msg'] = "步骤：{$stage_name}删除失败！";
      $return_data['result'] = 'no';
    }
    echo json_encode($return_data);
  }

  /** 完成步骤判断 */
  public function sure_temp_judge()
  {
    $c_id = $this->input->post('contract_id');
    $stage_id = $this->input->post('stage_id');
    //获取该步骤数据
    $data = $this->contract_warrant_model->get_by_id($stage_id);
    if ($data['step_id'] == 1) {
      $json_data['result'] = 'ok';
    } else {
      //先查询该合同下该步骤的上一步是否已经完成
      $prev_data = $this->contract_warrant_model->get_by_cond(array('contract_id' => $c_id, 'step_id' => $data['step_id'] - 1));
      $status = $prev_data[0]['isComplete'];
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
    $total = $this->warrant_model->get_count('warrant_template', array('company_id' => $this->user_arr['company_id']));
    if ($total >= 5) {
      $return_data['result'] = 'no';
      $return_data['msg'] = '公司下最多建立5个模板！';
    } else {
      $return_data['result'] = 'ok';
    }
    echo json_encode($return_data);
  }

  /* ----------------------------------------------------------------------------------------------- */
  /* ---------------------------------------------结束---------------------------------------------- */

  //添加实收实付
  public function add_flow()
  {
    //收付类型 actual 实收实付 should 应收应付
    $flow_type = $this->input->get('flow_type');
    $id = intval($this->input->get('id'));
    //获取合同配置项
    $config = $this->contract_config_model->get_config();
    $paramArray = array(
      'c_id' => trim($this->input->get('c_id')),
      'money_type' => trim($this->input->get('money_type')),
      'collect_type' => trim($this->input->get('collect_type')),
      'collect_money' => sprintf('%.2f', $this->input->get('collect_money')),
      'pay_type' => trim($this->input->get('pay_type')),
      'pay_money' => sprintf('%.2f', $this->input->get('pay_money')),
      'flow_time' => $this->input->get('flow_time'),
      'remark' => trim($this->input->get('remark'))
    );
    if ($flow_type == "actual") {
      $paramArray['payment_method'] = trim($this->input->get('payment_method'));
      $paramArray['flow_agency_id'] = trim($this->input->get('flow_agency_id'));
      $paramArray['flow_broker_id'] = trim($this->input->get('flow_broker_id'));
      $paramArray['counter_fee'] = sprintf('%.2f', $this->input->get('counter_fee'));
      $paramArray['docket'] = trim($this->input->get('docket'));
      $paramArray['docket_type'] = intval($this->input->get('docket_type'));
      $paramArray['is_flow'] = 0;
      $this->contract_flow_model->set_tbl('contract_actual_flow');
    } else {
      $this->contract_flow_model->set_tbl('contract_should_flow');
    }

    if ($id) {
      $old_data = $this->contract_flow_model->get_by_id($id);
      $update_result = $this->contract_flow_model->flow_update($id, $paramArray); //数据入库

      if ($update_result >= 1) {
        $str = $this->modify_flow_match($paramArray, $old_data, $flow_type);
        if ($flow_type == 'actual') {
          $add_data = array(
            'c_id' => $paramArray['c_id'],
            'type_name' => "实收实付",
            'content' => "修改实收实付，款类：{$config['money_type'][$paramArray['money_type']]}。" . $str,
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $return_data['msg'] = '修改实收实付成功！';
        } else {
          $add_data = array(
            'c_id' => $paramArray['c_id'],
            'type_name' => "应收应付",
            'content' => "修改应收应付，款类：{$config['money_type'][$paramArray['money_type']]}。" . $str,
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $return_data['msg'] = '修改应收应付成功！';
        }
        $this->contract_log_model->add_info($add_data);

        $return_data['result'] = 'ok';

        //操作日志
        $info = $this->signing_model->get_by_id($contract_id);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        if ($flow_type == 'actual') {
          $add_log_param['text'] = '修改编号为' . $info['number'] . '的交易合同的实收实付。' . $str;
        } else {
          $add_log_param['text'] = '修改编号为' . $info['number'] . '的交易合同的应收应付。' . $str;
        }
        $this->operate_log_model->add_operate_log($add_log_param);

      } else {
        if ($flow_type == 'actual') {
          $return_data['msg'] = '修改实收实付失败！';
        } else {
          $return_data['msg'] = '修改应收应付失败！';
        }
        $return_data['result'] = 'no';
      }
    } else {
      $paramArray['entry_company_id'] = $this->user_arr['company_id'];
      $paramArray['entry_agency_id'] = $this->user_arr['agency_id'];
      $paramArray['entry_broker_id'] = $this->user_arr['broker_id'];
      $paramArray['entry_time'] = time();
      $info = $this->api_broker_model->get_baseinfo_by_broker_id($paramArray['entry_broker_id']);
      $paramArray['entry_agency_name'] = $info['agency_name'];
      $paramArray['entry_broker_name'] = $info['truename'];
      //获取合同详情
      $contract = $this->signing_model->get_by_id($paramArray['c_id']);
      $paramArray['type'] = $contract['type'];
      $add_result = $this->contract_flow_model->add_flow($paramArray); //数据入库
      if (!empty($add_result) && is_int($add_result)) {
        if ($flow_type == 'actual') {
          $add_data = array(
            'c_id' => $paramArray['c_id'],
            'type_name' => "实收实付",
            'content' => "添加实收实付，款类：{$config['money_type'][$paramArray['money_type']]}。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $return_data['msg'] = '添加实收实付成功！';
        } else {
          $add_data = array(
            'c_id' => $paramArray['c_id'],
            'type_name' => "应收应付",
            'content' => "添加应收应付，款类：{$config['money_type'][$paramArray['money_type']]}。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $return_data['msg'] = '添加应收应付成功！';
        }
        $this->contract_log_model->add_info($add_data);
        $return_data['result'] = 'ok';
        $return_data['num'] = $this->contract_flow_model->count_by(array('c_id' => $paramArray['c_id']));

        //操作日志
        $info = $this->signing_model->get_by_id($contract_id);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        if ($flow_type == 'actual') {
          $add_log_param['text'] = '新增编号为' . $info['number'] . '的交易合同的实收实付。';
        } else {
          $add_log_param['text'] = '新增编号为' . $info['number'] . '的交易合同的应收应付。';
        }
        $this->operate_log_model->add_operate_log($add_log_param);
      } else {
        if ($flow_type == 'actual') {
          $return_data['msg'] = '添加实收实付失败！';
        } else {
          $return_data['msg'] = '添加应收应付失败！';
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
    //获取合同配置项
    $config = $this->contract_config_model->get_config();

    $data = $this->contract_flow_model->get_by_id($id);
    if ($flow_type == 'actual') {
      $this->contract_flow_model->set_tbl('contract_actual_flow');
    } else {
      $this->contract_flow_model->set_tbl('contract_should_flow');
    }
    $result = $this->contract_flow_model->del_by_id($id);
    if (!empty($result) && is_int($result)) {
      if ($flow_type == 'actual') {
        $add_data = array(
          'c_id' => $c_id,
          'type_name' => "实收实付",
          'content' => "删除实收实付，款类：{$config['money_type'][$data['money_type']]}。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $return_data['msg'] = '删除实收实付成功！';
      } else {
        $add_data = array(
          'c_id' => $c_id,
          'type_name' => "应收应付",
          'content' => "删除应收应付，款类：{$config['money_type'][$paramArray['money_type']]}。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $return_data['msg'] = '删除应收应付成功！';
      }
      $return_data['result'] = 'ok';
      $return_data['num'] = $this->contract_flow_model->count_by(array('c_id' => $c_id));
      //操作日志
      $info = $this->signing_model->get_by_id($contract_id);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      if ($flow_type == 'actual') {
        $add_log_param['text'] = '删除编号为' . $info['number'] . '的交易合同的实收实付。';
      } else {
        $add_log_param['text'] = '删除编号为' . $info['number'] . '的交易合同的应收应付。';
      }
      $this->operate_log_model->add_operate_log($add_log_param);
    } else {
      if ($flow_type == 'actual') {
        $return_data['msg'] = '删除实收实付失败！';
      } else {
        $return_data['msg'] = '删除应收应付失败！';
      }
      $return_data['result'] = 'no';
    }

    echo json_encode($return_data);
  }

  function flow_sure()
  {
    //获取合同配置项
    $config = $this->contract_config_model->get_config();
    $id = $this->input->post('id');
    $c_id = $this->input->post('c_id');
    $update_data = array('is_flow' => 1);
    $this->contract_flow_model->set_tbl('contract_actual_flow');
    $data = $this->contract_flow_model->get_by_id($id);
    $rs = $this->contract_flow_model->modify_data($id, $update_data);
    if ($rs) {
      $add_data = array(
        'c_id' => $c_id,
        'type_name' => "实收实付",
        'content' => "实收实付确认收付，款类：{$config['money_type'][$data['money_type']]}。",
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'updatetime' => time()
      );
      $this->contract_log_model->add_info($add_data);
      $return_data['result'] = 'ok';
      $return_data['msg'] = '该实收实付已确认！';
      //返回的跟进数据
      $return_data['follow_list'] = $add_data;
      $return_data['follow_list']['updatetime'] = date('Y-m-d', $add_data['updatetime']);

      //操作日志
      $info = $this->signing_model->get_by_id($contract_id);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'text' => '确认编号为' . $info['number'] . '的交易合同的实收实付的支付。',
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      $this->operate_log_model->add_operate_log($add_log_param);
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
    if ($type == 'actual') {
      $tbl = 'contract_actual_flow';
    } else {
      $tbl = 'contract_should_flow';
    }
    $this->contract_flow_model->set_tbl($tbl);
    $data['flow_list'] = $this->contract_flow_model->get_by_id($id);
    if ($type == 'actual') //实付
    {
      $this->broker_info_model->set_select_fields(array('broker_id', 'truename'));
      $data['broker_list'] = $this->broker_info_model->get_by_agency_id($data['flow_list']['flow_agency_id']);
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
    $stage = $this->contract_warrant_model->get_all_stage();
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
    if (array_key_exists("broker_name", $data)) {
      $str .= "归属人，" . $data2['broker_name'] . '->' . $data1['broker_name'] . "。";
    } else {
      $str .= "归属人，" . $data2['broker_name'] . '。';
    }
    foreach ($data as $key => $val) {
      switch ($key) {
        case 'percent':
          $str .= "占比" . $data2['percent'] . '->' . $data1['percent'] . "；";
          break;
        case 'divide_price':
          $str .= "实际分成金额" . $data2['divide_price'] . '->' . $data1['divide_price'] . "；";
          break;
        case 'achieve_agency_name_a':
          $str .= "门店业绩归属" . $data2['achieve_agency_name_b'] . '-' . $data2['achieve_broker_name_b'] . '->' . $data1['achieve_agency_name_b'] . '-' . $data1['achieve_broker_name_b'] . "；";
          break;
      }
    }
    return $str;
  }

  //修改合同内容匹配
  public function modify_flow_match($data1, $data2, $type)
  {
    $data = array_diff_assoc($data1, $data2);

    foreach ($data as $key => $val) {
      $this->load->model('api_broker_model');
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

      if ($type == 'actual') {
        switch ($key) {
          case 'flow_broker_id':
            $info1 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['broker_id_a']);
            $info2 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['broker_id_a']);
            $str .= "收付人{$info2['agency_name']} {$info2['truename']}->{$info1['agency_name']} {$info1['truename']}；";
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
  public function get_broker_info()
  {
    $broker_id = $this->input->get('broker_id', TRUE);
    $this->broker_info_model->set_select_fields(array('phone'));
    $data['data'] = $this->broker_info_model->get_by_broker_id($broker_id);
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
    $list = $this->signing_model->get_all_by($cond_where, $this->_offset, $this->_limit);
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
    $list = $this->signing_model->get_all_by($cond_where, $this->_offset, $this->_limit);
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
    } elseif ($type == 0) {
        //检查该房源是否已经预约
        $appointment_info = $this->signing_model->get_info_by_houseid($id);
        if (!empty($appointment_info)) {
            $result = array('has_appointment' => 1);
        } else {

      $this->sell_house_model->set_id($id);
        $this->sell_house_model->set_search_fields(array('id', 'block_id', 'block_name', 'buildarea', 'address', 'sell_type', 'owner', 'idcare', 'telno1', 'dong', 'unit', 'door', 'district_id', 'isshare_district'));
      $result = $this->sell_house_model->get_info_by_id();
      $result['house_id'] = format_info_id($id, 'sell');

            //检查房源是否在区域公盘
        $this->load->model('cooperate_district_model');
        $agency_indistrict = $this->cooperate_district_model->get_one_by_agency_id($this->user_arr['agency_id']);//门店所在区域公盘
        if (is_array($agency_indistrict) && $result['isshare_district'] == 0) {
            $block_id = $result['block_id'];
            $door = $result['door'];
            $unit = $result['unit'];
            $dong = $result['dong'];
            $house_district = $result['district_id'];
            $cond_where = "block_id = '$block_id' and door = '$door' and unit = '$unit' and dong = '$dong' and isshare_district = 1 and district_id = '$house_district'";
            $list = $this->sell_house_model->get_list_by_cond($cond_where);//检查区域公盘内房源是否重复
            if ($list) {
                $list[0]['house_id'] = format_info_id($list[0]['id'], 'sell');
                $result['belong_district'] = true;
                $result['district_house'] = $list[0];
            }
        }
        }
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
   * 导出合同报备数据
   * @author   wang
   */
  public function exportReport($type)
  {
    ini_set('memory_limit', '-1');
    $post_param = $this->input->post(NULL, true);
    $config = $this->contract_config_model->get_config();

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
      $post_param['enter_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['enter_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['enter_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['enter_broker_id'] = $this->user_arr['broker_id'];
    }

    //表单提交参数组成的查询条件
    $cond_where = $this->_get_cond_str($post_param);

    //查询交易类型 出售为1  出租为2
    $cond_where .= " AND `type` = " . $type;

    //清除条件头尾多余空格
    $cond_where = trim($cond_where);
    //符合条件的总行数
    $this->_limit = $this->signing_model->count_by($cond_where);

    $productlist = $this->signing_model->get_all_by($cond_where, $this->_offset, $this->_limit);
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
        $list[$key]['signing_time'] = date('Y-m-d H:i:s', $value['signing_time']);
        $list[$key]['agency_name'] = $value['agency_name_a'];
        $list[$key]['broker_name'] = $value['broker_name_a'];
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '合同编号');
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
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['agency_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['broker_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['status']);
    }

    $fileName = 'hetongbaobei' . strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('预约列表');
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
   * 导出合同数据
   * @author   wang
   */
  public function exportContract($type)
  {
    ini_set('memory_limit', '-1');
    $post_param = $this->input->post(NULL, true);
    $config = $this->contract_config_model->get_config();

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
      $post_param['enter_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['enter_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['enter_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['enter_broker_id'] = $this->user_arr['broker_id'];
    }

    //表单提交参数组成的查询条件
    $cond_where = $this->_get_cond_str($post_param);

    //查询交易类型 出售为1  出租为2
    $cond_where .= " AND `type` = " . $type . " AND `is_check` >0";

    //清除条件头尾多余空格
    $cond_where = trim($cond_where);
    //符合条件的总行数
    $this->_limit = $this->signing_model->count_by($cond_where);

    $productlist = $this->signing_model->get_all_by($cond_where, $this->_offset, $this->_limit);
    $list = array();
    if (is_full_array($productlist)) {
      foreach ($productlist as $key => $value) {
        $list[$key]['number'] = $value['number'];
        $list[$key]['type'] = $value['type'] == 1 ? '出售' : '出租';
        $list[$key]['house_addr'] = $value['house_addr'];
        $list[$key]['owner'] = $value['owner'];
        $list[$key]['buildarea'] = $value['buildarea'];
        $list[$key]['price'] = $value['price'];
        $list[$key]['agency_name_a'] = $value['agency_name_a'];
        $list[$key]['broker_name_a'] = $value['broker_name_a'];
        $list[$key]['signing_time'] = date('Y-m-d H', $value['signing_time']);
        $list[$key]['is_check'] = $config['cont_status'][$value['is_check']];
        //获取应收应付合计
        $this->contract_flow_model->set_tbl('contract_should_flow');
        $should_total = $this->contract_flow_model->get_total("c_id = {$value['id']} AND status < 2");
        $list[$key]['should_total'] = $should_total['collect_money_total'];
        if ($should_total['collect_money_total']) {
          //获取实收实付合计
          $this->contract_flow_model->set_tbl('contract_actual_flow');
          $actual_total = $this->contract_flow_model->get_total("c_id = {$value['id']} AND status < 2");
          $list[$key]['actual_total'] = $actual_total['collect_money_total'] ? $actual_total['collect_money_total'] : '0.00';
        }
        $list[$key]['remain_total'] = sprintf('%.2f', (floatval($should_total['collect_money_total']) - floatval($actual_total['collect_money_total'])));
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '合同编号');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '交易类型');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '房源地址');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '面积(㎡)');
    if ($type == 1) {
      $objPHPExcel->getActiveSheet()->setCellValue('E1', '成交价(W)');
    } else {
      $objPHPExcel->getActiveSheet()->setCellValue('E1', '租金(元/月)');
    }

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
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['agency_name_a']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['broker_name_a']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 2]['signing_time']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 2]['is_check']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $list[$i - 2]['should_total']);
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $list[$i - 2]['actual_total']);
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $list[$i - 2]['remain_total']);
      $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $list[$i - 2]['commission_time']);
      $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $list[$i - 2]['completed_time']);
    }

    $fileName = 'hetong' . strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('合同列表');
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
  public function broker_list()
  {
    $agency_id = $this->input->get('agency_id', TRUE);
    $this->broker_info_model->set_select_fields(array('broker_id', 'truename'));
    $data['list'] = $this->broker_info_model->get_by_agency_id($agency_id);
    //print_r($data['list']);die;
    if (is_full_array($data['list'])) {
      $data['result'] = 1;
      $data['msg'] = '查询成功';
    } else {
      $data['result'] = 0;
      $data['msg'] = '查询失败';
    }
    echo json_encode($data);
  }
    //通知公告
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
        $data['departments'] = $this->department_model->get_all_by_company_id($this->user_arr["company_id"]);
        //模板使用数据
        $data['user_menu'] = $this->user_menu;
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
        //post参数
        $post_param = $this->input->post(NULL, TRUE);
        $data['post_param'] = $post_param;
        //print_R($post_param);exit;

        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
        $this->_init_pagination($page, $this->_limit);

        //查询条件
        $cond_where = "company_id = '" . $this->user_arr["company_id"] . "'";

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
        $data['broker_id'] = $broker_id;//获取经纪人编号
        $this->view('signing_notice/signing_notice.php', $data);
    }

    //常用文档
}

/* End of file contract.php */
/* Location: ./application/mls/controllers/contract.php */
