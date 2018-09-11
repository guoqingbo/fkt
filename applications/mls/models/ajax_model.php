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
 * ajax处理类
 *
 *
 * @package         zsb
 * @subpackage      Models
 * @category        Models
 * @author          Lion
 */
class Ajax_model extends MY_Model
{
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function array_to_json($array)
  {
    return json_encode($array);
  }
}

?>
