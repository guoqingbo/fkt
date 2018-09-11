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
 * Collocation_contract_base_model CLASS
 *
 * 托管合同查询、添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          wll
 */
class Collocation_contract_base_model extends MY_Model
{

  /**
   * 托管合同表
   * @var string
   */
  private $_tbl = 'collocation_contract';
  /**
   * 托管应付业主表
   * @var string
   */
  private $_tbl2 = 'need_pay_owner';
  /**
   * 托管实付业主表
   * @var string
   */
  private $_tbl3 = 'actual_pay_owner';
  /**
   * 托管管家费用表
   * @var string
   */
  private $_tbl4 = 'steward_expenses';
  /**
   * 托管出租合同表
   * @var string
   */
  private $_tbl5 = 'collocation_rent';
  /**
   * 托管跟进表
   * @var string
   */
  private $_tbl6 = 'collocation_contract_log';
  /**
   * 托管合同编号编号
   *
   * @access private
   * @var integer
   */
  private $_id = 0;

  /**
   * 查询字段
   * @var string
   */
  private $_select_fields = array();

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 初始化合同编号
   *
   * @access  public
   * @param  int $id
   * @return  void
   */
  public function set_id($id)
  {
    $this->_id = intval($id);
  }

  /**
   * 获取相关字段配置信息
   */
  public function get_config()
  {
    $config = array(
      'rent_status' => array('1' => '待审核', '2' => '生效', '3' => '终止', '4' => '审核不通过'),
      'pay_type' => array('1' => '月付', '2' => '季付', '3' => '半年付', '4' => '年付', '5' => '其他'),
      'status' => array('1' => '待审核', '2' => '审核通过', '3' => '审核不通过'),
    );
    return $config;
  }

  /**
   * 获取id
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
   * 设置的托管合同表需要查询的字段数组
   *
   * @access  public
   * @param  array $arr_fields
   * @return  void
   */
  public function set_search_fields($arr_fields)
  {
    $this->_search_fields = $arr_fields;
  }

  public function get_search_fields()
  {
    return $this->_search_fields;
  }

  /**
   * 添加托管合同
   * @return string
   */
  public function add_info($data_info)
  {
    $this->db_city->insert($this->_tbl, $data_info);
    return $this->db_city->affected_rows() >= 1 ? $this->db_city->insert_id() : 0;
  }

  /**
   * 添加托管出租合同
   * @return string
   */
  public function add_rent_info($data_info)
  {
    $this->db_city->insert($this->_tbl5, $data_info);
    return $this->db_city->affected_rows() >= 1 ? $this->db_city->insert_id() : 0;
  }

