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
 * Cooperate_base_model CLASS
 *
 * 房客源合作基类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
class Cooperate_base_model extends MY_Model
{

  /**
   * 合作表名
   * @var string
   */
  protected $tbl = 'cooperate';

  /**
   * 合作表副表名
   * @var string
   */
  protected $tbl_att = 'cooperate_attached';

  /**
   * 合作日志步骤表
   * @var string
   */
  protected $tbl_log = 'cooperate_log';

  /**
   * 查询字段
   * @var string
   */
  private $_select_fields = '';


  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 设置合作表名称
   *
   * @access  public
   * @param  string $tbl_name 表名称
   * @return  void
   */
  public function set_tbl($tbl_name)
  {
    $this->tbl = strip_tags($tbl_name);
  }


  /**
   * 获取合作表名称
   *
   * @access  public
   * @param  void
   * @return  string 合作表名称
   */
  public function get_tbl()
  {
    return $this->tbl;
  }


  /**
   * 设置合作副表名称
   *
   * @access  public
   * @param  string $tbl_name 表名称
   * @return  void
   */
  public function set_attached_tbl($tbl_name)
  {
    $this->tbl_att = strip_tags($tbl_name);
  }


  /**
   * 获取合作副表名称
   *
   * @access  public
   * @param  void
   * @return  string 合作副表名称
   */
  public function get_attached_tbl()
  {
    return $this->tbl_att;
  }


  /**
   * 设置合作步骤日志表名称
   *
   * @access  public
   * @param  string $tbl_name 表名称
   * @return  void
   */
  public function set_log_tbl($tbl_name)
  {
    $this->tbl_log = strip_tags($tbl_name);
  }


  /**
   * 获取合作步骤日志表名称
   *
   * @access  public
   * @param  void
   * @return  string 合作表名称
   */
  public function get_log_tbl()
  {
    return $this->tbl_log;
  }


  /**
   * 设置需要查询的字段
   * @param array $select_fields
   */
  public function set_select_fields($select_fields)
  {
    $select_fields_str = '';

    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
    }

