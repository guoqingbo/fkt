<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 委托出租信息管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Seek_sell extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    //查看所有的模块
    $this->load->helper('page_helper');
    $this->load->model('seek_model');
    $this->load->helper('user_helper');
  }

  public function index()
  {
    $data = array();
    $data['title'] = "求购委托管理";
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
      $district = $this->input->post('district', true);
      $status = $this->input->post('status', true);
      $is_check = $this->input->post('is_check', true);
      $where = "";
      if ($id) {
        $where .= " and s.id = " . $id;
      }
      if ($uid) {
        $where .= " and realname like '%" . $uid . "%'";
      }
      if ($phone) {
        $where .= " and s.phone like '%" . $phone . "%'";
      }
      if ($district_id) {
        $where .= " and s.district_id = '" . $district_id . "'";
      }
      if ($status) {
        $where .= " and s.status = '" . $status . "'";
      }
      if ($is_check) {
        $where .= " and s.is_check = '" . $is_check . "'";
      }

      //清除条件头尾多余的“AND”和空格
      $where = trim($where);
      $where = trim($where, "and");
      $where = trim($where);


    }
    //分页开始
    $data['sold_num'] = $this->seek_model->get_num_by($where, $tbl = "seek_sell s");
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['sold_num'] ? ceil($data['sold_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($pg) ? intval($pg) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['list'] = $this->seek_model->get_list_by_cond($where, $tbl = 'seek_sell', $data['offset'], $data['pagesize'], 'ctime desc', '');
    $data['param_array'] = array(
      'id' => $id,
      'uid' => $uid,
      'phone' => $phone,
      'district_id' => $district,
      'status' => $status,
      'is_check' => $is_check,
    );
    $data['district'] = $this->seek_model->get_district();
    $this->load->view("seek_sell/index", $data);
  }

  public function entrust_grab_details($id)
  {
    $data = array();
    $data['title'] = "求购委托明细-抢拍名单";
    //form 表单提交
    $pg = $this->input->post('pg', true);
    if ($pg == "") {
      $pg = 1;
    }
    $where = "type = 3 and ent_id = " . $id;
    //分页开始
    $data['sold_num'] = $this->seek_model->get_num_by($where, $tbl = "grab_house");
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['sold_num'] ? ceil($data['sold_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($pg) ? intval($pg) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['list'] = $this->seek_model->get_list_by_grab($where, $tbl = 'grab_house', $data['offset'], $data['pagesize']);

    $this->load->view("entrust_sell/entrust_grab_details", $data);

  }

  public function entrust($id)
  {
    $data['title'] = "出租委托分配";
    $result = "";
    $data['list'] = $this->seek_model->get_list_by($id, $tbl = "seek_sell");
    $save = $this->input->post("submit_flag", true);
    if ($save == "save") {
      $sid = $this->input->post("id", true);
      $update_array = array(
        'status' => $this->input->post('status', true),
        'company_id' => $this->input->post('company_id', true),
        'agency_id' => $this->input->post('agency_id', true),
        'broker_id' => $this->input->post('broker_id', true)
      );
      $result = $this->seek_model->update_status($sid, $update_array, $tbl = "seek_sell");
    }
    $data['result'] = $result;
    $this->load->view('seek_sell/entrust', $data);
  }

  public function edit_entrust($id)
  {
    $data['title'] = "求购委托分配";
    $result = "";
    $data['list'] = $this->seek_model->get_list_by($id, $tbl = "seek_sell");
    $save = $this->input->post("submit_flag", true);
    //print_R($save);//die();
    if ($save == "save") {
      $sid = $this->input->post("id", true);
      $update_array = array(
        'phone' => $this->input->post('phone', true),
        'realname' => $this->input->post('realname', true),
        'larea' => $this->input->post('larea', true),
        'harea' => $this->input->post('harea', true),
        'lprice' => $this->input->post('lprice', true),
        'hprice' => $this->input->post('hprice', true),
        'district_id' => $this->input->post('district_id', true),
        'room' => $this->input->post('room', true),
        'hall' => $this->input->post('hall', true),
        'toilet' => $this->input->post('toilet', true)
      );
      $result = $this->seek_model->update_status($sid, $update_array, $tbl = "seek_sell");
    }
    $data['district'] = $this->seek_model->get_district();
    $data['result'] = $result;
    //print_r($data['list']);
    $this->load->view('seek_sell/edit_entrust', $data);
  }

  public function cancel_entrust($id)
  {
    $update_array = array(
      'status' => 0,
      'company_id' => "",
      'agency_id' => "",
      'broker_id' => ""
    );
    $result = $this->seek_model->update_status($id, $update_array, $tbl = "seek_sell");
    $data['result'] = $result;
    $this->load->view('seek_sell/cancel_entrust', $data);
  }

  public function del_pop($id)
  {
    echo json_encode($id);
  }

  public function del($id)
  {
    $data = array();
    $data['title'] = "出租委托下架";
    $del_reason = $this->input->get('del_reason', TRUE);
    $update_array = array('status' => 2, 'del_reason' => $del_reason);
    $data = $this->seek_model->update_status($id, $update_array, $tbl = "seek_sell");
    if (1 == $data) {
      //根据房客源类型，房客源id，找到400号码数据。
      $this->load->model('phone_info_400_model');
      $where_cond = array(
        'tbl' => 3,
        'row_id' => intval($id)
      );
      $phone_400_data = $this->phone_info_400_model->get_data_by_cond($where_cond);
      if (is_full_array($phone_400_data)) {
        $update_arr = array(
          'tbl' => 0,
          'row_id' => 0,
          'uid' => 0,
          'name' => '',
          'phone' => '',
          'status' => 3,
          'city_id' => 0,
          'binding_time' => 0,
          'unbundling_time' => time(),
          'over_time' => time() + 3600 * 24 * 30
        );
        $phone_400_id = $phone_400_data[0]['id'];
        $where_cond = array(
          'id' => $phone_400_id
        );
        $update_reuslt = $this->phone_info_400_model->update_data($update_arr, $where_cond);
      }
    }
    echo json_encode($data);
  }

  public function get_agency()
  {
    $company_name = $this->input->get("name", true);
    $result = $this->seek_model->get_agency($company_name);
    echo json_encode($result);
  }

  public function get_broker()
  {
    $agency_id = $this->input->get("agency_id", true);
    $result = $this->seek_model->get_broker($agency_id);
    echo json_encode($result);
  }


}

