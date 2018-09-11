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
 * 合同权证管理类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          kang
 */
load_m('warrant_base_model');

class Warrant_model extends Warrant_base_model
{

  public function __construct()
  {
    parent::__construct();
  }

  /** 获取系统所有默认模板 */
  public function get_default_temps()
  {
    //type=0代表是系统模板
    $condition = 'type = 0';

    $this->dbback_city->select('*');
    $this->dbback_city->from('warrant_template');
    $this->dbback_city->where($condition);

    //查询
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  /** 获取该经纪人的模板 */
  public function get_broker_temps($broker_id)
  {
    $condition = "broker_id = " . $broker_id . " AND type = 1";
    $this->dbback_city->select('*');
    $this->dbback_city->from('warrant_template');
    $this->dbback_city->where($condition);

    //查询
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  /** 根据条件查询出合同的步骤详情 */
  public function get_actual_step_by_con($where)
  {
    $this->dbback_city->select('b.id step_id, b.text, c.id stage_id, c.stage_name, d.broker_id, d.truename, e.id agency_id, e.name, a.createtime,
         f.truename complete_truename, g.name complete_name, a.complete_time, a.remark, a.isComplete, a.step_id');
    $this->dbback_city->from('contract_warrant_step as a');
    $this->dbback_city->join('warrant_step_conf as b', 'a.step_id = b.id', 'left');
    $this->dbback_city->join('warrant_all_stage as c', 'a.stage_id = c.id', 'left');
    $this->dbback_city->join('broker_info as d', 'a.broker_id = d.broker_id', 'left');
    $this->dbback_city->join('agency as e', 'a.agency_id = e.id', 'left');
    $this->dbback_city->join('broker_info as f', 'a.complete_broker_id = f.broker_id', 'left');
    $this->dbback_city->join('agency as g', 'a.complete_agency_id = g.id', 'left');

    $this->dbback_city->where($where);
    $this->dbback_city->order_by('a.step_id', 'ASC');

    //查询
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  /** 根据条件获取步骤字段 */
  public function get_text_by_con($where)
  {
    $this->dbback_city->select('text');
    $this->dbback_city->from('warrant_step_conf');
    $this->dbback_city->where($where);

    //查询
    $data = $this->dbback_city->get()->row_array();
    return $data['text'];
  }

  /** 根据默认模板ID查询步骤 */
  public function get_steps_by_tempid($template_id)
  {
    $condition = "a.template_id = " . $template_id;

    $this->dbback_city->select('b.text,c.stage_name');
    $this->dbback_city->from('warrant_template_step as a');
    $this->dbback_city->join('warrant_step_conf as b', 'a.step_id = b.id');
    $this->dbback_city->join('warrant_all_stage as c', 'a.stage_id = c.id');
    $this->dbback_city->where($condition);
    $this->dbback_city->order_by('a.step_id', 'ASC');

    //查询
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  /** 根据模板ID查询步骤ID信息 */
  public function get_default_steps_by_id($template_id)
  {
    $condition = "template_id = " . $template_id;
    //$this->dbback_city->select('step_id,stage_id,template_id');
    $this->dbback_city->select('step_id,stage_id');
    $this->dbback_city->from('warrant_template_step');
    $this->dbback_city->where($condition);
    $this->dbback_city->order_by('step_id', 'ASC');
    //查询
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  /** 根据模板ID获取模板信息 */
  public function get_temp_by_id($template_id)
  {
    $where = "id = " . $template_id;
    $this->dbback_city->select('*');
    $this->dbback_city->from('warrant_template');
    $this->dbback_city->where($where);
    //查询
    $data = $this->dbback_city->get()->row_array();
    return $data;
  }

  /** 根据筛选条件查询出步骤 */
  public function get_default_step_by_con($where)
  {
    $this->dbback_city->select('a.step_id, b.text, c.id stage_id, c.stage_name, d.broker_id, d.truename, e.id agency_id, e.name, a.createtime');
    $this->dbback_city->from('warrant_template_step as a');
    $this->dbback_city->join('warrant_step_conf as b', 'a.step_id = b.id', 'left');
    $this->dbback_city->join('warrant_all_stage as c', 'a.stage_id = c.id', 'left');
    $this->dbback_city->join('broker_info as d', 'a.broker_id = d.broker_id', 'left');
    $this->dbback_city->join('agency as e', 'a.agency_id = e.id', 'left');
//        $this->dbback_city->join('broker_info as f','a.complete_broker_id = f.broker_id','left');
//        $this->dbback_city->join('agency as g','a.complete_agency_id = g.id','left');
    $this->dbback_city->order_by('step_id', 'ASC');

    $this->dbback_city->where($where);

    //查询
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  /** 根据合同ID和步骤ID获取步骤完成状态 */
  public function get_step_status($where)
  {
    $this->dbback_city->select("isComplete");
    $this->dbback_city->from('contract_warrant_step');
    $this->dbback_city->where($where);

    $data = $this->dbback_city->get()->row_array();
    return $data['isComplete'];
  }

  /** 根据条件查询数量 */
  public function get_count_by_con($where, $tab_name)
  {
    $this->dbback_city->where($where);

    //查询
    $count = $this->dbback_city->count_all_results($tab_name);
    return $count;
  }


  /** 添加数据并返回ID*/
  public function insert_data_return_id($tab_name, $data)
  {
    $this->db_city->insert($tab_name, $data);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /** 修改数据并返回影响行数 */
  public function modify_data_return_row($tab_name, $data, $where)
  {
    $this->db_city->update($tab_name, $data, $where);
    return $this->db_city->affected_rows();
  }

  /** 删除 */
  public function delete_data_return_row($where, $tab_name)
  {
    $this->db_city->where($where);
    $this->db_city->delete($tab_name);

    return $this->db_city->affected_rows();
  }

  /** 获取流程阶段配置信息 */
  public function get_stage_conf()
  {
    $this->dbback_city->select('id,stage_name');
    $this->dbback_city->from('warrant_all_stage');

    //查询
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  /** 获取门店配置信息 */
  public function get_agency_conf()
  {
    $this->dbback_city->select('id,name');
    $this->dbback_city->from('agency');

    //查询
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  /** 获取门店---经纪人联动信息 */
  public function get_broker_by_agencyid($agency_id)
  {
    $condition = "agency_id = " . $agency_id;
    $this->dbback_city->select('broker_id, truename');
    $this->dbback_city->from('broker_info');
    $this->dbback_city->where($condition);

    //查询
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  /** 查询合同办结状态 */
  public function get_complete_status($contract_id)
  {
    $condition = "id = " . $contract_id;
    $this->dbback_city->select('is_completed');
    $this->dbback_city->from('contract');
    $this->dbback_city->where($condition);

    //查询
    $data = $this->dbback_city->get()->row_array();
    return $data['is_completed'];
  }

  /** 获取合同流程管理列表 */
  public function get_flow_list($where, $start = 0, $limit = 5)
  {
    $this->dbback_city->select('id,number,house_intro,signing_time');
    $this->dbback_city->from('contract');
    $this->dbback_city->where($where);
    $this->dbback_city->limit($limit, $start);
    $this->dbback_city->order_by('id', 'DESC');

    //查询
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  /**
   * 根据条件获取流程步骤字段
   * @  $tab_name表名
   * @  $where查询条件
   * @  $file要获取的字段
   * */
  public function get_text($tab_name, $where, $file)
  {
    $this->dbback_city->select($file);
    $this->dbback_city->from($tab_name);
    $this->dbback_city->where($where);

    $data = $this->dbback_city->get()->row_array();
    return $data[$file];
  }

  /** 获取经纪人配置信息 */
  /*public function get_broker_conf() {
      $this->dbback_city->select('broker_id,truename');
      $this->dbback_city->from('broker_info');

      //查询
      $data = $this->dbback_city->get()->result_array();
      return $data;
  }*/


  /*********************************************************************************************/


  /** 根据步骤条件获取模板信息*/
  /*public function get_temp_by_con($where,$group_file) {
      $this->dbback_city->select('b.*');
      $this->dbback_city->from('warrant_template_step as a');
      $this->dbback_city->join('warrant_template as b','a.template_id = b.id','left');
      $this->dbback_city->where($where);
      $this->dbback_city->group_by($group_file);
      //查询
      $data = $this->dbback_city->get()->row_array();
      //echo $this->dbback_city->last_query();
      return $data;
  }*/


}
