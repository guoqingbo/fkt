<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cooperate_order extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('common_load_source_helper');
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->helper('community_helper');
    $this->load->model('cooperate_model');//合作模型类
    $this->load->model('api_broker_base_model');
    $this->load->model('sell_house_model');//二手房源模型类
    $this->load->library('form_validation');//表单验证
    $this->load->model('agency_model');
  }


  /**
   * 出售合作订单
   */
  public function sell()
  {
    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';
    //订单状态
    $conf_data = $this->cooperate_model->get_base_conf();
    $status_arr = $conf_data['esta'];
    $data['status_arr'] = $status_arr;
    //筛选条件
    $data['where_cond'] = array();
    $where = "tbl = 'sell'";
    $data['where_cond']['tbl'] = 'sell';
    //状态
    $status = $this->input->post('status');
    //交易编号
    $order_sn = $this->input->post('order_sn');
    //公司编号
    $company_id = $this->input->post('company_id');
    $company_name = $this->input->post('company_name');
    //门店编号
    $agency_id = $this->input->post('agency_id');
    if ($status) {
      $data['where_cond']['esta'] = intval($status);
      $where .= " and esta = " . intval($status);
    }
    if ($order_sn) {
      $data['where_cond']['order_sn'] = trim($order_sn);
      $where .= " and order_sn = '" . trim($order_sn) . "'";
    }
    if ($agency_id) {
      $data['where_cond']['company_id'] = intval($company_id);
      $data['where_cond']['company_name'] = trim($company_name);
      $data['where_cond']['agency_id'] = intval($agency_id);
      $agency_arr = $this->agency_model->get_children_by_company_id($company_id);
      $data['agencys'] = $agency_arr;
      $where .= " and (agentid_a = '" . trim($agency_id)
        . "' OR agentid_b = '" . trim($agency_id) . "')";
    } else if ($company_id) {
      $data['where_cond']['company_id'] = intval($company_id);
      $data['where_cond']['company_name'] = trim($company_name);
      $agency_arr = $this->agency_model->get_children_by_company_id($company_id);
      $data['agencys'] = $agency_arr;
      if (is_full_array($agency_arr)) {
        $agency_id_string = '';
        foreach ($agency_arr as $vo) {
          $agency_id_string .= $vo['id'] . ',';
        }
        $agency_id_string = substr($agency_id_string, 0, -1);
        $where .= " and (agentid_a in (" . $agency_id_string
          . ") OR agentid_b in (" . $agency_id_string . "))";
      }

      //print_r($agency_id_string);exit;
    }
    //经纪人姓名
    $data['where_or_cond'] = array();
    $broker_name = $this->input->post('broker_name');
    if ($broker_name) {
      $data['where_cond']['broker_name_a'] = trim($broker_name);
      $data['where_or_cond']['broker_name_b'] = trim($broker_name);
      $where .= " and (broker_name_a = '" . trim($broker_name)
        . "' OR broker_name_b = '" . trim($broker_name) . "')";
    }
    //时间筛选
    $time_s = strtotime($this->input->post('time_s'));
    $time_e = strtotime($this->input->post('time_e')) + 86399;
    if ($time_s && $time_e) {
      $where .= ' and dateline >= "' . $time_s . '"';
      $where .= ' and dateline <= "' . $time_e . '"';
    }
    //分页开始
    $data['cooperate_num'] = $this->cooperate_model->get_cooperate_num_by_cond($where);
    $data['pagesize'] = 50; //设定每一页显示的记录数
    $data['pages'] = $data['cooperate_num'] ? ceil($data['cooperate_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    $data['cooperate_order'] = $this->cooperate_model->get_list_by_cond2($where, '', $data['offset'], $data['pagesize']);
    //数据重构
    foreach ($data['cooperate_order'] as $k => $v) {
      //根据经纪人id，获得门店
      $brokerid_a = intval($v['brokerid_a']);
      $brokerid_b = intval($v['brokerid_b']);
      if (!empty($brokerid_a) && !empty($brokerid_b)) {
        $broker_a_data = $this->api_broker_base_model->get_by_broker_id($brokerid_a);
        $broker_b_data = $this->api_broker_base_model->get_by_broker_id($brokerid_b);
        $agent_a_data = $this->api_broker_base_model->get_by_agency_id($broker_a_data['agency_id']);
        $v['agent_a_name'] = $agent_a_data['name'];
        $agent_b_data = $this->api_broker_base_model->get_by_agency_id($broker_b_data['agency_id']);
        $v['agent_b_name'] = $agent_b_data['name'];
        $company_a_data = $this->api_broker_base_model->get_by_agency_id($agent_a_data['company_id']);
        $v['company_a_name'] = $company_a_data['name'];
        $company_b_data = $this->api_broker_base_model->get_by_agency_id($agent_b_data['company_id']);
        $v['company_b_name'] = $company_b_data['name'];
      }
      //'评价'
      if ($v['appraise_a'] === '0' && $v['appraise_b'] === '0') {
        $v['appraise_str'] = '双方未评';
      } else if ($v['appraise_a'] === '0' && $v['appraise_b'] > 0) {
        $v['appraise_str'] = (2 == $v['apply_type']) ? '甲方已评' : '乙方已评';
      } else if ($v['appraise_b'] === '0' && $v['appraise_a'] > 0) {
        $v['appraise_str'] = (2 == $v['apply_type']) ? '乙方已评' : '甲方已评';
      } else {
        $v['appraise_str'] = '双方已评';
      }
      $data['cooperate_order2'][] = $v;
    }
    //需要加载的JS CS
    $data['css'] = load_css('mls/css/v1.0/autocomplete.css');
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic.js,'
      . 'mls/js/v1.0/cooperate_common.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    $this->load->view('cooperate_order/sell', $data);
  }

  //合作中心升级刷合作数据
  public function sell_cooperate_data_deal($this_page = 1)
  {
    //订单状态
    $conf_data = $this->cooperate_model->get_base_conf();
    $status_arr = $conf_data['esta'];
    $data['status_arr'] = $status_arr;
    //筛选条件
    $data['where_cond'] = array();
    $where = "tbl = 'sell'";
    $data['where_cond']['tbl'] = 'sell';
    //分页开始
    $data['cooperate_num'] = $this->cooperate_model->get_cooperate_num_by_cond($where);
    $data['pagesize'] = 1000; //设定每一页显示的记录数
    $data['pages'] = $data['cooperate_num'] ? ceil($data['cooperate_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($this_page) ? intval($this_page) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    $cooperate_order_arr = $this->cooperate_model->get_list_by_cond2($where, '', $data['offset'], $data['pagesize']);
    if (is_full_array($cooperate_order_arr)) {
      foreach ($cooperate_order_arr as $k => $v) {
        $sell_house_id = intval($v['rowid']);
        $house_data = $this->sell_house_model->get_house_by_id($sell_house_id);
        $update_arr = array();
        if (is_full_array($house_data[0])) {
          $update_arr['reward_type'] = $house_data[0]['reward_type'];
          $update_arr['reward_money'] = $house_data[0]['cooperate_reward'];
          $where_cond = array(
            'id' => intval($v['id'])
          );
          $update_result = $this->cooperate_model->update_info_by_cond($update_arr, $where_cond);
          if ($update_result == 1) {
            echo '更新成功';
          } else {
            echo '更新失败';
          }
        } else {
          echo '房源数据为空';
        }
        echo ',合作id:' . $v['id'] . '------<br>';
      }
    }
  }


  /**
   * 出租合作订单
   */
  public function rent()
  {
    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';
    //订单状态
    $conf_data = $this->cooperate_model->get_base_conf();
    $status_arr = $conf_data['esta'];
    $data['status_arr'] = $status_arr;
    //筛选条件
    $data['where_cond'] = array();
    $data['where_cond']['tbl'] = 'rent';
    $where = "tbl = 'rent'";
    //状态
    $status = $this->input->post('status');
    //交易编号
    $order_sn = $this->input->post('order_sn');
    //公司编号
    $company_id = $this->input->post('company_id');
    $company_name = $this->input->post('company_name');
    //门店编号
    $agency_id = $this->input->post('agency_id');
    if ($status) {
      $data['where_cond']['esta'] = intval($status);
      $where .= " and esta = " . intval($status);
    }
    if ($order_sn) {
      $data['where_cond']['order_sn'] = trim($order_sn);
      $where .= " and order_sn = '" . trim($order_sn) . "'";
    }
    if ($agency_id) {
      $data['where_cond']['company_id'] = intval($company_id);
      $data['where_cond']['company_name'] = trim($company_name);
      $data['where_cond']['agency_id'] = intval($agency_id);
      $agency_arr = $this->agency_model->get_children_by_company_id($company_id);
      $data['agencys'] = $agency_arr;
      $where .= " and (agentid_a = '" . trim($agency_id)
        . "' OR agentid_b = '" . trim($agency_id) . "')";
    } else if ($company_id) {
      $data['where_cond']['company_id'] = intval($company_id);
      $data['where_cond']['company_name'] = trim($company_name);
      $agency_arr = $this->agency_model->get_children_by_company_id($company_id);
      $data['agencys'] = $agency_arr;
      if (is_full_array($agency_arr)) {
        $agency_id_string = '';
        foreach ($agency_arr as $vo) {
          $agency_id_string .= $vo['id'] . ',';
        }
        $agency_id_string = substr($agency_id_string, 0, -1);
        $where .= " and (agentid_a in (" . $agency_id_string
          . ") OR agentid_b in (" . $agency_id_string . "))";
      }

      //print_r($agency_id_string);exit;
    }
    //经纪人姓名
    $data['where_or_cond'] = array();
    $broker_name = $this->input->post('broker_name');
    if ($broker_name) {
      $data['where_cond']['broker_name_a'] = trim($broker_name);
      $data['where_or_cond']['broker_name_b'] = trim($broker_name);
      $where .= " and (broker_name_a = '" . trim($broker_name)
        . "' OR broker_name_b = '" . trim($broker_name) . "')";
    }
    //时间筛选
    $time_s = strtotime($this->input->post('time_s'));
    $time_e = strtotime($this->input->post('time_e')) + 86399;
    if ($time_s && $time_e) {
      $where .= ' and dateline >= "' . $time_s . '"';
      $where .= ' and dateline <= "' . $time_e . '"';
    }
    //分页开始
    $data['cooperate_num'] = $this->cooperate_model->get_cooperate_num_by_cond($where);
    $data['pagesize'] = 50; //设定每一页显示的记录数
    $data['pages'] = $data['cooperate_num'] ? ceil($data['cooperate_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    $data['cooperate_order'] = $this->cooperate_model->get_list_by_cond2($where, '', $data['offset'], $data['pagesize']);
    //数据重构
    foreach ($data['cooperate_order'] as $k => $v) {
      $agent_a_data = $this->api_broker_base_model->get_by_agency_id($v['agentid_a']);
      $v['agent_a_name'] = $agent_a_data['name'];
      $agent_b_data = $this->api_broker_base_model->get_by_agency_id($v['agentid_b']);
      $v['agent_b_name'] = $agent_b_data['name'];
      $company_a_data = $this->api_broker_base_model->get_by_agency_id($agent_a_data['company_id']);
      $v['company_a_name'] = $company_a_data['name'];
      $company_b_data = $this->api_broker_base_model->get_by_agency_id($agent_b_data['company_id']);
      $v['company_b_name'] = $company_b_data['name'];
      //'评价'
      if ($v['appraise_a'] === '0' && $v['appraise_b'] === '0') {
        $v['appraise_str'] = '双方未评';
      } else if ($v['appraise_a'] === '0' && $v['appraise_b'] > 0) {
        $v['appraise_str'] = (2 == $v['apply_type']) ? '甲方已评' : '乙方已评';
      } else if ($v['appraise_b'] === '0' && $v['appraise_a'] > 0) {
        $v['appraise_str'] = (2 == $v['apply_type']) ? '乙方已评' : '甲方已评';
      } else {
        $v['appraise_str'] = '双方已评';
      }
      $data['cooperate_order2'][] = $v;
    }
    //需要加载的JS CS
    $data['css'] = load_css('mls/css/v1.0/autocomplete.css');
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic.js,'
      . 'mls/js/v1.0/cooperate_common.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    $this->load->view('cooperate_order/rent', $data);
  }

  /**
   * 冻结
   */
  public function modify($id)
  {
    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';

    if (!empty($id)) {
      $cond_where = array();
      $cond_where['id'] = $id;
      $update_arr = array();
      $update_arr['esta'] = 10;
      $data['update_result'] = $this->cooperate_model->update_info_by_cond($update_arr, $cond_where);
      //获得当前订单详情
      $detail_data = $this->cooperate_model->get_cooperate_by_cid($id);
      $tbl = $detail_data['tbl'];
      $params['type'] = "f";
      $params['id'] = $detail_data['rowid'];
      $params['id'] = format_info_id($params['id'], $tbl);
      if ($data['update_result']) {
        //冻结成功执行message添加动作
        //33
        $this->load->model('message_base_model');
        $this->message_base_model->add_message('1-9-2', $detail_data['brokerid_b'], $detail_data['broker_name_b'], '/cooperate/send_order_list/', $params);
        $this->message_base_model->add_message('1-9-1', $detail_data['brokerid_a'], $detail_data['broker_name_a'], '/cooperate/accept_order_list/', $params);
      }
    } else {
      $data['update_result'] = 0;
    }
    $this->load->view('cooperate_order/modify', $data);
  }

  /**
   * 详情
   */
  public function detail($c_id)
  {
    $login_city = !empty($_SESSION[WEB_AUTH]['city']) ? $_SESSION[WEB_AUTH]['city'] : 'sh';
    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';
    $c_id = intval($c_id);
    $data['ct_id'] = $c_id;
    if ($c_id > 0) {
      $data['iframe_src'] = MLS_URL . '/cooperate_admin/my_accept_order/' . $c_id . '/' . $login_city;
    }
    $this->load->view('cooperate_order/details', $data);
  }


  /**
   * 导出表数据
   * @author   wang
   */
  public function exportReport($type = '')
  {

    ini_set('memory_limit', '-1');
    //表单提交参数组成的查询条件
    //订单状态
    $conf_data = $this->cooperate_model->get_base_conf();
    $status_arr = $conf_data['esta'];

    //筛选条件
    if ($type) {
      $where = "tbl = 'rent'";
    } else {
      $where = "tbl = 'sell'";
    }
    //状态
    $status = $this->input->get('status');
    //交易编号
    $order_sn = $this->input->get('order_sn');
    //公司编号
    $company_id = $this->input->get('company_id');
    //门店编号
    $agency_id = $this->input->get('agency_id');
    //时间筛选
    $time_s = strtotime($this->input->get('time_s'));
    $time_e = strtotime($this->input->get('time_e')) + 86399;
    if ($time_s && $time_e) {
      $where .= ' and dateline >= "' . $time_s . '"';
      $where .= ' and dateline <= "' . $time_e . '"';
    }
    if ($status) {
      $where .= " and esta = " . intval($status);
    }
    if ($order_sn) {
      $where .= " and order_sn = '" . trim($order_sn) . "'";
    }
    if ($agency_id) {
      $agency_arr = $this->agency_model->get_children_by_company_id($company_id);
      $where .= " and (agentid_a = '" . trim($agency_id)
        . "' OR agentid_b = '" . trim($agency_id) . "')";
    } else if ($company_id) {
      $agency_arr = $this->agency_model->get_children_by_company_id($company_id);
      if (is_full_array($agency_arr)) {
        $agency_id_string = '';
        foreach ($agency_arr as $vo) {
          $agency_id_string .= $vo['id'] . ',';
        }
        $agency_id_string = substr($agency_id_string, 0, -1);
        $where .= " and (agentid_a in (" . $agency_id_string
          . ") OR agentid_b in (" . $agency_id_string . "))";
      }

      //print_r($agency_id_string);exit;
    }
    //经纪人姓名
    $broker_name = $this->input->get('broker_name');
    if ($broker_name) {
      $where .= " and (broker_name_a = '" . trim($broker_name)
        . "' OR broker_name_b = '" . trim($broker_name) . "')";
    }
    //分页开始
    $data['cooperate_num'] = $this->cooperate_model->get_cooperate_num_by_cond($where);
    $data['pagesize'] = 50; //设定每一页显示的记录数
    $data['pages'] = $data['cooperate_num'] ? ceil($data['cooperate_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    $data['cooperate_order'] = $this->cooperate_model->get_list_by_cond2($where, '', 0, $data['cooperate_num']);
    //数据重构
    foreach ($data['cooperate_order'] as $k => $v) {
      //根据经纪人id，获得门店
      $brokerid_a = intval($v['brokerid_a']);
      $brokerid_b = intval($v['brokerid_b']);
      if (!empty($brokerid_a) && !empty($brokerid_b)) {
        $broker_a_data = $this->api_broker_base_model->get_by_broker_id($brokerid_a);
        $broker_b_data = $this->api_broker_base_model->get_by_broker_id($brokerid_b);
        $agent_a_data = $this->api_broker_base_model->get_by_agency_id($broker_a_data['agency_id']);
        $v['agent_a_name'] = $agent_a_data['name'];
        $agent_b_data = $this->api_broker_base_model->get_by_agency_id($broker_b_data['agency_id']);
        $v['agent_b_name'] = $agent_b_data['name'];
        $company_a_data = $this->api_broker_base_model->get_by_agency_id($agent_a_data['company_id']);
        $v['company_a_name'] = $company_a_data['name'];
        $company_b_data = $this->api_broker_base_model->get_by_agency_id($agent_b_data['company_id']);
        $v['company_b_name'] = $company_b_data['name'];
      }
      //'评价'
      if ($v['appraise_a'] === '0' && $v['appraise_b'] === '0') {
        $v['appraise_str'] = '双方未评';
      } else if ($v['appraise_a'] === '0' && $v['appraise_b'] > 0) {
        $v['appraise_str'] = (2 == $v['apply_type']) ? '甲方已评' : '乙方已评';
      } else if ($v['appraise_b'] === '0' && $v['appraise_a'] > 0) {
        $v['appraise_str'] = (2 == $v['apply_type']) ? '乙方已评' : '甲方已评';
      } else {
        $v['appraise_str'] = '双方已评';
      }
      //合作时间读取
      $v['time_esta1'] = '';//申请时间
      $v['time_esta4'] = '';//生效时间
      $v['time_esta7'] = '';//成交时间
      $v['time_cycle'] = '';//合作成交周期
      $time_esta1 = $this->cooperate_model->get_cooperate_log_by($v['id'], 1);
      $time_esta4 = $this->cooperate_model->get_cooperate_log_by($v['id'], 4);
      $time_esta7 = $this->cooperate_model->get_cooperate_log_by($v['id'], 7);
      if ($time_esta1) {
        $v['time_esta1'] = date('Y-m-d H:i:s', $time_esta1['dateline']);
      }
      if ($time_esta4) {
        $v['time_esta4'] = date('Y-m-d H:i:s', $time_esta4['dateline']);
      }
      if ($time_esta7) {
        $v['time_esta7'] = date('Y-m-d H:i:s', $time_esta7['dateline']);
        $v['time_cycle'] = round(($time_esta7['dateline'] - $time_esta1['dateline']) / 86400, 2);
      }
      //成交价
      $price_unit = $v['tbl'] == 'sell' ? '万元' : '元/月';
      $v['price'] = strip_end_0($v['price']) . $price_unit;
      $data['cooperate_order2'][] = $v;
    }
    $list = $data['cooperate_order2'];
    //print_r($list);exit;

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
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '甲方');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '甲方');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '甲方');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '甲方');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '乙方');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '乙方');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '乙方');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '乙方');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', '');
    $objPHPExcel->getActiveSheet()->setCellValue('Q1', '');

    $objPHPExcel->getActiveSheet()->setCellValue('A2', '交易编号');
    $objPHPExcel->getActiveSheet()->setCellValue('B2', '经纪人');
    $objPHPExcel->getActiveSheet()->setCellValue('C2', '门店');
    $objPHPExcel->getActiveSheet()->setCellValue('D2', '公司');
    $objPHPExcel->getActiveSheet()->setCellValue('E2', '手机');
    $objPHPExcel->getActiveSheet()->setCellValue('F2', '经纪人');
    $objPHPExcel->getActiveSheet()->setCellValue('G2', '门店');
    $objPHPExcel->getActiveSheet()->setCellValue('H2', '公司');
    $objPHPExcel->getActiveSheet()->setCellValue('I2', '手机');
    $objPHPExcel->getActiveSheet()->setCellValue('J2', '状态更新时间');
    $objPHPExcel->getActiveSheet()->setCellValue('K2', '状态');
    $objPHPExcel->getActiveSheet()->setCellValue('L2', '评价');
    $objPHPExcel->getActiveSheet()->setCellValue('M2', '合作申请时间');
    $objPHPExcel->getActiveSheet()->setCellValue('N2', '合作生效时间');
    $objPHPExcel->getActiveSheet()->setCellValue('O2', '合作成交时间');
    $objPHPExcel->getActiveSheet()->setCellValue('P2', '合作成交周期');
    $objPHPExcel->getActiveSheet()->setCellValue('Q2', '成交价');

    //设置表格的值
    for ($i = 3; $i <= count($list) + 2; $i++) {

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 3]['order_sn'] . ' ');
      if (2 == $list[$i - 3]['apply_type']) {
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 3]['broker_name_b']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 3]['agent_b_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 3]['company_b_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 3]['phone_b']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 3]['broker_name_a']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 3]['agent_a_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 3]['company_a_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 3]['phone_a']);
      } else {
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 3]['broker_name_a']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 3]['agent_a_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 3]['company_a_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 3]['phone_a']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 3]['broker_name_b']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 3]['agent_b_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 3]['company_b_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 3]['phone_b']);
      }
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, date('Y-m-d', $list[$i - 3]['dateline']));
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $status_arr[$list[$i - 3]['esta']]);
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $list[$i - 3]['appraise_str']);
      $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $list[$i - 3]['time_esta1']);
      $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $list[$i - 3]['time_esta4']);
      $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $list[$i - 3]['time_esta7']);
      $objPHPExcel->getActiveSheet()->setCellValue('P' . $i, $list[$i - 3]['time_cycle']);
      $objPHPExcel->getActiveSheet()->setCellValue('Q' . $i, $list[$i - 3]['price']);
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

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
