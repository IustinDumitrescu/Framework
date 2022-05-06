<!DOCTYPE html>
<html>
<head>
    <title>Mysite</title>
    <link rel="stylesheet" href="../../css/index.css">
    <script src='../../js/jquery.js'></script>
    <script src="../../js/bootstrap.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<style>
    .custom_padding-all {
        padding: 40px;
    }

</style>
<div class="container">
    <div style="margin: 2em auto; text-align: center">
        <h1>Admin Login</h1>
    </div>
    <div class="container pt-1">
        <div class="col-xl-5 col-lg-7 col-md-7 col-12 mx-auto">
            <div class='bg-white shadow rounded custom_padding-all'>
                <?php if (isset($flash)) {
                    if ($flash["flashType"]) {
                        echo "
                    <div class=\"alert alert-success\" role=\"alert\">
                    {$flash["flashString"]}
                    </div>
                    ";
                    } else {
                        echo "<div class=\"alert alert-danger\" role=\"alert\">
                     {$flash["flashString"]}
                    </div>
                    ";
                    }
                } ?>
                <?php echo $formLoginAdmin["form"] ;?>
            </div>
        </div>
    </div>

</body>
</html>