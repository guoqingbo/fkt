<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 城市 Class
 *
 * 城市控制器
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author
 */
class Message extends MY_Controller
{
  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'sh';


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
  private $_limit = 20;

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
    $this->load->helper('page_helper');
    $this->load->model('message_model');//消息、公告模型类
    $this->load->library('form_validation');//表单验证
    $this->load->model('company_notice_model');
    $this->load->model('company_employee_model');
    $this->load->model('cooperate_friends_base_model');
  }

  /**
   * 系统消息首页
   * @access public
   * @return json
   */
  public function smessage_index()
  {
    $data = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $company_id = intval($broker_info['company_id']);
    $cond_where1 = 'broker_id = ' . $broker_id . ' AND (type = 1 or type = 2)';
    $cond_where2 = 'broker_id = ' . $broker_id . ' AND type = 3';
    $cond_where3 = 'broker_id = ' . $broker_id . ' AND type = 4';
    $cond_where4 = 'broker_id = ' . $broker_id . ' AND (type = 5 or type = 7)';
    $cond_where5 = 'broker_id = ' . $broker_id . ' AND (type = 6 or type = 8)';
    $cond_where6 = 'company_id = ' . $company_id;
    //排序字段
    $roomorder = 3;
    $order_arr = $this->_get_orderby_arr($roomorder);

    $cooperate_unread = $this->message_model->get_count_by_cond($cond_where1 . ' AND is_read = 0');
    $marketing_unread = $this->message_model->get_count_by_cond($cond_where2 . ' AND is_read = 0');
    $newhouse_unread = $this->message_model->get_count_by_cond($cond_where3 . ' AND is_read = 0');
    $notice_unread = $this->message_model->get_count_by_cond($cond_where4 . ' AND is_read = 0');
    $system_unread = $this->message_model->get_count_by_cond($cond_where5 . ' AND is_read = 0');
    $company_notice_count = $this->company_notice_model->get_count_by_cond($cond_where6);
    $company_notice_read = $this->message_model->get_count_by_cond_company_notice($cond_where6 . ' AND is_read = 1 AND company_notice_broker.broker_id = ' . $broker_id);

    $list1 = $this->message_model->get_row_by_cond($cond_where1, 0, 1);
    $list2 = $this->message_model->get_row_by_cond($cond_where2, 0, 1);
    $list3 = $this->message_model->get_row_by_cond($cond_where3, 0, 1);
    $list4 = $this->message_model->get_row_by_cond($cond_where4, 0, 1);
    $list5 = $this->message_model->get_row_by_cond($cond_where5, 0, 1);
    $list6 = $this->company_notice_model->get_company_notice_by($cond_where6, 0, 1);

    if (is_full_array($list1)) {
      $list1 = (array)$list1[0];
      $data['center']['cooperate_center'] = array(
        'un_read' => $cooperate_unread,
        'title' => $list1['title'],
        'dataline' => $list1['createtime'],
        'createtime' => $this->formate_time($list1['createtime'])
      );
    }
    if (is_full_array($list2)) {
      $list2 = (array)$list2[0];
      $data['center']['marketing_center'] = array(
        'un_read' => $marketing_unread,
        'title' => $list2['title'],
        'dataline' => $list2['createtime'],
        'createtime' => $this->formate_time($list2['createtime'])
      );
    }
    if (is_full_array($list3)) {
      $list3 = (array)$list3[0];
      $data['center']['newhouse_center'] = array(
        'un_read' => $newhouse_unread,
        'title' => $list3['title'],
        'dataline' => $list3['createtime'],
        'createtime' => $this->formate_time($list3['createtime'])
      );
    }
    if (is_full_array($list4)) {
      $list4 = (array)$list4[0];
      $data['center']['notice_center'] = array(
        'un_read' => $notice_unread,
        'title' => $list4['title'],
        'dataline' => $list4['createtime'],
        'createtime' => $this->formate_time($list4['createtime'])
      );
    }
    if (is_full_array($list5)) {
      $list5 = (array)$list5[0];
      $data['center']['system_center'] = array(
        'un_read' => $system_unread,
        'title' => $list5['title'],
        'dataline' => $list5['createtime'],
        'createtime' => $this->formate_time($list5['createtime'])
      );
    }
    if (is_full_array($list6)) {
      $list6 = (array)$list6[0];
      $data['center']['company_notice'] = array(
        'un_read' => ($company_notice_count - $company_notice_read),
        'title' => $list6['title'],
        'dataline' => $list6['createtime'],
        'createtime' => $this->formate_time($list6['createtime'])
      );
    }

    $this->result(1, '查系统消息首页成功', $data);
  }

  /**
   * 系统消息new
   * @access public
   * @return json
   */
  public function smessage_new()
  {
    $page = $this->input->get('page');
    $type = $this->input->get('type');
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //模板使用数据
    $data = array();
    //post参数
    $post_param = $this->input->post(NULL, TRUE);

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 20;
    $this->_limit = $pagesize;
    $this->_init_pagination($page, $this->_limit);

    //查询条件
    //$cond_where = array('broker_id'=>$broker_id);
    switch ($type) {
      case 1:
        $msg = '获取合作中心消息成功';
        $cond_where = 'broker_id = ' . $broker_id . ' AND (type = 1 or type = 2)';
        break;
      case 2:
        $msg = '获取营销中心消息成功';
        $cond_where = 'broker_id = ' . $broker_id . ' AND type = 3';
        break;
      case 3:
        $msg = '获取新房中心消息成功';
        $cond_where = 'broker_id = ' . $broker_id . ' AND type = 4';
        break;
      case 4:
        $msg = '获取公告通知消息成功';
        $cond_where = 'broker_id = ' . $broker_id . ' AND (type = 5 or type = 7)';
        break;
      case 5:
        $msg = '获取系统消息成功';
        $cond_where = 'broker_id = ' . $broker_id . ' AND (type = 6 or type = 8)';
        break;
    }

    //排序字段
    $roomorder = 3;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $this->_total_count = $this->message_model->get_count_by_cond($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->message_model->get_row_by_cond($cond_where, $this->_offset, $this->_limit);
    if (is_full_array($list)) {
      //数据重构
      $list2 = array();
      foreach ($list as $k => $v) {
        $apply_message = array();
        $a = array();
        $v = (array)$v;
        $a['message_id'] = $v['msg_id'];
        $a['title'] = $v['title'];
        $a['message'] = $v['message'];
        $a['from'] = $v['from'];
        $a['type'] = $v['type'];
        $a['is_read'] = $v['is_read'];
        //时间字段处理（区分昨天、今天和其它时间段）
        $a['createtime'] = $this->formate_time($v['createtime']);

        //查看是否为好友申请消息
        if ($v['type'] == 8) {
          $apply_message = $this->cooperate_friends_base_model->get_message_by_id($v['msg_id']);
          if (is_full_array($apply_message)) {
            $a['status'] = $apply_message['status'];
            $a['apply_id'] = $apply_message['apply_id'];
          }
        }
        $list2['list'][$k] = $a;
      }
      $this->result(1, $msg, $list2);
    } else {
      $this->result(1, '暂无消息');
    }
  }

  /**
   * 消息详情页
   *
   * @access  public
   * @return  json
   */
  public function details_new()
  {
    $id = $this->input->post('id', TRUE);
    $data['is_read'] = '1';
    $result = $this->message_model->get_result($id);
    if (!empty($result[0])) {
      $this->message_model->message_broker_update($data, 'msg_id = ' . $id);
      $result[0] = (array)$result[0];
      $res['id'] = $result[0]['id'];
      $res['title'] = $result[0]['title'];
      $res['message'] = $result[0]['message'];
      $res['type'] = $result[0]['type'];
      $url = $result[0]['url'];
      $res['url'] = $url;
      //类型type 1合作中心 2交易评价 3营销中心 4新房分销 5最新资讯 6采集中心 7任务分配 8系统消息
      if ($result[0]['type'] == 1) {
        if (strpos($url, 'accept')) {
          $res['mothod'] = 1;//接收
          $cid = str_replace('/cooperate/accept_order_list/?cid=', '', $url);
          if (is_numeric($cid)) {
            $res['cid'] = $cid;
          } else {
            $res['cid'] = 0;
          }
        } elseif (strpos($url, 'send')) {
          $res['mothod'] = 2;//发送
          $cid = str_replace('/cooperate/send_order_list/?cid=', '', $url);
          if (is_numeric($cid)) {
            $res['cid'] = $cid;
          } else {
            $res['cid'] = 0;
          }
        }
      } elseif ($result[0]['type'] == 2) {
        $res['mothod'] = 3;//评价
      } elseif ($result[0]['type'] == 4) {
        $res['cid'] = $url;
      } elseif ($result[0]['type'] == 5) {
        $res['url'] = $this->config->item('base_url') . $url;
      } elseif ($result[0]['type'] == 8) {
        $apply_message = $this->cooperate_friends_base_model->get_message_by_id($id);
        if (is_full_array($apply_message)) {
          $res['status'] = $apply_message['status'];
          $res['apply_id'] = $apply_message['apply_id'];
        }
      }
      $res['createtime'] = date('m-d H:i', $result[0]['createtime']);
      $this->result(1, '获得消息详情成功', $res);
    } else {
      $this->result(0, '获得消息详情失败');
    }
  }

  /**
   * 系统消息
   * @access public
   * @return json
   */
  public function smessage($page = 1)
  {
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //模板使用数据
    $data = array();
    //post参数
    $post_param = $this->input->post(NULL, TRUE);

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 20;
    $this->_limit = $pagesize;
    $this->_init_pagination($page, $this->_limit);

    //查询条件
    //$cond_where = array('broker_id'=>$broker_id);
    $cond_where = 'broker_id = ' . $broker_id . ' AND type <> 0';
    //排序字段
    $roomorder = 3;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $this->_total_count = $this->message_model->get_count_by_cond($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->message_model->get_row_by_cond($cond_where, $this->_offset, $this->_limit);
    if (!empty($list)) {
      //数据重构
      $list2 = array();
      foreach ($list as $k => $v) {
        $v = (array)$v;
        $a = array();
        $a['message_id'] = $v['msg_id'];
        $a['title'] = $v['title'];
        $a['message'] = $v['message'];
        $a['from'] = $v['from'];
        if ($v['type'] == 1) {
          $a['type'] = 2;
        } elseif ($v['type'] == 8) {
          $a['type'] = 1;
        } elseif ($v['type'] == 4) {
          $a['type'] = 3;
        } elseif ($v['type'] == 5) {
          $a['type'] = 4;
        } else {
          $a['type'] = $v['type'];
        }
        $a['is_read'] = $v['is_read'];
        //时间字段处理（区分昨天、今天和其它时间段）
        if ($v['createtime'] > strtotime(date('Y-m-d'))) {
          //今天
          $a['createtime'] = '今天' . date('H:i', $v['createtime']);
        } else if ($v['createtime'] < strtotime(date('Y-m-d')) && $v['createtime'] > strtotime(date('Y-m-d', strtotime('-1 day')))) {
          //昨天
          $a['createtime'] = '昨天' . date('H:i', $v['createtime']);
        } else {
          $a['createtime'] = date('m-d H:i', $v['createtime']);
        }
        //查看是否为好友申请消息
        if ($v['type'] == 8) {
          $apply_message = $this->cooperate_friends_base_model->get_message_by_id($v['msg_id']);
          if (is_full_array($apply_message)) {
            $a['status'] = $apply_message['status'];
            $a['apply_id'] = $apply_message['apply_id'];
          }
        }
        $list2[] = $a;
      }
      $this->result(1, '查系统消息成功', $list2);
    } else {
      $this->result(1, '暂无消息');
    }

  }

  /**
   * 消息详情页
   *
   * @access  public
   * @return  json
   */
  public function details()
  {
    $id = $this->input->post('id', TRUE);
    $data['is_read'] = '1';
    $result = $this->message_model->get_result($id);
    if (!empty($result[0])) {
      $this->message_model->message_broker_update($data, 'msg_id = ' . $id);
      $result[0] = (array)$result[0];
      $res['id'] = $result[0]['id'];
      $res['title'] = $result[0]['title'];
      $res['message'] = $result[0]['message'];
      $res['type'] = $result[0]['type'];
      $url = $result[0]['url'];
      //类型type 1最新资讯2合作中心3新房分销4任务分配5采集 中心6交易评价7营销中心
      if ($result[0]['type'] == 2) {
        if (strpos($url, 'accept')) {
          $res['mothod'] = 1;//接收
          $cid = str_replace('/cooperate/accept_order_list/?cid=', '', $url);
          if (is_numeric($cid)) {
            $res['cid'] = $cid;
          } else {
            $res['cid'] = 0;
          }
        } elseif (strpos($url, 'send')) {
          $res['mothod'] = 2;//发送
          $cid = str_replace('/cooperate/send_order_list/?cid=', '', $url);
          if (is_numeric($cid)) {
            $res['cid'] = $cid;
          } else {
            $res['cid'] = 0;
          }
        } elseif (strpos($url, 'my_evaluate')) {
          $res['mothod'] = 3;//评价
        }
      } elseif ($result[0]['type'] == 3) {
        $res['cid'] = $url;
      }
      $res['createtime'] = date('m-d H:i', $result[0]['createtime']);
      $this->result(1, '获得消息详情成功', $res);
    } else {
      $this->result(0, '获得消息详情失败');
    }
  }

  /**
   * 公司公告
   * @access public
   * @return void
   */
  public function notice($page = 1)
  {
    //遗留 判断是否登录
    $this->load->model('broker_model');
    $this->load->model('broker_info_model');
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $company_id = intval($broker_info['company_id']);

    //模板使用数据
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //print_R($post_param);exit;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $this->_init_pagination($page, $this->_limit);

    //查询条件
    $cond_where = array('company_id' => $company_id);

    //符合条件的总行数
    $this->_total_count = $this->company_notice_model->get_count_by_cond($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->company_notice_model->get_company_notice_by($cond_where, $this->_offset, $this->_limit);
    $list1 = array();
    $list2 = array();
    if (!empty($list)) {
      foreach ($list as $k => $vo) {
        $list1[$k]['id'] = $vo['id'];
        $list1[$k]['title'] = $vo['title'];
        $vo['contents'] = preg_replace("/\s/", '', trim(strip_tags($vo['contents'])));
        $list1[$k]['contents'] = mb_substr($vo['contents'], 0, 100, 'utf-8');
        $broker_info = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
        $list1[$k]['broker_name'] = $broker_info['truename'];
        if (mb_strlen($vo['contents']) > 100) {
          $list1[$k]['contents'] .= '...';
        }
        $notice_broker = $this->company_notice_model->get_company_notice_broker_by(array('n_id' => $vo['id'], 'broker_id' => $vo['broker_id']));
        if (is_full_array($notice_broker)) {
          $list1[$k]['is_read'] = $notice_broker['is_read'];
        } else {
          $list1[$k]['is_read'] = 0;
        }
        $list1[$k]['url'] = MLS_MOBILE_URL . "/message/company_notice_detail/" . $vo['id'];
        $list1[$k]['createtime'] = date('Y-m-d H:i:s', $vo['createtime']);
      }
      $list2['list'] = $list1;
      $this->result(1, '查公司公告消息成功', $list2);
    } else {
      $this->result(1, '暂无消息');
    }
  }

  /**
   * 公司公告详情company_notice
   * @access  public
   * @return  json
   */
  public function company_notice_detail($id = 0)
  {
    $this->load->model('broker_model');
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    if (!$id) {
      $id = $this->input->post('id', TRUE);
    }
    $detail = $this->company_notice_model->get_detail_by_id($id);
    $broker_info = $this->company_employee_model->get_broker_by_id($detail['broker_id']);
    $detail['broker_name'] = $broker_info['truename'];
    $this->company_notice_model->company_notice_broker_del(array('n_id' => $detail['id'], 'broker_id' => $broker_id));
    $this->company_notice_model->add_notice_broker(array('n_id' => $detail['id'], 'broker_id' => $broker_id, 'is_read' => 1, 'createtime' => time()));

    $detail['createtime'] = date('Y-m-d H:i:s', $detail['createtime']);
    $detail['is_read'] = 1;
    //print_r($detail);die;
    $this->load->view('notice/company_notice_detail', $detail);

  }


  /**
   * 联系人列表
   *
   * @access  public
   * @return  json
   */
  public function contacts()
  {
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $this->load->model('api_broker_model');
    $this->load->model('cooperate_model');
    //同事信息
    $brokerinfo = array();
    $agency_id = intval($broker_info['agency_id']);
    if ($agency_id > 0) {
      $brokerinfo = $this->api_broker_model->get_brokers_agency_id($agency_id);
    }
    $colleague = array();
    //去除本人信息
    if (is_array($brokerinfo) && !empty($brokerinfo)) {
      foreach ($brokerinfo as $k => $v) {
        if ($v['broker_id'] != $broker_id) {
          $colleague[] = $v;
        }
      }
    }
    $colleague2 = array();
    //同事信息数据重构
    foreach ($colleague as $k => $v) {
      $a['broker_id'] = $v['broker_id'];
      $a['broker_name'] = $v['truename'];
      $a['phone'] = $v['phone'];
      $a['photo'] = $v['photo'];
      if ('1' == $v['package_id']) {
        $a['remark'] = '店长';
      } else {
        $a['remark'] = '普通经纪人';
      }
      $colleague2[] = $a;
    }
    $colleague3 = array('name' => '我的同事（' . $broker_info['agency_name'] . '）', 'list' => $colleague2);
    //合作伙伴信息
    $partner_ids = $this->cooperate_model->get_cooperate_partner($broker_id);
    $partner_data = array();
    foreach ($partner_ids as $k => $v) {
      $partner_data[] = $this->api_broker_model->get_baseinfo_by_broker_id(intval($v));
    }
    $partner_data2 = array();
    //合作伙伴数据重构
    if (!empty($partner_data)) {
      foreach ($partner_data as $k => $v) {
        $b['broker_id'] = $v['broker_id'];
        $b['broker_name'] = $v['truename'];
        $b['phone'] = $v['phone'];
        $b['photo'] = $v['photo'];
        $b['remark'] = $v['agency_name'];
        $partner_data2[] = $b;
      }
    }
    $partner_data3 = array('name' => '我的合作伙伴', 'list' => $partner_data2);
    $data = array($colleague3, $partner_data3);
    $this->result(1, '获得联系人成功', $data);
  }

  /**
   * 设为已读
   *
   * @access  public
   * @return  json
   */
  public function read()
  {
    $data['is_read'] = '1';
    $ids = mb_substr($this->input->get('str', TRUE), 1);
    $result = $this->message_model->message_broker_update($data, 'msg_id in (' . $ids . ')');
    if ($result > 0) {
      $res['result'] = 'ok';
    }
    echo json_encode($res);

  }

  /**
   * 删除
   * @access  public
   * @return  json
   */
  public function del()
  {
    $ids = $this->input->get('id', TRUE);
    $broker_id = $this->user_arr['broker_id'];
    $result = $this->message_model->message_broker_del('msg_id in (' . $ids . ') and broker_id =' . $broker_id);
    if ($result > 0) {
      $this->result(1, '删除成功');
    } else {
      $this->result(0, '删除失败');
    }
  }

  /**
   * 获取排序参数
   *
   * @access private
   * @param  int $order_val
   * @return void
   */
  private function _get_orderby_arr($order_val)
  {
    $arr_order = array();

    switch ($order_val) {
      case 1:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
        break;
      case 2:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'ASC';
        break;
      case 3:
        $arr_order['order_key'] = 'createtime';
        $arr_order['order_by'] = 'DESC';
        break;
      case 4:
        $arr_order['order_key'] = 'createtime';
        $arr_order['order_by'] = 'ASC';
        break;
      default:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
    }

    return $arr_order;
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
   * 格式花时间
   * @param int $data_line 时间戳
   */
  private function formate_time($data_line)
  {
    if ($data_line > strtotime(date('Y-m-d'))) {
      //今天
      $formate_time = '今天' . date('H:i', $data_line);
    } else if ($data_line < strtotime(date('Y-m-d')) && $data_line > strtotime(date('Y-m-d', strtotime('-1 day')))) {
      //昨天
      $formate_time = '昨天' . date('H:i', $data_line);
    } else {
      $formate_time = date('m-d H:i', $data_line);
    }
    return $formate_time;
  }
}


/* End of file message.php */
/* Location: ./application/mls/controllers/message.php */
