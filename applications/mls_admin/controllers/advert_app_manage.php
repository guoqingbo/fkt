<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 广告管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Advert_app_manage extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('common_load_source_helper');
    $this->load->model('advert_app_manage_model');
    // $this->load->model('newhouse_request_model');
    //加载海外地产模型
    // $this->load->model('abroad_model');
    //加载海外地产模型
    //$this->load->model('tourism_model');
  }

  //广告增加
  public function index()
  {
    //$project = json_decode($this->newhouse_request_model->project(), true);
    $ad_type = array(
      1 => '采集中心首页', 2 => '热销楼盘首页', 3 => '资讯中心',
      4 => '合作中心首页', 5 => '指定热销楼盘', 6 => '自定义'
    );
    //暂定显示最大的数量的广告数为3条
    $max_ad_num = 6;
    $data_view = array();
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
//                . 'common/third/swf/swfupload.js,mls/js/v1.0/uploadpic.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');

    $advert = $this->advert_app_manage_model->get_all_by();
    for ($i = 0; $i < $max_ad_num; $i++) {
      if (isset($advert[$i]) && $advert[$i] != '') {
        continue;
      } else {
        $advert[$i] = array();
      }
    }
    $data_view['title'] = 'APP广告管理';
    $data_view['conf_where'] = 'index';
    $data_view['type'] = $ad_type;
    $data_view['advert'] = $advert;
    //$data_view['project'] = $project['data'];
    $this->load->view('advert_app_manage/index', $data_view);
  }

  public function upload_photo()
  {
    $action = $this->input->post('action');
    $fileurl = $this->input->post('fileurl_' . $action);
//        $this->load->model('pic_model');
//        $this->pic_model->set_filename('photofile_add');
//        $this->pic_model->set_image_width(720);
//        $this->pic_model->set_image_height(230);
//        $this->pic_model->set_resize_width(255);
//        $this->pic_model->set_resize_height(132);
//        $fileurl = $this->pic_model->common_upload();
    echo "<script>window.parent.changePhoto('" . $fileurl . "'," . $action . ")</script>";
  }

  public function news()
  {
    $this->advert_app_manage_model->set_tbl(2);
    $news = $this->advert_app_manage_model->get_one_by();
    $data_view['news'] = $news;
    $data_view['title'] = '返回广告管理';
    $data_view['conf_where'] = 'news';
    $data_view['js'] = load_js('common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    $this->load->view('advert_app_manage/news', $data_view);
  }

  public function push()
  {
    $ad_type = array(
      1 => '合作中心', 2 => '热销楼盘', 3 => '积分商城',
      4 => '采集中心', 5 => '金融', 6 => '客户预约',
      7 => '客户需求', 8 => '旅游地产', 9 => '海外地产'
    );
    //新房
    // $project_newhouse = json_decode($this->newhouse_request_model->project(), true);
    //$data_view['project_newhouse'] = $project_newhouse['data'];
    //海外
    //$abroad = $this->abroad_model->get_list_by_cond('status = 1',-1);
    //$data_view['abroad'] = $abroad;
    //旅游
    //$tourism = $this->tourism_model->get_list_by_cond('status = 1',-1);
    // $data_view['tourism'] = $tourism;
    $this->advert_app_manage_model->set_tbl(3);
    $news = $this->advert_app_manage_model->get_one_by();
    $data_view['news'] = $news;
    $data_view['type'] = $ad_type;
    $data_view['title'] = '返回广告管理';
    $data_view['conf_where'] = 'push';
    $data_view['js'] = load_js('common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    $this->load->view('advert_app_manage/push', $data_view);
  }

  public function save_ad()
  {
    $type = $this->input->post('type');
    $newhouse = $this->input->post('newhouse');
    $pic = $this->input->post('pic');
    $ad_id = $this->input->post('ad_id');
    $url = $this->input->post('url');
    $title = $this->input->post('title');
    $extra = array();
    if ($type == 5 && $newhouse) {
      // $project = json_decode($this->newhouse_request_model->project(), true);
//            foreach($project['data'] as $v)
//            {
//                if ($v['newhouse_id'] == $newhouse)
//                {
//                    $newhouse_name = $v['lp_name'];
//                    $extra['newhouse_id'] = $newhouse;
//                    $extra['newhouse_name'] = $newhouse_name;
//                    break;
//                }
//            }
    } else if ($type == 6 && $url && $title) {
      $extra['url'] = $url;
      $extra['title'] = $title;
    }
    $ad = $this->advert_app_manage_model->get_one_by($ad_id);
    if (is_full_array($ad)) {
      $update_data = array(
        'type' => $type, 'pic' => $pic,
        'extra' => serialize($extra), 'update_time' => time()
      );
      $this->advert_app_manage_model->update_by_id($update_data, $ad_id);
      echo 1;
    } else {
      $insert_data = array(
        'id' => $ad_id, 'type' => $type, 'pic' => $pic,
        'extra' => serialize($extra), 'update_time' => time()
      );
      $this->advert_app_manage_model->insert($insert_data);
      echo 1;
    }
  }

  public function save_news()
  {
    $content = $this->input->post('content');
    $is_push = $this->input->post('is_push');
    $title = $this->input->post('title');
    $type = $this->input->post('type');
    $push_name = $this->input->post('push_name');
    $this->advert_app_manage_model->set_tbl(2);
    $news = $this->advert_app_manage_model->get_one_by();
    if (is_full_array($news)) {
      $update_data = array(
        'title' => $title, 'is_push' => $is_push,
        'new_content' => $content, 'update_time' => time(),
        'push_name' => $push_name
      );
      if ($type == 2) {
        $update_data['content'] = $content;
      }
      $this->advert_app_manage_model->update_by_id($update_data);
      echo 1;
    } else {
      $insert_data = array(
        'title' => $title, 'is_push' => $is_push,
        'new_content' => $content, 'update_time' => time()
      );
      if ($type == 2) {
        $insert_data['content'] = $content;
      }
      $this->advert_app_manage_model->insert($insert_data);
      echo 1;
    }
    if ($is_push == 1 && trim($push_name) != '') {
      $this->load->model('push_func_model');
      $this->load->model('broker_online_app_model');
      $brokers = $this->broker_online_app_model->get_all_by_city($_SESSION[WEB_AUTH]["city"]);
      if (is_full_array($brokers)) {
        foreach ($brokers as $v) {
          //发送推送消息
          $this->push_func_model->send(1, 6, 1, 0, $v['broker_id'], array(), array(), $push_name);
        }
      }
    }
  }

  public function save_push()
  {
    $title = $this->input->post('title');
    $href_infofrom = $this->input->post('href_infofrom');
    $element_id = $this->input->post('element_id');
    $extra = array();
    if ($href_infofrom == 1) {
      $type = $this->input->post('type');
      $extra['type'] = $type;
      if ($type == 2) //热销楼盘
      {
        //$project = json_decode($this->newhouse_request_model->project(), true);
//                foreach($project['data'] as $v)
//                {
//                    if ($v['newhouse_id'] == $element_id)
//                    {
//                        $newhouse_name = $v['lp_name'];
//                        $extra['element_id'] = $element_id;
//                        $extra['element_name'] = $newhouse_name;
//                        break;
//                    }
//                }
      } else if ($type == 8) {
        //海外
        //$abroad = $this->abroad_model->get_list_by_cond('status = 1',-1);
//                foreach($abroad as $v)
//                {
//                    if ($v['id'] == $element_id)
//                    {
//                        $extra['element_id'] = $element_id;
//                        $extra['element_name'] = $v['block_name'];
//                        break;
//                    }
//                }
      } else if ($type == 9) {
        //旅游
//                $tourism = $this->tourism_model->get_list_by_cond('status = 1',-1);
//                foreach($tourism as $v)
//                {
//                    if ($v['id'] == $element_id)
//                    {
//                        $extra['element_id'] = $element_id;
//                        $extra['element_name'] = $v['block_name'];
//                        break;
//                    }
//                }
      }
    } else {
      $extra['url'] = $this->input->post('url');
    }
    $this->advert_app_manage_model->set_tbl(3);
    $news = $this->advert_app_manage_model->get_one_by();
    if (is_full_array($news)) {
      $update_data = array(
        'title' => $title, 'href_infofrom' => $href_infofrom,
        'extra' => serialize($extra),
        'update_time' => time()
      );
      $this->advert_app_manage_model->update_by_id($update_data);
      echo 1;
    } else {
      $insert_data = array(
        'title' => $title, 'href_infofrom' => $href_infofrom,
        'extra' => serialize($extra),
        'update_time' => time()
      );
      $this->advert_app_manage_model->insert($insert_data);
      echo 1;
    }
  }

  public function push_action()
  {
    $this->advert_app_manage_model->set_tbl(3);
    $news = $this->advert_app_manage_model->get_one_by();
    $this->load->model('push_func_model');
    $this->load->model('broker_online_app_model');
    $brokers = $this->broker_online_app_model->get_all_by_city($_SESSION[WEB_AUTH]["city"]);
    if (is_full_array($brokers)) {
      $extra = unserialize($news['extra']);
      $field = array();
      $field['title'] = $news['title'];
      $field['infofrom'] = $news['href_infofrom'];
      if ($news['href_infofrom'] == 1)  //内部跳
      {
        $field['type'] = $extra['type'];
        if (isset($extra['element_id'])) {
          $field['element_id'] = $extra['element_id'];
        }
        if (isset($extra['element_name'])) {
          $field['element_name'] = $extra['element_name'];
        }
      } else {
        $field['url'] = $extra['url'];
      }
      foreach ($brokers as $v) {
        //发送推送消息
        $this->push_func_model->send(1, 14, 1, 0, $v['broker_id'], $field, array(), $news['title']);
      }
    }
    echo 1;
  }

  public function preview()
  {
    $this->advert_app_manage_model->set_tbl(2);
    $news = $this->advert_app_manage_model->get_one_by();
    $data_view['news'] = $news;
    $data_view['title'] = '返回广告管理';
    $data_view['conf_where'] = 'news';
    $this->load->view('advert_app_manage/preview', $data_view);
  }

  public function upload_new_photo()
  {
    $filename = $this->input->post('action');
    $this->load->model('pic_model');
    $this->pic_model->set_filename($filename);
    $fileurl = $this->pic_model->common_upload();
//        $fileurl = str_replace("/thumb","",$fileurl);
    $fileurl = changepic($fileurl);
    echo "<script>window.parent.changePic('" . $fileurl . "')</script>";
  }

  public function del_photo($id = '')
  {
    $result = '';
    if (!empty($id)) {
      $update_data = array(
        'pic' => ''
      );
      $update_result = $this->advert_app_manage_model->update_by_id($update_data, intval($id));
      if (1 == $update_result) {
        $result = 'success';
      } else {
        $result = 'failed';
      }
    } else {
      $result = 'failed';
    }
    echo $result;
    exit;
  }
}

/* End of file advert_manage.php */
/* Location: ./application/mls_admin/controllers/advert_manage.php */
