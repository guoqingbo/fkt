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
 * Company_employee_base_model CLASS
 *
 * 黑名单增加 删除 编辑 功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lu
 */
class Blacklist_base_Model extends MY_Model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->blacklist = 'blacklist';
    $this->agent_blacklist = 'agent_blacklist';
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by($where)
  {
    $sql = "select count(*) as number from blacklist " . $where;
    $result = $this->dbback_city->query($sql)->row_array();
    return $result['number'];
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

    $sql = "select * from blacklist " . $where;
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }


  /**
   * 通过员工编号获取员工记录
   * @param int $broker_id 员工编号
   * @return array 员工记录组成的一维数组
   */
  public function get_broker_by_id($broker_id)
  {
    $this->dbback_city->select('id,agency_id,truename,company_id');
    //查询条件
    $this->dbback_city->where('broker_id', $broker_id);
    return $this->dbback_city->get('broker_info')->row_array();
  }

  /**
   * 通过员工编号获取员工注册填写记录
   * @param int $broker_id 员工编号
   * @return array 员工记录组成的一维数组
   */
  public function get_register_broker_by_id($broker_info_id)
  {
    $this->dbback_city->select('storename');
    //查询条件
    $this->dbback_city->where('broker_info_id', $broker_info_id);
    return $this->dbback_city->get('register_broker')->row_array();
  }

  /**
   * 通过公司编号获取公司记录
   * @param int $agency_id 公司编号
   * @return array 公司记录组成的一维数组
   */
  public function get_by_id($agency_id)
  {
    $this->dbback_city->select('name');
    //查询条件
    $this->dbback_city->where('id', $agency_id);
    return $this->dbback_city->get('agency')->row_array();
  }


  /**
   * 通过传值获取黑名单信息
   * @param int $black_id 黑名单编号
   * @return array 黑名单记录组成的一维数组
   */
  public function get_by_black_id($black_id)
  {
    $this->dbback_city->select('*');
    //查询条件
    $this->dbback_city->where('id', $black_id);
    return $this->dbback_city->get('blacklist')->row_array();
  }

  /**
   *添加前台黑名单
   */
  public function add_blacklist($data = array(), $database = 'db_city', $form_name = '')
  {
    $blacklist = $this->add_data($data, $database, $this->blacklist);
    return $blacklist;
  }

  /**
   *添加后台黑名单
   */
  public function add_agent_blacklist($data = array(), $database = 'db_city', $form_name = '')
  {
    $agent_blacklist = $this->add_data($data, $database, $this->agent_blacklist);
    return $agent_blacklist;
  }

  /**
   * 删除黑名单
   */
  public function del_blacklist($arr = array())
  {
    $result = $this->del($arr, 'db_city', $this->blacklist);
    return $result;
  }

  /**
   * 修改黑名单
   */
  function edit_blacklist($arr = array(), $where = array())
  {
    $result = $this->modify_data($arr, $where, 'db_city', $this->blacklist);
    return $result;
  }

}
