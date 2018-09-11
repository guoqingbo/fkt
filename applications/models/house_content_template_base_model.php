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
 * house_content_template_base_model CLASS
 *
 * 房源标题模板类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class house_content_template_base_model extends MY_Model
{

  /**
   * 房源标题模板
   * @var string
   */
  private $_tbl = 'house_content_template';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 根据筛选条件获得相关标题模板
   *
   * @access  public
   * @param  array $where筛选条件 , array $like模糊查询字段,int $offset偏移量,int $limit每页数量
   * @return  array
   */
  function get_content_template_by_cond($or_where = array(), $database = 'dbback_city')
  {
    $select = array('content');
    $result = $this->get_data(array('select' => $select, 'form_name' => $this->_tbl, 'or_where' => $or_where), $database);
    return $result;
  }

  /**
   * 出售房源 根据筛选条件获得相关标题模板
   *
   * @access  public
   * @param  array $where筛选条件 , array $like模糊查询字段,int $offset偏移量,int $limit每页数量
   * @return  array
   */
  function get_sell_content_template_by_cond($or_where = array(), $database = 'dbback_city')
  {
    $query_sql = 'select content from ' . $this->_tbl . ' where type = 1 ';
    $or_where_sql = '';
    if (is_full_array($or_where)) {
      $or_where_sql .= ' and (';
      foreach ($or_where as $k => $v) {
        $or_where_sql .= $k . '= "' . $v . '" or ';
      }
      $or_where_sql = trim($or_where_sql, 'or ');
      $or_where_sql .= ' )';
    }
    $query_sql .= $or_where_sql;
    $query = $this->db_city->query($query_sql);
    $result = $query->result_array();
    return $result;
  }

  /**
   * 出租房源 根据筛选条件获得相关标题模板
   *
   * @access  public
   * @param  array $where筛选条件 , array $like模糊查询字段,int $offset偏移量,int $limit每页数量
   * @return  array
   */
  function get_rent_content_template_by_cond($or_where = array(), $database = 'dbback_city')
  {
    $query_sql = 'select content from ' . $this->_tbl . ' where type = 2 ';
    $or_where_sql = '';
    if (is_full_array($or_where)) {
      $or_where_sql .= ' and (';
      foreach ($or_where as $k => $v) {
        $or_where_sql .= $k . '= "' . $v . '" or ';
      }
      $or_where_sql = trim($or_where_sql, 'or ');
      $or_where_sql .= ' )';
    }
    $query_sql .= $or_where_sql;
    $query = $this->db_city->query($query_sql);
    $result = $query->result_array();
    return $result;
  }

}

/* End of file city_base_model.php */
/* Location: ./application/models/city_base_model.php */
