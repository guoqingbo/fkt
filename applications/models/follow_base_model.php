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
 * Follow_base_model CLASS
 *
 * 跟进信息读取 删除 修改
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Lion
 */
class Follow_base_model extends MY_Model
{

  /**
   * 跟进表名
   * @var string
   */
  private $_customer_tbl = 'detailed_follow';

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
   * 设置出租 出售 跟进信息表名称
   * @access  public
   * @param  string $tbl_name 表名称
   * @return  void
   */
  public function set_tbl($tbl_name)
  {
    $this->_customer_tbl = strip_tags($tbl_name);
  }

  /**
   * 获取求购、求组跟进信息表名称
   *
   * @access  public
   * @param  void
   * @return  string 求购、求组跟进信息表名称
   */
  public function get_tbl()
  {
    return $this->_customer_tbl;
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
   * 获取需要查询的字段
   * @return string
   */
  public function get_select_fields()
  {
    return $this->_select_fields;
  }


  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->get_tbl());
  }

  /**
   * 添加跟进信息
   * @access  public
   * @return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  public function add_follow($data)
  {
    $this->db_city->insert($this->get_tbl(), $data);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }


  /**
   * 获取跟进方式
   *
   * @access  protected
   * @return  array
   */
  public function get_type()
  {
    //查询
    $type_arr = array();
    $this->db_city->order_by('sort');
    $type_tbl = $this->get_tbl();
    $type_arr = $this->db_city->get($type_tbl)->result_array();
    return $type_arr;
  }

  /**
   * 根据条件查询跟进信息
   * @access  protected
   * @return  array
   */

  public function get_lists($where, $start = -1, $limit = 20,
                            $order_key = 'id', $order_by = 'DESC')
  {
    $follow_tbl = $this->get_tbl();

    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }

    return $this->dbback_city->get($follow_tbl)->result_array();

  }

  /**
   * 房源列表加亮，获得相关房源id
   *
   * @access  public
   * @param  array $where筛选条件 , array $like模糊查询字段,int $offset偏移量,int $limit每页数量
   * @return  array
   */
  function get_follow_house($where = array(), $where_in, $database = 'dbback_city')
  {
    $this->dbback_city->select('distinct(house_id)');
    $this->dbback_city->from($this->_customer_tbl);
    $this->dbback_city->where($where);
    $this->dbback_city->where_in($where_in[0], $where_in[1]);
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 根据时间，获得最近的跟进数据
   *
   * @access  public
   * @param  array $where筛选条件 , array $like模糊查询字段,int $offset偏移量,int $limit每页数量
   * @return  array
   */
  function get_follow_house_order_by_date($where = array(), $where_in, $database = 'dbback_city')
  {
    $this->dbback_city->select('house_id , customer_id , date');
    $this->dbback_city->from($this->_customer_tbl);
    $this->dbback_city->where($where);
    $this->dbback_city->where_in($where_in[0], $where_in[1]);
    //$this->dbback_city->group_by('house_id');
    $this->dbback_city->order_by('date', 'desc');
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 根据时间，获得最近的跟进数据
   *
   * @access  public
   * @param  array $where筛选条件 , array $like模糊查询字段,int $offset偏移量,int $limit每页数量
   * @return  array
   */
  function get_id_order_by_date($where = array(), $database = 'dbback_city')
  {
    $this->dbback_city->select('house_id , customer_id , date');
    $this->dbback_city->from($this->_customer_tbl);
    $this->dbback_city->where($where);
    //$this->dbback_city->group_by('house_id');
    $this->dbback_city->order_by('date', 'desc');
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 客源列表加亮，获得相关客源id
   *
   * @access  public
   * @param  array $where筛选条件 , array $like模糊查询字段,int $offset偏移量,int $limit每页数量
   * @return  array
   */
  function get_follow_customer($where = array(), $where_in, $database = 'dbback_city')
  {
    $this->dbback_city->select('distinct(customer_id)');
    $this->dbback_city->from($this->_customer_tbl);
    $this->dbback_city->where($where);
    $this->dbback_city->where_in($where_in[0], $where_in[1]);
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 获取符合条件的跟进信息条数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_num($cond_where)
  {
    $count_num = 0;

    //跟进信息表
    $tbl_name = $this->get_tbl();
    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);

      $count_num = $this->dbback_city->count_all_results($tbl_name);
    }

    return intval($count_num);
  }

  /**
   * 房源录入跟进信息（经纪人）
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   *
   */
  public function house_inster($needarr)
  {

    $instarr = array();
    $instarr['customer_id'] = '';
    $instarr['follow_way'] = 7;
    $instarr['follow_type'] = 3;
    $instarr['text'] = '房源录入';
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }
    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }


  /**
   * 客源录入跟进信息（经纪人）
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   *
   */
  public function customer_inster($needarr)
  {
    $instarr = array();
    $instarr['house_id'] = '';
    $instarr['follow_way'] = 11;
    $instarr['follow_type'] = 3;
    $instarr['text'] = '客源录入';
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }
    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /**
   * 客源录入跟进信息,设置合作
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   *
   */
  public function customer_inster_share($needarr)
  {
    $instarr = array();
    $instarr['house_id'] = '';
    $instarr['follow_way'] = 11;
    $instarr['follow_type'] = 3;
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }
    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /**
   * 房源修改跟进信息（经纪人）
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   *
   */
  public function house_save($needarr)
  {
    $instarr = array();
    $instarr['follow_way'] = 8;
    $instarr['follow_type'] = 3;
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }

    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /**
   * 房源添加，设置合作
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   *
   */
  public function house_inster_share($needarr)
  {
    $instarr = array();
    $instarr['follow_way'] = 7;
    $instarr['follow_type'] = 3;
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }

    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }


  /**
   * 房源修改跟进信息（系统管理人员）
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   *
   */
  public function admin_house_save($needarr)
  {
    $instarr = array();
    $instarr['follow_way'] = 8;
    $instarr['follow_type'] = 3;
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }

    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /**
   * 房源合作跟进信息（经纪人）
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   *
   */
  public function house_share($needarr)
  {
    $instarr = array();
    $instarr['follow_way'] = 13;
    $instarr['follow_type'] = 3;
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }
    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }


  /**
   * 房源锁定跟进信息（经纪人）
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   *
   */
  public function house_lock($needarr)
  {
    $instarr = array();
    $instarr['follow_way'] = 14;
    $instarr['follow_type'] = 3;
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }

    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /**
   * 房源公私盘跟进信息（经纪人）
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   *
   */
  public function house_nature($needarr)
  {
    $instarr = array();
    $instarr['follow_way'] = 17;
    $instarr['follow_type'] = 3;
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }

    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /**
   * 客源修改跟进信息（经纪人）
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  public function customer_save($needarr)
  {
    $instarr = array();
    $instarr['follow_way'] = 12;
    $instarr['follow_type'] = 3;
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }
    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /**
   * 客源修改跟进信息（系统管理人员）
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  public function admin_customer_save($needarr)
  {
    $instarr = array();
    $instarr['follow_way'] = 12;
    $instarr['follow_type'] = 3;
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }
    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }


  /**
   * 客源合作跟进信息（经纪人）
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  public function customer_share($needarr)
  {
    $instarr = array();
    $instarr['follow_way'] = 16;
    $instarr['follow_type'] = 3;
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }
    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /**
   * 客源锁定跟进信息（经纪人）
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  public function customer_lock($needarr)
  {
    $instarr = array();
    $instarr['follow_way'] = 15;
    $instarr['follow_type'] = 3;
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }
    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }


  /**
   * 房源公私盘跟进信息（经纪人）
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   *
   */
  public function customer_nature($needarr)
  {
    $instarr = array();
    $instarr['follow_way'] = 18;
    $instarr['follow_type'] = 3;
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }

    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /**
   * 房源失效跟进信息（系统管理人员）
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  public function admin_house_failure($needarr)
  {
    $instarr = array();
    $instarr['follow_way'] = 9;
    $instarr['follow_type'] = 3;
    $instarr['text'] = '本日系统管理员对该房源进行失效操作';
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }
    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }


  /**
   * 房源成交跟进信息（系统管理人员）
   * @access public
   * @param  array $needarr （） 需要添加字段
   * @@return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   *
   */
  public function admin_house_success($needarr)
  {
    $instarr = array();
    $instarr['customer_id'] = '';
    $instarr['follow_way'] = 10;
    $instarr['follow_type'] = 3;
    $instarr['text'] = '本日系统管理员将该房源改为成交状态';
    $instarr['date'] = date('Y-m-d H:i:s');
    if (!empty($needarr)) {
      $cards = array_merge($needarr, $instarr);
    }

    $tbl = 'detailed_follow';
    $this->db_city->insert($tbl, $cards);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /**获取跟进方式配置信息
   *return array
   */
  public function get_config()
  {
    $this->db_city->order_by('follow_name', 'follow_way');
    $tbl = 'follow_up';
    $config = $this->db_city->get($tbl)->result_array();
    $data = array();
    foreach ($config as $key => $val) {
      $data[$val['type']][$val['sort']] = $val['follow_name'];
    }
    return $data;
  }


}

/* End of file follow_base_model.php */
/* Location: ./application/models/follow_base_model.php */
