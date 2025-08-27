<?php
// Redirect if setup is not completed
if (file_exists("./setup.php")) {
    header('Location: ./setup.php');
    exit;
}

require_once('initial_layout.php');


echo deliverTop("Era - File Sharing Platform");

// Group 1: primary cards
$mainCards = [
    ['title' => 'Manage Files', 'desc' => 'View and manage your uploaded files.', 'link' => 'manage.php', 'icon' => 'fa-folder'],
    ['title' => 'Upload Files', 'desc' => 'Upload new files to your account.', 'link' => 'upload.php', 'icon' => 'fa-upload'],
    ['title' => 'Update Security Questions', 'desc' => 'Update your security questions.', 'link' => 'reset.php', 'icon' => 'fa-key'],
    ['title' => 'Sign In', 'desc' => 'Sign In as a different user.', 'link' => 'login.php', 'icon' => 'fa-sign-in-alt'],
];


// Render cards as HTML
// Main cards (grid)
// Render cards as HTML
$cardsHtml = '';
foreach ($mainCards as $card) {
    $cardsHtml .= '
    <div class="card">
        <div class="icon"><i class="fas '.$card['icon'].'"></i></div>
        <h3>'.$card['title'].'</h3>
        <p>'.$card['desc'].'</p>
        <a href="'.$card['link'].'" class="btn">Go</a>
    </div>';
}

echo deliverMiddle("Welcome to Era Storage!", "Secure. Simple. Smart.", $cardsHtml);

// Append note *outside* the card grid
echo '
    <div style="text-align: center; margin-top: 2rem;">
        <p style="font-size: 1rem; color: #555;">
            Alternatively, <a href="security_login.php" style="color: #007bff; text-decoration: none;">login using security questions</a>.
        </p>
    </div>';

echo deliverBottom();
