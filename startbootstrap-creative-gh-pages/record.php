<?php
session_start();
if (empty($_SESSION['useraccount'])) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: log_in.php");
    exit;
}
include('header.php');
?>
<?php


try {
    require_once 'db.php';
    $order = $_POST["order"]??"";
    // 防止 SQL injection，??""是如果 $_POST["searchtxt"] 不存在 或 為 null，就使用 ""（空字串）當作預設值
    $searchtxt = mysqli_real_escape_string($conn, $_POST["searchtxt"] ?? "");

    $where = [];
    if ($searchtxt) {
        $where[] = "(record_id like '%$searchtxt%' or records like '%$searchtxt%' or record_point like '%$searchtxt%')";
    }
    $sql = "SELECT * FROM record";
    // $sql .= 是 字串相加，implode() 會把陣列變成一個字串，如果 $where 陣列裡有條件，就將它們用 AND 串接成 WHERE 子句，並加到 SQL 語句後方
    if (count($where) > 0) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    // 如果 $order 不是空的，就加入 ORDER BY 排序條件
    if ($order) {
        $sql .= " ORDER BY $order";
    }
    // 使用資料庫連線 $conn 執行 SQL 指令 $sql，並把結果存到 $result
    $result = mysqli_query($conn, $sql);
?>

<!-- 將 padding-top 調整為 90px 避免 header 蓋住內容 -->
<div class="container-fluid position-relative" style="padding-top:90px; padding-bottom:120px;">
    <!-- + 按鈕固定右上 -->
     <?php if ($_SESSION['userrole']==='M'): ?> 
                    <a href="record_insert.php" class="btn btn-danger position-fixed" 
                     style="top:3rem; right:1rem; z-index:1050;">＋</a>
                <?php else: 
                    echo " "?>
                <?php endif; ?>


    <!-- 搜尋與排序表單 -->
    <form action="record.php" method="post" class="row g-2 align-items-center mb-2">
        <div class="col-auto">
            <select name="order" class="form-select">
                <option value="">選擇排序欄位</option>
                <option value="record_id" <?=($order=="record_id")?'selected':''?>>學生帳號</option>
                <option value="records" <?=($order=="records")?'selected':''?>>違規紀錄</option>
                <option value="record_point" <?=($order=="record_point")?'selected':''?>>違規點數</option>
            </select>
        </div>
        <div class="col-auto">
            <input type="text" name="searchtxt" class="form-control" placeholder="搜尋學生帳號/違規紀錄/點數" value="<?=htmlspecialchars($searchtxt)?>">
        </div>
        <div class="col-auto">
            <input type="submit" class="btn btn-info" value="搜尋">
        </div>
    </form>

    <!-- 表格 -->
    <table id="jobTable" class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>違規單號</th>
                <th>學生帳號</th>
                <th>違規紀錄</th>
                <th>違規點數</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <!-- htmlspecialchars避免 XSS 攻擊 -->
                <td><?=htmlspecialchars($row["recordnm"])?></td>
                <td><?=htmlspecialchars($row["record_id"])?></td>
                <td><?=htmlspecialchars($row["records"])?></td>
                <td><?=htmlspecialchars($row["record_point"])?></td>
                <td>
                <?php if ($_SESSION['userrole']==='M'): ?> 
                    <!-- 用recordnm刪除 -->
                    <a href="record_delete.php?recordnm=<?=$row['recordnm']?>" class="btn btn-danger btn-sm">刪除</a>
                <?php else: 
                    echo " "?>
                <?php endif; ?>
                    
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>



 <?php
//  計算每位學生違規點數總和
$sql_sum = "SELECT record_id, SUM(record_point) AS total_points FROM record";
// 如果查詢結果>0
if (count($where) > 0) {
    // $sql_sum +=後面的字串(implode(分隔字串，陣列)是拼接字串的行為)
    $sql_sum .= " WHERE " . implode(' AND ', $where);
}
// 同一個 record_id 的多筆資料，全部加起來，變成一筆結果
$sql_sum .= " GROUP BY record_id ORDER BY record_id";

// 執行查詢
$result_sum = mysqli_query($conn, $sql_sum);
// 準備資料給圖表使用
$sumData = [];
$students = [];
$points = [];

// 把查詢結果放到陣列中
while ($row = mysqli_fetch_assoc($result_sum)) {
    $sumData[] = $row;
    $students[] = $row["record_id"];
    $points[] = (int)$row["total_points"];
}
?>

    <h5 class="mt-4">每位學生違規點數加總</h5>
<table id="sumTable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>學生帳號</th>
            <th>違規點數加總</th>
        </tr>
    </thead>
    <tbody>
    <!-- 顯示每位學生的違規點數總和，foreach是陣列迴圈，逐一讀取陣列資料 -->
    <!-- $sumData as $row:從 $sumData 這個陣列裡，一筆一筆拿資料出來，每次拿到的那一筆，暫時叫做 $row。 -->
    <!-- while 用在「資料還沒全部拿到時」foreach 用在「資料已經是陣列時」 -->
    <?php foreach ($sumData as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row["record_id"]) ?></td>
            <td><?= (int)$row["total_points"] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>


<div class="container mt-4 mb-4" style="max-width:800px; height:380px;">
  <h5 class="mb-3">違規點數總和統計圖（橫向）</h5>
  <canvas id="sumChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('sumChart').getContext('2d');

// 自動產生隨機顏色（讓每位學生不同顏色）
const randomColor = (n) => {
  const colors = [];
  for (let i = 0; i < n; i++) {
    const r = Math.floor(Math.random() * 255);
    const g = Math.floor(Math.random() * 255);
    const b = Math.floor(Math.random() * 255);
    colors.push(`rgba(${r},${g},${b},0.6)`);
  }
  return colors;
};

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?=json_encode($students, JSON_UNESCAPED_UNICODE)?>,
    datasets: [{
      label: '違規點數總和',
      data: <?=json_encode($points, JSON_NUMERIC_CHECK)?>,
      backgroundColor: randomColor(<?=count($students)?>),
      borderColor: 'rgba(0,0,0,0.8)',
      borderWidth: 1,
      borderRadius: 4
    }]
  },
  options: {
    indexAxis: 'y', // ✅ 橫向
    responsive: true,
    maintainAspectRatio: false, // ✅ 控制大小由外層容器決定
    scales: {
      x: {
        beginAtZero: true,
        title: { display: true, text: '違規點數' }
      },
      y: {
        title: { display: true, text: '學生帳號' }
      }
    },
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: (ctx) => `違規點數：${ctx.parsed.x}`
        }
      }
    }
  }
});
</script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#jobTable').DataTable({
        "paging": true,
        "ordering": true,
        "searching": false
    });

    $('#sumTable').DataTable({
        "paging": false,     // 不用分頁
        "ordering": true,    // 可以按照帳號或點數排序
        "searching": false,  // 不另外開搜尋
        "info": false
    });
});
</script>

<?php
    mysqli_close($conn); 
} catch(Exception $e) {
    echo 'Message: ' . $e->getMessage();
}
require_once "footer.php"; 
?>