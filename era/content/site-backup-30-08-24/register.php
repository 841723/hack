<?php
require_once('functions.global.php');
require_once('layout.php');

$message = '';

function sanitizeLoginInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

    $sqlKeywords = array('SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'TABLE', 'FROM', 'WHERE', 'AND', 'OR', 'UNION', '--');
    $input = str_ireplace($sqlKeywords, '', $input);

    $input = preg_replace('/[^a-zA-Z0-9@._-]/', '', $input);
    $input = substr($input, 0, 50);

    return $input;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = isset($_POST['username']) ? sanitizeLoginInput($_POST['username']) : '';
        $password = isset($_POST['password']) ? sanitizeLoginInput($_POST['password']) : '';

        if (empty($username) || empty($password)) {
            $message = "Username and password are required.";
        } else {
            $check_query = "SELECT * FROM users WHERE user_name = '$username'";
            $fetched = contactDB($check_query, 1);

            if (count($fetched) != 0) {
                $message = "User already exists. Please choose a different username.";
                header('Refresh: 2; URL = register.php');
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $query = "INSERT INTO users (user_name, user_password, auto_delete_files_after) VALUES ('$username', '$password_hash', -1);";
                $add_user = contactDB($query, 0);

                $message = "Registration successful! Redirecting to login page...";
                header('Refresh: 2; URL = login.php');
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to right, #e0f7fa, #f1f5f9);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: #fff;
            max-width: 400px;
            width: 90%;
            padding: 2.5rem 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #1f2937;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 1.8rem;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.4rem;
            margin-top: 1rem;
        }
        input[type="text"],
        input[type="password"] {
            padding: 0.75rem;
            font-size: 1rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background-color: #f9fafb;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
            background-color: #fff;
        }
        input[type="submit"] {
            margin-top: 1.8rem;
            background: linear-gradient(to right, #3b82f6, #2563eb);
            color: #fff;
            padding: 0.9rem;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        input[type="submit"]:hover {
            background: linear-gradient(to right, #2563eb, #3b82f6);
            transform: translateY(-1px);
        }
        .message {
            margin-top: 1.5rem;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            border: 1px solid #ddd;
            background-color: #f0f0f0;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Registration</h2>

        <?php
        if (!empty($message)) {
            echo "<div class='message'>" . htmlspecialchars($message) . "</div>";
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" autocomplete="off" novalidate>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required />

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required />

            <input type="submit" value="Register" />
        </form>
    </div>
</body>
</html>
