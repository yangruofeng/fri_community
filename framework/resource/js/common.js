/**
 * Created by sahara on 2016/8/20.
 */
function changeLang(lang, refresh_type) {
    var _domain = document.domain;
    jQuery.cookie((COOKIE_PRE || '') + 'lang', lang, {path: '/', domain: _domain, expires: 3652.1});
    var href = location.href.substr(0, location.href.length - location.hash.length);
    if (!refresh_type)
        location.href = href;
    if (refresh_type == 2) {
        if (href.indexOf('refresh=') < 0)
            href += (href.indexOf('?') > 0 ? '&refresh=1' : '?refresh=1')
        location.href = href;
    }
}

function browserRedirect() {
    var sUserAgent = navigator.userAgent.toLowerCase();
    var bIsIpad = sUserAgent.match(/ipad/i) == "ipad";
    var bIsIphoneOs = sUserAgent.match(/iphone os/i) == "iphone os";
    var bIsMidp = sUserAgent.match(/midp/i) == "midp";
    var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";
    var bIsUc = sUserAgent.match(/ucweb/i) == "ucweb";
    var bIsAndroid = sUserAgent.match(/android/i) == "android";
    var bIsCE = sUserAgent.match(/windows ce/i) == "windows ce";
    var bIsWM = sUserAgent.match(/windows mobile/i) == "windows mobile";
    if (bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM) {
        return 1;
    } else {
        return 0;
    }
}

$(document).ready(function () {
    $("input[type=number]").on("keydown", function (e) {
        if (e.keyCode == 69) return false;
        if (e.keyCode == 189) return false;
        if (e.keyCode == 187) return false;
        if (e.keyCode == 107) return false;
        if (e.keyCode == 109) return false;

    });
});

function message(title, msg, type, okFn, cancelFn) {
    switch (type) {
        case 'loading':
            _modalLoading(title, msg);
            break;
        case 'prompt':
            _modalPrompt(title, msg);
            break;
        case 'confirm':
            _modalConfirm(title, msg, okFn, cancelFn);
            break;
        case 'succ':
            _modalSuccess(title, msg, okFn);
            break;
        case 'error':
            _modalError(title, msg);
            break;
        case 'close':
            _modalClose();
            break;
        case 'closeAll':
            _modalCloseAll();
            break;
        default:
            _modalPrompt('Handle fail', '')
    }
}

function _modalLoading(title, msg) {
    zeroModal.loading(4);
}

function _modalPrompt(title, msg) {
    zeroModal.alert({
        content: title,
        contentDetail: msg,
        esc: true,
        top: 100,
        overlayClose: true,
        buttons: [{
            className: 'zeromodal-btn zeromodal-btn-primary',
            name: 'Ok',
            fn: function (opt) {
            }
        }]
    });
}

function _modalConfirm(title, msg, okFn, cancelFn) {
    zeroModal.confirm({
        content: title,
        contentDetail: msg,
        esc: true,
        top: 100,
        buttons: [{
            className: 'zeromodal-btn zeromodal-btn-primary',
            name: 'Ok',
            fn: function (opt) {
                if (typeof okFn === 'function') {
                    okFn();
                }
            }
        }, {
            className: 'zeromodal-btn zeromodal-btn-default',
            name: 'Cancel',
            fn: function (opt) {
                if (typeof cancelFn === 'function') {
                    cancelFn();
                }
            }
        }]
    });
}

function _modalSuccess(title, msg, okFn) {
    zeroModal.success({
        content: title,
        contentDetail: msg,
        esc: true,
        top: 100,
        overlayClose: true,
        buttons: [{
            className: 'zeromodal-btn zeromodal-btn-primary',
            name: 'Ok',
            fn: function (opt) {
                if (typeof okFn === 'function') {
                    okFn();
                }
            }
        }]
    });
}

function _modalError(title, msg) {
    zeroModal.error({
        content: title,
        contentDetail: msg,
        esc: true,
        top: 100,
        overlayClose: true,
        buttons: [{
            className: 'zeromodal-btn zeromodal-btn-primary',
            name: 'Ok',
            fn: function (opt) {
            }
        }]
    });
}

function _modalClose() {
    zeroModal.close();
}

