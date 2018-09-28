<?php
require '../Connect/conn.php';
set_time_limit(0);
if ($_POST['msg'] == "one") {
    $data = getContent();
    if ($data == "[]") {
        $con[] = array(
            'username' => "系统消息",
            'content' => "欢迎来到luosilent聊天室"
        );
        $data = json_encode($con);
    }
    exit($data);
}

$old = getData();

while (true) {
    $new = getData();
    if ($new > $old) {
        $newdata = getContent();
        print_r($newdata);
        break;
    }
    usleep(10000);
}

function getContent()
{
    $conn = conn();
    $sql = "SELECT c.*,u.username FROM talkroom c LEFT JOIN member u ON c.uid = u.id ORDER BY c.id ASC";
    $stmt = $conn->prepare($sql);
    $res = $stmt->execute();
    $data[] = array(
        'username' => "系统消息",
        'content' => "欢迎来到luosilent聊天室"
    );
    if ($res) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = array(
                'username' => $row['username'],
                'content' => $row['content'],
                'uid' => $row['uid']
            );
        }
    }else{
        $data = array();
    }

    return json_encode($data);
}

function getData()
{
    $conn = conn();
    $sql = "SELECT count(*) FROM talkroom";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetch();

    return $rows[0];

}