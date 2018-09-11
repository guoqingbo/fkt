<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Customer_collect
 *
 * @author LION
 */
class House_collect extends MY_Controller
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

  //构造函数
  public function __construct()
  {
    parent::__construct();
    $this->load->model('house_collect_model');
    //加载区属模型类
    $this->load->model('district_model');
    //加载楼盘模型类
    $this->load->model('community_model');
    //表单验证
    $this->load->library('form_validation');
    //加载客户MODEL
    $this->load->model('broker_model');
    $this->load->model('api_broker_model');
    $this->load->model('agency_model');
  }


  //首页
  public function index()
  {
    $this->sell_collects();
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
   * 收藏客源列表
   *
   * @access    public
   * @param    void
   * @return    void
   */
  public function sell_collects($page = 1)
  {
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      //是否开启合作中心
      $open_cooperate = $company_basic_data['open_cooperate'];
    } else {
      $open_cooperate = '';
    }

    //模板使用数据
    $data = array();
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $data['open_cooperate'] = $open_cooperate;
    $data['broker_id'] = $broker_id;
    $data['truename'] = $broker_info['truename'];

    $data['agency_id'] = $broker_info['agency_id'];//经纪人门店编号
    $data['agency_name'] = $broker_info['agency_name'];//获取经纪人所对应门店的名称
    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    //获取全部分公司信息
    $data['agency_list'] = $this->api_broker_model->get_agencys_by_company_id($company_id);
    $post_param = $this->input->post(NULL, TRUE);

    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);

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

    //收藏MODEL
    $type = 'sell_house';
    //查询条件
    $cond_where = "house_collect.broker_id = '" . $broker_id . "' AND "
      . "house_collect.status = 1 AND house_collect.tbl = '" . $type . "'";
    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param, $type);

    $cond_where .= $cond_where_ext;

    $post_param['block_name'] = trim($post_param['block_name']);
    $cond_or_like = array();
    if (!empty($post_param['block_name'])) {
      $cond_or_like['like_key'] = array('address', 'block_name', 'title');
      $cond_or_like['like_value'] = $post_param['block_name'];
    }

    //符合条件的总行数
    $this->_total_count =
      $this->house_collect_model->get_collection_num_by_cond($cond_where, $type, $cond_or_like);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $house_list = $this->house_collect_model->get_collection_list_by_cond_or_like($cond_where, $cond_or_like, $type, $this->_offset, $this->_limit);
    $this->load->model('api_broker_model');

    $rowid_arr = array();
    //循环获取经纪人姓名和门店信息
    if (count($house_list) > 0) {
      //经纪人帐号
      $broker_id_arr = array();
      $this->load->model('api_broker_model');

      foreach ($house_list as $key => $value) {
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);

        // 最新跟进时间
        $house_list[$key]['genjintime'] = date('Y-m-d H:i:s', $value['updatetime']);
        $house_list[$key]['telno'] = $brokerinfo['phone'];
        //悬赏金额处理
        if (!empty($value['cooperate_reward'])) {
          $cooperate_reward = intval($value['cooperate_reward']);
          if ($cooperate_reward > 10000) {
            $reuslt_reward = strip_end_0($cooperate_reward / 10000, 1);
            $house_list[$key]['cooperate_reward'] = $reuslt_reward . '万';
          }
        }

        $broker_id_gj = intval($value['broker_id']);

        if ($broker_id_gj > 0 && !in_array($broker_id_gj, $broker_id_arr)) {
          array_push($broker_id_arr, $broker_id_gj);
        }

        $rowid_arr[] = $value['id'];
      }

      $this->load->model('cooperate_model');
      $data['check_coop_reulst'] =
        $this->cooperate_model->check_is_cooped_by_houseid($rowid_arr, 'sell', $broker_id);

      //经纪人MODEL
      $this->load->model('api_broker_model');
      $this->load->model('api_broker_sincere_model');
      //合作成功率MODEL
      $this->load->model('cooperate_suc_ratio_base_model');
      $broker_num = count($broker_id_arr);
      for ($i = 0; $i < $broker_num; $i++) {
        $broker_arr = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id_arr[$i]);
        $house_broker_info[$broker_id_arr[$i]] = $broker_arr;

        //获取经纪人好评率
        $appraise_count = array();
        $appraise_count = $this->api_broker_sincere_model->get_trust_appraise_count($broker_id_arr[$i]);
        $house_broker_info[$broker_id_arr[$i]]['good_rate'] = !empty($appraise_count) ? $appraise_count['good_rate'] : 0;

        //经济人合作成功率
        $cop_succ_ratio_info = array();
        $cop_succ_ratio_info = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_id_arr[$i]);
        $house_broker_info[$broker_id_arr[$i]]['cop_succ_ratio_info'] = !empty($cop_succ_ratio_info) ? $cop_succ_ratio_info : array();
      }

      $data['house_broker_info'] = $house_broker_info;
    }

    //菜单
    $data['user_menu'] = $this->user_menu;

    //三级功能菜单
    $data['user_func_menu'] = $this->user_func_menu;

    $data['house_list'] = $house_list;
    //页面标题
    $data['page_title'] = '我的收藏出售信息列表页';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css'
      . ',mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'mls/js/v1.0/broker_common.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/customer_list.js,mls/js/v1.0/cooperate_common.js');

    //表单数据
    $data['post_param'] = $post_param;

    //如果后几个搜搜参数被选中则，后面的选择参数全部显示
    if (!empty($post_param['property_type']) || !empty($post_param['room']) ||
      !empty($post_param['public_type']) || !empty($post_param['status']) ||
      !empty($post_param['is_share']) || !empty($post_param['id'])
    ) {
      $data['cond_show'] = '';
    } else {
      $data['cond_show'] = 'hide';
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

    //加载发布页面模板
    $this->view('house/sell_list_collect', $data);
  }


  /**
   * 收藏客源列表
   *
   * @access    public
   * @param    void
   * @return    void
   */
  public function rent_collects($page = 1)
  {
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      //是否开启合作中心
      $open_cooperate = $company_basic_data['open_cooperate'];
    } else {
      $open_cooperate = '';
    }

    //模板使用数据
    $data = array();
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    $data['open_cooperate'] = $open_cooperate;
    $data['broker_id'] = $broker_id;
    $data['truename'] = $broker_info['truename'];
    $post_param = $this->input->post(NULL, TRUE);
    //根据查看房源权限获取门店与经济人相关信息
    $borker_id_arr = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
    $agency_id = $borker_id_arr['agency_id'];//经纪人门店编号
    $data['agency_id'] = $agency_id;
    $where = array('id' => $agency_id);
    $agency_name = $this->agency_model->get_one_by($where);
    $data['agency_name'] = $agency_name['name'];//获取经纪人所对应门店的名称
    //根据门店id获取所在门店下的所有经纪人
    $broker_arr = $this->api_broker_model->get_brokers_agency_id($agency_id);
    $data['broker_list'] = $broker_arr;
    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    //获取全部分公司信息
    $data['agency_list'] = $this->api_broker_model->get_agencys_by_company_id($company_id);
    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);

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

    //收藏MODEL
    $type = 'rent_house';
    //查询条件
    $cond_where = "house_collect.broker_id = '" . $broker_id . "' AND "
      . "house_collect.status = 1 AND house_collect.tbl = '" . $type . "'";
    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param, $type);
    $cond_where .= $cond_where_ext;

    $post_param['block_name'] = trim($post_param['block_name']);
    $cond_or_like = array();
    if (!empty($post_param['block_name'])) {
      $cond_or_like['like_key'] = array('address', 'block_name', 'title');
      $cond_or_like['like_value'] = $post_param['block_name'];
    }

    //符合条件的总行数
    $this->_total_count =
      $this->house_collect_model->get_collection_num_by_cond($cond_where, $type, $cond_or_like);

    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $house_list = $this->house_collect_model->get_collection_list_by_cond_or_like($cond_where, $cond_or_like, $type, $this->_offset, $this->_limit);

    //循环获取经纪人姓名和门店信息
    if (count($house_list) > 0) {
      //经纪人帐号
      $broker_id_arr = array();
      $this->load->model('api_broker_model');

      foreach ($house_list as $key => $value) {
        $brokerinfo = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);

        // 最新跟进时间
        $house_list[$key]['genjintime'] = date('Y-m-d H:i:s', $value['updatetime']);
        $house_list[$key]['telno'] = isset($brokerinfo['phone']) ? $brokerinfo['phone'] : '';
        $broker_id_gj = intval($value['broker_id']);

        if ($broker_id_gj > 0 && !in_array($broker_id_gj, $broker_id_arr)) {
          array_push($broker_id_arr, $broker_id_gj);
        }

        $rowid_arr[] = $value['id'];

        if ($value['price_danwei'] > 0) {
          $house_list[$key]['price'] = ($value['price'] / $value['buildarea']) / 30;
        }
      }

      //批量检查房源是已经申请过合作
      $this->load->model('cooperate_model');
      $data['check_coop_reulst'] =
        $this->cooperate_model->check_is_cooped_by_houseid($rowid_arr, 'rent', $broker_id);

      //经纪人MODEL
      $this->load->model('api_broker_model');
      $this->load->model('api_broker_sincere_model');
      $broker_num = count($broker_id_arr);
      //合作成功率MODEL
      $this->load->model('cooperate_suc_ratio_base_model');
      for ($i = 0; $i < $broker_num; $i++) {
        $broker_arr = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id_arr[$i]);
        $house_broker_info[$broker_id_arr[$i]] = $broker_arr;

        //获取经纪人好评率
        $appraise_count = array();
        $appraise_count = $this->api_broker_sincere_model->get_trust_appraise_count($broker_id_arr[$i]);
        $house_broker_info[$broker_id_arr[$i]]['good_rate'] = !empty($appraise_count) ? $appraise_count['good_rate'] : 0;

        //经济人合作成功率
        $cop_succ_ratio_info = array();
        $cop_succ_ratio_info = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_id_arr[$i]);
        $house_broker_info[$broker_id_arr[$i]]['cop_succ_ratio_info'] = !empty($cop_succ_ratio_info) ? $cop_succ_ratio_info : array();
      }

      $data['house_broker_info'] = $house_broker_info;
    }

    $data['house_list'] = $house_list;

    //菜单
    $data['user_menu'] = $this->user_menu;

    //三级功能菜单
    $data['user_func_menu'] = $this->user_func_menu;

    //页面标题
    $data['page_title'] = '我的收藏出租信息列表页';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css'
      . ',mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'mls/js/v1.0/broker_common.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/customer_list.js,mls/js/v1.0/cooperate_common.js');

    //表单数据11
    $data['post_param'] = $post_param;

    //如果后几个搜搜参数被选中则，后面的选择参数全部显示
    if (!empty($post_param['property_type']) || !empty($post_param['room']) ||
      !empty($post_param['public_type']) || !empty($post_param['status']) ||
      !empty($post_param['is_share']) || !empty($post_param['id'])
    ) {
      $data['cond_show'] = '';
    } else {
      $data['cond_show'] = 'hide';
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

    //加载发布页面模板
    $this->view('house/rent_list_collect', $data);
  }


  /**
   * 收藏客源信息
   *
   * @access    public
   * @param    mixed $rows_id 客源编号
   * @return    void
   */
  public function add_collect()
  {

    $rows_id = intval($this->input->get('row_id', TRUE));
    $type = strip_tags($this->input->get('dbname', TRUE));
    $result = array();
    if ($rows_id > 0 && $type != '') {
      //加载经纪人MODEL
      $broker_info = $this->user_arr;
      $broker_id = intval($broker_info['broker_id']);
      $agency_id = intval($broker_info['agency_id']);

      //收藏数据
      $collect_arr = array();
      $collect_arr['broker_id'] = $broker_id;
      $collect_arr['agency_id'] = $agency_id;
      $collect_arr['rows_id'] = $rows_id;
      $collect_arr['tbl'] = $type;
      $collect_arr['creattime'] = time();
      $collect_arr['status'] = 1;

      $this->load->model('house_collect_model');
      //判断是否已经收藏过改条客源
      $collect_num = $this->house_collect_model->get_collectionnum_by_cid($rows_id, $broker_id, $type, '1');

      //添加收藏数据
      if (intval($collect_num) == 0) {
        $msg = $this->house_collect_model->add_collection($collect_arr);
        if ($msg > 0) {
          $result['is_ok'] = 1;
          $result['msg'] = '已收藏';
        } else {
          $result['is_ok'] = 0;
          $result['msg'] = '收藏失败';
        }
      } else {
        $result['is_ok'] = 2;
        $result['msg'] = '已收藏过该客源信息';
      }
    }

    echo json_encode($result);
  }


  /**
   * 取消收藏房源信息
   *
   * @access    public
   * @param    mixed $rows_id 房源编号
   * @return    void
   */
  public function cancle_collect()
  {
    $rows_id = intval($this->input->get('row_id', TRUE));
    $type = strip_tags($this->input->get('dbname', TRUE));
    $result = array();

    if ($rows_id > 0 && $type != '') {

      //加载经纪人MODEL
      $broker_info = $this->user_arr;
      $broker_id = intval($broker_info['broker_id']);
      $msg = $this->house_collect_model->del_collection_by_id($rows_id, $broker_id, $type);

      if ($msg > 0) {
        $result['is_ok'] = 1;
        $result['msg'] = '已取消收藏';
      } else {
        $result['is_ok'] = 0;
        $result['msg'] = '取消收藏失败';
      }
    }

    echo json_encode($result);
  }


  //提交表单查询内容
  private function _get_cond_str($form_param, $tb_name)
  {
    $cond_where = '';

    //板块 ，区属
    $street = intval($form_param['street']);
    $district = intval($form_param['district']);
    if ($street) {
      $cond_where .= " AND `$tb_name`.street_id = '" . $street . "'";
    } elseif ($district) {
      $cond_where .= " AND  `$tb_name`.district_id = '" . $district . "'";
    }

    //楼盘ID
    if (!empty($form_param['block_name']) && $form_param['block_id'] > 0) {
      $cond_where .= " AND `$tb_name`.block_id = '" . $form_param['block_id'] . "'";
    }

    //面积条件
    if (!empty($form_param['areamin'])) {
      $areamin = $form_param['areamin'];
      $cond_where .= " AND `$tb_name`.buildarea >= '" . $areamin . "'";
    }

    if (!empty($form_param['areamax'])) {
      $areamax = $form_param['areamax'];
      $cond_where .= " AND `$tb_name`.buildarea <= '" . $areamax . "'";
    }


    //价格条件
    if (!empty($form_param['pricemin'])) {
      $pricemin = $form_param['pricemin'];
      $cond_where .= " AND `$tb_name`.price >= '" . $pricemin . "'";
    }

    if (!empty($form_param['pricemax'])) {
      $pricemax = $form_param['pricemax'];
      $cond_where .= " AND `$tb_name`.price <= '" . $pricemax . "'";
    }

    //物业类型条件
    if (isset($form_param['sell_type']) && !empty($form_param['sell_type']) && $form_param['sell_type'] > 0) {
      $sell_type = intval($form_param['sell_type']);
      $cond_where .= " AND `$tb_name`.sell_type = '" . $sell_type . "'";
    }

    //户型条件
    if (isset($form_param['room']) && !empty($form_param['room']) && $form_param['room'] > 0) {
      $room = intval($form_param['room']);
      if ($room <= 6) {
        $cond_where .= " AND room = '" . $room . "'";
      } else if ($room > 6) {
        $cond_where .= " AND room >= '" . $room . "'";
      }
    }

    //性质条件
    if (isset($form_param['nature']) && !empty($form_param['nature']) && $form_param['nature'] > 0) {
      $nature = intval($form_param['nature']);
      $cond_where .= " AND `$tb_name`.nature = '" . $nature . "'";
    }

    //状态条件
    if (isset($form_param['status']) && !empty($form_param['status']) && $form_param['status'] > 0) {
      $status = intval($form_param['status']);
      $cond_where .= " AND `$tb_name`.status = '" . $status . "'";
    }

    //装修条件
    if (isset($form_param['fitment']) && !empty($form_param['fitment']) && $form_param['fitment'] > 0) {
      $fitment = intval($form_param['fitment']);
      $cond_where .= " AND `$tb_name`.fitment = '" . $fitment . "'";
    }

    //朝向条件
    if (isset($form_param['forward']) && !empty($form_param['forward']) && $form_param['forward'] > 0) {
      $forward = intval($form_param['forward']);
      $cond_where .= " AND `$tb_name`.forward = '" . $forward . "'";
    }


    //时间范
    if (!empty($form_param['searchtime'])) {
      $searchtime = intval($form_param['searchtime']);
      $now_time = time();
      switch ($searchtime) {
        case '1':
          $creattime = $now_time - 86400 * 30;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '2':
          $creattime = $now_time - 86400 * 90;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '3':
          $creattime = $now_time - 86400 * 180;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '4':
          $creattime = $now_time - 86400 * 360;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
          break;

        case '5':
          $creattime = $now_time - 86400 * 360;
          $cond_where .= " AND createtime <  '" . $creattime . "' ";
          break;

        default :
          $creattime = $now_time - 86400 * 180;
          $cond_where .= " AND createtime >=  '" . $creattime . "' ";
      }
    }

    //门店
    if (!empty($form_param['agency_id']) && $form_param['agency_id'] != '') {
      $agency_id = intval($form_param['agency_id']);
      $cond_where .= " AND `$tb_name`.agency_id = '" . $agency_id . "'";
    }
    //经纪人
    if (!empty($form_param['broker_id']) && $form_param['broker_id'] != '') {
      $broker_id = intval($form_param['broker_id']);
      $cond_where .= " AND `$tb_name`.broker_id = '" . $broker_id . "'";
    }


    return $cond_where;
  }
}

/* End of file house_collect.php */
/* Location: ./applications/controllers/house_collect.php */
