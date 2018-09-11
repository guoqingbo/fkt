<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * zsb
 *
 * 业务类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * 经纪人权限
 *
 *
 * @package         zsb
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Broker_permission_base_model extends MY_Model
{

  /**
   * 缓存key
   * @var string
   */
  private $_mem_key = '';

  //当前经纪人ID
  private $_broker_id = 0;

  //当前经纪人所在公司ID
  private $_company_id = 0;

  protected $broker_permission = array(); //用户权限

  protected $system_role_data = array(); //用户所属角色默认权限

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('broker_model');
    $this->load->model('broker_info_model');
    $this->load->model('permission_agency_group_model');
    $this->load->model('permission_company_group_model');
    $this->load->model('permission_system_group_model');
    $this->load->model('permission_list_model');

    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_broker_permission_base_model_';
  }

  //设置当前经纪人ID并初始化其权限
  public function set_broker_id($broker_id = 0, $company_id = 0)
  {
//    if ($broker_id > 0 && $company_id > 0) {
      if ($broker_id > 0) {
      $this->_broker_id = $broker_id;
      $this->_company_id = $company_id;

      //初始化经纪人基本信息
      $this->_get_broker_permission();
    }
  }

  //获取公司权限组权限信息
  private function _get_company_group_permission()
  {
    $company_role_data = array();
    if ($this->_broker_id > 0) {
      $broker_data = $this->broker_info_model->get_one_by(array('broker_id' => $this->_broker_id));
      if (is_array($broker_data) && !empty($broker_data)) {
        $role_id = $broker_data['role_id'];
        $company_id = $broker_data['company_id'];
        $agency_id = $broker_data['agency_id'];
        if ($agency_id == '0') {
          $company_role_data = $this->permission_company_group_model->get_func_auth_by_id($role_id);
        } else {
          $company_role_data = $this->permission_agency_group_model->get_func_auth_by_id($role_id);
        }

      }
    }
    return $company_role_data;

  }

  //获取系统权限组权限信息
  private function _get_system_group_permission()
  {
    if ($this->_broker_id > 0) {
      $broker_data = $this->broker_info_model->get_one_by(array('broker_id' => $this->_broker_id));
      if (is_array($broker_data) && !empty($broker_data)) {
        $role_id = $broker_data['role_id'];
        $company_id = $broker_data['company_id'];
        $agency_id = $broker_data['agency_id'];
        if ($agency_id == '0') {
          $company_role_data = $this->permission_company_group_model->get_one_by(array('id' => $role_id));
        } else {
          $company_role_data = $this->permission_agency_group_model->get_one_by(array('id' => $role_id));
        }
        $system_group_id = $company_role_data['system_group_id'];
        $this->system_role_data = $this->permission_system_group_model->get_func_by_id_2($system_group_id);
      }
    }
    return $this->system_role_data;

  }

  //获取经纪人用户权限
  private function _get_broker_permission()
  {
    if ($this->_broker_id > 0) {
      $company_role_arr = $this->_get_company_group_permission();
      $system_role_arr = $this->_get_system_group_permission();
      $this->broker_permission = is_full_array($company_role_arr) && is_full_array($system_role_arr) ? array_intersect($company_role_arr, $system_role_arr) : array();

//			$this->_mem_key .= $this->_broker_id;
//			$broker_permission_cache = $this->mc->get($this->_mem_key);
//
//			if($broker_permission_cache['isok'] == 1){
//				$this->broker_permission = $broker_permission_cache['data'];
//			}else{
//				$company_role_arr = $this->_get_company_group_permission();
//				$system_role_arr = $this->_get_system_group_permission();
//				$this->broker_permission = array_intersect($company_role_arr,$system_role_arr);
//				$this->mc->add($this->_mem_key, array('isok'=>1, 'data'=>$this->broker_permission), 14400);
//			}
    }
  }

  //检查是否有此权限
  public function check($authid = 0, $data_owner = array())
  {
    $result = array();
    //是否有权限
    $is_per_result = false;
    //数据范围
    $data_area = 1;

    if ($this->_broker_id > 0 && !empty($this->broker_permission)) {
      //当前用户信息
//      $broker_data = $this->broker_info_model->get_one_by(array('broker_id' => $this->_broker_id));

      //当前用户对该操作是否有权限
      $check_result = 0;
      if (in_array($authid, $this->broker_permission)) {
        $check_result = 1;
      }

      //无操作目标时(针对房客源列表页的查看他人数据，有权限则是公司范围，无则是个人范围)
      if (empty($data_owner)) {
        if (1 == $check_result) {
          $is_per_result = true;
        } else {
          $is_per_result = false;
        }

      } else {
        //判断所操作的数据，是否属于当前公司。
        if ($data_owner['company_id'] == $this->_company_id) {
          //如果有权限
          if (1 == $check_result) {
            $is_per_result = true;
          } else {
            if (in_array($authid, $this->system_role_data)) {
              //当前操作是否默认当前经纪人有使用权限
              $permission_list = $this->permission_list_model->get_all_by(array('pid' => $authid));
              $is_this_user_hold = $permission_list['0']['is_this_user_hold'];
              if ($is_this_user_hold && $data_owner['broker_id'] == $this->_broker_id) {
                $is_per_result = true;
              } else {
                $is_per_result = false;
              }
            }
          }
        } else {
          $is_per_result = false;
        }

      }
    }

    $result['auth'] = $is_per_result;
    $result['area'] = $data_area;
    return $result;
  }
}

?>
