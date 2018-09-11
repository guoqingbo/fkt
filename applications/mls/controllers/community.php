<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统控制器
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
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
  protected $_city = 'hz';

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
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('district_model');//区属模型类
    //$this->load->model('community_model');//楼盘模型类
    $this->load->model('cmt_correction_model');//楼盘纠错模型类
    $this->load->model('help_center_model');//帮助中心模型类
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
    //获取当前经济人所在门店的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //是否开启了锁盘
    $is_lock_cmt = 0;
    if (is_full_array($company_basic_data)) {
      $is_lock_cmt = $company_basic_data['is_lock_cmt'];
    }
    $data['is_lock_cmt'] = $is_lock_cmt;

    $data['this_user'] = $this->user_arr;
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
    $this->load->model('community_model');
    $this->_total_count = $this->community_model->get_community_num($data['where_cond'], $data['like_code']);
    $this->_init_pagination($page);
    $data['community'] = $this->community_model->get_community($data['where_cond'], $data['like_code'], $this->_offset, $this->_limit);
    foreach ($data['community'] as $k => $v) {
      $v['build_type'] = str_replace('#', '、', $v['build_type']);
      $v['dist_name'] = $this->district_model->get_distname_by_id($v['dist_id']);
      $v['street_name'] = $this->district_model->get_streetname_by_id($v['streetid']);
      //当前楼盘当前区属对应的板块
      $v['street_arr'] = $this->district_model->get_street_bydist($v['dist_id']);
      $v['traffic'] = $v['bus_line'];
      $v['facilities_path'] = mb_substr($v['facilities'], 0, 25) . '......';
      $v['traffic_path'] = mb_substr($v['bus_line'], 0, 10) . '......';
      if (empty($v['surface_img'])) {
        $v['surface_img'] = MLS_SOURCE_URL . '/mls/images/v1.0/365mls.png';
      }
      //是否被锁
      $where_cond = array(
        'cmt_id' => intval($v['id']),
        'company_id' => intval($data['this_user']['company_id'])
      );
      $cmt_lock_info = $this->community_model->get_lock_cmt($where_cond);
      $v['is_lock'] = 0;
      if (is_full_array($cmt_lock_info)) {
        $v['is_lock'] = $cmt_lock_info[0]['is_lock'];
      }
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
      . 'mls/css/v1.0/house_new.css,'
      . 'mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/cmt_uploadpic.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,mls/js/v1.0/disk.js,mls/js/v1.0/backspace.js');

    $this->view('community/index2', $data);
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
    $this->load->model('community_model');
    $comm_data = $this->community_model->get_community(array('id' => trim($cmt_id)));
    $cmt_name = $comm_data[0]['cmt_name'];
    $field = $this->input->get('field');
    $field_name = $this->input->get('field_name');
    if ($field == 'green_rate') {
      $information = $this->input->get('information') / 100;
    } else if ($field == 'build_date') {
      $build_date_year = $this->input->get('information');
      $build_date_str = $build_date_year . '-1-1';
      $information = strtotime($build_date_str);
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
    $paramArray = array(
      'cmt_name' => trim($this->input->get('cmt_name')),
      'dist_id' => trim($this->input->get('dist_id')),
      'streetid' => trim($this->input->get('streetid')),
      'address' => trim($this->input->get('address')),
      'status' => 3
    );
    $return_data = '';

    $this->load->model('community_model');
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

    echo $return_data;
    exit;
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
      $cmt_info[0]['label'] = '暂无小区，请添加楼盘 ';
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

  /**
   * 帮助中心
   * @access public
   * @param  void
   * @return void
   */
  public function help_center($id = 0)
  {
    //页面菜单
    $data['user_menu'] = $this->user_menu;
    //页面标题
    $data['page_title'] = '帮助中心';
    $data['parents'] = $this->help_center_model->get_all_parents();
    if (!empty($data['parents']) and $id == 0) {
      $data['first'] = $this->help_center_model->get_children($data['parents']['0']['id']);
      $data['f_ptitle'] = $data['parents']['0']['title'];//第一项主菜单名称
      $data['now_active'] = $data['parents']['0']['id'];
    }
    if ($id) {
      $data['first'] = $this->help_center_model->get_children($id);
      $parent_name = $this->help_center_model->get_parent_name($id);
      $data['f_ptitle'] = $parent_name['title'];//第一项主菜单名称
      $data['now_active'] = $id;
    }
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/guest_disk.css,'
      . 'mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/disk.js,mls/js/v1.0/backspace.js');
    $this->view('community/help_center', $data);
  }

  /**
   * 帮助中心
   * @access public
   * @param  void
   * @return void
   */
  public function save_picture()
  {
    $post_param = $this->input->post(NULL, TRUE);
    $data = array();
    $data['cmt_id'] = $post_param['cmt_id'];
    $data['pic_type'] = $post_param['pic_type'];
    $data['image'] = $post_param['p_filename1'];
    $this->load->model('community_model');
    $this->community_model->add_cmt_pic($data);
    echo '<script>location.href="/community/img_detail/' . $data['cmt_id'] . '/' . $data['pic_type'] . '";</script>';
  }

  /**
   * 图片详情
   * @access public
   * @param  void
   * @return void
   */
  public function img_detail($id = 0, $type_id = 2)
  {
    $this->load->model('community_model');
    if (!empty($id)) {
      $data['id'] = $id;
      $data['type_id'] = $type_id;
      $cmt_id = intval($id);
      $cmt_data = $this->community_model->get_cmtinfo_longitude($cmt_id);
      $is_upload = $this->community_model->get_is_upload_pic_id_by($cmt_id);
      $data['is_upload'] = is_full_array($is_upload) ? $is_upload[0]['is_upload_pic'] : 0;
      //print_r($is_upload);
      if (!empty($cmt_data['surface_img'])) {
        $surface_img_url2 = '';
        $surface_img_url = $cmt_data['surface_img'];
//                $surface_img_url_arr = explode('/thumb',$surface_img_url);
//                if(is_array($surface_img_url_arr) && !empty($surface_img_url_arr)){
//                    foreach($surface_img_url_arr as $k => $v){
//                        $surface_img_url2 .= $v;
//                    }
//                }
        $surface_img_url2 = changepic($surface_img_url);
      } else {
        $surface_img_url2 = MLS_SOURCE_URL . '/mls/images/v1.0/365mls.png';
      }
      $data['surface_img'] = $surface_img_url2;
      $data['cmt_id'] = $id;
      //获取小区正门的图片
      $where_cond = array(
        'cmt_id' => $cmt_id,
        'pic_type' => 2
      );
      $img_data = $this->community_model->get_all_cmt_image_by_cmtid($where_cond);
      $img_src_arr = array();
      foreach ($img_data as $k => $v) {
//                $img_src = '';
//                $img_url_arr = explode('/thumb',$v['image']);
//                if(is_array($img_url_arr) && !empty($img_url_arr)){
//                    foreach($img_url_arr as $key => $value){
//                        $img_src .= $value;
//                    }
//                }
//                $img_src_arr[] = $img_src;
        $img_src_arr[] = changepic($v['image']);
      }
      $data['small_img_2'] = $img_src_arr;
      //获取外景图
      $where_cond = array(
        'cmt_id' => $cmt_id,
        'pic_type' => 3
      );
      $img_data = $this->community_model->get_all_cmt_image_by_cmtid($where_cond);
      $img_src_arr = array();
      foreach ($img_data as $k => $v) {
//                $img_src = '';
//                $img_url_arr = explode('/thumb',$v['image']);
//                if(is_array($img_url_arr) && !empty($img_url_arr)){
//                    foreach($img_url_arr as $key => $value){
//                        $img_src .= $value;
//                    }
//                }
//                $img_src_arr[] = $img_src;
        $img_src_arr[] = changepic($v['image']);
      }
      $data['small_img_3'] = $img_src_arr;
      //获取户型图
      $where_cond = array(
        'cmt_id' => $cmt_id,
        'pic_type' => 1
      );
      $img_data = $this->community_model->get_all_cmt_image_by_cmtid($where_cond);
      $img_src_arr = array();
      foreach ($img_data as $k => $v) {
//                $img_src = '';
//                $img_url_arr = explode('/thumb',$v['image']);
//                if(is_array($img_url_arr) && !empty($img_url_arr)){
//                    foreach($img_url_arr as $key => $value){
//                        $img_src .= $value;
//                    }
//                }
//                $img_src_arr[] = $img_src;
        $img_src_arr[] = changepic($v['image']);
      }
      $data['small_img_1'] = $img_src_arr;
      //获取小区环境
      $where_cond = array(
        'cmt_id' => $cmt_id,
        'pic_type' => 4
      );
      $img_data = $this->community_model->get_all_cmt_image_by_cmtid($where_cond);
      $img_src_arr = array();
      foreach ($img_data as $k => $v) {
//                $img_src = '';
//                $img_url_arr = explode('/thumb',$v['image']);
//                if(is_array($img_url_arr) && !empty($img_url_arr)){
//                    foreach($img_url_arr as $key => $value){
//                        $img_src .= $value;
//                    }
//                }
//                $img_src_arr[] = $img_src;
        $img_src_arr[] = changepic($v['image']);
      }
      $data['small_img_4'] = $img_src_arr;
      //获取内部设施
      $where_cond = array(
        'cmt_id' => $cmt_id,
        'pic_type' => 5
      );
      $img_data = $this->community_model->get_all_cmt_image_by_cmtid($where_cond);
      $img_src_arr = array();
      foreach ($img_data as $k => $v) {
//                $img_src = '';
//                $img_url_arr = explode('/thumb',$v['image']);
//                if(is_array($img_url_arr) && !empty($img_url_arr)){
//                    foreach($img_url_arr as $key => $value){
//                        $img_src .= $value;
//                    }
//                }
//                $img_src_arr[] = $img_src;
        $img_src_arr[] = changepic($v['image']);
      }
      $data['small_img_5'] = $img_src_arr;
      //获取周边环境
      $where_cond = array(
        'cmt_id' => $cmt_id,
        'pic_type' => 6
      );
      $img_data = $this->community_model->get_all_cmt_image_by_cmtid($where_cond);
      $img_src_arr = array();
      foreach ($img_data as $k => $v) {
//                $img_src = '';
//                $img_url_arr = explode('/thumb',$v['image']);
//                if(is_array($img_url_arr) && !empty($img_url_arr)){
//                    foreach($img_url_arr as $key => $value){
//                        $img_src .= $value;
//                    }
//                }
//                $img_src_arr[] = $img_src;
        $img_src_arr[] = changepic($v['image']);
      }
      $data['small_img_6'] = $img_src_arr;
    }
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/cmt_uploadpic.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,mls/js/v1.0/disk.js,mls/js/v1.0/backspace.js');

    $this->view('community/img_detail2', $data);
  }

  /**
   * 图片上传
   * @access public
   * @param  void
   * @return void
   */
  public function img_upload($id = 0)
  {
    $this->load->model('community_model');
    if (!empty($id)) {
      $cmt_data = $this->community_model->get_cmtinfo_longitude(intval($id));
      $data['cmt_data'] = $cmt_data;
    }
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/cmt_uploadpic.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,mls/js/v1.0/disk.js,mls/js/v1.0/backspace.js');

    $this->view('community/img_upload', $data);
  }


  /**
   * 修改楼盘字段 | 详情
   * @access public
   * @param  void
   * @return void
   */
  public function correct_community($id = 0)
  {
    $this->load->model('community_model');
    $user = $this->user_arr;
    $post_param = $this->input->post(NULL, TRUE);
    $cmt_id = $this->input->post('cmt_id', TRUE);
    $cmt_info = $this->community_model->find_cmt($cmt_id);
    if ($cmt_info[0]['buildarea'] != trim($post_param['buildarea'])) {
      $paramArray = array(
        'cmt_id' => $cmt_id,//楼盘id
        'cmt_name' => $cmt_info[0]['cmt_name'],//楼盘名称
        'correction_feild' => 'buildarea',//纠错字段
        'correction_feild_name' => '建筑面积',//纠错字段内容
        'correctioninfo' => $post_param['buildarea'],//纠错内容
        'creattime' => time(),//创建时间
        'ip' => $_SERVER['REMOTE_ADDR'],//客户端IP
        'esta' => 0, //信息状态（默认未审核）
        'user_id' => $user['broker_id'],//纠错人id
        'user_name' => $user['truename'],//纠错人姓名
      );
      $add_result = $this->cmt_correction_model->add_cmt_correction($paramArray);
      if (!empty($add_result) && is_int($add_result)) {
        $return_data = 'add success';
      } else {
        $return_data = 'add failed';
      }
    }
    if ($cmt_info[0]['property_year'] != trim($post_param['property_year'])) {
      $paramArray = array(
        'cmt_id' => $cmt_id,//楼盘id
        'cmt_name' => $cmt_info[0]['cmt_name'],//楼盘名称
        'correction_feild' => 'property_year',//纠错字段
        'correction_feild_name' => '产权年限',//纠错字段内容
        'correctioninfo' => $post_param['property_year'],//纠错内容
        'creattime' => time(),//创建时间
        'ip' => $_SERVER['REMOTE_ADDR'],//客户端IP
        'esta' => 0, //信息状态（默认未审核）
        'user_id' => $user['broker_id'],//纠错人id
        'user_name' => $user['truename'],//纠错人姓名
      );
      $add_result = $this->cmt_correction_model->add_cmt_correction($paramArray);
      if (!empty($add_result) && is_int($add_result)) {
        $return_data = 'add success';
      } else {
        $return_data = 'add failed';
      }
    }
    if ($cmt_info[0]['property_company'] != trim($post_param['property_company'])) {
      $paramArray = array(
        'cmt_id' => $cmt_id,//楼盘id
        'cmt_name' => $cmt_info[0]['cmt_name'],//楼盘名称
        'correction_feild' => 'property_company',//纠错字段
        'correction_feild_name' => '物业公司',//纠错字段内容
        'correctioninfo' => $post_param['property_company'],//纠错内容
        'creattime' => time(),//创建时间
        'ip' => $_SERVER['REMOTE_ADDR'],//客户端IP
        'esta' => 0, //信息状态（默认未审核）
        'user_id' => $user['broker_id'],//纠错人id
        'user_name' => $user['truename'],//纠错人姓名
      );
      $add_result = $this->cmt_correction_model->add_cmt_correction($paramArray);
      if (!empty($add_result) && is_int($add_result)) {
        $return_data = 'add success';
      } else {
        $return_data = 'add failed';
      }
    }
    if ($cmt_info[0]['parking'] != trim($post_param['parking'])) {
      $paramArray = array(
        'cmt_id' => $cmt_id,//楼盘id
        'cmt_name' => $cmt_info[0]['cmt_name'],//楼盘名称
        'correction_feild' => 'parking',//纠错字段
        'correction_feild_name' => '车位情况',//纠错字段内容
        'correctioninfo' => $post_param['parking'],//纠错内容
        'creattime' => time(),//创建时间
        'ip' => $_SERVER['REMOTE_ADDR'],//客户端IP
        'esta' => 0, //信息状态（默认未审核）
        'user_id' => $user['broker_id'],//纠错人id
        'user_name' => $user['truename'],//纠错人姓名
      );
      $add_result = $this->cmt_correction_model->add_cmt_correction($paramArray);
      if (!empty($add_result) && is_int($add_result)) {
        $return_data = 'add success';
      } else {
        $return_data = 'add failed';
      }
    }
    if ($cmt_info[0]['green_rate'] != $post_param['green_rate'] / 100) {
      $paramArray = array(
        'cmt_id' => $cmt_id,//楼盘id
        'cmt_name' => $cmt_info[0]['cmt_name'],//楼盘名称
        'correction_feild' => 'green_rate',//纠错字段
        'correction_feild_name' => '绿化率',//纠错字段内容
        'correctioninfo' => $post_param['green_rate'],//纠错内容
        'creattime' => time(),//创建时间
        'ip' => $_SERVER['REMOTE_ADDR'],//客户端IP
        'esta' => 0, //信息状态（默认未审核）
        'user_id' => $user['broker_id'],//纠错人id
        'user_name' => $user['truename'],//纠错人姓名
      );
      $add_result = $this->cmt_correction_model->add_cmt_correction($paramArray);
      if (!empty($add_result) && is_int($add_result)) {
        $return_data = 'add success';
      } else {
        $return_data = 'add failed';
      }
    }
    if ($cmt_info[0]['plot_ratio'] != trim($post_param['plot_ratio'])) {
      $paramArray = array(
        'cmt_id' => $cmt_id,//楼盘id
        'cmt_name' => $cmt_info[0]['cmt_name'],//楼盘名称
        'correction_feild' => 'plot_ratio',//纠错字段
        'correction_feild_name' => '容积率',//纠错字段内容
        'correctioninfo' => $post_param['plot_ratio'],//纠错内容
        'creattime' => time(),//创建时间
        'ip' => $_SERVER['REMOTE_ADDR'],//客户端IP
        'esta' => 0, //信息状态（默认未审核）
        'user_id' => $user['broker_id'],//纠错人id
        'user_name' => $user['truename'],//纠错人姓名
      );
      $add_result = $this->cmt_correction_model->add_cmt_correction($paramArray);
      if (!empty($add_result) && is_int($add_result)) {
        $return_data = 'add success';
      } else {
        $return_data = 'add failed';
      }
    }
    if ($cmt_info[0]['property_fee'] != trim($post_param['property_fee'])) {
      $paramArray = array(
        'cmt_id' => $cmt_id,//楼盘id
        'cmt_name' => $cmt_info[0]['cmt_name'],//楼盘名称
        'correction_feild' => 'property_fee',//纠错字段
        'correction_feild_name' => '物业费',//纠错字段内容
        'correctioninfo' => $post_param['property_fee'],//纠错内容
        'creattime' => time(),//创建时间
        'ip' => $_SERVER['REMOTE_ADDR'],//客户端IP
        'esta' => 0, //信息状态（默认未审核）
        'user_id' => $user['broker_id'],//纠错人id
        'user_name' => $user['truename'],//纠错人姓名
      );
      $add_result = $this->cmt_correction_model->add_cmt_correction($paramArray);
      if (!empty($add_result) && is_int($add_result)) {
        $return_data = 'add success';
      } else {
        $return_data = 'add failed';
      }
    }
    if ($cmt_info[0]['subway'] != trim($post_param['subway'])) {
      $paramArray = array(
        'cmt_id' => $cmt_id,//楼盘id
        'cmt_name' => $cmt_info[0]['cmt_name'],//楼盘名称
        'correction_feild' => 'subway',//纠错字段
        'correction_feild_name' => '地铁',//纠错字段内容
        'correctioninfo' => $post_param['subway'],//纠错内容
        'creattime' => time(),//创建时间
        'ip' => $_SERVER['REMOTE_ADDR'],//客户端IP
        'esta' => 0, //信息状态（默认未审核）
        'user_id' => $user['broker_id'],//纠错人id
        'user_name' => $user['truename'],//纠错人姓名
      );
      $add_result = $this->cmt_correction_model->add_cmt_correction($paramArray);
      if (!empty($add_result) && is_int($add_result)) {
        $return_data = 'add success';
      } else {
        $return_data = 'add failed';
      }
    }
    if ($cmt_info[0]['bus_line'] != trim($post_param['bus_line'])) {
      $paramArray = array(
        'cmt_id' => $cmt_id,//楼盘id
        'cmt_name' => $cmt_info[0]['cmt_name'],//楼盘名称
        'correction_feild' => 'bus_line',//纠错字段
        'correction_feild_name' => '公交线路',//纠错字段内容
        'correctioninfo' => $post_param['bus_line'],//纠错内容
        'creattime' => time(),//创建时间
        'ip' => $_SERVER['REMOTE_ADDR'],//客户端IP
        'esta' => 0, //信息状态（默认未审核）
        'user_id' => $user['broker_id'],//纠错人id
        'user_name' => $user['truename'],//纠错人姓名
      );
      $add_result = $this->cmt_correction_model->add_cmt_correction($paramArray);
      if (!empty($add_result) && is_int($add_result)) {
        $return_data = 'add success';
      } else {
        $return_data = 'add failed';
      }
    }
    if ($cmt_info[0]['floor_instruction'] != trim($post_param['floor_instruction'])) {
      $paramArray = array(
        'cmt_id' => $cmt_id,//楼盘id
        'cmt_name' => $cmt_info[0]['cmt_name'],//楼盘名称
        'correction_feild' => 'floor_instruction',//纠错字段
        'correction_feild_name' => '楼层状况',//纠错字段内容
        'correctioninfo' => $post_param['floor_instruction'],//纠错内容
        'creattime' => time(),//创建时间
        'ip' => $_SERVER['REMOTE_ADDR'],//客户端IP
        'esta' => 0, //信息状态（默认未审核）
        'user_id' => $user['broker_id'],//纠错人id
        'user_name' => $user['truename'],//纠错人姓名
      );
      $add_result = $this->cmt_correction_model->add_cmt_correction($paramArray);
      if (!empty($add_result) && is_int($add_result)) {
        $return_data = 'add success';
      } else {
        $return_data = 'add failed';
      }
    }
    if (trim($cmt_info[0]['facilities']) != trim($post_param['facilities'])) {
      $paramArray = array(
        'cmt_id' => $cmt_id,//楼盘id
        'cmt_name' => $cmt_info[0]['cmt_name'],//楼盘名称
        'correction_feild' => 'facilities',//纠错字段
        'correction_feild_name' => '楼盘配套',//纠错字段内容
        'correctioninfo' => $post_param['facilities'],//纠错内容
        'creattime' => time(),//创建时间
        'ip' => $_SERVER['REMOTE_ADDR'],//客户端IP
        'esta' => 0, //信息状态（默认未审核）
        'user_id' => $user['broker_id'],//纠错人id
        'user_name' => $user['truename'],//纠错人姓名
      );
      $add_result = $this->cmt_correction_model->add_cmt_correction($paramArray);
      if (!empty($add_result) && is_int($add_result)) {
        $return_data = 'add success';
      } else {
        $return_data = 'add failed';
      }
    }
    if ($cmt_info[0]['introduction'] != trim($post_param['introduction'])) {
      $paramArray = array(
        'cmt_id' => $cmt_id,//楼盘id
        'cmt_name' => $cmt_info[0]['cmt_name'],//楼盘名称
        'correction_feild' => 'introduction',//纠错字段
        'correction_feild_name' => '楼盘简介',//纠错字段内容
        'correctioninfo' => $post_param['introduction'],//纠错内容
        'creattime' => time(),//创建时间
        'ip' => $_SERVER['REMOTE_ADDR'],//客户端IP
        'esta' => 0, //信息状态（默认未审核）
        'user_id' => $user['broker_id'],//纠错人id
        'user_name' => $user['truename'],//纠错人姓名
      );
      $add_result = $this->cmt_correction_model->add_cmt_correction($paramArray);
      if (!empty($add_result) && is_int($add_result)) {
        $return_data = 'add success';
      } else {
        $return_data = 'add failed';
      }
    }
    echo '<script>location.href="' . MLS_URL . '/community/index";</script>';
  }

  /**
   * 添加图片
   * @access public
   * @param  void
   * @return void
   */
  public function add_img()
  {
    $this->load->model('community_model');
    $cmt_id = $this->input->get('cmt_id');
    $img_src_arr = $this->input->get('img_src');
    $img_type = $this->input->get('img_type');
    $add_img_data = array();
    if (!empty($img_src_arr) && is_array($img_src_arr)) {
      foreach ($img_src_arr as $k => $v) {
        $add_img_data[] = array(
          'cmt_id' => $cmt_id,
          'pic_type' => intval($img_type),
          'creattime' => time(),
          'ip' => $_SERVER['REMOTE_ADDR'],
          'image' => $v
        );
      }
      foreach ($add_img_data as $k => $v) {
        $addResult = $this->community_model->add_cmt_image($v);
      }
      if (!empty($addResult)) {
        echo 'add_success';
        exit;
      } else {
        echo 'add_failed';
        exit;
      }
    } else {
      echo 'add_failed';
      exit;
    }
  }

  /**
   * 根据图片类型筛选图片
   * @access public
   * @param  void
   * @return void
   */
  public function get_imgs_by_type()
  {
    $this->load->model('community_model');
    $cmt_id = $this->input->get('cmt_id');
    $img_type = $this->input->get('img_type');
    $where_cond = array(
      'cmt_id' => $cmt_id,
      'pic_type' => intval($img_type)
    );
    $img_data = $this->community_model->get_all_cmt_image_by_cmtid($where_cond);
    $img_src_arr = array();
    foreach ($img_data as $k => $v) {
      $img_src_arr[] = $v['image'];
    }
    if (!empty($img_src_arr)) {
      echo json_encode($img_src_arr);
    } else {
      echo json_encode(array('result' => 'no_img'));
    }
  }

  /**
   * 运营后台 楼栋单元门牌
   * @access public
   * @param  $cmt_id 楼盘id
   * @return void
   */
  public function admin_dong_door_unit($cmt_id = 0, $login_city = '')
  {
    $this->config->set_item('login_city', $login_city);
    $this->load->model('community_model');//楼盘模型类

    $data = array();
    $data['cmt_id'] = $cmt_id;
    //页面菜单
    $data['user_menu'] = $this->user_menu;
    //楼盘名
    $cmt_info = $this->community_model->find_cmt($cmt_id);
    $cmt_name = '';
    if (is_full_array($cmt_info)) {
      $cmt_name = $cmt_info[0]['cmt_name'];
      $is_lock = $cmt_info[0]['is_lock'];
    }
    $data['cmt_name'] = $cmt_name;
    $data['is_lock'] = $is_lock;
    //楼盘下的楼栋号
    $all_dong = $this->community_model->get_all_dong_by_cmtid($cmt_id);
    $data['all_dong_num'] = count($all_dong);
    $all_dong_unit_door = array();
    if (is_full_array($all_dong)) {
      foreach ($all_dong as $key => $value) {
        $dong_id = $value['id'];
        $all_unit = $this->community_model->get_all_unit_by_dongid($dong_id);
        //单元
        $unit_arr = array();
        if (is_full_array($all_unit)) {
          foreach ($all_unit as $key2 => $value2) {
            $unit_id = $value2['id'];
            $all_door = $this->community_model->get_all_door_by_unitid($unit_id);
            //门牌
            $door_arr = array();
            if (is_full_array($all_door)) {
              foreach ($all_door as $key3 => $value3) {
                $door_arr[] = $value3;
              }
            }
            $unit_arr[$value2['name']] = $door_arr;
          }
        }
        $all_dong_unit_door[$value['name']] = $unit_arr;
      }
    }

    $data['all_dong_unit_door'] = $all_dong_unit_door;


    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/house_new.css,'
      . 'mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/cmt_uploadpic.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,mls/js/v1.0/disk.js,mls/js/v1.0/backspace.js');
    $this->view('community/admin_dong_door_unit', $data);
  }

  /**
   * 楼栋单元门牌
   * @access public
   * @param  $dong_id 楼栋id
   * @return void
   */
  public function dong_door_unit($dong_id = 0, $cmt_id = 0)
  {
    $user = $this->user_arr;
    $company_id = 0;
    if (is_full_array($user)) {
      $company_id = intval($user['company_id']);
    }
    $this->load->model('community_model');
    $data = array();
    $data['dong_id'] = $dong_id;
    $data['cmt_id'] = $cmt_id;
    //页面菜单
    $data['user_menu'] = $this->user_menu;
    //楼盘名
    $cmt_info = $this->community_model->find_cmt($cmt_id);
    //是否锁盘，根据楼盘、公司id
    $where_cond = array(
      'cmt_id' => $cmt_id,
      'company_id' => $company_id
    );
    $cmt_lock_info = $this->community_model->get_lock_cmt($where_cond);
    $is_lock = 0;
    if (is_full_array($cmt_lock_info)) {
      $is_lock = $cmt_lock_info[0]['is_lock'];
    }
    $cmt_name = '';
    if (is_full_array($cmt_info)) {
      $cmt_name = $cmt_info[0]['cmt_name'];
    }
    $data['cmt_name'] = $cmt_name;
    $data['is_lock'] = $is_lock;
    //楼盘下的楼栋号
    $dong_where_cond = array(
      'dong_id' => $dong_id,
      'company_id' => $company_id
    );
    //该楼栋名
    $this_dong_data = $this->community_model->get_all_dong_by_param(array('id' => intval($dong_id)));
    $this_dong_name = '';
    if (is_full_array($this_dong_data)) {
      $this_dong_name = $this_dong_data[0]['name'];
    }
    $data['dong_name'] = $this_dong_name;
    //该楼栋、该公司下的所有单元
    $all_unit = $this->community_model->get_all_unit_by_param($dong_where_cond);
    //该楼栋、该公司下的所有门牌
    $all_door = $this->community_model->get_all_door_by_param($dong_where_cond);
    $data['all_dong_num'] = count($all_dong);
    $all_dong_unit_door = array();

    if (is_full_array($all_unit)) {
      foreach ($all_unit as $k => $v) {
        $door_arr = array();
        $unit_id = $v['id'];
        if ($v['dong_id'] == $dong_id) {
          foreach ($all_door as $a => $b) {
            if ($b['unit_id'] == $unit_id) {
              $door_arr[] = $b;
            }
          }
          $all_dong_unit_door[$v['name']] = $door_arr;
        }
      }
    }

    $data['all_dong_unit_door'] = $all_dong_unit_door;


    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/house_new.css,'
      . 'mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/cmt_uploadpic.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,mls/js/v1.0/disk.js,mls/js/v1.0/backspace.js');
    $this->view('community/dong_door_unit', $data);
  }

  /**
   * 楼栋单元门牌
   * @access public
   * @param  $cmt_id 楼盘id
   * @return void
   */
  public function dong_door_unit_2($cmt_id = 0)
  {
    $user = $this->user_arr;
    $company_id = 0;
    if (is_full_array($user)) {
      $company_id = intval($user['company_id']);
    }
    $this->load->model('community_model');
    $data = array();
    $data['cmt_id'] = $cmt_id;
    //页面菜单
    $data['user_menu'] = $this->user_menu;
    //楼盘名
    $cmt_info = $this->community_model->find_cmt($cmt_id);
    //是否锁盘，根据楼盘、公司id
    $where_cond = array(
      'cmt_id' => $cmt_id,
      'company_id' => $company_id
    );
    $cmt_lock_info = $this->community_model->get_lock_cmt($where_cond);
    $is_lock = 0;
    if (is_full_array($cmt_lock_info)) {
      $is_lock = $cmt_lock_info[0]['is_lock'];
    }
    $cmt_name = '';
    if (is_full_array($cmt_info)) {
      $cmt_name = $cmt_info[0]['cmt_name'];
    }
    $data['cmt_name'] = $cmt_name;
    $data['is_lock'] = $is_lock;
    //楼盘下的楼栋号
    $dong_where_cond = array(
      'cmt_id' => $cmt_id,
      'company_id' => $company_id
    );
    //该楼盘、该公司下的所有楼栋
    $all_dong = $this->community_model->get_all_dong_by_param($dong_where_cond);
    //该楼盘、该公司下的所有单元
    $all_unit = $this->community_model->get_all_unit_by_param($dong_where_cond);
    //该楼盘、该公司下的所有门牌
    $all_door = $this->community_model->get_all_door_by_param($dong_where_cond);
    $data['all_dong_num'] = count($all_dong);
    $all_dong_unit_door = array();

    if (is_full_array($all_dong)) {
      foreach ($all_dong as $key => $value) {
        $unit_arr = array();
        $dong_id = $value['id'];
        foreach ($all_unit as $k => $v) {
          $door_arr = array();
          $unit_id = $v['id'];
          if ($v['dong_id'] == $dong_id) {
            foreach ($all_door as $a => $b) {
              if ($b['unit_id'] == $unit_id) {
                $door_arr[] = $b;
              }
            }
            $unit_arr[$v['name']] = $door_arr;
          }
        }
        $all_dong_unit_door[$value['name']] = $unit_arr;
      }
    }

    $data['all_dong_unit_door'] = $all_dong_unit_door;


    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/house_new.css,'
      . 'mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/cmt_uploadpic.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,mls/js/v1.0/disk.js,mls/js/v1.0/backspace.js');
    $this->view('community/dong_door_unit_2', $data);
  }

  /**
   * 楼栋单
   * @access public
   * @param  $cmt_id 楼盘id
   * @return void
   */
  public function cmt_dong($cmt_id = 0)
  {
    $user = $this->user_arr;
    $company_id = 0;
    if (is_full_array($user)) {
      $company_id = intval($user['company_id']);
    }
    $this->load->model('community_model');
    $data = array();
    $data['cmt_id'] = $cmt_id;
    //页面菜单
    $data['user_menu'] = $this->user_menu;
    //楼盘名
    $cmt_info = $this->community_model->find_cmt($cmt_id);
    //是否锁盘，根据楼盘、公司id
    $where_cond = array(
      'cmt_id' => $cmt_id,
      'company_id' => $company_id
    );
    $cmt_lock_info = $this->community_model->get_lock_cmt($where_cond);
    $is_lock = 0;
    if (is_full_array($cmt_lock_info)) {
      $is_lock = $cmt_lock_info[0]['is_lock'];
    }
    $cmt_name = '';
    if (is_full_array($cmt_info)) {
      $cmt_name = $cmt_info[0]['cmt_name'];
    }
    $data['cmt_name'] = $cmt_name;
    $data['is_lock'] = $is_lock;
    //楼盘下的楼栋号
    $dong_where_cond = array(
      'cmt_id' => $cmt_id,
      'company_id' => $company_id
    );
    //该楼盘、该公司下的所有楼栋
    $all_dong = $this->community_model->get_all_dong_by_param($dong_where_cond);
    //该楼盘、该公司下的所有单元
    $all_unit = $this->community_model->get_all_unit_by_param($dong_where_cond);
    //该楼盘、该公司下的所有门牌
    $all_door = $this->community_model->get_all_door_by_param($dong_where_cond);
    $data['all_dong'] = $all_dong;
    $data['all_dong_num'] = count($all_dong);
    $all_dong_unit_door = array();

    if (is_full_array($all_dong)) {
      foreach ($all_dong as $key => $value) {
        $unit_arr = array();
        $dong_id = $value['id'];
        foreach ($all_unit as $k => $v) {
          $door_arr = array();
          $unit_id = $v['id'];
          if ($v['dong_id'] == $dong_id) {
            foreach ($all_door as $a => $b) {
              if ($b['unit_id'] == $unit_id) {
                $door_arr[] = $b;
              }
            }
            $unit_arr[$v['name']] = $door_arr;
          }
        }
        $all_dong_unit_door[$value['name']] = $unit_arr;
      }
    }

    $data['all_dong_unit_door'] = $all_dong_unit_door;


    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/house_new.css,'
      . 'mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/cmt_uploadpic.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,mls/js/v1.0/disk.js,mls/js/v1.0/backspace.js');
    $this->view('community/cmt_dong', $data);
  }

  /**
   * 楼栋单元门牌数据操作
   * @access public
   * @return void
   */
  public function dong_door_unit_add()
  {
    $user = $this->user_arr;
    $company_id = 0;
    if (is_full_array($user)) {
      $company_id = intval($user['company_id']);
    }
    $this->load->model('community_model');
    $cmt_arr = $this->input->get('cmt');
    $cmt_id = $this->input->get('cmt_id');
    if (is_full_array($cmt_arr) && !empty($cmt_id)) {
      foreach ($cmt_arr as $key1 => $value1) {
        $add_dong_arr = array(
          'cmt_id' => intval($cmt_id),
          'name' => $key1,
          'company_id' => $company_id
        );
        //添加楼栋
        $add_dong_arr_result = $this->community_model->add_dong($add_dong_arr);
        if ($add_dong_arr_result > 0 && is_full_array($value1)) {
          foreach ($value1 as $key2 => $value2) {
            $add_unit_arr = array(
              'dong_id' => $add_dong_arr_result,
              'name' => $key2,
              'cmt_id' => $cmt_id,
              'company_id' => $company_id
            );
            //添加单元
            $add_unit_arr_result = $this->community_model->add_unit($add_unit_arr);
            if ($add_unit_arr_result > 0 && is_full_array($value2)) {
              foreach ($value2 as $key3 => $value3) {
                $add_door_arr = array(
                  'unit_id' => $add_unit_arr_result,
                  'dong_id' => $add_dong_arr_result,
                  'name' => $value3,
                  'cmt_id' => $cmt_id,
                  'company_id' => $company_id
                );
                //添加门牌
                $add_door_arr_result = $this->community_model->add_door($add_door_arr);
              }
            }
          }
        }
      }
    } else {
      echo 'no_check';
      exit;
    }
    if (isset($add_door_arr_result) && $add_door_arr_result > 0) {
      echo 'success';
      exit;
    } else {
      echo 'failed';
      exit;
    }
  }

  /**
   * 楼栋单元门牌数据操作
   * @access public
   * @return void
   */
  public function admin_dong_door_unit_add()
  {
    $this->config->set_item('login_city', 'wuhan');
    $this->load->model('community_model');
    $cmt_arr = $this->input->get('cmt');
    $cmt_id = $this->input->get('cmt_id');
    if (is_full_array($cmt_arr) && !empty($cmt_id)) {
      foreach ($cmt_arr as $key1 => $value1) {
        $add_dong_arr = array(
          'cmt_id' => intval($cmt_id),
          'name' => $key1
        );
        //添加楼栋
        $add_dong_arr_result = $this->community_model->add_dong($add_dong_arr);
        if ($add_dong_arr_result > 0 && is_full_array($value1)) {
          foreach ($value1 as $key2 => $value2) {
            $add_unit_arr = array(
              'dong_id' => $add_dong_arr_result,
              'name' => $key2,
              'cmt_id' => $cmt_id
            );
            //添加单元
            $add_unit_arr_result = $this->community_model->add_unit($add_unit_arr);
            if ($add_unit_arr_result > 0 && is_full_array($value2)) {
              foreach ($value2 as $key3 => $value3) {
                $add_door_arr = array(
                  'unit_id' => $add_unit_arr_result,
                  'dong_id' => $add_dong_arr_result,
                  'name' => $value3,
                  'cmt_id' => $cmt_id
                );
                //添加门牌
                $add_door_arr_result = $this->community_model->add_door($add_door_arr);
              }
            }
          }
        }
      }
    } else {
      echo 'no_check';
      exit;
    }
    if (isset($add_door_arr_result) && $add_door_arr_result > 0) {
      echo 'success';
      exit;
    } else {
      echo 'failed';
      exit;
    }
  }

  /**
   * 楼栋单元门牌数据操作
   * @access public
   * @return void
   */
  public function dong_door_unit_modify()
  {
    $user = $this->user_arr;
    $company_id = 0;
    if (is_full_array($user)) {
      $company_id = intval($user['company_id']);
    }
    $this->load->model('community_model');
    $cmt_arr = $this->input->get('cmt');
    $cmt_id = $this->input->get('cmt_id');
    if (isset($cmt_id) && intval($cmt_id) > 0) {
      //楼栋单元门牌，修改操作，考虑增删改情况复杂，简单操作。
      //先删除当前楼盘下的所有楼栋、单元、门牌，然后再添加。
      $del_result = $this->community_model->delete_dong_unit_door_by_cmtid($cmt_id);
      if ($del_result) {
        if (is_full_array($cmt_arr)) {
          foreach ($cmt_arr as $key1 => $value1) {
            $add_dong_arr = array(
              'cmt_id' => intval($cmt_id),
              'name' => $key1,
              'company_id' => $company_id
            );
            //添加楼栋
            $add_dong_arr_result = $this->community_model->add_dong($add_dong_arr);
            if ($add_dong_arr_result > 0 && is_full_array($value1)) {
              foreach ($value1 as $key2 => $value2) {
                $add_unit_arr = array(
                  'dong_id' => $add_dong_arr_result,
                  'name' => $key2,
                  'cmt_id' => $cmt_id,
                  'company_id' => $company_id
                );
                //添加单元
                $add_unit_arr_result = $this->community_model->add_unit($add_unit_arr);
                if ($add_unit_arr_result > 0 && is_full_array($value2)) {
                  foreach ($value2 as $key3 => $value3) {
                    $add_door_arr = array(
                      'unit_id' => $add_unit_arr_result,
                      'dong_id' => $add_dong_arr_result,
                      'name' => $value3,
                      'cmt_id' => $cmt_id,
                      'company_id' => $company_id
                    );
                    //添加门牌
                    $add_door_arr_result = $this->community_model->add_door($add_door_arr);
                  }
                }
              }
            }
          }
        } else {
          echo 'no_check';
          exit;
        }
      }
    }

    if (isset($add_door_arr_result) && $add_door_arr_result > 0) {
      echo 'success';
      exit;
    } else {
      echo 'failed';
      exit;
    }
  }

  /**
   * 楼栋单元门牌数据操作
   * @access public
   * @return void
   */
  public function modify_dong()
  {
    $user = $this->user_arr;
    $company_id = 0;
    if (is_full_array($user)) {
      $company_id = intval($user['company_id']);
    }
    $this->load->model('community_model');
    $dong_arr = $this->input->get('dong');
    $dong_id = $this->input->get('dong_id');
    $cmt_id = $this->input->get('cmt_id');
    $dong_name = $this->input->get('dong_name');
    if (isset($dong_id) && intval($dong_id) > 0) {
      //楼栋单元门牌，修改操作，考虑增删改情况复杂，简单操作。
      //先删除当前楼栋下的所有楼栋、单元、门牌，然后再添加。
      $del_result = $this->community_model->delete_unit_door_by_dongid($dong_id);
      if ($del_result) {
        //修改楼栋名
        $update_name_arr = array(
          'name' => $dong_name
        );
        $this->community_model->update_cmt_dong_by_id($dong_id, $update_name_arr);

        if (is_full_array($dong_arr)) {
          foreach ($dong_arr as $key1 => $value1) {
            $add_unit_arr = array(
              'dong_id' => intval($dong_id),
              'name' => $key1,
              'company_id' => $company_id,
              'cmt_id' => $cmt_id
            );
            //添加单元
            $add_unit_arr_result = $this->community_model->add_unit($add_unit_arr);

            if ($add_unit_arr_result > 0 && is_full_array($value1)) {

              foreach ($value1 as $key2 => $value2) {
                $add_door_arr = array(
                  'dong_id' => $dong_id,
                  'unit_id' => $add_unit_arr_result,
                  'name' => $value2,
                  'cmt_id' => $cmt_id,
                  'company_id' => $company_id
                );
                //添加门牌
                $add_door_arr_result = $this->community_model->add_door($add_door_arr);

              }
            }
          }
        } else {
          echo 'no_check';
          exit;
        }
      }
    }

    if (isset($add_door_arr_result) && $add_door_arr_result > 0) {
      echo 'success';
      exit;
    } else {
      echo 'failed';
      exit;
    }
  }

  /**
   * 楼栋单元门牌数据操作
   * @access public
   * @return void
   */
  public function admin_dong_door_unit_modify()
  {
    $this->config->set_item('login_city', 'wuhan');
    $this->load->model('community_model');
    $cmt_arr = $this->input->get('cmt');
    $cmt_id = $this->input->get('cmt_id');
    if (isset($cmt_id) && intval($cmt_id) > 0) {
      //楼栋单元门牌，修改操作，考虑增删改情况复杂，简单操作。
      //先删除当前楼盘下的所有楼栋、单元、门牌，然后再添加。
      $del_result = $this->community_model->delete_dong_unit_door_by_cmtid($cmt_id);
      if ($del_result) {
        if (is_full_array($cmt_arr)) {
          foreach ($cmt_arr as $key1 => $value1) {
            $add_dong_arr = array(
              'cmt_id' => intval($cmt_id),
              'name' => $key1
            );
            //添加楼栋
            $add_dong_arr_result = $this->community_model->add_dong($add_dong_arr);
            if ($add_dong_arr_result > 0 && is_full_array($value1)) {
              foreach ($value1 as $key2 => $value2) {
                $add_unit_arr = array(
                  'dong_id' => $add_dong_arr_result,
                  'name' => $key2,
                  'cmt_id' => $cmt_id
                );
                //添加单元
                $add_unit_arr_result = $this->community_model->add_unit($add_unit_arr);
                if ($add_unit_arr_result > 0 && is_full_array($value2)) {
                  foreach ($value2 as $key3 => $value3) {
                    $add_door_arr = array(
                      'unit_id' => $add_unit_arr_result,
                      'dong_id' => $add_dong_arr_result,
                      'name' => $value3,
                      'cmt_id' => $cmt_id
                    );
                    //添加门牌
                    $add_door_arr_result = $this->community_model->add_door($add_door_arr);
                  }
                }
              }
            }
          }
        } else {
          echo 'no_check';
          exit;
        }
      }
    }

    if (isset($add_door_arr_result) && $add_door_arr_result > 0) {
      echo 'success';
      exit;
    } else {
      echo 'failed';
      exit;
    }
  }

  /**
   * 添加楼栋
   * @access public
   * @return void
   */
  public function add_dong($cmt_id = 0)
  {
    $data['cmt_id'] = $cmt_id;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/house_new.css,'
      . 'mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/cmt_uploadpic.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,mls/js/v1.0/disk.js,mls/js/v1.0/backspace.js');
    $this->view('community/add_dong', $data);
  }


  /**
   * 检测楼盘是否锁定
   * @access public
   * @return void
   */
  public function check_is_lock()
  {
    //当前公司id
    $company_id = intval($this->user_arr['company_id']);
    $this->load->model('community_model');
    $result_arr = array();
    $cmt_id = $this->input->get('cmt_id');
    if (isset($cmt_id) && !empty($cmt_id)) {
      $cmt_data = $this->community_model->get_cmtinfo_longitude(intval($cmt_id));
      if (is_full_array($cmt_data)) {
        //武汉站除外
        if ('37' == $this->user_arr['city_id']) {
          $is_lock = 0;
          //判断基本设置是否开启武汉锁盘
          //获取当前经济人所在门店的基本设置信息
          $company_basic_data = $this->company_basic_arr;
          //是否开启了锁盘
          $is_lock_cmt_wh = 0;
          if (is_full_array($company_basic_data)) {
            $is_lock_cmt_wh = $company_basic_data['is_lock_cmt_wh'];
          }
          if ('1' == $is_lock_cmt_wh && '1' == $cmt_data['is_lock']) {
            $is_lock = 1;
          } else {
            $is_lock = 0;
          }
        } else {
          //根据楼盘id,公司id,从锁盘表判断是否锁盘
          $where_cond = array(
            'company_id' => $company_id,
            'cmt_id' => $cmt_id
          );
          $cmt_lock_info = $this->community_model->get_lock_cmt($where_cond);
          $is_lock = 0;
          if (is_full_array($cmt_lock_info)) {
            $is_lock = $cmt_lock_info[0]['is_lock'];
          }
        }
        $result_arr['is_lock'] = $is_lock;
        //获得楼盘的楼栋、单元、门牌
        if ('1' == $is_lock) {
          if ('37' == $this->user_arr['city_id']) {
            $where_cond = array(
              'cmt_id' => $cmt_id
            );
          } else {
            $where_cond = array(
              'company_id' => $company_id,
              'cmt_id' => $cmt_id
            );
          }
          $all_dong = $this->community_model->get_all_dong_by_param($where_cond);
          if (is_full_array($all_dong)) {
            $result_arr['dong'] = $all_dong;
          }
        }
      }
    }
    echo json_encode($result_arr);
    exit;
  }

  /**
   * 锁盘 解锁
   * @access public
   * @return void
   */
  public function deal_is_lock()
  {
    $user = $this->user_arr;
    $company_id = 0;
    if (is_full_array($user)) {
      $company_id = intval($user['company_id']);
    }
    $this->load->model('community_model');
    $result_str = '';
    $cmt_id = $this->input->get('cmt_id');
    $type = $this->input->get('type');

    if (isset($cmt_id) && !empty($cmt_id) && isset($type) && !empty($type)) {
      //根据楼盘id、公司id判断是否已存在该数据
      $where_cond = array(
        'cmt_id' => $cmt_id,
        'company_id' => $company_id
      );
      $lock_cmt_data = $this->community_model->get_lock_cmt($where_cond);
      if (is_full_array($lock_cmt_data)) {
        //更新
        $id = intval($lock_cmt_data[0]['id']);
        if ('1' == $type) {
          $update_arr['is_lock'] = 1;
        } else {
          $update_arr['is_lock'] = 0;
        }
        $update_result = $this->community_model->update_cmt_lock_by_id($id, $update_arr);
        if (1 == $update_result) {
          $result_str = 'success';
        } else {
          $result_str = 'failed';
        }
      } else {
        //添加
        $add_data = array(
          'cmt_id' => $cmt_id,
          'company_id' => $company_id
        );
        if ('1' == $type) {
          $add_data['is_lock'] = 1;
        } else {
          $add_data['is_lock'] = 0;
        }
        $add_result = $this->community_model->add_cmt_lock($add_data);
        if (is_int($add_result) && $add_result > 0) {
          $result_str = 'success';
        } else {
          $result_str = 'failed';
        }
      }
    } else {
      $result_str = 'failed';
    }
    echo $result_str;
    exit;
  }

  /**
   * 锁盘 解锁
   * @access public
   * @return void
   */
  public function deal_is_lock_wh()
  {
    $this->config->set_item('login_city', 'wuhan');
    $this->load->model('community_model');
    $result_str = '';
    $cmt_id = $this->input->get('cmt_id');
    $type = $this->input->get('type');

    if (isset($cmt_id) && !empty($cmt_id) && isset($type) && !empty($type)) {
      if ('1' == $type) {
        $update_arr = array(
          'is_lock' => 1
        );
      } else {
        $update_arr = array(
          'is_lock' => 0
        );
      }

      $update_result = $this->community_model->update_cmt_by_id(intval($cmt_id), $update_arr);
      if (1 == $update_result) {
        $result_str = 'success';
      } else {
        $result_str = 'failed';
      }
    } else {
      $result_str = 'failed';
    }
    echo $result_str;
    exit;
  }

  /**
   * 根据楼栋，获得单元
   * @access public
   * @return void
   */
  public function get_unit_by_dong()
  {
    $this->load->model('community_model');
    $unit_data = array();
    $dong_id = $this->input->get('dong_id');
    if (isset($dong_id) && !empty($dong_id)) {
      $unit_data = $this->community_model->get_all_unit_by_dongid(intval($dong_id));
    }
    echo json_encode($unit_data);
    exit;
  }

  /**
   * 根据单元，获得门牌
   * @access public
   * @return void
   */
  public function get_door_by_unit()
  {
    $this->load->model('community_model');
    $door_data = array();
    $unit_id = $this->input->get('unit_id');
    if (isset($unit_id) && !empty($unit_id)) {
      $door_data = $this->community_model->get_all_door_by_unitid(intval($unit_id));
    }
    echo json_encode($door_data);
    exit;
  }

  //导入报表
  public function import()
  {
    if (!empty($_POST['sub'])) {
      $config['upload_path'] = str_replace("\\", "/", UPLOADS . DIRECTORY_SEPARATOR . 'temp');
      //目录不存在则创建目录
      if (!file_exists($config['upload_path'])) {
        $aryDirs = explode("/", substr($config['upload_path'], 0, strlen($config['upload_path'])));
        $strDir = "";
        foreach ($aryDirs as $value) {
          $strDir .= $value . "/";
          if (!@file_exists($strDir)) {
            if (!@mkdir($strDir, 0777)) {
              return "mkdirError";
            }
          }
        }
      }
      $config['file_name'] = date('YmdHis', time()) . rand(1000, 9999);
      $config['allowed_types'] = 'xlsx|xls';
      $config['max_size'] = "2000";
      $this->load->library('upload', $config);
      //打印成功或错误的信息
      if ($this->upload->do_upload('upfile')) {
        $upload_data = $this->upload->data();
        $filename = 'temp/' . $upload_data['file_name'];
        $result = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css"></head><body style="background:#F2F2F2;"><p class="up_m_b_date_up" style="text-align: center;"><span class="up_s">上传成功</span><input type="hidden" id=path value=' . $filename . '></p></body></html>';
      } else {
        $result = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css"></head><body style="background:#F2F2F2;"><p class="up_m_b_date_up" style="text-align: center;"><span class="up_e">上传失败</span>，请选择文件上传</p></body></html>';
      }
      echo $result;

    }
  }

  /**
   * 确定导入
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function sure()
  {
    $result = array();
    $path = $_POST['id'];
    $cmt_id = $_POST['broker_id'];
    $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
    $objReader = IOFactory::createReaderForFile($path);
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($path);
    $objWorksheet = $objPHPExcel->getActiveSheet();
    $highestRow = $objWorksheet->getHighestRow();

    $highestColumn = $objWorksheet->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $excelData = array();
    for ($row = 2; $row <= $highestRow; $row++) {
      for ($col = 0; $col < $highestColumnIndex; $col++) {
        $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
      }
    }

    if (is_full_array($excelData)) {
      $this->load->model('community_model');
      $dong_id = '';
      $unit_id = '';
      $door_id = '';

      foreach ($excelData as $value) {
        //楼栋信息处理
        if ($value[0] && $value[1] && $value[2]) {
          if ($value[0] != $dong_id) {
            $dong_sql = array(
              'cmt_id' => $cmt_id,
              'name' => $value[0],
              'company_id' => $this->user_arr['company_id']
            );
            $dong_result = $this->community_model->get_dong($dong_sql);
            if (is_full_array($dong_result)) {
              $dong_id = $dong_result[0]['id'];
            } else {
              //添加楼栋
              $dong_id = $this->community_model->add_dong($dong_sql);
            }

            //单元信息处理
            $unit_sql = array(
              'cmt_id' => $cmt_id,
              'dong_id' => $dong_id,
              'name' => $value[1],
              'company_id' => $this->user_arr['company_id']
            );
            $unit_result = $this->community_model->get_unit($unit_sql);
            if (is_full_array($unit_result)) {
              $unit_id = $unit_result[0]['id'];
            } else {
              //添加单元
              $unit_id = $this->community_model->add_unit($unit_sql);
            }

            //门牌信息处理
            $door_sql = array(
              'cmt_id' => $cmt_id,
              'dong_id' => $dong_id,
              'unit_id' => $unit_id,
              'name' => $value[2],
              'company_id' => $this->user_arr['company_id']
            );
            $door_result = $this->community_model->get_door($door_sql);
            if (is_full_array($door_result)) {
              $door_id = $door_result[0]['id'];
            } else {
              //添加门牌
              $door_id = $this->community_model->add_door($door_sql);
            }

          } else {
            //单元信息处理
            if ($value[1] != $unit_id) {
              $unit_sql = array(
                'cmt_id' => $cmt_id,
                'dong_id' => $dong_id,
                'name' => $value[1],
                'company_id' => $this->user_arr['company_id']
              );
              $unit_result = $this->community_model->get_unit($unit_sql);
              if (is_full_array($unit_result)) {
                $unit_id = $unit_result[0]['id'];
              } else {
                //添加单元
                $unit_id = $this->community_model->add_unit($unit_sql);
              }

              //门牌信息处理
              $door_sql = array(
                'cmt_id' => $cmt_id,
                'dong_id' => $dong_id,
                'unit_id' => $unit_id,
                'name' => $value[2],
                'company_id' => $this->user_arr['company_id']
              );
              $door_result = $this->community_model->get_door($door_sql);
              if (is_full_array($door_result)) {
                $door_id = $door_result[0]['id'];
              } else {
                //添加门牌
                $door_id = $this->community_model->add_door($door_sql);
              }
            } else {
              //门牌信息处理
              if ($value[2] != $door_id) {
                $door_sql = array(
                  'cmt_id' => $cmt_id,
                  'dong_id' => $dong_id,
                  'unit_id' => $unit_id,
                  'name' => $value[2],
                  'company_id' => $this->user_arr['company_id']
                );
                $door_result = $this->community_model->get_door($door_sql);
                if (is_full_array($door_result)) {
                  $door_id = $door_result[0]['id'];
                } else {
                  //添加门牌
                  $door_id = $this->community_model->add_door($door_sql);
                }
              }
            }
          }
        }
        $result['status'] = 'ok';
        $result['success'] = '导入成功';
      }
    } else {
      $result['status'] = 'error';
      $result['error'] = '导入失败';
    }
    unlink($path); //删除文件
    echo json_encode($result);
  }

}

/* End of file community.php */
/* Location: ./applications/mls/controllers/community.php */
