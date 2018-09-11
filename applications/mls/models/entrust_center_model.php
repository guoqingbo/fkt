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
 * entrust_center_model CLASS
 *
 * 营销中心业主委托
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
load_m("entrust_center_base_model");

class Entrust_center_model extends Entrust_center_base_model
{
  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }


}

/* End of file entrust_center_model.php */
/* Location: ./applications/mls/models/entrust_center_model.php */
