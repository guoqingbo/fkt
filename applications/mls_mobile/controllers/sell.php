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
class Sell extends MY_Controller
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
  private $_broker_id = 0;

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
    $this->load->model('buy_customer_model');
    $this->load->model('pic_model');
    $this->load->model('follow_model');
    $this->load->model('sell_house_model');
    $this->load->model('rent_house_model');
    $this->load->model('broker_model');
    $this->load->model('house_collect_model');
    $this->load->model('sell_model');
    $this->load->model('api_broker_model');
    $this->load->model('house_config_model');
    $this->load->model('agency_model');
    $this->load->model('broker_info_model');
    $this->load->model('cooperate_friends_base_model');
    $this->load->model('operate_log_model');


    //权限
    if (is_full_array($this->user_arr)) {
      $this->load->model('broker_permission_model');
      $this->broker_permission_model->set_broker_id($this->user_arr['broker_id'], $this->user_arr['company_id']);
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
    }
  }

  public function lists()
  {
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    //模板使用数据
    $data = array();
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];
    //获取房源默认排序字段
    $house_list_order_field = $company_basic_data['house_list_order_field'];
    //获取默认查询时间
    $sell_house_query_time = $company_basic_data['sell_house_query_time'];

    //新权限
    //范围（1公司2门店3个人）
    if ($company_id) {
      $view_other_per_data = $this->broker_permission_model->check('1');
      if ($company_id && $view_other_per_data['auth']) {
        $data['view_other_per'] = true;
      } else {
        $data['view_other_per'] = false;
      }
    } else {
      $data['view_other_per'] = false;
    }

    //获取出售信息基本配置资料
    $config = $this->house_config_model->get_config();
    //get参数
    $get_param = $this->input->get(NULL, TRUE);

    /** 分页参数 */
    $page = isset($get_param['page']) ? intval($get_param['page']) : 1;
    $pagesize = isset($get_param['pagesize']) ? intval($get_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);

    //根据经济人总公司编号获取全部分店信息
    //获取全部分公司信息
    $agency_list = $this->api_broker_model->get_agencys_by_company_id($company_id);
    //查询房源条件
    $cond_where = "company_id = " . $company_id;
    //获取当前经纪人所在门店的数据范围
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_view_house');
    $all_access_agency_ids = '';
    if (is_full_array($access_agency_ids_data)) {
      foreach ($access_agency_ids_data as $k => $v) {
        $all_access_agency_ids .= $v['sub_agency_id'] . ',';
      }
      $all_access_agency_ids .= $this->user_arr['agency_id'];
      $all_access_agency_ids = trim($all_access_agency_ids, ',');
    } else {
      $all_access_agency_ids = $this->user_arr['agency_id'];
    }
    $cond_where .= " AND agency_id in (" . $all_access_agency_ids . ")";

    //基本设置默认查询时间
    if ($get_param['create_time_range'] == 0) {
      //半年
      if ('1' == $sell_house_query_time) {
        $half_year_time = intval(time() - 365 * 0.5 * 24 * 60 * 60);
        $cond_where .= " AND createtime>= '" . $half_year_time . "' ";
      }
      //一年
      if ('2' == $sell_house_query_time) {
        $one_year_time = intval(time() - 365 * 24 * 60 * 60);
        $cond_where .= " AND createtime>= '" . $one_year_time . "' ";
      }
    }
    //查询有房要合作条件
    if (isset($get_param['house_type']) && $get_param['house_type'] == 1) {
      $cond_where .= " and isshare = 0 and status = 1 and broker_id = " . $broker_id;
    }
    //默认状态为有效
    if (!isset($get_param['status'])) {
      $get_param['status'] = 1;
    }

    //发布朋友圈筛选项和是否合作关系
    if ($get_param['isshare'] == '0') {
      $get_param['isshare_friend'] = 0;
    }

    $data['agency_id'] = $broker_info['agency_id'];
    if ($data['view_other_per']) {
      //如果有权限，赋予初始查询条件
      if (!isset($get_param['agency_id'])) {
        $get_param['agency_id'] = $broker_info['agency_id'];
      }
      if (!isset($get_param['broker_id'])) {
        $get_param['broker_id'] = $broker_info['broker_id'];
      }

      //获取全部分公司信息
      if (!empty($company_id)) {
        $data['agency_list'] = $this->api_broker_model->get_agencys_by_company_id($company_id);
        if ($get_param['agency_id']) {
          $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($get_param['agency_id']);
        }
      }
    } else {
      //本人
      $get_param['broker_id'] = $this->user_arr['broker_id'];
    }

    $cond_where_ext = $this->_get_cond_str($get_param);
    $cond_where .= $cond_where_ext;

    //获取有多少共享房源
    if (!empty($get_param['house_type']) && $get_param['house_type'] == 1) {
      //$where_share=array('isshare'=>'0','broker_id'=>$broker_id,'status'=>'5','status'=>'1');
      $share_num = $this->sell_house_model->get_housenum_by_cond($cond_where);
      $data['share_num'] = $share_num;
    }

    //设置默认排序字段
    if ('1' == $house_list_order_field) {
      $default_order = 13;
    } else if ('2' == $house_list_order_field) {
      $default_order = 7;
    } else {
      $default_order = 0;
    }
    $roomorder = (isset($get_param['orderby_id']) && $get_param['orderby_id'] != '') ? intval($get_param['orderby_id']) : $default_order;
    $order_arr = $this->_get_orderby_arr($roomorder);

    //设置查询字段
    $fileld = array('id', 'block_id', 'broker_id', 'broker_name', 'nature', 'block_name', 'district_id', 'street_id', 'sell_type', 'fitment', 'buildarea', 'price', 'address', 'room', 'hall', 'title', 'keys', 'isshare', 'entrust', 'pic', 'floor_type', 'floor', 'subfloor', 'totalfloor', 'toilet', 'is_outside', 'status', 'video_id', 'video_pic', 'telno1', 'telno2', 'telno3', 'isshare_friend');
    $this->sell_house_model->set_search_fields($fileld);
    //获取列表内容
    $list = $this->sell_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, $order_arr['order_key'], $order_arr['order_by']);
    $sell_list = array();
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $buy_street[$val['id']] = $val;
    }

    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $buy_district[$val['id']] = $val;
    }
    if ($list) {
      foreach ($list as $key => $val) {
        $sell_list[$key]['house_id'] = $val['id'];//房源id
        $sell_list[$key]['broker_id'] = $val['broker_id'];//经纪人id
        $sell_list[$key]['broker_name'] = $val['broker_name'];//经纪人姓名
        $sell_list[$key]['title'] = $val['block_name'];//小区名字
        $sell_list[$key]['nature'] = $config['nature'][$val['nature']];//公盘私盘
        $sell_list[$key]['keys'] = $val['keys'];//有无钥匙
        if (isset($config['entrust'][$val['entrust']]) && $val['entrust'] == 1) {
          $sell_list[$key]['entrust'] = 1;
        } else {
          $sell_list[$key]['entrust'] = 0;
        }
        $sell_list[$key]['is_share'] = $val['isshare'];//是否合作
        $sell_list[$key]['is_share_friend'] = $val['isshare_friend'];//是否合作朋友圈
        $sell_list[$key]['property_type'] = $config['sell_type'][$val['sell_type']];//出售类型
        $sell_list[$key]['block_name_address'] = $buy_district[$val['district_id']]['district'] . '-' . $buy_street[$val['street_id']]['streetname'];//区属-板块
        $sell_list[$key]['fitment'] = $config['fitment'][$val['fitment']];//装修程度
        $sell_list[$key]['room_hall'] = $val['room'] . '室' . $val['hall'] . '厅' . $val['toilet'] . '卫';//几室几厅
        $sell_list[$key]['buildarea'] = strip_end_0($val['buildarea']);//面积
        $sell_list[$key]['price'] = strip_end_0($val['price']);//售价
        $sell_list[$key]['pic_url'] = $val['pic'];
        //楼层
        if ($val['floor_type'] == 1) {
          $sell_list[$key]['storey_floor'] = $val['floor'] . '/' . $val['totalfloor'];
        }
        if ($val['floor_type'] == 2) {
          $sell_list[$key]['storey_floor'] = $val['floor'] . '-' . $val['subfloor'] . '/' . $val['totalfloor'];
        }
        if (!empty($get_param['house_type']) && $get_param['house_type'] == 1) {
          $sell_list[$key]['share_num'] = $data['share_num'];
        }
        $sell_list[$key]['is_outside'] = $val['is_outside'];
        $sell_list[$key]['status'] = $val['status'];
        $sell_list[$key]['video_id'] = $val['video_id'];
        $sell_list[$key]['video_pic'] = $val['video_pic'];
      }

    }

    $data['sell_list'] = $sell_list;
    if ($sell_list) {
      $this->result(1, '查询出售房源列表成功', $data);
    } else {
      $this->result(1, '没有相关房源数据', $data);
    }

  }

  //合作朋友圈
  public function friend_lists_pub()
  {

    $this->lists_pub('friend');
  }

  //合作中心出售房源列表信息
  public function lists_pub($friend = '')
  {
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $company_id = $broker_info['company_id'];
    //模板使用数据
    $data = array();
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];

    //获取出售信息基本配置资料
    $config = $this->house_config_model->get_config();

    //get参数
    $get_param = $this->input->get(NULL, TRUE);
    /** 分页参数 */
    $page = isset($get_param['page']) ? intval($get_param['page']) : 1;
    $pagesize = isset($get_param['pagesize']) ? intval($get_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);
    //查询房源条件
    if ($friend == 'friend') {
      $cond_where = "isshare = 1 AND status = 1 AND isshare_friend = 1";
    } else {
      $cond_where = "isshare = 1 AND status = 1 AND isshare_friend = 0";
    }

    if ($get_param['home'] && $broker_info['agency_id']) {
      $agency_info = $this->agency_model->get_one_by(array('id' => $broker_info['agency_id']));
      $cond_where .= " and district_id = " . $agency_info['dist_id'];
    }
    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($get_param);

    if ($get_param) {
      $cond_where .= $cond_where_ext;
    }

    //排序字段
    $roomorder = '';
    if (isset($get_param['orderby_id']) && !empty($get_param['orderby_id'])) {
      $roomorder = intval($get_param['orderby_id']);
    } else {
      $roomorder = 17;
    }
    $order_arr = $this->_get_orderby_arr($roomorder);

    //设置查询字段
    $fileld = array('id', 'block_id', 'broker_id', 'nature', 'block_name', 'district_id', 'street_id', 'sell_type', 'fitment', 'buildarea', 'price', 'address', 'room', 'hall', 'title', 'keys', 'isshare', 'entrust', 'pic', 'cooperate_reward', 'house_degree', 'reward_type', 'video_id', 'video_pic', 'commission_ratio', 'floor', 'totalfloor', 'sell_type');
    $this->sell_house_model->set_search_fields($fileld);
    //获取列表内容
    $list = $this->sell_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, $order_arr['order_key'], $order_arr['order_by'], 'set_share_time', 'desc');
    $sell_list = array();
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $buy_street[$val['id']] = $val;
    }

    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $buy_district[$val['id']] = $val;
    }
    if ($list) {
      foreach ($list as $key => $val) {
        $sell_list[$key]['house_id'] = $val['id'];//房源id
        $sell_list[$key]['broker_id'] = $val['broker_id'];//经纪人id
        $sell_list[$key]['title'] = $val['block_name'];//标题
        $sell_list[$key]['nature'] = $config['nature'][$val['nature']];//公盘私盘
        $sell_list[$key]['keys'] = $val['keys'];//有无钥匙
        if ($val['entrust'] == 1) {
          $sell_list[$key]['entrust'] = 1;
        } else {
          $sell_list[$key]['entrust'] = 2;
        }
        $sell_list[$key]['is_share'] = $val['isshare'];//是否合作
        $sell_list[$key]['cooperate_reward'] = $val['cooperate_reward'];//合作悬赏
        $sell_list[$key]['property_type'] = $config['sell_type'][$val['sell_type']];//出售类型
        $sell_list[$key]['block_name_address'] = $buy_district[$val['district_id']]['district'] . '-' . $buy_street[$val['street_id']]['streetname'];//小区名称地址
        if ($val['fitment']) {
          $sell_list[$key]['fitment'] = $config['fitment'][$val['fitment']];
        }
        //装修程度
        $sell_list[$key]['room_hall'] = $val['room'] . '室' . $val['hall'] . '厅';//几室几厅
        $sell_list[$key]['buildarea'] = strip_end_0($val['buildarea']);//面积
        $sell_list[$key]['price'] = strip_end_0($val['price']);//售价
        $sell_list[$key]['pic_url'] = $val['pic'];
        $sell_list[$key]['broker_info'] = $this->sincere($val['broker_id']);
        $sell_list[$key]['house_degree'] = $val['house_degree'];
        $sell_list[$key]['reward_type'] = $val['reward_type'];
        $sell_list[$key]['video_id'] = $val['video_id'];
        $sell_list[$key]['video_pic'] = $val['video_pic'];
        //获取佣金分配
        $commission_ratio = $this->sell_house_model->get_commission_ratio_id($val['commission_ratio']);
        $sell_list[$key]['commission_ratio_str'] = $config['commission_ratio'][$commission_ratio];

        if ($val['sell_type'] == 1 || $val['sell_type'] == 4) {
          $floor_info_rate = $val['floor'] / $val['totalfloor'];
          if ($floor_info_rate < 0.4) {
            $sell_list[$key]['floor_info'] = '低';
          } else if ($floor_info_rate >= 0.4 && $floor_info_rate <= 0.7) {
            $sell_list[$key]['floor_info'] = '中';
          } else {
            $sell_list[$key]['floor_info'] = '高';
          }

        } else {
          $sell_list[$key]['floor_info'] = '低';
        }
        $sell_list[$key]['floor_info'] = $sell_list[$key]['floor_info'] . '楼层/' . $val['totalfloor'];
      }

    }
    $data['sell_list'] = $sell_list;
    $this->result(1, '查询合作中心出售房源列表', $data);

  }

  /**
   *出售房源添加
   */
  public function add()
  {
    //获得基本设置数据
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      $house_customer_system = intval($company_basic_data['house_customer_system']);
      $sell_house_private_num = intval($company_basic_data['sell_house_private_num']);
    } else {
      $house_customer_system = $sell_house_private_num = 0;
    }
    $datainfo = array();
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $broker_name = strip_tags($broker_info['truename']);
    $agency_id = intval($broker_info['agency_id']);
    $company_id = intval($broker_info['company_id']);
    //所需的参数
    $devicetype = $this->input->post('api_key', TRUE);
    $datainfo['broker_id'] = $broker_id;
    $datainfo['broker_name'] = $broker_name;
    $datainfo['agency_id'] = $agency_id;
    $datainfo['company_id'] = $company_id;
    $datainfo['createtime'] = time();
    $datainfo['updatetime'] = time();
    $datainfo['ip'] = get_ip();
    $datainfo['sell_type'] = $this->input->post('sell_type', TRUE);
    $datainfo['block_name'] = $this->input->post('block_name', TRUE);
    $datainfo['block_id'] = $this->input->post('block_id', TRUE);
    $datainfo['district_id'] = $this->input->post('district_id', TRUE);
    $datainfo['street_id'] = $this->input->post('street_id', TRUE);
    $datainfo['dong'] = $this->input->post('dong', TRUE);
    $datainfo['unit'] = $this->input->post('unit', TRUE);
    $datainfo['door'] = $this->input->post('door', TRUE);
    $datainfo['owner'] = $this->input->post('owner', TRUE);
    $datainfo['telno1'] = $this->input->post('telno1', TRUE);
    $datainfo['title'] = $this->input->post('title', TRUE);
    $datainfo['bewrite'] = $this->input->post('bewrite');
    $datainfo['status'] = $this->input->post('status', TRUE);
    $datainfo['nature'] = $this->input->post('nature', TRUE);
    $datainfo['isshare'] = $this->input->post('isshare', TRUE);
    $datainfo['isshare_friend'] = $this->input->post('isshare_friend', TRUE);
    $sell_tag = $this->input->post('sell_tag', TRUE);

    $datainfo['buildarea'] = $this->input->post('buildarea', TRUE);
    $datainfo['address'] = $this->input->post('address', TRUE);
    $datainfo['price'] = $this->input->post('price', TRUE);
    $datainfo['room'] = $this->input->post('room', TRUE);
    $datainfo['hall'] = $this->input->post('hall', TRUE);
    $datainfo['toilet'] = $this->input->post('toilet', TRUE);
    $datainfo['floor'] = $this->input->post('floor', TRUE);
    $datainfo['floor_type'] = $this->input->post('floor_type', TRUE);
    $datainfo['forward'] = $this->input->post('forward', TRUE);
    $datainfo['fitment'] = $this->input->post('fitment', TRUE);
    $datainfo['buildyear'] = $this->input->post('buildyear', TRUE);
    $datainfo['keys'] = $this->input->post('keys', TRUE);
    $datainfo['entrust'] = $this->input->post('entrust', TRUE);
    $datainfo['taxes'] = $this->input->post('taxes', TRUE);
    $datainfo['video_id'] = $this->input->post('video_id', TRUE);
    $datainfo['video_pic'] = $this->input->post('video_pic', TRUE);
    $shinei_url = $this->input->post('shinei_url', TRUE);//室内图片
    $huxing_url = $this->input->post('huxing_url', TRUE);//户型图片
    $weituo_url = $this->input->post('weituo_url', TRUE);//委托协议书
    $weituo_url_2 = $this->input->post('weituo_url_2', TRUE);//委托协议书
    $weituo_url_3 = $this->input->post('weituo_url_3', TRUE);//委托协议书
    $idcard_url = $this->input->post('idcard_url', TRUE);//卖家身份证
    $house_card_url = $this->input->post('house_card_url', TRUE);//房产证
    $reward_type = $this->input->post('reward_type', TRUE);//奖赏类别
    $deviceid = $this->input->post('deviceid', TRUE);

    //基本设置，房客源制判断
    $house_private_check = true;
    //公盘私客制
    if (2 == $house_customer_system) {
      if ('1' == $datainfo['nature']) {
        $house_private_check = false;
        $house_private_check_text = '当前门店基本设置为公盘私客制';
      }
    } else if (3 == $house_customer_system) {
      //公盘制 获得当前经纪人的私盘数量
      $private_where_cond = 'broker_id = "' . $broker_id . '"' . ' and status = 1 and nature = 1';
      $private_num = $this->sell_house_model->get_housenum_by_cond($private_where_cond);
      if ('1' == $datainfo['nature'] && $private_num >= $sell_house_private_num) {
        $house_private_check = false;
        $house_private_check_text = '当前门店基本设置为公盘制';
      }
    } else {
      $house_private_check = true;
    }

    if ($datainfo['isshare_friend'] == '1' && isset($datainfo['isshare_friend'])) {
      $reward_type = 1;
    }

    if (!empty($sell_tag)) {
      $datainfo['sell_tag'] = trim($sell_tag, ',');
    }
    //判断是否开启了合作审核
    if (isset($company_basic_data['check_cooperate']) && '1' == $company_basic_data['check_cooperate']) {
      if (isset($datainfo['isshare']) && '1' == $datainfo['isshare']) {
        $datainfo['isshare'] = 2;
      }
    }

    //设置合作
    if (!empty($datainfo['isshare'])) {
      $datainfo['set_share_time'] = time();
      //选择赏金方式
      if ('2' == $reward_type) {
        $datainfo['reward_type'] = 2;
        //设置合作时间，合作状态1和2
        $datainfo['cooperate_reward'] = $this->input->post('cooperate_reward', TRUE);
        if ($datainfo['cooperate_reward']) {
          $datainfo['set_reward_broker_id'] = $broker_id;
        }
        //选择佣金分配
      } else if ('1' == $reward_type) {
        $datainfo['reward_type'] = 1;
        //设置佣金比例
        $datainfo['commission_ratio'] = $this->input->post('commission_ratio', TRUE);
      }
      //未开启合作审核，选择设置奖金，合作状态改成3
      if (!(2 == $datainfo['isshare']) && '2' == $reward_type) {
        $datainfo['isshare'] = 3;
      }
    } else {
      $datainfo['cooperate_reward'] = 0;
    }

    //根据合作资料，判断是否发送审核
    if (intval($datainfo['isshare']) > 0) {
      $coo_ziliao_check_1 = true;
      $coo_ziliao_check_2 = true;
      //委托协议书、卖家身份证、房产证验证 $coo_ziliao_check_1：悬赏合作必须三证齐全。$coo_ziliao_check_2：佣金悬赏必须传两证或者三证齐全或者不传。
      if ('2' == $reward_type) {
        if (!empty($weituo_url) && !empty($idcard_url) && !empty($house_card_url)) {
          $coo_ziliao_check_1 = true;
          $datainfo['cooperate_check'] = 2;
        } else {
          $coo_ziliao_check_1 = false;
        }
      } else if ('1' == $reward_type) {
        $datainfo['house_degree'] = 1;
        if (!empty($idcard_url) && !empty($house_card_url)) {
          $coo_ziliao_check_2 = true;
          $datainfo['cooperate_check'] = 2;
        } else {
          if (empty($weituo_url) && empty($idcard_url) && empty($house_card_url)) {
            $coo_ziliao_check_2 = true;
          } else {
            $coo_ziliao_check_2 = false;
          }
        }
      }
    } else {
      $coo_ziliao_check_1 = true;
      $coo_ziliao_check_2 = true;
    }

    $datainfo['is_outside'] = $this->input->post('is_outside', TRUE);//是否同步外网
    if ($datainfo['buildarea'] && $datainfo['price']) {
      $datainfo['avgprice'] = round($datainfo['price'] * 10000 / $datainfo['buildarea'], 2);
    }
    if (!strstr($datainfo['floor'], '-') && strstr($datainfo['floor'], '/')) {
      $foor = explode('/', $datainfo['floor']);
      $datainfo['floor_type'] = 1;
      $datainfo['floor'] = $foor[0];
      $datainfo['totalfloor'] = $foor[1];
    }
    if (strstr($datainfo['floor'], '/') && strstr($datainfo['floor'], '-')) {
      $foor = explode('/', $datainfo['floor']);
      $foor1 = explode('-', $foor[0]);
      $datainfo['floor_type'] = 2;
      $datainfo['floor'] = $foor1[0];
      $datainfo['totalfloor'] = $foor[1];
      $datainfo['subfloor'] = $foor1[1];
    }
    //判断楼层要大于0
    if ($datainfo['floor'] <= 0 || $datainfo['totalfloor'] <= 0) {
      $this->result(0, '楼层必须大于0');
      exit();
    }
    $datainfo['floor_scale'] = $datainfo['floor'] / $datainfo['totalfloor'];
    $house_num = 0;
    $house_id = '';
    $is_reward_limit = true;
    $is_reward_max = true;

    //获取当前经纪人发布悬赏房源的数量
    $reward_where_cond = 'set_reward_broker_id = "' . $broker_id . '"' . ' and isshare != 0 and status = 1 and cooperate_reward > 0';
    $cooperate_reward_num = $this->sell_house_model->get_housenum_by_cond($reward_where_cond);

    if ($datainfo['sell_type'] && $datainfo['block_id']) {
      //判断房源是否重复
      $block_id = $datainfo['block_id'];
      $door = $datainfo['door'];
      $unit = $datainfo['unit'];
      $dong = $datainfo['dong'];
      if (isset($datainfo['sell_type']) && intval($datainfo['sell_type']) < 5) {
        $house_num = $this->check_house($block_id, $door, $unit, $dong);
      } else {
        $house_num = 0;
      }

      //判断合作悬赏房源，是否超过上限（每人5条）
      if ('2' == $reward_type && !empty($datainfo['isshare']) && isset($datainfo['cooperate_reward']) && !empty($datainfo['cooperate_reward'])) {
        if (is_int($cooperate_reward_num) && $cooperate_reward_num > 4) {
          $is_reward_limit = false;
        }
      }
      //判断悬赏上限，是否超过房源总价的2%
      if ('2' == $reward_type && $datainfo['cooperate_reward'] > floatval($datainfo['price']) * 10000 * 0.03) {
        $is_reward_max = false;
      }
      $is_reward_limit = true;//个数限制去除

      //积分和提示语
      $credit_msg = '';
      $credit_score = 0;
      $level_score = 0;
      if ($house_num == 0) {
        if ($is_reward_limit && $is_reward_max && $coo_ziliao_check_1 && $coo_ziliao_check_2 && $house_private_check) {
          //是否需要同步到外网
          $is_rsync_outer = $datainfo['is_outside'] == 1 ? $this->fang100($datainfo) : true;
          if ($is_rsync_outer === true) {
            $datainfo['is_outside_time'] = time();
            $data['open_cooperate'] = $company_basic_data['open_cooperate'];
            if ('0' === $data['open_cooperate'] && 1 == $datainfo['isshare']) {
              $this->result('-1', '当前公司尚未开启合作中心');
              exit();
            }
            $house_id = $this->sell_house_model->add_sell_house_info($datainfo);
            $cid = $this->input->post('cid', TRUE);
            if ($cid > 0) {
              $this->load->model('collections_model_new');
              $this->collections_model_new->change_house_status_byid($cid, $broker_id, 'sell_house_collect');
              $this->collections_model_new->add_sell_house_sub($house_id, $cid);
            }
            //添加房源日志录入
            if ($house_id > 0) {
              //操作日志
              $add_log_param = array();
              $add_log_param['company_id'] = $broker_info['company_id'];
              $add_log_param['agency_id'] = $broker_info['agency_id'];
              $add_log_param['broker_id'] = $broker_id;
              $add_log_param['broker_name'] = $broker_info['truename'];
              $add_log_param['type'] = 2;
              $add_log_param['text'] = '出售房源 ' . 'CS' . $house_id;
              if ($devicetype == 'android') {
                $add_log_param['from_system'] = 2;
              } else {
                $add_log_param['from_system'] = 3;
              }
              $add_log_param['device_id'] = $deviceid;
              $add_log_param['from_ip'] = get_ip();
              $add_log_param['mac_ip'] = '127.0.0.1';
              $add_log_param['from_host_name'] = '127.0.0.1';
              $add_log_param['hardware_num'] = '测试硬件序列号';
              $add_log_param['time'] = time();

              $this->operate_log_model->add_operate_log($add_log_param);

              $this->load->model('follow_model');
              $needarr = array();
              $needarr['broker_id'] = $broker_id;
              $needarr['type'] = 1;
              $needarr['agency_id'] = $this->user_arr['agency_id'];//门店ID
              $needarr['company_id'] = $this->user_arr['company_id'];//总公司id
              $needarr['house_id'] = $house_id;
              $bool = $this->follow_model->house_inster($needarr);
              //判断该房源是否设置了合作
              if ('1' == $datainfo['isshare'] || '2' == $datainfo['isshare']) {
                $follow_text = '';
                if ('1' == $datainfo['isshare']) {
                  $follow_text = '是否合作:否>>是';
                } else if ('2' == $datainfo['isshare']) {
                  $follow_text = '是否合作:否>>审核中';
                }
                $needarrt = array();
                $needarrt['broker_id'] = $broker_id;
                $needarrt['type'] = 1;
                $needarrt['agency_id'] = $this->user_arr['agency_id'];//门店ID
                $needarrt['company_id'] = $this->user_arr['company_id'];//总公司id
                $needarrt['house_id'] = $house_id;
                $needarrt['text'] = $follow_text;
                $boolt = $this->follow_model->house_inster_share($needarrt);
              }

              //出售房源录入成功记录工作统计日志
              $this->info_count($house_id, 1);
            }

            if ($house_id > 0 && $datainfo['keys'] && $datainfo['key_number']) {
              //添加钥匙
              $this->add_key($house_id, $datainfo['key_number'], 'add');

              //出售房源钥匙提交记录工作统计日志
              $this->info_count($house_id, 6);
            }
            //合作加积分
            if ($house_id > 0 && $datainfo['isshare'] == 1) {
              $a_ratio = $this->input->post('a_ratio', TRUE);//甲方佣金分成比例
              $b_ratio = $this->input->post('b_ratio', TRUE);//已方佣金分成比例
              $buyer_ratio = $this->input->post('buyer_ratio', TRUE);//买方支付佣金比例
              $seller_ratio = $this->input->post('seller_ratio', TRUE);//卖方支付佣金比例
              $this->load->model('sell_house_share_ratio_model');
              $this->sell_house_share_ratio_model->add_house_cooperate_ratio($house_id, $seller_ratio, $buyer_ratio, $a_ratio, $b_ratio);
              //增加积分
              $this->load->model('api_broker_credit_model');
              $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_id));
              $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $house_id), 1);
              //判断积分是否增加成功
              if (is_full_array($credit_result) && $credit_result['status'] == 1) {
                $credit_score += $credit_result['score'];
              }
              //增加等级分值
              $this->load->model('api_broker_level_model');
              $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_id));
              $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $house_id), 1);
              //判断成长值是否增加成功
              if (is_full_array($level_result) && $level_result['status'] == 1) {
                $level_score += $level_result['score'];
              }
            }
            //统计视频上传的个数
            if ($house_id > 0 && $datainfo['video_id'] && $datainfo['video_pic']) {
              $this->info_count($house_id, 7);

              //操作日志
              $add_log_param = array();
              $add_log_param['company_id'] = $broker_info['company_id'];
              $add_log_param['agency_id'] = $broker_info['agency_id'];
              $add_log_param['broker_id'] = $broker_id;
              $add_log_param['broker_name'] = $broker_info['truename'];
              $add_log_param['type'] = 9;
              $add_log_param['text'] = '出售房源 ' . 'CS' . $house_id;
              if ($devicetype == 'android') {
                $add_log_param['from_system'] = 2;
              } else {
                $add_log_param['from_system'] = 3;
              }
              $add_log_param['device_id'] = $deviceid;
              $add_log_param['from_ip'] = get_ip();
              $add_log_param['time'] = time();

              $this->operate_log_model->add_operate_log($add_log_param);
            }
            if ($house_id > 0) {
              $pic_arr = array();
              $shinei_pic_id = '';
              if ($shinei_url) {
                //室内图片
                $shinei_arr = array();
                $shinei_url = json_decode($shinei_url);
                foreach ($shinei_url as $key => $val) {
                  $shinei_arr['tbl'] = 'sell_house';
                  $shinei_arr['rowid'] = $house_id;
                  $shinei_arr['type'] = 1;
                  $shinei_arr['url'] = $val;
                  $shinei_arr['block_id'] = $datainfo['block_id'];
                  $shinei_arr['createtime'] = time();
                  $shinei_pic_id .= $this->pic_model->insert_house_pic($shinei_arr) . ',';
                  //出售房源图片上传记录工作统计日志
                  $this->info_count($house_id, 3);
                }
              }
              $huxing_pic_id = '';
              //户型图片
              if ($huxing_url) {
                $huxing_arr = array();
                $huxing_url = json_decode($huxing_url);
                foreach ($huxing_url as $key => $val) {
                  $huxing_arr['tbl'] = 'sell_house';
                  $huxing_arr['rowid'] = $house_id;
                  $huxing_arr['type'] = 2;
                  $huxing_arr['url'] = $val;
                  $huxing_arr['block_id'] = $datainfo['block_id'];
                  $huxing_arr['createtime'] = time();
                  $huxing_pic_id .= $this->pic_model->insert_house_pic($huxing_arr) . ',';
                  //出售房源图片上传记录工作统计日志
                  $this->info_count($house_id, 3);
                }
              }
              $weituo_pic_id = '';
              if ($weituo_url) {
                //委托协议书
                $weituo_arr = array();
                $weituo_arr['tbl'] = 'sell_house';
                $weituo_arr['rowid'] = $house_id;
                $weituo_arr['type'] = 3;
                $weituo_arr['url'] = $weituo_url;
                $weituo_arr['block_id'] = $datainfo['block_id'];
                $weituo_arr['createtime'] = time();
                $weituo_pic_id .= $this->pic_model->insert_house_pic($weituo_arr) . ',';
                //出售房源图片上传记录工作统计日志
                $this->info_count($house_id, 3);
              }
              if ($weituo_url_2) {
                //委托协议书
                $weituo_arr = array();
                $weituo_arr['tbl'] = 'sell_house';
                $weituo_arr['rowid'] = $house_id;
                $weituo_arr['type'] = 3;
                $weituo_arr['url'] = $weituo_url_2;
                $weituo_arr['block_id'] = $datainfo['block_id'];
                $weituo_arr['createtime'] = time();
                $weituo_pic_id .= $this->pic_model->insert_house_pic($weituo_arr) . ',';
                //出售房源图片上传记录工作统计日志
                $this->info_count($house_id, 3);
              }
              if ($weituo_url_3) {
                //委托协议书
                $weituo_arr = array();
                $weituo_arr['tbl'] = 'sell_house';
                $weituo_arr['rowid'] = $house_id;
                $weituo_arr['type'] = 3;
                $weituo_arr['url'] = $weituo_url_3;
                $weituo_arr['block_id'] = $datainfo['block_id'];
                $weituo_arr['createtime'] = time();
                $weituo_pic_id .= $this->pic_model->insert_house_pic($weituo_arr) . ',';
                //出售房源图片上传记录工作统计日志
                $this->info_count($house_id, 3);
              }

              $idcard_pic_id = '';
              //卖家身份证
              if ($idcard_url) {
                $idcard_arr = array();
                $idcard_arr['tbl'] = 'sell_house';
                $idcard_arr['rowid'] = $house_id;
                $idcard_arr['type'] = 4;
                $idcard_arr['url'] = $idcard_url;
                $idcard_arr['block_id'] = $datainfo['block_id'];
                $idcard_arr['createtime'] = time();
                $idcard_pic_id .= $this->pic_model->insert_house_pic($idcard_arr) . ',';
                //出售房源图片上传记录工作统计日志
                $this->info_count($house_id, 3);
              }

              $housecard_pic_id = '';
              //房产证
              if ($house_card_url) {
                $idcard_arr = array();
                $idcard_arr['tbl'] = 'sell_house';
                $idcard_arr['rowid'] = $house_id;
                $idcard_arr['type'] = 5;
                $idcard_arr['url'] = $house_card_url;
                $idcard_arr['block_id'] = $datainfo['block_id'];
                $idcard_arr['createtime'] = time();
                $housecard_pic_id .= $this->pic_model->insert_house_pic($idcard_arr) . ',';
                //出售房源图片上传记录工作统计日志
                $this->info_count($house_id, 3);
              }

              if ($shinei_url || $huxing_url || $weituo_url || $weituo_url_2 || $weituo_url_3 || $idcard_url || $house_card_url) {
                $this->sell_house_model->set_id($house_id);
                if (!$shinei_url && !$huxing_url) {
                  $pic_arr['house_level'] = 0;
                } else if ($shinei_url && $huxing_url) {
                  $pic_arr['house_level'] = count($shinei_url) >= 3 ? 3 : 2;
                } else {
                  $pic_arr['house_level'] = 1;
                }
                $pic_arr['pic'] = $shinei_url ? $shinei_url[0] : $huxing_url[0];
                $pic_arr['pic_tbl'] = 'upload';
                $pic_arr['pic_ids'] = $shinei_pic_id . $huxing_pic_id . $weituo_pic_id . $idcard_pic_id . $housecard_pic_id;
                $result = $this->sell_house_model->update_info_by_id($pic_arr);
              }

              if ($datainfo['video_id'] && $datainfo['video_pic']) {
                $this->sell_house_model->set_id($house_id);
                $video_arr['house_level'] = 6;
                $result = $this->sell_house_model->update_info_by_id($video_arr);
              }

              //同步加积分
              if ($datainfo['is_outside'] == 1) {
                //房源符合至少5张室内图，1张户型图，成都同步平安好房
                $city_spell = $this->user_arr['city_spell'];
                /*if($city_spell == 'cd'){
                                    $this->load->model('pic_model');
                                    //统计室内图的数量
                                    $where = array('tbl'=>'sell_house','type'=>1,'rowid'=>$house_id);
                                    $num1 = $this->pic_model->count_house_pic_by_cond($where);
                                    //统计户型图的数量
                                    $where = array('tbl'=>'sell_house','type'=>2,'rowid'=>$house_id);
                                    $num2 = $this->pic_model->count_house_pic_by_cond($where);
                                    if($num1 >= 5 && $num2 >= 1){
                                        $this->load->model('pinganhouse_model');
                                        $add_data = array('house_id'=>$house_id,'outside_time'=>time());
                                        $this->pinganhouse_model->add_house($add_data);
                                    }
                                }*/
                //增加同步房源积分
                $this->sell_house_model->set_search_fields(array('id', 'broker_id', 'is_outside', 'status', 'pic_tbl', 'pic_ids'));
                $this->sell_house_model->set_id($house_id);
                $owner_arr = $this->sell_house_model->get_info_by_id();
                //增加积分
                $this->load->model('api_broker_credit_model');
                $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_id));
                $credit_result = $this->api_broker_credit_model->rsync_fang100($owner_arr, 1);
                /*if($city_spell == 'sz' || $city_spell == 'km'){
                                    $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
                                    $credit_result1 = $this->api_broker_credit_model->fang100_activity($owner_arr, 1);
                                    //判断积分是否增加成功
                                    if (is_full_array($credit_result1) && $credit_result1['status'] == 1)
                                    {
                                        $credit_score += $credit_result1['score'];
                                    }
                                }*/
                //判断积分是否增加成功
                if (is_full_array($credit_result) && $credit_result['status'] == 1) {
                  $credit_score += $credit_result['score'];
                }
                //增加等级分值
                $this->load->model('api_broker_level_model');
                $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_id));
                $level_result = $this->api_broker_level_model->rsync_fang100($owner_arr, 1);
                //判断成长值是否增加成功
                if (is_full_array($level_result) && $level_result['status'] == 1) {
                  $level_score += $level_result['score'];
                }
              }
              //合作是否加积分
              if ($credit_score > 0) {
                $credit_msg = '+' . $credit_score . '积分';
              }
              //合作是否加成长值
              if ($level_score > 0) {
                $credit_msg .= ',+' . $level_score . '成长值';
              }
            }
          }
        }
      }
    }
    $data = array();
    if ($house_num > 0) {
      $house_id = 0;
    }
    $data['house_num'] = $house_num;
    $data['house_id'] = $house_id;
    if ($house_num > 0 && $house_id == 0) {
      $this->result(0, '操作失败，该房源已经存在');
    } else if ($house_num == 0 && !$is_reward_limit) {
      $this->result(0, '合作悬赏个数超过上限');
    } else if ($house_num == 0 && !$is_reward_max) {
      $this->result(0, '赏金不能超过总价的3%');
    } else if ($is_rsync_outer === 'no_verified') {
      $this->result(0, '您当前没有认证资料，无法同步，请关闭同步功能新录入');
    } else if ($is_rsync_outer === 'no_permission') {
      $this->result(0, '非本人房源无法同步，同步失败');
    } else if ($is_rsync_outer === 'status_failed') {
      $this->result(0, '该房源为非有效房源，无法同步，请修改房源状态');
    } else if (!$coo_ziliao_check_1) {
      $this->result(0, '选择悬赏方式，必须上传委托协议书，卖家身份证及房产证');
    } else if (!$coo_ziliao_check_2) {
      $this->result(0, '选择佣金方式需满足：不上传任何资料或者 上传房产证及身份证，或者三证都传');
    } else if (!$house_private_check) {
      $this->result(0, $house_private_check_text);
    } else if ($house_num < 0 && $house_id < 0) {
      $this->result(0, '操作失败');
    } else if ($house_num == 0 && $house_id > 0) {
      //添加合作审核提示
      if ($datainfo['isshare'] == 2) {
        $this->result(1, '您发布的合作店长审核中，请耐心等待', $data);
      } else {
        $this->result(1, '操作成功！' . $credit_msg);
      }
    }
  }

  /**
   * 同步
   * @param int $house_id 房源编号
   * @param int $flag 1 同步 0 不同步
   */
  public function set_fang100()
  {
    $house_id = $this->input->get('house_id', TRUE);//房源id
    $flag = $this->input->get('flag', TRUE);//类型
    $credit_score = 0;//积分
    $level_score = 0;//成长值
    //获得当前数据所属的经纪人id
    $this->sell_house_model->set_search_fields(array('id', 'broker_id', 'is_outside', 'status', 'pic_tbl', 'pic_ids'));
    $this->sell_house_model->set_id($house_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    $validate_status = $this->fang100($owner_arr);
    if ($validate_status == true) {
      $is_outside_time = $flag == 1 ? time() : 0;
      $update_info = array('is_outside' => $flag, 'is_outside_time' => $is_outside_time);
      $this->sell_house_model->set_id($house_id);
      $update_status = $this->sell_house_model->update_info_by_id($update_info);
      $validate_status = $update_status === 1 ? 'fang100_success' : 'fang100_failed';
    }
    if ($validate_status == 'no_verified') {
      $this->result(0, '您当前没有认证资料，无法同步，请关闭同步功能新录入');
    } else if ($validate_status == 'no_permission') {
      $this->result(0, '非本人房源无法同步，同步失败');
    } else if ($validate_status == 'status_failed') {
      $this->result(0, '该房源为非有效房源，无法同步，请修改房源状态');
    } else if ($validate_status == 'fang100_success') {
      $credit_msg = '';
      if ($flag == 1) //同步
      {
        //房源符合至少5张室内图，1张户型图，成都同步平安好房
        $city_spell = $this->user_arr['city_spell'];
        if ($city_spell == 'cd') {
          $this->load->model('pic_model');
          //统计室内图的数量
          $where = array('tbl' => 'sell_house', 'type' => 1, 'rowid' => $house_id);
          $num1 = $this->pic_model->count_house_pic_by_cond($where);
          //统计户型图的数量
          $where = array('tbl' => 'sell_house', 'type' => 2, 'rowid' => $house_id);
          $num2 = $this->pic_model->count_house_pic_by_cond($where);
          if ($num1 >= 5 && $num2 >= 1) {
            $this->load->model('pinganhouse_model');
            $add_data = array('house_id' => $house_id, 'outside_time' => time());
            $this->pinganhouse_model->add_house($add_data);
          }
        }
        if ($flag == 1) //同步
        {
          $this->load->model('api_broker_credit_model');
          $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
          $credit_result = $this->api_broker_credit_model->rsync_fang100($owner_arr, 1);
          //判断积分是否增加成功
          if (is_full_array($credit_result) && $credit_result['status'] == 1) {
            $credit_score += $credit_result['score'];
          }
          if ($city_spell == 'sz' || $city_spell == 'km') {
            $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
            $credit_result1 = $this->api_broker_credit_model->fang100_activity($owner_arr, 1);
            //判断积分是否增加成功
            if (is_full_array($credit_result1) && $credit_result1['status'] == 1) {
              $credit_score += $credit_result1['score'];
            }
          }
          $this->load->model('api_broker_level_model');
          $this->api_broker_level_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
          $level_result = $this->api_broker_level_model->rsync_fang100($owner_arr, 1);
          if ($credit_score > 0) {
            $credit_msg .= '+' . $credit_score . '积分';
          }

          if (is_full_array($level_result) && $level_result['status']) {
            $credit_msg .= ',+' . $level_result['score'] . '成长值';
          }
        }
      }
      $this->result(1, '操作成功！' . $credit_msg);
    } else if ($validate_status == 'fang100_failed') {
      $this->result(0, '操作失败');
    }
  }

  public function fang100($owner_arr)
  {
    //一、判断当前经纪人是否是认证用户
    if ($this->user_arr['group_id'] != 2) {
      return 'no_verified';
    }
    //二、不是本人房源，提示没有权限
    if ($this->user_arr['broker_id'] != $owner_arr['broker_id']) {
      return 'no_permission';
    }
    //判断房源状态是否有效
    if ($owner_arr['status'] != '1') {
      return 'status_failed';
    }
    return true;
  }

  public function get_this_broker_outside_num()
  {
    $result_num = 0;
    $this_broker_id = intval($this->user_arr['broker_id']);
    $where_cond = array(
      'broker_id' => $this_broker_id,
      'is_outside' => 1
    );
    $result_num = $this->sell_house_model->get_housenum_by_cond($where_cond);
    return $result_num;
  }

  //添加钥匙
  public function add_key($house_id, $key_number, $method)
  {
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //房源信息
    $this->sell_house_model->set_id($house_id);
    $house_info = $this->sell_house_model->get_info_by_id();

    $this->load->model('key_model');

    $datainfo['number'] = $key_number;
    $datainfo['type'] = 1;
    $datainfo['house_id'] = $house_id;
    $datainfo['block_id'] = $house_info['block_id'];
    $datainfo['block_name'] = $house_info['block_name'];
    $datainfo['dong'] = $house_info['dong'];
    $datainfo['unit'] = $house_info['unit'];
    $datainfo['door'] = $house_info['door'];
    if ($method == 'add') {
      $datainfo['broker_id'] = $broker_id;
      $datainfo['agency_id'] = $this->user_arr['agency_id'];
      $datainfo['company_id'] = $this->user_arr['company_id'];;
      $datainfo['add_time'] = time();
    }
    $this->key_model->add_info($datainfo);
  }

  //房源是否重复
  public function check_house($block_id, $door, $unit, $dong)
  {
    //经纪人信息
    $broker_info = $this->user_arr;
    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    $agency_id = intval($broker_info['agency_id']);//门店编号
    //判断经纪人当前门店类型，直营or加盟
    $this->agency_model->set_select_fields(array('id', 'agency_type'));
    $this_agency_data = $this->agency_model->get_by_id($agency_id);
    if (is_full_array($this_agency_data)) {
      $agency_type = $this_agency_data['agency_type'];
    }
    //加盟店，去重范围只在自己门店。
    if (isset($agency_type) && '2' == $agency_type) {
      $agency_ids = $agency_id;
      //直营店，去重范围，当前公司下的所有直营店。
    } else {
      //获取当前公司下的所有直营店
      $agency_type_1_list = $this->api_broker_model->get_type_1_agencys_by_company_id($company_id);
      if (is_full_array($agency_type_1_list)) {
        $arr_agency_id = array();
        foreach ($agency_type_1_list as $key => $val) {
          $arr_agency_id[] = $val['agency_id'];
        }
        $agency_ids = implode(',', $arr_agency_id);
      } else {
        $agency_ids = $agency_id;
      }
    }
    $cond_where = "status != 5 and agency_id in (" . $agency_ids . ") and block_id = $block_id and door = '$door' and unit = '$unit' and dong = '$dong' ";
    $tbl = "sell_house";
    $this->sell_house_model->set_tbl($tbl);
    $house_num = $this->sell_house_model->get_housenum_by_cond($cond_where);
    return $house_num;
  }

  //房源修改
  public function modify()
  {
    //判断基本设置是否开启合作中心
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      $house_customer_system = intval($company_basic_data['house_customer_system']);
      $sell_house_private_num = intval($company_basic_data['sell_house_private_num']);
    } else {
      $house_customer_system = $sell_house_private_num = 0;
    }
    $data = array();
    //获取当前登录人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $house_id = $this->input->post('house_id', TRUE);

    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->sell_house_model->set_id($house_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    //是否有修改房源权限
    $house_modify_per = $this->broker_permission_model->check('8', $owner_arr);
    //修改房源关联门店权限
    $agency_house_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '4');
    if (!$house_modify_per['auth']) {
      $this->result('-1', '暂无权限');
      exit();
    } else {
      if (!$agency_house_modify_per) {
        $this->result('-1', '暂无权限');
        exit();
      }
    }
    //是否有变更房源性质权限
    $nature_change_per = $this->broker_permission_model->check('3', $owner_arr);
    $is_nature_change_per = 0;
    if ($nature_change_per['auth']) {
      $is_nature_change_per = 1;
    }

    //获取出售信息基本配置资料
    $config = $this->house_config_model->get_config();
    //基本信息‘状态’数据处理
    if (!empty($config['status']) && is_array($config['status'])) {
      foreach ($config['status'] as $k => $v) {
        if ('暂不售（租）' == $v) {
          $config['status'][$k] = '暂不售';
        }
      }
    }

    //单条房源数据
    $fileld = array('sell_type', 'forward', 'fitment', 'nature', 'block_id', 'district_id', 'status', 'street_id', 'taxes', 'entrust', 'address', 'dong', 'unit', 'door', 'owner', 'telno1', 'title', 'bewrite', 'isshare', 'buildarea', 'price', 'room', 'block_name', 'hall', 'toilet', 'floor', 'subfloor', 'totalfloor', 'buildyear', 'keys', 'pic', 'floor_type', 'createtime', 'updatetime', 'pic_ids', 'pic_tbl', 'is_outside', 'isshare_friend', 'broker_id', 'cooperate_reward', 'sell_tag', 'set_share_time', 'cooperate_check', 'reward_type', 'video_id', 'video_pic', 'commission_ratio');
    $this->sell_house_model->set_search_fields($fileld);
    $this->sell_house_model->set_id($house_id);
    $house_detail = $this->sell_house_model->get_info_by_id();

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    $role_level = intval($broker_info['role_level']);
    //店长以下的经纪人不允许操作他人的私盘
    if (is_int($role_level) && $role_level > 6) {
      if ($owner_arr['broker_id'] != $broker_info['broker_id'] && $house_detail['nature'] == '1') {
        $this->result('-1', '店长以下的经纪人不允许操作他人的私盘');
        exit();
      }
    }

    //获取出售信息基本配置资料
    $config = $this->house_config_model->get_config();
    //获取板块区属
    $district_name = $this->district_model->get_distname_by_id($house_detail['district_id']);//区属名称
    $street_name = $this->district_model->get_streetname_by_id($house_detail['street_id']);//板块名称
    $sell_type = $config['sell_type'][$house_detail['sell_type']];//出售类型
    $house_detail['sell_type'] = array('key' => $house_detail['sell_type'], 'name' => $sell_type);
    $house_detail['cmt_info'] = array('cmt_id' => $house_detail['block_id'], 'cmt_name' => $house_detail['block_name'], 'dist_id' => $house_detail['district_id'], 'districtname' => $district_name, 'streetid' => $house_detail['street_id'], 'streetname' => $street_name);
    $house_detail['status'] = array('key' => $house_detail['status'], 'name' => $config['status'][$house_detail['status']]);
    $commission_ratio = $this->sell_house_model->get_commission_ratio_id($house_detail['commission_ratio']);
    $house_detail['commission_ratio'] = array('key' => $commission_ratio, 'name' => $config['commission_ratio'][$commission_ratio]);
    $house_detail['sell_tag_result'] = array();
    if (!empty($house_detail['sell_tag'])) {
      $sell_tag_arr = explode(',', $house_detail['sell_tag']);
      if (is_full_array($sell_tag_arr)) {
        foreach ($sell_tag_arr as $k => $v) {
          $house_detail['sell_tag_result'][] = array('key' => $v, 'name' => $config['sell_tag'][$v]);
        }
      }
    }

    $house_detail['nature'] = array('key' => $house_detail['nature'], 'name' => $config['nature'][$house_detail['nature']], 'is_change_per' => $is_nature_change_per);
    if ($house_detail['entrust']) {
      $house_detail['entrust'] = array('key' => $house_detail['entrust'], 'name' => $config['entrust'][$house_detail['entrust']]);
    } else {
      $house_detail['entrust'] = array('key' => '-1', 'name' => '暂无数据');
    }
    $house_detail['forward'] = array('key' => $house_detail['forward'], 'name' => $config['forward'][$house_detail['forward']]);
    $house_detail['fitment'] = array('key' => $house_detail['fitment'], 'name' => $config['fitment'][$house_detail['fitment']]);
    $house_detail['createtime'] = date('Y-m-d H:i:s', $house_detail['createtime']);
    if ($house_detail['taxes']) {
      $house_detail['taxes'] = array('key' => $house_detail['taxes'], 'name' => $config['taxes'][$house_detail['taxes']]);
    } else {
      $house_detail['taxes'] = array('key' => '-1', 'name' => '暂无数据');
    }

    // 楼层
    if ($house_detail['floor_type'] == 1) {
      $house_detail['floor_arr'] = $house_detail['floor'] . '/' .
        $house_detail['totalfloor'];
    }
    if ($house_detail['floor_type'] == 2) {
      $house_detail['floor_arr'] = $house_detail['floor'] . '-' .
        $house_detail['subfloor'] . '/' . $house_detail['totalfloor'];
    }

    //房源佣金分成数据
    $this->load->model('sell_house_share_ratio_model');
    $ratio_info = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($house_id);
    if ($ratio_info && $house_detail['isshare'] == 1) {
      $house_detail['ratio_info'] = $ratio_info;
    }
    $this->load->model('pic_model');
    $all_pic_info = $this->pic_model->find_house_pic_by_ids($house_detail['pic_tbl'], $house_detail['pic_ids']);
    //按照原 sell_house 表中的 pic_ids 字段来展示房源图片
    $id_str = substr($house_detail['pic_ids'], 0, strlen($house_detail['pic_ids']) - 1);
    $arr = explode(',', $id_str);
    $house_detail['picinfo'] = array();
    foreach ($arr as $k => $v) {
      if (is_full_array($all_pic_info)) {
        foreach ($all_pic_info as $key => $value) {
          if ($value['id'] == $v) {
            $house_detail['picinfo'][] = $value;
          }
        }
      }
    }

    //是否有共享他人房源权限
    $house_share_per = $this->broker_permission_model->check('2', $owner_arr);
    if ($house_share_per['auth']) {
      $house_detail['house_share_per'] = 1;
    } else {
      $house_detail['house_share_per'] = 0;
    }

    $data['house_detail'] = $house_detail;
    //传递的参数
    $devicetype = $this->input->post('api_key', TRUE);
    $datainfo['updatetime'] = time();
    $datainfo['ip'] = get_ip();
    $datainfo['sell_type'] = $this->input->post('sell_type', TRUE);
    $datainfo['block_name'] = $this->input->post('block_name', TRUE);
    $datainfo['block_id'] = $this->input->post('block_id', TRUE);
    $datainfo['district_id'] = $this->input->post('district_id', TRUE);
    $datainfo['street_id'] = $this->input->post('street_id', TRUE);
    $datainfo['dong'] = $this->input->post('dong', TRUE);
    $datainfo['unit'] = $this->input->post('unit', TRUE);
    $datainfo['door'] = $this->input->post('door', TRUE);
    $datainfo['owner'] = $this->input->post('owner', TRUE);
    $datainfo['telno1'] = $this->input->post('telno1', TRUE);
    $datainfo['title'] = $this->input->post('title', TRUE);
    $datainfo['bewrite'] = $this->input->post('bewrite', TRUE);
    $datainfo['status'] = $this->input->post('status', TRUE);
    $datainfo['nature'] = $this->input->post('nature', TRUE);
    $datainfo['isshare'] = $this->input->post('isshare', TRUE);
    $datainfo['isshare_friend'] = $this->input->post('isshare_friend', TRUE);
    $datainfo['video_id'] = $this->input->post('video_id', TRUE);
    $datainfo['video_pic'] = $this->input->post('video_pic', TRUE);
    $sell_tag = $this->input->post('sell_tag', TRUE);
    $weituo_url = $this->input->post('weituo_url', TRUE);//委托协议书
    $weituo_url_2 = $this->input->post('weituo_url_2', TRUE);//委托协议书
    $weituo_url_3 = $this->input->post('weituo_url_3', TRUE);//委托协议书
    $idcard_url = $this->input->post('idcard_url', TRUE);//卖家身份证
    $house_card_url = $this->input->post('house_card_url', TRUE);//房产证
    $reward_type = $this->input->post('reward_type', TRUE);//奖赏类别
    if ($datainfo['isshare_friend'] == '1' && isset($datainfo['isshare_friend'])) {
      $reward_type = 1;
    }

    if (!empty($sell_tag)) {
      $datainfo['sell_tag'] = trim($sell_tag, ',');
    }


    $old_pic_ids = trim($house_detail['pic_ids'], ',');
    $old_pic_ids_arr = explode(',', $old_pic_ids);

    $picinfo3 = array();#委托协议书
    $picinfo4 = array();#身份证
    $picinfo5 = array();#房产证

    //房源图片数据重构
    if (is_full_array($old_pic_ids_arr)) {
      foreach ($old_pic_ids_arr as $k => $v) {
        if (is_full_array($house_detail['picinfo'])) {
          foreach ($house_detail['picinfo'] as $key => $value) {
            if ($value['id'] == $v && $value['type'] == 3) {
              $picinfo3[] = $value;
            } else if ($value['id'] == $v && $value['type'] == 4) {
              $picinfo4[] = $value;
            } else if ($value['id'] == $v && $value['type'] == 5) {
              $picinfo5[] = $value;
            }
          }
        }
      }
    }

    $pic3_back_str_0 = '';
    $pic3_back_str_1 = '';
    $pic3_back_str_2 = '';

    $pic4_back_str = '';
    $pic5_back_str = '';
    if (is_full_array($picinfo3[0])) {
      $pic3_back_str_0 = $picinfo3[0]['url'];
    }
    if (is_full_array($picinfo3[1])) {
      $pic3_back_str_1 = $picinfo3[1]['url'];
    }
    if (is_full_array($picinfo3[2])) {
      $pic3_back_str_2 = $picinfo3[2]['url'];
    }

    if (is_full_array($picinfo4[0])) {
      $pic4_back_str = $picinfo4[0]['url'];
    }
    if (is_full_array($picinfo5[0])) {
      $pic5_back_str = $picinfo5[0]['url'];
    }

    //根据合作资料，判断是否发送审核
    if (($pic3_back_str_0 != $weituo_url) || ($pic3_back_str_1 != $weituo_url_2) || ($pic3_back_str_2 != $weituo_url_3) || ($pic4_back_str != $idcard_url) || ($pic5_back_str != $house_card_url)) {
      $is_pic_change = true;
      $datainfo['cooperate_check'] = 2;
    } else {
      $is_pic_change = false;
    }

    //判断是否开启了合作审核
    if (isset($company_basic_data['check_cooperate']) && '1' == $company_basic_data['check_cooperate']) {
      if (isset($datainfo['isshare']) && '1' == $datainfo['isshare']) {
        $datainfo['isshare'] = 2;
      }
    } else {
      //判断合作是否从否变成是
      if (0 == $house_detail['isshare'] && 1 == $datainfo['isshare'] && '2' == $reward_type) {
        //判断是否发送了审核
        if (2 == $datainfo['cooperate_check']) {
          $datainfo['isshare'] = 3;
        } else {
          $datainfo['isshare'] = 1;
        }
      }
    }

    //合作赏金
    if (!empty($datainfo['isshare'])) {
      //选择赏金方式
      if ('2' == $reward_type) {
        $datainfo['reward_type'] = 2;
        //设置合作时间，合作状态1和2
        $datainfo['set_share_time'] = time();
        $datainfo['cooperate_reward'] = $this->input->post('cooperate_reward', TRUE);
        if (!empty($datainfo['cooperate_reward'])) {
          $datainfo['set_reward_broker_id'] = intval($broker_info['broker_id']);
        } else {
          $datainfo['set_reward_broker_id'] = 0;
        }
        //佣金分配方式
      } else if ('1' == $reward_type) {
        //设置佣金比例
        if (0 == $house_detail['isshare'] && 1 == $datainfo['isshare']) {
          $datainfo['commission_ratio'] = $this->input->post('commission_ratio', TRUE);
        }
        $datainfo['set_share_time'] = time();
        $datainfo['reward_type'] = 1;
        $datainfo['cooperate_reward'] = 0;
        $datainfo['set_reward_broker_id'] = 0;
      }
    } else {
      $datainfo['cooperate_reward'] = 0;
      $datainfo['set_reward_broker_id'] = 0;
    }

    //奖金方式，合作状态从否变成是、资料不变，提示重新上传资料。
    if ('0' == $house_detail['isshare'] && intval($datainfo['isshare']) > 0 && 2 == intval($house_detail['reward_type']) && 2 == intval($datainfo['reward_type']) && !$is_pic_change) {
      $coo_ziliao_check_3 = false;
    } else {
      $coo_ziliao_check_3 = true;
    }
    //根据奖励方式，做图片个数验证
    if (intval($datainfo['isshare']) > 0) {
      $coo_ziliao_check_1 = true;
      $coo_ziliao_check_2 = true;
      //委托协议书、卖家身份证、房产证验证 $coo_ziliao_check_1：悬赏合作必须三证齐全。$coo_ziliao_check_2：佣金悬赏必须传两证或者三证齐全或者不传。
      if ('2' == $reward_type) {
        if (!empty($weituo_url) && !empty($idcard_url) && !empty($house_card_url)) {
          $coo_ziliao_check_1 = true;
        } else {
          $coo_ziliao_check_1 = false;
        }
      } else if ('1' == $reward_type) {
        if (!empty($idcard_url) && !empty($house_card_url)) {
          $coo_ziliao_check_2 = true;
        } else {
          if (empty($weituo_url) && empty($idcard_url) && empty($house_card_url)) {
            $coo_ziliao_check_2 = true;
          } else {
            $coo_ziliao_check_2 = false;
          }
        }
      }
    } else {
      $coo_ziliao_check_1 = true;
      $coo_ziliao_check_2 = true;
    }

    $datainfo['buildarea'] = $this->input->post('buildarea', TRUE);
    $datainfo['address'] = $this->input->post('address', TRUE);
    $datainfo['price'] = $this->input->post('price', TRUE);
    $datainfo['room'] = $this->input->post('room', TRUE);
    $datainfo['hall'] = $this->input->post('hall', TRUE);
    $datainfo['toilet'] = $this->input->post('toilet', TRUE);
    $datainfo['floor'] = $this->input->post('floor', TRUE);
    $datainfo['forward'] = $this->input->post('forward', TRUE);
    $datainfo['fitment'] = $this->input->post('fitment', TRUE);
    $datainfo['buildyear'] = $this->input->post('buildyear', TRUE);
    $datainfo['buildarea'] = $this->input->post('buildarea', TRUE);
    $datainfo['keys'] = $this->input->post('keys', TRUE);
    $datainfo['key_number'] = $this->input->post('key_number', TRUE);
    $datainfo['entrust'] = $this->input->post('entrust', TRUE);
    $taxes = $this->input->post('taxes', TRUE);
    $deviceid = $this->input->get('deviceid', TRUE);
    if ($taxes !== -1) {
      $datainfo['taxes'] = $taxes;
    }
    if ($datainfo['buildarea'] && $datainfo['price']) {
      $datainfo['avgprice'] = round($datainfo['price'] * 10000 / $datainfo['buildarea'], 2);
    }
    $shinei_url = $this->input->post('shinei_url', TRUE);//室内图片
    $huxing_url = $this->input->post('huxing_url', TRUE);//户型图片

    if (!strstr($datainfo['floor'], '-') && strstr($datainfo['floor'], '/')) {
      $foor = explode('/', $datainfo['floor']);
      $datainfo['floor_type'] = 1;
      $datainfo['floor'] = $foor[0];
      $datainfo['totalfloor'] = $foor[1];
    }
    if (strstr($datainfo['floor'], '/') && strstr($datainfo['floor'], '-')) {
      $foor = explode('/', $datainfo['floor']);
      $foor1 = explode('-', $foor[0]);
      $datainfo['floor_type'] = 2;
      $datainfo['floor'] = $foor1[0];
      $datainfo['totalfloor'] = $foor[1];
      $datainfo['subfloor'] = $foor1[1];
    }
    $datainfo['floor_scale'] = $datainfo['floor'] / $datainfo['totalfloor'];
    $datainfo['is_outside'] = $this->input->post('is_outside', TRUE);//是否同步外网
    $result = 0;
    $is_reward_limit = true;
    $is_reward_max = true;
    //获取当前经纪人发布悬赏房源的数量
    $reward_where_cond = 'set_reward_broker_id = "' . intval($broker_info['broker_id']) . '"' . ' and isshare != 0 and status = 1 and cooperate_reward > 0';
    $cooperate_reward_num = $this->sell_house_model->get_housenum_by_cond($reward_where_cond);
    if ('2' == $reward_type && '0' == $house_detail['cooperate_reward'] && $datainfo['cooperate_reward'] > 0) {
      if (is_int($cooperate_reward_num) && $cooperate_reward_num > 4) {
        $is_reward_limit = false;
      }
    }
    $is_reward_limit = true;//个数限制去除

    //判断悬赏上限，是否超过房源总价的3%
    if ('2' == $reward_type && $datainfo['cooperate_reward'] > floatval($datainfo['price']) * 10000 * 0.03) {
      $is_reward_max = false;
    }

    $this->sell_house_model->set_id($house_id);
    $sell_backinfo = $this->sell_house_model->get_info_by_id();//修改前的信息
    //基本设置，房客源制判断
    $house_private_check = true;
    //公盘私客制
    if (2 == $house_customer_system) {
      if ('2' == $sell_backinfo['nature'] && '1' == $datainfo['nature']) {
        $house_private_check = false;
        $house_private_check_text = '当前门店基本设置为公盘私客制';
      }
    } else if (3 == $house_customer_system) {
      //公盘制 获得当前经纪人的私盘数量
      $private_where_cond = 'broker_id = "' . $broker_id . '"' . ' and status = 1 and nature = 1';
      $private_num = $this->sell_house_model->get_housenum_by_cond($private_where_cond);
      if ('2' == $sell_backinfo['nature'] && '1' == $datainfo['nature'] && $private_num >= $sell_house_private_num) {
        $house_private_check = false;
        $house_private_check_text = '当前门店基本设置为公盘制';
      }
    } else {
      $house_private_check = true;
    }

    //积分和提示语
    $credit_msg = '';
    $credit_score = 0;
    $level_score = 0;
    if ($house_id && $datainfo['sell_type'] && $datainfo['block_name'] && $is_reward_limit && $is_reward_max && $coo_ziliao_check_1 && $coo_ziliao_check_2 && $coo_ziliao_check_3 && $house_private_check) {
      //判断楼层要大于0
      if (intval($datainfo['floor']) <= 0 || intval($datainfo['totalfloor']) <= 0) {
        $this->result(0, '楼层必须大于0');
        exit();
      }
      $this->sell_house_model->set_id($house_id);
      $sell_backinfo = $this->sell_house_model->get_info_by_id();//修改前的信息
      if (1 == $sell_backinfo['isshare'] || 2 == $sell_backinfo['isshare']) {
        $datainfo['set_share_time'] = $sell_backinfo['set_share_time'];
      }

      $datainfo['broker_id'] = $sell_backinfo['broker_id'];
      if ($sell_backinfo['is_outside'] != $datainfo['is_outside']
        && $datainfo['is_outside'] == 1
      ) {
        $is_rsync_outer = $this->fang100($datainfo);
        if ($is_rsync_outer == true) {
          $datainfo['is_outside_time'] == time();
        }
      } else {
        $is_rsync_outer = true;
      }
      if ($is_rsync_outer === true) {
        $data['open_cooperate'] = $company_basic_data['open_cooperate'];
        if ('0' === $data['open_cooperate'] && $datainfo['isshare'] != $house_detail['isshare']) {
          $this->result('-1', '当前公司尚未开启合作中心');
          exit();
        }

        //判断是否有共享他人房源全选
        $cooperate_per = $this->broker_permission_model->check('2', $owner_arr);
        //如果没有权限，并且将合作字段从否改为是
        if (!$cooperate_per['auth'] && 1 == $datainfo['isshare'] && '0' === $house_detail['isshare']) {
          $this->result('-1', '无共享他人房源权限');
          exit();
        }

        $result = $this->sell_house_model->update_info_by_id($datainfo);
        $sell_dataifno = $this->sell_house_model->get_info_by_id();//修改过后信息
        //修改佣金比例
        if ($datainfo['isshare'] == 1) {
          $a_ratio = $this->input->post('a_ratio', TRUE);//甲方佣金分成比例
          $b_ratio = $this->input->post('b_ratio', TRUE);//已方佣金分成比例
          $buyer_ratio = $this->input->post('buyer_ratio', TRUE);//买方支付佣金比例
          $seller_ratio = $this->input->post('seller_ratio', TRUE);//卖方支付佣金比例
          $this->load->model('sell_house_share_ratio_model');
          $sell_backinfo_ratio = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($house_id);
          $sell_backinfo['a_ratio'] = $sell_backinfo_ratio['a_ratio'];
          $sell_backinfo['b_ratio'] = $sell_backinfo_ratio['b_ratio'];
          $sell_backinfo['buyer_ratio'] = $sell_backinfo_ratio['buyer_ratio'];
          $sell_backinfo['seller_ratio'] = $sell_backinfo_ratio['seller_ratio'];
          $this->sell_house_share_ratio_model->update_house_ratio_by_rowid($house_id, $seller_ratio, $buyer_ratio, $a_ratio, $b_ratio);
          $sell_dataifno_ratio = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($house_id);
          $sell_dataifno['a_ratio'] = $sell_dataifno_ratio['a_ratio'];
          $sell_dataifno['b_ratio'] = $sell_dataifno_ratio['b_ratio'];
          $sell_dataifno['buyer_ratio'] = $sell_dataifno_ratio['buyer_ratio'];
          $sell_dataifno['seller_ratio'] = $sell_dataifno_ratio['seller_ratio'];
          //增加积分
          $this->load->model('api_broker_credit_model');
          $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_info['broker_id']));
          $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $house_id), 1);
          //判断积分是否增加成功
          if (is_full_array($credit_result) && $credit_result['status'] == 1) {
            $credit_score = $credit_result['score'];
          }
          //增加等级分值
          $this->load->model('api_broker_level_model');
          $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_info['broker_id']));
          $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $house_id), 1);
          //判断成长值是否增加成功
          if (is_full_array($level_result) && $level_result['status'] == 1) {
            $level_score = $level_result['score'];
          }
        }
        //记录房源修改前的图片 比较图片的改过情况
        $old_pic_inside_room = array();//室内图+户型图
        //房源图片数据重构
        foreach ($arr as $k => $v) {
          if (is_full_array($all_pic_info)) {
            foreach ($all_pic_info as $key => $value) {
              if ($value['id'] == $v && ($value['type'] == 1 || $value['type'] == 2)) {
                $old_pic_inside_room[] = $value['url'];
              }
            }
          }
        }
        if ($shinei_url)//室内
        {
          $new_inside = json_decode($shinei_url);
        }
        if ($huxing_url) {
          $new_room = json_decode($huxing_url);
        }
        if (!$new_inside) {
          $new_inside = array();
        }
        if (!$new_room) {
          $new_room = array();
        }
        $new_pic_inside_room = array_merge($new_inside, $new_room);
        $sell_backinfo['pic_inside_room'] = $old_pic_inside_room;
        $sell_dataifno['pic_inside_room'] = $new_pic_inside_room;
        $sell_cont = $this->insetmatch($sell_backinfo, $sell_dataifno);
        //修改房源日志录入
        $need_info = $this->user_arr;
        $this->load->model('follow_model');
        $needarrt = array();
        $needarrt['broker_id'] = $need_info['broker_id'];
        $needarrt['type'] = 1;
        $needarrt['agency_id'] = $need_info['agency_id'];//门店ID
        $needarrt['company_id'] = $need_info['company_id'];//总公司id
        $needarrt['house_id'] = $house_id;
        $needarrt['text'] = $sell_cont;
        if (!empty($sell_cont)) {
          $boolt = $this->follow_model->house_save($needarrt);
          if (is_int($boolt) && $boolt > 0) {
            //判断该跟进距离上一次是否已超过基本设置天数，录入出售房源附表
            //获得基本设置房源跟进的天数
            //获取当前经济人所在公司的基本设置信息
            $this->load->model('house_customer_sub_model');
            $house_follow_day = intval($company_basic_data['house_follow_spacing_time']);

            $select_arr = array('id', 'house_id', 'date');
            $this->follow_model->set_select_fields($select_arr);
            $where_cond = 'house_id = "' . $house_id . '" and follow_type != 2 and type = 1';
            $last_follow_data = $this->follow_model->get_lists($where_cond, 0, 2, 'date');
            if (count($last_follow_data) == 2) {
              $time1 = $last_follow_data[0]['date'];
              $time2 = $last_follow_data[1]['date'];
              $date1 = date('Y-m-d', strtotime($time1));
              $date2 = date('Y-m-d', strtotime($time2));
              $differ_day = (strtotime($date1) - strtotime($date2)) / (24 * 3600);
              if ($differ_day < $house_follow_day) {
                $this->house_customer_sub_model->add_sell_house_sub($house_id, 0);
              } else {
                $this->house_customer_sub_model->add_sell_house_sub($house_id, 1);
              }
            }
          }
        }

        //出售房源修改工作统计日志
        if ($sell_cont) {
          $this->info_count($house_id, 2);
        }

        if ($datainfo['isshare'] == 0) {
          //取消合作后，终止与房源有关系的合作
          $stop_reason = 'private_house';
          $this->load->model('cooperate_model');
          $this->cooperate_model->stop_cooperate($house_id, 'sell', $stop_reason);
        }
        //添加钥匙
        if (!$sell_backinfo['keys'] && $sell_dataifno['keys'] && $sell_dataifno['key_number']) {
          $this->add_key($house_id, $sell_dataifno['key_number'], 'update');

          //出售房源钥匙提交记录工作统计日志
          $this->info_count($house_id, 6);
        }
        //添加视频
        if (!$sell_backinfo['video_id']
          && ($sell_dataifno['video_id'] != $sell_backinfo['video_id'])
        ) {
          $this->info_count($house_id, 7);
        }
        /***从有效状态改成其它状态，终止房源合作***/
        if ($sell_backinfo['status'] == 2 && $datainfo['status'] != 2) {
          $stop_reason = '';

          switch ($datainfo['status']) {
            case '1':
              $stop_reason = 'invalid_house';
              break;
            case '3':
              $stop_reason = 'reserve_house';
              break;
            case '4':
              $stop_reason = 'deal_house';
              break;
          }

          $this->load->model('cooperate_model');
          $this->cooperate_model->stop_cooperate($house_id, 'sell', $stop_reason);
        }
        //修改图片
        if ($house_detail['pic_ids'] && $house_detail['pic_tbl']) {
          $pic_url = explode(',', trim($house_detail['pic_ids'], ','));
          $pic_val = '';
          foreach ($pic_url as $val) {
            $pic_val .= $val . ',';
          }
          $this->pic_model->del_pic_by_ids($pic_val, $house_detail['pic_tbl']);
        }
        $pic_arr = array();
        $shinei_pic_id = '';
        if ($shinei_url) {
          //室内图片
          $shinei_arr = array();
          $shinei_url = json_decode($shinei_url);
          foreach ($shinei_url as $key => $val) {
            $shinei_arr['tbl'] = 'sell_house';
            $shinei_arr['rowid'] = $house_id;
            $shinei_arr['type'] = 1;
            $shinei_arr['url'] = $val;
            $shinei_arr['block_id'] = $datainfo['block_id'];
            $shinei_arr['createtime'] = time();
            $shinei_pic_id .= $this->pic_model->insert_house_pic($shinei_arr) . ',';
            //出售房源图片上传记录工作统计日志
            $this->info_count($house_id, 3);
          }
        }
        $huxing_pic_id = '';
        //户型图片
        if ($huxing_url) {
          $huxing_arr = array();
          $huxing_url = json_decode($huxing_url);
          foreach ($huxing_url as $key => $val) {
            $huxing_arr['tbl'] = 'sell_house';
            $huxing_arr['rowid'] = $house_id;
            $huxing_arr['type'] = 2;
            $huxing_arr['url'] = $val;
            $huxing_arr['block_id'] = $datainfo['block_id'];
            $huxing_arr['createtime'] = time();
            $huxing_pic_id .= $this->pic_model->insert_house_pic($huxing_arr) . ',';
            //出售房源图片上传记录工作统计日志
            $this->info_count($house_id, 3);
          }
        }
        $weituo_pic_id = '';
        if ($weituo_url) {
          //委托协议书
          $weituo_arr = array();
          $weituo_arr['tbl'] = 'sell_house';
          $weituo_arr['rowid'] = $house_id;
          $weituo_arr['type'] = 3;
          $weituo_arr['url'] = $weituo_url;
          $weituo_arr['block_id'] = $datainfo['block_id'];
          $weituo_arr['createtime'] = time();
          $weituo_pic_id .= $this->pic_model->insert_house_pic($weituo_arr) . ',';
          //出售房源图片上传记录工作统计日志
          $this->info_count($house_id, 3);
        }
        if ($weituo_url_2) {
          //委托协议书
          $weituo_arr = array();
          $weituo_arr['tbl'] = 'sell_house';
          $weituo_arr['rowid'] = $house_id;
          $weituo_arr['type'] = 3;
          $weituo_arr['url'] = $weituo_url_2;
          $weituo_arr['block_id'] = $datainfo['block_id'];
          $weituo_arr['createtime'] = time();
          $weituo_pic_id .= $this->pic_model->insert_house_pic($weituo_arr) . ',';
          //出售房源图片上传记录工作统计日志
          $this->info_count($house_id, 3);
        }
        if ($weituo_url_3) {
          //委托协议书
          $weituo_arr = array();
          $weituo_arr['tbl'] = 'sell_house';
          $weituo_arr['rowid'] = $house_id;
          $weituo_arr['type'] = 3;
          $weituo_arr['url'] = $weituo_url_3;
          $weituo_arr['block_id'] = $datainfo['block_id'];
          $weituo_arr['createtime'] = time();
          $weituo_pic_id .= $this->pic_model->insert_house_pic($weituo_arr) . ',';
          //出售房源图片上传记录工作统计日志
          $this->info_count($house_id, 3);
        }

        $idcard_pic_id = '';
        //卖家身份证
        if ($idcard_url) {
          $idcard_arr = array();
          $idcard_arr['tbl'] = 'sell_house';
          $idcard_arr['rowid'] = $house_id;
          $idcard_arr['type'] = 4;
          $idcard_arr['url'] = $idcard_url;
          $idcard_arr['block_id'] = $datainfo['block_id'];
          $idcard_arr['createtime'] = time();
          $idcard_pic_id .= $this->pic_model->insert_house_pic($idcard_arr) . ',';
          //出售房源图片上传记录工作统计日志
          $this->info_count($house_id, 3);
        }

        $housecard_pic_id = '';
        //卖家身份证
        if ($house_card_url) {
          $house_card_arr = array();
          $house_card_arr['tbl'] = 'sell_house';
          $house_card_arr['rowid'] = $house_id;
          $house_card_arr['type'] = 5;
          $house_card_arr['url'] = $house_card_url;
          $house_card_arr['block_id'] = $datainfo['block_id'];
          $house_card_arr['createtime'] = time();
          $housecard_pic_id .= $this->pic_model->insert_house_pic($house_card_arr) . ',';
          //出售房源图片上传记录工作统计日志
          $this->info_count($house_id, 3);
        }

        if ($shinei_url || $huxing_url || $weituo_url || $idcard_url || $house_card_url) {
          $this->sell_house_model->set_id($house_id);
          if (!$shinei_url && !$huxing_url) {
            $pic_arr['house_level'] = 0;
          } else if ($shinei_url && $huxing_url) {
            $pic_arr['house_level'] = count($shinei_url) >= 3 ? 3 : 2;
          } else {
            $pic_arr['house_level'] = 1;
          }
          $pic_arr['pic'] = $shinei_url ? $shinei_url[0] : $huxing_url[0];
          $pic_arr['pic_tbl'] = 'upload';
          $pic_arr['pic_ids'] = $shinei_pic_id . $huxing_pic_id . $weituo_pic_id . $idcard_pic_id . $housecard_pic_id;
          $this->sell_house_model->update_info_by_id($pic_arr);
        } else {
          $this->sell_house_model->set_id($house_id);
          $pic_arr['house_level'] = 0;
          $this->sell_house_model->update_info_by_id($pic_arr);
        }

        if ($datainfo['video_id'] && $datainfo['video_pic']) {
          $this->sell_house_model->set_id($house_id);
          $video_arr['house_level'] = 6;
          $this->sell_house_model->update_info_by_id($video_arr);
        }

        //同步房源加积分
        if ($datainfo['is_outside'] == 1) {
          //增加同步房源积分
          $this->sell_house_model->set_search_fields(array('id', 'broker_id', 'is_outside', 'status', 'pic_tbl', 'pic_ids'));
          $this->sell_house_model->set_id($house_id);
          $owner_arr = $this->sell_house_model->get_info_by_id();
          //增加积分
          $this->load->model('api_broker_credit_model');
          $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_info['broker_id']));
          $credit_result = $this->api_broker_credit_model->rsync_fang100($owner_arr, 1);
          //判断积分是否增加成功
          if (is_full_array($credit_result) && $credit_result['status'] == 1) {
            $credit_score += $credit_result['score'];
          }
          //增加等级分值
          $this->load->model('api_broker_level_model');
          $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_info['broker_id']));
          $level_result = $this->api_broker_level_model->rsync_fang100($owner_arr, 1);
          //判断成长值是否增加成功
          if (is_full_array($level_result) && $level_result['status'] == 1) {
            $level_score += $level_result['score'];
          }
        }
        //合作是否加积分
        if ($credit_score != 0) {
          $credit_msg = '+' . $credit_score . '积分';
        }
        //合作是否加等级分值
        if ($level_score != 0) {
          $credit_msg .= ',+' . $level_score . '成长值';
        }
      }
    }

    if ($house_id && empty($datainfo['sell_type'])) {
      $this->result('1', '获取房源信息成功', $data['house_detail']);
    }
    if ($is_rsync_outer === 'no_verified') {
      $this->result(0, '您当前没有认证资料，无法同步，请关闭同步功能新录入');
    } else if ($is_rsync_outer === 'no_permission') {
      $this->result(0, '非本人房源无法同步，同步失败');
    } else if ($is_rsync_outer === 'status_failed') {
      $this->result(0, '该房源为非有效房源，无法同步，请修改房源状态');
    } else if ($datainfo['sell_type'] && !$is_reward_limit) {
      $this->result(0, '合作悬赏个数超过上限');
    } else if ($datainfo['sell_type'] && !$is_reward_max) {
      $this->result(0, '赏金不能超过总价的3%');
    } else if (!$coo_ziliao_check_1) {
      $this->result(0, '选择悬赏方式，必须上传委托协议书，卖家身份证及房产证');
    } else if (!$coo_ziliao_check_2) {
      $this->result(0, '选择佣金方式需满足：不上传任何资料或者 上传房产证及身份证，或者三证都传');
    } else if (!$coo_ziliao_check_3) {
      $this->result(0, '请重新上传合作资料');
    } else if (!$house_private_check) {
      $this->result(0, $house_private_check_text);
    } else if ($result < 0 && $datainfo['sell_type'] && $house_id) {
      $this->result('0', '房源修改失败');
    } else if ($house_id && $datainfo['sell_type'] && $result > 0) {
      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $broker_info['company_id'];
      $add_log_param['agency_id'] = $broker_info['agency_id'];
      $add_log_param['broker_id'] = $broker_id;
      $add_log_param['broker_name'] = $broker_info['truename'];
      $add_log_param['type'] = 3;
      $add_log_param['text'] = '出售房源 ' . 'CS' . $house_id . ' ' . $sell_cont;
      if ($devicetype == 'android') {
        $add_log_param['from_system'] = 2;
      } else {
        $add_log_param['from_system'] = 3;
      }
      $add_log_param['device_id'] = $deviceid;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);

      //添加视频日志
      if (empty($house_detail['video_id']) && $datainfo['video_id']) {
        //操作日志
        $add_log_param = array();
        $add_log_param['company_id'] = $broker_info['company_id'];
        $add_log_param['agency_id'] = $broker_info['agency_id'];
        $add_log_param['broker_id'] = $broker_id;
        $add_log_param['broker_name'] = $broker_info['truename'];
        $add_log_param['type'] = 9;
        $add_log_param['text'] = '出售房源 ' . 'CS' . $house_id;
        if ($devicetype == 'android') {
          $add_log_param['from_system'] = 2;
        } else {
          $add_log_param['from_system'] = 3;
        }
        $add_log_param['device_id'] = $deviceid;
        $add_log_param['from_ip'] = get_ip();
        $add_log_param['time'] = time();

        $this->operate_log_model->add_operate_log($add_log_param);
      }

      //添加合作审核提示
      if ($datainfo['isshare'] == 2) {
        $this->result('1', '您发布的合作店长审核中，请耐心等待');
      } else {
        $this->result('1', '房源修改成功！' . $credit_msg);
      }
    }
  }


  //添加图片
  public function add_image()
  {
    $data = array();
    $input_name = 'pic';
    $this->load->model('pic_model');
    $this->pic_model->set_filename($input_name);
    $data['pic_url'] = $this->pic_model->upload();
    $this->result('1', '返回图片信息', $data);
  }


  private function _get_cond_str($form_param)
  {
    $cond_where = '';
    //房源编号
    if (isset($form_param['house_id']) && $form_param['house_id'] > 0) {
      $house_id = intval($form_param['house_id']);
      $cond_where .= " AND id = '" . $house_id . "'";
      return $cond_where;
    }
    //楼层floor
    $floor_min = isset($form_param['floor_min']) ? intval($form_param['floor_min']) : 0;
    $floor_max = isset($form_param['floor_max']) ? intval($form_param['floor_max']) : 0;
    if ($floor_min || $floor_max) {
      $cond_where .= " AND floor >= '" . $floor_min . "'";
      $cond_where .= " AND floor <= '" . $floor_max . "'";
    }
    //板块
    $street = '';
    if (isset($form_param['street']) && !empty($form_param['street']) && $form_param['street'] > 0) {
      $street = intval($form_param['street']);
      $cond_where .= " AND street_id = '" . $street . "'";
    }
    //区属
    $district = '';
    if (isset($form_param['district']) && !empty($form_param['district']) && $form_param['district'] > 0) {
      $district = intval($form_param['district']);
      $cond_where .= " AND district_id = '" . $district . "'";
    }

    //楼盘名称
    if (!empty($form_param['block_name'])) {
      $cond_where .= " AND (block_name like '%" . $form_param['block_name'] . "%' or address like '%" . $form_param['block_name'] . "%' or title like '%" . $form_param['block_name'] . "%')";
    }

    //楼盘名称+经纪人名模糊查询
    if (!empty($form_param['broker_block_name'])) {
      $cond_where .= " AND (block_name like '%" . $form_param['broker_block_name'] . "%' or address like '%" . $form_param['broker_block_name'] . "%' or title like '%" . $form_param['broker_block_name'] . "%' or broker_name like '%" . $form_param['broker_block_name'] . "%' or telno1 like '%" . $form_param['broker_block_name'] . "%' or telno2 like '%" . $form_param['broker_block_name'] . "%' or telno3 like '%" . $form_param['broker_block_name'] . "%')";
    }

    //楼盘ID
    if (!empty($form_param['block_id']) && $form_param['block_id'] > 0) {
      $cond_where .= " AND block_id = '" . $form_param['block_id'] . "'";
    }

    //面积条件
    if (!empty($form_param['area']) && $form_param['area'] > 0) {
      $sell_area = intval($form_param['area']);
      $area = $this->house_config_model->get_config();
      $area_val = $area['sell_area'][$sell_area];
      if (!empty($area_val)) {
        $area_val = preg_replace("#[^0-9-]#", '', $area_val);
        $area_val = explode('-', $area_val);
        if (count($area_val) == 2) {
          $cond_where .= " AND buildarea between '$area_val[0]' AND '$area_val[1]' ";

        } else {
          if ($sell_area == 1) {
            $cond_where .= " AND buildarea < '$area_val[0]' ";
          } else {
            $cond_where .= " AND buildarea > '$area_val[0]' ";
          }
        }
      }
    }

    //价格条件
    if (isset($form_param['price']) && !empty($form_param['price']) && $form_param['price'] > 0) {
      $price = intval($form_param['price']);
      $sell_price = $this->house_config_model->get_config();
      $price_val = $sell_price['sell_price'][$price];
      if ($price_val) {
        $price_val = preg_replace("#[^0-9-]#", '', $price_val);
        $price_val = explode('-', $price_val);
        if (count($price_val) == 2) {
          $cond_where .= " AND price between '$price_val[0]' AND  '$price_val[1]' ";
        } else {
          if ($price == 1) {
            $cond_where .= " AND price < '$price_val[0]' ";
          } else {
            $cond_where .= " AND price > '$price_val[0]' ";
          }
        }

      }
    }

    //楼层条件
    if (isset($form_param['story']) && !empty($form_param['story']) && $form_param['story'] > 0) {
      $story = intval($form_param['story']);
      $story_config = $this->house_config_model->get_config();
      $story_val = $story_config['story'][$story];
      if ($story_val) {
        $story_val = preg_replace("#[^0-9-]#", '', $story_val);
        $story_val = explode('-', $story_val);
        if (count($story_val) == 2) {
          $cond_where .= " AND floor between '$story_val[0]' AND  '$story_val[1]' ";
        } else {
          if ($story_val == 1) {
            //$cond_where .= " AND price < '$price_val[0]' ";
          } else {
            $cond_where .= " AND floor > '$story_val[0]' ";
          }
        }
      }
    }

    //物业类型条件
    if (isset($form_param['property_type']) && !empty($form_param['property_type']) && $form_param['property_type'] > 0) {
      $sell_type = intval($form_param['property_type']);
      $cond_where .= " AND sell_type = '" . $sell_type . "'";
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


    //装修条件
    if (isset($form_param['fitment']) && !empty($form_param['fitment']) && $form_param['fitment'] > 0) {
      $fitment = intval($form_param['fitment']);
      $cond_where .= " AND fitment = '" . $fitment . "'";
    }

    //朝向条件
    if (isset($form_param['forward']) && !empty($form_param['forward']) && $form_param['forward'] > 0) {
      $forward = intval($form_param['forward']);
      $cond_where .= " AND forward = '" . $forward . "'";
    }

    //是否合作
    if (isset($form_param['is_share'])) {
      $is_share = intval($form_param['is_share']);
      $cond_where .= " AND isshare = '" . $is_share . "'";
    }

    //是否是合作朋友圈的
    if (isset($form_param['isshare_friend'])) {
      $isshare_friend = intval($form_param['isshare_friend']);
      $cond_where .= " AND isshare = 1 AND isshare_friend = '" . $isshare_friend . "'";
    }

    //是否合作悬赏
    if (isset($form_param['is_cooperate_reward']) && !empty($form_param['is_cooperate_reward']) && $form_param['is_cooperate_reward'] > 0) {
      $is_cooperate_reward = intval($form_param['is_cooperate_reward']);
      if ($is_cooperate_reward === 1) {
        $cond_where .= " AND cooperate_reward > 0 ";
      } else if ($is_cooperate_reward === 2) {
        $cond_where .= " AND cooperate_reward = 0 ";
      }
    }

    //楼层比例高中低
    if (isset($form_param['floor_scale']) && !empty($form_param['floor_scale'])) {
      $floor_scale = intval($form_param['floor_scale']);
      if (is_int($floor_scale) && $floor_scale > 0) {
        if (1 == $floor_scale) {
          $cond_where .= " AND floor_scale > 0.7 ";
        } else if (2 == $floor_scale) {
          $cond_where .= " AND floor_scale >= 0.4 AND floor_scale <= 0.7 ";
        } else if (3 == $floor_scale) {
          $cond_where .= " AND floor_scale < 0.4 ";
        }
      }
    }

    //房源创建时间范围
    if (!empty($form_param['create_time_range'])) {
      $searchtime = intval($form_param['create_time_range']);
      $now_time = time();
      switch ($searchtime) {
        case '1':
          $creattime = $now_time - 86400 * 1;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '2':
          $creattime = $now_time - 86400 * 7;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '3':
          $creattime = $now_time - 86400 * 30;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '4':
          $creattime = $now_time - 86400 * 90;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '5':
          $creattime = $now_time - 86400 * 180;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;
        default:
      }
    }

    //设置合作时间范围
    if (!empty($form_param['set_share_time'])) {
      $searchtime = intval($form_param['set_share_time'] && $form_param['set_share_time'] > 0);
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
      $cond_where .= " AND set_share_time>= '" . $creattime . "' ";
    }

    //经纪人
    if (!empty($form_param['broker_id']) && $form_param['broker_id'] != '' && $form_param['broker_id'] > 0) {
      $broker_id = intval($form_param['broker_id']);
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    if (!empty($form_param['agency_id']) && $form_param['agency_id'] != '' && $form_param['agency_id'] > 0) {
      $agency_id = intval($form_param['agency_id']);
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }
    if (!empty($form_param['company_id']) && $form_param['company_id'] != '') {
      $company_id = intval($form_param['company_id']);
      $cond_where .= " AND company_id = '" . $company_id . "'";
    }
    if (isset($form_param['is_outside']) && $form_param['is_outside'] != '-1') {
      $is_outside = intval($form_param['is_outside']);
      $cond_where .= " AND is_outside = '" . $is_outside . "'";
    }
    if (isset($form_param['house_degree']) && !empty($form_param['house_degree'])) {
      $house_degree = intval($form_param['house_degree']);
      $cond_where .= " AND house_degree = '" . $house_degree . "'";
    }
    if (isset($form_param['reward_type']) && !empty($form_param['reward_type'])) {
      $reward_type = intval($form_param['reward_type']);
      $cond_where .= " AND reward_type = '" . $reward_type . "'";
    }
    return $cond_where;
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
      case 11:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'ASC';
        break;
      case 12:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
        break;
      case 13:
        $arr_order['order_key'] = 'createtime';
        $arr_order['order_by'] = 'DESC';
        break;
      case 14:
        $arr_order['order_key'] = 'cooperate_reward';
        $arr_order['order_by'] = 'DESC';
        break;
      case 15:
        $arr_order['order_key'] = 'set_share_time';
        $arr_order['order_by'] = 'ASC';
        break;
      case 16:
        $arr_order['order_key'] = 'set_share_time';
        $arr_order['order_by'] = 'DESC';
        break;
      case 17:
        $arr_order['order_key'] = 'house_degree';
        $arr_order['order_by'] = 'DESC';
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

  //查询楼盘区属板块信息
  public function get_cmtinfo_by_kw()
  {
    $data = array();
    $keyword = $this->input->get('keyword', TRUE);
    $this->load->model('community_model');
    $select_fields = array('id', 'cmt_name', 'dist_id', 'streetid', 'address', 'averprice', 'status', 'build_date');
    $this->community_model->set_select_fields($select_fields);
    $cmt_info = $this->community_model->auto_cmtname($keyword, 10);
    $cmt_arr = array();
    foreach ($cmt_info as $key => $val) {
      $cmt_arr[$key]['cmt_id'] = $val['id'];
      $cmt_arr[$key]['cmt_name'] = $val['cmt_name'];
      $cmt_arr[$key]['dist_id'] = $val['dist_id'];
      $cmt_arr[$key]['streetid'] = $val['streetid'];
      $cmt_arr[$key]['averprice'] = $val['averprice'];
      $cmt_arr[$key]['status'] = $val['status'];
      $cmt_arr[$key]['build_date'] = $val['build_date'];
      $cmt_arr[$key]['address'] = $val['address'];
      $cmt_arr[$key]['districtname'] = $val['districtname'];
      $cmt_arr[$key]['streetname'] = $val['streetname'];


    }
    if ($cmt_arr) {
      $data['cmt_info'] = $cmt_arr;
      $this->result('1', '楼盘名称区属板块', $data);
    }
    if (empty($cmt_info)) {
      $data['result'] = 0;
      $this->result('0', '暂无小区');
    }


  }

  //删除房源
  public function del()
  {
    //获取当前登录人信息
    $broker_info = $this->user_arr;
    $house_id = $this->input->get('house_id', TRUE);
    $house_id = trim($house_id);
    $house_id = trim($house_id, ',');
    $devicetype = $this->input->get('api_key', TRUE);
    $deviceid = $this->input->get('deviceid', TRUE);
    $up_num = '';

    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'nature', 'company_id'));
    $this->sell_house_model->set_id($house_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    //修改房源权限
    $house_modify_per = $this->broker_permission_model->check('8', $owner_arr);
    //修改房源关联门店权限
    $agency_house_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '4');
    if (!$house_modify_per['auth']) {
      $this->result('-1', '暂无权限');
      exit();
    } else {
      if (!$agency_house_modify_per) {
        $this->result('-1', '暂无权限');
        exit();
      }
    }

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    $role_level = intval($broker_info['role_level']);
    //店长以下的经纪人不允许操作他人的私盘
    if (is_int($role_level) && $role_level > 6) {
      if ($owner_arr['broker_id'] != $broker_info['broker_id'] && $owner_arr['nature'] == '1') {
        $this->result('-1', '店长以下的经纪人不允许操作他人的私盘');
        exit();
      }
    }

    if ($house_id) {
      $arr = array('status' => 5, 'isshare' => 0, 'isshare_friend' => 0);
      $cond_where = "id = " . $house_id . "";
      $up_num = $this->sell_house_model->update_info_by_cond($arr, $cond_where);
    }
    if ($up_num > 0) {
      //操作日志
      $this->sell_house_model->set_search_fields(array('block_name', 'address', 'dong', 'unit', 'door'));
      $this->sell_house_model->set_id(intval($house_id));
      $datainfo = $this->sell_house_model->get_info_by_id();

      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 4;
      $add_log_param['text'] = '出售房源 ' . 'CS' . $house_id;
      if ($devicetype == 'android') {
        $add_log_param['from_system'] = 2;
      } else {
        $add_log_param['from_system'] = 3;
      }
      $add_log_param['device_id'] = $deviceid;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);

      //删除房源，终止与房源有关系的合作
      $stop_reason = 'delete_house';
      $this->load->model('cooperate_model');
      $this->cooperate_model->stop_cooperate($house_id, 'sell', $stop_reason);
    }
    if ($up_num > 0) {
      $this->result('1', '出售房源注销成功');
    } else {
      $this->result('0', '出售房源注销失败');
    }
  }

  //设置合作
  public function set_share()
  {
    $this_broker_id = intval($this->user_arr['broker_id']);
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];
    if ('0' === $data['open_cooperate']) {
      $this->result('-1', '当前公司尚未开启合作中心');
      exit();
    }

    $str = $this->input->get('house_id', TRUE);
    $str = intval($str);
    //新权限
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'status', 'price', 'company_id'));
    $this->sell_house_model->set_id($str);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    if ($owner_arr['status'] != 1) {
      $this->result('-1', '该房源非有效状态，不能设置合作');
      exit();
    }
    $cooperate_per = $this->broker_permission_model->check('2', $owner_arr);
    if (!$cooperate_per['auth']) {
      $this->result('-1', '暂无权限');
      exit();
    }

    $flag = 1;
    //判断是否开启了合作审核
    if (isset($company_basic_data['check_cooperate']) && '1' == $company_basic_data['check_cooperate']) {
      $flag = 2;
    }

    $isshare_friend = $this->input->get('isshare_friend', TRUE);  //是否发到朋友圈

    $commission_ratio = $this->input->get('commission_ratio', TRUE);  //佣金分成

    if ($isshare_friend) {
      $cooperate_reward = 0;
      $is_reward_limit = true;
      $is_reward_max = true;
    } else {
      $cooperate_reward = $this->input->get('cooperate_reward', TRUE);//合作赏金

      $is_reward_limit = true;
      $is_reward_max = true;
      //获取当前经纪人发布悬赏房源的数量
      $reward_where_cond = 'set_reward_broker_id = "' . $this_broker_id . '"' . ' and isshare != 0 and status = 1 and cooperate_reward > 0';
      $cooperate_reward_num = $this->sell_house_model->get_housenum_by_cond($reward_where_cond);
      if (isset($cooperate_reward) && !empty($cooperate_reward) && is_int($cooperate_reward_num) && $cooperate_reward_num > 4) {
        $is_reward_limit = false;
      }
      //赏金上限
      if ($cooperate_reward > floatval($owner_arr['price']) * 10000 * 0.02) {
        $is_reward_max = false;
      }
      $isshare_friend = 0;
    }

    if (!$is_reward_limit) {
      $this->result('0', '合作悬赏个数超过上限');
      exit;
    } else if (!$is_reward_max) {
      $this->result('0', '赏金不能超过总价的2%');
      exit;
    } else {
      $this->change_share($str, $flag, intval($cooperate_reward), $this_broker_id, $isshare_friend, $commission_ratio);
    }

  }

  //取消合作
  public function cancel_share()
  {
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];
    if ('0' === $data['open_cooperate']) {
      $this->result('-1', '当前公司尚未开启合作中心');
      exit();
    }

    $str = $this->input->get('house_id', TRUE);
    $str = intval($str);

    //新权限
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'status', 'company_id'));
    $this->sell_house_model->set_id($str);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    if ($owner_arr['status'] != 1) {
      $this->result('-1', '该房源非有效状态，不能取消合作');
      exit();
    }
    $cooperate_per = $this->broker_permission_model->check('2', $owner_arr);
    if (!$cooperate_per['auth']) {
      $this->result('-1', '暂无权限');
      exit();
    }

    $flag = 0;
    $this->change_share($str, $flag);
  }

  //合作房源
  public function change_share($str, $flag, $cooperate_reward = 0, $set_reward_broker_id = 0, $isshare_friend = 0, $commission_ratio = 0)
  {
    $flag = intval($flag);
    $str = trim($str);
    $up_num = '';
    $score_result = array();
    if ($str && $flag <= 2 && $flag >= 0) {
      $arr = array('isshare' => $flag, 'isshare_friend' => $isshare_friend);
      //设置合作时间
      if (1 == $flag || 2 == $flag) {
        $arr['set_share_time'] = time();
      }
      //添加合作赏金
      if ((1 == $flag || 2 == $flag)) {
        if ($cooperate_reward > 0) {
          $arr['reward_type'] = 2;
          $arr['cooperate_reward'] = $cooperate_reward;
          $arr['set_reward_broker_id'] = $set_reward_broker_id;
        } else {
          $arr['reward_type'] = 1;
          $arr['commission_ratio'] = $commission_ratio;
        }
      } else if (0 === $flag) {
        //取消合作
        $arr['cooperate_reward'] = 0;
        $arr['set_reward_broker_id'] = 0;
        $arr['reward_type'] = 1;
        $arr['cooperate_check'] = 1;
        $arr['house_degree'] = 0;
        $arr['commission_ratio'] = 0;
        //删除合作资料图片
        $this->sell_house_model->set_search_fields(array('pic_ids', 'pic_tbl'));
        $this->sell_house_model->set_id(intval($str));
        $house_detail = $this->sell_house_model->get_info_by_id();
        if ($house_detail['pic_ids'] && $house_detail['pic_tbl']) {
          $house_detail['picinfo'] = $this->pic_model->find_house_pic_by_ids($house_detail['pic_tbl'], $house_detail['pic_ids']);
          $pic_str_del = '';
          $pic_str_liu = '';
          if (is_full_array($house_detail['picinfo'])) {
            foreach ($house_detail['picinfo'] as $k => $v) {
              if (3 == $v['type'] || 4 == $v['type'] || 5 == $v['type']) {
                $pic_str_del .= $v['id'] . ',';
              }
              if (1 == $v['type'] || 2 == $v['type']) {
                $pic_str_liu .= $v['id'] . ',';
              }
            }
          }

          if (!empty($pic_str_liu)) {
            $arr['pic_ids'] = $pic_str_liu;
          }

          $this->pic_model->del_pic_by_ids($pic_str_del, $house_detail['pic_tbl']);
        }
      }

      $cond_where = "id IN (0," . $str . ") AND isshare <> {$flag}";

      //跟进
      $this->load->model('follow_model');
      $ids_arr = array();
      $this->sell_house_model->set_search_fields(array("id"));
      $list = $this->sell_house_model->get_list_by_cond($cond_where);

      $text = $flag ? "是否合作:否>>是" : "是否合作:是>>否";

      foreach ($list as $key => $val) {
        $needarr = array();
        $needarr['broker_id'] = $this->user_arr['broker_id'];
        $needarr['house_id'] = $val['id'];
        $needarr['agency_id'] = $this->user_arr['agency_id'];//门店ID
        $needarr['company_id'] = $this->user_arr['company_id'];//总公司id
        $needarr['type'] = 1;
        $needarr['text'] = $text;
        $bool = $this->follow_model->house_save($needarr);
        $ids_arr[] = $val['id'];
      }

      $up_num = $this->sell_house_model->update_info_by_cond($arr, $cond_where);
    }

    if ($up_num > 0) {

      if ($flag == 1) {
        $credit_msg = '';
        //增加积分
        $this->load->model('api_broker_credit_model');
        $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
        $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $str), 1);
        //判断积分是否增加成功
        if (is_full_array($credit_result) && $credit_result['status'] == 1) {
          $credit_msg .= '+' . $credit_result['score'] . '积分';
        }
        //增加等级分值
        $this->load->model('api_broker_level_model');
        $this->api_broker_level_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
        $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $str), 1);
        //判断成长值是否增加成功
        if (is_full_array($level_result) && $level_result['status'] == 1) {
          $credit_msg .= ',+' . $level_result['score'] . '成长值';
        }
        $this->result('1', '设置合作成功！' . $credit_msg);
      } else if ($flag == 2) {
        $this->result('1', '当前公司开启合作审核，请等待审核...');
      } else {
        //取消合作后，终止与房源有关系的合作
        $stop_reason = 'private_house';
        $this->load->model('cooperate_model');
        $this->cooperate_model->stop_cooperate($str, 'sell', $stop_reason);
        $this->result('1', '取消合作成功');

      }

    } else {
      if ($flag == 2) {
        $this->result('0', '该房源已经发送审核');
      } else {
        $this->result('0', '设置合作失败');
      }
    }


  }

  //跟进方式配置信息
  public function config_follow()
  {
    $data = array();
    $follow_arr = array();
    //获取跟进方式
    $this->load->model('follow_model');
    $type_tbl = 'follow_up';
    $this->follow_model->set_tbl($type_tbl);
    $type_list = $this->follow_model->get_type();
    foreach ($type_list as $key => $val) {
      for ($i = 0; $i < 5; $i++) {
        $follow_arr[$i]['name'] = $type_list[$i]['follow_name'];
        $follow_arr[$i]['key'] = $type_list[$i]['sort'];
      }
    }
    $data['list'] = $follow_arr;
    $this->result('1', '跟进方式配置信息', $data);
  }

  //查看跟进
  public function follow()
  {
    $data = array();
    $broker_info = $this->user_arr;
    //经纪人id
    $broker_id = $broker_info['broker_id'];
    $house_id = $this->input->get('house_id', TRUE);
    $house_id = intval($house_id);
    $devicetype = $this->input->get('api_key', TRUE);
    $deviceid = $this->input->get('deviceid', TRUE);

    //新权限 出售房源查看跟进权限
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->sell_house_model->set_id($house_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    $view_follow_per = $this->broker_permission_model->check('10', $owner_arr);
    //出售房源跟进关联门店权限
    $agency_house_follow_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '5');
    if (!$view_follow_per['auth']) {
      $this->result('-1', '暂无权限');
      exit();
    } else {
      if (!$agency_house_follow_per) {
        $this->result('-1', '暂无权限');
        exit();
      }
    }

    $where_arr = "house_id = '" . $house_id . "'";
    $where_arr .= " AND (follow_type = 1 OR follow_type = 3)";
    //$where_arr.=" AND (broker_id = '".$broker_id."' OR broker_id = 0)";
    $where_arr .= "  AND type = 1 ";
    $follow_tbl = 'detailed_follow';
    $follow_config = $this->follow_model->get_config();
    $this->follow_model->set_tbl($follow_tbl);
    // 分页参数
    $page = $this->input->get('page', TRUE);
    $pagesize = $this->input->get('pagesize', TRUE);
    if (!$page) {
      $page = 1;
    }
    if (!$pagesize) {
      $pagesize = 5;
    }
    $this->_init_pagination($page, $pagesize);


    $follow_lists = $this->follow_model->get_lists($where_arr, $this->_offset, $this->_limit);
    $this->load->model('api_broker_model');
    $tbl = 'buy_customer';
    $this->buy_customer_model->set_tbl($tbl);
    $where = "company_id = '" . $broker_info['company_id'] . "'";
    $lists = $this->buy_customer_model->get_customer($where);

    //获取客源
    foreach ($lists as $key => $val) {
      $acustomer_list[$val['id']] = $val;
    }
    $follow_arr = array();
    if ($follow_lists) {
      foreach ($follow_lists as $key => $val) {
        $follow_arr[$key]['follow_way'] = $follow_config['follow_way'][$val['follow_way']];
        $follow_arr[$key]['follow_time'] = strtotime($val['date']);
        $follow_arr[$key]['follow_value'] = $val['text'];
        if ($val['broker_id']) {
          $broker_messagin = $this->api_broker_model->get_baseinfo_by_broker_id($val['broker_id']);
          $follow_arr[$key]['follow_broker_name'] = $broker_messagin['truename'];
        } else {
          $follow_arr[$key]['follow_broker_name'] = '系统官理员';
        }
        if ($val['customer_id']) {
          $follow_arr[$key]['follow_customer_name'] = $acustomer_list[$val['customer_id']]['truename'];
        }
        $follow_arr[$key]['foll_type'] = !in_array($val['follow_way'], array(7, 8, 11, 12)) ? 1 : 2;
      }

    }
    $data['follow_lists'] = $follow_arr;

    //操作日志
    $this->sell_house_model->set_search_fields(array('block_name', 'address', 'dong', 'unit', 'door'));
    $this->sell_house_model->set_id(intval($house_id));
    $datainfo = $this->sell_house_model->get_info_by_id();

    $add_log_param = array();
    $add_log_param['company_id'] = $this->user_arr['company_id'];
    $add_log_param['agency_id'] = $this->user_arr['agency_id'];
    $add_log_param['broker_id'] = $this->user_arr['broker_id'];
    $add_log_param['broker_name'] = $this->user_arr['truename'];
    $add_log_param['type'] = 5;
    $add_log_param['text'] = '出售房源 ' . 'CS' . $house_id;
    if ($devicetype == 'android') {
      $add_log_param['from_system'] = 2;
    } else {
      $add_log_param['from_system'] = 3;
    }
    $add_log_param['device_id'] = $deviceid;
    $add_log_param['from_ip'] = get_ip();
    $add_log_param['mac_ip'] = '127.0.0.1';
    $add_log_param['from_host_name'] = '127.0.0.1';
    $add_log_param['hardware_num'] = '测试硬件序列号';
    $add_log_param['time'] = time();

    $this->operate_log_model->add_operate_log($add_log_param);

    $this->result('1', '查看出售房源的跟进信息成功', $data);


  }

  //添加跟进
  public function addfollow()
  {
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (isset($company_basic_data['follow_text_num']) && $company_basic_data['follow_text_num'] > 0) {
      $follow_text_num = intval($company_basic_data['follow_text_num']);
    } else {
      $follow_text_num = 0;
    }

    $broker_info = $this->user_arr;
    $follow_arr = array();
    $follow_arr['house_id'] = $this->input->get('house_id', TRUE);//房源id
    $follow_arr['broker_id'] = $broker_info['broker_id'];//经纪人的ID
    $follow_arr['agency_id'] = $broker_info['agency_id'];//门店ID
    $follow_arr['company_id'] = $broker_info['company_id'];//总公司id
    $follow_arr['follow_way'] = $this->input->get('follow_type', TRUE);//跟进方式
    $follow_arr['customer_id'] = $this->input->get('customer_id', TRUE);//客户id
    $follow_arr['type'] = 1;//客户类型
    $follow_arr['follow_type'] = 1;//跟进类型
    $follow_arr['text'] = $this->input->get('text', TRUE);//跟进内容
    $follow_arr['date'] = date('Y-m-d H:i:s');//跟进时间
    $devicetype = $this->input->get('api_key', TRUE);
    $deviceid = $this->input->get('deviceid', TRUE);

    if ($follow_text_num > 0 && (mb_strlen($follow_arr['text']) < $follow_text_num)) {
      $this->result('2', '跟进内容不得少于' . $follow_text_num . '字');
    } else {
      //加载跟进MODEL
      $this->load->model('follow_model');
      $tbl = 'detailed_follow';
      $this->follow_model->set_tbl($tbl);
      $follow_id = '';
      if ($follow_arr['follow_way'] && $follow_arr['text']) {
        $follow_id = $this->follow_model->add_follow($follow_arr);
        $follow_date_info = array();
        $follow_date_info['updatetime'] = time();
        $this->sell_house_model->set_id($follow_arr['house_id']);
        $result = $this->sell_house_model->update_info_by_id($follow_date_info);
      }

      $data = array();
      $data['follow_id'] = $follow_id;
      if ($follow_id > 0) {
        //出售房源堪房-带看记录工作统计日志
        if ($follow_arr['follow_way'] == 1) {
          $this->info_count($follow_arr['house_id'], 4);
        } elseif ($follow_arr['follow_way'] == 5) {
          $this->info_count($follow_arr['house_id'], 5, $follow_arr['customer_id']);
        } else {
          $this->info_count($follow_arr['house_id'], 9);
        }
        //操作日志
        $add_log_param = array();
        $follow_way_str = '';
        if ('1' == $follow_arr['follow_way']) {
          $follow_way_str = '堪房跟进';
        } else if ('3' == $follow_arr['follow_way']) {
          $follow_way_str = '电话跟进';
        } else if ('4' == $follow_arr['follow_way']) {
          $follow_way_str = '磋商跟进';
        } else if ('5' == $follow_arr['follow_way']) {
          $follow_way_str = '带看跟进';
        } else {
          $follow_way_str = '其它跟进';
        }
        $add_log_param['company_id'] = $this->user_arr['company_id'];
        $add_log_param['agency_id'] = $this->user_arr['agency_id'];
        $add_log_param['broker_id'] = $this->user_arr['broker_id'];
        $add_log_param['broker_name'] = $this->user_arr['truename'];
        $add_log_param['type'] = 46;
        $add_log_param['text'] = '出售房源 ' . 'CS' . $follow_arr['house_id'] . ' ' . $follow_way_str . ' ' . $follow_arr['text'];
        if ($devicetype == 'android') {
          $add_log_param['from_system'] = 2;
        } else {
          $add_log_param['from_system'] = 3;
        }
        $add_log_param['device_id'] = $deviceid;
        $add_log_param['from_ip'] = get_ip();
        $add_log_param['mac_ip'] = '127.0.0.1';
        $add_log_param['from_host_name'] = '127.0.0.1';
        $add_log_param['hardware_num'] = '测试硬件序列号';
        $add_log_param['time'] = time();

        $this->operate_log_model->add_operate_log($add_log_param);
        $this->result('1', '添加跟进信息成功', $data);
      } else {
        $this->result('0', '添加跟进信息失败', $data);
      }
    }

  }

  public function source()
  {
    //模板使用数据
    $data = array();
    $post_param = $this->input->get('customer_name', TRUE);
    $broker_info = $this->user_arr;
    //经纪人的ID
    $broker_id = $broker_info['broker_id'];
    //加载客源MODEL
    $this->load->model('buy_customer_model');
    // 分页参数
    $pagee = $this->input->get('page', TRUE);
    if (!$pagee) {
      $pagee = 1;
    }
    $pagesize = $this->input->get('pagesize', TRUE);
    $page = isset($pagee) ? intval($pagee) : intval($page);
    if (!$pagesize) {
      $pagesize = 5;
    }
    $this->_init_pagination($page, $pagesize);
    //求购信息表名
    $tbl = 'buy_customer';
    //查询条件
    $cond_where = "broker_id = '" . $broker_id . "'";
    //表单提交参数组成的查询条件
    if (isset($post_param) && !empty($post_param)) {
      $cond_where_ext = $post_param;
      $cond_where_extt = "AND truename LIKE '%" . $cond_where_ext . "%'";
      $cond_where .= $cond_where_extt;
    }
    $this->buy_customer_model->set_tbl($tbl);
    $list = $this->buy_customer_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit);
    $customer_list = array();
    if ($list) {
      foreach ($list as $key => $val) {
        $customer_list[$key]['customer_id'] = $val['id'];
        $customer_list[$key]['customer_name'] = $val['truename'];
        $customer_list[$key]['customer_price'] = strip_end_0($val['price_min']) . '-' . strip_end_0($val['price_max']);
      }
    }
    $data['customer_list'] = $customer_list;
    if ($list) {
      $this->result('1', '返回客户信息成功', $data);

    } else {
      $this->result('1', '抱歉没有您相关的客源信息', $data);
    }
  }

  //房源详情
  public function details_house()
  {
    $house_id = $this->input->get('house_id', TRUE);
    $type = $this->input->get('type', TRUE);
    if (!$type) {
      $type = 0;
    }
    $this->details($house_id, $type);
  }

  public function details($house_id, $type)
  {
    //新权限
    //范围（个人或全公司）
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id', 'isshare'));
    $this->sell_house_model->set_id($house_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    if ($type == 0) {
      $view_other_per = $this->broker_permission_model->check('1', $owner_arr);
      if (!$view_other_per['auth']) {
        $this->result('-1', '暂无权限');
        exit();
      }
    }
    if ($type == 1 && $owner_arr['isshare'] == 0) {
      $this->result(0, '对不起该合作已被经纪人取消！');
      exit();
    }
    $data = array();
    /***
     * //新权限 判断是否明文显示业主电话
     * if(is_full_array($owner_arr)){
     * $is_phone_per = $this->broker_permission_model->check('9',$owner_arr);
     * $data['is_phone_per'] = $is_phone_per['auth'];
     * }else{
     * $data['is_phone_per'] = false;
     * }
     ***/
    $house_info = array();
    //获取出售信息基本配置资料
    $config = $this->house_config_model->get_config();
    //基本信息‘状态’数据处理
    if (!empty($config['status']) && is_array($config['status'])) {
      foreach ($config['status'] as $k => $v) {
        if ('暂不售（租）' == $v) {
          $config['status'][$k] = '暂不售';
        }
      }
    }
    $broker_info = $this->user_arr;
    $broker_id = $broker_info['broker_id'];
    $house_id = intval($house_id);
    //房源合作佣金分配
    $this->load->model('sell_house_share_ratio_model');
    $house_money = $this->sell_house_share_ratio_model->get_house_ratio_by_rowid($house_id);
    $house_ratio = array();
    if ($house_money) {
      $house_ratio['buyer_ratio'] = strip_end_0($house_money['buyer_ratio']) . '%';
      $house_ratio['seller_ratio'] = strip_end_0($house_money['seller_ratio']) . '%';
      $house_ratio['a_ratio'] = strip_end_0($house_money['a_ratio']) . '%';
      $house_ratio['b_ratio'] = strip_end_0($house_money['b_ratio']) . '%';
    }

    //设置查询字段
    $fileld = array('id', 'block_id', 'broker_id', 'block_name', 'district_id', 'street_id', 'sell_type', 'fitment', 'price', 'address', 'room', 'hall', 'title', 'keys', 'isshare', 'status', 'entrust', 'createtime', 'buildarea', 'bewrite', 'taxes', 'forward', 'avgprice', 'totalfloor', 'floor', 'nature', 'toilet', 'buildyear', 'subfloor', 'floor_type', 'updatetime', 'pic_ids', 'pic_tbl', 'cooperate_reward', 'sell_tag', 'is_outside', 'house_degree', 'reward_type', 'video_id', 'video_pic', 'isshare_friend', 'commission_ratio');
    $this->sell_house_model->set_search_fields($fileld);
    $this->sell_house_model->set_id($house_id);
    $house_info = $this->sell_house_model->get_info_by_id();
    $house_list = array();
    $house_list['status'] = $config['status'][$house_info['status']];//状态
    $house_list['sell_type'] = $config['sell_type'][$house_info['sell_type']];//类型
    if (isset($house_info['video_id']) && !empty($house_info['video_id'])) {
      $house_list['title'] = '[视频房源]' . $house_info['title'];//房源标题
    } else {
      $house_list['title'] = $house_info['title'];//房源标题
    }
    $house_list['fitment'] = $config['fitment'][$house_info['fitment']];//装修

    if ($house_info['forward']) {
      $house_list['forward'] = $config['forward'][$house_info['forward']];//朝向
    } else {
      $house_list['forward'] = '';//朝向
    }


    $house_list['sell_tag_result'] = array();
    if (!empty($house_info['sell_tag'])) {
      $sell_tag_arr = explode(',', $house_info['sell_tag']);
      if (is_full_array($sell_tag_arr)) {
        foreach ($sell_tag_arr as $k => $v) {
          $house_list['sell_tag_result'][] = array('key' => $v, 'name' => $config['sell_tag'][$v]);
        }
      }
    }
    //是否同步到fang100
    $house_list['is_outside'] = $house_info['is_outside'];
    //钥匙
    $house_list['keys'] = $house_info['keys'];
    if ($house_info['taxes']) {
      $house_list['taxes'] = $config['taxes'][$house_info['taxes']];//税费
    } else {
      $house_list['taxes'] = '';
    }
    $house_list['updatetime'] = date('Y-m-d', $house_info['updatetime']);//跟进时间
    $house_list['createtime'] = date('Y-m-d', $house_info['createtime']);
    //录入时间
    $house_list['house_id'] = $house_info['id'];
    $house_list['block_name'] = $house_info['block_name'];
    $house_list['price'] = strip_end_0($house_info['price']);//总价
    $house_list['avgprice'] = round(strip_end_0($house_info['avgprice']));//单价
    $house_list['buildarea'] = strip_end_0($house_info['buildarea']);//面积
    $house_list['video_id'] = $house_info['video_id'];
    $house_list['video_pic'] = $house_info['video_pic'];
    //是否为独家代理
    if ($house_info['entrust'] == 1) {
      $house_list['entrust'] = 1;
    } else {
      $house_list['entrust'] = 0;
    }
    if ($house_info['entrust']) {
      $house_list['entrust'] = $config['entrust'][$house_info['entrust']];//委托类型
    } else {
      $house_list['entrust'] = '';//委托类型
    }
    $house_list['bewrite'] = strip_tags($house_info['bewrite']);//房源描述
    $house_list['nature'] = $config['nature'][$house_info['nature']];//房源性质
    $house_list['hall_type'] = $house_info['room'] . '室' . $house_info['hall'] . '厅' . $house_info['toilet'] . '卫';
    $house_list['is_share'] = $house_info['isshare'];
    $house_list['cooperate_reward'] = $house_info['cooperate_reward'];
    $house_list['buildyear'] = $house_info['buildyear'];//建筑年代
    $house_list['district_name'] = $this->district_model->get_distname_by_id($house_info['district_id']);//区属名称
    $house_list['street_name'] = $this->district_model->get_streetname_by_id($house_info['street_id']);//板块
    //楼层
    if ($type == 1) //合作中心调接口
    {
      if ($house_info['sell_type'] == 1 || $house_info['sell_type'] == 4) {
        $floor_info_rate = $house_info['floor'] / $house_info['totalfloor'];
        if ($floor_info_rate < 0.4) {
          $house_list['floor_info'] = '低';
        } else if ($floor_info_rate >= 0.4 && $floor_info_rate <= 0.7) {
          $house_list['floor_info'] = '中';
        } else {
          $house_list['floor_info'] = '高';
        }

      } else {
        $house_list['floor_info'] = '低';
      }
      $house_list['floor_info'] = $house_list['floor_info'] . '楼层/' . $house_info['totalfloor'];
      //判断是否自己发布的房源
      $house_list['my_house'] = $this->user_arr['broker_id'] == $house_info['broker_id'] ? 1 : 0;
      //检测是否已经合作
      $this->load->model('cooperate_model');
      $is_applay_coop = $this->cooperate_model->check_is_cooped_by_houseid(array($house_id), 'sell', $broker_id);
      $house_list['is_applay_coop'] = $is_applay_coop[$house_id];
    } else {
      if ($house_info['floor_type'] == 1) {
        $house_list['floor_info'] = $house_info['floor'] . '/' . $house_info['totalfloor'];//单层
      }
      if ($house_info['floor_type'] == 2) {
        $house_list['floor_info'] = $house_info['floor'] . '-' . $house_info['subfloor'] . '/' . $house_info['totalfloor'];//跃层
      }
    }
    //共享时返回佣金分配
    if ($house_ratio && $house_info['isshare'] == 1) {
      $data['house_ratio'] = $house_ratio;
    } else {
      $data['house_ratio'] = array('key' => '0', 'name' => '0');
    }
    //经纬度
    $select_fields = array('b_map_x', 'b_map_y');
    $this->community_model->set_select_fields($select_fields);
    $community_longitude = $this->community_model->get_cmtinfo_longitude($house_info['block_id']);
    if ($community_longitude) {
      $house_list['b_map_x'] = $community_longitude['b_map_x'];
      $house_list['b_map_y'] = $community_longitude['b_map_y'];
    } else {
      $house_list['b_map_x'] = '0';
      $house_list['b_map_y'] = '0';
    }
    $house_list['reward_type'] = $house_info['reward_type'];//奖励类别
    $house_list['house_degree'] = $house_info['house_degree'];//房源特色

    $house_list['m_house_url'] = '';
    if (isset($this->user_arr['city_spell']) && !empty($this->user_arr['city_spell'])) {
      $city_spell = $this->user_arr['city_spell'];
      $house_list['m_house_url'] = MLS_MOBILE_URL . '/' . $city_spell . '/s/d/' . $house_id . '.html';//房源详情微信地址
    }
    if ($house_info['reward_type'] == 1) {
      //获取佣金分配
      $commission_ratio = $this->sell_house_model->get_commission_ratio_id($house_info['commission_ratio']);
      $house_list['commission_ratio_arr'] = $this->sell_house_model->get_commission_ratio($config['commission_ratio'][$commission_ratio]);
    }

    //获取经纪人基本信息
    $broker_mess = array();
    $this->load->model('api_broker_model');
    $broker_messagin = $this->api_broker_model->get_baseinfo_by_broker_id($house_info['broker_id']);
    //获取门店所属公司名
    $company_name = '';
    if (isset($broker_messagin['company_id']) && !empty($broker_messagin['company_id'])) {
      $company_where_cond = array(
        'id' => $broker_messagin['company_id'],
        'company_id' => 0
      );
      $company_data = $this->agency_model->get_one_by($company_where_cond);
      if (is_full_array($company_data)) {
        $company_name = $company_data['name'];
      }
    }
    $broker_mess['broker_name'] = $broker_messagin['truename']?$broker_messagin['truename']:'';
    $broker_mess['agency_name'] = $broker_messagin['agency_name']?$broker_messagin['agency_name']:'';
    $broker_mess['company_name'] = $company_name;
    $broker_mess['broker_tel'] = $broker_messagin['phone']?$broker_messagin['phone']:'';
    $broker_mess['broker_id'] = $house_info['broker_id'];
    //当前房源的经纪人与我之间的关系 0自己,1已添加,2申请中,3可添加
    if ($broker_id == $house_info['broker_id']) {
      $broker_mess['status_friend'] = 0;
    } else {
      $friend_info = $this->cooperate_friends_base_model->get_friend_by_broker_id($broker_id, $house_info['broker_id']);
      if (is_full_array($friend_info)) {
        $broker_mess['status_friend'] = 1;
      } else {
        //申请信息
        $apply_info = $this->cooperate_friends_base_model->get_apply_by_broker_id($broker_id, $house_info['broker_id']);
        if (is_full_array($apply_info)) {
          $broker_mess['status_friend'] = 2;
        } else {
          $broker_mess['status_friend'] = 3;
        }
      }
    }
    $data['broker_messagin'] = $broker_mess;
    //获取楼盘信息
    $community_info = $this->community_model->find_cmt($house_info['block_id']);
    $community_arr = array();
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $buy_street[$val['id']] = $val;
    }

    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $buy_district[$val['id']] = $val;
    }
    foreach ($community_info as $key => $val) {
      $community_arr['cmt_name'] = $val['cmt_name'];//楼盘名称
      $community_arr['address'] = $val['address'];//楼盘地址
      $community_arr['dis_street'] = $buy_district[$val['dist_id']]['district'] . '-' . $buy_street[$val['streetid']]['streetname'];

    }
    //经纪人信用信息
    $return_broker_list = $this->sincere($house_info['broker_id']);
    $return_broker_list['broker_name'] = $return_broker_list['broker_name']?$return_broker_list['broker_name']:'';
    $return_broker_list['photo'] = $return_broker_list['photo']? $return_broker_list['photo']:'';
    $return_broker_list['cop_suc_ratio'] = $return_broker_list['cop_suc_ratio']?$return_broker_list['cop_suc_ratio']:'';

    $data['broker_list'] = $return_broker_list;
    if ($community_arr) {
      $data['community_info'] = $community_arr;
    } else {
      $data['community_info'] = array('cmt_name' => '', 'address' => '', 'dis_street' => '');
    }

    //房源图片
    $picinfo = $this->pic_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
    $shineipic = $huxingpic = array();

    if ($picinfo) {
      foreach ($picinfo as $key => $val) {
        if ($val['type'] == 1) {
          //$shineipic[] = str_replace('thumb/','',$val);
          $shineipic[] = changepic($val);
        } else if ($val['type'] == 2) {
          //$huxingpic[] = str_replace('thumb/','',$val);
          $huxingpic[] = changepic($val);
        }
      }
    }


    if ($huxingpic) {
      $data['huxingpic'] = $huxingpic;
    } else {
      $data['huxingpic'] = array('key' => '0', 'name' => '0');
    }
    if ($shineipic) {
      $data['shineipic'] = $shineipic;
    } else {
      $data['shineipic'] = array('key' => '0', 'name' => '0');
    }

    if ($house_list) {
      $data['house_list'] = $house_list;
    } else {
      $data['house_list'] = array('key' => '0', 'name' => '0');
    }
    //判断该房源是否是申请合作状态
    if ($house_id) {
      $cooperate_where = array();
      $esta_arr = array('1', '2', '3', '4');
      $cooperate_where['rowid'] = $house_id;
      $cooperate_where['apply_type'] = 1;
      $cooperate_where['tbl'] = 'sell';
      $this->load->model('cooperate_model');
      $esta = $this->cooperate_model->get_cooperate_baseinfo_esta($cooperate_where);
      if ($esta && in_array($esta['esta'], $esta_arr)) {
        $data['cooperate_esta'] = 1;
      } else {
        $data['cooperate_esta'] = 0;
      }
    }

    $this->result('1', '房源详情页', $data);
  }

  //查看保密信息
  public function get_secret_info()
  {
    $broker_info = $this->user_arr;
    $house_id = $this->input->get('house_id', TRUE);
    $devicetype = $this->input->get('api_key', TRUE);
    $deviceid = $this->input->get('deviceid', TRUE);
    $house_id = intval($house_id);
    //新权限
    //范围（个人或全公司）
    //获得当前数据所属的经纪人id和门店id
    $this->sell_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id', 'nature'));
    $this->sell_house_model->set_id($house_id);
    $owner_arr = $this->sell_house_model->get_info_by_id();
    //判断公私盘
    if ('1' == $owner_arr['nature']) {
      $view_other_per = $this->broker_permission_model->check('138', $owner_arr);
    } else if ('2' == $owner_arr['nature']) {
      $view_other_per = $this->broker_permission_model->check('136', $owner_arr);
    }
    //保密信息关联门店权限
    if ('1' == $owner_arr['nature']) {
      $agency_secret_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '37');
    } else if ('2' == $owner_arr['nature']) {
      $agency_secret_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '35');
    }
    if (!$view_other_per['auth']) {
      $this->result('-1', '暂无权限');
      exit();
    } else {
      if (!$agency_secret_per) {
        $this->result('-1', '暂无权限');
        exit();
      }
    }
    $this->load->model('broker_model');
    $this->load->model('broker_view_secrecy_model');
    //查看自己的房客源数据，不记录次数；已经查看过的数据，不重复记录。
    $where_cond = array(
      'broker_id' => intval($broker_info['broker_id']),
      'view_type' => 1,
      'row_id' => $house_id
    );
    $query_result = $this->broker_view_secrecy_model->get_one_by($where_cond);
    if ($owner_arr['broker_id'] != $broker_info['broker_id'] && empty($query_result)) {
      $is_insert = true;
    } else {
      $is_insert = false;
    }
    $check_baomi_time = $this->broker_model->check_baomi_time($this->company_basic_arr,
      $this->user_arr, 1, $house_id, $is_insert);
    if (!$check_baomi_time['status']) {
      $this->result(0, '您每天可查看保密信息' . $check_baomi_time['secrecy_num']
        . '次,现在已达上限');
      return false;
    }
    $data = array();
    $house_info = array();
    $house_mess = array();
    //设置查询字段
    $fileld = array('id', 'dong', 'unit', 'telno1', 'owner', 'telno2', 'telno3', 'door', 'nature');
    $this->sell_house_model->set_search_fields($fileld);
    $this->sell_house_model->set_id($house_id);
    $house_mess = $this->sell_house_model->get_info_by_id();

    $house_info['house_mess'] = $house_mess['dong'] . '栋' . $house_mess['unit'] . '单元' . $house_mess['door'] . '门牌号';
    $house_info['tel'] = '';
    if ($house_mess['telno1']) {
      $house_info['tel'] .= $house_mess['telno1'] . '　';
    } elseif ($house_mess['telno2']) {
      $house_info['tel'] .= $house_mess['telno2'] . '　';
    } elseif ($house_mess['telno3']) {
      $house_info['tel'] .= $house_mess['telno3'];
    }
    $house_info['owner'] = $house_mess['owner'];
    $data['house_list'] = $house_info;

    $city_id = intval($this->user_arr['city_id']);
    $version = $this->input->get('version');
    if (3 === $city_id && '1.0.5' == $version) {
      $data['house_list'] = array('a', 'b');
    }

    if ($house_id) {
      $this->info_count($house_id, 8);//记录查看保密信息的记录
      $this->add_brower_log($house_id);
      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 45;
      $add_log_param['text'] = '出售房源 ' . 'CS' . $house_id;
      if ($devicetype == 'android') {
        $add_log_param['from_system'] = 2;
      } else {
        $add_log_param['from_system'] = 3;
      }
      $add_log_param['device_id'] = $deviceid;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);
    }
    $this->result('1', '保密信息查询结果', $data);


  }

  //查看保密信息次数

  public function add_brower_log($house_id)
  {
    $return_data = '';
    $user = $this->user_arr;
    $house_id = intval($house_id);
    $param_list = array(
      'house_id' => $house_id,
      'broker_id' => $user['broker_id'],
      'broker_name' => $user['truename'],
      'agency_id' => $user['agency_id'],
      'agency_name' => $user['agency_name'],
      'ip' => $_SERVER['REMOTE_ADDR'],
      'browertime' => time(),
    );
    $this->load->model('sell_house_model');
    $result = $this->sell_house_model->add($param_list);

  }

  //匹配的配置信息
  public function get_match_config()
  {
    $data = array();
    $aagency_list = array(
      '0' => '全网公盘',
      '1' => '本人',
      '2' => '所在的门店',
      '3' => '所在的公司'
    );
    $agency_list = array();
    foreach ($aagency_list as $key => $val) {
      $agency_list[$key]['key'] = $key;
      $agency_list[$key]['value'] = $val;
    }
    //时间范围
    $time = array(
      '0' => '不限',
      '1' => '一个月内',
      '2' => '一个季度',
      '3' => '半年内',
      '4' => '一年内'
    );
    $time_list = array();
    foreach ($time as $key => $val) {
      $time_list[$key]['key'] = $key;
      $time_list[$key]['value'] = $val;
    }
    $data['agency_list'] = $agency_list;
    $data['time_list'] = $time_list;
    if ($agency_list && $time_list) {
      $this->result('1', '匹配搜索条件配置信息', $data);
    } else {
      $this->result('1', '获取配置信息失败');
    }

  }

  //智能匹配
  public function match()
  {
    $data = array();
    $broker_info = $this->user_arr;
    $broker_id = $broker_info['broker_id'];
    $company_id = $broker_info['company_id'];
    $agency_id = $broker_info['agency_id'];
    if ($company_id) {
      $view_other_per_data = $this->broker_permission_model->check('26');
      $data['view_other_per'] = $view_other_per_data['auth'];
    } else {
      $data['view_other_per'] = false;
    }

    $this->load->model('auth_review_model');
    //身份认证信息
    $ident_info = $this->auth_review_model->get_new("broker_id = " . $broker_id . " AND type = 1", 0, 1);
    $ident_auth = (is_full_array($ident_info) && $ident_info['status'] == 2) ? 1 : 0;
    //获取系统公司基本设置
    $this->load->model('agency_basic_setting_model');
    $info = $this->agency_basic_setting_model->get_data_by_agency_id($agency_id);
    $company_setting = $info["0"];
    if (empty($company_setting)) {
      $result = $this->agency_basic_setting_model->get_default_data();
      $company_setting = $result["0"];
    }
    $open_cooperate = $company_setting['open_cooperate'];
    if ($ident_auth && $open_cooperate) {
      $data['view_share_per'] = true;
    } else {
      $data['view_share_per'] = false;
    }

    $post_param = $this->input->get(NULL, TRUE);
    $house_id = $post_param['house_id'];
    $house_id = intval($house_id);
    $house_info = array();
    //获取出售信息基本配置资料
    $config = $this->house_config_model->get_config();
    $this->sell_house_model->set_id($house_id);
    $fileld = array('id', 'district_id', 'broker_name', 'sell_type', 'fitment', 'title', 'room', 'hall', 'buildarea', 'price', 'address', 'street_id', 'block_name');
    $this->sell_house_model->set_search_fields($fileld);
    $house_info = $this->sell_house_model->get_info_by_id();
    $house_meg = array();
    //根据区属id获取板块
    $dis_list = $this->get_dis_list($house_info['district_id']);
    $data['dis_list'] = $dis_list;
    $house_meg['district_name_address'] = $this->district_model->get_distname_by_id($house_info['district_id']) . '-' . $this->district_model->get_streetname_by_id($house_info['street_id']);
    $house_meg['sell_type'] = $config['sell_type'][$house_info['sell_type']];
    $house_meg['fitment'] = $config['fitment'][$house_info['fitment']];
    $house_meg['room_type'] = $house_info['room'] . '室' . $house_info['hall'] . '厅';
    $house_meg['buildarea'] = strip_end_0($house_info['buildarea']);
    $house_meg['price'] = strip_end_0($house_info['price']);
    $house_meg['title'] = $house_info['block_name'];
    if ($house_meg) {
      //检测是否已经合作
      $this->load->model('cooperate_model');
      $is_applay_coop = $this->cooperate_model->check_is_cooped_by_houseid(array($house_id), 'sell', $this->user_arr['broker_id']);
      $house_meg['is_applay_coop'] = $is_applay_coop[$house_id];
      $data['house_list'] = $house_meg;
    }
    // 分页参数\

    if (!isset($post_param['page']) || empty($post_param['page'])) {
      $page = 1;
    } else {
      $page = $post_param['page'];
    }

    if (!isset($post_param['page_size']) || empty($post_param['page_size'])) {
      $page_size = 3;
    } else {
      $page_size = $post_param['page_size'];
    }
    $this->_init_pagination($page, $page_size);
    //匹配到的客源
    $where = 'status = 1';
    $where .= " and ( dist_id1  = '" . $house_info['district_id'] . "' OR dist_id2  = '" . $house_info['district_id'] . "' OR dist_id3  = '" . $house_info['district_id'] . "' ) ";
    $where .= " and property_type = '" . $house_info['sell_type'] . "' ";
    $where .= " and area_max >= '" . $house_info['buildarea'] . "' AND area_min <= '" . $house_info['buildarea'] . "' ";
    $where .= " and price_max >= '" . $house_info['price'] . "' AND price_min <= '" . $house_info['price'] . "' ";
    $where .= " and room_max >= '" . $house_info['room'] . "' AND room_min <= '" . $house_info['room'] . "' ";
    if (!isset($post_param['agency_range']) || empty($post_param['agency_range'])) {
      $post_param['agency_range'] = 1;
    }
    if (!isset($post_param['searchtime']) || empty($post_param['searchtime'])) {
      $post_param['searchtime'] = 3;
    }
    //合作中心请求
    if ($post_param['is_public']) {
      $where .= " and is_share = 1 ";
    }
    //条件搜索
    $cond_where_ext = $this->get_match($post_param);
    $where .= $cond_where_ext;

    $this->load->model('buy_customer_model');
    $customer_list =
      $this->buy_customer_model->get_buylist_by_cond($where, $this->_offset,
        $this->_limit);
    //获取求购客源的配置
    $buy_config = $this->buy_customer_model->get_base_conf();
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $buy_street[$val['id']] = $val;
    }

    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $buy_district[$val['id']] = $val;
    }

    $customer_arr = array();
    if ($customer_list) {
      foreach ($customer_list as $key => $val) {
        $customer_arr[$key]['customer_id'] = $val['id'];
        $customer_arr[$key]['customer_name'] = $val['truename'];
        $customer_arr[$key]['customer_status'] = $buy_config['public_type'][$val['public_type']];
        if ($val['is_share'] == 1) {
          $customer_arr[$key]['isshare'] = '合作';
        } else {
          $customer_arr[$key]['isshare'] = '非合作';
        }
        $customer_arr[$key]['area'] = strip_end_0($val['area_min']) . '-' . strip_end_0($val['area_max']);
        $customer_arr[$key]['price'] = strip_end_0($val['price_min']) . '-' . strip_end_0($val['price_max']);
        $customer_arr[$key]['customer_type'] = $buy_config['property_type'][$val['property_type']];
        $customer_arr[$key]['customer_district'] = '';
        if ($val['dist_id1']) {
          $customer_arr[$key]['customer_district'] .= $buy_district[$val['dist_id1']]['district'] . '-' . $buy_street[$val['street_id1']]['streetname'] . ',';
        }
        if ($val['dist_id2']) {
          $customer_arr[$key]['customer_district'] .= $buy_district[$val['dist_id2']]['district'] . '-' . $buy_street[$val['street_id2']]['streetname'] . ',';
        }
        if ($val['dist_id3']) {
          $customer_arr[$key]['customer_district'] .= $buy_district[$val['dist_id3']]['district'] . '-' . $buy_street[$val['street_id3']]['streetname'];
        }
        $customer_arr[$key]['customer_cmt'] = '';
        if ($val['cmt_name1']) {
          $customer_arr[$key]['customer_cmt'] .= $val['cmt_name1'] . ',';
        }
        if ($val['cmt_name2']) {
          $customer_arr[$key]['customer_cmt'] .= $val['cmt_name2'] . ',';
        }
        if ($val['cmt_name3']) {
          $customer_arr[$key]['customer_cmt'] .= $val['cmt_name3'];
        }
      }
    }
    if (empty($customer_list)) {
      $data['customer_list'] = '';

    } else {
      $data['customer_list'] = $customer_arr;
    }
    $this->result('1', '智能匹配', $data);
  }

  /**
   * 出售匹配条件
   * 根据表单提交参数，获取查询条件
   */
  public function get_match($form_param)
  {
    $broker_info = $this->user_arr;
    $broker_id = $broker_info['broker_id'];
    $agency_id = $broker_info['agency_id'];
    $company_id = intval($broker_info['company_id']);
    $agency_list = $this->api_broker_model->get_agencys_by_company_id($company_id);

    foreach ($agency_list as $v) {
      $in_str .= $v['agency_id'] . ',';
    }
    $in_str = trim($in_str, ',');

    $cond_where = '';
    //板块

    if (isset($form_param['street_id']) && $form_param['street_id'] > 0) {
      $street_id = intval($form_param['street_id']);
      $cond_where .= " and ( street_id1 = '" . $street_id . "' OR street_id2 = '" . $street_id . "' OR  street_id3 = '" . $street_id . "' )";
    }
    //门店搜索范围

    if (isset($form_param['agency_range']) && $form_param['agency_range'] > 0) {
      $agency_range = intval($form_param['agency_range']);
      switch ($agency_range) {
        case'1':
          $cond_where .= " and broker_id= '" . $broker_id . "' ";
          break;
        case'2':
          $cond_where .= " and public_type = 2 and agency_id IN (" . $in_str . ") ";
          break;
        case'3':
          $cond_where .= " and public_type = 2 and agency_id= '" . $agency_id . "' ";
          break;
        case'4':
          $cond_where .= " and is_share = 1 ";
          break;
      }
    }
    //时间范
    $now_time = time();
    if (isset($form_param['searchtime']) && $form_param['searchtime'] > 0) {
      $searchtime = intval($form_param['searchtime']);
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
          $creattime = $now_time - 86400 * 365;
          break;

        default :
          $creattime = $now_time - 86400 * 180;
      }
      $cond_where .= " and creattime>= '" . $creattime . "' ";
    }

    return $cond_where;
  }

  //获取房源举报的配置信息
  public function get_config()
  {
    $data = array();
    $report_arr = array(
      '1' => '房源虚假',
      '3' => '已成交',
      '5' => '其它'
    );
    $report_cofnig = array();
    foreach ($report_arr as $key => $val) {
      $report_cofnig[$key]['key'] = $key;
      $report_cofnig[$key]['value'] = $val;
    }
    $data['report_config'] = $report_cofnig;
    $this->result('1', '返回房源举报配置信息', $data);

  }

  //上传举报
  public function add_report()
  {
    $data = array();
    $house_id = $this->input->post('house_id', TRUE);//房源的id
    $report_type = $this->input->post('report_type', TRUE);//举报类型
    $report_text = $this->input->post('report_text', TRUE);//举报的具内容
    $date = time();//举报时间
    $style = '1';//1.为出售 2为出租
    $broker_info = $this->user_arr;
    $brokerinfo_id = $broker_info['broker_id'];//举报人的id
    $brokerinfo_name = $broker_info['truename'];//举报人的姓名
    $this->sell_house_model->set_id($house_id);
    $house_info = $this->sell_house_model->get_info_by_id();
    $brokered_id = $house_info['broker_id'];//被举报人的id
    $brokered_name = $house_info['broker_name'];//被举报的姓名
    //房源信息
    $data_info = array();
    $data_info['buildarea'] = $house_info['buildarea'];//房源面积
    $picmin = $house_info['lowprice'];//房源低价
    $picmax = $house_info['price'];//房源售价
    $data_info['block_name'] = $house_info['block_name'];//小区名字
    $data_info['room'] = $house_info['room'];//室
    $data_info['hall'] = $house_info['hall'];//厅
    $data_info['toilet'] = $house_info['toilet'];//卫
    $data_info['price'] = $picmin . '-' . $picmax . '万元';
    //获取出售信息基本配置资料
    $config = $this->house_config_model->get_config();
    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $dis[$val['id']] = $val;
    }
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $stred[$val['id']] = $val;

    }
    //获取相关配置信息
    $data_info['district'] = $dis[$house_info['district_id']]['district'];//区属
    $data_info['forward'] = $config['forward'][$house_info['forward']];//朝向
    $data_info['streetname'] = $stred[$house_info['street_id']]['streetname'];//板块
    $data_info['fitment'] = $config['fitment'][$house_info['fitment']];//装修
    $dbhouse_info = serialize($data_info);//序列化数组
    $tel = '';//业主电话
    if ($house_info['telno1'] != '') {
      $tel = $house_info['telno1'];
    } elseif ($house_info['telno2'] != '') {
      $tel = $house_info['telno2'];
    } elseif ($house_info['telno3'] != '') {
      $tel = $house_info['telno3'];
    }
    //加载图片地址
    $file = $this->input->post('img_name1', TRUE);
    $fileurl = implode(',', $file);
    $img_na = explode(',', $fileurl);
    $img_num = count($img_na);
    //图片名称
    $im_name = '';
    for ($i = 1; $i <= $img_num; $i++) {
      $im_name .= "证据";
      $im_name .= $i;
      $im_name .= ',';
    }
    //判断对该房源类型是否已经举报过
    $where = 'style = ' . $style;
    $where .= ' AND number = ' . $house_id;
    $where .= ' AND type = ' . $report_type;
    $this->load->model('report_model');
    $select_num = $this->report_model->count_by($where);
    $insert_data = array(
      'broker_id' => $brokerinfo_id,
      'broker_name' => $brokerinfo_name,
      'brokered_id' => $brokered_id,
      'brokered_name' => $brokered_name,
      'style' => $style,
      'number' => $house_id,
      'phone' => $tel,
      'photo_name' => $im_name,
      'type' => $report_type,
      'content' => $report_text,
      'photo_url' => $fileurl,
      'date_time' => $date,
      'status' => 1,
      'house_info' => $dbhouse_info
    );

    if (!empty($brokerinfo_id) && !empty($brokered_id) && $brokerinfo_id != $brokered_id) {
      $data['msg'] = '不能对自己的房源举报';
    }
    if ($select_num > 0) {
      $data['msg'] = '该客源的举报类型你已经举报过了';
    }

    $return_id = '';
    if (!empty($brokerinfo_id) && !empty($brokered_id) && $brokerinfo_id != $brokered_id && $select_num == 0) {
      $return_id = $this->report_model->insert($insert_data);
    }
    if ($return_id > 0) {
      $this->result('1', '举报成功');
    } else {
      $this->result('1', '举报失败', $data);
    }
  }

  //根据区属获取板块
  public function get_dis_list($dist_id)
  {
    $dist_id = intval($dist_id);
    $this->load->model('district_model');
    $dist_arr = $this->district_model->get_street_bydist($dist_id);
    $dis_list = array();
    foreach ($dist_arr as $key => $val) {
      $dis_list[$key]['key'] = $val['id'];
      $dis_list[$key]['name'] = $val['streetname'];
    }
    return $dis_list;
  }

  //获取经纪人信息
  public function sincere($broker_id)
  {
    $broker_id = intval($broker_id);
    $this->load->model('api_broker_sincere_model');
    $broker_messagin = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
    //经纪人姓名
    $sincere['broker_name'] = $broker_messagin['truename'];
    $sincere['photo'] = $broker_messagin['photo'];
    //合作成功率
    $this->load->model('cooperate_suc_ratio_base_model');
    $cop_succ_ratio_info = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_id);
    $sincere['cop_suc_ratio'] = $cop_succ_ratio_info['cop_succ_ratio'];
    //合作成功率平均值
    $avg_cop_suc_ratio = $this->cooperate_suc_ratio_base_model->get_avg_succ_ratio();
    $sincere['differ_suc_ratio'] = $avg_cop_suc_ratio > 0 ? strip_end_0(($cop_succ_ratio_info['cop_succ_ratio'] - $avg_cop_suc_ratio) / $avg_cop_suc_ratio) : 0;

    //好评率
    $trust_appraise_count = $this->api_broker_sincere_model->
    get_trust_appraise_count($broker_id);
    if (empty($trust_appraise_count['good_rate'])) {
      $good_rate = '--';
    } else {
      $good_rate = strip_end_0($trust_appraise_count['good_rate']);
    }
    $sincere['good_rate'] = $good_rate;
    //平均好评率
    $good_avg_rate = $this->api_broker_sincere_model->good_avg_rate($broker_id);
    $sincere['differ_good_rate'] = $good_avg_rate['good_rate_avg_high'];

    //获取经纪人的信用值和等级
    $sincere['trust_level'] = $this->api_broker_sincere_model->get_trust_level_by_broker_id($broker_id);
    unset($sincere['trust_level']['level']);
    //信息 态度 业务 细节分值统计
    $sincere['appraise_info'] = $this->api_broker_sincere_model->
    get_appraise_and_avg($broker_id);
    unset($sincere['appraise_info']['infomation']['level']);
    unset($sincere['appraise_info']['attitude']['level']);
    unset($sincere['appraise_info']['business']['level']);
    return $sincere;
  }

  //导入房源
  public function collect_publish()
  {
    $data = array();
    $house_id = $this->input->get('house_id', TRUE);
    $house_id = intval($house_id);
    $this->load->model('collections_model');
    $where = array('id' => $house_id);
    $lists = $this->collections_model->get_housesell_byid($where);
    //房源的配置信息
    $config = $this->house_config_model->get_config();
    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $dis[$val['id']] = $val;
    }
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $stred[$val['id']] = $val;
    }
    $house_list = array();
    if ($lists) {
      foreach ($lists as $key => $list) {
        $house_list['id'] = $list['id'];
        $house_list['sell_type'] = array('key' => $list['sell_type'], 'name' => $config['sell_type'][$list['sell_type']]);
        $house_list['dong'] = '';
        if ($list['dong']) {
          $house_list['dong'] = $list['dong'];
        }
        $house_list['unit'] = '';
        if ($list['unit']) {
          $house_list['unit'] = $list['unit'];
        }
        $house_list['door'] = '';
        if ($list['door']) {
          $house_list['door'] = $list['door'];
        }
        $house_list['owner'] = '0';
        if ($list['owner']) {
          $house_list['owner'] = $list['owner'];
        }
        $house_list['telno1'] = '0';
        if ($list['telno1']) {
          $house_list['telno1'] = $list['telno1'];
        }
        $house_list['price'] = '0';
        if ($list['price']) {
          $house_list['price'] = strip_end_0($list['price']);
        }
        $house_list['pic'] = '';
        if ($list['picurl'] != '暂无资料') {
          $picarr = explode('*', $list['picurl']);
          if (is_full_array($picarr) && $picarr[0] != '暂无资料') {
            $temp = 0;
            foreach ($picarr as $pic) {
              $house_list['pic'][$temp]['url'] = $pic;
              $temp++;

              if ($temp >= 10) {
                break;
              }
            }
          } else {
            $house_list['pic'] = '';
          }
        }
        $house_list['serverco']['key'] = '1';
        $house_list['serverco']['name'] = '毛坯';
        if ($list['serverco']) {
          $house_list['serverco']['key'] = $list['serverco'];
          switch ($list['serverco']) {
            case "1":
              $house_list['serverco']['name'] = "毛坯";
              break;
            case "2":
              $house_list['serverco']['name'] = "简装";
              break;
            case "3":
              $house_list['serverco']['name'] = "中装";
              break;
            case "4":
              $house_list['serverco']['name'] = "精装";
              break;
            case "5":
              $house_list['serverco']['name'] = "豪装";
              break;
            case "6":
              $house_list['serverco']['name'] = "婚装";
              break;
          }
        }
        $house_list['room'] = '0';
        if ($list['room']) {
          $house_list['room'] = $list['room'];
        }
        $house_list['hall'] = '0';
        if ($list['hall']) {
          $house_list['hall'] = $list['hall'];
        }
        $house_list['toilet'] = '0';
        if ($list['toilet']) {
          $house_list['toilet'] = $list['toilet'];
        }
        if ($list['floor'] && $list['totalfloor']) {
          $house_list['floor_arr'] = $list['floor'] . '/' . $list['totalfloor'];
        } else {
          $house_list['floor_arr'] = '0' . '/' . '0';
        }
        $house_list['remark'] = '无';
        if ($list['remark']) {
          $house_list['remark'] = $list['remark'];
        }
        $house_list['buildarea'] = '0';
        if ($list['buildarea']) {
          $house_list['buildarea'] = strip_end_0($list['buildarea']);
        }


        $house_list['rentnature'] = '0';
        if ($list['nature']) {
          $house_list['nature'] = array('key' => $list['nature'], 'name' => $config['nature'][$list['nature']]);
        } else {
          $house_list['rentnature'] = array('key' => '-1', 'name' => '-1');
        }
        $house_list['forward'] = '0';
        if ($list['forward']) {
          $house_list['forward'] = array('key' => $list['forward'], 'name' => $config['forward'][$list['forward']]);
        } else {
          $house_list['forward'] = array('key' => '-1', 'name' => '-1');
        }

        $house_list['buildyear'] = '0';
        if ($list['buildyear']) {
          $house_list['buildyear'] = $list['buildyear'];
        }
      }
      $community_infos = $this->community_model->get_cmtinfo_by_cmtname($list['house_name']);
      $data['cmt_status'] = '';
      if ($community_infos) {
        foreach ($community_infos as $key => $community_info) {
        }
        $data['cmt_status'] = 1;
        $house_list['community_info'] = array('cmt_id' => $community_info['id'], 'cmt_name' => $community_info['cmt_name'], 'dist_id' => $community_info['dist_id'], 'districtname' => $dis[$community_info['dist_id']]['district'], 'streetid' => $community_info['streetid'], 'streetname' => $stred[$community_info['streetid']]['streetname'], 'address' => $community_info['address']);

      } else {
        $data['cmt_status'] = 0;
        $house_list['cmt_info_name'] = $list['house_name'];
      }
      //房源标题 楼盘名+面积+价格+楼层+装修
      if ($house_list['community_info']['cmt_name']) //楼盘名
      {
        $house_list['title'] .= $house_list['community_info']['cmt_name'];
      }
      if ($house_list['buildarea'])//面积
      {
        $house_list['title'] .= $house_list['buildarea'] . '平';
      }
      if ($house_list['buildarea'])//价格
      {
        $house_list['title'] .= $house_list['price'] . '万';
      }
      if (strpos($house_list['floor_arr'], '/')) {
        $floor_arr = explode('/', $house_list['floor_arr']);
        $house_list['title'] .= ' ' . $floor_arr[0] . '楼';
      }
      if ($house_list['serverco']['name']) {
        $house_list['title'] .= ' ' . $house_list['serverco']['name'];
      }
      $data['house_list'] = $house_list;
      $this->result('1', '获取房源信息成功', $data);
    } else {
      $this->result('0', '获取房源信息失败', $data);
    }

  }

  //修改房源的日志录入匹配
  public function insetmatch($backinfo, $datainfo)
  {
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $config = $this->house_config_model->get_config();
    //获取区属
    $district = $this->district_model->get_district();
    foreach ($district as $key => $val) {
      $dis[$val['id']] = $val;
    }
    //获取板块
    $street = $this->district_model->get_street();
    foreach ($street as $key => $val) {
      $stred[$val['id']] = $val;
    }

    $constr = '';
    foreach ($backinfo as $key => $val) {
      if ($val != $datainfo[$key]) {
        switch ($key) {
          case 'a_ratio'://甲方佣金分成比例
            $constr .= '甲方佣金分成比例:' . strip_end_0($val) . '%>>' . strip_end_0($datainfo[$key]) . '%,';
            break;

          case 'b_ratio'://已方佣金分成比例
            $constr .= '已方佣金分成比例:' . strip_end_0($val) . '%>>' . strip_end_0($datainfo[$key]) . '%,';
            break;

          case 'buyer_ratio'://买方支付佣金比例
            $constr .= '买方支付佣金比例:' . strip_end_0($val) . '%>>' . strip_end_0($datainfo[$key]) . '%,';
            break;

          case 'seller_ratio'://卖方支付佣金比例
            $constr .= '卖方支付佣金比例:' . strip_end_0($val) . '%>>' . strip_end_0($datainfo[$key]) . '%,';
            break;

          case 'sell_type':
            $constr .= '物业类型:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'dong':
            $constr .= '栋座:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'block_name':
            $constr .= '小区名字:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'district_id':
            $constr .= '区属：' . $dis[$val]['district'] . '>>' . $dis[$datainfo[$key]]['district'] . ',';
            break;

          case 'street_id':
            $constr .= '板块：' . $stred[$val]['streetname'] . '>>' . $stred[$datainfo[$key]]['streetname'] . ',';
            break;

          case 'address':
            $constr .= '地址:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'unit':
            $constr .= '单元:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'door':
            $constr .= '门牌:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'title':
            $constr .= '标题:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'bewrite':
            if (empty($val)) {
              $val = '空';
            }
            if ($datainfo[$key]) {
              $datainfo[$key] = '空';
            }
            $constr .= '描述:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'owner':
            $constr .= '业主姓名:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'idcare':
            $constr .= '身份证:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'telno1':
            $constr .= '电话:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'telno2':
            $constr .= '电话2:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'telno3':
            $constr .= '电话3:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'proof':
            $constr .= '证书号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'mound_num':
            $constr .= '丘地号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'record_num':
            $constr .= '备案号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'status':
            $constr .= '状态:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'nature':
            $constr .= '房源性质:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'room':
            $constr .= '室:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'hall':
            $constr .= '厅:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'toilet':
            $constr .= '卫:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'balcony':
            $constr .= '阳台:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'floor_type':
            if ($val == 1) {
              $val = '单层';
            } else {
              $val = '跃层';
            }
            if ($datainfo[$key] == 1) {
              $datainfo[$key] = '单层';
            } else {
              $datainfo[$key] = '跃层';
            }
            $constr .= '楼层类型:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'floor':
            $constr .= '楼层:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'totalfloor':
            $constr .= '总楼层:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'subfloor':
            $constr .= '跃层:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'forward':
            $constr .= '朝向:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'totalfloor':
            $constr .= '总楼层:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'fitment':
            $constr .= '装修:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'buildarea':
            $constr .= '面积:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'buildyear':
            $constr .= '房龄:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'price':
            $constr .= '售价:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'avgprice':
            $constr .= '单价:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'lowprice':
            $constr .= '最低售价:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'taxes':
            $constr .= '税费:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'entrust':
            $constr .= '委托类型:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'keys':
            if ($val == 1) {
              $val = '有';
            } else {
              $val = '无';
            }
            if ($datainfo[$key] == 1) {
              $datainfo[$key] = '有';
            } else {
              $datainfo[$key] = '无';
            }
            $constr .= '钥匙:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'key_number':
            $constr .= '钥匙编号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'division':
            if ($val == 1) {
              $ver = '是';
            } elseif ($val == 2) {
              $ver = '否';
            }
            if ($datainfo[$key] == 1) {
              $data = '是';
            } elseif ($datainfo[$key] == 2) {
              $data = '否';
            }
            $constr .= '是否分割:' . $ver . '>>' . $data . ',';
            break;

          case 'property':
            $constr .= '产权:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'current':
            $constr .= '现状:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'infofrom':
            $constr .= '信息来源:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'villa_type':
            $constr .= '别墅类型:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'isshare':
            if ($val == 1) {
              $val = '是';
            } else {
              $val = '否';
            }
            if ($datainfo[$key] == 1) {
              $datainfo[$key] = '是';
            } else {
              $datainfo[$key] = '否';
            }
            $constr .= '是否合作:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'remark':
            $constr .= '备注:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'house_type':
            $constr .= '住宅类型:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case 'equipment':
            $gg = explode(',', $val);
            $tt = explode(',', $datainfo[$key]);
            $constr .= '房屋设施:';
            if ($val) {
              foreach ($gg as $keyy) {
                $constr .= $config[$key][$keyy] . ',';
              }
            }
            $constr .= '>>';
            if ($datainfo[$key]) {
              foreach ($tt as $tty) {
                $constr .= $config[$key][$tty] . ',';
              }
            }
            break;

          case 'strata_fee':
            if (empty($val)) {
              $val = '空';
            }
            if (empty($datainfo[$key])) {
              $datainfo[$key] = '空';
            }
            $constr .= '物业费:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'costs_type':
            if ($val == 1) {
              $val = '元/月/㎡';
            } else {
              $val = '元/月';
            }
            if ($datainfo[$key] == 1) {
              $datainfo[$key] = '元/月/㎡';
            } else {
              $datainfo[$key] = '元/月';
            }
            $constr .= '物业费类型:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'setting':
            $gg = explode(',', $val);
            $tt = explode(',', $datainfo[$key]);
            $constr .= '周边配套:';
            if ($val) {
              foreach ($gg as $keyy) {
                $constr .= $config[$key][$keyy] . ',';
              }
            }
            $constr .= '>>';
            if ($datainfo[$key]) {
              foreach ($tt as $tty) {
                $constr .= $config[$key][$tty] . ',';
              }
            }
            break;
          case 'pic_inside_room': //记录图片上传跟进记录
            //旧的图片不在新的图片中表示删除
            $delpic = 0;
            foreach ($val as $v) {
              if (!in_array($v, $datainfo[$key])) {
                $delpic++;
              }
            }
            //新的图片不在旧的图片中表示新增
            $addpic = 0;
            foreach ($datainfo[$key] as $v) {
              if (!in_array($v, $val)) {
                $addpic++;
              }
            }
            if ($addpic > 0 && $delpic > 0) {
              $constr .= '上传了' . $addpic . '张照片,删除了' . $delpic . '张照片,';
            } else if ($addpic > 0) {
              $constr .= '上传了' . $addpic . '张照片,';
            } else if ($delpic > 0) {
              $constr .= '删除了' . $delpic . '张照片,';
            }
            break;
        }
      }
    }
    return $constr;
  }

  /*工作统计日志
     * type:1出售2出租3求购4求租
     * $state：1信息录入2信息修改3图片上传4堪房5带看6钥匙提交
     */
  private function info_count($house_id, $state, $customer_id = 0)
  {
    $this->load->model('count_log_model');
    $this->load->model('count_num_model');
    $broker_info = $this->user_arr;
    $insert_log_data = array(
      'company_id' => $broker_info['company_id'],
      'agency_id' => $broker_info['agency_id'],
      'broker_id' => $broker_info['broker_id'],
      'dateline' => time(),
      'YMD' => date('Y-m-d'),
      'state' => $state,
      'type' => 1,
      'house_id' => $house_id,
      'customer_id' => $customer_id
    );
    $insert_id = $this->count_log_model->insert($insert_log_data);
    if ($insert_id) {
      $count_num_info = $this->count_num_model->get_one_by('broker_id = ' . $broker_info['broker_id'] . ' and YMD = ' . "'" . date('Y-m-d') . "'");
      if (is_full_array($count_num_info)) {
        //修改数据
        switch ($state) {
          case 1://信息录入
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'insert_num' => $count_num_info['insert_num'] + 1
            );
            break;
          case 2://信息修改
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'modify_num' => $count_num_info['modify_num'] + 1
            );
            break;
          case 3://图片上传
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'upload_num' => $count_num_info['upload_num'] + 1
            );
            break;
          case 4://堪房
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'look_num' => $count_num_info['look_num'] + 1
            );
            break;
          case 5://带看
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'looked_num' => $count_num_info['looked_num'] + 1
            );
            break;
          case 6://钥匙提交
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'key_num' => $count_num_info['key_num'] + 1
            );
            break;
          case 7://视频上传数
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'video_num' => $count_num_info['video_num'] + 1
            );
            break;
          case 8://查看保密信息
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'secret_num' => $count_num_info['secret_num'] + 1
            );
            break;
          case 9://普通跟进
            $update_data = array(
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'follow_num' => $count_num_info['follow_num'] + 1
            );
            break;
        }
        $row = $this->count_num_model->update_by_id($update_data, $count_num_info['id']);
        if ($row) {
          return 'success';
        } else {
          return 'error';
        }
      } else {
        //添加数据
        switch ($state) {
          case 1://信息录入
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'insert_num' => 1
            );
            break;
          case 2://信息修改
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'modify_num' => 1
            );
            break;
          case 3://图片上传
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'upload_num' => 1
            );
            break;
          case 4://堪房
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'look_num' => 1
            );
            break;
          case 5://带看
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'looked_num' => 1
            );
            break;
          case 6://钥匙提交
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'key_num' => 1
            );
            break;
          case 7://视频上传数
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'video_num' => 1
            );
            break;
          case 8://查看保密信息
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'secret_num' => 1
            );
            break;
          case 9://普通跟进
            $insert_num_data = array(
              'company_id' => $broker_info['company_id'],
              'agency_id' => $broker_info['agency_id'],
              'broker_id' => $broker_info['broker_id'],
              'dateline' => time(),
              'YMD' => date('Y-m-d'),
              'follow_num' => 1
            );
            break;
        }
        $insert_num_id = $this->count_num_model->insert($insert_num_data);
        if ($insert_num_id) {
          return 'success';
        } else {
          return 'error';
        }
      }
    } else {
      return 'error';
    }
  }
}


/* End of file sell.php */
/* Location: ./application/mls/controllers/sell.php */
