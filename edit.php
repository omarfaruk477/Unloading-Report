<?php
require('connection.php');

// Fetch data if ID is provided
if (isset($_GET['id'])) {
    $get_id = $_GET['id'];

    $sql = "SELECT * FROM unloagingdata WHERE id = :id";
    $dataStmt = $connection->prepare($sql);
    $dataStmt->bindParam(':id', $get_id, PDO::PARAM_INT);
    $dataStmt->execute();
    $data = $dataStmt->fetch(PDO::FETCH_OBJ);



    if ($data) {
        $edit_id = $data->id;
        $edit_date = $data->date;
        $edit_godown = $data->UnloadToGodown;
        $edit_delivery = $data->DirectDilevary;
        $edit_today_unload = $edit_godown + $edit_delivery;
        $edit_product = $data->product;
    } else {
        echo "No data found for the given ID.";
        exit;
    }
}

// Update data if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_submit'])) {
    $edit_date = $_POST['edit_date'];
    $edit_godown = $_POST['edit_godown'];
    $edit_delivery = $_POST['edit_delivery'];
    $edit_today_unload = $edit_godown + $edit_delivery; // Calculate dynamically on form submission
    $edit_product = $_POST['edit_product'];

    $sql2 = "UPDATE unloagingdata SET 
            date = :date,
            UnloadToGodown = :godown,
            DirectDilevary = :delivery,
            TodayUnload = :todaytotal,
            product = :product
            WHERE id = :id";
    $dataStmt2 = $connection->prepare($sql2);
    $dataStmt2->bindParam(':date', $edit_date);
    $dataStmt2->bindParam(':godown', $edit_godown, PDO::PARAM_INT);
    $dataStmt2->bindParam(':delivery', $edit_delivery, PDO::PARAM_INT);
    $dataStmt2->bindParam(':todaytotal', $edit_today_unload, PDO::PARAM_INT);
    $dataStmt2->bindParam(':product', $edit_product);
    $dataStmt2->bindParam(':id', $edit_id, PDO::PARAM_INT);

    if ($dataStmt2->execute()) {
        echo "Data updated successfully!";
        header("Location: index.php");
        exit;
    } else {
        echo "Error updating data.";
    }
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

<body class="bac">

    <div class="container mt-3">
        <div class="row d-flex justify-content-center">
            <div class="col-md-5">
                <a href="index.php" class="btn btn-primary mb-2">Back</a>

                <div class="card">
                    <div class="card-header">
                        <h2>Edit</h2>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?id=$edit_id"); ?>" method="POST">
                            <div class="my-3">
                                <label for="edit_date">Date</label>
                                <input type="date" name="edit_date" value="<?php echo htmlspecialchars($edit_date); ?>" class="form-control">
                            </div>
                            <div class="my-3">
                                <label for="edit_godown">Unload to Godown</label>
                                <input type="number" name="edit_godown" value="<?php echo htmlspecialchars($edit_godown); ?>" class="form-control">
                            </div>
                            <div class="my-3">
                                <label for="edit_delivery">Direct Delivery</label>
                                <input type="number" name="edit_delivery" value="<?php echo htmlspecialchars($edit_delivery); ?>" class="form-control">
                            </div>
                            <div class="my-3">
                                <label for="edit_product">Product</label>
                                <select name="edit_product" class="form-control">
                                    <option value="">-Select-</option>
                                    <option value="Dhan" <?php echo ($edit_product == 'Dhan') ? 'selected' : ''; ?>>Dhan</option>
                                    <option value="Robi" <?php echo ($edit_product == 'Robi') ? 'selected' : ''; ?>>Robi</option>
                                </select>
                            </div>
                            <input type="submit" name="edit_submit" value="Update" class="btn btn-secondary">
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