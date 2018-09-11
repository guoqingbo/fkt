;(function ($) {
  var calendar_wrap = $("#calendar");
  var prev_year_btn = $("#prev_year");
  var prev_month_btn = $("#prev_month");
  var next_year_btn = $("#next_year");
  var next_month_btn = $("#next_month");
  var year_wrap = $("#year");
  var month_wrap = $("#month");

  var Calendar = function (year, month) {
    this.calendar_days = $("#calendar").find('td');
    this.year = year;
    this.month = month;
    this.init();
  }, proto = Calendar.prototype;

  proto.init = function () {
    var self = this;
    self.calendar_days.empty();
    var date = new Date();
    var cur_year = date.getFullYear();
    var cur_month = date.getMonth() + 1;
    var today = date.getDate();  //获取今天

    date.setFullYear(self.year, self.month, 0);
    var days = date.getDate();  //获取总天数

    if (self.month == 1) {
      date.setFullYear(self.year - 1, 12, 0);
    } else {
      date.setFullYear(self.year, self.month - 1, 0);
    }
    var prev_month_days = date.getDate();

    date = new Date(self.year, self.month - 1, 1);
    var start_day = date.getDay();  //获取第一天日期

    for (var i = 0; i < start_day; i++) {
      var cur_td = self.calendar_days.eq(i);
      cur_td.html('<span class="gray">' + (prev_month_days - start_day + i + 1) + '</span>');
    }
    ; //上月天数循环
    for (var i = 0; i < days; i++) {
      var cur_td = self.calendar_days.eq(i + start_day);
      var day = i + 1;
      if (self.year == cur_year && self.month == cur_month && day == today) {
        cur_td.html('<span class="today">' + day + '</span>');
      } else {
        cur_td.html('<span>' + day + '</span>');
      }
    }
    ; //当月天数循环
    for (var i = 0; i < (42 - days - start_day); i++) {
      var cur_td = self.calendar_days.eq(i + days + start_day);
      cur_td.html('<span class="gray">' + (i + 1) + '</span>');
    }
    ; //下月天数循环
  }

  proto.changeDate = function (year, month) {
    var self = this;
    self.year = year;
    self.month = month;
    self.init();
  }

  var cur_date = new Date();
  var cur_year = cur_date.getFullYear();
  var cur_month = cur_date.getMonth() + 1;
  year_wrap.text(cur_year);
  month_wrap.text(cur_month);
  var calendar = new Calendar(cur_year, cur_month);

  prev_year_btn.click(function (event) {
    /* Act on the event */
    var year = year_wrap.text();
    year = parseInt(year) - 1;
    year_wrap.text(year);
    var month = month_wrap.text();
    calendar.changeDate(year, month);
  });

  prev_month_btn.click(function (event) {
    /* Act on the event */
    var year = year_wrap.text();
    var month = month_wrap.text();
    if (month == '1') {
      year = parseInt(year) - 1;
      month = 12;
      year_wrap.text(year);
      month_wrap.text(month);
    } else {
      month = parseInt(month) - 1;
      month_wrap.text(month);
    }
    calendar.changeDate(year, month);
  });

  next_month_btn.click(function (event) {
    /* Act on the event */
    var year = year_wrap.text();
    var month = month_wrap.text();
    if (month == '12') {
      year = parseInt(year) + 1;
      month = 1;
      year_wrap.text(year);
      month_wrap.text(month);
    } else {
      month = parseInt(month) + 1;
      month_wrap.text(month);
    }
    calendar.changeDate(year, month);
  });

  next_year_btn.click(function (event) {
    /* Act on the event */
    var year = year_wrap.text();
    year = parseInt(year) + 1;
    year_wrap.text(year);
    var month = month_wrap.text();
    calendar.changeDate(year, month);
  });
})($);
