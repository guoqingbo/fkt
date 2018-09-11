<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends MY_Controller
{

  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   *    http://example.com/index.php/welcome
   *  - or -
   *    http://example.com/index.php/welcome/index
   *  - or -
   * Since this controller is set as the default controller in
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see http://codeigniter.com/user_guide/general/urls.html
   */
  public function index($pclogin = 0)
  {
    setcookie('mortgage', '0', time() - 3600, '/');
    //当前用户信息
    $this_user = $this->user_arr;
    //事件提醒
    $this->load->model('remind_model');
    //消息、公告模型类
    $this->load->model('message_model');
    //跟进任务
    $this->load->model('task_model');
    //事件接收者
    $this->load->model('event_receiver_model');
    //查看保密信息与跟进进程
    $this->load->model('secret_follow_process_model');

    //根据基本设置，处理查看保密信息与写跟进进程。
    $company_basic_data = $this->company_basic_arr;
    if (is_full_array($company_basic_data)) {
      //是否开启查看保密信息必须写跟进
      $is_secret_follow = $company_basic_data['is_secret_follow'];
    } else {
      $is_secret_follow = '';
    }

    $data_view = array();

    //系统标题
    $data_view['title'] = $this->config->item('title');
    //系统菜单
    $data_view['menu'] = $this->permission_tab_model->get_module();
    //$data_view['menu'] = $this->config->item('menu');
    $where_cond = array(
      'broker_id' => $this_user['broker_id'],
      'status' => 1
    );
    $query_result = $this->secret_follow_process_model->get($where_cond);
    $menu_num = 0;
    $house_id = 0;
    if ('1' == $is_secret_follow && is_full_array($query_result) && $this_user['group_id'] != '1') {
      $menu_num = 1;
      $type = $query_result[0]['type'];
      $house_id = $query_result[0]['row_id'];
      if (is_full_array($data_view['menu']) && '2' == $type) {
        $data_view['menu'][1]['url'] = 'rent/lists/';
      }
    }
    $data_view['menu_num'] = $menu_num;

    //登录信息配置
    $data_view['deviceid'] = '';
    $data_view['osid'] = 0;
    //根据当前经纪人id（接受者id）获得对应的事件id
    $receiver_id = $this->user_arr['broker_id'];
    $event_id_data = $this->event_receiver_model->get_event_by_receiver($receiver_id);
    $event_id_arr = array();
    foreach ($event_id_data as $k => $v) {
      $event_id_arr[] = $v['event_id'];
    }

    $where_in = array();
    if (!empty($event_id_arr)) {
      $where_in = array('id', $event_id_arr);
    }

    //事件提醒数量
    $data_view['remind_num'] = $this->remind_model->get_remind_num(array(), $where_in);
    //我的消息数量
    $data_view['message_num'] = $this->message_model->get_count_by_cond(array('from' => 0, 'broker_id' => $this_user['broker_id'], 'is_read' => 0));
    //跟进任务
    $cond_where_task_info = 'id <> 0 and run_broker_id = "' . $this_user['broker_id'] . '"';
    $data_view['task_num'] = $this->task_model->count_by($cond_where_task_info);
    //最近采集的两条房源（滚动）
    $house_data = array();
    $data_view['recent_house_data'] = $house_data;

    if ($pclogin == 1) {
      $this->frame('pcwelcome', $data_view);
    } else {
      $this->frame('welcome', $data_view);
    }
  }


  public function save_suggest()
  {
    //意见建议
    $this->load->model('suggest_model');

    //经纪人信息
    $this->load->model('broker_model');
    $feedback = $this->input->post('feedback');
    $broker_id = $this->user_arr['broker_id'];
    $broker_info = $this->broker_model->get_by_id($broker_id);
    $telno = $broker_info['phone'];
    $city_id = $broker_info['city_id'];
    $add_data = array(
      'feedback' => $feedback,
      'telno' => $telno,
      'city_id' => $city_id,
      'status' => 1,
      'dateline' => time()
    );

    $result = $this->suggest_model->insert($add_data);

    if ($result) {
      echo 1;
    } else {
      echo 0;
    }
  }

  public function test()
  {
    $this->load->model('permission_tab_model');

    $this->permission_tab_model->get_module();
    echo $this->permission_tab_model->reset_tabs('rent', 'lists_pub');
  }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
