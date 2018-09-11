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
 * blacklist_model CLASS
 *
 * 中介黑名单模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          angel_in_us
 */
class Broker_manage_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->broker_info = 'broker_info';
    $this->sincere_appraise = 'sincere_appraise';
    $this->sincere_trust_level = 'sincere_trust_level';
  }


  /**
   * 获得所有经纪人信息
   * date  :  2015-01-27
   * author:  angel_in_us
   */
  public function get_all_broker($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->broker_info, 'where' => $where), $database);
    return $result;
  }


  /**
   * 获取符合条件的经纪人诚信条数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的经纪人诚信条数
   */
  public function get_count_by_cond($where = array(), $cond_between = array(), $database = 'dbback_city')
  {
    $count_num = 0;
    //查询条件
    if ($where != '') {
      $this->dbselect($database);
      $this->_db->from($this->broker_info);
      $this->_db->join($this->sincere_appraise, $this->sincere_appraise . '.broker_id =  ' . $this->broker_info . '.broker_id');
      $this->_db->where($where);
      if (!empty($cond_between)) {
        $array = array('trust <' => $cond_between['up'], 'trust  >' => $cond_between['down']);
        $this->_db->where($array);
      }
      $result = $this->_db->get();
      //print_r($this->db->queries);die;
      $count_num = count($result->result());
    }

    return intval($count_num);
  }


  /**
   * 获取符合条件的经纪人诚信 内容
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   查询条件
   */

  function get_row_by_cond($where = array(), $cond_between = array(), $order_by = array(), $offset = 0, $limit = 10, $database = 'dbback_city')
  {
    $this->dbselect($database);
    $this->_db->from($this->broker_info);
    $this->_db->join($this->sincere_appraise, $this->sincere_appraise . '.broker_id = ' . $this->broker_info . '.broker_id');
    $this->_db->where($where);
    if (!empty($cond_between)) {
      $array = array('trust <' => $cond_between['up'], 'trust  >' => $cond_between['down']);
      $this->_db->where($array);
    }
    if (!empty($order_by)) {
      //echo "<pre>"; print_r($order_by);die;
      $this->_db->order_by($order_by['order_name'], $order_by['order_way']);
    }
    $this->_db->limit($limit, $offset); //开始位置，数量
    $result = $this->_db->get();
    return $result->result();
  }


  //根据 主键 id =》 $level 来查询 sincere_trust_level 表中的 up 和 down
  public function get_min_max_trust($level, $database = 'dbback_city')
  {
    $this->dbselect($database);
    $this->_db->from($this->sincere_trust_level);
    $this->_db->where('id', $level);
    $result = $this->_db->get();
    return $result->result();
  }
}

/* End of file broker_manage_model.php */
/* Location: ./application/mls_admin/models/broker_manage_model.php */
