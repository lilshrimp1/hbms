<?php
    require_once '../Database/database.php';
    require_once '../models/User.php';

    $database = new database();
    $conn = $database->getConnection();
    session_start();


    User::setConnection($conn);
    $email = $_POST['email'] ?? null;

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if($email){
            $user = User::findByColumn('email', $email);
            if($user){    
                if(password_verify($_POST['password'], $user->password)){

                    // Check if account is active
                    if($user->status != 'active' && $user->status !== 'Active'){
                        $_SESSION['error'] = "Your account is deactivated. Please contact an administrator.";
                        header('Location: login.php');
                        exit();
                    }
                    // Store more user information in session
                    
                    $_SESSION['email'] = $user->email;
                    $_SESSION['role'] = $user->role;
                    $_SESSION['user_id'] = $user->id;


                    // $_SESSION['user'] = $user;
                    // $_SESSION['user']->email;
                    if($user->role == 'Super Admin' || $user->role == 'Admin' || $user->role == 'Front Desk'){
                        header('Location: ../main/index.php');
                        exit();
                    }else{
                        header('Location: ../guest/index.php');
                        exit();
                    }
                }
                else{
                    $_SESSION['error'] = "Invalid email or password.";
                }
            }
            else{
                $_SESSION['error'] = "Invalid email or password.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Library</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cal+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('final.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Cal Sans', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding-right: 5%;
        }

        .hbms-title {
            font-size: 150px;
            color: rgb(247, 247, 247);
            font-weight: bold;
            position: absolute;
            left: 18%; 
            top: 50%;
            transform: translateY(-50%);
        }

        .login-card {
            background-color: rgba(255, 255, 255, 0.75);
            border-radius: 80px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-card h3 {
            margin-bottom: 30px;
            color: #393E46;
            font-weight: bold;
            letter-spacing: 1px;
            font-size: 50px;
            text-align: left;
        }

        .form-label {
            float: left;
            color: #393E46;
            margin-top: 10px;
        }

        .form-control {
            background-color: #6c757d;
            border: 2px solid #6c757d;
            border-radius: 20px;
            padding: 0.6rem 1rem;
            color: white;
        }

        .form-control::placeholder {
            color: #dee2e6;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #222;
        }

        .btn-primary {
            background-color: #006A71;
            border: none;
            border-radius: 20px;
            padding: 0.5rem 2rem;
            margin-top: 20px;
        }

        .btn-primary:hover {
            background-color: #568ea7;
        }

        .login-card p {
            margin-top: 20px;
            font-size: 0.9rem;
        }

        .login-card a {
            color: #393E46;
            text-decoration: none;
            font-weight: 500;
        }

        .login-card a:hover {
            text-decoration: underline;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            padding: 40px 20px;
        }

        .login-container {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="hbms-title">H B M S</div>

    <div class="login-wrapper">
        <div class="login-card">
            <h3 class="mb-4">LOG IN</h3>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            ?>
            <form action="login.php" method="POST" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <button type="submit" class="btn btn-primary px-4 mx-auto d-block">Login</button>
            </form>
            <div class="text-center mt-3">
                <p>Doesn't have an account yet?<a href="#" data-bs-toggle="modal" data-bs-target="#signupModal" style="color: #006A71; font-weight: bold;">
                        Sign Up</a> here</p>
            </div>
        </div>
    </div>

<!-- SIGN UP MODAL -->
<div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0" style="border-radius: 30px; background-color: white; min-width: 350px;"> 
      <div class="modal-body p-4">
        <h2 class="text-center mb-3" style="font-family: 'Cal Sans', sans-serif; font-weight: 700; font-size: 1.5rem;">Register</h2>
        <form action="signup.php" method="POST">
          <div class="mb-2">
            <label class="form-label">Full Name:</label>
            <input type="text" name="name" class="form-control signup-input" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Contact Number:</label>
            <input type="text" name="contact" class="form-control signup-input" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Address:</label>
            <input type="text" name="address" class="form-control signup-input" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Email:</label>
            <input type="email" name="email" class="form-control signup-input" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Password:</label>
            <input type="password" name="password" class="form-control signup-input" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm Password:</label>
            <input type="password" name="confirm_password" class="form-control signup-input" required>
          </div>
          <button type="submit" class="btn w-100 text-white" style="background-color: #00b4b6; border-radius: 10px;">REGISTER</button>
        </form>
        <p class="text-center mt-3" style="font-size: 0.9rem;">Already have an account? <a href="#" data-bs-dismiss="modal" 
                 style="color: #006A71; font-weight: bold;">LOG IN</a> here</p>
      </div>
    </div>
  </div>
</div>

<style>
  .signup-input {
    background-color: #6c757d;
    border: none;
    border-radius: 20px;
    padding: 8px 12px;
    color: white;
  }
  .signup-input::placeholder {
    color: #dee2e6;
  }
  .signup-input:focus {
    border: 2px solid #222;
    outline: none;
    box-shadow: none;
  }
</style>


    <!-- JS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            'use strict';
            var forms = document.querySelectorAll('form');
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>
