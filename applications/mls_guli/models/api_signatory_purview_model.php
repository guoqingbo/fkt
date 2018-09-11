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
 * 经纪人权限API接口
 *
 *
 * @package         zsb
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Api_signatory_purview_base_model");

class Api_signatory_purview_model extends Api_signatory_purview_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }
}

?>
