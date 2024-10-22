<?php
// login.php
include('./config/db.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM user WHERE UserName='$username' AND Password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['login_user'] = $username;
        header("location: dashboard.php");
        exit();
    } else {
        $error = "Your Login Name or Password is invalid";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin</title>
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@100;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Josefin Sans', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: gradient 15s ease infinite;
            margin: 0;
            padding: 0;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease-in-out;
        }

        .glass-card:hover {
            transform: translateY(-8px);
        }

        .input-focus:focus {
            border-color: #764ba2;
            box-shadow: 0 0 12px rgba(118, 75, 162, 0.6);
        }

        .submit-btn {
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .submit-btn:hover {
            background: linear-gradient(90deg, #764ba2, #667eea);
            transform: scale(1.05);
        }

        .submit-btn:focus {
            outline: none;
            box-shadow: 0 0 8px rgba(118, 75, 162, 0.6);
        }

        .logo {
            animation: bounce 2s infinite;
            width: 64px;
            height: 64px;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .text-center h2 {
            letter-spacing: 2px;
            font-weight: 700;
        }

        .input-group input {
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .input-group input:hover {
            border-color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="glass-card p-6 sm:p-10 w-full max-w-md">
        <div class="text-center">
            <img class="mx-auto logo" src="https://balitimbungan.id/resto_assets/assets/img/favicon.png" alt="Bali Timbungan">
            <h2 class="mt-6 text-3xl font-bold text-white">Login to Admin Panel</h2>
        </div>
        <form class="mt-8 space-y-6" action="login.php" method="POST">
            <div class="input-group space-y-4">
                <div>
                    <label for="username" class="sr-only">Username</label>
                    <input id="username" name="username" type="text" autocomplete="username" required class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none input-focus focus:z-10 sm:text-sm" placeholder="Username">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none input-focus focus:z-10 sm:text-sm" placeholder="Password">
                </div>
            </div>

            <div>
                <button type="submit" class="submit-btn group relative w-full flex justify-center py-3 px-5 border border-transparent text-lg font-medium rounded-md text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2">
                    Sign In
                </button>
            </div>

            <?php if (isset($error)) { ?>
                <div class="text-center mt-4">
                    <p class="text-sm text-red-600"><?php echo $error; ?></p>
                </div>
            <?php } ?>
        </form>
    </div>
</body>
</html>
