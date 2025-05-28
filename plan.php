<?php
$date = $_GET['date'];
session_start();
$before_time = '';
$after_time = '';
$content = '';
$place = '';
$url = '';
$file = '';
$memo = '';
$errors = [];

//カレンダーページに戻す
if(isset($_GET["back"]) && ($_GET["back"] == 1)){
    //クエリパラメータでbackが送られてきているかつ、その値が1である場合に実行
    session_destroy();//セッションデータを消去する
    header("Location:index.php");//カレンダーページに戻す
    exit();//プログラム終了
}

/*
if(isset($_SESSION["date"])){
    $date = $_SESSION["date"];
}
*/

if(isset($_SESSION["before_time"])){
    $before_time = $_SESSION["before_time"];
}
if(isset($_SESSION["after_time"])){
    $after_time = $_SESSION["after_time"];
}
if(isset($_SESSION["content"])){
    $content = $_SESSION["content"];
}
if(isset($_SESSION["place"])){
    $place = $_SESSION["place"];
}
if(isset($_SESSION["url"])){
    $url = $_SESSION["url"];
}
if(isset($_SESSION["file"])){
    $file = $_SESSION["file"];
}
if(isset($_SESSION["memo"])){
    $memo = $_SESSION["memo"];
}
if(isset($_SESSION["errors"])){
    $errors = $_SESSION["errors"];
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $date; ?>予定</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <style>
    .container {
        font-family: 'Noto Sans', sans-serif;/*--GoogleFontsを使用--*/
        margin-top: 80px;
        width: 80%;
    }
    h1 {
        margin-bottom: 30px;
    }
    .center-block {
        margin: 0 auto;
        width: 470px;
    }
    .container input[type="submit"] {
        position: absolute;
        right: 300px;
    }
    </style>
</head>
<body>
    <main>
        <div class="container">
            <p><a href="index.php?back=1">カレンダーページに戻る</a></p>
            <h1><?php echo $date; ?></h1>
            <h3><?php echo $date; ?>予定フォーム</h3>
            <?php
            //エラーメッセ―ジがあるならここに表示する
            if(count($errors) > 0){
                foreach($errors as $error){
                    //エラーの配列から1つずつエラー内容を取り出して処理する
                    print('<p style="color:red;">'.$error.'</p>');
                }
            }
            ?>
            <form method="POST" action="confirm.php" enctype="multipart/form-data">
                <br>
                <table class="center-block">
                    <tr>
                        <td>
                            <ul>
                                <li><label for="before_time" style="color: crimson;">開始時間<small>必須</small></label></li>
                                <li><input type="time" name="before_time" placeholder="13:00" value="<?php echo $before_time; ?>" id="before_time"></li>
                            </ul>
                            <ul>
                                <li><label for="after_time" style="color:steelblue;">終了時間</label></li>
                                <li><input type="time" name="after_time" placeholder="20:00" value="<?php echo $after_time; ?>" id="after_time"></li>
                            </ul>
                            <ul>
                                <li><label for="content" style="color: crimson;">予定内容<small>必須</small></label></li>
                                <li><input type="text" name="content" placeholder="内容を入力" value="<?php echo $content; ?>" id="content"></li>
                            </ul>
                        </td>
                        <td>
                            <ul>
                                <li><label for="place" style="color:steelblue;">場所</label></li>
                                <li><input type="text" name="place" placeholder="日本工学院専門学校蒲田校" value="<?php echo $place; ?>" id="place"></li>
                            </ul>
                            <ul>
                                <li><label for="url" style="color:steelblue;">URL</label></li>
                                <li><input type="url" name="url" placeholder="URLを記述" value="<?php echo $url; ?>"></li>
                            </ul>
                            <ul>
                                <li><label for="file" style="color:steelblue;">ファイル</label></li>
                                <li><input type="file" name="file" value="<?php echo $file; ?>"></li>
                            </ul>
                            <ul>
                                <li><label for="memo" style="color:steelblue;">メモ</label></li>
                                <li><input type="text" name="memo" placeholder="メモの内容を記載" value="<?php echo $memo; ?>"></li>
                            </ul>
                            <ul>
                                <label for="date"></label>
                                <input type="hidden" name="date" value="<?php echo $date; ?>">
                            </ul>
                        </td>
                    </tr>
                </table>
                <p><input type="submit" value="予定を追加"></p>
            </form>
        </div>
    </main>
</body>
</html>