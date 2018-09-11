<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * zsb
 *
 * 业务类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * 经纪人基本信息及权限API接口
 *
 *
 * @package         zsb
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Gift_exchange_record_base_model extends MY_Model
{

  /**
   * 缓存key
   * @var string
   */
  private $_tbl = 'gift_exchange_record';
  private $_tbl_gift_manage = 'gift_manage';
  private $_tbl_broker = 'broker_info';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function get_one_new_by($where, $start = -1, $limit = 1,
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

    $this->dbback_city->from($this->_tbl);
    //$this->dbback_city->join($this->_tbl_gift_manage, "{$this->_tbl}.gift_id = {$this->_tbl_gift_manage}.id");
    $this->dbback_city->join($this->_tbl_broker, "{$this->_tbl}.broker_id = {$this->_tbl_broker}.broker_id");

    return $this->dbback_city->count_all_results();
  }

  /**
   * 获取礼品列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条公司记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20, $order_key = 'id', $order_by = 'DESC')
  {
    $this->dbback_city->select("{$this->_tbl}.*,{$this->_tbl}.score AS score_record,{$this->_tbl_broker}.truename,{$this->_tbl_broker}.phone");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl);
    //$this->dbback_city->join($this->_tbl_gift_manage, "{$this->_tbl}.gift_id = {$this->_tbl_gift_manage}.id");
    $this->dbback_city->join($this->_tbl_broker, "{$this->_tbl}.broker_id = {$this->_tbl_broker}.broker_id");

    //排序条件
    $this->dbback_city->order_by($this->_tbl . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 插入礼品数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的id 失败 false
   */
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
}

?>
