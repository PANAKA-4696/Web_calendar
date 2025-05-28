<?php
session_start();
if(isset($_POST) && ($_POST)){
    $date = $_POST["date"];
    $before_time = $_POST["before_time"];
    $after_time = $_POST["after_time"];
    $content = $_POST["content"];
    $place = $_POST["place"];
    $url = $_POST["url"];
    //$file = $_POST["file"];
    $memo = $_POST["memo"];

    //セッション内にユーザーが送信してきたデータを格納しておく
    $_SESSION["date"] = $date;
    $_SESSION["before_time"] = $before_time;
    $_SESSION["after_time"] = $after_time;
    $_SESSION["content"] = $content;
    $_SESSION["place"] = $place;
    $_SESSION["url"] = $url;
    //$_SESSION["file"] = $file;
    $_SESSION["memo"] = $memo;

    $errors = [];//エラーがあったときに格納するための配列
    $not_input_after = 0;//内容がないときに後で消すための処理を行うための変数
    $not_input_plase = 0;
    $not_input_url = 0;
    $not_input_file = 0;
    $not_input_memo = 0;

    //カレンダーページに戻す
    if(isset($_GET["back"]) && ($_GET["back"] == 1)){
        //クエリパラメータでbackが送られてきているかつ、その値が1である場合に実行
        session_destroy();//セッションデータを消去する
        header("Location:index.php");//カレンダーページに戻す
        exit();//プログラム終了
    }
    //予定フォーム画面に戻す
    if(isset($_GET["back"]) && ($_GET["back"] == 2)){
        //クエリパラメータでbackが送られてきているかつ、その値が1である場合に実行
        session_destroy();//セッションデータを消去する
        header("Location:plan.php?date='$date'");//予定フォームに戻す
        exit();//プログラム終了
    }

    //ファイルが入っているときと入っていないときの処理(生成AI使用)
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        /*
        $tmp_name = $_FILES['file']['tmp_name'];
        $name = $_FILES['file']['name'];
        ファイルのアップロード処理 (例: サーバーに保存)
        move_uploaded_file($tmp_name, 'uploads/' . $name);
        $file_path = 'uploads/' . $name; // 保存されたファイルのパス
        $_SESSION["file"] = $file_path;
        */
        $file = $_POST["file"];
        $_SESSION["file"] = $file;
    }else{
        $file = '内容が記入されていません';
        $not_input_file += 1;
    }

    //予定内容が記入されていなければエラー
    if(strlen($content) < 1){
        $errors["content"] = '予定内容が入力されていません';
    }
    //開始時間が記入されていなければエラー
    if(strlen($before_time) < 1){
        //strlen()→文字列の長さを返す関数
        $errors["before_time"] = '時間が記入されていません';
    }

    //内容が入っていないとき内容がないことを出力
    if(strlen($after_time) < 1){
        $after_time = '内容が記入されていません';
        $not_input_after += 1;
    }
    if(strlen($place) < 1){
        $place = '内容が記入されていません';
        $not_input_plase += 1;
    }
    if(strlen($url) < 1){
        $url = '内容が記入されていません';
        $not_input_url += 1;
    }
    
    /*
    if(strlen($file) < 1){
        $file = '内容が記入されていません';
        $not_input_file += 1;
    }
    */

    if(strlen($memo) < 1){
        $memo = '内容が記入されていません';
        $not_input_memo += 1;
    }

    if(count($errors) > 0){
        //エラ(ーが一個以上ある=どこかエラーがある
        $_SESSION["errors"] = $errors;
        header("Location:plan.php?date=$date");//前のページに戻す
        exit();
    }
    //以下確認画面を作成して表示
    ?>
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $date; ?>予定追加-確認画面-</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
        <style>
        .container {
            font-family: 'Noto Sans', sans-serif;/*--GoogleFontsを使用--*/
            margin-top: 50px;
            width: 80%;
        }
        .center-block {
            margin: 0 auto;
            width: 470px;
        }
        .container button {
            position: absolute;
            right: 270px;
            top: 590px;
        }
        .container h4{
            font-weight: bold;
            font-size: 25px;
        }
        .container li{
            font-size: 15px;
        }
        </style>
    </head>
    <body>
        <main>
            <div class="container">
                <p><a href="index.php?back=1">カレンダーページに戻る</a></p>
                <p><a href="plan.php?date=<?php echo $date; ?>">予定フォーム画面に戻る</a></p>
                <h1><?php echo $date; ?></h1>
                <h3><?php echo $date; ?>-確認画面-</h3>
                <table class="center-block">
                    <tr>
                        <td>
                            <ul>
                                <h4 style="color:crimson;">開始時間</h4>
                                <li><?php print(htmlspecialchars($before_time,ENT_QUOTES)); ?></li>
                            </ul>
                            <ul>
                                <h4 style="color:steelblue;">終了時間</h4>
                                <li><?php print(htmlspecialchars($after_time,ENT_QUOTES)); ?></li>
                            </ul>
                            <ul>
                                <h4 style="color:crimson;">予定内容</h4>
                                <li><?php print(htmlspecialchars($content,ENT_QUOTES)); ?></li>
                            </ul>
                        </td>
                        <td>　　　　　</td>
                        <td>
                            <ul>
                                <h4 style="color:steelblue;">場所</h4>
                                <li><?php print(htmlspecialchars($place,ENT_QUOTES)); ?></li>
                            </ul>
                            <ul>
                                <h4 style="color:steelblue;">URL</h4>
                                <li><?php print(htmlspecialchars($url,ENT_QUOTES)); ?></li>
                            </ul>
                            <ul>
                                <h4 style="color:steelblue;">ファイル</h4>
                                <li><?php print($file); ?></li>
                            </ul>
                            <ul>
                                <h4 style="color:steelblue;">メモ</h4>
                                <li><?php print(nl2br(htmlspecialchars(trim($memo),ENT_QUOTES))); ?></li>
                            </ul>
                        </td>
                    </tr>
                </table>
                <br>
                <p><button onclick="location.href='complete.php'">予定追加を確定する</button></p>
            </div>
        </main>
    </body>
    </html>
<?php
if($not_input_after = 1){
    $after_time = '';
}
if($not_input_plase = 1){
    $place = '';
}
if($not_input_url = 1){
    $url = '';
}
if($not_input_file = 1){
    $file = '';
}
if($not_input_memo = 1){
    $memo = '';
}
}
?>