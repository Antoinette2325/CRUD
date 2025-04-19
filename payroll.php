<?php
session_start();
require_once 'config.php';

// Handle form submission to add payroll data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payroll'])) {
    $employee_id = $_POST['employee_id'];
    $regular_pay = floatval($_POST['regular_pay']);
    $holiday_pay = floatval($_POST['holiday_pay']);
    $deductions = floatval($_POST['deductions']);
    $number_of_duties = intval($_POST['number_of_duties']);
    $payroll_date = $_POST['payroll_date'];

    // Calculate gross pay and net pay
    $gross_pay = $regular_pay + $holiday_pay;
    $net_pay = $gross_pay - $deductions;

    if (!empty($employee_id) && $regular_pay >= 0 && $holiday_pay >= 0 && $deductions >= 0 && $number_of_duties >= 0) {
        $stmt = $conn->prepare("INSERT INTO payroll (employee_id, regular_pay, holiday_pay, gross_pay, deductions, net_pay, number_of_duties, payroll_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("iddddids", $employee_id, $regular_pay, $holiday_pay, $gross_pay, $deductions, $net_pay, $number_of_duties, $payroll_date);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM payroll WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
        header("Location: payroll.php?delete_success=1"); // Redirect to refresh the page
        exit();
    } else {
        die("Prepare failed: " . $conn->error);
    }
}

// Fetch payroll data
$result = $conn->query("SELECT payroll.id, employees.name, payroll.regular_pay, payroll.holiday_pay, payroll.gross_pay, payroll.deductions, payroll.net_pay, payroll.number_of_duties, payroll.payroll_date 
                        FROM payroll 
                        JOIN employees ON payroll.employee_id = employees.id");

$payrollData = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $payrollData[] = $row;
    }
}

// Fetch employee list for the dropdown
$employees = $conn->query("SELECT id, name FROM employees");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function filterTable() {
            const searchInput = document.getElementById('search').value.toLowerCase();
            const rows = document.querySelectorAll('#payrollTable tbody tr');

            rows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                row.style.display = name.includes(searchInput) ? '' : 'none';
            });
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">
    <header class="bg-yellow-900 text-white w-full py-4 shadow-md fixed top-0 z-10">
        <div class="container mx-auto flex justify-between items-center px-4">
            <h1 class="text-2xl font-bold">Payroll</h1>
            <nav>
                <a href="admin_dashboard.php" class="text-white hover:underline">Back to Dashboard</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto px-20 py-20">
        <!-- Add Payroll Form -->
        <div class="bg-white shadow-lg rounded-lg p-8 mb-6 max-w-lg mx-auto">
            <h2 class="text-xl font-bold mb-4">Add Payroll</h2>
            <form method="POST" class="space-y-4">
                <select name="employee_id" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-900">
                    <option value="" disabled selected>Select Employee</option>
                    <?php while ($employee = $employees->fetch_assoc()): ?>
                        <option value="<?php echo $employee['id']; ?>"><?php echo htmlspecialchars($employee['name']); ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="number" name="regular_pay" placeholder="Regular Pay" step="0.01" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-900">
                <input type="number" name="holiday_pay" placeholder="Holiday Pay" step="0.01" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-900">
                <input type="number" name="deductions" placeholder="Deductions" step="0.01" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-900">
                <input type="number" name="number_of_duties" placeholder="Number of Duties" min="0" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-900">
                <input type="date" name="payroll_date" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-900">
                <button type="submit" name="add_payroll"
                    class="bg-yellow-900 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition duration-300">
                    Add Payroll
                </button>
            </form>
        </div>

        <?php if (isset($_GET['delete_success'])): ?>
            <div class="bg-green-500 text-white p-3 rounded mb-4">
                Payroll record deleted successfully.
            </div>
        <?php endif; ?>

        <!-- Display Payroll Data -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-xl font-bold mb-4">Employee Payroll</h2>
            <div class="mb-4">
                <input type="text" id="search" placeholder="Search by Name" onkeyup="filterTable()" 
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-900">
            </div>
            <table id="payrollTable" class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-yellow-900 text-white">
                        <th class="border border-gray-300 px-4 py-2">Name</th>
                        <th class="border border-gray-300 px-4 py-2">Regular Pay</th>
                        <th class="border border-gray-300 px-4 py-2">Holiday Pay</th>
                        <th class="border border-gray-300 px-4 py-2">Gross Pay</th>
                        <th class="border border-gray-300 px-4 py-2">Deductions</th>
                        <th class="border border-gray-300 px-4 py-2">Net Pay</th>
                        <th class="border border-gray-300 px-4 py-2">Number of Duties</th>
                        <th class="border border-gray-300 px-4 py-2">Payroll Date</th>
                        <th class="border border-gray-300 px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payrollData as $payroll): ?>
                        <tr class="text-center">
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($payroll['name']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($payroll['regular_pay']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($payroll['holiday_pay']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($payroll['gross_pay']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($payroll['deductions']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($payroll['net_pay']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($payroll['number_of_duties']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($payroll['payroll_date']); ?></td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="flex justify-center space-x-2">
                                    <a href="edit_payroll.php?id=<?php echo $payroll['id']; ?>" 
                                       class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-700">Edit</a>
                                    <a href="payroll.php?delete_id=<?php echo $payroll['id']; ?>" 
                                       class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-700"
                                       onclick="return confirm('Are you sure you want to delete this payroll record?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>