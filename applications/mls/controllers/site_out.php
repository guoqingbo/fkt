<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Site_out extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  //目标站点直接登录
  public function login_site()
  {
    $data['alias'] = $_GET['alias'];
    $data['city_spell'] = $_GET['city'];
    $data['username'] = $_GET['username'];
    $data['password'] = $_GET['password'];
    if ($data['alias'] == 'ajkvip') {
      $this->config->set_item('login_city', $data['city_spell']);
      $this->load->model('site_ajkvip_model');
      $data['ajkvip_use'] = $this->site_ajkvip_model->param_loginsign();
    }
    $this->load->view('site_set/site_login.php', $data);
  }

  //队列 定时任务
  public function demon_queue()
  {
    $city = $this->input->get('city');
    $this->config->set_item('login_city', $city);

    $this->load->model('site_mass_model');
    $this->load->model('group_queue_model');

    $queue = $this->group_queue_model->get_queue_demon(array('flag >= ' => 1));   //执行队列
    if ($queue) {
      $queue_id = $queue['id'];

      if (empty($queue['site_id']) || empty($queue['broker_id']) || empty($queue['house_id'])) {
        $del = $this->group_queue_model->delete_queue_demon(array('id' => $queue_id));  //删除定时任务  有问题的数据
        return false;
      } else {
        $doing = $this->group_queue_model->update_queue_demon($queue_id);  //返回false表示 已经有定时任务执行，并发需要
        if ($doing) {
          $bool = $this->site_mass_model->choose_site('', $queue['site_id']);  //加载 站点model
          switch ($queue['type']) {  //1发布 2刷新 3下架
            case '1':
              $res = $this->union_model->publish_param($queue);
              break;
            case '2':
              $res = $this->union_model->queue_refresh($queue);
              break;
            case '3':
              $res = $this->union_model->queue_esta($queue);
              break;
          }
          if (isset($res['flag']) && in_array($res['flag'], array('wubasms', 'gjcode'))) {
            //定时任务 重返 队列
            $queue['isback'] = 1;
            $queue['backinfo'] = serialize($res);
            $add = $this->group_queue_model->add_queue($queue);
          } elseif (isset($res['flag']) && $res['flag'] == 'success' && $queue['type'] == 1) {
            //涛涛的统计
            $site = $this->site_mass_model->get_only_site($queue['site_id']);
            $text = '群发房源编号为' . $queue['house_id'] . '的房源到' . $site['name'];
            $this->site_mass_model->operate_log($queue['broker_id'], $text);
          }
          $del = $this->group_queue_model->delete_queue_demon(array('id' => $queue_id));  //删除定时任务
          return true;
        }
      }
    }
  }


}

/* End of file site_out.php */
/* Location: ./application/mls/controllers/site_out.php */
