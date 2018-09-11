<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS系统开站安装布署
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Startup extends MY_Controller
{

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    //加载安装步署模型类
    $this->load->model('startup_model');
    $this->load->helper('user_helper');
  }

  public function init()
  {

  }

  public function run()
  {
    $this->init();
    /**
     * //初始化经纪人用户组表
     * echo '开始初始化用户组表数据<br/>';
     * $this->init_group();
     * echo '结束初始化用户组表数据<br/>';
     * //初始化经纪人用户组表
     * echo '开始初始化用户套餐表数据<br/>';
     * $this->init_package();
     * echo '结束初始化用户套餐表数据<br/>';**/
  }

  //初始化经纪人用户组表
  public function init_group()
  {
    $fields = array(
      array('name' => '试用', 'status' => 1),
      array('name' => '试用已认证', 'status' => 1),
      array('name' => '付费', 'status' => 1),
    );
    $this->load->model('group_model');
    $this->group_model->truncate();
    $this->group_model->insert($fields);
  }

  //初始化经纪人用户套餐表
  public function init_package()
  {
    $fields = array(
      array('name' => '经纪人', 'status' => 1),
      array('name' => '店长', 'status' => 1),
      array('name' => '总店长', 'status' => 1),
    );
    $this->load->model('package_model');
    $this->package_model->truncate();
    $this->package_model->insert($fields);
  }

  //初始化默认权限信息
  public function init_permission()
  {

    $permission = array(
      '房源管理' => array(
        'alias' => 'house', 'value' => array(
          '查看房源管理' => array('alias' => 'see_module', 'value' => array(
            'all' => '查看房源管理',)),
          '查看出售房源管理' => array('alias' => 'sell_see_menu', 'value' => array(
            'all' => '查看出售房源管理',)),
          '查看出售房源' => array('alias' => 'sell_see_manage', 'value' => array(
            'person' => '查看本人出售房源', 'agent' => '查看门店出售房源',
            'company' => '查看公司出售房源')),
          '新增出售房源' => array('alias' => 'sell_add', 'value' => array(
            'all' => '新增出售房源')),
          '合作出售房源' => array('alias' => 'sell_share', 'value' => array(
            'person' => '合作本人出售房源', 'agent' => '合作门店出售房源',
            'company' => '合作公司出售房源')),
          '取消出售房源合作' => array('alias' => 'sell_cancel_share', 'value' => array(
            'person' => '取消本人出售房源合作', 'agent' => '取消门店出售房源合作',
            'company' => '取消公司出售房源合作')),
          '出售私盘转公盘' => array('alias' => 'sell_private_to_share', 'value' => array(
            'person' => '出售本人私盘转公盘', 'agent' => '出售门店私盘转公盘',
            'company' => '出售公司私盘转公盘')),
          '出售公盘转私盘' => array('alias' => 'share_to_private', 'value' => array(
            'person' => '出售本人公盘转私盘', 'agent' => '出售门店人公盘转私盘',
            'company' => '出售门店公盘转私盘')),
          '出售锁盘' => array('alias' => 'sell_lock_block', 'value' => array(
            'person' => '出售本人锁盘', 'agent' => '出售门店锁盘',
            'company' => '出售公司锁盘')),
          '出售接盘' => array('alias' => 'sell_unlock_block', 'value' => array(
            'person' => '出售本人接盘', 'agent' => '出售门店接盘',
            'company' => '出售公司接盘')),
          '出售智能匹配' => array('alias' => 'sell_intelligence_match', 'value' => array(
            'person' => '出售本人智能匹配', 'agent' => '出售门店智能匹配',
            'company' => '出售公司智能匹配')),
          '出售写跟进' => array('alias' => 'sell_write_follow', 'value' => array(
            'all' => '出售写跟进')),
          '分配出售任务' => array('alias' => 'sell_assign_task', 'value' => array(
            'agent' => '分配门店出售任务', 'company' => '分配公司出售任务')),
          '出售删除' => array('alias' => 'delete_house', 'value' => array(
            'person' => '出售本人删除', 'agent' => '出售门店删除',
            'company' => '出售公司删除')),
          '分配出售房源' => array('alias' => 'sell_assign_house', 'value' => array(
            'agent' => '分配门店房源', 'company' => '分配公司房源')),
          '出售房源导入' => array('alias' => 'sell_import_house', 'value' => array(
            'all' => '出售房源导入')),
          '出售报表导出' => array('alias' => 'sell_forms_export', 'value' => array(
            'person' => '出售报表本人导出', 'agent' => '出售报表门店导出',
            'company' => '出售报表公司导出')),
          '查看出售保密信息' => array('alias' => 'sell_secret_info', 'value' => array(
            'person' => '查看出售本人保密信息', 'agent' => '查看出售门店保密信息',
            'company' => '查看出售公司保密信息')),
          '查看出售合作查询' => array('alias' => 'sell_contract_see', 'value' => array(
            'person' => '查看本人出售合作查询', 'agent' => '查看门店出售合作查询',
            'company' => '查看公司出售合作查询')),
          '出售钥匙管理' => array('alias' => 'sell_key_manage', 'value' => array(
            'all' => '出售钥匙管理')),
          '出售钥匙查看详情' => array('alias' => 'sell_key_see', 'value' => array(
            'all' => '出售钥匙查看详情')),
          '借出售钥匙' => array('alias' => 'sell_key_borrow', 'value' => array(
            'all' => '借出售钥匙')),
          '还出售房源业主钥匙' => array('alias' => 'sell_key_give_owner', 'value' => array(
            'all' => '还出售房源业主钥匙')),
          '还出售房源钥匙' => array('alias' => 'sell_key_give', 'value' => array(
            'all' => '还出售房源钥匙')),

          '查看出租房源管理' => array('alias' => 'rent_see_menu', 'value' => array(
            'all' => '查看出租房源管理',)),
          '查看出租房源' => array('alias' => 'rent_see_manage', 'value' => array(
            'person' => '查看本人出租房源', 'agent' => '查看门店出租房源',
            'company' => '查看公司出租房源')),
          '新增出租房源' => array('alias' => 'rent_add', 'value' => array(
            'all' => '新增出租房源')),
          '合作出租房源' => array('alias' => 'rent_share', 'value' => array(
            'person' => '合作本人出租房源', 'agent' => '合作门店出租房源',
            'company' => '合作公司出租房源')),
          '取消出租房源合作' => array('alias' => 'rent_cancel_share', 'value' => array(
            'person' => '取消本人出租房源合作', 'agent' => '取消门店出租房源合作',
            'company' => '取消公司出租房源合作')),
          '出租私盘转公盘' => array('alias' => 'rent_private_to_share', 'value' => array(
            'person' => '出租本人私盘转公盘', 'agent' => '出租门店私盘转公盘',
            'company' => '出租公司私盘转公盘')),
          '出租公盘转私盘' => array('alias' => 'rent_share_to_private', 'value' => array(
            'person' => '出租本人公盘转私盘', 'agent' => '出租门店人公盘转私盘',
            'company' => '出租门店公盘转私盘')),
          '出租锁盘' => array('alias' => 'rent_lock_block', 'value' => array(
            'person' => '出租本人锁盘', 'agent' => '出租门店锁盘',
            'company' => '出租公司锁盘')),
          '出租接盘' => array('alias' => 'rent_unlock_block', 'value' => array(
            'person' => '出租本人接盘', 'agent' => '出租门店接盘',
            'company' => '出租公司接盘')),
          '出租智能匹配' => array('alias' => 'rent_intelligence_match', 'value' => array(
            'person' => '出租本人智能匹配', 'agent' => '出租门店智能匹配',
            'company' => '出租公司智能匹配')),
          '出租写跟进' => array('alias' => 'rent_write_follow', 'value' => array(
            'all' => '出租写跟进')),
          '分配出租任务' => array('alias' => 'rent_assign_task', 'value' => array(
            'agent' => '分配门店出租任务', 'company' => '分配公司出租任务')),
          '出租删除' => array('alias' => 'rent_delete_house', 'value' => array(
            'person' => '出租本人删除', 'agent' => '出租门店删除',
            'company' => '出租公司删除')),
          '分配出租房源' => array('alias' => 'rent_assign_house', 'value' => array(
            'agent' => '分配门店房源', 'company' => '分配公司房源')),
          '出租房源导入' => array('alias' => 'import_house', 'value' => array(
            'all' => '出租房源导入')),
          '出租报表导出' => array('alias' => 'rent_forms_export', 'value' => array(
            'person' => '出租报表本人导出', 'agent' => '出租报表门店导出',
            'company' => '出租报表公司导出')),
          '查看出租保密信息' => array('alias' => 'rent_secret_info', 'value' => array(
            'person' => '查看出租本人保密信息', 'agent' => '查看出租门店保密信息',
            'company' => '查看出租公司保密信息')),
          '查看出租合作查询' => array('alias' => 'rent_contract_see', 'value' => array(
            'person' => '查看本人出租合作查询', 'agent' => '查看门店出租合作查询',
            'company' => '查看公司出租合作查询')),
          '出租钥匙管理' => array('alias' => 'rent_key_manage', 'value' => array(
            'all' => '出租钥匙管理')),
          '出租钥匙查看详情' => array('alias' => 'rent_key_see', 'value' => array(
            'all' => '出租钥匙查看详情')),
          '借出租钥匙' => array('alias' => 'rent_key_borrow', 'value' => array(
            'all' => '借出租钥匙')),
          '还出租房源业主钥匙' => array('alias' => 'rent_key_give_owner', 'value' => array(
            'all' => '还出租房源业主钥匙')),
          '还出租房源钥匙' => array('alias' => 'rent_key_give', 'value' => array(
            'all' => '还出租房源钥匙')),
        )
      ),

      '客源管理' => array(
        'alias' => 'buy_customer', 'value' => array(
          '查看客源管理' => array('alias' => 'see_module', 'value' => array(
            'all' => '查看客源管理',)),
          '查看求购客源管理' => array('alias' => 'buy_see_menu', 'value' => array(
            'all' => '查看求购客源管理',)),
          '查看求购客源' => array('alias' => 'buy_see_manage', 'value' => array(
            'person' => '查看本人求购客源', 'agent' => '查看门店求购客源',
            'company' => '查看公司求购客源')),
          '新增求购客源' => array('alias' => 'buy_add', 'value' => array(
            'all' => '新增求购客源')),
          '合作求购客源' => array('alias' => 'buy_share', 'value' => array(
            'person' => '合作本人求购客源', 'agent' => '合作门店求购客源',
            'company' => '合作公司求购客源')),
          '取消求购客源合作' => array('alias' => 'buy_cancel_share', 'value' => array(
            'person' => '取消本人求购客源合作', 'agent' => '取消求购客源合作',
            'company' => '取消公司求购客源合作')),

          '求购私盘转公盘' => array('alias' => 'buy_private_to_share', 'value' => array(
            'person' => '求购本人私盘转公盘', 'agent' => '求购门店私盘转公盘',
            'company' => '求购公司私盘转公盘')),
          '求购公盘转私盘' => array('alias' => 'buy_share_to_private', 'value' => array(
            'person' => '求购本人公盘转私盘', 'agent' => '求购门店人公盘转私盘',
            'company' => '求购门店公盘转私盘')),

          '求购锁盘' => array('alias' => 'buy_lock_block', 'value' => array(
            'person' => '求购本人锁盘', 'agent' => '求购门店锁盘',
            'company' => '求购公司锁盘')),
          '求购接盘' => array('alias' => 'buy_unlock_block', 'value' => array(
            'person' => '求购本人接盘', 'agent' => '求购门店接盘',
            'company' => '求购公司接盘')),
          '求购智能匹配' => array('alias' => 'buy_intelligence_match', 'value' => array(
            'person' => '求购本人智能匹配', 'agent' => '求购门店智能匹配',
            'company' => '求购公司智能匹配')),
          '求购写跟进' => array('alias' => 'buy_write_follow', 'value' => array(
            'all' => '求购写跟进')),
          '分配求购任务' => array('alias' => 'buy_assign_task', 'value' => array(
            'agent' => '分配门店求购任务', 'company' => '分配公司求购任务')),
          '求购删除' => array('alias' => 'buy_delete_house', 'value' => array(
            'person' => '求购本人删除', 'agent' => '求购门店删除',
            'company' => '求购公司删除')),

          '分配求购客源' => array('alias' => 'buy_assign_house', 'value' => array(
            'agent' => '分配门店客源', 'company' => '分配公司客源')),
          '求购客源导入' => array('alias' => 'import_house', 'value' => array(
            'all' => '求购客源导入')),

          '求购报表导出' => array('alias' => 'buy_forms_export', 'value' => array(
            'person' => '求购报表本人导出', 'agent' => '求购报表门店导出',
            'company' => '求购报表公司导出')),
          '查看求购保密信息' => array('alias' => 'buy_secret_info', 'value' => array(
            'person' => '查看求购本人保密信息', 'agent' => '查看求购门店保密信息',
            'company' => '查看求购公司保密信息')),
          '查看求购合作查询' => array('alias' => 'buy_contract_see', 'value' => array(
            'person' => '查看本人求购合作查询', 'agent' => '查看门店求购合作查询',
            'company' => '查看公司求购合作查询')),
          '求购提醒' => array('alias' => 'buy_remind', 'value' => array(
            'all' => '求购提醒')),

          '查看求租客源管理' => array('alias' => 'rent_see_menu', 'value' => array(
            'all' => '查看求租客源管理',)),
          '查看求租客源' => array('alias' => 'rent_see_manage', 'value' => array(
            'person' => '查看本人求租客源', 'agent' => '查看门店求租客源',
            'company' => '查看公司求租客源')),
          '新增求租客源' => array('alias' => 'rent_add', 'value' => array(
            'all' => '新增求租客源')),
          '合作求租客源' => array('alias' => 'rent_share', 'value' => array(
            'person' => '合作本人求租客源', 'agent' => '合作门店求租客源',
            'company' => '合作公司求租客源')),
          '取消求租客源合作' => array('alias' => 'rent_cancel_share', 'value' => array(
            'person' => '取消本人求租客源合作', 'agent' => '取消求租客源合作',
            'company' => '取消公司求租客源合作')),

          '求租私盘转公盘' => array('alias' => 'rent_private_to_share', 'value' => array(
            'person' => '求租本人私盘转公盘', 'agent' => '求租门店私盘转公盘',
            'company' => '求租公司私盘转公盘')),
          '求租公盘转私盘' => array('alias' => 'rent_share_to_private', 'value' => array(
            'person' => '求租本人公盘转私盘', 'agent' => '求租门店人公盘转私盘',
            'company' => '求租门店公盘转私盘')),

          '求租锁盘' => array('alias' => 'rent_lock_block', 'value' => array(
            'person' => '求租本人锁盘', 'agent' => '求租门店锁盘',
            'company' => '求租公司锁盘')),
          '求租接盘' => array('alias' => 'rent_unlock_block', 'value' => array(
            'person' => '求租本人接盘', 'agent' => '求租门店接盘',
            'company' => '求租公司接盘')),
          '求租智能匹配' => array('alias' => 'rent_intelligence_match', 'value' => array(
            'person' => '求租本人智能匹配', 'agent' => '求租门店智能匹配',
            'company' => '求租公司智能匹配')),
          '求租写跟进' => array('alias' => 'rent_write_follow', 'value' => array(
            'all' => '求租写跟进')),
          '分配求租任务' => array('alias' => 'rent_assign_task', 'value' => array(
            'agent' => '分配门店求租任务', 'company' => '分配公司求租任务')),
          '求租删除' => array('alias' => 'rent_delete_house', 'value' => array(
            'person' => '求租本人删除', 'agent' => '求租门店删除',
            'company' => '求租公司删除')),

          '分配求租客源' => array('alias' => 'rent_assign_house', 'value' => array(
            'agent' => '分配门店客源', 'company' => '分配公司客源')),
          '求租客源导入' => array('alias' => 'import_house', 'value' => array(
            'all' => '求租客源导入')),

          '求租报表导出' => array('alias' => 'rent_forms_export', 'value' => array(
            'person' => '求租报表本人导出', 'agent' => '求租报表门店导出',
            'company' => '求租报表公司导出')),
          '查看求租保密信息' => array('alias' => 'rent_secret_info', 'value' => array(
            'person' => '查看求租本人保密信息', 'agent' => '查看求租门店保密信息',
            'company' => '查看求租公司保密信息')),
          '查看求租合作查询' => array('alias' => 'rent_contract_see', 'value' => array(
            'person' => '查看本人求租合作查询', 'agent' => '查看门店求租合作查询',
            'company' => '查看公司求租合作查询')),
          '求租提醒' => array('alias' => 'rent_remind', 'value' => array(
            'all' => '求租提醒')),
        )
      ),
      '公盘公客' => array(
        'alias' => 'pubic_customer', 'value' => array(
          '查看公盘公客' => array('alias' => 'see_module', 'value' => array(
            'all' => '查看公盘公客')),
          '查看公盘' => array(
            'alias' => 'public_see_menu', 'value' => array(
              'all' => '查看公盘')),
          '公盘匹配' => array(
            'alias' => 'public_share_match', 'value' => array(
              'all' => '公盘匹配')),
          '查看公客' => array(
            'alias' => 'customer_see_menu', 'value' => array(
              'all' => '查看公盘')),
          '公客匹配' => array(
            'alias' => 'customer_share_match', 'value' => array(
              'all' => '公客匹配')),
        )
      ),
      '合作申请' => array(
        'alias' => 'cooperate', 'value' => array(
          '查看合作申请' => array('alias' => 'see_module', 'value' => array(
            'all' => '查看合作申请')),
          '查看我收到的合作申请' => array(
            'alias' => 'accept_see_menu', 'value' => array(
              'all' => '查看我收到的合作申请')),
          '查看我收到的全部合作' => array(
            'alias' => 'accept_see_all', 'value' => array(
              'person' => '查看我收到的全部本人合作', 'agent' => '查看我收到的全部门店合作',
              'company' => '查看我收到的全部公司合作')),
          '查看我收到的待处理合作' => array(
            'alias' => 'accept_see_suspend', 'value' => array(
              'person' => '查看我收到的本人待处理合作', 'agent' => '查看我收到的门店待处理合作',
              'company' => '查看我收到的公司待处理合作')),
          '查看我收到的待评价合作' => array(
            'alias' => 'accept_suspend_appraise', 'value' => array(
              'person' => '查看我收到的本人待评价合作', 'agent' => '查看我收到的门店待评价合作',
              'company' => '查看我收到的公司待评价合作')),
          '查看我收到的合作生效' => array(
            'alias' => 'accept_effect', 'value' => array(
              'person' => '查看我收到的本人合作生效', 'agent' => '查看我收到的门店合作生效',
              'company' => '查看我收到的公司合作生效')),
          '查看我收到的交易成功' => array(
            'alias' => 'accept_trade_success', 'value' => array(
              'person' => '查看我收到的本人交易成功', 'agent' => '查看我收到的门店交易成功',
              'company' => '查看我收到的公司交易成功')),
          '查看我收到的详情' => array(
            'alias' => 'accept_trade_success', 'value' => array(
              'person' => '查看我收到的本人详情', 'agent' => '查看我收到的门店详情',
              'company' => '查看我收到的公司详情')),
          '查看我发起的合作申请' => array(
            'alias' => 'send_see_menu', 'value' => array(
              'all' => '查看我发起的合作申请')),
          '查看我发起的全部合作' => array(
            'alias' => 'send_see_all', 'value' => array(
              'person' => '查看我发起的全部本人合作', 'agent' => '查看我发起的全部门店合作',
              'company' => '查看我发起的全部公司合作')),
          '查看我发起的待处理合作' => array(
            'alias' => 'send_see_suspend', 'value' => array(
              'person' => '查看我发起的本人待处理合作', 'agent' => '查看我发起的门店待处理合作',
              'company' => '查看我发起的公司待处理合作')),
          '查看我发起的待评价合作' => array(
            'alias' => 'send_suspend_appraise', 'value' => array(
              'person' => '查看我发起的本人待评价合作', 'agent' => '查看我发起的门店待评价合作',
              'company' => '查看我发起的公司待评价合作')),
          '查看我发起的合作生效' => array(
            'alias' => 'send_effect', 'value' => array(
              'person' => '查看我发起的本人合作生效', 'agent' => '查看我发起的门店合作生效',
              'company' => '查看我发起的公司合作生效')),
          '查看我发起的交易成功' => array(
            'alias' => 'send_trade_success', 'value' => array(
              'person' => '查看我发起的本人交易成功', 'agent' => '查看我发起的门店交易成功',
              'company' => '查看我发起的公司交易成功')),
          '查看我发起的详情' => array(
            'alias' => 'send_trade_success', 'value' => array(
              'person' => '查看我发起的本人详情', 'agent' => '查看我发起的门店详情',
              'company' => '查看我发起的公司详情')),
        )
      ),
      '人力资源管理' => array(
        'alias' => 'human_resource', 'value' => array(
          '查看人力资源管理' => array('alias' => 'see_module', 'value' => array(
            'all' => '查看人力资源管理')),
          '公司信息' => array(
            'alias' => 'company_info_menu', 'value' => array(
              'all' => '公司信息')),
          '查看公司信息' => array(
            'alias' => 'company_see_info', 'value' => array(
              'all' => '查看公司信息')),
          '店面管理' => array(
            'alias' => 'agency_menu', 'value' => array(
              'all' => '店面管理')),
          '查看店面' => array(
            'alias' => 'agency_see', 'value' => array(
              'all' => '查看公司信息')),
          '修改店面' => array(
            'alias' => 'agency_modify', 'value' => array(
              'all' => '修改店面')),
          '删除店面' => array(
            'alias' => 'agency_delete', 'value' => array(
              'all' => '删除店面')),
          '员工管理' => array(
            'alias' => 'staff_menu', 'value' => array(
              'all' => '查看员工管理')),

          '查看员工' => array(
            'alias' => 'staff_see', 'value' => array(
              'agent' => '查看门店员工', 'company' => '查看公司员工')),
          '修改员工' => array(
            'alias' => 'staff_modify', 'value' => array(
              'agent' => '修改门店员工', 'company' => '修改公司公司')),
          '添加员工' => array(
            'alias' => 'staff_add', 'value' => array(
              'agent' => '添加门店员工', 'company' => '添加公司员工')),
          '删除员工' => array(
            'alias' => 'staff_delete', 'value' => array(
              'agent' => '删除门店员工', 'company' => '删除公司员工')),
          '基本工资设置' => array(
            'alias' => 'salary_set_menu', 'value' => array(
              'all' => '查看基本工资设置')),
          '查看基本工资' => array(
            'alias' => 'salary_set_see', 'value' => array(
              'agent' => '查看门店基本工资', 'company' => '查看公司基本工资')),
          '修改基本工资' => array(
            'alias' => 'salary_set_modify', 'value' => array(
              'agent' => '修改门店基本工资', 'company' => '修改公司基本工资')),
          '删除基本工资' => array(
            'alias' => 'salary_set_delete', 'value' => array(
              'agent' => '删除门店基本工资', 'company' => '删除公司基本工资')),

          '考勤统计' => array(
            'alias' => 'attend_count_menu', 'value' => array(
              'all' => '查看考勤统计')),
          '查看考勤统计' => array(
            'alias' => 'attend_count_see', 'value' => array(
              'agent' => '查看门店考勤统计', 'company' => '查看公司考勤统计')),
          '考勤管理' => array(
            'alias' => 'attend_manage_menu', 'value' => array(
              'all' => '查看考勤管理')),
          '查看考勤管理' => array(
            'alias' => 'attend_manage_see', 'value' => array(
              'agent' => '查看门店考勤管理', 'company' => '查看公司考勤管理')),
          '修改考勤管理' => array(
            'alias' => 'attend_manage_modify', 'value' => array(
              'agent' => '修改门店考勤管理', 'company' => '修改公司考勤管理')),

          '外出管理' => array(
            'alias' => 'go_out_menu', 'value' => array(
              'all' => '查看外出管理')),
          '查看外出管理' => array(
            'alias' => 'go_out_see', 'value' => array(
              'agent' => '查看门店外出管理', 'company' => '查看公司外出管理')),
          '添加外出管理' => array(
            'alias' => 'go_out_add', 'value' => array(
              'agent' => '添加门店外出管理', 'company' => '添加公司外出管理')),
          '修改外出管理' => array(
            'alias' => 'go_out_add', 'value' => array(
              'agent' => '修改门店外出管理', 'company' => '修改公司外出管理')),
          '删除外出管理' => array(
            'alias' => 'go_out_add', 'value' => array(
              'agent' => '删除门店外出管理', 'company' => '删除公司外出管理')),

          '通知管理' => array(
            'alias' => 'notice_menu', 'value' => array(
              'all' => '查看通知管理')),
          '查看通知' => array(
            'alias' => 'notice_see', 'value' => array(
              'agent' => '查看门店通知', 'company' => '查看公司通知')),
          '添加通知' => array(
            'alias' => 'notice_add', 'value' => array(
              'agent' => '添加门店通知', 'company' => '添加公司通知')),
          '删除通知' => array(
            'alias' => 'notice_delete', 'value' => array(
              'agent' => '删除门店通知', 'company' => '删除公司通知')),
          '操作日志' => array(
            'alias' => 'operator_log_menu', 'value' => array(
              'all' => '查看操作日志')),
          '查看操作日志' => array(
            'alias' => 'operator_log_see', 'value' => array(
              'agent' => '查看门店操作日志', 'company' => '查看公司操作日志')),
          '员工日志' => array(
            'alias' => 'staff_log_menu', 'value' => array(
              'all' => '查看员工日志')),
          '查看日志' => array(
            'alias' => 'staff_log_see', 'value' => array(
              'agent' => '查看门店员工日志', 'company' => '查看公司员工日志')),
          '批示日志' => array(
            'alias' => 'staff_log_modify', 'value' => array(
              'agent' => '批示门店员工日志', 'company' => '批示公司员工日志')),
          '删除日志' => array(
            'alias' => 'staff_log_delete', 'value' => array(
              'agent' => '删除门店员工日志', 'company' => '删除公司员工日志')),
          '跟进日志' => array(
            'alias' => 'follow_log_menu', 'value' => array(
              'all' => '查看跟进日志')),
          '查看跟进日志' => array(
            'alias' => 'follow_log_see', 'value' => array(
              'agent' => '查看门店跟进日志', 'company' => '查看公司跟进日志')),
        )
      ),

      '统计分析' => array(
        'alias' => 'statist', 'value' => array(
          '查看统计分析' => array('alias' => 'see_module', 'value' => array(
            'all' => '查看统计分析')),
          '录入采集' => array(
            'alias' => 'input', 'value' => array(
              'person' => '录入个人采集房源', 'agent' => '录入门店采集房源',
              'company' => '录入公司采集房源')),
          '举报采集' => array(
            'alias' => 'report', 'value' => array(
              'person' => '举报个人采集房源', 'agent' => '举报门店采集房源',
              'company' => '举报公司采集房源')),
          '查看采集订阅' => array(
            'alias' => 'see_order', 'value' => array(
              'person' => '查看个人订阅', 'agent' => '查看门店订阅',
              'company' => '查看公司订阅')),
          '修改采集订阅' => array(
            'alias' => 'modify_order', 'value' => array(
              'person' => '修改个人订阅', 'agent' => '修改门店订阅',
              'company' => '修改公司订阅')),
          '删除采集订阅' => array(
            'alias' => 'delete_order', 'value' => array(
              'person' => '删除个人订阅', 'agent' => '删除门店订阅',
              'company' => '删除公司订阅')),
        )
      ),


    );
    //模块
    $this->load->model('permission_module_model');
    $this->permission_module_model->truncate();
    //功能
    $this->load->model('permission_func_model');
    $this->permission_func_model->truncate();
    //菜单
    $this->load->model('permission_menu_model');
    $this->permission_menu_model->truncate();
    //插入数据
    foreach ($permission as $k => $v) {
      //清空模块数据
      $module_name = $k;
      $module_alias = $v['alias'];
      $module_value = $v['value'];
      $module_data = array(
        'name' => $module_name, 'alias' => $module_alias
      );
      $module_id = $this->permission_module_model->insert($module_data);
      foreach ($module_value as $k_m => $v_m) {
        $func_name = $k_m;
        $func_alias = $v_m['alias'];
        $func_value = $v_m['value'];
        $func_data = array(
          'module_id' => $module_id, 'name' => $func_name,
          'alias' => $func_alias
        );
        $func_id = $this->permission_func_model->insert($func_data);
        foreach ($func_value as $k_f => $v_f) {
          $menu_k = $module_alias . '_' . $func_alias . '_' . $k_f;
          $menu_data = array(
            'func_id' => $func_id, 'name' => $v_f,
            'alias' => $menu_k,
          );
          $this->permission_menu_model->insert($menu_data);
        }
      }
    }
  }

  //初始化权限角色
  public function init_system_permission_role()
  {
    $this->load->model('permission_system_role_model');
    $this->permission_system_role_model->truncate();
    $fields = array(
      array('name' => '总店长', 'description' => '我是总店长，你们都得听我的',),
      array('name' => '经纪人', 'description' => '我努力，我奋斗，我骄傲。没错我就是经纪人',)
    );
    $this->permission_system_role_model->insert($fields);
  }

  //初始化权重分配
  public function init_weight()
  {
    $this->load->model('appraise_weight_model');

    $this->appraise_weight_model->truncate();
    $fields = array(
      array('name' => '信息真实度', 'alias' => 'infomation',),
      array('name' => '合作满意度', 'alias' => 'attitude',),
      array('name' => '业务专业度', 'alias' => 'business',)
    );
    $this->appraise_weight_model->insert($fields);
  }

  public function init_trust_level()
  {
    $this->load->model('sincere_trust_level_model');
    $this->sincere_trust_level_model->truncate();
    $fields = array(
      array('name' => '☀☀☀☀☀', 'down' => 2501, 'up' => 0),
      array('name' => '☀☀☀☀', 'down' => 1501, 'up' => 2500),
      array('name' => '☀☀☀', 'down' => 1001, 'up' => 1500),
      array('name' => '☀☀', 'down' => 501, 'up' => 1000),
      array('name' => '☀', 'down' => 201, 'up' => 500),
      array('name' => '❤❤❤❤❤', 'down' => 101, 'up' => 200),
      array('name' => '❤❤❤❤', 'down' => 51, 'up' => 100),
      array('name' => '❤❤❤', 'down' => 21, 'up' => 50),
      array('name' => '❤❤', 'down' => 6, 'up' => 20),
      array('name' => '❤', 'down' => 0, 'up' => 5),
    );
    $this->sincere_trust_level_model->insert($fields);
  }

  public function init_credit()
  {
    $this->load->model('credit_way_model');
    $this->credit_way_model->truncate();
    $fields = array(
      array('name' => '签到', 'alias' => 'sign', 'description' => '可多次奖励', 'score' => 10),
      array('name' => '头像认证', 'alias' => 'avatar_cert', 'description' => '一次性奖励', 'score' => 100),
      array('name' => '身份证认证', 'alias' => 'ident_cert', 'description' => '一次性奖励', 'score' => 100),
      array('name' => '名片认证', 'alias' => 'card_cert', 'description' => '一次性奖励', 'score' => 100),
      array('name' => '门店认证（室内图）', 'alias' => 'agency_cert_room', 'description' => '一次性奖励', 'score' => 100),
      array('name' => '门店认证（室外图）', 'alias' => 'agency_cert_outside', 'description' => '一次性奖励', 'score' => 100),
      array('name' => '举报采集房源通过', 'alias' => 'report_collect_house_success', 'description' => '可多次奖励', 'score' => 50),
      array('name' => '举报公盘/公客通过', 'alias' => 'report_share_success', 'description' => '可多次奖励', 'score' => 50),
      array('name' => '合作生效后举报信息虚假通过', 'alias' => 'report_info_false', 'description' => '可多次奖励', 'score' => 50),
      array('name' => '成交后举报不按协议履行合同并通过', 'alias' => 'no_accord_agreement', 'description' => '可多次奖励', 'score' => 50),
      array('name' => '楼盘字典完善', 'alias' => 'perfect_block', 'description' => '可多次奖励', 'score' => 10),
      array('name' => '设置为全网公盘', 'alias' => 'set_network_public', 'description' => '可多次奖励', 'score' => 50),
      array('name' => '合作生效', 'alias' => 'cooperate_success', 'description' => '可多次奖励', 'score' => 50),
      array('name' => '合作评价', 'alias' => 'cooperate_appraise', 'description' => '可多次奖励', 'score' => 50),
      array('name' => '采集房源查看（增值）', 'alias' => 'collect_house_see', 'description' => '可多次奖励', 'score' => -50),
      array('name' => '查看公盘、公客（增值）', 'alias' => 'see_share', 'description' => '可多次奖励', 'score' => -50),
      array('name' => '录入公盘、公客（增值）', 'alias' => 'add_share', 'description' => '可多次奖励', 'score' => -50),
      array('name' => '房客源匹配（增值）', 'alias' => 'customer_house_match', 'description' => '可多次奖励', 'score' => -100),
      array('name' => '房源合作置顶推荐', 'alias' => 'house_share_top', 'description' => '可多次奖励', 'score' => -50),
    );
    $this->credit_way_model->insert($fields);
  }

  public function test()
  {
    $data = array(2 => array(1 => 'sell/lists/'));
    echo serialize($data) . '---';
    $func = array(1 => 1, 2 => 1);
    echo serialize($func);
  }

  public function init_module()
  {
    $this->load->model('permission_module_model');
    $menu = array(
      array('name' => '工作台', 'init_auth' => 0), array('name' => '房源管理', 'init_auth' => 1),
      array('name' => '客源管理', 'init_auth' => 1), array('name' => '公盘公客', 'init_auth' => 1),
      array('name' => '合作申请', 'init_auth' => 1), array('name' => '好房搜搜', 'init_auth' => 0),
      array('name' => '好房发发', 'init_auth' => 0), array('name' => '统计分析', 'init_auth' => 1),
      array('name' => '业务工具', 'init_auth' => 0), array('name' => '个人中心', 'init_auth' => 0),
      array('name' => '人力资源', 'init_auth' => 1), array('name' => '系统管理', 'init_auth' => 1),
    );
    $this->permission_module_model->truncate();
    $this->permission_module_model->insert($menu);
  }
}

/* End of file startup.php */
/* Location: ./application/zsb/controllers/startup.php */
