<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 委托出售信息管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Entrust_sell_review extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    //查看所有的模块
    $this->load->helper('page_helper');
    $this->load->model('entrust_model');
    $this->load->model('push_func_model');
    $this->load->helper('user_helper');
    $this->load->model('message_model');
    $this->load->model('push_func_model');
  }

  public function index()
  {
    $data = array();
    $data['title'] = "出售委托审核";
    //form 表单提交
    $param_array = $this->input->post(Null, true);
    if ($param_array['submit_flag'] == 'search') {
      $where = "";
      if ($param_array['realname']) {
        $where .= " and s.realname = '" . $param_array['realname'] . "'";
      }
      if ($param_array['phone']) {
        $where .= " and s.phone like '%" . $param_array['phone'] . "%'";
      }
      if ($param_array['comt_name']) {
        $where .= " and s.comt_name like '%" . $param_array['comt_name'] . "%'";
      }
      if ($param_array['starttime']) {
        $where .= " and s.ctime >=" . strtotime($param_array['starttime']);
      }
      if ($param_array['endtime']) {
        $where .= " and s.ctime <=" . strtotime($param_array['endtime'] . " 23:59:59");
      }
      if ($param_array['lprice']) {
        $where .= " and s.hprice >=" . $param_array['lprice'];
      }
      if ($param_array['hprice']) {
        $where .= " and s.hprice <=" . $param_array['hprice'];
      }
      if ($param_array['larea']) {
        $where .= " and s.area >=" . $param_array['larea'];
      }
      if ($param_array['harea']) {
        $where .= " and s.area <=" . $param_array['harea'];
      }
      if ($param_array['status']) {
        $where .= " and s.status = " . $param_array['status'];
      }
      if ($param_array['is_check']) {
        $where .= " and s.is_check = " . $param_array['is_check'];
      }

      //清除条件头尾多余的“AND”和空格
      $where = trim($where);
      $where = trim($where, "and");
      $where = trim($where);
    }
    //分页开始
    $data['sold_num'] = $this->entrust_model->get_num_by($where, $tbl = "ent_sell s");
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['sold_num'] ? ceil($data['sold_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($param_array['pg']) ? intval($param_array['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['list'] = $this->entrust_model->get_list_by_cond($where, $tbl = 'ent_sell', $data['offset'], $data['pagesize'], 's.id', 'DESC');
    $data['param_array'] = $param_array;
    $this->load->view("entrust_sell_review/index", $data);
  }

  public function edit_entrust($id)
  {
    $data['title'] = "出售委托审核";
    $result = "";
    $data['list'] = $this->entrust_model->get_list_by($id, $tbl = "ent_sell");
    $save = $this->input->post("submit_flag", true);
    if ($save == "save") {
      $sid = $this->input->post("id", true);
      $update_array = array(
        'area' => $this->input->post('area', true),
        'hprice' => $this->input->post('hprice', true),
        'realname' => $this->input->post('realname', true),
        'phone' => $this->input->post('phone', true),
        'comt_name' => $this->input->post('block_name', true),
        'comt_id' => $this->input->post('block_id', true),
        'is_check' => $this->input->post('is_check', true),
        'remark' => $this->input->post('remark', true)
      );
      if ($update_array['is_check'] == 2) {
        $update_array['updatetime'] = time();
      }
      $result = $this->entrust_model->update_status($sid, $update_array, $tbl = "ent_sell");
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
          'tbl' => 1,
          'row_id' => $sid,
          'uid' => $data['list']['uid'],
          'name' => $data['list']['realname'],
          'phone' => $data['list']['phone'],
          'status' => 2,
          'city_id' => $city_id,
          'binding_time' => time()
        );
        if ($phone_id > 0) {
          $where_cond = array(
            'id' => $phone_id
          );
          $update_result = $this->phone_info_400_model->update_data($update_arr, $where_cond);
        }

        $list = $this->entrust_model->get_list_by($sid, $tbl = "ent_sell");
        //根据区属获取范围内的经纪人，发送推送消息
        $broker_list = $this->entrust_model->get_broker_by_dist($list['dist_id']);
        if (is_full_array($broker_list)) {
          foreach ($broker_list as $key => $val) {
            /*$this->message_model->add_message('3-22',$val['broker_id'],'','/entrust_center/ent_sell',array('comt_name'=>$list['comt_name'],'hprice'=>$list['hprice']));*/
            $this->push_func_model->send(1, 9, 1, 0, $val['broker_id'], array(), array('comt_name' => $list['comt_name'], 'hprice' => $list['hprice'], 'price_danwei' => '万'), '');
          }
        }
      }
    }

    $data['result'] = $result;
    $this->load->view('entrust_sell_review/edit_entrust', $data);
  }

  public function edit_entrust_review($id)
  {
    $data['title'] = "出售委托审核";
    $result = "";
    $data['list'] = $this->entrust_model->get_list_by($id, $tbl = "ent_sell");
    $this->load->view('entrust_sell_review/edit_entrust_review', $data);
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
    echo json_encode($data);
    //$this->load->view('entrust_sell/del',$data);
  }


}
