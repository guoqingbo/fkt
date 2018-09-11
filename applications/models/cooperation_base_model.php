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
 * House_base_model CLASS
 *
 * 出售、出租客户信息管理类,提供增加、修改、删除、查询出售、出租客户信息的方法。
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Lion
 */
class cooperation_base_model extends MY_Model
{

  /**
   * 信息录入经纪人编号
   *
   * @access private
   * @var integer
   */
  private $_broker_id = 0;

  /**
   * 出售、出租信息编号
   *
   * @access private
   * @var integer
   */
  private $_id = 0;

  /**
   * 出售出租信息录入城市
   *
   * @access private
   * @var string
   */
  private $_city = NULL;

  /**
   * 出售、出租信息表
   *
   * @access private
   * @var string
   */
  private $_house_tbl = NULL;

  /**
   * 出售、出租信息表名称数组
   *
   * @access private
   * @var string
   */
  protected $_tbl_arr = array('sell_house', 'rent_house');

  /**
   * 出售、出租表查询字段
   *
   * @access private
   * @var array
   */
  private $_search_fields = array();


  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 初始化出售出租信息编号
   *
   * @access  public
   * @param  int $id 出售出租信息编号
   * @return  void
   */
  public function set_id($id)
  {
    $this->_id = intval($id);
  }


  /**
   * 获取出售出租信息编号
   *
   * @access  public
   * @param  void
   * @return  int 出售出租信息编号
   */
  public function get_id()
  {
    return $this->_id;
  }


  /**
   * 初始化经纪人帐号编号
   *
   * @access  public
   * @param  int $broker_id 经纪人帐号编号
   * @return  void
   */
  public function set_broker_id($broker_id)
  {
    $this->_broker_id = intval($broker_id);
  }


  /**
   * 获取经纪人帐号编号
   *
   * @access  public
   * @param  void
   * @return  int 经纪人帐号编号
   */
  public function get_broker_id()
  {
    return intval($this->_broker_id);
  }


  /**
   * 设置出售、求组信息表名称
   *
   * @access  public
   * @param  string $tbl_name 表名称
   * @return  void
   */
  public function set_tbl($tbl_name)
  {
    $this->_house_tbl = strip_tags($tbl_name);
  }


  /**
   * 获取出售、求组信息表名称
   *
   * @access  public
   * @param  void
   * @return  string 出售、求组信息表名称
   */
  public function get_tbl()
  {
    return $this->_house_tbl;
  }


  /**
   * 设置的房源需求信息表需要查询的字段数组
   *
   * @access  public
   * @param  array $arr_fields 房源信息字段
   * @return  void
   */
  public function set_search_fields($arr_fields)
  {
    $this->_search_fields = $arr_fields;
  }


  /**
   * 获取设置的房源需求信息表需要查询的字段数组
   *
   * @access  public
   * @param  void
   * @return  array  房源需求信息表需要查询的字段数组
   */
  public function get_search_fields()
  {
    return $this->_search_fields;
  }


  /**
   * 添加出售、出租需求信息
   *
   * @access  protected
   * @return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  protected function add_info($data_info)
  {
    $this->db_city->insert($this->get_tbl(), $data_info);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }


  /**
   * 根据编号删除出售、出租需求信息
   *
   * @access  protected
   * @return  boolean 是否删除成功，TRUE-成功，FAlSE-失败。
   */
  protected function delete_info_by_id()
  {
    $id = $this->get_id();

    $tbl_name = $this->get_tbl();

    $this->db_city->delete($tbl_name, array('id' => $id));

    return ($this->db_city->affected_rows() == 1) ? TRUE : FALSE;
  }


