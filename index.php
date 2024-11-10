<?php
require('connection.php');
?>


<?php
// SERVER REQUEST ACCESS
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {

    // Total sum calculation
    $sql = "SELECT SUM(TodayUnload) AS total_unload FROM unloagingdata";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $result['total_unload'] ?? 0;

    // Get values from form fields and calculate TodayUnload
    $date = $_POST['date'];
    $godown = $_POST['godown'];
    $delivery = $_POST['delivery'];
    $today_unload = $godown + $delivery;
    $product = $_POST['product'];

    if (empty($date) || empty($godown) || empty($delivery) || empty($product)) {
        $msg = creatAlert('All filed are required');
    } else {
        // Prepare an INSERT query with placeholders
        $sql = "INSERT INTO unloagingdata (date, UnloadToGodown, DirectDilevary, TodayUnload, product) VALUES (:date, :godown, :delivery, :today_unload, :product)";
        $statement = $connection->prepare($sql);

        // Bind parameters to avoid SQL injection
        $statement->bindParam(':date', $date);
        $statement->bindParam(':godown', $godown);
        $statement->bindParam(':delivery', $delivery);
        $statement->bindParam(':today_unload', $today_unload);
        $statement->bindParam(':product', $product);

        // Execute the query
        $statement->execute();
    }
}

// Total sum by colum "UnloadToGodown" "DirectDilevary" "TodayUnload"
$totalSql = "SELECT
SUM(UnloadToGodown) AS total_UnloadToGodown,
SUM(DirectDilevary) AS total_DirectDilevary,
SUM(TodayUnload) AS total_TodayUnload
FROM unloagingdata";
$totalStmt = $connection->prepare($totalSql);
$totalStmt->execute();
$totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);

$totalGodown = $totalResult['total_UnloadToGodown'];
$totalDirectDelivery = $totalResult['total_DirectDilevary'];
$totalTodayUnload = $totalResult['total_TodayUnload'];

// Total sum by product "Dhan"
$dhanTotalSql = "SELECT 
SUM(UnloadToGodown) AS dhan_total_godown, 
SUM(DirectDilevary) AS dhan_total_delivery, 
SUM(TodayUnload) AS dhan_today_total 
FROM unloagingdata 
WHERE product = 'Dhan'";
$dhanStmt = $connection->prepare($dhanTotalSql);
$dhanStmt->execute();
$dhanResult = $dhanStmt->fetch(PDO::FETCH_ASSOC);
$dhan_total_godown = $dhanResult['dhan_total_godown'] ?? 0;
$dhan_total_delivery = $dhanResult['dhan_total_delivery'] ?? 0;
$dhan_today_total = $dhanResult['dhan_today_total'] ?? 0;

// Total sum by product "Robi"
$robiTotalSql = "SELECT 
SUM(UnloadToGodown) AS dhan_total_godown, 
SUM(DirectDilevary) AS dhan_total_delivery, 
SUM(TodayUnload) AS dhan_today_total 
FROM unloagingdata 
WHERE product = 'Robi'";
$robiStmt = $connection->prepare($robiTotalSql);
$robiStmt->execute();
$robiResult = $robiStmt->fetch(PDO::FETCH_ASSOC);
$robi_total_godown = $robiResult['dhan_total_godown'] ?? 0;
$robi_total_delivery = $robiResult['dhan_total_delivery'] ?? 0;
$robi_today_total = $robiResult['dhan_today_total'] ?? 0;





// Fetch all records
$dataSql = "SELECT * FROM unloagingdata";
$dataStmt = $connection->prepare($dataSql);
$dataStmt->execute();
$data = $dataStmt->fetchAll(PDO::FETCH_OBJ);




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unloading Report</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>

<body class="bac">

    <div class="container mt-3">
        <div class="row d-flex justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h2>Unloading Report</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_modal">
                            add new
                        </button>
                    </div>
                    <div class="aler-msg ">
                        <?php echo $msg ?? ""; ?>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="align-middle text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Unload to Godown</th>
                                    <th>Direct Delivery</th>
                                    <th>Today Unload</th>
                                    <th>Cumulative Unload</th>
                                    <th>Product</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-end">

                                <?php
                                $i = 0;
                                $j = 1;
                                foreach ($data as $item):
                                    $k = $i += $item->TodayUnload;

                                ?>

                                    <tr class="align-middle text-center">
                                        <td><?php echo $j;
                                            $j++; ?></td>
                                        <td><?= htmlspecialchars($item->date) ?></td>
                                        <td><?= htmlspecialchars($item->UnloadToGodown) ?></td>
                                        <td><?= htmlspecialchars($item->DirectDilevary) ?></td>
                                        <td><?= htmlspecialchars($item->TodayUnload) ?></td>
                                        <td><?php echo $k; ?></td>
                                        <td><?= htmlspecialchars($item->product) ?></td>
                                        <td>
                                            <a href="edit.php?id=<?= htmlspecialchars($item->id) ?>" class="btn btn-primary">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="align-middle text-center">

                                    <th colspan="2">Total =</th>
                                    <th><?= htmlspecialchars($totalGodown) ?></th>
                                    <th><?= htmlspecialchars($totalDirectDelivery) ?></th>
                                    <th><?= htmlspecialchars($totalTodayUnload) ?></th>
                                    <th colspan="3"></th>
                                </tr>
                                <tr class="align-middle text-center fw-semibold" style="font-size:13px;">

                                    <td colspan="2">Total =</td>
                                    <td> Dhan=<?= htmlspecialchars($dhan_total_godown) ?>,<br>
                                        <p class="text-danger">Robi=<?= htmlspecialchars($robi_total_godown) ?></p>
                                    </td>
                                    <td> Dhan=<?= htmlspecialchars($dhan_total_delivery) ?>,<br>
                                        <p class="text-danger">Robi=<?= htmlspecialchars($robi_total_delivery) ?></p>
                                    </td>
                                    <td> Dhan=<?= htmlspecialchars($dhan_today_total) ?>,<br>
                                        <p class="text-danger">Robi=<?= htmlspecialchars($robi_today_total) ?></p>
                                    </td>
                                    <td colspan="3">
                                        </th>
                                </tr>
                                <tr>
                                    <th colspan="8" class="text-center ">
                                        Total Lighter to <span style="color:Green;font-size:18px;font-weight:700">Godown: <?= htmlspecialchars($totalGodown) ?> bag</span>, Total Lighter to <span style="color:Navy;font-size:18px;font-weight:700">Delivery: <?= htmlspecialchars($totalDirectDelivery) ?> bag</span> and Lighter to<span style="color:Red;font-size:18px;font-weight:700">Total unload: <?= htmlspecialchars($totalTodayUnload) ?> bag.</span>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- ADD New form 'Modal' here -->

    <div class="modal fade" id="add_modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <h2>Creat an account</h2>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                                <div class="my-3">
                                    <label for="">Date</label>
                                    <input type="date" name="date" class="form-control" required>
                                </div>
                                <div class="my-3">
                                    <label for="">Unload to Godown</label>
                                    <input type="number" name="godown" class="form-control" required>
                                </div>
                                <div class="my-3">
                                    <label for="">Direct Delivery</label>
                                    <input type="number" name="delivery" class="form-control" required>
                                </div>
                                <div class="my-3">
                                    <label for="">Product</label>
                                    <select name="product" id="" class="form-control">
                                        <option value="">-Select-</option>
                                        <option value="Dhan">Dhan</option>
                                        <option value="Robi">Robi</option>
                                    </select>
                                </div>
                                <input type="submit" name="submit" value="Save" class="btn btn-secondary">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>






    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>