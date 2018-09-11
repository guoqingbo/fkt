<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 房客源合作控制器
 * @package     mls
 * @subpackage  Controllers
 * @category    Controllers
 * @author      fisher
 */
class Cooperate_friends extends MY_Controller
{

  /**
   * 经纪人id
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
  private $_limit = 3;

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
    $this->load->model('cooperate_friends_base_model');
    $this->load->model('agency_model');
    $this->load->model('broker_info_model');
    $this->load->model('push_func_model');
    $this->load->model('message_base_model');
    $this->load->model('push_func_model');
    $this->load->library('Verify');
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


  /*
   * 合作朋友圈
   * @param string $tbl (sell/rent)
   * @param int $rowid
   * @param int $broker_a_id
   * @param int $broker_b_id
   */
  /*public function index($type = 0){
        if($type){
            $this->_add_friend_lists();
        }else{
            $this->_friend_lists();
        }
    }*/
  public function friend_lists()
  {
    //处理个人信息
    $broker_info = $this->user_arr;
    $broker_id = $broker_info['broker_id'];
    //获取表单提交数据
    $post_param = $this->input->get(NULL, TRUE);
    //获取朋友列表
    $select_fields = array('DISTINCT(cooperate_friends.broker_id_friend)', 'cooperate_friends.id', 'broker_info.truename', 'broker_info.agency_id', 'broker_info.company_id', 'broker_info.photo');
    $this->cooperate_friends_base_model->set_select_fields($select_fields);
    $time = time();
    $cond_where = "broker_info.expiretime >= {$time} and broker_info.status = 1 and cooperate_friends.status = 1 and cooperate_friends.broker_id =" . $broker_id;
    if ($post_param['search_name'] && $post_param['search_name'] != '查找经纪人') {
      $cond_where .= " and broker_info.truename like '%" . $post_param['search_name'] . "%'";
    }
    $friends_arr = $this->cooperate_friends_base_model->get_friends_list_by_cond($cond_where, 0, 0);
    foreach ($friends_arr as $key => $vo) {
      $agency = $this->agency_model->get_by_id($vo['agency_id']);//所属总公司的信息
      $friends_arr[$key]['agency_name'] = $agency['name'];
      $company = $this->agency_model->get_by_id($vo['company_id']);//所属总公司的信息
      $friends_arr[$key]['company_name'] = $company['name'];
    }
    $data['friends_arr'] = $friends_arr;///print_r($friends_arr);exit;

    $this->result(1, '查询合作朋友圈朋友列表成功', $data);
  }

  public function add_friend_lists()
  {
    //处理个人信息
    $broker_info = $this->user_arr;
    $broker_id = $broker_info['broker_id'];
    //获取表单提交数据
    $post_param = $this->input->get(NULL, TRUE);
    if ($post_param['search_name'] && $post_param['search_name'] != '查找经纪人') {
      //获取朋友列表
      $select_fields = array('truename', 'broker_id', 'agency_id', 'company_id', 'photo');
      $this->broker_info_model->set_select_fields($select_fields);
      $time = time();
      $cond_where = "company_id > 0 and expiretime >= {$time} and status = 1 and truename like '%" . $post_param['search_name'] . "%'";
      $broker_all_info = $this->broker_info_model->get_all_by($cond_where, 0, 0);
      foreach ($broker_all_info as $key => $vo) {
        $agency = $this->agency_model->get_by_id($vo['agency_id']);//所属总公司的信息
        $broker_all_info[$key]['agency_name'] = $agency['name'];
        $company = $this->agency_model->get_by_id($vo['company_id']);//所属总公司的信息
        $broker_all_info[$key]['company_name'] = $company['name'];
        //根据好友列表获取当前关系 1已添加 2已发送请求 3未添加
        //好友信息
        $friend_info = $this->cooperate_friends_base_model->get_friend_by_broker_id($broker_id, $vo['broker_id']);
        if (is_full_array($friend_info)) {
          $broker_all_info[$key]['status'] = 1;
        } else {
          //申请信息
          $apply_info = $this->cooperate_friends_base_model->get_apply_by_broker_id($broker_id, $vo['broker_id']);
          if (is_full_array($apply_info)) {
            $broker_all_info[$key]['status'] = 2;
          } else {
            $broker_all_info[$key]['status'] = 3;
          }
        }
      }
    } else {
      $broker_all_info = array();
    }

    $data['broker_all_info'] = $broker_all_info;
    $this->result(1, '查询合作朋友圈添加朋友列表成功', $data);
  }