function _modalCloseAll() {
    zeroModal.closeAll();
}

var complexSelect = complexSelect || {};
complexSelect.create = function (_conf) {//
    var _title = _conf._title ? _conf._title : 'Title';
    var _width = _conf._width ? _conf._width : '800px';
    var _height = _conf._height ? _conf._height : '400px';

    if (_conf._url) {
        var _url = _conf._url;
    } else {
        alert('Url Required!');
        return;
    }

    complexSelect.callback = _conf._callback;

    $('#complexSelectModal').remove();
    var _html = '';
    _html += '<div class="modal" id="complexSelectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">';
    _html += '<style>#complexSelectModal .modal-dialog {margin-top: 10px!important;}</style>';
    _html += '<div class="modal-dialog" role="document" style="width: ' + _width + '">';
    _html += '<div class="modal-content">';
    _html += '<div class="modal-header">';
    _html += '<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="complexSelect.colse()"><span aria-hidden="true">&times;</span></button>';
    _html += '<h4 class="modal-title" id="myModalLabel">' + _title + '</h4></div>';
    _html += '<div class="modal-body">';
    _html += '<iframe id="frameStage" style="border: 0" src="' + _url + '" name="frameStage" width="100%" height="' + _height + '"></iframe>';
    _html += '</div><div class="modal-footer">';
    _html += '<button type="button" class="btn btn-default" data-dismiss="modal" onclick="complexSelect.colse()">Cancel</button>';
    _html += '<button type="button" class="btn btn-danger" onclick="complexSelect.confirm()">Confirm</button>';
    _html += '</div></div></div></div>';
    $('body').append(_html);
    $('#complexSelectModal').modal('show');
}
complexSelect.confirm = function () {
    var id, ext;
    $('#complexSelectModal #frameStage').contents().find('input[data-key="selector"]:checked').each(function (i) {
        var _id = $(this).data('value');
        var _ext = $(this).data('ext');
        if (i === 0) {
            id = _id;
            ext = _ext;
        } else {
            id += ',' + _id;
            ext += ',' + _ext;
        }
    });
    complexSelect.callback(id, ext);
    complexSelect.colse();
}
complexSelect.colse = function () {
    $('#complexSelectModal').next('.modal-backdrop').remove();
    $('#complexSelectModal').remove();
}

function validform(opt) {
    var i = 0, params = opt.params, len = params.length, rules = {}, messages = {}, str = JSON.stringify(params);
    $(opt.ele + ' input[name=validate]').val(str);
    for (i; i < len; i++) {
        var val = params[i], rule = val.rules, message = val.messages, temp = new Array();
        if (rule.regexp) {
            temp['reg'] = true;
            temp['regexp'] = rule['regexp'];
            temp['regexpFun'] = rule['regexpFun'];
            delete rule['regexpFun']
            delete rule['regexp'];
        }
        rules[val.field] = rule;

        if (message.regexp) {
            temp['regexpMsg'] = message['regexp'];
            delete message['regexp'];
        }
        messages[val.field] = message;
        if (temp['reg']) {
            var input = '<input type="hidden" name="' + val.field + 'Regexp" value="' + temp['regexp'] + '" />';
            $(opt.ele).append(input);
            addValidMethod(temp);
        }
    }
    $(opt.ele).validate({
        errorPlacement: function (error, element) {
            element.nextAll('.validate-checktip').first().html(error)
        },
        rules: rules,
        messages: messages
    });
}
function addValidMethod(exp) {
    var checkFun = exp['regexpFun'], checkReg = exp['regexp'], checkMsg = exp['regexpMsg'];
    $.validator.addMethod(checkFun, function (value, element, params) {
        return this.optional(element) || (checkReg.test(value));
    }, checkMsg);
}

function formatCurrency(num) {
    num = num.toString().replace(/\$|\,/g, '');
    if (isNaN(num))
        num = "0";
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num * 100 + 0.50000000001);
    cents = num % 100;
    num = Math.floor(num / 100).toString();
    if (cents < 10)
        cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
        num = num.substring(0, num.length - (4 * i + 3)) + ',' +
        num.substring(num.length - (4 * i + 3));
    return (((sign) ? '' : '-') + num + '.' + cents);
}
