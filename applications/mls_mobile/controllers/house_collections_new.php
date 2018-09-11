<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 好房看看 Class
 *
 * 采集控制器
 *
 * @package      mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      angel_in_us
 */
class House_collections_new extends MY_Controller
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
    //$this->load->model('collections_model_new');//采集模型类
    $this->load->library('form_validation');//表单验证
    //$this->load->model('district_model');//区属模型类
    $this->load->model('broker_model');  //经纪人模型类
    $this->load->model('house_config_model');  //房源配置模型类
    //	$this->load->model('view_log_model');//添加日志
    error_reporting(E_ALL || ~E_NOTICE);
  }


  /**
   * 好房看看---出售房源
   * @access public
   * @return void
   * date 2014-12-28
   * author angel_in_us
   */
  public function collect_sell($page = 1)
  {
    $this->load->model('collections_model_new');//采集模型类
    $this->load->model('district_model');//区属模型类

    //获取经纪人基本信息
    $broker_data = $this->user_arr;
    $broker_id = $this->input->get('broker_id');
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    $data['where_in'] = array();
    $data['like'] = $data['or_like'] = array();
    $arr = array("broker_id" => $broker_id, 'tbl_name' => 'sell_house_collect');
    $judge = $this->collections_model_new->get_agent_house($arr);
    $house_ids = array();
    foreach ($judge as $k => $v) {
      $house_ids[] = $v['house_id'];
    }
    $data['judge'] = $judge;
    $data['broker_info'] = $broker_id;
    $data['valid_time'] = 3;

    //get参数  ===>>>实际为post参数
    $post_param = $this->input->get(NULL, TRUE);
    $data['where_cond'] = $this->_get_cond_str($post_param);
//        if($data['where_cond']['district'] == '姑苏')
//        {
//                if($data['where_cond']['house_name'] != " " && !empty($data['where_cond']['house_name'])){
//                        $data['like'] = array('block'=>$data['where_cond']['house_name']);
//                        unset($data['where_cond']['house_name']);
//                }
//
//                if($data['where_cond']['district'] != "" && !empty($data['where_cond']['district'])){
//                        $data['or_like']['like_key'] = 'district';
//                        $data['or_like']['like_value'] = array('平江','沧浪','金阊');
//                }
//        }
//        else
//        {
    if ($data['where_cond']['district'] != " " && !empty($data['where_cond']['district'])) {
      $data['like'] = array('district' => $data['where_cond']['district']);
      unset($data['where_cond']['district']);
    }
    if ($data['where_cond']['street'] != " " && !empty($data['where_cond']['street'])) {
      $data['like'] = $data['like'] + array('block' => $data['where_cond']['street']);
      unset($data['where_cond']['street']);
    }
    if ($data['where_cond']['house_name'] != "" && !empty($data['where_cond']['house_name'])) {
      $data['or_like']['like_key'] = array('block', 'house_name', 'house_title');
      $data['or_like']['like_value'] = $data['where_cond']['house_name'];
    }
    //}

    unset($data['where_cond']['house_name']);
    //分页请求
    if (!isset($post_param['page_size']) && empty($post_param['page_size'])) {
      $this->_limit = $this->_limit;
    } else {
      $this->_limit = $post_param['page_size'];
    }

    if (!isset($post_param['page']) && empty($post_param['page'])) {
      $page = 1;
    } else {
      $this->_init_pagination($post_param['page']);
    }

    $data['district'] = $this->district_model->get_cj_district();
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    //$this->_init_pagination($page);
    //已查看
    $arr = array("broker_id" => $broker_id, 'tbl_name' => 'sell_house_collect');
    $judge = $this->collections_model_new->get_agent_house($arr);
    //sphinx搜素
    $result = $this->collections_model_new->sphinx($data['where_cond'], $data['where_in'], $data['like'], $data['or_like'], $data['valid_time'], $broker_data['city_spell'], 'sell', $page, $this->_limit, $this->_offset);
    $data['blacklist'] = $result['blacklist'];
    //符合条件的总行数
    $this->_total_count = $count = $result['total'];
//		$this->_total_count = $count = $this->collections_model_new->get_sell_num($data['where_cond'],$data['like'],$data['or_like']);
    //计算总页数
//        $pages  = $this->_total_count > 0 ? ceil( $this->_total_count / $this->_limit ) : 0;
    $pages = $this->_total_count > 0 ? (ceil($this->_total_count / $this->_limit) > $page_max ? $page_max : ceil($this->_total_count / $this->_limit)) : 0;
    //分页处理
    $params = array(
      'pages' => $pages, //总页数(sphinx专用)
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $post_param['page'],//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');
//        $data['order_by'] = 'createtime';
//        $data['blacklist'] = $this->collections_model_new->get_house_sell($data['where_cond'],$data['where_in']=array(),$data['like'],$data['or_like'],$data['order_by'],$this->_limit,$this->_offset);
    //$data['blacklist_all'] = $this->collections_model_new->get_house_sell_allids();
    $k = 0;
    foreach ($data['blacklist_all'] as $key => $value) {
      if (is_object($value)) {
        $value = (array)$value;
      }
      if (in_array($value['id'], $house_ids)) {
        $k = $k + 1;
      }
    }
    $read_count = $k;
    $info = array('count' => 0, 'read_count' => 0, 'data' => array());
    //获取列表内容
    if (!empty($data['blacklist'])) {
      //数据重构
      $info['count'] = $count;
      $info['read_count'] = $read_count;
      foreach ($data['blacklist'] as $k => $v) {
        $id = $v['id'];
        $res = $this->collections_model_new->get_sell_house_collect_history($id);
        if (!empty($res)) {
          $a['history'] = 1;
          if ($v['price'] > $res[0]['price']) {
            $a['price_change'] = 1;//价格上升
          } elseif ($val['price'] < $res[0]['price']) {
            $a['price_change'] = 2;//价格下降
          }
        } else {
          $a['price_change'] = 0;
          $a['history'] = 0;
        }
        $type = $v['sell_type'];
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
        if (in_array($v['id'], $house_ids)) {
          $a['if_read'] = 1;
        } else {
          $a['if_read'] = 0;
        }
        $a['sell_type'] = $sell_type;
        $a['district'] = $v['district'];
        $a['block'] = $v['block'];
        if (empty($v['house_name'])) {
          $v['house_name'] = '暂无资料';
        }
        $a['house_name'] = $v['house_name'];
        $a['telno1'] = $v['telno1'];
        $a['room'] = $v['room'];
        $a['hall'] = $v['hall'];
        $a['toilet'] = $v['toilet'];
        $a['balcony'] = $v['balcony'];
        $a['floor'] = $v['floor'];
        $a['totalfloor'] = $v['totalfloor'];
        $a['pic'] = $v['picurl'] != '暂无资料' ? 1 : 0;
        $a['owner'] = $v['owner'] ? $v['owner'] : '暂无资料';
        $a['buildarea'] = strip_end_0($v['buildarea']);
        $a['price'] = strip_end_0($v['price']);
        //$a['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
        if ($v['createtime'] > strtotime(date('Y-m-d'))) {
          //今天
          $a['createtime'] = '今天' . date('H:i', $v['createtime']);
        } else {
          //24小时以前
          $a['createtime'] = date('Y-m-d', $v['createtime']);
        }
        //是否为刷新房源
        $a['refresh'] = $v['refresh'];
        $info['data'][] = $a;
      }

      $info['count'] = $count;
      $info['read_count'] = count($judge);
      $this->result(1, '查询出售房源成功', $info);
    } else {
      $this->result(1, '暂无查询内容', $info);
    }
  }

  /**
   * 采集房源 出售 用于合作app
   * @access public
   * @return void
   * date 2016-8-7
   * author yuan
   */
  public function collect_sell_cooperate($page = 1)
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_spell = $this->input->get('city_spell');
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_new');//采集模型类
      $this->load->model('district_model');//区属模型类

      $broker_id = $this->input->get('broker_id');
      $data['conf_where'] = 'index';
      $data['where_cond'] = array();
      $data['like'] = $data['or_like'] = array();

      $judge_str = $this->input->get('judge_str');
      $judge = array();
      if (!empty($judge_str)) {
        $judge = unserialize($judge_str);
      }

      $house_ids = array();
      foreach ($judge as $k => $v) {
        $house_ids[] = $v['house_id'];
      }
      $data['judge'] = $judge;
      $data['broker_info'] = $broker_id;

      //get参数  ===>>>实际为post参数
      $post_param = $this->input->get(NULL, TRUE);
      $data['where_cond'] = $this->_get_cond_str($post_param);

      if ($data['where_cond']['district'] != " " && !empty($data['where_cond']['district'])) {
        $data['like'] = array('district' => $data['where_cond']['district']);
        unset($data['where_cond']['district']);
      }
      if ($data['where_cond']['street'] != " " && !empty($data['where_cond']['street'])) {
        $data['like'] = $data['like'] + array('block' => $data['where_cond']['street']);
        unset($data['where_cond']['street']);
      }
      if ($data['where_cond']['house_name'] != "" && !empty($data['where_cond']['house_name'])) {
        $data['or_like']['like_key'] = array('block', 'house_name', 'house_title');
        $data['or_like']['like_value'] = $data['where_cond']['house_name'];
      }

      unset($data['where_cond']['house_name']);
      //分页请求
      if (!isset($post_param['page_size']) && empty($post_param['page_size'])) {
        $this->_limit = $this->_limit;
      } else {
        $this->_limit = $post_param['page_size'];
      }

      if (!isset($post_param['page']) && empty($post_param['page'])) {
        $page = 1;
      } else {
        $this->_init_pagination($post_param['page']);
      }

      $data['district'] = $this->district_model->get_cj_district();
      // 分页参数
      $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
      //符合条件的总行数
      $this->_total_count = $count = $this->collections_model_new->get_sell_num($data['where_cond'], $data['like'], $data['or_like']);
      //计算总页数
      $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
      //分页处理
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
      $data['blacklist'] = $this->collections_model_new->get_house_sell($data['where_cond'], $data['where_in'] = array(), $data['like'], $data['or_like'], $data['order_by'], $this->_limit, $this->_offset);
      //$data['blacklist_all'] = $this->collections_model_new->get_house_sell_allids();
      $k = 0;
      foreach ($data['blacklist_all'] as $key => $value) {
        if (is_object($value)) {
          $value = (array)$value;
        }
        if (in_array($value['id'], $house_ids)) {
          $k = $k + 1;
        }
      }
      $read_count = $k;
      $info = array('count' => 0, 'read_count' => 0, 'data' => array());
      //获取列表内容
      if (!empty($data['blacklist'])) {
        //数据重构
        $info['count'] = $count;
        $info['read_count'] = $read_count;
        foreach ($data['blacklist'] as $k => $v) {
          $id = $v['id'];
          $res = $this->collections_model_new->get_sell_house_collect_history($id);
          if (!empty($res)) {
            $a['history'] = 1;
            if ($v['price'] > $res[0]['price']) {
              $a['price_change'] = 1;//价格上升
            } elseif ($val['price'] < $res[0]['price']) {
              $a['price_change'] = 2;//价格下降
            }
          } else {
            $a['price_change'] = 0;
            $a['history'] = 0;
          }
          $type = $v['sell_type'];
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
          if (in_array($v['id'], $house_ids)) {
            $a['if_read'] = 1;
          } else {
            $a['if_read'] = 0;
          }
          $a['sell_type'] = $sell_type;
          $a['district'] = $v['district'];
          $a['block'] = $v['block'];
          if (empty($v['house_name'])) {
            $v['house_name'] = '暂无资料';
          }
          $a['house_name'] = $v['house_name'];
          $a['telno1'] = $v['telno1'];
          $a['room'] = $v['room'];
          $a['hall'] = $v['hall'];
          $a['toilet'] = $v['toilet'];
          $a['balcony'] = $v['balcony'];
          $a['floor'] = $v['floor'];
          $a['totalfloor'] = $v['totalfloor'];
          $a['pic'] = $v['picurl'] != '暂无资料' ? 1 : 0;
          $a['owner'] = $v['owner'] ? $v['owner'] : '暂无资料';
          $a['buildarea'] = strip_end_0($v['buildarea']);
          $a['price'] = strip_end_0($v['price']);
          //$a['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
          if ($v['createtime'] > strtotime(date('Y-m-d'))) {
            //今天
            $a['createtime'] = '今天' . date('H:i', $v['createtime']);
          } else {
            //24小时以前
            $a['createtime'] = date('Y-m-d', $v['createtime']);
          }
          //是否为刷新房源
          $a['refresh'] = $v['refresh'];
          $info['data'][] = $a;
        }

        $info['count'] = $count;
        $info['read_count'] = count($judge);
        $this->result(1, '查询出售房源成功', $info);
      } else {
        $this->result(1, '暂无查询内容', $info);
      }
    } else {
      $this->result(0, '城市参数错误', $info);
    }
  }

  /**
   * 采集房源 出售 用于合作app
   * @access public
   * @return void
   * date 2014-12-28
   * author yuan
   */
  public function collect_rent_cooperate($page = 1)
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_spell = $this->input->get('city_spell');
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_new');//采集模型类
      $this->load->model('district_model');//区属模型类

      $broker_id = $this->input->get('broker_id');
      $data['conf_where'] = 'index';
      $data['where_cond'] = array();
      $data['like'] = $data['or_like'] = array();

      $judge_str = $this->input->get('judge_str');
      $judge = array();
      if (!empty($judge_str)) {
        $judge = unserialize($judge_str);
      }
      $house_ids = array();
      foreach ($judge as $k => $v) {
        $house_ids[] = $v['house_id'];
      }
      $data['judge'] = $judge;
      $data['broker_info'] = $broker_id;

      //get参数   ===>>>实际为post参数
      $get_param = $this->input->get(NULL, TRUE);
      $data['where_cond'] = $this->_get_cond_str_rent($get_param);

      if ($data['where_cond']['district'] != " " && !empty($data['where_cond']['district'])) {
        $data['like'] = array('district' => $data['where_cond']['district']);
        unset($data['where_cond']['district']);
      }
      if ($data['where_cond']['street'] != " " && !empty($data['where_cond']['street'])) {
        $data['like'] = $data['like'] + array('block' => $data['where_cond']['street']);
        unset($data['where_cond']['street']);
      }
      if ($data['where_cond']['house_name'] != "" && !empty($data['where_cond']['house_name'])) {
        $data['or_like']['like_key'] = array('block', 'house_name', 'house_title');
        $data['or_like']['like_value'] = $data['where_cond']['house_name'];
      }
      unset($data['where_cond']['house_name']);

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

      $data['district'] = $this->district_model->get_cj_district();
      //post参数
      $post_param = $get_param;
      // 分页参数
      $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
      //符合条件的总行数
      $this->_total_count = $count = $this->collections_model_new->get_rent_num($data['where_cond'], $data['like'], $data['or_like']);
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
      $data['blacklist'] = $this->collections_model_new->get_house_rent($data['where_cond'], $data['where_in'] = array(), $data['like'], $data['or_like'], $data['order_by'], $this->_offset, $this->_limit);
      //$data['blacklist_all'] = $this->collections_model_new->get_house_rent_allids();

      $k = 0;
      foreach ($data['blacklist_all'] as $key => $value) {
        if (is_object($value)) {
          $value = (array)$value;
        }
        if (in_array($value['id'], $house_ids)) {
          $k = $k + 1;
        }
      }
      $read_count = $k;
      $info = array('count' => 0, 'read_count' => 0, 'data' => array());
      //获取列表内容
      if (!empty($data['blacklist'])) {
        foreach ($data['blacklist'] as $k => $v) {
          $id = $v['id'];
          $res = $this->collections_model_new->get_rent_house_collect_history($id);
          if (!empty($res)) {
            $a['history'] = 1;
            if ($v['price'] > $res[0]['price']) {
              $a['price_change'] = 1;//价格上升
            } elseif ($val['price'] < $res[0]['price']) {
              $a['price_change'] = 2;//价格下降
            }
          } else {
            $a['history'] = 0;
            $a['price_change'] = 0;
          }
          $type = $v['rent_type'];
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
          if (in_array($v['id'], $house_ids)) {
            $a['if_read'] = 1;
          } else {
            $a['if_read'] = 0;
          }
          $a['rent_type'] = $rent_type;
          $a['district'] = $v['district'];
          $a['block'] = $v['block'];
          if (empty($v['house_name'])) {
            $v['house_name'] = '暂无资料';
          }
          $a['house_name'] = $v['house_name'];
          $a['room'] = $v['room'];
          $a['hall'] = $v['hall'];
          $a['toilet'] = $v['toilet'];
          $a['balcony'] = $v['balcony'];
          $a['floor'] = $v['floor'];
          $a['totalfloor'] = $v['totalfloor'];
          $a['pic'] = $v['picurl'] != '暂无资料' ? 1 : 0;
          $a['telno1'] = $v['telno1'];
          $a['owner'] = $v['owner'] ? $v['owner'] : '暂无资料';
          $a['buildarea'] = strip_end_0($v['buildarea']);
          $a['price'] = strip_end_0($v['price']);
          if ($v['createtime'] > strtotime(date('Y-m-d'))) {
            //今天
            $a['createtime'] = '今天' . date('H:i', $v['createtime']);
          } else {
            //24小时以前
            $a['createtime'] = date('Y-m-d', $v['createtime']);
          }
          //是否为刷新房源
          $a['refresh'] = $v['refresh'];
          $info['data'][] = $a;
        }
        $info['count'] = $count;
        $info['read_count'] = count($judge);
        $this->result(1, '查询出租房源成功', $info);
      } else {
        $this->result(1, '暂无查询内容', $info);
      }
    } else {
      $this->result(0, '城市参数错误', $info);
    }
  }

  /**
   * 好房看看---出租房源
   * @access public
   * @return void
   * date 2014-12-28
   * author angel_in_us
   */
  public function collect_rent($page = 1)
  {
    $this->load->model('collections_model_new');//采集模型类
    $this->load->model('district_model');//区属模型类

    //获取经纪人基本信息
    $broker_data = $this->user_arr;
    $broker_id = $this->input->get('broker_id');
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    $data['where_in'] = array();
    $data['like'] = $data['or_like'] = array();

    $arr = array("broker_id" => $broker_id, 'tbl_name' => 'rent_house_collect');
    $judge = $this->collections_model_new->get_agent_house($arr);
    $house_ids = array();
    foreach ($judge as $k => $v) {
      $house_ids[] = $v['house_id'];
    }
    $data['judge'] = $judge;
    $data['broker_info'] = $broker_id;
    $data['valid_time'] = 3;

    //get参数   ===>>>实际为post参数
    $get_param = $this->input->get(NULL, TRUE);
    $data['where_cond'] = $this->_get_cond_str_rent($get_param);
//        if($data['where_cond']['district'] == '姑苏')
//		{
//			if($data['where_cond']['house_name'] != " " && !empty($data['where_cond']['house_name'])){
//				$data['like'] = array('block'=>$data['where_cond']['house_name']);
//				unset($data['where_cond']['house_name']);
//			}
//
//			if($data['where_cond']['district'] != "" && !empty($data['where_cond']['district'])){
//				$data['or_like']['like_key'] = 'district';
//				$data['or_like']['like_value'] = array('平江','沧浪','金阊');
//			}
//		}
//		else
//		{
    if ($data['where_cond']['district'] != " " && !empty($data['where_cond']['district'])) {
      $data['like'] = array('district' => $data['where_cond']['district']);
      unset($data['where_cond']['district']);
    }
    if ($data['where_cond']['street'] != " " && !empty($data['where_cond']['street'])) {
      $data['like'] = $data['like'] + array('block' => $data['where_cond']['street']);
      unset($data['where_cond']['street']);
    }
    if ($data['where_cond']['house_name'] != "" && !empty($data['where_cond']['house_name'])) {
      $data['or_like']['like_key'] = array('block', 'house_name', 'house_title');
      $data['or_like']['like_value'] = $data['where_cond']['house_name'];
    }
//		}
    unset($data['where_cond']['house_name']);

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

    $data['district'] = $this->district_model->get_cj_district();
    //post参数
    $post_param = $get_param;
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    //$this->_init_pagination($page);
    //已查看
    $arr = array("broker_id" => $broker_id, 'tbl_name' => 'rent_house_collect');
    $judge = $this->collections_model_new->get_agent_house($arr);
    //sphinx搜素
    $result = $this->collections_model_new->sphinx($data['where_cond'], $data['where_in'], $data['like'], $data['or_like'], $data['valid_time'], $broker_data['city_spell'], 'rent', $page, $this->_limit, $this->_offset);
    $data['blacklist'] = $result['blacklist'];
    //符合条件的总行数
    $this->_total_count = $count = $result['total'];
//        $this->_total_count = $count = $this->collections_model_new->get_rent_num($data['where_cond'],$data['like'],$data['or_like']);
    //计算总页数
    //$pages  = $this->_total_count > 0 ? ceil( $this->_total_count / $this->_limit ) : 0;
    $pages = $this->_total_count > 0 ? (ceil($this->_total_count / $this->_limit) > $page_max ? $page_max : ceil($this->_total_count / $this->_limit)) : 0;
    //分页处理000000000000
    $params = array(
      'pages' => $pages, //总页数(sphinx专用)
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $post_param['page'],//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');
//        $data['order_by'] = 'createtime';
//        $data['blacklist'] = $this->collections_model_new->get_house_rent($data['where_cond'],$data['where_in']=array(),$data['like'],$data['or_like'],$data['order_by'],$this->_offset,$this->_limit);
    //$data['blacklist_all'] = $this->collections_model_new->get_house_rent_allids();

    $k = 0;
    foreach ($data['blacklist_all'] as $key => $value) {
      if (is_object($value)) {
        $value = (array)$value;
      }
      if (in_array($value['id'], $house_ids)) {
        $k = $k + 1;
      }
    }
    $read_count = $k;
    $info = array('count' => 0, 'read_count' => 0, 'data' => array());
    //获取列表内容
    if (!empty($data['blacklist'])) {
      foreach ($data['blacklist'] as $k => $v) {
        $id = $v['id'];
        $res = $this->collections_model_new->get_rent_house_collect_history($id);
        if (!empty($res)) {
          $a['history'] = 1;
          if ($v['price'] > $res[0]['price']) {
            $a['price_change'] = 1;//价格上升
          } elseif ($val['price'] < $res[0]['price']) {
            $a['price_change'] = 2;//价格下降
          }
        } else {
          $a['history'] = 0;
          $a['price_change'] = 0;
        }
        $type = $v['rent_type'];
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
        if (in_array($v['id'], $house_ids)) {
          $a['if_read'] = 1;
        } else {
          $a['if_read'] = 0;
        }
        $a['rent_type'] = $rent_type;
        $a['district'] = $v['district'];
        $a['block'] = $v['block'];
        if (empty($v['house_name'])) {
          $v['house_name'] = '暂无资料';
        }
        $a['house_name'] = $v['house_name'];
        $a['room'] = $v['room'];
        $a['hall'] = $v['hall'];
        $a['toilet'] = $v['toilet'];
        $a['balcony'] = $v['balcony'];
        $a['floor'] = $v['floor'];
        $a['totalfloor'] = $v['totalfloor'];
        $a['pic'] = $v['picurl'] != '暂无资料' ? 1 : 0;
        $a['telno1'] = $v['telno1'];
        $a['owner'] = $v['owner'] ? $v['owner'] : '暂无资料';
        $a['buildarea'] = strip_end_0($v['buildarea']);
        $a['price'] = strip_end_0($v['price']);
        if ($v['createtime'] > strtotime(date('Y-m-d'))) {
          //今天
          $a['createtime'] = '今天' . date('H:i', $v['createtime']);
        } else {
          //24小时以前
          $a['createtime'] = date('Y-m-d', $v['createtime']);
        }
        //是否为刷新房源
        $a['refresh'] = $v['refresh'];
        $info['data'][] = $a;
      }
      $info['count'] = $count;
      $info['read_count'] = count($judge);
      $this->result(1, '查询出租房源成功', $info);
    } else {
      $this->result(1, '暂无查询内容', $info);
    }
  }

  /**
   * 好房看看---出售房源
   * @access public
   * @return void
   * date 2015-01-09
   * author angel_in_us
   */
  public function good_sell_details()
  {
    $this->load->model('collections_model_new');//采集模型类
    $this->load->model('view_log_model');//添加日志
    $house_id = $this->input->get('house_id');

    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    //根据房源house_id去查询房源详情
    if (!empty($house_id)) {
      $data['where_cond'] = array('id' => $house_id);
      $data['house_info'] = $this->collections_model_new->get_housesell_byid($data['where_cond']);
      //获取房源详情
      if (!empty($data['house_info'])) {
        foreach ($data['house_info'] as $key => $v) {
          //数据重构
          $info = array();
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
            default:
              $sell_type = "其它";
              break;
          }
          //住宅类型 1:多层 2:高层 3:小高层 4:复式 5:顶+阁 6:底+院 7:私房 8:其它
          switch ($v['house_type']) {
            case "1":
              $house_type = "多层";
              break;
            case "2":
              $house_type = "高层";
              break;
            case "3":
              $house_type = "小高层";
              break;
            case "4":
              $house_type = "复式";
              break;
            case "5":
              $house_type = "顶+阁";
              break;
            case "6":
              $house_type = "底+院";
              break;
            case "7":
              $house_type = "私房";
              break;
            case "8":
              $house_type = "其它";
              break;
            default:
              $house_type = "其它";
              break;
          }
          //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
          switch ($v['forward']) {
            case "1":
              $forward = "东";
              break;
            case "2":
              $forward = "东南";
              break;
            case "3":
              $forward = "南";
              break;
            case "4":
              $forward = "西南";
              break;
            case "5":
              $forward = "西";
              break;
            case "6":
              $forward = "西北";
              break;
            case "7":
              $forward = "北";
              break;
            case "8":
              $forward = "东北";
              break;
            case "9":
              $forward = "东西";
              break;
            case "10":
              $forward = "南北";
              break;
            default:
              $forward = "暂无资料";
              break;
          }
          //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
          switch ($v['serverco']) {
            case "1":
              $serverco = "毛坯";
              break;
            case "2":
              $serverco = "简装";
              break;
            case "3":
              $serverco = "中装";
              break;
            case "4":
              $serverco = "精装";
              break;
            case "5":
              $serverco = "豪装";
              break;
            case "6":
              $serverco = "婚装";
              break;
            default:
              $serverco = "暂无资料";
              break;
          }
          if ($v['lowprice'] == '' || empty($v['lowprice'])) {
            $v['lowprice'] = '暂无资料';
          }
          if ($v['buildyear'] == '' || empty($v['buildyear'])) {
            $v['buildyear'] = '暂无资料';
          }
          if ($v['remark'] == '' || empty($v['remark'])) {
            $v['remark'] = '暂无资料';
          }
          $a['sell_type'] = $sell_type;
          $a['district'] = $v['district'];
          $a['block'] = $v['block'];
          $a['house_name'] = $v['house_name'];
          $a['buildarea'] = strip_end_0($v['buildarea']);
          $a['house_addr'] = trim($v['house_addr']);
          $a['house_type'] = $house_type;
          $a['price'] = strip_end_0($v['price']);
          $a['avgprice'] = floor(strip_end_0($v['avgprice']));
          $a['lowprice'] = strip_end_0($v['lowprice']);
          $a['forward'] = $forward;
          $a['room'] = $v['room'];
          $a['hall'] = $v['hall'];
          $a['toilet'] = $v['toilet'];
          $a['balcony'] = $v['balcony'];
          $a['floor'] = $v['floor'];
          $a['totalfloor'] = $v['totalfloor'];
          $a['serverco'] = $serverco;
          $a['pic'] = '';
          if ($v['picurl'] != '暂无资料') {
            $picarr = explode('*', $v['picurl']);
            if (is_full_array($picarr) && $picarr[0] != '暂无资料') {
              $temp = 0;
              foreach ($picarr as $pic) {
                if ($pic != '') {
                  $a['pic'][$temp]['url'] = $pic;
                  $temp++;
                }

                if ($temp >= 10) {
                  break;
                }
              }
            } else {
              $a['pic'] = '';
            }
          }
          //$a['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
          if ($v['createtime'] > strtotime(date('Y-m-d'))) {
            //今天
            $a['createtime'] = '今天：' . date('H:i:s', $v['createtime']);
          } else {
            //24小时以前
            $a['createtime'] = date('Y-m-d', $v['createtime']);
          }
          $a['buildyear'] = $v['buildyear'];
          $a['owner'] = $v['owner'];
          $a['telno1'] = $v['telno1'];
          $a['remark'] = $v['remark'];
          //判断价格是否有变动
          $res = $this->collections_model_new->get_sell_house_collect_history($house_id);
          if (!empty($res)) {
            $type = array(
              '0' => '',
              '1' => '毛坯',
              '2' => '简装',
              '3' => '中装',
              '4' => '精装',
              '5' => '豪装',
              '6' => '婚装'
            );
            foreach ($res as $key => $val) {
              $res[$key]['serverco'] = $type[$val['serverco']];
              $res[$key]['oldtime'] = date('Y-m-d H:i:s', $val['oldtime']);
              $res[$key]['price'] = strip_end_0($res[$key]['price']);
              unset($res[$key]['id']);
              unset($res[$key]['createtime']);
            }
            $a['price_change_history'] = $res;
          } else {
            $a['price_change_history'] = array();
          }
          //判断疑视经纪人
          $a['isagent'] = $v['isagent'];
          //检测房源是否下架
          $check_mess = $this->collections_model_new->check_sell_house($house_id);
          if ($check_mess && !empty($check_mess)) {
            $interval = time() - $check_mess['checktime'];
            $status = $interval > 3600 ? 1 : $check_mess['status'];//1 正常 2下架
            $choose_check = $interval > 3600 ? 1 : 2;//1 检查 2不检查
          } else {
            $choose_check = 1;//1 检查 2不检查
          }
          if ($choose_check == 1) {
            $data['id'] = $house_id;
            $data['sell_type'] = $v['sell_type'];//1=》住宅；2=》别墅；3=》商铺；4=》写字楼；
            $data['source_from'] = $v['source_from'];//source_from 0=》赶集；1=》58同城
            $data['oldurl'] = $v['oldurl'];
            $status = $this->collections_model_new->check_sell_ajax($data);
          }
          $a['status'] = $status;
          //组装条件查询经纪人已看房源
          $arr = array("broker_id" => $this->user_arr['broker_id'], 'tbl_name' => 'sell_house_collect', 'house_id' => $house_id);
          $judge = $this->collections_model_new->get_agent_house($arr);
          if (is_full_array($judge)) {
            $a['is_input'] = $judge[0]['is_input'];
          } else {
            $a['is_input'] = -1;
          }
          //组装查询条件
          $where_cond = array(
            'tbl_name' => 'sell_house_collect',
            'house_id' => $house_id
          );
          //该房源被查看的次数
          $read_times = array();
          $read_times = $this->collections_model_new->get_readtimes_byid($where_cond);
          $a['read_times'] = count($read_times);
          //获取经纪人基本信息
          $broker_data = $this->user_arr;
          $company_id = $broker_data['company_id'];
          $num = $this->collections_model_new->collect_sell_publish_check($house_id, $company_id);
          $a['company_add_house'] = $num > 0 ? 1 : 0;
          $info = $a;
        }
        //加入点击日志
        $this->view_log_model->add_collect_click_log($this->user_arr['broker_id'], $house_id, 1);
        $this->result(1, '查询成功', $info);
      } else {
        $this->result(1, '未查询到数据！');
      }
    } else {
      $this->result(0, '参数不合法，查询失败！');
    }
  }

  /**
   * 出售详情，用于合作app
   * @access public
   * @return void
   * date 2015-01-09
   * author yuan
   */
  public function good_sell_details_cooperate()
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_spell = $this->input->get('city_spell');
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_new');//采集模型类
      $this->load->model('view_log_model');//添加日志
      $house_id = $this->input->get('house_id');

      $data['conf_where'] = 'index';
      $data['where_cond'] = array();
      //根据房源house_id去查询房源详情
      if (!empty($house_id)) {
        $data['where_cond'] = array('id' => $house_id);
        $data['house_info'] = $this->collections_model_new->get_housesell_byid($data['where_cond']);
        //获取房源详情
        if (!empty($data['house_info'])) {
          foreach ($data['house_info'] as $key => $v) {
            //数据重构
            $info = array();
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
              default:
                $sell_type = "其它";
                break;
            }
            //住宅类型 1:多层 2:高层 3:小高层 4:复式 5:顶+阁 6:底+院 7:私房 8:其它
            switch ($v['house_type']) {
              case "1":
                $house_type = "多层";
                break;
              case "2":
                $house_type = "高层";
                break;
              case "3":
                $house_type = "小高层";
                break;
              case "4":
                $house_type = "复式";
                break;
              case "5":
                $house_type = "顶+阁";
                break;
              case "6":
                $house_type = "底+院";
                break;
              case "7":
                $house_type = "私房";
                break;
              case "8":
                $house_type = "其它";
                break;
              default:
                $house_type = "其它";
                break;
            }
            //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
            switch ($v['forward']) {
              case "1":
                $forward = "东";
                break;
              case "2":
                $forward = "东南";
                break;
              case "3":
                $forward = "南";
                break;
              case "4":
                $forward = "西南";
                break;
              case "5":
                $forward = "西";
                break;
              case "6":
                $forward = "西北";
                break;
              case "7":
                $forward = "北";
                break;
              case "8":
                $forward = "东北";
                break;
              case "9":
                $forward = "东西";
                break;
              case "10":
                $forward = "南北";
                break;
              default:
                $forward = "暂无资料";
                break;
            }
            //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
            switch ($v['serverco']) {
              case "1":
                $serverco = "毛坯";
                break;
              case "2":
                $serverco = "简装";
                break;
              case "3":
                $serverco = "中装";
                break;
              case "4":
                $serverco = "精装";
                break;
              case "5":
                $serverco = "豪装";
                break;
              case "6":
                $serverco = "婚装";
                break;
              default:
                $serverco = "暂无资料";
                break;
            }
            if ($v['lowprice'] == '' || empty($v['lowprice'])) {
              $v['lowprice'] = '暂无资料';
            }
            if ($v['buildyear'] == '' || empty($v['buildyear'])) {
              $v['buildyear'] = '暂无资料';
            }
            if ($v['remark'] == '' || empty($v['remark'])) {
              $v['remark'] = '暂无资料';
            }
            $a['sell_type'] = $sell_type;
            $a['district'] = $v['district'];
            $a['block'] = $v['block'];
            $a['house_name'] = $v['house_name'];
            $a['buildarea'] = strip_end_0($v['buildarea']);
            $a['house_addr'] = trim($v['house_addr']);
            $a['house_type'] = $house_type;
            $a['price'] = strip_end_0($v['price']);
            $a['avgprice'] = floor(strip_end_0($v['avgprice']));
            $a['lowprice'] = strip_end_0($v['lowprice']);
            $a['forward'] = $forward;
            $a['room'] = $v['room'];
            $a['hall'] = $v['hall'];
            $a['toilet'] = $v['toilet'];
            $a['balcony'] = $v['balcony'];
            $a['floor'] = $v['floor'];
            $a['totalfloor'] = $v['totalfloor'];
            $a['serverco'] = $serverco;
            $a['pic'] = '';
            if ($v['picurl'] != '暂无资料') {
              $picarr = explode('*', $v['picurl']);
              if (is_full_array($picarr) && $picarr[0] != '暂无资料') {
                $temp = 0;
                foreach ($picarr as $pic) {
                  if ($pic != '') {
                    $a['pic'][$temp]['url'] = $pic;
                    $temp++;
                  }

                  if ($temp >= 10) {
                    break;
                  }
                }
              } else {
                $a['pic'] = '';
              }
            }
            //$a['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
            if ($v['createtime'] > strtotime(date('Y-m-d'))) {
              //今天
              $a['createtime'] = '今天：' . date('H:i:s', $v['createtime']);
            } else {
              //24小时以前
              $a['createtime'] = date('Y-m-d', $v['createtime']);
            }
            $a['buildyear'] = $v['buildyear'];
            $a['owner'] = $v['owner'];
            $a['telno1'] = $v['telno1'];
            $a['remark'] = $v['remark'];
            //判断价格是否有变动
            $res = $this->collections_model_new->get_sell_house_collect_history($house_id);
            if (!empty($res)) {
              $type = array(
                '0' => '',
                '1' => '毛坯',
                '2' => '简装',
                '3' => '中装',
                '4' => '精装',
                '5' => '豪装',
                '6' => '婚装'
              );
              foreach ($res as $key => $val) {
                $res[$key]['serverco'] = $type[$val['serverco']];
                $res[$key]['oldtime'] = date('Y-m-d H:i:s', $val['oldtime']);
                $res[$key]['price'] = strip_end_0($res[$key]['price']);
                unset($res[$key]['id']);
                unset($res[$key]['createtime']);
              }
              $a['price_change_history'] = $res;
            } else {
              $a['price_change_history'] = array();
            }
            //判断疑视经纪人
            $a['isagent'] = $v['isagent'];
            //检测房源是否下架
            $check_mess = $this->collections_model_new->check_sell_house($house_id);
            if ($check_mess && !empty($check_mess)) {
              $interval = time() - $check_mess['checktime'];
              $status = $interval > 3600 ? 1 : $check_mess['status'];//1 正常 2下架
              $choose_check = $interval > 3600 ? 1 : 2;//1 检查 2不检查
            } else {
              $choose_check = 1;//1 检查 2不检查
            }
            if ($choose_check == 1) {
              $data['id'] = $house_id;
              $data['sell_type'] = $v['sell_type'];//1=》住宅；2=》别墅；3=》商铺；4=》写字楼；
              $data['source_from'] = $v['source_from'];//source_from 0=》赶集；1=》58同城
              $data['oldurl'] = $v['oldurl'];
              $status = $this->collections_model_new->check_sell_ajax($data);
            }
            $a['status'] = $status;
            //组装条件查询经纪人已看房源
            $arr = array("broker_id" => $this->user_arr['broker_id'], 'tbl_name' => 'sell_house_collect', 'house_id' => $house_id);
            $judge = $this->collections_model_new->get_agent_house($arr);
            if (is_full_array($judge)) {
              $a['is_input'] = $judge[0]['is_input'];
            } else {
              $a['is_input'] = -1;
            }
            //组装查询条件
            $where_cond = array(
              'tbl_name' => 'sell_house_collect',
              'house_id' => $house_id
            );
            //该房源被查看的次数
            $read_times = array();
            $read_times = $this->collections_model_new->get_readtimes_byid($where_cond);
            $a['read_times'] = count($read_times);
            //获取经纪人基本信息
            $broker_data = $this->user_arr;
            $company_id = $broker_data['company_id'];
            $num = $this->collections_model_new->collect_sell_publish_check($house_id, $company_id);
            $a['company_add_house'] = $num > 0 ? 1 : 0;
            $info = $a;
          }
          //加入点击日志
          $this->view_log_model->add_collect_click_log($this->user_arr['broker_id'], $house_id, 1);
          $this->result(1, '查询成功', $info);
        } else {
          $this->result(1, '未查询到数据！');
        }
      } else {
        $this->result(0, '参数不合法，查询失败！');
      }
    } else {
      $this->result(0, '城市参数错误', $info);
    }
  }

  /**
   * 出租详情，用于合作app
   * @access public
   * @return void
   * date 2015-01-09
   * author yuan
   */
  public function good_rent_details_cooperate()
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_spell = $this->input->get('city_spell');
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_new');//采集模型类
      $this->load->model('view_log_model');//添加日志

      $house_id = $this->input->get('house_id');
      $data['conf_where'] = 'index';
      $data['where_cond'] = array();
      if (!empty($house_id)) {
        $data['where_cond'] = array('id' => $house_id);
        $data['house_info'] = $this->collections_model_new->get_houserent_byid($data['where_cond']);//获取获取房源详情
        if (!empty($data['house_info'])) {
          foreach ($data['house_info'] as $key => $v) {
            //数据重构
            $info = array();
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
              default:
                $rent_type = "其它";
                break;
            }
            //住宅类型 1:多层 2:高层 3:小高层 4:复式 5:顶+阁 6:底+院 7:私房 8:其它
            switch ($v['house_type']) {
              case "1":
                $house_type = "多层";
                break;
              case "2":
                $house_type = "高层";
                break;
              case "3":
                $house_type = "小高层";
                break;
              case "4":
                $house_type = "复式";
                break;
              case "5":
                $house_type = "顶+阁";
                break;
              case "6":
                $house_type = "底+院";
                break;
              case "7":
                $house_type = "私房";
                break;
              case "8":
                $house_type = "其它";
                break;
              default:
                $house_type = "其它";
                break;
            }
            //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
            switch ($v['forward']) {
              case "1":
                $forward = "东";
                break;
              case "2":
                $forward = "东南";
                break;
              case "3":
                $forward = "南";
                break;
              case "4":
                $forward = "西南";
                break;
              case "5":
                $forward = "西";
                break;
              case "6":
                $forward = "西北";
                break;
              case "7":
                $forward = "北";
                break;
              case "8":
                $forward = "东北";
                break;
              case "9":
                $forward = "东西";
                break;
              case "10":
                $forward = "南北";
                break;
              default:
                $forward = "暂无资料";
                break;
            }
            //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
            switch ($v['serverco']) {
              case "1":
                $serverco = "毛坯";
                break;
              case "2":
                $serverco = "简装";
                break;
              case "3":
                $serverco = "中装";
                break;
              case "4":
                $serverco = "精装";
                break;
              case "5":
                $serverco = "豪装";
                break;
              case "6":
                $serverco = "婚装";
                break;
              default:
                $serverco = "简装";
                break;
            }
            if ($v['lowprice'] == '' || empty($v['lowprice'])) {
              $v['lowprice'] = '暂无资料';
            }
            if ($v['buildyear'] == '' || empty($v['buildyear'])) {
              $v['buildyear'] = '暂无资料';
            }
            if ($v['remark'] == '' || empty($v['remark'])) {
              $v['remark'] = '暂无资料';
            }
            $a['rent_type'] = $rent_type;
            $a['district'] = $v['district'];
            $a['block'] = $v['block'];
            $a['house_name'] = $v['house_name'];
            $a['buildarea'] = strip_end_0($v['buildarea']);
            $a['house_addr'] = trim($v['house_addr']);
            $a['house_type'] = $house_type;
            $a['price'] = strip_end_0($v['price']);
            $a['pricetype'] = $v['pricetype'];
            $a['avgprice'] = $v['avgprice'] ? "暂无资料" : strip_end_0($v['avgprice']);
            $a['forward'] = $forward;
            $a['room'] = $v['room'];
            $a['hall'] = $v['hall'];
            $a['toilet'] = $v['toilet'];
            $a['balcony'] = $v['balcony'];
            $a['floor'] = $v['floor'];
            $a['totalfloor'] = $v['totalfloor'];
            $a['serverco'] = $serverco;
            $a['pic'] = '';
            if ($v['picurl'] != '暂无资料') {
              $picarr = explode('*', $v['picurl']);
              if (is_full_array($picarr) && $picarr[0] != '暂无资料') {
                $temp = 0;
                foreach ($picarr as $pic) {
                  if ($pic != '') {
                    $a['pic'][$temp]['url'] = $pic;
                    $temp++;
                  }

                  if ($temp >= 10) {
                    break;
                  }
                }
              } else {
                $a['pic'] = '';
              }
            }
            if ($v['createtime'] > strtotime(date('Y-m-d'))) {
              //今天
              $a['createtime'] = '今天：' . date('H:i:s', $v['createtime']);
            } else {
              //24小时以前
              $a['createtime'] = date('Y-m-d', $v['createtime']);
            }
            $a['buildyear'] = $v['buildyear'];
            $a['owner'] = $v['owner'];
            $a['telno1'] = $v['telno1'];
            $a['remark'] = $v['remark'];
            //判断价格是否有变动
            $res = $this->collections_model_new->get_rent_house_collect_history($house_id);
            if (!empty($res)) {
              $type = array(
                '0' => '暂无',
                '1' => '毛坯',
                '2' => '简装',
                '3' => '中装',
                '4' => '精装',
                '5' => '豪装',
                '6' => '婚装'
              );
              foreach ($res as $key => $val) {
                $res[$key]['serverco'] = $type[$val['serverco']];
                $res[$key]['oldtime'] = date('Y-m-d H:i:s', $val['oldtime']);
                $res[$key]['price'] = strip_end_0($res[$key]['price']);
                unset($res[$key]['id']);
                unset($res[$key]['createtime']);
              }
              $a['price_change_history'] = $res;
            } else {
              $a['price_change_history'] = array();
            }
            //判断疑视经纪人
            $a['isagent'] = $v['isagent'];
            //检测房源是否下架
            $check_mess = $this->collections_model_new->check_rent_house($house_id);
            if ($check_mess && !empty($check_mess)) {
              $interval = time() - $check_mess['checktime'];
              $status = $interval > 3600 ? 1 : $check_mess['status'];//1 正常 2下架
              $choose_check = $interval > 3600 ? 1 : 2;//1 检查 2不检查
            } else {
              $choose_check = 1;//1 检查 2不检查
            }
            if ($choose_check == 1) {
              $data['id'] = $house_id;
              $data['sell_type'] = $v['sell_type'];//1=》住宅；2=》别墅；3=》商铺；4=》写字楼；
              $data['source_from'] = $v['source_from'];//source_from 0=》赶集；1=》58同城
              $data['oldurl'] = $v['oldurl'];
              $status = $this->collections_model_new->check_rent_ajax($data);
            }
            $a['status'] = $status;
            //组装条件查询经纪人已看房源
            $arr = array("broker_id" => $this->user_arr['broker_id'], 'tbl_name' => 'rent_house_collect', 'house_id' => $house_id);
            $judge = $this->collections_model_new->get_agent_house($arr);
            if (is_full_array($judge)) {
              $a['is_input'] = $judge[0]['is_input'];
            } else {
              $a['is_input'] = -1;
            }
            //组装查询条件
            $where_cond = array(
              'tbl_name' => 'rent_house_collect',
              'house_id' => $house_id
            );
            //该房源被查看的次数
            $read_times = array();
            $read_times = $this->collections_model_new->get_readtimes_byid($where_cond);
            $a['read_times'] = count($read_times);
            //获取经纪人基本信息
            $broker_data = $this->user_arr;
            $company_id = $broker_data['company_id'];
            $num = $this->collections_model_new->collect_rent_publish_check($house_id, $company_id);
            $a['company_add_house'] = $num > 0 ? 1 : 0;
            $info = $a;
          }
          //加入点击日志
          $this->view_log_model->add_collect_click_log($this->user_arr['broker_id'], $house_id, 2);
          $this->result(1, '查询成功', $info);
        } else {
          $this->result(1, '未查询到数据！');
        }
      } else {
        $this->result(0, '参数不合法，查询失败！');
      }
    } else {
      $this->result(0, '城市参数错误', $info);
    }
  }

  /**
   * 好房看看---出售房源
   * @access public
   * @return void
   * date 2015-01-09
   * author angel_in_us
   */
  public function good_sell_phone()
  {
    $this->load->model('collections_model_new');//采集模型类
    if ($this->user_arr['group_id'] != 2) {
      $arr = array("broker_id" => $this->user_arr['broker_id'], 'tbl_name' => 'sell_house_collect');
      $judge = $this->collections_model_new->get_agent_house($arr);
      if (count($judge) >= 5) {
        $this->result(0, '未认证用户最多可查看个人出售房源5条，现已达到上限');
        return false;
      }
    }
    $house_id = $this->input->get('house_id');
    $broker_id = $this->input->get('broker_id');
    $tbl_name = $this->input->get('tbl_name');
    //当经纪人点击解密电话时，往 agent_house_judge 数据表里插入数据，即设置为已读！
    if (!empty($house_id) && !empty($broker_id) && !empty($tbl_name)) {
      if ($tbl_name == 1) {
        $tbl_name = 'sell_house_collect';
      } else if ($tbl_name == 2) {
        $tbl_name = 'rent_house_collect';
      }
      //判断经纪人是否查看过该房源信息，是 则不做操作，否则向agent_house_judge表里插入经纪人查看信息
      $where = array(
        'house_id' => $house_id,
        'broker_id' => $broker_id,
        'tbl_name' => $tbl_name
      );
      $result = $this->collections_model_new->check_agent_house($where);
      $ahouse_info = array(
        'house_id' => $house_id,
        'broker_id' => $broker_id,
        'tbl_name' => $tbl_name,
        'is_input' => 0,
        'createtime' => time()
      );
      if (!empty($result)) {

      } else {
        $this->collections_model_new->add_agent_house($ahouse_info);
      }
    }
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    //根据房源house_id去查询房源详情
    if (!empty($house_id)) {
      $data['where_cond'] = array('id' => $house_id);
      $data['house_info'] = $this->collections_model_new->get_housesell_byid($data['where_cond']);
      //获取房源详情
      if (!empty($data['house_info'])) {
        foreach ($data['house_info'] as $key => $v) {
          //数据重构
          $info = array();
          $a['owner'] = $v['owner'];
          $a['telno1'] = $v['telno1'];
          $info = $a;
        }
        $this->result(1, '查询成功', $info);
      } else {
        $this->result(1, '未查询到数据！');
      }
    } else {
      $this->result(0, '参数不合法，查询失败！');
    }
  }

  /**
   * 出售解密号码---用于合作app
   * @access public
   * @return void
   * date 2015-01-09
   * author yuan
   */
  public function good_sell_phone_cooperate()
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_spell = $this->input->get('city_spell');
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_new');//采集模型类
    }

    $house_id = $this->input->get('house_id');
    $broker_id = $this->input->get('broker_id');
    $tbl_name = $this->input->get('tbl_name');

    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    //根据房源house_id去查询房源详情
    if (!empty($house_id)) {
      $data['where_cond'] = array('id' => $house_id);
      $data['house_info'] = $this->collections_model_new->get_housesell_byid($data['where_cond']);
      //获取房源详情
      if (!empty($data['house_info'])) {
        foreach ($data['house_info'] as $key => $v) {
          //数据重构
          $info = array();
          $a['owner'] = $v['owner'];
          $a['telno1'] = $v['telno1'];
          $info = $a;
        }
        $this->result(1, '查询成功', $info);
      } else {
        $this->result(1, '未查询到数据！');
      }
    } else {
      $this->result(0, '参数不合法，查询失败！');
    }
  }

  /**
   * 出租解密号码---用于合作app
   * @access public
   * @return void
   * date 2015-01-09
   * author yuan
   */
  public function good_rent_phone_cooperate()
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_spell = $this->input->get('city_spell');
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_new');//采集模型类
    }

    $house_id = $this->input->get('house_id');
    $broker_id = $this->input->get('broker_id');
    $tbl_name = $this->input->get('tbl_name');

    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    //根据房源house_id去查询房源详情
    if (!empty($house_id)) {
      $data['where_cond'] = array('id' => $house_id);
      $data['house_info'] = $this->collections_model_new->get_houserent_byid($data['where_cond']);
      //获取房源详情
      if (!empty($data['house_info'])) {
        foreach ($data['house_info'] as $key => $v) {
          //数据重构
          $info = array();
          $a['owner'] = $v['owner'];
          $a['telno1'] = $v['telno1'];
          $info = $a;
        }
        $this->result(1, '查询成功', $info);
      } else {
        $this->result(1, '未查询到数据！');
      }
    } else {
      $this->result(0, '参数不合法，查询失败！');
    }
  }

  /**
   * 好房看看---出租房源
   * @access public
   * @return void
   * date 2015-01-09
   * author angel_in_us
   */
  public function good_rent_details()
  {
    $this->load->model('collections_model_new');//采集模型类
    $this->load->model('view_log_model');//添加日志
    $house_id = $this->input->get('house_id');
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    if (!empty($house_id)) {
      $data['where_cond'] = array('id' => $house_id);
      $data['house_info'] = $this->collections_model_new->get_houserent_byid($data['where_cond']);//获取获取房源详情
      if (!empty($data['house_info'])) {
        foreach ($data['house_info'] as $key => $v) {
          //数据重构
          $info = array();
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
            default:
              $rent_type = "其它";
              break;
          }
          //住宅类型 1:多层 2:高层 3:小高层 4:复式 5:顶+阁 6:底+院 7:私房 8:其它
          switch ($v['house_type']) {
            case "1":
              $house_type = "多层";
              break;
            case "2":
              $house_type = "高层";
              break;
            case "3":
              $house_type = "小高层";
              break;
            case "4":
              $house_type = "复式";
              break;
            case "5":
              $house_type = "顶+阁";
              break;
            case "6":
              $house_type = "底+院";
              break;
            case "7":
              $house_type = "私房";
              break;
            case "8":
              $house_type = "其它";
              break;
            default:
              $house_type = "其它";
              break;
          }
          //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
          switch ($v['forward']) {
            case "1":
              $forward = "东";
              break;
            case "2":
              $forward = "东南";
              break;
            case "3":
              $forward = "南";
              break;
            case "4":
              $forward = "西南";
              break;
            case "5":
              $forward = "西";
              break;
            case "6":
              $forward = "西北";
              break;
            case "7":
              $forward = "北";
              break;
            case "8":
              $forward = "东北";
              break;
            case "9":
              $forward = "东西";
              break;
            case "10":
              $forward = "南北";
              break;
            default:
              $forward = "暂无资料";
              break;
          }
          //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
          switch ($v['serverco']) {
            case "1":
              $serverco = "毛坯";
              break;
            case "2":
              $serverco = "简装";
              break;
            case "3":
              $serverco = "中装";
              break;
            case "4":
              $serverco = "精装";
              break;
            case "5":
              $serverco = "豪装";
              break;
            case "6":
              $serverco = "婚装";
              break;
            default:
              $serverco = "简装";
              break;
          }
          if ($v['lowprice'] == '' || empty($v['lowprice'])) {
            $v['lowprice'] = '暂无资料';
          }
          if ($v['buildyear'] == '' || empty($v['buildyear'])) {
            $v['buildyear'] = '暂无资料';
          }
          if ($v['remark'] == '' || empty($v['remark'])) {
            $v['remark'] = '暂无资料';
          }
          $a['rent_type'] = $rent_type;
          $a['district'] = $v['district'];
          $a['block'] = $v['block'];
          $a['house_name'] = $v['house_name'];
          $a['buildarea'] = strip_end_0($v['buildarea']);
          $a['house_addr'] = trim($v['house_addr']);
          $a['house_type'] = $house_type;
          $a['price'] = strip_end_0($v['price']);
          $a['pricetype'] = $v['pricetype'];
          $a['avgprice'] = $v['avgprice'] ? "暂无资料" : strip_end_0($v['avgprice']);
          $a['forward'] = $forward;
          $a['room'] = $v['room'];
          $a['hall'] = $v['hall'];
          $a['toilet'] = $v['toilet'];
          $a['balcony'] = $v['balcony'];
          $a['floor'] = $v['floor'];
          $a['totalfloor'] = $v['totalfloor'];
          $a['serverco'] = $serverco;
          $a['pic'] = '';
          if ($v['picurl'] != '暂无资料') {
            $picarr = explode('*', $v['picurl']);
            if (is_full_array($picarr) && $picarr[0] != '暂无资料') {
              $temp = 0;
              foreach ($picarr as $pic) {
                if ($pic != '') {
                  $a['pic'][$temp]['url'] = $pic;
                  $temp++;
                }

                if ($temp >= 10) {
                  break;
                }
              }
            } else {
              $a['pic'] = '';
            }
          }
          if ($v['createtime'] > strtotime(date('Y-m-d'))) {
            //今天
            $a['createtime'] = '今天：' . date('H:i:s', $v['createtime']);
          } else {
            //24小时以前
            $a['createtime'] = date('Y-m-d', $v['createtime']);
          }
          $a['buildyear'] = $v['buildyear'];
          $a['owner'] = $v['owner'];
          $a['telno1'] = $v['telno1'];
          $a['remark'] = $v['remark'];
          //判断价格是否有变动
          $res = $this->collections_model_new->get_rent_house_collect_history($house_id);
          if (!empty($res)) {
            $type = array(
              '0' => '暂无',
              '1' => '毛坯',
              '2' => '简装',
              '3' => '中装',
              '4' => '精装',
              '5' => '豪装',
              '6' => '婚装'
            );
            foreach ($res as $key => $val) {
              $res[$key]['serverco'] = $type[$val['serverco']];
              $res[$key]['oldtime'] = date('Y-m-d H:i:s', $val['oldtime']);
              $res[$key]['price'] = strip_end_0($res[$key]['price']);
              unset($res[$key]['id']);
              unset($res[$key]['createtime']);
            }
            $a['price_change_history'] = $res;
          } else {
            $a['price_change_history'] = array();
          }
          //判断疑视经纪人
          $a['isagent'] = $v['isagent'];
          //检测房源是否下架
          $check_mess = $this->collections_model_new->check_rent_house($house_id);
          if ($check_mess && !empty($check_mess)) {
            $interval = time() - $check_mess['checktime'];
            $status = $interval > 3600 ? 1 : $check_mess['status'];//1 正常 2下架
            $choose_check = $interval > 3600 ? 1 : 2;//1 检查 2不检查
          } else {
            $choose_check = 1;//1 检查 2不检查
          }
          if ($choose_check == 1) {
            $data['id'] = $house_id;
            $data['sell_type'] = $v['sell_type'];//1=》住宅；2=》别墅；3=》商铺；4=》写字楼；
            $data['source_from'] = $v['source_from'];//source_from 0=》赶集；1=》58同城
            $data['oldurl'] = $v['oldurl'];
            $status = $this->collections_model_new->check_rent_ajax($data);
          }
          $a['status'] = $status;
          //组装条件查询经纪人已看房源
          $arr = array("broker_id" => $this->user_arr['broker_id'], 'tbl_name' => 'rent_house_collect', 'house_id' => $house_id);
          $judge = $this->collections_model_new->get_agent_house($arr);
          if (is_full_array($judge)) {
            $a['is_input'] = $judge[0]['is_input'];
          } else {
            $a['is_input'] = -1;
          }
          //组装查询条件
          $where_cond = array(
            'tbl_name' => 'rent_house_collect',
            'house_id' => $house_id
          );
          //该房源被查看的次数
          $read_times = array();
          $read_times = $this->collections_model_new->get_readtimes_byid($where_cond);
          $a['read_times'] = count($read_times);
          //获取经纪人基本信息
          $broker_data = $this->user_arr;
          $company_id = $broker_data['company_id'];
          $num = $this->collections_model_new->collect_rent_publish_check($house_id, $company_id);
          $a['company_add_house'] = $num > 0 ? 1 : 0;
          $info = $a;
        }
        //加入点击日志
        $this->view_log_model->add_collect_click_log($this->user_arr['broker_id'], $house_id, 2);
        $this->result(1, '查询成功', $info);
      } else {
        $this->result(1, '未查询到数据！');
      }
    } else {
      $this->result(0, '参数不合法，查询失败！');
    }
  }

  /**
   * 好房看看---出售房源
   * @access public
   * @return void
   * date 2015-01-09
   * author angel_in_us
   */
  public function good_rent_phone()
  {
    $this->load->model('collections_model_new');//采集模型类
    if ($this->user_arr['group_id'] != 2) {
      $arr = array("broker_id" => $this->user_arr['broker_id'], 'tbl_name' => 'rent_house_collect');
      $judge = $this->collections_model_new->get_agent_house($arr);
      if (count($judge) >= 5) {
        $this->result(0, '未认证用户最多可查看个人出租房源5条，现已达到上限');
        return false;
      }
    }
    $house_id = $this->input->get('house_id');
    $broker_id = $this->input->get('broker_id');
    $tbl_name = $this->input->get('tbl_name');

    //当经纪人点击解密电话时，往 agent_house_judge 数据表里插入数据，即设置为已读！
    if (!empty($house_id) && !empty($broker_id) && !empty($tbl_name)) {
      if ($tbl_name == 1) {
        $tbl_name == 'sell_house_collect';
      } else if ($tbl_name = 2) {
        $tbl_name = 'rent_house_collect';
      }
      $where = array(
        'house_id' => $house_id,
        'broker_id' => $broker_id,
        'tbl_name' => $tbl_name
      );
      $result = $this->collections_model_new->check_agent_house($where);
      $ahouse_info = array(
        'house_id' => $house_id,
        'broker_id' => $broker_id,
        'tbl_name' => $tbl_name,
        'is_input' => 0,
        'createtime' => time()
      );
      if (!empty($result)) {

      } else {
        $this->collections_model_new->add_agent_house($ahouse_info);
      }
    }
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    //根据房源house_id去查询房源详情
    if (!empty($house_id)) {
      $data['where_cond'] = array('id' => $house_id);
      $data['house_info'] = $this->collections_model_new->get_houserent_byid($data['where_cond']);
      //获取房源详情
      if (!empty($data['house_info'])) {
        foreach ($data['house_info'] as $key => $v) {
          //数据重构
          $info = array();
          $a['owner'] = $v['owner'];
          $a['telno1'] = $v['telno1'];
          $info = $a;
        }
        $this->result(1, '查询成功', $info);
      } else {
        $this->result(1, '未查询到数据！');
      }
    } else {
      $this->result(0, '参数不合法，查询失败！');
    }
  }

  //举报
  public function report_agent()
  {
    $this->load->model('collections_model_new');//采集模型类
    $report_result = array();
    $r_addtime = time();
    $r_reason = '中介冒充个人';
    $r_person = $this->user_arr['phone'];
    $broker_id = $this->user_arr['broker_id'];
    $r_tel = $this->input->get('r_tel');
    $where_cond = array();
    if (!empty($r_tel)) {
      $where_cond = array('r_tel' => $r_tel);
    } else {
      $this->result(0, '参数不合法');
      die();
    }
    $result = $this->collections_model_new->check_reprot_tel($where_cond);
    if (is_array($result) && !empty($result)) {
      $this->result(0, '该房源已经被举报，请勿重复举报！');
    } else {
      $info = array(
        'r_tel' => $r_tel,
        'r_reason' => $r_reason,
        'r_status' => 3,
        'r_addtime' => $r_addtime,
        'r_person' => $r_person,
        'broker_id' => $broker_id,
      );
      $rel = $this->collections_model_new->agent_reportlist($info);
      if ($rel == "") {
        $this->result(0, '该房源已经被举报，请勿重复举报！');
      } else {
        $this->result(1, '举报成功');
      }
    }

  }

  /**
   * 采集设置的相关数据设置
   * 2016.5.26
   * cc
   */
  public function save_collect_set()
  {
    $this->load->model('collections_model_new');//采集模型类
    $result = array();
    $post_param['district'] = $this->input->post('district');
    $post_param['district'] = str_replace("区", "", $post_param['district']);
    $post_param['district'] = str_replace("县", "", $post_param['district']);
    $post_param['dist_id'] = $this->input->post('dist_id');
    $post_param['broker_id'] = $this->user_arr['broker_id'];
    $post_param['street'] = $this->input->post('streetname');
    $post_param['street_id'] = $this->input->post('street_id');
    $post_param['block_name'] = $this->input->post('blockname');
    $post_param['price'] = $this->input->post('price');
    $post_param['room'] = $this->input->post('room');
    $post_param['type'] = $this->input->post('type');
    $res = $this->collections_model_new->get_collect_set($post_param);
    if (empty($res)) {
      $num = $this->collections_model_new->get_collect_set_num($post_param['broker_id'], $post_param['type']);
      if ($num < 5) {
        $post_param['createtime'] = time();
        $res = $this->collections_model_new->save_collect_set($post_param);
        if ($res) {
          $result['status'] = 1; //成功
          $this->result(1, '操作成功');
          return false;
        } else {
          $result['status'] = 4; //执行失败
        }
      } else {
        $result['status'] = 3; //超过5条上限
      }
    } else {
      $result['status'] = 2; //重复添加
    }
    $this->result(0, '操作失败', $result);
  }

  /**
   * 删除采集设置条件
   * 2016.5.27
   * cc
   */
  public function delete_collect_set()
  {
    $this->load->model('collections_model_new');//采集模型类
    $id = $this->input->get('set_id');
    $res = $this->collections_model_new->delete_collect_set_byid($id);
    if ($res) {
      $this->result(1, '操作成功');
    } else {
      $this->result(0, '操作失败');
    }
  }

  /**
   * 查询采集
   * 2016.5.27
   * cc
   */
  public function get_collect_set()
  {
    $this->load->model('collections_model_new');//采集模型类
    $type = $this->input->get('type');
    $my_search = $this->collections_model_new->get_collect_set_info($this->user_arr['broker_id'], $type);
    //print_r($my_search);
    $new_search = array('list' => array());
    if ($my_search) {
      $config_house = $this->house_config_model->get_config();
      //print_r($config_house);
      $arr_price = $type == 'sell' ? $config_house['sell_price'] : $config_house['rent_price'];
      foreach ($my_search as $key => $value) {
        //获取当前条件下的房源数量
        $where_set = array();
        $valid_time = time() - 86400;//一天之内
        $where_set['where_cond']['createtime >='] = $valid_time;
        $where_set['where_cond']['sell_type'] = 1;
        $info = '';
        if ($value['district']) {
          $info .= $value['district'] . '/';
          $where_set['like'] = array('district' => $value['district']);
        }
        if ($value['street']) {
          $info .= $value['street'] . '/';
          $where_set['like'] = array('block' => $value['street']);
        }
        if ($value['block_name']) {
          $info .= $value['block_name'] . '/';
          $where_set['or_like']['like_key'] = array('house_name');
          $where_set['or_like']['like_value'] = $value['block_name'];
        }
        if ($value['price']) {
          $info .= $config_house['sell_price'][$value['price']] . '/';
          $price = $config_house['sell_price'][$value['price']];
          $price_arr = explode('-', $price);
          if (count($price_arr) == 2) {
            $price1 = intval($price_arr[0]);
            $price2 = intval($price_arr[1]);
            $where_set['where_cond'] = $where_set['where_cond'] + array('price >=' => $price1);
            $where_set['where_cond'] = $where_set['where_cond'] + array('price <=' => $price2);
          } else {
            if ($search_rent[0]['price'] == 1) {
              $price2 = intval($price_arr[0]);
              $where_set['where_cond'] = $where_set['where_cond'] + array('price <=' => $price2);
            } else {
              $price1 = intval($price_arr[0]);
              $where_set['where_cond'] = $where_set['where_cond'] + array('price >=' => $price1);
            }
          }
        }
        if ($value['room']) {
          $info .= $config_house['room'][$value['room']];
          $where_set['where_cond']['room'] = $value['room'];
        }
        $new_search['list'][$key]['site_id'] = $value['id'];
        $new_search['list'][$key]['district'] = empty($value['district']) ? '' : $value['district'];
        $new_search['list'][$key]['dist_id'] = empty($value['dist_id']) ? '' : $value['dist_id'];
        $new_search['list'][$key]['streetname'] = empty($value['street']) ? '' : $value['street'];
        $new_search['list'][$key]['street_id'] = empty($value['street_id']) ? '' : $value['street_id'];
        $new_search['list'][$key]['blockname'] = empty($value['block_name']) ? '' : $value['block_name'];
        $new_search['list'][$key]['price'] = $value['price'];
        $new_search['list'][$key]['price_str'] = $arr_price[$value['price']];
        $new_search['list'][$key]['room'] = $value['room'];
        $new_search['list'][$key]['room_str'] = empty($config_house['room'][$value['room']])
          ? '' : $config_house['room'][$value['room']];
        $new_search['list'][$key]['house_num'] = $this->collections_model_new->get_sell_num($where_set['where_cond'], $where_set['like'], $where_set['or_like']);
      }
    }
    $this->result(1, '查询成功', $new_search);
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

  private function _get_cond_str($form_param)
  {
    $this->load->model('district_model');//区属模型类
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
      if ($this->user_arr['city_id'] == 3) {
        $district = str_replace('县', '', str_replace('区', '', $district));
      }
      $district = array('district' => $district);
      print_r($district);
      $cond_where = $cond_where + $district;
    }
    //采集区属
    if (isset($form_param['district_cj']) && !empty($form_param['district_cj']) && $form_param['district_cj'] > 0) {
      $district = intval($form_param['district_cj']);
      $district = $this->district_model->get_district_byid($district);
      if ($this->user_arr['city_id'] == 3) {
        $district = str_replace('县', '', str_replace('区', '', $district));
      }
      $district = array('district' => $district);
      $cond_where = $cond_where + $district;
    }

    //采集板块
    if (isset($form_param['street_cj']) && !empty($form_param['street_cj']) && $form_param['street_cj'] > 0) {
      $street = intval($form_param['street_cj']);
      $street = $this->district_model->get_street_byid($street);
      $street = array('street' => $street);
      $cond_where = $cond_where + $street;
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


    //采集朝向
    if (isset($form_param['forward']) && !empty($form_param['forward'])) {
      $forward = intval($form_param['forward']);
      $cond_where = $cond_where + array('forward' => $forward);
    }

    //采集装修
    if (isset($form_param['fitment']) && !empty($form_param['fitment'])) {
      $fitment = intval($form_param['fitment']);
      $cond_where = $cond_where + array('serverco' => $fitment);
    }

    //采集来源
    if (isset($form_param['source_from']) && !empty($form_param['source_from'])) {
      $source_from = intval($form_param['source_from']);
      $cond_where = $cond_where + array('source_from' => intval($source_from) - 1);
    }

    //采集时间
    if (isset($form_param['valid_time']) && !empty($form_param['valid_time'])) {
      $valid_time = intval($form_param['valid_time']);
      if ($valid_time != "" && $valid_time == 4) {//三个月之外
        $valid_time = time() - 7776000;
        $cond_where = $cond_where + array('createtime <=' => $valid_time);
      } elseif ($valid_time != "" && $valid_time == 3) {//三个月之内
        $valid_time = time() - 7776000;
        $cond_where = $cond_where + array('createtime >=' => $valid_time);
      } elseif ($valid_time != "" && $valid_time == 2) {//一个月内
        $valid_time = time() - 2592000;
        $cond_where = $cond_where + array('createtime >=' => $valid_time);
      } else {//一周内
        $valid_time = time() - 604800;
        $cond_where = $cond_where + array('createtime >=' => $valid_time);
      }
    } else {
      $valid_time = time() - 7776000;
      $cond_where = $cond_where + array('createtime >=' => $valid_time);
    }
    return $cond_where;
  }

  private function _get_cond_str_rent($form_param)
  {
    $this->load->model('district_model');//区属模型类
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
      $district = str_replace('县', '', str_replace('区', '', $district));
      $district = array('district' => $district);
      $cond_where = $cond_where + $district;
    }
    //采集区属
    if (isset($form_param['district_cj']) && !empty($form_param['district_cj']) && $form_param['district_cj'] > 0) {
      $district = intval($form_param['district_cj']);
      $district = $this->district_model->get_district_byid($district);
      $district = str_replace('县', '', str_replace('区', '', $district));
      $district = array('district' => $district);
      $cond_where = $cond_where + $district;
    }
    //采集板块
    if (isset($form_param['street_cj']) && !empty($form_param['street_cj']) && $form_param['street_cj'] > 0) {
      $street = intval($form_param['street_cj']);
      $street = $this->district_model->get_street_byid($street);
      $street = array('street' => $street);
      $cond_where = $cond_where + $street;
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

    //采集朝向
    if (isset($form_param['forward']) && !empty($form_param['forward'])) {
      $forward = intval($form_param['forward']);
      $cond_where = $cond_where + array('forward' => $forward);
    }

    //采集装修
    if (isset($form_param['fitment']) && !empty($form_param['fitment'])) {
      $fitment = intval($form_param['fitment']);
      $cond_where = $cond_where + array('serverco' => $fitment);
    }

    //采集来源
    if (isset($form_param['source_from']) && !empty($form_param['source_from'])) {
      $source_from = intval($form_param['source_from']);
      $cond_where = $cond_where + array('source_from' => intval($source_from) - 1);
    }

    //采集时间
    if (isset($form_param['valid_time']) && !empty($form_param['valid_time'])) {
      $valid_time = intval($form_param['valid_time']);
      if ($valid_time != "" && $valid_time == 4) {//三个月之外
        $valid_time = time() - 7776000;
        $cond_where = $cond_where + array('createtime <=' => $valid_time);
      } elseif ($valid_time != "" && $valid_time == 3) {//三个月之内
        $valid_time = time() - 7776000;
        $cond_where = $cond_where + array('createtime >=' => $valid_time);
      } elseif ($valid_time != "" && $valid_time == 2) {//一个月内
        $valid_time = time() - 2592000;
        $cond_where = $cond_where + array('createtime >=' => $valid_time);
      } else {//一周内
        $valid_time = time() - 604800;
        $cond_where = $cond_where + array('createtime >=' => $valid_time);
      }
    } else {
      $valid_time = time() - 7776000;
      $cond_where = $cond_where + array('createtime >=' => $valid_time);
    }
    return $cond_where;

  }
}

/* End of file house_collections.php */
/* Location: ./application/mls/controllers/house_collections.php */