  //添加好友申请
  public function add_apply()
  {
    $data = array();
    $broker_info = $this->user_arr;
    $broker_id = $broker_info['broker_id'];
    $broker_id_friend = $this->input->get('broker_id_friend', TRUE);
    $add_arr = array(
      'broker_id_send' => $broker_id,
      'broker_id_receive' => $broker_id_friend,
      'infofrom' => 2,
      'createtime' => time()
    );
    $apply_info = $this->cooperate_friends_base_model->get_apply_by_broker_id($broker_id, $broker_id_friend);
    if (is_full_array($apply_info)) {
      $this->result(0, '重复申请！', $data);
    } else {
      $insert_id = $this->cooperate_friends_base_model->add_apply($add_arr);
      if ($insert_id > 0) {
        //$result = array('status'=> 1 , 'msg' => '申请成功！' , 'broker_id_friend' => $broker_id_friend);
        //成功后添加消息推送和消息提醒
        $params = array();
        $params['name'] = $broker_info['truename'];
        $msg_id = $this->message_base_model->add_message('8-51-1', $broker_id_friend, '', '/message/bulletin/', $params);
        if ($msg_id) {
          $add_arr['msg_id'] = $msg_id;
          $add_arr['apply_id'] = $insert_id;
          $msg_f_id = $this->cooperate_friends_base_model->add_friend_message($add_arr);
          $this->cooperate_friends_base_model->update_apply(array('msg_f_id' => $msg_f_id), array('id' => $insert_id));
        }
        $this->push_func_model->send(1, 15, 1, $broker_id, $broker_id_friend, array('msg_id' => $msg_id), array('broker_name' => $params['name']));
        $data['broker_id_friend'] = $broker_id_friend;
        $this->result(1, '申请成功！', $data);
      } else {
        $this->result(0, '申请失败！', $data);
      }
    }
  }

  //好友确认通过申请
  public function sure_apply()
  {
    $data = array();
    $apply_id = $this->input->get('apply_id', NULL);
    //申请详情
    $apply_info = $this->cooperate_friends_base_model->get_apply_by_id($apply_id);
    if ($apply_info['status'] == 1) {
      $this->result(0, '已添加！', $data);
    } elseif ($apply_info['status'] == 2) {
      $this->result(0, '对方已拒绝！', $data);
    } elseif ($apply_info['status'] == 3) {
      $this->result(0, '请求已过期！', $data);
    } else {
      //更新申请表状态及消息关联表状态
      $update_arr = array(
        'status' => 1,
        'updatetime' => time()
      );

      $update_result = $this->cooperate_friends_base_model->update_apply($update_arr, array('id' => $apply_id));
      //申请详情
      $apply_info = $this->cooperate_friends_base_model->get_apply_by_id($apply_id);
      //更新消息关联表的状态
      $this->cooperate_friends_base_model->update_friend_message($update_arr, array('id' => $apply_info['msg_f_id']));
      //查看对方是否也有好友申请，同步更新状态
      $apply_info_check = $this->cooperate_friends_base_model->get_apply_by_broker_id($apply_info['broker_id_receive'], $apply_info['broker_id_send']);
      if (is_full_array($apply_info_check)) {
        $this->cooperate_friends_base_model->update_apply($update_arr, array('id' => $apply_info_check['id']));
        $this->cooperate_friends_base_model->update_friend_message($update_arr, array('id' => $apply_info_check['msg_f_id']));
      }
      //修改申请表成功，将双方经纪人添加到好友表中
      if ($update_result) {
        $add_arr1 = array(
          'broker_id' => $apply_info['broker_id_send'],
          'broker_id_friend' => $apply_info['broker_id_receive'],
          'infofrom' => 2,
          'createtime' => time()
        );
        $insert_id1 = $this->cooperate_friends_base_model->add_friend($add_arr1);
        $add_arr2 = array(
          'broker_id' => $apply_info['broker_id_receive'],
          'broker_id_friend' => $apply_info['broker_id_send'],
          'infofrom' => 2,
          'createtime' => time()
        );
        $insert_id2 = $this->cooperate_friends_base_model->add_friend($add_arr2);
        //成功后添加消息推送和消息提醒
        $params = array();
        $broker_info = $this->user_arr;
        $params['name'] = $broker_info['truename'];
        $msg_id = $this->message_base_model->add_message('8-51-2', $apply_info['broker_id_send'], '', '/sell/lists_pub/friend', $params);
        $this->push_func_model->send(1, 15, 2, $apply_info['broker_id_receive'], $apply_info['broker_id_send'], array('msg_id' => $msg_id), array('broker_name' => $params['name']));
      }
      if ($insert_id1 && $insert_id2) {
        $this->result(1, '添加成功！', $data);
      } else {
        $this->result(0, '添加失败！', $data);
      }
    }
  }

