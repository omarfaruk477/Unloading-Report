<?php
if (file_exists(__DIR__ . "/autoload.php")) {
    require_once __DIR__ . "/autoload.php";
}

// Database connection
try {
    $connection = new PDO("mysql:host=localhost;dbname=unloadingreport", "omar", "F625268f");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

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
    $totalUnload = $total + $today_unload;  // Add today's unload to previous total
    $product = $_POST['product'];

    // Prepare an INSERT query with placeholders
    $sql = "INSERT INTO unloagingdata (date, UnloadToGodown, DirectDilevary, TodayUnload, TotalUnload, product)
            VALUES (:date, :godown, :delivery, :today_unload, :totalUnload, :product)";
    $statement = $connection->prepare($sql);

    // Bind parameters to avoid SQL injection
    $statement->bindParam(':date', $date);
    $statement->bindParam(':godown', $godown);
    $statement->bindParam(':delivery', $delivery);
    $statement->bindParam(':today_unload', $today_unload);
    $statement->bindParam(':totalUnload', $totalUnload);
    $statement->bindParam(':product', $product);

    // Execute the query
    $statement->execute();
}

// Combined sum calculation for multiple columns "Colum-1 is UnloadToGodown" "Colum-2 is DirectDilevary" "Colum-3 is TodayUnload"
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

<body>

    <div class="container mt-3">
        <div class="row d-flex justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h2>Unloading Report</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create_comment_modal">
                            add new
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="align-middle text-center">
                                <tr>
                                    <th>Date</th>
                                    <th>Unload to Godown</th>
                                    <th>Direct Delivery</th>
                                    <th>Today Unload</th>
                                    <th>Cumulative Unload</th>
                                    <th>Product</th>
                                </tr>
                            </thead>
                            <tbody class="text-end">
                                <?php foreach ($data as $item): ?>
                                    <tr class="align-middle text-center">
                                        <td><?= htmlspecialchars($item->date) ?></td>
                                        <td><?= htmlspecialchars($item->UnloadToGodown) ?></td>
                                        <td><?= htmlspecialchars($item->DirectDilevary) ?></td>
                                        <td><?= htmlspecialchars($item->TodayUnload) ?></td>
                                        <td><?= htmlspecialchars($item->TotalUnload) ?></td>
                                        <td><?= htmlspecialchars($item->product) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="align-middle text-center">

                                    <th>Total =</th>
                                    <th><?= htmlspecialchars($totalGodown) ?></th>
                                    <th><?= htmlspecialchars($totalDirectDelivery) ?></th>
                                    <th><?= htmlspecialchars($totalTodayUnload) ?></th>
                                    <th colspan="2"></th>
                                </tr>
                                <tr>
                                    <th colspan="6" class="text-center">
                                        Total Lighter to godown: <?= htmlspecialchars($totalGodown) ?> bag, Total Lighter to delivery: <?= htmlspecialchars($totalDirectDelivery) ?> bag and Total Lighter Unload: <?= htmlspecialchars($totalTodayUnload) ?> bag.
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

    <div class="modal fade" id="create_comment_modal">
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