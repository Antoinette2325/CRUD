<?php
session_start();
require_once 'config.php';

// Handle Create, Update, and Delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_employee'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);

        if (!empty($name) && !empty($email)) {
            $stmt = $conn->prepare("INSERT INTO employees (name, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $email);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST['update_employee'])) {
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);

        if (!empty($id) && !empty($name) && !empty($email)) {
            $stmt = $conn->prepare("UPDATE employees SET name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $email, $id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST['delete_employee'])) {
        $id = $_POST['id'];

        if (!empty($id)) {
            $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST['reset_ids'])) {
        $conn->query("TRUNCATE TABLE employees");
        $conn->query("ALTER TABLE employees AUTO_INCREMENT = 1");
    }
}

// Fetch total employee count
$result = $conn->query("SELECT COUNT(*) AS total FROM employees");
$totalEmployees = $result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">
    <!-- Header -->
    <header class="bg-yellow-900 text-white w-full py-4 shadow-md fixed top-0 z-10">
        <div class="container mx-auto flex justify-between items-center px-4">
            <h1 class="text-2xl font-bold">Admin Dashboard</h1>
 </h1>
            <nav>
                <button id="hamburger" class="text-white focus:outline-none">
                    <span class="text-2xl">☰</span>
                </button>
                <ul id="menu" class="hidden absolute bg-yellow-900 text-white right-4 mt-2 rounded shadow-lg">
                    <li>
                        <a href="logout.php" class="block px-6 py-2 hover:bg-yellow-700 font-medium text-lg" style="font-family: 'Poppins', sans-serif;">
                            Logout
                        </a>
                    </li>
                    <li>
                        <a href="employee_list.php" class="block px-6 py-2 hover:bg-yellow-700 font-medium text-lg" style="font-family: 'Poppins', sans-serif;">
                            Employee List
                        </a>
                    </li>
                    <li>
                        <a href="payroll.php" class="block px-6 py-2 hover:bg-yellow-700 font-medium text-lg" style="font-family: 'Poppins', sans-serif;">
                            Payroll
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-9">
        <div class="bg-yellow-800 shadow-1xl shadow-yellow-900 rounded-lg p-4 mb-4 max-w-xs mx-auto text-center">
            <h2 class="text-lg font-bold mb-2 text-white">Employee Count</h2>
            <div class="bg-yellow-700 p-2 rounded-lg shadow-md">
                <span class="text-white text-lg"><?php echo $totalEmployees; ?></span>
            </div>
        </div>

        <div class="bg-yellow-800 shadow-1xl shadow-yellow-900 rounded-lg p-4 mb-4 max-w-xs mx-auto text-center">
            <h2 class="text-xl font-bold mb-4 text- text-white">Add Employee</h2>
            <form method="POST" class="space-y-4">
                <input type="text" name="name" placeholder="Name" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-900">
                <input type="email" name="email" placeholder="Email" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-900">
                <button type="submit" name="add_employee"
                    class="bg-yellow-100 text-black font-bold mb-4 px-4 py-2 rounded-lg hover:bg-yellow-600 transition duration-300">
                    Add Employee
                </button>
            </form>
        </div>
    </main>
    <script>
        const hamburger = document.getElementById('hamburger');
        const menu = document.getElementById('menu');

        hamburger.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>