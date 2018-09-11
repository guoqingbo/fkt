<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 登录控制器
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Cooperate_lol extends MY_Controller
{

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('project_cooperate_lol_model');
  }

  //登录页
  public function index()
  {
    $data = array();
    $data['page_title'] = '英雄联盟';
    $group = $this->project_cooperate_lol_model->get_cooperate_effect();
    if (is_full_array($group)) {
      foreach ($group as $key => $val) {
        $group[$key]['phone'] = substr_replace($val['phone'], 'XXXX', 3, 4);
      }
    }
    $data['broker_id'] = $this->user_arr['broker_id'];
    $data['group'] = $group;
    $broker_id = $this->user_arr['broker_id'];
    $data['cooperation'] = $this->project_cooperate_lol_model->get_cooperate_success_list($broker_id);
    $data['reward_list'] = $this->project_cooperate_lol_model->get_cooperate_reward_type();
    $data['win_list'] = $this->project_cooperate_lol_model->get_cooperate_win_list('', 0, 0);
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/xcc.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/lottery.js');
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js');
    $this->view('project/cooperate/lol/index', $data);
  }


  public function add_apply()
  {
    if (!$this->project_cooperate_lol_model->is_active_intime()) {
      echo json_encode(array('result' => 0, 'reason' => '活动已结束'));
      die();
    }
    $params = array(
      's_id' => $this->input->post('s_id'),
      'seller_owner' => $this->input->post('seller_owner'),
      'seller_idcard' => $this->input->post('seller_idcard'),
      'seller_telno' => $this->input->post('seller_telno'),
      'buyer_owner' => $this->input->post('buyer_owner'),
      'buyer_idcard' => $this->input->post('buyer_idcard'),
      'buyer_telno' => $this->input->post('buyer_telno'),
      'pic' => implode(',', $this->input->post('p_filename')),
      'create_time' => time()
    );

    if ($params['s_id'] && $params['seller_owner'] && $params['seller_idcard'] && $params['seller_telno'] &&
      $params['buyer_owner'] && $params['buyer_idcard'] && $params['buyer_telno'] && $params['pic']
    ) {
      if (preg_match('/[\x{4e00}-\x{9fa5}A-Za-z0-9]+$/u', $params['buyer_owner']) && preg_match('/[\x{4e00}-\x{9fa5}A-Za-z0-9]+$/u', $params['seller_owner'])) {
        if (preg_match('/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', $params['buyer_idcard']) && preg_match('/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', $params['seller_idcard'])) {
          if (preg_match('/(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/', $params['buyer_telno']) && preg_match('/(^(\d{3,4}-?)?\d{7,8})$|(1[0-9]{10})/', $params['seller_telno'])) {
            //通过s_id 获得c_id和order_sn
            $result = $this->project_cooperate_lol_model->get_cooperate_success_by_id($params['s_id'], $this->user_arr['broker_id']);
            if (is_full_array($result)) {
              $params['c_id'] = $result['c_id'];
              $params['order_sn'] = $result['order_sn'];
              $this->project_cooperate_lol_model->add_cooperate_success_applay($params);
              $data = array('result' => 200, 'msg' => '申请成功', 'data' => $params);
            } else {
              $data = array('result' => 101, 'msg' => '申请失败');
            }
          } else {
            $data = array('result' => 101, 'msg' => '请输入正确的电话号码');
          }
        } else {
          $data = array('result' => 101, 'msg' => '请输入正确的身份证号码');
        }
      } else {
        $data = array('result' => 101, 'msg' => '请输入正确的姓名');
      }
    } else {
      $data = array('result' => 100, 'msg' => '请填完完整的数据');
    }
    echo json_encode($data);
  }

  //抽奖
  public function lottery()
  {
    //防打开地址式
    if (!strstr($_SERVER['HTTP_HOST'], MLS_URL)) {
      die('为了建设祖国更美好的未来，请不要模拟参数！');
    }
    //判断时间内
    if (!$this->project_cooperate_lol_model->is_active_intime_lottery()) {
      echo json_encode(array('result' => 0, 'reason' => '活动已结束'));
      die();
    }
    $lottery_reward = array('result' => 1, 'award_id' => '', 'award_name' => '', 'award_writer' => '');
    $broker_id = $this->user_arr['broker_id'];
    //更新抽奖机会表
    $effect_rows = $this->project_cooperate_lol_model->reduce_broker_lucky_once($broker_id);
    if ($effect_rows > 0) {
      $insert_win_data = array();
      $reward_type = '';
      $reward = $this->project_cooperate_lol_model->get_cooperate_reward_type();
      $format_reward = change_to_key_array($reward, 'id');
      //优先查找预设某人中奖
      $reserve_win_where = 'broker_id = ' . $this->user_arr['broker_id'] . " and phone = '"
        . $this->user_arr['phone'] . "'" . ' and city_id = '
        . $this->user_arr['city_id'] . ' and status = 0';
      $reserve_win = $this->project_cooperate_lol_model->get_reserve_win_by($reserve_win_where);
      if (is_full_array($reserve_win)) {
        $reserve_win_row = $this->project_cooperate_lol_model->update_reserve_win($reserve_win['id'], array('status' => 1, 'update_time' => time()));
        if ($reserve_win_row > 0) {
          //预设的指定奖品
          $reward_type = $reserve_win['reward_type'];
        }
      } else //走正常抽奖逻辑
      {
        $rand_num = rand(1, 7);
        if ($rand_num == 1) //抽中除保温杯之外的奖品
        {
          $current_date = date('Y-m-d');
          //查找可以中奖的奖品
          $where = "open_time <= '{$current_date}' and status = 0 and valid_flag = 1";
          $reward_one = $this->project_cooperate_lol_model->get_cooperate_reward_by($where);
          if (is_full_array($reward_one)) {
            $update_affected_row = $this->project_cooperate_lol_model->update_cooperate_reward($reward_one['id'], array('status' => 1));
            if ($update_affected_row > 0) {
              $reward_type = $reward_one['type']; //随机抽中的奖品
            }
          }
        }
      }
      if ($reward_type == '') //没有抽中奖品，或者奖品已经发完了
      {
        $reward_type = 1; //抽中保温杯
      }
      //客户端返回奖口类型
      $reward_id = $this->project_cooperate_lol_model->rand_reward_id($format_reward[$reward_type]['num']);
      $lottery_reward['award_id'] = $reward_id;
      $lottery_reward['award_name'] = $format_reward[$reward_type]['name'];
      $lottery_reward['award_writer'] = $format_reward[$reward_type]['writer'];
      $lottery_reward['rand_num'] = $rand_num;
      //记录抽中奖人的名单
      $insert_win_data['broker_id'] = $this->user_arr['broker_id'];
      $insert_win_data['broker_name'] = $this->user_arr['truename'];
      $insert_win_data['phone'] = $this->user_arr['phone'];
      $insert_win_data['reward_type'] = $reward_type;
      $insert_win_data['create_time'] = time();
      $this->project_cooperate_lol_model->add_cooperate_win($insert_win_data);
    } else {
      //根本没有抽奖的机会
      $lottery_reward['award_name'] = '温馨提示';
      $lottery_reward['award_writer'] = '英雄，不要总是戳人家嘛！悄悄告诉你，每一次合作成交，初审资料通过后，都可以抽奖一次哦！';
    }
    echo json_encode($lottery_reward);
  }


  //判断活动是否结束
  function check_time()
  {
    //判断时间内
    if (!$this->project_cooperate_lol_model->is_active_intime()) {
      echo json_encode(array('result' => 0, 'reason' => '活动已结束'));
      die();
    } else {
      echo json_encode(array('result' => 1));
    }
  }

  function chushen($c_id = "")
  {
    $broker_id = $this->user_arr['broker_id'];
    $data['list'] = $this->project_cooperate_lol_model->get_all_ordersn_by_broker_id($broker_id, $c_id);

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/xcc.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/verification03.js');
    $this->view('project/cooperate/lol/cooperate_chushen_pop', $data);
  }

  function lol_chushen()
  {
    $broker_id = $this->user_arr['broker_id'];
    $data['list'] = $this->project_cooperate_lol_model->get_all_ordersn_by_broker_id($broker_id);

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/xcc.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/verification03.js');
    $this->view('project/cooperate/lol/lol_chushen_pop', $data);
  }
}
/* End of file Cooperate_project_lol.php */
/* Location: ./application/mls/controllers/Cooperate_project_lol.php */
