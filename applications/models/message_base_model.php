<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Buy_match_model CLASS
 *
 * 消息模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          angel_in_us
 */
class Message_base_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();

    $this->message = 'message';
    $this->message_broker = 'message_broker';
    $this->message_open_pop = 'message_open_pop';
    $this->message_pop = 'message_pop';
  }


  /**
   * 操作触发消息方法
   * @params   int $type 消息类型(消息来源);
   * @params   int $broker_id  经纪人id;
   * @params   string $broker_name  经纪人姓名;
   * @params   string $fromer  分配任务的人;
   * @params   int $deal_id 合同编号/房源编号/客源编号;
   * @params   array $n_params 备用，新增参数；$n_params['reason']=>举报原因;$n_params['prize']=>奖励积分
   * @return  void
   * @date     2015-01-19
   * @author   angel_in_us
   */
  public function pub_message($type = '', $broker_id = '', $broker_name = '', $fromer = '', $deal_id = '', $url = '', $n_params = array())
  {
    $send_time = time();
    $phone = '4000231231';

    switch ($type) {
      //分配任务后被分配经纪人收到消息提示 (******房源管理里面的分配任务******)
      case "1a":
        /*
				标题：您有一条分配任务，请尽快处理。
				内容：亲爱的王晓二，张总分配给您一条跟进任务，房源编号为CS234234。点击查看。
				点击“点击查看”，跳转到我的跟进任务列表页。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您有一条分配任务，请尽快处理。',
          'message' => '亲爱的' . $broker_name . '，' . $fromer . '分配给您一条跟进任务，房源编号为' . $deal_id . '。',
          'url' => $url,
          'from' => 1,
          'type' => '1',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;
      //分配任务后被分配经纪人收到消息提示 (******客源管理里面的分配任务******)
      case "1b":
        /*
				标题：您有一条分配任务，请尽快处理。
				内容：亲爱的王晓二，张总分配给您一条跟进任务，客源编号为CS234234。点击查看。
				点击“点击查看”，跳转到我的跟进任务列表页。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您有一条分配任务，请尽快处理。',
          'message' => '亲爱的' . $broker_name . '，' . $fromer . '分配给您一条跟进任务，客源编号为' . $deal_id . '。',
          'url' => $url,
          'from' => 1,
          'type' => '1',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //当离任务最迟完成时间15小时时，系统消息提示
      case "2":
        /*
				标题：您有一条分配任务即将过期，请尽快处理。
				内容：亲爱的王晓二，张总分配给您一条跟进任务，房源编号为CS234234，即将过期，请尽快处理。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您有一条分配任务即将过期，请尽快处理。',
          'message' => '亲爱的' . $broker_name . '，' . $fromer . '分配给您一条跟进任务，房源编号为' . $deal_id . '，即将过期，请尽快处理。',
          'url' => $url,
          'from' => 1,
          'type' => '1',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //撤销任务后被分配经纪人收到消息提示（房源任务被撤销）
      case "3a1":
        /*
				标题：您有一条分配任务已被撤销。
				内容：亲爱的王晓二，您的房源编号为CS234234的跟进任务已被张总撤销。点击查看。
				点击“点击查看”，跳转到我的跟进任务列表页。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您有一条分配任务已被撤销。',
          'message' => '亲爱的' . $broker_name . '，您的房源编号为' . $deal_id . '的跟进任务已被' . $fromer . '撤销。',
          'url' => $url,
          'from' => 1,
          'type' => '1',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //撤销任务后被分配经纪人收到消息提示（客源任务被撤销）
      case "3a2":
        /*
				标题：您有一条分配任务已被撤销。
				内容：亲爱的王晓二，您的客源编号为CS234234的跟进任务已被张总撤销。点击查看。
				点击“点击查看”，跳转到我的跟进任务列表页。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您有一条分配任务已被撤销。',
          'message' => '亲爱的' . $broker_name . '，您的客源编号为' . $deal_id . '的跟进任务已被' . $fromer . '撤销。',
          'url' => $url,
          'from' => 1,
          'type' => '1',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //接受佣金分配后甲方收到系统消息
      case "4a":
        /*
				标题：合同编号1231232的合作申请已生效！
				内容：亲爱的李大爷，合同编号1231232的合作申请已被王小二接受，合作生效。自合作生效起三个月内的成交将由平台担保，请尽快联系对方看房交易，祝您顺利开单！查看详情。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作申请已生效！',
          'message' => '亲爱的' . $broker_name . '，' . $n_params['block_name'] . '的合作申请已被' . $fromer . '接受，合作生效，请尽快联系对方看房交易，祝您顺利开单！',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //接受佣金分配后乙方收到系统消息
      case "4b":
        /*
				(TO乙方)标题：合同编号1231232的合作申请已生效！
				内容：亲爱的李大爷，合同编号1231232的合作申请已接受，合作生效。请尽快联系对方看房交易，祝您顺利开单！查看详情。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作申请已生效！',
          'message' => '亲爱的' . $broker_name . '，' . $n_params['block_name'] . '的合作申请已接受，合作生效，请尽快联系对方看房交易，祝您顺利开单！',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //拒绝佣金分配后甲方（被申请方）收到系统消息
      case "5":
        /*
				标题：合同编号1231232的合作失败！
				内容：亲爱的李大爷，很遗憾，合同编号1231232的合作申请佣金分配已被王小二拒绝，本交易合作失败。合作中心的合作机会还很多，祝您顺利开单！查看详情。
				点击“查看详情”跳转SRS-06-01我收到的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作失败！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，合同编号' . $deal_id . '的合作申请佣金分配已被' . $fromer . '拒绝，本交易合作失败。合作中心的合作机会还很多，祝您顺利开单！',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //确认成交后双方收到系统消息 =>>>因为双方收到同样的提示信息，所以 都为 case 6
      case "6":   //1-5
        /*
				标题：合同编号1231232的合作交易成功！
				内容：亲爱的王小二，合同编号1231232的合作交易成功。请尽快至线下交易中心分佣！平台做担保，佣金一分不会少。查看详情。
				点击“查看详情”跳转SRS-06-01我收到的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作交易成功！请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，' . $n_params['block_name'] . '的合作交易成功。请您尽快对您的合作方进行评价。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //成交逾期失败后双方收到系统消息 =>>>因为双方收到同样的提示信息，所以 都为 case 7
      case "7":  //1-8
        /*
				标题：合同编号1231232的合作交易逾期失败！
				内容：亲爱的王小二，合同编号1231232的合作交易已满三个月，成交逾期失败。合作中心的合作机会还很多，祝您顺利开单！查看详情。
				点击“查看详情”跳转SRS-06-01我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作交易逾期失败！请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作交易已满三个月，成交逾期失败。合作中心的合作机会还很多，祝您顺利开单！请您尽快对您的合作方进行评价。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //合作冻结后乙方（申请方）收到系统消息
      case "8":  //1-9
        /*
				标题：合同编号1231232的合作被冻结！
				内容：亲爱的王小二，合同编号1231232的合作交易被冻结。
				点击“查看详情”跳转SRS-06-01我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作被冻结！',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作交易被冻结。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //与别人成交后乙方（申请方）收到系统消息（如果甲方与乙1和乙2均合作，乙1成交，那么乙2收到消息，所以如果与乙1、乙2都成交的话，请调用两次）
      case "9":
        /*
				标题：合同编号1231232的合作终止！
				内容：亲爱的王小二，合同编号1231232的合作交易，由于房源已成交，合作终止。查看详情。
				点击“查看详情”跳转SRS-06-01我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作终止！如合作已生效，请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作交易，由于房源已成交，合作终止。合作中心的合作机会还很多，祝您顺利开单！如合作已生效，请您尽快对您的合作方进行评价。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //如果该房源线下渠道已成交或者失效/删除/下架，所有该房源的乙方合作人收到系统消息（如果甲方与乙1和乙2均合作，与乙3成交，那么乙1和乙2收到消息）  ===>>>>>>>成交
      case "10a":
        /*
				标题：合同编号1231232的合作终止！
				内容：亲爱的王小二，合同编号1231232的合作交易，由于房源已成交/失效/删除/下架，合作终止。查看详情。
				点击“查看详情”跳转SRS-06-01我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作终止！如合作已生效，请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作交易，由于房源已成交，合作终止。合作中心的合作机会还很多，祝您顺利开单！如合作已生效，请您尽快对您的合作方进行评价。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //如果该房源线下渠道已成交或者失效/删除/下架，所有该房源的乙方合作人收到系统消息（如果甲方与乙1和乙2均合作，与乙3成交，那么乙1和乙2收到消息）  ===>>>>>>>失效
      case "10b":
        /*
				标题：合同编号1231232的合作终止！
				内容：亲爱的王小二，合同编号1231232的合作交易，由于房源已失效，合作终止。查看详情。
				点击“查看详情”跳转SRS-06-01我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作终止！如合作已生效，请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作交易，由于房源已失效，合作终止。合作中心的合作机会还很多，祝您顺利开单！如合作已生效，请您尽快对您的合作方进行评价。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //如果该房源线下渠道已成交或者失效/删除/下架，所有该房源的乙方合作人收到系统消息（如果甲方与乙1和乙2均合作，与乙3成交，那么乙1和乙2收到消息）  ===>>>>>>>删除
      case "10c":
        /*
				标题：合同编号1231232的合作终止！
				内容：亲爱的王小二，合同编号1231232的合作交易，由于房源已删除，合作终止。查看详情。
				点击“查看详情”跳转SRS-06-01我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作终止！如合作已生效，请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作交易，由于房源已删除，合作终止。合作中心的合作机会还很多，祝您顺利开单！如合作已生效，请您尽快对您的合作方进行评价。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //如果该房源线下渠道已成交或者失效/删除/下架，所有该房源的乙方合作人收到系统消息（如果甲方与乙1和乙2均合作，与乙3成交，那么乙1和乙2收到消息）  ===>>>>>>>下架
      case "10d":
        /*
				标题：合同编号1231232的合作终止！
				内容：亲爱的王小二，合同编号1231232的合作交易，由于房源已下架，合作终止。查看详情。
				点击“查看详情”跳转SRS-06-01我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作终止！如合作已生效，请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作交易，由于房源已下架，合作终止。合作中心的合作机会还很多，祝您顺利开单！如合作已生效，请您尽快对您的合作方进行评价。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //如果该房源线下渠道已成交或者失效/删除/下架/预定，所有该房源的乙方合作人收到系统消息（如果甲方与乙1和乙2均合作，与乙3成交，那么乙1和乙2收到消息）  ===>>>>>>>下架
      case "10e":
        /*
				标题：合同编号1231232的合作终止！
				内容：亲爱的王小二，合同编号1231232的合作交易，由于房源已下架，合作终止。查看详情。
				点击“查看详情”跳转SRS-06-01我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作终止！如合作已生效，请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作交易，由于房源已预定，合作终止。合作中心的合作机会还很多，祝您顺利开单！如合作已生效，请您尽快对您的合作方进行评价。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //如果该房源线下渠道已成交或者失效/删除/下架，所有该房源的乙方合作人收到系统消息（如果甲方与乙1和乙2均合作，与乙3成交，那么乙1和乙2收到消息）  ===>>>>>>>下架
      case "10f":
        /*
				标题：合同编号1231232的合作终止！
				内容：亲爱的王小二，合同编号1231232的合作交易，由于房源已下架，合作终止。查看详情。
				点击“查看详情”跳转SRS-06-01我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作终止！如合作已生效，请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作交易，由于房源取消合作，合作终止。合作中心的合作机会还很多，祝您顺利开单！如合作已生效，请您尽快对您的合作方进行评价。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //接受合作申请后乙方（申请方）收到系统消息
      case "11":
        /*
				标题：合同编号1231232的合作申请已接受！
				内容：亲爱的王小二，合同编号1231232的合作申请已被李大爷接受，请尽快联系对方确认佣金分配，祝您顺利开单！查看详情。
				点击“查看详情”跳转SRS-06-02我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作申请已接受！',
          'message' => '亲爱的' . $broker_name . '，' . $n_params['block_name'] . '的合作申请已被' . $fromer . '接受，请尽快联系对方确认佣金分配，祝您顺利开单！',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //拒绝合作申请后乙方（申请方）收到系统消息
      case "12":
        /*
				标题：合同编号1231232的合作申请已被拒绝！
				内容：亲爱的王小二，很遗憾，合同编号1231232的合作申请已被李大爷拒绝。合作中心的合作机会还很多，祝您顺利开单！查看详情。
				点击“查看详情”跳转SRS-06-02我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作申请已被拒绝！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，' . $n_params['block_name'] . '的合作申请已被' . $fromer . '拒绝。合作中心的合作机会还很多，祝您顺利开单！',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //提交佣金分配后乙方（申请方）收到系统消息
      case "13":
        /*
				标题：合同编号1231232的合作申请佣金分配已提交，请尽快确认！
				内容：亲爱的王小二，合同编号1231232的合作申请佣金分配已被李大爷提交，请尽快确认，祝您顺利开单！查看详情。
				点击“查看详情”跳转SRS-06-02我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作申请佣金分配已提交，请尽快确认！',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作申请佣金分配已被' . $fromer . '提交，请尽快确认，祝您顺利开单！',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //确认成交后乙方（申请方）收到系统消息
      case "14":
        /*
				标题：合同编号1231232的合作交易成功！
				内容：亲爱的王小二，合同编号1231232的合作交易成功。请尽快至线下交易中心分佣！平台做担保，佣金一分不会少。查看详情。
				点击“查看详情”跳转SRS-06-02我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作交易成功！',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作交易成功。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //确认成交失败后乙方（申请方）收到系统消息
      case "15":
        /*
				标题：合同编号1231232的合作交易失败！
				内容：亲爱的王小二，合同编号1231232的合作交易失败。合作中心的合作机会还很多，祝您顺利开单！查看详情。
				点击“查看详情”跳转SRS-06-02我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作交易失败！',
          'message' => '亲爱的' . $broker_name . '，' . $n_params['block_name'] . '的合作交易失败。合作中心的合作机会还很多，祝您顺利开单！',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //取消合作后乙方（申请方）收到系统消息
      case "16":
        /*
				标题：合同编号1231232的合作交易已取消！
				内容：亲爱的王小二，合同编号1231232的合作交易已被李大爷取消。合作中心的合作机会还很多，祝您顺利开单！查看详情。
				点击“查看详情”跳转SRS-06-02我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作交易已取消！',
          'message' => '亲爱的' . $broker_name . '，' . $n_params['block_name'] . '的合作交易已被' . $fromer . '取消。取消原因：' . $n_params['reason'] . '。合作中心的合作机会还很多，祝您顺利开单！',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //确认成交失败后甲方（被申请方）收到系统消息
      case "17":
        /*
				标题：合同编号1231232的合作交易逾期失败！
				内容：亲爱的王小二，合同编号1231232的合作交易已满三个月，成交逾期失败。合作中心的合作机会还很多，祝您顺利开单！
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作交易逾期失败！请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作交易已满三个月，成交逾期失败。合作中心的合作机会还很多，祝您顺利开单！请您尽快对您的合作方进行评价。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //合作冻结后甲方（被申请方）收到系统消息
      case "18":
        /*
				标题：合同编号1231232的合作被冻结！
				内容：亲爱的王小二，合同编号1231232的合作交易被冻结，查看详情。
				点击“查看详情”跳转SRS-06-02我收到的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作被冻结！',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作交易被冻结。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //是否接受合作：当48小时未处理时，系统消息提示
      case "19":
        /*
				标题：您有1条合作申请待处理。
				内容：“亲爱的王小二，您有1条合作申请待处理，请尽快在24小时内处理完毕，否则会降低您一定的合作满意度分值哦！”
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您有1条合作申请待处理。',
          'message' => '亲爱的' . $broker_name . '，您有1条合作申请待处理，请尽快在24小时内处理完毕，否则会扣除您一定的合作满意度分值哦！',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //是否接受合作：当72小时未处理时，系统消息提示
      case "20a":
        /*
				【TO 被申请方】标题：您有1条合作申请未及时处理。
				内容：“亲爱的王小二，合同编号为1231232的合作申请在72小时内未及时处理，合作失败。扣除您一定的合作满意度分值，查看详情”点击查看详情，跳转SRS-07-06我的处罚记录
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您有1条合作申请未及时处理。',
          'message' => '亲爱的' . $broker_name . '，合同编号为' . $deal_id . '的合作申请在72小时内未及时处理，合作失败。扣除您一定的合作满意度分值。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //是否接受合作：当72小时未处理时，系统消息提示
      case "20b":
        /*
				【TO 申请方】标题：合同编号1231232的合作已失败！
			     内容：亲爱的王小二，合同编号1231232的合作由于对方未在72小时内及时处理，合作失败。合作中心的合作机会还很多，祝您顺利开单！查看详情。  点击查看详情，跳转SRS-06-02我发起的合作申请。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作已失败！',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作由于对方未在72小时内及时处理，合作失败。合作中心的合作机会还很多，祝您顺利开单！',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //被评价人收到系统消息提示
      case "21":
        /*
				标题：您收到来自合作经纪人的评价，已生效。
				内容：亲爱的王小二，合同编号1232312的合作方已对您做出了评价，已生效。查看详情。
				点击“查看详情”跳转SRS-07-04我的评价。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您收到来自合作经纪人的评价，已生效。',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作方已对您做出了评价，已生效。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //合作评价生效
      case "22":
        /*
				标题：当经纪人被评价完毕后，信用分即时增减，同时系统消息提示
				内容：亲爱的王小二，合同编号12312312312的合作，对方经纪人已对您做出了评价，评价已生效，详情请点击查看”
				点击“点击查看”，跳转SRS-07-04我的评价列表页。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您收到来自合作经纪人的评价，已生效。',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作，对方经纪人已对您做出了评价，评价已生效，',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //23a 当经纪人单方面取消合作后，信用分即时扣减。
      //23b 同时系统消息提示取消操作的经纪人
      case "23a":
        /*
				标题：合同编号12312312312的合作已取消
				内容：“亲爱的王小二，合同编号12312312312的合作您已经取消。由于合作已生效，您单方面取消合作扣除您信用分1分。查看详情”点击后，跳转SRS-08-02我的信用分记录。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作已取消。',
          'message' => '亲爱的' . $broker_name . '，' . $n_params['block_name'] . '的合作您已经取消。取消原因：' . $n_params['reason'] . '。由于合作已生效，您单方面取消合作扣除您信用分1分。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //23a 当经纪人单方面取消合作后，信用分即时扣减。
      //23b 同时系统消息提示取消操作的经纪人
      case "23b":
        /*
				标题：合同编号12312312312的合作已取消
				内容：“亲爱的王小三，合同编号12312312312的合作已被对方取消，查看详情”点击后，跳转SRS-06-01我收到的合作申请（如该经纪人为被申请方）或者SRS-06-02我发起的的合作申请（如该经纪人为申请方）。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作已取消。',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作已被对方取消。取消原因：' . $n_params['reason'] . '。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //后台房源下架
      case "24":
        /*
				标题：您的房源编号为CS123的房源已下架
				内容：“亲爱的王小二，由于XXXXXXXXXXXXXXXX（填写的反馈内容），您的房源编号为CS123的房源已下架，如有疑问，请联系'.$phone.'”
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您的房源已下架。',
          'message' => '亲爱的' . $broker_name . '，由于' . $fromer . '，您的房源编号为' . $deal_id . '的房源已下架，如有疑问，请联系' . $phone,//此处的 $fromer 为 填写的反馈内容
          'url' => $url,
          'from' => 1,
          'type' => '1',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;


      //后台客源下架
      case "25":
        /*
				标题：您的客源编号为CS123的客源已下架
				内容：“亲爱的王小二，由于XXXXXXXXXXXXXXXX（填写的反馈内容），您的客源编号为CS123的客源已下架，如有疑问，请联系'.$phone.'”
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您的客源已下架。',
          'message' => '亲爱的' . $broker_name . '，由于' . $fromer . '，您的客源编号为' . $deal_id . '的客源已下架，如有疑问，请联系' . $phone,//此处的 $fromer 为 填写的反馈内容
          'url' => $url,
          'from' => 1,
          'type' => '1',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //如运营修改合作状态，系统消息提示
      case "26":
        /*
				标题：合同编号12321321已修改为交易成功状态！
				内容：亲爱的王小二，合同编号为12321321的合作已被系统管理员修改为交易成功状态。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作已修改为交易成功状态！',
          'message' => '亲爱的' . $broker_name . '，合同编号为' . $deal_id . '的合作已被系统管理员修改为交易成功状态。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //如价格真实奖励，系统消息提示
      case "27":
        /*
				标题：成交价格真实奖励已到账！
				内容：亲爱的王小二，合同编号为12321321的合作房源已成交，您录入的成交价已证实真实，奖励您100积分已到账，点击查看。
				点击“点击查看”，跳转SRS-10-08-04中我的积分记录。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '成交价格真实奖励已到账！',
          'message' => '亲爱的' . $broker_name . '，合同编号为' . $deal_id . '的合作房源已成交，您录入的成交价已证实真实，奖励您' . $n_params['prize'] . '积分已到账。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //失效后系统消息通知双方经纪人
      case "28": //这个 case 要调用两次，因为双方经纪人都要通知，请注意
        /*
				标题：合同编号为12321312的合作评价已失效。
				内容：亲爱的王小二，合同编号为12321312的合作评价已被系统管理员改为失效状态，不计入您的信用评价中。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作评价已失效。',
          'message' => '亲爱的' . $broker_name . '，合同编号为' . $deal_id . '的合作评价已被系统管理员改为失效状态，不计入您的信用评价中。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（已成交）
      case "29a1":
        /*
				TO举报人：
				【成功】标题：合作房源CS00012举报成功！
				内容：亲爱的王小二，您的举报经审核已通过。为感谢您的参与，奖励您50积分，已到账，请查收。让我们一起努力，共同打造诚信共享平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作房源' . $deal_id . '举报成功！',
          'message' => '亲爱的' . $broker_name . '，您的举报经审核已通过。为感谢您的参与，奖励您' . $n_params['prize'] . '积分，已到账，请查收。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（已成交）
      case "29a2":
        /*
				TO举报人：
			    【失败】标题：合作房源CS00012举报失败！
				内容：亲爱的王小二，很遗憾，您的举报经审核未通过。如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作房源' . $deal_id . '举报失败！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，您的举报经审核未通过。如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（已成交）
      case "29a3":
        /*
				TO举报人：
			    【成功】标题：合作客源CS00012举报成功！
				内容：亲爱的王小二，您的举报经审核已通过。为感谢您的参与，奖励您50积分，已到账，请查收。让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作客源' . $deal_id . '举报成功！',
          'message' => '亲爱的' . $broker_name . '，您的举报经审核已通过。为感谢您的参与，奖励您' . $n_params['prize'] . '积分，已到账，请查收。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（已成交）
      case "29a4":
        /*
				TO举报人：
			    【失败】标题：合作客源CS00012举报失败！
				内容：亲爱的王小二，很遗憾，您的举报经审核未通过。如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => $deal_id . '合作单号客源虚假举报失败！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，您的举报经审核未通过。如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（已成交）
      case "29b1":
        /*
				TO被举报人
				【成功】标题：合作房源CS00012涉嫌虚假已被举报下架！
				内容：亲爱的王小二，您的房源CS00012由于涉嫌虚假，举报经审核通过已下架。同时扣除您动态评分中信息真实度系统评分0.1分，如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信共享平台。查看详情。
				点击查看详情，跳转SRS-07-06我的处罚记录。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作房源' . $deal_id . '已被举报下架！',
          'message' => '亲爱的' . $broker_name . '，您的房源' . $deal_id . '由于涉嫌虚假，未及时更新状态，被举报经审核通过已下架。如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（已成交）
      case "29b2":
        /*
				TO被举报人
				【成功】标题：合作客源CS00012涉嫌虚假已被举报下架！
				内容：亲爱的王小二，您的客源CS00012由于涉嫌虚假，举报经审核通过已下架。同时扣除您动态评分中信息可信度系统评分0.1分，如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信合作平台。查看详情。
				点击查看详情，跳转SRS-07-06我的处罚记录。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作客源' . $deal_id . '已被举报下架！',
          'message' => '亲爱的' . $broker_name . '，您的客源' . $deal_id . '由于实际为已成交状态，未及时更新状态，被举报经审核通过已下架。如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（房源虚假、客源虚假）
      case "30a1":
        /*
				TO举报人：
				【成功】标题：合作房源CS00012举报成功！
				内容：亲爱的王小二，您的举报经审核已通过。为感谢您的参与，奖励您100积分，已到账，请查收。让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作房源' . $deal_id . '举报成功！',
          'message' => '亲爱的' . $broker_name . '，您的举报经审核已通过。为感谢您的参与，奖励您' . $n_params['prize'] . '积分，已到账，请查收。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（房源虚假、客源虚假）
      case "30a2":
        /*
				TO举报人：
				【失败】标题：合作房源CS00012举报失败！
				内容：亲爱的王小二，很遗憾，您的举报经审核未通过。如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作房源' . $deal_id . '举报失败！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，您的举报经审核未通过。如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（房源虚假、客源虚假）
      case "30a3":
        /*
				TO举报人：
				【成功】标题：合作客源CS00012举报成功！
				内容：亲爱的王小二，您的举报经审核已通过。为感谢您的参与，奖励您100积分，已到账，请查收。让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作客源' . $deal_id . '举报成功！',
          'message' => '亲爱的' . $broker_name . '，您的举报经审核已通过。为感谢您的参与，奖励您' . $n_params['prize'] . '积分，已到账，请查收。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（房源虚假、客源虚假）
      case "30a4":
        /*
				TO举报人：
				【失败】标题：合作客源CS00012举报失败！
				内容：亲爱的王小二，很遗憾，您的举报经审核未通过。如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作客源' . $deal_id . '举报失败！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，您的举报经审核未通过。如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（房源虚假、客源虚假）
      case "30b1":
        /*
				TO被举报人
				【成功】标题：合作房源CS00012涉嫌虚假已被举报下架！
				内容：亲爱的王小二，您的房源CS00012由于涉嫌虚假，举报经审核通过已下架。同时扣除您动态评分中信息可信度系统评分0.1分，如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信合作平台。查看详情。
				点击查看详情，跳转SRS-07-06我的处罚记录。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作房源涉嫌虚假已被举报下架！',
          'message' => '亲爱的' . $broker_name . '，您的房源' . $deal_id . '由于涉嫌虚假，举报经审核通过已下架。同时扣除您动态评分中信息真实度系统评分0.1分，如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（房源虚假、客源虚假）
      case "30b2":
        /*
				TO被举报人
				【成功】标题：合作客源CS00012涉嫌虚假已被举报下架！
				内容：亲爱的王小二，您的客源CS00012由于涉嫌虚假，举报经审核通过已下架。同时扣除您动态评分中信息可信度系统评分0.1分，如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信合作平台。查看详情。
				点击查看详情，跳转SRS-07-06我的处罚记录。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作客源涉嫌虚假已被举报下架！',
          'message' => '亲爱的' . $broker_name . '，您的客源' . $deal_id . '由于涉嫌虚假，举报经审核通过已下架。同时扣除您动态评分中信息真实度系统评分0.1分，如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //不按协议履行合同工审核通过、不通过均走系统消息提示
      case "31a1":
        /*
				TO举报人：
				【成功】标题：不按协议履行合同举报成功！
				内容：亲爱的王小二，您的举报经审核已通过。为感谢您的参与，奖励您100积分，已到账，请查收。同时我们已对被举报经纪人扣除相应的信用分作为惩罚，让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '不按协议履行合同举报成功！',
          'message' => '亲爱的' . $broker_name . '，您对编号为' . $deal_id . '的合同的举报经审核已通过。为感谢您的参与，奖励您' . $n_params['prize'] . '积分，已到账，请查收。同时我们已对被举报经纪人扣除相应的信用分作为惩罚，让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //不按协议履行合同工审核通过、不通过均走系统消息提示
      case "31a2":
        /*
				TO举报人：
				【失败】标题：不按协议履行合同举报失败！
				内容：亲爱的王小二，很遗憾，您的举报经审核未通过。如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '不按协议履行合同举报失败！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，您对编号为' . $deal_id . '的合同的举报经审核未通过。如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //不按协议履行合同工审核通过、不通过均走系统消息提示
      case "31b1":
        /*
				TO被举报人
				【成功】标题：由于您不按协议履行合同，经核实确认处罚生效！
				内容：亲爱的王小二，您的合作20143213123由于未按协议履行合同，举报经审核已通过。扣除您信用分1分，查看详情。如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信合作平台。
				点击“查看详情”后，跳转SRS-08-02我的信用分记录。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '由于您不按协议履行合同，经核实确认处罚生效！',
          'message' => '亲爱的' . $broker_name . '，您的合同编号为' . $deal_id . '的合同由于未按协议履行，举报经审核已通过。扣除您信用分1分。如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示
      case "32a1":
        /*
				TO举报人：
				【成功】标题：不按协议履行合同举报成功！
				内容：亲爱的王小二，您的举报经审核已通过。为感谢您的参与，奖励您100积分，已到账，请查收。同时我们已对被举报经纪人扣除相应的信用分作为惩罚，让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '不按协议履行合同举报成功！',
          'message' => '亲爱的' . $broker_name . '，您的举报经审核已通过。为感谢您的参与，奖励您' . $n_params['prize'] . '积分，已到账，请查收。同时我们已对被举报经纪人扣除相应的信用分作为惩罚，让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示
      case "32a2":
        /*
				TO举报人：
				【失败】标题：不按协议履行合同举报失败！
				内容：亲爱的王小二，很遗憾，您的举报经审核未通过。如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '不按协议履行合同举报失败！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，您的举报经审核未通过。如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示
      case "32a3":
        /*
				TO举报人：
				【失败】标题：不按协议履行合同举报失败！
				内容：亲爱的王小二，很遗憾，您的举报经审核未通过。如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合同举报失败！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，您对合同编号为：' . $deal_id . '的举报经审核未通过。原因为：' . $n_params['reason'] . '如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示
      case "32b1":
        /*
				TO被举报人
				【成功】标题：由于您不按协议履行合同，经核实确认处罚生效！
				内容：亲爱的王小二，您的合作20143213123由于未按协议履行合同，举报经审核已通过。扣除您信用分1分，查看详情。如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信合作平台。
				点击“查看详情”后，跳转SRS-08-02我的信用分记录。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '由于您不按协议履行合同，经核实确认处罚生效！',
          'message' => '亲爱的' . $broker_name . '，您的合作' . $deal_id . '由于未按协议履行合同，举报经审核已通过。扣除您信用分1分。如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //评价成功后，被评价人收到系统消息提示
      case "33":
        /*
			 标题：您收到来自合作经纪人的评价，已生效。
             内容：亲爱的王小二，合同编号1232312的合作方已对您做出了评价，已生效。查看详情。
             点击“查看详情”跳转SRS-07-04我的评价。
		     */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您收到来自合作经纪人的评价，已生效。',
          'message' => '亲爱的' . $broker_name . '，' . $n_params['block_name'] . '的合作方已对您做出了评价，已生效。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //申诉成功后通知评价人
      case "33a":
        /*
			 标题：涉嫌恶意评价，扣减信用分1分。
             内容：亲爱的王小二，合同编号为12321312的合作评价被判定为恶意评价，扣减您信用分1分。查看详情。
             点击“查看详情”跳转SRS-07-04我的评价。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '涉嫌恶意评价，扣减信用分1分。',
          'message' => '亲爱的' . $broker_name . '，合同编号为' . $deal_id . '的合作评价被判定为恶意评价，扣减您信用分1分。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //申诉成功后通知申诉人
      case "33b":
        /*
    		 标题：您的评价申诉已经审核通过。
              内容：亲爱的王小二，合同编号为12321312的合作评价申诉已被审核通过。查看详情。
              点击“查看详情”跳转SRS-07-04我的评价。
	        */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您的评价申诉已经审核通过。',
          'message' => '亲爱的' . $broker_name . '，合同编号为' . $deal_id . '的合作评价申诉已被审核通过。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //申诉失败通知申诉人
      case "33c":
        /*
    		标题：您的评价申诉审核未通过。
            内容：亲爱的王小二，合同编号为12321312的合作评价申诉审核未通过。查看详情。
            点击“查看详情”跳转SRS-07-04我的评价。
	        */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您的评价申诉审核未通过。',
          'message' => '亲爱的' . $broker_name . '，合同编号为' . $deal_id . '的合作评价申诉审核未通过。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //管理员改成失效后 通知被评价人
      case "33d":
        /*
            标题：合同编号为12321312的合作评价已失效。
            内容：亲爱的王小二，合同编号为12321312的合作评价已被系统管理员改为失效状态，不计入您的信用评价中。查看详情。
            */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作评价已失效。',
          'message' => '亲爱的' . $broker_name . '，合同编号为' . $deal_id . '的合作评价已被系统管理员改为失效状态，不计入您的信用评价中。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //申诉成功后被申诉人扣减1分信用分，同时系统消息提示
      case "34":
        /*
				标题：涉嫌恶意评价，扣减信用分1分。
				内容：亲爱的王小二，合同编号为12321312的合作评价被判定为恶意评价，扣减您信用分1分。查看详情。
				点击“查看详情”跳转SRS-07-04我的评价。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '涉嫌恶意评价，扣减信用分1分。',
          'message' => '亲爱的' . $broker_name . '，合同编号为' . $deal_id . '的合作评价被判定为恶意评价，扣减您信用分1分。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      case "35":
        /*
			     标题：身份认证失败。
			     内容：亲爱的王小二，您的最新身份认证失败,请重新认证。
			     */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '身份认证失败。',
          'message' => '亲爱的' . $broker_name . ',您的最新身份认证失败,请重新认证。',
          'url' => $url,
          'from' => 1,
          'type' => '1',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      case "35a":
        /*
			     标题：身份资质认证失败。
			     内容：亲爱的王小二，您的最新身份认证失败,请重新认证。
			     */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '身份资质认证失败。',
          'message' => '亲爱的' . $broker_name . ',您的最新身份资质认证失败,请重新认证。',
          'url' => $url,
          'from' => 1,
          'type' => '1',
          'createtime' => $send_time
        );

        //$this->db_city->insert($this->message, $data);//插入数据
        //$insert_id = $this->db_city->insert_id();
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        //$this->db_city->insert($this->message_broker, $info);//插入数据
        //$result = $this->db_city->insert_id();
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      case "36":
        /*
			         标题：资质认证失败。
			         内容：亲爱的王小二，您的最新资质认证失败,请重新认证。
			         */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '资质认证失败。',
          'message' => '亲爱的' . $broker_name . ',您的最新资质认证失败,请重新认证。',
          'url' => $url,
          'from' => 1,
          'type' => '1',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //发起合作后甲方收到系统消息
      case "37":
        /*
			         标题：您收到一条合作申请！
			         内容：亲爱的李大爷，您收到来自王小二的合作申请，合同编号1231232。请在三天内完成申请处理，祝您顺利开单！查看详情。
					 点击“查看详情”跳转SRS-06-01我收到的合作申请。
			         */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您收到一条合作申请！',
          'message' => '亲爱的' . $broker_name . '，您收到来自' . $fromer . '的合作申请，' . $n_params['block_name'] . '。请在三天内完成申请处理，祝您顺利开单！',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //是否接受佣金分配：当72小时未处理时，系统消息提示
      case "38b":
        /*
			         【TO 被申请方】标题：您有1条合作的佣金分配未及时确认。
					  内容：“亲爱的王小二，合同编号为1231232的合作佣金分配在72小时内未及时确认，合作失败。扣除您一定的合作满意度分值，查看详情”点击查看详情，跳转SRS-07-06我的处罚记录
			         */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您有1条合作的佣金分配未及时确认。',
          'message' => '亲爱的' . $broker_name . '，合同编号为' . $deal_id . '的合作佣金分配在72小时内未及时确认，合作失败。扣除您一定的合作满意度分值',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //是否接受佣金分配：当72小时未处理时，系统消息提示
      case "38a":
        /*
			         【TO 申请方】标题：合同编号1231232的合作已失败！
					  内容：亲爱的王小二，合同编号1231232的合作佣金分配由于对方未在72小时内及时处理，合作失败。合作中心的合作机会还很多，祝您顺利开单！查看详情。  点击查看详情，跳转SRS-06-02我发起的合作申请。
			         */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作已失败！',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作佣金分配由于对方未在72小时内及时处理，合作失败。合作中心的合作机会还很多，祝您顺利开单！',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //成交失败后双方收到系统消息  =>>>因为双方收到的消息一样，所以共用一个 case 39，请调用两次。
      case "39":
        /*
			        标题：合同编号1231232的合作成交失败！请尽快评价。
					内容：亲爱的王小二，合同编号1231232的合作成交失败。合作中心的合作机会还很多，祝您顺利开单！请您尽快对您的合作方进行评价。查看详情。
					点击“查看详情”跳转SRS-06-01我发起的合作申请。
			    */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作成交失败！请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，合同编号' . $deal_id . '的合作成交失败。合作中心的合作机会还很多，祝您顺利开单！请您尽快对您的合作方进行评价。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //是否接受佣金分配：当48小时未处理时，系统消息提示
      case "40":
        /*
			        标题：您有1条合作待确认佣金分配。
					内容：“亲爱的王小二，您有1条合作待确认佣金分配，请尽快在24小时内处理完毕，否则会扣除您一定的合作满意度分值哦！”
			    */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您有1条合作待确认佣金分配。',
          'message' => '亲爱的' . $broker_name . '，您有1条合作待确认佣金分配，请尽快在24小时内处理完毕，否则会扣除您一定的合作满意度分值哦！',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //申诉成功后申诉人收到系统消息提示：
      case "41":
        /*
			        标题：您的评价申诉已经审核通过。
					内容：亲爱的王小二，合同编号为12321312的合作评价申诉已被审核通过。查看详情。
					点击“查看详情”跳转SRS-07-04我的评价。
			    */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您的评价申诉已经审核通过。',
          'message' => '亲爱的' . $broker_name . '，合同编号为' . $deal_id . '的合作评价申诉已被审核通过。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //申诉失败后申诉人收到系统消息提示：
      case "42":
        /*
			        标题：您的评价申诉审核未通过。
					内容：亲爱的王小二，合同编号为12321312的合作评价申诉审核未通过。查看详情。
					点击“查看详情”跳转SRS-07-04我的评价。
			    */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '您的评价申诉审核未通过。',
          'message' => '亲爱的' . $broker_name . '，合同编号为' . $deal_id . '的合作评价申诉审核未通过。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（其它）
      case "43a1":
        /*
				TO举报人：
				【成功】标题：合作房源CS00012举报成功！
				内容：亲爱的王小二，您的举报经审核已通过。为感谢您的参与，奖励您100积分，已到账，请查收。让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作房源' . $deal_id . '举报成功！',
          'message' => '亲爱的' . $broker_name . '，您的举报经审核已通过。为感谢您的参与，奖励您' . $n_params['prize'] . '积分，已到账，请查收。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（其它）
      case "43a2":
        /*
				TO举报人：
				【失败】标题：合作房源CS00012举报失败！
				内容：亲爱的王小二，很遗憾，您的举报经审核未通过。如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作房源' . $deal_id . '举报失败！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，您的举报经审核未通过。如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（其它）
      case "43a3":
        /*
				TO举报人：
				【成功】标题：合作客源CS00012举报成功！
				内容：亲爱的王小二，您的举报经审核已通过。为感谢您的参与，奖励您100积分，已到账，请查收。让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作客源' . $deal_id . '举报成功！',
          'message' => '亲爱的' . $broker_name . '，您的举报经审核已通过。为感谢您的参与，奖励您' . $n_params['prize'] . '积分，已到账，请查收。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（其它）
      case "43a4":
        /*
				TO举报人：
				【失败】标题：合作客源CS00012举报失败！
				内容：亲爱的王小二，很遗憾，您的举报经审核未通过。如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信合作平台。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作客源' . $deal_id . '举报失败！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，您的举报经审核未通过。如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信合作平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（其它）  注释：此处的  $fromer 为经纪人填入的 举报原因！！！！！！
      case "43b1":
        /*
				TO被举报人
				【成功】标题：合作房源CS00012涉嫌虚假已被举报下架！
				内容：内容：亲爱的王小二，您的房源CS00012被举报经审核通过已下架，举报原因：当撒打算的撒大。同时扣除您动态评分中信息真实度系统评分0.1分，如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信共享平台。查看详情。
				点击查看详情，跳转SRS-07-06我的处罚记录。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作房源已被举报下架！',
          'message' => '亲爱的' . $broker_name . '，您的房源' . $deal_id . '被举报经审核通过已下架，举报原因：' . $fromer . '。如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信共享平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //审核通过、不通过均走系统消息提示（其它）  注释：此处的  $fromer 为经纪人填入的 举报原因！！！！！！
      case "43b2":
        /*
				TO被举报人
				【成功】标题：合作客源CS00012涉嫌虚假已被举报下架！
				内容：亲爱的王小二，您的客源CS00012被举报经审核通过已下架，举报原因：当撒打算的撒大。同时扣除您动态评分中信息真实度系统评分0.1分，如有疑问，请联系'.$phone.'。让我们一起努力，共同打造诚信共享平台。查看详情。
				点击查看详情，跳转SRS-07-06我的处罚记录。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '合作客源已被举报下架！',
          'message' => '亲爱的' . $broker_name . '，您的客源' . $deal_id . '被举报经审核通过已下架，举报原因：' . $fromer . '。如有疑问，请联系' . $phone . '。让我们一起努力，共同打造诚信共享平台。',
          'url' => $url,
          'from' => 1,
          'type' => '2',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //有新的房源划分到您名下，请知晓
      case "44a1":
        /*
				标题：有新的房源划分到您名下，请知晓。
				内容：亲爱的王晓二，张总分配给您新的房源，房源编号为CS234234。点击查看。
				点击“点击查看”，打开该房源详情页。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '有新的房源划分到您名下，请知晓。',
          'message' => '亲爱的' . $broker_name . '，' . $fromer . '分配给您新的房源，请您注意查看。',
          'url' => $url,
          'from' => 1,
          'type' => '1',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //有新的客源划分到您名下，请知晓。
      case "44a2":
        /*
				标题：有新的客源划分到您名下，请知晓。
				内容：亲爱的王晓二，张总分配给您新的客源，客源编号为QG234234。点击查看。
				点击“点击查看”，打开该客源详情页。
			*/
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '有新的客源划分到您名下，请知晓。',
          'message' => '亲爱的' . $broker_name . '，' . $fromer . '分配给您新的客源，请您注意查看。',
          'url' => $url,
          'from' => 1,
          'type' => '1',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      //新房分销。
      case "45":
        /*
			     标题：客户报备成功。
			     内容：亲爱的王晓二，开发商确认“客户姓名”（客户号码）为有效客户，可以安排带看，（楼盘名）。
			     点击“点击查看”，打开该客源详情页。
		     */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '客户报备成功',
          'message' => '亲爱的' . $broker_name . '，开发商确认“' . $n_params['cm_name'] . '”（' . $n_params['cm_phone'] . '）为有效客户，可以安排带看，（' . $n_params['lp_name'] . '）。',
          'url' => $url,
          'from' => 1,
          'type' => '3',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      case "46":
        /*
    		         标题：客户报备失败。
    		         内容：亲爱的王晓二，姓名（客户号码）为无效客户，报备失败 （楼盘名）。
    		         点击“点击查看”，打开该客源详情页。
            */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '客户报备失败',
          'message' => '亲爱的' . $broker_name . '，“' . $n_params['cm_name'] . '”（' . $n_params['cm_phone'] . '）为无效客户，报备失败 ，（' . $n_params['lp_name'] . '）。',
          'url' => $url,
          'from' => 1,
          'type' => '3',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      case "47":
        /*
    	             标题：客户看房成功。
    	             内容：亲爱的王晓二，客户姓名（客户号码）到场看房成功，请跟进客户认购，（楼盘名）。
    	             点击“点击查看”，打开该客源详情页。
             */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '客户看房成功',
          'message' => '亲爱的' . $broker_name . '，“' . $n_params['cm_name'] . '”（' . $n_params['cm_phone'] . '）到场看房成功，请跟进客户认购 ，（' . $n_params['lp_name'] . '）。',
          'url' => $url,
          'from' => 1,
          'type' => '3',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      case "48":
        /*
                                         标题：客户签约成功。
                                         内容：亲爱的王晓二，审核通过客户姓名（客户号码）已签约，（楼盘名）。
                                         点击“点击查看”，打开该客源详情页。
            */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '客户签约成功',
          'message' => '亲爱的' . $broker_name . '，审核通过客户“' . $n_params['cm_name'] . '”（' . $n_params['cm_phone'] . '）已签约，（' . $n_params['lp_name'] . '）。',
          'url' => $url,
          'from' => 1,
          'type' => '3',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      case "49":
        /*
                标题：客户签约失败。
                内容：亲爱的王晓二，审核未通过姓名（客户号码）没有签约，请再次确认，（楼盘名）。
                点击“点击查看”，打开该客源详情页。
            */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '客户签约失败',
          'message' => '亲爱的' . $broker_name . '，“' . $n_params['cm_name'] . '”（' . $n_params['cm_phone'] . '）没有签约，请再次确认，（' . $n_params['lp_name'] . '）。',
          'url' => $url,
          'from' => 1,
          'type' => '3',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      case "50":
        /*
                                         标题：恭喜您结佣成功。
                                         内容：亲爱的王晓二，恭喜您完成客户姓名（客户号码）结佣，请继续努力。（楼盘名）.
                                         点击“点击查看”，打开该客源详情页。
            */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '恭喜您结佣成功',
          'message' => '亲爱的' . $broker_name . '，恭喜您完成客户“' . $n_params['cm_name'] . '”（' . $n_params['cm_phone'] . '）结佣，请继续努力，（' . $n_params['lp_name'] . '）。',
          'url' => $url,
          'from' => 1,
          'type' => '3',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;

      case "51":
        /*
                                         标题：很遗憾，您结佣失败。
                                         内容：亲爱的王晓二，客户姓名（客户号码）结佣失败，该客户认购后因故解约，未能完成购房。（楼盘名）.
                                         点击“点击查看”，打开该客源详情页。
            */
        //按类型组装消息，插入到 message 表里
        $data = array(
          'title' => '很遗憾，您结佣失败',
          'message' => '亲爱的' . $broker_name . '，客户“' . $n_params['cm_name'] . '”（' . $n_params['cm_phone'] . '）结佣失败，该客户认购后因故解约，未能完成购房，（' . $n_params['lp_name'] . '）。',
          'url' => $url,
          'from' => 1,
          'type' => '3',
          'createtime' => $send_time
        );
        $insert_id = $this->add_data($data, 'db_city', $this->message);

        //同时把关联的经纪人信息插入到 message_broker 表里
        $info = array(
          'broker_id' => $broker_id,
          'msg_id' => $insert_id,
          'is_read' => 0,
          'createtime' => $send_time,
          'updatetime' => $send_time
        );
        $result = $this->add_data($info, 'db_city', $this->message_broker);
        break;
    }

    return $insert_id;
  }

  /**
   * 操作触发消息方法
   * @params   int $type 消息类型(消息来源);
   * @params   int $broker_id  经纪人id;
   * @params   string $broker_name  经纪人姓名;
   * @params   string $fromer  分配任务的人;
   * @params   int $deal_id 合同编号/房源编号/客源编号;
   * @return  void
   * @date     2015-01-19
   * @author   angel_in_us
   */
  public function agree_contract_message($broker_id, $brokername, $c_id, $h_id, $contract_no, $url, $sendto)
  {
    /*
			标题：房源编号CS12312的房源已成交。
			内容：亲爱的王小二，房源编号CS12312的房源已成交，详情请查看出售合同SN15011914231912。

			标题：客源编号QG12312的客源已成交
			内容：亲爱的王小二，客源编号QG12312的客源已成交，详情请查看出售合同SN15011914231914。
		*/
    //按类型组装消息，插入到 message 表里
    //sendto 决定发送给谁， 1 发送给房源经纪人  0 发送给客源经纪人

    $send_time = time();

    if ($sendto) {
      $data = array(
        'title' => '房源编号' . $h_id . '的房源已成交',
        'message' => '亲爱的' . $brokername . '，房源编号' . $h_id . '的房源已成交，详情请查看出售合同' . $contract_no . '。',
        'url' => $url,
        'from' => 1,
        'createtime' => $send_time
      );
    } else {
      $data = array(
        'title' => '客源编号' . $c_id . '的客源已成交',
        'message' => '亲爱的' . $brokername . '，客源编号' . $c_id . '的客源已成交，详情请查看出售合同' . $contract_no . '。',
        'url' => $url,
        'from' => 1,
        'createtime' => $send_time
      );
    }
    $insert_id = $this->add_data($data, 'db_city', $this->message);
    //同时把关联的经纪人信息插入到 message_broker 表里
    $info = array(
      'broker_id' => $broker_id,
      'msg_id' => $insert_id,
      'is_read' => 0,
      'createtime' => $send_time,
      'updatetime' => $send_time
    );
    $result = $this->add_data($info, 'db_city', $this->message_broker);
  }

  /**
   * 操作触发消息方法
   * @params   int $type 消息类型(消息来源);
   * @params   int $broker_id  经纪人id;
   * @params   string $broker_name  经纪人姓名;
   * @params   string $fromer  分配任务的人;
   * @params   int $deal_id 合同编号/房源编号/客源编号;
   * @return  void
   * @date     2015-01-19
   * @author   angel_in_us
   */
  public function suggest_message($broker_id, $brokername, $feedback, $adminfeedback, $creattime)
  {
    /*
			标题：您的意见反馈已处理。
			内容：亲爱的王小二，您于XXXX年XX月XX日提出的意见：XXXX反馈已经处理完成，感谢您的宝贵意见！

			标题：您的意见反馈已处理。
			内容：亲爱的王小二，您于XXXX年XX月XX日提出的意见：XXXX反馈已经处理完成。以下是管理员发送的反馈信息：XXXX
		*/
    //按类型组装消息，插入到 message 表里
    $send_time = time();
    if ($adminfeedback) {
      $message = '亲爱的' . $brokername . '，您于' . $creattime . '提出的 ' . $feedback . ' 意见反馈已经处理完成。以下是管理员发送的反馈信息：' . $adminfeedback;
    } else {
      $message = '亲爱的' . $brokername . '，您于' . $creattime . '提出的 ' . $feedback . ' 意见反馈已经处理完成，感谢您的宝贵意见！';
    }
    $data = array(
      'title' => '您的意见反馈已处理',
      'message' => $message,
      'url' => '',
      'from' => 1,
      'createtime' => $send_time
    );
    $insert_id = $this->add_data($data, 'db_city', $this->message);
    //同时把关联的经纪人信息插入到 message_broker 表里
    $info = array(
      'broker_id' => $broker_id,
      'msg_id' => $insert_id,
      'is_read' => 0,
      'createtime' => $send_time,
      'updatetime' => $send_time
    );
    $result = $this->add_data($info, 'db_city', $this->message_broker);
  }



  /*
	 * 参数params： type房f/客k源 id房/客源编号 np需要继续评价 jf奖励积分 pf评分
					name人名 dist区属 lp楼盘名 yj佣金 area面积 reason举报理由
					zxtitle资讯标题 zxinfo资讯内容  title房源标题

	 * 合作中心1：收到合作1 合作接受2 提交佣金3(先不做) 接受佣金4(先不做) 合作成交5 合作失败6
                                                  合作取消7 成交逾期8 合作冻结9 合作终止10 交易失败11 举报成功12
                                                  举报失败13 处罚通知14 房源下架15 客源下架16 合作审核通过43 合作审核不通过44 初审资料通过49 初审资料不通过50
	 * 交易评价2：收到评价17 申诉成功18 申诉失败19 处罚通知20 评价失效21
	 * 营销中心3：卖房委托22 租房委托23 求购需求24 求租需求25 预约看房26
	 * 新房分销4：新盘上架27 客户有效28 带看成功29 客户认购30 客户签约31
 			        认购失败32 签约失败33 完成结佣34 结佣失败35 报备失败36
	 * 最新资讯5：新闻资讯37
	 * 采集中心6：举报成功38 举报失败39
	 * 任务分配7：收到任务40 任务提醒41 任务撤销42 收到房/客源 48
     * 系统消息8：资料认证45 修改头像46 资质认证47 权证消息 50
	 */

  /**
   * 操作触发消息方法
   * @params   string $type 消息类型(消息来源);
   * @params   int $broker_id  经纪人id;
   * @params   string $broker_name  经纪人姓名;
   * @params   string $url  跳转链接;
   * @params   array $params
   * @return   int 消息ID
   * @date     2015/9/29
   * @author   fisher
   */
  public function add_message($type, $broker_id, $broker_name, $url = '', $params = array())
  {
    $tbl = isset($params['type']) && $params['type'] == 'f' ? '房源' : '客源';
    $tel400 = $this->config->item('tel400');

    switch ($type) {
      //当前用户收到一条合作请求
      case "1-1-1":
        $data = array(
          'title' => '您收到了一条合作申请！',
          'message' => '亲爱的' . $broker_name . '，您的' . $tbl . $params['id'] . '收到来自' . $params['name'] . '的合作申请，请在48小时内处理，祝您顺利开单！',
          'type' => 1,
          'type_list' => 1);
        break;
      //当48小时未处理时，系统消息提示
      case "1-1-2":
        $data = array(
          'title' => '您有一条待处理的合作申请！',
          'message' => '亲爱的' . $broker_name . '，您有1条合作申请待处理，请尽快在24小时内处理完毕，否则会扣除您一定的合作满意度分值哦！',
          'type' => 1,
          'type_list' => 1);
        break;
      //当前用户发送的合作被对方接受
      case "1-2":
        $data = array(
          'title' => '您发出的合作被对方接受！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '合作申请已被' . $params['name'] . '接受，请尽快联系对方进行合作事宜，祝您顺利开单！',
          'type' => 1,
          'type_list' => 2);
        break;
      //用户接受对方发送的合作
      case "1-2-1":
        $data = array(
          'title' => '您已接受对方发出的合作！',
          'message' => '亲爱的' . $broker_name . '，您已接受' . $params['name'] . '发出的' . $tbl . $params['id'] . '合作申请，合作生效，祝您顺利开单！',
          'type' => 1,
          'type_list' => 2);
        break;
      //对方确认当前合作成交
      case "1-5":
        $data = array(
          'title' => '您的合作已成交，请尽快评价！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作交易成功。请您尽快对您的合作方进行评价。请尽快至线下交易中心分佣！平台做担保，佣金一分不会少。',
          'type' => 1,
          'type_list' => 5);
        break;
      //收到合作申请方未在72小时内处理
      case "1-6-1":
        $data = array(
          'title' => '您的发出合作已失败！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '合作由于对方未在72小时内及时处理，合作失败。合作中心的合作机会还很多，祝您顺利开单！ ',
          'type' => 1,
          'type_list' => 6);
        break;
      //收到合作方拒绝合作
      case "1-6-2":
        $data = array(
          'title' => '您发出的合作已被拒绝！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，您发出的' . $tbl . $params['id'] . '合作申请已被' . $params['name'] . '拒绝，合作失败。合作中心的合作机会还很多，祝您顺利开单！',
          'type' => 1,
          'type_list' => 6);
        break;
      //用户申请或参与的合作被对方取消，在合作生效前
      case "1-7-1":
        $data = array(
          'title' => '您的合作已被取消！',
          'message' => '亲爱的' . $broker_name . '，' . $tbl . $params['id'] . '合作申请已由' . $params['name'] . '取消，合作中心的合作机会还很多，祝您顺利开单！',
          'type' => 1,
          'type_list' => 7);
        break;
      //用户申请或参与的合作被对方取消，在合作生效后
      case "1-7-2":
        $data = array(
          'title' => '您的合作已被取消！请尽快评价',
          'message' => '亲爱的' . $broker_name . '，' . $tbl . $params['id'] . '合作申请已由' . $params['name'] . '取消，合作中心的合作机会还很多，祝您顺利开单！ 请尽快对您的合作方进行评价。',
          'type' => 1,
          'type_list' => 7);
        break;
      //用户申请或参与的合作逾期失败
      case "1-8":
        $data = array(
          'title' => '您的合作已逾期，请尽快评价！',
          'message' => '亲爱的' . $broker_name . '，' . $tbl . $params['id'] . '合作交易已满三个月，成交逾期失败。合作中心的合作机会还很多，祝您顺利开单！请您尽快对您的合作方进行评价。',
          'type' => 1,
          'type_list' => 8);
        break;
      //用户申请或参与的合作被冻结
      case "1-9-1":
        $data = array(
          'title' => '您的合作已被冻结！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '合作已被冻结。合作中心的合作机会还很多，祝您顺利开单！请您尽快对您的合作方进行评价。点击查看详情。',
          'type' => 1,
          'type_list' => 9);
        break;
      case "1-9-2":
        $data = array(
          'title' => '您的合作已被冻结！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '合作已被冻结。合作中心的合作机会还很多，祝您顺利开单！请您尽快对您的合作方进行评价。点击查看详情。',
          'type' => 1,
          'type_list' => 9);
        break;
      //用户申请或参与的房源被举报并通过审核，房源下架，所有该房源参与的合作终止。所有该房源参与的合作人收到消息。在合作生效前
      case "1-10-1":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您合作的' . $tbl . $params['id'] . '，由于房源已下架，合作终止。',
          'type' => 1,
          'type_list' => 10);
        break;
      //用户申请或参与的房源被举报并通过审核，房源下架，所有该房源参与的合作终止。所有该房源参与的合作人收到消息。在合作生效后
      case "1-10-2":
        $data = array(
          'title' => '您的合作已终止！请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，您合作的' . $tbl . $params['id'] . '，由于房源已下架，合作终止。请尽快对您的合作方进行评价。',
          'type' => 1,
          'type_list' => 10);
        break;
      //用户申请或参与的房源被举报并通过审核，房源不下架，举报人参与的该房源参与的合作终止。合作双方收到消息。在合作生效前
      case "1-10-3":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您合作的' . $tbl . $params['id'] . '，由于举报“举报理由”通过审核，合作终止。',
          'type' => 1,
          'type_list' => 10);
        break;
      //用户申请或参与的房源被举报并通过审核，房源不下架，举报人参与的该房源参与的合作终止。合作双方收到消息。在合作生效后
      case "1-10-4":
        $data = array(
          'title' => '您的合作已终止！请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，您合作的' . $tbl . $params['id'] . '，由于举报“举报理由”通过审核，合作终止。请尽快对您的合作方进行评价。',
          'type' => 1,
          'type_list' => 10);
        break;
      //用户举报房源，并通过运营审核。在合作生效前
      case "1-10-5":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您合作的' . $tbl . $params['id'] . '，由于举报“举报理由”通过审核，合作终止。',
          'type' => 1,
          'type_list' => 10);
        break;
      //用户举报房源，并通过运营审核。在合作生效后
      case "1-10-6":
        $data = array(
          'title' => '您的合作已终止！请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，您合作的' . $tbl . $params['id'] . '，由于举报“举报理由”通过审核，合作终止。请尽快对您的合作方进行评价。',
          'type' => 1,
          'type_list' => 10);
        break;
      //合约生效后一方被举报责任违约，即不按协议履行合同，运营通过审核。在合作生效前
      case "1-10-7":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您合作的' . $tbl . $params['id'] . '，由于举报“举报理由”通过审核，合作终止。',
          'type' => 1,
          'type_list' => 10);
        break;
      //合约生效后一方被举报责任违约，即不按协议履行合同，运营通过审核。在合作生效后
      case "1-10-8":
        $data = array(
          'title' => '您的合作已终止！请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，您合作的' . $tbl . $params['id'] . '，由于举报“举报理由”通过审核，合作终止。请尽快对您的合作方进行评价。',
          'type' => 1,
          'type_list' => 10);
        break;
      //合作成功后，该房/客源参与的其他合作终止，参与合作方收到消息。
      case "1-10-9":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作交易，由于房源已成交，合作终止。请尽快对您的合作方进行评价。',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已成交生效前，所有该房源的甲方合作人收到系统消息
      case "1-10-10-1":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '的合作交易，由于房源已成交，合作终止。合作中心的合作机会还很多，祝您顺利开单！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已成交生效后，所有该房源的甲方合作人收到系统消息
      case "1-10-10-2":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '的合作交易，由于房源已成交，合作终止。合作中心的合作机会还很多，祝您顺利开单！请尽快对您的合作方进行评价！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已成交生效前，所有该房源的乙方合作人收到系统消息
      case "1-10-10-3":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作交易，由于房源已成交，合作终止。合作中心的合作机会还很多，祝您顺利开单！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已成交生效后，所有该房源的乙方合作人收到系统消息
      case "1-10-10-4":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作交易，由于房源已成交，合作终止。合作中心的合作机会还很多，祝您顺利开单！请尽快对您的合作方进行评价！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已失效 生效前，所有该房源的甲方合作人收到系统消息
      case "1-10-11-1":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '的合作交易，由于房源已失效，合作终止。合作中心的合作机会还很多，祝您顺利开单！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已失效 生效后，所有该房源的甲方合作人收到系统消息
      case "1-10-11-2":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '的合作交易，由于房源已失效，合作终止。合作中心的合作机会还很多，祝您顺利开单！请尽快对您的合作方进行评价！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已失效 生效前，所有该房源的乙方合作人收到系统消息
      case "1-10-11-3":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作交易，由于房源已失效，合作终止。合作中心的合作机会还很多，祝您顺利开单！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已失效 生效后，所有该房源的乙方合作人收到系统消息
      case "1-10-11-4":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作交易，由于房源已失效，合作终止。合作中心的合作机会还很多，祝您顺利开单！请尽快对您的合作方进行评价！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已注销生效前，所有该房源的甲方合作人收到系统消息
      case "1-10-12-1":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '的合作交易，由于房源已注销，合作终止。合作中心的合作机会还很多，祝您顺利开单！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已注销生效后，所有该房源的甲方合作人收到系统消息
      case "1-10-12-2":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '的合作交易，由于房源已注销，合作终止。合作中心的合作机会还很多，祝您顺利开单！请尽快对您的合作方进行评价！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已注销生效前，所有该房源的乙方合作人收到系统消息
      case "1-10-12-3":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作交易，由于房源已注销，合作终止。合作中心的合作机会还很多，祝您顺利开单！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已注销生效后，所有该房源的乙方合作人收到系统消息
      case "1-10-12-4":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作交易，由于房源已注销，合作终止。合作中心的合作机会还很多，祝您顺利开单！请尽快对您的合作方进行评价，！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已暂不售生效前，所有该房源的甲方合作人收到系统消息
      case "1-10-13-1":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '的合作交易，由于房源已暂不售，合作终止。合作中心的合作机会还很多，祝您顺利开单！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已暂不售生效后，所有该房源的甲方合作人收到系统消息
      case "1-10-13-2":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '的合作交易，由于房源已暂不售，合作终止。合作中心的合作机会还很多，祝您顺利开单！请尽快对您的合作方进行评价！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已暂不售生效前，所有该房源的乙方合作人收到系统消息
      case "1-10-13-3":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作交易，由于房源已暂不售，合作终止。合作中心的合作机会还很多，祝您顺利开单！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已暂不售生效后，所有该房源的乙方合作人收到系统消息
      case "1-10-13-4":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作交易，由于房源已暂不售，合作终止。合作中心的合作机会还很多，祝您顺利开单！请尽快对您的合作方进行评价！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已预定生效前，所有该房源的甲方合作人收到系统消息
      case "1-10-14-1":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '的合作交易，由于房源已预定，合作终止。合作中心的合作机会还很多，祝您顺利开单！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已预定生效后，所有该房源的甲方合作人收到系统消息
      case "1-10-14-2":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '的合作交易，由于房源已预定，合作终止。合作中心的合作机会还很多，祝您顺利开单！请尽快对您的合作方进行评价！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已预定生效前，所有该房源的乙方合作人收到系统消息
      case "1-10-14-3":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作交易，由于房源已预定，合作终止。合作中心的合作机会还很多，祝您顺利开单！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已预定生效后，所有该房源的乙方合作人收到系统消息
      case "1-10-14-4":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作交易，由于房源已预定，合作终止。合作中心的合作机会还很多，祝您顺利开单！请尽快对您的合作方进行评价！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已合作生效，取消合作，所有该房源的甲方合作人收到系统消息
      case "1-10-15-1":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '的合作交易，由于房源状态已被改为不合作，合作终止。合作中心的合作机会还很多，祝您顺利开单！点击查看查看详情',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已预定生效后，所有该房源的甲方合作人收到系统消息
      case "1-10-15-2":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您发出的' . $tbl . $params['id'] . '的合作交易，由于房源状态已被改为不合作，合作终止。合作中心的合作机会还很多，祝您顺利开单！请尽快对您的合作方进行评价，点击查看查看详情',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已预定生效前，所有该房源的乙方合作人收到系统消息
      case "1-10-15-3":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作交易，由于房源状态已被改为不合作，合作终止。合作中心的合作机会还很多，祝您顺利开单！',
          'type' => 1,
          'type_list' => 10);
        break;
      //该房源线下渠道已预定生效后，所有该房源的乙方合作人收到系统消息
      case "1-10-15-4":
        $data = array(
          'title' => '您的合作已终止！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作交易，由于房源状态已被改为不合作，合作终止。合作中心的合作机会还很多，祝您顺利开单！请尽快对您的合作方进行评价！',
          'type' => 1,
          'type_list' => 10);
        break;
      //对方确认交易未成交
      case "1-11":
        $data = array(
          'title' => '您的合作交易失败！请尽快评价。',
          'message' => '亲爱的' . $broker_name . '，你参与的' . $tbl . $params['id'] . '合作交易失败。合作中心的合作机会还很多，祝您顺利开单！请尽快对您的合作方进行评价。',
          'type' => 1,
          'type_list' => 11);
        break;
      //用户举报的合作房/客源举报成功
      case "1-12":
        $data = array(
          'title' => '“' . $params['reason'] . '”举报成功！',
          'message' => '亲爱的' . $broker_name . '，您举报的' . $tbl . $params['id'] . '经审核已通过。为感谢您的参与，奖励您' . $params['jf'] . '积分，已到账，请查收。让我们一起努力，共同打造诚信共享平台。',
          'type' => 1,
          'type_list' => 12);
        break;
      //用户举报的合作房/客源举报失败
      case "1-13":
        $data = array(
          'title' => '“' . $params['reason'] . '”举报失败！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，您举报的' . $tbl . $params['id'] . '经审核未通过。如有疑问，请联系我们。让我们一起努力，共同打造诚信共享平台。',
          'type' => 1,
          'type_list' => 13);
        break;
      //收到合作申请方未在72小时内处理
      case "1-14-1":
        $data = array(
          'title' => '您有一条合作申请未及时处理！',
          'message' => '亲爱的' . $broker_name . '，由于您未在72小时内及时处理' . $params['name'] . '发起的' . $tbl . $params['id'] . '合作申请，合作失败。扣除您合作满意度' . $params['pf'] . '分。',
          'type' => 1,
          'type_list' => 14);
        break;
      //合作房/客源被举报，并通过审核。包括合作生效前及合作生效后，举报理由为虚假房/客源时发送。
      case "1-14-2":
        $data = array(
          'title' => '合作' . $tbl . $params['id'] . '涉嫌虚假已被举报下架！',
          'message' => '亲爱的' . $broker_name . '，您的' . $tbl . $params['id'] . '由于涉嫌虚假，举报经审核通过已下架。同时扣除您动态评分中信息可信度系统评分' . $params['pf'] . '分，如有疑问，请联系' . $tel400 . '。让我们一起努力，共同打造诚信共享平台。',
          'type' => 1,
          'type_list' => 14);
        break;
      //合约生效后一方被举报责任违约，即不按协议履行合同，运营通过审核
      case "1-14-3":
        $data = array(
          'title' => '由于您不按协议履行合同，经核实确认处罚生效！',
          'message' => '亲爱的' . $broker_name . '，您合作的' . $tbl . $params['id'] . '由于未按协议履行合同，举报经审核已通过。扣除您信用分' . $params['pf'] . '分。如有疑问，请联系' . $tel400 . '。让我们一起努力，共同打造诚信共享平台。',
          'type' => 1,
          'type_list' => 14);
        break;
      //合作生效后一方取消合作，发送消息给取消合作的用户。
      case "1-14-4":
        $data = array(
          'title' => '由于您取消了已生效的合作，经核实确认处罚生效！',
          'message' => '亲爱的' . $broker_name . '，您合作的' . $tbl . $params['id'] . '合作您已经取消。由于合作已生效，您单方面取消合作扣除您信用分' . $params['pf'] . '分。',
          'type' => 1,
          'type_list' => 14);
        break;
      //合作房/客源被举报，并通过审核。包括合作生效前及合作生效后，举报理由为替他时发送。
      case "1-14-5":
        $data = array(
          'title' => '合作' . $tbl . $params['id'] . '涉嫌虚假已被举报下架！',
          //'message'=>'亲爱的'.$broker_name.'，您的'.$tbl.$params['id'].'由于涉嫌虚假，举报经审核通过已下架。同时扣除您动态评分中信息可信度系统评分'.$params['pf'].'分，如有疑问，请联系'.$tel400.'。让我们一起努力，共同打造诚信共享平台。点击查看详情。',
          'message' => '亲爱的' . $broker_name . '，您的' . $tbl . $params['id'] . '被举报经审核通过已下架，举报原因：' . $params['reason'] . '。如有疑问，请联系我们。让我们一起努力，共同打造诚信共享平台。',
          'type' => 1,
          'type_list' => 14);
        break;
      //运营后台下架用户房源
      case "1-15":
        $data = array(
          'title' => '您的房源编号为' . $params['id'] . '的房源已下架',
          'message' => '亲爱的' . $broker_name . '，由于“' . $params['reason'] . '”，您的房源编号为' . $params['id'] . '的房源已下架，如有疑问，请联系' . $tel400 . '。',
          'type' => 1,
          'type_list' => 15);
        break;
      //运营后台下架用户客源
      case "1-16":
        $data = array(
          'title' => '您的客源编号为' . $params['id'] . '的客源已下架',
          'message' => '亲爱的' . $broker_name . '，由于“' . $params['reason'] . '”，您的客源编号为' . $params['id'] . '的客源已下架，如有疑问，请联系' . $tel400 . '。',
          'type' => 1,
          'type_list' => 16);
        break;
      //当店长审核不通过他的合作房源
      case "1-43":
        $data = array(
          'title' => '您的的合作房源审核已被拒绝！',
          'message' => '亲爱的' . $broker_name . '，您提交' . $tbl . $params['id'] . '的合作审核不通过。',
          'type' => 1,
          'type_list' => 15);
        break;
      //当店长审核通过他的合作房源
      case "1-44":
        $data = array(
          'title' => '您的的合作房源审核已经通过！',
          'message' => '亲爱的' . $broker_name . '，您提交' . $tbl . $params['id'] . '的合作审核通过。',
          'type' => 1,
          'type_list' => 16);
        break;
      //初审资料通过给合作双方发送消息(不同公司)
      case "1-49-1":
        $data = array(
          'title' => '房源成交初审资料审核通过！',
          'message' => '亲爱的' . $broker_name . '，交易编号' . $params['order_sn'] . '的成交资料已通过审核，500积分已到账，快去积分商城看看吧！点击进入积分商城',
          'type' => 1,
          'type_list' => 16);
        break;
      case "1-49-2":
        $data = array(
          'title' => '房源成交初审资料审核通过！',
          'message' => '亲爱的' . $broker_name . '，交易编号' . $params['order_sn'] . '的成交资料已通过审核，500积分已到账，快去积分商城看看吧！点击进入积分商城',
          'type' => 1,
          'type_list' => 16);
        break;
      //初审资料通过给合作双方发送消息(同一公司)
      case "1-49-3":
        $data = array(
          'title' => '房源成交初审资料审核通过！',
          'message' => '亲爱的' . $broker_name . '，交易编号' . $params['order_sn'] . '的成交资料已通过审核！',
          'type' => 1,
          'type_list' => 16);
        break;
      case "1-49-4":
        $data = array(
          'title' => '房源成交初审资料审核通过！',
          'message' => '亲爱的' . $broker_name . '，交易编号' . $params['order_sn'] . '的成交资料已通过审核！',
          'type' => 1,
          'type_list' => 16);
        break;
      //初审资料通过给合作双方发送消息
      case "1-50":
        $data = array(
          'title' => '房源成交初审资料审核失败！',
          'message' => '亲爱的' . $broker_name . '，交易编号' . $params['order_sn'] . '的成交资料未通过审核。点击查看详情',
          'type' => 1,
          'type_list' => 16);
        break;
      //合作资料图片审核消息
      case "1-51-1":
        $data = array(
          'title' => '出售房源资料审核通过！',
          'message' => '亲爱的' . $broker_name . '，您发布的合作房源' . $params['block_name'] . ' ' . $params['buildarea'] . '平 ' . $params['price'] . '万 ' . '已通过审核，该房源将在合作中心置顶显示。',
          'type' => 1,
          'type_list' => 46);
        break;
      //合作资料图片审核消息
      case "1-51-2":
        $data = array(
          'title' => '出售房源资料审核驳回！',
          'message' => '对不起，您发布的合作房源' . $params['block_name'] . ' ' . $params['buildarea'] . '平 ' . $params['price'] . '万 ' . '未通过审核，请尝试重新提交资料审核。',
          'type' => 1,
          'type_list' => 47);
        break;
      //收到对方合作经纪人的评价
      case "2-17":
        $data = array(
          'title' => '您收到来自合作经纪人的评价，已生效。',
          'message' => '亲爱的' . $broker_name . '，' . $tbl . $params['id'] . '的合作方已对您做出了评价，已生效。',
          'type' => 2,
          'type_list' => 17);
        break;
      //对评价申诉后，并通过运营审核
      case "2-18":
        $data = array(
          'title' => '您的评价申诉已通过运营审核！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作评价已申诉成功。',
          'type' => 2,
          'type_list' => 18);
        break;
      //对评价申诉后，未通过运营审核
      case "2-19":
        $data = array(
          'title' => '您的评价申诉未通过运营审核！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作评价已申诉失败。',
          'type' => 2,
          'type_list' => 19);
        break;
      //对评价申诉后，并通过运营审核，发送给被申诉方。
      case "2-20":
        $data = array(
          'title' => '由于您涉嫌恶意评价，经核实确认处罚生效！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作评价被判定为恶意评价，扣减您信用分' . $params['pf'] . '分。',
          'type' => 2,
          'type_list' => 20);
        break;
      //运营后台更改合作评价状态为失效。
      case "2-21":
        $data = array(
          'title' => '您有一条合作评价失效！',
          'message' => '亲爱的' . $broker_name . '，您参与的' . $tbl . $params['id'] . '的合作评价已被系统管理员改为失效状态，不计入您的信用评价中。',
          'type' => 2,
          'type_list' => 21);
        break;
      //用户收到百房网C端客户发出的卖房委托
      case "3-22":
        $data = array(
          'title' => '有的新的委托房源啦，赶紧去抢吧！',
          'message' => '您所在的区域有新的房源委托“' . $params['comt_name'] . $params['hprice'] . '万”，快去抢房源吧！点击查看详情。',
          'type' => 3,
          'type_list' => 22);
        break;
      //用户收到百房网C端客户发出的租房委托
      case "3-23":
        $data = array(
          'title' => '有的新的委托房源啦，赶紧去抢吧！',
          'message' => '您所在的区域有新的房源委托“' . $params['comt_name'] . $params['hprice'] . '万”，快去抢房源吧！点击查看详情。',
          'type' => 3,
          'type_list' => 23);
        break;
      //用户收到百房网C端客户发出的求购信息
      case "3-24":
        $data = array(
          'title' => '有的新的求购客源啦，赶紧去抢吧！',
          'message' => '您所在的区域有新的客户需求“' . $params['district'] . $params['lprice'] . "-" . $params['hprice'] . '万”，快去抢客源吧！点击查看详情。',
          'type' => 3,
          'type_list' => 24);
        break;
      //用户收到百房网C端客户发出的求租信息
      case "3-25":
        $data = array(
          'title' => '有的新的求租客源啦，赶紧去抢吧！',
          'message' => '您所在的区域有新的客户需求“' . $params['district'] . $params['lprice'] . "-" . $params['hprice'] . '元/月”，快去抢客源吧！点击查看详情。',
          'type' => 3,
          'type_list' => 25);
        break;
      //用户收到百房网C端客户发出的二手房预约看房信息
      case "3-26":
        $data = array(
          'title' => '您收到一条预约看房信息，请尽快查看！',
          'message' => '有新的预约看房了，赶紧去看看吧，祝您能顺利开单！点击查看详情。',
          'type' => 3,
          'type_list' => 26);
        break;
      //热销楼盘有新上架可分销的新盘
      case "4-27":
        $data = array(
          'title' => '有新楼盘上架了，请尽快查看，参与分销！',
          'message' => '亲爱的' . $broker_name . '，【' . $params['dist'] . '】' . $params['lp'] . '参与热销楼盘，参与分销最多可得' . $params['yj'] . '元/套佣金，赶紧去报备客户吧！点击查看楼盘详情。',
          'type' => 4,
          'type_list' => 27);
        break;
      //开发商确认当前用户报备客户有效
      case "4-28":
        $data = array(
          'title' => '您的客户报备成功！',
          'message' => '亲爱的' . $broker_name . '，您向“' . $params['lp'] . '”报备的' . $params['name'] . '已被确认为有效，请尽快查看并联系客户带看吧，祝您顺利开单！点击查看报备详情。',
          'type' => 4,
          'type_list' => 28);
        break;
      //客户到案场看房成功
      case "4-29":
        $data = array(
          'title' => '您的客户' . $params['name'] . '带看成功！',
          'message' => '亲爱的' . $broker_name . '，您在“' . $params['lp'] . '”报备的客户' . $params['name'] . '已经完成了带看，请尽快与客户商谈，祝你顺利开单！',
          'type' => 4,
          'type_list' => 29);
        break;
      //客户已对房源认购
      case "4-30":
        $data = array(
          'title' => '您的客户' . $params['name'] . '认购成功！',
          'message' => '亲爱的' . $broker_name . '，您在“' . $params['lp'] . '”报备的客户' . $params['name'] . '已经认购成功！请尽快与客户商谈签约，祝你顺利开单！',
          'type' => 4,
          'type_list' => 30);
        break;
      //客户已签约
      case "4-31":
        $data = array(
          'title' => '您的客户' . $params['name'] . '签约成功！',
          'message' => '亲爱的' . $broker_name . '，您在“' . $params['lp'] . '”报备的客户' . $params['name'] . '已经签约成功！等待结佣！',
          'type' => 4,
          'type_list' => 31);
        break;
      //客户未认购
      case "4-32":
        $data = array(
          'title' => '您的客户' . $params['name'] . '认购失败！',
          'message' => '亲爱的' . $broker_name . '，您在“' . $params['lp'] . '”报备的客户' . $params['name'] . '认购失败，不要灰心，热销楼盘还有更多的楼盘值得推荐，祝你顺利开单！',
          'type' => 4,
          'type_list' => 32);
        break;
      //客户未签约
      case "4-33":
        $data = array(
          'title' => '您的客户' . $params['name'] . '签约失败！',
          'message' => '亲爱的' . $broker_name . '，您在“' . $params['lp'] . '”报备的客户' . $params['name'] . '签约失败，不要灰心，热销楼盘还有更多的楼盘值得推荐，祝你顺利开单！',
          'type' => 4,
          'type_list' => 33);
        break;
      //用户结佣完成
      case "4-34":
        $data = array(
          'title' => '恭喜您已完成结佣！',
          'message' => '恭喜您，“' . $params['lp'] . '”已为您完成结佣！热销楼盘还有更多楼盘，赶紧来赚取更多的佣金吧！',
          'type' => 4,
          'type_list' => 34);
        break;
      //用户结佣失败
      case "4-35":
        $data = array(
          'title' => '您的分销结佣失败！',
          'message' => '亲爱的' . $broker_name . '，很遗憾您在“' . $params['lp'] . '”的结佣失败，如有疑问请联系' . $tel400 . '，热销楼盘还有更多的楼盘值得推荐，快去看看吧！',
          'type' => 4,
          'type_list' => 35);
        break;
      //用户报备客户失败
      case "4-36":
        $data = array(
          'title' => '您的客户' . $params['name'] . '报备失败！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，您在“' . $params['lp'] . '”报备的' . $params['name'] . '报备失败，赶紧选择更多的客户来报备吧，高额佣金等您来！',
          'type' => 4,
          'type_list' => 36);
        break;
      //运营后台编辑资讯发送
      case "5-37":
        $data = array(
          'title' => $params['zxtitle'],
          'message' => $params['zxinfo'],
          'type' => 5,
          'type_list' => 37);
        break;
      //用户举报的采集中心房源举报成功
      case "6-38":
        $data = array(
          'title' => '房源举报成功！',
          'message' => '亲爱的' . $broker_name . '，您举报的采集中心房源(联系方式：' . $params['phone'] . ')经审核已通过。感谢您的参与，让我们一起努力，共同打造诚信共享平台。',
          'type' => 6,
          'type_list' => 38);
        break;
      //用户举报的采集中心房源举报失败
      case "6-39":
        $data = array(
          'title' => '房源' . $params['id'] . '举报失败！',
          'message' => '亲爱的' . $broker_name . '，很遗憾，您举报的采集中心房源(联系方式：' . $params['phone'] . ')经审核未通过。如有疑问，请联系' . $tel400 . '。让我们一起努力，共同打造诚信共享平台。',
          'type' => 6,
          'type_list' => 39);
        break;
      //用户采集中心订阅
      case "6-52":
        $data = array(
          'title' => '采集订阅',
          'message' => '您采集订阅的“' . $params['info'] . '”有新房源了，赶快去看看吧！进入采集中心，点击“采集订阅”，好房一手掌握！',
          'type' => 6,
          'type_list' => 52);
        break;
      //您有一条分配任务，请尽快处理。
      case "7-40-1":
        $data = array(
          'title' => '您有一条分配任务，请尽快处理。',
          'message' => '亲爱的' . $broker_name . '，' . $params['name'] . '分配给您一条跟进任务，房源编号为' . $params['id'] . '。',
          'type' => 7,
          'type_list' => 40);
        break;
      //您有一条分配任务，请尽快处理。
      case "7-40-2":
        $data = array(
          'title' => '您有一条分配任务，请尽快处理。',
          'message' => '亲爱的' . $broker_name . '，' . $params['name'] . '分配给您一条跟进任务，客源编号为' . $params['id'] . '。',
          'type' => 7,
          'type_list' => 40);
        break;
      //当离任务最迟完成时间15小时时，系统消息提示
      case "7-41":
        $data = array(
          'title' => '您有一条分配任务即将过期，请尽快处理。',
          'message' => '亲爱的' . $broker_name . '，' . $params['name'] . '分配给您一条跟进任务，' . $tbl . '编号为' . $params['id'] . '。即将过期，请尽快处理。',
          'type' => 7,
          'type_list' => 41);
        break;
      //撤销任务后被分配经纪人收到消息提示
      /*case "7-42":
				$data = array(
					'title'=>'您有一条分配任务已被撤销。',
					'message'=>'亲爱的'.$broker_name.'，您的房源编号为'.$params['id'].'的跟进任务已被'.$params['name'].'撤销。',
					'type'=>7,
					'type_list'=>42);
				break;*/
      //有新的房源划分到您名下，请知晓
      /*case "7-48-1":
				$data = array(
					'title'=>'有新的房源划分到您名下，请知晓',
					'message'=>'亲爱的'.$broker_name.'，'.$params['name'].'分配给您新的房源，房源编号为'.$params['id'].'。',
					'type'=>7,
					'type_list'=>48);
				break;
            //有新的客源划分到您名下，请知晓
			case "7-48-2":
				$data = array(
					'title'=>'有新的客源划分到您名下，请知晓',
					'message'=>'亲爱的'.$broker_name.'，'.$params['name'].'分配给您新的客源，客源编号为'.$params['id'].'。',
					'type'=>7,
					'type_list'=>48);
				break;*/
      //资料认证成功经纪人收到消息提示
      case "8-45-1":
        $data = array(
          'title' => '您的帐号已认证成功！',
          'message' => '亲爱的' . $broker_name . '，您的帐号已认证成功，您将拥有更多的特权，赶紧去看看吧。',
          'type' => 8,
          'type_list' => 45);
        break;
      //资料认证失败经纪人收到消息提示
      case "8-45-2":
        if ($params['reason']) {
          $params['reason'] = '因“' . $params['reason'] . '”';
        }
        $data = array(
          'title' => '您的帐号认证失败！',
          'message' => '亲爱的' . $broker_name . '，您的帐号' . $params['reason'] . '未通过认证审核，请按要求上传资料后，重新提交。',
          'type' => 8,
          'type_list' => 45);
        break;
      //修改头像成功经纪人收到消息提示
      case "8-46-1":
        $data = array(
          'title' => '您的帐号认证成功！',
          'message' => '亲爱的' . $broker_name . '，您的头像已成功修改。',
          'type' => 8,
          'type_list' => 46);
        break;
      //修改头像失败经纪人收到消息提示
      case "8-46-2":
        if ($params['reason']) {
          $params['reason'] = '因“' . $params['reason'] . '”';
        }
        $data = array(
          'title' => '您的帐号认证失败！',
          'message' => '亲爱的' . $broker_name . '，您的头像“' . $params['reason'] . '”未通过审核，请重新上传图片。',
          'type' => 8,
          'type_list' => 46);
        break;
      //总店长以下的经纪人申请资质重新认证
      case "8-47":
        $data = array(
          'title' => '你有员工申请资质重新认证！',
          'message' => '亲爱的' . $broker_name . '，' . $params['reason'] . '向您发起了资质的重新认证申请，请联系' . $params['reason'] . '后按照更换门店/公司的步骤，进行下一步操作。',
          'type' => 8,
          'type_list' => 47);
        break;
      //积分兑换
      case "8-48":
        $data = array(
          'title' => '恭喜您成功兑换经纪人培训会入场券！',
          'message' => $params['company_name'] . ' ' . $params['agency_name'] . ' ' . $broker_name . '，已成功兑换1张大型经纪人培训会入场券，兑换时间为' . $params['create_date'] . '，请在入场时出示此系统消息，凭此消息入场。',
          'type' => 8,
          'type_list' => 48);
        break;
      //积分兑换
      case "8-50":
        $data = array(
          'title' => '您有一条权证提醒！',
          'message' => '亲爱的' . $broker_name . '，您有一条合同' . $params['number'] . '的权证流程提醒，提醒日期：' . $params['remind_time'] . '，提醒内容："' . $params['remind_remark'] . '"。点击查看详情！',
          'type' => 8,
          'type_list' => 50);
        break;
      //合作朋友圈添加好友申请
      case "8-51-1":
        $data = array(
          'title' => '朋友圈提示！',
          //'message'=>'亲爱的'.$broker_name.'，您有一条合同'.$params['number'].'的权证流程提醒，提醒日期：'.$params['remind_time'].'，提醒内容："'.$params['remind_remark'].'"。点击查看详情！',
          'message' => '经纪人' . $params['name'] . '请求添加您为好友，同意后，您将在合作朋友圈中与其共享房源信息。',
          'type' => 8,
          'type_list' => 51);
        break;
      //合作朋友圈接受申请
      case "8-51-2":
        $data = array(
          'title' => '朋友圈提示！',
          //'message'=>'亲爱的'.$broker_name.'，您有一条合同'.$params['number'].'的权证流程提醒，提醒日期：'.$params['remind_time'].'，提醒内容："'.$params['remind_remark'].'"。点击查看详情！',
          'message' => '经纪人' . $params['name'] . '已经接受了您的好友请求。',
          'type' => 8,
          'type_list' => 51);
        break;
      //合作朋友圈拒绝申请
      case "8-51-3":
        $data = array(
          'title' => '朋友圈提示！',
          //'message'=>'亲爱的'.$broker_name.'，您有一条合同'.$params['number'].'的权证流程提醒，提醒日期：'.$params['remind_time'].'，提醒内容："'.$params['remind_remark'].'"。点击查看详情！',
          'message' => '经纪人' . $params['name'] . '已经拒绝了您的好友请求。',
          'type' => 8,
          'type_list' => 51);
        break;
      //首次登录发送消息
      case "8-52":
        $data = array(
          'title' => '快速认证，体验更多功能！',
          //'message'=>'亲爱的'.$broker_name.'，您有一条合同'.$params['number'].'的权证流程提醒，提醒日期：'.$params['remind_time'].'，提醒内容："'.$params['remind_remark'].'"。点击查看详情！',
          'message' => '欢迎您使用' . SOFTWARE_NAME . '，进入个人中心提交认证资料，采集无限制，还有群发、合作等更多功能免费用！赶快行动吧！详情咨询：' . $tel400,
          'type' => 8,
          'type_list' => 52);
        break;
      case "8-53":
        $data = array(
          'title' => '您的认证资料已提交',
          'message' => $broker_name . '您好，您的认证资料信息已提交，工作人员会在1-3个工作日内审核通过，请耐心等待，如有问题，请咨询' . $tel400,
          'type' => 8,
          'type_list' => 53);
        if ($params['phone']) {
          $data['message'] .= '转' . $params['phone'];
        }
        break;

    }
    if (is_full_array($data)) {
      //插入消息表
      $data['url'] = $url;
      $data['createtime'] = time();
      $insert_id = $this->add_data($data, 'db_city', $this->message);
      //同时把关联的经纪人信息插入到 message_broker 表里
      $send_time = time();
      $info = array(
        'broker_id' => $broker_id,
        'msg_id' => $insert_id,
        'is_read' => 0,
        'createtime' => $send_time,
        'updatetime' => $send_time
      );
      $result = $this->add_data($info, 'db_city', $this->message_broker);
      //判断消息所属类型是否开启了弹窗功能
      $is_pop_open = $this->is_open_pop($data['type']);
      //如果开启弹窗功能，添加数据到弹窗消息数据库
      if ($is_pop_open == 1) {
        $data['id'] = $insert_id;
        $this->add_data($data, 'db_city', $this->message_pop);
      }
      return $insert_id;
    }
  }

  public function is_open_pop($id)
  {
    $this->dbback_city->where('id', $id);
    $result = $this->dbback_city->get($this->message_open_pop)->row_array();
    return $result['is_pop_open'];
  }

  /**
   * 发布新消息
   */
  function add($paramlist = array(), $database = 'db_city')
  {
    $result = $this->add_data($paramlist, $database, $this->message);
    return $result;
  }

  function add_pop($paramlist = array(), $database = 'db_city')
  {
    $result = $this->add_data($paramlist, $database, $this->message_pop);
    return $result;
  }

  /**
   * 添加数据到message_broker #发布时用到
   */
  function add_message_broker($paramlist = array(), $database = 'db_city')
  {
    $result = $this->add_data($paramlist, $database, $this->message_broker);
    return $result;
  }
}

?>
