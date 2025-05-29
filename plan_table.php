<?php
if(!isset($_GET['date'])){
    header("Location: index.php"); // 日付が指定されていなければカレンダーへ
    exit();
}
$date = htmlspecialchars($_GET['date'], ENT_QUOTES, 'UTF-8');

include 'db_define.php';
session_start(); // エラーメッセージ表示などに使う可能性を考慮

$dsn = 'mysql:dbname='.DBNAME.';host='.DBHOST.';port='.DBPORT.';charset=utf8';
$output = '';
$db_error = null;

try{
    $dbh = new PDO($dsn, DBUSER, DBPASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $sql = "SELECT * FROM plan WHERE date = :date ORDER BY before_time ASC";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->execute();

    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($plans) > 0) {
        foreach($plans as $result){
            $output .= '<div class="plan-list-item">';
            $output .= '<p class="plan-id">予定番号: '.htmlspecialchars($result["id"], ENT_QUOTES, 'UTF-8').'</p>';
            $output .= '<p><strong>開始時間:</strong> '.htmlspecialchars(substr($result["before_time"], 0, 5), ENT_QUOTES, 'UTF-8').'</p>'; // HH:MM 形式
            if(!empty($result["after_time"])){
                $output .= '<p><strong>終了時間:</strong> '.htmlspecialchars(substr($result["after_time"], 0, 5), ENT_QUOTES, 'UTF-8').'</p>';
            }
            $output .= '<p><strong>予定内容:</strong> '.htmlspecialchars($result["content"], ENT_QUOTES, 'UTF-8').'</p>';
            if(!empty($result["place"])){
                $output .= '<p><strong>場所:</strong> '.htmlspecialchars($result["place"], ENT_QUOTES, 'UTF-8').'</p>';
            }
            if(!empty($result["url"])){
                $output .= '<p><strong>URL:</strong> <a href="'.htmlspecialchars($result["url"], ENT_QUOTES, 'UTF-8').'" target="_blank" rel="noopener noreferrer">'.htmlspecialchars($result["url"], ENT_QUOTES, 'UTF-8').'</a></p>';
            }
            if(!empty($result["file"])){
                // uploads/timestamp_filename.ext のような形式を想定
                $fileName = basename($result["file"]); // パスからファイル名のみ取得
                // 元のファイル名に戻すのは難しいので、保存されたファイル名でリンク
                $output .= '<p><strong>ファイル:</strong> <a href="'.htmlspecialchars($result["file"], ENT_QUOTES, 'UTF-8').'" target="_blank" download>'.htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8').'</a></p>';
            }
            if(!empty($result["memo"])){
                $output .= '<p><strong>メモ:</strong> <br>'.nl2br(htmlspecialchars($result["memo"], ENT_QUOTES, 'UTF-8')).'</p>';
            }
            // 削除フォーム
            $output .= '<form method="POST" action="delete.php" onsubmit="return confirm(\'本当にこの予定を削除しますか？ 予定番号: '.htmlspecialchars($result["id"], ENT_QUOTES, 'UTF-8').'\');" style="margin-top:10px;">';
            $output .= '<input type="hidden" name="id" value="'.htmlspecialchars($result["id"], ENT_QUOTES, 'UTF-8').'">';
            $output .= '<input type="hidden" name="date" value="'.$date.'">';
            $output .= '<input type="submit" value="この予定を削除" class="delete-button">';
            $output .= '</form>';
            $output .= '</div>';
        }
    } else {
        $output = '<div class="no-plans-message"><p>この日の予定はまだありません。</p></div>';
    }

} catch(PDOException $e){
    error_log('DB Select Error (plan_table): ' . $e->getMessage());
    $db_error = '予定の読み込み中にエラーが発生しました。';
    $output = '<div class="error-message">'.$db_error.'</div>';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $date; ?> の予定一覧</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" xintegrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <div class="container mt-30">
            <p class="back-link"><a href="index.php">カレンダーページに戻る</a></p>
            <h1><?php echo $date; ?> の予定</h1>
            <p><a href="plan.php?date=<?php echo $date; ?>" class="btn btn-primary" style="margin-bottom: 20px; display:inline-block; background-color: #28a745; border-color:#28a745;">この日に新しい予定を追加する</a></p>

            <?php if(isset($_GET['error']) && $_GET['error'] === 'invalid_id'): ?>
                <div class="error-message" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom:15px;">
                    エラー: 不正なIDが指定されました。
                </div>
            <?php endif; ?>
            
            <?php print($output); ?>
        </div>
    </main>
</body>
</html>
