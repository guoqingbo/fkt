<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 个人中心-我的成交-出租
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class My_deal_rent extends MY_Controller
{
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
    $this->load->model('house_deal_model');
  }

  public function index()
  {
    $broker_id = $this->user_arr['broker_id'];

    $data = array();
    $data['type'] = 'rent';

    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page, 5);

    //查询房源条件
    $cond_where = "type = 2 AND broker_id = {$broker_id} ";

    //符合条件的总行数
    $this->_total_count =
      $this->house_deal_model->count_by($cond_where);


    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->house_deal_model->get_all_by($cond_where, $this->_offset, $this->_limit);
    foreach ($list as $key => $val) {
      $list[$key]['house_info'] = unserialize($val['house']);
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

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();

    //页面标题
    $data['page_title'] = '我的成交-出租';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/personal_center.js');

    $this->view('uncenter/my_deal/my_deal', $data);
  }

  public function house_list()
  {
    $this->load->model('rent_house_model');
    $broker_id = $this->user_arr['broker_id'];

    //已添加的成交记录
    $house_id_arr = array();
    $this->house_deal_model->set_select_fields(array("house_id"));
    $house_deal_list = $this->house_deal_model->get_all_by("type = 2 AND broker_id = {$broker_id} ", -1);
    foreach ($house_deal_list as $key => $val) {
      $house_id_arr[] = $val["house_id"];
    }
    $house_id_str = implode(",", $house_id_arr);

    $data = array();
    $data['type'] = 'rent';

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page, 5);

    //查询房源条件
    $cond_where = "status != 5 AND broker_id = {$broker_id} ";

    //排除已添加的房源
    if ($house_id_str) {
      $cond_where .= " AND id not in ({$house_id_str})";
    }

    //表单提交参数组成的查询条件
    //房源编号
    $house_id = isset($post_param['house_id']) ? intval($post_param['house_id']) : 0;
    if ($house_id) {
      $cond_where .= " AND id = '" . $house_id . "'";
    }

    //楼盘ID
    $block_id = isset($post_param['block_id']) ? intval($post_param['block_id']) : 0;
    if ($block_id) {
      $cond_where .= " AND block_id = '" . $block_id . "'";
    }

    //符合条件的总行数
    $this->_total_count =
      $this->rent_house_model->get_count_by_cond($cond_where);


    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->rent_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit);

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

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();

    //页面标题
    $data['page_title'] = '添加成交房源';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/personal_center.js');

    $this->view('uncenter/my_deal/house_list', $data);
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

  public function add($house_id = 0)
  {
    $this->load->model('rent_house_model');
    $data = array();
    $data['type'] = 'rent';

    $this->rent_house_model->set_id($house_id);
    $data_info = $this->rent_house_model->get_info_by_id();

    //加载区属模型类
    $this->load->model('district_model');
    $data_info['district_name'] = $this->district_model->get_distname_by_id($data_info['district_id']);
    $data_info['street_name'] = $this->district_model->get_streetname_by_id($data_info['street_id']);

    $data_info['telnos'] = $data_info['telno1'];
    $data_info['telnos'] .= !empty($data_info['telno2']) ? ', ' . $data_info['telno2'] : '';
    $data_info['telnos'] .= !empty($data_info['telno3']) ? ', ' . $data_info['telno3'] : '';

    $post_param = $this->input->post(NULL, TRUE);
    $submit_flag = $post_param['submit_flag'];
    if ('add' == $submit_flag) {
      $broker_id = $this->user_arr['broker_id'];
      $agency_id = $this->user_arr['agency_id'];
      $house_str = serialize($data_info);
      $datainfo = array(
        "broker_id" => $broker_id,
        "agency_id" => $agency_id,
        "type" => 2,
        "house_id" => $house_id,
        "house" => $house_str,
        "price" => doubleval($post_param['price']),
        "name" => trim($post_param['name']),
        "tel" => trim($post_param['tel']),
        "createtime" => time()
      );
      $id = $this->house_deal_model->add_info($datainfo);
      if ($id) {
        $this->jump(MLS_URL . '/my_deal_rent/details/' . $id . '/1', '添加成功');
        exit;
      } else {
        $this->jump(MLS_URL . '/my_deal_rent/add/' . $house_id, '添加失败');
        exit;
      }
    }

    $data['data_info'] = $data_info;

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();

    //页面标题
    $data['page_title'] = '成交房源详情';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/personal_center.js,'
      . 'mls/js/v1.0/jquery.validate.min.js');

    $this->view('uncenter/my_deal/add', $data);
  }

  public function details($id, $flag = 0)
  {
    $data = array();
    $data['type'] = 'rent';
    $data['flag'] = $flag;

    $data_info = $this->house_deal_model->get_by_id($id);
    $data_info['house_info'] = unserialize($data_info['house']);

    $data['data_info'] = $data_info;

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();

    //页面标题
    $data['page_title'] = '成交房源详情';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/personal_center.js,'
      . 'mls/js/v1.0/jquery.validate.min.js');

    $this->view('uncenter/my_deal/details', $data);
  }

}
