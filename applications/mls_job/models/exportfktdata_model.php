<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Exportfktdata_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->agency = 'agency';
    $this->broker_info = 'broker_info';
  }

  public function select_company($where = array(), $database = 'db_city')
  {
    $result = $this->get_data(array('form_name' => $this->agency, 'where' => $where, 'select' => array()), $database);
    return $result;
  }

  public function select_store($where = array(), $database = 'db_city')
  {
    $result = $this->get_data(array('form_name' => $this->agency, 'where' => $where, 'select' => array()), $database);
    return $result;
  }

  public function select_broker_info($database = 'db_city')
  {
    $result = $this->get_data(array('form_name' => $this->broker_info, 'select' => array('broker_id', 'agency_id', 'company_id', 'truename', 'phone', 'area_id', 'photo', 'status', 'group_id')), $database);
    return $result;
  }

}
