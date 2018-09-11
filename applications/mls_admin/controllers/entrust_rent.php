<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 委托出租信息管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Entrust_rent extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    //查看所有的模块
    $this->load->helper('page_helper');
    $this->load->model('entrust_model');
    $this->load->helper('user_helper');
  }

  public function index()
  {
    $data = array();
    $data['title'] = "出租委托管理";
    //form 表单提交
    $submit_flag = $this->input->post('submit_flag', true);
    $pg = $this->input->post('pg', true);
    if ($pg == "") {
      $pg = 1;
    }
    if ($submit_flag == "search") {
      $id = $this->input->post('id', true);
      $uid = $this->input->post('uid', true);
      $phone = $this->input->post('phone', true);
      $comt_name = $this->input->post('comt_name', true);
      $company_id = $this->input->post('company_id', true);
      $agency_id = $this->input->post('agency_id', true);
      $status = $this->input->post('status', true);

      $where = "";
      if ($id !== "") {
        $where .= " and s.id = " . $id;
      }
      if ($uid !== "") {
        $where .= " and uid = '" . $uid . "'";
      }
      if ($phone !== "") {
        $where .= " and s.phone = '" . $phone . "'";
      }
      if ($comt_name !== "") {
        $where .= " and comt_name = '" . $comt_name . "'";
      }
      if ($company_id !== "") {
        $where .= " and  s.company_id = '" . $company_id . "'";
      }
      if ($agency_id !== "") {
        $where .= " and  s.agency_id = '" . $agency_id . "'";
      }
      if ($status !== "") {
        $where .= " and s.status = '" . $status . "'";
      }

      //清除条件头尾多余的“AND”和空格
      $where = trim($where);
      $where = trim($where, "and");
      $where = trim($where);


    }
    //分页开始
    $data['sold_num'] = $this->entrust_model->get_num_by($where, $tbl = "ent_rent s");
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['sold_num'] ? ceil($data['sold_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($pg) ? intval($pg) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['list'] = $this->entrust_model->get_list_by_cond($where, $tbl = 'ent_rent', $data['offset'], $data['pagesize'], 'status', 'ASC');

    $data['param_array'] = array(
      'id' => $id,
      'uid' => $uid,
      'phone' => $phone,
      'comt_name' => $comt_name,
      'company_id' => $company_id,
      'agency_id' => $agency_id,
      'status' => $status
    );
    $this->load->view("entrust_rent/index", $data);
  }

  public function entrust($id)
  {
    $data['title'] = "出租委托分配";
    $result = "";
    $data['list'] = $this->entrust_model->get_list_by($id, $tbl = "ent_rent");
    $save = $this->input->post("submit_flag", true);
    if ($save == "save") {
      $sid = $this->input->post("id", true);
      $update_array = array(
        'status' => $this->input->post('status', true),
        'company_id' => $this->input->post('company_id', true),
        'agency_id' => $this->input->post('agency_id', true),
        'broker_id' => $this->input->post('broker_id', true)
      );
      $result = $this->entrust_model->update_status($sid, $update_array, $tbl = "ent_rent");
    }
    $data['result'] = $result;
    $this->load->view('entrust_rent/entrust', $data);
  }

  public function edit_entrust($id)
  {
    $data['title'] = "出租委托分配";
    $result = "";
    $data['list'] = $this->entrust_model->get_list_by($id, $tbl = "ent_rent");
    $save = $this->input->post("submit_flag", true);
    print_R($save);//die();
    if ($save == "save") {
      $sid = $this->input->post("id", true);
      $update_array = array(
        'status' => $this->input->post('status', true),
        'company_id' => $this->input->post('company_id', true),
        'agency_id' => $this->input->post('agency_id', true),
        'broker_id' => $this->input->post('broker_id', true)
      );
      $result = $this->entrust_model->update_status($sid, $update_array, $tbl = "ent_rent");
    }
    $data['result'] = $result;
    $this->load->view('entrust_rent/edit_entrust', $data);
  }

  public function cancel_entrust($id)
  {
    $update_array = array(
      'status' => 0,
      'company_id' => "",
      'agency_id' => "",
      'broker_id' => ""
    );
    $result = $this->entrust_model->update_status($id, $update_array, $tbl = "ent_rent");
    $data['result'] = $result;
    $this->load->view('entrust_rent/cancel_entrust', $data);
  }

  public function del($id)
  {
    $data = array();
    $data['title'] = "出租委托删除";
    $data['result'] = $this->entrust_model->del_by($id, $tbl = "ent_rent");
    $this->load->view('entrust_rent/del', $data);
  }

  public function get_agency()
  {
    $company_name = $this->input->get("name", true);
    $result = $this->entrust_model->get_agency($company_name);
    echo json_encode($result);
  }

  public function get_broker()
  {
    $agency_id = $this->input->get("agency_id", true);
    $result = $this->entrust_model->get_broker($agency_id);
    echo json_encode($result);
  }


}