    $this->_select_fields = $select_fields_str;
  }


  /**
   * 返回需要查询的字段
   * @param void
   * @return string 查询字段
   */
  public function get_search_fields()
  {
    return $this->_select_fields;
  }


  /**
   * 获取求购、求租信息基础配置信息
   *
   * @access  public
   * @param  array $type 基本信息类型 buy-求购/rent-求租
   * @param  array $key 需求信息编号数组 配置信息Key值
   * @return  array 配置信息数组
   */
  public function get_base_conf($type = '', $key = '')
  {
    $conf_arr = array(
        'esta' => array(1 => '申请中', 4 => '合作生效', 5 => '合作失败', 6 => '已取消合作', 7 => '交易成功', 8 => '成交失败', 9 => '成交逾期失效', 10 => '合作冻结', 11 => '合作终止'),
      'cancel_reason' => array(1 => '房源已成交', 3 => '个人意愿撤销合作', 4 => '其它'),
      'refuse_reason' => array(1 => '房源已成交', 3 => '个人意愿撤销合作', 4 => '其它'),
      'stop_reason' => array('invalid_house' => '房源失效', 'delete_house' => '房源删除', 'reserve_house' => '房源预定',
        'down_house' => '房源下架', 'private_house' => '房源取消合作', 'deal_house' => '房源被修改为已成交状态', 'cop_deal_house' => '该房源优先与其它经纪人成交')
    );

    return $conf_arr;
  }


  /**
   * 生成订单编号
   * @param string $type 订单类型
   * @return 订单编号
   */
  public function build_order_sn($type)
  {
    $order_sn = '';

    $type = strip_tags($type);

    switch ($type) {
      case 'sell':
        $str_type = '01';
        break;
      case 'rent':
        $str_type = '02';
        break;
      case 'buy_customer':
        $str_type = '03';
        break;
      case 'rent_customer':
        $str_type = '04';
        break;
    }

    $order_sn = date('ymdHis') . $str_type . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

    return $order_sn;
  }


  /*
   * 根据合作ID获取合作详细信息
   * @parames int $cid
   * @return array
   */
  public function get_cooperate_by_cid($cid)
  {
    $cid = intval($cid);
    $cooperate_arr = array();

    if ($cid > 0) {
      //查询字段
      if ($this->_select_fields) {
        $this->dbback_city->select($this->_select_fields);
      } else {
        $this->dbback_city->select("$this->tbl.* , $this->tbl_att.*");
      }

      //查询条件
      $this->dbback_city->where($this->tbl . '.id', $cid);
      $this->dbback_city->from($this->tbl);
      $this->dbback_city->join($this->tbl_att, "$this->tbl.id = $this->tbl_att.id");
      $cooperate_arr = $this->dbback_city->get()->row_array();
    }

    return $cooperate_arr;
  }


  /*
   * 根据合作ID获取合作主表基本信息
   * @parames int $cid
   * @return array
   */
  public function get_cooperate_baseinfo_by_cid($cid)
  {
    $cid = intval($cid);
    $cooperate_arr = array();

    if ($cid > 0) {
      //查询字段
      if ($this->_select_fields) {
        $this->dbback_city->select($this->_select_fields);
      }

      //查询条件
      $this->dbback_city->where('id', $cid);
      $this->dbback_city->from($this->tbl);
      $cooperate_arr = $this->dbback_city->get()->row_array();
    }

    return $cooperate_arr;
  }

  /*
     * 根据合同编号获取合作主表基本信息
     * @parames int $cid
     * @return array
     */
  public function get_cooperate_baseinfo_by_order_sn($order_sn)
  {
    $order_sn = intval($order_sn);
    $cooperate_arr = array();

    if ($order_sn > 0) {
      //查询字段
      if ($this->_select_fields) {
        $this->dbback_city->select($this->_select_fields);
      }

      //查询条件
      $this->dbback_city->where('order_sn', $order_sn);
      $this->dbback_city->from($this->tbl);
      $cooperate_arr = $this->dbback_city->get()->row_array();
    }

    return $cooperate_arr;
  }


  /*
   * 根据合作ID获取合作编号
   * @parames int $cid
   * @return string
   */
  public function get_order_sn_by_cid($cid)
  {
    $cid = intval($cid);
    $order_sn = '';
    $cooperate_info = array();

    if ($cid > 0) {
      //查询字段
      $this->dbback_city->select('order_sn');
      //查询条件
      $this->dbback_city->where('id', $cid);
      $this->dbback_city->from($this->tbl);
      $cooperate_info = $this->dbback_city->get()->row_array();
    }

    $order_sn = !empty($cooperate_info) ? $cooperate_info['order_sn'] : '';

    return $order_sn;
  }

  /*
   * 根据合作ID获取合作详细信息
   * @parames int $cid
   * @parames int $agencyid
   * @return array
   */
  public function get_cooperate_by_cid_agencyid($cid, $agencyid)
  {
    $cid = intval($cid);
    $agencyid = intval($agencyid);
    $cooperate_arr = array();

    if ($cid > 0 && $agencyid > 0) {
      //查询字段
      if ($this->_select_fields) {
        $this->dbback_city->select($this->_select_fields);
      } else {
        $this->dbback_city->select("$this->tbl.id, $this->tbl.esta, $this->tbl.who_do, $this->tbl.price, $this->tbl_att.*");
      }

      //查询条件
      $wheresql = $this->tbl . ".id = '" . $cid . "' AND (" . $this->tbl . ".agentid_a = '" . $agencyid . "' or " . $this->tbl . ".agentid_b = '" . $agencyid . "')";
      $this->dbback_city->where($wheresql);
      $this->dbback_city->from($this->tbl);
      $this->dbback_city->join($this->tbl_att, "$this->tbl.id = $this->tbl_att.id");
      $cooperate_arr = $this->dbback_city->get()->row_array();
    }

    return $cooperate_arr;
  }


  /**
   * 更新合作状态
   *
   * @access  public
   * @param  array $update_arr 需要更新字段的键值对
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function update_info_by_cond($update_arr, $cond_where, $escape = TRUE)
  {
    $tbl_name = $this->get_tbl();

    $up_num = 0;

    if ($tbl_name == '' || empty($update_arr) || $cond_where == '') {
      return $up_num;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($tbl_name);
    $up_num = $this->db_city->affected_rows();

    return $up_num;
  }


  /**
   * 获取符合条件的合作数量
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_cooperate_num_by_cond($cond_where)
  {
    $count_num = 0;

    //合作表名称
    $tbl_name = $this->get_tbl();

    //查询条件
    if ($cond_where != '' && $tbl_name != '') {
      $this->dbback_city->where($cond_where);
      $count_num = $this->dbback_city->count_all_results($tbl_name);
    }

    return intval($count_num);
  }

  /**
   * 获取符合条件的合作数量
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_cooperate_effect_num_by_cond($cond_where)
  {
    $count_num = 0;

    //合作表名称
    $tbl_name = $this->get_tbl();

    //查询条件
    if ($cond_where != '' && $tbl_name != '') {
      $this->db_city->where($cond_where);
      $count_num = $this->db_city->count_all_results($tbl_name);
    }

    return intval($count_num);
  }

  /**
   * 获取符合条件的合作信息列表
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   合作列表数组
   */
  public function get_list_by_cond($cond_where, $offset = 0, $limit = 10,
                                   $order_key = 'dateline', $order_by = 'DESC')
  {
    //合作信息表
    $tbl_name = $this->get_tbl();

    //需要查询的房源需求信息字段
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      $this->dbback_city->select($select_fields);//查询字段
    }

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }

    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);

    //查询
    $arr_data = $this->dbback_city->get($tbl_name, $limit, $offset)->result_array();

    return $arr_data;
  }

  /**
   * 获取符合条件的合作信息列表
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   合作列表数组
   */
  public function get_list_by_cond2($cond_where, $cond_or_where, $offset = 0, $limit = 10,
                                    $order_key = 'dateline', $order_by = 'DESC')
  {
    $tbl_name = $this->get_tbl();
    //合作信息表

    //需要查询的房源需求信息字段
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      $this->dbback_city->select($select_fields);//查询字段
    }

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }

    if ($cond_or_where != '') {
      $this->dbback_city->or_where($cond_or_where);
    }

    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);

    //查询
    $arr_data = $this->dbback_city->get($tbl_name, $limit, $offset)->result_array();
    return $arr_data;
  }


  /**
   * 根据查询条件返回合作状态
   *
   * @access  public
   * @where  array   查询条件
   * @return  int     esta 状态
   */
  public function get_cooperate_baseinfo_esta($where)
  {
    $esta = '';
    if ($where) {
      $filed = 'esta';
      $tbl = 'cooperate';
      $this->dbback_city->select($filed);
      $this->dbback_city->where($where);
      $esta = $this->dbback_city->get($tbl)->row_array();
    }

    return $esta;
  }

  /**
   * 添加合作步骤日志
   *
   * @access  public
   * @param  array $log_arr 步骤日志
   * @return  int     日志ID
   */
  public function add_cooperate_log($log_arr)
  {
    //日志表名称
    $tbl_name = $this->get_log_tbl();
    $insert_id = 0;

    if ($tbl_name != '' && is_array($log_arr) && !empty($log_arr)) {
      $this->db_city->insert($tbl_name, $log_arr);

      //如果插入成功，则返回插入的id
      if (($this->db_city->affected_rows()) >= 1) {
        $insert_id = $this->db_city->insert_id();
      }
    }

    return $insert_id;
  }


  /**
   * 添加合作步骤日志
   *
   * @access  public
   * @param  array $log_arr 步骤日志
   * @return  int     日志ID
   */
  public function update_cooperate_esta_log($cid, $esta)
  {
    //日志表名称
    $tbl_name = $this->get_log_tbl();

    //设置条件
    $this->db_city->set('esta', $esta);
    $this->db_city->where('cid', $cid);

    //排序条件
    $this->db_city->limit(1);
    $this->db_city->order_by('id', 'desc');
    //更新数据
    $this->db_city->update($tbl_name);
    $up_num = $this->db_city->affected_rows();
  }

  /**
   * 更新合作附表信息
   *
   * @access  public
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function update_cooperate_att_by_cond($up_arr, $cond_where, $escape = TRUE)
  {
    $tbl_name = $this->get_attached_tbl();

    $up_num = 0;
    if ($tbl_name == '' || empty($up_arr) || $cond_where == '') {
      return $up_num;
    }

    foreach ($up_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($tbl_name);
    $up_num = $this->db_city->affected_rows();

    return $up_num;
  }


  /**
   * 获取合作附表信息
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  array  合作附表信息数组
   */
  public function get_att_list_by_cond($cond_where)
  {
    //合作附表
    $tbl_name = $this->get_attached_tbl();

    //需要查询的房源需求信息字段
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      $this->dbback_city->select($select_fields);
    }

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }

    //查询
    $arr_data = $this->dbback_city->get($tbl_name)->result_array();

    return $arr_data;
  }


  /**
   * 设置合作统计条件（待处理申请、待评价合作、合作生效、交易成功）
   *
   *      待处理申请：（包括申请中、待确认佣金分配这两个状态）
   *
   *      待评价合作：（包括交易失败、交易成功、成交逾期失败、合作终止这四个状态）
   *
   * @access  public
   * @param  int $type 统计类型( wait_do:待处理申请 wait_do_a:甲方待处理申请、wait_do_b:已方待处理申请
   *          wait_appraise:待评价合作、cop_effect:合作生效、cop_success:交易成功 )
   * @return  void
   */
  public function set_cooperate_statistics_cond($type, $primary_postfix)
  {
    switch ($type) {
      case 'wait_do':
        $this->dbback_city->where_in('esta', array(1, 2, 3));
        break;
      case 'wait_do_a':
        $this->dbback_city->where_in('esta', array(1, 2));
        break;
      case 'wait_do_b':
        $this->dbback_city->where_in('esta', array(3));
        break;
      case 'wait_appraise':
        $this->dbback_city->where_in('esta', array(6, 7, 8, 9, 11));
        $this->dbback_city->where_in('step', array(3, 4));
        $this->dbback_city->where_in('appraise' . $primary_postfix, array(0));
        break;
      case 'cop_effect':
        $this->dbback_city->where_in('esta', array(4));
        break;
      case 'cop_success':
        $this->dbback_city->where_in('esta', array(7));
        break;
    }
  }


  /**
   * 获取符合条件的合作统计数量（待处理申请、待评价合作、合作生效、交易成功）
   *
   *      待处理申请：（包括申请中、待确认佣金分配这两个状态）
   *
   *      待评价合作：（包括交易失败、交易成功、成交逾期失败、合作终止这四个状态）
   *
   * @access  public
   * @param  int $type 统计类型(1:待处理申请、2:待评价合作、3:合作生效、4:交易成功)
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_cooperate_statistics_by_cond($type, $cond_where, $cond_where_in, $cond_where_like = array(), $primary_postfix = '')
  {
    $this->set_cooperate_statistics_cond($type, $primary_postfix);
    $this->set_cond_where_in($cond_where_in);
    $this->set_cond_where_like($cond_where_like);

    return $this->get_cooperate_num_by_cond($cond_where);
  }


  /**
   * 设置where in条件
   *
   * @access  public
   * @param  string $cond_where_in in查询条件
   * @return  void
   */
  public function set_cond_where_in($cond_where_in = array())
  {
    if (is_full_array($cond_where_in)) {
      foreach ($cond_where_in as $key => $val) {
        $this->dbback_city->where_in($key, $val);
      }
    }
  }


  /**
   * 设置WHERE LIKE条件
   *
   * @access  public
   * @param  string $cond_where_like in查询条件
   * @return  void
   */
  public function set_cond_where_like($cond_where_like = array())
  {
    if (is_array($cond_where_like)) {
      foreach ($cond_where_like as $key => $val) {
        $this->dbback_city->like($key, $val);
      }
    }
  }


  /**
   * 根据合作编号获取合作附表信息
   *
   * @access  public
   * @param  mixed $cid 合作编号（单个ID或者数组编号）
   * @param int $is_unserialize 1 反序列化返回，0不反序列化返回
   * @return  array   合作列表
   */
  public function get_house_att_by_cid($cid, $is_unserialize = 1)
  {
    $house_arr = array();

    if (is_array($cid)) {
      $cid_str = implode(',', $cid);
      $cond_where = "id IN ($cid_str)";
    } else if (intval($cid) > 0) {
      $cond_where = "id =  '" . $cid . "'";
    }

    $select_fields = array('id', 'house');
    $this->set_select_fields($select_fields);
    $house_att_arr = array();
    $house_att_arr = $this->get_att_list_by_cond($cond_where);
    //echo '<pre>';print_r($house_att_arr);die;
    $house_num = count($house_att_arr);

    for ($i = 0; $i < $house_num; $i++) {
      $temp_unserialize_arr = $is_unserialize == 1 ?
        unserialize($house_att_arr[$i]['house']) : $house_att_arr[$i]['house'];
      $house_arr[$house_att_arr[$i]['id']] = $temp_unserialize_arr;
    }

    return $house_arr;
  }


  /**
   * 终止合作
   *
   * @access  public
   * @param  int $cid 合同编号
   * @param  string $stop_reason
   *          终止合作原因 deal_other/del_house/invalid_house/down_house
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  protected function update_cooperate_stop($cid, $stop_reason, $escape = TRUE)
  {
    $up_num = 0;
    $up_arr = array();
    $time = time();

    $up_arr['esta'] = 11;
    $up_arr['who_do'] = 0;
    $up_arr['dateline'] = $time;
    $cond_where = "id = '" . $cid . "' ";

    //更新合作状态
    $up_num = $this->update_info_by_cond($up_arr, $cond_where, $escape);

    if ($up_num > 0) {
      $up_arr2['stop_reason'] = strip_tags($stop_reason);
      $up_arr2['step_time'] = $time;

      //更新附表状态
      $this->update_cooperate_att_by_cond($up_arr2, $cond_where, $escape);
    }

    return $up_num;
  }


  /**
   * 合作冻结
   *
   * @access  public
   * @param  int $cid 合同编号
   * @param  int $broker_id 房源经纪人
   * @param  string $stop_reason 失败原因
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function freeze_cooperate($cid, $broker_id, $stop_reason, $escape = TRUE)
  {
    $up_num = 0;
    $up_arr = array();
    $esta = 10;
    $time = time();

    if ($esta > 0) {
      $up_arr['esta'] = $esta;
      $up_arr['who_do'] = 0;
      $up_arr['dateline'] = $time;
      $cond_where = "id = '" . $cid . "' ";
      $up_num = $this->update_info_by_cond($up_arr, $cond_where, $escape);

      if ($up_num > 0) {
        $up_arr2['stop_reason'] = strip_tags($stop_reason);
        $up_arr2['step_time'] = $time;

        //更新附表状态
        $this->update_cooperate_att_by_cond($up_arr2, $cond_where, $escape);
      }
    }

    return $up_num;
  }

  /**
   * 根据门店ID获取合门店名字信息
   *
   * @access  public
   * @param   mixed $aid 合作编号（单个ID或者数组编号）
   * @return  array   门店名字列表
   */
  public function get_agency_att_by_aid($aid)
  {
    $agency_arr = array();

    if (is_array($aid)) {
      $aid_str = implode(',', $aid);
      $cond_where = "id IN ($aid_str)";
    } else if (intval($aid) > 0) {
      $cond_where = "id =  '" . $aid . "'";
    }

    $sql = "SELECT id , name FROM agency WHERE " . $cond_where;
    $agency_att_arr = $this->dbback_city->query($sql)->result_array();
    $agency_num = count($agency_att_arr);
    for ($i = 0; $i < $agency_num; $i++) {
      $agency_arr[$agency_att_arr[$i]['id']] = $agency_att_arr[$i]['name'];
    }

    return $agency_arr;
  }


  /*
     * 根据合作ID获取合作记录
     * @parames int $cid
     * @return array
     */
  public function get_cooperate_log_by($cid, $esta)
  {
    $cid = intval($cid);
    $esta = intval($esta);
    $cooperate_arr = array();

    if ($cid > 0) {
      //查询字段
      if ($this->_select_fields) {
        $this->dbback_city->select($this->_select_fields);
      }

      //查询条件
      $this->dbback_city->select('dateline');
      $this->dbback_city->where('cid', $cid);
      $this->dbback_city->where('esta', $esta);
      $this->dbback_city->from($this->tbl_log);
      $cooperate_arr = $this->dbback_city->get()->row_array();
    }

    return $cooperate_arr;
  }

}

/* End of file cooperate_base_model.php */
/* Location: ./application/models/cooperate_base_model.php */
