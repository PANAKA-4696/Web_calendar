<?php
//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

//前月・次月リンクが選択された場合は、GETパラメーターから年月を取得
if(isset($_GET['ym'])){
    $ym = $_GET['ym'];
}else{
    //今月の年月を表示
    $ym = date('Y-m');
}

//タイムスタンプ(どの時刻を基準にするか)を作成し、フォーマットをチェックする
$timestamp = strtotime($ym . '-01');
if($timestamp === false){//エラー対策として形式チェックを追加
    //falseが返ってきたときは、現在の年月・タイムスタンプを取得
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

//今月の日付フォーマット 例) 2020-10-2
$today_date = date('Y-m-j'); // 変数名を変更 (todayクラスとの混同を避けるため)

//カレンダーのタイトルを作成 例) 2020年10月
$html_title =date('Y年n月', $timestamp);

//前月・次月の年月を取得
$prev = date('Y-m', strtotime('-1 month', $timestamp));
$next = date('Y-m', strtotime('+1 month', $timestamp));

//該当月の日数を取得
$day_count = date('t', $timestamp);

//1日が何曜日か (0:日曜日, 1:月曜日, ..., 6:土曜日)
$youbi = date('w', $timestamp);

//カレンダー作成の準備
$weeks = [];
$week = '';

//第1週目：空のセルを追加
$week .= str_repeat('<td></td>', $youbi);

$holidays = [ // 簡単な祝日リスト（年によって変動するものは別途対応が必要）
    '01-01' => '元日',
    '02-11' => '建国記念の日',
    '02-23' => '天皇誕生日',
    // '03-20' or '03-21' => '春分の日', (要計算)
    '04-29' => '昭和の日',
    '05-03' => '憲法記念日',
    '05-04' => 'みどりの日',
    '05-05' => 'こどもの日',
    // '07-xx' => '海の日', (要計算 - 7月第3月曜日)
    '08-11' => '山の日',
    // '09-xx' => '敬老の日', (要計算 - 9月第3月曜日)
    // '09-22' or '09-23' => '秋分の日', (要計算)
    // '10-xx' => 'スポーツの日', (要計算 - 10月第2月曜日)
    '11-03' => '文化の日',
    '11-23' => '勤労感謝の日',
];
// 注意: 成人の日、春分の日、秋分の日、海の日、敬老の日、スポーツの日は年によって日付が変動するため、
// 正確な祝日表示にはより複雑なロジックまたは外部ライブラリが必要です。
// ここでは固定日のみを簡易的に扱います。

for($day = 1; $day <= $day_count; $day++, $youbi++){
    $current_date_str = $ym . '-' . sprintf('%02d', $day); // YYYY-MM-DD 形式
    $month_day_str = sprintf('%02d-%02d', date('m', strtotime($current_date_str)), $day); // MM-DD 形式

    $td_class = [];
    if($today_date == $ym . '-' . $day){ // $current_date_str と比較しても良い
        $td_class[] = 'today';
    }

    $holiday_name = '';
    if (array_key_exists($month_day_str, $holidays)) {
        $td_class[] = 'holiday';
        $holiday_name = $holidays[$month_day_str];
    }
    // 日曜日の場合は holiday クラスを追加 (CSSで文字色を赤にするため)
    if ($youbi % 7 == 0) {
        // $td_class[] = 'sunday'; // CSSで nth-of-type(1) で指定しているので不要かも
    }
    // 土曜日の場合
    if ($youbi % 7 == 6) {
        // $td_class[] = 'saturday'; // CSSで nth-of-type(7) で指定しているので不要かも
    }


    $week .= '<td class="' . implode(' ', $td_class) . '">';
    $week .= '<div class="day-number">' . $day . '</div>';
    if (!empty($holiday_name)) {
        $week .= '<div class="holiday-name">' . htmlspecialchars($holiday_name, ENT_QUOTES) . '</div>';
    }
    $week .= '<div class="actions">';
    $week .= '<a href="plan.php?date='.$current_date_str.'" class="add-plan">追加</a>';
    $week .= '<a href="plan_table.php?date='.$current_date_str.'" class="view-plan">予定</a>';
    $week .= '</div>';
    $week .= '</td>';

    if($youbi % 7 == 6 || $day == $day_count){//週終わり、月終わりの場合
        if($day == $day_count){//月の最終日、空のセルを追加
            $week .= str_repeat('<td></td>', 6 - ($youbi % 7));
        }
        $weeks[] = '<tr>' . $week . '</tr>';
        $week = '';//weekをリセット
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHPカレンダー</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" xintegrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-30">
        <div class="calendar-header">
            <a href="?ym=<?php echo $prev; ?>" class="calendar-nav-link">&lt;</a>
            <h3><?php echo $html_title; ?></h3>
            <a href="?ym=<?php echo $next; ?>" class="calendar-nav-link">&gt;</a>
        </div>
        <table class="table table-bordered calendar-table">
            <thead>
                <tr>
                    <th>日</th>
                    <th>月</th>
                    <th>火</th>
                    <th>水</th>
                    <th>木</th>
                    <th>金</th>
                    <th>土</th>
                </tr>
            </thead>
            <tbody>
            <?php
                foreach ($weeks as $w) { // 変数名を $week から $w に変更
                    echo $w;
                }
            ?>
            </tbody>
        </table>
    </div>
</body>
</html>
