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
class Tourism extends MY_Controller
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

    //加载楼盘模型类
    $this->load->model('community_model');
    //表单验证
    $this->load->library('form_validation');
    //加载海外地产模型
    $this->load->model('tourism_model');
    //加载基本配置MODEL
    $this->load->model('house_config_model');
  }

  /**
   * 旅游地产列表页
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function index($page = 1)
  {
    //模板使用数据
    $data = array();
    //树型菜单
    $data['user_menu'] = $this->user_menu;
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);

    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;//echo $cond_where;

    //设置默认排序字段

    //$roomorder = (isset($post_param['orderby_id'])&&$post_param['orderby_id']!='') ? intval($post_param['orderby_id']) : $default_order;
    //$order_arr = $this->_get_orderby_arr($roomorder);

    //符合条件的总行数
    $this->_total_count =
      $this->tourism_model->get_count_by_cond($cond_where);
    $data['total_count'] = $this->_total_count;
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    $data['pages'] = $pages;

    //获取列表内容
    $list = $this->tourism_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit);
    //print_r( $list);die;
    if ($list) {
      foreach ($list as $key => $val) {
        if ($val['province_id']) {
          $province_info = $this->tourism_model->get_by_province_id($val['province_id']);
          $list[$key]['province_name'] = $province_info['province_name'];
          unset($province_info);
        }
        if ($val['city_id']) {
          $city_info = $this->tourism_model->get_by_city_id($val['city_id']);
          $list[$key]['city_name'] = $city_info['city_name'];
          unset($city_info);
        }
        if ($val['house_type'] == '1') {
          $list[$key]['house_type'] = '公寓';
        } elseif ($val['house_type'] == '2') {
          $list[$key]['house_type'] = '独栋别墅';
        } elseif ($val['house_type'] == '3') {
          $list[$key]['house_type'] = '联排别墅';
        } else {
          $list[$key]['house_type'] = '其他';
        }
        $list[$key]['feature'] = explode(',', $val['feature']);
        foreach ($list[$key]['feature'] as $k => $v) {
          if ($v == 1) {
            $list[$key]['feature'][$k] = '海景';
          } elseif ($v == 2) {
            $list[$key]['feature'][$k] = '山景';
          } elseif ($v == 3) {
            $list[$key]['feature'][$k] = '过冬';
          } elseif ($v == 4) {
            $list[$key]['feature'][$k] = '避暑';
          } elseif ($v == 5) {
            $list[$key]['feature'][$k] = '温泉';
          } elseif ($v == 6) {
            $list[$key]['feature'][$k] = '高尔夫';
          } elseif ($v == 7) {
            $list[$key]['feature'][$k] = '养老';
          }
        }
      }
    }
    $data['list'] = $list;
    //echo '<pre>';print_r($data['list']);die;
    //获取基本配置资料
    $config_data = $this->house_config_model->get_config();
    $data['config'] = $config_data;

    //获取国家
    $province = $this->tourism_model->get_province();
    $data['province'] = $province;

    //获取城市
    $city = $this->tourism_model->get_city();
    $data['city'] = $city;

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
    $data['page_title'] = '旅游合作项目列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/css/v1.0/reseat.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/oversea.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js');
    //加载发布页面模板
    $this->view('tourism/tourism_index', $data);
  }

  /**
   * 旅游地产详情页
   * @param int $house_id
   */
  public function detail($tourism_id)
  {
    $data = array();
    //用户菜单栏
    $this->load->model('permission_tab_model');
    $data['user_menu'] = $this->permission_tab_model->get_tab("tourism", "index");
    //房源id
    $data['house_id'] = $tourism_id;
    //认证组
    $data['group_id'] = $this->user_arr['group_id'];

    $room_list = $area_list = $room_build_arr = $room_build_list = array();

    //获取列表内容
    $huxing_list = $this->tourism_model->get_huxing_by_id($tourism_id);
    if ($huxing_list) {
      foreach ($huxing_list as $key => $val) {

        $room_list[$val['room']][] = $val;
        $area_list[] = $val['area'];
        $room_build_arr[$val['room']][] = $val['area'];
        sort($area_list);
        $small_area = $area_list[0];
        $big_area = $area_list[count($area_list) - 1];
      }
    }
    if (is_full_array($room_build_arr)) {
      foreach ($room_build_arr as $key => $room_build) {
        if (count($room_build) == 1) {
          $room_build_list[$key] = array('small' => $room_build[0], 'big' => $room_build[0]);
        } else if (count($room_build) == 2) {
          if ($room_build[0] < $room_build[1]) {
            $small = $room_build[0];
            $big = $room_build[1];
          } else {
            $small = $room_build[1];
            $big = $room_build[0];
          }
          $room_build_list[$key] = array('small' => $small, 'big' => $big);
        } else {
          sort($room_build);
          $small = $room_build[0];
          $big = $room_build[count($room_build) - 1];
          $room_build_list[$key] = array('small' => $small, 'big' => $big);
        }
      }
    }
    unset($room_build_arr);

    //echo '<pre>';print_r($area_list);die;
    $data['room_build_list'] = $room_build_list;
    $data['room_list'] = $room_list;
    $data['huxing_list'] = $huxing_list;
    $list = $this->tourism_model->get_by_id($tourism_id);

    $list['small_area'] = $small_area;
    $list['big_area'] = $big_area;

    $list['effect_pic_ids'] = explode(',', $list['effect_pic_ids']);
    $list['real_pic_ids'] = explode(',', $list['real_pic_ids']);

    $list['pics1'] = array_merge($list['effect_pic_ids'], $list['real_pic_ids']);

    $list['outdoor_pic_ids'] = explode(',', $list['outdoor_pic_ids']);
    $list['traffic_pic_ids'] = explode(',', $list['traffic_pic_ids']);
    $list['pics2'] = array_merge($list['outdoor_pic_ids'], $list['traffic_pic_ids']);

    $list['template_pic_ids'] = explode(',', $list['template_pic_ids']);
    $list['support_pic_ids'] = explode(',', $list['support_pic_ids']);
    $list['pics3'] = array_merge($list['template_pic_ids'], $list['support_pic_ids']);

    $list['pics'] = array_merge($list['pics1'], $list['pics2']);
    $list['pics'] = array_merge($list['pics'], $list['pics3']);

    //$list['project_pic_ids'] = explode(',',$list['project_pic_ids']);
    if ($list['province_id']) {
      $province_info = $this->tourism_model->get_by_province_id($list['province_id']);
      $list['province_name'] = $province_info['province_name'];
      $list['province_name_english'] = $province_info['province_name_english'];
      unset($province_info);
    }
    if ($list['city_id']) {
      $city_info = $this->tourism_model->get_by_city_id($list['city_id']);
      $list['city_name'] = $city_info['city_name'];
      $list['city_name_english'] = $city_info['city_name_english'];
      $list['city_info'] = $city_info['city_info'];
      $list['city_pic_ids'] = explode(',', $city_info['city_pic_ids']);

    }
    if ($list['house_type'] == '1') {
      $list['house_type'] = '公寓';
    } elseif ($list['house_type'] == '2') {
      $list['house_type'] = '独栋别墅';
    } elseif ($list['house_type'] == '3') {
      $list['house_type'] = '联排别墅';
    } else {
      $list['house_type'] = '其他';
    }

    $data['list'] = $list;
    //echo '<pre>';print_r($data['list']);die;
    //页面标题
    $data['page_title'] = '旅游合作项目列表详情页';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/css/v1.0/reseat.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/oversea.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js');
    //加载发布页面模板
    $this->view('tourism/tourism_detail', $data);
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

    //国家ID
    $province_id = isset($form_param['province_id']) ? $form_param['province_id'] : 0;
    if ($province_id) {
      $province_ids = implode(',', $province_id);
      $cond_where .= " AND province_id in (" . $province_ids . ")";
    }
    //特色
    $feature = isset($form_param['feature']) ? $form_param['feature'] : 0;
    if ($feature) {
      //$feature = implode(',',$feature);
      foreach ($feature as $val) {
        $cond_where .= " AND feature like '%" . $val . "%'";
      }
    }
    //均价

    if (isset($form_param['avg_price']) && !empty($form_param['avg_price']) && $form_param['avg_price'] > 0) {
      $avg_price = intval($form_param['avg_price']);
      $tourism_price = $this->house_config_model->get_config();

      $price_val = $tourism_price['tourism_price'][$avg_price];
      if ($price_val) {
        $price_val = preg_replace("#[^0-9-]#", '', $price_val);
        $price_val = explode('-', $price_val);
        if (count($price_val) == 2) {
          $cond_where .= " AND avg_price >=" . $price_val[0] . " AND avg_price <=" . $price_val[1];
        } else {
          if ($avg_price == 1) {
            $cond_where .= " AND avg_price < " . $price_val[0];
          } else {
            $cond_where .= " AND avg_price > " . $price_val[0];
          }
        }

      }
    }
    $cond_where .= " AND status = 1 ";
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, 'AND');
    $cond_where = trim($cond_where);
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
      case 18:
        $arr_order['order_key'] = 'dong';
        $arr_order['order_by'] = 'ASC';
        break;
      case 19:
        $arr_order['order_key'] = 'dong';
        $arr_order['order_by'] = 'DESC';
        break;
      case 20:
        $arr_order['order_key'] = 'unit';
        $arr_order['order_by'] = 'ASC';
        break;
      case 21:
        $arr_order['order_key'] = 'unit';
        $arr_order['order_by'] = 'DESC';
        break;
      case 22:
        $arr_order['order_key'] = 'door';
        $arr_order['order_by'] = 'ASC';
        break;
      case 23:
        $arr_order['order_key'] = 'door';
        $arr_order['order_by'] = 'DESC';
        break;
      case 24:
        $arr_order['order_key'] = 'totalfloor';
        $arr_order['order_by'] = 'ASC';
        break;
      case 25:
        $arr_order['order_key'] = 'totalfloor';
        $arr_order['order_by'] = 'DESC';
        break;
      default:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
    }

    return $arr_order;
  }
}

/* End of file abraod.php */
/* Location: ./application/mls/controllers/abraod.php */
