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
 * signatory_view_secrecy CLASS
 *
 * 经纪人查看保密信息记录
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("signatory_view_secrecy_base_model");

class signatory_view_secrecy extends signatory_view_secrecy_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file signatory_view_secrecy.php */
/* Location: ./app/models/signatory_view_secrecy.php */
