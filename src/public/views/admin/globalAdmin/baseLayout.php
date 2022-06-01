<!DOCTYPE html>
<html>
<head>
    <title>MyAdmiin</title>
    <link rel="stylesheet" href="../../css/index.css">
    <script src='../../../js/jquery.js'></script>
    <script src="../../../js/bootstrap.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<style>
    body {
        background-color: #f8f9fa!important;
    }

    .container-new {
        margin: 0;
        display: flex;
        height: 100vh;
        width: 100vw;
    }

    .container-action {
        border-radius: 1em;
        background-color: white;
        padding: 1em;
    }
</style>
<body>
<?php echo $templateAdmin->getfirstNav() ;?>
<div class="container-new container-fluid">
<div style="min-width: 20%; background-color: #f8f9fa!important;">
<?php echo $templateAdmin->getLateralNav(); ?>
</div>


