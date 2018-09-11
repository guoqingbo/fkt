<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 付费申请审核
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Pay_applay extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('pay_applay_model');
    $this->load->helper('user_helper');
  }

  public function index()
  {
    $data_view = array();
    $this->load->helper('page_helper');
    $pg = $this->input->post('pg');
    $search_where = $this->input->post('search_where');
    $search_value = $this->input->post('search_value');
    $search_status = $this->input->post('search_status');
    if (!$search_status) {
      $search_status = 99;
    }
    $where = '';
    //引入经纪人基本类库
    $this->load->model('broker_model');
    $brokers = array(); //搜索查询条件值
    $this->broker_model->set_select_fields(array('id'));
    $search_broker_base = false;
    if ($search_where && $search_value) {
      $search_broker = "$search_where like '%$search_value%' and status = 1";
      //查找所有的broker_id
      $brokers = $this->broker_model->get_all_by($search_broker);
      $search_broker_base = true;
    }
    //引入经纪人基本类库
    $this->load->model('broker_info_model');
    $broker_ids = $this->broker_info_model->format_brokers($brokers);
    if (is_full_array($broker_ids)) {
      $broker_ids = implode(',', $broker_ids);
      $where .= "broker_id in ($broker_ids)";
    } else if ($search_broker_base) {
      $where .= "broker_id = 0";
    }

    if ($search_status != 99) {
      if ($where == '') {
        $where = "status = " . $search_status;
      } else {
        $where .= " and status = " . $search_status;
      }
    }

    //条件
    $data_view['where_cond'] = array(
      'search_where' => $search_where, 'search_value' => $search_value, 'search_status' => $search_status
    );

    //分页开始
    $data_view['count'] = $this->pay_applay_model->count_by($where);
    $data_view['pagesize'] = 10; //设定每一页显示的记录数
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //门店列表
    $data_view['pay_applay'] = $this->pay_applay_model->get_all_by(
      $where, $data_view['offset'], $data_view['pagesize']);
    if ($data_view['pay_applay']) {
      $this->broker_model->set_select_fields(array('phone'));
      foreach ($data_view['pay_applay'] as &$v) {
        //根据经纪人id获取信息
        $broker = $this->broker_model->get_by_id($v['broker_id']);
        $v['phone'] = $broker['phone'];
        $v['status_str'] = $this->pay_applay_model->status[$v['status']];
      }
    }
    $data_view['title'] = '付费申请';
    $data_view['conf_where'] = 'index';
    $this->load->view('pay_applay/index', $data_view);
  }

  public function add()
  {
    $insert_data = array(
      'broker_id' => 7, 'status' => 1
    );
    $this->pay_applay_model->insert($insert_data);
  }

  public function modify($id)
  {
    $data_view = array();
    $data_view['title'] = '付费申请-修改付费';
    $data_view['conf_where'] = 'index';
    $data_view['modifyResult'] = '';
    //查询付费记录
    $data_view['pay_applay'] = $this->pay_applay_model->get_by_id($id);
    $submit_flag = $this->input->post('submit_flag');
    if ($submit_flag == 'modify') {

      $broker_id = $data_view['pay_applay']['broker_id'];
      //获取审核过后的状态
      $status = $this->input->post('status');
      $remark = $this->input->post('remark');
      $update_data = array(
        'status' => $status, 'remark' => $remark
      );
      $this->load->model('broker_info_model');
      //查找用户的用户组必须是已认证
      $broker_info = $this->broker_info_model->get_by_broker_id($broker_id);
      if ($broker_info['group_id'] == 2) //已认证经纪人
      {
        $this->pay_applay_model->update_by_id($update_data, $id);
        //更改经纪人用户组状态
        $update_data = array('group_id' => 3);
        $this->broker_info_model->update_by_broker_id($update_data, $broker_id);
        $data_view['modifyResult'] = 1;
      } else {
        $data_view['modifyResult'] = 0;
      }
    } else {
      //配置数组
      $where_config = array();
      $where_config['status'] = $this->pay_applay_model->status;
      $data_view['where_config'] = $where_config;
    }
    $this->load->view('pay_applay/modify', $data_view);
  }

}
