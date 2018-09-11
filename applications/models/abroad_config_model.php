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
 * Contract_config_model CLASS *
 *
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Lalala
 */
class Abroad_config_model extends MY_Model
{

  public function get_config()
  {
    $config = array(
      //参与途径
      'from' => array(
        0 => '请选择',
        1 => '网站',
        2 => '微信',
        3 => 'APP',
        4 => '客户端'
      ),
      //参与方式
      'from_type' => array(
        0 => '请选择',
        1 => '报名',
        2 => '经纪人报备'
      ),
      //审核状态
      'status' => array(
        0 => '请选择',
        1 => '待审核',
        2 => '审核通过',
        3 => '审核不通过',
        4 => '已参会',
        5 => '已参团',
        6 => '已成交'
      )
    );
    return $config;
  }
}


/* End of file Contract_config_model.php */
/* Location: ./application/models/Contract_config_model.php */

