<?php
session_start();

function deliverTop($pagetitle) {
    return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>' . htmlspecialchars($pagetitle) . '</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }
        #wrapper {
            display: flex;
            min-height: 100vh;
            width: 100vw;
            box-sizing: border-box;
        }
        /* Sidebar styles */
        .sidebar {
            width: 240px;
            background: #fff;
            border-radius: 0 12px 12px 0;
            box-shadow: 2px 0 15px rgba(0,0,0,0.07);
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        .sidebar-logo img {
            width: 80px;
            border-radius: 50%;
            margin-bottom: 1.5rem;
        }
        .sidebar nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            width: 100%;
        }
        .sidebar nav ul li {
            margin-bottom: 1.2rem;
            width: 100%;
        }
        .sidebar nav ul li a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            padding: 0.7rem 1rem;
            border-radius: 6px;
            transition: background 0.2s, color 0.2s;
            font-size: 1.05rem;
        }
        .sidebar nav ul li a i {
            margin-right: 0.8rem;
            font-size: 1.3rem;
        }
        .sidebar nav ul li a:hover, .sidebar nav ul li a.active {
            background: #007bff;
            color: #fff;
        }
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 2rem 2rem 2rem 0;
            min-width: 0;
        }
        /* Header and card styles */
        header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .avatar img {
            width: 100px;
            border-radius: 50%;
        }
        h1 {
            font-size: 2.5rem;
            margin: 1rem 0 0.5rem;
        }
        p {
            font-size: 1.1rem;
            color: #666;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card .icon {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 1rem;
        }
        .card h3 {
            margin: 0.5rem 0;
            font-size: 1.3rem;
        }
        .card p {
            color: #777;
        }
        .card .btn {
            margin-top: 1rem;
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 0.6rem 1.2rem;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .card .btn:hover {
            background-color: #0056b3;
        }
        footer ul.icons {
            display: none; /* optional: hide original button group */
        }
        main {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }
        /* Responsive styles */
        @media (max-width: 900px) {
            #wrapper {
                flex-direction: column;
                padding: 0;
            }
            .sidebar {
                flex-direction: row;
                width: 100%;
                border-radius: 0 0 12px 12px;
                margin: 0 0 1.5rem 0;
                justify-content: space-around;
                padding: 1rem 0.5rem;
                box-shadow: 0 2px 15px rgba(0,0,0,0.07);
            }
            .sidebar nav ul {
                display: flex;
                flex-direction: row;
                justify-content: space-around;
                width: 100%;
            }
            .sidebar nav ul li {
                margin-bottom: 0;
                margin-right: 0.5rem;
            }
            .sidebar-logo img {
                width: 60px;
                margin-bottom: 0;
                margin-right: 1rem;
            }
            .main-content {
                padding: 1rem 0.5rem 0.5rem 0.5rem;
            }
        }
        @media (max-width: 600px) {
            .card {
                padding: 1rem;
            }
            h1 {
                font-size: 2rem;
            }
            .main-content {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
';
}


function deliverMiddle($toptext, $bottomtext, $contentHtml) {
    return '
    <header>
        <div class="avatar">
            <a href="index.php">
                <img src="images/main.png" alt="Logo" />
            </a>
        </div>
        <h1>' . htmlspecialchars($toptext) . '</h1>
        <p>' . htmlspecialchars($bottomtext) . '</p>
    </header>

    <main>
        ' . $contentHtml . '
    </main>';
}

function deliverBottom() {
    $bottom = '
        <footer id="footer">
            <style>
                #footer {
                    margin-top: 3rem;
                    text-align: center;
                    padding: 2rem 0;
                    background: transparent;
                }

                .footer-signout .btn.logout {
                    display: inline-block;
                    background-color: #dc3545;
                    color: #fff;
                    padding: 0.6rem 1.2rem;
                    border-radius: 5px;
                    font-weight: 500;
                    text-decoration: none;
                    transition: background-color 0.3s;
                    font-size: 1rem;
                }

                .footer-signout .btn.logout:hover {
                    background-color: #a71d2a;
                }
            </style>

            <div class="footer-signout">
                <a href="logout.php" class="btn logout">
                    <i class="fas fa-sign-out-alt"></i> Sign Out
                </a>
            </div>
        </footer>

    </div> <!-- /#wrapper -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.body.classList.remove("is-preload");
            if (navigator.userAgent.match(/(MSIE|rv:11\\.0)/)) {
                document.body.classList.add("is-ie");
            }
        });
    </script>

    </body>
</html>';

    return $bottom;
}

?>
