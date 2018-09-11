<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
load_m('Basic_setting_base_model');

class basic_setting_model extends Basic_setting_base_model
{
  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    parent::set_bs_tbl('basic_setting');
  }
}
