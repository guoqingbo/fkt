<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 个人中心-工作日志
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class My_log extends MY_Controller
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

  public function __construct()
  {
    parent::__construct();
    $this->load->model('my_log_model');
    $this->load->model('personnel_log_instructions_model');
  }

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
      $this->my_log_model->count_by($cond_where);


    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->my_log_model->get_all_by($cond_where, $this->_offset, $this->_limit);
    foreach ($list as $key => $val) {
      $list[$key]['instructions'] = $this->personnel_log_instructions_model->get_list_by("log_id = {$val['id']}");
    }

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
    $data['page_title'] = '工作日志列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css,mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');

    $this->view('uncenter/my_log/my_log', $data);
  }

  /**
   * 出售列表条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = '';
    //时间条件
    date_default_timezone_set('PRC');
    if (isset($form_param['start_time']) && $form_param['start_time']) {
      $start_time = strtotime($form_param['start_time'] . " 00:00");
      $cond_where .= " AND create_time >= '" . $start_time . "'";
    }

    if (isset($form_param['end_time']) && $form_param['end_time']) {
      $end_time = strtotime($form_param['end_time'] . " 23:59");
      $cond_where .= " AND create_time <= '" . $end_time . "'";
    }
    if (isset($start_time) && isset($end_time) && $start_time > $end_time) {
      $this->jump(MLS_URL . '/my_log/', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    return $cond_where;
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
   * 日志详情
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function details($id)
  {
    $isajax = $this->input->get('isajax', TRUE);
    //详情信息
    $this->my_log_model->set_select_fields(array("title", "content"));
    $data_info = $this->my_log_model->get_by_id($id);
    $data_info['instructions'] = $this->personnel_log_instructions_model->get_list_by("log_id = {$id}");
    foreach ($data_info['instructions'] as $key => $val) {
      $data_info['instructions'][$key]['create_time'] = date("Y-m-d H:i:s", $val['create_time']);
    }

    if ($isajax) {
      echo json_encode(array('result' => 'ok', 'data' => $data_info));
    }
  }


  /**
   * 添加日志信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function add()
  {
    $broker_id = $this->user_arr['broker_id'];

    $datainfo['broker_id'] = $broker_id;
    $title = $this->input->get('title', TRUE);
    $content = $this->input->get('content', TRUE);
    $datainfo['title'] = trim($title);
    $datainfo['content'] = trim($content);
    $datainfo['create_time'] = time();

    $id = $this->my_log_model->add_info($datainfo);
    if ($id) {
      echo json_encode(array('result' => 'ok'));
    } else {
      echo json_encode(array('result' => 'no'));
    }
//        if($id){
//            $url_manage = MLS_URL.'/my_log/';
//            $page_text = '添加成功';
//        }else{
//            $url_manage = MLS_URL.'/my_log/';
//            $page_text = '添加失败';
//        }
//        $this->jump($url_manage, $page_text, 3000);
  }


  /**
   * 删除 日志
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function del($del_id = 0)
  {
    //遗留 判断有无删除此房源权限

    $isajax = $this->input->get('isajax', TRUE);
    if ($isajax) {
      $str = $this->input->get('str', TRUE);
    } else {
      $str = $del_id;
    }

    $str = trim($str);
    $str = trim($str, ',');
    if ($str) {
      $ids = explode(',', $str);
      $this->my_log_model->del_by_id($ids);
    }
    if ($isajax) {
      echo json_encode(array('result' => 'ok'));
    } else {
      $this->jump(MLS_URL . '/my_log/', '删除成功');
    }
  }
}
