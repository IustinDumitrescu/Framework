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
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
    <a class="navbar-brand" href="#">MySite</a>
        <ul class="navbar-nav flex-row">
            <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="/calculator">Calculator</a></li>
        </ul>
        <ul class="navbar-nav flex-row ml-md-auto">
            <?php if (!$logged) { ?>
            <li class="nav-item"><a class=" nav-link" href="/login"><i class="fa-solid fa-right-to-bracket"></i>  Login</a></li>
            <li class="nav-item"><a class="nav-item nav-link " href="/register"><i class="fa-solid fa-user"></i> Register</a></li>
            <?php } else { ?>
                <li class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                       <?php echo $user->getEmail()?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="/logout">Logout</a>
                    </div>
                </li>

            <?php }?>
        </ul>
    </div>
</nav>


