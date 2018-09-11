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
 * Contract_base_model CLASS
 *
 * 后台合同模板
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lalala
 */
class Warrant_base_model extends MY_Model
{

  /**
   * 流程步骤表
   * @var string
   */
  private $warrant_stage_tbl = 'warrant_all_stage';

  /**
   * 系统模板表
   * @var string
   */
  private $warrant_temp_tbl = 'warrant_template';


  /**
   * 系统模板步骤表
   * @var string
   */
  private $warrant_temp_step_tbl = 'warrant_template_step';

  /**
   * 系统模板配置表
   * @var string
   */
  private $warrant_step_conf_tbl = 'warrant_step_conf';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /** 根据条件查询数量 */
  public function get_count($tab_name, $where)
  {
    if ($where) {
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($tab_name);
  }


  /** 添加数据并返回ID*/
  public function insert_data($tab_name, $data)
  {
    $this->db_city->insert($tab_name, $data);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /** 修改数据并返回影响行数 */
  public function modify_data($tab_name, $data, $where)
  {
    $this->db_city->update($tab_name, $data, $where);
    return $this->db_city->affected_rows();
  }

  /** 删除 */
  public function delete_data($tab_name, $where)
  {
    $this->db_city->where($where);
    $this->db_city->delete($tab_name);
    return $this->db_city->affected_rows();
  }

  /** 获取所有流程步骤数据 */
  public function get_all_stage()
  {
    $result = $this->dbback_city->get($this->warrant_stage_tbl)->result_array();
    if (is_full_array($result)) {
      foreach ($result as $key => $val) {
        $new_result[$val['id']] = $val;
      }
    }
    return $new_result;
  }

  /** 获取系统所有默认模板 */
  public function get_default_temp_by_id($id)
  {
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->warrant_temp_step_tbl)->row_array();
  }

  /** 获取系统所有默认模板 */
  public function get_default_temps()
  {
    //type=0代表是系统模板
    $condition = 'type = 0';

    $this->dbback_city->select('*');
    $this->dbback_city->where($condition);

    //查询
    return $this->dbback_city->get($this->warrant_temp_tbl)->row_array();
  }

  /** 获取系统所有默认模板 */
  public function get_all_temps_by_companyid($company_id)
  {
    //type=0代表是系统模板

    $this->dbback_city->select('*');
    $this->dbback_city->where('type', 1);
    $this->dbback_city->where('company_id', $company_id);
    //查询
    return $this->dbback_city->get($this->warrant_temp_tbl)->result_array();
  }

  public function get_all_temps($where, $start = 0, $limit = 10,
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
    return $this->dbback_city->get($this->warrant_temp_tbl)->result_array();
  }

  /** 根据模板ID获取模板信息 */
  public function get_temp_by_id($template_id)
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('warrant_template');
    $this->dbback_city->where('id', $template_id);
    //查询
    $data = $this->dbback_city->get()->row_array();
    return $data;
  }

  /** 根据模板ID获取模板信息 */
  public function get_temp_by_cond($where)
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('warrant_template');
    $this->dbback_city->where($where);
    //查询
    $data = $this->dbback_city->get()->row_array();
    return $data;
  }

  /** 根据筛选条件查询出步骤 */
  public function get_step_by_con($where)
  {
    $this->dbback_city->where($where);
    //查询
    return $this->dbback_city->get($this->warrant_temp_step_tbl)->result_array();
  }

  /** 根据筛选条件查询出步骤 */
  public function get_step_by_template_id($id)
  {

    $this->dbback_city->where('template_id', $id);
    $this->dbback_city->order_by('step_id', 'ASC');
    //查询
    return $this->dbback_city->get($this->warrant_temp_step_tbl)->result_array();
  }

  /** 根据筛选条件查询出步骤 */
  public function get_step_by_id($id)
  {

    $this->dbback_city->where('id', $id);
    //查询
    return $this->dbback_city->get($this->warrant_temp_step_tbl)->row_array();
  }

  public function update_step_status($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->warrant_temp_step_tbl, $update_data);
    } else {
      $this->db_city->update($this->warrant_temp_step_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /** 获取流程阶段配置信息 */
  public function get_stage_conf()
  {
    $this->dbback_city->select('id,text');
    //查询
    $data = $this->dbback_city->get($this->warrant_step_conf_tbl)->result_array();
    if (is_full_array($data)) {
      foreach ($data as $key => $val) {
        $new_data[$key + 1] = $val;
      }
    }
    return $new_data;
  }

  /**
   * 更新步骤选中状态
   * @param array $update_data 更新的数据源数组
   * @param int $id 编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->warrant_stage_tbl, $update_data);
    } else {
      $this->db_city->update($this->warrant_stage_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 添加新模版
   * @param array $update_data 更新的数据源数组
   * @param int $id 编号
   * @return int 成功后返回受影响的行数
   */
  public function add_new_template($data)
  {
    $this->db_city->insert($this->warrant_temp_tbl, $data);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }
}

/* End of file contract_base_model.php */
/* Location: ./applications/models/contract_base_model.php */
