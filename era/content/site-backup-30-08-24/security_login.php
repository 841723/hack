<?php
session_start();

// Function to contact the database using SQLite3 prepared statements
function contactDB($query, $params = [], $type = 0) {
    global $db;

    try {
        $stmt = $db->prepare($query);

        $paramIndex = 1;
        foreach ($params as $param) {
            $stmt->bindValue($paramIndex, $param, getParamType($param));
            $paramIndex++;
        }

        $result = $stmt->execute();

        return ($type == 0) ? $result->fetchArray(SQLITE3_NUM) : $result->fetchArray(SQLITE3_ASSOC);

    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Helper for parameter types
function getParamType($param) {
    if (is_int($param)) return SQLITE3_INTEGER;
    if (is_float($param)) return SQLITE3_FLOAT;
    if (is_null($param)) return SQLITE3_NULL;
    return SQLITE3_TEXT;
}

// Connect to SQLite
$db = new SQLite3('filedb.sqlite');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $answer1 = $_POST['answer1'];
    $answer2 = $_POST['answer2'];
    $answer3 = $_POST['answer3'];

    $query = "SELECT user_id, security_answer1, security_answer2, security_answer3 FROM users WHERE user_name = ?";
    $user_data = contactDB($query, [$username], 1);

    if ($user_data) {
        if (
            $answer1 === $user_data['security_answer1'] &&
            $answer2 === $user_data['security_answer2'] &&
            $answer3 === $user_data['security_answer3']
        ) {
            $_SESSION['eravalid'] = true;
            $_SESSION['erauser'] = $user_data['user_id'];
            $operation_successful = true;
        } else {
            $error_message = "Incorrect answers. Please try again.";
        }
    } else {
        $error_message = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Log in with Security Questions</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Inter', Arial, sans-serif;
            background: linear-gradient(120deg, #e0e7ff 0%, #f8fafc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background-color: #fff;
            max-width: 500px;
            width: 100%;
            border-radius: 12px;
            padding: 2.5rem 2rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 1.65rem;
            font-weight: 700;
            color: #1d3557;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        p.description {
            text-align: center;
            color: #555;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.4rem;
            display: block;
            font-size: 1rem;
        }

        input[type="text"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            background-color: #f9fafb;
            font-size: 1.05rem;
            transition: border 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
        }

        input.answer-input {
            width: 90%;
            margin: 0 auto;
            display: block;
            text-align: center;
        }

        input[type="text"]:focus {
            border-color: #6366f1;
            background-color: #fff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.3);
        }

        input[type="submit"] {
            padding: 0.85rem;
            background: linear-gradient(90deg, #6366f1, #2563eb);
            color: white;
            font-size: 1.05rem;
            font-weight: 600;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease, box-shadow 0.3s ease;
            margin-top: 1rem;
        }

        input[type="submit"]:hover {
            background: linear-gradient(90deg, #2563eb, #6366f1);
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.2);
        }

        .error, .success {
            max-width: 320px;
            margin: 0 auto 1.2rem;
            padding: 0.9rem 1.1rem;
            border-radius: 6px;
            font-weight: 500;
            text-align: center;
        }

        .error {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
            font-weight: 600;
        }

        @media (max-width: 500px) {
            .container {
                padding: 2rem 1rem;
            }

            input.answer-input {
                width: 100%;
            }
        }
    </style>
    <script>
        function showSuccessAndRedirect() {
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.display = 'block';
                setTimeout(() => {
                    window.location.href = 'manage.php';
                }, 1000);
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Log in Using Security Questions</h1>
        <p class="description">
            If you’ve forgotten your password, you can log in by answering your security questions instead.
        </p>
        <?php
        if (isset($error_message)) {
            echo "<div class='error'>" . htmlspecialchars($error_message) . "</div>";
        }
        if (isset($operation_successful) && $operation_successful) {
            echo "<div id='successMessage' class='success'>Login successful. Redirecting…</div>";
            echo "<script>showSuccessAndRedirect();</script>";
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" autocomplete="off">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autocomplete="username" class="answer-input" />
            </div>
            <div class="form-group">
                <label for="question1">What is your mother's maiden name?</label>
                <input type="text" id="question1" name="answer1" required class="answer-input" />
            </div>
            <div class="form-group">
                <label for="question2">What was the name of your first pet?</label>
                <input type="text" id="question2" name="answer2" required class="answer-input" />
            </div>
            <div class="form-group">
                <label for="question3">In which city were you born?</label>
                <input type="text" id="question3" name="answer3" required class="answer-input" />
            </div>
            <input type="submit" value="Verify and Log In" />
        </form>
    </div>
</body>
</html>

