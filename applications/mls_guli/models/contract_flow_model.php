<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * zsb
 *
 * 实收实付类库
 *
 * @package         mls
 * @author          lalala
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * 实收实付流程类
 *
 *
 * @package         zsb
 * @subpackage      Models
 * @category        Models
 * @author          No.one
 */
class Contract_flow_model extends MY_Model
{

  private $_flow_tbl = 'contract_actual_flow';

  private $_search_fields = array();

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 获交易表名称
   *
   * @access  public
   * @param  void
   * @return  string 出售、求组信息表名称
   */
  public function set_tbl($tbl)
  {
    return $this->_flow_tbl = $tbl;
  }

  /**
   * 获交易表名称
   *
   * @access  public
   * @param  void
   * @return  string 出售、求组信息表名称
   */
  public function get_tbl()
  {
    return $this->_flow_tbl;
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
   * 获取设置合同实收实付信息表需要查询的字段数组
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
    $this->dbback_city->join('contract a', 'a.id = f.c_id');
    return $this->dbback_city->count_all_results($this->_flow_tbl . ' f');
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
  public function get_list_by_cond($where = '', $offset = 0, $limit = 10,
                                   $order_key = 'id', $order_by = 'DESC'
  )
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    } else {
      $this->dbback_city->select('*,f.status as flow_status,f.id as f_id');
    }
    $this->dbback_city->from($this->_flow_tbl . ' f');
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    //排序条件
    $this->dbback_city->order_by('f.' . $order_key, $order_by);
    if ($offset >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $offset);
    }

    $this->dbback_city->join('contract a', 'a.id = f.c_id');

    $arr_data = $this->dbback_city->get()->result_array();

    return $arr_data;
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

    if ($this->_flow_tbl == '' || empty($update_arr) || $cond_where == '') {
      return FALSE;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($this->_flow_tbl);

    return $this->db_city->affected_rows();
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
  public function get_list_by_cond1($where = '')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    $this->dbback_city->from($this->_flow_tbl);
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    //排序条件
    $this->dbback_city->order_by('id', 'DESC');

    $arr_data = $this->dbback_city->get()->result_array();

    return $arr_data;
  }

  /**
   * 根据id获取符合条件的收付信息列表
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   出售出租信息列表
   */
  public function get_by_id($id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    } else {
      $this->dbback_city->select('*,f.status as flow_status,f.id as f_id');
    }
    $this->dbback_city->from($this->_flow_tbl . ' f');
    $this->dbback_city->where('f.id', $id);
    $this->dbback_city->join('contract a', 'a.id = f.c_id');
    $arr_data = $this->dbback_city->get()->row_array();
    return $arr_data;
  }

  /**
   * 添加实收实付
   * @param array $paramlist 实收实付字段
   * @return insert_id or 0
   */
  function add_flow($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      $this->db_city->insert($this->_flow_tbl, $paramlist);//插入数据
      if (($this->db_city->affected_rows()) >= 1) {
        $result = $this->db_city->insert_id();//如果插入成功，则返回插入的id
      } else {
        $result = 0;    //如果插入失败,返回0
      }
    } else {
      $result = 0;
    }
    return $result;
  }

  /**
   * 修改实收实付
   * @param array $paramlist 实收实付字段
   * @return insert_id or 0
   */
  function flow_update($id, $paramlist)
  {
    if (!empty($paramlist) && is_array($paramlist) && $id) {
      $this->db_city->where('id', $id);
      $effected_rows = $this->db_city->update($this->_flow_tbl, $paramlist);
      if ($effected_rows >= 1) {
        $result = $effected_rows;
      } else {
        $result = 0;
      }
    } else {
      $result = 0;
    }
    return $result;
  }

  /**
   * 获取要实收实付的信息
   * @date
   * @author
   */
  function get_info($where = array(), $where_in = array(), $like = array(), $offset = 0, $limit = 10, $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => 'contract_actual_flow', 'where' => $where, 'where_in' => $where_in, 'like' => $like, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }

  /**
   * 设置收付记录状态
   *
   * @access  public
   * @param  array $data 添加数据,string $database 数据库
   * @return  int
   */
  public function modify_data($id, $data)
  {
    $this->db_city->where('id', $id);
    $this->db_city->update($this->_flow_tbl, $data);
    return $this->db_city->affected_rows();
  }


  //总计数据
  public function get_total($where)
  {
    //查询字段
    $this->dbback_city->select("SUM(collect_money) AS collect_money_total,SUM(pay_money) AS pay_money_total");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }

    $this->dbback_city->from($this->_flow_tbl);
    //返回结果
    return $this->dbback_city->get()->row_array();
  }

  /**
   * 连表查询总数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   出售出租信息列表
   */
  public function get_total2($where)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    } else {
      $this->dbback_city->select("SUM(f.collect_money) AS collect_money_total,SUM(f.pay_money) AS pay_money_total");
    }
    $this->dbback_city->from($this->_flow_tbl . ' f');
    if ($where) {
      $this->dbback_city->where($where);
    }
    $this->dbback_city->join('contract a', 'a.id = f.c_id');
    $arr_data = $this->dbback_city->get()->row_array();
    return $arr_data;
  }

  /**
   * 获取符合条件的信息条数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_count_by_cond($cond_where = '')
  {
    $count_num = 0;


    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
      $count_num = $this->dbback_city->count_all_results('contract_actual_flow');
    }

    return intval($count_num);
  }

  public function del_by_id($id)
  {
    $this->db_city->where('id', $id);
    $this->db_city->from($this->_flow_tbl);
    $this->db_city->delete();
    return $this->db_city->affected_rows();
  }


  /**
   * 根据cid删除
   *
   * @param int $id
   * @return 0 or 1
   */
  public function del_by_cid($cid)
  {
    $this->db_city->where('c_id', $cid);
    $this->db_city->delete($this->_flow_tbl);
    return $this->db_city->affected_rows();
  }

}

?>
