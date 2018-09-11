$(function () {
// 禁止Backspace键
  document.getElementsByTagName("body")[0].onkeydown = function (ev) {
    var oEvent = ev || event;
    if (oEvent.keyCode == 8) {
      var elem = oEvent.srcElement;
      var name = elem.nodeName;

      if (name != 'INPUT' && name != 'TEXTAREA') {
        oEvent.returnValue = false;
        return;
      }
      var type_e = elem.type.toUpperCase();
      if (name == 'INPUT' && (type_e != 'TEXT' && type_e != 'TEXTAREA' && type_e != 'PASSWORD' && type_e != 'FILE')) {
        oEvent.returnValue = false;
        return;
      }
      if (name == 'INPUT' && (elem.readOnly == true || elem.disabled == true)) {
        oEvent.returnValue = false;
        return;
      }
    }
  }

});
