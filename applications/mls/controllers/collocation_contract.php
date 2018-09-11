<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 城市 Class
 *
 * 托管合同控制器
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      Lion
 */
class Collocation_contract extends MY_Controller
{
  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'sh';


  /**
   * 录入经纪人id
   *
   * @access private
   * @var int
   */
  private $_broker_id = 0;

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
    //加载托管合同模型类
    $this->load->model('collocation_contract_model');
    //加载托管出租合同模型类
    $this->load->model('collocation_rent_contract_model');
    //加载业绩分成模型类
    $this->load->model('contract_divide_model');
    //加载合同跟进模型类
    $this->load->model('collocation_contract_log_model');
    //加载实收实付MODEL
    $this->load->model('contract_flow_model');
    //加载合同基本配置MODEL
    $this->load->model('contract_config_model');
    //加载经纪人MODEL
    $this->load->model('broker_info_model');
    //加载门店MODEL
    $this->load->model('agency_model');
    //加载出租MODEL
    $this->load->model('rent_house_model');
    $this->load->model('api_broker_model');
    //加载合同MODEL
    $this->load->model('contract_model');
    $this->load->model('permission_tab_model');
    $this->load->model('contract_log_model');
    //获取出售信息基本配置资料
    $this->load->model('house_config_model');
    //操作日志MODEL
    $this->load->model('operate_log_model');
    //权限
    if (is_full_array($this->user_arr)) {
      $this->load->model('broker_permission_model');
      $this->broker_permission_model->set_broker_id($this->user_arr['broker_id'], $this->user_arr['company_id']);
    }
  }

  public function index($page = 1)
  {
    //模板使用数据
    $data = array();
    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //页面搜索条件
    $post_config = array();
    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['agency_id']);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];

    //查询房源条件
    $cond_where = "";
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;

    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);

    //符合条件的总行数
    $this->_total_count =
      $this->collocation_contract_model->count_by($cond_where);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->collocation_contract_model->get_list_by($cond_where, $this->_offset, $this->_limit);

    if ($list) {
      foreach ($list as $key => $val) {
        $agency = array();
        if ($val['agency_id']) {
          $agency = $this->agency_model->get_by_id($val['agency_id']);
        }
        //查找门店名称
        $list[$key]['agency_name'] = $agency['name'];
        $broker = array();
        //查找经纪人
        if ($val['broker_id']) {
          $broker = $this->broker_info_model->get_by_broker_id($val['broker_id']);
        }
        $list[$key]['broker_name'] = $broker['truename'];
      }
    }
    $data['list'] = $list;
    $data['post_config'] = $post_config;
    //新增托管合同、编辑和删除、作废
    $collocation_add_per = $this->broker_permission_model->check('120');
    $collocation_edit_per = $this->broker_permission_model->check('121');
    $collocation_delete_per = $this->broker_permission_model->check('122');
    $collocation_cancel_per = $this->broker_permission_model->check('123');
    $data['auth'] = array(
      'add' => $collocation_add_per, 'edit' => $collocation_edit_per,
      'delete' => $collocation_delete_per, 'cancel' => $collocation_cancel_per
    );
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
    $data['page_title'] = '托管合同列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css,mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('collocation_contract/list', $data);
  }


  /**
   * 托管合同列表条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = '';
    //托管编号
    $collocation_id = isset($form_param['collocation_id']) ? $form_param['collocation_id'] : 0;
    if ($collocation_id) {
      $cond_where .= " AND `collocation_contract`.collocation_id like '%" . $collocation_id . "%'";
    }
    //房源编号
    $house_id = isset($form_param['house_id']) ? $form_param['house_id'] : 0;
    if ($house_id) {
      $cond_where .= " AND `collocation_contract`.house_id like '%" . $house_id . "%'";
    }
    //楼盘ID
    if (!empty($form_param['block_name']) && $form_param['block_id'] > 0) {
      $cond_where .= " AND `collocation_contract`.block_id = '" . $form_param['block_id'] . "'";
    }
    //楼盘名称
    /*$block_id= isset($form_param['block_id'])?intval($form_param['block_id']):0;
		if($block_id)
		{
			$cond_where .= " AND `collocation_contract`.block_id = '".$block_id."'";
		}*/
    //业主姓名
    $owner = isset($form_param['owner']) ? $form_param['owner'] : 0;
    if ($owner) {
      $cond_where .= " AND `collocation_contract`.owner like '%" . $owner . "%'";
    }
    //付款方式
    $pay_type = isset($form_param['pay_type']) ? intval($form_param['pay_type']) : 0;
    if ($pay_type) {
      $cond_where .= " AND `collocation_contract`.pay_type = '" . $pay_type . "'";
    }
    //审核合同状态
    $status = isset($form_param['status']) ? intval($form_param['status']) : 0;
    if ($status) {
      $cond_where .= " AND `collocation_contract`.status = '" . $status . "'";
    }
    //合同所属公司
    if (isset($form_param['company_id']) && $form_param['company_id'] > 0) {
      $cond_where .= " AND `collocation_contract`.company_id = '" . $form_param['company_id'] . "'";
    }
    //签约门店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND `collocation_contract`.agency_id = '" . $agency_id . "'";
    }
    //签约人
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($broker_id) {
      $cond_where .= " AND `collocation_contract`.broker_id = '" . $broker_id . "'";
    }
    //时间条件
    date_default_timezone_set('PRC');
    //托管开始时间，托管结束时间，签约时间
    $search_where = isset($form_param['search_where']) ? $form_param['search_where'] : 0;
    $time_s = isset($form_param['time_s']) ? strtotime($form_param['time_s']) : 0;
    $time_e = isset($form_param['time_e']) ? strtotime($form_param['time_e']) : 0;
    if ($search_where) {

      if ($time_s && $time_e && $time_s > $time_e) {
        $this->jump(MLS_URL . '/contract/', '您查询的开始时间不能大于结束时间！');
        exit;
      }

      if ($search_where == 'signing_time') {
        //签约日期
        if ($time_s) {

          $cond_where .= " AND `collocation_contract`.signing_time >= '" . $time_s . "'";
        }
        if ($time_e) {

          $cond_where .= " AND `collocation_contract`.signing_time <= '" . $time_e . "'";
        }
      } elseif ($search_where == 'collo_start_time') {
        //托管开始时间
        if ($time_s) {

          $cond_where .= " AND `collocation_contract`.collo_start_time >= '" . $time_s . "'";
        }
        if ($time_e) {

          $cond_where .= " AND `collocation_contract`.collo_start_time <= '" . $time_e . "'";
        }
      } else {
        //托管结束时间
        if ($time_s) {

          $cond_where .= " AND `collocation_contract`.collo_end_time >= '" . $time_s . "'";
        }
        if ($time_e) {

          $cond_where .= " AND `collocation_contract`.collo_end_time <= '" . $time_e . "'";
        }
      }
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
   * 录入托管合同
   * @access public
   * @return void
   */
  public function add_contract()
  {
    $data = array();
    $data['user_tree_menu'] = $this->permission_tab_model->get_tree_menu('collocation_contract', 'index');
    //页面搜索条件
    $post_config = array();
    $range_menu = $this->contract_model->get_range_menu_by_role_level($this->user_arr, $this->user_arr['agency_id']);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];
    //默认本店
    $data['agency_id'] = $this->user_arr['agency_id'];
    $data['post_config'] = $post_config;

    $post_param = $this->input->post(NULL, TRUE);

    $submit_flag = $post_param['submit_flag'];
    if ($submit_flag == "add") {
      //检查合同编号唯一性
      $where = "company_id = {$this->user_arr['company_id']} and collocation_id = '{$post_param['collocation_id']}'";
      $result = $this->collocation_contract_model->get_one_by($where);
      if (is_full_array($result)) {
        echo json_encode(array('result' => '0', "msg" => "公司内已有该编号的合同！"));
        exit;
      } else {
        //托管合同添加信息数组
        $datainfo = array(
          'collocation_id' => $post_param['collocation_id'],
          'house_id' => $post_param['house_id'],
          'block_name' => trim($post_param['block_name']),
          'block_id' => trim($post_param['block_id']),
          'houses_area' => intval($post_param['houses_area']),
          'houses_address' => trim($post_param['houses_address']),
          'type' => trim($post_param['type']),
          'collo_start_time' => strtotime($post_param['collo_start_time']),
          'collo_end_time' => strtotime($post_param['collo_end_time']),
          'total_month' => $post_param['total_month'],
          'owner' => trim($post_param['owner']),
          'owner_tel' => trim($post_param['owner_tel']),
          'owner_idcard' => trim($post_param['owner_idcard']),
          'pay_ditch' => trim($post_param['pay_ditch']),
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => intval($post_param['agency_id']),
          'broker_id' => intval($post_param['broker_id']),
          'broker_tel' => trim($post_param['broker_tel']),
          'rental' => $post_param['rental'],
          'pay_type' => trim($post_param['pay_type']),
          'rental_total' => $post_param['rental_total'],
          'desposit' => $post_param['desposit'],
          'penal_sum' => $post_param['penal_sum'],
          'tax_type' => $post_param['tax_type'],
          'property_manage_assume' => $post_param['property_manage_assume'],
          'property_fee' => $post_param['property_fee'],
          'agency_commission' => $post_param['agency_commission'],
          'rent_free_time' => intval($post_param['rent_free_time']),
          'desposit_type' => trim($post_param['desposit_type']),
          'divide_a_agency_id' => $post_param['divide_a_agency_id'],
          'divide_a_broker_id' => $post_param['divide_a_broker_id'],
          'divide_a_money' => $post_param['divide_a_money'],
          'divide_b_agency_id' => $post_param['divide_b_agency_id'],
          'divide_b_broker_id' => $post_param['divide_b_broker_id'],
          'divide_b_money' => $post_param['divide_b_money'],
          'out_agency_id' => $post_param['out_agency_id'],
          'out_broker_id' => $post_param['out_broker_id'],
          'stop_agreement_num' => intval($post_param['stop_agreement_num']),
          'list_items' => trim($post_param['list_items']),
          'remarks' => trim($post_param['remarks']),
          'status' => 1,//首次添加合同的时候默认状态是待审核
          'signing_time' => strtotime($post_param['signing_time'])//默认签约时间为当前时间
        );
        $collocation_add_per = $this->broker_permission_model->check('120');
        if (isset($collocation_add_per['auth']) && $collocation_add_per['auth']) {
          //添加
          $id = $this->collocation_contract_model->add_info($datainfo);
        } else {
          $this->redirect_permission_none();
          die();
        }
        if ($id) {
          //合同跟进——添加
          $add_data = array(
            'c_id' => $id,
            'type_name' => "合同录入",
            'content' => "对该合同进行录入。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $this->collocation_contract_log_model->add_info($add_data);
          //操作日志
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '录入合同编号为' . $datainfo['collocation_id'] . '的托管合同。',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);
          echo json_encode(array('result' => 'ok', "msg" => "添加合同成功"));
          exit;
        } else {
          echo json_encode(array('result' => 'no', "msg" => "添加合同失败"));
          exit;
        }
      }
    }


    //页面标题
    $data['page_title'] = '新增托管合同';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/house_manage.css,' . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,' . 'mls/js/v1.0/jquery.validate.min.js,' . 'mls/js/v1.0/verification_contract.js');
    $this->view("collocation_contract/add", $data);
  }

  /**
   * 修改合同
   * @access  public
   * @param   void
   * @return  void
   */
  public function modify($id)
  {
    $data = array();
    //树型菜单
    $data['user_tree_menu'] = $this->permission_tab_model->get_tree_menu('collocation_contract', 'index');
    //有托管合同
    if (intval($id) > 0) {
      $role_level = $this->user_arr['role_level'];
      //查询托管合同内容
      $collo_list = $this->get_detail($id);
    }
    //页面搜索条件
    $post_config = array();
    //签约门店，人
    $range_menu = $this->contract_model->get_range_menu_by_role_level($this->user_arr, $collo_list['agency_id']);
    $post_config['agencys'] = $range_menu['agencys'];
    $post_config['brokers'] = $range_menu['brokers'];
    //业绩分成A,B门店，人
    $range_menu2 = $this->contract_model->get_range_menu_by_role_level($this->user_arr, $collo_list['divide_a_agency_id']);
    $post_config['divide_a_agencys'] = $range_menu2['agencys'];
    $post_config['divide_a_brokers'] = $range_menu2['brokers'];
    $range_menu3 = $this->contract_model->get_range_menu_by_role_level($this->user_arr, $collo_list['divide_b_agency_id']);
    $post_config['divide_b_agencys'] = $range_menu3['agencys'];
    $post_config['divide_b_brokers'] = $range_menu3['brokers'];
    //退房经纪人
    $range_menu4 = $this->contract_model->get_range_menu_by_role_level($this->user_arr, $collo_list['out_agency_id']);
    $post_config['out_agencys'] = $range_menu4['agencys'];
    $post_config['out_brokers'] = $range_menu4['brokers'];
    $data['post_config'] = $post_config;

    $data['collo_list'] = $collo_list;


    $post_param = $this->input->post(NULL, TRUE);
    $submit_flag = $post_param['submit_flag'];
    if ($submit_flag == "modify") {
      //检查合同编号唯一性
      $where = "company_id = {$this->user_arr['company_id']} and collocation_id = '{$post_param['collocation_id']}' and id != {$post_param['modify_id']}";
      $result = $this->collocation_contract_model->get_one_by($where);
      if (is_full_array($result)) {
        echo json_encode(array('result' => '0', "msg" => "公司内已有该编号的合同！"));
        exit;
      } else {
        //合同编辑信息数组
        $datainfo = array(
          'collocation_id' => $post_param['collocation_id'],
          'house_id' => $post_param['house_id'],
          'block_name' => trim($post_param['block_name']),
          'block_id' => trim($post_param['block_id']),
          'houses_area' => sprintf('%.2f', $post_param['houses_area']),
          'houses_address' => trim($post_param['houses_address']),
          'type' => trim($post_param['type']),
          'collo_start_time' => strtotime($post_param['collo_start_time']),
          'collo_end_time' => strtotime($post_param['collo_end_time']),
          'total_month' => $post_param['total_month'],
          'owner' => trim($post_param['owner']),
          'owner_tel' => trim($post_param['owner_tel']),
          'owner_idcard' => trim($post_param['owner_idcard']),
          'pay_ditch' => trim($post_param['pay_ditch']),
          'agency_id' => intval($post_param['agency_id']),
          'broker_id' => intval($post_param['broker_id']),
          'broker_tel' => trim($post_param['broker_tel']),
          'rental' => sprintf('%.2f', $post_param['rental']),
          'pay_type' => trim($post_param['pay_type']),
          'rental_total' => sprintf('%.2f', $post_param['rental_total']),
          'desposit' => sprintf('%.2f', $post_param['desposit']),
          'penal_sum' => sprintf('%.2f', $post_param['penal_sum']),
          'tax_type' => intval($post_param['tax_type']),
          'property_manage_assume' => intval($post_param['property_manage_assume']),
          'property_fee' => sprintf('%.2f', $post_param['property_fee']),
          'agency_commission' => sprintf('%.2f', $post_param['agency_commission']),
          'rent_free_time' => intval($post_param['rent_free_time']),
          'desposit_type' => intval($post_param['desposit_type']),
          'divide_a_agency_id' => intval($post_param['divide_a_agency_id']),
          'divide_a_broker_id' => intval($post_param['divide_a_broker_id']),
          'divide_a_money' => sprintf('%.2f', $post_param['divide_a_money']),
          'divide_b_agency_id' => intval($post_param['divide_b_agency_id']),
          'divide_b_broker_id' => intval($post_param['divide_b_broker_id']),
          'divide_b_money' => sprintf('%.2f', $post_param['divide_b_money']),
          'out_agency_id' => intval($post_param['out_agency_id']),
          'out_broker_id' => intval($post_param['out_broker_id']),
          'stop_agreement_num' => intval($post_param['stop_agreement_num']),
          'list_items' => trim($post_param['list_items']),
          'remarks' => trim($post_param['remarks']),
          //'status' => 1,//首次添加合同的时候默认状态是待审核
          'signing_time' => strtotime($post_param['signing_time'])
        );
        //修改权限
        $collocation_edit_per = $this->broker_permission_model->check('121');
        if (isset($collocation_edit_per['auth']) && $collocation_edit_per['auth']) {
          $data = $this->get_detail($id);
          $rs = $this->collocation_contract_model->update_by_id($datainfo, $id);
          if ($rs) {
            if ($collo_list['status'] == 4) {
              $effect = $this->collocation_contract_model->update_by_id(array('status' => '1'), $id);
            }
            $content = $this->modify_match($datainfo, $data, '1');
            //合同跟进——修改合同
            $add_data = array(
              'c_id' => $id,
              'type_name' => "合同修改",
              'content' => "对该合同进行修改。" . $content,
              'broker_id' => $this->user_arr['broker_id'],
              'broker_name' => $this->user_arr['truename'],
              'updatetime' => time()
            );
            $this->collocation_contract_log_model->add_info($add_data);

            //操作日志
            $add_log_param = array(
              'company_id' => $this->user_arr['company_id'],
              'agency_id' => $this->user_arr['agency_id'],
              'broker_id' => $this->user_arr['broker_id'],
              'broker_name' => $this->user_arr['truename'],
              'type' => 35,
              'text' => '修改合同编号为' . $datainfo['collocation_id'] . '的托管合同。' . $content,
              'from_system' => 1,
              'from_ip' => get_ip(),
              'mac_ip' => '127.0.0.1',
              'from_host_name' => '127.0.0.1',
              'hardware_num' => '测试硬件序列号',
              'time' => time()
            );
            $this->operate_log_model->add_operate_log($add_log_param);
          }
        } else {
          $this->redirect_permission_none();
          die();
        }
        if ($rs) {

          echo json_encode(array('result' => 'ok', "msg" => "合同修改成功"));
          exit;
        } else {
          echo json_encode(array('result' => 'no', "msg" => "合同修改失败"));
          exit;
        }
      }
    }
    //页面标题
    $data['page_title'] = '编辑合同';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/house_manage.css,' . 'mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,' . 'mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,' . 'mls/js/v1.0/jquery.validate.min.js,' . 'mls/js/v1.0/verification_contract.js');
    $this->view("collocation_contract/modify", $data);
  }

  /**
   * 选择托管合同
   * @access public
   * @return array
   */
  public function get_collocation_contract($id = 0)
  {
    //模板使用数据
    $data = array();
    $data['id'] = $id;
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);
    $post_config = array();
    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post['agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post['agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post['broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post['agency_id']);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];

    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];
    //查询合同条件
    $cond_where = "";
    //门店权限

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str1($post_param);
    $cond_where .= $cond_where_ext;

    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);

    //符合条件的总行数
    $this->_total_count =
      $this->collocation_contract_model->count_by($cond_where);
    //获取列表内容
    $list = $this->collocation_contract_model->get_list_by($cond_where, $this->_offset, $this->_limit);

    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
        $list[$key]['agency_name'] = $brokerinfo['agency_name'];
        $list[$key]['broker_name'] = $brokerinfo['truename'];
        $list[$key]['house_id'] = format_info_id($val['id'], 'sell');
      }
    }
    $data['list'] = $list;
    $data['post_config'] = $post_config;
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

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


    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();

    //页面标题
    $data['page_title'] = '选择托管合同';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,' . 'mls/css/v1.0/contract_manage.css,' . 'mls/css/v1.0/house_manage.css,' . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view("collocation_contract/select_collocation_contract", $data);

  }

  /**
   * 选择托管合同列表条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str2($form_param)
  {
    $cond_where = '';
    //托管合同编号
    $collocation_id = isset($form_param['collocation_id']) ? $form_param['collocation_id'] : 0;
    if ($collocation_id) {
      $cond_where .= " AND `collocation_contract`.collocation_id like '%" . $collocation_id . "%'";
    }

    //房源编号
    $house_id = isset($form_param['house_id']) ? $form_param['house_id'] : "";
    if ($house_id) {
      $house_id = substr($house_id, 2);
      $cond_where .= " AND `house_id` = '" . $house_id . "'";
    }
    //楼盘
    $block_id = isset($form_param['block_id']) ? intval($form_param['block_id']) : 0;
    if ($block_id) {
      $cond_where .= " AND `block_id` = '" . $block_id . "'";
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

    return $cond_where;

  }

  /**
   * 选择房源列表条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str1($form_param)
  {
    $cond_where = '';
    //托管编号
    $collocation_id = isset($form_param['collocation_id']) ? $form_param['collocation_id'] : 0;
    if ($collocation_id) {
      $cond_where .= " AND collocation_id like '%" . $collocation_id . "%'";
    }

    //状态
    $status = isset($form_param['status']) ? intval($form_param['status']) : 0;
    if ($status) {
      $cond_where .= " AND status = '" . $status . "'";
    }
    /*//楼盘
        $block_id= isset($form_param['block_id'])?$form_param['block_id']:0;
		if($block_id)
		{
			$cond_where .= " AND block_id = '".$block_id."'";
		}*/
    //楼盘ID
    if (!empty($form_param['block_name']) && $form_param['block_id'] > 0) {
      $cond_where .= " AND block_id = '" . $form_param['block_id'] . "'";
    }
    //房源编号
    $house_id = isset($form_param['house_id']) ? $form_param['house_id'] : "";
    if ($house_id) {
      $house_id = substr($house_id, 2);
      $cond_where .= " AND id = '" . $house_id . "'";

    }

    //门店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($broker_id) {
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    //签约公司
    if (isset($form_param['company_id']) && $form_param['company_id'] > 0) {
      $cond_where .= " AND company_id = '" . $form_param['company_id'] . "'";
    }

    return $cond_where;
  }


  /**
   * 删除托管合同
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function del()
  {
    $collocation_delete_per = $this->broker_permission_model->check('122');
    if (isset($collocation_delete_per['auth']) && $collocation_delete_per['auth']) {
      //搜索参数范围权限控制
      $role_level = $this->user_arr['role_level'];
      if ($role_level < 6) //公司
      {
        //所属公司
        $updater_arr['company_id'] = $this->user_arr['company_id'];
      } else if ($role_level < 8) //门店
      {
        //所属公司
        $updater_arr['company_id'] = $this->user_arr['company_id'];
        //所属门店
        $updater_arr['payee_agency_id'] = $this->user_arr['agency_id'];
      } else {
        //公司权限
        $updater_arr['company_id'] = $this->user_arr['company_id'];
        //所属门店
        $updater_arr['payee_agency_id'] = $this->user_arr['agency_id'];
        //所属经纪人
        $updater_arr['payee_broker_id'] = $this->user_arr['broker_id'];
      }

      $id = $this->input->get('id', TRUE);
      $rs = $this->collocation_contract_model->del_by_id($id);

    } else {
      $this->redirect_permission_none();
      die();
    }

    if ($rs) {
      //合同跟进——删除合同
      $data = array(
        'c_id' => $id,
        'type_name' => "合同删除",
        'content' => "本日对该合同信息进行删除。",
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'updatetime' => time()
      );
      $this->collocation_contract_log_model->add_info($data);

      //操作日志
      $info = $this->collocation_contract_model->get_by_id($id);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'text' => '删除合同编号为' . $info['collocation_id'] . '的托管合同。',
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      $this->operate_log_model->add_operate_log($add_log_param);
      //删除托管合同下的应付，实付，管家
      $this->collocation_contract_model->del_need_by_id($id);
      $this->collocation_contract_model->del_actual_pay_by_id($id);
      $this->collocation_contract_model->del_steward_by_id($id);
      echo json_encode($data['result'] = 'ok');
      exit;
    } else {
      echo json_encode($data['result'] = 'no');
      exit;
    }
  }

  /**
   * 作废
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function cancel()
  {
    $collocation_cancel_per = $this->broker_permission_model->check('123');
    if (isset($collocation_cancel_per['auth']) && $collocation_cancel_per['auth']) {
      //搜索参数范围权限控制
      $updater_arr = array('status' => 3);
      $role_level = $this->user_arr['role_level'];
      if ($role_level < 6) //公司
      {
        //所属公司
        $updater_arr['company_id'] = $this->user_arr['company_id'];
      } else if ($role_level < 8) //门店
      {
        //所属公司
        $updater_arr['company_id'] = $this->user_arr['company_id'];
        //所属门店
        $updater_arr['agency_id'] = $this->user_arr['agency_id'];
      } else {
        //公司权限
        $updater_arr['company_id'] = $this->user_arr['company_id'];
        //所属门店
        $updater_arr['agency_id'] = $this->user_arr['agency_id'];
        //所属经纪人
        $updater_arr['broker_id'] = $this->user_arr['broker_id'];
      }
      $id = $this->input->get('id', TRUE);
      $rs = $this->collocation_contract_model->update_by_id($updater_arr, $id);
    } else {
      $this->redirect_permission_none();
      die();
    }

    if ($rs) {
      //合同跟进——作废合同
      $data = array(
        'c_id' => $id,
        'type_name' => "合同作废",
        'content' => "对该合同进行作废，合同已终止。",
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'updatetime' => time()
      );
      $this->collocation_contract_log_model->add_info($data);

      //操作日志
      $info = $this->collocation_contract_model->get_by_id($id);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'text' => '删除合同编号为' . $info['collocation_id'] . '的托管合同。',
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      $this->operate_log_model->add_operate_log($add_log_param);

      echo json_encode($data['result'] = 'ok');
      exit;
    } else {
      echo json_encode($data['result'] = 'no');
      exit;
    }
  }

  /**
   * 获取托管合同详情
   * @access public
   * @return array
   */
  public function get_detail($id)
  {
    $data = $this->collocation_contract_model->get_by_id($id);
    if (is_full_array($data)) {
      $config = $this->contract_config_model->get_config();
      $data['config'] = $config;
    }
    return $data;
  }

  /**
   * 合同详情--应付，实付，管家，出租，合同跟进
   * @access public
   * @return void
   * tab:1--应付（默认刚进页面是应付）  2--实付  3--管家  4--出租  5--合同跟进
   */
  public function contract_detail($id, $tab = 1)
  {
    $data = array();
    //树型菜单
    $data['user_tree_menu'] = $this->permission_tab_model->get_tree_menu('collocation_contract', 'index');
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    $collo_detail = $this->get_detail($id);

    //获取签约门店
    $agency_info = $this->api_broker_model->get_by_agency_id($collo_detail['agency_id']);
    $collo_detail['agency_name'] = $agency_info['name'];
    //获取签约人
    $broker_info = $this->broker_info_model->get_by_broker_id($collo_detail['broker_id']);
    $collo_detail['broker_name'] = $broker_info['truename'];
    //业绩分成a，b经纪人
    $divide_a_agency_info = $this->api_broker_model->get_by_agency_id($collo_detail['divide_a_agency_id']);
    $collo_detail['divide_a_agency_name'] = $divide_a_agency_info['name'];
    $divide_a_broker_info = $this->broker_info_model->get_by_broker_id($collo_detail['divide_a_broker_id']);
    $collo_detail['divide_a_broker_name'] = $divide_a_broker_info['truename'];

    $divide_b_agency_info = $this->api_broker_model->get_by_agency_id($collo_detail['divide_b_agency_id']);
    $collo_detail['divide_b_agency_name'] = $divide_b_agency_info['name'];
    $divide_b_broker_info = $this->broker_info_model->get_by_broker_id($collo_detail['divide_b_broker_id']);
    $collo_detail['divide_b_broker_name'] = $divide_b_broker_info['truename'];
    //退房经纪人
    $out_agency_info = $this->api_broker_model->get_by_agency_id($collo_detail['out_agency_id']);
    $collo_detail['out_agency_name'] = $out_agency_info['name'];
    $out_broker_info = $this->broker_info_model->get_by_broker_id($collo_detail['out_broker_id']);
    $collo_detail['out_broker_name'] = $out_broker_info['truename'];

    $data['collo_detail'] = $collo_detail;

    $data['tab'] = $tab;
    $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($this->user_arr['broker_id']);
    $data['brokerinfo'] = $brokerinfo;
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['broker_id'] = $this->user_arr['broker_id'];
    }
    $post_config = array();
    //付款人
    $range_menu = $this->contract_model->get_range_menu_by_role_level($this->user_arr, $post_param['agency_id']);
    $post_config['agencys'] = $range_menu['agencys'];
    $post_config['brokers'] = $range_menu['brokers'];
    $data['post_config'] = $post_config;
    //通过托管合同id去应付表里找对应的应付业主数据
    if ($tab == 1) {
      //符合条件的总行数
      $this->_total_count =
        $this->collocation_contract_model->count_by_collo_tab(array('c_id' => $collo_detail['id']), '1');

      //应付
      $need_pay = $this->collocation_contract_model->get_list_by_cid(array('c_id' => $collo_detail['id']), $tab, $this->_offset, $this->_limit);

      //获取应付总价
      foreach ($need_pay as $val) {
        $need_total_fee += $val['total_fee'];
        $data['need_total_fee'] = $need_total_fee;
      }

      //获取实付
      $actual_date = $this->collocation_contract_model->get_list_by_cid(array('c_id' => $collo_detail['id']), 2);

      //获取实付总价
      foreach ($actual_date as $val) {
        $actual_total_fee += $val['total_fee'];
        $data['actual_total_fee'] = $actual_total_fee;
      }
      //获取应付跟实付之间的差值
      $data['value'] = $data['need_total_fee'] - $data['actual_total_fee'];

      //付款业主添加、编辑和删除、确认付款
      $payment_add_per = $this->broker_permission_model->check('79');
      $payment_edit_per = $this->broker_permission_model->check('80');
      $payment_del_per = $this->broker_permission_model->check('81');
      $payment_sure_per = $this->broker_permission_model->check('84');
      $data['auth'] = array(
        'add' => $payment_add_per, 'edit' => $payment_edit_per,
        'delete' => $payment_del_per, 'sure' => $payment_sure_per
      );

      $data['need_pay'] = $need_pay;
    } elseif ($tab == 2) {
      //符合条件的总行数
      $this->_total_count =
        $this->collocation_contract_model->count_by_collo_tab(array('c_id' => $collo_detail['id']), '2');
      //实付----实付添加的前提是该合同下面有应付
      $need_date = $this->collocation_contract_model->get_list_by_cid(array('c_id' => $collo_detail['id']), 1);
      $data['need_date'] = $need_date;

      //获取应付总价
      foreach ($need_date as $val) {
        $need_total_fee += $val['total_fee'];
        $data['need_total_fee'] = $need_total_fee;
      }

      $actual_pay = $this->collocation_contract_model->get_list_by_cid(array('c_id' => $collo_detail['id']), $tab, $this->_offset, $this->_limit);

      //获取实付总价
      foreach ($actual_pay as $val) {
        $actual_total_fee += $val['total_fee'];
        $data['actual_total_fee'] = $actual_total_fee;
      }

      //获取应付跟实付之间的差值
      $data['value'] = $data['need_total_fee'] - $data['actual_total_fee'];

      //付款业主添加、编辑和删除、确认付款
      $payment_add_per = $this->broker_permission_model->check('79');
      $payment_edit_per = $this->broker_permission_model->check('80');
      $payment_del_per = $this->broker_permission_model->check('81');
      $payment_sure_per = $this->broker_permission_model->check('84');
      $data['auth'] = array(
        'add' => $payment_add_per, 'edit' => $payment_edit_per,
        'delete' => $payment_del_per, 'sure' => $payment_sure_per
      );

      $data['actual_pay'] = $actual_pay;
    } elseif ($tab == 3) {
      //符合条件的总行数
      $this->_total_count =
        $this->collocation_contract_model->count_by_collo_tab(array('c_id' => $collo_detail['id']), '3');
      //管家
      $steward_pay = $this->collocation_contract_model->get_list_by_cid(array('c_id' => $collo_detail['id']), $tab, $this->_offset, $this->_limit);
      foreach ($steward_pay as $key => $val) {
        //获取报销部门
        $agency_info1 = $this->api_broker_model->get_by_agency_id($val['agency_id']);
        $steward_pay[$key]['agency_name'] = $agency_info1['name'];
      }

      //管家添加、编辑和删除
      $steward_add_per = $this->broker_permission_model->check('85');
      $steward_edit_per = $this->broker_permission_model->check('86');
      $steward_del_per = $this->broker_permission_model->check('87');
      $data['auth'] = array(
        'add_ste' => $steward_add_per, 'edit_ste' => $steward_edit_per,
        'delete_ste' => $steward_del_per
      );

      $data['steward_pay'] = $steward_pay;
    } elseif ($tab == 4) {
      //符合条件的总行数
      $this->_total_count =
        $this->collocation_contract_model->count_by_collo_tab(array('c_id' => $collo_detail['id']), '4');
      //出租
      $rent_pay = $this->collocation_contract_model->get_list_by_cid(array('c_id' => $collo_detail['id']), $tab, $this->_offset, $this->_limit);
      if ($rent_pay) {
        foreach ($rent_pay as $key => $val) {
          $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
          //echo '<pre>';print_r($brokerinfo);die;
          $rent_pay[$key]['broker_name'] = $brokerinfo['truename'];
          $rent_pay[$key]['agency_name'] = $brokerinfo['agency_name'];
        }
      }

      //出租添加、编辑和删除,作废
      $rent_add_per = $this->broker_permission_model->check('90');
      $rent_edit_per = $this->broker_permission_model->check('91');
      $rent_del_per = $this->broker_permission_model->check('92');
      $rent_cancel_per = $this->broker_permission_model->check('93');
      $data['auth'] = array(
        'add_rent' => $rent_add_per, 'edit_rent' => $rent_edit_per,
        'delete_rent' => $rent_del_per, 'cancel_rent' => $rent_cancel_per
      );

      $data['rent_pay'] = $rent_pay;
    } elseif ($tab == 5) {
      //符合条件的总行数
      $this->_total_count =
        $this->collocation_contract_model->count_by_collo_tab(array('c_id' => $collo_detail['id'], 'type' => '1'), '5');
      //跟进 遗留
      $follow = $this->collocation_contract_model->get_list_by_cid(array('c_id' => $collo_detail['id'], 'type' => '1'), $tab, $this->_offset, $this->_limit);
      $data['follow'] = $follow;
    }

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    if (!$id || !is_full_array($data)) {
      $error_url = '/collocation_contract';
      $page_text = '参数错误';
      $this->jump($error_url, $page_text);
    }
    //获取该公司下的所有门店
    $data['agency'] = $this->agency_model->get_children_by_company_id($this->user_arr['company_id']);
    $data['user_menu'] = $this->user_menu;
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
    $data['page_title'] = '托管合同详情页';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,' . 'mls/js/v1.0/scrollPic.js,' . 'mls/js/v1.0/radio_checkbox_mod2.js');
    $this->view("collocation_contract/contract_info", $data);

  }

  /**
   * 获取托管合同下是否有出租合同
   * @access public
   * @return array
   */
  public function get_rent_info()
  {
    $c_id = $this->input->get('c_id', TRUE);
    //托管合同下出租合同信息
    $rent_detail = $this->collocation_contract_model->get_rent_by_cid($c_id);
    //$collo_detail = $this->get_detail($c_id);
    //echo '<pre>';print_r($rent_detail);die;
    if (is_full_array($rent_detail)) {
      foreach ($rent_detail as $val) {
        if ($val['rent_end_time'] > time() && $val['status'] != 3) {
          echo json_encode($data['result'] = 'ok');
          exit;
        } else {
          echo json_encode($data['result'] = 'no');
          exit;
        }
      }
    } else {
      echo json_encode($data['result'] = '11');
      exit;
    }
    return $data;
  }

  /**
   * 录入托管合同--应付业主
   * @access public
   * @return void
   */
  public function add_need_pay($type = 0)
  {
    $post_param = $this->input->post(NULL, TRUE);
    //print_r($post_param);die;
    if ($type == 1) {
      //应付单次添加
      $datainfo = array(
        'c_id' => intval($post_param['c_id']),
        'collocation_id' => $post_param['collocation_id'],
        'rental' => $post_param['rental'],
        'water_fee' => $post_param['water_fee'],
        'ele_fee' => $post_param['ele_fee'],
        'gas_fee' => $post_param['gas_fee'],
        'int_fee' => $post_param['int_fee'],
        'need_pay_time' => strtotime($post_param['need_pay_time']),
        //'stop_time' => strtotime($post_param['stop_time']),
        'tv_fee' => $post_param['tv_fee'],
        'property_fee' => $post_param['property_fee'],
        'preserve_fee' => $post_param['preserve_fee'],
        'garbage_fee' => $post_param['garbage_fee'],
        'other_fee' => $post_param['other_fee'],
        'remark' => trim($post_param['remark']),
        'status' => 1,//首次添加合同的时候默认状态是待审核
        //'fund_status' => 1,
        'pay_type' => 5, //单次添加付款方式为其他
        'pay_times' => 1,//单次添加是次数为1
        'total_fee' => $post_param['need_pay_total'],
        'create_time' => time(),
        'company_id' => $this->user_arr['company_id'],
        'enter_agency_id' => $this->user_arr['agency_id'],//录入门店
        'enter_broker_id' => $this->user_arr['broker_id'] //录入人
      );
      $payment_add_per = $this->broker_permission_model->check('79');
      if (isset($payment_add_per['auth']) && $payment_add_per['auth']) {
        //添加
        $id = $this->collocation_contract_model->add_need_pay_info($datainfo, $type);
      } else {
        $this->redirect_permission_none();
        die();
      }

      if ($id) {
        //合同跟进——添加
        $add_data = array(
          'c_id' => $post_param['c_id'],
          'type_name' => "付款业主",
          'content' => "添加应付业主。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $this->collocation_contract_log_model->add_info($add_data);


        //操作日志
        $info = $this->collocation_contract_model->get_by_id($post_param['c_id']);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '新增合同编号为' . $info['collocation_id'] . '的托管合同的付款业主。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
        echo json_encode($data['result'] = 'ok');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    } elseif ($type == 2) {

      for ($i = 0; $i < $post_param['pay_times']; $i++) {
        //应付批量添加
        $datainfo = array(
          'c_id' => intval($post_param['c_id']),
          'collocation_id' => $post_param['collocation_id'],
          'rental' => $post_param['rental'],
          'water_fee' => $post_param['water_fee'],
          'ele_fee' => $post_param['ele_fee'],
          'gas_fee' => $post_param['gas_fee'],
          'int_fee' => $post_param['int_fee'],
          'need_pay_time' => strtotime($post_param['need_pay_time']),
          'stop_time' => strtotime($post_param['stop_time']),
          'tv_fee' => $post_param['tv_fee'],
          'property_fee' => $post_param['property_fee'],
          'preserve_fee' => $post_param['preserve_fee'],
          'garbage_fee' => $post_param['garbage_fee'],
          'other_fee' => $post_param['other_fee'],
          'remark' => trim($post_param['remark']),
          'status' => 1,//首次添加合同的时候默认状态是待审核
          //'fund_status' => 1,
          'pay_type' => $post_param['pay_type'],
          'pay_times' => $post_param['pay_times'],
          'total_fee' => $post_param['need_pay_total_pl'],
          'create_time' => time(),
          'company_id' => $this->user_arr['company_id'],
          'enter_agency_id' => $this->user_arr['agency_id'],//录入门店
          'enter_broker_id' => $this->user_arr['broker_id'] //录入人
        );

        $year_ = date('Y', strtotime($post_param['need_pay_time']));
        $month_ = date('m', strtotime($post_param['need_pay_time']));
        $d = days_in_month($month_, $year_);

        $payment_add_per = $this->broker_permission_model->check('79');
        if (isset($payment_add_per['auth']) && $payment_add_per['auth']) {
          //添加
          $id = $this->collocation_contract_model->add_need_pay_info($datainfo, $type);
        } else {
          $this->redirect_permission_none();
          die();
        }

        $need_pay_time = strtotime($post_param['need_pay_time']) + $d * 60 * 60 * 24;
        $post_param['need_pay_time'] = date('Y-m-d', $need_pay_time);
        //echo $post_param['need_pay_time'];die;
      }
      if ($id) {
        //合同跟进——添加
        $add_data = array(
          'c_id' => $post_param['c_id'],
          'type_name' => "付款业主",
          'content' => "添加应付业主。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($post_param['c_id']);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '新增合同编号为' . $info['collocation_id'] . '的托管合同的付款业主。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
        echo json_encode($data['result'] = 'ok');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    } elseif ($type == 3) {
      //实付
      if (empty($post_param['fund_status'])) {
        $post_param['fund_status'] = 1;
      }
      $datainfo = array(
        'c_id' => intval($post_param['c_id']),
        'collocation_id' => $post_param['collocation_id'],
        'rental' => $post_param['rental'],
        'water_fee' => $post_param['water_fee'],
        'ele_fee' => $post_param['ele_fee'],
        'gas_fee' => $post_param['gas_fee'],
        'int_fee' => $post_param['int_fee'],
        'actual_pay_time' => strtotime($post_param['actual_pay_time']),
        'actual_pay_type' => $post_param['actual_pay_type'],
        'tv_fee' => $post_param['tv_fee'],
        'property_fee' => $post_param['property_fee'],
        'preserve_fee' => $post_param['preserve_fee'],
        'garbage_fee' => $post_param['garbage_fee'],
        'other_fee' => $post_param['other_fee'],
        'agency_id' => intval($post_param['agency_id']),
        'broker_id' => intval($post_param['broker_id']),
        'remark' => trim($post_param['remark']),
        'receipts_num' => trim($post_param['receipts_num']),
        'status' => 1,//首次添加合同的时候默认状态是待审核
        'fund_status' => $post_param['fund_status'],
        'total_fee' => $post_param['actual_pay_total'],
        'create_time' => time(),
        'company_id' => $this->user_arr['company_id'],
        'enter_agency_id' => $this->user_arr['agency_id'],//录入门店
        'enter_broker_id' => $this->user_arr['broker_id'] //录入人
      );
      $payment_add_per = $this->broker_permission_model->check('79');
      if (isset($payment_add_per['auth']) && $payment_add_per['auth']) {
        //添加
        $id = $this->collocation_contract_model->add_need_pay_info($datainfo, $type);
      } else {
        $this->redirect_permission_none();
        die();
      }
      if ($id) {
        //合同跟进——添加
        $add_data = array(
          'c_id' => $post_param['c_id'],
          'type_name' => "付款业主",
          'content' => "添加实付业主。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($post_param['c_id']);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '新增合同编号为' . $info['collocation_id'] . '的托管合同的付款业主。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);

        echo json_encode($data['result'] = 'ok');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    } elseif ($type == 4) {
      //管家
      $datainfo = array(
        'c_id' => intval($post_param['c_id']),
        'collocation_id' => $post_param['collocation_id'],
        'project_name' => trim($post_param['project_name']),
        'total_fee' => $post_param['total_fee'],
        'owner_bear' => $post_param['owner_bear'],
        'customer_bear' => $post_param['customer_bear'],
        'company_bear' => $post_param['company_bear'],
        'reimbursement_time' => strtotime($post_param['reimbursement_time']),
        'withhold_time' => strtotime($post_param['withhold_time']),
        'agency_id' => $post_param['agency_id'],
        'remark' => trim($post_param['remark']),
        'status' => 1,//首次添加合同的时候默认状态是待审核
        'create_time' => time(),
        'company_id' => $this->user_arr['company_id'],
        'enter_agency_id' => $this->user_arr['agency_id'],//录入门店
        'enter_broker_id' => $this->user_arr['broker_id'] //录入人
      );
      $steward_add_per = $this->broker_permission_model->check('88');
      if (isset($steward_add_per['auth']) && $steward_add_per['auth']) {
        //添加
        $id = $this->collocation_contract_model->add_need_pay_info($datainfo, $type);
      } else {
        $this->redirect_permission_none();
        die();
      }

      if ($id) {
        //合同跟进——添加
        $add_data = array(
          'c_id' => $post_param['c_id'],
          'type_name' => "管家费用",
          'content' => "添加管家费用。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $this->collocation_contract_log_model->add_info($add_data);


        //操作日志
        $info = $this->collocation_contract_model->get_by_id($post_param['c_id']);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '新增合同编号为' . $info['collocation_id'] . '的托管合同的管家费用。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
        echo json_encode($data['result'] = 'ok');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    }

  }

  /**
   * 修改应付，实付，管家
   * @access public
   * @return void
   */
  public function need_pay_edit()
  {
    $id = $this->input->get('id');
    $tab = $this->input->get('tab');
    $data['arr'] = $this->collocation_contract_model->get_need_pay_by_id($id, $tab);
    if ($tab == 1) {
      $data['arr']['need_pay_time'] = date('Y-m-d', $data['arr']['need_pay_time']);
    } elseif ($tab == 2) {

      $data['arr']['actual_pay_time'] = date('Y-m-d', $data['arr']['actual_pay_time']);
      $this->broker_info_model->set_select_fields(array('broker_id', 'truename'));
      $data['broker_list'] = $this->broker_info_model->get_by_agency_id($data['arr']['agency_id']);
    } elseif ($tab == 3) {
      $data['arr']['reimbursement_time'] = date('Y-m-d', $data['arr']['reimbursement_time']);
      $data['arr']['withhold_time'] = date('Y-m-d', $data['arr']['withhold_time']);
    }
    if (is_full_array($data['arr'])) {
      $data['result'] = 1;
    } else {
      $data['result'] = 0;
    }
    echo json_encode($data);
  }

  /**
   * 保存修改应付业主,实付业主,管家费用
   * @access public
   * @return void
   */
  public function save_need_pay($tab)
  {
    $post_param = $this->input->post(NULL, TRUE);
    if ($tab == 1) {
      //应付业主修改信息数组
      $datainfo = array(
        'rental' => sprintf('%.2f', $post_param['rental']),
        'water_fee' => sprintf('%.2f', $post_param['water_fee']),
        'ele_fee' => sprintf('%.2f', $post_param['ele_fee']),
        'gas_fee' => sprintf('%.2f', $post_param['gas_fee']),
        'int_fee' => sprintf('%.2f', $post_param['int_fee']),
        'need_pay_time' => strtotime($post_param['need_pay_time']),
        'tv_fee' => sprintf('%.2f', $post_param['tv_fee']),
        'property_fee' => sprintf('%.2f', $post_param['property_fee']),
        'preserve_fee' => sprintf('%.2f', $post_param['preserve_fee']),
        'garbage_fee' => sprintf('%.2f', $post_param['garbage_fee']),
        'other_fee' => sprintf('%.2f', $post_param['other_fee']),
        'remark' => trim($post_param['remark']),
        'total_fee' => trim($post_param['need_pay_total'])
      );
      $payment_edit_per = $this->broker_permission_model->check('80');
      if (isset($payment_edit_per['auth']) && $payment_edit_per['auth']) {
        $data['result'] = $this->collocation_contract_model->update_need_pay_by_id($datainfo, $post_param['need_pay_id'], $tab);
      } else {
        $this->redirect_permission_none();
        die();
      }
      if ($data['result']) {
        $data = $this->collocation_contract_model->get_need_pay_by_id($post_param['need_pay_id'], '1');
        $content = $this->modify_match($datainfo, $data, '2');
        //修改应付业主
        $add_data = array(
          'c_id' => $post_param['c_id'],
          'type_name' => "付款业主",
          'content' => "对应付业主进行修改。" . $content,
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($post_param['c_id']);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '修改合同编号为' . $info['collocation_id'] . '的托管合同的付款业主。' . $content,
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
        echo json_encode($data['result'] = 'ok');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    } elseif ($tab == 2) {
      //实付业主修改信息数组
      if ($post_param['fund_status'] != 2) {
        $post_param['fund_status'] = 1;
      }
      $datainfo = array(
        'rental' => sprintf('%.2f', $post_param['rental']),
        'water_fee' => sprintf('%.2f', $post_param['water_fee']),
        'ele_fee' => sprintf('%.2f', $post_param['ele_fee']),
        'gas_fee' => sprintf('%.2f', $post_param['gas_fee']),
        'int_fee' => sprintf('%.2f', $post_param['int_fee']),
        'actual_pay_time' => strtotime($post_param['actual_pay_time']),
        'actual_pay_type' => $post_param['actual_pay_type'],
        'tv_fee' => sprintf('%.2f', $post_param['tv_fee']),
        'property_fee' => sprintf('%.2f', $post_param['property_fee']),
        'preserve_fee' => sprintf('%.2f', $post_param['preserve_fee']),
        'garbage_fee' => sprintf('%.2f', $post_param['garbage_fee']),
        'other_fee' => sprintf('%.2f', $post_param['other_fee']),
        'agency_id' => intval($post_param['agency_id']),
        'broker_id' => intval($post_param['broker_id']),
        'remark' => trim($post_param['remark']),
        'receipts_num' => trim($post_param['receipts_num']),
        'fund_status' => $post_param['fund_status'],
        'total_fee' => $post_param['actual_pay_total']
      );
      $payment_edit_per = $this->broker_permission_model->check('80');
      if (isset($payment_edit_per['auth']) && $payment_edit_per['auth']) {
        $data['result'] = $this->collocation_contract_model->update_need_pay_by_id($datainfo, $post_param['actual_pay_id'], $tab);
      } else {
        $this->redirect_permission_none();
        die();
      }

      if ($data['result']) {
        $data = $this->collocation_contract_model->get_need_pay_by_id($post_param['actual_pay_id'], '2');
        $content = $this->modify_match($datainfo, $data, '3');
        //修改实付业主
        $add_data = array(
          'c_id' => $post_param['c_id'],
          'type_name' => "付款业主",
          'content' => "对实付业主进行修改。" . $content,
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($post_param['c_id']);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '修改合同编号为' . $info['collocation_id'] . '的托管合同的付款业主。' . $content,
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);

        echo json_encode($data['result'] = 'ok');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    } elseif ($tab == 3) {
      //管家费用修改信息数组
      $datainfo = array(
        'project_name' => trim($post_param['project_name']),
        'total_fee' => sprintf('%.2f', $post_param['total_fee']),
        'owner_bear' => sprintf('%.2f', $post_param['owner_bear']),
        'customer_bear' => sprintf('%.2f', $post_param['customer_bear']),
        'company_bear' => sprintf('%.2f', $post_param['company_bear']),
        'reimbursement_time' => strtotime($post_param['reimbursement_time']),
        'withhold_time' => strtotime($post_param['withhold_time']),
        'agency_id' => $post_param['agency_id'],
        'remark' => trim($post_param['remark'])
      );
      $steward_edit_per = $this->broker_permission_model->check('86');
      if (isset($steward_edit_per['auth']) && $steward_edit_per['auth']) {
        $data['result'] = $this->collocation_contract_model->update_need_pay_by_id($datainfo, $post_param['steward_expenses_id'], $tab);
      } else {
        $this->redirect_permission_none();
        die();
      }

      if ($data['result']) {
        $data = $this->collocation_contract_model->get_need_pay_by_id($post_param['steward_expenses_id'], '3');
        $content = $this->modify_match($datainfo, $data, '4');
        //修改管家费用
        $add_data = array(
          'c_id' => $post_param['c_id'],
          'type_name' => "管家费用",
          'content' => "对管家费用进行修改。" . $content,
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($post_param['c_id']);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '修改合同编号为' . $info['collocation_id'] . '的托管合同的管家费用。' . $content,
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);

        echo json_encode($data['result'] = 'ok');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    }
  }

  /**
   * tab=1核销应付业主，在审核通过下，tab=2更改资金状态为已确认
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function cancel_verification()
  {
    $id = $this->input->get('id', TRUE);
    $c_id = $this->input->get('c_id', TRUE);
    $tab = $this->input->get('tab', TRUE);
    if ($tab == 1 || $tab == 2) {//实付确认付款
      $payment_sure_per = $this->broker_permission_model->check('84');
      if (isset($payment_sure_per['auth']) && $payment_sure_per['auth']) {
        $rs = $this->collocation_contract_model->update_need_pay_by_id(array("fund_status" => 2), $id, $tab);
        if ($rs) {
          //实付确认付款
          $add_data = array(
            'c_id' => $c_id,
            'type_name' => "付款业主",
            'content' => "实付业主已确认。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $this->collocation_contract_log_model->add_info($add_data);

          //操作日志
          $info = $this->collocation_contract_model->get_by_id($c_id);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '确认合同编号为' . $info['collocation_id'] . '的托管合同的付款业主。',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);
        }
      } else {
        $this->redirect_permission_none();
        die();
      }
    } elseif ($tab == 4) {//出租作废
      $rent_cancel_per = $this->broker_permission_model->check('93');
      if (isset($rent_cancel_per['auth']) && $rent_cancel_per['auth']) {
        $rs = $this->collocation_contract_model->update_need_pay_by_id(array("status" => 3), $id, $tab);
        if ($rs) {
          //出租作废
          $add_data = array(
            'c_id' => $c_id,
            'type_name' => "合同作废",
            'content' => "对该合同进行作废，合同已终止。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $this->collocation_contract_log_model->add_info($add_data);

          //操作日志
          $info = $this->collocation_contract_model->get_by_id($c_id);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '作废合同编号为' . $info['collocation_id'] . '的出租合同',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);
        }
      } else {
        $this->redirect_permission_none();
        die();
      }
    }
    if ($rs) {
      echo json_encode($data['result'] = 'ok');
      exit;
    } else {
      echo json_encode($data['result'] = 'no');
      exit;
    }
  }

  /**
   * 删除托管合同下应付业主的信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function del_need_pay()
  {
    $id = $this->input->get('id', TRUE);
    $c_id = $this->input->get('c_id', TRUE);
    $tab = $this->input->get('tab', TRUE);
    if ($tab == 1 || $tab == 2) {//付款业主
      $payment_del_per = $this->broker_permission_model->check('81');
      if (isset($payment_del_per['auth']) && $payment_del_per['auth']) {
        $rs = $this->collocation_contract_model->del_need_pay_by_id($id, $tab);
        if ($rs) {
          if ($tab == 1) {
            //删除应付业主
            $add_data = array(
              'c_id' => $c_id,
              'type_name' => "付款业主",
              'content' => "删除应付业主。",
              'broker_id' => $this->user_arr['broker_id'],
              'broker_name' => $this->user_arr['truename'],
              'updatetime' => time()
            );
            $this->collocation_contract_log_model->add_info($add_data);
          } else {
            //删除实付业主
            $add_data = array(
              'c_id' => $c_id,
              'type_name' => "付款业主",
              'content' => "删除实付业主。",
              'broker_id' => $this->user_arr['broker_id'],
              'broker_name' => $this->user_arr['truename'],
              'updatetime' => time()
            );
            $this->collocation_contract_log_model->add_info($add_data);
          }

          //操作日志
          $info = $this->collocation_contract_model->get_by_id($c_id);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '删除合同编号为' . $info['collocation_id'] . '的托管合同的付款业主。',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);

        }
      } else {
        $this->redirect_permission_none();
        die();
      }
    } elseif ($tab == 3) {//管家
      $steward_del_per = $this->broker_permission_model->check('87');
      if (isset($steward_del_per['auth']) && $steward_del_per['auth']) {
        $rs = $this->collocation_contract_model->del_need_pay_by_id($id, $tab);
        if ($rs) {
          //删除管家费用
          $add_data = array(
            'c_id' => $c_id,
            'type_name' => "管家费用",
            'content' => "删除管家费用。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $this->collocation_contract_log_model->add_info($add_data);


          //操作日志
          $info = $this->collocation_contract_model->get_by_id($post_param['c_id']);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '删除合同编号为' . $info['collocation_id'] . '的托管合同的管家费用。',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);
        }
      } else {
        $this->redirect_permission_none();
        die();
      }
    } elseif ($tab == 4) {//出租
      $rent_del_per = $this->broker_permission_model->check('92');
      if (isset($rent_del_per['auth']) && $rent_del_per['auth']) {
        $rs = $this->collocation_contract_model->del_need_pay_by_id($id, $tab);
        if ($rs) {
          //删除出租
          $add_data = array(
            'c_id' => $c_id,
            'type_name' => "出租合同删除",
            'content' => "删除出租合同。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $this->collocation_contract_log_model->add_info($add_data);
          //删除出租合同时同时删除下面的应收，实收
          $this->collocation_rent_contract_model->del_need_by_id($id);
          $this->collocation_rent_contract_model->del_actual_by_id($id);

          //操作日志
          $info = $this->collocation_contract_model->get_by_id($c_id);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '删除合同编号为' . $info['collocation_id'] . '的出租合同。',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);
        }
      } else {
        $this->redirect_permission_none();
        die();
      }
    }

    if ($rs) {
      echo json_encode($data['result'] = 'ok');
      exit;
    } else {
      echo json_encode($data['result'] = 'no');
      exit;
    }
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

  //托管合同弹窗的方法
  public function get_info()
  {
    $id = $this->input->post('id');
    $this->collocation_contract_model->set_id($id);
    $this->collocation_contract_model->set_search_fields(array('id', 'collocation_id', 'block_name', 'houses_address', 'block_id'));
    $result = $this->collocation_contract_model->get_collocationinfo_by_id();
    echo json_encode($result);
  }

  //托管房源弹窗的方法
  public function house_get_info()
  {
    $id = $this->input->post('id');
    $this->rent_house_model->set_id($id);
    $this->rent_house_model->set_search_fields(array('block_id', 'block_name', 'buildarea', 'address', 'sell_type', 'owner', 'idcare', 'telno1'));
    $result = $this->rent_house_model->get_info_by_id();
    $result['house_id'] = format_info_id($id, 'rent');

    echo json_encode($result);
  }


  /**
   * 录入托管合同下出租合同
   * @access public
   * @return void
   */
  public function add_rent_contract($id = 0)
  {
    $data = array();

    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //页面搜索条件
    $post_config = array();
    //默认本本店
    $data['agency_id'] = $this->user_arr['agency_id'];
    $range_menu = $this->contract_model->get_range_menu_by_role_level($this->user_arr, $this->user_arr['agency_id']);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];

    $data['post_config'] = $post_config;
    if ($id) {
      //合同详情
      $collo_list = $this->get_detail($id);

      //获取签约门店
      $agency_info = $this->api_broker_model->get_by_agency_id($collo_list['agency_id']);
      $collo_list['agency_name'] = $agency_info['name'];
      //获取签约人
      $broker_info = $this->broker_info_model->get_by_broker_id($collo_list['broker_id']);
      $collo_list['broker_name'] = $broker_info['truename'];
      $data['collo_list'] = $collo_list;
    }
    $submit_flag = $post_param['submit_flag'];
    if ($submit_flag == "add") {
      $where = "company_id = {$this->user_arr['company_id']} and collo_rent_id = '{$post_param['collo_rent_id']}'";
      $result = $this->collocation_rent_contract_model->get_one_by($where);
      if (is_full_array($result)) {
        echo json_encode(array('result' => '0', "msg" => "公司内已有该编号的合同！"));
        exit;
      } else {
        //托管合同添加信息数组
        $datainfo = array(
          'c_id' => intval($post_param['c_id']),
          'collocation_id' => $post_param['collocation_id'],
          'company_id' => $this->user_arr['company_id'],
          'collo_rent_id' => $post_param['collo_rent_id'],
          'block_name' => trim($post_param['block_name']),
          'block_id' => trim($post_param['block_id']),
          'agency_id_a' => trim($post_param['agency_id_a']),
          'broker_id_a' => trim($post_param['broker_id_a']),
          'houses_address' => trim($post_param['houses_address']),
          'rent_start_time' => strtotime($post_param['rent_start_time']),
          'rent_end_time' => strtotime($post_param['rent_end_time']),
          'rent_total_month' => $post_param['rent_total_month'],
          'signing_time' => strtotime($post_param['signing_time']),
          'customer_name' => trim($post_param['customer_name']),
          'customer_tel' => trim($post_param['customer_tel']),
          'customer_idcard' => trim($post_param['customer_idcard']),
          'pay_ditch' => trim($post_param['pay_ditch']),
          'agency_id' => intval($post_param['agency_id']),
          'broker_id' => intval($post_param['broker_id']),
          'broker_tel' => trim($post_param['broker_tel']),
          'rental' => $post_param['rental'],
          'pay_type' => trim($post_param['pay_type']),
          'rental_total' => $post_param['rental_total'],
          'desposit' => $post_param['desposit'],
          'penal_sum' => $post_param['penal_sum'],
          'tax_type' => intval($post_param['tax_type']),
          'property_fee' => $post_param['property_fee'],
          'agency_commission' => $post_param['agency_commission'],
          'rent_free_time' => intval($post_param['rent_free_time']),
          'rent_type' => trim($post_param['rent_type']),
          'property_manage_assume' => $post_param['property_manage_assume'] ? $post_param['property_manage_assume'] : '',
          'houses_preserve_agency_id' => trim($post_param['houses_preserve_agency_id']),
          'houses_preserve_broker_id' => trim($post_param['houses_preserve_broker_id']),
          'houses_preserve_money' => trim($post_param['houses_preserve_money']),
          'customer_preserve_agency_id' => trim($post_param['customer_preserve_agency_id']),
          'customer_preserve_broker_id' => trim($post_param['customer_preserve_broker_id']),
          'customer_preserve_money' => trim($post_param['customer_preserve_money']),
          'out_broker_agency_id' => trim($post_param['out_broker_agency_id']),
          'out_broker_broker_id' => trim($post_param['out_broker_broker_id']),
          'stop_agreement_num' => intval($post_param['stop_agreement_num']),
          'expire_time' => strtotime($post_param['expire_time']),
          'remark' => $post_param['remark'],
          'status' => 1//首次添加合同的时候默认状态是待审核
        );
        $rent_add_per = $this->broker_permission_model->check('90');
        if (isset($rent_add_per['auth']) && $rent_add_per['auth']) {
          //添加
          $id = $this->collocation_contract_model->add_rent_info($datainfo);
        } else {
          $this->redirect_permission_none();
          die();
        }

        if ($id) {
          //添加出租合同跟进
          $add_data = array(
            'c_id' => $post_param['c_id'],
            'type_name' => "合同录入",
            'content' => "录入出租合同，合同编号：" . $datainfo['collo_rent_id'] . "。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time()
          );
          $this->collocation_contract_log_model->add_info($add_data);

          //操作日志
          $info = $this->collocation_contract_model->get_by_id($post_param['c_id']);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '录入合同编号为' . $info['collocation_id'] . '的出租合同。',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);

          echo json_encode(array('result' => 'ok', "msg" => "添加出租合同成功"));
          exit;
        } else {
          echo json_encode(array('result' => 'no', "msg" => "添加出租合同失败"));
          exit;
        }
      }
    }

    //页面标题
    $data['page_title'] = '新增托管出租合同';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/house_manage.css,' . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,' . 'mls/js/v1.0/jquery.validate.min.js,' . 'mls/js/v1.0/verification_contract.js');
    if ($id) {
      //树型菜单
      $data['user_tree_menu'] = $this->permission_tab_model->get_tree_menu('collocation_contract', 'index');
      $this->view("collocation_contract/add_rent", $data);
    } else {
      //树型菜单/collocation_contract/rent_contract_list/
      $data['user_tree_menu'] = $this->permission_tab_model->get_tree_menu('collocation_contract', 'rent_contract_list');
      $this->view("collocation_contract/list_add_rent", $data);
    }

  }

  /**
   * 修改托管合同下出租合同
   * @access public
   * @return void
   */
  public function rent_modify($id = 0)
  {

    $data = array();

    //树型菜单
    $data['user_tree_menu'] = $this->permission_tab_model->get_tree_menu('collocation_contract', 'rent_contract_list');
    //有托管合同
    if (intval($id) > 0) {
      $role_level = $this->user_arr['role_level'];
      //合同详情
      $collo_rent_list = $this->get_rent_detail($id);
    }
    //页面搜索条件
    $post_config = array();
    //签约门店，人
    $range_menu = $this->contract_model->get_range_menu_by_role_level($this->user_arr, $collo_rent_list['agency_id']);
    $post_config['agencys'] = $range_menu['agencys'];
    $post_config['brokers'] = $range_menu['brokers'];
    //房源管理跟客源管理门店，人
    $range_menu2 = $this->contract_model->get_range_menu_by_role_level($this->user_arr, $collo_rent_list['houses_preserve_agency_id']);
    $post_config['houses_preserve_agencys'] = $range_menu2['agencys'];
    $post_config['houses_preserve_brokers'] = $range_menu2['brokers'];
    $range_menu3 = $this->contract_model->get_range_menu_by_role_level($this->user_arr, $collo_rent_list['customer_preserve_agency_id']);
    $post_config['customer_preserve_agencys'] = $range_menu3['agencys'];
    $post_config['customer_preserve_brokers'] = $range_menu3['brokers'];
    //退房经纪人
    $range_menu4 = $this->contract_model->get_range_menu_by_role_level($this->user_arr, $collo_rent_list['out_broker_agency_id']);
    $post_config['out_agencys'] = $range_menu4['agencys'];
    $post_config['out_brokers'] = $range_menu4['brokers'];
    $data['post_config'] = $post_config;


    //获取所属门店
    $agency_info = $this->api_broker_model->get_by_agency_id($collo_rent_list['agency_id_a']);
    $collo_rent_list['agency_name'] = $agency_info['name'];
    //获取所属经纪人
    $broker_info = $this->broker_info_model->get_by_broker_id($collo_rent_list['broker_id_a']);
    $collo_rent_list['broker_name'] = $broker_info['truename'];

    //根据出租合同的托管id查找托管编号
    $collocation_contract_info = $this->collocation_contract_model->get_by_id($collo_rent_list['c_id']);
    $collo_rent_list['collocation_id'] = $collocation_contract_info['collocation_id'];
    $data['collo_rent_list'] = $collo_rent_list;

    $post_param = $this->input->post(NULL, TRUE);
    $submit_flag = $post_param['submit_flag'];
    if ($submit_flag == "modify") {
      $where = "company_id = {$this->user_arr['company_id']} and collo_rent_id = '{$post_param['collo_rent_id']}' and id !='{$post_param['modify_id']}'";
      $result = $this->collocation_rent_contract_model->get_one_by($where);
      if (is_full_array($result)) {
        echo json_encode(array('result' => '0', "msg" => "公司内已有该编号的合同！"));
        exit;
      } else {
        //托管合同修改信息数组
        $datainfo = array(
          //'c_id' => intval($post_param['c_id']),
          'collo_rent_id' => $post_param['collo_rent_id'],
          //'block_name' => trim($post_param['block_name']),
          //'block_name' => trim($post_param['block_name']),
          //'agency_id_a' => trim($post_param['agency_id_a']),
          //'broker_id_a' => trim($post_param['broker_id_a']),
          //'houses_address' => trim($post_param['houses_address']),
          'rent_start_time' => strtotime($post_param['rent_start_time']),
          'rent_end_time' => strtotime($post_param['rent_end_time']),
          'rent_total_month' => $post_param['rent_total_month'],
          'signing_time' => strtotime($post_param['signing_time']),
          'customer_name' => $post_param['customer_name'],
          'customer_tel' => $post_param['customer_tel'],
          'customer_idcard' => $post_param['customer_idcard'],
          'pay_ditch' => trim($post_param['pay_ditch']),
          'agency_id' => intval($post_param['agency_id']),
          'broker_id' => intval($post_param['broker_id']),
          'broker_tel' => trim($post_param['broker_tel']),
          'rental' => sprintf('%.2f', $post_param['rental']),
          'pay_type' => trim($post_param['pay_type']),
          'rental_total' => sprintf('%.2f', $post_param['rental_total']),
          'desposit' => sprintf('%.2f', $post_param['desposit']),
          'penal_sum' => sprintf('%.2f', $post_param['penal_sum']),
          'tax_type' => intval($post_param['tax_type']),
          'property_fee' => sprintf('%.2f', $post_param['property_fee']),
          'agency_commission' => sprintf('%.2f', $post_param['agency_commission']),
          'rent_free_time' => intval($post_param['rent_free_time']),
          'rent_type' => trim($post_param['rent_type']),
          'property_manage_assume' => intval($post_param['property_manage_assume']),
          'houses_preserve_agency_id' => intval($post_param['houses_preserve_agency_id']),
          'houses_preserve_broker_id' => intval($post_param['houses_preserve_broker_id']),
          'houses_preserve_money' => trim($post_param['houses_preserve_money']),
          'customer_preserve_agency_id' => intval($post_param['customer_preserve_agency_id']),
          'customer_preserve_broker_id' => intval($post_param['customer_preserve_broker_id']),
          'customer_preserve_money' => trim($post_param['customer_preserve_money']),
          'out_broker_agency_id' => intval($post_param['out_broker_agency_id']),
          'out_broker_broker_id' => intval($post_param['out_broker_broker_id']),
          'stop_agreement_num' => intval($post_param['stop_agreement_num']),
          'expire_time' => strtotime($post_param['expire_time']),
          'remark' => trim($post_param['remark']),
          //'status' => 1//首次添加合同的时候默认状态是待审核

        );
        $rent_edit_per = $this->broker_permission_model->check('91');
        if (isset($rent_edit_per['auth']) && $rent_edit_per['auth']) {
          //添加
          $insertid = $this->collocation_contract_model->update_by_rent_id($datainfo, $id);
        } else {
          $this->redirect_permission_none();
          die();
        }
        if ($insertid) {
          $data = $this->collocation_contract_model->get_by_rent_id($post_param['id']);
          $content = $this->modify_match($datainfo, $data, '5');
          //修改出租合同
          $add_data = array(
            'c_id' => $post_param['id'],
            'type_name' => "合同修改",
            'content' => "对出租合同进行修改。" . $content,
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time(),
            'type' => '2'
          );
          $this->collocation_contract_log_model->add_info($add_data);

          //操作日志
          $info = $this->collocation_contract_model->get_by_id($post_param['c_id']);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '修改合同编号为' . $info['collocation_id'] . '的出租合同。' . $content,
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);

          echo json_encode(array('result' => 'ok', "msg" => "修改出租合同成功"));
          exit;
        } else {
          echo json_encode(array('result' => 'no', "msg" => "修改出租合同失败"));
          exit;
        }
      }
    }

    //页面标题
    $data['page_title'] = '修改托管出租合同';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/house_manage.css,' . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,' . 'mls/js/v1.0/jquery.validate.min.js,' . 'mls/js/v1.0/verification_contract.js');
    $this->view("collocation_contract/modify_rent", $data);
  }

  /**
   * 获取托管出租合同详情
   * @access public
   * @return array
   */
  public function get_rent_detail($id)
  {
    $data = $this->collocation_contract_model->get_by_rent_id($id);
    if (is_full_array($data)) {
      $config = $this->contract_config_model->get_config();
      $data['config'] = $config;
    }
    return $data;
  }

  /**
   * 出租合同详情--应收，实收，合同跟进
   * @access public
   * @return void
   * tab:1--应收（默认刚进页面是应收）  2--实收  3--合同跟进
   */
  public function rent_contract_detail($id, $tag = 1)
  {
    $data = array();
    //树型菜单
    $data['user_tree_menu'] = $this->permission_tab_model->get_tree_menu('collocation_contract', 'index');
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //对应id出租合同详情
    $collo_rent_detail = $this->get_rent_detail($id);
    //所属经纪人
    $agency_info1 = $this->api_broker_model->get_by_agency_id($collo_rent_detail['agency_id_a']);
    $collo_rent_detail['agency_name_a'] = $agency_info1['name'];
    $broker_info1 = $this->broker_info_model->get_by_broker_id($collo_rent_detail['broker_id_a']);
    $collo_rent_detail['broker_name_a'] = $broker_info1['truename'];
    //获取签约门店
    $agency_info = $this->api_broker_model->get_by_agency_id($collo_rent_detail['agency_id']);
    $collo_rent_detail['agency_name'] = $agency_info['name'];
    //获取签约人
    $broker_info = $this->broker_info_model->get_by_broker_id($collo_rent_detail['broker_id']);
    $collo_rent_detail['broker_name'] = $broker_info['truename'];
    //房源维护，客源维护
    $houses_preserve_agency_info = $this->api_broker_model->get_by_agency_id($collo_rent_detail['houses_preserve_agency_id']);
    $collo_rent_detail['houses_preserve_agency_name'] = $houses_preserve_agency_info['name'];
    $houses_preserve_broker_info = $this->broker_info_model->get_by_broker_id($collo_rent_detail['houses_preserve_broker_id']);
    $collo_rent_detail['houses_preserve_broker_name'] = $houses_preserve_broker_info['truename'];

    $customer_preserve_agency_info = $this->api_broker_model->get_by_agency_id($collo_rent_detail['customer_preserve_agency_id']);
    $collo_rent_detail['customer_preserve_agency_name'] = $customer_preserve_agency_info['name'];
    $customer_preserve_broker_info = $this->broker_info_model->get_by_broker_id($collo_rent_detail['customer_preserve_broker_id']);
    $collo_rent_detail['customer_preserve_broker_name'] = $customer_preserve_broker_info['truename'];
    //退房经纪人
    $out_broker_agency_info = $this->api_broker_model->get_by_agency_id($collo_rent_detail['out_broker_agency_id']);
    $collo_rent_detail['out_broker_agency_name'] = $out_broker_agency_info['name'];
    $out_broker_broker_info = $this->broker_info_model->get_by_broker_id($collo_rent_detail['out_broker_broker_id']);
    $collo_rent_detail['out_broker_broker_name'] = $out_broker_broker_info['truename'];

    //根据出租合同的托管id查找托管编号
    $collocation_contract_info = $this->collocation_contract_model->get_by_id($collo_rent_detail['c_id']);
    $collo_rent_detail['collocation_id'] = $collocation_contract_info['collocation_id'];
    $collo_rent_detail['house_id'] = $collocation_contract_info['house_id'];
    $collo_rent_detail['houses_area'] = $collocation_contract_info['houses_area'];
    $collo_rent_detail['type'] = $collocation_contract_info['type'];
    $data['collo_rent_detail'] = $collo_rent_detail;
    $data['tag'] = $tag;
    $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($this->user_arr['broker_id']);
    $data['brokerinfo'] = $brokerinfo;

    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['broker_id'] = $this->user_arr['broker_id'];
    }
    $post_config = array();
    //付款人
    $range_menu = $this->contract_model->get_range_menu_by_role_level($this->user_arr, $post_param['agency_id']);
    $post_config['agencys'] = $range_menu['agencys'];
    $post_config['brokers'] = $range_menu['brokers'];
    $data['post_config'] = $post_config;
    //收款客户添加、编辑和删除、确认付款
    $receipt_add_per = $this->broker_permission_model->check('96');
    $receipt_edit_per = $this->broker_permission_model->check('97');
    $receipt_del_per = $this->broker_permission_model->check('98');
    $receipt_sure_per = $this->broker_permission_model->check('101');
    $data['auth'] = array(
      'add' => $receipt_add_per, 'edit' => $receipt_edit_per,
      'delete' => $receipt_del_per, 'sure' => $receipt_sure_per
    );
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //通过托管合同id去应付表里找对应的应付业主数据
    if ($tag == 1) {
      //符合条件的总行数
      $this->_total_count =
        $this->collocation_rent_contract_model->count_by_rent_tag(array('r_id' => $collo_rent_detail['id']), '1');
      //应收
      $need_receive = $this->collocation_rent_contract_model->get_list_by_rid(array('r_id' => $collo_rent_detail['id']), $tag, $this->_offset, $this->_limit);

      //获取应收总价
      foreach ($need_receive as $val) {
        $need_total_fee += $val['total_fee'];
        $data['need_total_fee'] = $need_total_fee;
      }
      $actual_date = $this->collocation_rent_contract_model->get_list_by_rid(array('r_id' => $collo_rent_detail['id']), '2');
      //获取实收总价
      foreach ($actual_date as $val) {
        $actual_total_fee += $val['total_fee'];
        $data['actual_total_fee'] = $actual_total_fee;
      }

      //获取应收跟实收之间的差值
      $data['value'] = $data['need_total_fee'] - $data['actual_total_fee'];

      $data['need_receive'] = $need_receive;
    } elseif ($tag == 2) {
      //符合条件的总行数
      $this->_total_count =
        $this->collocation_rent_contract_model->count_by_rent_tag(array('r_id' => $collo_rent_detail['id']), '2');
      //实收----实收添加的前提是该合同下面有应收
      $need_date = $this->collocation_rent_contract_model->get_list_by_rid(array('r_id' => $collo_rent_detail['id']), 1);
      $data['need_date'] = $need_date;

      $actual_receive = $this->collocation_rent_contract_model->get_list_by_rid(array('r_id' => $collo_rent_detail['id']), $tag, $this->_offset, $this->_limit);

      //获取应收总价
      foreach ($need_date as $val) {
        $need_total_fee += $val['total_fee'];
        $data['need_total_fee'] = $need_total_fee;
      }
      //获取实收总价
      foreach ($actual_receive as $val) {
        $actual_total_fee += $val['total_fee'];
        $data['actual_total_fee'] = $actual_total_fee;
      }

      //获取应收跟实收之间的差值
      $data['value'] = $data['need_total_fee'] - $data['actual_total_fee'];

      $data['actual_receive'] = $actual_receive;
    } elseif ($tag == 3) {
      //符合条件的总行数
      $this->_total_count =
        $this->collocation_rent_contract_model->count_by_rent_tag(array('c_id' => $collo_rent_detail['id'], 'type' => '2'), '3');
      //跟进
      $follow = $this->collocation_rent_contract_model->get_list_by_rid(array('c_id' => $collo_rent_detail['id'], 'type' => '2'), $tag, $this->_offset, $this->_limit);
      $data['follow'] = $follow;
    }
    //获取该公司下的所有门店
    $data['agency'] = $this->agency_model->get_children_by_company_id($this->user_arr['company_id']);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
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

    if (!$id || !is_full_array($data)) {
      $error_url = '/collocation_contract';
      $page_text = '参数错误';
      $this->jump($error_url, $page_text);
    }
    $data['user_menu'] = $this->user_menu;
    //echo '<pre>';print_r($data);die;
    //页面标题
    $data['page_title'] = '出租合同详情页';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,' . 'mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,' . 'mls/js/v1.0/scrollPic.js,' . 'mls/js/v1.0/radio_checkbox_mod2.js');
    $this->view("collocation_contract/rent_contract_info", $data);
  }

  /**
   * 删除出租合同下应收客户的信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function del_need_receive()
  {
    $id = $this->input->get('id', TRUE);
    $r_id = $this->input->get('r_id', TRUE);
    $tag = $this->input->get('tag', TRUE);
    $receipt_detele_per = $this->broker_permission_model->check('98');
    if (isset($receipt_detele_per['auth']) && $receipt_detele_per['auth']) {
      $rs = $this->collocation_rent_contract_model->del_need_receive_by_id($id, $tag);
    } else {
      $this->redirect_permission_none();
      die();
    }
    if ($rs) {
      if ($tag == 1) {//应收
        //删除应收客户
        $add_data = array(
          'c_id' => $r_id,
          'type_name' => "收款客户",
          'content' => "删除应收客户。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time(),
          'type' => '2'
        );
        $this->collocation_contract_log_model->add_info($add_data);
      } else {//实收
        //删除实收客户
        $add_data = array(
          'c_id' => $r_id,
          'type_name' => "收款客户",
          'content' => "删除实收客户。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time(),
          'type' => '2'
        );
        $this->collocation_contract_log_model->add_info($add_data);
      }
      //操作日志
      $info = $this->collocation_contract_model->get_by_id($r_id);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'text' => '删除合同编号为' . $info['collocation_id'] . '的托管合同的收款客户。',
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      $this->operate_log_model->add_operate_log($add_log_param);
      echo json_encode($data['result'] = 'ok');
      exit;
    } else {
      echo json_encode($data['result'] = 'no');
      exit;
    }
  }

  /**
   * 录入出租合同下  1--单次应收添加  2--批量应收添加  3--单次实收添加
   * @access public
   * @return void
   */
  public function add_need_receive($type = 0)
  {
    $post_param = $this->input->post(NULL, TRUE);
    //echo '<pre>';print_r($post_param);die;
    if ($type == 1) {
      //应收单次添加
      $datainfo = array(
        'r_id' => intval($post_param['r_id']),
        'collocation_id' => $post_param['collocation_id'],
        'collo_rent_id' => $post_param['collo_rent_id'],
        'rental' => $post_param['rental'],
        'water_fee' => $post_param['water_fee'],
        'ele_fee' => $post_param['ele_fee'],
        'gas_fee' => $post_param['gas_fee'],
        'int_fee' => $post_param['int_fee'],
        'need_receive_time' => strtotime($post_param['need_receive_time']),
        'tv_fee' => $post_param['tv_fee'],
        'property_fee' => $post_param['property_fee'],
        'preserve_fee' => $post_param['preserve_fee'],
        'garbage_fee' => $post_param['garbage_fee'],
        'other_fee' => $post_param['other_fee'],
        'remark' => trim($post_param['remark']),
        'status' => 1,//首次添加合同的时候默认状态是待审核
        'pay_type' => 5,
        'pay_times' => 1,//单次添加是次数为1
        'total_fee' => $post_param['need_receive_total'],
        'create_time' => time(),
        'company_id' => $this->user_arr['company_id'],
        'enter_agency_id' => $this->user_arr['agency_id'],//录入门店
        'enter_broker_id' => $this->user_arr['broker_id'] //录入人
      );
      $receipt_add_per = $this->broker_permission_model->check('96');
      if (isset($receipt_add_per['auth']) && $receipt_add_per['auth']) {
        //添加
        $id = $this->collocation_rent_contract_model->add_need_receive_info($datainfo, $type);
      } else {
        $this->redirect_permission_none();
        die();
      }
      if ($id) {
        //添加应收客户
        $add_data = array(
          'c_id' => $post_param['r_id'],
          'type_name' => "收款客户",
          'content' => "添加应收客户。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time(),
          'type' => '2'
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($post_param['r_id']);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '添加合同编号为' . $info['collocation_id'] . '的托管合同的收款客户。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);

        echo json_encode($data['result'] = 'ok');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    } elseif ($type == 2) {

      for ($i = 0; $i < $post_param['pay_times']; $i++) {
        //应收批量添加
        $datainfo = array(
          'r_id' => intval($post_param['r_id']),
          'collocation_id' => $post_param['collocation_id'],
          'collo_rent_id' => $post_param['collo_rent_id'],
          'rental' => $post_param['rental'],
          'water_fee' => $post_param['water_fee'],
          'ele_fee' => $post_param['ele_fee'],
          'gas_fee' => $post_param['gas_fee'],
          'int_fee' => $post_param['int_fee'],
          'need_receive_time' => strtotime($post_param['need_receive_time']),
          'stop_time' => strtotime($post_param['stop_time']),
          'tv_fee' => $post_param['tv_fee'],
          'property_fee' => $post_param['property_fee'],
          'preserve_fee' => $post_param['preserve_fee'],
          'garbage_fee' => $post_param['garbage_fee'],
          'other_fee' => $post_param['other_fee'],
          'remark' => trim($post_param['remark']),
          'status' => 1,//首次添加合同的时候默认状态是待审核
          'pay_type' => $post_param['pay_type'],
          'pay_times' => $post_param['pay_times'],
          'total_fee' => $post_param['need_receive_total_pl'],
          'create_time' => time(),
          'company_id' => $this->user_arr['company_id'],
          'enter_agency_id' => $this->user_arr['agency_id'],//录入门店
          'enter_broker_id' => $this->user_arr['broker_id'] //录入人
        );
        $year_ = date('Y', strtotime($post_param['need_receive_time']));
        $month_ = date('m', strtotime($post_param['need_receive_time']));
        $d = days_in_month($month_, $year_);
        $receipt_add_per = $this->broker_permission_model->check('96');
        if (isset($receipt_add_per['auth']) && $receipt_add_per['auth']) {
          //添加
          $id = $this->collocation_rent_contract_model->add_need_receive_info($datainfo, $type);
        } else {
          $this->redirect_permission_none();
          die();
        }
        $need_receive_time = strtotime($post_param['need_receive_time']) + $d * 60 * 60 * 24;
        $post_param['need_receive_time'] = date('Y-m-d', $need_receive_time);
      }
      if ($id) {
        //添加应收客户
        $add_data = array(
          'c_id' => $post_param['r_id'],
          'type_name' => "收款客户",
          'content' => "添加应收客户。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time(),
          'type' => '2'
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($r_id);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '添加合同编号为' . $info['collocation_id'] . '的托管合同的收款客户。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);

        echo json_encode($data['result'] = 'ok');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    } elseif ($type == 3) {
      //实收
      $datainfo = array(
        'r_id' => intval($post_param['r_id']),
        'collocation_id' => $post_param['collocation_id'],
        'collo_rent_id' => $post_param['collo_rent_id'],
        'rental' => $post_param['rental'],
        'water_fee' => $post_param['water_fee'],
        'ele_fee' => $post_param['ele_fee'],
        'gas_fee' => $post_param['gas_fee'],
        'int_fee' => $post_param['int_fee'],
        'actual_receive_time' => strtotime($post_param['actual_receive_time']),
        'agency_id' => $post_param['agency_id'],
        'broker_id' => $post_param['broker_id'],
        'receipt_type' => $post_param['receipt_type'],
        'tv_fee' => $post_param['tv_fee'],
        'property_fee' => $post_param['property_fee'],
        'preserve_fee' => $post_param['preserve_fee'],
        'garbage_fee' => $post_param['garbage_fee'],
        'other_fee' => $post_param['other_fee'],
        'remark' => trim($post_param['remark']),
        'receipts_num' => trim($post_param['receipts_num']),
        'status' => 1,//首次添加合同的时候默认状态是待审核
        'fund_status' => 1,//首次添加默认为付款
        'slot_card_fee' => $post_param['slot_card_fee'],
        'total_fee' => $post_param['actual_receive_total'],
        'create_time' => time(),
        'company_id' => $this->user_arr['company_id'],
        'enter_agency_id' => $this->user_arr['agency_id'],//录入门店
        'enter_broker_id' => $this->user_arr['broker_id'] //录入人
      );
      $receipt_add_per = $this->broker_permission_model->check('96');
      if (isset($receipt_add_per['auth']) && $receipt_add_per['auth']) {
        //添加
        $id = $this->collocation_rent_contract_model->add_need_receive_info($datainfo, $type);
      } else {
        $this->redirect_permission_none();
        die();
      }
      if ($id) {
        //添加实收客户
        $add_data = array(
          'c_id' => $post_param['r_id'],
          'type_name' => "收款客户",
          'content' => "添加实收客户。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time(),
          'type' => '2'
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($r_id);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '添加合同编号为' . $info['collocation_id'] . '的托管合同的收款客户。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);

        echo json_encode($data['result'] = 'ok');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    }

  }

  /**
   * 修改 出租 应收，实收
   * @access public
   * @return void
   */
  public function rent_edit()
  {
    $id = $this->input->get('id');
    $tag = $this->input->get('tag');
    $data['arr'] = $this->collocation_rent_contract_model->get_need_receive_by_id($id, $tag);
    if ($tag == 1) {
      $data['arr']['need_receive_time'] = date('Y-m-d', $data['arr']['need_receive_time']);
    } elseif ($tag == 2) {
      $data['arr']['actual_receive_time'] = date('Y-m-d', $data['arr']['actual_receive_time']);
      $this->broker_info_model->set_select_fields(array('broker_id', 'truename'));
      $data['broker_list'] = $this->broker_info_model->get_by_agency_id($data['arr']['agency_id']);
    }
    if (is_full_array($data['arr'])) {
      $data['result'] = 1;
    } else {
      $data['result'] = 0;
    }
    echo json_encode($data);
  }

  /**
   * 保存修改出租应收客户，实收客户
   * @access public
   * @return void
   */
  public function save_need_receive($tag)
  {
    $post_param = $this->input->post(NULL, TRUE);
    //$agency = $this->agency_model->get_one_by(array('id'=>$post_param['agency_id']));
    //$broker = $this->broker_info_model->get_by_broker_id($post_param['broker_id']);
    if ($tag == 1) {
      //应收客户修改信息数组
      $datainfo = array(
        'rental' => sprintf('%.2f', $post_param['rental']),
        'water_fee' => sprintf('%.2f', $post_param['water_fee']),
        'ele_fee' => sprintf('%.2f', $post_param['ele_fee']),
        'gas_fee' => sprintf('%.2f', $post_param['gas_fee']),
        'int_fee' => sprintf('%.2f', $post_param['int_fee']),
        'need_receive_time' => strtotime($post_param['need_receive_time']),
        'tv_fee' => sprintf('%.2f', $post_param['tv_fee']),
        'property_fee' => sprintf('%.2f', $post_param['property_fee']),
        'preserve_fee' => sprintf('%.2f', $post_param['preserve_fee']),
        'garbage_fee' => sprintf('%.2f', $post_param['garbage_fee']),
        'other_fee' => sprintf('%.2f', $post_param['other_fee']),
        'remark' => trim($post_param['remark']),
        'total_fee' => sprintf('%.2f', $post_param['need_receive_total'])
      );
      $receipt_edit_per = $this->broker_permission_model->check('97');
      if (isset($receipt_edit_per['auth']) && $receipt_edit_per['auth']) {
        $data['result'] = $this->collocation_rent_contract_model->update_need_receive_by_id($datainfo, $post_param['need_receive_id'], $tag);
      } else {
        $this->redirect_permission_none();
        die();
      }

      if ($data['result']) {
        $data = $this->collocation_rent_contract_model->get_need_receive_by_id($post_param['need_receive_id'], '1');
        $content = $this->modify_match($datainfo, $data, '6');
        //修改应收客户
        $add_data = array(
          'c_id' => $post_param['r_id'],
          'type_name' => "收款客户",
          'content' => "对应收客户进行修改。" . $content,
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time(),
          'type' => '2'
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($post_param['r_id']);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '修改合同编号为' . $info['collocation_id'] . '的出租合同的收款客户。' . $content,
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
        echo json_encode($data['result'] = 'ok');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    } elseif ($tag == 2) {
      //实收客户修改信息数组
      /*if($post_param['fund_status'] != 2){
				$post_param['fund_status'] = 1;
			}*/
      $datainfo = array(
        //'r_id' => intval($post_param['r_id']),
        'rental' => sprintf('%.2f', $post_param['rental']),
        'water_fee' => sprintf('%.2f', $post_param['water_fee']),
        'ele_fee' => sprintf('%.2f', $post_param['ele_fee']),
        'gas_fee' => sprintf('%.2f', $post_param['gas_fee']),
        'int_fee' => sprintf('%.2f', $post_param['int_fee']),
        'actual_receive_time' => strtotime($post_param['actual_receive_time']),
        'receipt_type' => $post_param['receipt_type'],
        'tv_fee' => sprintf('%.2f', $post_param['tv_fee']),
        'property_fee' => sprintf('%.2f', $post_param['property_fee']),
        'preserve_fee' => sprintf('%.2f', $post_param['preserve_fee']),
        'garbage_fee' => sprintf('%.2f', $post_param['garbage_fee']),
        'other_fee' => sprintf('%.2f', $post_param['other_fee']),
        'remark' => trim($post_param['remark']),
        'receipts_num' => trim($post_param['receipts_num']),
        //'status' => 1,//首次添加合同的时候默认状态是待审核
        'slot_card_fee' => sprintf('%.2f', $post_param['slot_card_fee']),
        'total_fee' => sprintf('%.2f', $post_param['actual_receive_total'])
      );
      $receipt_edit_per = $this->broker_permission_model->check('97');
      if (isset($receipt_edit_per['auth']) && $receipt_edit_per['auth']) {
        $data['result'] = $this->collocation_rent_contract_model->update_need_receive_by_id($datainfo, $post_param['actual_receive_id'], $tag);
      } else {
        $this->redirect_permission_none();
        die();
      }

      if ($data['result']) {
        $data = $this->collocation_rent_contract_model->get_need_receive_by_id($post_param['actual_receive_id'], '2');
        $content = $this->modify_match($datainfo, $data, '7');
        //修改实收客户
        $add_data = array(
          'c_id' => $post_param['r_id'],
          'type_name' => "收款客户",
          'content' => "对实收客户进行修改。" . $content,
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time(),
          'type' => '2'
        );
        $this->collocation_contract_log_model->add_info($add_data);
        //操作日志
        $info = $this->collocation_contract_model->get_by_id($post_param['r_id']);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '修改合同编号为' . $info['collocation_id'] . '的出租合同的收款客户。' . $content,
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
        echo json_encode($data['result'] = 'ok');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    }
  }

  /**
   * 在审核通过下，tag=2实收客户，确认付款
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function sure_receipt()
  {
    $id = $this->input->get('id', TRUE);
    $r_id = $this->input->get('r_id', TRUE);
    $tag = $this->input->get('tag', TRUE);
    $receipt_sure_per = $this->broker_permission_model->check('101');
    if (isset($receipt_sure_per['auth']) && $receipt_sure_per['auth']) {
      $rs = $this->collocation_rent_contract_model->update_need_receive_by_id(array("fund_status" => 2), $id, $tag);
    } else {
      $this->redirect_permission_none();
      die();
    }

    if ($rs) {
      //实收客户确认付款
      $add_data = array(
        'c_id' => $r_id,
        'type_name' => "收款客户",
        'content' => "实收客户已确认。",
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'updatetime' => time(),
        'type' => '2'
      );
      $this->collocation_contract_log_model->add_info($add_data);

      //操作日志
      $info = $this->collocation_contract_model->get_by_id($r_id);
      $add_log_param = array(
        'company_id' => $this->user_arr['company_id'],
        'agency_id' => $this->user_arr['agency_id'],
        'broker_id' => $this->user_arr['broker_id'],
        'broker_name' => $this->user_arr['truename'],
        'type' => 35,
        'text' => '确认编号为' . $info['collocation_id'] . '的托管合同的收款客户。',
        'from_system' => 1,
        'from_ip' => get_ip(),
        'mac_ip' => '127.0.0.1',
        'from_host_name' => '127.0.0.1',
        'hardware_num' => '测试硬件序列号',
        'time' => time()
      );
      $this->operate_log_model->add_operate_log($add_log_param);
      echo json_encode($data['result'] = 'ok');
      exit;
    } else {
      echo json_encode($data['result'] = 'no');
      exit;
    }
  }

  //管家费用列表
  public function steward_list($page = 1)
  {

    //模板使用数据
    $data = array();
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;
    //页面搜索条件
    $post_config = array();
    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['agency_id']);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];

    //查询房源条件
    $cond_where = "";

    //将模糊查询里传来的合同编号找出所对应的托管合同ID
    /*if($post_param['collocation_id'] != ''){
			$collo_data = $this->collocation_contract_model->get_by_collocation_id($post_param['collocation_id']);
			$post_param['c_id'] = $collo_data['id'] == '' ? 0: $collo_data['id'];
		}*/
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str_rent($post_param);
    $cond_where .= $cond_where_ext;

    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);
    //符合条件的总行数
    $this->_total_count =
      $this->collocation_contract_model->count_by_tab($cond_where, $tab = '3');

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->collocation_contract_model->get_list_by_tab($cond_where, $this->_offset, $this->_limit, $tab = '3', 'reimbursement_time');

    if ($list) {
      foreach ($list as $key => $val) {
        //获取报销门店
        $agency_info = $this->api_broker_model->get_by_agency_id($val['agency_id']);
        $list[$key]['agency_name'] = $agency_info['name'];
        $collo_detail = $this->get_detail($val['c_id']);
        $list[$key]['collocation_id'] = $collo_detail['collocation_id'];
      }
    }
    $data['list'] = $list;
    $data['post_config'] = $post_config;

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
    $data['page_title'] = '管家费用列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css,mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('collocation_contract/steward_list', $data);
  }

  //出租合同列表
  public function rent_contract_list($page = 1)
  {

    //模板使用数据
    $data = array();
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;
    //页面搜索条件
    $post_config = array();
    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['agency_id']);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];
    $data['post_config'] = $post_config;
    //查询房源条件
    $cond_where = "";
    //将模糊查询里传来的合同编号找出所对应的托管合同ID
    /*if($post_param['collocation_id'] != ''){
			$collo_data = $this->collocation_contract_model->get_by_collocation_id($post_param['collocation_id']);
			$post_param['c_id'] = $collo_data['id'] == '' ? 0 : $collo_data['id'];
		}*/

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str_rent($post_param);
    $cond_where .= $cond_where_ext;

    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);
    //符合条件的总行数
    $this->_total_count =
      $this->collocation_contract_model->count_by_tab($cond_where, $tab = '4');

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->collocation_contract_model->get_list_by_tab($cond_where, $this->_offset, $this->_limit, $tab = '4');

    if ($list) {
      foreach ($list as $key => $val) {
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
        //echo '<pre>';print_r($brokerinfo);die;
        $list[$key]['broker_name'] = $brokerinfo['truename'];
        $list[$key]['agency_name'] = $brokerinfo['agency_name'];
        $collo_detail = $this->get_detail($val['c_id']);
        $list[$key]['collocation_id'] = $collo_detail['collocation_id'];
      }
    }
    $data['list'] = $list;
    //出租添加、编辑和删除,作废
    $rent_add_per = $this->broker_permission_model->check('90');
    $rent_edit_per = $this->broker_permission_model->check('91');
    $rent_del_per = $this->broker_permission_model->check('92');
    $rent_cancel_per = $this->broker_permission_model->check('93');
    $data['auth'] = array(
      'add' => $rent_add_per, 'edit' => $rent_edit_per,
      'delete' => $rent_del_per, 'cancel' => $rent_cancel_per
    );
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
    $data['page_title'] = '出租合同列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css,mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('collocation_contract/rent_list', $data);
  }

  /**
   * 托管合同出租列表条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str_rent($form_param)
  {
    $cond_where = '';
    /*//托管编号得到的合同DI
		$c_id= $form_param['c_id'];
		if($c_id)
		{
			$cond_where .= " AND c_id = '".$c_id."'";
		}elseif($c_id === 0){
			$cond_where .= " AND c_id = '-1'";
		}*/
    //出租合同编号
    $collocation_id = isset($form_param['collocation_id']) ? $form_param['collocation_id'] : 0;
    if ($collocation_id) {
      $cond_where .= " AND collocation_id like '%" . $collocation_id . "%'";
    }
    //出租合同编号
    $collo_rent_id = isset($form_param['collo_rent_id']) ? $form_param['collo_rent_id'] : 0;
    if ($collo_rent_id) {
      $cond_where .= " AND collo_rent_id like '%" . $collo_rent_id . "%'";
    }
    //楼盘ID
    if (!empty($form_param['block_name']) && $form_param['block_id'] > 0) {
      $cond_where .= " AND block_id = '" . $form_param['block_id'] . "'";
    }
    //客户姓名
    $customer_name = isset($form_param['customer_name']) ? $form_param['customer_name'] : 0;
    if ($customer_name) {
      $cond_where .= " AND customer_name = '" . $customer_name . "'";
    }
    //付款方式
    $pay_type = isset($form_param['pay_type']) ? intval($form_param['pay_type']) : 0;
    if ($pay_type) {
      $cond_where .= " AND pay_type = '" . $pay_type . "'";
    }
    //审核合同状态
    $status = isset($form_param['status']) ? intval($form_param['status']) : 0;
    if ($status) {
      $cond_where .= " AND status = '" . $status . "'";
    }
    //合同所属公司
    if (isset($form_param['company_id']) && $form_param['company_id'] > 0) {
      $cond_where .= " AND company_id = '" . $form_param['company_id'] . "'";
    }
    //签约门店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    //签约人
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($broker_id) {
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    //时间条件
    date_default_timezone_set('PRC');
    //托管开始时间，托管结束时间，签约时间
    $search_where = isset($form_param['search_where']) ? $form_param['search_where'] : 0;
    $search_where_time = isset($form_param['search_where_time']) ? $form_param['search_where_time'] : 0;
    $time_s = isset($form_param['time_s']) ? strtotime($form_param['time_s']) : 0;
    $time_e = isset($form_param['time_e']) ? strtotime($form_param['time_e']) : 0;
    $time_st = isset($form_param['time_st']) ? strtotime($form_param['time_st']) : 0;
    $time_et = isset($form_param['time_et']) ? strtotime($form_param['time_et']) : 0;
    if ($search_where_time) {
      if ($time_st && $time_et && $time_st > $time_et) {
        $this->jump(MLS_URL . '/contract/', '您查询的开始时间不能大于结束时间！');
        exit;
      }
      if ($search_where_time == 'reimbursement_time') {
        //报销日期
        if ($time_st) {

          $cond_where .= " AND reimbursement_time >= '" . $time_st . "'";
        }
        if ($time_et) {

          $cond_where .= " AND reimbursement_time <= '" . $time_et . "'";
        }
      } elseif ($search_where_time == 'withhold_time') {
        //出租开始时间
        if ($time_st) {

          $cond_where .= " AND withhold_time >= '" . $time_st . "'";
        }
        if ($time_et) {

          $cond_where .= " AND withhold_time <= '" . $time_et . "'";
        }
      }
    }
    if ($search_where) {

      if ($time_s && $time_e && $time_s > $time_e) {
        $this->jump(MLS_URL . '/contract/', '您查询的开始时间不能大于结束时间！');
        exit;
      }

      if ($search_where == 'signing_time') {
        //签约日期
        if ($time_s) {

          $cond_where .= " AND signing_time >= '" . $time_s . "'";
        }
        if ($time_e) {

          $cond_where .= " AND signing_time <= '" . $time_e . "'";
        }
      } elseif ($search_where == 'collo_start_time') {
        //出租开始时间
        if ($time_s) {

          $cond_where .= " AND rent_start_time >= '" . $time_s . "'";
        }
        if ($time_e) {

          $cond_where .= " AND rent_start_time <= '" . $time_e . "'";
        }
      } else {
        //托管结束时间
        if ($time_s) {

          $cond_where .= " AND `collocation_contract`.rent_end_time >= '" . $time_s . "'";
        }
        if ($time_e) {

          $cond_where .= " AND `collocation_contract`.rent_end_time <= '" . $time_e . "'";
        }
      }
    }

    return $cond_where;
  }

  //付款业主列表
  public function pay_owner_list($tab = 1)
  {

    //模板使用数据
    $data = array();
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    $data['tab'] = $tab;
    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;
    //页面搜索条件
    $post_config = array();
    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post['agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post['agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post['broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post['agency_id']);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];


    //查询房源条件
    $cond_where = "";
    //将模糊查询里传来的合同编号找出所对应的托管合同ID
    /*if ($post_param['collocation_id'] != '')
		{
			$collo_data = $this->collocation_contract_model->get_by_collocation_id($post_param['collocation_id']);
			$post_param['c_id'] = $collo_data['id'] == '' ? 0 : $collo_data['id'];
		}*/


    //权限问题暂时遗留

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str_pay($post_param);
    $cond_where .= $cond_where_ext;

    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);

    //符合条件的总行数
    if ($tab == 1) {
      $this->_total_count = $this->collocation_contract_model->count_by_tab($cond_where, $tab = '1');
    } else {
      $this->_total_count = $this->collocation_contract_model->count_by_tab($cond_where, $tab = '2');
    }
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    if ($tab == 1) {
      //获取应付列表内容
      $list = $this->collocation_contract_model->get_list_by_tab($cond_where, $this->_offset, $this->_limit, $tab = '1', 'need_pay_time');
    } else {
      //获取实付列表内容
      $list = $this->collocation_contract_model->get_list_by_tab($cond_where, $this->_offset, $this->_limit, $tab = '2', 'actual_pay_time');
    }
    /*$list_new = array();
			$list = array_merge($list1,$list2);
			foreach($list as $key=>$val){
				if($key >= $this->_offset && $key < ($this->_offset+$this->_limit)){
					$list_new[] = $list[$key];
				}
			}*/
    //echo '<pre>';print_r($list_new);die;
    if ($list) {
      foreach ($list as $key => $val) {
        $collo_detail = $this->get_detail($val['c_id']);
        $list[$key]['collocation_id'] = $collo_detail['collocation_id'];
        $list[$key]['house_id'] = $collo_detail['house_id'];
        $list[$key]['signing_time'] = $collo_detail['signing_time'];
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
    $data['page_title'] = '付款业主列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css,mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('collocation_contract/pay_owner_list', $data);
  }

  //付款查询条件
  private function _get_cond_str_pay($form_param)
  {
    $cond_where = '';
    /*//托管编号获取的合同ID
		$c_id= $form_param['c_id'];
		if($c_id)
		{
			$cond_where .= " AND c_id = '".$c_id."'";
		}elseif($c_id === 0){
			$cond_where .= " AND c_id = '-1'";
		}*/
    //托管编号
    $collocation_id = isset($form_param['collocation_id']) ? $form_param['collocation_id'] : 0;
    if ($collocation_id) {
      $cond_where .= " AND collocation_id like '%" . $collocation_id . "%'";
    }
    /*//托管编号获取的出租合同ID
		$r_id= isset($form_param['r_id'])?$form_param['r_id']:0;
		if($r_id)
		{
			$cond_where .= " AND r_id = '".$r_id."'";
		}*/
    //出租编号
    $collo_rent_id = isset($form_param['collo_rent_id']) ? $form_param['collo_rent_id'] : 0;
    if ($collo_rent_id) {
      $cond_where .= " AND collo_rent_id like '%" . $collo_rent_id . "%'";
    }
    //房源编号
    $house_id = isset($form_param['house_id']) ? $form_param['house_id'] : 0;
    if ($house_id) {
      $cond_where .= " AND house_id = '" . $house_id . "'";
    }
    //审核合同状态
    $status = isset($form_param['status']) ? intval($form_param['status']) : 0;
    if ($status) {
      $cond_where .= " AND status = '" . $status . "'";
    }
    //合同所属公司
    if (isset($form_param['company_id']) && $form_param['company_id'] > 0) {
      $cond_where .= " AND company_id = '" . $form_param['company_id'] . "'";
    }
    //时间条件
    date_default_timezone_set('PRC');
    //应付时间，实付时间
    $search_where = isset($form_param['search_where']) ? $form_param['search_where'] : 0;

    $time_s = isset($form_param['time_s']) ? strtotime($form_param['time_s']) : 0;
    $time_e = isset($form_param['time_e']) ? strtotime($form_param['time_e']) : 0;


    if ($search_where) {

      if ($time_s && $time_e && $time_s > $time_e) {
        $this->jump(MLS_URL . '/contract/', '您查询的开始时间不能大于结束时间！');
        exit;
      }

      if ($search_where == 'need_pay_time') {
        //应付日期
        if ($time_s) {

          $cond_where .= " AND need_pay_time >= '" . $time_s . "'";
        }
        if ($time_e) {

          $cond_where .= " AND need_pay_time <= '" . $time_e . "'";
        }
      } elseif ($search_where == 'actual_pay_time') {
        //实付时间
        if ($time_s) {

          $cond_where .= " AND actual_pay_time >= '" . $time_s . "'";
        }
        if ($time_e) {

          $cond_where .= " AND actual_pay_time <= '" . $time_e . "'";
        }
      } elseif ($search_where == 'need_receive_time') {
        //应收日期
        if ($time_s) {

          $cond_where .= " AND need_receive_time >= '" . $time_s . "'";
        }
        if ($time_e) {

          $cond_where .= " AND need_receive_time <= '" . $time_e . "'";
        }
      } elseif ($search_where == 'actual_receive_time') {
        //实收日期
        if ($time_s) {

          $cond_where .= " AND actual_receive_time >= '" . $time_s . "'";
        }
        if ($time_e) {

          $cond_where .= " AND actual_receive_time <= '" . $time_e . "'";
        }
      }
    }

    return $cond_where;
  }

  //收款客户列表
  public function receive_customer_list($tag = 1)
  {

    //模板使用数据
    $data = array();
    $data['tag'] = $tag;
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;
    //页面搜索条件
    $post_config = array();
    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['agency_id']);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];
    //获取该公司下的所有门店
    //$data['agency'] = $this->agency_model->get_children_by_company_id($this->user_arr['company_id']);

    //查询房源条件
    $cond_where = "";

    /*//将模糊查询里传来的合同编号找出所对应的托管合同ID
		$collo_data = $this->collocation_contract_model->get_by_collocation_id($post_param['collocation_id']);
		//print_r($collo_data);echo '<br/>';
		//根据托管合同ID去出租表里找信息
		$rent_data = $this->collocation_contract_model->get_by_collocation_id_rent($collo_data['id']);
		//print_r($rent_data);
		foreach($rent_data as $val){
			$post_param['r_id'] = $val['id'];
		}*/
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str_pay($post_param);
    $cond_where .= $cond_where_ext;

    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);
    //符合条件的总行数
    if ($tag == 1) {
      $this->_total_count = $this->collocation_rent_contract_model->count_by_tag($cond_where, $tag = '1');
    } else {
      $this->_total_count = $this->collocation_rent_contract_model->count_by_tag($cond_where, $tag = '2');
    }
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    if ($tag == 1) {
      //获取应付列表内容
      $list = $this->collocation_rent_contract_model->get_list_by_tag($cond_where, $this->_offset, $this->_limit, $tag = '1', 'need_receive_time');
    } else {
      //获取实付列表内容
      $list = $this->collocation_rent_contract_model->get_list_by_tag($cond_where, $this->_offset, $this->_limit, $tag = '2', 'actual_receive_time');
    }
    /*$list_new = array();
			$list = array_merge($list1,$list2);
			foreach($list as $key=>$val){
				if($key >= $this->_offset && $key < ($this->_offset+$this->_limit)){
					$list_new[] = $list[$key];
				}
			}*/
    //echo '<pre>';print_r($list_new);die;
    if ($list) {
      foreach ($list as $key => $val) {
        $collo_rent_detail = $this->get_rent_detail($val['r_id']);
        $list[$key]['collo_rent_id'] = $collo_rent_detail['collo_rent_id'];
        $collo_detail = $this->get_detail($collo_rent_detail['c_id']);
        $list[$key]['collocation_id'] = $collo_detail['collocation_id'];
        $list[$key]['house_id'] = $collo_detail['house_id'];
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
    $data['page_title'] = '收款客户列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css,mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('collocation_contract/receive_customer_list', $data);
  }

  //托管合同的审核
  public function collocation_audit($page = 1)
  {
    //模板使用数据
    $data = array();
    //post参数
    $post_param = $this->input->post(NULL, TRUE);

    $data['post_param'] = $post_param;
    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;
    //页面搜索条件
    $post_config = array();
    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['agency_id']);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];

    //查询房源条件
    $cond_where = "";
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str_audit($post_param);
    $cond_where .= $cond_where_ext;

    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);
    //符合条件的总行数
    $this->_total_count =
      $this->collocation_contract_model->count_by($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->collocation_contract_model->get_list_by($cond_where, $this->_offset, $this->_limit);

    if ($list) {
      foreach ($list as $key => $val) {
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
        $list[$key]['broker_name'] = $brokerinfo['truename'];
        $list[$key]['agency_name'] = $brokerinfo['agency_name'];
      }
    }

    //算出总待审核的条数
    $wait_audit = 'status = 1 and ' . $cond_where;
    $total = $this->_total_count =
      $this->collocation_contract_model->count_by($wait_audit);
    $data['total'] = $total;
    //托管合同审核，反审核
    $collocation_audit = $this->broker_permission_model->check('124');
    $collocation_turn_audit = $this->broker_permission_model->check('125');
    $data['auth'] = array(
      'audit' => $collocation_audit, 'turn_audit' => $collocation_turn_audit
    );
    $data['post_config'] = $post_config;
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
    $data['page_title'] = '托管合同审核列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css,mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('collocation_contract/collocation_audit', $data);
  }


  /**
   * 托管合同审核列表条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str_audit($form_param)
  {
    $cond_where = '';
    //托管编号
    $collocation_id = isset($form_param['collocation_id']) ? $form_param['collocation_id'] : 0;
    if ($collocation_id) {
      $cond_where .= " AND collocation_id like '%" . $collocation_id . "%'";
    }
    //房源编号
    $collo_rent_id = isset($form_param['collo_rent_id']) ? $form_param['collo_rent_id'] : 0;
    if ($collo_rent_id) {
      $cond_where .= " AND collo_rent_id like '%" . $collo_rent_id . "%'";
    }
    //审核合同状态
    $status = isset($form_param['status']) ? intval($form_param['status']) : 0;
    if ($status) {
      $cond_where .= " AND status = '" . $status . "'";
    }
    //合同所属公司
    if (isset($form_param['company_id']) && $form_param['company_id'] > 0) {
      $cond_where .= " AND company_id = '" . $form_param['company_id'] . "'";
    }
    //签约门店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    //签约人
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($broker_id) {
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    //签约时间条件
    date_default_timezone_set('PRC');
    if (isset($form_param['start_time']) && $form_param['start_time']) {
      $cond_where .= " AND signing_time >= '" . strtotime($form_param['start_time']) . "'";
    }

    if (isset($form_param['end_time']) && !empty($form_param['end_time'])) {
      $cond_where .= " AND signing_time <= '" . strtotime($form_param['end_time']) . "'";
    }
    return $cond_where;
  }

  //付款业主审核列表
  public function pay_owner_audit($tab = 1)
  {

    //模板使用数据
    $data = array();
    $data['tab'] = $tab;
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;
    //页面搜索条件
    $post_config = array();
    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['agency_id']);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];
    //查询房源条件
    $cond_where = "";

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str_pay_audit($post_param);
    $cond_where .= $cond_where_ext;

    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);


    //符合条件的总行数
    if ($tab == 1) {
      $this->_total_count = $this->collocation_contract_model->count_by_tab($cond_where, $tab = '1');

    } else {
      $this->_total_count = $this->collocation_contract_model->count_by_tab($cond_where, $tab = '2');

    }
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    if ($tab == 1) {
      //获取应付列表内容
      $list = $this->collocation_contract_model->get_list_by_tab($cond_where, $this->_offset, $this->_limit, $tab = '1', 'create_time');
    } else {
      //获取实付列表内容
      $list = $this->collocation_contract_model->get_list_by_tab($cond_where, $this->_offset, $this->_limit, $tab = '2', 'create_time');
    }
    //echo '<pre>';print_r($list);die;
    if ($list) {
      foreach ($list as $key => $val) {
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['enter_broker_id']);
        $list[$key]['broker_name'] = $brokerinfo['truename'];
        $list[$key]['agency_name'] = $brokerinfo['agency_name'];
        $collo_detail = $this->get_detail($val['c_id']);
        $list[$key]['collocation_id'] = $collo_detail['collocation_id'];

      }
    }
    //应付待审核的条数
    if ($cond_where) {
      $where = $cond_where . 'and status = 1';
    } else {
      $where = 'status = 1';
    }
    $data['wait_audit_1'] = $this->collocation_contract_model->count_by_tab($where, $tab = '1');

    //实付待审核的条数
    if ($cond_where) {
      $where = $cond_where . 'and status = 1';
    } else {
      $where = 'status = 1';
    }
    $data['wait_audit_2'] = $this->collocation_contract_model->count_by_tab($where, $tab = '2');

    $data['list'] = $list;
    //付款 -- 审核，反审核
    $payment_audit = $this->broker_permission_model->check('82');
    $payment_turn_audit = $this->broker_permission_model->check('83');
    $data['auth'] = array(
      'audit' => $payment_audit, 'turn_audit' => $payment_turn_audit
    );
    $data['post_config'] = $post_config;


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
    $data['page_title'] = '付款业主审核列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css,mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('collocation_contract/pay_owner_audit', $data);
  }

  //付款查询条件
  private function _get_cond_str_pay_audit($form_param)
  {
    $cond_where = '';
    //托管编号
    $collocation_id = isset($form_param['collocation_id']) ? $form_param['collocation_id'] : 0;
    if ($collocation_id) {
      $cond_where .= " AND collocation_id like '%" . $collocation_id . "%'";
    }
    //出租合同编号
    $collo_rent_id = isset($form_param['collo_rent_id']) ? $form_param['collo_rent_id'] : 0;
    if ($collo_rent_id) {
      $cond_where .= " AND collo_rent_id like '%" . $collo_rent_id . "%'";
    }
    //审核合同状态
    $status = isset($form_param['status']) ? intval($form_param['status']) : 0;
    if ($status) {
      $cond_where .= " AND status = '" . $status . "'";
    }
    //合同所属公司
    if (isset($form_param['company_id']) && $form_param['company_id'] > 0) {
      $cond_where .= " AND company_id = '" . $form_param['company_id'] . "'";
    }
    //时间条件
    date_default_timezone_set('PRC');
    //录入门店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND enter_agency_id = '" . $agency_id . "'";
    }
    //录入人
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($broker_id) {
      $cond_where .= " AND enter_broker_id = '" . $broker_id . "'";
    }
    //录入时间条件
    date_default_timezone_set('PRC');
    if (isset($form_param['start_time']) && $form_param['start_time']) {
      $cond_where .= " AND create_time >= '" . strtotime($form_param['start_time']) . "'";
    }

    if (isset($form_param['end_time']) && !empty($form_param['end_time'])) {
      $cond_where .= " AND create_time <= '" . strtotime($form_param['end_time']) . "'";
    }

    return $cond_where;
  }

  //管家费用审核列表
  public function steward_audit($page = 1)
  {

    //模板使用数据
    $data = array();
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;

    //页面搜索条件
    $post_config = array();
    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['agency_id']);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];

    //查询房源条件
    $cond_where = "";

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str_pay_audit($post_param);
    $cond_where .= $cond_where_ext;

    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);
    //符合条件的总行数
    $this->_total_count =
      $this->collocation_contract_model->count_by_tab($cond_where, $tab = '3');

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->collocation_contract_model->get_list_by_tab($cond_where, $this->_offset, $this->_limit, $tab = '3', 'create_time');

    if ($list) {
      foreach ($list as $key => $val) {
        //获取报销门店
        $agency_info = $this->api_broker_model->get_by_agency_id($val['agency_id']);
        $list[$key]['agency_name'] = $agency_info['name'];
        $collo_detail = $this->get_detail($val['c_id']);
        $list[$key]['collocation_id'] = $collo_detail['collocation_id'];
        //获取录入门店，录入人
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['enter_broker_id']);
        $list[$key]['broker_name'] = $brokerinfo['truename'];
        $list[$key]['agency'] = $brokerinfo['agency_name'];
      }
    }
    $data['list'] = $list;
    //获取总待审核条数
    $audit_total = 'status = 1 and ' . $cond_where;
    $total = $this->_total_count = $this->collocation_contract_model->count_by_tab($audit_total, $tab = '3');
    $data['total'] = $total;
    //管家 审核，反审核
    $steward_audit = $this->broker_permission_model->check('88');
    $steward_turn_audit = $this->broker_permission_model->check('89');
    $data['auth'] = array(
      'audit' => $steward_audit, 'turn_audit' => $steward_turn_audit
    );
    $data['post_config'] = $post_config;

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
    $data['page_title'] = '管家费用审核列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css,mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('collocation_contract/steward_audit', $data);
  }

  //出租合同审核列表
  public function rent_contract_audit($page = 1)
  {

    //模板使用数据
    $data = array();
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;
    //页面搜索条件
    $post_config = array();
    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['agency_id']);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];

    //查询房源条件
    $cond_where = "";

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str_audit($post_param);
    $cond_where .= $cond_where_ext;

    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);
    //符合条件的总行数
    $this->_total_count =
      $this->collocation_contract_model->count_by_tab($cond_where, $tab = '4');
    $data['total_count'] = $this->_total_count;
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->collocation_contract_model->get_list_by_tab($cond_where, $this->_offset, $this->_limit, $tab = '4');

    if ($list) {
      foreach ($list as $key => $val) {
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
        //echo '<pre>';print_r($brokerinfo);die;
        $list[$key]['broker_name'] = $brokerinfo['truename'];
        $list[$key]['agency_name'] = $brokerinfo['agency_name'];
        $collo_detail = $this->get_detail($val['c_id']);
        $list[$key]['collocation_id'] = $collo_detail['collocation_id'];
      }
    }
    $data['list'] = $list;
    $audit = 'status = 1 and ' . $cond_where;
    $total = $this->_total_count =
      $this->collocation_contract_model->count_by_tab($audit, $tab = '4');
    $data['total'] = $total;
    //出租 审核，反审核
    $rent_audit = $this->broker_permission_model->check('94');
    $rent_turn_audit = $this->broker_permission_model->check('95');
    $data['auth'] = array(
      'audit' => $rent_audit, 'turn_audit' => $rent_turn_audit
    );
    $data['post_config'] = $post_config;
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
    $data['page_title'] = '出租合同审核列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css,mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('collocation_contract/rent_audit', $data);
  }

  //收款客户审核列表
  public function receive_customer_audit($tag = 1)
  {

    //模板使用数据
    $data = array();
    $data['tag'] = $tag;
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;
    //页面搜索条件
    $post_config = array();
    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['agency_id']);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];

    //查询房源条件
    $cond_where = "";


    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str_pay_audit($post_param);
    $cond_where .= $cond_where_ext;

    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);
    //符合条件的总行数
    if ($tag == 1) {
      $this->_total_count = $this->collocation_rent_contract_model->count_by_tag($cond_where, $tag = '1');
    } else {
      $this->_total_count = $this->collocation_rent_contract_model->count_by_tag($cond_where, $tag = '2');
    }
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    if ($tag == 1) {
      //获取应付列表内容
      $list = $this->collocation_rent_contract_model->get_list_by_tag($cond_where, $this->_offset, $this->_limit, $tag = '1', 'need_receive_time');
    } else {
      //获取实付列表内容
      $list = $this->collocation_rent_contract_model->get_list_by_tag($cond_where, $this->_offset, $this->_limit, $tag = '2', 'actual_receive_time');
    }
    if ($list) {
      foreach ($list as $key => $val) {
        $collo_rent_detail = $this->get_rent_detail($val['r_id']);
        $list[$key]['collo_rent_id'] = $collo_rent_detail['collo_rent_id'];
        $collo_detail = $this->get_detail($collo_rent_detail['c_id']);
        $list[$key]['collocation_id'] = $collo_detail['collocation_id'];
        $list[$key]['house_id'] = $collo_detail['house_id'];
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($val['enter_broker_id']);
        $list[$key]['agency_name'] = $brokerinfo['agency_name'];
        $list[$key]['broker_name'] = $brokerinfo['truename'];
      }
    }
    //应收待审核的条数
    if ($cond_where) {
      $where = $cond_where . 'and status = 1';
    } else {
      $where = 'status = 1';
    }
    $data['wait_audit_1'] = $this->collocation_rent_contract_model->count_by_tag($where, $tag = '1');

    //实收待审核的条数
    if ($cond_where) {
      $where = $cond_where . 'and status = 1';
    } else {
      $where = 'status = 1';
    }
    $data['wait_audit_2'] = $this->collocation_rent_contract_model->count_by_tag($where, $tag = '2');
    $data['list'] = $list;
    //收款 审核，反审核
    $receipt_audit = $this->broker_permission_model->check('99');
    $receipt_turn_audit = $this->broker_permission_model->check('100');
    $data['auth'] = array(
      'audit' => $receipt_audit, 'turn_audit' => $receipt_turn_audit
    );
    $data['post_config'] = $post_config;
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
    $data['page_title'] = '收款客户审核列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/contract_manage.css,mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    $this->view('collocation_contract/receive_customer_audit', $data);
  }

  //托管审核
  public function audit_agreement()
  {

    $id = $this->input->get('id', TRUE);
    $audit_end = $this->input->get('audit_end', TRUE);
    $audit_view = $this->input->get('audit_view', TRUE);
    if ($audit_end == 1) {
      $collocation_audit = $this->broker_permission_model->check('124');
      if (isset($collocation_audit['auth']) && $collocation_audit['auth']) {
        $updater_arr = array("status" => 2, 'audit_view' => $audit_view, 'check_time' => time());
        $updater_arr['company_id'] = $this->user_arr['company_id'];
        //审核通过
        $rs = $this->collocation_contract_model->update_by_id($updater_arr, $id);
      } else {
        $this->redirect_permission_none();
        die();
      }

      if ($rs) {
        //合同跟进——审核
        $add_data = array(
          'c_id' => $id,
          'type_name' => "合同审核",
          'content' => "合同通过审核。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($id);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '审核合同编号为' . $info['collocation_id'] . '的托管合同。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
        echo json_encode($data['result'] = 'ok');
        exit;
      } else {
        echo json_encode($data['result'] = '1');
        exit;
      }
    } elseif ($audit_end == 2) {
      $collocation_audit = $this->broker_permission_model->check('124');
      if (isset($collocation_audit['auth']) && $collocation_audit['auth']) {
        $updater_arr = array("status" => 4, 'audit_view' => $audit_view);
        $updater_arr['company_id'] = $this->user_arr['company_id'];
        //审核不通过
        $rs = $this->collocation_contract_model->update_by_id($updater_arr, $id);
      } else {
        $this->redirect_permission_none();
        die();
      }

      if ($rs) {
        //合同跟进——审核
        $add_data = array(
          'c_id' => $id,
          'type_name' => "合同审核",
          'content' => "合同未通过审核。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($id);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '审核合同编号为' . $info['collocation_id'] . '的托管合同。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);

        echo json_encode($data['result'] = 'no');
        exit;
      } else {
        echo json_encode($data['result'] = '-1');
        exit;
      }
    } else {//反审核，将状态改成待审核
      $collocation_turn_audit = $this->broker_permission_model->check('125');
      if (isset($collocation_turn_audit['auth']) && $collocation_turn_audit['auth']) {
        //搜索参数范围权限控制
        $updater_arr = array('status' => 1);
        $role_level = $this->user_arr['role_level'];
        if ($role_level < 6) //公司
        {
          //所属公司
          $updater_arr['company_id'] = $this->user_arr['company_id'];
        } else if ($role_level < 8) //门店
        {
          //所属公司
          $updater_arr['company_id'] = $this->user_arr['company_id'];
          //所属门店
          //$updater_arr['agency_id'] = $this->user_arr['agency_id'];
        } else {
          //公司权限
          $updater_arr['company_id'] = $this->user_arr['company_id'];
          //所属门店
          //$updater_arr['agency_id'] = $this->user_arr['agency_id'];
          //所属经纪人
          //$updater_arr['broker_id'] = $this->user_arr['broker_id'];
        }
        $rs = $this->collocation_contract_model->update_by_id($updater_arr, $id);
      } else {
        $this->redirect_permission_none();
        die();
      }

      if ($rs) {
        //合同跟进——反审核
        $add_data = array(
          'c_id' => $id,
          'type_name' => "合同审核",
          'content' => "对合同进行反审核操作。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time()
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($id);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '反审核合同编号为' . $info['collocation_id'] . '的托管合同。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);

        echo json_encode($data['result'] = 'ok');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    }
  }

  //应付，实付，管家，出租审核
  public function pay_audit()
  {
    $id = $this->input->get('id', TRUE);
    $type = $this->input->get('type', TRUE);
    $tab = $this->input->get('tab', TRUE);
    $audit_end = $this->input->get('audit_end', TRUE);
    $audit_view = $this->input->get('audit_view', TRUE);
    $c_id = $this->input->get('c_id', TRUE);
    if ($tab == 4) {//出租的
      if ($audit_end == 1) {
        $rent_audit = $this->broker_permission_model->check('94');
        if (isset($rent_audit['auth']) && $rent_audit['auth']) {
          $updater_arr = array("status" => 2, 'audit_view' => $audit_view, 'check_time' => time());
          $updater_arr['company_id'] = $this->user_arr['company_id'];
          //审核通过
          $rs = $this->collocation_contract_model->update_need_pay_by_id($updater_arr, $id, '4');
        } else {
          $this->redirect_permission_none();
          die();
        }

        if ($rs) {
          //合同跟进——审核
          $add_data = array(
            'c_id' => $c_id,
            'type_name' => "合同审核",
            'content' => "对合同进行审核操作。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time(),
            'type' => '2'
          );
          $this->collocation_contract_log_model->add_info($add_data);

          //操作日志
          $info = $this->collocation_contract_model->get_by_id($c_id);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '审核合同编号为' . $info['collocation_id'] . '的出租合同。',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);

          echo json_encode($data['result'] = 'ok');
          exit;
        } else {
          echo json_encode($data['result'] = '1');
          exit;
        }
      } elseif ($audit_end == 2) {
        $rent_audit = $this->broker_permission_model->check('94');
        if (isset($rent_audit['auth']) && $rent_audit['auth']) {
          $updater_arr = array("status" => 4, 'audit_view' => $audit_view);
          $updater_arr['company_id'] = $this->user_arr['company_id'];
          //审核不通过
          $rs = $this->collocation_contract_model->update_need_pay_by_id($updater_arr, $id, '4');
        } else {
          $this->redirect_permission_none();
          die();
        }

        if ($rs) {
          //合同跟进——审核
          $add_data = array(
            'c_id' => $c_id,
            'type_name' => "合同审核",
            'content' => "对合同进行审核不通过操作。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time(),
            'type' => '2'
          );
          $this->collocation_contract_log_model->add_info($add_data);

          //操作日志
          $info = $this->collocation_contract_model->get_by_id($c_id);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '审核合同编号为' . $info['collocation_id'] . '的出租合同。',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);

          echo json_encode($data['result'] = 'no');
          exit;
        } else {
          echo json_encode($data['result'] = '-1');
          exit;
        }
      } else {//反审核，将状态改成待审核
        $rent_turn_audit = $this->broker_permission_model->check('95');
        if (isset($rent_turn_audit['auth']) && $rent_turn_audit['auth']) {
          $updater_arr = array("status" => 1);
          $updater_arr['company_id'] = $this->user_arr['company_id'];
          $rs = $this->collocation_contract_model->update_need_pay_by_id($updater_arr, $id, '4');
        } else {
          $this->redirect_permission_none();
          die();
        }

        if ($rs) {
          //合同跟进——审核
          $add_data = array(
            'c_id' => $c_id,
            'type_name' => "合同审核",
            'content' => "对合同进行反审核操作。",
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'updatetime' => time(),
            'type' => '2'
          );
          $this->collocation_contract_log_model->add_info($add_data);

          //操作日志
          $info = $this->collocation_contract_model->get_by_id($c_id);
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '反审核合同编号为' . $info['collocation_id'] . '的出租合同。',
            'from_system' => 1,
            'from_ip' => get_ip(),
            'mac_ip' => '127.0.0.1',
            'from_host_name' => '127.0.0.1',
            'hardware_num' => '测试硬件序列号',
            'time' => time()
          );
          $this->operate_log_model->add_operate_log($add_log_param);

          echo json_encode($data['result'] = 'ok');
          exit;
        } else {
          echo json_encode($data['result'] = 'no');
          exit;
        }
      }
    } else {//应付，实付，管家
      if ($type == 1) {//审核通过
        //付款审核
        if ($tab == 1 || $tab == 2) {
          $payment_audit = $this->broker_permission_model->check('82');
          if (isset($payment_audit['auth']) && $payment_audit['auth']) {
            $updater_arr['company_id'] = $this->user_arr['company_id'];
            $updater_arr = array("status" => 2);
            $rs = $this->collocation_contract_model->update_need_pay_by_id($updater_arr, $id, $tab);
            if ($rs) {
              //付款业主——审核
              $add_data = array(
                'c_id' => $c_id,
                'type_name' => "财务审核",
                'content' => "对付款业主进行审核操作。",
                'broker_id' => $this->user_arr['broker_id'],
                'broker_name' => $this->user_arr['truename'],
                'updatetime' => time()
              );
              $this->collocation_contract_log_model->add_info($add_data);

              //操作日志
              $info = $this->collocation_contract_model->get_by_id($c_id);
              $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'agency_id' => $this->user_arr['agency_id'],
                'broker_id' => $this->user_arr['broker_id'],
                'broker_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '审核合同编号为' . $info['collocation_id'] . '的托管合同的付款业主。',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
              );
              $this->operate_log_model->add_operate_log($add_log_param);
            }
          } else {
            $this->redirect_permission_none();
            die();
          }
        } else {//管家审核
          $steward_audit = $this->broker_permission_model->check('88');
          if (isset($steward_audit['auth']) && $steward_audit['auth']) {
            $updater_arr['company_id'] = $this->user_arr['company_id'];
            $updater_arr = array("status" => 2);
            $rs = $this->collocation_contract_model->update_need_pay_by_id($updater_arr, $id, $tab);
            if ($rs) {
              //管家——审核
              $add_data = array(
                'c_id' => $c_id,
                'type_name' => "财务审核",
                'content' => "对管家费用进行审核操作。",
                'broker_id' => $this->user_arr['broker_id'],
                'broker_name' => $this->user_arr['truename'],
                'updatetime' => time()
              );
              $this->collocation_contract_log_model->add_info($add_data);

              //操作日志
              $info = $this->collocation_contract_model->get_by_id($c_id);
              $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'agency_id' => $this->user_arr['agency_id'],
                'broker_id' => $this->user_arr['broker_id'],
                'broker_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '审核合同编号为' . $info['collocation_id'] . '的托管合同的管家费用。',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
              );
              $this->operate_log_model->add_operate_log($add_log_param);
            }
          } else {
            $this->redirect_permission_none();
            die();
          }
        }
        if ($rs) {
          echo json_encode($data['result'] = 'ok1');
          exit;
        } else {
          echo json_encode($data['result'] = 'no');
          exit;
        }
      } elseif ($type == 2) {//审核不通过
        //付款审核
        if ($tab == 1 || $tab == 2) {
          $payment_audit = $this->broker_permission_model->check('82');
          if (isset($payment_audit['auth']) && $payment_audit['auth']) {
            $updater_arr['company_id'] = $this->user_arr['company_id'];
            $updater_arr = array("status" => 3);
            $rs = $this->collocation_contract_model->update_need_pay_by_id($updater_arr, $id, $tab);
            if ($rs) {
              //付款业主——审核不通过
              $add_data = array(
                'c_id' => $c_id,
                'type_name' => "财务审核",
                'content' => "对付款业主进行审核不通过操作。",
                'broker_id' => $this->user_arr['broker_id'],
                'broker_name' => $this->user_arr['truename'],
                'updatetime' => time()
              );
              $this->collocation_contract_log_model->add_info($add_data);

              //操作日志
              $info = $this->collocation_contract_model->get_by_id($c_id);
              $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'agency_id' => $this->user_arr['agency_id'],
                'broker_id' => $this->user_arr['broker_id'],
                'broker_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '审核合同编号为' . $info['collocation_id'] . '的托管合同的付款业主。',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
              );
              $this->operate_log_model->add_operate_log($add_log_param);

            }
          } else {
            $this->redirect_permission_none();
            die();
          }
        } else {//管家审核
          $steward_audit = $this->broker_permission_model->check('88');
          if (isset($steward_audit['auth']) && $steward_audit['auth']) {
            $updater_arr['company_id'] = $this->user_arr['company_id'];
            $updater_arr = array("status" => 3);
            $rs = $this->collocation_contract_model->update_need_pay_by_id($updater_arr, $id, $tab);
            if ($rs) {
              //管家——审核
              $add_data = array(
                'c_id' => $c_id,
                'type_name' => "财务审核",
                'content' => "对管家费用进行审核不通过操作。",
                'broker_id' => $this->user_arr['broker_id'],
                'broker_name' => $this->user_arr['truename'],
                'updatetime' => time()
              );
              $this->collocation_contract_log_model->add_info($add_data);

              //操作日志
              $info = $this->collocation_contract_model->get_by_id($c_id);
              $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'agency_id' => $this->user_arr['agency_id'],
                'broker_id' => $this->user_arr['broker_id'],
                'broker_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '审核合同编号为' . $info['collocation_id'] . '的托管合同的管家费用。',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
              );
              $this->operate_log_model->add_operate_log($add_log_param);

            }
          } else {
            $this->redirect_permission_none();
            die();
          }

        }
        if ($rs) {
          echo json_encode($data['result'] = 'ok2');
          exit;
        } else {
          echo json_encode($data['result'] = 'no');
          exit;
        }
      } else {//反审核
        if ($tab == 1 || $tab == 2) {
          $payment_turn_audit = $this->broker_permission_model->check('83');
          if (isset($payment_turn_audit['auth']) && $payment_turn_audit['auth']) {
            $updater_arr['company_id'] = $this->user_arr['company_id'];
            $updater_arr = array("status" => 1);
            $rs = $this->collocation_contract_model->update_need_pay_by_id($updater_arr, $id, $tab);
            if ($rs) {
              //付款业主反审核
              $add_data = array(
                'c_id' => $c_id,
                'type_name' => "财务审核",
                'content' => "对付款业主进行反审核操作。",
                'broker_id' => $this->user_arr['broker_id'],
                'broker_name' => $this->user_arr['truename'],
                'updatetime' => time()
              );
              $this->collocation_contract_log_model->add_info($add_data);

              //操作日志
              $info = $this->collocation_contract_model->get_by_id($c_id);
              $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'agency_id' => $this->user_arr['agency_id'],
                'broker_id' => $this->user_arr['broker_id'],
                'broker_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '反审核合同编号为' . $info['collocation_id'] . '的托管合同的付款业主。',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
              );
              $this->operate_log_model->add_operate_log($add_log_param);
            }
          } else {
            $this->redirect_permission_none();
            die();
          }

        } else {//管家审核
          $steward_turn_audit = $this->broker_permission_model->check('89');
          if (isset($steward_turn_audit['auth']) && $steward_turn_audit['auth']) {
            $updater_arr['company_id'] = $this->user_arr['company_id'];
            $updater_arr = array("status" => 1);
            $rs = $this->collocation_contract_model->update_need_pay_by_id($updater_arr, $id, $tab);
            if ($rs) {
              //管家费用反审核
              $add_data = array(
                'c_id' => $c_id,
                'type_name' => "财务审核",
                'content' => "对管家费用进行反审核操作。",
                'broker_id' => $this->user_arr['broker_id'],
                'broker_name' => $this->user_arr['truename'],
                'updatetime' => time()
              );
              $this->collocation_contract_log_model->add_info($add_data);


              //操作日志
              $info = $this->collocation_contract_model->get_by_id($c_id);
              $add_log_param = array(
                'company_id' => $this->user_arr['company_id'],
                'agency_id' => $this->user_arr['agency_id'],
                'broker_id' => $this->user_arr['broker_id'],
                'broker_name' => $this->user_arr['truename'],
                'type' => 35,
                'text' => '反审核合同编号为' . $info['collocation_id'] . '的托管合同的管家费用。',
                'from_system' => 1,
                'from_ip' => get_ip(),
                'mac_ip' => '127.0.0.1',
                'from_host_name' => '127.0.0.1',
                'hardware_num' => '测试硬件序列号',
                'time' => time()
              );
              $this->operate_log_model->add_operate_log($add_log_param);
            }
          } else {
            $this->redirect_permission_none();
            die();
          }
        }

        if ($rs) {
          echo json_encode($data['result'] = 'ok3');
          exit;
        } else {
          echo json_encode($data['result'] = 'no');
          exit;
        }
      }
    }
  }

  //应收，实收审核
  public function receive_audit()
  {
    $id = $this->input->get('id', TRUE);
    $r_id = $this->input->get('r_id', TRUE);
    $type = $this->input->get('type', TRUE);
    $tag = $this->input->get('tag', TRUE);
    if ($type == 1) {//审核通过
      $receipt_audit = $this->broker_permission_model->check('99');
      if (isset($receipt_audit['auth']) && $receipt_audit['auth']) {
        $updater_arr = array("status" => 2);
        $updater_arr['company_id'] = $this->user_arr['company_id'];
        $rs = $this->collocation_rent_contract_model->update_need_receive_by_id($updater_arr, $id, $tag);

      } else {
        $this->redirect_permission_none();
        die();
      }

      if ($rs) {
        //收款--审核
        $add_data = array(
          'c_id' => $r_id,
          'type_name' => "财务审核",
          'content' => "对收款客户进行审核操作。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time(),
          'type' => '2'
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($r_id);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '审核合同编号为' . $info['collocation_id'] . '的托管合同的收款客户。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
        echo json_encode($data['result'] = 'ok1');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    } elseif ($type == 2) {//审核不通过
      $receipt_audit = $this->broker_permission_model->check('99');
      if (isset($receipt_audit['auth']) && $receipt_audit['auth']) {
        $updater_arr = array("status" => 3);
        $updater_arr['company_id'] = $this->user_arr['company_id'];
        $rs = $this->collocation_rent_contract_model->update_need_receive_by_id($updater_arr, $id, $tag);
      } else {
        $this->redirect_permission_none();
        die();
      }

      if ($rs) {
        //收款--审核
        $add_data = array(
          'c_id' => $r_id,
          'type_name' => "财务审核",
          'content' => "对收款客户进行审核不通过操作。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time(),
          'type' => '2'
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($r_id);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '审核合同编号为' . $info['collocation_id'] . '的托管合同的收款客户。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);

        echo json_encode($data['result'] = 'ok2');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    } else {//反审核
      $receipt_turn_audit = $this->broker_permission_model->check('100');
      if (isset($receipt_turn_audit['auth']) && $receipt_turn_audit['auth']) {
        $updater_arr = array("status" => 1);
        $updater_arr['company_id'] = $this->user_arr['company_id'];
        $rs = $this->collocation_rent_contract_model->update_need_receive_by_id($updater_arr, $id, $tag);
      } else {
        $this->redirect_permission_none();
        die();
      }

      if ($rs) {
        //收款--反审核
        $add_data = array(
          'c_id' => $r_id,
          'type_name' => "财务审核",
          'content' => "对收款客户进行反审核操作。",
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'updatetime' => time(),
          'type' => '2'
        );
        $this->collocation_contract_log_model->add_info($add_data);

        //操作日志
        $info = $this->collocation_contract_model->get_by_id($r_id);
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '反审核合同编号为' . $info['collocation_id'] . '的托管合同的收款客户。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
        echo json_encode($data['result'] = 'ok3');
        exit;
      } else {
        echo json_encode($data['result'] = 'no');
        exit;
      }
    }
  }

  //修改合同内容匹配
  public function modify_match($data1, $data2, $type)
  {
    $data = array_diff_assoc($data1, $data2);
    $str = '';
    $base_config = $this->house_config_model->get_config();
    foreach ($data as $key => $val) {
      if ($type == 1) {//托管合同的修改
        switch ($key) {
          case 'collocation_id':
            $str .= "“托管合同编号”由“{$data2['collocation_id']}”改为“{$data1['collocation_id']}”；";
            break;
          case 'house_id':
            $str .= "“房源编号”由“{$data2['house_id']}”改为“{$data1['house_id']}”；";
            break;
          case 'block_name':
            $str .= "“楼盘”由“" . $data2['block_name'] . "”改为“" . $data1['block_name'] . "”；";
            break;
          /*case 'block_id':
						$str .= "“楼盘ID”由“{$data2['block_id']}”改为“{$data1['block_id']}”；";
						break;*/
          case 'houses_area':
            $str .= "“面积”由“" . $data2['houses_area'] . "m²”改为“" . $data1['houses_area'] . "m²”；";
            break;
          case 'houses_address':
            $str .= "“房源地址”由“" . $data2['houses_address'] . "”改为“" . $data1['houses_address'] . "”；";
            break;
          case 'type':
            $str .= "“物业类型”由“" . $base_config['sell_type'][$data2['type']] . "”改为“" . $base_config['sell_type'][$data1['type']] . "”；";
            break;
          case 'collo_start_time':
            $str .= "“托管开始时间”由“{$data2['collo_start_time']}”改为“{$data1['collo_start_time']}”；";
            break;
          case 'collo_end_time':
            $str .= "“签约日期”由“{$data2['collo_end_time']}”改为“{$data1['collo_end_time']}”；";
            break;
          case 'total_month':
            $str .= "“托管总月数”由“{$data2['total_month']}”改为“{$data1['total_month']}”；";
          case 'owner':
            $str .= "“业主姓名”由“{$data2['owner']}”改为“{$data1['owner']}”；";
            break;
          case 'owner_tel':
            $str .= "“业主联系方式”由“{$data2['owner_tel']}”改为“{$data1['owner_tel']}”；";
            break;
          case 'owner_idcard':
            $str .= "“业主身份证号”由“{$data2['owner_idcard']}”改为“{$data1['owner_idcard']}”；";
            break;
          case 'pay_ditch':
            $str .= "“付款渠道”由“{$data2['pay_ditch']}”改为“{$data1['pay_ditch']}”；";
            break;
          case 'agency_id':
            $agency_info1 = $this->api_broker_model->get_by_agency_id($data1['agency_id']);
            $agency_info2 = $this->api_broker_model->get_by_agency_id($data2['agency_id']);
            $str .= "“签约门店”由“" . $agency_info2['name'] . "”改为“" . $agency_info1['name'] . "”；";
            break;
          case 'broker_id':
            $info1 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['broker_id']);
            $info2 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['broker_id']);
            $str .= "“签约人”由“" . $info2['truename'] . "”改为“" . $info1['truename'] . "”；";
            break;
          case 'broker_tel':
            $str .= "“签约人电话”由“{$data2['broker_tel']}”改为“{$data1['broker_tel']}”；";
            break;
          case 'rental':
            $str .= "“每月租金”由“{$data2['rental']}”改为“{$data1['rental']}”；";
            break;
          case 'pay_type'://付款方式
            $str .= "付款方式”由“" . $base_config['rentpaytype'][$data2['pay_type']] . "”改为“" . $base_config['rentpaytype'][$data1['pay_type']] . "”；";
            break;
          case 'rental_total':
            $str .= "“租金总额”由“{$data2['rental_total']}”改为“{$data1['rental_total']}”；";
            break;
          case 'desposit':
            $str .= "“押金金额”由“{$data2['desposit']}”改为“{$data1['desposit']}”；";
            break;
          case 'penal_sum':
            $str .= "“违约金额”由“{$data2['penal_sum']}”改为“{$data1['penal_sum']}”；";
            break;
          case 'tax_type'://税费承担
            $str .= "税费承担”由“" . $base_config['tax_type'][$data2['tax_type']] . "”改为“" . $base_config['tax_type'][$data1['tax_type']] . "”；";
            break;
          case 'property_fee':
            $str .= "“每月物业费用”由“{$data2['property_fee']}”改为“{$data1['property_fee']}”；";
            break;
          case 'agency_commission':
            $str .= "“中介佣金”由“{$data2['agency_commission']}”改为“{$data1['agency_commission']}”；";
            break;
          case 'property_manage_assume'://物管承担
            $str .= "物管承担”由“" . $base_config['tene_type'][$data2['property_manage_assume']] . "”改为“" . $base_config['tene_type'][$data1['property_manage_assume']] . "”；";
            break;
          case 'rent_free_time':
            $str .= "“免租天数”由“{$data2['rent_free_time']}”改为“{$data1['rent_free_time']}”；";
            break;
          case 'desposit_type'://托管状态
            $str .= "托管状态”由“" . $base_config['desposit_type'][$data2['desposit_type']] . "”改为“" . $base_config['desposit_type'][$data1['desposit_type']] . "”；";
            break;
          case 'divide_a_agency_id':
            $agency_info3 = $this->api_broker_model->get_by_agency_id($data1['divide_a_agency_id']);
            $agency_info4 = $this->api_broker_model->get_by_agency_id($data2['divide_a_agency_id']);
            $str .= "“业绩分成门店a”由“" . $agency_info4['name'] . "”改为“" . $agency_info3['name'] . "”；";
            break;
          case 'divide_a_broker_id':
            $info3 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['divide_a_broker_id']);
            $info4 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['divide_a_broker_id']);
            $str .= "“业绩分成经纪人a”由“" . $info4['truename'] . "”改为“" . $info3['truename'] . "”；";
            break;
          case 'divide_a_money':
            $str .= "“业绩分成费用”由“{$data2['divide_a_money']}”改为“{$data1['divide_a_money']}”；";
            break;
          case 'divide_b_agency_id':
            $agency_info5 = $this->api_broker_model->get_by_agency_id($data1['divide_b_agency_id']);
            $agency_info6 = $this->api_broker_model->get_by_agency_id($data2['divide_b_agency_id']);
            $str .= "“业绩分成门店b”由“" . $agency_info6['name'] . "”改为“" . $agency_info5['name'] . "”；";
            break;
          case 'divide_b_broker_id':
            $info5 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['divide_b_broker_id']);
            $info6 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['divide_b_broker_id']);
            $str .= "“业绩分成经纪人b”由“" . $info6['truename'] . "”改为“" . $info5['truename'] . "”；";
            break;
          case 'divide_b_money':
            $str .= "“业绩分成费用”由“{$data2['divide_b_money']}”改为“{$data1['divide_b_money']}”；";
            break;
          case 'out_agency_id':
            $agency_info7 = $this->api_broker_model->get_by_agency_id($data1['out_agency_id']);
            $agency_info8 = $this->api_broker_model->get_by_agency_id($data2['out_agency_id']);
            $str .= "“退房经纪门店”由“" . $agency_info8['name'] . "”改为“" . $agency_info7['name'] . "”；";
            break;
          case 'out_broker_id':
            $info7 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['out_broker_id']);
            $info8 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['out_broker_id']);
            $str .= "“退房经纪人”由“" . $info8['truename'] . "”改为“" . $info7['truename'] . "”；";
            break;
          case 'stop_agreement_num':
            $str .= "“终止协议号”由“{$data2['stop_agreement_num']}”改为“{$data1['stop_agreement_num']}”；";
            break;
          case 'list_items':
            $str .= "“物品清单”由“{$data2['list_items']}”改为“{$data1['list_items']}”；";
            break;
          case 'remarks':
            $str .= "“备注”由“{$data2['remarks']}”改为“{$data1['remarks']}”；";
            break;
          case 'status'://状态
            $str .= "审核状态”由“" . $base_config['cont_status'][$data2['status']] . "”改为“" . $base_config['cont_status'][$data1['status']] . "”；";
            break;
          case 'signing_time':
            $str .= "“签约日期”由“" . date('Y-m-d', $data2['signing_time']) . "”改为“" . date('Y-m-d', $data2['signing_time']) . "”；";
            break;
        }
      } elseif ($type == 2) {//应付业主
        switch ($key) {
          case 'c_id':
            $str .= "“托管合同ID”由“{$data2['c_id']}”改为“{$data1['c_id']}”；";
            break;
          case 'rental':
            $str .= "“租金”由“{$data2['rental']}”改为“{$data1['rental']}”；";
            break;
          case 'water_fee':
            $str .= "“水费”由“{$data2['water_fee']}”改为“{$data1['water_fee']}”；";
            break;
          case 'ele_fee':
            $str .= "“电费”由“{$data2['ele_fee']}”改为“{$data1['ele_fee']}”；";
            break;
          case 'gas_fee':
            $str .= "“燃气费”由“{$data2['gas_fee']}”改为“{$data1['gas_fee']}”；";
            break;
          case 'int_fee':
            $str .= "“网费”由“{$data2['int_fee']}”改为“{$data1['int_fee']}”；";
          case 'owner':
            $str .= "“业主姓名”由“{$data2['owner']}”改为“{$data1['owner']}”；";
            break;
          case 'tv_fee':
            $str .= "“电视费”由“{$data2['tv_fee']}”改为“{$data1['tv_fee']}”；";
            break;
          case 'property_fee':
            $str .= "“物业费”由“{$data2['property_fee']}”改为“{$data1['property_fee']}”；";
            break;
          case 'preserve_fee':
            $str .= "“维护费”由“{$data2['preserve_fee']}”改为“{$data1['preserve_fee']}”；";
            break;
          case 'garbage_fee':
            $str .= "“垃圾费”由“{$data2['garbage_fee']}”改为“{$data1['garbage_fee']}”；";
            break;
          case 'other_fee':
            $str .= "“杂费”由“{$data2['other_fee']}”改为“{$data1['other_fee']}”；";
            break;
          case 'need_pay_time':
            $str .= "“应付日期”由“" . date('Y-m-d', $data2['need_pay_time']) . "”改为“" . date('Y-m-d', $data2['need_pay_time']) . "”；";
            break;
          case 'remark':
            $str .= "“备注”由“{$data2['remark']}”改为“{$data1['remark']}”；";
            break;
          case 'status'://状态
            $str .= "审核状态”由“" . $base_config['audit_status'][$data2['status']] . "”改为“" . $base_config['audit_status'][$data1['status']] . "”；";
            break;
          /*case 'pay_type'://付款方式
						$str .= "付款方式”由“" . $base_config['rentpaytype'][$data2['pay_type']] . "”改为“" .$base_config['rentpaytype'][$data2['pay_type']]. "”；";
						break;
					case 'stop_time':
						$str .= "“停付日期”由“" . date('Y-m-d',$data2['stop_time']) . "”改为“" .date('Y-m-d',$data2['stop_time']). "”；";
						break;*/
          case 'total_fee':
            $str .= "“合计费用”由“{$data2['total_fee']}”改为“{$data1['total_fee']}”；";
            break;
        }
      } elseif ($type == 3) {//实付业主
        switch ($key) {
          case 'c_id':
            $str .= "“托管合同ID”由“{$data2['c_id']}”改为“{$data1['c_id']}”；";
            break;
          case 'rental':
            $str .= "“租金”由“{$data2['rental']}”改为“{$data1['rental']}”；";
            break;
          case 'water_fee':
            $str .= "“水费”由“{$data2['water_fee']}”改为“{$data1['water_fee']}”；";
            break;
          case 'ele_fee':
            $str .= "“电费”由“{$data2['ele_fee']}”改为“{$data1['ele_fee']}”；";
            break;
          case 'gas_fee':
            $str .= "“燃气费”由“{$data2['gas_fee']}”改为“{$data1['gas_fee']}”；";
            break;
          case 'int_fee':
            $str .= "“网费”由“{$data2['int_fee']}”改为“{$data1['int_fee']}”；";
            break;
          case 'tv_fee':
            $str .= "“电视费”由“{$data2['tv_fee']}”改为“{$data1['tv_fee']}”；";
            break;
          case 'property_fee':
            $str .= "“物业费”由“{$data2['property_fee']}”改为“{$data1['property_fee']}”；";
            break;
          case 'preserve_fee':
            $str .= "“维护费”由“{$data2['preserve_fee']}”改为“{$data1['preserve_fee']}”；";
            break;
          case 'garbage_fee':
            $str .= "“垃圾费”由“{$data2['garbage_fee']}”改为“{$data1['garbage_fee']}”；";
            break;
          case 'other_fee':
            $str .= "“杂费”由“{$data2['other_fee']}”改为“{$data1['other_fee']}”；";
            break;
          case 'agency_id':
            $agency_info1 = $this->api_broker_model->get_by_agency_id($data1['agency_id']);
            $agency_info2 = $this->api_broker_model->get_by_agency_id($data2['agency_id']);
            $str .= "“付款门店”由“" . $agency_info2['name'] . "”改为“" . $agency_info1['name'] . "”；";
            break;
          case 'broker_id':
            $info1 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['broker_id']);
            $info2 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['broker_id']);
            $str .= "“付款经纪人”由“" . $info2['truename'] . "”改为“" . $info1['truename'] . "”；";
            break;
          case 'actual_pay_time':
            $str .= "“实付日期”由“" . date('Y-m-d', $data2['actual_pay_time']) . "”改为“" . date('Y-m-d', $data1['actual_pay_time']) . "”；";
            break;
          case 'actual_pay_type'://实付方式
            $str .= "实付方式”由“" . $base_config['actual_pay_type'][$data2['actual_pay_type']] . "”改为“" . $base_config['actual_pay_type'][$data1['actual_pay_type']] . "”；";
            break;
          case 'receipts_num':
            $str .= "“单据号”由“{$data2['receipts_num']}”改为“{$data1['receipts_num']}”；";
            break;
          case 'remark':
            $str .= "“备注”由“{$data2['remark']}”改为“{$data1['remark']}”；";
            break;
          case 'status'://状态
            $str .= "审核状态”由“" . $base_config['audit_status'][$data2['status']] . "”改为“" . $base_config['audit_status'][$data1['status']] . "”；";
            break;
          /*case 'pay_type'://付款方式
						$str .= "付款方式”由“" . $base_config['rentpaytype'][$data2['pay_type']] . "”改为“" .$base_config['rentpaytype'][$data2['pay_type']]. "”；";
						break;
					case 'stop_time':
						$str .= "“停付日期”由“" . date('Y-m-d',$data2['stop_time']) . "”改为“" .date('Y-m-d',$data2['stop_time']). "”；";
						break;*/
          case 'total_fee':
            $str .= "“合计费用”由“{$data2['total_fee']}”改为“{$data1['total_fee']}”；";
            break;
        }
      } elseif ($type == 4) {//管家费用
        switch ($key) {
          case 'c_id':
            $str .= "“托管合同ID”由“{$data2['c_id']}”改为“{$data1['c_id']}”；";
            break;
          case 'reimbursement_time':
            $str .= "“报销日”由“" . date('Y-m-d', $data2['reimbursement_time']) . "”改为“" . date('Y-m-d', $data1['reimbursement_time']) . "”；";
            break;
          case 'project_name':
            $str .= "“项目名称”由“{$data2['project_name']}”改为“{$data1['project_name']}”；";
            break;
          case 'total_fee':
            $str .= "“合计费用”由“{$data2['total_fee']}”改为“{$data1['total_fee']}”；";
            break;
          case 'owner_bear':
            $str .= "“业主承担”由“{$data2['owner_bear']}”改为“{$data1['owner_bear']}”；";
            break;
          case 'customer_bear':
            $str .= "“客户承担”由“{$data2['customer_bear']}”改为“{$data1['customer_bear']}”；";
            break;
          case 'company_bear':
            $str .= "“公司承担”由“{$data2['company_bear']}”改为“{$data1['company_bear']}”；";
            break;
          case 'withhold_time':
            $str .= "“扣款日”由“" . date('Y-m-d', $data2['withhold_time']) . "”改为“" . date('Y-m-d', $data1['withhold_time']) . "”；";
            break;
          case 'agency_id':
            $agency_info1 = $this->api_broker_model->get_by_agency_id($data1['agency_id']);
            $agency_info2 = $this->api_broker_model->get_by_agency_id($data2['agency_id']);
            $str .= "“报销部门”由“" . $agency_info2['name'] . "”改为“" . $agency_info1['name'] . "”；";
            break;
          case 'remark':
            $str .= "“备注”由“{$data2['remark']}”改为“{$data1['remark']}”；";
            break;
          case 'status'://状态
            $str .= "审核状态”由“" . $base_config['audit_status'][$data2['status']] . "”改为“" . $base_config['audit_status'][$data1['status']] . "”；";
            break;
        }
      } elseif ($type == 5) {//出租合同
        switch ($key) {
          case 'collo_rent_id':
            $str .= "“出租合同编号”由“{$data2['collo_rent_id']}”改为“{$data1['collo_rent_id']}”；";
            break;
          case 'rent_start_time':
            $str .= "“出租开始时间”由“" . date('Y-m-d', $data2['rent_start_time']) . "”改为“" . date('Y-m-d', $data1['rent_start_time']) . "”；";
            break;
          case 'rent_end_time':
            $str .= "“出租结束时间”由“" . date('Y-m-d', $data2['rent_end_time']) . "”改为“" . date('Y-m-d', $data1['rent_end_time']) . "”；";
            break;
          case 'rent_total_month':
            $str .= "“出租总月数”由“{$data2['rent_total_month']}”改为“{$data1['rent_total_month']}”；";
            break;
          case 'signing_time':
            $str .= "“签约日期”由“" . date('Y-m-d', $data2['signing_time']) . "”改为“" . date('Y-m-d', $data1['signing_time']) . "”；";
            break;
          case 'customer_name':
            $str .= "“客户姓名”由“{$data2['customer_name']}”改为“{$data1['customer_name']}”；";
            break;
          case 'customer_tel':
            $str .= "“客户联系方式”由“{$data2['customer_tel']}”改为“{$data1['customer_tel']}”；";
            break;
          case 'customer_idcard':
            $str .= "“客户身份证号”由“{$data2['customer_idcard']}”改为“{$data1['customer_idcard']}”；";
            break;
          case 'pay_ditch':
            $str .= "“付款渠道”由“{$data2['pay_ditch']}”改为“{$data1['pay_ditch']}”；";
            break;
          case 'agency_id':
            $agency_info1 = $this->api_broker_model->get_by_agency_id($data1['agency_id']);
            $agency_info2 = $this->api_broker_model->get_by_agency_id($data2['agency_id']);
            $str .= "“签约门店”由“" . $agency_info2['name'] . "”改为“" . $agency_info1['name'] . "”；";
            break;
          case 'broker_id':
            $info1 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['broker_id']);
            $info2 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['broker_id']);
            $str .= "“签约人”由“" . $info2['truename'] . "”改为“" . $info1['truename'] . "”；";
            break;
          case 'broker_tel':
            $str .= "“签约人电话”由“{$data2['broker_tel']}”改为“{$data1['broker_tel']}”；";
            break;
          case 'rental':
            $str .= "“每月租金”由“{$data2['rental']}”改为“{$data1['rental']}”；";
            break;
          case 'pay_type'://付款方式
            $str .= "付款方式”由“" . $base_config['rentpaytype'][$data2['pay_type']] . "”改为“" . $base_config['rentpaytype'][$data1['pay_type']] . "”；";
            break;
          case 'rental_total':
            $str .= "“租金总额”由“{$data2['rental_total']}”改为“{$data1['rental_total']}”；";
            break;
          case 'desposit':
            $str .= "“押金金额”由“{$data2['desposit']}”改为“{$data1['desposit']}”；";
            break;
          case 'penal_sum':
            $str .= "“违约金额”由“{$data2['penal_sum']}”改为“{$data1['penal_sum']}”；";
            break;
          case 'tax_type'://税费承担
            $str .= "税费承担”由“" . $base_config['tax_type'][$data2['tax_type']] . "”改为“" . $base_config['tax_type'][$data1['tax_type']] . "”；";
            break;
          case 'property_fee':
            $str .= "“每月物业费用”由“{$data2['property_fee']}”改为“{$data1['property_fee']}”；";
            break;
          case 'agency_commission':
            $str .= "“中介佣金”由“{$data2['agency_commission']}”改为“{$data1['agency_commission']}”；";
            break;
          case 'rent_free_time':
            $str .= "“免租天数”由“{$data2['rent_free_time']}”改为“{$data1['rent_free_time']}”；";
            break;
          case 'property_manage_assume'://物管承担
            $str .= "物管承担”由“" . $base_config['tene_type'][$data2['property_manage_assume']] . "”改为“" . $base_config['tene_type'][$data1['property_manage_assume']] . "”；";
            break;

          case 'rent_type'://出租状态
            $str .= "出租状态”由“" . $base_config['desposit_type'][$data2['rent_type']] . "”改为“" . $base_config['desposit_type'][$data1['rent_type']] . "”；";
            break;
          case 'houses_preserve_agency_id':
            $agency_info3 = $this->api_broker_model->get_by_agency_id($data1['houses_preserve_agency_id']);
            $agency_info4 = $this->api_broker_model->get_by_agency_id($data2['houses_preserve_agency_id']);
            $str .= "“房源维护门店”由“" . $agency_info4['name'] . "”改为“" . $agency_info3['name'] . "”；";
            break;
          case 'houses_preserve_broker_id':
            $info3 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['houses_preserve_broker_id']);
            $info4 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['houses_preserve_broker_id']);
            $str .= "“房源维护经纪人”由“" . $info4['truename'] . "”改为“" . $info3['truename'] . "”；";
            break;
          case 'houses_preserve_money':
            $str .= "“房源维护金额”由“{$data2['houses_preserve_money']}”改为“{$data1['houses_preserve_money']}”；";
            break;
          case 'customer_preserve_agency_id':
            $agency_info5 = $this->api_broker_model->get_by_agency_id($data1['customer_preserve_agency_id']);
            $agency_info6 = $this->api_broker_model->get_by_agency_id($data2['customer_preserve_agency_id']);
            $str .= "“客源维护门店”由“" . $agency_info6['name'] . "”改为“" . $agency_info5['name'] . "”；";
            break;
          case 'customer_preserve_broker_id':
            $info5 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['customer_preserve_broker_id']);
            $info6 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['customer_preserve_broker_id']);
            $str .= "“客源维护经纪人”由“" . $info6['truename'] . "”改为“" . $info5['truename'] . "”；";
            break;
          case 'customer_preserve_money':
            $str .= "“客源维护金额”由“{$data2['customer_preserve_money']}”改为“{$data1['customer_preserve_money']}”；";
            break;
          case 'out_broker_agency_id':
            $agency_info7 = $this->api_broker_model->get_by_agency_id($data1['out_broker_agency_id']);
            $agency_info8 = $this->api_broker_model->get_by_agency_id($data2['out_broker_agency_id']);
            $str .= "“退房经纪门店”由“" . $agency_info8['name'] . "”改为“" . $agency_info7['name'] . "”；";
            break;
          case 'out_broker_broker_id':
            $info7 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['out_broker_broker_id']);
            $info8 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['out_broker_broker_id']);
            $str .= "“退房经纪人”由“" . $info8['truename'] . "”改为“" . $info7['truename'] . "”；";
            break;
          case 'stop_agreement_num':
            $str .= "“终止协议号”由“{$data2['stop_agreement_num']}”改为“{$data1['stop_agreement_num']}”；";
            break;
          case 'expire_time':
            $str .= "“到期日期”由“" . date('Y-m-d', $data2['expire_time']) . "”改为“" . date('Y-m-d', $data1['expire_time']) . "”；";
            break;
          case 'remark':
            $str .= "“备注”由“{$data2['remark']}”改为“{$data1['remark']}”；";
            break;
          case 'status'://状态
            $str .= "审核状态”由“" . $base_config['cont_status'][$data2['status']] . "”改为“" . $base_config['cont_status'][$data1['status']] . "”；";
            break;
        }
      } elseif ($type == 6) {//应收客户
        switch ($key) {
          case 'r_id':
            $str .= "“出租合同ID”由“{$data2['r_id']}”改为“{$data1['r_id']}”；";
            break;
          case 'rental':
            $str .= "“租金”由“{$data2['rental']}”改为“{$data1['rental']}”；";
            break;
          case 'water_fee':
            $str .= "“水费”由“{$data2['water_fee']}”改为“{$data1['water_fee']}”；";
            break;
          case 'ele_fee':
            $str .= "“电费”由“{$data2['ele_fee']}”改为“{$data1['ele_fee']}”；";
            break;
          case 'gas_fee':
            $str .= "“燃气费”由“{$data2['gas_fee']}”改为“{$data1['gas_fee']}”；";
            break;
          case 'int_fee':
            $str .= "“网费”由“{$data2['int_fee']}”改为“{$data1['int_fee']}”；";
          case 'owner':
            $str .= "“业主姓名”由“{$data2['owner']}”改为“{$data1['owner']}”；";
            break;
          case 'tv_fee':
            $str .= "“电视费”由“{$data2['tv_fee']}”改为“{$data1['tv_fee']}”；";
            break;
          case 'property_fee':
            $str .= "“物业费”由“{$data2['property_fee']}”改为“{$data1['property_fee']}”；";
            break;
          case 'preserve_fee':
            $str .= "“维护费”由“{$data2['preserve_fee']}”改为“{$data1['preserve_fee']}”；";
            break;
          case 'garbage_fee':
            $str .= "“垃圾费”由“{$data2['garbage_fee']}”改为“{$data1['garbage_fee']}”；";
            break;
          case 'other_fee':
            $str .= "“杂费”由“{$data2['other_fee']}”改为“{$data1['other_fee']}”；";
            break;
          case 'need_receive_time':
            $str .= "“应收日期”由“" . date('Y-m-d', $data2['need_receive_time']) . "”改为“" . date('Y-m-d', $data1['need_receive_time']) . "”；";
            break;
          case 'remark':
            $str .= "“备注”由“{$data2['remark']}”改为“{$data1['remark']}”；";
            break;
          case 'status'://状态
            $str .= "审核状态”由“" . $base_config['audit_status'][$data2['status']] . "”改为“" . $base_config['audit_status'][$data1['status']] . "”；";
            break;
          /*case 'pay_type'://付款方式
						$str .= "付款方式”由“" . $base_config['rentpaytype'][$data2['pay_type']] . "”改为“" .$base_config['rentpaytype'][$data2['pay_type']]. "”；";
						break;
					case 'stop_time':
						$str .= "“停付日期”由“" . date('Y-m-d',$data2['stop_time']) . "”改为“" .date('Y-m-d',$data2['stop_time']). "”；";
						break;*/
          case 'total_fee':
            $str .= "“合计费用”由“{$data2['total_fee']}”改为“{$data1['total_fee']}”；";
            break;
        }
      } elseif ($type == 7) {//实收客户
        switch ($key) {
          case 'r_id':
            $str .= "“出租合同ID”由“{$data2['r_id']}”改为“{$data1['r_id']}”；";
            break;
          case 'rental':
            $str .= "“租金”由“{$data2['rental']}”改为“{$data1['rental']}”；";
            break;
          case 'water_fee':
            $str .= "“水费”由“{$data2['water_fee']}”改为“{$data1['water_fee']}”；";
            break;
          case 'ele_fee':
            $str .= "“电费”由“{$data2['ele_fee']}”改为“{$data1['ele_fee']}”；";
            break;
          case 'gas_fee':
            $str .= "“燃气费”由“{$data2['gas_fee']}”改为“{$data1['gas_fee']}”；";
            break;
          case 'int_fee':
            $str .= "“网费”由“{$data2['int_fee']}”改为“{$data1['int_fee']}”；";
            break;
          case 'tv_fee':
            $str .= "“电视费”由“{$data2['tv_fee']}”改为“{$data1['tv_fee']}”；";
            break;
          case 'property_fee':
            $str .= "“物业费”由“{$data2['property_fee']}”改为“{$data1['property_fee']}”；";
            break;
          case 'preserve_fee':
            $str .= "“维护费”由“{$data2['preserve_fee']}”改为“{$data1['preserve_fee']}”；";
            break;
          case 'garbage_fee':
            $str .= "“垃圾费”由“{$data2['garbage_fee']}”改为“{$data1['garbage_fee']}”；";
            break;
          case 'other_fee':
            $str .= "“杂费”由“{$data2['other_fee']}”改为“{$data1['other_fee']}”；";
            break;
          case 'agency_id':
            $agency_info1 = $this->api_broker_model->get_by_agency_id($data1['agency_id']);
            $agency_info2 = $this->api_broker_model->get_by_agency_id($data2['agency_id']);
            $str .= "“收款门店”由“" . $agency_info2['name'] . "”改为“" . $agency_info1['name'] . "”；";
            break;
          case 'broker_id':
            $info1 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['broker_id']);
            $info2 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['broker_id']);
            $str .= "“收款经纪人”由“" . $info2['truename'] . "”改为“" . $info1['truename'] . "”；";
            break;
          case 'actual_receive_time':
            $str .= "“实收日期”由“" . date('Y-m-d', $data2['actual_receive_time']) . "”改为“" . date('Y-m-d', $data1['actual_receive_time']) . "”；";
            break;
          case 'actual_pay_type'://实付方式
            $str .= "实付方式”由“" . $base_config['actual_pay_type'][$data2['actual_pay_type']] . "”改为“" . $base_config['actual_pay_type'][$data1['actual_pay_type']] . "”；";
            break;
          case 'receipts_num':
            $str .= "“单据号”由“{$data2['receipts_num']}”改为“{$data1['receipts_num']}”；";
            break;
          case 'remark':
            $str .= "“备注”由“{$data2['remark']}”改为“{$data1['remark']}”；";
            break;
          case 'status'://状态
            $str .= "审核状态”由“" . $base_config['audit_status'][$data2['status']] . "”改为“" . $base_config['audit_status'][$data1['status']] . "”；";
            break;
          /*case 'pay_type'://付款方式
						$str .= "付款方式”由“" . $base_config['rentpaytype'][$data2['pay_type']] . "”改为“" .$base_config['rentpaytype'][$data2['pay_type']]. "”；";
						break;
					case 'stop_time':
						$str .= "“停付日期”由“" . date('Y-m-d',$data2['stop_time']) . "”改为“" .date('Y-m-d',$data2['stop_time']). "”；";
						break;*/
          case 'total_fee':
            $str .= "“合计费用”由“{$data2['total_fee']}”改为“{$data1['total_fee']}”；";
            break;
        }
      }
    }
    return $str;
  }

  //管家，付款，出租----导出
  public function export($type = '', $tab = '')
  {
    //获取所有请求参数
    $post_param = $this->input->post(NULL, TRUE);
    $config = $this->collocation_contract_model->get_config();
    //所属公司
    $post_param['company_id'] = $this->user_arr['company_id'];
    if ($type == 1) {//付款业主导出
      $cond_where = $this->_get_cond_str_pay($post_param);
      //清除条件头尾多余的“AND”和空格
      $cond_where = trim($cond_where);
      $cond_where = trim($cond_where, "AND");
      $cond_where = trim($cond_where);
      //获取列表内容
      $list = $this->collocation_contract_model->get_list_by_tab($cond_where, -1, '', $tab, 'create_time');
      foreach ($list as $key => $val) {
        $collo_detail = $this->get_detail($val['c_id']);
        $list[$key]['collocation_id'] = $collo_detail['collocation_id'];
        $list[$key]['house_id'] = $collo_detail['house_id'];
        $list[$key]['signing_time'] = $collo_detail['signing_time'];
      }
    } elseif ($type == 2) {//管家费用导出
      $cond_where = $this->_get_cond_str_rent($post_param);
      //清除条件头尾多余的“AND”和空格
      $cond_where = trim($cond_where);
      $cond_where = trim($cond_where, "AND");
      $cond_where = trim($cond_where);
      //获取列表内容
      $list = $this->collocation_contract_model->get_list_by_tab($cond_where, -1, '', '3', 'create_time');
      foreach ($list as $key => $val) {
        $collo_detail = $this->get_detail($val['c_id']);
        $list[$key]['collocation_id'] = $collo_detail['collocation_id'];
      }
    } elseif ($type == 3) {//出租导出
      $cond_where = $this->_get_cond_str_rent($post_param);
      //清除条件头尾多余的“AND”和空格
      $cond_where = trim($cond_where);
      $cond_where = trim($cond_where, "AND");
      $cond_where = trim($cond_where);
      //获取列表内容
      $list = $this->collocation_contract_model->get_list_by_tab($cond_where, -1, '', '4');
      //echo '<pre>';print_r($list);die;
      foreach ($list as $key => $val) {
        $collo_detail = $this->get_detail($val['c_id']);
        $list[$key]['collocation_id'] = $collo_detail['collocation_id'];
      }
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
    if ($type == 1) {//付款业主
      //设置表格导航属性
      if ($tab == 1) {
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '应付时间');
      } else {
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '实付时间');
      }

      $objPHPExcel->getActiveSheet()->setCellValue('B1', '托管合同编号');
      $objPHPExcel->getActiveSheet()->setCellValue('C1', '房源编号');
      $objPHPExcel->getActiveSheet()->setCellValue('D1', "租金");
      $objPHPExcel->getActiveSheet()->setCellValue('E1', '水费');
      $objPHPExcel->getActiveSheet()->setCellValue('F1', '电费');
      $objPHPExcel->getActiveSheet()->setCellValue('G1', '燃气费');
      $objPHPExcel->getActiveSheet()->setCellValue('H1', '网费');
      $objPHPExcel->getActiveSheet()->setCellValue('I1', '电视费');
      $objPHPExcel->getActiveSheet()->setCellValue('J1', '物业费');
      $objPHPExcel->getActiveSheet()->setCellValue('K1', '维护费');
      $objPHPExcel->getActiveSheet()->setCellValue('L1', '垃圾费');
      $objPHPExcel->getActiveSheet()->setCellValue('M1', '杂费');
      $objPHPExcel->getActiveSheet()->setCellValue('N1', '合计');
      if ($tab == 1) {
        $objPHPExcel->getActiveSheet()->setCellValue('O1', '签约时间');
        $objPHPExcel->getActiveSheet()->setCellValue('P1', '状态');
      } else {
        $objPHPExcel->getActiveSheet()->setCellValue('O1', '收据号');
        $objPHPExcel->getActiveSheet()->setCellValue('P1', '签约时间');
        $objPHPExcel->getActiveSheet()->setCellValue('Q1', '状态');
      }

      //设置表格的值
      for ($i = 2; $i <= count($list) + 1; $i++) {
        if ($tab == 1) {
          $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, date('Y-m-d', $list[$i - 2]['need_pay_time']));
        } else {
          $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, date('Y-m-d', $list[$i - 2]['actual_pay_time']));
        }
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['collocation_id']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['house_id']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['rental']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['water_fee']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['ele_fee']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['gas_fee']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 2]['int_fee']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 2]['tv_fee']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $list[$i - 2]['property_fee']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $list[$i - 2]['preserve_fee']);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $list[$i - 2]['garbage_fee']);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $list[$i - 2]['other_fee']);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $list[$i - 2]['total_fee']);
        if ($tab == 1) {
          $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, date('Y-m-d', $list[$i - 2]['signing_time']));
          $objPHPExcel->getActiveSheet()->setCellValue('P' . $i, $config['status'][$list[$i - 2]['status']]);
        } else {
          $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $list[$i - 2]['receipts_num']);
          $objPHPExcel->getActiveSheet()->setCellValue('P' . $i, date('Y-m-d', $list[$i - 2]['signing_time']));
          $objPHPExcel->getActiveSheet()->setCellValue('Q' . $i, $config['status'][$list[$i - 2]['status']]);
        }
      }
    } elseif ($type == 2) {//管家费用
      //设置表格导航属性
      $objPHPExcel->getActiveSheet()->setCellValue('A1', '托管合同编号');
      $objPHPExcel->getActiveSheet()->setCellValue('B1', '报销日期');
      $objPHPExcel->getActiveSheet()->setCellValue('C1', '项目名称');
      $objPHPExcel->getActiveSheet()->setCellValue('D1', "费用总计");
      $objPHPExcel->getActiveSheet()->setCellValue('E1', '业主承担');
      $objPHPExcel->getActiveSheet()->setCellValue('F1', '客户承担');
      $objPHPExcel->getActiveSheet()->setCellValue('G1', '公司承担');
      $objPHPExcel->getActiveSheet()->setCellValue('H1', '报销部门');
      $objPHPExcel->getActiveSheet()->setCellValue('I1', '扣款日期');
      $objPHPExcel->getActiveSheet()->setCellValue('J1', '说明');
      $objPHPExcel->getActiveSheet()->setCellValue('K1', '状态');

      //设置表格的值
      for ($i = 2; $i <= count($list) + 1; $i++) {
        //报销部门
        $agency = $this->agency_model->get_by_id($list[$i - 2]['agency_id']);

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['collocation_id']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, date('Y-m-d', $list[$i - 2]['reimbursement_time']));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['project_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['total_fee']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['owner_bear']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['customer_bear']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['company_bear']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $agency['name']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, date('Y-m-d', $list[$i - 2]['withhold_time']));
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $list[$i - 2]['remark']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $config['status'][$list[$i - 2]['status']]);
      }
    } elseif ($type == 3) {//出租
      //设置表格导航属性
      $objPHPExcel->getActiveSheet()->setCellValue('A1', '托管编号');
      $objPHPExcel->getActiveSheet()->setCellValue('B1', '出租合同编号');
      $objPHPExcel->getActiveSheet()->setCellValue('C1', '租客姓名');
      $objPHPExcel->getActiveSheet()->setCellValue('D1', "租金（元/月）");
      $objPHPExcel->getActiveSheet()->setCellValue('E1', '付款方式');
      $objPHPExcel->getActiveSheet()->setCellValue('F1', '起租时间');
      $objPHPExcel->getActiveSheet()->setCellValue('G1', '停租时间');
      $objPHPExcel->getActiveSheet()->setCellValue('H1', '签约时间');
      $objPHPExcel->getActiveSheet()->setCellValue('I1', '签约门店');
      $objPHPExcel->getActiveSheet()->setCellValue('J1', '签约人');
      $objPHPExcel->getActiveSheet()->setCellValue('K1', '合同状态');
      //设置表格的值
      for ($i = 2; $i <= count($list) + 1; $i++) {
        $payee_agency = $this->agency_model->get_by_id($list[$i - 2]['agency_id']);
        //签约门店，签约人
        $payee_broker = $this->broker_info_model->get_by_broker_id($list[$i - 2]['broker_id']);

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['collocation_id']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['collo_rent_id']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['customer_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['rental']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $config['pay_type'][$list[$i - 2]['pay_type']]);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, date('Y-m-d', $list[$i - 2]['rent_start_time']));
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, date('Y-m-d', $list[$i - 2]['rent_end_time']));
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, date('Y-m-d', $list[$i - 2]['signing_time']));
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $payee_agency['name']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $payee_broker['name']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $config['rent_status'][$list[$i - 2]['status']]);
      }
    }

    $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('product_nums');
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

  //应收，实收---导出
  public function customer_export($tag = '')
  {
    //获取所有请求参数
    $post_param = $this->input->post(NULL, TRUE);
    $config = $this->collocation_contract_model->get_config();
    //所属公司
    $post_param['company_id'] = $this->user_arr['company_id'];
    $cond_where = $this->_get_cond_str_pay($post_param);
    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);

    $list = $this->collocation_rent_contract_model->get_list_by_tag($cond_where, -1, '', $tag, 'create_time');

    foreach ($list as $key => $val) {
      $collo_rent_detail = $this->get_rent_detail($val['r_id']);
      $list[$key]['collo_rent_id'] = $collo_rent_detail['collo_rent_id'];
      $collo_detail = $this->get_detail($collo_rent_detail['c_id']);
      $list[$key]['collocation_id'] = $collo_detail['collocation_id'];
      $list[$key]['house_id'] = $collo_detail['house_id'];
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

    if ($tag == 1) {
      $objPHPExcel->getActiveSheet()->setCellValue('A1', '应收时间');
    } else {
      $objPHPExcel->getActiveSheet()->setCellValue('A1', '实收时间');
    }

    $objPHPExcel->getActiveSheet()->setCellValue('B1', '托管合同编号');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '出租合同编号');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', "房源编号");
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '租金');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '水费');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '电费');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '燃气费');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '网费');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '电视费');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '物业费');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '维护费');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '垃圾费');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', '杂费');
    if ($tag == 1) {
      $objPHPExcel->getActiveSheet()->setCellValue('O1', '合计');
      $objPHPExcel->getActiveSheet()->setCellValue('P1', '状态');
    } else {
      $objPHPExcel->getActiveSheet()->setCellValue('O1', '合计');
      $objPHPExcel->getActiveSheet()->setCellValue('P1', '收据号');
      $objPHPExcel->getActiveSheet()->setCellValue('Q1', '状态');
    }

    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {
      if ($tag == 1) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, date('Y-m-d', $list[$i - 2]['need_receive_time']));
      } else {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, date('Y-m-d', $list[$i - 2]['actual_receive_time']));
      }
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['collocation_id']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['collo_rent_id']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['house_id']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['rental']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['water_fee']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['ele_fee']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 2]['gas_fee']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 2]['int_fee']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $list[$i - 2]['tv_fee']);
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $list[$i - 2]['property_fee']);
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $list[$i - 2]['preserve_fee']);
      $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $list[$i - 2]['garbage_fee']);
      $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $list[$i - 2]['other_fee']);
      if ($tab == 1) {
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $list[$i - 2]['total_fee']);
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $i, $config['status'][$list[$i - 2]['status']]);
      } else {
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $list[$i - 2]['total_fee']);
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $i, $list[$i - 2]['receipts_num']);
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . $i, $config['status'][$list[$i - 2]['status']]);
      }
    }

    $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('product_nums');
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
}

/* End of file contract.php */
/* Location: ./application/mls/controllers/contract.php */
