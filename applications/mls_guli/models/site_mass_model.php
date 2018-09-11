<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_m("Site_mass_base_model");

class Site_mass_model extends Site_mass_base_model
{
  public function __construct()
  {
    parent::__construct();
  }

  //获取 站点
  public function mget_site_by_where($where)
  {
    $mass_site = $this->dbback_city->where($where)->get('mass_site')->result_array();
    return $mass_site;
  }

  /**
   * 获取某条房源的图片
   * 来自 pic_model
   * @param string $tbl 表名
   * @param int $house_id 房源编号
   * @param int $sort 图片类型
   * @return array
   */
  public function find_house_pic_by_ids($tbl, $ids)
  {
    if ($ids) {
      $ids = trim($ids, ',');
      $this->db_city->select('id,type,url');
      $where = "id in ($ids) ";
      $this->db_city->where($where);
      return $this->db_city->get($tbl)->result_array();
    }

  }

  //涛涛的统计
  public function operate_log($signatory_id, $text)
  {
    $signatory_info = $this->dbback_city->where(array('signatory_id' => $signatory_id))->get('signatory_info')->row_array();

    $add_log_param = array();
    $add_log_param['company_id'] = $signatory_info['company_id'];
    $add_log_param['department_id'] = $signatory_info['department_id'];
    $add_log_param['signatory_id'] = $signatory_info['signatory_id'];
    $add_log_param['signatory_name'] = $signatory_info['truename'];
    $add_log_param['type'] = 37;
    $add_log_param['text'] = $text;
    $add_log_param['from_system'] = 1;
    $add_log_param['from_ip'] = get_ip();
    $add_log_param['mac_ip'] = '127.0.0.1';
    $add_log_param['from_host_name'] = '127.0.0.1';
    $add_log_param['hardware_num'] = '测试硬件序列号';
    $add_log_param['time'] = time();

    $this->load->model('signatory_operate_log_model');
    $this->Signatory_operate_log_model->add_operate_log($add_log_param);
  }
}


