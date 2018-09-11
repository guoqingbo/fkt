<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2015
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Entrust_base_model CLASS
 *
 * 营销中心预约出售
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
class Check_work_base_model extends MY_Model
{

  private $check_work = 'check_work';


  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_check_work_base_model_';
  }


  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by_sell($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl_apnt);
    $this->dbback_city->join($this->_tbl_sell_house, "{$this->_tbl_apnt}.house_id = {$this->_tbl_sell_house}.id");
    $this->dbback_city->join($this->_tbl_broker, "{$this->_tbl_sell_house}.broker_id = {$this->_tbl_broker}.broker_id");
    return $this->dbback_city->count_all_results();
  }


  //考勤
  public function get_list_by($where, $start = -1, $limit = 20, $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    $this->dbback_city->select("*");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->check_work);

    //排序条件
    $this->dbback_city->order_by($this->check_work . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }


  public function get_one_by($where)
  {
    //查询字段
    $this->dbback_city->select("*");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->check_work);

    //返回结果
    return $this->dbback_city->get()->row_array();
  }

  /**
   * 添加check_work
   *
   * @access  protected
   * @param  string $id 参数
   * @return  array   结果
   */
  function add_work($where = array(), $database = 'db_city')
  {
    $this->dbselect($database);
    $insert_id = $this->db->insert($this->check_work, $where);
    return $insert_id;
  }

  /**
   * 修改check_work
   *
   * @access  protected
   * @param  string $id 参数
   * @return  array   结果
   */
  function update_work($where, $update_data, $database = 'db_city')
  {
    $this->dbselect($database);
    $this->db->where($where);
    return $this->db->update($this->check_work, $update_data);
  }


  /**
   * 删除check_work
   *
   * @access  protected
   * @param  string $id 参数
   * @return  array   结果
   */
  function del_work($where = array(), $database = 'db_city')
  {
    $this->dbselect($database);
    return $this->db->delete($this->check_work, $where);
  }

  /**
   * 获取员工信息
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条员工记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20)
  {
    //排序条件
    if ($start >= 0 && $limit > 0) {
      $where = $where . " limit " . $start . "," . $limit;
    }

    $sql = "select broker_id from broker_info " . $where;
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by($where)
  {
    $sql = "select count(*) as number from broker_info " . $where;
    $result = $this->dbback_city->query($sql)->row_array();
    return $result['number'];
  }

  /*******************************************************************************************/


}

/* End of file entrust_base_model.php */
/* Location: ./application/models/entrust_base_model.php */
