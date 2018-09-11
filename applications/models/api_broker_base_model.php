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
class Api_broker_base_model extends MY_Model
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
    $this->_mem_key = $city . '_api_broker_base_model_';
  }

  /**
   * 获取经纪人的基本信息
   * @param int $broker_id 经纪人编号
   * @return array {'broker_id' : '', ......}
   */
  public function get_baseinfo_by_broker_id($broker_id)
  {
    $mem_key = $this->_mem_key . 'new_get_baseinfo_by_broker_id_' . $broker_id;
//    $this->mc->delete($mem_key);
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $broker = $cache['data'];
    } else {
      //经纪人挂靠城市的基本信息
      $this->load->model('broker_info_base_model');
      $broker = $this->broker_info_base_model->get_by_broker_id($broker_id);
      if (is_full_array($broker)) {
        /***
         * //引入认证模型类
         * $this->load->model('auth_review_model');
         *
         * //身份认证信息
         * $ident_info = $this->auth_review_model->get_new("broker_id = " . $broker_id ." AND type = 1" ,0,1);
         * $broker['ident_auth'] = (is_full_array($ident_info)&&$ident_info['status']==2) ? 1 : 0;
         *
         * //资质认证信息
         * $quali_info = $this->auth_review_model->get_new("broker_id = " . $broker_id ." AND type = 2",0,1);
         * $broker['quali_auth'] = (is_full_array($quali_info)&&$quali_info['status']==2) ? 1 : 0;
         ***/

        //获取门店
        $agency = $this->get_by_agency_id($broker['agency_id']);
        if (is_full_array($agency)) {
          $broker['agency_name'] = $agency['name'];
          $broker['company_id'] = $agency['company_id'];
        } else {
          $broker['agency_name'] = '';
          //$broker['company_id'] = '';
        }
      } else {
        $broker = array();
      }
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $broker), 600);
    }
    return $broker;
  }

  /**
   * 查询经纪人或者批量经纪人信息
   * @param type $broker_id
   * @return type
   */
  public function get_by_broker_id($broker_id)
  {
    $mem_key = $this->_mem_key . 'get_by_broker_id_' . $broker_id;
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $broker = $cache['data'];
    } else {
      //经纪人挂靠城市的基本信息
      $this->load->model('broker_info_base_model');
      $broker_info_fileds = array();
      $this->broker_info_base_model->set_select_fields($broker_info_fileds);
      $broker = $this->broker_info_base_model->get_by_broker_id($broker_id);
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $broker), 600);
    }
    return $broker;
  }

  /**
   * 查询经纪人或者批量经纪人信息
   * @param type $broker_id
   * @return type
   */
  public function get_by_broker_ids($broker_id)
  {
    //经纪人挂靠城市的基本信息
    $this->load->model('broker_info_base_model');
    $broker_info_fileds = array();
    $this->broker_info_base_model->set_select_fields($broker_info_fileds);
    return $this->broker_info_base_model->get_by_broker_id($broker_id);
  }

  public function delete_memcache_broker_id($broker_id)
  {
    $mem_key_get = $this->_mem_key . 'get_by_broker_id_' . $broker_id;
    $this->mc->delete($mem_key_get);
    $mem_key_getbaseinfo = $this->_mem_key . 'new_get_baseinfo_by_broker_id_' . $broker_id;
    $this->mc->delete($mem_key_getbaseinfo);
  }

  /**
   * 根据门店编号获取经纪人列表数组
   * @param int $agency_id 公司编号
   * @return array [{'broker_id' : '经纪人编号', 'truename' : '经纪人姓名'}，
   *  {{'broker_id' : '经纪人编号', 'truename' : '经纪人姓名'}}]
   */
  public function get_brokers_agency_id($agency_id)
  {
    $mem_key = $this->_mem_key . 'get_brokers_agency_id' . $agency_id;
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $brokers = $cache['data'];
    } else {
      $this->load->model('broker_info_base_model');
      $broker_info_fileds = array();
      $this->broker_info_base_model->set_select_fields($broker_info_fileds);
      $brokers = $this->broker_info_base_model->get_by_agency_id($agency_id);
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $brokers), 600);
    }
    return $brokers;
  }

  /**
   * 根据门店编号获取公司信息数组
   * @param int $agency_id 公司编号
   * @param array [{'id' : '', ''}, {}]
   */
  public function get_by_agency_id($agency_id)
  {
    $mem_key = $this->_mem_key . 'get_by_agency_id' . $agency_id;
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $agency = $cache['data'];
    } else {
      $this->load->model('agency_base_model');
      $this->agency_base_model->set_select_fields(array());
      $agency = $this->agency_base_model->get_by_id($agency_id);
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $agency), 600);
    }
    return $agency;
  }

  /**
   * 总公司编号获取所有分公司
   * @param int $company_id 公司编号
   * @return array [{'agency_id' : '门店编号', 'agency_name' : '门店名称'}，
   *  {'agency_id' : '门店编号', 'agency_name' : '门店名称'}]
   */
  public function get_agencys_by_company_id($company_id)
  {
    $mem_key = $this->_mem_key . 'get_agencys_by_company_id' . $company_id;
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $agencys = $cache['data'];
    } else {
      $this->load->model('agency_base_model');
      $agency_fileds = array('id as agency_id', 'name as agency_name');
      $this->agency_base_model->set_select_fields($agency_fileds);
      $agencys = $this->agency_base_model->get_children_by_company_id($company_id);
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $agencys), 600);
    }
    return $agencys;
  }

  /**
   * 总公司编号获取所有直营门店
   * @param int $company_id 公司编号
   * @return array [{'agency_id' : '门店编号', 'agency_name' : '门店名称'}，
   *  {'agency_id' : '门店编号', 'agency_name' : '门店名称'}]
   */
  public function get_type_1_agencys_by_company_id($company_id)
  {
    $this->load->model('agency_base_model');
    $agency_fileds = array('id as agency_id', 'name as agency_name');
    $this->agency_base_model->set_select_fields($agency_fileds);
    $agencys = $this->agency_base_model->get_children_by_company_id_type($company_id, 1);
    return $agencys;
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
