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
 * Agency_model CLASS
 *
 * 门店业务逻辑类 提供增加公司，修改、删除等功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Agency_base_model");

class Agency_model extends Agency_base_model
{

  private $_cmt_tbl = 'agency';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 设置门店状态为有效
   * @param int $agency_id 门店编号
   * @return type
   */
  public function set_status_pass($agency_id)
  {
    return $this->set_esta($agency_id, 1);
  }

  /**
   * 设置门店状态为删除
   * @param int $agency_id 门店编号
   * @return type
   */
  public function set_status_delete($agency_id)
  {
    return $this->set_esta($agency_id, 2);
  }

  /**
   * 批量设置门店客户经理
   * @param int $agency_id 门店编号
   * @return type
   */
  public function set_master($ids, $master)
  {
    $sql = "update agency set master_id = '" . $master . "' where id in (" . $ids . ")";
    $this->db_city->query($sql);

    $sql = "update broker_info set master_id = '" . $master . "' where agency_id in (" . $ids . ")";
    $this->db_city->query($sql);
  }

  public function get_company_id($companyname)
  {
    $this->dbback_city->select('id');
      $cond_where = "company_id = 0 and name like '%$companyname%'";
      $this->dbback_city->where($cond_where);
      $companyinfo = $this->dbback_city->get($this->_cmt_tbl)->result_array();
      if (!empty($companyinfo)) {
          $company_id_str = "";
          foreach ($companyinfo as $key => $val) {
              $company_id_str .= $val['id'] . ",";
          }
      }

      return trim($company_id_str, ",");
  }
}

/* End of file Agency_model.php */
/* Location: ./app/models/Agency_model.php */
