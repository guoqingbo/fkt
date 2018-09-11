<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2015
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * appoint_center_model CLASS
 *
 * 营销中心客户预约
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
load_m("appoint_center_base_model");

class appoint_center_model extends appoint_center_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->appoint = 'apnt';
  }


  /**
   * 删除预约信息
   */
  public function del_appoint($arr = array())
  {
    $result = $this->del($arr, 'db_city', $this->appoint);
    return $result;
  }
}

/* End of file appoint_center_model.php */
/* Location: ./applications/mls/models/appoint_center_model.php */
