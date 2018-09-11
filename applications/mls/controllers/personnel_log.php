<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 门店管理--办公管理-员工日志
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Personnel_log extends MY_Controller
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
    $this->load->model('personnel_log_model');
    $this->load->model('personnel_log_see_model');
    $this->load->model('personnel_log_instructions_model');
  }

  public function index()
  {
    $broker_info = $this->user_arr;
    $broker_id = $broker_info['broker_id'];
    $agency_id = $broker_info['agency_id'];
    $company_id = $broker_info['company_id'];

    //模板使用数据
    $data = array();

    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);

    //查询房源条件
    $cond_where = "";

    //权限条件($func_area  1 本人  2 门店  3公司)
    $func_area = isset($this->user_func_permission['area']) ? $this->user_func_permission['area'] : 1;

    if ($func_area == 2) {
      //获取经纪人列表数组
      $this->load->model('api_broker_model');
      $brokers = $this->api_broker_model->get_brokers_agency_id($agency_id);
      $broker_ids = array();
      foreach ($brokers as $k => $v) {
        $broker_ids[] = $v['broker_id'];
      }
      if (is_full_array($broker_ids)) {
        $broker_ids_str = implode(",", $broker_ids);
        //权限条件
        $cond_where .= "`personnel_log`.broker_id in({$broker_ids_str})";
      }
    } else if ($func_area == 3) {
      //获取所有分公司数组
      $this->load->model('api_broker_model');
      $agencys = $this->api_broker_model->get_agencys_by_company_id($company_id);
      //所有分公司id数组
      $brokers = array();
      foreach ($agencys as $k => $v) {
        $brokers = array_merge($brokers, $this->api_broker_model->get_brokers_agency_id($v['agency_id']));
      }
      $broker_ids = array();
      foreach ($brokers as $k => $v) {
        $broker_ids[] = $v['broker_id'];
      }
      if (is_full_array($broker_ids)) {
        $broker_ids_str = implode(",", $broker_ids);
        //权限条件
        $cond_where .= "`personnel_log`.broker_id in({$broker_ids_str})";
      }
    }

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;

    //符合条件的总行数
    $this->_total_count =
      $this->personnel_log_model->count_by($cond_where);


    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->personnel_log_model->get_list_by($cond_where, $this->_offset, $this->_limit);
    foreach ($list as $key => $val) {
      $see_info = $this->personnel_log_see_model->get_one_by("log_id = {$val['id']} AND broker_id ={$broker_id}");
      if (empty($see_info)) {
        $list[$key]['is_see'] = 0;
      } else {
        $list[$key]['is_see'] = 1;
      }
      $instructions_info = $this->personnel_log_instructions_model->get_one_by("log_id = {$val['id']} AND broker_id ={$broker_id}");
      if (empty($instructions_info)) {
        $list[$key]['is_ins'] = 0;
      } else {
        $list[$key]['is_ins'] = 1;
      }
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
    $data['page_title'] = '员工日志列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js');
    $this->view('agency/office/personnel_log', $data);
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
      $this->jump(MLS_URL . '/personnel_log/', '您查询的开始时间不能大于结束时间！');
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
    $this->personnel_log_model->set_select_fields(array("title", "content"));
    $data_info = $this->personnel_log_model->get_by_id($id);

    $broker_id = $this->user_arr['broker_id'];
    //判断是否查看
    $see_info = $this->personnel_log_see_model->get_one_by("log_id = {$id} AND broker_id ={$broker_id}");
    if (empty($see_info)) {
      $see_info['log_id'] = $id;
      $see_info['broker_id'] = $broker_id;
      $see_info['create_time'] = time();
      $this->personnel_log_see_model->add_info($see_info);
    }

    if ($isajax) {
      echo json_encode(array('result' => 'ok', 'data' => $data_info));
    }
  }

  /**
   * 批示
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function instruct()
  {
    $broker_id = $this->user_arr['broker_id'];
    $log_id = $this->input->post('log_id', TRUE);

    //判断是否批示该日志
    $instructions_info = $this->personnel_log_instructions_model->get_one_by("log_id = {$log_id} AND broker_id ={$broker_id}");
    if (empty($instructions_info)) {
      $content = $this->input->post('content', TRUE);
      $datainfo['broker_id'] = $broker_id;
      $datainfo['log_id'] = $log_id;
      $datainfo['content'] = trim($content);
      $datainfo['create_time'] = time();

      $id = $this->personnel_log_instructions_model->add_info($datainfo);
      if ($id) {
        $result = "ok";
        $url_manage = MLS_URL . '/personnel_log/';
        $page_text = '批示成功';
      } else {
        $result = "no";
        $url_manage = MLS_URL . '/personnel_log/';
        $page_text = '批示失败';
      }
    } else {
      $result = "no";
      $url_manage = MLS_URL . '/personnel_log/';
      $page_text = '您已批示过该日志';
    }

    $isajax = $this->input->post('isajax', TRUE);
    if ($isajax) {
      echo json_encode(array('result' => $result, "ms" => $page_text));
    } else {
      $this->jump($url_manage, $page_text, 3000);
    }
  }

  /**
   * 批量阅读
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function batch_see($ids = 0)
  {
    $isajax = $this->input->get('isajax', TRUE);
    if ($isajax) {
      $str = $this->input->get('str', TRUE);
    } else {
      $str = $ids;
    }

    $str = trim($str);
    $str = trim($str, ',');
    if ($str) {
      $broker_id = $this->user_arr['broker_id'];
      $ids1 = explode(',', $str);
      //取得已阅日志编号
      $see_all = $this->personnel_log_see_model->get_all_by("log_id in ({$str}) AND broker_id ={$broker_id}", -1);
      $ids2 = array();
      foreach ($see_all as $key => $val) {
        $ids2[] = $val['log_id'];
      }
      //取得未阅日志编号
      $ids = array_diff($ids1, $ids2);
      if ($ids) {
        $batch_data = array();
        foreach ($ids as $k => $v) {
          $data['log_id'] = $v;
          $data['broker_id'] = $broker_id;
          $data['create_time'] = time();
          $batch_data[] = $data;
        }
        $this->personnel_log_see_model->add_batch_info($batch_data);
      }
    }
    if ($isajax) {
      echo json_encode(array('result' => 'ok'));
    } else {
      $this->jump(MLS_URL . '/personnel_log/', '删除成功');
    }
  }

  /**
   * 批量批示
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function batch_instruct($ids = 0)
  {
    $isajax = $this->input->get('isajax', TRUE);
    if ($isajax) {
      $str = $this->input->get('str', TRUE);
      $content = $this->input->get('content', TRUE);
    } else {
      $str = $ids;
    }

    $str = trim($str);
    $str = trim($str, ',');
    if ($str) {
      $broker_id = $this->user_arr['broker_id'];
      $ids1 = explode(',', $str);

      //取得已阅日志编号
      $see_all = $this->personnel_log_instructions_model->get_all_by("log_id in ({$str}) AND broker_id ={$broker_id}", -1);
      $ids2 = array();
      foreach ($see_all as $key => $val) {
        $ids2[] = $val['log_id'];
      }
      //取得未阅日志编号
      $ids = array_diff($ids1, $ids2);
      if ($ids) {
        $batch_data = array();
        foreach ($ids as $k => $v) {
          $data_info = $this->personnel_log_model->get_by_id($v);
          $data['log_id'] = $v;
          $data['broker_id'] = $broker_id;
          $data['content'] = trim($content);
          $data['create_time'] = time();
          $batch_data[] = $data;
        }
        if (is_full_array($batch_data)) {
          $this->personnel_log_instructions_model->add_batch_info($batch_data);
        } else {
          $this->redirect_permission_none();
          exit;
        }
      }
    }
    if ($isajax) {
      echo json_encode(array('result' => 'ok'));
    } else {
      $this->jump(MLS_URL . '/personnel_log/', '删除成功');
    }
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
    $arr_id = array();
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
      foreach ($ids as $k => $v) {
        $data_info = $this->personnel_log_model->get_by_id($v);
        $arr_id[$k] = $v;
      }
      if (is_full_array($arr_id)) {
        $this->personnel_log_model->del_by_id($arr_id);
      } else {
        $this->redirect_permission_none();
        exit;
      }

    }
    if ($isajax) {
      echo json_encode(array('result' => 'ok'));
    } else {
      $this->jump(MLS_URL . '/personnel_log/', '删除成功');
    }
  }

}
