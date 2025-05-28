<?php
$date = $_GET['date'];
//予定追加フォームから送られてきた内容をすべて表示する

include 'db_define.php';
session_start();//セッションを利用可能にする

if(isset($_GET["back"]) && ($_GET["back"] == 1)){
    //クエリパラメータでbackが送られてきているかつ、その値が1である場合に実行
    session_destroy();//セッションデータを消去する
    header("Location:index.php");//カレンダーページに戻す
    exit();//プログラム終了
}

/*
if(!isset($_SESSION["move"]) || ($_SESSION["move"]) != 1){
    //isset($_SESSION["move"])で$_SESSION["move"]が初期化されているかどうかを判定する
    //値が1ではない→このページに訪れているのがおかしい
    header("Location:index.php");//カレンダーページに戻す
    exit();//プログラム終了
}
*/
$dsn = 'mysql:dbname='.DBNAME.';host='.DBHOST.';port='.DBPORT;
try{
    $dbh = new PDO($dsn,DBUSER,DBPASS);
}
catch(PDOException $e){
    print('DBに接続できません'.$e->getMessage());
    exit();
}
$sql = "SELECT * FROM plan WHERE date = '$date' ORDER BY before_time ASC";//日付が選んだ日付で開始時間の昇順に並ぶようにする
$stmt = $dbh->prepare($sql);
$stmt->execute();//SQL文を発行
$output = '';
while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
    $output .= '<tr>';
    $output .= '<td>';
    $output .= '<div class="item">';
    $output .= '<p style="color:crimson;">予定番号:'.escTag($result["id"]).'</p>';
    //substr($input_str, 0, -3)
    $output .= '<p>開始時間:'.escTag(substr($result["before_time"], 0, -3)).'</p>';
    if(isset($result["after_time"]) || ($result["after_time"])){
        $output .= '<p>終了時間:'.escTag(substr($result["after_time"], 0, -3)).'</p>';
    }
    $output .= '<p>予定内容:'.escTag($result["content"]).'</p>';
    if(isset($result["place"]) || ($result["place"])){
        $output .= '<p>場所:'.escTag($result["place"]).'</p>';
    }
    if(isset($result["url"]) || ($result["url"])){
        $output .= '<p>URL:'.escTag($result["url"]).'</p>';
    }
    if(isset($result["file"]) || ($result["file"])){
        $output .= '<p>ファイル:'.escTag($result["file"]).'</p>';
    }
    if(isset($result["memo"]) || ($result["memo"])){
        $output .= '<p>メモ:'.escTag($result["memo"]).'</p>';
    }
    //$delete = "DELETE FROM plan WHERE 'plan'.'id' = ".escTag($result["id"])."";
    //$mtst = $dbh->prepare($delete);
    //$output .= "<p><button onclick=".$mtst->execute();">予定を削除する</p>";
    $output .= "<label for=\"id\"></label>";
    $output .= "<input type=\"hidden\" name=\"id\" value=".$result["id"].">";
    $output .= "<p><input type=\"submit\" value=\"予定内容を削除\"></p>";

    $output .= "</div>";
    $output .= "</tr>";
    $output .= '</td>';
    /*
    <label for="date"></label>
    <input type="hidden" name="date" value="<?php echo $date; ?>">
    <label for="id"></label>
    <input type="hidden" name="id" value="<?php echo $result["id"]; ?>">
    */
}
if($output == ''){//$outputが空→つまりまだ何も予定がない状況を考慮しておく
    $output .= '<div class="item"><p>まだ何も予定がありません</P></div>';
}

function escTag($html){
    //htmlspecialchars()の省略形の作成
    return htmlspecialchars($html,ENT_QUOTES);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予定表示</title>
</head>
<body>
    <main>
        <p><a href="index.php?back=1">カレンダーページに戻る</a></p>
        <h1><?php echo $date; ?>予定内容</h1>
        <form method="POST" action="delete.php" enctype="multipart/form-data">
            <label for="date"></label>
            <input type="hidden" name="date" value="<?php echo $date; ?>">
            <table>
                <?php print($output); ?>
            </table>
        </form>
    </main>
</body>
</html>