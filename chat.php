<?php
session_start();
?>
<!DOCTYPE html>
<head>
    <title>luosilent聊天室</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="user-scalable=no"/>
    <script src="js/jquery-3.3.1.js"></script>
    <link rel="stylesheet" href="css/style.css" media="screen" type="text/css"/>
    <script>
        var uid = '<?php echo $_SESSION['uid'] ?>';
        $(document).ready(function () {
            function bottom() {
                var cs = document.getElementById("chatshow");
                cs.scrollTop = cs.scrollHeight;
            }

            $("#post").click(function () {
                postMsg();
            });
            $(document).keypress(function (e) {
                if (e.which == 13) {
                    e.preventDefault();
                    postMsg();
                }
            });

            function postMsg() {
                var content = $("#content").val();
                if (!$.trim(content)) {
                    alert('请填写内容');
                    return false;
                }
                $("#content").val("");
                $.post("Control/post.php", {"content": content})
            }

            $(".close").click(function () {
                if (confirm("您确定要关闭本页吗？")) {
                    $.ajax({
                        url: "Control/logout.php",
                        type: "post",
                        data: {"uid": uid},
                        success: function (res) {
                            var obj = JSON.parse(res);
                            if (obj.code == 0) {
                                window.location.reload();
                            } else if (obj.code == 1) {
                                alert(obj.msg);
                            }
                        }
                    });
                }
            });

            function getData(msg) {
                $.ajax({
                    url: "Control/get.php",
                    type: "post",
                    data: {"msg": msg},
                    success: function (data) {
                        if (data) {
                            // console.log(data);
                            var obj = JSON.parse(data);
                            var chatcontent = '';
                            $.each(obj, function (key, val) {
                                var ptime = val['post_time'];
                                if (ptime === undefined) {
                                    var nowDate = new Date();
                                    ptime = nowDate.toLocaleString();
                                }
                                var dTime = timeFn(ptime);
                                if (val['uid'] === uid) {
                                    if (dTime > 3) {
                                        chatcontent += "<li class='time'>" + ptime + "</li>" + "<li class='right'>" + val['content'] + "</li>";
                                    }
                                    else {
                                        chatcontent += "<li class='right'>" + val['content'] + "</li>";
                                    }

                                } else {
                                    if (dTime > 3) {
                                        chatcontent += "<li class='time'>" + ptime + "</li>" + "<li class='left'>" + val['username'] + "：" + val['content'] + "</li>";
                                    }
                                    else {
                                        chatcontent += "<li class='left'>" + val['username'] + "：" + val['content'] + "</li>";
                                    }

                                }
                            });
                            $("#chatshow").html(chatcontent);
                            bottom();
                        }
                        getData("");
                    },
                    error:function () {
                        console.log(1)
                    }
                });
            }

            getData("one");

            function timeFn(d) {
                var dateBegin = new Date(d.replace("-", "/"));//将-转化为/，使用new Date
                var dateEnd = new Date();//获取当前时间
                var dateDiff = dateEnd.getTime() - dateBegin.getTime();//时间差的毫秒数
                var dayDiff = Math.floor(dateDiff / (24 * 3600 * 1000));//计算出相差天数
                var leave1 = dateDiff % (24 * 3600 * 1000);    //计算天数后剩余的毫秒数
                var hours = Math.floor(leave1 / (3600 * 1000));//计算出小时数
                //计算相差分钟数
                var leave2 = leave1 % (3600 * 1000);   //计算小时数后剩余的毫秒数
                minutes = Math.floor(leave2 / (60 * 1000));//计算相差分钟数

                return minutes;
            }

            $("#userlist p").click(function () {
                $("#content").val("@" + $(this).text() + " ");
            });
        });

    </script>
</head>
<body>
<div id="main">
    <div id="userlist">
        <h1>在线用户</h1>
        <div>
            <?php
            require 'Connect/conn.php';
            $conn = conn();
            $sql = "select * from member where islogin = :islogin";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':islogin', '1');
            $res = $stmt->execute();
            if ($res) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<p>' . $row['username'] . '</p>';
                }
            }
            ?>
        </div>
        <h1>离线用户</h1>
        <div>
            <?php
            $sql = "select * from member where islogin = :islogin";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':islogin', '0');
            $res = $stmt->execute();
            if ($res) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<p>' . $row['username'] . '</p>';
                }
            }
            ?>
        </div>
    </div>
    <div class="message" style="height: 800px">
        <span class="close"></span>
        <ul class="chat-thread" id="chatshow">
        </ul>
        <div style="margin-top: 20px;">
            <textarea name="content" id="content"></textarea>
        </div>
        <span id="post">发布</span>
    </div>
</div>

</body>
</html>