  /**
   * 添加托管合同--实付业主,应付，管家
   * @return string
   */
  public function add_need_pay_info($data_info, $type)
  {
    //判断是哪个表
    if ($type == 1 || $type == 2) {
      $this->_tb = $this->_tbl2;
    } elseif ($type == 3) {
      $this->_tb = $this->_tbl3;
    } elseif ($type == 4) {
      $this->_tb = $this->_tbl4;
    }
    $this->db_city->insert($this->_tb, $data_info);
    return $this->db_city->affected_rows() >= 1 ? $this->db_city->insert_id() : 0;
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
   * 符合条件托管合同的行数
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
   * 获取合同列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by($where, $start = 0, $limit = 20,
                             $order_key = 'id', $order_by = 'DESC')
  {
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
    //返回结果
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 根据查询条件返回一条合同表的记录
   * @param string $where 查询条件
   * @return array 返回一条一维数组的表记录
   */
  public function get_one_by($where = '')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where($where);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 通过托管合同ID获取记录
   * @param int $id 编号
   * @return array 合同记录组成的一维数组
   */
  public function get_by_id($id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  //根据托管合同获取记录
  public function get_by_collocation_id($collocation_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('collocation_id', $collocation_id);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  //根据出租合同id
  public function get_by_rent_id($id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tbl5)->row_array();
  }

  //根据托管DI获取出租合同记录
  public function get_by_collocation_id_rent($c_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('c_id', $c_id);
    return $this->dbback_city->get($this->_tbl5)->result_array();
  }

  //根据托管合同id去出租表里找
  public function get_rent_by_cid($c_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('c_id', $c_id);
    return $this->dbback_city->get($this->_tbl5)->result_array();
  }

  /**
   * 通过编号获取记录
   * @param int $id 编号
   * @return array 合同记录列表数据
   */
  public function get_list_by_cid($where = '', $tab, $start = 0, $limit = 15,
                                  $order_key = 'id', $order_by = 'DESC')
  {
    //判断是找哪个表的数据
    if ($tab == 1) {//应付
      $this->_tb = $this->_tbl2;
    } elseif ($tab == 2) {//实付
      $this->_tb = $this->_tbl3;
    } elseif ($tab == 3) {//管家
      $this->_tb = $this->_tbl4;
    } elseif ($tab == 4) {//出租
      $this->_tb = $this->_tbl5;
    } elseif ($tab == 5) {//跟进
      $this->_tb = $this->_tbl6;
    }
    //查询条件
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->_tb);

    //排序条件
    $this->dbback_city->order_by($this->_tb . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }


  //通过id获取相关表的记录
  public function get_need_pay_by_id($id, $tab)
  {
    //判断来自哪个表
    if ($tab == 1) {//应付
      $this->_tb = $this->_tbl2;
    } elseif ($tab == 2) {//实付
      $this->_tb = $this->_tbl3;
    } elseif ($tab == 3) {//管家
      $this->_tb = $this->_tbl4;
    }
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tb)->row_array();
  }

  /**
   * 通过合同编号获取记录
   * @param int $number 合同编号
   * @return array 合同记录组成的一维数组
   */
  public function get_by_contract_number($contract_no)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('number', $contract_no);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 根据编号查询合同是否办结
   * @param $id 合同ID
   * @return int 返回状态
   */
  public function is_completed_by_id($id)
  {
    $this->db_city->select('is_completed');
    $this->db_city->where('id', $id);
    $result = $this->db_city->get($this->_tbl)->row_array();
    return $result['is_completed'];
  }

  /**
   * 根据托管合同编号更新合同的详细信息数据
   * @param array $update_data 更新的数据源数组
   * @param array $id 编号
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
   * 更新某条房源需求信息
   *
   * @access  protected
   * @param  array $update_arr 需要更新字段的键值对
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  boolean 是更新成功，TRUE-成功，FAlSE失败。
   */
  public function update_info_by_cond($update_arr, $cond_where, $tbl = '', $escape = TRUE)
  {

    if ($tbl == '' || empty($update_arr) || $cond_where == '') {
      return FALSE;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($tbl);

    return $this->db_city->affected_rows();
  }

  //根据出租合同id更新
  public function update_by_rent_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl5, $update_data);
    } else {
      $this->db_city->update($this->_tbl5, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 根据应付业主id更新
   * @param array $update_data 更新的数据源数组
   * @param array $id 编号
   * @return int 成功后返回受影响的行数
   */
  public function update_need_pay_by_id($update_data, $id, $tab)
  {
    //判断来自哪个表
    if ($tab == 1) {//应付
      $this->_tb = $this->_tbl2;
    } elseif ($tab == 2) {//实付
      $this->_tb = $this->_tbl3;
    } elseif ($tab == 3) {//管家
      $this->_tb = $this->_tbl4;
    } elseif ($tab == 4) {//出租
      $this->_tb = $this->_tbl5;
    }
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tb, $update_data);
    } else {
      $this->db_city->update($this->_tb, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 删除托管合同记录
   *
   * @param int $id
   * @return 0 or 1
   */
  public function del_by_id($id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    $this->db_city->delete($this->_tbl);
    return $this->db_city->affected_rows();
  }

  /**
   * 删除托管合同下应付业主记录
   *
   * @param int $id
   * @return 0 or 1
   */
  public function del_need_pay_by_id($id, $tab)
  {
    //判断是来自哪个表
    if ($tab == 1) {//应付
      $this->_tb = $this->_tbl2;
    } elseif ($tab == 2) {//实付
      $this->_tb = $this->_tbl3;
    } elseif ($tab == 3) {//管家
      $this->_tb = $this->_tbl4;
    } elseif ($tab == 4) {//出租
      $this->_tb = $this->_tbl5;
    }
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    $this->db_city->delete($this->_tb);
    return $this->db_city->affected_rows();
  }

  //删除托管合同时也删除对应下面的应付，实付，管家
  public function del_need_by_id($id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('c_id', $id);
    $this->db_city->delete($this->_tbl2);
    return $this->db_city->affected_rows();
  }

  public function del_actual_pay_by_id($id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('c_id', $id);
    $this->db_city->delete($this->_tbl3);
    return $this->db_city->affected_rows();
  }

  public function del_steward_by_id($id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('c_id', $id);
    $this->db_city->delete($this->_tbl4);
    return $this->db_city->affected_rows();
  }

  /**
   * 根据id
   *
   * @access  protected
   * @return  array
   */
  public function get_collocationinfo_by_id()
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
   * 根据条件获取合同信息
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  array 出售、出租信息
   */
  protected function get_info_by_cond($cond_where)
  {
    $arr_data = array();

    //获取表名称
    $tbl_demand = $this->_tbl;

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

  //出租,管家费用,合同列表数据
  public function get_list_by_tab($where, $start = 0, $limit = 20, $tab,
                                  $order_key = 'signing_time', $order_by = 'DESC')
  {
    //判断是找哪个表的数据
    if ($tab == 1) {//应付
      $this->_tb = $this->_tbl2;
    } elseif ($tab == 2) {//实付
      $this->_tb = $this->_tbl3;
    } elseif ($tab == 3) {//管家
      $this->_tb = $this->_tbl4;
    } elseif ($tab == 4) {//出租
      $this->_tb = $this->_tbl5;
    }
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }

    $this->dbback_city->from($this->_tb);

    //排序条件
    $this->dbback_city->order_by($this->_tb . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by_tab($where = '', $tab)
  {
    //判断是找哪个表的数据
    if ($tab == 1) {//应付
      $this->_tb = $this->_tbl2;
    } elseif ($tab == 2) {//实付
      $this->_tb = $this->_tbl3;
    } elseif ($tab == 3) {//管家
      $this->_tb = $this->_tbl4;
    } elseif ($tab == 4) {//出租
      $this->_tb = $this->_tbl5;
    }
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->_tb);
  }

  //对应托管合同下的应付，实付，管家，出租的总数
  public function count_by_collo_tab($where = '', $tab)
  {
    //判断是找哪个表的数据
    if ($tab == 1) {//应付
      $this->_tb = $this->_tbl2;
    } elseif ($tab == 2) {//实付
      $this->_tb = $this->_tbl3;
    } elseif ($tab == 3) {//管家
      $this->_tb = $this->_tbl4;
    } elseif ($tab == 4) {//出租
      $this->_tb = $this->_tbl5;
    } elseif ($tab == 5) {//跟进
      $this->_tb = $this->_tbl6;
    }
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->_tb);
  }
}

/* End of file contract_base_model.php */
/* Location: ./applications/models/contract_base_model.php */
