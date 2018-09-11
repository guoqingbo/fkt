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
 * 客源浏览日志数据模型类
 *
 *
 * @package         zsb
 * @subpackage      Models
 * @category        Models
 * @author          Lion
 */
class Rent_customer_brower_model extends MY_Model
{
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->brower_rent_customer_log = 'browse_rent_cunstomer_mess_log';
  }

  /**
   * 添加一条浏览记录
   * @param array $paramlist 添加字段
   * @return insert_id or 0
   */
  function add($paramlist = array())
  {
    $result = $this->add_data($paramlist, 'db_city', $this->brower_rent_customer_log);
    return $result;
  }

  /**
   * 获得浏览记录
   * @param array $where where字段
   * @return array 以客源浏览日志信息组成的多维数组
   */
  public function get_brower_log($where = array(), $offset = 0, $pagesize = 0, $order_by = array(), $group_by = '', $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->brower_rent_customer_log, 'group_by' => $group_by, 'where' => $where, 'limit' => $offset, 'offset' => $pagesize, 'order_by_array' => $order_by), $database);
    return $comm;
  }

  /**
   * 根据条件获得浏览记录数分组总数
   * @param int $customer_id customer_id字段
   * @return string 浏览记录数
   */
  public function get_brower_log_group_num($customer_id)
  {
    $this->dbselect('dbback_city');
    $sql = "";
    if (!empty($customer_id)) {
      $sql = "SELECT COUNT(*) as group_num FROM ";
      $sql .= " (SELECT * FROM (`browse_rent_cunstomer_mess_log`) ";
      $sql .= " WHERE `customer_id` = $customer_id GROUP BY `broker_id`) AS NUM";
      $query = $this->db->query($sql);
      $result_arr = $query->result();
      $result = $result_arr[0]->group_num;
    }
    return $result;
  }

  /**
   * 获得浏览记录总数
   * @param array $where where字段
   * @return string 浏览记录总数
   */
  public function get_brower_log_num($where = array())
  {
    $comm = $this->get_data(array('form_name' => $this->brower_rent_customer_log, 'where' => $where, 'select' => array('count(*) as num')), 'dbback_city');
    return $comm[0]['num'];
  }

  /**
   * 根据条件获得当天浏览记录数
   * @param array $where where字段
   * @return string 浏览记录数
   */
  public function get_today_brower_log_num($customer_id, $broker_id, $today_browertime)
  {
    $this->dbselect('dbback_city');
    $where_sql = '';
    if (!empty($customer_id) && !empty($today_browertime)) {
      $where_sql = "SELECT count( * ) AS num FROM (";
      $where_sql .= "`browse_rent_cunstomer_mess_log`";
      $where_sql .= ")";
      $where_sql .= " WHERE 1=1";
      $where_sql .= " AND customer_id =$customer_id";
      if (!empty($broker_id)) {
        $where_sql .= " AND broker_id =$broker_id";
      }
      $where_sql .= " AND browertime";
      $where_sql .= " BETWEEN $today_browertime[0]";
      $where_sql .= " AND $today_browertime[1]";
      $query = $this->db->query($where_sql);
      $result_arr = $query->result();
      $result = $result_arr[0]->num;
    }
    return $result;
  }

}

?>
