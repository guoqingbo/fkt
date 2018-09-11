<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * User_advise_model CLASS
 *
 * 用户建议模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @date      2015-03-17
 * @author          angel_in_us
 */
class User_advise_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->mls_user_advice = 'user_advice';
  }

  /**
   * 把用户所发布的反馈建议入库到 mls_user_advice 表里
   * @date      2015-03-17
   * @author       angel_in_us
   */
  public function add_advice($data = array(), $database = 'db_city', $form_name = '')
  {
    $result = $this->add_data($data, $database, $this->mls_user_advice);
    return $result;
  }
}

/* End of file user_advise_model.php */
/* Location: ./application/mls/models/user_advise_model.php */
