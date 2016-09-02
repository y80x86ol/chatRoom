<?php
$room = require_once 'config/room.php';

if (!isset($_POST['room_num']) || !isset($room[$_POST['room_num']])) {
    echo '错误请求来源!';
    die();
}
$username = substr(trim($_POST['username']), 0, 15);
$room_num = $_POST['room_num'];
$room_name = $room[$room_num]['name'];
$room_description = $room[$room_num]['description'];

$avatar = $_POST['avatar'];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Standard Meta -->

        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

        <!-- Site Properties -->
        <title>聊天室</title>
        <link rel="stylesheet" type="text/css" href="public/semantic/semantic.css">
        <link rel="stylesheet" type="text/css" href="public/css/chat.css">

        <script src="public/jquery.js"></script><style type="text/css"></style>
        <script src="public/semantic/semantic.js"></script>

    </head>
    <body>
        <div class="ui container">
            <div class="ui segments">
                <div class="ui segment"><i class="home icon"></i>欢迎来到<span class="color-red"><?php echo $room_name ?></span>聊天室</div>
                <div class="ui segment">
                    <div class="ui grid">
                        <div class="twelve wide computer three wide tablet six wide mobile column">
                            <div class="ui segment">
                                <div class="ui comments" id="chat-body">
                                    <!--聊天内容-->
                                </div>
                            </div>
                            <div class="ui fluid action input">
                                <input type="text" placeholder="聊一聊" class="messageContent" id="messageContent">
                                <div class="ui button" onclick="sendMessage()" data-content="发送消息间隔不小于三秒">发送</div>
                            </div>
                        </div>
                        <div class="four wide column">
                            <div class="ui segment">
                                <div class="ui relaxed divided list">
                                    <div class="item">
                                        <img class="ui mini circular image" src="public/images/avatar/<?php echo $avatar ?>.jpg">
                                        <div class="content">
                                            <div class="ui sub header"><?php echo $username; ?></div>在线<span id="user-online-time"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ui segment">
                                <p>当前在线 <span id="chat-online-num"></span>人</p>
                                <div class="ui relaxed divided list" id="chat-online-list">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ui segment">底部</div>
            </div>
        </div>
        <script>
            /**
             * 用户名校验检查
             * @type type
             */
            var username = '<?php echo $username; ?>';
            if (!username) {
                alert("请输入用户名");
                window.location = 'index.php';
            }
            var uid = 0; //用户ID
            var avatar = '<?php echo $avatar ?>'; //用户头像
            var date = new Date();
            var login_time = date.getTime(); //用户登录时间

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
                var data = '{"room":"' +<?php echo $room_num ?> + '","type":"connect","user":"' + username + '","msg":"系统消息：' + username + '进入了聊天室","avatar":"' + avatar + '"}';
                ws.send(data);
            };
            //客户端接收服务端数据时触发
            ws.onmessage = function (evt)
            {
                console.log(evt);
                if (evt.type == 'message') {
                    var data = JSON.parse(evt.data);
                    var html = get_chat_body(data);
                    $("#chat-body").append(html);
                    scrollBottom();
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
                    //alert("发言间隔不能少于3秒");
                    $('.button').popup("show");
                    return false;
                }

                //发送消息
                var data = '{"room":"' +<?php echo $room_num ?> + '","type":"msg","user":"' + username + '","msg":"' + messageContent + '","avatar":"' + avatar + '"}';
                ws.send(data);
                $("#messageContent").val('');
                before_send_time = date.getTime();
            }

            function get_chat_body(chat_info) {
                //精简聊天记录
                clearChatBody()
                //获取模板内容
                var chat_template = '';
                if (chat_info.type == 'info') {
                    chat_template = '<span class="color-green"><i class="alarm outline icon"></i>' +
                            chat_info.msg +
                            '</span><div class="clear"></div>';
                }

                if (chat_info.type == 'connect') {
                    chat_template = '<span class="color-green"><i class="alarm outline icon"></i>' +
                            chat_info.msg +
                            '</span><div class="clear"></div>';
                    apiGetCount();
                }

                if (chat_info.type == 'notice') {
                    chat_template = '<span class="color-red"><i class="alarm outline icon"></i>' +
                            chat_info.msg +
                            '</span><div class="clear"></div>';
                    alert(chat_info.msg);
                }

                if (chat_info.type == 'msg') {
                    chat_template = '<div class="comment">' +
                            '<a class="avatar">' +
                            '<img src="public/images/avatar/' + chat_info.avatar + '.jpg">' +
                            '</a>' +
                            '<div class="content">' +
                            '<a class="author">' + chat_info.user + ' 说：</a>' +
                            '<div class="text">' + chat_info.msg + '</div>' +
                            '</div>'
                    '</div>';
                }


                return chat_template;
            }


            /**
             * 让聊天窗口每次滚动到最末尾
             * @returns {undefined}
             */
            function scrollBottom() {
                $('#chat-body').scrollTop($('#chat-body')[0].scrollHeight);
            }

            /**
             * 清除部分老的聊天记录
             * @returns {undefined}             
             **/
            function clearChatBody() {
                var chat_body_length = $('#chat-body')[0].scrollHeight;
                if (chat_body_length > 3000) {
                    $('#chat-body').empty();
                }
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

        <script>
            /**
             * 通过ajax来获取用户列表和房间人数
             */
            function apiGetCount() {
                $.ajax({
                    type: 'get',
                    url: 'apiGetCount.php',
                    dataType: 'json',
                    data: {"room": "<?php echo $room_num ?>"},
                    success: function (data) {
                        if (data.error == '0') {
                            console.log(data.user_list);
                            //修改在线聊天人数
                            $("#chat-online-num").text(data.room_count);
                            //修改用户列表
                            if (data.user_list) {
                                var user_list = '';
                                for (i in data.user_list) {
                                    if (data.user_list[i].username == '<?php echo $username ?>') {
                                        $("#user-online-time").text(data.user_list[i].online_time);
                                    }
                                    user_list +=
                                            '<div class="item">' +
                                            '<img class="ui mini circular image" src="public/images/avatar/' + data.user_list[i].avatar + '.jpg">' +
                                            '<div class="content">' +
                                            '<div class="ui sub header">' + data.user_list[i].username + '</div>在线' + data.user_list[i].online_time + '</div>' +
                                            '</div>';
                                }
                                $("#chat-online-list").html(user_list);
                            }
                        }
                    }
                });
            }
            window.setInterval('apiGetCount()', 60000);
        </script>
    </body>
</html>