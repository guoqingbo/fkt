<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 后台发布消息管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      kang
 */
class Issue_msg extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->helper('user_helper');
    $this->load->model('issue_msg_model');
    $this->load->model('message_model');
  }

  /**
   * 消息管理首页
   */
  public function index()
  {
    $data['title'] = "后台发布消息管理";
    $data['conf_where'] = 'index';

    $data['where_cond'] = array();
    date_default_timezone_set('PRC');
    if ($this->input->post('start_time') && $this->input->post('end_time')) {
      $start_time = strtotime($this->input->post('start_time') . " 00:00");
      $end_time = strtotime($this->input->post('end_time') . " 23:59");
      if ($start_time > $end_time) {
        echo "<script>alert('您查询的开始时间不能大于结束时间！');location.href='" . MLS_ADMIN_URL . "/issue_msg/index';</script>";
      }
      if ($start_time && $end_time) {
        $data['where_cond'] = array('createtime >=' => $start_time, "createtime <=" => $end_time, 'type' => 5);
      }
    } else {
      $data['where_cond'] = array('type' => 5);   //查询出from为0的数据即可
    }

    //分页开始
    $data['issue_msg_num'] = $this->issue_msg_model->get_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['issue_msg_num'] ? ceil($data['issue_msg_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['issue_msg'] = $this->issue_msg_model->get_issue_msg($data['where_cond'], $data['offset'], $data['pagesize']);
    $this->load->view('issue_msg/index', $data);
  }

  /**
   * 发布新消息
   */
  public function issue()
  {
    $data['title'] = '发布新消息';
    $data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');

    if ('add' == $submit_flag) {
      $url_type = intval($this->input->post('url_type'));
      $url = trim($this->input->post('url' . $url_type));
      $paramArray = array(
        'title' => trim($this->input->post('title')),
        'message' => trim($this->input->post('message')),
        'url' => $url,
        'type' => 5,
        'type_list' => 38,
        'createtime' => time()
      );
      if (!empty($paramArray['title']) && !empty($paramArray['message'])) {
        $addResult = $this->message_model->add($paramArray);//  添加消息数据到message表
        $this->message_model->add_pop($paramArray);//  添加消息数据到message表
        $user_group = $this->input->post('user_group');
        if ($user_group == 2) {
          $where = array('group_id' => '2');
        } elseif ($user_group == 3) {
          $where = array('group_id' => '1');
        }
        $rows = $this->issue_msg_model->get_all_broker($where);   //查询出所有经纪人的broker_id
        foreach ($rows as $key => $row) {
          $params = array(
            'broker_id' => trim($row['broker_id']),
            'msg_id' => $addResult,
            'is_read' => 0,
            'createtime' => time(),
            'updatetime' => time()
          );
          $RS = $this->message_model->add_message_broker($params); //添加数据到message_broker
        }
      } else {
        $data['issue_msg_error'] = '标题/消息内容';
      }
    }
    $arr_url_type = $this->issue_msg_model->arr_url_type();
    $data['arr_url_type'] = $arr_url_type;
    $data['addResult'] = $addResult;
    $this->load->view('issue_msg/issue', $data);
  }

  /**
   * 修改消息
   */
  public function modify($id)
  {
    $data['title'] = '修改消息';
    $data['conf_where'] = "index";
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $issue_msgData = $this->issue_msg_model->getinfo_byid($id);
      $data['issue_msg'] = $issue_msgData[0];
    }
    if ('modify' == $submit_flag) {
      $url_type = intval($this->input->post('url_type'));
      $url = trim($this->input->post('url' . $url_type));
      $paramArray = array(
        'title' => trim($this->input->post('title')),
        'message' => trim($this->input->post('message')),
        'url' => $url
      );
      if (!empty($paramArray['title']) && !empty($paramArray['message'])) {
        $modifyResult = $this->issue_msg_model->modify($id, $paramArray);
      } else {
        $data['issue_msg_error'] = '标题/消息内容不能为空';
      }
    }
    $data['modifyResult'] = $modifyResult;
    $arr_url_type = $this->issue_msg_model->arr_url_type();
    $data['arr_url_type'] = $arr_url_type;
    foreach ($arr_url_type as $key => $val) {
      $url_arr[] = $val['url'];
    }
    $data['url_arr'] = $url_arr;
    $this->load->view('issue_msg/modify', $data);
  }

  /**
   * 删除消息
   */
  public function del($id)
  { //只删除message_broker中与之关联的数据
    $data['title'] = '删除消息';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $issue_msgData = $this->issue_msg_model->del_issue_msg($id);  //删除消息
      $rs = $this->issue_msg_model->del_message_broker($id);  //删除message_broker中的数据
      if ($rs && $issue_msgData) {
        $delResult = 1;//删除成功
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('issue_msg/del', $data);
  }

  /**
   * 设置首页轮播
   */
  public function set_slider()
  {
    $id = $this->input->get('id');
    $slider = $this->input->get('slider');
    $slider = $slider == 0 ? 1 : 0;
    $paramArray = array('slider' => $slider);
    echo $this->issue_msg_model->modify($id, $paramArray);
  }

  /**
   * 设置首页置顶
   */
  public function set_top()
  {
    $id = $this->input->get('id');
    $is_top = $this->input->get('is_top');
    $is_top = $is_top == 0 ? 1 : 0;
    $paramArray = array('is_top' => $is_top);
    echo $this->issue_msg_model->modify($id, $paramArray);
  }
}
