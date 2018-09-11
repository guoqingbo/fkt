<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 委托出售信息管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Entrust_sell extends MY_Controller
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
    $data['title'] = "出售委托管理";
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
      $status = $this->input->post('status', true);
      $is_check = $this->input->post('is_check', true);

      $where = "";
      if ($id !== "") {
        $where .= " and s.id = '" . $id . "'";
      }
      if ($uid !== "") {
        $where .= " and realname like '%" . $uid . "%'";
      }
      if ($phone !== "") {
        $where .= " and s.phone like '%" . $phone . "%'";
      }
      if ($comt_name !== "") {
        $where .= " and comt_name like '%" . $comt_name . "%'";
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
      if ($is_check !== "") {
        $where .= " and s.is_check = '" . $is_check . "'";
      }

      //清除条件头尾多余的“AND”和空格
      $where = trim($where);
      $where = trim($where, "and");
      $where = trim($where);
    }
    //分页开始
    $data['sold_num'] = $this->entrust_model->get_num_by($where, $tbl = "ent_sell s");
    $data['pagesize'] = 8; //设定每一页显示的记录数
    $data['pages'] = $data['sold_num'] ? ceil($data['sold_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($pg) ? intval($pg) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['list'] = $this->entrust_model->get_list_by_cond($where, $tbl = 'ent_sell', $data['offset'], $data['pagesize'], 's.id', 'DESC');

    $data['param_array'] = array(
      'id' => $id,
      'uid' => $uid,
      'phone' => $phone,
      'comt_name' => $comt_name,
      'agency_id' => $agency_id,
      'status' => $status,
      'is_check' => $is_check,
    );
    $this->load->view("entrust_sell/index", $data);
  }

  public function entrust_grab_details($id)
  {
    $data = array();
    $data['title'] = "出售委托明细-抢拍名单";
    //form 表单提交
    $pg = $this->input->post('pg', true);
    if ($pg == "") {
      $pg = 1;
    }
    $where = "type = 1 and ent_id = " . $id;
    //分页开始
    $data['sold_num'] = $this->entrust_model->get_num_by($where, $tbl = "grab_house");
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['sold_num'] ? ceil($data['sold_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($pg) ? intval($pg) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['list'] = $this->entrust_model->get_list_by_grab($where, $tbl = 'grab_house', $data['offset'], $data['pagesize']);

    $this->load->view("entrust_sell/entrust_grab_details", $data);

  }


  public function edit_entrust($id)
  {
    $data['title'] = "出售委托分配";
    $result = "";
    $data['list'] = $this->entrust_model->get_list_by($id, $tbl = "ent_sell");
    $save = $this->input->post("submit_flag", true);
    //print_R($save);//die();
    if ($save == "save") {
      $sid = $this->input->post("id", true);
      $update_array = array(
        //'status'=>$this->input->post('status',true),
        'area' => $this->input->post('area', true),
        'hprice' => $this->input->post('hprice', true),
        'realname' => $this->input->post('realname', true),
        'phone' => $this->input->post('phone', true),
        'comt_name' => $this->input->post('block_name', true),
        'comt_id' => $this->input->post('block_id', true),
      );
      //print_R($update_array);exit;
      $result = $this->entrust_model->update_status($sid, $update_array, $tbl = "ent_sell");
    }
    $data['result'] = $result;
    $this->load->view('entrust_sell/edit_entrust', $data);
  }

  /*public function cancel_entrust($id){
      $update_array=array(
                  'status'=>0,
                  'company_id'=>"",
                  'agency_id'=>"",
                  'broker_id'=>""
        );
      $result=$this->entrust_model->update_status($id,$update_array,$tbl="ent_sell");
      $data['result']=$result;
      $this->load->view('entrust_sell/cancel_entrust',$data);
  }*/

  public function del_pop($id)
  {
    echo json_encode($id);
  }

  public function del($id)
  {
    $del_reason = $this->input->get('del_reason', TRUE);
    $update_array = array('status' => 2, 'del_reason' => $del_reason);
    $data = $this->entrust_model->update_status($id, $update_array, $tbl = "ent_sell");
    //如果下架，400号码解绑，到真空状态。
    if (1 == $data) {
      //根据房客源类型，房客源id，找到400号码数据。
      $this->load->model('phone_info_400_model');
      $where_cond = array(
        'tbl' => 1,
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


}
