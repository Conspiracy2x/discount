<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DiscountDB";

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Function to create a new discount code
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_code'])) {
    $code = $_POST['new_code'];
    $value = $_POST['new_value'];
    $sql = "INSERT INTO discount_codes (code, value) VALUES ('$code', '$value')";
    $conn->query($sql);
}

// Function to delete a discount code
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_code'])) {
    $id = $_POST['delete_id'];
    $sql = "DELETE FROM discount_codes WHERE id=$id";
    $conn->query($sql);
}

// Function to apply a discount code
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_code'])) {
    $product_price = $_POST['product_price'];
    $code = $_POST['apply_discount_code'];
    $sql = "SELECT value FROM discount_codes WHERE code='$code'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $value = $row['value'];
        $discount = ($product_price * $value) / 100;
        $new_price = $product_price - $discount;
        $message = "Original Price: $product_price, Discount: $discount, New Price: $new_price";
    } else {
        $message = "Invalid discount code.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Discount Code Manager</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }
        table {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="text-center">Discount Code Manager</h1>
        
        <!-- Create New Discount Code -->
        <div class="card">
            <div class="card-header">
                Create New Discount Code
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="new_code">Code:</label>
                        <input type="text" class="form-control" name="new_code" id="new_code">
                    </div>
                    <div class="form-group">
                        <label for="new_value">Value (%):</label>
                        <input type="number" class="form-control" name="new_value" id="new_value">
                    </div>
                    <input type="submit" class="btn btn-primary" name="create_code" value="Create">
                </form>
            </div>
        </div>

        <!-- View Existing Discount Codes -->
        <div class="card mt-4">
            <div class="card-header">
                Existing Discount Codes
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Value (%)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, code, value FROM discount_codes";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . $row['code'] . "</td>";
                                echo "<td>" . $row['value'] . "</td>";
                                echo "<td>";
                                echo "<form action='' method='POST'>";
                                echo "<input type='hidden' name='delete_id' value='" . $row['id'] . "'>";
                                echo "<input type='submit' class='btn btn-danger' name='delete_code' value='Delete'>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Apply a Discount Code -->
        <div class="card mt-4">
            <div class="card-header">
                Apply a Discount Code
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="product_price">Product Price:</label>
                        <input type="text" class="form-control" name="product_price" id="product_price">
                    </div>
                    <div class="form-group">
                        <label for="apply_discount_code">Discount Code:</label>
                        <input type="text" class="form-control" name="apply_discount_code" id="apply_discount_code">
                    </div>
                    <input type="submit" class="btn btn-primary" name="apply_code" value="Apply">
                </form>
                <p class="mt-3"><?php echo $message; ?></p>
            </div>
        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>
