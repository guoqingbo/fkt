<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统控制器
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 */

/**
 * Customer Controller CLASS
 *
 * 楼盘相关功能 控制器
 *
 * @package         MLS
 * @subpackage      Controllers
 * @category        Controllers
 * @author          xz
 */
class Community extends MY_Controller
{

  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'nj';

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
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('district_model');//区属模型类
    $this->load->model('community_model');//楼盘模型类
    $this->load->model('cmt_correction_model');//楼盘纠错模型类
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
   * 楼盘管理默认首页(楼盘字典列表页)
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function index()
  {
    //页面菜单
    $data['user_menu'] = $this->user_menu;
    //区属
    $data['district'] = $this->district_model->get_district();
    $data['where_cond'] = array();
    $data['like_code'] = array();
    $strcode = $this->input->post('strcode');
    if (isset($strcode) && !empty($strcode)) {
      $data['like_code']['cmt_name'] = trim($strcode);
    }
    $data['strcode'] = $strcode;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $this->_total_count = $this->community_model->get_community_num($data['where_cond'], $data['like_code']);
    $this->_init_pagination($page);
    $data['community'] = $this->community_model->get_community($data['where_cond'], $data['like_code'], $this->_offset, $this->_limit);
    foreach ($data['community'] as $k => $v) {
      $v['dist_name'] = $this->district_model->get_distname_by_id($v['dist_id']);
      $v['street_name'] = $this->district_model->get_streetname_by_id($v['streetid']);
      //当前楼盘当前区属对应的板块
      $v['street_arr'] = $this->district_model->get_street_bydist($v['dist_id']);
      $v['traffic'] = $v['bus_line'];
      $v['facilities_path'] = mb_substr($v['facilities'], 0, 25) . '......';
      $v['traffic_path'] = mb_substr($v['bus_line'], 0, 10) . '......';
      $data['community2'][] = $v;
    }

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
    $data['page_title'] = '楼盘字典';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/disk.js,mls/js/v1.0/backspace.js');

    $this->view('community/index', $data);
  }


