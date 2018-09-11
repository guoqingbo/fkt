<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package  CodeIgniter
 * @author  ExpressionEngine Dev Team
 * @copyright  Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license  http://codeigniter.com/user_guide/license.html
 * @link  http://codeigniter.com
 * @since  Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Loader Class
 *
 * Loads views and files
 *
 * @package  CodeIgniter
 * @subpackage  Libraries
 * @author  ExpressionEngine Dev Team
 * @category  Loader
 * @link  http://codeigniter.com/user_guide/libraries/loader.html
 */
class MY_Loader extends CI_Loader
{
  //构造函数
  public function __construct()
  {
    parent::__construct();
    $this->_ci_ob_level = ob_get_level();

    //xz 2014/5/30
    $this->_ci_library_paths = array(APPPATH, PUBLICPATH, BASEPATH);
    $this->_ci_helper_paths = array(APPPATH, PUBLICPATH, BASEPATH);
    $this->_ci_model_paths = array(APPPATH, PUBLICPATH);
    $this->_ci_view_paths = array(APPPATH . 'views/' => TRUE);

    log_message('debug', "Loader Class Initialized");
  }
}

/* End of file MY_Loader.php */
/* Location: ./application/zsb/core/MY_Loader.php */
