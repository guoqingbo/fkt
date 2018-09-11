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
class House_collections_cooperate extends MY_Controller
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
    $this->load->library('form_validation');//表单验证
    $this->load->library('Curl');
    $this->load->model('broker_model');  //经纪人模型类
    $this->load->model('city_model');  //经纪人模型类
    $this->load->model('house_config_model');  //房源配置模型类
    error_reporting(E_ALL || ~E_NOTICE);
  }

  /**
   * 首页采集数据
   * @access public
   * @return void
   * date 2016-8-7
   * author yuan
   */
  public function index_collect($page = 1)
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $this->load->model('district_model');//区属模型类

      $data['conf_where'] = 'index';
      $data['where_cond'] = array();

      //get参数  ===>>>实际为post参数
      $post_param = array('sell_type' => 1);
      $data['where_cond'] = $this->_get_cond_str($post_param, $city_id);
      $data['order_by'] = 'createtime';
      $sell_house = $this->collections_model_cooperate->get_house_sell_index($data['where_cond'], $data['order_by'], 7, 0);

      $post_param = array('rent_type' => 1);
      $data['where_cond'] = $this->_get_cond_str_rent($post_param, $city_id);
      $rent_house = $this->collections_model_cooperate->get_house_rent_index($data['where_cond'], $data['order_by'], 3, 0);

      //获得今日更新总数
      $y = date("Y");
      $m = date("m");
      $d = date("d");
      $todayTime = mktime(0, 0, 0, $m, $d, $y);
      $house_num_where = array(
        'createtime >=' => $todayTime
      );
      $sell_num = $this->collections_model_cooperate->get_sell_num($house_num_where, array(), array());
      $rent_num = $this->collections_model_cooperate->get_rent_num($house_num_where, array(), array());
      $all_num = intval($sell_num) + intval($rent_num);

      $info = array('today_house_num' => $all_num, 'data' => array());

      //获取列表内容
      if (!empty($sell_house)) {
        //数据重构
        foreach ($sell_house as $k => $v) {
          //是否为刷新房源
          $a['rowid'] = $v['id'];
          $a['cooperate_type'] = '出售';
          if (empty($v['house_name'])) {
            $a['block_name'] = '暂无资料';
          } else {
            $a['block_name'] = $v['house_name'];
          }
          $a['room'] = $v['room'] . '室' . $v['hall'] . '厅' . $v['toilet'] . '卫';
          $a['area'] = intval($v['buildarea']) . '平方米';
          $a['price'] = intval($v['price']);
          $a['unit'] = '万元';
          //一天前
          $time_str = '';
          if (time() - $v['createtime'] > 86400) {
            $time_str = '1天前';
          } else {
            //一个小时前
            if (time() - $v['createtime'] > 3600) {
              $hour = (time() - $v['createtime']) / 3600;
              $time_str = floor($hour) . '小时前';
            } else {
              $minute = (time() - $v['createtime']) / 60;
              if ($minute < 1) {
                $time_str = '刚刚';
              } else {
                $time_str = floor($minute) . '分钟前';
              }
            }
          }
          $a['time'] = $time_str;
          $info['data'][] = $a;
        }
        foreach ($rent_house as $k => $v) {
          //是否为刷新房源
          $a['rowid'] = $v['id'];
          $a['cooperate_type'] = '出租';
          if (empty($v['house_name'])) {
            $a['block_name'] = '暂无资料';
          } else {
            $a['block_name'] = $v['house_name'];
          }
          $a['room'] = $v['room'] . '室' . $v['hall'] . '厅' . $v['toilet'] . '卫';
          $a['area'] = intval($v['buildarea']) . '平方米';
          $a['price'] = intval($v['price']);
          $a['unit'] = '元/月';
          //一天前
          $time_str = '';
          if (time() - $v['createtime'] > 86400) {
            $time_str = '1天前';
          } else {
            //一个小时前
            if (time() - $v['createtime'] > 3600) {
              $hour = (time() - $v['createtime']) / 3600;
              $time_str = floor($hour) . '小时前';
            } else {
              $minute = (time() - $v['createtime']) / 60;
              if ($minute < 1) {
                $time_str = '刚刚';
              } else {
                $time_str = floor($minute) . '分钟前';
              }
            }
          }
          $a['time'] = $time_str;
          $info['data'][] = $a;
        }

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
   * date 2016-8-7
   * author yuan
   */
  public function collect_sell($page = 1)
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $this->load->model('district_model');//区属模型类

      $broker_id = $this->input->get('broker_id');
      $data['conf_where'] = 'index';
      $data['where_cond'] = array();
      $data['where_in'] = array();
      $data['like'] = $data['or_like'] = array();

      $arr = array("broker_id" => $broker_id, 'tbl_name' => 'sell_house_collect');
      $judge = $this->collections_model_cooperate->get_agent_house($arr);

      $house_ids = array();
      foreach ($judge as $k => $v) {
        $house_ids[] = $v['house_id'];
      }
      $data['judge'] = $judge;
      $data['broker_info'] = $broker_id;
      $data['valid_time'] = 3;

      //get参数  ===>>>实际为post参数
      $post_param = $this->input->get(NULL, TRUE);
      $post_param['sell_type'] = $post_param['type'];
      $post_param['sell_area'] = $post_param['area'];
      $post_param['sell_price'] = $post_param['price'];
      unset($post_param['type']);
      unset($post_param['area']);
      unset($post_param['price']);
      $data['where_cond'] = $this->_get_cond_str($post_param, $city_id);

      if ($data['where_cond']['district'] != " " && !empty($data['where_cond']['district'])) {
        $data['like'] = array('district' => $data['where_cond']['district']);
        unset($data['where_cond']['district']);
      }
      if ($data['where_cond']['street'] != " " && !empty($data['where_cond']['street'])) {
        $data['like'] = $data['like'] + array('block' => $data['where_cond']['street']);
        unset($data['where_cond']['street']);
      }
      if ($data['where_cond']['house_name'] != "" && !empty($data['where_cond']['house_name'])) {
        $data['or_like']['like_key'] = array('house_name');
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
      //sphinx搜素
      $result = $this->collections_model_cooperate->sphinx($data['where_cond'], $data['where_in'], $data['like'], $data['or_like'], $data['valid_time'], $city_spell, 'sell', $page, $this->_limit, $this->_offset);
      $data['blacklist'] = $result['blacklist'];
      //符合条件的总行数
      $this->_total_count = $count = $result['total'];
//            $this->_total_count = $count = 1;//$this->collections_model_cooperate->get_sell_num($data['where_cond'],$data['like'],$data['or_like']);
      //计算总页数
      $pages = $this->_total_count > 0 ? (ceil($this->_total_count / $this->_limit) > $page_max ? $page_max : ceil($this->_total_count / $this->_limit)) : 0;
//            $pages  = $this->_total_count > 0 ? ceil( $this->_total_count / $this->_limit ) : 0;
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
//            $data['order_by'] = 'createtime';
//            $data['blacklist'] = $this->collections_model_cooperate->get_house_sell($data['where_cond'],$data['where_in']=array(),$data['like'],$data['or_like'],$data['order_by'],$this->_limit,$this->_offset);
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
      $info = array('count' => 0, 'read_count' => 0, 'collect_set' => array(), 'data' => array());
      //根据经纪人id查询唯一订阅条件
      $where_cond = array(
        'broker_id' => intval($broker_id),
        'type' => 'sell'
      );
      $collect_set_cooperate = $this->collections_model_cooperate->get_collect_set($where_cond);
      if (is_full_array($collect_set_cooperate[0])) {
        $info['collect_set'] = $collect_set_cooperate[0];
        $set_price = $collect_set_cooperate[0]['price'];
        $set_room = $collect_set_cooperate[0]['room'];
        $config_house = $this->house_config_model->get_config();
        if ($set_price > 0) {
          $info['collect_set']['price_name'] = $config_house['sell_price'][$set_price];
        }
        if ($set_room > 0) {
          $info['collect_set']['room_name'] = $config_house['room'][$set_room];
        }
      } else {
        $info['collect_set'] = array('id' => 0);
      }

      //get参数  ===>>>实际为post参数
      $post_param = $this->input->get(NULL, TRUE);

      //获取列表内容
      if (!empty($data['blacklist'])) {
        //数据重构
        $info['count'] = $count;
        $info['read_count'] = $read_count;
        foreach ($data['blacklist'] as $k => $v) {
          $id = $v['id'];
          $res = $this->collections_model_cooperate->get_sell_house_collect_history($id);
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
          if ((!empty($v['picurl']) && $v['picurl'] != '暂无资料') || (!empty($v['web_picurl']) && $v['web_picurl'] != '暂无资料')) {
            $a['pic'] = 1;
          } else {
            $a['pic'] = 0;
          }
          $a['owner'] = $v['owner'] ? $v['owner'] : '暂无资料';
          $a['buildarea'] = intval($v['buildarea']);
          $a['price'] = intval($v['price']);
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
   * 采集房源 出售 订阅查询 用于合作app
   * @access public
   * @return void
   * date 2016-8-7
   * author yuan
   */
  public function collect_sell_set($page = 1)
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $this->load->model('district_model');//区属模型类

      $broker_id = $this->input->get('broker_id');
      $data['conf_where'] = 'index';
      $data['where_cond'] = array();
      $data['like'] = $data['or_like'] = array();

      $arr = array("broker_id" => $broker_id, 'tbl_name' => 'sell_house_collect');
      $judge = $this->collections_model_cooperate->get_agent_house($arr);

      $house_ids = array();
      foreach ($judge as $k => $v) {
        $house_ids[] = $v['house_id'];
      }
      $data['judge'] = $judge;
      $data['broker_info'] = $broker_id;

      //get参数  ===>>>实际为post参数
      $post_param = $this->input->get(NULL, TRUE);
      $post_param['sell_type'] = 1;
      //根据经纪人id查询唯一订阅条件
      $where_cond = array(
        'broker_id' => intval($broker_id),
        'type' => 'sell'
      );
      $collect_set_cooperate = $this->collections_model_cooperate->get_collect_set($where_cond);
      if (is_full_array($collect_set_cooperate)) {
        $post_param['district_cj'] = $collect_set_cooperate[0]['dist_id'];
        $post_param['district_2_cj'] = $collect_set_cooperate[0]['dist_id_2'];
        $post_param['district_3_cj'] = $collect_set_cooperate[0]['dist_id_3'];
        $post_param['street_cj'] = $collect_set_cooperate[0]['street_id'];
        $post_param['street_2_cj'] = $collect_set_cooperate[0]['street_id_2'];
        $post_param['street_3_cj'] = $collect_set_cooperate[0]['street_id_3'];
//                $post_param['house_name'] = $collect_set_cooperate[0]['block_name'];
//                $post_param['house_name_2'] = $collect_set_cooperate[0]['block_name_2'];
//                $post_param['house_name_3'] = $collect_set_cooperate[0]['block_name_3'];
        $post_param['sell_price'] = $collect_set_cooperate[0]['price'];
        $post_param['room'] = $collect_set_cooperate[0]['room'];
        $data['where_cond'] = $this->_get_cond_str($post_param, $city_id);

        //区属、板块1
        $district_street_1 = array();
        if ($data['where_cond']['district'] != " " && !empty($data['where_cond']['district'])) {
          $district_street_1['district'] = $data['where_cond']['district'];
          unset($data['where_cond']['district']);
        }
        if ($data['where_cond']['street'] != " " && !empty($data['where_cond']['street'])) {
          $district_street_1['block'] = $data['where_cond']['street'];
          unset($data['where_cond']['street']);
        }
        if (is_full_array($district_street_1)) {
          $data['like'][] = $district_street_1;
        }

        //区属、板块2
        $district_street_2 = array();
        if ($data['where_cond']['district_2'] != " " && !empty($data['where_cond']['district_2'])) {
          $district_street_2['district'] = $data['where_cond']['district_2'];
          unset($data['where_cond']['district_2']);
        }
        if ($data['where_cond']['street_2'] != " " && !empty($data['where_cond']['street_2'])) {
          $district_street_2['block'] = $data['where_cond']['street_2'];
          unset($data['where_cond']['street_2']);
        }
        if (is_full_array($district_street_2)) {
          $data['like'][] = $district_street_2;
        }

        //区属、板块3
        $district_street_3 = array();
        if ($data['where_cond']['district_3'] != " " && !empty($data['where_cond']['district_3'])) {
          $district_street_3['district'] = $data['where_cond']['district_3'];
          unset($data['where_cond']['district_3']);
        }
        if ($data['where_cond']['street_3'] != " " && !empty($data['where_cond']['street_3'])) {
          $district_street_3['block'] = $data['where_cond']['street_3'];
          unset($data['where_cond']['street_3']);
        }
        if (is_full_array($district_street_3)) {
          $data['like'][] = $district_street_3;
        }

        $data['or_like'] = array();
        if (!empty($collect_set_cooperate[0]['block_name'])) {
          $data['or_like'][] = $collect_set_cooperate[0]['block_name'];
        }
        if (!empty($collect_set_cooperate[0]['block_name_2'])) {
          $data['or_like'][] = $collect_set_cooperate[0]['block_name_2'];
        }
        if (!empty($collect_set_cooperate[0]['block_name_3'])) {
          $data['or_like'][] = $collect_set_cooperate[0]['block_name_3'];
        }

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

        $data['order_by'] = 'createtime';
        //$data['blacklist'] = $this->collections_model_cooperate->get_house_sell_set($data['where_cond'],$data['order_by'],$this->_limit,$this->_offset,'dbback');

        //sphinx搜素
        $data['valid_time'] = 3;
        $result = $this->collections_model_cooperate->sphinx_set($data['where_cond'], $data['like'], $data['or_like'], $data['valid_time'], $city_spell, 'sell', $page, $this->_limit, $this->_offset);
        $data['blacklist'] = $result['blacklist'];

        $this->_total_count = $count = $result['total'];

        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
        //符合条件的总行数
        //$this->_total_count = $count = $this->collections_model_cooperate->get_sell_num_set($data['where_cond'],'dbback');
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
            $res = $this->collections_model_cooperate->get_sell_house_collect_history($id);
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
            $a['buildarea'] = intval($v['buildarea']);
            $a['price'] = intval($v['price']);
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
        $info = array('count' => 0, 'read_count' => 0, 'data' => array());
        $this->result(1, '当前经纪人未查到订阅条件', $info);
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
  public function collect_rent($page = 1)
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $this->load->model('district_model');//区属模型类

      $broker_id = $this->input->get('broker_id');
      $data['conf_where'] = 'index';
      $data['where_cond'] = array();
      $data['where_in'] = array();
      $data['like'] = $data['or_like'] = array();

      $arr = array("broker_id" => $broker_id, 'tbl_name' => 'rent_house_collect');
      $judge = $this->collections_model_cooperate->get_agent_house($arr);

      $house_ids = array();
      foreach ($judge as $k => $v) {
        $house_ids[] = $v['house_id'];
      }
      $data['judge'] = $judge;
      $data['broker_info'] = $broker_id;
      $data['valid_time'] = 3;

      //get参数   ===>>>实际为post参数
      $get_param = $this->input->get(NULL, TRUE);
      $get_param['rent_type'] = $get_param['type'];
      $get_param['rent_area'] = $get_param['area'];
      $get_param['rent_price'] = $get_param['price'];
      unset($get_param['type']);
      unset($get_param['area']);
      unset($get_param['price']);

      $data['where_cond'] = $this->_get_cond_str_rent($get_param, $city_id);

      if ($data['where_cond']['district'] != " " && !empty($data['where_cond']['district'])) {
        $data['like'] = array('district' => $data['where_cond']['district']);
        unset($data['where_cond']['district']);
      }
      if ($data['where_cond']['street'] != " " && !empty($data['where_cond']['street'])) {
        $data['like'] = $data['like'] + array('block' => $data['where_cond']['street']);
        unset($data['where_cond']['street']);
      }
      if ($data['where_cond']['house_name'] != "" && !empty($data['where_cond']['house_name'])) {
        $data['or_like']['like_key'] = array('house_name');
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
      //sphinx搜素
      $result = $this->collections_model_cooperate->sphinx($data['where_cond'], $data['where_in'], $data['like'], $data['or_like'], $data['valid_time'], $city_spell, 'rent', $page, $this->_limit, $this->_offset);
      $data['blacklist'] = $result['blacklist'];
      //符合条件的总行数
      $this->_total_count = $count = $result['total'];
//            $this->_total_count = $count = 1;//$this->collections_model_cooperate->get_rent_num($data['where_cond'],$data['like'],$data['or_like']);
      //计算总页数
      $pages = $this->_total_count > 0 ? (ceil($this->_total_count / $this->_limit) > $page_max ? $page_max : ceil($this->_total_count / $this->_limit)) : 0;
//            $pages  = $this->_total_count > 0 ? ceil( $this->_total_count / $this->_limit ) : 0;
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
//            $data['order_by'] = 'createtime';
//            $data['blacklist'] = $this->collections_model_cooperate->get_house_rent($data['where_cond'],$data['where_in']=array(),$data['like'],$data['or_like'],$data['order_by'],$this->_limit,$this->_offset);
      //$data['blacklist_all'] = $this->collections_model_cooperate->get_house_rent_allids();

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
      $info = array('count' => 0, 'read_count' => 0, 'collect_set' => array(), 'data' => array());
      //根据经纪人id查询唯一订阅条件
      $where_cond = array(
        'broker_id' => intval($broker_id),
        'type' => 'rent'
      );
      $collect_set_cooperate = $this->collections_model_cooperate->get_collect_set($where_cond);
      if (is_full_array($collect_set_cooperate[0])) {
        $info['collect_set'] = $collect_set_cooperate[0];
        $set_price = $collect_set_cooperate[0]['price'];
        $set_room = $collect_set_cooperate[0]['room'];
        $config_house = $this->house_config_model->get_config();
        if ($set_price > 0) {
          $info['collect_set']['price_name'] = $config_house['rent_price'][$set_price];
        }
        if ($set_room > 0) {
          $info['collect_set']['room_name'] = $config_house['room'][$set_room];
        }
      } else {
        $info['collect_set'] = array('id' => 0);
      }
      //获取列表内容
      if (!empty($data['blacklist'])) {
        foreach ($data['blacklist'] as $k => $v) {
          $id = $v['id'];
          $res = $this->collections_model_cooperate->get_rent_house_collect_history($id);
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
          if ((!empty($v['picurl']) && $v['picurl'] != '暂无资料') || (!empty($v['web_picurl']) && $v['web_picurl'] != '暂无资料')) {
            $a['pic'] = 1;
          } else {
            $a['pic'] = 0;
          }
          $a['telno1'] = $v['telno1'];
          $a['owner'] = $v['owner'] ? $v['owner'] : '暂无资料';
          $a['buildarea'] = intval($v['buildarea']);
          $a['price'] = intval($v['price']);
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
   * 采集房源 出售 用于合作app
   * @access public
   * @return void
   * date 2014-12-28
   * author yuan
   */
  public function collect_rent_set($page = 1)
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $this->load->model('district_model');//区属模型类

      $broker_id = $this->input->get('broker_id');
      $data['conf_where'] = 'index';
      $data['where_cond'] = array();
      $data['like'] = $data['or_like'] = array();

      $arr = array("broker_id" => $broker_id, 'tbl_name' => 'rent_house_collect');
      $judge = $this->collections_model_cooperate->get_agent_house($arr);

      $house_ids = array();
      foreach ($judge as $k => $v) {
        $house_ids[] = $v['house_id'];
      }
      $data['judge'] = $judge;
      $data['broker_info'] = $broker_id;

      //get参数   ===>>>实际为post参数
      $post_param = $this->input->get(NULL, TRUE);
      $post_param['rent_type'] = 1;
      //根据经纪人id查询唯一订阅条件
      $where_cond = array(
        'broker_id' => intval($broker_id),
        'type' => 'rent'
      );
      $collect_set_cooperate = $this->collections_model_cooperate->get_collect_set($where_cond);
      if (is_full_array($collect_set_cooperate)) {
        $post_param['district_cj'] = $collect_set_cooperate[0]['dist_id'];
        $post_param['district_2_cj'] = $collect_set_cooperate[0]['dist_id_2'];
        $post_param['district_3_cj'] = $collect_set_cooperate[0]['dist_id_3'];
        $post_param['street_cj'] = $collect_set_cooperate[0]['street_id'];
        $post_param['street_2_cj'] = $collect_set_cooperate[0]['street_id_2'];
        $post_param['street_3_cj'] = $collect_set_cooperate[0]['street_id_3'];
//                $post_param['house_name'] = $collect_set_cooperate[0]['block_name'];
//                $post_param['house_name_2'] = $collect_set_cooperate[0]['block_name_2'];
//                $post_param['house_name_3'] = $collect_set_cooperate[0]['block_name_3'];
        $post_param['rent_price'] = $collect_set_cooperate[0]['price'];
        $post_param['room'] = $collect_set_cooperate[0]['room'];

        $data['where_cond'] = $this->_get_cond_str_rent($post_param, $city_id);

        //区属、板块1
        $district_street_1 = array();
        if ($data['where_cond']['district'] != " " && !empty($data['where_cond']['district'])) {
          $district_street_1['district'] = $data['where_cond']['district'];
          unset($data['where_cond']['district']);
        }
        if ($data['where_cond']['street'] != " " && !empty($data['where_cond']['street'])) {
          $district_street_1['block'] = $data['where_cond']['street'];
          unset($data['where_cond']['street']);
        }
        if (is_full_array($district_street_1)) {
          $data['like'][] = $district_street_1;
        }

        //区属、板块2
        $district_street_2 = array();
        if ($data['where_cond']['district_2'] != " " && !empty($data['where_cond']['district_2'])) {
          $district_street_2['district'] = $data['where_cond']['district_2'];
          unset($data['where_cond']['district_2']);
        }
        if ($data['where_cond']['street_2'] != " " && !empty($data['where_cond']['street_2'])) {
          $district_street_2['block'] = $data['where_cond']['street_2'];
          unset($data['where_cond']['street_2']);
        }
        if (is_full_array($district_street_2)) {
          $data['like'][] = $district_street_2;
        }

        //区属、板块3
        $district_street_3 = array();
        if ($data['where_cond']['district_3'] != " " && !empty($data['where_cond']['district_3'])) {
          $district_street_3['district'] = $data['where_cond']['district_3'];
          unset($data['where_cond']['district_3']);
        }
        if ($data['where_cond']['street_3'] != " " && !empty($data['where_cond']['street_3'])) {
          $district_street_3['block'] = $data['where_cond']['street_3'];
          unset($data['where_cond']['street_3']);
        }
        if (is_full_array($district_street_3)) {
          $data['like'][] = $district_street_3;
        }

        $data['or_like'] = array();
        if (!empty($collect_set_cooperate[0]['block_name'])) {
          $data['or_like'][] = $collect_set_cooperate[0]['block_name'];
        }
        if (!empty($collect_set_cooperate[0]['block_name_2'])) {
          $data['or_like'][] = $collect_set_cooperate[0]['block_name_2'];
        }
        if (!empty($collect_set_cooperate[0]['block_name_3'])) {
          $data['or_like'][] = $collect_set_cooperate[0]['block_name_3'];
        }

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

        $data['order_by'] = 'createtime';
        //$data['blacklist'] = $this->collections_model_cooperate->get_house_rent_set($data['where_cond'],$data['order_by'],$this->_offset,$this->_limit);

        //sphinx搜素
        $data['valid_time'] = 3;
        $result = $this->collections_model_cooperate->sphinx_set($data['where_cond'], $data['like'], $data['or_like'], $data['valid_time'], $city_spell, 'rent', $page, $this->_limit, $this->_offset);
        $data['blacklist'] = $result['blacklist'];

        $this->_total_count = $count = $result['total'];

        // 分页参数
        $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
        //符合条件的总行数
        //$this->_total_count = $count = $this->collections_model_cooperate->get_rent_num_set($data['where_cond'],'dbback');
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
            $res = $this->collections_model_cooperate->get_rent_house_collect_history($id);
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
            $a['buildarea'] = intval($v['buildarea']);
            $a['price'] = intval($v['price']);
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
        $info = array('count' => 0, 'read_count' => 0, 'data' => array());
        $this->result(1, '当前经纪人未查到订阅条件', $info);
      }
    } else {
      $this->result(0, '城市参数错误', $info);
    }
  }

  /**
   * 我的采集---出售房源 用于合作app
   * @access public
   * @return void
   * date 2014-12-28
   * author yuan
   */
  public function my_collect_sell($page = 1)
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $this->load->model('district_model');//区属模型类

      $broker_id = $this->input->get('broker_id');
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
      $result = $this->collections_model_cooperate->check_agent_house($where);

      //实际为post参数
      $get_param = $this->input->get(NULL, TRUE);
      $get_param['sell_type'] = $get_param['type'];
      $get_param['sell_area'] = $get_param['area'];
      $get_param['sell_price'] = $get_param['price'];
      unset($get_param['type']);
      unset($get_param['area']);
      unset($get_param['price']);
      $data['where_cond'] = $this->_get_cond_str($get_param, $city_id);

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
      $judge = $this->collections_model_cooperate->get_agent_house($arr);
      $data['judge'] = $judge;
      $data['broker_info'] = $broker_id;

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

      $data['district'] = $this->district_model->get_cj_district();
      //post参数
      $post_param = $get_param;
      // 分页参数
      $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
      $this->_init_pagination($page);
      //符合条件的总行数
      $this->_total_count = count($judge);
      $count = $this->collections_model_cooperate->get_sell_num($data['where_cond'], $data['like'], $data['or_like']);
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
      $data['blacklist'] = $this->collections_model_cooperate->get_house_sell($data['where_cond'], $data['where_in'], $data['like'], $data['or_like'], $data['order_by'], $this->_limit, $this->_offset);


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
          $a['buildarea'] = intval($v['buildarea']);
          $a['price'] = intval($v['price']);
          //$a['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
          if ($v['createtime'] > strtotime(date('Y-m-d'))) {
            //今天
            $a['createtime'] = '今天：' . date('H:i:s', $v['createtime']);
          } else {
            //24小时以前
            $a['createtime'] = date('Y-m-d', $v['createtime']);
          }
          $a['if_read'] = 1;

          //价格变动
          $id = $v['id'];
          $res = $this->collections_model_cooperate->get_sell_house_collect_history($id);
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
          $a['refresh'] = $v['refresh'];
          $info['data'][] = $a;
        }
        $info['count'] = $count;
        $info['read_count'] = count($judge);
        $this->result(1, '查询已查看出售房源成功', $info);
      } else {
        $this->result(1, '暂无查询内容', array('data' => array()));
      }
    } else {
      $this->result(0, '城市参数错误', $info);
    }
  }

  /**
   * 我的采集---出租房源 用于合作app
   * @access public
   * @return void
   * date 2014-12-28
   * author yuan
   */
  public function my_collect_rent($page = 1)
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $this->load->model('district_model');//区属模型类

      $broker_id = $this->input->get('broker_id');
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
      $result = $this->collections_model_cooperate->check_agent_house($where);

      //实际为post参数
      $get_param = $this->input->get(NULL, TRUE);
      $get_param['rent_type'] = $get_param['type'];
      $get_param['rent_area'] = $get_param['area'];
      $get_param['rent_price'] = $get_param['price'];
      unset($get_param['type']);
      unset($get_param['area']);
      unset($get_param['price']);
      $data['where_cond'] = $this->_get_cond_str_rent($get_param, $city_id);

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

      if ($data['where_cond']['street'] != " " && !empty($data['where_cond']['street'])) {
        $data['like'] = $data['like'] + array('block' => $data['where_cond']['street']);
        unset($data['where_cond']['street']);
      }

      if ($data['where_cond']['house_name'] != "" && !empty($data['where_cond']['house_name'])) {
        $data['or_like']['like_key'] = array('block', 'house_name', 'house_title');
        $data['or_like']['like_value'] = $data['where_cond']['house_name'];
      }

      $arr = array("broker_id" => $broker_id, 'tbl_name' => 'rent_house_collect');
      $judge = $this->collections_model_cooperate->get_agent_house($arr);
      $data['judge'] = $judge;
      $data['broker_info'] = $broker_id;

      $data['district'] = $this->district_model->get_cj_district();

      //post参数
      $post_param = $get_param;
      // 分页参数
      $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
      $this->_init_pagination($page);
      //符合条件的总行数
      $this->_total_count = count($judge);
      $count = $this->collections_model_cooperate->get_rent_num($data['where_cond'], $data['like'], $data['or_like']);
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
      $data['blacklist'] = $this->collections_model_cooperate->get_house_rent($data['where_cond'], $data['where_in'], $data['like'], $data['or_like'], $data['order_by'], $this->_limit, $this->_offset);


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
          $a['buildarea'] = intval($v['buildarea']);
          $a['price'] = intval($v['price']);
          if ($v['createtime'] > strtotime(date('Y-m-d'))) {
            //今天
            $a['createtime'] = '今天：' . date('H:i:s', $v['createtime']);
          } else {
            //24小时以前
            $a['createtime'] = date('Y-m-d', $v['createtime']);
          }
          $a['if_read'] = 1;

          //价格变动
          $id = $v['id'];
          $res = $this->collections_model_cooperate->get_rent_house_collect_history($id);
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
          $a['refresh'] = $v['refresh'];
          $info['data'][] = $a;
        }
        $info['count'] = $count;
        $info['read_count'] = count($judge);
        $this->result(1, '查询已查看出租房源成功', $info);
      } else {
        $this->result(1, '暂无查询内容', array('data' => array()));
      }
    } else {
      $this->result(0, '城市参数错误', $info);
    }
  }

  /**
   * 出售详情，用于合作app
   * @access public
   * @return void
   * date 2015-01-09
   * author yuan
   */
  public function good_sell_details()
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $broker_id = intval($this->input->get('broker_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $this->load->model('view_log_model');//添加日志
      $house_id = $this->input->get('house_id');

      $data['conf_where'] = 'index';
      $data['where_cond'] = array();
      //根据房源house_id去查询房源详情
      if (!empty($house_id)) {
        //已读房源id
        $arr = array("broker_id" => $broker_id, 'tbl_name' => 'sell_house_collect');
        $judge = $this->collections_model_cooperate->get_agent_house($arr);

        $read_house_ids = array();
        foreach ($judge as $k => $v) {
          $read_house_ids[] = $v['house_id'];
        }

        $data['where_cond'] = array('id' => $house_id);
        $data['house_info'] = $this->collections_model_cooperate->get_housesell_byid($data['where_cond']);
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
            //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产
            switch ($v['source_from']) {
              case "0":
                $source_from_str = "赶集";
                break;
              case "1":
                $source_from_str = "58同城";
                break;
              case "2":
                $source_from_str = "房天下";
                break;
              case "3":
                $source_from_str = "house365";
                break;
              case "4":
                $source_from_str = "链家地产";
                break;
              default:
                $source_from_str = "暂无资料";
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
            $a['from'] = $source_from_str;
            $a['sell_type'] = $sell_type;
            $a['district'] = $v['district'];
            $a['block'] = $v['block'];
            if (empty($v['house_name'])) {
              $a['house_name'] = '暂无资料';
            } else {
              $a['house_name'] = $v['house_name'];
            }
            $a['buildarea'] = intval($v['buildarea']);
            $a['house_addr'] = trim($v['house_addr']);
            $a['house_type'] = $house_type;
            $a['price'] = intval($v['price']);
            $a['avgprice'] = floor(intval($v['avgprice']));
            if (!($a['avgprice'] > 0) && $a['buildarea'] > 0) {
              $a['avgprice'] = floor($a['price'] * 10000 / $a['buildarea']);
            }
            $a['lowprice'] = intval($v['lowprice']);
            $a['forward'] = $forward;
            $a['room'] = $v['room'];
            $a['hall'] = $v['hall'];
            $a['toilet'] = $v['toilet'];
            $a['balcony'] = $v['balcony'];
            $a['floor'] = $v['floor'];
            $a['totalfloor'] = $v['totalfloor'];
            $a['house_title'] = $v['house_title'];
            $a['serverco'] = $serverco;
            $a['pic'] = array();
            if ($v['picurl'] != '暂无资料' && $v['picurl'] != '') {
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
            } else {
              if ($v['web_picurl'] != '暂无资料' && $v['web_picurl'] != '') {
                $picarr = explode('*', $v['web_picurl']);
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
            $a['remark'] = trim(strip_tags($v['remark']));
            //判断价格是否有变动
            $res = $this->collections_model_cooperate->get_sell_house_collect_history($house_id);
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
                $res[$key]['oldtime'] = date('Y.m.d', $val['oldtime']);
                $res[$key]['price'] = intval($res[$key]['price']);
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
            $check_mess = $this->collections_model_cooperate->check_sell_house($house_id);
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
              $status = $this->collections_model_cooperate->check_sell_ajax($data);
            }
            $a['status'] = $status;
            //组装条件查询经纪人已看房源
            $arr = array("broker_id" => $this->user_arr['broker_id'], 'tbl_name' => 'sell_house_collect', 'house_id' => $house_id);
            $judge = $this->collections_model_cooperate->get_agent_house($arr);
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
            $read_times = $this->collections_model_cooperate->get_readtimes_byid($where_cond);
            $a['read_times'] = count($read_times);
            //获取经纪人基本信息
            $broker_data = $this->user_arr;
            $company_id = $broker_data['company_id'];
            $num = $this->collections_model_cooperate->collect_sell_publish_check($house_id, $company_id);
            $a['company_add_house'] = $num > 0 ? 1 : 0;
            //房源状态，是否下架
            $check_result = $this->collections_model_cooperate->check_sell_house($house_id);
            $a['check_status'] = '';
            if (is_full_array($check_result)) {
              $a['check_status'] = $check_result['status'];
            }
            //是否已读
            $a['is_read'] = 0;
            if (is_full_array($read_house_ids) && in_array($house_id, $read_house_ids)) {
              $a['is_read'] = 1;
            }
            $a['refresh'] = $v['refresh'];
            $info = $a;
          }
          //加入点击日志
          $this->view_log_model->add_collect_click_log($this->user_arr['broker_id'], $house_id, 1);
          $info['is_data'] = 1;
          $this->result(1, '查询成功', $info);
        } else {
          $info = array(
            'is_data' => 0
          );
          $this->result(1, '未查询到数据', $info);
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
  public function good_rent_details()
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $broker_id = intval($this->input->get('broker_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $this->load->model('view_log_model');//添加日志

      $house_id = $this->input->get('house_id');
      $data['conf_where'] = 'index';
      $data['where_cond'] = array();
      if (!empty($house_id)) {
        //已读房源id
        $arr = array("broker_id" => $broker_id, 'tbl_name' => 'rent_house_collect');
        $judge = $this->collections_model_cooperate->get_agent_house($arr);

        $read_house_ids = array();
        foreach ($judge as $k => $v) {
          $read_house_ids[] = $v['house_id'];
        }

        $data['where_cond'] = array('id' => $house_id);
        $data['house_info'] = $this->collections_model_cooperate->get_houserent_byid($data['where_cond']);//获取获取房源详情
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
            //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产
            switch ($v['source_from']) {
              case "0":
                $source_from_str = "赶集";
                break;
              case "1":
                $source_from_str = "58同城";
                break;
              case "2":
                $source_from_str = "房天下";
                break;
              case "3":
                $source_from_str = "house365";
                break;
              case "4":
                $source_from_str = "链家地产";
                break;
              default:
                $source_from_str = "暂无资料";
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
            $a['from'] = $source_from_str;
            $a['rent_type'] = $rent_type;
            $a['district'] = $v['district'];
            $a['block'] = $v['block'];
            if (empty($v['house_name'])) {
              $a['house_name'] = '暂无资料';
            } else {
              $a['house_name'] = $v['house_name'];
            }
            $a['buildarea'] = intval($v['buildarea']);
            $a['house_addr'] = trim($v['house_addr']);
            $a['house_type'] = $house_type;
            $a['price'] = intval($v['price']);
            $a['pricetype'] = $v['pricetype'];
            $a['avgprice'] = $v['avgprice'] ? "暂无资料" : intval($v['avgprice']);
            $a['forward'] = $forward;
            $a['room'] = $v['room'];
            $a['hall'] = $v['hall'];
            $a['toilet'] = $v['toilet'];
            $a['balcony'] = $v['balcony'];
            $a['floor'] = $v['floor'];
            $a['totalfloor'] = $v['totalfloor'];
            $a['serverco'] = $serverco;
            $a['house_title'] = $v['house_title'];
            $a['pic'] = array();
            if ($v['picurl'] != '暂无资料' && $v['picurl'] != '') {
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
            } else {
              if ($v['web_picurl'] != '暂无资料' && $v['web_picurl'] != '') {
                $picarr = explode('*', $v['web_picurl']);
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
            $a['remark'] = trim(strip_tags($v['remark']));
            //判断价格是否有变动
            $res = $this->collections_model_cooperate->get_rent_house_collect_history($house_id);
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
                $res[$key]['oldtime'] = date('Y.m.d', $val['oldtime']);
                $res[$key]['price'] = intval($res[$key]['price']);
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
            $check_mess = $this->collections_model_cooperate->check_rent_house($house_id);
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
              $status = $this->collections_model_cooperate->check_rent_ajax($data);
            }
            $a['status'] = $status;
            //组装条件查询经纪人已看房源
            $arr = array("broker_id" => $this->user_arr['broker_id'], 'tbl_name' => 'rent_house_collect', 'house_id' => $house_id);
            $judge = $this->collections_model_cooperate->get_agent_house($arr);
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
            $read_times = $this->collections_model_cooperate->get_readtimes_byid($where_cond);
            $a['read_times'] = count($read_times);
            //获取经纪人基本信息
            $broker_data = $this->user_arr;
            $company_id = $broker_data['company_id'];
            $num = $this->collections_model_cooperate->collect_rent_publish_check($house_id, $company_id);
            $a['company_add_house'] = $num > 0 ? 1 : 0;
            //房源状态，是否下架
            $check_result = $this->collections_model_cooperate->check_rent_house($house_id);
            $a['check_status'] = '';
            if (is_full_array($check_result)) {
              $a['check_status'] = $check_result['status'];
            }
            //是否已读
            $a['is_read'] = 0;
            if (is_full_array($read_house_ids) && in_array($house_id, $read_house_ids)) {
              $a['is_read'] = 1;
            }
            $a['refresh'] = $v['refresh'];
            $info = $a;
          }
          //加入点击日志
          $this->view_log_model->add_collect_click_log($this->user_arr['broker_id'], $house_id, 2);
          $info['is_data'] = 1;
          $this->result(1, '查询成功', $info);
        } else {
          $info = array(
            'is_data' => 0
          );
          $this->result(1, '未查询到数据', $info);
        }
      } else {
        $this->result(0, '参数不合法，查询失败！');
      }
    } else {
      $this->result(0, '城市参数错误', $info);
    }
  }

  /**
   * 出售解密号码---用于合作app
   * @access public
   * @return void
   * date 2015-01-09
   * author yuan
   */
  public function good_sell_phone()
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $is_auth = $this->input->get('is_auth');
      if ('1' == $is_auth) {
        $this->config->set_item('login_city', $city_spell);
        $this->load->model('collections_model_cooperate');//采集模型类

        $house_id = $this->input->get('house_id');
        $broker_id = $this->input->get('broker_id');

        //当经纪人点击解密电话时，往 agent_house_judge 数据表里插入数据，即设置为已读！
        if (!empty($house_id) && !empty($broker_id)) {
          $tbl_name = 'sell_house_collect';
          //判断经纪人是否查看过该房源信息，是 则不做操作，否则向agent_house_judge表里插入经纪人查看信息
          $where = array(
            'house_id' => $house_id,
            'broker_id' => $broker_id,
            'tbl_name' => $tbl_name
          );
          $result = $this->collections_model_cooperate->check_agent_house($where);
          $ahouse_info = array(
            'house_id' => $house_id,
            'broker_id' => $broker_id,
            'tbl_name' => $tbl_name,
            'is_input' => 0,
            'createtime' => time()
          );
          if (empty($result)) {
            $this->collections_model_cooperate->add_agent_house($ahouse_info);
          }
        }

        $data['conf_where'] = 'index';
        $data['where_cond'] = array();
        //根据房源house_id去查询房源详情
        if (!empty($house_id)) {
          $data['where_cond'] = array('id' => $house_id);
          $data['house_info'] = $this->collections_model_cooperate->get_housesell_byid($data['where_cond']);
          //获取房源详情
          if (!empty($data['house_info'])) {
            foreach ($data['house_info'] as $key => $v) {
              //数据重构
              $info = array();
              $a['owner'] = $v['owner'];
              $a['telno1'] = $v['telno1'];
              $info = $a;
            }
            //加入统计日志
            $this->info_count($broker_id, $city_spell, 1, $house_id);
            $this->result(1, '查询成功', $info);
          } else {
            $this->result(1, '未查询到数据！');
          }
        } else {
          $this->result(0, '参数不合法，查询失败！');
        }
      } else {
        $result = array('owner' => '');
        $this->result(0, '帐号未认证！', $result);
      }
    } else {
      $this->result(0, '城市参数错误', $info);
    }
  }

  /**
   * 出租解密号码---用于合作app
   * @access public
   * @return void
   * date 2015-01-09
   * author yuan
   */
  public function good_rent_phone()
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $is_auth = $this->input->get('is_auth');
      if ('1' == $is_auth) {
        $this->config->set_item('login_city', $city_spell);
        $this->load->model('collections_model_cooperate');//采集模型类
        $house_id = $this->input->get('house_id');
        $broker_id = $this->input->get('broker_id');

        //当经纪人点击解密电话时，往 agent_house_judge 数据表里插入数据，即设置为已读！
        if (!empty($house_id) && !empty($broker_id)) {
          $tbl_name = 'rent_house_collect';
          $where = array(
            'house_id' => $house_id,
            'broker_id' => $broker_id,
            'tbl_name' => $tbl_name
          );
          $result = $this->collections_model_cooperate->check_agent_house($where);
          $ahouse_info = array(
            'house_id' => $house_id,
            'broker_id' => $broker_id,
            'tbl_name' => $tbl_name,
            'is_input' => 0,
            'createtime' => time()
          );
          if (empty($result)) {
            $this->collections_model_cooperate->add_agent_house($ahouse_info);
          }
        }

        $data['conf_where'] = 'index';
        $data['where_cond'] = array();
        //根据房源house_id去查询房源详情
        if (!empty($house_id)) {
          $data['where_cond'] = array('id' => $house_id);
          $data['house_info'] = $this->collections_model_cooperate->get_houserent_byid($data['where_cond']);
          //获取房源详情
          if (!empty($data['house_info'])) {
            foreach ($data['house_info'] as $key => $v) {
              //数据重构
              $info = array();
              $a['owner'] = $v['owner'];
              $a['telno1'] = $v['telno1'];
              $info = $a;
            }
            //加入统计日志
            $this->info_count($broker_id, $city_spell, 2, $house_id);
            $this->result(1, '查询成功', $info);
          } else {
            $this->result(1, '未查询到数据！');
          }
        } else {
          $this->result(0, '参数不合法，查询失败！');
        }
      } else {
        $result = array('owner' => '');
        $this->result(0, '帐号未认证！', $result);
      }
    } else {
      $this->result(0, '城市参数错误', $info);
    }

  }


  //举报
  public function report_agent()
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $report_result = array();
      $r_addtime = time();
      $r_reason = '中介冒充个人';
      $r_person = $this->input->get('phone');
      $broker_id = $this->input->get('broker_id');
      $r_tel = $this->input->get('r_tel');
      $where_cond = array();
      if (!empty($r_tel)) {
        $where_cond = array('r_tel' => $r_tel);
      } else {
        $this->result(0, '参数不合法');
        die();
      }
      $result = $this->collections_model_cooperate->check_reprot_tel($where_cond);
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
          'is_cooperate_app' => 1
        );
        $rel = $this->collections_model_cooperate->agent_reportlist($info);
        if ($rel == "") {
          $this->result(0, '该房源已经被举报，请勿重复举报！');
        } else {
          $url = MLS_MOBILE_URL . '/config/broker_trust/' . $broker_id;
          $data = $this->curl->vget($url, '');
          $this->result(1, '举报成功');
        }
      }
    } else {
      $this->result(0, '城市参数错误', $info);
    }

  }

  /**
   * 采集设置的相关数据设置
   * 2016.5.26
   * cc
   */
  public function save_collect_set()
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->post('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $result = array();
      $post_param['district'] = trim($this->input->post('district'));
      $post_param['district'] = str_replace("区", "", $post_param['district']);
      $post_param['district'] = str_replace("县", "", $post_param['district']);
      $post_param['district_2'] = trim($this->input->post('district_2'));
      $post_param['district_2'] = str_replace("区", "", $post_param['district_2']);
      $post_param['district_2'] = str_replace("县", "", $post_param['district_2']);
      $post_param['district_3'] = trim($this->input->post('district_3'));
      $post_param['district_3'] = str_replace("区", "", $post_param['district_3']);
      $post_param['district_3'] = str_replace("县", "", $post_param['district_3']);

      $post_param['dist_id'] = $this->input->post('dist_id');
      $post_param['dist_id_2'] = $this->input->post('dist_id_2');
      $post_param['dist_id_3'] = $this->input->post('dist_id_3');

      $post_param['broker_id'] = $this->input->post('broker_id');
      $post_param['street'] = trim($this->input->post('streetname'));
      $post_param['street_id'] = $this->input->post('street_id');
      $post_param['street_2'] = trim($this->input->post('streetname_2'));
      $post_param['street_id_2'] = $this->input->post('street_id_2');
      $post_param['street_3'] = trim($this->input->post('streetname_3'));
      $post_param['street_id_3'] = $this->input->post('street_id_3');

      $post_param['block_name'] = trim($this->input->post('blockname'));
      $post_param['block_name_2'] = trim($this->input->post('blockname_2'));
      $post_param['block_name_3'] = trim($this->input->post('blockname_3'));
      $post_param['price'] = $this->input->post('price');
      $post_param['room'] = $this->input->post('room');
      $post_param['type'] = $this->input->post('type');
      $res = $this->collections_model_cooperate->get_collect_set($post_param);
      if (empty($res)) {
        $num = $this->collections_model_cooperate->get_collect_set_num($post_param['broker_id'], $post_param['type']);
        if ($num < 1) {
          $post_param['createtime'] = time();
          $res = $this->collections_model_cooperate->save_collect_set($post_param);
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
    } else {
      $this->result(0, '城市参数错误', $info);
    }
  }

  /**
   * 删除采集设置条件
   * 2016.5.27
   * cc
   */
  public function delete_collect_set()
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $id = $this->input->get('set_id');
      $res = $this->collections_model_cooperate->delete_collect_set_byid($id);
      if ($res) {
        $this->result(1, '操作成功');
      } else {
        $this->result(0, '操作失败');
      }
    } else {
      $this->result(0, '城市参数错误', $info);
    }
  }

  /**
   * 修改采集设置条件
   * 2016.8.24
   * yuan
   */
  public function modify_collect_set()
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->post('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $post_param['district'] = trim($this->input->post('district'));
      $post_param['district'] = str_replace("区", "", $post_param['district']);
      $post_param['district'] = str_replace("县", "", $post_param['district']);
      $post_param['district_2'] = trim($this->input->post('district_2'));
      $post_param['district_2'] = str_replace("区", "", $post_param['district_2']);
      $post_param['district_2'] = str_replace("县", "", $post_param['district_2']);
      $post_param['district_3'] = trim($this->input->post('district_3'));
      $post_param['district_3'] = str_replace("区", "", $post_param['district_3']);
      $post_param['district_3'] = str_replace("县", "", $post_param['district_3']);

      $post_param['dist_id'] = $this->input->post('dist_id');
      $post_param['dist_id_2'] = $this->input->post('dist_id_2');
      $post_param['dist_id_3'] = $this->input->post('dist_id_3');

      $post_param['broker_id'] = $this->input->post('broker_id');
      $post_param['street'] = trim($this->input->post('streetname'));
      $post_param['street_id'] = $this->input->post('street_id');
      $post_param['street_2'] = trim($this->input->post('streetname_2'));
      $post_param['street_id_2'] = $this->input->post('street_id_2');
      $post_param['street_3'] = trim($this->input->post('streetname_3'));
      $post_param['street_id_3'] = $this->input->post('street_id_3');

      $post_param['block_name'] = trim($this->input->post('blockname'));
      $post_param['block_name_2'] = trim($this->input->post('blockname_2'));
      $post_param['block_name_3'] = trim($this->input->post('blockname_3'));
      $post_param['price'] = $this->input->post('price');
      $post_param['room'] = $this->input->post('room');
      $post_param['type'] = $this->input->post('type');

      $set_id = intval($this->input->post('set_id'));
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $res = $this->collections_model_cooperate->update_collect_set_byid($set_id, $post_param);
      if ($res) {
        $this->result(1, '操作成功');
      } else {
        $this->result(0, '操作失败');
      }
    } else {
      $this->result(0, '城市参数错误', $info);
    }
  }

  /**
   * 查询采集
   * 2016.5.27
   * cc
   */
  public function get_collect_set()
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $broker_id = intval($this->input->get('broker_id'));
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $type = $this->input->get('type');
      $my_search = $this->collections_model_cooperate->get_collect_set_info($broker_id, $type);
      $new_search = array('list' => array());
      if ($my_search) {
        $config_house = $this->house_config_model->get_config();
        //print_r($config_house);
        $arr_price = $type == 'sell' ? $config_house['sell_price'] : $config_house['rent_price'];
        $set_data = $my_search[0];
        //获取当前条件下的房源数量
        $where_set = array();
        $valid_time = time() - 86400;//一天之内
        $where_set['where_cond']['createtime >='] = $valid_time;
        $where_set['where_cond']['sell_type'] = 1;
        if ($set_data['district']) {
          $where_set['like'] = array('district' => $set_data['district']);
        }
        if ($set_data['street']) {
          $where_set['or_like_2']['like_key'] = 'block';
          $where_set['or_like_2']['like_value'] = array($set_data['street'], $set_data['street_2'], $set_data['street_3']);
        }
        if ($set_data['block_name']) {
          $where_set['or_like']['like_key'] = 'house_name';
          $where_set['or_like']['like_value'] = array($set_data['block_name'], $set_data['block_name_2'], $set_data['block_name_3']);
        }
        if ($set_data['price']) {
          $price = $config_house['sell_price'][$set_data['price']];
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
        if ($set_data['room']) {
          $where_set['where_cond']['room'] = $set_data['room'];
        }
        $new_search['list']['id'] = $set_data['id'];
        $new_search['list']['district'] = empty($set_data['district']) ? '' : $set_data['district'];
        $new_search['list']['dist_id'] = empty($set_data['dist_id']) ? '' : $set_data['dist_id'];
        $new_search['list']['district_2'] = empty($set_data['district_2']) ? '' : $set_data['district_2'];
        $new_search['list']['dist_id_2'] = empty($set_data['dist_id_2']) ? '' : $set_data['dist_id_2'];
        $new_search['list']['district_3'] = empty($set_data['district_3']) ? '' : $set_data['district_3'];
        $new_search['list']['dist_id_3'] = empty($set_data['dist_id_3']) ? '' : $set_data['dist_id_3'];
        $new_search['list']['street'] = empty($set_data['street']) ? '' : $set_data['street'];
        $new_search['list']['street_id'] = empty($set_data['street_id']) ? '' : $set_data['street_id'];
        $new_search['list']['street_2'] = empty($set_data['street_2']) ? '' : $set_data['street_2'];
        $new_search['list']['street_id_2'] = empty($set_data['street_id_2']) ? '' : $set_data['street_id_2'];
        $new_search['list']['street_3'] = empty($set_data['street_3']) ? '' : $set_data['street_3'];
        $new_search['list']['street_id_3'] = empty($set_data['street_id_3']) ? '' : $set_data['street_id_3'];
        $new_search['list']['block_name'] = empty($set_data['block_name']) ? '' : $set_data['block_name'];
        $new_search['list']['block_name_2'] = empty($set_data['block_name_2']) ? '' : $set_data['block_name_2'];
        $new_search['list']['block_name_3'] = empty($set_data['block_name_3']) ? '' : $set_data['block_name_3'];
        $new_search['list']['price'] = $set_data['price'];
        $new_search['list']['price_name'] = $arr_price[$set_data['price']];
        $new_search['list']['room'] = $set_data['room'];
        $new_search['list']['room_name'] = empty($config_house['room'][$set_data['room']])
          ? '' : $config_house['room'][$set_data['room']];
        $new_search['list']['house_num'] = $this->collections_model_cooperate->get_sell_num($where_set['where_cond'], $where_set['like'], $where_set['or_like'], 'dbback', $where_set['or_like_2']);
      } else {
        $new_search['list']['id'] = 0;
      }
      $this->result(1, '查询成功', $new_search);
    } else {
      $this->result(0, '城市参数错误');
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

  private function _get_cond_str($form_param, $city_id = 0)
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
      if ($city_id == 3) {
        $district = str_replace('县', '', str_replace('区', '', $district));
      }
      $district = array('district' => $district);
      $cond_where = $cond_where + $district;
    }
    //采集区属
    if (isset($form_param['district_cj']) && !empty($form_param['district_cj']) && $form_param['district_cj'] > 0) {
      $district = intval($form_param['district_cj']);
      if ($city_id == 17) {
        if ($district == 1) {
          $district = 5;
        } elseif ($district == 2) {
          $district = 1;
        } elseif ($district == 3) {
          $district = 6;
        } elseif ($district == 4) {
          $district = 11;
        } elseif ($district == 5) {
          $district = 2;
        } elseif ($district == 6) {
          $district = 12;
        } elseif ($district == 7) {
          $district = 8;
        } elseif ($district == 8) {
          $district = 9;
        } elseif ($district == 9) {
          $district = 10;
        } elseif ($district == 10) {
          $district = 7;
        }
      }
      $district = $this->district_model->get_district_byid($district);
      if ($city_id == 3) {
        $district = str_replace('县', '', str_replace('区', '', $district));
      }
      $district = array('district' => $district);
      $cond_where = $cond_where + $district;
    }
    //采集区属_2
    if (isset($form_param['district_2_cj']) && !empty($form_param['district_2_cj']) && $form_param['district_2_cj'] > 0) {
      $district_2 = intval($form_param['district_2_cj']);
      $district_2 = $this->district_model->get_district_byid($district_2);
      if ($city_id == 3) {
        $district_2 = str_replace('县', '', str_replace('区', '', $district_2));
      }
      $district_2 = array('district_2' => $district_2);
      $cond_where = $cond_where + $district_2;
    }
    //采集区属_3
    if (isset($form_param['district_3_cj']) && !empty($form_param['district_3_cj']) && $form_param['district_3_cj'] > 0) {
      $district_3 = intval($form_param['district_3_cj']);
      $district_3 = $this->district_model->get_district_byid($district_3);
      if ($city_id == 3) {
        $district_3 = str_replace('县', '', str_replace('区', '', $district_3));
      }
      $district_3 = array('district_3' => $district_3);
      $cond_where = $cond_where + $district_3;
    }

    //采集板块
    if (isset($form_param['street_cj']) && !empty($form_param['street_cj']) && $form_param['street_cj'] > 0) {
      $street = intval($form_param['street_cj']);
      $street = $this->district_model->get_street_byid($street);
      $street = array('street' => $street);
      $cond_where = $cond_where + $street;
    }
    //采集板块_2
    if (isset($form_param['street_2_cj']) && !empty($form_param['street_2_cj']) && $form_param['street_2_cj'] > 0) {
      $street_2 = intval($form_param['street_2_cj']);
      $street_2 = $this->district_model->get_street_byid($street_2);
      $street_2 = array('street_2' => $street_2);
      $cond_where = $cond_where + $street_2;
    }
    //采集板块_3
    if (isset($form_param['street_3_cj']) && !empty($form_param['street_3_cj']) && $form_param['street_3_cj'] > 0) {
      $street_3 = intval($form_param['street_3_cj']);
      $street_3 = $this->district_model->get_street_byid($street_3);
      $street_3 = array('street_3' => $street_3);
      $cond_where = $cond_where + $street_3;
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

  //根据订阅条件获得查询sql
  private function _get_cond_str_set($form_param, $city_id = 0)
  {
    $where_str = 'id > 0 ';
    $this->load->model('district_model');//区属模型类

    //三个月之内
    $_time = time() - 7776000;
    $where_str .= ' and  createtime >= ' . $_time;

    //城市
    if (isset($form_param['city_id']) && !empty($form_param['city_id']) && $form_param['city_id'] > 0) {
      $where_str .= ' and  city = ' . $form_param['city_id'];
    }
    //采集区属
    if (isset($form_param['district_cj']) && !empty($form_param['district_cj']) && $form_param['district_cj'] > 0) {
      $district = intval($form_param['district_cj']);

      $district = $this->district_model->get_district_byid($district);
      if ($city_id == 3) {
        $district = str_replace('县', '', str_replace('区', '', $district));
      }
      $where_str .= ' and  (district like "%' . $district . '%"';

      //采集区属_2
      if (isset($form_param['district_2_cj']) && !empty($form_param['district_2_cj']) && $form_param['district_2_cj'] > 0) {
        $district_2 = intval($form_param['district_2_cj']);
        $district_2 = $this->district_model->get_district_byid($district_2);
        if ($city_id == 3) {
          $district_2 = str_replace('县', '', str_replace('区', '', $district_2));
        }
        $where_str .= ' or district like "%' . $district_2 . '%"';

        //采集区属_3
        if (isset($form_param['district_3_cj']) && !empty($form_param['district_3_cj']) && $form_param['district_3_cj'] > 0) {
          $district_3 = intval($form_param['district_3_cj']);
          $district_3 = $this->district_model->get_district_byid($district_3);
          if ($city_id == 3) {
            $district_3 = str_replace('县', '', str_replace('区', '', $district_3));
          }
          $where_str .= ' or district like "%' . $district_3 . '%"';
        }
      }
      $where_str .= ')';
    }


    //采集板块
    if (isset($form_param['street_cj']) && !empty($form_param['street_cj']) && $form_param['street_cj'] > 0) {
      $street = intval($form_param['street_cj']);
      $street = $this->district_model->get_street_byid($street);
      $where_str .= ' and  (block like "%' . $street . '%"';

      //采集板块_2
      if (isset($form_param['street_2_cj']) && !empty($form_param['street_2_cj']) && $form_param['street_2_cj'] > 0) {
        $street_2 = intval($form_param['street_2_cj']);
        $street_2 = $this->district_model->get_street_byid($street_2);
        $where_str .= ' or block like "%' . $street_2 . '%"';

        //采集板块_3
        if (isset($form_param['street_3_cj']) && !empty($form_param['street_3_cj']) && $form_param['street_3_cj'] > 0) {
          $street_3 = intval($form_param['street_3_cj']);
          $street_3 = $this->district_model->get_street_byid($street_3);
          $where_str .= ' or block like "%' . $street_3 . '%"';
        }
      }
      $where_str .= ')';
    }

    //楼盘名
    if (!empty($form_param['house_name']) && $form_param['house_name'] != '') {
      $house_name = trim($form_param['house_name']);
      $where_str .= ' and  (house_name like "%' . $house_name . '%") ';
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
          $where_str .= ' and  buildarea >= ' . $area_val[0] . ' and buildarea <= ' . $area_val[1];
        } else {
          if ($sell_area == 1) {
            $where_str .= ' and  buildarea <= ' . $area_val[0];
          } else {
            $where_str .= ' and  buildarea >= ' . $area_val[0];
          }
        }
      }
    }

    //出售价格条件
    if (isset($form_param['sell_price']) && !empty($form_param['sell_price']) && $form_param['sell_price'] > 0) {
      $price = intval($form_param['sell_price']);
      $sell_price = $this->house_config_model->get_config();
      $price_val = $sell_price['sell_price'][$price];
      if ($price_val) {
        $price_val = preg_replace("#[^0-9-]#", '', $price_val);
        $price_val = explode('-', $price_val);
        if (count($price_val) == 2) {
          $where_str .= ' and  price >= ' . $price_val[0] . ' and price <= ' . $price_val[1];
        } else {
          if ($price == 1) {
            $where_str .= ' and  price <= ' . $price_val[0];
          } else {
            $where_str .= ' and  price >= ' . $price_val[0];
          }
        }
      }
    }

    //出租价格条件
    if (isset($form_param['rent_price']) && !empty($form_param['rent_price']) && $form_param['rent_price'] > 0) {
      $price = intval($form_param['rent_price']);
      $rent_price = $this->house_config_model->get_config();
      $price_val = $rent_price['rent_price'][$price];
      if ($price_val) {
        $price_val = preg_replace("#[^0-9-]#", '', $price_val);
        $price_val = explode('-', $price_val);
        if (count($price_val) == 2) {
          $where_str .= ' and  price >= ' . $price_val[0] . ' and price <= ' . $price_val[1];
        } else {
          if ($price == 1) {
            $where_str .= ' and  price <= ' . $price_val[0];
          } else {
            $where_str .= ' and  price >= ' . $price_val[0];
          }
        }
      }
    }

    //户型条件
    if (isset($form_param['room']) && !empty($form_param['room']) && $form_param['room'] > 0) {
      $room = intval($form_param['room']);
      $where_str .= ' and  room = ' . $room;

    }

    return $where_str;
  }

  private function _get_cond_str_rent($form_param, $city_id = 0)
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
      if ($city_id == 17) {
        if ($district == 1) {
          $district = 5;
        } elseif ($district == 2) {
          $district = 1;
        } elseif ($district == 3) {
          $district = 6;
        } elseif ($district == 4) {
          $district = 11;
        } elseif ($district == 5) {
          $district = 2;
        } elseif ($district == 6) {
          $district = 12;
        } elseif ($district == 7) {
          $district = 8;
        } elseif ($district == 8) {
          $district = 9;
        } elseif ($district == 9) {
          $district = 10;
        } elseif ($district == 10) {
          $district = 7;
        }
      }
      $district = $this->district_model->get_district_byid($district);
      $district = str_replace('县', '', str_replace('区', '', $district));
      $district = array('district' => $district);
      $cond_where = $cond_where + $district;
    }
    //采集区属_2
    if (isset($form_param['district_2_cj']) && !empty($form_param['district_2_cj']) && $form_param['district_2_cj'] > 0) {
      $district_2 = intval($form_param['district_2_cj']);
      $district_2 = $this->district_model->get_district_byid($district_2);
      $district_2 = str_replace('县', '', str_replace('区', '', $district_2));
      $district_2 = array('district_2' => $district_2);
      $cond_where = $cond_where + $district_2;
    }
    //采集区属_3
    if (isset($form_param['district_3_cj']) && !empty($form_param['district_3_cj']) && $form_param['district_3_cj'] > 0) {
      $district_3 = intval($form_param['district_3_cj']);
      $district_3 = $this->district_model->get_district_byid($district_3);
      $district_3 = str_replace('县', '', str_replace('区', '', $district_3));
      $district_3 = array('district_3' => $district_3);
      $cond_where = $cond_where + $district_3;
    }
    //采集板块
    if (isset($form_param['street_cj']) && !empty($form_param['street_cj']) && $form_param['street_cj'] > 0) {
      $street = intval($form_param['street_cj']);
      $street = $this->district_model->get_street_byid($street);
      $street = array('street' => $street);
      $cond_where = $cond_where + $street;
    }
    //采集板块_2
    if (isset($form_param['street_2_cj']) && !empty($form_param['street_2_cj']) && $form_param['street_2_cj'] > 0) {
      $street_2 = intval($form_param['street_2_cj']);
      $street_2 = $this->district_model->get_street_byid($street_2);
      $street_2 = array('street_2' => $street_2);
      $cond_where = $cond_where + $street_2;
    }
    //采集板块_3
    if (isset($form_param['street_3_cj']) && !empty($form_param['street_3_cj']) && $form_param['street_3_cj'] > 0) {
      $street_3 = intval($form_param['street_3_cj']);
      $street_3 = $this->district_model->get_street_byid($street_3);
      $street_3 = array('street_3' => $street_3);
      $cond_where = $cond_where + $street_3;
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

  /**
   * 采集房源 出售 用于合作app
   * @access public
   * @return void
   * date 2016-8-7
   * author yuan
   */
  public function ceshi($page = 1)
  {
    //该方法不设置登录检测，根据经纪人信息设置城市分库
    $city_id = intval($this->input->get('city_id'));
    $city_data = $this->city_model->get_by_id($city_id);
    $city_spell = '';
    if (is_full_array($city_data)) {
      $city_spell = $city_data['spell'];
    }
    if (is_string($city_spell) && !empty($city_spell)) {
      $this->config->set_item('login_city', $city_spell);
      $this->load->model('collections_model_cooperate');//采集模型类
      $this->load->model('district_model');//区属模型类

      $broker_id = $this->input->get('broker_id');
      $data['conf_where'] = 'index';
      $data['where_cond'] = array();
      $data['like'] = $data['or_like'] = array();

      $arr = array("broker_id" => $broker_id, 'tbl_name' => 'sell_house_collect');
      $judge = $this->collections_model_cooperate->get_agent_house($arr);

      $house_ids = array();
      foreach ($judge as $k => $v) {
        $house_ids[] = $v['house_id'];
      }
      $data['judge'] = $judge;
      $data['broker_info'] = $broker_id;

      //get参数  ===>>>实际为post参数
      $post_param = $this->input->get(NULL, TRUE);
      $post_param['sell_type'] = $post_param['type'];
      $post_param['sell_area'] = $post_param['area'];
      $post_param['sell_price'] = $post_param['price'];
      unset($post_param['type']);
      unset($post_param['area']);
      unset($post_param['price']);
      $data['where_cond'] = $this->_get_cond_str($post_param, $city_id);

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
      $this->_total_count = $count = 1;//$this->collections_model_cooperate->get_sell_num($data['where_cond'],$data['like'],$data['or_like']);
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
      $data['blacklist'] = $this->collections_model_cooperate->get_house_sell($data['where_cond'], $data['where_in'] = array(), $data['like'], $data['or_like'], $data['order_by'], $this->_limit, $this->_offset);
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
      $info = array('count' => 0, 'read_count' => 0, 'collect_set' => array(), 'data' => array());
      //根据经纪人id查询唯一订阅条件
      $where_cond = array(
        'broker_id' => intval($broker_id),
        'type' => 'sell'
      );
      $collect_set_cooperate = $this->collections_model_cooperate->get_collect_set($where_cond);
      if (is_full_array($collect_set_cooperate[0])) {
        $info['collect_set'] = $collect_set_cooperate[0];
        $set_price = $collect_set_cooperate[0]['price'];
        $set_room = $collect_set_cooperate[0]['room'];
        $config_house = $this->house_config_model->get_config();
        if ($set_price > 0) {
          $info['collect_set']['price_name'] = $config_house['sell_price'][$set_price];
        }
        if ($set_room > 0) {
          $info['collect_set']['room_name'] = $config_house['room'][$set_room];
        }
      } else {
        $info['collect_set'] = array('id' => 0);
      }

      //get参数  ===>>>实际为post参数
      $post_param = $this->input->get(NULL, TRUE);

      //获取列表内容
      if (!empty($data['blacklist'])) {
        //数据重构
        $info['count'] = $count;
        $info['read_count'] = $read_count;
        foreach ($data['blacklist'] as $k => $v) {
          $id = $v['id'];
          $res = $this->collections_model_cooperate->get_sell_house_collect_history($id);
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
          $a['buildarea'] = intval($v['buildarea']);
          $a['price'] = intval($v['price']);
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

  /*采集统计日志
	 * $city:城市简拼
     * type:1出售2出租
     */
  private function info_count($c_broker_id, $city, $type, $house_id)
  {
    $this->load->model('broker_log_model');
    $this->load->model('broker_num_model');

    $insert_log_data = array(
      'c_broker_id' => $c_broker_id,
      'city' => $city,
      'type' => $type,
      'house_id' => $house_id,
      'dateline' => time(),
      'YMD' => date('Y-m-d')
    );
    $insert_id = $this->broker_log_model->insert($insert_log_data);
    if ($insert_id) {
      $count_num_info = $this->broker_num_model->get_one_by('city = "' . $city . '" and c_broker_id = ' . $c_broker_id . ' and YMD = ' . "'" . date('Y-m-d') . "'");
      if (is_full_array($count_num_info)) {
        //修改数据
        $update_data = array('dateline' => time());
        switch ($type) {
          case 1://出售
            $update_data['see_sell_num'] = $count_num_info['see_sell_num'] + 1;
            $update_data['see_sell'] = 1;
            break;
          case 2://出租
            $update_data['see_rent_num'] = $count_num_info['see_rent_num'] + 1;
            $update_data['see_rent'] = 1;
            break;
        }

        $row = $this->broker_num_model->update_by_id($update_data, $count_num_info['id']);
        if ($row) {
          return 'success';
        } else {
          return 'error';
        }
      } else {
        //添加数据
        $insert_num_data = array(
          'c_broker_id' => $c_broker_id,
          'city' => $city,
          'dateline' => time(),
          'YMD' => date('Y-m-d')
        );
        switch ($type) {
          case 1://出售
            $insert_num_data['see_sell_num'] = 1;
            $insert_num_data['see_sell'] = 1;
            break;
          case 2://出租
            $insert_num_data['see_rent_num'] = 1;
            $insert_num_data['see_rent'] = 1;
            break;
        }
        $insert_num_id = $this->broker_num_model->insert($insert_num_data);
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

  //采集查看量
  public function lists_num()
  {
    $this->load->model('broker_num_model');
    $cond_where = $this->input->post(null, true);
    $sql = 'select c_broker_id,SUM(see_sell_num) AS see_sell_num,SUM(see_rent_num) AS see_rent_num,YMD from c_broker_num';

    $sql .= ' where id>0';
    if (isset($cond_where['where']['c_broker_id']) && $cond_where['where']['c_broker_id']) {
      $sql .= ' and c_broker_id = ' . $cond_where['where']['c_broker_id'];
    }
    if (isset($cond_where['where']['dateline >=']) && $cond_where['where']['dateline >='] && isset($cond_where['where']['dateline <=']) && $cond_where['where']['dateline <=']) {
      $sql .= ' and dateline >=' . $cond_where['where']['dateline >='] . ' and dateline <=' . $cond_where['where']['dateline <='];
    }
    $sql .= ' group by YMD,c_broker_id order by id desc';
    $count_sql = 'SELECT COUNT(*) AS total_count FROM (' . $sql . ') AS broker_num';
    if (empty($cond_where['offset'])) {
      $cond_where['offset'] = 0;
    }
    if (empty($cond_where['limit'])) {
      $cond_where['limit'] = 30;
    }
    $sql .= ' limit ' . $cond_where['offset'] * $cond_where['limit'] . ',' . $cond_where['limit'];
    //符合条件的总行数
    $count_data = $this->broker_num_model->query($count_sql);
    $count_array = $count_data->result();
    $data['total_count'] = $count_array[0]->total_count;
    $broker_nun_data = $this->broker_num_model->query($sql);
    $data['lists'] = $broker_nun_data->result();
    $this->result('1', '获取成功', $data);
  }

  //采集查看人数
  public function lists_count()
  {
    $this->load->model('broker_num_model');
    $cond_where = $this->input->post(null, true);
    $sql = 'select SUM(see_sell) AS see_sell,SUM(see_rent) AS see_rent,YMD from c_broker_num';

    if (isset($cond_where['where']['dateline >=']) && $cond_where['where']['dateline >='] && isset($cond_where['where']['dateline <=']) && $cond_where['where']['dateline <=']) {
      $sql .= ' where dateline >=' . $cond_where['where']['dateline >='] . ' and dateline <=' . $cond_where['where']['dateline <='];
    }

    $sql .= " where city = '" . $cond_where['where']['city'] . "'";

    $sql .= ' group by YMD order by id desc';

    $count_sql = 'SELECT COUNT(*) AS total_count FROM (' . $sql . ') AS broker_num';
    if (empty($cond_where['offset'])) {
      $cond_where['offset'] = 0;
    }
    if (empty($cond_where['limit'])) {
      $cond_where['limit'] = 30;
    }
    $sql .= ' limit ' . $cond_where['offset'] * $cond_where['limit'] . ',' . $cond_where['limit'];
    //符合条件的总行数
    $count_data = $this->broker_num_model->query($count_sql);
    $count_array = $count_data->result();
    $data['total_count'] = $count_array[0]->total_count;
    $broker_nun_data = $this->broker_num_model->query($sql);
    $data['lists'] = $broker_nun_data->result();
    $this->result('1', '获取成功', $data);
  }
}

/* End of file house_collections.php */
/* Location: ./application/mls/controllers/house_collections.php */
