// 评分插件

var Class = {
  create: function () {
    return function () {
      this.initialize.apply(this, arguments);
    }
  }
}
var Extend = function (destination, source) {
  for (var property in source) {
    destination[property] = source[property];
  }
}
function stopDefault(e) {
  if (e && e.preventDefault) {
    e.preventDefault();
  } else {
    window.event.returnValue = false;
  }
  return false;
}

var Stars = Class.create();
Stars.prototype = {
  initialize: function (star, options) {
    this.SetOptions(options); //默认属性
    var flag = 999; //定义全局指针
    var isIE = (document.all) ? true : false; //IE?
    var starlist = document.getElementById(star).getElementsByTagName('span'); //星星列表
    var $starlist = $(starlist)
    var input = document.getElementById(this.options.Input) || document.getElementById(star + "-input"); // 输出结果
    var tips = document.getElementById(this.options.Tips) || document.getElementById(star + "-tips"); // 打印提示
    var nowClass = " " + this.options.nowClass; // 定义选中星星样式名
    var tipsTxt = this.options.tipsTxt; // 定义提示文案
    var len = starlist.length; //星星数量


    $starlist.each(function (index, element) {
      $(this).hover(function () {
        $(this).addClass('star' + index + '_hover')
      }, function () {
        $(this).removeClass('star' + index + '_hover')
      })
    });


    for (var i = 0; i < len; i++) { // 绑定事件 点击 鼠标滑过
      starlist[i].value = i;
      starlist[i].onclick = function (e) {
        stopDefault(e);
        this.className = this.className + nowClass;
        flag = this.value;
        input.value = this.getAttribute("star:value");
        tips.innerHTML = tipsTxt[this.value]
        return false
      }

      starlist[i].onmouseover = function () {
        tips.innerHTML = tipsTxt[this.value]
        if (flag < 999) {
          var reg = RegExp(nowClass, "g");
          starlist[flag].className = starlist[flag].className.replace(reg, "")
        }

      }

      starlist[i].onmouseout = function () {

        if (flag < 999) {
          starlist[flag].className = starlist[flag].className + nowClass;
          tips.innerHTML = tipsTxt[starlist[flag].value]
        }
        else {
          tips.innerHTML = "";
        }

      }

    }
    ;

  },
  //设置默认属性
  SetOptions: function (options) {
    this.options = {//默认值
      Input: "",//设置触保存分数的INPUT
      Tips: "",//设置提示文案容器
      nowClass: "current-rating",//选中的样式名
      tipsTxt: [
        '<span class="t_f"><strong class="num">1</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>信息不存在或与实际情况完全不符</span>',
        '<span class="t_f"><strong class="num">2</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>信息存在，但与实际情况有差距</span>',
        '<span class="t_f"><strong class="num">3</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>信息介绍与实际情况略有出入</span>',
        '<span class="t_f"><strong class="num">4</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>信息介绍与实际情况基本一致</span>',
        '<span class="t_f"><strong class="num">5</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>信息介绍与实际情况完全一致</span>'
      ]//提示文案
    };
    Extend(this.options, options || {});
  }
};


var Stars1 = new Stars("stars1");

var Stars2 = new Stars("stars2", {
  tipsTxt: [
    '<span class="t_f"><strong class="num">1</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>态度恶劣，完全不理</span>',
    '<span class="t_f"><strong class="num">2</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>有待改进，不够积极</span>',
    '<span class="t_f"><strong class="num">3</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>态度积极，基本满意</span>',
    '<span class="t_f"><strong class="num">4</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>反馈及时，态度友好</span>',
    '<span class="t_f"><strong class="num">5</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>完全可靠，配合度高</span>'
  ]
});


var Stars3 = new Stars(
  "stars3", {
    tipsTxt: [
      '<span class="t_f"><strong class="num">1</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>完全不专业，太业余</span>',
      '<span class="t_f"><strong class="num">2</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>业务不熟练，不满意</span>',
      '<span class="t_f"><strong class="num">3</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>还可以，基本满意</span>',
      '<span class="t_f"><strong class="num">4</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>比较专业，业务熟练</span>',
      '<span class="t_f"><strong class="num">5</strong>分</span><span class="t_tex"><i class="sj">&nbsp;</i>非常优秀，专业可靠</span>'
    ]
  })
