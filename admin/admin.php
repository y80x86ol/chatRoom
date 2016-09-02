<?php
$room_config = require_once '../config/room.php';

$username = "管理员";
$room_num = $_POST['room_num'];

if (!$room_num) {
    die("来源错误");
}

$room_name = $room_config[$room_num]['name'];
$room_description = $room_config[$room_num]['description'];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Standard Meta -->

        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

        <!-- Site Properties -->
        <title>管理后台-聊天室</title>
        <link rel="stylesheet" type="text/css" href="../public/semantic/semantic.css">
        <link rel="stylesheet" type="text/css" href="../public/css/chat.css">

        <script src="../public/jquery.js"></script><style type="text/css"></style>
        <script src="../public/semantic/semantic.js"></script>

    </head>
    <body>
        <div class="ui container">
            <div class="ui segments">
                <div class="ui segment"><i class="home icon"></i>当前：<span class="color-red"><?php echo $room_name ?></span>聊天室</div>
                <div class="ui segment">
                    <div class="twelve wide computer three wide tablet six wide mobile column">
                        <div class="ui fluid action input">
                            <input type="text" placeholder="发送公告" class="messageContent" id="messageContent">
                            <div class="ui button" onclick="sendMessage()">发送</div>
                        </div>
                        <div class="ui relaxed divided list" id="chat-online-list">
                        </div>
                    </div>
                </div>
                <div class="ui segment">底部</div>
            </div>
        </div>
        <script>
            /**
             * 建立连接
             * @type WebSocket
             */
            var ws = new WebSocket("ws://localhost:2345");
            //连接建立时触发
            ws.onopen = function ()
            {
                //不发送任何消息
                var data = '{"room":"<?php echo $room_num ?>","type":"connect","user":"<?php echo $username ?>","msg":"系统消息：管理员进入聊天室","avatar":"10000"}';
                ws.send(data);
            };
            //客户端接收服务端数据时触发
            ws.onmessage = function (evt)
            {
                console.log(evt);
            };
            //连接关闭时触发
            ws.onclose = function (evt)
            {
                alert("对不起，聊天室已经被关闭了，请立即检查PHP server是否正常运行！");
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
            function sendMessage() {
                var messageContent = $("#messageContent").val();
                if (!messageContent) {
                    return false;
                }
                var data = '{"room":"<?php echo $room_num ?>","type":"notice","user":"<?php echo $username ?>","msg":"系统公告：' + messageContent + '","avatar":"10000"}';
                ws.send(data);
                $("#messageContent").val('');
                $(".messageState").html("消息发送成功！");
            }

            /**
             * 回车键监听事件
             * @param {type} param1
             * @param {type} param2
             */
            $('#messageContent').bind('keyup', function (event) {
                if (event.keyCode == "13") {
                    //回车执行查询
                    sendMessage();
                }
            });

            /**
             * 输入内容时，暂时清除消息状态
             * @param {type} param
             */
            $("#messageContent").focus(function () {
                $(".messageState").html('');
            });

            function chat_out(obj) {
                var data_username = $(obj).attr('data-username');
                var data = '{"room":"<?php echo $room_num ?>","type":"msg","user":"' + data_username + '","msg":"系统消息：' + data_username + '被踢出聊天室","avatar":"1"}';
                ws.send(data);
            }

        </script>
        <script>
            /**
             * 通过ajax来获取用户列表和房间人数
             */
            apiGetCount();
            function apiGetCount() {
                $.ajax({
                    type: 'get',
                    url: '../apiGetCount.php',
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
                                    user_list +=
                                            '<div class="item">' +
                                            '<img class="ui mini circular image" src="../public/images/avatar/' + data.user_list[i].avatar + '.jpg">' +
                                            '<div class="content">' +
                                            '<div class="ui sub header">' + data.user_list[i].username + '</div>在线' + data.user_list[i].online_time + '<button class="ui button" onclick="chat_out(this)" data-username="' + data.user_list[i].username + '">踢</button></div>' +
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