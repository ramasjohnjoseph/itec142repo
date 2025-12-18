<?php
require 'config.php';
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // Prepared Statement: prevents SQL Injection
        $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Password Hashing: Securely verify the plain text password against the stored hash
        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: dashboard.php");
            exit;
        } else {
            // Keep error message vague for security
            $message = "Invalid username or password.";
        }
    } catch (PDOException $e) {
        $message = "An error occurred. Please try again later.";
        // Log the actual error for debugging, but don't show it to the user
        error_log($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Community Bulletin Board</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
  <div class="max-w-4xl w-full p-8">
    <div class="bg-white rounded-2xl shadow-2xl p-10 md:p-16 relative overflow-hidden">


      <div class="flex flex-col md:flex-row items-center gap-12">
        <div class="md:w-1/2 text-center md:text-left">
          <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-green-800 leading-tight">
            COMMUNITY <br class="hidden md:block">BULLETIN BOARD
          </h1>
          <p class="mt-4 text-gray-600 font-medium">Stay updated with the latest campus events and announcements.</p>
        </div>

        <div class="md:w-1/2 mt-8 md:mt-0 flex items-center justify-center">
          <div class="w-full max-w-md">
            <?php if ($message): ?>
              <div id="loginMessage" role="alert" aria-live="assertive" class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm" tabindex="-1">
                <?= htmlspecialchars($message) ?>
              </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5" aria-label="Login form">
              <div>
              <div>
                <label for="username" class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Username</label>
                <input id="username" type="text" name="username" placeholder="Enter your username" autocomplete="username" aria-describedby="loginMessage" autofocus
                       class="w-full px-6 py-4 rounded-xl bg-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-600 focus:bg-white transition-all" required>
              </div>
              <div>
                <label for="password" class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Password</label>
                <input id="password" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" aria-describedby="loginMessage"
                       class="w-full px-6 py-4 rounded-xl bg-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-600 focus:bg-white transition-all" required>
              </div>
              <button id="loginSubmit" type="submit" aria-label="Login" class="w-full bg-green-800 text-white py-4 rounded-xl font-bold tracking-wide hover:bg-green-900 shadow-lg shadow-green-900/20 transform transition-transform active:scale-[0.98]">
                LOGIN
              </button>
              
              <div class="text-center mt-4">
                <a href="#" class="text-xs text-gray-500 hover:text-green-700 transition-colors">Forgot password?</a>
              </div>
              </div>
              
              <div class="flex items-center justify-center gap-4 mt-6 pt-6 border-t border-gray-100">
                <span class="text-sm text-gray-600">Don't have an account?</span>
                <a href="register.php" class="px-5 py-2 bg-yellow-400 text-xs font-bold text-yellow-900 rounded-full hover:bg-yellow-500 transition-all uppercase tracking-tighter">Register Now</a>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-green-50 rounded-full opacity-50 pointer-events-none"></div>
    </div>
  </div>

  <?php include __DIR__ . '/scripts.php'; ?>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if (window.AOS) AOS.init({ duration: 400, once: true, disable: window.matchMedia('(prefers-reduced-motion: reduce)').matches });
      // Move focus to the message if login failed to help screen reader users
      var msg = document.getElementById('loginMessage');
      if (msg) msg.focus();
    });
  </script>

</body>
</html>