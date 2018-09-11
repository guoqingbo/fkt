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


load_m("city_base_model");

class City_model extends city_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

}

/* End of file City_model.php */
/* Location: ./app/models/City_model.php */
