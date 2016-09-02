<?php
$room_config = require_once '../config/room.php';
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
                <div class="ui segment">
                    <div class="twelve wide computer three wide tablet six wide mobile column">
                        <form class="ui form" method="post" action="admin.php">
                            <div class="field">
                                <label>选择聊天室</label>
                                <select class="ui fluid dropdown" name='room_num'>
                                    <?php foreach ($room_config as $num => $item): ?>
                                        <option value="<?php echo $num ?>"><?php echo $item['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button class="ui button" type="submit">管理聊天室</button>

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