<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Cooperate_model CLASS
 *
 * 房客源合作业务逻辑类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
load_m("cooperate_base_model");

class Cooperate_model extends Cooperate_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();

    //设置表名称
    $this->set_table('cooperate');
    $this->set_attached_tbl('cooperate_attached');
    $this->set_log_tbl('cooperate_log');
  }


  /*
   * 添加合作信息
   * @parames array $data_arr
   * @return  array
   */
  public function add_cooperate($data_arr)
  {
    $result = array();

    //客源编号类型
    $order_type = '';

    if ($data_arr['tbl'] == 'sell') {
      $order_type = $data_arr['customer_id'] > 0 ? 'buy_customer' : 'sell';
    } else if ($data_arr['tbl'] == 'rent') {
      $order_type = $data_arr['customer_id'] > 0 ? 'rent_customer' : 'rent';
    }

    $order_sn = $this->build_order_sn($order_type);

    $time = time();
    $step = 1;
    $esta = 1;
    $who_do = 1;//下步骤由甲方推荐

    $data = array(
      'order_sn' => $order_sn,
      'step' => $step,
      'esta' => $esta,
      'tbl' => $data_arr['tbl'],
      'rowid' => $data_arr['rowid'],
      'customer_id' => $data_arr['customer_id'],
      'agentid_a' => $data_arr['agentid_a'],
      'brokerid_a' => $data_arr['brokerid_a'],
      'block_name' => $data_arr['block_name'],
      'broker_name_a' => $data_arr['broker_name_a'],
      'phone_a' => $data_arr['phone_a'],
      'agentid_b' => $data_arr['agentid_b'],
      'brokerid_b' => $data_arr['brokerid_b'],
      'broker_name_b' => $data_arr['broker_name_b'],
      'phone_b' => $data_arr['phone_b'],
      'apply_type' => $data_arr['apply_type'],
      'who_do' => $who_do,
      'creattime' => $time,
      'dateline' => $time
    );

    if (isset($data_arr['reward_type']) && !empty($data_arr['reward_type'])) {
      $data['reward_type'] = $data_arr['reward_type'];
    }
    if (isset($data_arr['cooperate_reward']) && !empty($data_arr['cooperate_reward'])) {
      $data['reward_money'] = $data_arr['cooperate_reward'];
    }

    $cid = $this->add_data($data, 'db_city', $this->tbl);

    if ($cid > 0) {
      //添加附表数据
      $data = array(
        'id' => $cid,
        'house' => $data_arr['house'],
        'broker_a' => $data_arr['broker_a'],
        'broker_b' => $data_arr['broker_b'],
        'step_time' => $time
      );

      $this->add_data($data, 'db_city', $this->tbl_att);

      //添加日志记录
      $log_arr = array();
      $log_arr['cid'] = $cid;
      $log_arr['broker_id'] = $data_arr['brokerid_b'];
      $log_arr['step'] = $step;
      $log_arr['esta'] = $esta;
      $log_arr['dateline'] = $time;
      $this->add_cooperate_log($log_arr);

      $result = array('cid' => $cid, 'order_sn' => $order_sn);
    }

    return $result;
  }


  /*
   * 读取申请合作的经纪人信息
   * @parames int $broker_id
   * @return array
   */
  public function get_cooperate_broker($broker_id)
  {
    $broker_info = array();
    $broker_id = intval($broker_id);

    if ($broker_id > 0) {
      //经纪人中介公司基本信息
      $this->load->model('api_broker_model');
      $broker = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
      //echo '<pre>';print_r($broker);die;
      if (!empty($broker) && $broker['broker_id'] > 0) {
        $broker_info['broker_id'] = $broker['broker_id'];//编号
        $broker_info['phone'] = $broker['phone'];//电话号码
        $broker_info['photo'] = $broker['photo'];//头像
        $broker_info['trust'] = $broker['trust'];//信用值
        $broker_info['credit'] = $broker['credit'];//积分
        $broker_info['cop_suc_ratio'] = $broker['cop_suc_ratio'];//合作成功率
        $broker_info['truename'] = $broker['truename'];//真实姓名
        $broker_info['agency_id'] = $broker['agency_id'];//门店编号
        $broker_info['agency_name'] = $broker['agency_name'];//公司名称
        $broker_info['company_id'] = $broker['company_id'];//公司ID
      }
      unset($broker);
    }
    return $broker_info;
  }


  /*
   * 读取申请合作的房源信息
   * @parames string $tbl
   * @parames int $rowid
   * @return array
   */
  public function get_cooperate_house($tbl, $rowid)
  {
    $house_info = array();
    $tbl = $tbl == 'sell' ? 'sell' : 'rent';
    $rowid = intval($rowid);

    if ($rowid > 0) {
      //经纪人中介公司基本信息
      if ($tbl == 'sell') {
        $this->load->model('sell_house_model', 'house_model');
      } else {
        $this->load->model('rent_house_model', 'house_model');
      }

      $house = $this->house_model->get_hezuo_info($rowid);
      //echo '<pre>';print_r($house);die;

      if (isset($house) && !empty($house['id']) && $house['id'] > 0) {
        $house_info['tbl'] = $tbl;
        $house_info['broker_id'] = $house['broker_id'];
        $house_info['floor'] = $house['floor'];
        $house_info['floor_type'] = $house['floor_type'];
        $house_info['subfloor'] = $house['subfloor'];
        $house_info['totalfloor'] = $house['totalfloor'];
        $house_info['rowid'] = $house['id'];
        $house_info['photo'] = isset($house['photo']) ? $house['photo'] : '';
        $house_info['districtname'] = $house['district_name'];
        $house_info['streetname'] = $house['street_name'];
        $house_info['blockname'] = $house['block_name'];
        $house_info['room'] = $house['room'];
        $house_info['hall'] = $house['hall'];
        $house_info['telno1'] = $house['telno1'];
        $house_info['telno2'] = $house['telno2'];
        $house_info['telno3'] = $house['telno3'];
        $house_info['toilet'] = $house['toilet'];
        $house_info['fitment'] = $house['fitment'];
        $house_info['forward'] = $house['forward'];
        $house_info['price'] = $house['price'];
        $house_info['price_danwei'] = $house['price_danwei'];
        $house_info['buildarea'] = $house['buildarea'];
        $house_info['title'] = $house['title'];
        $house_info['buildyear'] = $house['buildyear'];
        $house_info['cooperate_reward'] = $house['cooperate_reward'];
        $house_info['reward_type'] = $house['reward_type'];
        $house_info['commission_ratio'] = $house['commission_ratio'];
      }

      unset($house);
    }

    return $house_info;
  }


  /*
   * 根据合作编号更新合作信息
   * @parames int $cid 合作编号
   * @parames array $up_arr 更新内容
   * @param	boolean  $escape 是否转义更新字段的值
   * @return int 影响行数
   */
  public function update_cooperation_by_id($cid, $up_arr, $escape = TRUE)
  {
    $msg = 0;

    if ($cid > 0 && !empty($up_arr)) {
      $cond_where = 'id = ' . $cid;
      $msg = parent::update_info_by_cond($update_arr, $cond_where, $escape);
    }

    return $msg;
  }


  /**
   * 根据经纪人编号和条件获取我发起的合作数量
   *
   * @access  public
   * @param  int $broker_id 发起合作的经纪人ID
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_cooperate_num_apply($broker_id, $cond_where)
  {
    $cooperate_num = 0;
    $broker_id = intval($broker_id);
    $cond_where = "brokerid_b = " . $broker_id . " AND " . $cond_where;
    $cooperate_num = parent::get_cooperate_num_by_cond($cond_where);

    return $cooperate_num;
  }


  /**
   * 根据经纪人编号和条件获取我发起的合作列表
   *
   * @access  public
   * @param  int $broker_id 发起合作的经纪人ID
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   合作列表
   */
  public function get_cooperate_lists_apply($broker_id, $cond_where, $offset = 0, $limit = 10,
                                            $order_key = 'dateline', $order_by = 'DESC')
  {
    $cooperate_list = array();

    $broker_id = intval($broker_id);
    $cond_where = "brokerid_b = " . $broker_id . " AND " . $cond_where;
    $cooperate_list = parent::get_list_by_cond($cond_where, $offset, $limit, $order_key, $order_by);

    return $cooperate_list;
  }


  /**
   * 根据经纪人编号和条件获取我收到的合作数量
   *
   * @access  public
   * @param  int $broker_id 发起合作的经纪人ID
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_cooperate_num_accept($broker_id, $cond_where)
  {
    $cooperate_num = 0;
    $broker_id = intval($broker_id);
    $cond_where = "brokerid_a = " . $broker_id . " AND " . $cond_where;
    $cooperate_num = parent::get_cooperate_num_by_cond($cond_where);

    return $cooperate_num;
  }


  /**
   * 根据经纪人编号和条件获取我收到的合作列表
   *
   * @access  public
   * @param  int $broker_id 发起合作的经纪人ID
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   合作列表
   */
  public function get_cooperate_lists_accept($broker_id, $cond_where, $offset = 0, $limit = 10,
                                             $order_key = 'dateline', $order_by = 'DESC')
  {
    $cooperate_list = array();

    $broker_id = intval($broker_id);
    $cond_where = "brokerid_a = " . $broker_id . " AND " . $cond_where;
    $cooperate_list = parent::get_list_by_cond($cond_where, $offset, $limit, $order_key, $order_by);

    return $cooperate_list;
  }


  /**
   * 根据客源编号获取客源合作条数
   *
   * @access  public
   * @param  int $broker_id 发起合作的经纪人ID
   * @param  string $type 合作房源类型
   * @return  int   符合条件的信息条数
   */
  public function get_cooperate_num_by_cid($cid, $tbl)
  {
    $cooperate_num = 0;
    $cid = intval($cid);
    $tbl = strip_tags($tbl);

    if ($cid > 0 && !empty($tbl)) {
      $cond_where = "customer_id = " . $cid . " AND tbl = '" . $tbl . "'";
      $cooperate_num = parent::get_cooperate_num_by_cond($cond_where);
    }

    return $cooperate_num;
  }


  /**
   * 根据客源编号获取客源合作数据
   *
   * @access  public
   * @param  int $cid 发起合作的经纪人ID
   * @param  string $tbl 合作房源类型
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   合作列表
   */
  public function get_cooperate_lists_by_cid($cid, $tbl, $offset = 0, $limit = 10,
                                             $order_key = 'dateline', $order_by = 'DESC')
  {
    $cooperate_list = array();
    $cid = intval($cid);
    $tbl = strip_tags($tbl);

    if ($cid > 0 && !empty($tbl)) {
      $cond_where = "customer_id = " . $cid . " AND tbl = '" . $tbl . "'";
      $cooperate_list = parent::get_list_by_cond($cond_where, $offset, $limit, $order_key, $order_by);
    }

    return $cooperate_list;
  }


  /**
   * 根据房源编号获取房源合作条数
   *
   * @access  public
   * @param  int $houseid 房源编号
   * @param  string $type 合作房源类型
   * @return  int   符合条件的信息条数
   */
  public function get_cooperate_num_by_houseid($houseid, $tbl, $cond_where_cp = '')
  {
    $cooperate_num = 0;
    $houseid = intval($houseid);
    $tbl = strip_tags($tbl);

    if ($houseid > 0 && !empty($tbl)) {
      $cond_where = "rowid = " . $houseid . " AND tbl = '" . $tbl . "'";
      $cond_where .= !empty($cond_where_ext) ? ' AND ' . $cond_where_ext : '';
      $cooperate_num = parent::get_cooperate_num_by_cond($cond_where);
    }

    return $cooperate_num;
  }


  /**
   * 根据房源编号获取房源合作数据
   *
   * @access  public
   * @param  int $houseid 房源编号
   * @param  string $tbl 合作房源类型
   * @param  string $cond_where_cp 其它搜索条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   合作列表
   */
  public function get_cooperate_lists_by_houseid($houseid, $tbl, $cond_where_ext = '', $offset = 0, $limit = 10,
                                                 $order_key = 'dateline', $order_by = 'DESC')
  {
    $cooperate_list = array();
    $houseid = intval($houseid);
    $tbl = strip_tags($tbl);

    if ($houseid > 0 && !empty($tbl)) {
      $cond_where = "rowid = " . $houseid . " AND tbl = '" . $tbl . "'";
      $cond_where .= !empty($cond_where_ext) ? ' AND ' . $cond_where_ext : '';
      $cooperate_list = parent::get_list_by_cond($cond_where, $offset, $limit, $order_key, $order_by);
    }

    return $cooperate_list;
  }


  /**
   * 查询经纪人是否已经申请合作过指定的房源
   *
   * @access  public
   * @param  mixed $houseid 房源编号 数组/整型
   * @param  string $tbl 合作类型
   * @param  int $broker_id 经纪人编号
   * @param  string $cond_where_cp 其它查询条件
   * @return  array   数组，key为房源编号，value 1已经合作，0未合作
   */
  public function check_is_cooped_by_houseid($houseid, $tbl, $broker_id, $cond_where_cp = '')
  {
    $check_result = array();
    $broker_id = intval($broker_id); //经纪人编号
    $tbl = strip_tags($tbl); //房源类型
    $cond_where = '';

    //查询经纪人是否已经申请过该客源
    if (is_array($houseid)) {
      if (!empty($houseid) && $broker_id > 0 && !empty($tbl)) {
        $houseid_str = implode(",", $houseid);
        $cond_where = "rowid IN(" . $houseid_str . ") AND tbl = '" . $tbl . "' "
          . "AND brokerid_b = '" . $broker_id . "' AND esta IN (1,2,3,4,7)";
      }
      $house_num = count($houseid); //房源编号个数
    } else {
      $houseid = intval($houseid);//房源编号
      $cond_where = "rowid = " . $houseid . " AND tbl = '" . $tbl . "' "
        . "AND brokerid_b = '" . $broker_id . "' AND esta IN (1,2,3,4,7)";

      $house_num = 1; //房源编号个数
    }

    $arr_data = array();
    if ($cond_where != '') {
      $cond_where .= !empty($cond_where_ext) ? ' AND ' . $cond_where_ext : '';
      //合作信息表
      $tbl_name = $this->get_tbl();
      //查询字段
      $this->dbback_city->select('rowid');
      //查询条件
      $this->dbback_city->where($cond_where);
      //查询
      $arr_data = $this->dbback_city->get($tbl_name)->result_array();
    }

    //循环查到的数据，匹配
    if (is_array($arr_data) && !empty($arr_data)) {
      for ($i = 0; $i < $house_num; $i++) {
        foreach ($arr_data as $key => $value) {
          if ($value['rowid'] == $houseid[$i]) {
            $check_result[$houseid[$i]] = 1;
            break;
          } elseif ($value['rowid'] == $houseid) {
            $check_result[$houseid] = 1;
            break;
          } else {
            $check_result[$houseid[$i]] = 0;
          }
        }
      }
    } else {
      if (is_array($houseid) && !empty($houseid)) {
        for ($i = 0; $i < $house_num; $i++) {
          $check_result[$houseid[$i]] = 0;
        }
      } else {
        $check_result = array();
      }

    }

    return $check_result;
  }


  /**
   * 查询经纪人是否已经申请合作过指定的客源
   *
   * @access  public
   * @param  mixed $cid 客源编号 数组/整型
   * @param  string $tbl 合作类型
   * @param  int $broker_id 经纪人编号
   * @param  string $cond_where_cp 其它查询条件
   * @return  array   数组，key为客源编号，value 1已经合作，0未合作
   */
  public function check_is_cooped_by_cid($cid, $tbl, $broker_id, $cond_where_cp = '')
  {
    $check_result = array();
    $broker_id = intval($broker_id); //经纪人编号
    $tbl = strip_tags($tbl); //房源类型
    $cond_where = '';

    //查询经纪人是否已经申请过该客源
    if (is_array($cid)) {
      if (!empty($cid) && $broker_id > 0 && !empty($tbl)) {
        $cid_str = implode(",", $cid);
        $cond_where = "customer_id IN(" . $cid_str . ") AND tbl = '" . $tbl . "' "
          . "AND brokerid_b = '" . $broker_id . "' AND esta IN (1,2,3,4,7)";
      }

      $customer_num = count($cid); //客源编号个数
    } else {
      $cid = intval($cid);//房源编号
      $cond_where = "customer_id = " . $cid . " AND tbl = '" . $tbl . "' "
        . "AND brokerid_b = '" . $broker_id . "' AND esta IN (1,2,3,4,7)";

      $customer_num = 1; //房源编号个数
    }

    $arr_data = array();
    if ($cond_where != '') {
      $cond_where .= !empty($cond_where_ext) ? ' AND ' . $cond_where_ext : '';

      //合作信息表
      $tbl_name = $this->get_tbl();
      //查询字段
      $this->dbback_city->select('customer_id');
      //查询条件
      $this->dbback_city->where($cond_where);
      //查询
      $arr_data = $this->dbback_city->get($tbl_name)->result_array();
    }

    //循环查到的数据，匹配
    if (is_array($arr_data) && !empty($arr_data)) {
      for ($i = 0; $i < $customer_num; $i++) {
        foreach ($arr_data as $key => $value) {
          if ($value['customer_id'] == $cid[$i]) {
            $check_result[$cid[$i]] = 1;
            break;
          } else {
            $check_result[$cid[$i]] = 0;
          }
        }
      }
    } else {
      for ($i = 0; $i < $customer_num; $i++) {
        $check_result[$cid[$i]] = 0;
      }
    }

    return $check_result;
  }

  /**
   * 接收合作
   *
   * @access  public
   * @param  int $cid 合作编号
   * @param  int $broker_id 被申请经纪人编号
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function accept_cooperation($cid, $broker_id, $escape = TRUE)
  {
    $esta = 4;//状态
    $who_do = 3;//下步甲方操作
    $step = 3;//日志步骤
    $time = time();

    $up_arr = array();
    $up_arr['step'] = $step;
    $up_arr['esta'] = $esta;
    $up_arr['who_do'] = $who_do;
    $up_arr['dateline'] = $time;

    $up_num = 0;
    $cond_where = "id = '" . $cid . "' AND brokerid_a = '" . $broker_id . "'";
    $up_num = parent::update_info_by_cond($up_arr, $cond_where, $escape);

    //添加日志记录
    if ($up_num > 0) {
      $log_arr = array();
      $log_arr['cid'] = $cid;
      $log_arr['step'] = $step;
      $log_arr['esta'] = $esta;
      $log_arr['broker_id'] = $broker_id;
      $log_arr['dateline'] = $time;
      $this->add_cooperate_log($log_arr);
    }

    return $up_num;
  }


  /**
   * 拒绝合作
   *
   * @access  public
   * @param  int $cid 合作编号
   * @param  int $broker_id 被申请经纪人编号
   * @param  int $step 操作步骤
   * @param  string $refuse_reason 合作失败原因
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function refuse_to_cooperation($cid, $broker_id, $step, $refuse_reason, $escape = TRUE)
  {
    $step = intval($step);
    $esta = 5;
    $now_time = time();
    $up_arr = array();
    $up_arr['step'] = $step;
    $up_arr['esta'] = 5;
    $up_arr['who_do'] = 0;
    $up_arr['dateline'] = $now_time;
    $cond_where = "id = '" . $cid . "' AND ( brokerid_a = '" . $broker_id . "' OR brokerid_b = '" . $broker_id . "') ";

    $up_num = 0;
    $up_num = parent::update_info_by_cond($up_arr, $cond_where, $escape);

    if ($up_num > 0) {
      $up_att_arr = array();
      $up_att_arr['refuse_reason'] = serialize($refuse_reason);
      $up_att_arr['step_time'] = $now_time;
      $cond_where = "id = '" . $cid . "'";
      parent::update_cooperate_att_by_cond($up_att_arr, $cond_where);

      //添加日志
      $log_arr = array();
      $log_arr['cid'] = $cid;
      $log_arr['step'] = $step;
      $log_arr['esta'] = $esta;
      $log_arr['broker_id'] = $broker_id;
      $log_arr['dateline'] = time();
      $result = $this->add_cooperate_log($log_arr);

    }

    return $up_num;
  }


  /**
   * 取消合作
   *
   * @access  public
   * @param  int $cid 合同编号
   * @param  int $broker_id 被申请经纪人编号
   * @param  array $cancle_arr 取消类型、说明、步骤、取消经纪人
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function cancle_cooperation($cid, $broker_id, $cancle_arr, $escape = TRUE)
  {
    $now_time = time();
    $up_arr = array();
    $up_arr['step'] = $cancle_arr['step'];
    $up_arr['esta'] = 6;
    $up_arr['who_do'] = 0;
    $up_arr['dateline'] = $now_time;
    $cond_where = "id = '" . $cid . "' AND ( brokerid_a = '" . $broker_id . "' OR brokerid_b = '" . $broker_id . "') ";
    $up_num = 0;
    $up_num = parent::update_info_by_cond($up_arr, $cond_where, $escape);

    if ($up_num > 0) {
      $up_att_arr = array();
      $up_att_arr['cancel_reason'] = serialize($cancle_arr);
      $up_att_arr['step_time'] = $now_time;
      $cond_where = "id = '" . $cid . "'";
      parent::update_cooperate_att_by_cond($up_att_arr, $cond_where);
    }

    return $up_num;
  }


  /**
   * 提交佣金方案
   *
   * @access  public
   * @param  int $cid 合作编号
   * @param  int $broker_id 被申请经纪人编号
   * @param  array $commission_arr 佣金方案数组
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function sub_allocation_scheme($cid, $broker_id, $commission_arr, $escape = TRUE)
  {
    $step = 2;
    $now_time = time();

    $up_arr = array();
    $up_arr['step'] = $step;
    $up_arr['esta'] = 3;
    $up_arr['who_do'] = 2;//下步流程由乙方来推荐
    $up_arr['dateline'] = $now_time;
    $cond_where = "id = '" . $cid . "' AND brokerid_a = '" . $broker_id . "'";
    $up_num = 0;
    $up_num = parent::update_info_by_cond($up_arr, $cond_where, $escape);

    if ($up_num > 0) {
      $up_att_arr = array();
      $up_att_arr['ratio'] = serialize($commission_arr);
      $up_att_arr['step_time'] = $now_time;
      $cond_where = "id = '" . $cid . "'";

      parent::update_cooperate_att_by_cond($up_att_arr, $cond_where);

      //添加日志
      $log_arr = array();
      $log_arr['cid'] = $cid;
      $log_arr['broker_id'] = $broker_id;
      $log_arr['step'] = 2;
      $log_arr['esta'] = 3;
      $log_arr['dateline'] = time();
      $result = $this->add_cooperate_log($log_arr);
    }

    return $up_num;
  }


  /**
   * 确认佣金方案
   *
   * @access  public
   * @param  int $cid 合同编号
   * @param  int $broker_id 被申请经纪人编号
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function confirm_allocation_scheme($cid, $broker_id, $escape = TRUE)
  {
    $up_num = 0;
    $up_arr = array();
    $up_arr['step'] = 3;
    $up_arr['esta'] = 4;
    $up_arr['who_do'] = 3;
    $up_arr['dateline'] = time();

    $cond_where = "id = '" . $cid . "' AND brokerid_b = '" . $broker_id . "'";
    $up_num = parent::update_info_by_cond($up_arr, $cond_where, $escape);

    //添加操作日志
    if ($up_num > 0) {
      $log_arr = array();
      $log_arr['cid'] = $cid;
      $log_arr['broker_id'] = $broker_id;
      $log_arr['step'] = 2;
      $log_arr['esta'] = 4;
      $log_arr['dateline'] = time();
      $this->add_cooperate_log($log_arr);
    }

    return $up_num;
  }


  /**
   * 提交交易总额
   *
   * @access  public
   * @param  int $cid 合同编号
   * @param  int $broker_id 被申请经纪人编号
   * @param  int $esta 合作状态
   * @param  float $total_price 成交总价
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function sub_total_price($cid, $broker_id, $esta, $total_price, $escape = TRUE)
  {
    $up_num = 0;
    $time = time();

    $up_arr = array();
    $up_arr['step'] = 4;
    $up_arr['esta'] = intval($esta);
    $up_arr['who_do'] = 0;
    $up_arr['price'] = floatval($total_price);
    $up_arr['dateline'] = $time;
    $cond_where = "id = '" . $cid . "' ";

    $up_num = parent::update_info_by_cond($up_arr, $cond_where, $escape);

    if ($up_num > 0) {
      $log_arr = array();
      $log_arr['cid'] = $cid;
      $log_arr['broker_id'] = $broker_id;
      $log_arr['step'] = 3;
      $log_arr['esta'] = 7;
      $log_arr['dateline'] = $time;
      $result = $this->add_cooperate_log($log_arr);
    }

    return $up_num;
  }


  /**
   * 提交交易失败结果
   *
   * @access  public
   * @param  int $cid 合同编号
   * @param  int $broker_id 被申请经纪人编号
   * @param  int $esta 合作结果状态
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function sub_cooperate_result($cid, $broker_id, $esta, $escape = TRUE)
  {
    $up_num = 0;
    $up_arr = array();
    $esta = intval($esta);
    $time = time();

    if ($esta > 0) {
      $up_arr['step'] = 4;
      $up_arr['esta'] = $esta;
      $up_arr['who_do'] = 0;
      $up_arr['dateline'] = $time;
      $cond_where = "id = '" . $cid . "' ";
      $up_num = parent::update_info_by_cond($up_arr, $cond_where, $escape);

      if ($up_num > 0) {
        $log_arr = array();
        $log_arr['cid'] = $cid;
        $log_arr['broker_id'] = $broker_id;
        $log_arr['step'] = 3;
        $log_arr['esta'] = $esta;
        $log_arr['dateline'] = $time;
        $result = $this->add_cooperate_log($log_arr);
      }
    }

    return $up_num;
  }


  /**
   * 成交逾期失败
   *
   * @access  public
   * @param  int $cid 合同编号
   * @param  int $broker_id 操作经纪人
   * @return  int 更新影响行数
   */
  public function overdue_failed_cooperate($cid)
  {
    $up_num = 0;
    $up_arr = array();
    $esta = 9;
    $time = time();

    if ($esta > 0) {
      $up_arr['step'] = 4;
      $up_arr['esta'] = 9;
      $up_arr['who_do'] = 0;
      $up_arr['dateline'] = $time;
      $cond_where = "id = '" . $cid . "' ";
      $up_num = parent::update_info_by_cond($up_arr, $cond_where);

      if ($up_num > 0) {
        $log_arr = array();
        $log_arr['cid'] = $cid;
        $log_arr['broker_id'] = 0;
        $log_arr['step'] = 3;
        $log_arr['esta'] = $esta;
        $log_arr['dateline'] = $time;
        $result = $this->add_cooperate_log($log_arr);
      }
    }

    return $up_num;
  }


  /**
   * 获取合同操作日志
   *
   * @access  public
   * @param  int $cid 合同编号
   * @return  array 合同日志
   */
  public function get_cooperation_log_by_cid($cid)
  {
    $log_arr = array();
    $cid = intval($cid);

    if ($cid > 0) {
      $tbl_name = $this->get_log_tbl();
      $cond_where = "cid = " . $cid;
      $this->dbback_city->where($cond_where);
      $log_arr = $this->dbback_city->get($tbl_name)->result_array();
    }

    return $log_arr;
  }


  /**
   * 根据合作经纪人编号和房源编号获取合作数量
   *
   * @access  public
   * @param  string $tbl 房源类型
   * @param  int $brokerid_a 被申请合作的经纪人ID
   * @param  int $brokerid_b 发起合作的经纪人ID
   * @param  int $row_id 查询条件
   * @return  int  符合条件的合作信息条数
   */
  public function get_valid_cooperate_num($tbl, $brokerid_a, $brokerid_b, $row_id)
  {
    $cooperate_num = 0;
    $tbl = trim(strip_tags($tbl));
    $brokerid_b = intval($brokerid_b);
    $brokerid_a = intval($brokerid_a);
    $row_id = intval($row_id);

    $cond_where = "tbl = '" . $tbl . "' AND rowid = " . $row_id . " AND brokerid_b = " . $brokerid_b . " AND "
      . "brokerid_b = " . $brokerid_b . " AND esta IN (1,2,3,4,7)";
    $cooperate_num = parent::get_cooperate_num_by_cond($cond_where);

    return $cooperate_num;
  }


  /*
   * 终止合作
   * 房源下架\失效\删除、取消合作、合作成交成功都会触发终止合作操作
   * @param int $houseid 房源编号
   * @param string $tbl 房源类型 sell/rent
   * @param sting $stop_reason 终止合作原因字符串
   */
  public function stop_cooperate($houseid, $tbl, $stop_reason)
  {
    if ($houseid > 0 && !empty($tbl) && in_array($tbl, array('sell', 'rent'))) {
      //查询房源编号所参与的合同编号
      $cond_where_ext = 'esta IN (1,2,3,4)';
      $cooperate_num = $this->get_cooperate_num_by_houseid($houseid, $tbl, $cond_where_ext);

      if ($cooperate_num > 0) {
        $per_num = 5;
        $loop_num = ceil($cooperate_num / $per_num);

        $select_fields = array('id', 'order_sn', 'esta', 'tbl', 'rowid', 'brokerid_a', 'broker_name_a', 'brokerid_b', 'broker_name_b');
        $this->set_select_fields($select_fields);

        for ($i = 0; $i < $loop_num; $i++) {
          $offset = $i * $per_num;
          $cooperate_info = $this->get_cooperate_lists_by_houseid($houseid, $tbl, $cond_where_ext, $offset, $per_num);

          $search_num = count($cooperate_info);
          if ($search_num > 0) {
            foreach ($cooperate_info as $key => $value) {
              $esta = $value['esta'];
              //根据合同编号更新合同为终止状态，并填写终止原因
              $up_num = $this->update_cooperate_stop($value['id'], $stop_reason);
              $this->update_cooperate_esta_log($value['id'], 11);
              if ($up_num > 0) {
                switch ($stop_reason) {
                  case 'deal_house':  //成交房源
                    $message_type1 = '1-10-10-1';
                    $message_type2 = '1-10-10-2';
                    $message_type3 = '1-10-10-3';
                    $message_type4 = '1-10-10-4';
                    break;
                  case 'invalid_house':   //失效房源
                    $message_type1 = '1-10-11-1';
                    $message_type2 = '1-10-11-2';
                    $message_type3 = '1-10-11-3';
                    $message_type4 = '1-10-11-4';
                    break;
                  case 'delete_house':    //删除房源
                    $message_type1 = '1-10-12-1';
                    $message_type2 = '1-10-12-2';
                    $message_type3 = '1-10-12-3';
                    $message_type4 = '1-10-12-4';
                    break;
                  /*
                  case 'down_house':  //下架房源
                      $message_type1 = '1-10-10-1';
                      $message_type2 = '1-10-10-2';
                      $message_type3 = '1-10-10-3';
                      $message_type4 = '1-10-10-4';
                  break;

                   */
                  case 'reserve_house':   //预定房源
                    $message_type1 = '1-10-13-1';
                    $message_type2 = '1-10-13-2';
                    $message_type3 = '1-10-13-3';
                    $message_type4 = '1-10-13-4';
                    break;
                  case 'private_house':   //取消合作
                    $message_type1 = '1-10-14-1';
                    $message_type2 = '1-10-14-2';
                    $message_type3 = '1-10-14-3';
                    $message_type4 = '1-10-14-4';
                    break;
                  case 'cancel_house':   //取消合作
                    $message_type1 = '1-10-15-1';
                    $message_type2 = '1-10-15-2';
                    $message_type3 = '1-10-15-3';
                    $message_type4 = '1-10-15-4';
                    break;
                  case 'cop_deal_house':   //合同内房源成交
                    $message_type1 = '1-10-9';
                    break;
                }

                if ($message_type1 != '') {
                  $broker_id = $value['brokerid_b'];
                  $broker_name = $value['broker_name_b'];
                  $order_sn = $value['order_sn'];
                  $url = '/cooperate/accept_order_list/?cid=' . $value['id'];
                  $params['type'] = 'f';
                  $params['id'] = format_info_id($houseid, $tbl);
                  //发送站内信
                  //33
                  $this->load->model('message_base_model');
                  if ($esta == 4) {
                    $this->message_base_model->add_message($message_type4, $broker_id, $broker_name, $url, $params);
                  } else {
                    $this->message_base_model->add_message($message_type2, $broker_id, $broker_name, $url, $params);
                  }
                  if ($message_type2 != '') {
                    $broker_id = $value['brokerid_a'];
                    $broker_name = $value['broker_name_a'];
                    $url = '/cooperate/send_order_list/?cid=' . $value['id'];
                    if ($esta == 4) {
                      $this->message_base_model->add_message($message_type3, $broker_id, $broker_name, $url, $params);
                    } else {
                      $this->message_base_model->add_message($message_type1, $broker_id, $broker_name, $url, $params);
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }

  /*
  * 根据房客源编号、甲方经纪人的编号和其对应的门店编号获取符合条件信息组
  * @param int $rowid 房源编号
  */
  public function get_cooperation_by($rowid, $brokerid_a, $agentid_a)
  {
    $sql = "select * from cooperate where rowid= '" . $rowid . "' and brokerid_a = '" . $brokerid_a . "' and agentid_a = '" . $agentid_a . "' and esta <= 4";
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;

  }


  /**
   * 根据门店id获得当前门店刚发起合作申请的出售房源
   * @param int $agency_id 门店id
   * @return $result_arr 房源id
   */
  public function get_sell_house_cooperate_id_esta_1_by_agency_id($agency_id = 0)
  {
    $this->dbselect('dbback_city');
    $where_sql = '';
    $result_arr = array();
    if (!empty($agency_id)) {
      $where_sql = 'SELECT cooperate.id FROM `cooperate` left join `sell_house` on cooperate.rowid = sell_house.id ';
      $where_sql .= ' where cooperate.tbl = "sell" and cooperate.step = 1 and cooperate.esta = 1 ';
      $where_sql .= 'and sell_house.agency_id = "' . intval($agency_id) . '"';
      $query = $this->db->query($where_sql);
      $result_arr = $query->result_array();
    }
    return $result_arr;
  }

  /**
   * 根据门店id获得当前门店刚发起合作申请的出售房源
   * @param int $agency_id 门店id
   * @return $result_arr 房源id
   */
  public function get_rent_house_cooperate_id_esta_1_by_agency_id($agency_id = 0)
  {
    $this->dbselect('dbback_city');
    $where_sql = '';
    $result_arr = array();
    if (!empty($agency_id)) {
      $where_sql = 'SELECT cooperate.id FROM `cooperate` left join `rent_house` on cooperate.rowid = rent_house.id ';
      $where_sql .= ' where cooperate.tbl = "rent" and cooperate.step = 1 and cooperate.esta = 1 ';
      $where_sql .= 'and rent_house.agency_id = "' . intval($agency_id) . '"';
      $query = $this->db->query($where_sql);
      $result_arr = $query->result_array();
    }
    return $result_arr;
  }

  /**
   * 根据门店id获得当前公司刚发起合作申请的求购客源
   * @param int $agency_id 门店id
   * @return $result_arr 客源id
   */
  public function get_buy_customer_cooperate_id_esta_1_by_agency_id($agency_id = 0)
  {
    $this->dbselect('dbback_city');
    $where_sql = '';
    $result_arr = array();
    if (!empty($agency_id)) {
      $where_sql = 'SELECT cooperate.id FROM `cooperate` left join `buy_customer` on cooperate.customer_id = buy_customer.id ';
      $where_sql .= ' where cooperate.tbl = "sell" and cooperate.step = 1 and cooperate.esta = 1 ';
      $where_sql .= 'and buy_customer.agency_id = "' . intval($agency_id) . '"';
      $query = $this->db->query($where_sql);
      $result_arr = $query->result_array();
    }
    return $result_arr;
  }

  /**
   * 根据门店id获得当前门店刚发起合作申请的求购客源
   * @param int $agency_id 门店id
   * @return $result_arr 客源id
   */
  public function get_rent_customer_cooperate_id_esta_1_by_agency_id($agency_id = 0)
  {
    $this->dbselect('dbback_city');
    $where_sql = '';
    $result_arr = array();
    if (!empty($agency_id)) {
      $where_sql = 'SELECT cooperate.id FROM `cooperate` left join `rent_customer` on cooperate.customer_id = rent_customer.id ';
      $where_sql .= ' where cooperate.tbl = "rent" and cooperate.step = 1 and cooperate.esta = 1 ';
      $where_sql .= 'and rent_customer.agency_id = "' . intval($agency_id) . '"';
      $query = $this->db->query($where_sql);
      $result_arr = $query->result_array();
    }
    return $result_arr;
  }

  public function change_esta_by_cooperate_id($cooperate_id_arr, $refuse_reason)
  {
    $cooperate_id_str = '';
    $result = false;
    if (is_full_array($cooperate_id_arr)) {
      foreach ($cooperate_id_arr as $k => $v) {
        $cooperate_id_str .= $v['id'] . ',';
      }
      $cooperate_id_str = trim($cooperate_id_str, ',');
      $data = array();
      $data['esta'] = 5;
      $data['who_do'] = 0;
      $data['dateline'] = time();
      $cond_where = "id in ($cooperate_id_str)";
      $result = parent::update_info_by_cond($data, $cond_where);

      //附表添加数据
      $up_att_arr = array();
      $up_att_arr['refuse_reason'] = serialize($refuse_reason);
      $up_att_arr['step_time'] = time();
      $cond_where = "id in ($cooperate_id_str)";
      parent::update_cooperate_att_by_cond($up_att_arr, $cond_where);
    }
    return $result;
  }

}

/* End of file cooperate_model.php */
/* Location: ./app/models/cooperate_model.php */
