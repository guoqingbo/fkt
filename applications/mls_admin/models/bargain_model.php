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
 * bargain_model CLASS
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lalala
 */
load_m("Bargain_base_model");

class Bargain_model extends Bargain_base_model
{

    /**
     * 类初始化
     */
    public function __construct()
    {
        parent::__construct();
        $this->_tbl1 = 'bargain';
    }
}

/* End of file bargain_model.php */
/* Location: ./app/models/bargain_model.php */
