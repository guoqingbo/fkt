<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Demo Class
 *
 * 功能管理
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Demo extends MY_Controller
{
  /**
   * 解析函数
   *
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function help()
  {
    $description = "<font style='font-family:Microsoft YaHei'>/****************************<br>
         *1、用户登录成功进入模块功能权限判断 如：房源列表是否有查看公司房源 通过调用get_permission_by 
    	        如需测试不同的功能，请进入控制permission进行修改；<br>
         *2、如有总公司操作的权限，则获取所有分门店get_agencys_by_company_id；<br>
         *3、如有门店权限获取所有get_brokers_agency_id；<br>
         *4、查看各自模块别名和功能别名详见mls_admin目录下startup控制器init_permission方法；<br>
         *5、祝各位朋友调用旅途顺利，如有疑问或有不足，望不吝赐教，在此感谢各位的配合；<br>
         ***************************/</font>";
    echo $description;
  }

  /**
   * 根据公司编号+角色编号+模块别名+功能别名获取相应权限
   * @param int $company_id 公司编号
   * @param int $role_id 角色编号
   * @param string $module_alias 模块别名
   * @param string $func_alias 功能别名
   * @return array {'name' : '查看公司采集房源', 'alias' => 'collect_see_company'}
   */
  public function get_permission_by()
  {
    $company_id = $this->user_arr['company_id']; //公司编号
    $role_id = $this->user_arr['role_id']; //经纪人角色
    $module_alias = 'house'; //房源管理 -- 模块
    $func_alias = 'see_module'; //查看房源管理' -- 功能
    $this->load->model('api_broker_model');
    $permission = $this->api_broker_model->get_permission_by($company_id, $role_id,
      $module_alias, $func_alias);
    print_r($permission);
  }

  /**
   * 获取经纪人的基本信息
   * @param int $broker_id 经纪人编号
   * @return array {'broker_id' : '', ......}
   */
  public function get_baseinfo_by_broker_id()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_model');
    $broker = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);
    print_r($broker);
  }

  /**
   * 总公司编号获取所有分公司
   * @param int $company_id 公司编号
   * @return array [{'agency_id' : '门店编号', 'agency_name' : '门店名称'}，
   *  {'agency_id' : '门店编号', 'agency_name' : '门店名称'}]
   */
  public function get_agencys_by_company_id()
  {
    $this->load->model('api_broker_model');
    $company_id = $this->user_arr['company_id'];
    $agencys = $this->api_broker_model->get_agencys_by_company_id($company_id);
    print_r($agencys);
  }

  /**
   * 根据公司编号获取经纪人列表数组
   * @param int $agency_id 公司编号
   * @return array [{'broker_id' : '经纪人编号', 'truename' : '经纪人姓名'}，
   *  {{'broker_id' : '经纪人编号', 'truename' : '经纪人姓名'}}]
   */
  public function get_brokers_agency_id()
  {
    $agency_id = $this->user_arr['agency_id'];
    $this->load->model('api_broker_model');
    $brokers = $this->api_broker_model->get_brokers_agency_id($agency_id);
    print_r($brokers);
  }

  /**
   * 获取经纪人积分
   * @param int $broker_id
   * @return int 经纪人积分值
   */
  public function get_credit_by_broker_id()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_credit_model');
    $credit = $this->api_broker_credit_model->get_credit_by_broker_id($broker_id);
    echo $credit;
  }

  /**
   * 给经纪人增加积分
   * @param int $broker_id 经纪人编号
   * @param string $alias 操作对象的别名
   * @param string $description 描述 可使用系统描述
   * @param int $score 分值 可使用系统分值
   * @param number $ishidden 是否隐藏 1：显示， 2：隐藏
   * @return ['status' ： '状态'， ‘score’ ： 分值， ‘msg’ : '错误信息']
   * 状态 0 代表 '失败'， 'msg' ： 'broker_id和alias字段不能为空'
   *     0 代表 '失败'， 'msg' : '数据库执行失败，增加积分失败'
   *     0 代表 '失败'， 'msg' : '此功能已认证过' //备注 针对于一次性认证 如：头像认证
   *     0 代表 '失败'， 'msg' : '此功能今天已经奖励过' //备注 每天一次性奖励 如：签到
   *     0 代表 '失败'， 'msg' : '此功能超过每天上限值' //备注 每天多次性奖励，每天有上限积分 如：设置为全网公盘
   *     1 代表 '成功'， 'score' : '返回成功增加的积分值'
   */
  public function increase_broker_credit()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_credit_model');
    $increase = $this->api_broker_credit_model->increase($broker_id, 'set_share_house_customer'/*签到别名，详见credit_way对应表*/);
    print_r($increase);
  }

  /**
   * 给经纪人扣除积分
   * @param int $broker_id 经纪人编号
   * @param string $alias 操作对象的别名
   * @param string $description 描述 可使用系统描述
   * @param int $score 分值 可使用系统分值
   * @param number $ishidden 是否隐藏 1：显示， 2：隐藏
   * @return ['status' ： '状态'， ‘score’ ： 分值， ‘msg’ : '错误信息']
   * 状态 0 代表 '失败'， 'msg' ： 'broker_id和alias字段不能为空'
   *     0 代表 '失败'， 'msg' : '数据库执行失败，增加积分失败'
   *     0 代表 '失败'， 'msg' : '积分不足，扣除积分失败'
   *     1 代表 '成功'， 'score' : '返回成功扣除的积分值'
   */
  public function reduce_broker_credit()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_credit_model');
    $reduce = $this->api_broker_credit_model->reduce($broker_id, 'collect_house_see'/*采集房源查看（增值）别名，详见credit_way对应表*/);
    print_r($reduce);
  }

  /**
   * 增加、扣除信用
   * @param int $broker_id 经纪人编号
   * @param string $type_alias 类型别名
   * @param string $func_alias 功能别名
   * @param int $score 分值，默认为0
   * @return boolean 返回添加或者更新的分值
   */
  function update_trust()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_sincere_model');
    $func_alias = 'no_accord_agreement_signature'; //合作生效后不按协议履行合同，被举报成功
    $result = $this->api_broker_sincere_model->update_trust($broker_id, $func_alias);
    // true成功 or false失败
  }

  /**
   * 获取某个经纪人的信用值和等级
   * @param int $broker_id
   * @return {'trust' : '', 'level' : '' }
   */
  function get_trust_level_by_broker_id()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_sincere_model');
    $trust_level = $this->api_broker_sincere_model->get_trust_level_by_broker_id($broker_id);
    print_r($trust_level);
  }

  /**
   * 获取某个经纪人信用分
   * @param int $broker_id
   * @return int 信用分数
   */
  public function get_trust_by_broker_id()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_sincere_model');
    echo $this->api_broker_sincere_model->get_trust_by_broker_id($broker_id);
  }

  /**
   * 获取某个分值信用值所对应的等级 - 转换成静态数据的方法
   * @param int $trust 信用分
   */
  public function get_level_by_trust()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_sincere_model');
    echo $this->api_broker_sincere_model->get_level_by_trust($broker_id);
  }

  //写评价
  function appraise()
  {
    $broker_id = $this->user_arr['broker_id'];
    $house_info = array(
      'district' => '鼓楼区',   //区属
      'street' => '三牌楼',    //板块
      'block_name' => '天福园', //小区名称
      'room' => 3, //室
      'hall' => 2,//厅
      'toilet' => 1, //卫
      'fitment' => '精装', //装修
      'forward' => '南', //朝向
      'buildarea' => 102, //面积
      'price' => '100万', //价格
    );
    $info = array(
      'broker_id' => $broker_id, /**经纪人编号-评价人*/
      'trade_type' => 1,/*交易的状态*/
      'transaction_id' => '201411231212341234', /**交易编号*/
      'house_info' => $house_info, /**房源详情*/
      'trust_type' => 'good', /*整体评价* good=好评 medium=中评 bad=差评*/
      'infomation' => 3, /*信息真实度 1-5分之间*/
      'attitude' => 5, /*合作满意度 1-5分之间*/
      'business' => 5, /*业务专业度 1-5分之间*/
      'content' => '这个房源是假的，你怎能这样了。', /*评价内容*/
      'partner_id' => $broker_id /*合作方的经纪人编号-被评价人*/
    );
    $this->load->model('sincere_appraise_cooperate_model');
    $result = $this->sincere_appraise_cooperate_model->appraise($info);
    // true成功 or false失败
  }

  /**
   * 信用评价 好评，中评，差评
   * @param int $broker 经纪人编号
   * @param string $type_name 整体评价类型 good medium bad
   * @return array {'good' : '好评', 'medium' : '', 'bad' : '',
   * 'total' : '总数', 'good_rate' : '好评率'}
   */
  public function get_trust_appraise_count()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_sincere_model');
    $result = $this->api_broker_sincere_model->get_trust_appraise_count($broker_id);
    print_r($result);
  }

  public function good_avg_rate()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_sincere_model');
    $result = $this->api_broker_sincere_model->good_avg_rate($broker_id);
    print_r($result);
  }

  /**
   * 获取经纪人动态评分基本统计信息
   * @param int $broker 经纪人编号
   * @return array
   */
  public function get_appraise_and_avg()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_sincere_model');
    $result = $this->api_broker_sincere_model->get_appraise_and_avg($broker_id);
    print_r($result);
  }

  /**
   * 经纪人动态评分详细信息
   * @param int $broker 经纪人编号
   * @return array
   */
  public function get_appraise_info()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_sincere_model');
    $result = $this->api_broker_sincere_model->get_appraise_info($broker_id);
    print_r($result);
  }

  /**
   * 更新信息真实度
   * @param int $broker_id 经纪人编号
   * @param string $alias 操作类型别名
   */
  public function update_infomation()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_sincere_model');
    $alias = 'house_info_false'; //房源信息虚假
    $result = $this->api_broker_sincere_model->update_infomation($broker_id, $alias);
    print_r($result);
  }

  /**
   * 更新合作满意度
   * @param int $broker_id 经纪人编号
   * @param string $alias 操作类型别名
   * @return int 插入编号
   */
  public function update_attitude()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_sincere_model');
    $alias = 'whether_accept_cooperate'; //是否及时接受合作申请
    $result = $this->api_broker_sincere_model->update_attitude($broker_id, $alias);
    print_r($result);
  }

  /**
   * 更新合作满意度
   * @param int $broker_id 经纪人编号
   * @param string $alias 操作类型别名
   * @return int 插入编号
   */
  public function update_businsess()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_sincere_model');
    $alias = 'broker_appriase'; //经纪人评价
    $result = $this->api_broker_sincere_model->update_attitude($broker_id, $alias, 5);
    print_r($result);
  }

  /**
   * 处罚记录
   * @param int $broker_id 经纪人编号
   * @param int $type 类型
   * @param int $score 分值
   * @param string $number 房源编号
   * @param array $house_info 房源详细信息
   */
  public function punish()
  {
    $broker_id = $this->user_arr['broker_id'];
    $this->load->model('api_broker_sincere_model');
    $type_name = 'mali_appraise'; //恶意评价
    $this->load->model('sincere_trust_config_model');
    $trust_config = $this->sincere_trust_config_model->get_config();
    $score = $trust_config['operator_trust_action'][$type_name]['score'];//分值
    $number = 'CS2321673512';//对象编号
    $house_info = array(
      'districtname' => '鼓楼区',   //区属
      'streetname' => '三牌楼',    //板块
      'blockname' => '天福园', //小区名称
      'room' => 3, //室
      'hall' => 2,//厅
      'toilet' => 1, //卫
      'fitment' => 2, //装修
      'forward' => 2, //朝向
      'buildarea' => 102, //面积
      'price' => '100', //价格
      'tbl' => 'sell'
    );
    $result = $this->api_broker_sincere_model->punish($broker_id, $type_name, $score,
      $number, $house_info);
    print_r($result);
  }

  /**
   * 合作申诉
   * @param int $broker_id 经纪人编号
   * @param int $appraise_id 合作编号
   * @param string $photo_url 图片名称
   * @param string $photo_name 图片名称
   * @param string $reason 理由
   */
  public function appraise_appeal()
  {
    $broker_id = $this->user_arr['broker_id'];
    $appraise_id = 1;
    $photo_url = MLS_FILE_SERVER_URL . '/njhouse/2015/01/26/thumb/142225723854c5ec567ea5b.jpg';
    $photo_name = 'Water lilies.jpg';
    $reason = '我是冤枉的啊我是冤枉的啊';
    $this->load->model('api_broker_sincere_model');
    $result = $this->api_broker_sincere_model->appraise_appeal(
      $broker_id, $appraise_id, $photo_url, $photo_name, $reason);
  }
}
