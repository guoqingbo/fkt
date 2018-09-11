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
class Rent extends MY_Controller
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
    $this->load->model('pic_model');
    //加载区属模型类
    $this->load->model('district_model');
    //加载楼盘模型类
    $this->load->model('community_model');
    //表单验证
    $this->load->library('form_validation');
    $this->load->model('buy_customer_model');
    //加载客户MODEL
    $this->load->model('follow_model');
    $this->load->model('rent_house_model');
    $this->load->model('house_config_model');
    $this->load->model('broker_model');
    $this->load->model('house_collect_model');
    $this->load->model('api_broker_model');
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


  //判断重复房源
  public function check_unique_house()
  {
    $msg = 0;
    $house_num = 0;

    $block_id = $this->input->get('block_id', TRUE);
    $door = $this->input->get('door', TRUE);
    $unit = $this->input->get('unit', TRUE);
    $dong = $this->input->get('dong', TRUE);

    if (!empty($block_id) && !empty($door) && !empty($unit) && !empty($dong)) {
      //经纪人信息
      $broker_info = $this->user_arr;
      //根据经济人总公司编号获取全部分店信息
      $company_id = intval($broker_info['company_id']);//获取总公司编号
      //获取全部分公司信息
      $agency_list = $this->api_broker_model->get_agencys_by_company_id($company_id);

      $arr_agency_id = array();
      foreach ($agency_list as $key => $val) {
        $arr_agency_id[] = $val['agency_id'];
      }
      $agency_ids = implode(',', $arr_agency_id);
      $cond_where = "status != 5 and agency_id in (" . $agency_ids . ") and block_id = $block_id and door = $door and unit = $unit and dong = $dong ";
      $tbl = "rent_house";
      $house_tbl = $this->rent_house_model->set_tbl($tbl);
      $house_num = $this->rent_house_model->get_housenum_by_cond($cond_where);
    }

    $msg = $house_num > 0 ? 1 : 0;

    echo $msg;


  }


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
    $tbl = "rent_house";
    $this->rent_house_model->set_tbl($tbl);
    $house_num = $this->rent_house_model->get_housenum_by_cond($cond_where);

    return $house_num;
  }

  /**
   * 添加出租信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function add()
  {
    //获得基本设置数据
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      $house_customer_system = intval($company_basic_data['house_customer_system']);
      $rent_house_private_num = intval($company_basic_data['rent_house_private_num']);
    } else {
      $house_customer_system = $rent_house_private_num = 0;
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
    $deviceid = $this->input->post('deviceid', TRUE);
    $datainfo['sell_type'] = $this->input->post('sell_type', TRUE);
    $datainfo['block_name'] = $this->input->post('block_name', TRUE);
    $datainfo['block_id'] = $this->input->post('block_id', TRUE);
    $datainfo['district_id'] = $this->input->post('district_id', TRUE);
    $datainfo['street_id'] = $this->input->post('street_id', TRUE);
    $datainfo['address'] = $this->input->post('address', TRUE);
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
    $datainfo['video_id'] = $this->input->post('video_id', TRUE);
    $datainfo['video_pic'] = $this->input->post('video_pic', TRUE);
    $rent_tag = $this->input->post('rent_tag', TRUE);
    if (!empty($rent_tag)) {
      $datainfo['rent_tag'] = trim($rent_tag, ',');
    }

    //判断是否开启了合作审核
    if (isset($company_basic_data['check_cooperate']) && '1' == $company_basic_data['check_cooperate']) {
      if (isset($datainfo['isshare']) && '1' == $datainfo['isshare']) {
        $datainfo['isshare'] = 2;
      }
    }
    if (isset($datainfo['isshare']) && $datainfo['isshare'] > 0) {
      //设置合作时间，合作状态1和2
      $datainfo['set_share_time'] = time();
    }
    $datainfo['price'] = $this->input->post('price', TRUE);
    $datainfo['price_danwei'] = $this->input->post('price_danwei', TRUE);
    $datainfo['room'] = $this->input->post('room', TRUE);
    $datainfo['hall'] = $this->input->post('hall', TRUE);
    $datainfo['toilet'] = $this->input->post('toilet', TRUE);
    $datainfo['floor_type'] = $this->input->post('floor_type', TRUE);
    $datainfo['floor'] = $this->input->post('floor', TRUE);
    $datainfo['forward'] = $this->input->post('forward', TRUE);
    $datainfo['fitment'] = $this->input->post('fitment', TRUE);
    $datainfo['buildyear'] = $this->input->post('buildyear', TRUE);
    $datainfo['buildarea'] = $this->input->post('buildarea', TRUE);
    $datainfo['keys'] = $this->input->post('keys', TRUE);
    $datainfo['rententrust'] = $this->input->post('rententrust', TRUE);
    $shinei_url = $this->input->post('shinei_url', TRUE);//室内图片
    $huxing_url = $this->input->post('huxing_url', TRUE);//户型图片
    $datainfo['is_outside'] = $this->input->post('is_outside', TRUE);//是否同步外网

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
      $private_num = $this->rent_house_model->get_housenum_by_cond($private_where_cond);
      if ('1' == $datainfo['nature'] && $private_num >= $rent_house_private_num) {
        $house_private_check = false;
        $house_private_check_text = '当前门店基本设置为公盘制';
      }
    } else {
      $house_private_check = true;
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
    if ($datainfo['price_danwei'] == 1) {
      $datainfo['price'] = 30 * $datainfo['buildarea'] * $datainfo['price'];
    }
    $house_num = 0;
    $house_id = 0;
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
      //积分和提示语
      $credit_msg = '';
      $credit_score = 0;
      $level_score = 0;
      if ($house_num == 0 && $house_private_check) {
        //是否需要同步到外网
        $is_rsync_outer = $datainfo['is_outside'] == 1 ? $this->fang100($datainfo) : true;
        if ($is_rsync_outer === true) {
          $datainfo['is_outside_time'] == time();
          $data['open_cooperate'] = $company_basic_data['open_cooperate'];
          if ('0' === $data['open_cooperate'] && 1 == $datainfo['isshare']) {
            $this->result('-1', '当前公司尚未开启合作中心');
            exit();
          }

          $house_id = $this->rent_house_model->add_rent_house_info($datainfo);
          $cid = $this->input->post('cid', TRUE);
          if ($cid > 0) {
            $this->load->model('collections_model_new');
            $this->collections_model_new->change_house_status_byid($cid, $broker_id, 'rent_house_collect');
            $this->collections_model_new->add_rent_house_sub($house_id, $cid);
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
            $add_log_param['text'] = '出租房源 ' . 'CZ' . $house_id;
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
            $needarr['type'] = 2;
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
              $needarrt['type'] = 2;
              $needarrt['agency_id'] = $this->user_arr['agency_id'];//门店ID
              $needarrt['company_id'] = $this->user_arr['company_id'];//总公司id
              $needarrt['house_id'] = $house_id;
              $needarrt['text'] = $follow_text;
              $boolt = $this->follow_model->house_inster_share($needarrt);
            }

            //出租房源录入成功记录工作统计日志
            $this->info_count($house_id, 1);
          }

          if ($house_id > 0 && $datainfo['keys'] && $datainfo['key_number']) {
            //添加钥匙
            $this->add_key($house_id, $datainfo['key_number'], 'add');

            //出售房源录入成功记录工作统计日志-钥匙提交
            $this->info_count($house_id, 6);
          }
          //合作加积分
          if ($house_id > 0 && $datainfo['isshare'] == 1) {
            $a_ratio = $this->input->post('a_ratio', TRUE);//甲方佣金分成比例
            $b_ratio = $this->input->post('b_ratio', TRUE);//已方佣金分成比例
            $buyer_ratio = $this->input->post('buyer_ratio', TRUE);//买方支付佣金比例
            $seller_ratio = $this->input->post('seller_ratio', TRUE);//卖方支付佣金比例
            $this->load->model('rent_house_share_ratio_model');
            $this->rent_house_share_ratio_model->add_house_cooperate_ratio($house_id, $seller_ratio, $buyer_ratio, $a_ratio, $b_ratio);
            //增加积分
            $this->load->model('api_broker_credit_model');
            $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_id));
            $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $house_id), 2);
            //判断积分是否增加成功
            if (is_full_array($credit_result) && $credit_result['status'] == 1) {
              $credit_score = $credit_result['score'];
            }
            //增加等级分值
            $this->load->model('api_broker_level_model');
            $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_id));
            $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $house_id), 2);
            //判断成长值是否增加成功
            if (is_full_array($level_result) && $level_result['status'] == 1) {
              $level_score = $level_result['score'];
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
            $add_log_param['text'] = '出租房源 ' . 'CZ' . $house_id;
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
        }
      }
      if ($house_id > 0) {
        $pic_arr = array();
        $shinei_pic_id = '';
        if ($shinei_url) {
          //室内图片
          $shinei_arr = array();
          $shinei_url = json_decode($shinei_url);
          foreach ($shinei_url as $key => $val) {
            $shinei_arr['tbl'] = 'rent_house';
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

        $this->rent_house_model->set_id($house_id);
        if ($datainfo['video_id'] && $datainfo['video_pic']) {
          $pic_arr['house_level'] = 6;
        } elseif ($shinei_url && $huxing_url) {
          $pic_arr['house_level'] = count($shinei_url) >= 3 ? 3 : 2;
          $pic_arr['pic'] = $shinei_url ? $shinei_url[0] : $huxing_url[0];
          $pic_arr['pic_tbl'] = 'upload';
          $pic_arr['pic_ids'] = $shinei_pic_id . $huxing_pic_id;
        } else if (!$shinei_url && !$huxing_url) {
          $pic_arr['house_level'] = 0;
        } else {
          $pic_arr['house_level'] = 1;
          $pic_arr['pic'] = $shinei_url ? $shinei_url[0] : $huxing_url[0];
          $pic_arr['pic_tbl'] = 'upload';
          $pic_arr['pic_ids'] = $shinei_pic_id . $huxing_pic_id;
        }
        $result = $this->rent_house_model->update_info_by_id($pic_arr);
        //同步加积分
        if ($datainfo['is_outside'] == 1) {
          //获得当前数据所属的经纪人id
          $this->rent_house_model->set_search_fields(array('id', 'broker_id', 'is_outside', 'status', 'pic_tbl', 'pic_ids'));
          $this->rent_house_model->set_id($house_id);
          $owner_arr = $this->rent_house_model->get_info_by_id();
          //增加积分
          $this->load->model('api_broker_credit_model');
          $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_id));
          $credit_result = $this->api_broker_credit_model->rsync_fang100($owner_arr, 2);
          //判断积分是否增加成功
          if (is_full_array($credit_result) && $credit_result['status'] == 1) {
            $credit_score += $credit_result['score'];
          }
          //增加等级分值
          $this->load->model('api_broker_level_model');
          $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_id));
          $level_result = $this->api_broker_level_model->rsync_fang100($owner_arr, 2);
          //判断成长值是否增加成功
          if (is_full_array($level_result) && $level_result['status'] == 1) {
            $level_score += $level_result['score'];
          }
        }
        //合作是否加积分
        if ($credit_score != 0) {
          $credit_msg = '+' . $credit_score . '积分';
        }
        //合作是否加成长值
        if ($level_score != 0) {
          $credit_msg .= ',+' . $level_score . '积分';
        }
      }
    }
    $data = array();
    if ($house_num > 0) {
      $house_id = 0;
    }
    $data['house_num'] = $house_num;
    $data['house_id'] = $house_id;
    if ($house_num > 0) {
      $this->result(0, '操作失败，该房源已经存在', $data);
    } else if (!$house_private_check) {
      $this->result(0, $house_private_check_text);
    } else if ($is_rsync_outer === 'no_verified') {
      $this->result(0, '您当前没有认证资料，无法同步，请关闭同步功能新录入');
    } else if ($is_rsync_outer === 'no_permission') {
      $this->result(0, '非本人房源无法同步，同步失败');
    } else if ($is_rsync_outer === 'status_failed') {
      $this->result(0, '该房源为非有效房源，无法同步，请修改房源状态');
    } else if ($house_id == 0 && $house_num == 0) {
      $this->result(0, '操作失败', $data);
    } else if ($house_num == 0 && $house_id > 0) {
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

    //获得当前数据所属的经纪人id
    $this->rent_house_model->set_search_fields(array('id', 'broker_id', 'is_outside', 'status', 'pic_tbl', 'pic_ids'));
    $this->rent_house_model->set_id($house_id);
    $owner_arr = $this->rent_house_model->get_info_by_id();
    $validate_status = $this->fang100($owner_arr);
    if ($validate_status == true) {
      $is_outside_time = $flag == 1 ? time() : 0;
      $update_info = array('is_outside' => $flag, 'is_outside_time' => $is_outside_time);
      $this->rent_house_model->set_id($house_id);
      $update_status = $this->rent_house_model->update_info_by_id($update_info);
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
        $this->load->model('api_broker_credit_model');
        $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
        $credit_result = $this->api_broker_credit_model->rsync_fang100($owner_arr, 2);
        if (is_full_array($credit_result) && $credit_result['status']) {
          $credit_msg .= '+' . $credit_result['score'] . '积分';
        }
        $this->load->model('api_broker_level_model');
        $this->api_broker_level_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
        $level_result = $this->api_broker_level_model->rsync_fang100($owner_arr, 2);
        if (is_full_array($level_result) && $level_result['status']) {
          $credit_msg .= ',+' . $level_result['score'] . '成长值';
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
    $result_num = $this->rent_house_model->get_housenum_by_cond($where_cond);
    return $result_num;
  }

  /**
   * 修改出租信息
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function modify()
  {
    //获得基本设置数据
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      $house_customer_system = intval($company_basic_data['house_customer_system']);
      $rent_house_private_num = intval($company_basic_data['rent_house_private_num']);
    } else {
      $house_customer_system = $rent_house_private_num = 0;
    }
    $data = array();
    //获取当前登录人信息
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    $house_id = $this->input->post('house_id', TRUE);
    $house_id = intval($house_id);

    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $this->rent_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->rent_house_model->set_id($house_id);
    $owner_arr = $this->rent_house_model->get_info_by_id();
    //是否有修改房源权限
    $house_modify_per = $this->broker_permission_model->check('7', $owner_arr);
    //修改出租房源关联门店权限
    $agency_house_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '3');
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
          $config['status'][$k] = '暂不租';
        }
      }
    }
    //单条房源数据
    $fileld = array('sell_type', 'block_id', 'district_id', 'street_id', 'nature', 'forward', 'fitment', 'status', 'rententrust', 'address', 'dong', 'unit', 'door', 'owner', 'telno1', 'title', 'bewrite', 'isshare', 'buildarea', 'price', 'room', 'hall', 'toilet', 'floor', 'subfloor', 'totalfloor', 'buildyear', 'keys', 'pic', 'floor_type', 'price_danwei', 'block_name', 'createtime', 'updatetime', 'pic_ids', 'pic_tbl', 'is_outside', 'isshare_friend', 'broker_id', 'rent_tag', 'set_share_time', 'video_id', 'video_pic');
    $this->rent_house_model->set_search_fields($fileld);
    $this->rent_house_model->set_id($house_id);
    $house_detail = $this->rent_house_model->get_info_by_id();

    //根据权限role_id获得当前经纪人的角色，判断店长以上or店长以下
    $role_level = intval($broker_info['role_level']);
    //店长以下的经纪人不允许操作他人的私盘
    if (is_int($role_level) && $role_level > 6) {
      if ($owner_arr['broker_id'] != $broker_info['broker_id'] && $house_detail['nature'] == '1') {
        $this->result('-1', '店长以下的经纪人不允许操作他人的私盘');
        exit();
      }
    }

    //获取板块区属
    $district_name = $this->district_model->get_distname_by_id($house_detail['district_id']);//区属名称
    $street_name = $this->district_model->get_streetname_by_id($house_detail['street_id']);//板块名称
    $sell_type = $config['sell_type'][$house_detail['sell_type']];//出售类型
    $house_detail['sell_type'] = array('key' => $house_detail['sell_type'], 'name' => $sell_type);
    $house_detail['cmt_info'] = array('cmt_id' => $house_detail['block_id'], 'cmt_name' => $house_detail['block_name'], 'dist_id' => $house_detail['district_id'], 'districtname' => $district_name, 'streetid' => $house_detail['street_id'], 'streetname' => $street_name);
    $house_detail['status'] = array('key' => $house_detail['status'], 'name' => $config['status'][$house_detail['status']]);
    $house_detail['nature'] = array('key' => $house_detail['nature'], 'name' => $config['nature'][$house_detail['nature']], 'is_change_per' => $is_nature_change_per);
    $house_detail['rent_tag_result'] = array();
    if (!empty($house_detail['rent_tag'])) {
      $rent_tag_arr = explode(',', $house_detail['rent_tag']);
      if (is_full_array($rent_tag_arr)) {
        foreach ($rent_tag_arr as $k => $v) {
          $house_detail['rent_tag_result'][] = array('key' => $v, 'name' => $config['rent_tag'][$v]);
        }
      }
    }

    if ($house_detail['rententrust']) {
      $house_detail['rententrust'] = array('key' => $house_detail['rententrust'], 'name' => $config['rententrust'][$house_detail['rententrust']]);
    } else {
      $house_detail['rententrust'] = array('key' => '-1', 'name' => '暂无数据');
    }

    $house_detail['forward'] = array('key' => $house_detail['forward'], 'name' => $config['forward'][$house_detail['forward']]);
    $house_detail['fitment'] = array('key' => $house_detail['fitment'], 'name' => $config['fitment'][$house_detail['fitment']]);
    $house_detail['createtime'] = date('Y-m-d H:i:s', $house_detail['createtime']);
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
    $this->load->model('rent_house_share_ratio_model');
    $ratio_info = $this->rent_house_share_ratio_model->get_house_ratio_by_rowid($house_id);
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

    if ($house_detail['price_danwei'] == 1 && $house_detail['price']) {
      $house_detail['price'] = (string)($house_detail['price'] / $house_detail['buildarea'] / 30);
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
    $deviceid = $this->input->post('deviceid', TRUE);
    $datainfo['sell_type'] = $this->input->post('sell_type', TRUE);
    $datainfo['block_name'] = $this->input->post('block_name', TRUE);
    $datainfo['block_id'] = $this->input->post('block_id', TRUE);
    $datainfo['district_id'] = $this->input->post('district_id', TRUE);
    $datainfo['street_id'] = $this->input->post('street_id', TRUE);
    $datainfo['address'] = $this->input->post('address', TRUE);
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
    $rent_tag = $this->input->post('rent_tag', TRUE);
    if (!empty($rent_tag)) {
      $datainfo['rent_tag'] = trim($rent_tag, ',');
    }

    //判断是否开启了合作审核
    if (isset($company_basic_data['check_cooperate']) && '1' == $company_basic_data['check_cooperate']) {
      if (isset($datainfo['isshare']) && '1' == $datainfo['isshare']) {
        $datainfo['isshare'] = 2;
      }
    }
    if (isset($datainfo['isshare']) && $datainfo['isshare'] > 0) {
      //设置合作时间，合作状态1和2
      $datainfo['set_share_time'] = time();
    }
    $datainfo['price'] = $this->input->post('price', TRUE);
    $datainfo['price_danwei'] = $this->input->post('price_danwei', TRUE);
    $datainfo['room'] = $this->input->post('room', TRUE);
    $datainfo['hall'] = $this->input->post('hall', TRUE);
    $datainfo['toilet'] = $this->input->post('toilet', TRUE);
    $datainfo['floor'] = $this->input->post('floor', TRUE);
    $datainfo['forward'] = $this->input->post('forward', TRUE);
    $datainfo['fitment'] = $this->input->post('fitment', TRUE);
    $datainfo['buildyear'] = $this->input->post('buildyear', TRUE);
    $datainfo['buildarea'] = $this->input->post('buildarea', TRUE);
    $datainfo['keys'] = $this->input->post('keys', TRUE);
    $datainfo['rententrust'] = $this->input->post('rententrust', TRUE);
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
    if ($datainfo['price_danwei'] == 1 && $datainfo['price']) {
      $datainfo['price'] = 30 * $datainfo['buildarea'] * $datainfo['price'];
    }
    $datainfo['is_outside'] = $this->input->post('is_outside', TRUE);
    $result = 0;
    //积分和提示语
    $credit_msg = '';
    $credit_score = 0;
    $level_score = 0;

    //基本设置，房客源制判断
    $this->rent_house_model->set_id($house_id);
    $sell_backinfo = $this->rent_house_model->get_info_by_id();//修改前的信息
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
      $private_num = $this->rent_house_model->get_housenum_by_cond($private_where_cond);
      if ('2' == $sell_backinfo['nature'] && '1' == $datainfo['nature'] && $private_num >= $rent_house_private_num) {
        $house_private_check = false;
        $house_private_check_text = '当前门店基本设置为公盘制';
      }
    } else {
      $house_private_check = true;
    }

    if ($house_id && $datainfo['sell_type'] && $datainfo['block_name'] && $house_private_check) {
      //判断楼层要大于0
      if ($datainfo['floor'] <= 0 || $datainfo['totalfloor'] <= 0) {
        $this->result(0, '楼层必须大于0');
        exit();
      }
      $this->rent_house_model->set_id($house_id);
      $sell_backinfo = $this->rent_house_model->get_info_by_id();//修改前的信息
      if (1 == $sell_backinfo['isshare'] || 2 == $sell_backinfo['isshare']) {
        $datainfo['set_share_time'] = $sell_backinfo['set_share_time'];
      }

      $datainfo['broker_id'] = $sell_backinfo['broker_id'];
      if ($sell_backinfo['is_outside'] != $datainfo['is_outside']
        && $datainfo['is_outside'] == 1
      ) {
        $is_rsync_outer = $this->fang100($datainfo);
        $datainfo['is_outside_time'] == time();
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

        $result = $this->rent_house_model->update_info_by_id($datainfo);
        $sell_dataifno = $this->rent_house_model->get_info_by_id();//修改过后信息

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
        $rent_cont = $this->insetmatch($sell_backinfo, $sell_dataifno);
        //修改房源日志录入
        $this->load->model('follow_model');
        $needarrt = array();
        $needarrt['broker_id'] = $this->user_arr['broker_id'];
        $needarrt['type'] = 2;
        $needarrt['agency_id'] = $this->user_arr['agency_id'];//门店ID
        $needarrt['company_id'] = $this->user_arr['company_id'];//总公司id
        $needarrt['house_id'] = $house_id;
        $needarrt['text'] = $rent_cont;
        if (!empty($rent_cont)) {
          $boolt = $this->follow_model->house_save($needarrt);
          if (is_int($boolt) && $boolt > 0) {
            //判断该跟进距离上一次是否已超过基本设置天数，录入出售房源附表
            //获得基本设置房源跟进的天数
            //获取当前经济人所在公司的基本设置信息
            $this->load->model('house_customer_sub_model');
            $house_follow_day = intval($company_basic_data['house_follow_spacing_time']);

            $select_arr = array('id', 'house_id', 'date');
            $this->follow_model->set_select_fields($select_arr);
            $where_cond = 'house_id = "' . $house_id . '" and follow_type != 2 and type = 2';
            $last_follow_data = $this->follow_model->get_lists($where_cond, 0, 2, 'date');
            if (count($last_follow_data) == 2) {
              $time1 = $last_follow_data[0]['date'];
              $time2 = $last_follow_data[1]['date'];
              $date1 = date('Y-m-d', strtotime($time1));
              $date2 = date('Y-m-d', strtotime($time2));
              $differ_day = (strtotime($date1) - strtotime($date2)) / (24 * 3600);
              if ($differ_day < $house_follow_day) {
                $this->house_customer_sub_model->add_rent_house_sub($house_id, 0);
              } else {
                $this->house_customer_sub_model->add_rent_house_sub($house_id, 1);
              }
            }
          }
        }

        //出租房源修改成功记录工作统计日志
        if ($rent_cont) {
          $this->info_count($house_id, 2);
        }

        //修改佣金比例
        if ($datainfo['isshare'] == 1) {
          $a_ratio = $this->input->post('a_ratio', TRUE);//甲方佣金分成比例
          $b_ratio = $this->input->post('b_ratio', TRUE);//已方佣金分成比例
          $buyer_ratio = $this->input->post('buyer_ratio', TRUE);//买方支付佣金比例
          $seller_ratio = $this->input->post('seller_ratio', TRUE);//卖方支付佣金比例
          $this->load->model('sell_house_share_ratio_model');
          if ($a_ratio && $b_ratio && $buyer_ratio && $seller_ratio) {
            $this->rent_house_share_ratio_model->update_house_ratio_by_rowid($house_id, $seller_ratio, $buyer_ratio, $a_ratio, $b_ratio);
          }
          //增加积分
          $this->load->model('api_broker_credit_model');
          $this->api_broker_credit_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
          $credit_result = $this->api_broker_credit_model->publish_cooperate_house(array('id' => $house_id), 2);
          //判断积分是否增加成功
          if (is_full_array($credit_result) && $credit_result['status'] == 1) {
            $credit_score = $credit_result['score'];
          }
          //增加等级分值
          $this->load->model('api_broker_level_model');
          $this->api_broker_level_model->set_broker_param(array('broker_id' => $this->user_arr['broker_id']));
          $level_result = $this->api_broker_level_model->publish_cooperate_house(array('id' => $house_id), 2);
          //判断成长值是否增加成功
          if (is_full_array($level_result) && $level_result['status'] == 1) {
            $level_score = $level_result['score'];
          }
        }
        if ($datainfo['isshare'] == 0) {
          //取消合作后，终止与房源有关系的合作
          $stop_reason = 'private_house';
          $this->load->model('cooperate_model');
          $this->cooperate_model->stop_cooperate($house_id, 'rent', $stop_reason);

        }
        //添加钥匙
        if (!$sell_backinfo['keys'] && $sell_dataifno['keys'] && $sell_dataifno['key_number']) {
          $this->add_key($house_id, $sell_dataifno['key_number'], 'update');

          //出售房源录入成功记录工作统计日志-钥匙提交
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
            $shinei_arr['tbl'] = 'rent_house';
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
            $huxing_arr['tbl'] = 'rent_house';
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

        $this->rent_house_model->set_id($house_id);
        if ($datainfo['video_id'] && $datainfo['video_pic']) {
          $pic_arr['house_level'] = 6;
        } elseif ($shinei_url && $huxing_url) {
          $pic_arr['house_level'] = count($shinei_url) >= 3 ? 3 : 2;
          $pic_arr['pic'] = $shinei_url ? $shinei_url[0] : $huxing_url[0];
          $pic_arr['pic_tbl'] = 'upload';
          $pic_arr['pic_ids'] = $shinei_pic_id . $huxing_pic_id;
        } else if (!$shinei_url && !$huxing_url) {
          $pic_arr['house_level'] = 0;
        } else {
          $pic_arr['house_level'] = 1;
          $pic_arr['pic'] = $shinei_url ? $shinei_url[0] : $huxing_url[0];
          $pic_arr['pic_tbl'] = 'upload';
          $pic_arr['pic_ids'] = $shinei_pic_id . $huxing_pic_id;
        }
        $this->rent_house_model->update_info_by_id($pic_arr);
        //同步加积分
        if ($datainfo['is_outside'] == 1) {
          //获得当前数据所属的经纪人id
          $this->rent_house_model->set_search_fields(array('id', 'broker_id', 'is_outside', 'status', 'pic_tbl', 'pic_ids'));
          $this->rent_house_model->set_id($house_id);
          $owner_arr = $this->rent_house_model->get_info_by_id();
          //增加积分
          $this->load->model('api_broker_credit_model');
          $this->api_broker_credit_model->set_broker_param(array('broker_id' => $broker_info['broker_id']));
          $credit_result = $this->api_broker_credit_model->rsync_fang100($owner_arr, 2);
          //判断积分是否增加成功
          if (is_full_array($credit_result) && $credit_result['status'] == 1) {
            $credit_score += $credit_result['score'];
          }
          //增加等级分值
          $this->load->model('api_broker_level_model');
          $this->api_broker_level_model->set_broker_param(array('broker_id' => $broker_info['broker_id']));
          $level_result = $this->api_broker_level_model->rsync_fang100($owner_arr, 2);
          //判断成长值是否增加成功
          if (is_full_array($level_result) && $level_result['status'] == 1) {
            $level_score += $level_result['score'];
          }
        }
        //合作是否加积分
        if ($credit_score != 0) {
          $credit_msg = '+' . $credit_score . '积分';
        }
        //合作是否加成长值
        if ($level_score != 0) {
          $credit_msg = ',+' . $level_score . '成长值';
        }
      }

    }

    //$data['result_id']=$result;
    if ($house_id && empty($datainfo['sell_type'])) {
      $this->result('1', '获取房源信息成功', $data['house_detail']);
    } else if (!$house_private_check) {
      $this->result(0, $house_private_check_text);
    }
    if ($is_rsync_outer === 'no_verified') {
      $this->result(0, '您当前没有认证资料，无法同步，请关闭同步功能新录入');
    } else if ($is_rsync_outer === 'no_permission') {
      $this->result(0, '非本人房源无法同步，同步失败');
    } else if ($is_rsync_outer === 'status_failed') {
      $this->result(0, '该房源为非有效房源，无法同步，请修改房源状态');
    } else if ($result < 0 && $datainfo['sell_type'] && $house_id) {
      $this->result('0', '房源修改失败');
    } else if ($house_id && $datainfo['sell_type'] && $result > 0) {
      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $broker_info['company_id'];
      $add_log_param['agency_id'] = $broker_info['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $broker_info['truename'];
      $add_log_param['type'] = 3;
      $add_log_param['text'] = '出租房源 ' . 'CZ' . $house_id . ' ' . $rent_cont;
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
        $add_log_param['broker_id'] = $this->user_arr['broker_id'];
        $add_log_param['broker_name'] = $broker_info['truename'];
        $add_log_param['type'] = 9;
        $add_log_param['text'] = '出租房源 ' . 'CZ' . $house_id;
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

      if ($datainfo['isshare'] == 2) {
        $this->result('1', '您发布的合作店长审核中，请耐心等待');
      } else {
        $this->result('1', '房源修改成功！' . $credit_msg);
      }
    }
  }


  /**
   * 出租房源列表页
   *
   * @access  public
   * @param  void
   * @return  void
   */


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
    $rent_house_query_time = $company_basic_data['rent_house_query_time'];

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
    $aagency_list = $this->api_broker_model->get_agencys_by_company_id($company_id);
    //查询房源条件
    $cond_where = "company_id = " . $company_id;
    //根据数据范围，获得门店数据
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
      if ('1' == $rent_house_query_time) {
        $half_year_time = intval(time() - 365 * 0.5 * 24 * 60 * 60);
        $cond_where .= " AND createtime>= '" . $half_year_time . "' ";
      }
      //一年
      if ('2' == $rent_house_query_time) {
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
      if (!isset($get_param['agency_id'])) {
        $get_param['agency_id'] = $this->user_arr['agency_id'];
      }
      if (!isset($get_param['broker_id'])) {
        $get_param['broker_id'] = $this->user_arr['broker_id'];
      }
      if (!empty($company_id)) {
        //获取全部分公司信息
        $data['agency_list'] = $this->api_broker_model->get_agencys_by_company_id($company_id);
        $data['broker_list'] = $this->api_broker_model->get_brokers_agency_id($get_param['agency_id']);
      }
    } else {
      $get_param['broker_id'] = $this->user_arr['broker_id'];
    }

    //搜索字段
    $cond_where_ext = $this->_get_cond_str($get_param);
    if ($get_param) {
      $cond_where .= $cond_where_ext;
    }
    //获取有多少共享房源
    if (!empty($get_param['house_type']) && $get_param['house_type'] == 1) {
      //$where_share=array('isshare'=>'0','broker_id'=>$broker_id);
      $share_num = $this->rent_house_model->get_housenum_by_cond($cond_where);
      $data['share_num'] = $share_num;
    }

    //排序字段
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
    $fileld = array('id', 'block_id', 'nature', 'broker_id', 'broker_name', 'block_name', 'district_id', 'street_id', 'sell_type', 'fitment', 'buildarea', 'price', 'address', 'room', 'hall', 'title', 'keys', 'isshare', 'rententrust', 'pic', 'floor_type', 'floor', 'subfloor', 'totalfloor', 'price_danwei', 'toilet', 'is_outside', 'status', 'video_id', 'video_pic', 'telno1', 'telno2', 'telno3', 'isshare_friend');
    $this->rent_house_model->set_search_fields($fileld);
    //获取列表内容
    $list = $this->rent_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, $order_arr['order_key'], $order_arr['order_by']);
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
    $price_danwei = '';
    if ($list) {
      foreach ($list as $key => $val) {
        $sell_list[$key]['house_id'] = $val['id'];//房源id
        $sell_list[$key]['broker_id'] = $val['broker_id'];//经纪人id
        $sell_list[$key]['broker_name'] = $val['broker_name'];//经纪人姓名
        $sell_list[$key]['title'] = $val['block_name'];//小区名字
        $sell_list[$key]['nature'] = $config['nature'][$val['nature']];//公盘私盘
        $sell_list[$key]['keys'] = $val['keys'];//有无钥匙
        if ($val['rententrust'] == 3) {
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
        if ($val['price_danwei'] == 0) {
          $price_danwei = '元/月';
          $sell_list[$key]['price'] = strip_end_0($val['price']) . $price_danwei;//租价
        } else {
          $price_danwei = '元/㎡*天';
          $sell_list[$key]['price'] = strip_end_0($val['price'] / 30 / $val['buildarea']) . $price_danwei;//租价
        }

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
      $this->result(1, '查询出租房源列表成功', $data);
    }

    if (empty($sell_list)) {
      $this->result(1, '没有相关房源数据', $data);
    }
  }






  /**
   * 出租房源详情页
   *
   * @access  public
   * @param  void
   * @return  void
   */


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
    $this->rent_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id', 'isshare'));
    $this->rent_house_model->set_id($house_id);
    $owner_arr = $this->rent_house_model->get_info_by_id();
    $data = array();
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
    //新权限 判断是否明文显示业主电话
    /***
     * if(is_full_array($owner_arr)){
     * $is_phone_per = $this->broker_permission_model->check('9',$owner_arr);
     * $data['is_phone_per'] = $is_phone_per['auth'];
     * }else{
     * $data['is_phone_per'] = false;
     * }***/
    $house_info = array();
    //获取出售信息基本配置资料
    $config = $this->house_config_model->get_config();
    //基本信息‘状态’数据处理
    if (!empty($config['status']) && is_array($config['status'])) {
      foreach ($config['status'] as $k => $v) {
        if ('暂不售（租）' == $v) {
          $config['status'][$k] = '暂不租';
        }
      }
    }
    $broker_info = $this->user_arr;
    $broker_id = $broker_info['broker_id'];
    $house_id = intval($house_id);
    //房源合作佣金分配
    $this->load->model('rent_house_share_ratio_model');
    $house_money = $this->rent_house_share_ratio_model->get_house_ratio_by_rowid($house_id);
    $house_ratio = array();
    if ($house_money) {
      $house_ratio['buyer_ratio'] = strip_end_0($house_money['buyer_ratio']) . '%';
      $house_ratio['seller_ratio'] = strip_end_0($house_money['seller_ratio']) . '%';
      $house_ratio['a_ratio'] = strip_end_0($house_money['a_ratio']) . '%';
      $house_ratio['b_ratio'] = strip_end_0($house_money['b_ratio']) . '%';
    }

    //设置查询字段
    $fileld = array('id', 'block_id', 'broker_id', 'block_name', 'district_id', 'street_id', 'sell_type', 'fitment', 'price', 'address', 'room', 'hall', 'title', 'keys', 'isshare', 'status', 'rententrust', 'createtime', 'buildarea', 'bewrite', 'forward', 'totalfloor', 'floor', 'nature', 'toilet', 'buildyear', 'floor_type', 'subfloor', 'price_danwei', 'updatetime', 'pic_ids', 'pic_tbl', 'rent_tag', 'is_outside', 'video_id', 'video_pic', 'isshare_friend');
    $this->rent_house_model->set_search_fields($fileld);
    $this->rent_house_model->set_id($house_id);
    $house_info = $this->rent_house_model->get_info_by_id();
    $house_list = array();
    $house_list['status'] = $config['status'][$house_info['status']];//状态
    $house_list['sell_type'] = $config['sell_type'][$house_info['sell_type']];//类型
    if (isset($house_info['video_id']) && !empty($house_info['video_id'])) {
      $house_list['title'] = '[视频房源]' . $house_info['title'];//房源标题
    } else {
      $house_list['title'] = $house_info['title'];//房源标题
    }
    $house_list['fitment'] = $config['fitment'][$house_info['fitment']];//装修
    $house_list['forward'] = $config['forward'][$house_info['forward']];//朝向
    $house_list['house_id'] = $house_info['id'];
    $house_list['block_name'] = $house_info['block_name'];
    $house_list['rent_tag_result'] = array();
    if (!empty($house_info['rent_tag'])) {
      $rent_tag_arr = explode(',', $house_info['rent_tag']);
      if (is_full_array($rent_tag_arr)) {
        foreach ($rent_tag_arr as $k => $v) {
          $house_list['rent_tag_result'][] = array('key' => $v, 'name' => $config['rent_tag'][$v]);
        }
      }
    }
    //是否同步到fang100
    $house_list['is_outside'] = $house_info['is_outside'];
    //钥匙
    $house_list['keys'] = $house_info['keys'];
    $house_list['updatetime'] = date('Y-m-d', $house_info['updatetime']);//跟进时间
    $house_list['createtime'] = date('Y-m-d', $house_info['createtime']);
    //录入时间
    $price_danwei = '';
    if ($house_info['price_danwei'] == 0) {
      $price_danwei = '元/月';
      $house_list['price'] = strip_end_0($house_info['price']) . $price_danwei;//总价
    } else {
      $price_danwei = '元/㎡*天';
      $house_list['price'] = strip_end_0($house_info['price'] / 30 / $house_info['buildarea']) . $price_danwei;//总价
    }

    $house_list['buildarea'] = strip_end_0($house_info['buildarea']);//面积
    //是否为独家代理
    if ($house_info['rententrust'] == 3) {
      $house_list['entrust'] = 1;
    } else {
      $house_list['entrust'] = 0;
    }
    $house_list['entrust'] = $config['rententrust'][$house_info['rententrust']];//委托类型
    $house_list['video_id'] = $house_info['video_id'];
    $house_list['video_pic'] = $house_info['video_pic'];
    $house_list['bewrite'] = str_replace('&nbsp;', '', strip_tags($house_info['bewrite']));//房源描述
    $house_list['nature'] = $config['nature'][$house_info['nature']];//房源性质
    $house_list['hall_type'] = $house_info['room'] . '室' . $house_info['hall'] . '厅' . $house_info['toilet'] . '卫';
    $house_list['is_share'] = $house_info['isshare'];
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
      $is_applay_coop = $this->cooperate_model->check_is_cooped_by_houseid(array($house_id), 'rent', $broker_id);
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
    $house_list['m_house_url'] = '';
    if (isset($this->user_arr['city_spell']) && !empty($this->user_arr['city_spell'])) {
      $city_spell = $this->user_arr['city_spell'];
      $house_list['m_house_url'] = MLS_MOBILE_URL . '/' . $city_spell . '/r/d/' . $house_id . '.html';//房源详情微信地址
    }
    $house_list['cooperate_reward'] = 0;

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
    //当前房源的经纪人与我之间的关系 0自己,1已添加,2申请,3可添加
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
      $community_arr['dis_street'] = $buy_district[$val['dist_id']]['district'] . '-' . $buy_street[$val['streetid']]['streetname'];//区属板块

    }
    //经纪人信用信息
    $return_broker_list = $this->sincere($house_info['broker_id']);
    if ($return_broker_list) {
        $return_broker_list['broker_name'] = $return_broker_list['broker_name']?$return_broker_list['broker_name']:'';
        $return_broker_list['photo'] = $return_broker_list['photo']? $return_broker_list['photo']:'';
        $return_broker_list['cop_suc_ratio'] = $return_broker_list['cop_suc_ratio']?$return_broker_list['cop_suc_ratio']:'';
        $data['broker_list'] = $return_broker_list;
    } else {
      $data['broker_list'] = array('key' => '0', 'name' => '0');
    }

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
//					$shineipic[] = str_replace('thumb/','',$val);
          $shineipic[] = changepic($val);
        } else {
//					$huxingpic[] = str_replace('thumb/','',$val);
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
    //判断该房源是否是申请合作状态
    if ($house_id) {
      $cooperate_where = array();
      $esta_arr = array('1', '2', '3', '4');
      $cooperate_where['rowid'] = $house_id;
      $cooperate_where['apply_type'] = 1;
      $cooperate_where['tbl'] = 'rent';
      $this->load->model('cooperate_model');
      $esta = $this->cooperate_model->get_cooperate_baseinfo_esta($cooperate_where);
      if ($esta && in_array($esta['esta'], $esta_arr)) {
        $data['cooperate_esta'] = 1;
      } else {
        $data['cooperate_esta'] = 0;
      }
    }
    if ($house_list) {
      $data['house_list'] = $house_list;
    } else {
      $data['house_list'] = array('key' => '0', 'name' => '0');
    }
    $this->result('1', '房源详情页', $data);
  }

  //查看保密信息
  public function get_secret_info()
  {
    $data = array();
    $house_info = array();
    $house_mess = array();
    $broker_info = $this->user_arr;
    $house_id = $this->input->get('house_id', TRUE);
    $devicetype = $this->input->get('api_key', TRUE);
    $deviceid = $this->input->get('deviceid', TRUE);
    $house_id = intval($house_id);

    //新权限
    //范围（个人或全公司）
    //获得当前数据所属的经纪人id和门店id
    $this->rent_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id', 'nature'));
    $this->rent_house_model->set_id($house_id);
    $owner_arr = $this->rent_house_model->get_info_by_id();
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
      'view_type' => 2,
      'row_id' => $house_id
    );
    $query_result = $this->broker_view_secrecy_model->get_one_by($where_cond);
    if ($owner_arr['broker_id'] != $broker_info['broker_id'] && empty($query_result)) {
      $is_insert = true;
    } else {
      $is_insert = false;
    }
    $check_baomi_time = $this->broker_model->check_baomi_time($this->company_basic_arr,
      $this->user_arr, 2, $house_id, $is_insert);
    if (!$check_baomi_time['status']) {
      $this->result(0, '您每天可查看保密信息' . $check_baomi_time['secrecy_num']
        . '次,现在已达上限');
      return false;
    }
    //设置查询字段
    $fileld = array('id', 'dong', 'unit', 'telno1', 'owner', 'telno2', 'telno3', 'door', 'nature');
    $this->rent_house_model->set_search_fields($fileld);
    $this->rent_house_model->set_id($house_id);
    $house_mess = $this->rent_house_model->get_info_by_id();

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
      $add_log_param['text'] = '出租房源 ' . 'CZ' . $house_id;
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

  /**
   * 查看保密信息次数
   */
  public function add_brower_log($hosue_id)
  {
    $return_data = '';
    $user = $this->user_arr;
    $house_id = intval($hosue_id);
    $param_list = array(
      'house_id' => $house_id,
      'broker_id' => $user['broker_id'],
      'broker_name' => $user['truename'],
      'agency_id' => $user['agency_id'],
      'agency_name' => $user['agency_name'],
      'ip' => $_SERVER['REMOTE_ADDR'],
      'browertime' => time(),
    );
    $this->load->model('rent_house_model');
    $result = $this->rent_house_model->add($param_list);

  }

  /**
   * 出售房源访问记录
   *
   * @access  public
   * @param  int $house_id 房源编号
   * @param  int $is_public 是否公盘
   * @return  void
   */
  public function ajax_get_brower_log($house_id = '1')
  {
    //加载客源浏览日志MODEL
    $this->load->model('rent_house_model');
    $data['where_cond'] = array('house_id' => $house_id);
    //分组字段
    $group_by = 'broker_id';
    //分页开始
    $data['user_num'] = $this->rent_house_model->get_brower_log_sell_num($data['where_cond']);
    $data['group_by_num'] = $this->rent_house_model->get_brower_log_group_num($house_id);
    $data['pagesize'] = 4; //设定每一页显示的记录数
    $data['pages'] = $data['group_by_num'] ? ceil($data['group_by_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_GET['pg']) ? intval($_GET['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    //排序字段
    $order_by_array = array('browertime', 'desc');
    //房源浏览日志数据
    $brower_list = $this->rent_house_model->get_brower_log($data['where_cond'], $data['offset'],
      $data['pagesize'], $order_by_array, $group_by);

    $brower_list2 = array();
    //数据重构
    foreach ($brower_list as $k => $v) {
      if (!empty($v['browertime'])) {
        $where = array('house_id' => $house_id, 'broker_id' => $v['broker_id']);
        $today_browertime = array(strtotime(date('Y-m-d')), strtotime(date('Y-m-d', strtotime('+1 day'))));//今天的时间戳范围
        $v['browerdate'] = date('Y-m-d H:i:s', $v['browertime']);
        $v['brower_num'] = $this->rent_house_model->get_brower_log_sell_num($where);//总查阅次数
        $v['today_brower_num'] = $this->rent_house_model->get_today_brower_log_num($house_id, $v['broker_id'], $today_browertime);//今日查阅次数
        $first_brower = $this->rent_house_model->get_brower_log($where, 0, 0, array('browertime', 'asc'));//初次浏览记录
        $recent_brower = $this->rent_house_model->get_brower_log($where, 0, 0, array('browertime', 'desc'));//最近浏览记录
        $v['first_brower'] = date('Y-m-d H:i:s', $first_brower[0]['browertime']);
        $v['recent_brower'] = date('Y-m-d H:i:s', $recent_brower[0]['browertime']);
      }
      $brower_list2[] = $v;
    }
    echo json_encode($brower_list2);
  }

  /**
   * 查看出租房源访问记录
   *
   * @access  public
   * @param   int $house_id 房源编号
   * @param   int $is_public 是否公盘
   * @return  void
   */
  public function ajax_get_view_log($house_id = '1')
  {
    //客源访问日志信息
    $type = 'rent';
    $this->load->model('view_log_model');
    $cond_where = "h_id = '" . $house_id . "'";
    $this->_total_count = $this->view_log_model->get_view_log_num_by_hid($type, $house_id);

    //分页开始
    $data['log_num'] = $this->view_log_model->get_view_log_num_by_hid($type, $house_id);//浏览总数
    $data['pagesize'] = 2; //设定每一页显示的记录数
    $data['pages'] = $data['log_num'] ? ceil($data['log_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_GET['pg']) ? intval($_GET['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    $view_log_list = $this->view_log_model->get_view_log_list_by_hid($type, $house_id, $data['offset'], $data['pagesize']);
    $view_log_list2 = array();
    foreach ($view_log_list as $k => $v) {
      $v['datetime'] = date('Y-m-d H:i:s', $v['datetime']);
      $view_log_list2[] = $v;
    }
    echo json_encode($view_log_list2);
  }

  /**
   * 客源申请合作分页请求
   *
   * @access  public
   * @param   int $house_id 房源编号
   * @param   int $is_public 是否公盘
   * @return  void
   */
  public function ajax_get_cooperate_log($house_id = '1')
  {
    //客源合作日志
    $this->load->model('cooperate_model');
    //分页开始
    $data['cooperate_num'] = $this->cooperate_model->get_cooperate_num_by_houseid($house_id, 'rent');//浏览总数
    $data['pagesize'] = 2; //设定每一页显示的记录数
    $data['cooperate_pages'] = $data['cooperate_num'] ? ceil($data['cooperate_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['cooperate_page'] = isset($_GET['pg']) ? intval($_GET['pg']) : 1; // 获取当前页数
    $data['cooperate_page'] = ($data['cooperate_page'] > $data['cooperate_pages'] && $data['cooperate_pages'] != 0) ? $data['cooperate_pages'] : $data['cooperate_page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['cooperate_page'] - 1);   //计算记录偏移量

    $cooperate_log_list = $this->cooperate_model->get_cooperate_lists_by_houseid($house_id, 'rent', $data['offset'], $data['pagesize']);
    $cooperate_log_list2 = array();
    //合作基础配置文件
    $cooperate_conf = $this->cooperate_model->get_base_conf();
    foreach ($cooperate_log_list as $k => $v) {
      $v['creattime'] = date('Y-m-d H:i:s', $v['creattime']);
      $v['esta'] = $cooperate_conf['esta'][$v['esta']];
      $cooperate_log_list2[] = $v;
    }
    echo json_encode($cooperate_log_list2);
  }


  //查看保密信息

  public function get_secret_info_1()
  {
    $house_id = intval($this->input->get('house_id'));
    $broker_info = array();
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    $data_info = array();

    if ($house_id > 0) {
      $this->rent_house_model->set_id($house_id);
      $select_feilds = array('id', 'broker_id', 'agency_id', 'dong', 'unit', 'door', 'owner', 'telno1',
        'telno2', 'telno3', 'lowprice', 'isshare', 'lock');
      $this->rent_house_model->set_search_fields($select_feilds);
      $data_info = $this->rent_house_model->get_info_by_id();

      //判断是否锁定，有无权限查看（锁定状态下，发布人和锁定人可以查看）
      if (!empty($data_info) && ($data_info['lock'] == 0 || in_array($broker_id, array($data_info['broker_id'], $data_info['lock'])))) {
        $data_info['telnos'] = $data_info['telno1'];
        $data_info['telnos'] .= !empty($data_info['telno2']) ? ', ' . $data_info['telno2'] : '';
        $data_info['telnos'] .= !empty($data_info['telno3']) ? ', ' . $data_info['telno3'] : '';
        $data_info['lowprice'] = strip_end_0($data_info['lowprice']);
      } else {
        $data_info = array();
      }
    }
    echo json_encode($data_info);

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
      default:
        $arr_order['order_key'] = 'updatetime';
        $arr_order['order_by'] = 'DESC';
    }

    return $arr_order;
  }


  /**
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
    //楼层floor
    $floor_min = isset($form_param['floor_min']) ? intval($form_param['floor_min']) : 0;
    $floor_max = isset($form_param['floor_max']) ? intval($form_param['floor_max']) : 0;
    if ($floor_min || $floor_max) {
      $cond_where .= " AND floor >= '" . $floor_min . "'";
      $cond_where .= " AND floor <= '" . $floor_max . "'";
    }
    //板块
    if (isset($form_param['street']) && !empty($form_param['street']) && $form_param['street'] > 0) {
      $street = intval($form_param['street']);
      $cond_where .= " AND street_id = '" . $street . "'";
    }
    //区属
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
      $area_val = $area['rent_area'][$sell_area];
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
      $price_val = $sell_price['rent_price'][$price];
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
      $isshare = intval($form_param['is_share']);
      $cond_where .= " AND isshare='" . $isshare . "'";
    }

    //是否是合作朋友圈的
    if (isset($form_param['isshare_friend'])) {
      $isshare_friend = intval($form_param['isshare_friend']);
      $cond_where .= " AND isshare = 1 AND isshare_friend = '" . $isshare_friend . "'";
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
    $now_time = time();
    if (!empty($form_param['set_share_time'])) {
      $searchtime = intval($form_param['set_share_time']);
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

    //门店
    if (!empty($form_param['agency_id']) && $form_param['agency_id'] != '' && $form_param['agency_id'] > 0) {
      $agency_id = intval($form_param['agency_id']);
      $cond_where .= " AND agency_id = '" . $agency_id . "'";
    }

    //经纪人
    if (!empty($form_param['broker_id']) && $form_param['broker_id'] != '' && $form_param['broker_id'] > 0) {
      $broker_id = intval($form_param['broker_id']);
      $cond_where .= " AND broker_id = '" . $broker_id . "'";
    }
    if (!empty($form_param['company_id']) && $form_param['company_id'] != '') {
      $company_id = intval($form_param['company_id']);
      $cond_where .= " AND company_id = '" . $company_id . "'";
    }
    if (isset($form_param['is_outside']) && $form_param['is_outside'] != '-1') {
      $is_outside = intval($form_param['is_outside']);
      $cond_where .= " AND is_outside = '" . $is_outside . "'";
    }
    return $cond_where;
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

  //删除房源
  public function del()
  {
    //获取当前登录人信息
    $broker_info = $this->user_arr;
    $up_num = 0;
    $house_id = $this->input->get('house_id', TRUE);
    $house_id = trim($house_id);
    $house_id = trim($house_id, ',');
    $devicetype = $this->input->get('api_key', TRUE);
    $deviceid = $this->input->get('deviceid', TRUE);

    //新权限
    //范围（1公司2门店3个人）
    //获得当前数据所属的经纪人id和门店id
    $this->rent_house_model->set_search_fields(array('broker_id', 'agency_id', 'nature', 'company_id'));
    $this->rent_house_model->set_id($house_id);
    $owner_arr = $this->rent_house_model->get_info_by_id();
    //修改房源权限
    $house_modify_per = $this->broker_permission_model->check('7', $owner_arr);
    //修改出租房源关联门店权限
    $agency_house_modify_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '3');
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
      $up_num = $this->rent_house_model->update_info_by_cond($arr, $cond_where);
    }
    if ($up_num > 0) {
      //操作日志
      $this->rent_house_model->set_search_fields(array('block_name', 'address', 'dong', 'unit', 'door'));
      $this->rent_house_model->set_id(intval($house_id));
      $datainfo = $this->rent_house_model->get_info_by_id();

      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 4;
      $add_log_param['text'] = '出租房源 ' . 'CZ' . $house_id;
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
      $this->result('1', '出租房源注销成功');
    } else {
      $this->result('0', '出租房源注销失败');
    }
  }

  //取消合作
  public function cancel_share()
  {
    $str = $this->input->get('house_id', TRUE);
    $str = intval($str);

    //新权限
    //获得当前数据所属的经纪人id和门店id
    $this->rent_house_model->set_search_fields(array('broker_id', 'agency_id', 'status', 'company_id'));
    $this->rent_house_model->set_id($str);
    $owner_arr = $this->rent_house_model->get_info_by_id();
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


  //设置合作
  public function set_share()
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
    $this->rent_house_model->set_search_fields(array('broker_id', 'agency_id', 'status', 'company_id'));
    $this->rent_house_model->set_id($str);
    $owner_arr = $this->rent_house_model->get_info_by_id();
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
    if (!$isshare_friend) {
      $isshare_friend = 0;
    }

    $a_ratio = $this->input->get('a_ratio', TRUE);//甲方佣金分成比例
    $b_ratio = $this->input->get('b_ratio', TRUE);//已方佣金分成比例
    $buyer_ratio = $this->input->get('buyer_ratio', TRUE);//买方支付佣金比例
    $seller_ratio = $this->input->get('seller_ratio', TRUE);//卖方支付佣金比例
    if ($a_ratio && $b_ratio && $buyer_ratio && $seller_ratio || true) {
      //$this->load->model('rent_house_share_ratio_model');
      //$this->rent_house_share_ratio_model->update_house_ratio_by_rowid($str,$seller_ratio , $buyer_ratio , $a_ratio , $b_ratio);
      $this->change_share($str, $flag, $isshare_friend);
    } else {
      //$this->result('0','佣金分配值不能为空');
      //exit;
    }

  }

  //合作
  public function change_share($str, $flag, $isshare_friend = 0)
  {
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    $data['open_cooperate'] = $company_basic_data['open_cooperate'];
    if ('0' === $data['open_cooperate']) {
      $this->result('-1', '当前公司尚未开启合作中心');
      exit();
    }

    $score_result = array();
    $flag = intval($flag);
    $str = intval($str);

    //新权限
    //获得当前数据所属的经纪人id和门店id
    $this->rent_house_model->set_search_fields(array('broker_id', 'agency_id', 'status', 'company_id'));
    $this->rent_house_model->set_id($str);
    $owner_arr = $this->rent_house_model->get_info_by_id();
    if ($owner_arr['status'] != 1) {
      $this->result('-1', '该房源非有效状态，不能设置合作');
      exit();
    }
    $cooperate_per = $this->broker_permission_model->check('2', $owner_arr);
    if (!$cooperate_per['auth']) {
      $this->result('-1', '暂无权限');
      exit();
    }

    $up_num = '';
    if ($str && $flag <= 2 && $flag >= 0) {
      $arr = array('isshare' => $flag, 'isshare_friend' => $isshare_friend);
      //设置合作时间
      if (1 == $flag || 2 == $flag) {
        $arr['set_share_time'] = time();
      }
      $cond_where = "id IN (0," . $str . ") AND isshare <> {$flag}";
      //跟进
      $this->load->model('follow_model');
      $ids_arr = array();

      $this->rent_house_model->set_search_fields(array("id"));
      $list = $this->rent_house_model->get_list_by_cond($cond_where);

      $text = $flag ? "是否合作:否>>是" : "是否合作:是>>否";

      foreach ($list as $key => $val) {
        $needarr = array();
        $needarr['broker_id'] = $this->user_arr['broker_id'];
        $needarr['house_id'] = $val['id'];
        $needarr['agency_id'] = $this->user_arr['agency_id'];//门店ID
        $needarr['company_id'] = $this->user_arr['company_id'];//总公司id
        $needarr['type'] = 2;
        $needarr['text'] = $text;
        $bool = $this->follow_model->house_save($needarr);
        $ids_arr[] = $val['id'];
      }

      $up_num = $this->rent_house_model->update_info_by_cond($arr, $cond_where);
    }

    if ($up_num > 0) {

      //设置合作后，并添加积分
      if ($flag == 1) {
        $this->result('1', '设置合作成功');
      } else if ($flag == 2) {
        $this->result('1', '当前公司开启合作审核，请等待审核...');
      } else {
        //取消合作后，终止与房源有关系的合作
        $stop_reason = 'private_house';
        $this->load->model('cooperate_model');
        $this->cooperate_model->stop_cooperate($str, 'rent', $stop_reason);
        $this->result('1', '取消合作成功');

      }


    } else {
      if ($flag == 2) {
        $this->result('0', '该房源已经发送审核');
      } else {
        $this->result('0', '设置失败');
      }
    }
  }


  /**
   * 设为私盘
   * @access private
   * @return void
   */
  public function set_private()
  {
    $str = $this->input->get('str', TRUE);
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);
    $this->_change_nature($str, $flag);
  }

  /**
   * 设为公盘
   * @access private
   * @return void
   */
  public function set_public()
  {
    $str = $this->input->get('str', TRUE);
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);
    $this->_change_nature($str, $flag);
  }

  /**
   * 设为公盘、私盘
   * @access private
   * @return void
   */
  public function _change_nature($str, $flag)
  {
    if ($str && $flag <= 2 && $flag >= 1) {
      $arr = array('nature' => $flag);
      $cond_where = "id in (0," . $str . ") and nature <> {$flag}";
      //跟进
      $this->load->model('follow_model');
      $ids_arr = array();

      $this->rent_house_model->set_search_fields(array("id"));
      $list = $this->rent_house_model->get_list_by_cond($cond_where);
      $text = $flag > 1 ? "设置公私盘:私盘>>公盘" : "设置公私盘:公盘>>私盘";
      foreach ($list as $key => $val) {
        $needarr = array();
        $needarr['broker_id'] = $this->user_arr['broker_id'];
        $needarr['house_id'] = $val['id'];
        $needarr['type'] = 2;
        $needarr['text'] = $text;
        $bool = $this->follow_model->house_save($needarr);
        $ids_arr[] = $val['id'];
      }
      $up_num = $this->rent_house_model->update_info_by_cond($arr, $cond_where);
    } else {
      $reslult = array('result' => 'no', "msg" => "设置失败");
    }
    if ($up_num > 0) {
      $reslult = array('result' => 'ok', "arr" => $ids_arr, "msg" => "设置成功，共设置{$up_num}条数据");
    } else {
      $reslult = array('result' => 'no', "msg" => "设置失败");
    }
    echo json_encode($reslult);
  }

  /**
   * 设为锁定
   * @access private
   * @return void
   */
  public function set_lock()
  {
    $str = $this->input->get('str', TRUE);
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);
    $this->_change_lock($str, $flag);
  }

  /**
   * 设为解锁
   * @access private
   * @return void
   */
  public function set_unlock()
  {
    $str = $this->input->get('str', TRUE);
    $flag = $this->input->get('flag', TRUE);
    $flag = intval($flag);
    $this->_change_lock($str, $flag);
  }

  /**
   * 锁定、解锁
   * @access private
   * @return void
   */
  public function _change_lock($str, $flag)
  {
    if ($str && $flag <= 1 && $flag >= 0) {
      $broker_id = $this->user_arr['broker_id'];
      if ($flag == 0) {
        //解锁
        $arr = array('lock' => $flag);
        $cond_where = "id in (0," . $str . ") and `lock` = {$broker_id}";
      } else if ($flag == 1) {
        //锁定
        $arr = array('lock' => $broker_id);
        $cond_where = "id in (0," . $str . ") and `lock` = 0";
      }
      //跟进
      $this->load->model('follow_model');
      $ids_arr = array();

      $this->rent_house_model->set_search_fields(array("id"));
      $list = $this->rent_house_model->get_list_by_cond($cond_where);
      $text = $flag ? "是否锁定:否>>是" : "是否锁定:是>>否";
      foreach ($list as $key => $val) {
        $needarr = array();
        $needarr['broker_id'] = $broker_id;
        $needarr['house_id'] = $val['id'];
        $needarr['type'] = 2;
        $needarr['text'] = $text;
        $bool = $this->follow_model->house_save($needarr);
        $ids_arr[] = $val['id'];
      }
      $up_num = $this->rent_house_model->update_info_by_cond($arr, $cond_where);
    } else {
      $reslult = array('result' => 'no', "msg" => "设置失败");
    }
    if ($up_num > 0) {
      $reslult = array('result' => 'ok', "arr" => $ids_arr, "msg" => "设置成功，共设置{$up_num}条数据");
    } else {
      $reslult = array('result' => 'no', "msg" => "设置失败");
    }
    echo json_encode($reslult);
  }

  /**
   * 新增楼盘
   *
   * @access  public
   * @param  void
   * @return  string
   */
  public function add_community()
  {
    $facilities_str = '';
    $build_type_str = '';
    //周边配套
    $facilities_arr = $this->input->get('facilities');
    if (!empty($facilities_arr) && is_array($facilities_arr)) {
      $facilities_str = implode('#', $facilities_arr);
    }
    //物业类型
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
      'buildarea' => $this->input->get('buildarea'),
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
      'facilities' => $facilities_str,
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
    } else if ($paramArray['build_date'] == '') {
      $return_data = '500';//建筑年代为空
    } else {
      $cmt_data = $this->community_model->get_cmtinfo_by_cmtname($paramArray['cmt_name']);
      if (!empty($cmt_data) && is_array($cmt_data)) {
        $return_data = '600';//该小区已存在
      } else {
        $add_result = $this->community_model->add_community($paramArray);//楼盘数据入库
        if (!empty($add_result) && is_int($add_result)) {
          $return_data = 'true';
          //外景图
          $location_pic_arr = $this->input->get('location_pic');
          if (!empty($location_pic_arr) && is_array($location_pic_arr)) {
            //封面
            $surface = $this->input->get('surface');
            $cmt_img_arr = array();
            //外景图数据重构
            foreach ($location_pic_arr as $k => $v) {
              $img_arr = array();
              $img_arr['cmt_id'] = intval($add_result);
              $img_arr['image'] = $v;
              $img_arr['pic_type'] = 3;
              $img_arr['creattime'] = time();
              $img_arr['ip'] = $_SERVER['REMOTE_ADDR'];
              if ($surface == $v) {
                $img_arr['is_surface'] = 1;
              } else {
                $img_arr['is_surface'] = 0;
              }
              $cmt_img_arr[] = $img_arr;
            }
            foreach ($cmt_img_arr as $k => $v) {
              $this->community_model->add_cmt_image($v);//楼盘图片入库
            }
          }
        } else {
          $return_data = 'false';
        }
      }
    }
    echo $return_data;
    exit;
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
   * 出租匹配
   *
   * @access  public
   * @param  void
   * @return  void
   */
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
    //获取系统门店基本设置
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
    $this->rent_house_model->set_id($house_id);
    $fileld = array('id', 'broker_id', 'district_id', 'sell_type', 'fitment', 'title', 'room', 'hall', 'buildarea', 'price_danwei', 'price', 'address', 'street_id', 'broker_name', 'block_name');
    $this->rent_house_model->set_search_fields($fileld);
    $house_info = $this->rent_house_model->get_info_by_id();
    //根据区属id获取板块
    $dis_list = $this->get_dis_list($house_info['district_id']);
    $data['dis_list'] = $dis_list;
    $house_meg = array();
    $house_meg['district_name_address'] = $this->district_model->get_distname_by_id($house_info['district_id']) . '-' . $this->district_model->get_streetname_by_id($house_info['street_id']);
    $house_meg['sell_type'] = $config['sell_type'][$house_info['sell_type']];
    if ($house_info['fitment']) {
      $house_meg['fitment'] = $config['fitment'][$house_info['fitment']];
    } else {
      $house_meg['fitment'] = '';
    }
    $house_meg['room_type'] = $house_info['room'] . '室' . $house_info['hall'] . '厅';
    $house_meg['buildarea'] = strip_end_0($house_info['buildarea']);

    if ($house_info['price_danwei'] == 0) {
      $house_meg['price'] = strip_end_0($house_info['price']) . '元/月';
    } else {
      $house_meg['price'] = strip_end_0($house_info['price'] / 30 / $house_info['buildarea']) . '元/㎡*天';
    }

    $house_meg['title'] = $house_info['block_name'];
    if ($house_meg) {
      //检测是否已经合作
      $this->load->model('cooperate_model');
      $is_applay_coop = $this->cooperate_model->check_is_cooped_by_houseid(array($house_id), 'rent', $this->user_arr['broker_id']);
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
    $where .= " AND ( dist_id1  = '" . $house_info['district_id'] . "' OR dist_id2  = '" . $house_info['district_id'] . "' OR dist_id3  = '" . $house_info['district_id'] . "' ) ";
    $where .= " AND property_type = '" . $house_info['sell_type'] . "' ";
    $where .= " AND area_max >= '" . $house_info['buildarea'] . "' AND area_min <= '" . $house_info['buildarea'] . "' ";
    $where .= " AND price_max >= '" . $house_info['price'] . "' AND price_min <= '" . $house_info['price'] . "' ";

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

    $this->load->model('rent_customer_model');
    $customer_list =
      $this->rent_customer_model->get_rentlist_by_cond($where, $this->_offset,
        $this->_limit);
    //获取求购客源的配置
    $buy_config = $this->rent_customer_model->get_base_conf();
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
    $price_danwei = '';
    if (is_full_array($customer_list)) {
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
        if ($val['price_danwei'] == 0) {
          $price_danwei = '元/月';
          $price_min = $val['price_min'];
          $price_max = $val['price_max'];
        } else {
          $price_danwei = '元/㎡*天';
          $price_min = round($val['price_min'] / 30 / $val['area_min'], 2);
          $price_max = round($val['price_max'] / 30 / $val['area_max'], 2);
        }
        $customer_arr[$key]['price'] = strip_end_0($price_min) . '-' . strip_end_0($price_max) . $price_danwei;
        $customer_arr[$key]['customer_type'] = $buy_config['property_type'][$val['property_type']];
        $customer_arr[$key]['customer_district'] = '';
        if ($val['dist_id1']) {
          $customer_arr[$key]['customer_district'] .= $buy_district[$val['dist_id1']]['district'] . '-' . $buy_street[$val['street_id2']]['streetname'] . ',';
        }
        if ($val['dist_id2']) {
          $customer_arr[$key]['customer_district'] .= $buy_district[$val['dist_id2']]['district'] . '-' . $buy_street[$val['street_id2']]['streetname'] . ',';
        }
        if ($val['dist_id3']) {
          $customer_arr[$key]['customer_district'] .= $buy_district[$val['dist_id3']]['district'] . '-' . $buy_street[$val['street_id2']]['streetname'];
        }
        $customer_arr[$key]['customer_cmt'] = '';
        if ($val['cmt_name1']) {
          $customer_arr[$key]['customer_cmt'] .= $val['cmt_name1'] . ',';
        }
        if ($val['cmt_name2']) {
          $customer_arr[$key]['customer_cmt'] .= $val['cmt_name2'] . ',';
        }
        if ($val['cmt_name3']) {
          $customer_arr[$key]['customer_cmt'] .= $val['cmt_name3'] . ',';
        }


      }
    }
    if (empty($customer_list)) {
      $data['customer_list'] = array('key' => '0', 'name' => '0');

    } else {
      $data['customer_list'] = $customer_arr;
    }


    $this->result('1', '智能匹配', $data);
  }

  /**
   * 出租匹配条件
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

    ////楼盘
    if (isset($form_param['block_id']) && $form_param['block_id'] > 0) {
      $block_id = intval($form_param['block_id']);
      $cond_where .= " and ( cmt_id1 = '" . $block_id . "' OR cmt_id2  = '" . $block_id . "' OR  cmt_id3  = '" . $block_id . "' )";
    }

    //户型条件
    $room = intval($form_param['room']);
    if ($room) {
      $cond_where .= " AND room_max >= '" . $room . "' AND room_min <= '" . $room . "' ";
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

  //合作朋友圈
  public function friend_lists_pub()
  {

    $this->lists_pub('friend');
  }

  //合作中心出租详细表
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

    //post参数
    $get_param = $this->input->get(NULL, TRUE);
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
    //查询房源条件
    if ($friend == 'friend') {
      $cond_where = "isshare = 1 AND status = 1 AND isshare_friend = 1";
    } else {
      $cond_where = "isshare = 1 AND status = 1 AND isshare_friend = 0";
    }
    $cond_where_ext = $this->_get_cond_str($get_param);
    if ($get_param) {
      $cond_where .= $cond_where_ext;
    }

    //排序字段
    $roomorder = '';
    if (isset($get_param['orderby_id']) && !empty($get_param['orderby_id'])) {
      $roomorder = intval($get_param['orderby_id']);
    }
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
    $order_arr = $this->_get_orderby_arr($roomorder);
    //设置查询字段
    $fileld = array('id', 'broker_id', 'block_id', 'nature', 'block_name', 'district_id', 'street_id', 'sell_type', 'fitment', 'buildarea', 'price', 'address', 'room', 'hall', 'title', 'keys', 'isshare', 'rententrust', 'pic', 'price_danwei', 'video_id', 'video_pic', 'floor', 'totalfloor', 'sell_type');
    $this->rent_house_model->set_search_fields($fileld);
    //获取列表内容
    $list = $this->rent_house_model->get_list_by_cond($cond_where, $this->_offset, $this->_limit, $order_arr['order_key'], $order_arr['order_by']);
    $sell_list = array();
    $price_danwei = '';
    if ($list) {
      foreach ($list as $key => $val) {
        $sell_list[$key]['house_id'] = $val['id'];//房源id
        $sell_list[$key]['broker_id'] = $val['broker_id'];//经纪人id
        $sell_list[$key]['title'] = $val['block_name'];//标题
        $sell_list[$key]['nature'] = $config['nature'][$val['nature']];//公盘私盘
        $sell_list[$key]['keys'] = $val['keys'];//有无钥匙
        if ($val['rententrust'] && $val['rententrust'] == 3) {
          $sell_list[$key]['entrust'] = 1;
        } else {
          $sell_list[$key]['entrust'] = 2;
        }
        $sell_list[$key]['is_share'] = $val['isshare'];//是否合作
        $sell_list[$key]['property_type'] = $config['sell_type'][$val['sell_type']];//出售类型
        $sell_list[$key]['block_name_address'] = $buy_district[$val['district_id']]['district'] . '-' . $buy_street[$val['street_id']]['streetname'];//小区名称地址
        if ($val['fitment']) {
          $sell_list[$key]['fitment'] = $config['fitment'][$val['fitment']];
        }
        //装修程度
        $sell_list[$key]['room_hall'] = $val['room'] . '室' . $val['hall'] . '厅';//几室几厅
        $sell_list[$key]['buildarea'] = strip_end_0($val['buildarea']);//面积
        if ($val['price_danwei'] == 0) {
          $price_danwei = '元/月';
          $sell_list[$key]['price'] = strip_end_0($val['price']) . $price_danwei;//租价
        } else {
          $price_danwei = '元/㎡*天';
          $sell_list[$key]['price'] = strip_end_0($val['price'] / 30 / $val['buildarea']) . $price_danwei;//租价
        }

        $sell_list[$key]['pic_url'] = $val['pic'];
        $sell_list[$key]['broker_info'] = $this->sincere($val['broker_id']);
        $sell_list[$key]['video_id'] = $val['video_id'];
        $sell_list[$key]['video_pic'] = $val['video_pic'];
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
    $this->result(1, '查询合作中心出租房源列表', $data);

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


    //新权限 出租房源查看跟进权限
    //获得当前数据所属的经纪人id和门店id
    $this->rent_house_model->set_search_fields(array('broker_id', 'agency_id', 'company_id'));
    $this->rent_house_model->set_id($house_id);
    $owner_arr = $this->rent_house_model->get_info_by_id();
    $view_follow_per = $this->broker_permission_model->check('13', $owner_arr);
    //出售房源跟进关联门店权限
    $agency_house_follow_per = $this->agency_permission_model->check($this->user_arr['agency_id'], $owner_arr, '6');
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
    $where_arr .= "  AND type = 2 ";
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
    $tbl = 'rent_customer';
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
          $follow_arr[$key]['follow_broker_name'] = '系统管理员';
        }
        if ($val['customer_id']) {
          $follow_arr[$key]['follow_customer_name'] = $acustomer_list[$val['customer_id']]['truename'];
        }
        $follow_arr[$key]['foll_type'] = !in_array($val['follow_way'], array(7, 8, 11, 12)) ? 1 : 2;
      }
    }
    $data['follow_lists'] = $follow_arr;

    //操作日志
    $this->rent_house_model->set_search_fields(array('block_name', 'address', 'dong', 'unit', 'door'));
    $this->rent_house_model->set_id(intval($house_id));
    $datainfo = $this->rent_house_model->get_info_by_id();

    $add_log_param = array();
    $add_log_param['company_id'] = $this->user_arr['company_id'];
    $add_log_param['agency_id'] = $this->user_arr['agency_id'];
    $add_log_param['broker_id'] = $this->user_arr['broker_id'];
    $add_log_param['broker_name'] = $this->user_arr['truename'];
    $add_log_param['type'] = 5;
    $add_log_param['text'] = '出租房源 ' . 'CZ' . $house_id;
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

    $this->result('1', '查看出租房源的跟进信息', $data);


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
    $follow_arr['type'] = 2;//客户类型
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
        $this->rent_house_model->set_id($follow_arr['house_id']);
        $result = $this->rent_house_model->update_info_by_id($follow_date_info);
      }

      $data = array();
      $data['follow_id'] = $follow_id;
      if ($follow_id > 0) {
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
        $add_log_param['text'] = '出租房源 ' . 'CZ' . $follow_arr['house_id'] . ' ' . $follow_way_str . ' ' . $follow_arr['text'];
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
    $tbl = 'rent_customer';
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
    $price_danwei = '';
    if ($list) {
      foreach ($list as $key => $val) {
        $customer_list[$key]['customer_id'] = $val['id'];
        $customer_list[$key]['customer_name'] = $val['truename'];
        if ($val['price_danwei'] == 0) {
          $price_danwei = '元/月';
        } else {
          $price_danwei = '元/㎡*天';
        }
        $customer_list[$key]['customer_price'] = strip_end_0($val['price_min']) . '-' . strip_end_0($val['price_max']) . $price_danwei;
      }
    }
    $data['customer_list'] = $customer_list;
    if ($list) {
      $this->result('1', '返回客户信息成功', $data);

    } else {
      $this->result('1', '抱歉没有您相关的客源信息', $data);
    }
  }

  //修改房源的日志录入
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

          case 'title':
            $constr .= '标题:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'bewrite':
            $constr .= '描述:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case 'street_id':
            $constr .= '板块：' . $stred[$val]['streetname'] . '>>' . $stred[$datainfo[$key]]['streetname'] . ',';
            break;

          case'address':
            $constr .= '地址:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'unit':
            $constr .= '单元:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'door':
            $constr .= '门牌:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'owner':
            $constr .= '业主姓名:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'idcare':
            $constr .= '身份证:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'telno1':
            $constr .= '电话:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'telno2':
            $constr .= '电话2:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'telno3':
            $constr .= '电话3:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'proof':
            $constr .= '证书号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'mound_num':
            $constr .= '丘地号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'record_num':
            $constr .= '备案号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'status':
            $constr .= '状态:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case'nature':
            $constr .= '房源性质:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case'room':
            $constr .= '室:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'hall':
            $constr .= '厅:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'toilet':
            $constr .= '卫:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'balcony':
            $constr .= '阳台:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'floor_type':
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


          case'price_danwei':
            if ($val == 0) {
              $val = '元/月';
            } else {
              $val = '元/㎡*天';
            }
            if ($datainfo[$key] == 0) {
              $datainfo[$key] = '元/月';
            } else {
              $datainfo[$key] = '元/㎡*天';
            }
            $constr .= '单位:' . $val . '>>' . $datainfo[$key] . ',';
            break;


          case'floor':
            $constr .= '楼层:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'totalfloor':
            $constr .= '总楼层:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'forward':
            $constr .= '朝向:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case'totalfloor':
            $constr .= '总楼层:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'fitment':
            $constr .= '装修:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case'buildarea':
            $constr .= '面积:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'buildyear':
            $constr .= '房龄:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'price':
            $constr .= '租金:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'keys':
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

          case'isshare':
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

          case'key_number':
            $constr .= '钥匙编号:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'rententrust':
            $constr .= '委托类型:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case'division':
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

          case'property':
            $constr .= '产权:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case'current':
            $constr .= '现状:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case'infofrom':
            $constr .= '信息来源:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case'villa_type':
            $constr .= '别墅类型:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case'remark':
            $constr .= '备注:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'deposit':
            $constr .= '押金:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'lowprice':
            $constr .= '最低租金:' . $val . '>>' . $datainfo[$key] . ',';
            break;

          case'house_type':
            $constr .= '住宅类型:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case'rentpaytype':
            $constr .= '付款方式:' . $config[$key][$val] . '>>' . $config[$key][$datainfo[$key]] . ',';
            break;

          case'equipment':
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

          case'setting':
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


  //添加钥匙
  public function add_key($house_id, $key_number, $method)
  {
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //房源信息
    $this->rent_house_model->set_id($house_id);
    $house_info = $this->rent_house_model->get_info_by_id();

    $this->load->model('key_model');

    $datainfo['number'] = $key_number;
    $datainfo['type'] = 2;
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

  //上传举报
  public function add_report()
  {
    $data = array();
    $house_id = $this->input->post('house_id', TRUE);//房源的id
    $report_type = $this->input->post('report_type', TRUE);//举报类型
    $report_text = $this->input->post('report_text', TRUE);//举报的具内容
    $date = time();//举报时间
    $style = '2';//1.为出售 2为出租
    $broker_info = $this->user_arr;
    $brokerinfo_id = $broker_info['broker_id'];//举报人的id
    $brokerinfo_name = $broker_info['truename'];//举报人的姓名
    $this->rent_house_model->set_id($house_id);
    $house_info = $this->rent_house_model->get_info_by_id();
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
      $this->result('0', '不能对自己的房源举报');
    }
    if ($select_num > 0) {
      $this->result('0', '该房源的类型你已经举报过了');
    }

    $return_id = '';
    if (!empty($brokerinfo_id) && !empty($brokered_id) && $brokerinfo_id != $brokered_id && $select_num == 0) {
      $return_id = $this->report_model->insert($insert_data);
    }
    if ($return_id > 0) {
      $this->result('1', '举报成功');
    } else {
      $this->result('1', '举报失败');
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
    $lists = $this->collections_model->get_houserent_byid($where);
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
        $house_list['sell_type'] = array('key' => $list['rent_type'], 'name' => $config['sell_type'][$list['rent_type']]);
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
          $house_list['rentnature'] = array('key' => $list['nature'], 'name' => $config['nature'][$list['nature']]);
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
        $house_list['title'] .= $house_list['price'] . '元/月';
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
      'type' => 2,
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

/* End of file rent.php */
/* Location: ./application/mls/controllers/rent.php */
