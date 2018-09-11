<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * buy_customer_model CLASS
 *
 * 统计分析管理类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          kang
 */
load_m('statistic_base_model');

class Statistic_analysis_model extends Statistic_base_model
{

  public function __contruct()
  {
    parent::__construct();
  }

  /** 查询所有数量 */
  /*public function get_all_count($tab_name) {
      $this->dbback_city->select("count(*)");
      $this->dbback_city->from($tab_name);

      return $this->dbback_city->count_all_results();//->row_array();
  }*/

  /** 根据条件查询数量 */
  public function get_count($where, $tab_name)
  {
    if ($where) {
      $this->dbback_city->where($where);
    }
    //查询
    $count = $this->dbback_city->count_all_results($tab_name);
    return $count;
  }

  /** 添加数据并返回ID*/
  public function add_data($data, $tab_name)
  {
    $this->db_city->insert($tab_name, $data);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /** 修改数据并返回影响行数 */
  public function upd_data($where, $data, $tab_name)
  {
    $this->db_city->update($tab_name, $data, $where);
    return $this->db_city->affected_rows();
  }

  /** 删除 */
  public function del_data($where, $tab_name)
  {
    $this->db_city->where($where);
    $this->db_city->delete($tab_name);

    return $this->db_city->affected_rows();
  }

  /**
   * 查询
   * @param $where  查询条件
   * @param $files  要查询的字段
   * @param $limit  分页条数
   * @param $offset 偏移量
   * @param $order_key  排序字段
   * @param $order_by  排序方式（ASC DESC）
   */
  public function sel_data($where, $files, $limit, $offset, $order_key, $order_by, $tab_name)
  {
    $this->dbback_city->select($files);
    $this->dbback_city->from($tab_name);
    $this->dbback_city->where($where);
    $this->dbback_city->limit($limit, $offset);
    $this->dbback_city->order_by($order_key, $order_by);

    return $this->dbback_city->get()->result_array();
  }

  /**
   * 获取分组数据
   * @param $group_by 分组字段
   * @param $tab_name 表名
   */
  /*public function get_group_data($group_file,$tab_name) {
      $this->dbback_city->select("infofrom,count(*)");
      $this->dbback_city->from($tab_name);
      $this->dbback_city->group_by($group_file);

      return $this->dbback_city->get()->result_array();
  }*/

  /**
   * 根据条件获取分组数据
   * @param $where   查询条件
   * @param $group_file   分组字段
   * @param $tab_name   表名
   * @return   array
   */
  public function get_group_data_by_con($where, $group_file, $tab_name)
  {
    $this->dbback_city->select("infofrom,count(*)");
    $this->dbback_city->from($tab_name);
    if ($where) {
      $this->dbback_city->where($where);
    }
    $this->dbback_city->group_by($group_file);

    return $this->dbback_city->get()->result_array();
  }

  /**
   * 获取业绩明细
   * @param $where   查询条件
   * @param $offset  偏移量
   * @param $limit   分页查询数量
   * @param $order_key    排序字段
   * @param $order_by    排序规则（DESC/ASC）
   * @return array
   */
  public function get_performance($where, $offset, $limit, $order_key, $order_by)
  {
    $this->dbback_city->select("b.completed_time,a.type divide_type,a.price,c.name,a.signatory_name,b.number,b.type bargain_type,d.name register_name,e.truename register_truename,b.status,b.remarks");
    $this->dbback_city->from('bargain_divide as a');
    $this->dbback_city->join('bargain as b', "a.bargain_id = b.id", 'left');
    $this->dbback_city->join('department as c', 'a.department_id = c.id', 'left');
    $this->dbback_city->join('department as d', 'b.department_id = d.id', 'left');
    $this->dbback_city->join('signatory_info e', 'b.signatory_id = e.signatory_id', 'left');
    $this->dbback_city->where($where);
    $this->dbback_city->order_by($order_key, $order_by);
    $this->dbback_city->limit($limit, $offset);

    $data = $this->dbback_city->get()->result_array();
    //echo $this->dbback_city->last_query();
    return $data;
  }

  /**
   * 获取业绩数量
   * @param $where
   * @return mixed
   */
  public function get_performance_count($where)
  {
    $this->dbback_city->select("count(*)");
    $this->dbback_city->from('bargain_divide as a');
    $this->dbback_city->join('bargain as b', "a.bargain_id = b.id", 'left');
    $this->dbback_city->join('department as c', 'a.department_id = c.id', 'left');
    $this->dbback_city->join('department as d', 'b.department_id = d.id', 'left');
    $this->dbback_city->join('signatory_info e', 'b.signatory_id = e.signatory_id', 'left');

    $this->dbback_city->where($where);
    $data = $this->dbback_city->get()->row_array();
    return $data['count(*)'];
  }

  /**
   * 获取经纪人的提成
   * @param $files   获取字段
   * @param $where   查询条件
   * @param $offset   偏移量
   * @param $limit    分页
   * @param $order_key   排序字段
   * @param $order_by    排序规则
   * @return array
   */
  public function get_signatory_commission($files, $where, $offset, $limit, $order_key, $order_by)
  {
    //$files = "a.truename,b.name,sum(c.price) sell_price";
    $this->dbback_city->select($files);
    $this->dbback_city->from('signatory_info as a');
    $this->dbback_city->join('department as b', 'a.department_id = b.id', 'left');
    $this->dbback_city->join('bargain_divide as c', 'a.signatory_id = c.signatory_id', 'left');
    $this->dbback_city->join('bargain as d', 'c.bargain_id = d.id', 'left');
    if ($where) {
      $this->dbback_city->where($where);
    }
    if ($order_key && $order_by) {
      $this->dbback_city->order_by($order_key, $order_by);
    }
    if ($limit > 0 && $offset >= 0) {
      $this->dbback_city->limit($limit, $offset);
    }
    $this->dbback_city->group_by('a.signatory_id');

    $data = $this->dbback_city->get()->result_array();
    //echo $this->dbback_city->last_query();
    return $data;
  }

  public function get_signatory_commission_count($where)
  {
    //$files = "a.truename,b.name,sum(c.price) sell_price";
    $this->dbback_city->select("count(*)");
    $this->dbback_city->from('signatory_info as a');
    $this->dbback_city->join('department as b', 'a.department_id = b.id', 'left');
    $this->dbback_city->join('bargain_divide as c', 'a.signatory_id = c.signatory_id', 'left');
    $this->dbback_city->join('bargain as d', 'c.bargain_id = d.id', 'left');
    if ($where) {
      $this->dbback_city->where($where);
    }

    $data = $this->dbback_city->get()->row_array();
    return $data['count(*)'];
  }

  /**
   * 获取经纪人的详情
   * @param $files
   * @param $where
   * @param $offset
   * @param $limit
   * @param $order_key
   * @param $order_by
   * @return mixed
   */
  public function get_signatory_info($files, $where, $offset, $limit, $order_key, $order_by)
  {
    $this->dbback_city->select($files);
    $this->dbback_city->from('signatory_info as a');
    $this->dbback_city->join('department as b', 'a.department_id = b.id', 'left');
    if ($where) {
      $this->dbback_city->where($where);
    }
    if ($order_key && $order_by) {
      $this->dbback_city->order_by($order_key, $order_by);
    }
    if ($limit > 0 && $offset >= 0) {
      $this->dbback_city->limit($limit, $offset);
    }

    $data = $this->dbback_city->get()->result_array();
    //echo $this->dbback_city->last_query();
    return $data;
  }

  /**
   * 查询今日跟进日志条数
   * @param $where  查询条件
   * @return array
   */
  public function get_follow_log($where)
  {
    //type 是跟进类型
    $this->dbback_city->select('type,count(*) as num');
    $this->dbback_city->from('detailed_follow');
    $this->dbback_city->where($where);
    $this->dbback_city->group_by('type');

    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  /**
   * 查询今日任务日志条数
   * @param $where   查询条件
   * @return mixed
   */
  public function get_task_log($where)
  {
    //type是任务类型
    $this->dbback_city->select('task_style,count(*) as num');
    $this->dbback_city->from('task');
    $this->dbback_city->where($where);
    $this->dbback_city->group_by('task_style');

    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

}
