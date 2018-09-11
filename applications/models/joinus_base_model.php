<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          lalala
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Cooperate_base_model CLASS
 *
 * 房客源合作基类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
class joinus_base_model extends MY_Model
{

  /**
   * 合作表名
   * @var string
   */
  protected $joinus_tbl = 'joinus';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }


  /**
   * 查询合作审核列表
   * @param int $in_time 多长时间内
   */
  public function get_joinus_list($where, $start = -1, $limit = 20,
                                  $order_key = 'id', $order_by = 'DESC')
  {
    if ($where) {
      //查询条件
      $this->dbback->where($where);
    }
    //排序条件
    $this->dbback->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback->limit($limit, $start);
    }
    //返回结果
    return $this->dbback->get($this->joinus_tbl)->result_array();
  }


  /**
   * 获取申请资料的数据数量
   * @param int
   */
  public function get_joinus_num($where)
  {
    if ($where) {
      //查询条件
      $this->dbback->where($where);
    }
    return $this->dbback->count_all_results($this->joinus_tbl);
  }

  /**
   * 查询省市信息
   * @param int
   */
  public function get_joinus_by_id($id)
  {
    //查询条件
    $this->dbback->where('id', $id);
    //返回结果
    return $this->dbback->get($this->joinus_tbl)->row_array();
  }

  /**
   * 查询省市信息
   * @param int
   */
  public function get_province_city_by_id($id)
  {
    //查询条件
    $this->dbback->where('id', $id);
    //返回结果
    return $this->dbback->get('area')->row_array();
  }
}

/* End of file cooperate_base_model.php */
/* Location: ./application/models/cooperate_base_model.php */