  /**
   * 楼盘纠错
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function cmt_correction()
  {
    $user = $this->user_arr;
    $return_data = '';
    $cmt_id = $this->input->get('cmt_id');
    //获得楼盘名称
    $comm_data = $this->community_model->get_community(array('id' => trim($cmt_id)));
    $cmt_name = $comm_data[0]['cmt_name'];
    $field = $this->input->get('field');
    $field_name = $this->input->get('field_name');
    if ($field == 'green_rate' || $field == 'plot_ratio') {
      $information = $this->input->get('information') / 100;
    } else {
      $information = $this->input->get('information');
    }
    $paramArray = array(
      'cmt_id' => $cmt_id,//楼盘id
      'cmt_name' => $cmt_name,//楼盘名称
      'correction_feild' => $field,//纠错字段
      'correction_feild_name' => $field_name,//纠错字段内容
      'correctioninfo' => $information,//纠错内容
      'creattime' => time(),//创建时间
      'ip' => $_SERVER['REMOTE_ADDR'],//客户端IP
      'esta' => 0, //信息状态（默认未审核）
      'user_id' => $user['broker_id'],//纠错人id
      'user_name' => $user['truename'],//纠错人姓名
    );
    //判断信息是否有变化
    $old_information = $comm_data[0][$field];
    if ($old_information == $information) {
      $return_data = 'same info';
    } else if ($field == 'cmt_name' && $information == '') {
      $return_data = 'cmt_name is null';
    } else if ($field == 'dist_id' && $information == '') {
      $return_data = 'dist_id is null';
    } else if ($field == 'streetid' && $information == '') {
      $return_data = 'street_id is null';
    } else if ($field == 'address' && $information == '') {
      $return_data = 'address is null';
    } else if ($field == 'build_date' && $information == '') {
      $return_data = 'build_date is null';
    } else {
      $add_result = $this->cmt_correction_model->add_cmt_correction($paramArray);
      if (!empty($add_result) && is_int($add_result)) {
        $return_data = 'add success';
      } else {
        $return_data = 'add failed';
      }
    }
    echo $return_data;
  }

  /**
   * 添加楼盘
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function add_community()
  {
    //物业类型
    $build_type_str = '';
    $build_type_arr = $this->input->get('build_type');
    if (!empty($build_type_arr) && is_array($build_type_arr)) {
      $build_type_str = implode('#', $build_type_arr);
    }
    $paramArray = array(
      'cmt_name' => trim($this->input->get('cmt_name')),
      'dist_id' => trim($this->input->get('dist_id')),
      'streetid' => trim($this->input->get('streetid')),
      'address' => trim($this->input->get('address')),
      'build_date' => $this->input->get('build_date'),
      'property_year' => $this->input->get('property_year'),
      'coverarea' => $this->input->get('coverarea'),
      'property_company' => $this->input->get('property_company'),
      'developers' => $this->input->get('developers'),
      'parking' => $this->input->get('parking'),
      'green_rate' => $this->input->get('green_rate'),
      'plot_ratio' => intval($this->input->get('plot_ratio')) / 100,
      'property_fee' => intval($this->input->get('property_fee')) / 100,
      'build_num' => $this->input->get('build_num'),
      'total_room' => $this->input->get('total_room'),
      'floor_instruction' => $this->input->get('floor_instruction'),
      'introduction' => $this->input->get('introduction'),
      'facilities' => $this->input->get('facilities'),
      'build_type' => $build_type_str
    );
    $return_data = '';
    if ($paramArray['cmt_name'] == '') {
      $return_data = '100';//楼盘名为空
    } else if ($paramArray['dist_id'] == '') {
      $return_data = '200';//区属为空
    } else if ($paramArray['streetid'] == '') {
      $return_data = '300';//板块为空
    } else if ($paramArray['address'] == '') {
      $return_data = '400';//地址为空
    } else {
      $cmt_data = $this->community_model->get_cmtinfo_by_cmtname($paramArray['cmt_name']);
      if (!empty($cmt_data) && is_array($cmt_data)) {
        $return_data = '500';//该小区已存在
      } else {
        $add_result = $this->community_model->add_community($paramArray);//楼盘数据入库
        if (!empty($add_result) && is_int($add_result)) {
          $return_data = 'true';
        } else {
          $return_data = 'false';
        }
      }
    }
    echo $return_data;
    exit;
  }

  /**
   * 添加楼盘
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function add_cmt()
  {
    $paramArray = array(
      'cmt_name' => strval($this->input->post('cmt_name')),
      'dist_id' => intval($this->input->post('dist_id')),
      'streetid' => intval($this->input->post('street_id')),
      'address' => strval($this->input->post('address')),
      'status' => 3
    );
    $flag = 0;
    $msg = '';
    if ($paramArray['cmt_name'] == '') {
      $msg = '楼盘名为空';
    } else if ($paramArray['dist_id'] == '') {
      $msg = '区属为空';
    } else if ($paramArray['streetid'] == '') {
      $msg = '板块为空';
    } else if ($paramArray['address'] == '') {
      $msg = '地址为空';
    } else {
      $cmt_data = $this->community_model->get_cmtinfo_by_cmtname_from_official($paramArray['cmt_name']);
      if (!empty($cmt_data) && is_array($cmt_data)) {
        $msg = '该小区已存在';
      } else {
        $add_result = $this->community_model->add_community($paramArray);//楼盘数据入库
        if (!empty($add_result) && is_int($add_result)) {
          $flag = 1;
          $msg = '添加成功';
        } else {
          $msg = '系统错误';
        }
      }
    }
    $return_arr = array(
      'result' => $flag,
      'msg' => $msg
    );
    if (1 == $flag) {
      $data['cmt_id'] = $add_result;
    }
    $this->result($flag, $msg, $data);
  }

  /**
   * 根据关键词获取楼盘信息
   *
   * @access public
   * @param  void
   * @return json
   */
  public function get_cmtinfo_by_kw()
  {
    $keyword = $this->input->get('keyword', TRUE);
    $this->load->model('community_model');
    $select_fields = array('id', 'cmt_name', 'dist_id', 'streetid', 'address', 'averprice', 'status', 'build_date');
    $this->community_model->set_select_fields($select_fields);
    $cmt_info = $this->community_model->auto_cmtname($keyword, 10);
    foreach ($cmt_info as $key => $value) {
      $cmt_info[$key]['label'] = $value['cmt_name'];
    }

    if (empty($cmt_info)) {
      $cmt_info[0]['id'] = 0;
      $cmt_info[0]['label'] = '暂无小区';
      $cmt_info[0]['averprice'] = 0.00;
      $cmt_info[0]['address'] = '暂无地址';
      $cmt_info[0]['status'] = -1;
      $cmt_info[0]['districtname'] = '暂无信息';
      $cmt_info[0]['streetname'] = '暂无信息';
    }

    echo json_encode($cmt_info);
  }


  /**
   * 页面ajax请求根据属区获得对应板块
   */
  public function find_street_bydis($districtID)
  {
    if (!empty($districtID)) {
      $districtID = intval($districtID);
      $street = $this->district_model->get_street_bydist($districtID);
      echo json_encode($street);
    } else {
      echo json_encode(array('result' => 'no result'));
    }
  }

  /**
   * 页面ajax请求根据属区获得对应板块
   */
  public function find_street_bydt($district)
  {
    if (!empty($district)) {
      $street = $this->district_model->get_street_bydt($district);
      echo json_encode($street);
    } else {
      echo json_encode(array('result' => 'no result'));
    }
  }

  /**
   * 画户型图
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function draw()
  {
    //页面菜单
    $data['user_menu'] = $this->user_menu;
    //页面标题
    $data['page_title'] = '画户型图';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/disk.js,mls/js/v1.0/backspace.js');
    $this->view('community/draw', $data);
  }
}

/* End of file community.php */
/* Location: ./applications/mls/controllers/community.php */
