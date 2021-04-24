<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\{
    Validator,
    Format,
};

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
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

                    $array = ['cpfOuCnpj' => '04764334879'];
                    $rules = ['cpfOuCnpj' => 'identifierOrCompany'];

                    $validator = new Validator();
                    $validator->set($array, $rules);

                    echo '<pre>';
                    print_r($validator->getErros());
                    ?>
                    <hr />
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
                        $ruleSingle .= 'maxUploadSize:100';

                        $ruleMultiple = 'fileName|mimeType:jpeg|minFile:1|maxFile:3|minUploadSize:10|maxUploadSize:100';
                        $ruleMultiple .= ', Mensagem personalizada aqui!';

                        $rules = [
                            'fileUploadSingle' => $ruleSingle,
                            'fileUploadMultiple' => $ruleMultiple
                        ];

                        $validator = new Validator();
                        $validator->set($array, $rules);

                        echo '<pre>';
                        print_r($validator->getErros());

                        echo '<hr>';

                        echo '<pre>';
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
