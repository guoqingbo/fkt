<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 我的采集 Class
 *
 * 采集控制器
 *
 * @package      mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      angel_in_us
 */
class My_collections_nj extends MY_Controller
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
    $this->load->helper('page_helper');
    $this->load->model('collections_model_nj');//采集模型类
    $this->load->library('form_validation');//表单验证
    $this->load->model('district_model');//区属模型类
    $this->load->model('house_config_model');  //房源配置模型类
  }


  /**
   * 我的采集---出售房源
   * @access public
   * @return void
   * date 2014-12-28
   * author angel_in_us
   */
  public function my_collect_sell($page = 1)
  {
    $broker_id = $this->input->post('broker_id');
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    $data['like'] = $data['or_like'] = array();

    //根据broker_id 和 tbl_name 查询agent_house_judge中的 house_id 用来后续查经纪人已看房源记录
    $where = array(
      'broker_id' => $broker_id,
      'tbl_name' => 'sell_house_collect',
      'is_input' => 0,   //不显示已录入房源！！！
      'is_del' => 0   //不显示已“删除”房源！！！
    );
    $result = $this->collections_model_nj->check_agent_house($where);

    //实际为post参数
    $get_param = $this->input->post(NULL, TRUE);
    $data['where_cond'] = $this->_get_cond_str($get_param);

    //分页请求
    if (!isset($get_param['page_size']) && empty($get_param['page_size'])) {
      $this->_limit = $this->_limit;
    } else {
      $this->_limit = $get_param['page_size'];
    }

    if (!isset($get_param['page']) && empty($get_param['page'])) {
      $page = 1;
    } else {
      $this->_init_pagination($get_param['page']);
    }
    //所得house_id 都放在$house_ids 里面
    $house_ids = array();
    foreach ($result as $key => $value) {
      $house_ids[] = $value['house_id'];
    }
    if (!empty($house_ids)) {
      $data['where_in'] = array("id", $house_ids);
    } else {
      $data['where_in'] = array('id', array('a'));
    }
    $arr = array("broker_id" => $broker_id, 'tbl_name' => 'sell_house_collect');
    $judge = $this->collections_model_nj->get_agent_house($arr);
    $data['judge'] = $judge;
    $data['broker_info'] = $broker_id;

    if ($data['where_cond']['district'] != " " && !empty($data['where_cond']['district'])) {
      $data['like'] = array('district' => $data['where_cond']['district']);
      unset($data['where_cond']['district']);
    }

    if ($data['where_cond']['house_name'] != "" && !empty($data['where_cond']['house_name'])) {
      $data['or_like']['like_key'] = array('block', 'house_name', 'house_title');
      $data['or_like']['like_value'] = $data['where_cond']['house_name'];
    }

    $data['district'] = $this->district_model->get_cj_district();
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);
    //符合条件的总行数
    $this->_total_count = count($judge);
    $count = $this->collections_model_nj->get_sell_num($data['where_cond'], $data['like'], $data['or_like']);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    //分页处理000000000000
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $post_param['page'],//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');
    $data['order_by'] = 'createtime';
    $data['blacklist'] = $this->collections_model_nj->get_house_sell($data['where_cond'], $data['where_in'], $data['like'], $data['or_like'], $data['order_by'], $this->_limit, $this->_offset);


    //获取列表内容
    if (!empty($data['blacklist'])) {
      //数据重构
      $info = array();
      foreach ($data['blacklist'] as $k => $v) {
        switch ($v['sell_type']) {
          case "1":
            $sell_type = "住宅";
            break;
          case "2":
            $sell_type = "别墅";
            break;
          case "3":
            $sell_type = "商铺";
            break;
          case "4":
            $sell_type = "写字楼";
            break;
        }
        $a['house_id'] = $v['id'];
        $a['sell_type'] = $sell_type;
        $a['district'] = $v['district'];
        $a['block'] = $v['block'];
        $a['house_name'] = $v['house_name'];
        $a['room'] = $v['room'];
        $a['hall'] = $v['hall'];
        $a['toilet'] = $v['toilet'];
        $a['balcony'] = $v['balcony'];
        $a['floor'] = $v['floor'];
        $a['totalfloor'] = $v['totalfloor'];
        $a['telno1'] = $v['telno1'];
        $a['pic'] = $v['picurl'] != '暂无资料' ? 1 : 0;
        $a['owner'] = $v['owner'] ? $v['owner'] : '暂无资料';
        $a['buildarea'] = strip_end_0($v['buildarea']);
        $a['price'] = strip_end_0($v['price']);
        //$a['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
        if ($v['createtime'] > strtotime(date('Y-m-d'))) {
          //今天
          $a['createtime'] = '今天：' . date('H:i:s', $v['createtime']);
        } else {
          //24小时以前
          $a['createtime'] = date('Y-m-d', $v['createtime']);
        }
        $a['if_read'] = 1;
        $info['data'][] = $a;
      }
      $info['count'] = $count;
      $info['read_count'] = count($judge);
      $this->result(1, '查询已查看出售房源成功', $info);
    } else {
      $this->result(1, '暂无查询内容', array('data' => array()));
    }
  }

  //我的采集的举报
  public function my_collect_report()
  {
    $this->load->model('collections_model_nj');
    $house_id = $this->input->get('house_id', TRUE);//房源id
    $report_text = $this->input->get('report_text', TRUE);//具体举报内容
    $report_type = $this->input->get('report_type', TRUE);//举报类型1出售2出租
    $house_id = intval($house_id);
    $broker_info = $this->user_arr;
    $broker_name = $broker_info['truename'];
    $where_cond = array('id' => $house_id);
    $house_info = array();
    if (!empty($report_type) && $report_type == 1) {
      $house_info = $this->collections_model_nj->get_housesell_byid($where_cond);

    }
    if (!empty($report_type) && $report_type == 2) {
      $house_info = $this->collections_model_nj->get_houserent_byid($where_cond);
    }
    $rel = '';
    $telno1 = '';
    if ($house_info) {
      foreach ($house_info as $key => $val) {
        $telno1 = $val['telno1'];
      }
      $info = array(
        'r_tel' => $telno1,
        'r_reason' => $report_text,
        'r_status' => 3,
        'r_addtime' => time(),
        'r_person' => $broker_name,
      );
      $rel = $this->collections_model_nj->agent_reportlist($info);
    }
    if ($rel > 0) {
      $this->result('1', '举报成功');
    } else {
      $this->result('0', '举报失败,该房源已被举报');
    }

  }


  /**
   * 我的采集---出租房源
   * @access public
   * @return void
   * date 2014-12-28
   * author angel_in_us
   */
  public function my_collect_rent($page = 1)
  {
    $broker_id = $this->input->post('broker_id');
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    $data['like'] = $data['or_like'] = array();

    //根据broker_id 和 tbl_name 查询agent_house_judge中的 house_id 用来后续查经纪人已看房源记录
    $where = array(
      'broker_id' => $broker_id,
      'tbl_name' => 'rent_house_collect',
      'is_input' => 0,  //不显示已录入房源！！！
      'is_del' => 0  //不显示已“删除”房源！！！
    );
    $result = $this->collections_model_nj->check_agent_house($where);

    //实际为post参数
    $get_param = $this->input->post(NULL, TRUE);
    $data['where_cond'] = $this->_get_cond_str_rent($get_param);

    //分页请求
    if (!isset($get_param['page_size']) && empty($get_param['page_size'])) {
      $this->_limit = $this->_limit;
    } else {
      $this->_limit = $get_param['page_size'];
    }

    if (!isset($get_param['page']) && empty($get_param['page'])) {
      $page = 1;
    } else {
      $this->_init_pagination($get_param['page']);
    }

    //所得house_id 都放在$house_ids 里面
    $house_ids = array();
    foreach ($result as $key => $value) {
      $house_ids[] = $value['house_id'];
    }
    if (!empty($house_ids)) {
      $data['where_in'] = array("id", $house_ids);
    } else {
      $data['where_in'] = array('id', array('a'));
    }

    if ($data['where_cond']['district'] != " " && !empty($data['where_cond']['district'])) {
      $data['like'] = array('district' => $data['where_cond']['district']);
      unset($data['where_cond']['district']);
    }

    if ($data['where_cond']['house_name'] != "" && !empty($data['where_cond']['house_name'])) {
      $data['or_like']['like_key'] = array('block', 'house_name', 'house_title');
      $data['or_like']['like_value'] = $data['where_cond']['house_name'];
    }

    $arr = array("broker_id" => $broker_id, 'tbl_name' => 'rent_house_collect');
    $judge = $this->collections_model_nj->get_agent_house($arr);
    $data['judge'] = $judge;
    $data['broker_info'] = $broker_id;

    $data['district'] = $this->district_model->get_cj_district();

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    $this->_init_pagination($page);
    //符合条件的总行数
    $this->_total_count = count($judge);
    $count = $this->collections_model_nj->get_rent_num($data['where_cond'], $data['like'], $data['or_like']);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    //分页处理000000000000
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $post_param['page'],//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');
    $data['order_by'] = 'createtime';
    $data['blacklist'] = $this->collections_model_nj->get_house_rent($data['where_cond'], $data['where_in'], $data['like'], $data['or_like'], $data['order_by'], $this->_limit, $this->_offset);


    //获取列表内容
    if (!empty($data['blacklist'])) {
      //数据重构
      $info = array();
      foreach ($data['blacklist'] as $k => $v) {
        switch ($v['rent_type']) {
          case "1":
            $rent_type = "住宅";
            break;
          case "2":
            $rent_type = "别墅";
            break;
          case "3":
            $rent_type = "商铺";
            break;
          case "4":
            $rent_type = "写字楼";
            break;
        }
        $a['house_id'] = $v['id'];
        $a['rent_type'] = $rent_type;
        $a['district'] = $v['district'];
        $a['block'] = $v['block'];
        $a['house_name'] = $v['house_name'];
        $a['room'] = $v['room'];
        $a['hall'] = $v['hall'];
        $a['toilet'] = $v['toilet'];
        $a['balcony'] = $v['balcony'];
        $a['floor'] = $v['floor'];
        $a['totalfloor'] = $v['totalfloor'];
        $a['telno1'] = $v['telno1'];
        $a['pic'] = $v['picurl'] != '暂无资料' ? 1 : 0;
        $a['owner'] = $v['owner'] ? $v['owner'] : '暂无资料';
        $a['buildarea'] = strip_end_0($v['buildarea']);
        $a['price'] = strip_end_0($v['price']);
        if ($v['createtime'] > strtotime(date('Y-m-d'))) {
          //今天
          $a['createtime'] = '今天：' . date('H:i:s', $v['createtime']);
        } else {
          //24小时以前
          $a['createtime'] = date('Y-m-d', $v['createtime']);
        }
        $a['if_read'] = 1;
        $info['data'][] = $a;
      }
      $info['count'] = $count;
      $info['read_count'] = count($judge);
      $this->result(1, '查询已查看出租房源成功', $info);
    } else {
      $this->result(1, '暂无查询内容', array('data' => array()));
    }
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

  /*
  *重组出售的搜索条件
  *
  *
  */
  private function _get_cond_str($form_param)
  {
    $cond_where = array();

    //板块
    if (isset($form_param['block']) && !empty($form_param['block']) && $form_param['block'] > 0) {
      $block = intval($form_param['block']);
      $block = $this->district_model->get_streetname_by_id($block);
      $block = array('block' => $block);
      $cond_where = $block;
    }
    //区属
    if (isset($form_param['district']) && !empty($form_param['district']) && $form_param['district'] > 0) {
      $district = intval($form_param['district']);
      $district = $this->district_model->get_distname_by_id($district);
      $district = array('district' => $district);
      $cond_where = $cond_where + $district;
    }

    //采集区属
    if (isset($form_param['district_cj']) && !empty($form_param['district_cj']) && $form_param['district_cj'] > 0) {
      $district = intval($form_param['district_cj']);
      $district = $this->district_model->get_cjdistname_by_id($district);
      $district = str_replace('县', '', str_replace('区', '', $district));
      $district = array('district' => $district);
      $cond_where = $cond_where + $district;
    }

    //楼盘名
    if (!empty($form_param['house_name']) && $form_param['house_name'] != '') {
      $house_name = trim($form_param['house_name']);
      $house_name = array('house_name' => $house_name);
      $cond_where = $cond_where + $house_name;
    }

    //面积条件
    if (!empty($form_param['sell_area']) && $form_param['sell_area'] > 0) {
      $sell_area = intval($form_param['sell_area']);
      $area = $this->house_config_model->get_config();
      $area_val = $area['sell_area'][$sell_area];
      if (!empty($area_val)) {
        $area_val = preg_replace("#[^0-9-]#", '', $area_val);
        $area_val = explode('-', $area_val);
        if (count($area_val) == 2) {
          $cond_where = $cond_where + array('buildarea >=' => $area_val[0], 'buildarea <=' => $area_val[1]);
        } else {
          if ($sell_area == 1) {
            $cond_where = $cond_where + array('buildarea <=' => $area_val[0]);
          } else {
            $cond_where = $cond_where + array('buildarea >=' => $area_val[0]);
          }
        }
      }
    }


    //价格条件
    if (isset($form_param['sell_price']) && !empty($form_param['sell_price']) && $form_param['sell_price'] > 0) {
      $price = intval($form_param['sell_price']);
      $sell_price = $this->house_config_model->get_config();
      $price_val = $sell_price['sell_price'][$price];
      if ($price_val) {
        $price_val = preg_replace("#[^0-9-]#", '', $price_val);
        $price_val = explode('-', $price_val);
        if (count($price_val) == 2) {
          $cond_where = $cond_where + array('price >=' => $price_val[0], 'price <=' => $price_val[1]);
        } else {
          if ($price == 1) {
            $cond_where = $cond_where + array('price <=' => $price_val[0]);
          } else {
            $cond_where = $cond_where + array('price >=' => $price_val[0]);
          }
        }
      }
    }

    //物业类型条件
    if (isset($form_param['sell_type']) && !empty($form_param['sell_type']) && $form_param['sell_type'] > 0) {
      $sell_type = intval($form_param['sell_type']);
      $sell_type = array('sell_type' => $sell_type);
      $cond_where = $cond_where + $sell_type;
    }

    //户型条件
    if (isset($form_param['room']) && !empty($form_param['room']) && $form_param['room'] > 0) {
      $room = intval($form_param['room']);
      $room = array('room' => $room);
      $cond_where = $cond_where + $room;
    }
    return $cond_where;
  }

  //重组出租的搜索条件
  private function _get_cond_str_rent($form_param)
  {
    $cond_where = array();
    //板块
    if (isset($form_param['block']) && !empty($form_param['block']) && $form_param['block'] > 0) {
      $block = intval($form_param['block']);
      $block = $this->district_model->get_streetname_by_id($block);
      $block = array('block' => $block);
      $cond_where = $block;
    }
    //区属
    if (isset($form_param['district']) && !empty($form_param['district']) && $form_param['district'] > 0) {
      $district = intval($form_param['district']);
      $district = $this->district_model->get_distname_by_id($district);
      $district = array('district' => $district);
      $cond_where = $cond_where + $district;
    }

    //采集区属
    if (isset($form_param['district_cj']) && !empty($form_param['district_cj']) && $form_param['district_cj'] > 0) {
      $district = intval($form_param['district_cj']);
      $district = $this->district_model->get_cjdistname_by_id($district);
      $district = str_replace('县', '', str_replace('区', '', $district));
      $district = array('district' => $district);
      $cond_where = $cond_where + $district;
    }

    //楼盘名
    if (!empty($form_param['house_name']) && $form_param['house_name'] != '') {
      $house_name = trim($form_param['house_name']);
      $house_name = array('house_name' => $house_name);
      $cond_where = $cond_where + $house_name;
    }

    //面积条件
    if (!empty($form_param['rent_area']) && $form_param['rent_area'] > 0) {
      $rent_area = intval($form_param['rent_area']);
      $area = $this->house_config_model->get_config();
      $area_val = $area['rent_area'][$rent_area];
      if (!empty($area_val)) {
        $area_val = preg_replace("#[^0-9-]#", '', $area_val);
        $area_val = explode('-', $area_val);
        if (count($area_val) == 2) {
          $cond_where = $cond_where + array('buildarea >=' => $area_val[0], 'buildarea <=' => $area_val[1]);

        } else {
          if ($rent_area == 1) {
            $cond_where = $cond_where + array('buildarea <=' => $area_val[0]);
          } else {
            $cond_where = $cond_where + array('buildarea >=' => $area_val[0]);
          }
        }
      }
    }


    //价格条件
    if (isset($form_param['rent_price']) && !empty($form_param['rent_price']) && $form_param['rent_price'] > 0) {
      $price = intval($form_param['rent_price']);
      $rent_price = $this->house_config_model->get_config();
      $price_val = $rent_price['rent_price'][$price];
      if ($price_val) {
        $price_val = preg_replace("#[^0-9-]#", '', $price_val);
        $price_val = explode('-', $price_val);
        if (count($price_val) == 2) {
          $cond_where = $cond_where + array('price >=' => $price_val[0], 'price <=' => $price_val[1]);
        } else {
          if ($price == 1) {
            $cond_where = $cond_where + array('price <=' => $price_val[0]);
          } else {
            $cond_where = $cond_where + array('price >=' => $price_val[0]);
          }
        }
      }
    }

    //物业类型条件
    if (isset($form_param['rent_type']) && !empty($form_param['rent_type']) && $form_param['rent_type'] > 0) {
      $rent_type = intval($form_param['rent_type']);
      $rent_type = array('rent_type' => $rent_type);
      $cond_where = $cond_where + $rent_type;
    }

    //户型条件
    if (isset($form_param['room']) && !empty($form_param['room']) && $form_param['room'] > 0) {
      $room = intval($form_param['room']);
      $room = array('room' => $room);
      $cond_where = $cond_where + $room;
    }
    return $cond_where;
  }
}

/* End of file my_collections.php */
/* Location: ./application/mls/controllers/my_collections.php */
