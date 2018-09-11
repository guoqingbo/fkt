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
  private $_limit = 15;

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
    $this->load->model('remind_model');
    $this->load->model('event_receiver_model');
    $this->load->model('company_notice_model');
    $this->load->model('company_employee_model');
    $this->load->model('cooperate_friends_base_model');
  }


  /**
   * 系统消息
   * @access public
   * @return void
   */
  public function bulletin($page = 1)
  {
    //遗留 判断是否登录
    $this->load->model('broker_model');
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    //模板使用数据
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //print_R($post_param);exit;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page, $this->_limit);

    //查询条件
    $cond_where = array('broker_id' => $broker_id);

    //排序字段
    $roomorder = 3;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $this->_total_count = $this->message_model->get_count_by_cond($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->message_model->get_row_by_cond($cond_where, $this->_offset, $this->_limit);
    foreach ($list as $k => $vo) {
      $list[$k]['message'] = $this->substr_for_string($vo['message'], 30);
      //查看是否为好友申请消息
      if ($vo['type'] == 8) {
        $apply_message = $this->cooperate_friends_base_model->get_message_by_id($vo['msg_id']);
        if (is_full_array($apply_message)) {
          $list[$k]['status'] = $apply_message['status'];
          $list[$k]['apply_id'] = $apply_message['apply_id'];
        }
      }
    }

    $data['list'] = $list;
    //消息分类
    $data['type'] = $this->message_model->get_all_type();
    //分页处理000000000000
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/personal_center.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/openWin.js,'
      . 'mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/message.js,'
      . 'mls/js/v1.0/personal_center.js');


    //页面标题
    $data['page_title'] = '个人中心---消息管理';
    $data['broker_id'] = $broker_id;//获取经纪人编号
    $this->view('message/bulletin.php', $data);
  }

  /**
   * 跟进提醒
   * @access public
   * @return void
   */
  public function smessage($page = 1)
  {
    //遗留 判断是否登录
    $this->load->model('broker_model');
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    //模板使用数据
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page, $this->_limit);

    //查询条件
    //$cond_where = array('broker_id'=>$broker_id);
    $cond_where = "broker_id = " . $broker_id;
    //排序字段
    $roomorder = 3;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $this->_total_count = $this->message_model->get_count_by_cond_smessage($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取提醒列表内容
    $list = $this->message_model->get_smessage_by($cond_where, $this->_offset, $this->_limit);
    foreach ($list as $k => $vo) {
      $list[$k]['contents'] = $this->substr_for_string($vo['contents'], 6);
    }
    $data['list'] = $list;
    //print_r($list);
    //获取跟进列表内容
    $detail = $this->message_model->get_detail_by($cond_where);
    foreach ($detail as $k => $vo) {
      $detail[$k]['text'] = $this->substr_for_string($vo['text'], 10);
    }
    //print_r($detail);
    $data['detail'] = $detail;
    //分页处理000000000000
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');


    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/personal_center.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/openWin.js,'
      . 'mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/message.js,'
      . 'mls/js/v1.0/personal_center.js');

    //页面标题
    $data['page_title'] = '个人中心---消息管理';
    $data['broker_id'] = $broker_id;//获取经纪人id
    //渲染页面
    $this->view('message/smessage.php', $data);
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
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page, $this->_limit);

    //查询条件
    $cond_where = array('company_id' => $company_id);

    //排序字段
    $roomorder = 3;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $this->_total_count = $this->company_notice_model->get_count_by_cond($cond_where);
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->company_notice_model->get_company_notice_by($cond_where, $this->_offset, $this->_limit);
    foreach ($list as $k => $vo) {
      $vo['contents'] = trim(strip_tags($vo['contents']));
      $list[$k]['contents'] = mb_substr($vo['contents'], 0, 30, 'utf-8');
      $broker_info = $this->company_employee_model->get_broker_by_id($vo['broker_id']);
      $list[$k]['broker_name'] = $broker_info['truename'];
      if (mb_strlen($vo['contents']) > 30) {
        $list[$k]['contents'] .= '...';
      }
      $notice_broker = $this->company_notice_model->get_company_notice_broker_by(array('n_id' => $vo['id'], 'broker_id' => $vo['broker_id']));
      if (is_full_array($notice_broker)) {
        $list[$k]['is_read'] = $notice_broker['is_read'];
      } else {
        $list[$k]['is_read'] = 0;
      }
    }
    $data['list'] = $list;
    //print_r($list);exit;
    //分页处理000000000000
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //print_r($params);
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      //. 'mls/css/v1.0/personal_center.css'
      . 'mls/css/v1.0/notice.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/openWin.js,'
      . 'mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/message.js,'
      . 'mls/js/v1.0/personal_center.js');


    //页面标题
    $data['page_title'] = '个人中心---公司公告';
    $data['broker_id'] = $broker_id;//获取经纪人编号
    $this->view('message/notice.php', $data);
  }

  /**
   * 公司公告详情company_notice
   * @access  public
   * @return  json
   */
  public function company_notice_detail()
  {
    $this->load->model('broker_model');
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    $id = $this->input->post('id', TRUE);
    $detail = $this->company_notice_model->get_detail_by_id($id);


    $this->company_notice_model->company_notice_broker_del(array('n_id' => $detail['id'], 'broker_id' => $broker_id));
    $this->company_notice_model->add_notice_broker(array('n_id' => $detail['id'], 'broker_id' => $broker_id, 'is_read' => 1, 'createtime' => time()));

    $detail['createtime'] = date('Y-m-d H:i:s', $detail['createtime']);
    $detail['is_read'] = 1;
    //print_r($detail);die;
    echo json_encode($detail);

  }

  /**
   * 公司公告设为已读
   * @access  public
   * @return  json
   */
  public function notice_read()
  {
    $this->load->model('broker_model');
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $result = 0;
    $ids = $this->input->post('id', TRUE);
    foreach ($ids as $vo) {
      $detail = $this->company_notice_model->get_detail_by_id($vo);
      $this->company_notice_model->company_notice_broker_del(array('n_id' => $detail['id'], 'broker_id' => $broker_id));
      $insert_id = $this->company_notice_model->add_notice_broker(array('n_id' => $detail['id'], 'broker_id' => $broker_id, 'is_read' => 1, 'createtime' => time()));
      if ($insert_id) {
        $result++;
      }
    }
    if ($result == count($ids)) {
      $res['result'] = 'ok';
    } else {
      $res['result'] = '';
    }
    echo json_encode($res);

  }

  /**
   * 跟进提醒详情页smessage_detail
   * @access  public
   * @return  json
   */
  public function smessage_detail()
  {
    $id = $this->input->post('id', TRUE);
    $data = $this->message_model->get_event_by_id($id);
    //print_r($data);die();
    $detail = $this->message_model->get_detail_by_id($data['detail_id']);
    if ($detail) {
      $data['date'] = $detail['date'];
      $data['follow_way'] = $detail['follow_way'];
      $data['text'] = $detail['text'];
      $data['type'] = $detail['type'];
      $data['house_id'] = $detail['house_id'];
      $data['customer_id'] = $detail['customer_id'];
    }
    $data['create_time'] = date("Y-m-d H:i:s", $data['create_time']);
    $update_arr = array("is_look" => 2);
    $num = $this->message_model->update_event_by_ids($id, $update_arr);
    $data['num'] = $num;
    echo json_encode($data);

  }

  /**
   * 跟进提醒详情页完成操作
   * @access  public
   * @return  json
   */
  public function complete()
  {
    $id = $this->input->post('id', TRUE);
    $update_arr = array("status" => 1);
    $num = $this->message_model->update_event_by_ids($id, $update_arr);
    echo json_encode($num);
  }


  /**
   * 消息详情页
   * @access  public
   * @return  json
   */
  public function details()
  {
    date_default_timezone_set('PRC');
    $id = $this->input->post('id', TRUE);
    $data['is_read'] = '1';
    $result = $this->message_model->get_result($id);
    if (!empty($result)) {
      $this->message_model->message_broker_update($data, 'msg_id = ' . $id);
      $apply_message = $this->cooperate_friends_base_model->get_message_by_id($result['id']);
      if (is_full_array($apply_message)) {
        $result['status'] = $apply_message['status'];
        $result['apply_id'] = $apply_message['apply_id'];
        $res['status'] = $result['status'];
        $res['apply_id'] = $result['apply_id'];
      }
      $res['id'] = $result['id'];
      $res['title'] = $result['title'];
      $res['from'] = $result['from'];
      $res['is_read'] = $data['is_read'];


      if (!empty($result['url']) && $result['url'] != "" && $result['type'] != 3 && !$result['apply_id']) {
        $res['message'] = $result['message'] . '<a href="' . MLS_URL . $result['url'] . '">查看详情</a>';
      } else {
        if ($res['status'] == 1) {
          $res['message'] = $result['message'] . '<a href="' . MLS_URL . '/sell/friend_lists_pub">查看详情</a>';
        } else {
          $res['message'] = $result['message'];
        }
      }

      $res['createtime'] = date('Y-m-d H:i:s', $result['createtime']);
      echo json_encode($res);
    }
  }

  /**
   * 删除
   * @access  public
   * @return  json
   */
  public function del()
  {
    $ids = $this->input->get('str', TRUE);
    $broker_id = $this->input->get('broker_id', TRUE);
    $result = $this->message_model->message_broker_del('msg_id in (' . $ids . ') and broker_id =' . $broker_id);
    if ($result > 0) {
      $data['result'] = 'ok';
    }
    echo json_encode($data);
  }

  /**
   * 设为已读
   * @access  public
   * @return  json
   */
  public function read()
  {
    $id = $this->input->post('id', TRUE);
    $update_arr = array("is_read" => 1);
    $result = $this->message_model->update_bulletin_by_ids($id, $update_arr);
    if ($result > 0) {
      $res['result'] = 'ok';
    }
    echo json_encode($result);

  }


  /**
   * 获取排序参数
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
   * 公告
   * @access public
   * @return void
   */


  public function my_remind($page = 1)
  {
    //根据当前经纪人id（接受者id）获得对应的事件id
    $receiver_id = $this->user_arr['broker_id'];
    $event_id_data = $this->event_receiver_model->get_event_by_receiver($receiver_id);
    $event_id_arr = array();
    foreach ($event_id_data as $k => $v) {
      $event_id_arr[] = $v['event_id'];
    }
    $where_in = array();
    if (!empty($event_id_arr)) {
      $where_in = array('id', $event_id_arr);
    }
    //模板使用数据
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    $remind_list2 = array();
    $where_cond = "id != 0 ";
    $data['like_code'] = array();
    $where_param = $this->input->post(NULL, TRUE);
    if (!empty($where_param['min_create_time'])) {
      $where_cond .= "AND create_time >= " . strtotime($where_param['min_create_time']) . " ";
    }
    if (!empty($where_param['max_create_time'])) {
      $where_cond .= "AND create_time <= " . strtotime($where_param['max_create_time']) . " ";
    }
    //所在公司的分店信息
    $company_id = intval($this->user_arr['company_id']);
    $this->load->model('api_broker_model');
    $company_id = $this->user_arr['company_id'];
    $data['agencys'] = $this->api_broker_model->get_agencys_by_company_id($company_id);
    //分页开始
    $data['remind_num'] = $this->remind_model->get_remind_num($where_cond, $where_in, $data['like_code']);
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['remind_num'] ? ceil($data['remind_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $this->_total_count = $data['remind_num'];
    $this->_init_pagination($page);

    $remind_list = $this->remind_model->get_remind($where_cond, $where_in, $data['like_code'], $this->_offset, $this->_limit);
    foreach ($remind_list as $k => $v) {
      $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
      $v['notice_time'] = date('Y-m-d H:i:s', $v['notice_time']);
      $remind_list2[] = $v;
    }
    $data['remind_list'] = $remind_list2;

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post/ajax
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '跟进提醒';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('message/my_remind', $data);


  }

  //截取中英文字符串超过固定长度为省略号
  function substr_for_string($sourcestr, $cutlength)
  {
    $returnstr = "";
    $i = 0;
    $n = 0;
    $str_length = strlen($sourcestr);    //字符串的字节数
    while (($n < $cutlength) and ($i <= $str_length)) {
      $temp_str = substr($sourcestr, $i, 1);
      $ascnum = Ord($temp_str); //得到字符串中第$i位字符的ascii码
      if ($ascnum >= 224) //如果ASCII位高与224，
      {
        $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
        $i = $i + 3; //实际Byte计为3
        $n++; //字串长度计1
      } elseif ($ascnum >= 192)//如果ASCII位高与192，
      {
        $returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
        $i = $i + 2; //实际Byte计为2
        $n++; //字串长度计1
      } elseif ($ascnum >= 65 && $ascnum <= 90) //如果是大写字母，
      {
        $returnstr = $returnstr . substr($sourcestr, $i, 1);
        $i = $i + 1; //实际的Byte数仍计1个
        $n++; //但考虑整体美观，大写字母计成一个高位字符
      } else //其他情况下，包括小写字母和半角标点符号，
      {
        $returnstr = $returnstr . substr($sourcestr, $i, 1);
        $i = $i + 1;    //实际的Byte数计1个
        $n = $n + 0.5;    //小写字母和半角标点等与半个高位字符宽…
      }
    }
    if ($str_length > $cutlength) {
      $returnstr = $returnstr . "...";    //超过长度时在尾处加上省略号
    }
    return $returnstr;
  }
}
/* End of file message.php */
/* Location: ./application/mls/controllers/message.php */
