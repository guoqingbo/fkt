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
 * grab_model CLASS
 *
 * 抢房、客源model。
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          LION
 */
load_m('grab_base_model');

class Grab_model extends Grab_base_model
{
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file follow_model.php */
/* Location: ./application/mls_guli/models/follow_model.php */

