<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 通知管理 Class
 *
 * 通知管理
 *
 * @package      mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      angel_in_us
 */
class Notice_manage extends MY_Controller
{
  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'sh';


  /**
   * 录入经纪人id
   *
   * @access private
   * @var int
   */
  private $_boker_id = 0;

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
  private $_limit = 10;

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
    $this->load->library('form_validation');//表单验证
    $this->load->model('notice_model');//通知模型类
    $this->load->model('api_broker_model');//经纪人接口模型类
    $this->load->model('remind_model');//事件提醒类
    $this->load->model('event_receiver_model');//事件接受者类
    error_reporting(E_ALL || ~E_NOTICE);
  }


  /**
   * 通知管理首页---通知消息列表页
   * @access public
   * @return void
   * date 2015-01-14
   * author angel_in_us
   */
  public function notice_list($page = 1)
  {
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    $data['where_in'] = array();
    $data['like'] = array();
    $broker_info = array();
    $broker_info = $this->user_arr;

    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    //echo $_POST['angel'].$_POST['notice_ids[]'];exit;
    //多选情况下删除通知信息
    if ($_POST['angel'] == 'angel_in_us' && !empty($_POST['notice_ids'])) {

      foreach ($_POST['notice_ids'] as $key => $value) {
        $result = "";
        $arr = array("id" => $value);
        //权限判断
        $notice_detail = $this->notice_model->get_notice_byid($arr);
        $result = $this->notice_model->del_notice_byid($arr);
      }
      echo "<script>location.href='" . MLS_URL . "/notice_manage/notice_list';</script>";
    }

    //根据当前登录经纪人所具有的管理权限范围（门店 或者 公司）来组装通知消息的接受者（receiver）id、name
    $judge = $this->user_func_permission;
    if ($judge['area'] == 2) {
      //经纪人的管理权限范围是：门店
      $data['broker_info_list'] = $this->api_broker_model->get_brokers_agency_id($broker_info['agency_id']);#经纪人信息列表：broker_id、true_name
      $data['agency_name'][] = $broker_info['agency_name'];#门店名
      $data['agency_id'][] = $broker_info['agency_id'];#门店id

      //所有经纪人broker_id数组
      $broker_ids = array();
      foreach ($data['broker_info_list'] as $k => $v) {
        $broker_ids[] = $v['broker_id'];
      }
      if (is_full_array($broker_ids)) {
        //权限条件
        $data['where_in'] = array('broker_id', $broker_ids);
      }

    } else if ($judge['area'] == 3) {
      //经纪人的管理权限范围是：整个公司
      $company_id = intval($broker_info['company_id']);//获取总公司编号
      $agency_list = $this->api_broker_model->get_agencys_by_company_id($company_id);//根据经济人总公司编号获取全部分店信息
      $data['agency_name'] = array();
      $data['agency_id'] = array();
      $data['broker_info_list'] = array();//array('broker_id' => 10,'truename' => '吴发顶')
      foreach ($agency_list as $key => $value) {
        $data['agency_name'][] = $value['agency_name'];#门店名
        $data['agency_id'][] = $value['agency_id'];#门店id
        $data['broker_info_list'] = array_merge($data['broker_info_list'], $this->api_broker_model->get_brokers_agency_id($value['agency_id']));#经纪人信息列表：broker_id、true_name
      }

      //所有经纪人broker_id数组
      $broker_ids = array();
      foreach ($data['broker_info_list'] as $k => $v) {
        $broker_ids[] = $v['broker_id'];
      }
      if (is_full_array($broker_ids)) {
        //权限条件
        $data['where_in'] = array('broker_id', $broker_ids);
      }
    }

    //发布通知消息
    if ($_POST['angel'] == 'angel_in_us' && !empty($_POST['title']) && !empty($_POST['contents']) && !empty($_POST['notice_time'])) {
      //不指定则发布给所有员工
      if (empty($_POST['receiver'])) {
        if ($judge['area'] == 3) {
          //经纪人的管理权限范围是：门店
          $data['broker_info_list'] = $this->api_broker_model->get_brokers_agency_id($broker_info['agency_id']);#经纪人信息列表：broker_id、true_name
          $broker_ids = "";
          //组装经纪人编号broker_id
          foreach ($data['broker_info_list'] as $key => $value) {
            $broker_ids .= $value['broker_id'] . ",";
          }
          $broker_ids = substr($broker_ids, 0, strlen($broker_ids) - 1);
        } else if ($judge['area'] == 2) {
          //经纪人的管理权限范围是：整个公司
          $company_id = intval($broker_info['company_id']);//获取总公司编号
          $agency_list = $this->api_broker_model->get_agencys_by_company_id($company_id);//根据经济人总公司编号获取全部分店信息
          $data['broker_info_list'] = array();//array('broker_id' => 10,'truename' => '吴发顶')
          foreach ($agency_list as $key => $value) {
            $data['broker_info_list'] = array_merge($data['broker_info_list'], $this->api_broker_model->get_brokers_agency_id($value['agency_id']));#经纪人信息列表：broker_id、true_name
          }
          $broker_ids = "";
          //组装经纪人编号broker_id
          foreach ($data['broker_info_list'] as $key => $value) {
            $broker_ids .= $value['broker_id'] . ",";
          }
          $broker_ids = substr($broker_ids, 0, strlen($broker_ids) - 1);
        }
      } else {
        //发给指定员工
        $broker_ids = "";
        //组装经纪人编号broker_id
        foreach ($_POST['receiver'] as $key => $value) {
          $broker_ids .= $value . ",";
        }
        $broker_ids = substr($broker_ids, 0, strlen($broker_ids) - 1);
      }
      if ($_POST['notice_time'] != "") {
        $_POST['notice_time'] = strtotime($_POST['notice_time']);
      } else {
        $_POST['notice_time'] = time();
      }
      //向  notice 表里插入所发布的通知信息
      $info = array(
        'contents' => $_POST['contents'],
        'receiver' => $broker_ids,
        'title' => $_POST['title'],
        'notice_time' => $_POST['notice_time'],
        'create_time' => time(),
        'level' => $_POST['level'],
        'broker_id' => $data['broker_info']
      );
      $result1 = $this->notice_model->add_notice($info);
      //向  event_remind 表里插入所发布的通知信息
      $data = array(
        'title' => $_POST['title'],
        'contents' => $_POST['contents'],
        'agency_id' => $broker_info['agency_id'],
        'broker_id' => $broker_info['broker_id'],
        'broker_name' => $broker_info['truename'],
        'create_time' => time(),
        'notice_time' => $_POST['notice_time'],
        'status' => '0'
      );
      $event_id = $this->remind_model->add_remind($data);

      //向  event_receiver 表里插入接受者编号 receiver_id 、 事件编号 event_id
      $receiver_ids = explode(',', $broker_ids);
      foreach ($receiver_ids as $key => $value) {
        $data2 = array(
          'receiver_id' => $value,
          'event_id' => $event_id
        );
        $result2 = $this->event_receiver_model->add_receiver($data2);
      }
      if ($result1 && $result2 && $event_id) {
        echo "<script>location.href='" . MLS_URL . "/notice_manage/notice_list';</script>";
      } else {

      }
    }

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);
    //符合条件的总行数
    $this->_total_count =
      $this->notice_model->get_notice_num($data['where_cond'], $data['where_in'], $data['like']);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    //分页处理000000000000
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $post_param['page'],//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');
    $notice_list = $this->notice_model->get_notice($data['where_cond'], $data['where_in'], $data['like'], $this->_limit, $this->_offset);

    if (is_full_array($notice_list)) {
      foreach ($notice_list as $key => $value) {
        $receiver_name = '';
        $receiver_arr = explode(',', $value['receiver']);
        for ($i = 0; $i < count($receiver_arr); $i++) {
          if ($i != 0) {
            $receiver_name .= ',';
          }
          $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($receiver_arr[$i]);
          $receiver_name .= $broker_info['truename'];
        }
        $notice_list[$key]['receiver_name'] = $receiver_name;
      }

    }
    $data['notice_list'] = $notice_list;
    //页面标题
    $data['page_title'] = '通知管理';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    //底部JS2
    $data['fuck_js'] = load_js('common/third/My97DatePicker/WdatePicker.js');

    //加载列表页面
    $this->view('office/notice_list.php', $data);
  }


  /**
   * 通知消息详情页
   * @access public
   * @return void
   * date 2015-01-14
   * author angel_in_us
   */
  public function notice_detail($nid)
  {
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    //根据通知信息id去查询房源详情
    if (!empty($nid)) {
      $data['where_cond'] = array('id' => $nid);
    }
    //权限判断
    $notice_detail = $this->notice_model->get_notice_byid($data['where_cond']);
    $detail_return = $this->user_func_permission;
    $this->redirect_permission_none();
    exit;

    if (is_full_array($notice_detail)) {
      foreach ($notice_detail as $key => $value) {
        $receiver_name = '';
        $receiver_arr = explode(',', $value['receiver']);
        for ($i = 0; $i < count($receiver_arr); $i++) {
          if ($i != 0) {
            $receiver_name .= ',';
          }
          $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($receiver_arr[$i]);
          $receiver_name .= $broker_info['truename'];
        }
        $notice_detail[$key]['receiver_name'] = $receiver_name;
      }
    }
    $data['notice_detail'] = $notice_detail;
    //页面标题
    $data['page_title'] = '通知管理';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    //加载通知详情页面
    $this->view('office/notice_detail.php', $data);
  }


  /**
   * 根据通知消息id删除对应的通知
   * @access public
   * @return void
   * date 2015-01-14
   * author angel_in_us
   */
  public function del_notice($nid)
  {
    if ($nid) {
      $arr = array("id" => $nid);

      $result = $this->notice_model->del_notice_byid($arr);
      if ($result) {
        echo "<script>location.href='" . MLS_URL . "/notice_manage/notice_list';</script>";
      } else {
        echo "<script>alert('删除失败，请稍后重试~！');location.href='" . MLS_URL . "/notice_manage/notice_list';</script>";
      }
    } else {
      echo "<script>alert('非法删除，请稍后重试~！');location.href='" . MLS_URL . "/notice_manage/notice_list';</script>";
    }
  }


  public function get_brokerinfo_by_agencyid($agency_id)
  {
    if (!empty($agency_id)) {
      $result = $this->api_broker_model->get_brokers_agency_id($agency_id);#经纪人信息列表：broker_id、true_name
      echo json_encode($result);
    } else {
      echo json_encode(array('result' => 'no result'));
    }
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
}

/* End of file notice_manage.php */
/* Location: ./application/mls/controllers/notice_manage.php */
