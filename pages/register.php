
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register</title>
  <link rel="stylesheet" href="login_register.css" />
</head>
<body>
  <div class="form-container">
    <form action="auth/login_process.php" method="POST" class="form-box">
      <h2>Registration</h2>
      <input type="text" name="name" placeholder="Enter your name" required />
      <input type="email" name="email" placeholder="Enter your email" required />
      <input type="password" name="password" placeholder="Create a password" required />
      <input type="password" name="confirm_password" placeholder="Confirm a password" required />
      <label><input type="checkbox" required /> I accept all terms & conditions</label>
      <button type="submit">Register Now</button>
      <p>Already have an account? <a href="?page=login">Login now</a></p>
    </form>
  </div>
</body>
</html>
