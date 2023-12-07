<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./assets/images/logotipo.jpg" type="image/x-icon">
        <link rel="stylesheet" href="./css/style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <title>VideoClub • Marcos</title>
    </head>
    <body class="body">
        <div class="contenedor">
            <!-- INICIO DEL HEADER -->
            <header>
                <h1 class="header__h1">Bienvenido al Video Club Marcos</h1>    
            </header>
            <!-- FIN DEL HEADER -->

            <!-- INICIO DEL MAIN -->
            <main class="main">
                <div class="mt-5 box__img">
                    <img class="main__img rounded" src="./assets/images/logotipo.jpg" alt="alt"/>
                </div>
                <div class="mt-5 d-flex flex-column align-items-center">
                    <h2 class="main__title">Iniciar Sesión</h2>
                    <form class="d-flex flex-column align-items-center" method="post" action="./pages/calendario.php">
                        <div class="mt-4 box__form">
                            <label class="form-label">Usuario</label>
                            <input maxlength="20" name="user" type="text" class="form-control w-50 outline-0" id="inputEmail1">
                        </div>
                        <div class="mt-4 box__form">
                            <label class="form-label">Contraseña</label>
                            <input maxlength="15" name="password" type="password" class="form-control w-50 outline-0" id="inputPassword1">
                        </div>
                        <button class="mt-3 main__btn d-flex justify-content-center border-0 rounded" type="submit">Entrar</button>
                    </form>
                </div>               
            </main>
            <!-- FIN DEL MAIN -->
        </div> 
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>
</html>