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

//もしセッションデータがなければ、最初のページに飛ばす
if(count($_SESSION) == 0){
    //セッションデータが0となっている→想定経路と異なる
    header("Location:plan.php?date=$date");//記入画面に移動させる
    exit();
}

//セッションからデータを取り出す(セッションのデータは基本的に改変が不可能なので、チェックされているならそのままでOK)
$date = $_SESSION["date"];
$before_time = $_SESSION["before_time"];
$after_time = $_SESSION["after_time"];
$content = $_SESSION["content"];
$place = $_SESSION["place"];
$url = $_SESSION["url"];
//$file = $_SESSION["file"];
$memo = $_SESSION["memo"];

/*
print($date);
print($before_time);
print($after_time);
print($content);
print($place);
print($url);
print($memo);
//echo $file;
*/

//任意のものの値をNULLにする
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK){
}else{
    $file = NULL;//変数fileの値をNULLにする
}
if(strlen($after_time) < 1){
    $after_time = NULL;
}
if(strlen($place) < 1){
    $place = NULL;
}
if(strlen($url) < 1){
    $url = NULL;
}
if(strlen($memo) < 1){
    $memo = NULL;
}
//ID,date,before_time,after_time,content,place,url,memo,file
$sql = 'INSERT INTO plan VALUES(NULL,?,?,?,?,?,?,?,?)';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($date, $before_time, $after_time, $content, $place, $url, $file, $memo));

//セッションのデータを消去する
session_destroy();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $date; ?>予定追加</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
        <style>
            .container {
                font-family: 'Noto Sans', sans-serif;/*--GoogleFontsを使用--*/
                margin-top: 80px;
                width: 80%;
            }
            h1{
                right: 300px;
            }
        </style>
</head>
<body>
    <main>
        <div class="container">
            <h1>予定追加-完了-</h1>
            <p style="color:crimson;">予定の追加が完了しました</p>
        </div>
    </main>
    <div class="container">
        <p><button onclick="location.href='index.php'">カレンダーページに戻る</button></p>
    </div>
</body>
</html>