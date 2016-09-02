<?php
$room_config = require_once 'config/room.php';
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
                <div class="ui segment">

                    <div class="twelve wide computer three wide tablet six wide mobile column">
                        <form class="ui form" method="post" action="chatroom.php">
                            <div class="field">
                                <label>用户名</label>
                                <input type="text" name="username" placeholder="给自己起一个响亮的名字" minlength="6" maxlength="8" required="">
                            </div>
                            <div class="field">
                                <label>选择聊天室</label>
                                <select class="ui fluid dropdown" name='room_num'>
                                    <?php foreach ($room_config as $num => $item): ?>
                                        <option value="<?php echo $num ?>"><?php echo $item['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="ui form">
                                <div class="inline fields">
                                    <label for="fruit">选择一个头像:</label>
                                    <div class="field">
                                        <div class="ui radio checkbox">
                                            <input type="radio" name="avatar" value="1" checked="" tabindex="0" class="hidden">
                                            <label><img class="ui mini image" src="public/images/avatar/1.jpg"></label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui radio checkbox">
                                            <input type="radio" name="avatar" value="2" tabindex="0" class="hidden">
                                            <label><img class="ui mini image" src="public/images/avatar/2.jpg"></label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui radio checkbox">
                                            <input type="radio" name="avatar" value="3" tabindex="0" class="hidden">
                                            <label><img class="ui mini image" src="public/images/avatar/3.jpg"></label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui radio checkbox">
                                            <input type="radio" name="avatar" value="4" tabindex="0" class="hidden">
                                            <label><img class="ui mini image" src="public/images/avatar/4.jpg"></label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui radio checkbox">
                                            <input type="radio" name="avatar" value="5" tabindex="0" class="hidden">
                                            <label><img class="ui mini image" src="public/images/avatar/5.jpg"></label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui radio checkbox">
                                            <input type="radio" name="avatar" value="6" tabindex="0" class="hidden">
                                            <label><img class="ui mini image" src="public/images/avatar/6.jpg"></label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui radio checkbox">
                                            <input type="radio" name="avatar" value="7" tabindex="0" class="hidden">
                                            <label><img class="ui mini image" src="public/images/avatar/7.jpg"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="field">
                                <div class="ui checkbox">
                                    <input type="checkbox" tabindex="0" class="" required="">
                                    <label>我同意遵守国家法律法规进行聊天</label>
                                </div>
                            </div>
                            <button class="ui button" type="submit">进入聊天室</button>

                        </form>
                    </div>

                </div>
                <div class="ui segment">底部</div>
            </div>
        </div>
        <script>
            $('.ui.radio.checkbox').checkbox();
        </script>
    </body>
</html>