<?php
session_start();

// POSTデータがない場合はplan.phpにリダイレクト（日付情報があればそれも渡す）
if(!(isset($_POST) && !empty($_POST))){
    $redirect_date = isset($_SESSION["date"]) ? $_SESSION["date"] : (isset($_GET["date"]) ? $_GET["date"] : '');
    if (!empty($redirect_date)) {
        header("Location: plan.php?date=" . urlencode($redirect_date));
    } else {
        header("Location: plan.php"); // 日付不明ならplan.phpの初期へ
    }
    exit();
}

// POSTされたデータをセッションに保存
$_SESSION["date"] = $_POST["date"];
$_SESSION["before_time"] = $_POST["before_time"];
$_SESSION["after_time"] = $_POST["after_time"];
$_SESSION["content"] = $_POST["content"];
$_SESSION["place"] = $_POST["place"];
$_SESSION["url"] = $_POST["url"];
$_SESSION["memo"] = $_POST["memo"];

// ファイル処理
$file_display_name = 'ファイル未選択';
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    // ファイルがアップロードされた場合
    // ここではファイル名をセッションに保存する例（実際には一時ファイルパスなどを保存し、complete.phpで移動処理）
    $_SESSION["file_tmp_name"] = $_FILES['file']['tmp_name'];
    $_SESSION["file_name"] = $_FILES['file']['name'];
    $file_display_name = htmlspecialchars($_FILES['file']['name'], ENT_QUOTES, 'UTF-8');
} else {
    // ファイルがアップロードされなかった場合、またはエラーがあった場合
    unset($_SESSION["file_tmp_name"]); // 念のためクリア
    unset($_SESSION["file_name"]);
}


$date = htmlspecialchars($_SESSION["date"], ENT_QUOTES, 'UTF-8');
$before_time = htmlspecialchars($_SESSION["before_time"], ENT_QUOTES, 'UTF-8');
$after_time = htmlspecialchars($_SESSION["after_time"], ENT_QUOTES, 'UTF-8');
$content = htmlspecialchars($_SESSION["content"], ENT_QUOTES, 'UTF-8');
$place = htmlspecialchars($_SESSION["place"], ENT_QUOTES, 'UTF-8');
$url = htmlspecialchars($_SESSION["url"], ENT_QUOTES, 'UTF-8');
$memo = nl2br(htmlspecialchars(trim($_SESSION["memo"]), ENT_QUOTES, 'UTF-8'));


// バリデーション (plan.phpにエラーメッセージを渡すため、エラーがあればリダイレクト)
$errors = [];
if(empty(trim($_POST["content"]))){ // contentは必須
    $errors["content"] = '予定内容が入力されていません';
}
if(empty(trim($_POST["before_time"]))){ // before_timeは必須
    $errors["before_time"] = '開始時間が入力されていません';
}
// URLバリデーション (任意入力だが、入力されたら形式チェック)
if(!empty(trim($_POST["url"])) && !filter_var(trim($_POST["url"]), FILTER_VALIDATE_URL)){
    $errors["url"] = 'URLの形式が正しくありません。';
}


if(count($errors) > 0){
    $_SESSION["errors"] = $errors;
    // エラーがあった項目をセッションに再格納してplan.phpに戻す
    $_SESSION["before_time"] = $_POST["before_time"];
    $_SESSION["after_time"] = $_POST["after_time"];
    $_SESSION["content"] = $_POST["content"];
    $_SESSION["place"] = $_POST["place"];
    $_SESSION["url"] = $_POST["url"];
    $_SESSION["memo"] = $_POST["memo"];
    // ファイル情報はセッションに残っているはず
    header("Location:plan.php?date=" . urlencode($_POST["date"]));
    exit();
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $date; ?> 予定追加 -確認画面-</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" xintegrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <div class="container mt-50">
            <p class="back-link"><a href="plan.php?date=<?php echo urlencode($date); ?>">予定フォーム画面に戻る</a></p>
            <h1><?php echo $date; ?></h1>
            <h3>入力内容確認</h3>

            <div class="confirm-container">
                <table class="confirm-table center-block">
                    <tr>
                        <td style="vertical-align: top;">
                            <ul>
                                <li><h4 class="text-danger">開始時間</h4></li>
                                <li><?php echo !empty($before_time) ? $before_time : '未入力'; ?></li>
                            </ul>
                            <ul>
                                <li><h4 class="text-info">終了時間</h4></li>
                                <li><?php echo !empty($after_time) ? $after_time : '未入力'; ?></li>
                            </ul>
                            <ul>
                                <li><h4 class="text-danger">予定内容</h4></li>
                                <li><?php echo !empty($content) ? $content : '未入力'; ?></li>
                            </ul>
                        </td>
                        <td style="width: 50px;"></td> <td style="vertical-align: top;">
                            <ul>
                                <li><h4 class="text-info">場所</h4></li>
                                <li><?php echo !empty($place) ? $place : '未入力'; ?></li>
                            </ul>
                            <ul>
                                <li><h4 class="text-info">URL</h4></li>
                                <li>
                                    <?php if(!empty($url)): ?>
                                        <a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer"><?php echo $url; ?></a>
                                    <?php else: ?>
                                        未入力
                                    <?php endif; ?>
                                </li>
                            </ul>
                            <ul>
                                <li><h4 class="text-info">ファイル</h4></li>
                                <li><?php echo $file_display_name; ?></li>
                            </ul>
                            <ul>
                                <li><h4 class="text-info">メモ</h4></li>
                                <li><?php echo !empty(trim($memo)) ? $memo : '未入力'; // trimして空かチェック ?></li>
                            </ul>
                        </td>
                    </tr>
                </table>
                <div class="page-actions">
                    <button onclick="location.href='plan.php?date=<?php echo urlencode($date); ?>'" type="button" class="btn-secondary">修正する</button>
                    <button onclick="location.href='complete.php'" type="button">この内容で登録する</button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