  /**
   * 根据多个编号批量删除出售、出租需求信息
   *
   * @access  protected
   * @param  array $arr_ids 需求信息编号数组
   * @return  boolean 是否删除成功，TRUE-成功，FAlSE失败。
   */
  protected function delete_info_by_ids($arr_ids)
  {
    $tbl_name = $this->get_tbl();

    if (isset($arr_ids) && !empty($arr_ids)) {
      //查询字段
      $arr_ids_str = implode(',', $arr_ids);
      $cond_where = "id IN(" . $arr_ids_str . ")";

      $this->db_city->where($cond_where);
      $this->db_city->delete($tbl_name);
    }

    return ($this->db_city->affected_rows() >= 1) ? TRUE : FALSE;
  }


  /**
   * 更新某条房源需求信息
   *
   * @access  protected
   * @param  array $update_arr 需要更新字段的键值对
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  boolean 是更新成功，TRUE-成功，FAlSE失败。
   */
  public function update_info_by_cond($update_arr, $cond_where, $escape = TRUE)
  {
    $tbl_name = $this->get_tbl();

    if ($tbl_name == '' || empty($update_arr) || $cond_where == '') {
      return FALSE;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($tbl_name);

    return $this->db_city->affected_rows();
  }


  /**
   * 更新某条房源需求信息
   *
   * @access  protected
   * @param  array $update_arr 需要更新字段的键值对
   * @param  boolean $escape 是否转义更新字段的值
   * @return  boolean 是更新成功，TRUE-成功，FAlSE失败。
   */
  public function update_info_by_id($update_arr, $escape = TRUE)
  {
    $id = $this->get_id();

    if ($id > 0) {
      $cond_where = "id = '" . $id . "' ";
      return $this->update_info_by_cond($update_arr, $cond_where, $escape);
    } else {
      return FALSE;
    }
  }

  /**
   * 更新客源需求信息
   *
   * @access  protected
   * @param  mixed $ids 单个ID或者ID数组
   * @param  array $update_arr 需要更新字段的键值对
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int  更新条数
   */
  public function update_info_by_ids($ids, $update_arr, $escape = TRUE)
  {
    $update_num = 0;

    if (!empty($ids) && is_array($update_arr) && !empty($update_arr)) {
      if (is_array($ids)) {
        $customer_id_str = implode(',', $ids);
        $cond_where = "id IN (" . $customer_id_str . ") ";
        $update_num = $this->update_info_by_cond($update_arr, $cond_where, $escape);
      } else {
        $cond_where = "id = " . $ids;
        $update_num = $this->update_info_by_cond($update_arr, $cond_where, $escape);
      }
    }

    return $update_num;
  }

  /**
   * 更改房源为成交状态
   *
   * @access  public
   * @return  boolean 是更新成功，TRUE-成功，FAlSE失败。
   */
  public function deal_house()
  {
    $update_arr['status'] = 3;
    $up_num = $this->update_info_by_id($update_arr);

    return $up_num > 0 ? TRUE : FALSE;
  }


  /**
   * 获取符合条件的房源需求信息条数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的出售信息条数
   */
  public function get_count_by_cond($cond_where = '')
  {
    $count_num = 0;

    //房源需求信息表
    $tbl_demand = $this->get_tbl();

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
      $this->dbback_city->distinct();
      $count_num = $this->dbback_city->count_all_results($tbl_demand);
    }

    return intval($count_num);
  }


