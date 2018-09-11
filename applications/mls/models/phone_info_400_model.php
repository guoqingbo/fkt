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
 * Phone_info_400_model CLASS
 *
 * 400电话管理
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          yuan
 */
load_m("Phone_info_400_base_model");

class Phone_info_400_model extends Phone_info_400_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file Advert_app_manage_model.php */
/* Location: ./app/models/Advert_app_manage_model.php */
