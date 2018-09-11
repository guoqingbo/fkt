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
 * Auth_review_model CLASS
 *
 * 身份、资质审核等功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Auth_review_base_model");

class Auth_review_model extends Auth_review_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

}

/* End of file Pay_applay_model.php */
/* Location: ./app/models/pay_applay_model.php */
