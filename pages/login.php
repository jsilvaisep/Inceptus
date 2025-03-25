
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="stylesheet" href="login_register.css" />
</head>
<body>
  <div class="form-container">
    <form action="auth/login_process.php" method="POST" class="form-box">
      <h2>Login</h2>
      <input type="email" name="email" placeholder="Enter your email" required />
      <input type="password" name="password" placeholder="Enter your password" required />
      <div class="form-links">
        <label><input type="checkbox" name="remember" /> Remember me</label>
        <a href="#">Forgot password?</a>
      </div>
      <button type="submit">Login Now</button>
      <p>Don't have an account? <a href="?page=register">Sign up now</a></p>
    </form>
  </div>
</body>
</html>
