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
class House_collections_nj extends MY_Controller
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
    $this->load->model('broker_model');  //经纪人模型类
    $this->load->model('house_config_model');  //房源配置模型类

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
    $broker_id = $this->input->post('broker_id');
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    $data['like'] = $data['or_like'] = array();
    $arr = array("broker_id" => $broker_id, 'tbl_name' => 'sell_house_collect');
    $judge = $this->collections_model_nj->get_agent_house($arr);
    $house_ids = array();
    foreach ($judge as $k => $v) {
      $house_ids[] = $v['house_id'];
    }
    $data['judge'] = $judge;
    $data['broker_info'] = $broker_id;

    //get参数  ===>>>实际为post参数
    $post_param = $this->input->post(NULL, TRUE);
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
    $judge = $this->collections_model_nj->get_agent_house($arr);
    //符合条件的总行数
    $this->_total_count = $count = $this->collections_model_nj->get_sell_num($data['where_cond'], $data['like'], $data['or_like']);
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
    $data['blacklist'] = $this->collections_model_nj->get_house_sell($data['where_cond'], $data['where_in'] = array(), $data['like'], $data['or_like'], $data['order_by'], $this->_limit, $this->_offset);
    //$data['blacklist_all'] = $this->collections_model_nj->get_house_sell_allids();
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
          $a['createtime'] = '今天：' . date('H:i:s', $v['createtime']);
        } else {
          //24小时以前
          $a['createtime'] = date('Y-m-d', $v['createtime']);
        }
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
   * 好房看看---出租房源
   * @access public
   * @return void
   * date 2014-12-28
   * author angel_in_us
   */
  public function collect_rent($page = 1)
  {
    $broker_id = $this->input->post('broker_id');
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    $data['like'] = $data['or_like'] = array();

    $arr = array("broker_id" => $broker_id, 'tbl_name' => 'rent_house_collect');
    $judge = $this->collections_model_nj->get_agent_house($arr);
    $house_ids = array();
    foreach ($judge as $k => $v) {
      $house_ids[] = $v['house_id'];
    }
    $data['judge'] = $judge;
    $data['broker_info'] = $broker_id;

    //get参数   ===>>>实际为post参数
    $get_param = $this->input->post(NULL, TRUE);
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
    $post_param = $this->input->post(NULL, TRUE);
    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($page);
    //$this->_init_pagination($page);
    //已查看
    $arr = array("broker_id" => $broker_id, 'tbl_name' => 'rent_house_collect');
    $judge = $this->collections_model_nj->get_agent_house($arr);
    //符合条件的总行数
    $this->_total_count = $count = $this->collections_model_nj->get_rent_num($data['where_cond'], $data['like'], $data['or_like']);
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
    $data['blacklist'] = $this->collections_model_nj->get_house_rent($data['where_cond'], $data['where_in'] = array(), $data['like'], $data['or_like'], $data['order_by'], $this->_offset, $this->_limit);
    //$data['blacklist_all'] = $this->collections_model_nj->get_house_rent_allids();

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
          $a['createtime'] = '今天：' . date('H:i:s', $v['createtime']);
        } else {
          //24小时以前
          $a['createtime'] = date('Y-m-d', $v['createtime']);
        }
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
    $house_id = $this->input->get('house_id');

    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    //根据房源house_id去查询房源详情
    if (!empty($house_id)) {
      $data['where_cond'] = array('id' => $house_id);
      $data['house_info'] = $this->collections_model_nj->get_housesell_byid($data['where_cond']);
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
   * 好房看看---出售房源
   * @access public
   * @return void
   * date 2015-01-09
   * author angel_in_us
   */
  public function good_sell_phone()
  {
    if ($this->user_arr['group_id'] != 2) {
      $arr = array("broker_id" => $this->user_arr['broker_id'], 'tbl_name' => 'sell_house_collect');
      $judge = $this->collections_model_nj->get_agent_house($arr);
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
      $result = $this->collections_model_nj->check_agent_house($where);
      $ahouse_info = array(
        'house_id' => $house_id,
        'broker_id' => $broker_id,
        'tbl_name' => $tbl_name,
        'is_input' => 0,
        'createtime' => time()
      );
      if (!empty($result)) {

      } else {
        $this->collections_model_nj->add_agent_house($ahouse_info);
      }
    }
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    //根据房源house_id去查询房源详情
    if (!empty($house_id)) {
      $data['where_cond'] = array('id' => $house_id);
      $data['house_info'] = $this->collections_model_nj->get_housesell_byid($data['where_cond']);
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
    $house_id = $this->input->get('house_id');
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    if (!empty($house_id)) {
      $data['where_cond'] = array('id' => $house_id);
      $data['house_info'] = $this->collections_model_nj->get_houserent_byid($data['where_cond']);//获取获取房源详情
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
   * 好房看看---出售房源
   * @access public
   * @return void
   * date 2015-01-09
   * author angel_in_us
   */
  public function good_rent_phone()
  {
    if ($this->user_arr['group_id'] != 2) {
      $arr = array("broker_id" => $this->user_arr['broker_id'], 'tbl_name' => 'rent_house_collect');
      $judge = $this->collections_model_nj->get_agent_house($arr);
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
      $result = $this->collections_model_nj->check_agent_house($where);
      $ahouse_info = array(
        'house_id' => $house_id,
        'broker_id' => $broker_id,
        'tbl_name' => $tbl_name,
        'is_input' => 0,
        'createtime' => time()
      );
      if (!empty($result)) {

      } else {
        $this->collections_model_nj->add_agent_house($ahouse_info);
      }
    }
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    //根据房源house_id去查询房源详情
    if (!empty($house_id)) {
      $data['where_cond'] = array('id' => $house_id);
      $data['house_info'] = $this->collections_model_nj->get_houserent_byid($data['where_cond']);
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
    return $cond_where;

  }
}

/* End of file house_collections.php */
/* Location: ./application/mls/controllers/house_collections.php */
