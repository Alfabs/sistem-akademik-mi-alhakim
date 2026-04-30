<!-- header.php -->

<?php
require_once __DIR__ . '/../config/app.php';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MI AL-HAKIM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/sistem-akademik-mi-alhakim/assets/css/style.css">

    <style>
        body{
            font-family: 'Montserrat', sans-serif;
            background:#f8f9fa;
            margin:0;
        }

        #wrapper{
            display:flex;
            min-height:100vh;
        }

        #page-content-wrapper{
            flex:1;
            display:flex;
            flex-direction:column;
        }

        .main-content{
            padding:20px;
        }
    </style>
</head>
<body>

<div id="wrapper">