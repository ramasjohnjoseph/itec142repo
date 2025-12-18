<?php
require 'config.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);

    // Hash password before storing
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // FIXED: Column name changed to password_hash to match login.php
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hashedPassword, $full_name, $email]);
        $message = "Registration successful! You can now <a href='login.php' class='text-blue-600 underline font-bold'>login</a>.";
    } catch (PDOException $e) {
        $message = "Error: Username or Email might already exist.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Bulletin Board</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <?php include __DIR__ . '/header.php'; ?>
  <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border-t-4 border-green-700 mt-6">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Create Account</h2>
    <?php if ($message): ?>
      <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm border border-green-200"><?= $message ?></div>
    <?php endif; ?>
    <form method="POST" class="space-y-4">
      <input type="text" name="username" placeholder="Username" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
      <input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
      <input type="text" name="full_name" placeholder="Full Name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
      <input type="email" name="email" placeholder="Email Address" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
      <button type="submit" class="w-full bg-green-700 text-white py-2 rounded-lg font-bold hover:bg-green-800 transition">REGISTER</button>
    </form>
    <p class="mt-6 text-sm text-center text-gray-600">Already have an account? <a href="login.php" class="text-green-700 font-bold hover:underline">Login</a></p>
  </div>

  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>