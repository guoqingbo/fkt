<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * 房客源附表类
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Community_base_model CLASS
 *
 * 房客源附表数据管理相关功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
class House_customer_sub_base_model extends MY_Model
{
  /**
   * 添加出售房源数据
   * @param int $_id房客源id $is_follow_over是否已超过设置时间
   * @return string 浏览记录数
   */
  public function add_sell_house_sub($_id = 0, $is_follow_over = 0)
  {
    $sql = "";
    if (!empty($_id)) {
      $sql = "replace into sell_house_sub (id,is_follow_over) values ($_id , $is_follow_over)";
      $query = $this->db_city->query($sql);
    }
  }

  /**
   * 添加出租房源数据
   * @param int $_id房客源id $is_follow_over是否已超过设置时间
   * @return string 浏览记录数
   */
  public function add_rent_house_sub($_id = 0, $is_follow_over = 0)
  {
    $sql = "";
    if (!empty($_id)) {
      $sql = "replace into rent_house_sub (id,is_follow_over) values ($_id , $is_follow_over)";
      $query = $this->db_city->query($sql);
    }
  }

  /**
   * 添加求购客源数据
   * @param int $_id房客源id $is_follow_over是否已超过设置时间
   * @return string 浏览记录数
   */
  public function add_buy_customer_sub($_id = 0, $is_follow_over = 0)
  {
    $sql = "";
    if (!empty($_id)) {
      $sql = "replace into buy_customer_sub (id,is_follow_over) values ($_id , $is_follow_over)";
      $query = $this->db_city->query($sql);
    }
  }

  /**
   * 添加求租客源数据
   * @param int $_id房客源id $is_follow_over是否已超过设置时间
   * @return string 浏览记录数
   */
  public function add_rent_customer_sub($_id = 0, $is_follow_over = 0)
  {
    $sql = "";
    if (!empty($_id)) {
      $sql = "replace into rent_customer_sub (id,is_follow_over) values ($_id , $is_follow_over)";
      $query = $this->db_city->query($sql);
    }
  }

  /**
   * 获得符合条件的出售房源id
   */
  public function get_sell_house_by_arrids($where_in = array())
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('sell_house_sub');
    $this->dbback_city->where('is_follow_over = 1');
    $this->dbback_city->where_in($where_in[0], $where_in[1]);
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 获得符合条件的出租房源id
   */
  public function get_rent_house_by_arrids($where_in = array())
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('rent_house_sub');
    $this->dbback_city->where('is_follow_over = 1');
    $this->dbback_city->where_in($where_in[0], $where_in[1]);
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 获得符合条件的求购客源id
   */
  public function get_buy_customer_by_arrids($where_in = array())
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('buy_customer_sub');
    $this->dbback_city->where('is_follow_over = 1');
    $this->dbback_city->where_in($where_in[0], $where_in[1]);
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 获得符合条件的求租客源id
   */
  public function get_rent_customer_by_arrids($where_in = array())
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('rent_customer_sub');
    $this->dbback_city->where('is_follow_over = 1');
    $this->dbback_city->where_in($where_in[0], $where_in[1]);
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

}


/* End of file block_base_model.php */
/* Location: ./models/block_base_model.php */
