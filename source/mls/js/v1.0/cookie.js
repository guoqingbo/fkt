var expdate = new Date();

function getCookieVal(offset) {
  var endstr = document.cookie.indexOf(";", offset);
  if (endstr == -1) endstr = document.cookie.length;
  return unescape(document.cookie.substring(offset, endstr));
}

function Cookie(name) {
  var arg = name + "=";
  var alen = arg.length;
  var clen = document.cookie.length;
  var i = 0;
  while (i < clen) {
    var j = i + alen;
    if (document.cookie.substring(i, j) == arg) return getCookieVal(j);
    i = document.cookie.indexOf(" ", i) + 1;
    if (i == 0) break;
  }
  return null;
}

function setCookie(name, value, expires, path, domain, secure) {
  expdate.setTime(expdate.getTime() + (24 * 60 * 60 * 1000 * 365));
  document.cookie = name + "=" + encodeURIComponent(value) +
    ((expires) ? "; expires=" + expires.toGMTString() : "") +
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    ((secure) ? "; secure" : "");
}

function deleteCookie(name) {
  expdate = new Date();
  expdate.setTime(expdate.getTime() - (86400 * 1000 * 1));
  setCookie(name, "", expdate);
}
