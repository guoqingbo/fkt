<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 房友房源数据导入
 *
 * 用于MLS房源导入处理
 *
 *
 * @package         applications
 * @author          lalala
 * @copyright       Copyright (c) 2006 - 2015
 * @version         1.0
 */
class Report extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 海外报备加成长值
   */
  public function abroad_report()
  {
    //加载出售基本配置MODEL
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    $string = $this->input->get('string');
    $abroad = json_decode($string, true);
    $this->load->model('api_broker_level_base_model');
    $this->api_broker_level_base_model->set_broker_param(array('broker_id' => $abroad['broker_id']), 1);
    if (is_full_array($abroad)) {
      $this->api_broker_level_base_model->abroad_report($abroad);
    }
  }

  /**
   * 旅游报备加成长值
   */
  public function tourism_report()
  {
    //加载出售基本配置MODEL
    $city = ltrim($this->input->get('city', TRUE));
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    $string = $this->input->get('string');
    $tourism = json_decode($string, true);
    $this->load->model('api_broker_level_base_model');
    $this->api_broker_level_base_model->set_broker_param(array('broker_id' => $tourism['broker_id']), 1);
    if (is_full_array($tourism)) {
      $this->api_broker_level_base_model->tourism_report($tourism);
    }
  }

  /**
   * 新房报备加成长值
   */
  public function xffx_baobei()
  {
    $post_param = $this->input->post(NULL, TRUE);
    //加载出售基本配置MODEL
    $city = ltrim($post_param['city']);
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);

    //加成长值
    $this->load->model('api_broker_level_base_model');
    $this->api_broker_level_base_model->set_broker_param(array('broker_id' => $post_param['broker_id']), 1);
    if (is_full_array($post_param)) {
      $this->api_broker_level_base_model->xffx_baobei($post_param);
    }
    //加积分
    $this->load->model('api_broker_credit_base_model');
    $this->api_broker_credit_base_model->set_broker_param(array('broker_id' => $post_param['broker_id']), 1);
    if (is_full_array($post_param)) {
      $this->api_broker_credit_base_model->xffx_baobei($post_param);
    }
  }

  /**
   * 新房带看加成长值
   */
  public function xffx_daikan()
  {
    $post_param = $this->input->post(NULL, TRUE);
    //加载出售基本配置MODEL
    $city = ltrim($post_param['city']);
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);
    //加成长值
    $this->load->model('api_broker_level_base_model');
    $this->api_broker_level_base_model->set_broker_param(array('broker_id' => $post_param['broker_id']), 1);
    if (is_full_array($post_param)) {
      $this->api_broker_level_base_model->xffx_daikan($post_param);
    }
    //加积分
    $this->load->model('api_broker_credit_base_model');
    $this->api_broker_credit_base_model->set_broker_param(array('broker_id' => $post_param['broker_id']), 1);
    if (is_full_array($post_param)) {
      $this->api_broker_credit_base_model->xffx_daikan($post_param);
    }
  }

  /**
   * 新房认购加成长值
   */
  public function xffx_rengou()
  {
    $post_param = $this->input->post(NULL, TRUE);
    //加载出售基本配置MODEL
    $city = ltrim($post_param['city']);
    $city = $city ? $city : 'hz';
    //设置城市参数
    $this->set_city($city);
    //加成长值
    $this->load->model('api_broker_level_base_model');
    $this->api_broker_level_base_model->set_broker_param(array('broker_id' => $post_param['broker_id']), 1);
    if (is_full_array($post_param)) {
      $this->api_broker_level_base_model->xffx_rengou($post_param);
    }
    //加积分
    $this->load->model('api_broker_credit_base_model');
    $this->api_broker_credit_base_model->set_broker_param(array('broker_id' => $post_param['broker_id']), 1);
    if (is_full_array($post_param)) {
      $this->api_broker_credit_base_model->xffx_rengou($post_param);
    }
  }


}
