<?php
session_start();

// POSTデータがなければカレンダーページにリダイレクト
if(!(isset($_POST['id']) && isset($_POST['date']))){
    header("Location: index.php");
    exit();
}

include 'db_define.php';
$dsn = 'mysql:dbname='.DBNAME.';host='.DBHOST.';port='.DBPORT.';charset=utf8';

$db_error = null;
$delete_success = false;
$target_date = htmlspecialchars($_POST['date'], ENT_QUOTES, 'UTF-8');
$target_id = filter_var($_POST['id'], FILTER_VALIDATE_INT);


if ($target_id === false) {
    // IDが不正な場合はエラーとして扱うか、リダイレクト
    header("Location: plan_table.php?date=" . urlencode($target_date) . "&error=invalid_id");
    exit();
}


try{
    $dbh = new PDO($dsn, DBUSER, DBPASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // 削除対象のファイルパスを取得 (ファイルも削除する場合)
    $sql_select_file = 'SELECT file FROM plan WHERE id = :id';
    $stmt_select = $dbh->prepare($sql_select_file);
    $stmt_select->bindParam(':id', $target_id, PDO::PARAM_INT);
    $stmt_select->execute();
    $file_to_delete_row = $stmt_select->fetch(PDO::FETCH_ASSOC);
    $file_to_delete_path = ($file_to_delete_row && !empty($file_to_delete_row['file'])) ? $file_to_delete_row['file'] : null;


    $sql = 'DELETE FROM plan WHERE id = :id';
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $target_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $delete_success = true;
            // ファイルも削除
            if ($file_to_delete_path && file_exists($file_to_delete_path)) {
                unlink($file_to_delete_path); // ファイル削除実行
            }
        } else {
            // 削除対象が見つからなかった場合 (既に削除されているなど)
            $db_error = "指定された予定が見つからないか、既に削除されています。";
        }
    } else {
        $db_error = "予定の削除に失敗しました。";
    }

} catch(PDOException $e){
    error_log('DB Delete Error: ' . $e->getMessage());
    $db_error = 'データベースエラーが発生しました。';
}

// 完了後は不要なセッションデータをクリア
// (このページでは特にセッションを使っていないが、念のため)
// session_destroy(); // 他に影響がなければ

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予定削除</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" xintegrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <div class="container mt-80 status-message-container">
            <?php if($delete_success): ?>
                <div class="status-message success">
                    <h1>削除完了</h1>
                    <p>ID: <?php echo htmlspecialchars($target_id, ENT_QUOTES, 'UTF-8'); ?> の予定の削除が完了しました。</p>
                </div>
            <?php else: ?>
                <div class="status-message error"> <h1>削除失敗</h1>
                    <p><?php echo htmlspecialchars($db_error ?: '予定の削除に失敗しました。', ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            <?php endif; ?>

            <div class="page-actions">
                <button onclick="location.href='plan_table.php?date=<?php echo urlencode($target_date); ?>'">予定一覧に戻る</button>
                <button onclick="location.href='index.php'">カレンダーページに戻る</button>
            </div>
        </div>
    </main>
</body>
</html>
