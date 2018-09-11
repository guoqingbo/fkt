<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * Advert_app_manage_model CLASS
 *
 * APP广告管理
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Common_base_model");

class Call_recharge_model extends Common_base_model
{
    protected $_tbl = 'call_recharge';

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
