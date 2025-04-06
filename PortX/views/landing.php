<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive admin dashboard and web application ui kit. ">
    <meta name="keywords" content="login, signin">

    <title>Pro Port</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,300i" rel="stylesheet">

    <!-- Styles -->
    <link href="./css/core.min.css" rel="stylesheet">
    <link href="./css/app.min.css" rel="stylesheet">
    <link href="./css/style.min.css" rel="stylesheet">

    <!-- Favicons -->
    <link rel="apple-touch-icon" href="./img/apple-touch-icon.png">
    <link rel="icon" href="./img/favicon.png">
</head>

<body>


<div class="row no-gutters min-h-fullscreen bg-white">
    <div class="col-md-6 col-lg-7 col-xl-8 d-none d-md-block bg-img" style="background-image: url(./img/gallery/11.jpg)" data-overlay="5">

        <div class="row h-100 pl-50">
            <div class="col-md-10 col-lg-8 align-self-end">
                <img src="./img/logo-light-lg.png" alt="...">
                <br><br><br>

                <br><br>
            </div>
        </div>

    </div>



    <div class="col-md-6 col-lg-5 col-xl-4 align-self-center">
        <div class="px-80 py-30">
            <h4>Login</h4>
            <p><small>Sign into your account</small></p>
            <?php if (!empty($msg)){ echo $msg; }  ?>
            <br>
            <form action="" method="" onsubmit="Login.login(event)">
                <div class="form-group">
                    <input type="text" class="form-control" name="email" id="email" placeholder="Email">
                    <label for="email">Email</label>
                    <span id="error_em" style="color:red;"></span>
                </div>

                <div class="form-group">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                    <label for="password">Password</label>
                    <span id="error_pw" style="color:red;"></span>
                </div>

                <div class="form-group flexbox">
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" checked>
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description">Remember me</span>
                    </label>

                    <a class="text-muted hover-primary fs-13" href="#">Forgot password?</a>
                </div>

                <div class="form-group">
                    <button name="submit" class="btn btn-bold btn-block btn-primary" type="submit">Login
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>




<!-- Scripts -->
<script src="./js/core.min.js"></script>
<script src="./js/app.min.js"></script>
<script src="./js/script.min.js"></script>
<script src="./js/main.js"></script>

</body>
</html>