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
 * Sincere_punish_base_model CLASS
 *
 * 经纪人的动态评分管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Sincere_punish_base_model extends MY_Model
{

  /**
   * 类型别名
   * @var array
   */
  private $_type_alias = array(
    'mali_appraise' => 1, 'house_info_false' => 2, 'customer_info_false' => 3,
    'no_accord_agreement_signature' => 4, 'no_accord_agreement_trade_success' => 5,
    'cancel_cooperate' => 6, 'whether_accept_cooperate' => 7,
    'whether_accept_brokerage' => 8,
  );

  private $_type = array(
    1 => '恶意评价', 2 => '房源虚假', 3 => '客源虚假',
    4 => '合作生效后不按协议履行合同', 5 => '交易成功后不按协议履行合同',
    6 => '取消合作', 7 => '处理合作申请不及时',
    8 => '确认佣金分配不及时'
  );

  /**
   * 处罚表
   * @var string
   */
  private $_tbl = 'sincere_punish';

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
   * 获取配置信息
   * @return array
   */
  public function get_config()
  {
    return array(
      'type' => $this->_type,
      'type_alias' => $this->_type_alias,
    );
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
   * 根据type获取其代表的民称
   * @param int $type
   * @return multitype:string unknown
   */
  public function get_func_by_type($type)
  {
    return $this->_type[$type];
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
   * 获取处罚记录列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20,
                             $order_key = 'id', $order_by = 'ASC')
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
   * 插入处罚数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的处罚id 失败 false
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

/* End of file Sincere_punish_base_model.php */
/* Location: ./applications/models/Sincere_punish_base_model.php */
