<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 城市 Class
 *
 * 诚意金
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      Lion
 */
class Contract_earnest_money extends MY_Controller
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
  private $_limit = 10;

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
    $this->load->model('operate_log_model');
    $this->load->model('contract_earnest_money_model');
    $this->load->model('agency_model');
    $this->load->model('broker_info_model');
    $this->load->model('broker_permission_model');
    //权限
    if (is_full_array($this->user_arr)) {
      $this->load->model('broker_permission_model');
      $this->broker_permission_model->set_broker_id($this->user_arr['broker_id'], $this->user_arr['company_id']);
      //$this->load->model('agency_permission_model');
      //$this->agency_permission_model->set_agency_id($this->user_arr['agency_id']);
    }
  }


  //诚意金列表
  public function manage($type = 'sell')
  {
    $data = array();
    //树型菜单
    $data['user_tree_menu'] = $this->user_tree_menu;
    //获取所有请求参数
    $post_param = $this->input->post(NULL, TRUE);
    $config = $this->contract_earnest_money_model->get_config();
    //页面搜索条件
    $post_config = array();
    //记录搜索过的条件
    $data['post_param'] = $post_param;
    //关键词搜索
    $post_config['keyword_type'] = array('请选择', '业主姓名', '客户姓名');
    //诚意金状态
    $post_config['status'] = array_merge(array('请选择'), $config['status']);
    //交易类型
    $post_config['type'] = $type;
    //请求地址
    $post_config['request_url'] = '/contract_earnest_money/manage/';
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);
    //表单提交参数组成的查询条件
    $post_param['trade_type'] = $type == 'sell' ? 1 : 2;
    $data['trade_type'] = $post_param['trade_type'];
    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['payee_company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['payee_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['payee_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['payee_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['payee_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['payee_broker_id'] = $this->user_arr['broker_id'];
    }
    /**
     * 数据范围
     * 1、店长以上权限看公司
     * 2、店长及店长秘书权限查看本门店
     * 3、店长秘书以下没有权限
     */
    //获取访问菜单
    $this->load->model('contract_model');
    $range_menu = $this->contract_model->get_range_menu_by_role_level(
      $this->user_arr, $post_param['payee_agency_id']);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];

    $cond_where = $this->_get_cond_str($post_param);
    //符合条件的总行数
    $this->_total_count = $this->contract_earnest_money_model->count_by($cond_where);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    //获取列表内容
    $list = $this->contract_earnest_money_model->get_all_by($cond_where, $this->_offset, $this->_limit);
    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        $payee_agency = array();
        if ($val['payee_agency_id']) {
          $payee_agency = $this->agency_model->get_by_id($val['payee_agency_id']);
        }
        //查找门店名称
        $list[$key]['agency_name'] = $payee_agency['name'];
        $payee_broker = array();
        //查找经纪人
        if ($val['payee_broker_id']) {
          $payee_broker = $this->broker_info_model->get_by_broker_id($val['payee_broker_id']);
        }
        $list[$key]['broker_name'] = $payee_broker['truename'];
        $list[$key]['house_id'] = format_info_id($val['house_id'], $type);
      }
    }
    //诚意金统计
    $data['sum'] = $this->contract_earnest_money_model->sum_earnest_price_by($cond_where);
    //载入数据
    $data['list'] = $list;
    $data['post_config'] = $post_config;
    $data['config'] = $config;
    //新增诚意金、编辑和删除
    $earnest_money_add_per = $this->broker_permission_model->check('106');
    $earnest_money_edit_per = $this->broker_permission_model->check('107');
    $earnest_money_delete_per = $this->broker_permission_model->check('108');
    $data['auth'] = array(
      'add' => $earnest_money_add_per, 'edit' => $earnest_money_edit_per,
      'delete' => $earnest_money_delete_per,
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
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_contract.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
      . 'mls/js/v1.0/backspace.js');
    $this->view('contract/earnest_money/manage', $data);
  }

  //打开详情页
  public function details($type, $id)
  {
    $config = $this->contract_earnest_money_model->get_config();
    $post_param = array();
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['payee_company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['payee_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['payee_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['payee_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['payee_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['payee_broker_id'] = $this->user_arr['broker_id'];
    }
    $cond_where = $this->_get_cond_str($post_param);
    $cond_where .= ' AND id = ' . $id;
    $earnest_money = $this->contract_earnest_money_model->get_one_by($cond_where);
    $earnest_money['house_id'] = format_info_id($earnest_money['house_id'], $type);
    $data['earnest_money'] = $earnest_money;
    $post_agency_id = $earnest_money['payee_agency_id'];
    //页面搜索条件
    $post_config = array();
    $this->load->model('contract_model');
    $range_menu = $this->contract_model->get_range_menu_by_role_level($this->user_arr, $post_agency_id);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];
    //交易类型
    $post_config['trade_type'] = $config['trade_type'];
    //物业类型
    $post_config['sell_type'] = $config['sell_type'];
    //收款方式
    $post_config['collect_type'] = array_merge(array('请选择'), $config['collect_type']);
    //退款方式
    $post_config['refund_type'] = $config['refund_type'];
    //诚意金状态
    $post_config['status'] = array_merge(array('请选择'), $config['status']);
    //交易类型
    $post_config['type_id'] = $type == 'sell' ? 1 : 2;
    //标题
    $data['earnest_money_id'] = $id;
    $data['post_config'] = $post_config;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_contract.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
      . 'mls/js/v1.0/backspace.js');
    $this->view('contract/earnest_money/details', $data);
  }

  public function edit($type, $id = 0)
  {
    $config = $this->contract_earnest_money_model->get_config();
    //有诚意金
    if (intval($id) > 0) {
      $role_level = $this->user_arr['role_level'];
      if ($role_level < 6) //公司
      {
        //所属公司
        $post_param['payee_company_id'] = $this->user_arr['company_id'];
      } else if ($role_level < 8) //门店
      {
        //所属公司
        $post_param['payee_company_id'] = $this->user_arr['company_id'];
        //所属门店
        $post_param['payee_agency_id'] = $this->user_arr['agency_id'];
      } else {
        //所属公司
        $post_param['payee_company_id'] = $this->user_arr['company_id'];
        //所属门店
        $post_param['payee_agency_id'] = $this->user_arr['agency_id'];
        //所属经纪人
        $post_param['payee_broker_id'] = $this->user_arr['broker_id'];
      }
      //查询诚意金内容
      $cond_where = $this->_get_cond_str($post_param);
      $cond_where .= ' AND id = ' . $id;
      $earnest_money = $this->contract_earnest_money_model->get_one_by($cond_where);
      $earnest_money['house_id'] = format_info_id($earnest_money['house_id'], $type);
      $data['earnest_money'] = $earnest_money;
      $post_agency_id = $earnest_money['payee_agency_id'];
      //修改权限
      $earnest_money_edit_per = $this->broker_permission_model->check('107');
      if (isset($earnest_money_edit_per['auth']) && !$earnest_money_edit_per['auth']) {
        $this->redirect_permission_none_iframe('js_edit_pop');
        die();
      }
    } else //添加
    {
      $earnest_money_add_per = $this->broker_permission_model->check('106');
      if (isset($earnest_money_add_per['auth']) && !$earnest_money_add_per['auth']) {
        $this->redirect_permission_none_iframe('js_edit_pop');
        die();
      }
    }
    //页面搜索条件
    $post_config = array();
    $this->load->model('contract_model');
    $range_menu = $this->contract_model->get_range_menu_by_role_level($this->user_arr, $post_agency_id);
    //门店数据
    $post_config['agencys'] = $range_menu['agencys'];
    //经纪人数据
    $post_config['brokers'] = $range_menu['brokers'];
    //交易类型
    $post_config['trade_type'] = $config['trade_type'];
    //物业类型
    $post_config['sell_type'] = $config['sell_type'];
    //收款方式
    $post_config['collect_type'] = array_merge(array('请选择'), $config['collect_type']);
    //退款方式
    $post_config['refund_type'] = $config['refund_type'];
    //诚意金状态
    $post_config['status'] = $config['status'];
    //交易类型
    $post_config['type_id'] = $type == 'sell' ? 1 : 2;
    //标题
    $data['earnest_money_id'] = $id;
    $data['post_config'] = $post_config;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_contract.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
      . 'mls/js/v1.0/backspace.js');
    $this->view('contract/earnest_money/edit', $data);
  }

  //保存合同诚意金
  public function save()
  {
    $post_param = $this->input->post(NULL, TRUE);
    //合同添加信息数组
    $data_info = array(
      'broker_id' => intval($this->user_arr['broker_id']),
      'agency_id' => intval($this->user_arr['agency_id']),
      'company_id' => intval($this->user_arr['company_id']),
      'trade_type' => trim($post_param['trade_type']),
      'sell_type' => trim($post_param['sell_type']),
      'house_id' => substr(trim($post_param['house_id']), 2),
      'intension_price' => sprintf('%.2f', $post_param['intension_price']),
      'block_name' => trim($post_param['block_name']),
      'block_id' => intval($post_param['block_id']),
      'address' => trim($post_param['address']),
      'seller_owner' => trim($post_param['seller_owner']),
      'seller_telno' => trim($post_param['seller_telno']),
      'seller_idcard' => trim($post_param['seller_idcard']),
      'buyer_owner' => trim($post_param['buyer_owner']),
      'buyer_telno' => trim($post_param['buyer_telno']),
      'buyer_idcard' => trim($post_param['buyer_idcard']),
      'earnest_price' => intval($post_param['earnest_price']),
      'collection_time' => trim($post_param['collection_time']),
      'status' => trim($post_param['status']),
      'payee_agency_id' => intval($post_param['payee_agency_id']),
      'payee_broker_id' => intval($post_param['payee_broker_id']),
      'collect_type' => trim($post_param['collect_type']),
      'refund_type' => trim($post_param['refund_type']),
      'refund_reason' => trim($post_param['refund_reason']),
      'remark' => trim($post_param['remark']),
      'update_time' => time()
    );
    $id = $post_param['id'];
    if (intval($id) > 0) //修改
    {
      //诚意金候改状态
      //查找原来的没有修改之前的状态
      $where = 'id = ' . $id;
      $earnest_money = $this->contract_earnest_money_model->get_one_by($where);
      if ($earnest_money['status'] != $data_info['status']) {
        $earnest_money_status_per = $this->broker_permission_model->check('109');
        if (isset($earnest_money_status_per['auth']) && !$earnest_money_status_per['auth']) {
          echo json_encode(array('result' => -1, 'id' => $id));
          die();
        }
      }
      //修改权限
      $earnest_money_edit_per = $this->broker_permission_model->check('107');
      if (isset($earnest_money_edit_per['auth']) && $earnest_money_edit_per['auth']) {
        $effected_rows = $this->contract_earnest_money_model->update_by_id($data_info, $id);
        if ($effected_rows) {
          $str = $this->modify_match($data_info, $earnest_money);
          //操作日志
          $add_log_param = array(
            'company_id' => $this->user_arr['company_id'],
            'agency_id' => $this->user_arr['agency_id'],
            'broker_id' => $this->user_arr['broker_id'],
            'broker_name' => $this->user_arr['truename'],
            'type' => 35,
            'text' => '房源编号为' . $post_param['house_id'] . '的诚意金。' . $str,
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
    } else //添加
    {
      $earnest_money_add_per = $this->broker_permission_model->check('106');
      if (isset($earnest_money_add_per['auth']) && $earnest_money_add_per['auth']) {
        $effected_rows = $this->contract_earnest_money_model->insert($data_info);
        //操作日志
        $add_log_param = array(
          'company_id' => $this->user_arr['company_id'],
          'agency_id' => $this->user_arr['agency_id'],
          'broker_id' => $this->user_arr['broker_id'],
          'broker_name' => $this->user_arr['truename'],
          'type' => 35,
          'text' => '新增房源编号为' . $post_param['house_id'] . '的诚意金' . $data_info['earnest_price'] . '元。',
          'from_system' => 1,
          'from_ip' => get_ip(),
          'mac_ip' => '127.0.0.1',
          'from_host_name' => '127.0.0.1',
          'hardware_num' => '测试硬件序列号',
          'time' => time()
        );
        $this->operate_log_model->add_operate_log($add_log_param);
      } else {
        $this->redirect_permission_none();
        die();
      }
    }
    echo json_encode(array('result' => $effected_rows, 'id' => $id));
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
    $earnest_money_delete_per = $this->broker_permission_model->check('108');
    if (isset($earnest_money_delete_per['auth']) && !$earnest_money_delete_per['auth']) {
      $this->redirect_permission_none();
      die();
    }
    $updater_arr = array('valid_flag' => 2);
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
    $id = $this->input->get('id');
    $effected_rows = $this->contract_earnest_money_model->update_by_id($updater_arr, $id);
    //操作日志
    $info = $this->contract_earnest_money_model->get_by_id($id);
    if ($info['trade_type'] == 1) {
      $house_id = format_info_id($info['house_id'], 'sell');
    } else {
      $house_id = format_info_id($info['house_id'], 'rent');
    }
    $add_log_param = array(
      'company_id' => $this->user_arr['company_id'],
      'agency_id' => $this->user_arr['agency_id'],
      'broker_id' => $this->user_arr['broker_id'],
      'broker_name' => $this->user_arr['truename'],
      'type' => 35,
      'text' => '删除房源编号为' . $house_id . '的诚意金' . $data_info['earnest_price'] . '元。',
      'from_system' => 1,
      'from_ip' => get_ip(),
      'mac_ip' => '127.0.0.1',
      'from_host_name' => '127.0.0.1',
      'hardware_num' => '测试硬件序列号',
      'time' => time()
    );
    $this->operate_log_model->add_operate_log($add_log_param);
    echo json_encode(array('result' => $effected_rows));
  }

  //根据门店id获取经纪人
  public function broker_list()
  {
    $agency_id = $this->input->get('agency_id', TRUE);
    $this->load->model('broker_info_model');
    $this->broker_info_model->set_select_fields(array('broker_id', 'truename'));
    $data['list'] = $this->broker_info_model->get_by_agency_id($agency_id);
    if (is_full_array($data['list'])) {
      $data['result'] = 1;
      $data['msg'] = '查询成功';
    } else {
      $data['result'] = 0;
      $data['msg'] = '查询失败';
    }
    echo json_encode($data);
  }

  //修改合同内容匹配
  public function modify_match($data1, $data2)
  {
    $data = array_diff_assoc($data1, $data2);
    $str = '';
    $config = $this->contract_earnest_money_model->get_config();
    foreach ($data as $key => $val) {
      switch ($key) {
        case 'broker_id':
          $info1 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['broker_id']);
          $info2 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['broker_id']);
          $str .= "“经纪人”由“" . $info2['agency_name'] . "-" . $info2['truename'] . "”改为“" . $info1['agency_name'] . "-" . $info1['truename'] . "”；";
          break;
        case 'house_id':
          if ($data2['trade_type'] == 1) {
            $str1 = "CS";
          } else {
            $str1 = "CZ";
          }
          $str .= "“房源编号”由“{$str1}{$data2['house_id']}”改为“{$str1}{$data1['house_id']}”；";
          break;
        case 'block_name':
          $str .= "“楼盘”由“" . $data2['block_name'] . "”改为“" . $data1['block_name'] . "”；";
          break;
        case 'address':
          $str .= "“地址”由“" . $data2['address'] . "”改为“" . $data1['address'] . "”；";
          break;
        case 'sell_type':
          $str .= "“物业类型”由“" . $config['sell_type'][$data2['sell_type']] . "”改为“" . $config['sell_type'][$data2['sell_type']] . "”；";
          break;
        case 'intension_price':
          $str .= "“意向金额”由“" . strip_end_0($data2['intension_price']) . "元”改为“" . strip_end_0($data1['intension_price']) . "元”；";
          break;
        case 'seller_owner':
          $str .= "“业主姓名”由“" . $data2['seller_owner'] . "”改为“" . $data1['seller_owner'] . "”；";
          break;
        case 'seller_telno':
          $str .= "“业主电话”由“" . $data2['seller_telno'] . "”改为“" . $data1['seller_telno'] . "”；";
          break;
        case 'seller_idcard':
          $str .= "“业主身份证号”由“" . $data2['seller_idcard'] . "”改为“" . $data1['seller_idcard'] . "”；";
          break;
        case 'buyer_owner':
          $str .= "“买方姓名”由“" . $data2['buyer_owner'] . "”改为“" . $data1['buyer_owner'] . "”；";
          break;
        case 'buyer_telno':
          $str .= "“买方电话”由“" . $data2['buyer_telno'] . "”改为“" . $data1['buyer_telno'] . "”；";
          break;
        case 'buyer_idcard':
          $str .= "“买方身份证号”由“" . $data2['buyer_idcard'] . "”改为“" . $data1['buyer_idcard'] . "”；";
          break;
        case 'earnest_price':
          $str .= "“诚意金额”由“" . strip_end_0($data2['earnest_price']) . "”改为“" . strip_end_0($data1['earnest_price']) . "”；";
          break;
        case 'collection_time':
          $str .= "“收款时间”由“" . data('Y-m-d', $data2['collection_time']) . "”改为“" . data('Y-m-d', $data1['collection_time']) . "”；";
          break;
        case 'status':
          $str .= "“诚意金状态”由“" . $config['status'][$data2['status']] . "”改为“" . $config['status'][$data1['status']] . "”；";
          break;
        case 'payee_broker_id':
          $info1 = $this->api_broker_model->get_baseinfo_by_broker_id($data1['payee_broker_id']);
          $info2 = $this->api_broker_model->get_baseinfo_by_broker_id($data2['payee_broker_id']);
          $str .= "“收款经纪人”由“" . $info2['agency_name'] . "-" . $info2['truename'] . "”改为“" . $info1['agency_name'] . "-" . $info1['truename'] . "”；";
          break;
        case 'collect_type':
          $str .= "“收款方式”由“" . $config['collect_type'][$data2['collect_type']] . "”改为“" . $config['collect_type'][$data1['collect_type']] . "”；";
          break;
        case 'refund_type':
          $str .= "“退款方式”由“" . $config['refund_type'][$data2['refund_type']] . "”改为“" . $config['refund_type'][$data1['refund_type']] . "”；";
          break;
        case 'refund_reason':
          $str .= "“退款说明”由“" . $data2['refund_reason'] . "”改为“" . $data1['refund_reason'] . "”；";
          break;
        case 'remark':
          $str .= "“备注”由“" . $data2['remark'] . "”改为“" . $data1['remark'] . "”；";
          break;
      }
    }
    return $str;
  }

  public function export($type = '')
  {
    //获取所有请求参数
    $post_param = $this->input->post(NULL, TRUE);
    $config = $this->contract_earnest_money_model->get_config();
    $post_param['trade_type'] = $type == 'sell' ? 1 : 2;
    //搜索参数范围权限控制
    $role_level = $this->user_arr['role_level'];
    if ($role_level < 6) //公司
    {
      //所属公司
      $post_param['payee_company_id'] = $this->user_arr['company_id'];
    } else if ($role_level < 8) //门店
    {
      //所属公司
      $post_param['payee_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['payee_agency_id'] = $this->user_arr['agency_id'];
    } else {
      //所属公司
      $post_param['payee_company_id'] = $this->user_arr['company_id'];
      //所属门店
      $post_param['payee_agency_id'] = $this->user_arr['agency_id'];
      //所属经纪人
      $post_param['payee_broker_id'] = $this->user_arr['broker_id'];
    }
    $cond_where = $this->_get_cond_str($post_param);
    //获取列表内容
    $list = $this->contract_earnest_money_model->get_all_by($cond_where, -1);
    //print_r($list);die();
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
    $D_name = $type == 'sell' ? '万元' : '元';
    //设置表格导航属性
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '房源编号');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '房源地址');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '业主姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', "意向金额({$D_name})");
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '客户姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '诚意金额(元)');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '收款门店');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '收款人');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '收款方式');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '状态');
    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {
      $payee_agency = $this->agency_model->get_by_id($list[$i - 2]['payee_agency_id']);
      //查找经纪人
      $payee_broker = $this->broker_info_model->get_by_broker_id($list[$i - 2]['payee_broker_id']);
      $house_id = format_info_id($list[$i - 2]['house_id'], $type);
      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $house_id);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['address']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['seller_owner']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, strip_end_0($list[$i - 2]['intension_price']));
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['buyer_owner']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['earnest_price']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $payee_agency['name']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $payee_broker['truename']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $config['collect_type'][$list[$i - 2]['collect_type']]);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $config['status'][$list[$i - 2]['status']]);
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
    $cond_where = '`valid_flag` = 1';
    $keyword_type = isset($form_param['keyword_type']) ? intval($form_param['keyword_type']) : 0;
    $keyword = isset($form_param['keyword']) ? trim($form_param['keyword']) : "";
    if ($keyword) {
      if ($keyword_type == 1) {
        //业主姓名
        $cond_where .= " AND seller_owner like '%" . $keyword . "%'";
      } elseif ($keyword_type == 2) {
        //客户姓名
        $cond_where .= " AND buyer_owner like '%" . $keyword . "%'";
      }
    }
    //交易方式
    if (isset($form_param['trade_type'])) {
      $cond_where .= " AND trade_type = '" . $form_param['trade_type'] . "'";
    }
    //报备时间
    if (isset($form_param['start_time'])) {
      $cond_where .= " AND collection_time >= '" . $form_param['start_time'] . "'";
    }
    if (isset($form_param['end_time']) && !empty($form_param['end_time'])) {
      $cond_where .= " AND collection_time <= '" . $form_param['end_time'] . "'";
    }
    //楼盘
    if (isset($form_param['block_id']) && !empty($form_param['block_id'])) {
      $cond_where .= " AND block_id = '" . $form_param['block_id'] . "'";
    }
    if (isset($form_param['block_name']) && !empty($form_param['block_name'])) {
      $cond_where .= " AND block_name like '%" . $form_param['block_name'] . "%'";
    }
    //签约公司
    if (isset($form_param['payee_company_id']) && $form_param['payee_company_id'] > 0) {
      $cond_where .= " AND company_id = '" . $form_param['payee_company_id'] . "'";
    }
    //签约门店
    if (isset($form_param['payee_agency_id']) && $form_param['payee_agency_id'] > 0) {
      $cond_where .= " AND payee_agency_id = '" . $form_param['payee_agency_id'] . "'";
    }
    //签约人
    if (isset($form_param['payee_broker_id']) && $form_param['payee_broker_id'] > 0) {
      $cond_where .= " AND payee_broker_id = '" . $form_param['payee_broker_id'] . "'";
    }
    //状态
    if (isset($form_param['status']) && $form_param['status'] > 0) {
      $cond_where .= " AND status = '" . $form_param['status'] . "'";
    }
    return $cond_where;
  }
}
