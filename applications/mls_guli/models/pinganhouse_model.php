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
 * sell_house_model CLASS
 *
 * 平安好房
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lalala
 */

//加载父类文件
load_m('Pinganhouse_base_model');

class Pinganhouse_model extends Pinganhouse_base_model
{


  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file sell_house_model.php */
/* Location: ./applications/mls_guli/models/sell_house_model.php */
