<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * My_DB Class
 *
 * @package         common
 * @subpackage      Libraries
 * @category        Libraries
 * @author          esf Dev Team
 * @link
 */
class My_DB
{
  private $db = NULL;

  public function __construct()
  {

  }

  public function get_db_obj($dbtype)
  {
    $CI = &get_instance();

    //第一次创建数据库连接
    if (!is_object($this->db)) {
      $this->db = $CI->load->database($dbtype, TRUE);
    }

    return $this->db;
  }

  public function __destruct()
  {
    if (is_object($this->db)) {
      $this->db->close();
    }
  }
}
