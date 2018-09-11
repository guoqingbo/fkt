<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 后台功能迭代通知
 *
 * @package    mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author     yuan
 * Date: 16-6-2
 * Time: 下午3:17
 */
class Features_notice extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('features_notice_model');
  }

  /** 功能迭代通知首页 OK*/
  public function index()
  {
    $data = array();

    $data['title'] = "功能迭代管理";
    $data['conf_where'] = 'index';

    $data['status_arr'] = array(
      '1' => '有效',
      '2' => '草稿',
      '3' => '删除',
    );

    $search_status = $this->input->post('status');
    $data['where_cond'] = array();
    if (isset($search_status) && intval($search_status) > 0) {
      $data['where_cond']['status'] = intval($search_status);
    }

    //分页开始
    $data['user_num'] = $this->features_notice_model->get_features_notice_num();
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['user_num'] ? ceil($data['user_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    //获取所有主菜单
    $data['lists'] = $this->features_notice_model->getfeatures_notice($data['where_cond'], $data['like_code'], $data['offset'], $data['pagesize'], 'create_time ');

    $this->load->view('features_notice/index', $data);
  }

  /** 新增 */
  public function add()
  {
    $data = array();
    $data['this_user_name'] = $_SESSION[WEB_AUTH]['truename'];
    $data['title'] = "新增功能";
    $data['conf_where'] = 'index';
    $this->load->view('features_notice/add', $data);
  }

  //添加功能迭代
  public function add_notice()
  {
    $title = $this->input->post('title');
    if (empty($title)) {
      $json_data['status'] = 2;
    } else {
      $add_data['title'] = $title;//标题
      $add_data['author_id'] = $_SESSION[WEB_AUTH]['uid'];
      $add_data['author_name'] = $this->input->post('author_name');//作者
      $add_data['content'] = $this->input->post('content');//内容
      $add_data['key_word'] = $this->input->post('key_word');//关键词
      $notice_upload_file_1 = $this->input->post('notice_upload_file_1');
      $notice_upload_file_2 = $this->input->post('notice_upload_file_2');
      $add_data['create_time'] = time();//创建时间
      $add_data['status'] = 1;//有效状态
      //功能迭代通知，添加数据
      $add_result = $this->features_notice_model->add_features_notice($add_data);
      if (isset($add_result) && $add_result > 0) {
        $json_data['status'] = 1;
        //上传文件表，添加数据
        $add_data_file = array();
        $add_data_file['notice_id'] = $add_result;
        if (!empty($notice_upload_file_1)) {
          $add_data_file['file_num'] = 1;
          $add_data_file['file_url'] = MLS_SOURCE_URL . '/word/admin_file/' . $notice_upload_file_1;
          $this->features_notice_model->add_features_notice_file($add_data_file);
        }
        if (!empty($notice_upload_file_2)) {
          $add_data_file['file_num'] = 2;
          $add_data_file['file_url'] = MLS_SOURCE_URL . '/word/admin_file/' . $notice_upload_file_2;
          $this->features_notice_model->add_features_notice_file($add_data_file);
        }
      } else {
        $json_data['status'] = 0;
      }
    }
    echo json_encode($json_data);
  }

  //添加留言
  public function add_message()
  {
    $message_content = $this->input->post('message_content');
    $notice_id = $this->input->post('notice_id');
    if (empty($message_content)) {
      $json_data['status'] = 2;
    } else {
      $add_data['writer_id'] = $_SESSION[WEB_AUTH]['uid'];//留言者id
      $add_data['writer_name'] = $_SESSION[WEB_AUTH]['username'];//留言者姓名
      $add_data['content'] = $message_content;//内容
      $add_data['create_time'] = time();//创建时间
      $add_data['notice_id'] = intval($notice_id);//所属通知表id
      $add_result = $this->features_notice_model->add_features_message($add_data);
      if (isset($add_result) && $add_result > 0) {
        $json_data['status'] = 1;
      } else {
        $json_data['status'] = 0;
      }
    }
    echo json_encode($json_data);
  }

  public function update_notice()
  {
    $title = $this->input->post('title');
    if (empty($title)) {
      $json_data['status'] = 2;
    } else {
      $notice_id = $this->input->post('notice_id');//作者
      $update_data['title'] = $title;//标题
      $update_data['author_id'] = $_SESSION[WEB_AUTH]['uid'];
      $update_data['author_name'] = $this->input->post('author_name');//作者
      $update_data['content'] = $this->input->post('content');//内容
      $update_data['key_word'] = $this->input->post('key_word');//关键词
      $notice_upload_file_1 = $this->input->post('notice_upload_file_1');
      $notice_upload_file_2 = $this->input->post('notice_upload_file_2');
      $update_data['create_time'] = time();//创建时间
      $status = intval($this->input->post('status'));
      if ($status > 0) {
        $update_data['status'] = $status;
      } else {
        $update_data['status'] = 2;
      }
      $update_result = $this->features_notice_model->modify_features_notice($notice_id, $update_data);
      if (isset($update_result) && 1 == $update_result) {
        $json_data['status'] = 1;
        //上传文件表，更新数据。判断文档是否改变，重新上传
        $file_list = $this->features_notice_model->get_file_by_notice_id($notice_id);
        $file_old_1 = '';
        $file_old_2 = '';
        if (is_full_array($file_list)) {
          foreach ($file_list as $key => $value) {
            if (1 == $value['file_num']) {
              $file_old_1 = $value['file_url'];
            } else if (2 == $value['file_num']) {
              $file_old_2 = $value['file_url'];
            }
          }
        }

        $modify_data_file = array();
        if (!empty($notice_upload_file_1) && $file_old_1 != MLS_SOURCE_URL . '/word/admin_file/' . $notice_upload_file_1) {
          $modify_data_file['file_url'] = MLS_SOURCE_URL . '/word/admin_file/' . $notice_upload_file_1;
          //判断做添加or修改操作
          $where_cond = array(
            'notice_id' => $notice_id,
            'file_num' => 1
          );
          $notice_file = $this->features_notice_model->get_notice_file($where_cond);
          if (is_full_array($notice_file)) {
            $this->features_notice_model->modify_features_notice_file($notice_id, 1, $modify_data_file);
          } else {
            $modify_data_file['notice_id'] = $notice_id;
            $modify_data_file['file_num'] = 1;
            $this->features_notice_model->add_features_notice_file($modify_data_file);
          }
        }
        if (!empty($notice_upload_file_2) && $file_old_2 != MLS_SOURCE_URL . '/word/admin_file/' . $notice_upload_file_2) {
          $modify_data_file['file_url'] = MLS_SOURCE_URL . '/word/admin_file/' . $notice_upload_file_2;
          //判断做添加or修改操作
          $where_cond = array(
            'notice_id' => $notice_id,
            'file_num' => 2
          );
          $notice_file = $this->features_notice_model->get_notice_file($where_cond);
          if (is_full_array($notice_file)) {
            $this->features_notice_model->modify_features_notice_file($notice_id, 2, $modify_data_file);
          } else {
            $modify_data_file['notice_id'] = $notice_id;
            $modify_data_file['file_num'] = 2;
            $this->features_notice_model->add_features_notice_file($modify_data_file);
          }
        }
      } else {
        $json_data['status'] = 0;
      }
    }
    echo json_encode($json_data);
  }

  /** 修改 */
  public function modify($id = 0)
  {
    $data = array();
    $motice_details = $this->features_notice_model->get_details_by_id($id);
    $data['motice_details'] = array();
    if (is_full_array($motice_details)) {
      $data['motice_details'] = $motice_details[0];
    }

    //文档
    $file_list = $this->features_notice_model->get_file_by_notice_id($id);
    $data['file_list'] = $file_list;
    $file_old_1_url = '';
    $file_old_2_url = '';
    if (is_full_array($file_list)) {
      foreach ($file_list as $key => $value) {
        if (1 == $value['file_num']) {
          $file_old_1_url = $value['file_url'];
        } else if (2 == $value['file_num']) {
          $file_old_2_url = $value['file_url'];
        }
      }
    }

    $file_old_1 = '';
    if (!empty($file_old_1_url)) {
      $file_old_1 = substr(strrchr($file_old_1_url, '/'), 1);
    }
    $file_old_2 = '';
    if (!empty($file_old_2_url)) {
      $file_old_2 = substr(strrchr($file_old_2_url, '/'), 1);
    }
    $data['file_old_1'] = $file_old_1;
    $data['file_old_2'] = $file_old_2;

    $data['title'] = "修改功能";
    $data['conf_where'] = 'index';
    $this->load->view('features_notice/modify', $data);
  }

  /** 查看详情 */
  public function details($id = 0)
  {
    $data = array();
    //通知详情
    $motice_details = $this->features_notice_model->get_details_by_id($id);
    $data['motice_details'] = array();
    if (is_full_array($motice_details)) {
      $data['motice_details'] = $motice_details[0];
    }
    //留言
    $leave_message_list = $this->features_notice_model->get_message_by_notice_id($id);
    $data['leave_message_list'] = $leave_message_list;
    //文档
    $file_list = $this->features_notice_model->get_file_by_notice_id($id);
    $data['file_list'] = $file_list;

    $data['title'] = "查看详情";
    $data['conf_where'] = 'index';
    $this->load->view('features_notice/details', $data);
  }

  //改变状态
  public function change_status($status = 0)
  {
    $notice_id = $this->input->post('notice_id');
    if (!empty($status) && !empty($notice_id)) {
      $update_data['status'] = $status;
      $update_result = $this->features_notice_model->modify_features_notice($notice_id, $update_data);
      if (isset($update_result) && 1 == $update_result) {
        $json_data['status'] = 1;
      } else {
        $json_data['status'] = 0;
      }
    } else {
      $json_data['status'] = 0;
    }

    echo json_encode($json_data);
  }

  /*
   * 上传图片
   */
  public function upload_photo()
  {
    $filename = $this->input->post('action');
    $this->load->model('pic_model');
    $this->pic_model->set_filename($filename);
    $fileurl = $this->pic_model->common_upload();
    $fileurl = changepic($fileurl);
    echo "<script>window.parent.changePic('" . $fileurl . "')</script>";
  }

  /*
   * 上传文件
   */
  public function upload_file()
  {
    $file_type = $this->input->post('file_type');
    $file_str = '';
    if (1 == $file_type) {
      $file_str = 'fujian_file_1';
    } else if (2 == $file_type) {
      $file_str = 'fujian_file_2';
    }
    //设置文件保存目录 注意包含/
    $uploaddir = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR . 'word' . DIRECTORY_SEPARATOR . 'admin_file' . DIRECTORY_SEPARATOR;
    $filename = explode(".", $_FILES[$file_str]['name']);
    do {
      $filename[0] = substr(md5(rand(1, 10000) . time()), 0, 8); //设置随机数长度
      $name = implode(".", $filename);
      $uploadfile = $uploaddir . $name;
    } while (file_exists($uploadfile));

    if (move_uploaded_file($_FILES[$file_str]['tmp_name'], $uploadfile)) {
      if (1 == $file_type) {
        echo "<script>window.parent.set_upload_file_1('" . $name . "')</script>";
      } else if (2 == $file_type) {
        echo "<script>window.parent.set_upload_file_2('" . $name . "')</script>";
      }
      echo "<script>alert('上传成功');</script>";
    } else {
      echo "<script>alert('上传失败');</script>";
    }

  }

}
