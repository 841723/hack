<?php
require_once('layout.php');
require_once('functions.global.php');

// Check session validity before outputting anything
if (!isset($_SESSION['eravalid']) || $_SESSION['eravalid'] !== true) {
    header('Location: login.php');
    exit();
}

// Output the page top with sidebar and main content container open
echo deliverTop("Era - Update Security Questions");

// Connect to SQLite3 database
$db = new SQLite3('filedb.sqlite');

// Initialize variables
$error_message = '';
$operation_successful = false;

// Process POST submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $new_answer1 = trim($_POST['new_answer1'] ?? '');
    $new_answer2 = trim($_POST['new_answer2'] ?? '');
    $new_answer3 = trim($_POST['new_answer3'] ?? '');

    if ($username === '' || $new_answer1 === '' || $new_answer2 === '' || $new_answer3 === '') {
        $error_message = "All fields are required.";
    } else {
        $query = "UPDATE users SET security_answer1 = ?, security_answer2 = ?, security_answer3 = ? WHERE user_name = ?";
        $stmt = $db->prepare($query);
        $stmt->bindValue(1, $new_answer1, SQLITE3_TEXT);
        $stmt->bindValue(2, $new_answer2, SQLITE3_TEXT);
        $stmt->bindValue(3, $new_answer3, SQLITE3_TEXT);
        $stmt->bindValue(4, $username, SQLITE3_TEXT);

        if ($stmt->execute()) {
            $operation_successful = true;
        } else {
            $error_message = "Error updating security questions. Please try again.";
        }
    }
}
?>

<style>
    /* Container centered inside main-content */
    .container {
        background-color: #fff;
        padding: 2.5rem 2rem;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        max-width: 500px;
        width: 100%;
        margin: 2rem auto; /* centers container */
    }

    h2 {
        text-align: center;
        color: #1f2937;
        margin-bottom: 1rem;
        font-size: 1.6rem;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        font-weight: 500;
        margin-bottom: 0.3rem;
        color: #374151;
    }

    input[type="text"] {
        padding: 0.75rem;
        font-size: 1rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        margin-bottom: 1.2rem;
        background-color: #f9fafb;
        transition: border 0.3s ease, box-shadow 0.3s ease;
    }

    input[type="text"]:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        background-color: #fff;
    }

    input[type="submit"] {
        background: linear-gradient(to right, #3b82f6, #2563eb);
        color: #fff;
        padding: 0.9rem;
        font-size: 1.05rem;
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

    .error, .success {
        padding: 1rem;
        margin-bottom: 1.2rem;
        border-radius: 6px;
        text-align: center;
        font-weight: 500;
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
    }

    @media (max-width: 500px) {
        .container {
            padding: 2rem 1.2rem;
        }
    }
</style>

<div class="container">
    <h2>Update Security Questions</h2>

    <?php if ($error_message): ?>
        <div class="error"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <?php if ($operation_successful): ?>
        <div id="successMessage" class="success">If the user exists, answers have been updated — redirecting...</div>
        <script>
            setTimeout(() => {
                window.location.href = 'manage.php';
            }, 1500);
        </script>
    <?php endif; ?>

    <form action="reset.php" method="post" autocomplete="off">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required />

        <label for="new_answer1">New Answer to Security Question 1</label>
        <input type="text" id="new_answer1" name="new_answer1" required />

        <label for="new_answer2">New Answer to Security Question 2</label>
        <input type="text" id="new_answer2" name="new_answer2" required />

        <label for="new_answer3">New Answer to Security Question 3</label>
        <input type="text" id="new_answer3" name="new_answer3" required />

        <input type="submit" value="Update Security Questions" />
    </form>
</div>

</div> <!-- Close main-content -->
</div> <!-- Close wrapper -->
</body>
</html>
