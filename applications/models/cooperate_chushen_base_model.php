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
class Cooperate_chushen_base_model extends MY_Model
{

  /**
   * 合作表名
   * @var string
   */
  protected $cooperate_chushen_tbl = 'cooperate_chushen';

  protected $cooperate_tbl = 'cooperate';

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
  public function get_cooperate_chushen_list($where, $start = -1, $limit = 20,
                                             $order_key = 'id', $order_by = 'DESC')
  {
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
    return $this->dbback_city->get($this->cooperate_chushen_tbl)->result_array();
  }

  /**
   * 查询合作审核列表
   * @param int $in_time 多长时间内
   */
  public function get_cooperate_chushen_by_id($id)
  {
    //查询条件
    $this->dbback_city->where('id', $id);
    //返回结果
    return $this->dbback_city->get($this->cooperate_chushen_tbl)->row_array();
  }

  /**
   * 获取申请资料的数据数量
   * @param int
   */
  public function get_cooperate_chushen_num($where = '')
  {
    if (!empty($where)) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->cooperate_chushen_tbl);
  }

  /**
   *根据合同id检查该合同的初审资料是否正在审核中
   * @access  public
   * @param  int $c_id 合同id
   * @return  array
   */
  public function check_chushen_is_apply($c_id)
  {
    $this->dbback_city->where('c_id', $c_id);
    $this->dbback_city->where('status', 0);
    return $this->dbback_city->get($this->cooperate_chushen_tbl)->row_array();
  }

  /**
   *添加初审资料
   * @access  public
   * @param  array $params 初审资料
   * @return  id 影响行数
   */

  public function add_cooperate_chushen($params)
  {
    //插入提交审核数据
    $result = $this->db_city->insert($this->cooperate_chushen_tbl, $params);
    if ($result) {
      //更新合作可提交审核资料状态is_apply
      $result = $this->update_cooperate_apply($params['c_id'], 1);
    }
    return $result;
  }


  /**
   * 更新合作成功是否可以申请
   * @param int $c_id 合作编号
   * @param int $applay 是否可以申请
   */
  public function update_cooperate_apply($c_id, $apply)
  {
    $update_status = array('is_apply' => $apply);
    $this->db_city->where('id', $c_id);
    $this->db_city->update($this->cooperate_tbl, $update_status);
    return $this->db_city->affected_rows();
  }

  public function get_cooperate_chushen_status($c_id)
  {
    $this->dbback_city->where('c_id', $c_id);
    $this->dbback_city->select('status');
    $this->dbback_city->order_by('create_time', 'desc');
    return $this->dbback_city->get($this->cooperate_chushen_tbl)->row_array();
  }

  /**
   * 后台审核初审资料，更新审核状态
   * @param int $id
   * @param int $c_id
   * @param int $status
   */
  public function update_cooperate_chushen_status($id, $c_id, $status)
  {
    $update_status = array('status' => $status);
    $this->db_city->where('id', $id);
    $this->db_city->update($this->cooperate_chushen_tbl, $update_status);
    $result = $this->db_city->affected_rows();
    if ($result && $status == 2) //驳回
    {
      //更新交易否可以申请，可以再次审核
      $result = $this->update_cooperate_apply($c_id, 0);
    }
    return $result;
  }
}

/* End of file cooperate_base_model.php */
/* Location: ./application/models/cooperate_base_model.php */
