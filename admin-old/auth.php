<?php
session_start();

$timeout = 900; // 15 minutes

if (!isset($_SESSION['admin'])) {
    header("Location: login");
    exit();
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: login?expired=1");
    exit();
}

$_SESSION['last_activity'] = time();