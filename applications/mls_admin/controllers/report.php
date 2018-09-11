<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * report CLASS
 *
 * 公盘公客举报控制器类
 *
 * @package         datacenter
 * @subpackage      controllers
 * @category        controllers
 * @author          angel_in_us
 */
class Report extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('report_model');//中介黑名单模型类
    $this->load->library('form_validation');//表单验证
    $this->load->helper('user_helper');
  }

  /**
   * 举报列表页面
   */
  public function index()
  {
    $data = array();
    $style = $this->input->get('style');
    if (!$style) {
      $style = 1;
    }

    $data['style'] = $style;
    switch ($style) {
      case 1:
        $data['prize_danwei'] = '万元';
        break;
      case 2:
        $data['prize_danwei'] = '元/月';
        break;
      case 3:
        $data['prize_danwei'] = '万元';
        break;
      case 4:
        $data['prize_danwei'] = '元/月';
        break;
    }
    $data['title'] = '公盘公客举报管理';
    $data['conf_where'] = 'index';


    $where = 'style = ' . $style;

    $pg = $this->input->post('pg');

    $status = $this->input->post('status');
    $data['status'] = $status;
    if ($status) {
      $where .= ' and status =' . $status;
    }
    $type = $this->input->post('type');
    $data['type'] = $type;
    if ($type) {
      $where .= ' and type =' . $type;
    }
    $broker_name = $this->input->post('broker_name');
    $data['broker_name'] = $broker_name;
    if ($broker_name) {
      $where .= ' and broker_name ="' . $broker_name . '"';
    }
    $brokered_name = $this->input->post('brokered_name');
    $data['brokered_name'] = $brokered_name;
    if ($brokered_name) {
      $where .= ' and brokered_name ="' . $brokered_name . '"';
    }

    //分页开始
    $data['count'] = $this->report_model->count_by($where);

    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['count'] ? ceil($data['count']
      / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages']
      && $data['pages'] != 0) ? $data['pages']
      : $data['page'];  //判断跳转页数
    //计算记录偏移量
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);

    $report_info = $this->report_model->get_all_by($where, $data['offset'], $data['pagesize']);

    $data['report_info'] = $report_info;
    //print_r($data);exit;
    $this->load->view('report/report', $data);
  }

  /**
   * 修改待审核
   */
  public function modify()
  {
    $id = $this->input->post('id');
    $modify_status = $this->input->post('status_modify');
    $this->report_model->update_by_id(array('status' => $modify_status), $id);
    $this->load->model('api_broker_sincere_model');
    $this->load->model('message_model');

    $report_info = $this->report_model->get_by_id($id);
    $style = $report_info['style'];
    $broker_id = $report_info['broker_id'];//举报人id
    $broker_name = $report_info['broker_name'];
    $brokered_id = $report_info['brokered_id'];//被举报人id
    $brokered_name = $report_info['brokered_name'];
    $house_info = $report_info['house_info'];
    $unser_house_info = unserialize($house_info);
    $number = $report_info['number'];//房源编号
    $type = $report_info['type'];//举报类型 1房源虚假2客源虚假3已成交 4其它
    $content = $report_info['content'];//举报内容
    $ser_house_info = serialize($unser_house_info);
    if ($modify_status == 2) {//已通过
      //举报人奖励
      $in_scores = 0;
      //当房源虚假/客源虚假 扣除被举报人信息真实度
      if ($type == 1 || $type == 2) {
        $alias = $type == 1 ? 'house_info_false' : 'customer_info_false';
        $score = $this->api_broker_sincere_model->update_infomation($brokered_id, $alias);

        //录入处罚库
        $this->api_broker_sincere_model->punish($broker_id, $brokered_id, $alias, $score, format_info_id($number, $unser_house_info['tbl']), $ser_house_info);
      }
      $this->load->model('cooperate_model');
      if ($style == 1 or $style == 2) {//房源信息
        //合作终止
        $this->cooperate_model->update_info_by_cond(array('esta' => 11, 'who_do' => 0, 'dateline' => time()), 'rowid = ' . $number);
        //更新房源状态***
        if ($style == 1) {//出售
          $this->load->model('sell_house_model');
          $this->sell_house_model->update_info_by_cond(array('isshare' => 0, 'is_report' => 1, 'updatetime' => time()), 'id = ' . $number);

          //to举报人
          $params = array();
          $params['type'] = "f";
          $params['name'] = "";
          $params['id'] = format_info_id($number, 'sell');
          $params['prize'] = $in_scores;
          $params['jf'] = 50;
          $params['pf'] = 0.1;
          //33
          $this->message_model->add_message('1-12', $broker_id, $broker_name, '/my_growing_credit/', $params);
          switch ($type) {
            case 1://房源虚假
              //to被举报人
              $this->message_model->add_message('1-14-2', $brokered_id, $brokered_name, '/my_growing_punish/', $params);
              break;
            case 3://已成交
              //to被举报人
              $this->message_model->add_message('1-14-2', $brokered_id, $brokered_name, '', $params);
              break;
            case 4://其它
              //to被举报人
              $params['name'] = $content;
              $this->message_model->add_message('1-14-2', $brokered_id, $brokered_name, $params);
              break;
          }

        } else {//出租
          $this->load->model('rent_house_model');
          $this->rent_house_model->update_info_by_cond(array('isshare' => 0, 'is_report' => 1, 'updatetime' => time()), 'id = ' . $number);

          //to举报人
          $params = array();
          $params['type'] = "f";
          $params['name'] = "";
          $params['id'] = format_info_id($number, 'rent');
          $params['prize'] = $in_scores;
          $params['jf'] = 50;
          $params['pf'] = 0.1;
          $params['reason'] = $content;
          $this->message_model->add_message('1-12', $broker_id, $broker_name, '/my_growing_credit/', $params);
          switch ($type) {
            case 1://房源虚假
              //to被举报人
              $this->message_model->add_message('1-14-2', $brokered_id, $brokered_name, '/my_growing_punish/', $params);
              break;
            case 3://已成交
              //to被举报人
              $this->message_model->add_message('1-14-2', $brokered_id, $brokered_name, '', $params);
              break;
            case 4://其它
              //to被举报人
              $this->message_model->add_message('1-14-5', $brokered_id, $brokered_name, '', $params);
              break;
          }
        }

      } else {//客源消息
        //合作终止
        $this->cooperate_model->update_info_by_cond(array('esta' => 11, 'who_do' => 0, 'dateline' => time()), 'customer_id = ' . $number);
        //更新客源状态
        if ($style == 3) {//求购
          $this->load->model('buy_customer_model');
          $this->buy_customer_model->update_customerinfo_by_cond(array('is_share' => 0, 'is_report' => 1, 'updatetime' => time()), 'id = ' . $number);

          //to举报人
          $params = array();
          $params['name'] = "";
          $params['id'] = format_info_id($number, 'buy_customer');
          $params['prize'] = $in_scores;
          $params['jf'] = 50;
          $params['pf'] = 0.1;
          $params['reason'] = $content;
          $this->message_model->add_message('1-12', $broker_id, $broker_name, '/my_growing_credit/', $params);
          switch ($type) {
            case 2://客源虚假
              //to被举报人
              $this->message_model->add_message('1-14-2', $brokered_id, $brokered_name, '/my_growing_punish/', $params);
              break;
            case 3://已成交
              //to被举报人
              $this->message_model->add_message('1-14-2', $brokered_id, $brokered_name, '', $params);
              break;
            case 4://其它
              //to被举报人
              $this->message_model->add_message('1-14-5', $brokered_id, $brokered_name, '', $params);
              break;
          }
        } else {//求租
          $this->load->model('rent_customer_model');
          $this->rent_customer_model->update_customerinfo_by_cond(array('is_share' => 0, 'is_report' => 1, 'updatetime' => time()), 'id = ' . $number);

          //to举报人
          $params = array();
          $params['name'] = "";
          $params['id'] = format_info_id($number, 'rent_customer');
          $params['prize'] = $in_scores;
          $params['jf'] = 50;
          $params['pf'] = 0.1;
          $params['reason'] = $content;
          $this->message_model->add_message('1-12', $broker_id, $broker_name, '/my_growing_credit/', $params);
          switch ($type) {
            case 2://客源虚假
              //to被举报人
              $this->message_model->add_message('1-14-2', $brokered_id, $brokered_name, '/my_growing_punish/', $params);
              break;
            case 3://已成交
              //to被举报人
              $this->message_model->add_message('1-14-2', $brokered_id, $brokered_name, '', $params);
              break;
            case 4://其它
              //to被举报人
              $this->message_model->add_message('1-14-2', $brokered_id, $brokered_name, '', $params);
              break;
          }
        }
      }
    } elseif ($modify_status == 3) {//未通过
      if ($style == 1) {//出售
        //to举报人
        $params = array();
        $params['type'] = "f";
        $params['name'] = "";
        $params['id'] = format_info_id($number, 'sell');
        $this->message_model->add_message('1-13', $broker_id, $broker_name, '/my_growing_credit/', $params);
      } elseif ($style == 2) {//出租
        //to举报人
        $params = array();
        $params['type'] = "f";
        $params['name'] = "";
        $params['id'] = format_info_id($number, 'rent');
        $this->message_model->add_message('1-13', $broker_id, $broker_name, '/my_growing_credit/', $params);
      } elseif ($style == 3) {//求购
        //to举报人
        $params = array();
        $params['name'] = "";
        $params['id'] = format_info_id($number, 'buy_customer');
        $this->message_model->add_message('1-13', $broker_id, $broker_name, '/my_growing_credit/', $params);
      } else {//求租
        //to举报人
        $params = array();
        $params['name'] = "";
        $params['id'] = format_info_id($number, 'rent_customer');
        $this->message_model->add_message('1-13', $broker_id, $broker_name, '/my_growing_credit/', $params);
      }
    }
    echo '操作成功';
  }
}

/* End of file report.php */
/* Location: ./application/controllers/report.php */
