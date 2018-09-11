<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Community_bak extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('common_load_source_helper');
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->helper('community_helper');
    //$this->load->model('getpingying_model');//拼音模型类
    $this->load->model('community_bak_model');//楼盘模型类
    $this->load->model('district_model');//区属模型类
    $this->load->model('sell_house_model');//二手房源模型类
    $this->load->library('form_validation');//表单验证
    $this->load->library('getpingying');//拼音
    $this->load->library('Spell');//拼音
  }

  /**
   * 楼盘列表页面(根据区属、板块名获得id)
   */
  public function index()
  {
    $community_data = $this->community_bak_model->getcommunity(12000, 3000);
    $community_data2 = array();
    $i = 0;
    foreach ($community_data as $k => $v) {
      $i++;
      $dist_data = $this->district_model->get_district_id($v['dist_name']);
      $street_data = $this->district_model->get_street_ding(array('streetname' => $v['street_name']));
      $dist_id = $dist_data['id'];
      $street_id = $street_data[0]['id'];
      //修改数据
      $modify_data = array('dist_id' => $dist_id, 'streetid' => $street_id);
      $modify_result = $this->community_bak_model->modifycommunity($v['id'], $modify_data);
      echo $i;
      var_dump($modify_result);
    }
  }

  /**
   * 楼盘列表页面(根据楼盘名称获得名称拼音及首字母)
   */
  public function name_spell_s()
  {
    $community_data = $this->community_bak_model->getcommunity(0, 3000);
    die('gggg');
    $i = 0;
    foreach ($community_data as $k => $v) {
      $i++;
      if (!empty($v['cmt_name'])) {
        $name_spell_s = '';
        for ($i = 0; $i < strlen($v['cmt_name']); $i = $i + 3) {
          $strone = substr($v['cmt_name'], $i, 3);
          $name_spell_s .= getFirstCharter($strone);
        }
        $gbk_cmt_name = iconv('UTF-8', 'GBK', $v['cmt_name']);
        $name_spell = $this->getpingying->getAllPY($gbk_cmt_name);
      }
      //修改数据
      $modify_data = array('name_spell_s' => $name_spell_s, 'name_spell' => $name_spell);
      $modify_result = $this->community_bak_model->modifycommunity($v['id'], $modify_data);
      echo $i;
      var_dump($modify_result);
    }
  }

  /**
   * 设置正式楼盘
   */
  public function set_status()
  {
    $community_data = $this->community_bak_model->getcommunity(12000, 3000);
    $i = 0;
    foreach ($community_data as $k => $v) {
      $i++;
      //修改数据
      $modify_data = array('status' => 2);
      $modify_result = $this->community_bak_model->modifycommunity($v['id'], $modify_data);
      echo $i;
      var_dump($modify_result);
    }
  }

  /**
   * 竣工日期**年转化为时间戳
   */
  public function build_date()
  {
    $community_data = $this->community_bak_model->getcommunity(18000, 3000);
    $i = 0;
    foreach ($community_data as $k => $v) {
      $i++;
      //修改数据
      if (!empty($v['build_date'])) {
        $year_str = strval($v['build_date']) . '-1-1';
        $_time = strtotime($year_str);
        $modify_data = array('build_date' => $_time);
        $modify_result = $this->community_bak_model->modifycommunity($v['id'], $modify_data);
        echo $i;
        var_dump($modify_result);
        echo '<br>';
      }
    }
  }

}
