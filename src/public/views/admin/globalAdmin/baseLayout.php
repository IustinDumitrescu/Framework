<!DOCTYPE html>
<html>
<head>
    <title>MyAdmin</title>
    <link rel="stylesheet" href="../../../css/index.css">
    <script src='../../../js/jquery.js'></script>
    <script src="../../../js/bootstrap.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../../css/algolia.css">
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

    .switch {
        position: relative;
        display: inline-block;
        width: 42px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 17px;
        width: 17px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(18px);
        -ms-transform: translateX(18px);
        transform: translateX(18px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
<body>
<?php echo $templateAdmin->getfirstNav() ;?>
<div class="container-new container-fluid">
<div style="min-width: 20%; background-color: #f8f9fa!important;">
<?php echo $templateAdmin->getLateralNav(); ?>
</div>
 <script>
     function changeValue (element) {
        let idOfCheckbox = element.getAttribute('id');

        let idOfHidden = idOfCheckbox.substring(0, idOfCheckbox.length - 9);

        let hidden =  document.getElementById(`${idOfHidden}`);

        hidden.value = element.checked === true;

     }
 </script>



