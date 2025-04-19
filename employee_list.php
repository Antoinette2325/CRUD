<?php
session_start();
require_once 'config.php';

// Handle Reset IDs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_ids'])) {
    $conn->query("TRUNCATE TABLE employees");
    $conn->query("ALTER TABLE employees AUTO_INCREMENT = 1");
    header("Location: employee_list.php");
    exit();
}

// Handle Update Employee
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_employee'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (!empty($id) && !empty($name) && !empty($email)) {
        $stmt = $conn->prepare("UPDATE employees SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: employee_list.php");
        exit();
    }
}

// Handle Delete Employee
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_employee'])) {
    $id = $_POST['id'];

    if (!empty($id)) {
        $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: employee_list.php");
        exit();
    }
}

// Fetch all employees
$employees = $conn->query("SELECT id, name, email FROM employees");
if (!$employees) {
    die("Error fetching employees: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee List</title> <!-- Moved to the top of the <head> section -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleEdit(rowId) {
            const row = document.getElementById(`row-${rowId}`);
            const displayFields = row.querySelectorAll('.display-field');
            const editFields = row.querySelectorAll('.edit-field');

            displayFields.forEach(field => field.classList.toggle('hidden'));
            editFields.forEach(field => field.classList.toggle('hidden'));
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">
<header class="bg-yellow-900 text-white w-full py-4 shadow-md fixed top-0 z-10">
<div class="container mx-auto flex justify-between items-center px-4">
            <h1 class="text-2xl font-bold">Employee List</h1>
            <a href="admin_dashboard.php" class="text-white hover:underline">Back to Dashboard</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-90"> <!-- Increased top padding for more spacing -->
        <div class="bg-white shadow-lg rounded-lg p-6 max-w-4xl mx-auto">
            <h2 class="text-xl font-bold mb-4">Employee List</h2>
            <form method="POST" class="mb-6">
               
            </form>
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2">ID</th>
                        <th class="border border-gray-300 px-4 py-2">Name</th>
                        <th class="border border-gray-300 px-4 py-2">Email</th>
                        <th class="border border-gray-300 px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($employee = $employees->fetch_assoc()): ?>
                        <tr id="row-<?php echo $employee['id']; ?>">
                            <td class="border border-gray-300 px-4 py-2"><?php echo $employee['id']; ?></td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span class="display-field"><?php echo $employee['name']; ?></span>
                                <form method="POST" class="hidden edit-field">
                                    <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
                                    <input type="text" name="name" value="<?php echo $employee['name']; ?>" class="w-full p-2 border border-gray-300 rounded-lg">
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span class="display-field"><?php echo $employee['email']; ?></span>
                                <input type="email" name="email" value="<?php echo $employee['email']; ?>" class="hidden edit-field w-full p-2 border border-gray-300 rounded-lg">
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <button type="button" onclick="toggleEdit(<?php echo $employee['id']; ?>)" class="display-field bg-yellow-900 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition duration-300">
                                    Edit
                                </button>
                                <button type="submit" name="update_employee" class="hidden edit-field bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-500 transition duration-300">
                                    Save
                                </button>
                                </form>
                                <form method="POST" class="inline-block">
                                    <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
                                    <button type="submit" name="delete_employee"
                                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition duration-300">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>