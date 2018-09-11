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
 * 经纪人工作日报 CLASS
 *
 * 经纪人基础类 提供注册、登录、修改密码、查询等功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Broker_daily_base_model extends MY_Model
{

  /**
   * 经纪人表名
   * @var string
   */
  private $_tbl = 'broker_daily';

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

  //判断经纪人是否提交过工作日报
  public function is_exist_daily($broker_id)
  {
    $this->db_city->where('broker_id', $broker_id);
    $this->db_city->where('ymd', date('Y-m-d'));
    return $this->db_city->count_all_results($this->_tbl);
  }

  /**
   * 添加日报
   * @return int 返回插入id
   */
  public function add($insert_data)
  {
    $insert_data['ymd'] = date('Y-m-d');
    $insert_data['create_time'] = time();
    //单条插入
    if ($this->db_city->insert($this->_tbl, $insert_data)) {
      return $this->db_city->insert_id();
    }
    return false;
  }

  /**
   * 根据查询条件返回一条的记录
   * @param string $where 查询条件
   * @return array 返回一条一维数组的记录
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
   * 更新数据
   * @param array $where 更新的数据源数组
   * @param array $comment
   * @return int 成功后返回受影响的行数
   */
  public function update_comment_by($where, $update_set)
  {
    $this->db_city->set('comment', $update_set['comment']);
    $this->db_city->set('comment_broker_id', $update_set['comment_broker_id']);
    $this->db_city->set('comment_time', time());
    $this->db_city->where($where);
    $this->db_city->update($this->_tbl);
    return $this->db_city->affected_rows();
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
   * 获取经纪人日报
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条用户记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20,
                             $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where($where);
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    $data_info = $this->dbback_city->get($this->_tbl)->result_array();
    return $data_info;
  }
}

/* End of file Broker_daily_base_model.php */
/* Location: ./application/models/Broker_daily_base_model.php */
