/**
 * 为表单生成随机字符
 * @param name 表单名
 * @param len 字符长度
 */
function input_random(name, len) {
    require(['package/spark-md5.min'], function (md5) {
        let code = (new Date()).valueOf().toString();
        let hash = md5.hash(code) + md5.hash(code);
        $("[name=" + name + "]").val(hash.substr(0, len))
    });
}

/**
 * 删除事件
 * @param bt 按钮
 * @param message 提示消息
 */
function destroy(bt, message) {
    message = message ? message : '确定删除吗？';
    require(['hdjs'], function (hdjs) {
        hdjs.confirm(message, function () {
            $(bt).prev('form').submit();
        });
    });
    return false;
}

/**
 * 发送验证码
 * @param btId
 * @param inputName
 */
function send_code(btId, inputName) {
    require(['hdjs', 'bootstrap'], function (hdjs) {
        let option = {
            //按钮
            el: btId,
            //后台链接
            url: '/member/send/code',
            //验证码等待发送时间
            timeout: window.system.message_timeout,
            //表单，手机号或邮箱的INPUT表单
            input: inputName
        };
        hdjs.validCode(option);
    })
}

/**
 * 异步POST请求
 * @param action
 * @param data
 */
function ajax_post_request(action, data) {
    require(['axios', 'hdjs'], function (axios, hdjs) {
        axios.post(action, data).then(function (response) {
            hdjs.swal({
                text: response.data.message,
                button: false,
                icon: 'success'
            });
        }).catch(function (error) {
            hdjs.swal({
                text: error.response.data.message,
                button: false,
                icon: 'warning'
            });
        })
    });
}

/**
 * 异步GET请求
 * @param action
 * @param data
 */
function ajax_get_request(action, data) {
    require(['axios', 'hdjs'], function (axios, hdjs) {
        axios.get(action, {params: data}).then(function (response) {
            hdjs.swal({
                text: response.data.message,
                button: false,
                icon: 'success'
            });
        }).catch(function (error) {
            hdjs.swal({
                text: error.response.data.message,
                button: false,
                icon: 'warning'
            });
        })
    });
}

/**
 * 提取markdown菜单
 * @param el 内容元素
 * @param container 放菜单容器
 */
function markdown_toc(el, container) {
    require(['jquery'], function ($) {
        $(el).find("h1,h2,h3").each(function (i) {
            $(this).attr('id', 'id' + i);
        });
        $(el).find("h1,h2,h3").each(function (i) {
            let num = $(this)[0].tagName.substr(1, 1);
            let li = "<li><a href='#id" + i + "' class='h" + num + "'>" + $(this).text() + "</a></li>";
            $(container).append(li);
        });
    })
}