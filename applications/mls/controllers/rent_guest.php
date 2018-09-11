<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 城市 Class
 *
 * 城市控制器
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      Lion
 */
class rent_guest extends MY_Controller
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
    //加载区属模型类
    $this->load->model('district_model');
    //加载楼盘模型类
    $this->load->model('community_model');
    //表单验证
    $this->load->library('form_validation');
    //加载客户MODEL
    $this->load->model('rent_house_model');
    $this->load->model('broker_model');

    error_reporting(E_ALL || ~E_NOTICE);
  }


  //公盘出售列表
  public function lists($page = 1)
  {
    // 判断是否登录
    $this->load->model('broker_model');
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);


    //模板使用数据
    $data = array();

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $blockname = $this->input->post('blockname', true);
    $data['post_param'] = $post_param;
    //print_R($post_param);exit;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);


    //查询房源条件
    $cond_where = "isshare = 1 AND status != 5";

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;

    //排序字段
    $roomorder = intval($post_param['orderby_id']);
    $order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $this->_total_count =
      $this->rent_house_model->get_count_by_cond($cond_where);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->rent_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, $order_arr['order_key'], $order_arr['order_by']);

    $this->load->model('broker_info_model');
    $brokeridstr = '';
    if ($list) {
      foreach ($list as $key => $val) {
        $brokeridstr .= $val['broker_id'] . ',';
        $brokerinfo = $this->broker_info_model->get_baseinfo_by_broker_id($val['broker_id']);
        $list[$key]['telno'] = $brokerinfo['phone'];
        $list[$key]['broker_name'] = $brokerinfo['truename'];
        $list[$key]['agency_name'] = $brokerinfo['agency_name'];
        // 最新跟进时间
        $list[$key]['genjintime'] = date('Y-m-d H:i:s', $val['updatetime']);
      }
    }
    $data['list'] = $list;

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();

    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $data['district'][$val['id']] = $val;
    }
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $data['street'][$val['id']] = $val;
    }

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
    //页面标题
    $data['page_title'] = '公盘出售列表页';
    $data['page_list'] = $this->page_list->show('jump');

    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/guest_disk.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/disk.js,mls/js/v1.0/backspace.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js');

    //加载发布页面模板
    $this->view('guest/rent_guest', $data);


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
   * 公盘出售列表条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = '';
    //房源编号
    if (isset($form_param['house_id']) && $form_param['house_id'] > 0) {
      $house_id = intval($form_param['house_id']);
      $cond_where .= " AND id = '" . $house_id . "'";
      return $cond_where;
    }

    //板块 ，区属
    $street = intval($form_param['street']);
    $district = intval($form_param['district']);
    if ($street) {
      $cond_where .= " AND street_id = '" . $street . "'";
    } elseif ($district) {
      $cond_where .= " AND district_id = '" . $district . "'";
    }

    //楼盘
    if (!empty($form_param['block_name']) && $form_param['block_id'] > 0) {
      $cond_where .= " AND block_id = '" . $form_param['block_id'] . "'";
    }

    //面积条件
    if (!empty($form_param['areamin'])) {
      $areamin = $form_param['areamin'];
      if (!is_numeric($areamin)) {
        echo "<script>alert('面积需填写整数哦！');history.go(-1);</script>";
        exit;
      }
      $cond_where .= " AND buildarea >= '" . $areamin . "'";
    }
    if (!empty($form_param['areamax'])) {
      $areamax = $form_param['areamax'];
      if (!is_numeric($areamax)) {
        echo "<script>alert('面积需填写整数哦！');history.go(-1);</script>";
        exit;
      }
      $cond_where .= " AND buildarea <= '" . $areamax . "'";
    }
    if (($form_param['areamax'] > 0 && $form_param['areamin'] > 0) && $form_param['areamax'] < $form_param['areamin']) {
      echo "<script>alert('您输入的面积区间有误，请修改后再试试！');history.go(-1);</script>";
      exit;
    }

    //价格条件
    if (!empty($form_param['pricemin'])) {
      $pricemin = $form_param['pricemin'];
      if (!is_numeric($pricemin)) {
        echo "<script>alert('价格需填写整数哦！');history.go(-1);</script>";
        exit;
      }
      $cond_where .= " AND price >= '" . $pricemin . "'";
    }
    if (!empty($form_param['pricemax'])) {
      $pricemax = $form_param['pricemax'];
      if (!is_numeric($pricemax)) {
        echo "<script>alert('价格需填写整数哦！');history.go(-1);</script>";
        exit;
      }
      $cond_where .= " AND price <= '" . $pricemax . "'";
    }
    if (($form_param['pricemax'] > 0 && $form_param['pricemin'] > 0) && $form_param['pricemax'] < $form_param['pricemin']) {
      echo "<script>alert('您输入的价格区间有误，请修改后再试试！');history.go(-1);</script>";
      exit;
    }

    //物业类型条件
    if (isset($form_param['rent_type']) && !empty($form_param['rent_type']) && $form_param['rent_type'] > 0) {
      $rent_type = intval($form_param['rent_type']);
      $cond_where .= " AND rent_type = '" . $rent_type . "'";
    }

    //户型条件
    if (isset($form_param['room']) && !empty($form_param['room']) && $form_param['room'] > 0) {
      $room = intval($form_param['room']);
      $cond_where .= " AND room = '" . $room . "'";
    }

    //状态条件
    if (isset($form_param['status']) && !empty($form_param['status']) && $form_param['status'] > 0) {
      $status = intval($form_param['status']);
      $cond_where .= " AND status = '" . $status . "'";
    }


    //性质条件
    if (isset($form_param['nature']) && !empty($form_param['nature']) && $form_param['nature'] > 0) {
      $nature = intval($form_param['nature']);
      $cond_where .= " AND nature = '" . $nature . "'";
    }

    //状态条件
    if (isset($form_param['status']) && !empty($form_param['status']) && $form_param['status'] > 0) {
      $status = intval($form_param['status']);
      $cond_where .= " AND status = '" . $status . "'";
    }
    //时间范围
    if (isset($form_param['searchtime']) && !empty($form_param['searchtime'])) {

      $searchtime = intval($form_param['searchtime']);
      $now_time = time();
      switch ($searchtime) {
        case '1':
          $creattime = $now_time - 86400 * 30;
          break;

        case '2':
          $creattime = $now_time - 86400 * 90;
          break;

        case '3':
          $creattime = $now_time - 86400 * 180;
          break;

        case '4':
          $creattime = $now_time - 86400 * 360;
          break;

        default :
          $creattime = $now_time - 86400 * 180;
      }

      $cond_where .= " AND createtime>= '" . $creattime . "' ";
    }


    return $cond_where;


  }


  /**
   * 页面ajax请求根据属区获得对应板块
   * @access  public
   * @param  int 区属id
   * @return  array
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
        $arr_order['order_key'] = 'buildyear';
        $arr_order['order_by'] = 'DESC';
        break;
      case 4:
        $arr_order['order_key'] = 'buildyear';
        $arr_order['order_by'] = 'ASC';
        break;
      case 5:
        $arr_order['order_key'] = 'buildarea';
        $arr_order['order_by'] = 'ASC';
        break;
      case 6:
        $arr_order['order_key'] = 'buildarea';
        $arr_order['order_by'] = 'DESC';
        break;
      case 7:
        $arr_order['order_key'] = 'price';
        $arr_order['order_by'] = 'ASC';
        break;
      case 8:
        $arr_order['order_key'] = 'price';
        $arr_order['order_by'] = 'DESC';
        break;
      case 9:
        $arr_order['order_key'] = 'avgprice';
        $arr_order['order_by'] = 'ASC';
        break;
      case 10:
        $arr_order['order_key'] = 'avgprice';
        $arr_order['order_by'] = 'DESC';
        break;
      default:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
    }

    return $arr_order;
  }


  public function details($house_id)
  {
    $house_id = intval($house_id);

    $house_info = array();

    if ($house_id <= 0) {
      //$this->jump(MLS_URL.'/rent/lists/', '没有发现您查询的记录');
      //return;
    }

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();

    $this->rent_house_model->set_id($house_id);
    $house_info = $this->rent_house_model->get_info_by_id();
    $house_info['district_name'] = $this->district_model->get_distname_by_id($house_info['district_id']);
    $house_info['street_name'] = $this->district_model->get_streetname_by_id($house_info['street_id']);

    $house_info['setting_arr'] = explode(',', $house_info['setting']);
    $house_info['equipment_arr'] = explode(',', $house_info['equipment']);
    $house_info['shop_trade_arr'] = explode(',', $house_info['shop_trade']);


    $this->load->model('pic_model');
    $picinfo = $this->pic_model->find_house_pic_by('rent_house', $house_id);
    $shineipic = $huxingpic = array();
    if ($picinfo) {
      foreach ($picinfo as $key => $val) {
        if ($val['type'] == 1) {
          $shineipic[] = $val;
        } else {
          $huxingpic[] = $val;
        }
      }
    }
    if ($shineipic[0]) {
//            $house_info['shinei'] = str_replace('/thumb/','/',$shineipic[0]['url']);
      $house_info['shinei'] = changepic($shineipic[0]['url']);
    }
    if ($huxingpic[0]) {
//            $house_info['huxing'] = str_replace('/thumb/','/',$huxingpic[0]['url']);
      $house_info['huxing'] = changepic($huxingpic[0]['url']);
    }
    //获取小区信息
    $community_info = $this->community_model->find_cmt($house_info['block_id']);
    $community_arr = array();
    foreach ($community_info as $key => $val) {
      $community_arr['id'] = $val['id'];//id
      $community_arr['address'] = $val['address'];//楼盘地址
      $community_arr['build_type'] = $val['build_type'];//物业类型
      $community_arr['build_date'] = $val['build_date'];//建筑年代
      $community_arr['property_year'] = $val['property_year'];//产权年限
      $community_arr['buildarea'] = $val['buildarea'];//建筑面积
      $community_arr['coverarea'] = $val['coverarea'];//	占地面积
      $community_arr['property_company'] = $val['property_company'];//物业公司
      $community_arr['developers'] = $val['developers'];//开发商
      $community_arr['parking'] = $val['parking'];//车位
      $community_arr['green_rate'] = $val['green_rate'];//绿化率
      $community_arr['plot_ratio'] = $val['plot_ratio'];//容积率
      $community_arr['property_fee'] = $val['property_fee'];//物业费
      $community_arr['build_num'] = $val['build_num'];//总栋数
      $community_arr['total_room'] = $val['total_room'];//总户数
      $community_arr['floor_instruction'] = $val['floor_instruction'];//楼层情况
      $community_arr['introduction'] = $val['introduction'];//楼盘介绍
      $community_arr['facilities'] = $val['facilities'];//设施
    }
    //获取楼盘图片
    $this->load->model('cmt_correction_base_model');
    $cmt_arr = $this->cmt_correction_base_model->find_cmt_pic_by($house_info['block_id']);
    $data['cmt_arr'] = $cmt_arr;
    $data['build_type'] = (explode('#', $community_arr['build_type']));
    $data['facilities'] = explode('#', $community_arr['facilities']);
    $data['community_info'] = $community_arr;

    $data['data_info'] = $house_info;
    $data['huxingpic'] = $huxingpic;
    $data['shineipic'] = $shineipic;

    //页面标题
    $data['page_title'] = '公盘出售信息详情页';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');

    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/scrollPic.js');
    //加载页面
    $this->view('guest/rent_guest_info', $data);

  }

  /**
   * 删除 公盘出售
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
      $arr = array('status' => 5);
      $cond_where = "id in (0," . $str . ")";
      $this->rent_house_model->update_info_by_cond($arr, $cond_where);
    }
    if ($isajax) {
      echo json_encode(array('result' => 'ok'));
    } else {
      $this->jump(MLS_URL . '/rent/lists/', '删除成功');
    }
  }


  public function match($house_id)
  {
    $house_id = intval($house_id);
    $house_info = array();

    if ($house_id <= 0) {
      $this->jump(MLS_URL . '/rent/lists/', '没有发现您查询的记录');
      return;
    }

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();

    $this->rent_house_model->set_id($house_id);
    $house_info = $this->rent_house_model->get_info_by_id();
    $house_info['district_name'] = $this->district_model->get_distname_by_id($house_info['district_id']);
    $house_info['street_name'] = $this->district_model->get_streetname_by_id($house_info['street_id']);
    $this->load->model('broker_info_model');
    $brokerinfo = $this->broker_info_model->get_baseinfo_by_broker_id($house_info['broker_id']);
    $house_info['phone'] = $brokerinfo['phone'];
    $house_info['broker_name'] = $brokerinfo['truename'];
    $house_info['agency_name'] = $brokerinfo['agency_name'];

    $data['house_info'] = $house_info;


    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $data['street'][$val['id']] = $val;
    }
    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $data['district'][$val['id']] = $val;
    }

    $cond_where = '';

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    if (empty($post_param)) {
      $post_param['searchrange'] = 1;
      $post_param['searchtime'] = 3;
      //物业类型
      $post_param['rent_type'] = $house_info['rent_type'];
      //户型条件
      $post_param['room'] = $house_info['room'];
      //区属
      $post_param['district_id'] = $house_info['district_id'];
      //板块
      $post_param['street_id'] = 0;
    }
    $data['post_param'] = $post_param;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page, 3);

    //查询房源条件
    $cond_where = 'id > 0 ';
    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cusmoter_cond_str($post_param);
    $cond_where .= $cond_where_ext;


    //排序字段
    $roomorder = intval($post_param['orderby_id']);
    $order_arr = $this->_get_orderby_arr($roomorder);

    //加载客源MODEL
    $this->load->model('buy_customer_model');
    //符合条件的总行数
    $this->_total_count = $this->buy_customer_model->get_buynum_by_cond($cond_where);
    $data['total_count'] = $this->_total_count;

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $customer_list =
      $this->buy_customer_model->get_buylist_by_cond($cond_where, $this->_offset,
        $this->_limit, $order_arr['order_key'], $order_arr['order_by']);
    //print_R($customer_list);

    //循环获取经纪人姓名和门店信息
    if (count($customer_list) > 0) {
      //经纪人帐号
      $broker_id_arr = array();
      foreach ($customer_list as $key => $value) {
        $customer_list[$key]['genjintime'] = date('Y-m-d H:i:s', $value['updatetime']);
        $broker_id = intval($value['broker_id']);
        if ($broker_id > 0 && !in_array($broker_id, $broker_id_arr)) {
          array_push($broker_id_arr, $broker_id);
        }
      }
      //经纪人MODEL
      $this->load->model('broker_info_model');
      $broker_num = count($broker_id_arr);
      for ($i = 0; $i < $broker_num; $i++) {
        $broker_arr = $this->broker_info_model->get_baseinfo_by_broker_id($broker_id_arr[$i]);
        $customer_broker_info[$broker_id_arr[$i]] = $broker_arr;
      }
      $data['customer_broker_info'] = $customer_broker_info;
    }
    $data['customer_list'] = $customer_list;

    //加载求购客户MODEL
    $this->load->model('buy_customer_model');

    //获取求购信息基本配置资料
    $conf_customer = $this->buy_customer_model->get_base_conf();
    $data['conf_customer'] = $conf_customer;

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


    //页面标题
    $data['page_title'] = '公盘出售房源匹配';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');

    //加载发布页面模板
    $this->view('guest/rent_guest_match', $data);
  }


  /**
   * 出售匹配条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cusmoter_cond_str($form_param)
  {
    $cond_where = '';
    //$post_param['searchrange'] = 1;

    //楼盘条件
    $block_id = intval($form_param['search_block_id']);
    if ($block_id) {
      $cond_where .= " AND ( cmt_id1 = '" . $block_id . "' OR cmt_id2 = '" . $block_id . "' OR cmt_id3 = '" . $block_id . "' ) ";
    }

    //户型条件
    $room = intval($form_param['room']);
    if ($room) {
      $cond_where .= " AND room_max >= '" . $room . "' AND room_min <= '" . $room . "' ";
    }
    //区属
    $district_id = intval($form_param['district_id']);
    //板块
    $street_id = intval($form_param['street_id']);
    if ($street_id) {
      $cond_where .= " AND ( street_id1 = '" . $street_id . "' OR street_id2 = '" . $street_id . "' OR  street_id2 = '" . $street_id . "' )";
    } elseif ($district_id) {
      $cond_where .= " AND ( dist_id1 = '" . $district_id . "' OR dist_id2 = '" . $district_id . "' OR  dist_id3 = '" . $district_id . "' )";
    }

    //物业类型
    $rent_type = intval($form_param['rent_type']);
    if ($rent_type) {
      $cond_where .= " AND property_type = '" . $rent_type . "' ";
    }

    //时间范
    $searchtime = intval($form_param['searchtime']);
    $now_time = time();
    switch ($searchtime) {
      case '1':
        $creattime = $now_time - 86400 * 30;
        break;

      case '2':
        $creattime = $now_time - 86400 * 90;
        break;

      case '3':
        $creattime = $now_time - 86400 * 180;
        break;

      case '4':
        $creattime = $now_time - 86400 * 360;
        break;

      default :
        $creattime = $now_time - 86400 * 180;
    }

    $cond_where .= " AND creattime>= '" . $creattime . "' ";

    return $cond_where;
  }


}


?>
