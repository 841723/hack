<?php

require_once('functions.global.php');
require_once('layout_login.php');

function deliverMiddle_login($toptext, $formHtml, $bottomtext = '') {
    $middle = '
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4a90e2 0%, #50e3c2 100%);
            color: #333;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        #wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            box-sizing: border-box;
        }

        #main {
            width: 100%;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        #main header {
            margin-bottom: 2rem;
        }

        #main header h1 {
            font-weight: 700;
            font-size: 2.5rem;
            color: #fff;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
            margin: 0;
        }

        .signin-form {
            background: #fff;
            padding: 3rem 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1), 0 6px 6px rgba(0,0,0,0.08);
            max-width: 100%;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            box-sizing: border-box;
        }

        .signin-form:hover {
            box-shadow: 0 15px 25px rgba(0,0,0,0.15), 0 8px 8px rgba(0,0,0,0.12);
        }

        .signin-form input {
            font-size: 1.125rem;
            padding: 0.85rem 1.2rem;
            border-radius: 8px;
            border: 1.8px solid #ddd;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            outline-offset: 2px;
            outline-color: transparent;
            width: 100%;
            box-sizing: border-box;
        }

        .signin-form input:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 8px rgba(74, 144, 226, 0.5);
            outline-color: #4a90e2;
        }

        .signin-form .btn.signin-btn {
            background-color: #4a90e2;
            color: white;
            font-weight: 600;
            font-size: 1.125rem;
            padding: 0.9rem 1.2rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            box-shadow: 0 4px 6px rgba(74,144,226,0.4);
            transition: background-color 0.25s ease, box-shadow 0.25s ease;
            user-select: none;
        }

        .signin-form .btn.signin-btn:hover,
        .signin-form .btn.signin-btn:focus {
            background-color: #357ABD;
            box-shadow: 0 6px 12px rgba(53,122,189,0.6);
            outline: none;
        }

        .signin-form .btn.signin-btn i {
            font-size: 1.3rem;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }

        @media (max-width: 480px) {
            #main header h1 {
                font-size: 2rem;
            }

            .signin-form {
                padding: 2rem 1.5rem;
                gap: 1rem;
            }
        }
    </style>

    <div id="wrapper">
        <section id="main">
            <header>
                <h1>' . htmlspecialchars($toptext) . '</h1>
            </header>
            ' . $formHtml . '
            ' . $bottomtext . '
        </section>
    </div>
    ';

    return $middle;
}


$error_message = '';

if (isset($_POST['submitted']) && $_POST['submitted'] == true) {

    $login_username = $_POST['username'] ?? '';

    // Verify username exists
    $valid_usernames = contactDB("SELECT user_name FROM users", 0);

    if (!in_array($login_username, $valid_usernames)) {
        $error_message = 'Invalid username or password.';
    } else {
        $relevant_password_hash = contactDB("SELECT user_password FROM users WHERE user_name='$login_username';", 0)[0];
        $relevant_user_id = contactDB("SELECT user_id FROM users WHERE user_name='$login_username';", 0)[0];

        if (password_verify($_POST['password'], $relevant_password_hash)) {
            $_SESSION['eravalid'] = true;
            $_SESSION['erauser'] = $relevant_user_id;
            header('Location: manage.php');
            exit;
        } else {
            $error_message = 'Invalid username or password.';
            $_SESSION['eravalid'] = false;
            $_SESSION['erauser'] = null;
        }
    }
}

echo deliverTop("Era - Sign in");

$error_html = '';
if (!empty($error_message)) {
    $error_html = '<div class="error-message" role="alert" aria-live="assertive" style="color:#b00020; background:#f8d7da; border:1px solid #f5c2c7; padding:12px; border-radius:6px; margin-bottom:1rem; font-weight:600;">' . htmlspecialchars($error_message) . '</div>';
}

echo deliverMiddle_login(
    "Sign In",
    $error_html . '
    <form action="login.php" method="post" class="signin-form" novalidate>
        <input type="hidden" name="submitted" value="true">

        <input type="text" name="username" id="username" placeholder="Username" required autofocus value="' . htmlspecialchars($_POST['username'] ?? '') . '">

        <input type="password" name="password" id="password" placeholder="Password" required>

        <button type="submit" class="btn signin-btn">
            <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
    </form>
    '
);

?>
