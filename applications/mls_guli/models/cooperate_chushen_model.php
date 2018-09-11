<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          lalala
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Cooperate_model CLASS
 *
 * 房客源合作业务逻辑类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
load_m("cooperate_chushen_base_model");

class Cooperate_chushen_model extends Cooperate_chushen_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

}

/* End of file cooperate_model.php */
/* Location: ./app/models/cooperate_model.php */
