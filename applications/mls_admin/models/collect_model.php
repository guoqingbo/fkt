<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * 房源采集（赶集，58）
 * 2016.3.10
 * cc
 */
load_m('collect_base_model');

class Collect_model extends Collect_base_model
{

  public function __construct()
  {
    parent::__construct();
  }
}