  //好友拒绝申请
  public function refuse_apply()
  {
    $data = array();
    $apply_id = $this->input->get('apply_id', NULL);
    //申请详情
    $apply_info = $this->cooperate_friends_base_model->get_apply_by_id($apply_id);
    if ($apply_info['status'] == 1) {
      $this->result(0, '已添加！', $data);
    } elseif ($apply_info['status'] == 2) {
      $this->result(0, '对方已拒绝！', $data);
    } elseif ($apply_info['status'] == 3) {
      $this->result(0, '请求已过期！', $data);
    } else {
      //更新申请表状态
      $update_arr = array(
        'status' => 2,
        'updatetime' => time()
      );

      $update_result = $this->cooperate_friends_base_model->update_apply($update_arr, array('id' => $apply_id));

      //修改申请表成功，更改状态
      if ($update_result) {
        //申请详情
        $apply_info = $this->cooperate_friends_base_model->get_apply_by_id($apply_id);
        //更新消息关联表的状态
        $this->cooperate_friends_base_model->update_friend_message($update_arr, array('id' => $apply_info['msg_f_id']));
        //查看对方是否也有好友申请，同步更新状态
        $apply_info_check = $this->cooperate_friends_base_model->get_apply_by_broker_id($apply_info['broker_id_receive'], $apply_info['broker_id_send']);
        if (is_full_array($apply_info_check)) {
          $this->cooperate_friends_base_model->update_apply($update_arr, array('id' => $apply_info_check['id']));
          $this->cooperate_friends_base_model->update_friend_message($update_arr, array('id' => $apply_info_check['msg_f_id']));
        }
        //成功后添加消息推送和消息提醒
        $params = array();
        $broker_info = $this->user_arr;
        $params['name'] = $broker_info['truename'];
        $msg_id = $this->message_base_model->add_message('8-51-3', $apply_info['broker_id_send'], '', '/message/bulletin/', $params);
        $this->push_func_model->send(1, 15, 3, $apply_info['broker_id_receive'], $apply_info['broker_id_send'], array('msg_id' => $msg_id), array('broker_name' => $params['name']));
        $this->result(1, '操作成功！', $data);
      } else {
        $this->result(0, '操作失败！', $data);
      }
    }
  }

  //删除好友
  public function del_friend()
  {
    $data = array();
    $broker_id_friend = $this->input->get('broker_id_friend', NULL);
    $broker_info = $this->user_arr;
    $broker_id = $broker_info['broker_id'];
    //好友详情双方
    $friend_info = $this->cooperate_friends_base_model->get_friend_by_broker_id($broker_id, $broker_id_friend);

    //删除好友双方均删除
    $update_arr = array('status' => 2, 'updatetime' => time());

    $update_result1 = $this->cooperate_friends_base_model->update_friend($update_arr, array('id' => $friend_info[0]['id']));
    $update_result2 = $this->cooperate_friends_base_model->update_friend($update_arr, array('id' => $friend_info[1]['id']));

    //修改申请表成功，将双方经纪人添加到好友表中
    if ($update_result1 && $update_result2) {
      $this->result(1, '操作成功！', $data);
    } else {
      $this->result(0, '操作失败！', $data);
    }
  }
}

/* End of file cooperate.php */
/* Location: ./application/mls/controllers/cooperate.php */
