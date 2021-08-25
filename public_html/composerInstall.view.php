<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Instalação do Docker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
</head>

<body>
    <h2></h2>
    <header>
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 text-center mt-1">
                    <div class="col-12 text-center">
                        <p class="h1"> Olá Dev! </p>
                    </div>
                    <div class="col-12 text-center text-muted mt-3">
                        <p class="text-center">Para configurar o seu ambiente de desenvolvimento antes precisamos instalar as
                            dependências necessárias do projeto.
                            <br>apenas clique no botão abaixo e aguarde...
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container" style="height:10vh;">
        <div class="row d-flex align-items-center justify-content-center h-100">
            <button id="btnIniciar" class="btn btn-success">
                <span>Clique Aqui</span><em class="ml-3 fas fa-spinner fa-spin d-none"></em>
            </button>
        </div>
    </div>
    <div class="container" style="height:355px;">
        <div class="row d-flex align-items-center justify-content-center">
            <div class="form-group col-10">
                <label for="textArea">Log:</label>
                <textarea class="form-control bg-light" id="textArea" rows="15" readonly></textarea>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <script type="text/javascript" src="static/js/notify.min.js"></script>
    <script type="text/javascript" src="static/js/funcoes.min.js"></script>
    <script type="text/javascript" src="static/js/composer-install.min.js"></script>

    <footer data-diretorio="<?= URL; ?>">
        <div class="col-12 text-center text-muted">
            Se falhar por algum motivo, utilize o comando "composer install" <br><br>
        </div>
    </footer>
</body>

</html>
