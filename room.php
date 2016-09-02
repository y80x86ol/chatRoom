<?php
$room = array(
    1 => '技术交流',
    2 => '灌水达人',
    3 => '种子达人'
);

if (!isset($_POST['room_num']) || !isset($room[$_POST['room_num']])) {
    echo '错误请求来源!';
    die();
}
$room_num = $_POST['room_num'];
?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <title><?php echo $room[$room_num] ?> - 西门聊天室</title>
        <link rel="stylesheet" href="public/css/style.css" media="screen" type="text/css" />
    </head>

    <body>
        <div id="convo" data-from="Sonu Joshi">
            <p class="chart-info">聊天室：<?php echo $room[$room_num] ?><span class="pull-right">当前在线人数：<span class="chart-num"></span>人</span></p>
            <div class="chart-content">
                <ul class="chat-thread">
                </ul>
                <div class="chart-user-info">
                    <span class="user-info-title">用户列表</span>
                    <ul class="user-list">
                    </ul>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <div class="send-message">
            <input name="message" class="messageContent" id="messageContent" type="text" maxlength="50"/>
            <input type="button" class="messageButton" onclick="sendMessage()"value="发送"/>
        </div>
        <div class="credits">西门聊天室</div>

        <script src="//cdn.bootcss.com/jquery/1.12.1/jquery.min.js"></script>
        <script>
                /**
                 * 用户名校验检查
                 * @type type
                 */
                var username = '<?php echo isset($_POST['username']) ? substr(trim($_POST['username']), 0, 15) : ''; ?>';
                if (!username) {
                    alert("请输入用户名");
                    window.location = 'index.php';
                }
                scrollBottom();
                /**
                 * 建立连接
                 * @type WebSocket
                 */
                var ws = new WebSocket("ws://localhost:2345");
                //连接建立时触发
                ws.onopen = function ()
                {
                    //建立链接，进入聊天室，发送欢迎信息
                    var data = '{"room":"' +<?php echo $room_num ?> + '","type":"connect","user":"' + username + '","msg":"系统消息：' + username + '进入了聊天室"}';
                    ws.send(data);
                };
                //客户端接收服务端数据时触发
                ws.onmessage = function (evt)
                {
                    console.log(evt);
                    if (evt.type == 'message') {
                        var data = JSON.parse(evt.data);
                        var html = '';
                        if (data.type == 'msg') {
                            if (data.user == username) {
                                html = '<li class="right">你(' + username + ')说：' + data.msg + '</li>';
                            } else {
                                html = '<li class="left"><span class="usernameHighlight">' + data.user + '</span>说：' + data.msg + '</li>';
                            }
                        } else if (data.type == 'info' || data.type == 'notice' || data.type == 'connect') {
                            html = '<li class="left">' + data.msg + '</li>';
                        }

                        $(".chat-thread").append(html);
                        scrollBottom();

                        //修改在线聊天人数
                        $(".chart-num").text(data.chartNum);

                        //修改用户列表
                        if (data.userList) {
                            var user_list = '';
                            for (i in data.userList) {
                                user_list += '<li>' + data.userList[i] + '</li>';
                            }
                            $(".user-list").html(user_list);
                        }
                    }

                };
                //连接关闭时触发
                ws.onclose = function (evt)
                {
                    alert("对不起，聊天室被迫关闭，请加入其他聊天室继续High！");
                    console.log("WebSocketClosed!");
                };
                //通信发生错误时触发
                ws.onerror = function (evt)
                {
                    alert("对不起，通信发生了错误，请检查你的网络！");
                    console.log("WebSocketError!");
                };

                /**
                 * 发送消息
                 * @returns {undefined}
                 */
                var date = new Date();
                var before_send_time = 0;
                var now_time = date.getTime();
                function sendMessage() {
                    //聊天内容不能为空
                    var messageContent = $("#messageContent").val();
                    if (!messageContent) {
                        return false;
                    }

                    //进行发言时间限制
                    date = new Date();
                    now_time = date.getTime();
                    var time_difference = now_time - before_send_time;
                    if (time_difference < 3000) {
                        alert("发言间隔不能少于3秒");
                        return false;
                    }

                    //发送消息
                    var data = '{"room":"' +<?php echo $room_num ?> + '","type":"msg","user":"' + username + '","msg":"' + messageContent + '"}';
                    ws.send(data);
                    $("#messageContent").val('');
                    before_send_time = date.getTime();
                }
                /**
                 * 让聊天窗口每次滚动到最末尾
                 * @returns {undefined}
                 */
                function scrollBottom() {
                    $('.chat-thread').scrollTop($('.chat-thread')[0].scrollHeight);
                }

                /**
                 * 键盘监听事件
                 * @param {type} param1
                 * @param {type} param2
                 */
                $('#messageContent').bind('keyup', function (event) {
                    if (event.keyCode == "13") {//enter
                        //回车执行查询
                        sendMessage();
                    }
                    if (event.keyCode == "16") {//F5
                        return "刷新该页面会退出重新进入聊天室，确定要这样吗？";
                    }
                });

                /**
                 * 退出聊天室监听事件
                 */
                $(window).bind('beforeunload', function () {
                    return "你确定要这样离开大家吗？";
                });
        </script>

    </body>
</html>