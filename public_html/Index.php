<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\{
    Validator,
    Format,
};
use DevUtils\conf\Conf;

require_once '../conf/Conf.php';
require_once 'AutoInstall.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

(new Conf());
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>brunoconte3/dev-utils</title>
    <link rel="stylesheet" href="static/css/index.min.css">
</head>

<body>
    <div class="container">
        <header id="body-title-page">
            <h1>brunoconte3/dev-utils</h1>
            <small>Espaço para fazer seus testes</small>
        </header>
        <section class="body-section-class">
            <div class="item-section-class">
                <div>
                    <?php
                    echo '<p>Aqui vem os seus testes!</p>';
                    $array = [
                        'cpfOuCnpj' => '04764334879',
                        'nomePais' => 'Brasil',
                        'dadosEmpresa' => ['empresa' => 'cooper'],
                    ];
                    $rules = [
                        'cpfOuCnpj' => 'identifierOrCompany',
                        'nomePais' => 'required|alpha',
                        'dadosEmpresa' => 'required|array',
                    ];
                    $validator = new Validator();
                    $validator->set($array, $rules);
                    ?>
                    <pre>
                        <?php
                        if (empty($validator->getErros())) {
                            echo '<p style="background-color:green;">Sucesso! dados válidos!</p>';
                        } else {
                            echo '<p style="background-color:red;">Revise a entrada!<pre></p>';
                            print_r($validator->getErros());
                        }
                        ?>
                    <hr />
                    </pre>
                </div>
                <div>
                    <?php
                    if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
                        $fileUploadSingle = $_FILES['fileUploadSingle'];
                        $fileUploadMultiple = $_FILES['fileUploadMultiple'];
                        $array = [
                            'fileUploadSingle' => $fileUploadSingle,
                            'fileUploadMultiple' => $fileUploadMultiple
                        ];
                        $ruleSingle = 'requiredFile|fileName|mimeType:jpeg;png;jpg;txt;docx;xlsx;pdf|minUploadSize:10|';
                        $ruleSingle .= 'maxUploadSize:30000|maxFile:1|minWidth:200|maxWidth:200|minHeight:200|';
                        $ruleSingle .= 'maxHeight:200';
                        $ruleMultiple = 'fileName|mimeType:jpeg;png|minFile:1|maxFile:3|minUploadSize:10';
                        $ruleMultiple .= '|minWidth:200|maxWidth:200|minHeight:200|maxHeight:200|';
                        $ruleMultiple .= 'maxUploadSize:30000, Mensagem personalizada aqui!';
                        $rules = [
                            'fileUploadSingle' => $ruleSingle,
                            'fileUploadMultiple' => $ruleMultiple
                        ];
                        $validator = new Validator();
                        $validator->set($array, $rules); ?>
                        <pre>
                            <?php print_r($validator->getErros()); ?>
                        <hr>
                        <pre>
                        <?php
                        print_r(Format::restructFileArray($fileUploadSingle));
                        print_r(Format::restructFileArray($fileUploadMultiple));
                    }
                        ?>
                    <div id="bd-form-upload">
                        <form method="POST" enctype="multipart/form-data">
                            <!-- Upload de um único arquivo. -->
                            <div>
                                <label for="fileUploadSingle">Upload de um arquivo</label>
                                <input type="file" name="fileUploadSingle" />
                            </div>
                            <!-- Upload de um ou múltiplos arquivos. -->
                            <div>
                                <label for="fileUploadSingle">Upload de múltiplos arquivo</label>
                                <input type="file" name="fileUploadMultiple[]" multiple="multiple">
                            </div>
                            <div>
                                <button type="submit">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
