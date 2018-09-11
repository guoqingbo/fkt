<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls_job
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * @package         mls_job
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Stat_login_model extends MY_Model
{

  private $stat_login_tbl = 'stat_login';

  public function __construct()
  {
    parent::__construct();
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
    return $this->dbback_city->count_all_results($this->stat_login_tbl);
  }

  /**
   * 获取登录访问量
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20,
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
    return $this->dbback_city->get($this->stat_login_tbl)->result_array();
  }

  public function get_day_num($stat_time)
  {
    $starttime = strtotime($stat_time . ' 00:00:00');
    $endtime = strtotime($stat_time . ' 23:59:59');

    $sql = 'select phone, count(*) from login_log where dateline > ' . $starttime . ' and dateline < ' . $endtime . ' group by phone ';
    $query = $this->dbback_city->query($sql);
    $result = $query->result_array();
    return count($result);
  }

    //以门店为单位统计登陆量

    public function get_login_num($where_arr)
    {
        $sql = 'select count(*) as login_num,agency_name from login_log where dateline > ' . $where_arr['start_time'] . ' and dateline < ' . $where_arr['end_time'] . ' and agency_id > 0  group by agency_id';
        $query = $this->dbback_city->query($sql);
        $result = $query->result_array();
        return $result;
    }
  public function get_broker_online_dateline($cityid)
  {
    $cityid = intval($cityid);
    $sql = 'select id, pc, app from `mls`.`broker` as b left join `mls`.`broker_online_dateline` as bod on b.id = bod.broker_id where b.city_id = ' . $cityid;
    $query = $this->dbback_city->query($sql);
    $result = $query->result_array();

    return $result;
  }
}

/* End of file stat_login_model.php */
/* Location: ./application/mls_admin/models/stat_login_model.php */
