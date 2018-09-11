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
 * 经纪人基本信息及权限API接口
 *
 *
 * @package         zsb
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Api_signatory_base_model extends MY_Model
{

  /**
   * 缓存key
   * @var string
   */
  private $_mem_key = '';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_api_signatory_base_model_';
  }

  /**
   * 获取经纪人的基本信息
   * @param int $signatory_id 经纪人编号
   * @return array {'signatory_id' : '', ......}
   */
  public function get_baseinfo_by_signatory_id($signatory_id)
  {
    $mem_key = $this->_mem_key . 'new_get_baseinfo_by_signatory_id_' . $signatory_id;
    $this->mc->delete($mem_key);
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $signatory = $cache['data'];
    } else {
      //经纪人挂靠城市的基本信息
      $this->load->model('signatory_info_base_model');
      $signatory = $this->signatory_info_base_model->get_by_signatory_id($signatory_id);
      if (is_full_array($signatory)) {
        /***
         * //引入认证模型类
         * $this->load->model('auth_review_model');
         *
         * //身份认证信息
         * $ident_info = $this->auth_review_model->get_new("signatory_id = " . $signatory_id ." AND type = 1" ,0,1);
         * $signatory['ident_auth'] = (is_full_array($ident_info)&&$ident_info['status']==2) ? 1 : 0;
         *
         * //资质认证信息
         * $quali_info = $this->auth_review_model->get_new("signatory_id = " . $signatory_id ." AND type = 2",0,1);
         * $signatory['quali_auth'] = (is_full_array($quali_info)&&$quali_info['status']==2) ? 1 : 0;
         ***/

        //获取门店
        $department = $this->get_by_department_id($signatory['department_id']);
        if (is_full_array($department)) {
          $signatory['department_name'] = $department['name'];
          $signatory['company_id'] = $department['company_id'];
        } else {
          $signatory['department_name'] = '';
          //$signatory['company_id'] = '';
        }
      } else {
        $signatory = array();
      }
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $signatory), 600);
    }
    return $signatory;
  }

  /**
   * 查询经纪人或者批量经纪人信息
   * @param type $signatory_id
   * @return type
   */
  public function get_by_signatory_id($signatory_id)
  {
    $mem_key = $this->_mem_key . 'get_by_signatory_id_' . $signatory_id;
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $signatory = $cache['data'];
    } else {
      //经纪人挂靠城市的基本信息
      $this->load->model('signatory_info_base_model');
      $signatory_info_fileds = array();
      $this->signatory_info_base_model->set_select_fields($signatory_info_fileds);
      $signatory = $this->signatory_info_base_model->get_by_signatory_id($signatory_id);
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $signatory), 600);
    }
    return $signatory;
  }

  /**
   * 查询经纪人或者批量经纪人信息
   * @param type $signatory_id
   * @return type
   */
  public function get_by_signatory_ids($signatory_id)
  {
    //经纪人挂靠城市的基本信息
    $this->load->model('signatory_info_base_model');
    $signatory_info_fileds = array();
    $this->signatory_info_base_model->set_select_fields($signatory_info_fileds);
    return $this->signatory_info_base_model->get_by_signatory_id($signatory_id);
  }

  public function delete_memcache_signatory_id($signatory_id)
  {
    $mem_key_get = $this->_mem_key . 'get_by_signatory_id_' . $signatory_id;
    $this->mc->delete($mem_key_get);
    $mem_key_getbaseinfo = $this->_mem_key . 'new_get_baseinfo_by_signatory_id_' . $signatory_id;
    $this->mc->delete($mem_key_getbaseinfo);
  }

  /**
   * 根据门店编号获取经纪人列表数组
   * @param int $department_id 公司编号
   * @return array [{'signatory_id' : '经纪人编号', 'truename' : '经纪人姓名'}，
   *  {{'signatory_id' : '经纪人编号', 'truename' : '经纪人姓名'}}]
   */
  public function get_signatorys_department_id($department_id)
  {
    $mem_key = $this->_mem_key . 'get_signatorys_department_id' . $department_id;
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $signatorys = $cache['data'];
    } else {
      $this->load->model('signatory_info_base_model');
      $signatory_info_fileds = array();
      $this->signatory_info_base_model->set_select_fields($signatory_info_fileds);
      $signatorys = $this->signatory_info_base_model->get_by_department_id($department_id);
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $signatorys), 600);
    }
    return $signatorys;
  }

  /**
   * 根据门店编号获取公司信息数组
   * @param int $department_id 公司编号
   * @param array [{'id' : '', ''}, {}]
   */
  public function get_by_department_id($department_id)
  {
    $mem_key = $this->_mem_key . 'get_by_department_id' . $department_id;
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $department = $cache['data'];
    } else {
      $this->load->model('department_base_model');
      $this->department_base_model->set_select_fields(array());
      $department = $this->department_base_model->get_by_id($department_id);
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $department), 600);
    }
    return $department;
  }

  /**
   * 总公司编号获取所有分公司
   * @param int $company_id 公司编号
   * @return array [{'department_id' : '门店编号', 'department_name' : '门店名称'}，
   *  {'department_id' : '门店编号', 'department_name' : '门店名称'}]
   */
  public function get_departments_by_company_id($company_id)
  {
    $mem_key = $this->_mem_key . 'get_departments_by_company_id' . $company_id;
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $departments = $cache['data'];
    } else {
      $this->load->model('department_base_model');
      $department_fileds = array('id as department_id', 'name as department_name');
      $this->department_base_model->set_select_fields($department_fileds);
      $departments = $this->department_base_model->get_children_by_company_id($company_id);
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $departments), 600);
    }
    return $departments;
  }

  /**
   * 总公司编号获取所有直营门店
   * @param int $company_id 公司编号
   * @return array [{'department_id' : '门店编号', 'department_name' : '门店名称'}，
   *  {'department_id' : '门店编号', 'department_name' : '门店名称'}]
   */
  public function get_type_1_departments_by_company_id($company_id)
  {
    $this->load->model('department_base_model');
    $department_fileds = array('id as department_id', 'name as department_name');
    $this->department_base_model->set_select_fields($department_fileds);
    $departments = $this->department_base_model->get_children_by_company_id_type($company_id, 1);
    return $departments;
  }


  /**
   * 获取合作成功率的平均值
   * @return float
   */
  public function avg_cop_suc_ratio()
  {
    $this->load->model('cooperate_suc_ratio_base_model');
    $avg_cop_suc_ratio = $this->cooperate_suc_ratio_base_model->get_avg_succ_ratio();
    return $avg_cop_suc_ratio;
  }
}

?>
