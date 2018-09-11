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
class Signatory_purview_base_model extends MY_Model
{

  /**
   * 缓存key
   * @var string
   */
  private $_mem_key = '';

  //当前经纪人ID
  private $_signatory_id = 0;

  //当前经纪人所在公司ID
  private $_company_id = 0;

  protected $signatory_purview = array(); //用户权限

  protected $system_role_data = array(); //用户所属角色默认权限

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('signatory_model');
    $this->load->model('signatory_info_model');
    $this->load->model('purview_department_group_model');
    $this->load->model('purview_company_group_model');
    $this->load->model('purview_system_group_model');
    $this->load->model('purview_list_model');

    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_signatory_purview_base_model_';
  }

  //设置当前经纪人ID并初始化其权限
  public function set_signatory_id($signatory_id = 0, $company_id = 0)
  {
    if ($signatory_id > 0 && $company_id > 0) {
      $this->_signatory_id = $signatory_id;
      $this->_company_id = $company_id;

      //初始化经纪人基本信息
      $this->_get_signatory_purview();
    }
  }

  //获取公司权限组权限信息
  private function _get_company_group_purview()
  {
    $company_role_data = array();
    if ($this->_signatory_id > 0) {
      $signatory_data = $this->signatory_info_model->get_one_by(array('signatory_id' => $this->_signatory_id));
      if (is_array($signatory_data) && !empty($signatory_data)) {
        $role_id = $signatory_data['role_id'];
//        $company_id = $signatory_data['company_id'];
        $department_id = $signatory_data['department_id'];
        if ($department_id == '0') {
          $company_role_data = $this->purview_company_group_model->get_func_auth_by_id($role_id);
        } else {
          $company_role_data = $this->purview_department_group_model->get_func_auth_by_id($role_id);
        }

      }
    }
    return $company_role_data;

  }

  //获取系统权限组权限信息
  private function _get_system_group_purview()
  {
    if ($this->_signatory_id > 0) {
      $signatory_data = $this->signatory_info_model->get_one_by(array('signatory_id' => $this->_signatory_id));
      if (is_array($signatory_data) && !empty($signatory_data)) {
//        $role_id = $signatory_data['role_id'];
//        $company_id = $signatory_data['company_id'];
//        $department_id = $signatory_data['department_id'];
//        if ($department_id == '0') {
//          $company_role_data = $this->purview_company_group_model->get_one_by('id in ('.$role_id.')');
//        } else {
//          $company_role_data = $this->purview_department_group_model->get_one_by('id in ('.$role_id.')');
//        }
//        $system_group_id = $company_role_data['system_group_id'];
//        $this->system_role_data = $this->purview_system_group_model->get_func_by_id_2($system_group_id);
          $this->system_role_data = $this->purview_system_group_model->get_func_by_id_2($signatory_data['role_level']);
      }
    }
    return $this->system_role_data;

  }

  //获取经纪人用户权限
  private function _get_signatory_purview()
  {
    if ($this->_signatory_id > 0) {
      $company_role_arr = $this->_get_company_group_purview();
      $system_role_arr = $this->_get_system_group_purview();
      $this->signatory_purview = is_full_array($company_role_arr) && is_full_array($system_role_arr) ? array_intersect($company_role_arr, $system_role_arr) : array();
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

    if ($this->_signatory_id > 0 && !empty($this->signatory_purview)) {
      //当前用户信息
//      $signatory_data = $this->signatory_info_model->get_one_by(array('signatory_id' => $this->_signatory_id));

      //当前用户对该操作是否有权限
      $check_result = 0;
      if (in_array($authid, $this->signatory_purview)) {
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
              $purview_list = $this->purview_list_model->get_all_by(array('pid' => $authid));
              $is_this_user_hold = $purview_list['0']['is_this_user_hold'];
              if ($is_this_user_hold && $data_owner['signatory_id'] == $this->_signatory_id) {
                $is_per_result = true;
              } else {
                $is_per_result = false;
              }
            } else {
              //当前操作是否默认当前经纪人有使用权限
              $purview_list = $this->purview_list_model->get_all_by(array('pid' => $authid));
              $is_this_user_hold = $purview_list['0']['is_this_user_hold'];
              if ($is_this_user_hold && $data_owner['signatory_id'] == $this->_signatory_id) {
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
