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
 * 黑名单增加 删除 编辑 功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lu
 */
class Organization_base_Model extends MY_Model
{

  /**
   * 经纪人表名
   * @var string
   */
  private $_tbl_signatory = 'signatory';


  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    //$this->blacklist = 'blacklist';
    //$this->load->model('auth_review_model');
    //$this->load->model('newhouse_sync_account_base_model');
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by($where)
  {
    $sql = "select count(*) as number from signatory_info " . $where;
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
  public function get_all_by($where, $start = -1, $limit = 20)
  {
    //排序条件
    if ($start >= 0 && $limit > 0) {
      $where = $where . " limit " . $start . "," . $limit;
    }

    $sql = "select * from signatory_info " . $where;
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }


  /**
   * 通过员工编号获取员工记录
   * @param int $signatory_id 员工编号
   * @return array 员工记录组成的一维数组
   */
  public function get_signatory_by_id($signatory_id)
  {
    $this->dbback_city->select('department_id,truename');
    //查询条件
    $this->dbback_city->where('signatory_id', $signatory_id);
    return $this->dbback_city->get('signatory_info')->row_array();
  }

  /**
   * 通过公司编号获取公司记录
   * @param int $department_id 公司编号
   * @return array 公司记录组成的一维数组
   */
  public function get_by_id($department_id)
  {
    $this->dbback_city->select('name');
    //查询条件
    $this->dbback_city->where('id', $department_id);
    return $this->dbback_city->get('department')->row_array();
  }


  /**
   * 获取信用等级信息
   */
  public function get_trust_level_info()
  {
    $sql = "select * from sincere_trust_level ";
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }

  /**
   * 获取认证信息
   */
  public function get_auth_review($where)
  {
    $sql = "select distinct signatory_id,status from auth_review where " . $where;
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }

  /**
   * 遍历函数
   */
  public function get_foreach($array, $where)
  {
    foreach ($array as $vo) {
      $result[] = $this->auth_review_model->get_new($where . " and signatory_id = " . $vo['signatory_id'], 0, 1);
    }

    return $result;
  }

  /**
   * 修改密码
   * @param string $signatory_id 当前经纪人id
   * @param string $old_password 原密码
   * @param string $new_password 新密码
   * @param string $verify_password 确认密码
   * @return int 受影响的行数
   */
  public function modify_password($signatory_id, $new_password, $verify_password)
  {
    /*$is_true_password = $this->is_true_password($signatory_id,$old_password);
    if (!$is_true_password)
    {
        return 'password_not_true';
    }*/
    if ($new_password != $verify_password) {
      return 'password_not_same';
    }
    $update_data = array('password' => md5($new_password));
    $this->db->where('id', $signatory_id);
    $this->db->update($this->_tbl_signatory, $update_data);
    //$this->newhouse_sync_account_base_model->updatedepartment($update_data,$signatory_id);
    return $this->db->affected_rows();
  }

  /**
   * 获取权限组列表
   */
  public function get_permission_group()
  {
    $sql = "select id,name,level from permission_system_group order by level ASC";
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;

  }

  /**
   * 根据总公司id和权限组id获取身份role_id
   */
  public function get_role_id_by($department_id, $system_group_id)
  {
    $sql = "select id from permission_department_group where department_id = " . $department_id . " AND system_group_id = " . $system_group_id;
    $result = $this->dbback_city->query($sql)->row_array();
    return $result;
  }

  /**
   * 根据身份权限role_id获取职务等级与本身对应signatory_id的组合数组
   */
  public function get_system_group_id_by($role_id, $signatory_id)
  {
    $sql = "select p.id,p.system_group_id,b.signatory_id from permission_department_group p left join signatory_info b on p.id =b.role_id where p.id = " . $role_id . " AND b.signatory_id = " . $signatory_id;
    $result = $this->dbback_city->query($sql)->row_array();
    return $result;

  }

  /**
   * 根据身份权限role_id获取职务等级
   */
  public function get_level_by($role_id)
  {
    $sql = "select system_group_id from permission_department_group where id = " . $role_id;
    $result = $this->dbback_city->query($sql)->row_array();
    return $result;

  }
}
