<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * autocollect_nj controller CLASS
 *
 * 自动更新数据库类
 *
 * @package         datacenter
 * @subpackage      controllers
 * @category        controllers
 * @author          lalala
 */
class Check_my_task extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  //检查任务是否逾期
  public function check_is_over_date()
  {
    $city = $this->input->get('city', true);

    $job_referer_url = MLS_JOB_URL . '/check_my_task/check_is_over_date';
    //job_start($job_referer_url);

    $this->set_city($city);
    $this->load->model('task_model');
    $this->task_model->check_is_over_date();

    //2016/2/1 星期一job_end($job_referer_url);
  }

  //检查任务是否离任务最迟完成时间15小时，系统提醒
  public function check_is_near_overdate()
  {
    $city = $this->input->get('city', true);

    $this->set_city($city);

    $this->load->model('task_model');
    $this->load->model('message_model');
    $this->load->model('broker_info_model');
    $result = $this->task_model->check_is_near_overdate();
    $time = time();
    foreach ($result as $key => $val) {
      if ($val['over_date'] > $time) {
        $timediff = $val['over_date'] - $time;
        $days = intval($timediff / 86400);
        //计算小时数
        $remain = $timediff % 86400;
        $hours = intval($remain / 3600);
        if ($hours <= 15) {
          $run_broker_id = $val['run_broker_id']; //执行人的id
          $run_broker_info = $this->broker_info_model->get_by_broker_id($run_broker_id);
          $run_broker_name = $run_broker_info['truename'];  //执行人的姓名

          $id = $val['house_id'] == 0 ? $val['custom_id'] : $val['house_id'];//获取相对应的房/客源编号
          if ($val['task_style'] == 1) {
            $params['id'] = format_info_id($id, 'sell');
            $params['f'] = 'f';
          } elseif ($val['task_style'] == 2) {
            $params['id'] = format_info_id($id, 'rent');
            $params['f'] = 'f';
          } elseif ($val['task_style'] == 3) {
            $params['id'] = format_info_id($id, 'buy_customer');
          } elseif ($val['task_style'] == 4) {
            $params['id'] = format_info_id($id, 'rent_customer');
          }
          $allot_broker_id = $val['allot_broker_id']; //分配者的id
          $allot_broker_info = $this->broker_info_model->get_by_broker_id($allot_broker_id);
          $params['name'] = $allot_broker_info['truename'];  //分配者的姓名

          $url = "/my_task/index";
          //发送站内信
          $return_id = $this->message_model->add_message('7-41', $run_broker_id, $run_broker_name, $url, $params);
          if ($return_id) {
            $this->task_model->update_by_id(array('is_near_overdate_pop' => 1), $val['id']);
          }
        }
      }
    }
  }
}
