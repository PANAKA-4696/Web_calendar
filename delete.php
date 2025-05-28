<?php
session_start();

include 'db_define.php';
$dsn = 'mysql:dbname='.DBNAME.';host='.DBHOST.';port='.DBPORT;

try{
    $dbh = new PDO($dsn,DBUSER,DBPASS);
}
catch(PDOException $e){
    print('DBに接続できません'.$e->getMessage());
    exit();
}

if(isset($_POST) && ($_POST)){
    $date = $_POST["date"];
    $id = $_POST["id"];

    $errors = [];
    
    //カレンダーページに戻す
    if(isset($_GET["back"]) && ($_GET["back"] == 1)){
        //クエリパラメータでbackが送られてきているかつ、その値が1である場合に実行
        session_destroy();//セッションデータを消去する
        header("Location:index.php");//カレンダーページに戻す
        exit();//プログラム終了
    }

    if(strlen($date) < 1){
        $errors["date"] = 'errorが検出されました';
    }
    if(strlen($id) < 1){
        $errors["id"] = 'errorが検出されました';
    }

    if(count($errors) > 0){
        //エラ(ーが一個以上ある=どこかエラーがある
        $_SESSION["errors"] = $errors;
        header("Location:plan_table.php?date=$date");//前のページに戻す
        exit();
    }

    $sql = 'DELETE FROM plan WHERE id = ?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));

    //セッションのデータを消去する
    session_destroy();

    ?>

    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $date; ?>予定番号<?php echo $id; ?>削除</title>
    </head>
    <body>
        <main>
            <div class="container">
                <h1>削除-完了-</h1>
                <p style="color:crimson;">予定の削除が完了しました</p>
            </div>
        </main>
        <div class="container">
            <p><button onclick="location.href='index.php'">カレンダーページに戻る</button></p>
        </div>
    </body>
    </html>
<?php
}
?>