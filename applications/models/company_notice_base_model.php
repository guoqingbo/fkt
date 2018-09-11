<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Buy_match_model CLASS
 *
 * 消息模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          angel_in_us
 */
class Company_notice_base_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->company_notice = 'company_notice'; //公司公告
    $this->company_notice_broker = 'company_notice_broker'; //公司公告经纪人
  }

  /**
   * 获取符合条件的公告条数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_count_by_cond($where = array(), $database = 'dbback_city')
  {
    $count_num = 0;
    $this->dbselect($database);
    $this->db->from($this->company_notice);
    //查询条件
    if ($where) {
      $this->db->where($where);
    }
    $result = $this->db->get();
    $count_num = count($result->result());


    return intval($count_num);
  }

  /**
   * 获取符合条件的公司公告内容
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_company_notice_by($where, $start = -1, $limit = 20,
                                        $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    $this->dbback_city->select("*");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->company_notice);
    //$this->dbback_city->join($this->_tbl1,$this->company_notice.'.detail_id = '.$this->_tbl1.'.id');

    //排序条件
    $this->dbback_city->order_by($this->company_notice . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 获取已查看公告信息的具体内容
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_company_notice_broker_by($where)
  {
    //查询字段
    $this->dbback_city->select("*");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->company_notice_broker);
    //返回结果
    return $this->dbback_city->get()->row_array();
  }

  /**
   * 通过id获取符合跟进条件的信息具体内容
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_detail_by_id($id)
  {
    //查询字段
    $this->dbback_city->select("*");
    $this->dbback_city->where(array('id' => $id));
    $this->dbback_city->from($this->company_notice);
    return $this->dbback_city->get()->row_array();
  }

  /**
   * 添加company_notice
   *
   * @access  protected
   * @param  string $id 参数
   * @return  array   结果
   */
  function add_notice($where = array(), $database = 'db_city')
  {
    $this->dbselect($database);
    $insert_id = $this->db->insert($this->company_notice, $where);
    return $insert_id;
  }

  /**
   * 删除company_notice
   *
   * @access  protected
   * @param  string $id 参数
   * @return  array   结果
   */
  function company_notice_del($where = array(), $database = 'db_city')
  {
    $this->dbselect($database);
    return $this->db->delete($this->company_notice, $where);
  }

  /**
   * 添加company_notice_broker
   *
   * @access  protected
   * @param  string $id 参数
   * @return  array   结果
   */
  function add_notice_broker($where = array(), $database = 'db_city')
  {
    $this->dbselect($database);
    $insert_id = $this->db->insert($this->company_notice_broker, $where);
    return $insert_id;
  }

  /**
   * 删除company_notice_broker
   *
   * @access  protected
   * @param  string $id 参数
   * @return  array   结果
   */
  function company_notice_broker_del($where = array(), $database = 'db_city')
  {
    $this->dbselect($database);
    return $this->db->delete($this->company_notice_broker, $where);
  }

  /**
   * 修改company_notice_broker
   *
   * @access  protected
   * @param  string $id 参数
   * @return  array   结果
   */
  function update_notice_broker($id, $update_arr, $database = 'db_city')
  {
    $this->dbselect($database);
    $this->db->where('id', $id);
    $insert_id = $this->db->update($this->company_notice, $update_arr);
    return $insert_id;
  }

  /*public function get_all_type(){
      $this->dbback_city->select('module');
      $result = $this->dbback_city->get('company_notice_open_pop')->result_array();
      foreach($result as $key=>$val){
          $new_arr[$key+1] = $val['module'];
      }
      return $new_arr;
  }*/
}

?>