  /**
   * 获取符合条件的房源需求信息列表
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   出售出租信息列表
   */
  public function get_list_by_cond($cond_where = '', $offset = 0, $limit = 10,
                                   $order_key = 'updatetime', $order_by = 'DESC')
  {
    //房源需求信息表
    $tbl_demand = $this->get_tbl();

    //需要查询的房源需求信息字段
    $select_fields = $this->get_search_fields();

    if (isset($select_fields) && !empty($select_fields)) {
      //查询字段
      $select_fields_str = implode(',', $select_fields);
      $this->dbback_city->select($select_fields_str);
    }

    //查询条件.
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }

    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);

    if ($offset >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $offset);
    }

    //查询
    $arr_data = $this->dbback_city->get($tbl_demand)->result_array();

    return $arr_data;
  }

  /**
   * 获取可分配的房源
   * @param string $cond_where 搜索条件
   * @return type
   */
  public function get_allocate_house($cond_where)
  {
    //查询条件.
    if ($cond_where != '') {
      //房源需求信息表
      $tbl_demand = $this->get_tbl();
      $this->dbback_city->where($cond_where);
      return $this->dbback_city->get($tbl_demand)->result_array();
    }
  }

  /**
   * 根据条件获取出售、出租信息
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  array 出售、出租信息
   */
  protected function get_info_by_cond($cond_where)
  {
    $arr_data = array();

    //获取表名称
    $tbl_demand = $this->get_tbl();

    //获得需要查询的出售、出租信息字段
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
      $this->db_city->select($select_fields_str);
    }

    //查询条件
    if ($cond_where != '') {
      $this->db_city->where($cond_where);
    }

    //查询
    $arr_data = $this->db_city->get($tbl_demand)->row_array();
    return $arr_data;
  }


  /**
   * 根据出售、出租编号获取出售、出租信息
   *
   * @access  protected
   * @return  array 出售出租信息
   */
  public function get_info_by_id()
  {
    $demandinfo = array();
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      //查询字段
      $select_fields_str = implode(',', $select_fields);
      $this->db_city->select($select_fields_str);
    }
    //获取出售、出租信息编号
    $id = $this->get_id();

    if ($id <= 0) {
      return $demandinfo;
    }

    $cond_where = "id = " . $id;
    $demandinfo = $this->get_info_by_cond($cond_where);

    return $demandinfo;
  }

  /**
   * 自定义语句执行
   */
  public function query($sql)
  {
    $result = false;
    if ($sql) {
      $result = $this->db_city->query($sql);
    }
    return $result;
  }

  /**
   * 格式化房源、客源编号信息
   * @param   int $id 房源、客源编号ID
   * @return  stirng  $type 客源、房源类型
   * @update    2014/05/30 xz
   */
  public function format_info_id($id, $type)
  {
    $format_str = '';

    switch (strtolower($type)) {
      case 'sell':
        $format_str = 'CS' . $id;
        break;
      case 'rent':
        $format_str = 'CZ' . $id;
        break;
      case 'buy_customer':
        $format_str = 'QG' . $id;
        break;
      case 'rent_customer':
        $format_str = 'QZ' . $id;
        break;
    }

    return $format_str;
  }

  //公司分店下拉列表框数据
  public function get_agency_norepeat($where)
  {
    $sql = "select id,name as store_name from agency " . $where;
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }

  //获得分店下的所有经纪人
  public function get_all_by($aid, $cid)
  {
    $agency_id = $aid;
    $company_id = $cid;
    $time = time();
    $sql = "select truename,broker_id from broker_info where agency_id= '" . $agency_id . "' and company_id = '" . $company_id . "' and expiretime >=  {$time} and status = 1";
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }

  /**
   * 在指定时间内设置为合作的最新一条房源
   * @param int $in_time 时间内  单位秒
   * @return array
   */
  public function get_coop_in_time($in_time = 300)
  {
    //房源需求信息表
    $tbl_demand = $this->get_tbl();
    //需要查询的房源需求信息字段
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      //查询字段
      $select_fields_str = implode(',', $select_fields);
      $this->dbback_city->select($select_fields_str);
    }
    $to_time = time();
    $from_time = $to_time - $in_time;
    $cond_where = 'isshare = 1 AND updatetime >= ' . $from_time
      . ' AND updatetime <= ' . $to_time;
    //查询条件.
    $this->dbback_city->where($cond_where);
    //排序条件
    $this->dbback_city->order_by('updatetime', 'DESC');
    $this->dbback_city->limit(1);
    //查询
    return $this->dbback_city->get($tbl_demand)->row_array();
  }
}

/* End of file house_base_model.php */
/* Location: ./application/models/house_base_model.php */
