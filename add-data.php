<?php
if (file_exists(__DIR__ . "/autoload.php")) {
    require_once __DIR__ . "/autoload.php";
}

// DATABASE CONNECTION
$connection = new PDO("mysql:host=localhost;dbname=unloadingreport", "omar", "F625268f");
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
    $remark = $_POST['remark'];

    // Prepare an INSERT query with placeholders
    $sql = "INSERT INTO unloagingdata (date, UnloadToGodown, DirectDilevary, TodayUnload, TotalUnload, remark)
            VALUES (:date, :godown, :delivery, :today_unload, :totalUnload, :remark)";
    $statement = $connection->prepare($sql);

    // Bind parameters to avoid SQL injection
    $statement->bindParam(':date', $date);
    $statement->bindParam(':godown', $godown);
    $statement->bindParam(':delivery', $delivery);
    $statement->bindParam(':today_unload', $today_unload);
    $statement->bindParam(':totalUnload', $totalUnload);
    $statement->bindParam(':remark', $remark);

    // Execute the query
    $statement->execute();
}
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
            <div class="col-md-5">
                <a class="btn btn-primary my-3" href="index.php">Back</a>
                <div class="card">
                    <div class="card-header">
                        <h2 class="">Unloading Report</h2>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
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
                                <label for="">Remark</label>
                                <input type="text" name="remark" class="form-control">
                            </div>
                            <input type="submit" name="submit" value="Save" class="btn btn-secondary">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>