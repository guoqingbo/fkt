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
 * Department_model CLASS
 *
 * 归属公司、员工工资展示类 提供展示公司，员工工资等功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lu
 */
load_m("Company_employee_base_model");

class Company_employee_model extends company_employee_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file Department_model.php */
/* Location: ./app/models/Department_model.php */
