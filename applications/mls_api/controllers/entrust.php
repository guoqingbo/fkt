<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 委托房源接口
 *
 * 用于MLS房源委托功能数据处理
 *
 *
 * @package         applications
 * @author          fisher
 * @copyright       Copyright (c) 2006 - 2015
 * @version         1.0
 */
class Entrust extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();

    //设置成熟参数
    $city = $this->input->get('city', TRUE);
    $this->set_city($city);

    $this->load->model('entrust_model');
  }

  /**
   * Index Page for this controller.
   */
  public function index()
  {
    $this->result(1, 'Entrust API for MLS.');
  }

  /**
   *  提交委托房源数据
   */
  public function set_entrust_house()
  {
    $fid = $this->input->get('fid', TRUE);
    $fid = intval($fid);

    if ($fid > 0) {
      $entrust = $this->entrust_model->get_entrust_id_by_houseid($fid);
      if (!isset($entrust['state'])) {
        $id = $this->entrust_model->add_entrust_house($fid);

        if ($id > 0) {
          $this->result(1, '委托房源设置成功。', array('id' => $id));
        } else {
          $this->result(0, '委托房源设置失败。');
        }
      } else {
        $this->result(0, '该房源已设置过委托房源，不可再次设置。');
      }
    } else {
      $this->result(0, '委托房源设置失败，房源编号错误。');
    }
  }

  /**
   *  停止委托房源
   */
  public function stop_entrust_house()
  {
    $fid = $this->input->get('fid', TRUE);
    $fid = intval($fid);
    $result = 0;

    if ($fid > 0) {
      $result = $this->entrust_model->stop_entrust_house($fid);
    }

    if ($result > 0) {
      $this->result(1, '委托房源已取消。');
    } else {
      $this->result(0, '委托房源取消失败。');
    }
  }

  /**
   * 根据房源ID获取参与该房源委托的经纪人列表
   */
  public function get_entrust_list()
  {
    $fid = $this->input->get('fid', TRUE);
    $fid = intval($fid);
    $nopic = $this->input->get('nopic', TRUE);
    $nopic = intval($nopic);

    if ($fid > 0) {
      $entrust = $this->entrust_model->get_entrust_id_by_houseid($fid);

      if (isset($entrust['state']) && $entrust['state'] == 1) {
        $broker_arr = $this->entrust_model->get_entrust_broker_by_houseid($fid);
        $appraise_arr = $this->entrust_model->get_entrust_appraise_by_houseid($fid);
        if ($nopic != 1) {
          $pic_arr = $this->entrust_model->get_entrust_pic_by_houseid($fid);
        }

        if (is_array($appraise_arr) && !empty($appraise_arr)) {
          $temp = array();
          foreach ($appraise_arr as $appraise) {
            $temp[$appraise['brokerid']] = $appraise;
          }

          $appraise_arr = $temp;
        }

        if (is_array($broker_arr) && !empty($broker_arr)) {
          $temp = array();
          foreach ($broker_arr as $key => $broker) {
            if ($broker['receive']) {
              $temp[$key]['brokerid'] = $broker['brokerid'];
              $temp[$key]['phone'] = $broker['phone'];
              $temp[$key]['truename'] = $broker['truename'];
              $temp[$key]['company_name'] = $broker['company_name'];
              $temp[$key]['photo'] = $broker['photo'];
              $temp[$key]['appraise'] = isset($appraise_arr[$broker['brokerid']]) ? $appraise_arr[$broker['brokerid']]['appraise'] : '';
              $temp[$key]['dateline'] = isset($appraise_arr[$broker['brokerid']]) ? $appraise_arr[$broker['brokerid']]['dateline'] : '';
            }
          }

          $broker_arr = $temp;
        }

        $data = array();
        $data['broker'] = $broker_arr;
        //$data['assess'] = $appraise_arr;
        if ($nopic != 1) {
          $data['pic'] = $pic_arr;
        }

        $this->result(1, 'success', $data);
      } else {
        $this->result(0, '该房源不是委托房源。');
      }
    } else {
      $this->result(0, '委托房源设置失败，房源编号错误。');
    }
  }
}

/* End of file entrust.php */
/* Location: ./application/controllers/entrust.php */
