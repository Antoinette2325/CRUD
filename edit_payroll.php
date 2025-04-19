<?php
session_start();
require_once 'config.php';

// Get the payroll ID from the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM payroll WHERE id = $id");
    $payroll = $result->fetch_assoc();
}

// Handle form submission to update payroll data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payroll'])) {
    $id = intval($_POST['id']);
    $regular_pay = floatval($_POST['regular_pay']);
    $holiday_pay = floatval($_POST['holiday_pay']);
    $deductions = floatval($_POST['deductions']);
    $number_of_duties = intval($_POST['number_of_duties']);
    $payroll_date = $_POST['payroll_date'];

    // Calculate gross pay and net pay
    $gross_pay = $regular_pay + $holiday_pay;
    $net_pay = $gross_pay - $deductions;

    $stmt = $conn->prepare("UPDATE payroll SET regular_pay = ?, holiday_pay = ?, gross_pay = ?, deductions = ?, net_pay = ?, number_of_duties = ?, payroll_date = ? WHERE id = ?");
    $stmt->bind_param("dddddisi", $regular_pay, $holiday_pay, $gross_pay, $deductions, $net_pay, $number_of_duties, $payroll_date, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: payroll.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Payroll</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 min-h-screen flex items-center justify-center">
    <div style="background-color: white; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border-radius: 0.5rem; padding: 1.5rem; width: 100%; max-width: 28rem;">
        <h2 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 1rem;">Edit Payroll</h2>
        <form method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            <input type="hidden" name="id" value="<?php echo $payroll['id']; ?>">
            <input type="number" name="regular_pay" value="<?php echo $payroll['regular_pay']; ?>" placeholder="Regular Pay" step="0.01" required
                style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; outline: none; transition: box-shadow 0.2s; box-shadow: 0 0 0 2px transparent;">
            <input type="number" name="holiday_pay" value="<?php echo $payroll['holiday_pay']; ?>" placeholder="Holiday Pay" step="0.01" required
                style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; outline: none; transition: box-shadow 0.2s; box-shadow: 0 0 0 2px transparent;">
            <input type="number" name="deductions" value="<?php echo $payroll['deductions']; ?>" placeholder="Deductions" step="0.01" required
                style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; outline: none; transition: box-shadow 0.2s; box-shadow: 0 0 0 2px transparent;">
            <input type="number" name="number_of_duties" value="<?php echo $payroll['number_of_duties']; ?>" placeholder="Number of Duties" min="0" required
                style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; outline: none; transition: box-shadow 0.2s; box-shadow: 0 0 0 2px transparent;">
            <input type="date" name="payroll_date" value="<?php echo $payroll['payroll_date']; ?>" required
                style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; outline: none; transition: box-shadow 0.2s; box-shadow: 0 0 0 2px transparent;">
            <button type="submit" name="update_payroll"
                style="background-color: #78350f; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; transition: background-color 0.3s; cursor: pointer; border: none;">
                Update Payroll
            </button>
        </form>
        <a href="payroll.php" style="display: inline-block; background-color: #6b7280; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-align: center; text-decoration: none; transition: background-color 0.3s; margin-top: 1rem; margin-left: 0.5rem;">
            Back to Payroll
        </a>
    </div>
</body>
</html>