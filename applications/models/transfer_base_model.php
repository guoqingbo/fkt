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
class Transfer_base_model extends MY_Model
{

  /**
   * 流程步骤表
   * @var string
   */
  private $transfer_stage_tbl = 'transfer_all_stage';

  /**
   * 系统模板表
   * @var string
   */
  private $transfer_temp_tbl = 'transfer_template';


  /**
   * 系统模板步骤表
   * @var string
   */
  private $transfer_temp_step_tbl = 'transfer_template_step';

  /**
   * 系统模板配置表
   * @var string
   */
  private $transfer_step_conf_tbl = 'transfer_step_conf';

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
    $result = $this->dbback_city->get($this->transfer_stage_tbl)->result_array();
    if (is_full_array($result)) {
      foreach ($result as $key => $val) {
        $new_result[$val['id']] = $val;
      }
    }
    return $new_result;
  }

    /** 获取所有流程步骤id对应的名称 */
    public function get_all_stage_name()
    {
        $result = $this->dbback_city->get($this->transfer_stage_tbl)->result_array();
        if (is_full_array($result)) {
            foreach ($result as $key => $val) {
                $new_result[$val['id']] = $val['stage_name'];
            }
        }
        return $new_result;
    }

  /** 获取系统所有默认模板 */
  public function get_default_temp_by_id($id)
  {
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->transfer_temp_step_tbl)->row_array();
  }

  /** 获取系统所有默认模板 */
  public function get_default_temps()
  {
    //type=0代表是系统模板
    $condition = 'type = 0';

    $this->dbback_city->select('*');
    $this->dbback_city->where($condition);

    //查询
    return $this->dbback_city->get($this->transfer_temp_tbl)->row_array();
  }

  /** 获取系统所有默认模板 */
  public function get_all_temps_by_companyid($company_id)
  {
    //type=0代表是系统模板

    $this->dbback_city->select('*');
    $this->dbback_city->where('type', 1);
    $this->dbback_city->where('company_id', $company_id);
    //查询
    return $this->dbback_city->get($this->transfer_temp_tbl)->result_array();
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
    return $this->dbback_city->get($this->transfer_temp_tbl)->result_array();
  }

  /** 根据模板ID获取模板信息 */
  public function get_temp_by_id($template_id)
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('transfer_template');
    $this->dbback_city->where('id', $template_id);
    //查询
    $data = $this->dbback_city->get()->row_array();
    return $data;
  }

  /** 根据模板ID获取模板信息 */
  public function get_temp_by_cond($where)
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('transfer_template');
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
    return $this->dbback_city->get($this->transfer_temp_step_tbl)->result_array();
  }

  /** 根据筛选条件查询出步骤 */
  public function get_step_by_template_id($id)
  {

    $this->dbback_city->where('template_id', $id);
    $this->dbback_city->order_by('step_id', 'ASC');
    //查询
    return $this->dbback_city->get($this->transfer_temp_step_tbl)->result_array();
  }

  /** 根据筛选条件查询出步骤 */
  public function get_step_by_id($id)
  {

    $this->dbback_city->where('id', $id);
    //查询
    return $this->dbback_city->get($this->transfer_temp_step_tbl)->row_array();
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
      $this->db_city->update_batch($this->transfer_temp_step_tbl, $update_data);
    } else {
      $this->db_city->update($this->transfer_temp_step_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /** 获取流程阶段配置信息 */
  public function get_stage_conf()
  {
    $this->dbback_city->select('id,text');
    //查询
    $data = $this->dbback_city->get($this->transfer_step_conf_tbl)->result_array();
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
      $this->db_city->update_batch($this->transfer_stage_tbl, $update_data);
    } else {
      $this->db_city->update($this->transfer_stage_tbl, $update_data);
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
    $this->db_city->insert($this->transfer_temp_tbl, $data);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

    /**
     * 获取对应交易的流程模板
     * @param array $update_data 更新的数据源数组
     * @param int $id 编号
     * @return int 成功后返回受影响的行数
     */
    public function get_stages($type)
    {
        $stage = [];
        $all_stage = $this->get_all_stage_name();
        if (!empty($type)) {
            $this->dbback_city->where_in('template_id', $type);
            $this->dbback_city->order_by('step_id', 'ASC');
            //查询
            $type_stage = $this->dbback_city->get($this->transfer_temp_step_tbl)->result_array();
            if (!empty($type_stage)) {
                foreach ($type_stage as $key => $val) {
                    $stage[$val['stage_id']] = $all_stage[$val['stage_id']];
                }
            }
        } else {
            return $all_stage;
        }
        unset($all_stage, $type_stage);
        return $stage;
    }
}

/* End of file contract_base_model.php */
/* Location: ./applications/models/contract_base_model.php */
