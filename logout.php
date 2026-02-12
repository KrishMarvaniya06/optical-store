<?php
session_start();

/* ✅ UNSET ALL SESSION VALUES */
$_SESSION = [];

/* ✅ DESTROY SESSION COMPLETELY */
session_destroy();

/* ✅ REDIRECT BACK TO HOME */
header("Location: index.php");
exit();