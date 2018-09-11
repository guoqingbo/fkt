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
 * Company_employee_base_model CLASS
 *
 * 归属公司、员工工资展示类 提供展示公司，员工工资等功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lu
 */
class Signing_employee_base_model extends MY_Model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by($where)
  {
    $sql = "select count(*) as number from signatory_info b
                left join department a on a.id = b.department_id" . $where;
    $result = $this->dbback_city->query($sql)->row_array();
    return $result['number'];
  }

  /**
   * 获取员工信息
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条员工记录组成的二维数组
   */
  public function get_all_by($c_id, $where, $start = -1, $limit = 20)
  {
    //排序条件
    if ($start >= 0 && $limit > 0) {
      $where = $where . " limit " . $start . "," . $limit;

    }
      $sql = "select b.id,b.signatory_id,b.truename,b.phone,b.qq,b.package_id,b.base_salary,a.name as store_name,s.name as sname
                    from signatory_info b
                    left join department a on a.id = b.department_id
                    left join purview_department_group p on b.role_id = p.id 
                    left join purview_system_group s on p.system_group_id=s.id {$where} ";
      $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }

  /**
   * 通过员工编号获取员工记录
   * @param int $broker_id 员工编号
   * @return array 员工记录组成的一维数组
   */
  public function get_broker_by_id($broker_id)
  {
    $this->dbback_city->select('*');
    //查询条件
    $this->dbback_city->where('broker_id', $broker_id);
    return $this->dbback_city->get('broker_info')->row_array();
  }

  /**
   * 通过签约人编号获取员工记录
   * @param int $broker_id 员工编号
   * @return array 员工记录组成的一维数组
   */
  public function get_signatory_by_id($broker_id)
  {
    $this->dbback_city->select('*');
    //查询条件
    $this->dbback_city->where('signatory_id', $broker_id);
    return $this->dbback_city->get('signatory_info')->row_array();
  }

  /**
   * 通过公司编号获取公司记录
   * @param int $agency_id 公司编号
   * @return array 公司记录组成的一维数组
   */
  public function get_by_id($agency_id)
  {
    $this->dbback_city->select('*');
    //查询条件
    $this->dbback_city->where('id', $agency_id);
    return $this->dbback_city->get('agency')->row_array();
  }

  /**
   *
   * 保存修改的基本工资
   * @param int $id broker_in中的id，int $base_salary 基本工资
   * @return int affected_rows
   */
  public function modify_salary($id, $base_salary)
  {
    $this->db_city->where('broker_id', $id);
    $this->db_city->set('base_salary', $base_salary, false);
    $this->db_city->update('broker_info');
    return $this->db_city->affected_rows();
  }

  //分店搜索栏中去除重复项
  public function get_agency_norepeat($b_id, $company_id)
  {
    if ($company_id !== "0") {
      $sql = "select id,name as store_name from agency where company_id =" . $company_id . " order by id ASC";
    } else {
      $sql = "select storename as store_name from register_broker where broker_info_id =" . $b_id;
    }
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }

  //查询当前人的备注信息
  public function get_remark_by($id, $c_id)
  {
    if ($c_id > 0) {
      $sql = "select b.id,b.broker_id,b.truename,b.phone,b.qq,b.package_id,b.base_salary,r.id rid,r.remark,a.name as store_name
                    from broker_info b
                    left join broker_info_remark r on r.remarker_id = b.broker_id
                    left join agency a on a.id = b.agency_id where b.id = " . $id;
      $result = $this->dbback_city->query($sql)->result_array();
    } else {
      $sql = "select b.id,b.broker_id,b.truename,b.phone,b.qq,b.package_id,b.base_salary,r.id rid,r.remark,a.storename as store_name
                    from broker_info b
                    left join broker_info_remark r on r.remarker_id = b.broker_id
                    left join register_broker a on a.broker_info_id = b.id where b.id = " . $id;
      $result = $this->dbback_city->query($sql)->result_array();
    }
    foreach ($result as $key => $val) {
      if ($val['package_id'] == 1) {
        $result[$key]['package_id'] = '总店长';
      } else {
        $result[$key]['package_id'] = '经纪人';
      }
    }
    return $result;
  }

  //更新备注信息
  public function update_remark($id, $remark)
  {
    $update_data = array('remark' => $remark);
    $this->db_city->where('id', $id);
    $this->db_city->update("broker_info_remark", $update_data);
    return $this->db_city->affected_rows();
  }

  //增加备注信息
  public function insert_remark($broker_id, $remarker_id, $remark)
  {
    $insert_data = array('broker_id' => $broker_id, 'remarker_id' => $remarker_id, 'remark' => $remark);
    $this->db_city->insert("broker_info_remark", $insert_data);
    return $this->db_city->affected_rows();
  }


}
/* End of file agency_base_model.php */
/* Location: ./applications/models/agency_base_model.php */
