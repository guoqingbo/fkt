<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * zsb
 *
 * 取付款类库
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
 * 收款类
 *
 *
 * @package         zsb
 * @subpackage      Models
 * @category        Models
 * @author          No.one
 */
class Payment_model extends MY_Model
{
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->_tbl = 'payment';
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
    return $this->_tbl = $tbl;
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
    return $this->_tbl;
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
   * 获取设置成交取付款信息表需要查询的字段数组
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
    return $this->dbback_city->count_all_results($this->_tbl);
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
    $this->dbback_city->from($this->_cash_tbl . ' f');
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    //排序条件
    $this->dbback_city->order_by('f.' . $order_key, $order_by);
    if ($offset >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $offset);
    }

    $this->dbback_city->join('bargain a', 'a.id = f.c_id');

    $arr_data = $this->dbback_city->get()->result_array();

    return $arr_data;
  }

  /**
   * 更新某条信息
   *
   * @access  protected
   * @param  array $update_arr 需要更新字段的键值对
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  boolean 是更新成功，TRUE-成功，FAlSE失败。
   */
  public function update_info_by_cond($update_arr, $cond_where, $escape = TRUE)
  {

    if ($this->_tbl == '' || empty($update_arr) || $cond_where == '') {
      return FALSE;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($this->_tbl);

    return $this->db_city->affected_rows();
  }

  /**
   * 获取符合条件的收付款信息
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   出售出租信息列表
   */
  public function get_one_by_cond($where = '')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    $this->dbback_city->from($this->_tbl);
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $arr_data = $this->dbback_city->get()->row_array();

    return $arr_data;
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
    $this->dbback_city->from($this->_tbl);
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
    $this->dbback_city->from($this->_tbl . ' f');
    $this->dbback_city->where('f.id', $id);
    $this->dbback_city->join('bargain a', 'a.id = f.c_id');
    $arr_data = $this->dbback_city->get()->row_array();
    return $arr_data;
  }

  /**
   * 添加取付款
   * @param array $paramlist 取付款字段
   * @return insert_id or 0
   */
  function add_flow($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      $this->db_city->insert($this->_tbl, $paramlist);//插入数据
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
   * 修改取付款
   * @param array $paramlist 取付款字段
   * @return insert_id or 0
   */
  function flow_update($id, $paramlist)
  {
    if (!empty($paramlist) && is_array($paramlist) && $id) {
      $this->db_city->where('id', $id);
      $effected_rows = $this->db_city->update($this->_tbl, $paramlist);
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
   * 获取要取付款的信息
   * @date
   * @author
   */
  function get_info($where = array(), $where_in = array(), $like = array(), $offset = 0, $limit = 10, $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => 'payment', 'where' => $where, 'where_in' => $where_in, 'like' => $like, 'offset' => $offset, 'limit' => $limit), $database);
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
    $this->db_city->update($this->_tbl, $data);
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

    $this->dbback_city->from($this->_tbl);
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
    $this->dbback_city->from($this->_tbl . ' f');
    if ($where) {
      $this->dbback_city->where($where);
    }
    $this->dbback_city->join('bargain a', 'a.id = f.c_id');
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
      $count_num = $this->dbback_city->count_all_results('payment');
    }

    return intval($count_num);
  }

  public function del_by_id($id)
  {
    $this->db_city->where('id', $id);
    $this->db_city->from($this->_tbl);
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
    $this->db_city->delete($this->_tbl);
    return $this->db_city->affected_rows();
  }

  public function insert($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  /**
   * 更新数据
   * @param array $update_data 更新的数据源数组
   * @param array $agency_id 证书申请编号数组
   * @param int $company_id 总证书申请编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

    /**
     * 根据流程名称id,和流程完成时间查询成交ids
     *
     * @param int $id
     * @return 0 or 1
     */
    public function get_payment_ids($params)
    {
        $where = 'bargain_id > 0 and company_id = ' . $params['enter_company_id'] . ' and status = 2';
        if (!empty($params['buy_type'])) {
            $where .= ' and buy_type = ' . $params['buy_type'];
        }
        if (!empty($params['loan_bank'])) {
            $where .= ' and loan_bank = ' . $params['loan_bank'];
        }
        if (!empty($params['loan_type'])) {
            $where .= ' and loan_type = ' . $params['loan_type'];
        }
        $this->dbback_city->select('bargain_id');
        $this->dbback_city->where($where);
        $res = $this->dbback_city->get($this->_tbl)->result_array();
        $ids = [0];
        if (!empty($res)) {
            foreach ($res as $key => $val) {
                $ids[] = $val['bargain_id'];
                unset($res[$key]);
            }
        }
        return array_unique($ids);
    }
}

?>
