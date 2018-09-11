<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * 基本设置业务类
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Basic_setting_base_model CLASS
 *
 * 基本设置管理相关功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
class Basic_setting_base_model extends MY_Model
{
  /**
   * 基础设置表名称
   * @var string
   */
  private $_bs_tbl = 'basic_setting';


  /**
   * 查询字段
   * @var string
   */
  public $select_fields = '';


  /**
   * 设置基本设置表名称
   *
   * @access  public
   * @param  string $tblname 基本设置表名称
   * @return  void
   */
  public function set_bs_tbl($tblname)
  {
    $this->_bs_tbl = trim(strip_tags($tblname));
  }


  /**
   * 获取基本设置表名称
   *
   * @access  public
   * @param  void
   * @return  string 区属表名称
   */
  public function get_bs_tbl()
  {
    return $this->_bs_tbl;
  }

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 根据公司id获取对应的基本设置信息
   * @param int $id 公司ID
   *
   * @return array
   */
  public function get_data_by_company_id($id)
  {

    $this->dbback_city->select('*');
    $this->dbback_city->where('company_id', $id);
    return $this->dbback_city->get($this->_bs_tbl)->result_array();
  }

  /**
   * 获取基本设置默认值
   *
   * @return array
   */
  public function get_default_data()
  {
    $this->dbback_city->select('*');
    $this->dbback_city->where('is_default', 1);
    return $this->dbback_city->get($this->_bs_tbl)->result_array();
  }

  /**
   * 获取当前城市下所有公司的默认设置
   *
   * @return array
   */
  public function get_all_company_data()
  {
    $this->dbback_city->select('company_id , sell_house_nature_public , rent_house_nature_public , buy_customer_nature_public , rent_customer_nature_public');
    $this->dbback_city->where('is_default', 0);
    return $this->dbback_city->get($this->_bs_tbl)->result_array();
  }

  /**
   * 判断公司基本设置数据是否存在，分部操作
   * @param array $update_data 更新的数据源数组
   * @param int $id 编号
   * @return int 成功后返回受影响的行数
   */
  public function judge_by_id($paramArray, $company_id)
  {
    $sql = "select id from basic_setting where company_id = " . $company_id;
    $result = $this->db_city->query($sql)->result_array();
    if (!empty($result)) {
      return $this->update_by_id($paramArray, $company_id);
    } else {
      $paramArray['company_id'] = $company_id;
      return $this->insert($paramArray);
    }
  }

  /**
   * 更新公司基本设置数据
   * @param array $update_data 更新的数据源数组
   * @param int $id 编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $id)
  {

    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('company_id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_bs_tbl, $update_data);
    } else {
      $this->db_city->update($this->_bs_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 插入公司基本设置数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的权限组id 失败 false
   */
  public function insert($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->_bs_tbl, $insert_data)) {
        return $this->db_city->affected_rows();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->_bs_tbl, $insert_data)) {
        return $this->db_city->affected_rows();
      }
    }
    return false;
  }

  /**
   * 更新默认数据
   * @param array $update_data 更新的数据源数组
   * @return int 成功后返回受影响的行数
   */
  public function update_default_data($update_data)
  {
    $this->db_city->where(array('is_default' => 1));
    $this->db_city->update($this->_bs_tbl, $update_data);
    return $this->db_city->affected_rows();
  }

  /**
   * 获取当前城市下所有公司的基本设置
   *
   * @return array
   */
  public function get_all_company_data_not_default()
  {
    $this->dbback_city->select('*');
    $this->dbback_city->where('is_default', 0);
    return $this->dbback_city->get($this->_bs_tbl)->result_array();
  }

}


/* End of file block_base_model.php */
/* Location: ./models/block_base_model.php */
