<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 员工日报
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class My_daily extends MY_Controller
{
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
    $this->load->model('broker_daily_model');
  }

  //经纪人是否提交过日报
  public function is_exist_daily()
  {
    $count = $this->broker_daily_model->is_exist_daily($this->user_arr['broker_id']);
    $result = array('status' => 1);
    if ($count > 0) {
      $result['status'] = 2;
    }
    echo json_encode($result);
  }

  //提交日报
  public function add_daily()
  {
    $title = $this->input->post('title');
    $content = $this->input->post('content');
    $promble = $this->input->post('promble');
    $result = array('status' => 1, 'msg' => '操作成功');
    //判断参数是否合法
    if (trim($title) == '' || trim($content) == '' || trim($promble) == '') {
      $result = array('status' => 0, 'msg' => '参数不合法');
      echo json_encode($result);
      return false;
    }
    //判断经纪人今天是否已经提交过日报
    $count = $this->broker_daily_model->is_exist_daily($this->user_arr['broker_id']);
    if ($count > 0) {
      $result = array('status' => 2, 'msg' => '已提交过日报');
      echo json_encode($result);
      return false;
    }
    //提交日报
    $daily_data = array(
      'broker_id' => $this->user_arr['broker_id'],
      'agency_id' => $this->user_arr['agency_id'],
      'company_id' => $this->user_arr['company_id'],
      'title' => $title, 'content' => $content, 'promble' => $promble
    );
    $this->broker_daily_model->add($daily_data);
    echo json_encode($result);
  }

  //查看我的工作日报
  public function index()
  {
    $broker_id = $this->user_arr['broker_id'];
    //模板使用数据
    $data = array();

    $data['user_menu'] = $this->user_menu;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);


    //查询房源条件
    $cond_where = "broker_id = {$broker_id} ";
    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;

    //符合条件的总行数
    $this->_total_count =
      $this->broker_daily_model->count_by($cond_where);


    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->broker_daily_model->get_all_by($cond_where, $this->_offset, $this->_limit);
    $data['list'] = $list;

    //分页处理
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

    //页面标题
    $data['page_title'] = '工作日报列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
      . 'mls/js/v1.0/backspace.js');

    $this->view('uncenter/my_daily/my_daily', $data);
  }

  public function find_daily($id)
  {
    if (intval($id) <= 0) {
      return false;
    }
    $daily = $this->broker_daily_model->get_one_by(array('broker_id' => $this->user_arr['broker_id'], 'id' => $id));
    if (is_full_array($daily) && $daily['comment_broker_id'] > 0) {
      $this->load->model('broker_info_model');
      $broker = $this->broker_info_model->get_by_broker_id($daily['comment_broker_id']);
      $daily['broker'] = $broker;
    }
    $data['daily'] = $daily;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/contract_manage.css,'
      . 'mls/css/v1.0/contract.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/verification_contract.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js'
      . 'mls/js/v1.0/backspace.js');
    $this->view('uncenter/my_daily/details', $data);
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
   * 出售列表条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = '';
    //时间条件
    if (isset($form_param['start_date_begin']) && $form_param['start_date_begin']) {
      $start_time = strtotime($form_param['start_date_begin'] . " 00:00:00");
      $cond_where .= " AND create_time >= '" . $start_time . "'";
    }

    if (isset($form_param['start_date_end']) && $form_param['start_date_end']) {
      $end_time = strtotime($form_param['start_date_end'] . " 23:59:59");
      $cond_where .= " AND create_time <= '" . $end_time . "'";
    }
    if (isset($form_param['comment']) && $form_param['comment']) {
      if ($form_param['comment'] == 1) {
        $cond_where .= " AND comment_broker_id = 0";
      } else {
        $cond_where .= " AND comment_broker_id > 0";
      }
    }
    return $cond_where;
  }
}

/* End of file broker_daily.php */
/* Location: ./application/mls/controllers/broker_daily.php */
