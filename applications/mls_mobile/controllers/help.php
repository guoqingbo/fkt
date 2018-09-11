<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统控制器
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 */

/**
 * Help Controller CLASS
 *
 * MLS-MOBILE帮助中心
 *
 * @package         MLS
 * @subpackage      Controllers
 * @category        Controllers
 * @author          xz
 */
class Help extends MY_Controller
{

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('apply_manage_model');
  }

  //税费计算器
  public function get_tax_calculate()
  {
    $averprice = $this->input->get('averprice');
    $buildarea = $this->input->get('buildarea');
    $transferyear = $this->input->get('transferyear');
    $firstbuy = $this->input->get('firstbuy');
    if (!$averprice || !$buildarea || !$transferyear || !$firstbuy) {
      $this->result(0, '参数不合法');
    } else {
      $lab4_rate = $this->user_arr['city_spell'] == 'qingdao' ? 2 : 1;
      $this->load->model('help_model');
      $housekind = 1;
      $this->result(1, '查询成功', $this->help_model->get_tax_calculate($housekind, $averprice,
        $buildarea, $transferyear, $firstbuy, $lab4_rate));
    }
  }

  public function get_daikuan()
  {
    $this->load->library('Daikuan');
    $api_array = $this->daikuan->get_daikuan();
    foreach ($api_array['loan'] as $k => $v) {
      //商业贷款
      $tmp = array();
      $tmp['Ratio12'] = strval($v['business']['1']);
      $tmp['Ratio36'] = $v['business']['3'] ? strval($v['business']['3']) : strval($v['business']['5']);
      $tmp['Ratio60'] = strval($v['business']['5']);
      $tmp['Ratio360'] = strval($v['business']['30']);
      $tmp['Description'] = $v['description'];
      $tmp['selected'] = $v['selected'] ? '1' : '0';//默认选中的利率
      $rate_conf['Bussiness'][] = $tmp;

      //公积金贷款
      $tmp = array();
      $tmp['Ratio60'] = strval($v['fund']['5']);
      $tmp['Ratio360'] = strval($v['fund']['30']);
      $tmp['Description'] = $v['description'];
      $tmp['selected'] = $v['selected'] ? '1' : '0';//默认选中的利率
      if ($tmp['selected'] == 1 && ($api_key == "iphone" || $api_key == "iPhone")) {
        array_unshift($rate_conf['Found'], $tmp);
      } else {
        $rate_conf['Found'][] = $tmp;
      }
    }
    $this->result(1, '查询成功', $rate_conf);
  }

  public function check_version()
  {
    $now_version = $this->input->get('version');
    $devicetype = $this->input->get('api_key');

    if (empty($now_version) || empty($devicetype)) {
      $this->result(0, '参数不合法');
      return false;
    }

    //exist_new_version 是否有新版本
    //is_forced 是否强制升级
    //update_url 更新地址
    $exist_new_version = 0;
    $update_version = '';
    $is_forced = 0;
    $update_url = '';
    $update_content = '';
    $file_size = '';
    $apply_name = '';

    if ($devicetype == 'android') {
      $androidupdate = $this->apply_manage_model->get_apply(array('type' => 1, 'version_type' => 2));
      if ($androidupdate['version'] && $androidupdate['version'] != $now_version) {
        $exist_new_version = 1;
        $update_version = $androidupdate['version'];
        $is_forced = $androidupdate['is_forced'];
        $update_url = $androidupdate['update_url'];
        $update_content = $androidupdate['update_content'];
        $file_size = $androidupdate['apply_size'];
        $apply_name = $androidupdate['apply_name'];
      }
    } else if ($devicetype == 'iPhone') {
      $iosupdate = $this->apply_manage_model->get_apply(array('type' => 1, 'version_type' => 1));
      if ($iosupdate['version'] && $iosupdate['version'] != $now_version) {
        $exist_new_version = 0;
        $update_version = $iosupdate['version'];
        $is_forced = $iosupdate['is_forced'];
        $update_url = $iosupdate['update_url'];
        $update_content = $iosupdate['update_content'];
        $file_size = $iosupdate['apply_size'];
        $apply_name = $iosupdate['apply_name'];
      }
    }

    $data = array(
      'exist_new_version' => $exist_new_version,
      'update_version' => $update_version,
      'is_forced' => $is_forced,
      'update_url' => $update_url,
      'update_content' => $update_content,
      'file_size' => $file_size,
      'apply_name' => $apply_name
    );

    $this->result(1, '查询成功', $data);
  }

  /**
   * 获取视频帮助
   */
  public function video_help()
  {
    $help_content = file_get_contents(dirname(__FILE__) . '/../views/video/video.php');
    $this->result(1, '查询成功', array('help_content' => $help_content));
  }
}
