<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 委托出售信息管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Seek_rent_review extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    //查看所有的模块
    $this->load->helper('page_helper');
    $this->load->model('seek_model');
    $this->load->model('district_model');
    $this->load->helper('user_helper');
    $this->load->model('push_func_model');
    $this->load->model('message_model');
  }

  public function index()
  {
    $data = array();
    $data['title'] = "求租委托审核";
    //form 表单提交
    $submit_flag = $this->input->post('submit_flag', true);
    $pg = $this->input->post('pg', true);
    if ($pg == "") {
      $pg = 1;
    }
    /*if($submit_flag=="search"){
    $id=$this->input->post('id',true);
    $uid=$this->input->post('uid',true);
    $phone=$this->input->post('phone',true);
    $comt_name=$this->input->post('comt_name',true);
    //$company_name=$this->input->post('company_name',true);
    //$company_id=$this->input->post('company_id',true);
    //$agency_id=$this->input->post('agency_id',true);
    $status=$this->input->post('status',true);
    $is_check=$this->input->post('is_check',true);

    $where="";
    if($id!=="")
    {
        $where .= " and s.id = '".$id."'";
    }
    if($uid!=="")
    {
        $where .=" and realname like '%".$uid."%'";
    }
    if($phone!=="")
    {
        $where .=" and s.phone like '%".$phone."%'";
    }
    if($comt_name!=="")
    {
        $where .=" and comt_name like '%".$comt_name."%'";
    }
    if($company_id!=="")
    {
        $where .=" and  s.company_id = '".$company_id."'";
    }
    if($agency_id!=="")
    {
        $where .=" and  s.agency_id = '".$agency_id."'";
    }
    if($status!=="")
    {
        $where .=" and s.status = '".$status."'";
    }
    if($is_check!=="")
    {
        $where .=" and s.is_check = '".$is_check."'";
    }

    //清除条件头尾多余的“AND”和空格
    $where = trim($where);
    $where = trim($where, "and");
    $where = trim($where);


  }*/
    //分页开始
    $data['sold_num'] = $this->seek_model->get_num_by($where, $tbl = "seek_rent s");
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['sold_num'] ? ceil($data['sold_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($pg) ? intval($pg) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['list'] = $this->seek_model->get_list_by_cond($where, $tbl = 'seek_rent', $data['offset'], $data['pagesize']);


    /*$data['param_array']=array(
                'id'=>$id,
                'uid'=>$uid,
                'phone'=>$phone,
                'comt_name'=>$comt_name,
                //'company_id'=>$company_id,
                //'company_name'=>$company_name,
                'agency_id'=>$agency_id,
                'status'=>$status,
                'is_check'=>$is_check,
    );*/
    $this->load->view("seek_rent_review/index", $data);
  }

  public function edit_entrust($id)
  {
    $data['title'] = "求租委托审核";
    $result = "";
    $data['list'] = $this->seek_model->get_list_by($id, $tbl = "seek_rent");
    $save = $this->input->post("submit_flag", true);
    $data['district'] = $this->district_model->get_district();
    //print_R($save);//die();
    if ($save == "save") {
      $sid = $this->input->post("id", true);
      $update_array = array(
        //'status'=>$this->input->post('status',true),
        'larea' => $this->input->post('larea', true),
        'harea' => $this->input->post('harea', true),
        'hprice' => $this->input->post('hprice', true),
        'lprice' => $this->input->post('lprice', true),
        'room' => $this->input->post('room', true),
        'hall' => $this->input->post('hall', true),
        'toilet' => $this->input->post('toilet', true),
        'realname' => $this->input->post('realname', true),
        'phone' => $this->input->post('phone', true),
        'district_id' => $this->input->post('district_id', true),
        'is_check' => $this->input->post('is_check', true),
        'remark' => $this->input->post('remark', true)
      );
      if ($update_array['is_check'] == 2) {
        $update_array['updatetime'] = time();
      }
      $result = $this->seek_model->update_status($sid, $update_array, $tbl = "seek_rent");
      if ('1' == $data['list']['status'] && '2' == $update_array['is_check'] && 1 == $result) {
        //审核通过，随机绑定400电话
        $this->load->model('phone_info_400_model');
        //在随机池中随机获得一条400电话数据
        $phone_400_data = $this->phone_info_400_model->get_one_by_num_group(100000, 999999);
        if (is_full_array($phone_400_data)) {
          $phone_id = intval($phone_400_data['id']);
        }

        $city_id = 0;
        //城市
        $city_spell = $_SESSION['esfdatacenter']['city'];
        $this->load->model('city_model');
        if (!empty($city_spell)) {
          $city_data = $this->city_model->get_city_by_spell($city_spell);
          if (is_full_array($city_data)) {
            $city_id = $city_data['id'];
          }
        }

        $update_arr = array(
          'tbl' => 4,
          'row_id' => $sid,
          'uid' => $data['list']['uid'],
          'name' => $data['list']['realname'],
          'phone' => $data['list']['phone'],
          'status' => 2,
          'city_id' => $city_id,
          'binding_time' => time()
        );
        if (isset($phone_id) && $phone_id > 0) {
          $where_cond = array(
            'id' => $phone_id
          );
          $update_result = $this->phone_info_400_model->update_data($update_arr, $where_cond);
        }

        $list = $this->seek_model->get_list_by($sid, $tbl = "seek_rent");
        $district = $this->district_model->get_distname_by_id($list['district_id']);
        //根据区属获取范围内的经纪人，发送推送消息
        $broker_list = $this->seek_model->get_broker_by_dist($list['district_id']);
        if (is_full_array($broker_list)) {
          foreach ($broker_list as $key => $val) {
            //$this->message_model->add_message('3-25',$val['broker_id'],'','/customer_demand/seek_rent',array('district'=>$district,'lprice'=>$list['lprice'] , 'hprice'=>$list['hprice']));
            $this->push_func_model->send(1, 11, 1, 0, $val['broker_id'], array(), array('district' => $district, 'lprice' => $list['lprice'], 'hprice' => $list['hprice'], 'price_danwei' => '元/月'), '');
          }
        }
      }
    }
    $data['result'] = $result;
    $this->load->view('seek_rent_review/edit_entrust', $data);
  }

  public function edit_entrust_review($id)
  {
    $data['title'] = "求租委托审核";
    $result = "";
    $data['list'] = $this->seek_model->get_list_by($id, $tbl = "seek_rent");
    $data['district'] = $this->district_model->get_district();
    $this->load->view('seek_rent_review/edit_entrust_review', $data);
  }

  /*public function cancel_entrust($id){
      $update_array=array(
                  'status'=>0,
                  'company_id'=>"",
                  'agency_id'=>"",
                  'broker_id'=>""
        );
      $result=$this->seek_model->update_status($id,$update_array,$tbl="ent_sell");
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
    $data = $this->seek_model->update_status($id, $update_array, $tbl = "ent_sell");
    echo json_encode($data);
    //$this->load->view('entrust_sell/del',$data);
  }


}
