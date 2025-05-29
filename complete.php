<?php
session_start();

// セッションデータがない（直接アクセスなど）場合は、plan.phpにリダイレクト
if(!isset($_SESSION["date"]) || !isset($_SESSION["content"])){ // 主要なデータがあるか確認
    header("Location: plan.php"); // 日付不明ならplan.phpの初期へ
    exit();
}

include 'db_define.php'; // DB接続情報
$dsn = 'mysql:dbname='.DBNAME.';host='.DBHOST.';port='.DBPORT.';charset=utf8';

try{
    $dbh = new PDO($dsn, DBUSER, DBPASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーモードを例外に設定
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // SQLインジェクション対策
}
catch(PDOException $e){
    // 本番環境では詳細なエラーメッセージの表示は避ける
    error_log('DB Connection Error: ' . $e->getMessage());
    // ユーザーには汎用的なエラーメッセージを表示
    $user_error_message = "データベース接続に失敗しました。しばらくしてから再度お試しください。";
    // ここでは簡略化のため直接表示するが、実際にはエラーページにリダイレクトなど
    // exit($user_error_message);
    // 今回は元のコードに合わせて表示
    print('DBに接続できません'.$e->getMessage());
    exit();
}

// セッションからデータを取り出す
$date = $_SESSION["date"];
$before_time = $_SESSION["before_time"];
$after_time = !empty($_SESSION["after_time"]) ? $_SESSION["after_time"] : NULL;
$content = $_SESSION["content"];
$place = !empty($_SESSION["place"]) ? $_SESSION["place"] : NULL;
$url = !empty($_SESSION["url"]) ? $_SESSION["url"] : NULL;
$memo = !empty(trim($_SESSION["memo"])) ? trim($_SESSION["memo"]) : NULL; // trimして空ならNULL

$file_path_to_db = NULL; // DBに保存するファイルパス
// ファイルアップロード処理
if (isset($_SESSION['file_tmp_name']) && isset($_SESSION['file_name'])) {
    $upload_dir = 'uploads/'; // アップロード先ディレクトリ
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true); // ディレクトリがなければ作成
    }
    // ファイル名をユニークにする（例: タイムスタンプ + 元のファイル名）
    $uploaded_file_name = time() . '_' . basename($_SESSION['file_name']);
    $destination = $upload_dir . $uploaded_file_name;

    if (move_uploaded_file($_SESSION['file_tmp_name'], $destination)) {
        $file_path_to_db = $destination; // DBには保存先のパスを記録
    } else {
        // ファイル移動失敗時のエラー処理 (ログ記録など)
        error_log("File upload failed: " . $_SESSION['file_name']);
    }
}


try {
    // SQLインジェクション対策のため、プリペアドステートメントを使用
    $sql = 'INSERT INTO plan (date, before_time, after_time, content, place, url, file, memo) VALUES(:date, :before_time, :after_time, :content, :place, :url, :file, :memo)';
    $stmt = $dbh->prepare($sql);

    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':before_time', $before_time, PDO::PARAM_STR);
    $stmt->bindParam(':after_time', $after_time, PDO::PARAM_STR); // NULL許容
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->bindParam(':place', $place, PDO::PARAM_STR); // NULL許容
    $stmt->bindParam(':url', $url, PDO::PARAM_STR);     // NULL許容
    $stmt->bindParam(':file', $file_path_to_db, PDO::PARAM_STR); // NULL許容
    $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);   // NULL許容
    
    $stmt->execute();

} catch (PDOException $e) {
    error_log('DB Insert Error: ' . $e->getMessage());
    // ユーザーには汎用的なエラーメッセージを表示
    // exit("予定の登録に失敗しました。しばらくしてから再度お試しください。");
    // 今回は元のコードに合わせて表示
    print('DB登録エラー: '.$e->getMessage());
    exit();
}

// 登録完了後、不要なセッションデータを消去
unset($_SESSION["date"], $_SESSION["before_time"], $_SESSION["after_time"], 
    $_SESSION["content"], $_SESSION["place"], $_SESSION["url"], 
    $_SESSION["memo"], $_SESSION["file_tmp_name"], $_SESSION["file_name"], $_SESSION["errors"]);

// セッション自体を完全にクリアしたい場合は session_destroy() も検討
// session_destroy(); // ただし、他のセッション変数も消えるので注意

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予定追加完了</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" xintegrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <div class="container mt-80 status-message-container">
            <div class="status-message success">
                <h1>予定追加完了</h1>
                <p>予定の追加が完了しました。</p>
            </div>
            <div class="page-actions">
                <button onclick="location.href='index.php'">カレンダーページに戻る</button>
            </div>
        </div>
    </main>
</body>
</html>
