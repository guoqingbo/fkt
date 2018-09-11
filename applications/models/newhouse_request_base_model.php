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
 * Newhouse_request_base_model CLASS
 *
 * 请求新房接口功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Newhouse_request_base_model extends MY_Model
{

  // private $_base_api_url = 'http://mls-xffxapi.house365.com/fgj/index.php?ver=v2&';

  private $_city = '';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->library('Curl');
    $this->_city = $this->config->item('login_city');
  }

  //获取楼盘
  public function project()
  {
    $this->_base_api_url .= 'method=getAllHouse&city=' . $this->_city;
    return Curl::curl_get_contents($this->_base_api_url);
  }
}
