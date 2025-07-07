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
                    
                    // Processar dados do formulário se enviado
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && array_key_exists('testar_validacao', $_POST)) {
                        $array = [
                            'cpfOuCnpj' => $_POST['cpfOuCnpj'] ?? '',
                            'nomePais' => $_POST['nomePais'] ?? '',
                            'dadosEmpresa' => $_POST['dadosEmpresa'] ?? '',
                        ];
                        $rules = [
                            'cpfOuCnpj' => 'identifierOrCompany',
                            'nomePais' => 'required|alpha|min:3|max:30',
                            'dadosEmpresa' => 'required|alpha|min:3|max:80',
                        ];
                        $validator = new Validator();
                        $validator->set($array, $rules);
                    }
                    ?>
                    
                    
                    <!-- Formulário para testar validações -->
                    <div class="item-section-class">
                        <h3>Teste de Validação de Dados</h3>
                        <form method="POST">
                            <div>
                                <label for="cpfOuCnpj">CPF ou CNPJ:</label>
                                <input type="text" name="cpfOuCnpj" id="cpfOuCnpj" placeholder="Ex: 04764334879 ou 39.678.379/0001-29" 
                                       value="<?php echo $_POST['cpfOuCnpj'] ?? ''; ?>">
                                <small>Digite um CPF ou CNPJ válido (com ou sem máscara)</small>
                            </div>
                            
                            <div>
                                <label for="nomePais">Nome do País:</label>
                                <input type="text" name="nomePais" id="nomePais" placeholder="Ex: Brasil" 
                                       value="<?php echo $_POST['nomePais'] ?? ''; ?>">
                                <small>Digite apenas letras e espaços (3-30 caracteres)</small>
                            </div>
                            
                            <div>
                                <label for="dadosEmpresa">Nome da Empresa:</label>
                                <input type="text" name="dadosEmpresa" id="dadosEmpresa" placeholder="Ex: cooper" 
                                       value="<?php echo $_POST['dadosEmpresa'] ?? ''; ?>">
                                <small>Digite o nome da empresa (3-80 caracteres)</small>
                            </div>
                            
                            <div>
                                <button type="submit" name="testar_validacao">Testar Validação</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Resultado da validação -->
                    <?php if (isset($validator)): ?>
                        <?php
                        if (empty($validator->getErros())) {
                            echo '<p style="background-color:green;color:white;">Sucesso! dados válidos!</p>';
                        } else {
                            echo '<p style="background-color:red;color:white;">Revise a entrada&#8628;</p>';
                            echo '<pre style="background-color: #f8d7da; font-family: math;padding: 1rem;color: #510651; border:1px solid #f5c6cb;">';
                            print_r($validator->getErros());
                            echo '</pre>';
                        }
                        ?>
                    <?php endif; ?>
                    
                    <hr />
                    
                    <!-- Exemplo hardcoded (old) -->
                    <!--
                    <div class="item-section-class">
                        <h4>Exemplo:</h4>
                        <?php
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
                                echo '<p style="background-color:red;">Revise a entrada!</p>';
                                print_r($validator->getErros());
                            }
                            ?>
                        </pre>
                    </div>
                    -->
                </div>
                <div class="item-section-class">
                    <h3>Teste de Upload de Arquivos</h3>
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_files'])) {
                        try {
                            $fileUploadSingle = $_FILES['fileUploadSingle'];
                            $fileUploadMultiple = $_FILES['fileUploadMultiple'];
                            
                            $array = [
                                'fileUploadSingle' => $fileUploadSingle,
                                'fileUploadMultiple' => $fileUploadMultiple
                            ];
                            $ruleSingle = 'requiredFile|fileName|mimeType:jpeg;png;jpg;txt;docx;xlsx;pdf|minUploadSize:10|';
                            $ruleSingle .= 'maxUploadSize:150000|maxFile:1'; // |minWidth:200|maxWidth:200|minHeight:200|maxHeight:200->Requer biblioteca GD
                            
                            $ruleMultiple = 'fileName|mimeType:jpeg;png|minFile:1|maxFile:3|minUploadSize:10|';
                            $ruleMultiple .= 'maxUploadSize:150000, Mensagem personalizada aqui!'; // |minWidth:200|maxWidth:200|minHeight:200|maxHeight:200->Requer biblioteca GD
                            
                            $rules = [];
                            if (!empty($fileUploadSingle['name'][0])) {
                                $rules['fileUploadSingle'] = $ruleSingle;
                            }
                            if (!empty($fileUploadMultiple['name'][0])) {
                                $rules['fileUploadMultiple'] = $ruleMultiple;
                            }
                            
                            $validator = new Validator();
                            $validator->set($array, $rules);
                            ?>
                            
                        <!-- Resultado da validação de upload -->
                        <?php
                        if (empty($validator->getErros()) && (!empty($fileUploadSingle['name'][0]) || !empty($fileUploadMultiple['name'][0]))) {
                            echo '<p style="background-color:green;color:white;">✓ Sucesso! Arquivos válidos!</p>';
                        } else {
                            if (empty($fileUploadSingle['name'][0]) && empty($fileUploadMultiple['name'][0])) {
                                echo '<p style="background-color:red;color:white;">⚠ Nenhum arquivo foi enviado!</p>';
                            } else {
                                echo '<p style="background-color:red;color:white;">⚠ Erros no upload!</p>';
                                echo '<pre style="background-color: #f8d7da; font-family: math;padding: 1rem;color: #510651; border:1px solid #f5c6cb;">';
                                print_r($validator->getErros());
                                echo '</pre>';
                            }
                        }
                        ?>
                            
                        <hr/>
                        <h4>Dados dos arquivos enviados:</h4>
                        <pre style="<?php echo (empty($validator->getErros()) && (!empty($fileUploadSingle['name'][0]) || !empty($fileUploadMultiple['name'][0]))) ? 'background-color: #d4edda; font-family: math;padding: 1rem;color: #510651; border:1px solid #c3e6cb;' : 'background-color: #f8d7da; font-family: math;padding: 1rem;color: #8B008B; border:1px solid #f5c6cb;'; ?> padding: 15px; border-radius: 4px;">
                            <?php
                            print_r(Format::restructFileArray($fileUploadSingle));
                            print_r(Format::restructFileArray($fileUploadMultiple));
                            ?>
                        </pre>
                        <?php } catch (Exception $e) {
                            echo '<p style="background-color:red;color:white;">ERRO: ' . $e->getMessage() . '</p>';
                        } ?>
                    <?php } ?>
                    <div id="bd-form-upload">
                        <form method="POST" enctype="multipart/form-data" action="">
                            <!-- Upload de um único arquivo. -->
                            <div>
                                <label for="fileUploadSingle">Upload de um arquivo</label>
                                <input type="file" name="fileUploadSingle" />
                            </div>
                            <!-- Upload de um ou múltiplos arquivos. -->
                            <div>
                                <label for="fileUploadMultiple">Upload de múltiplos arquivo</label>
                                <input type="file" name="fileUploadMultiple[]" multiple="multiple">
                            </div>
                            <div>
                                <button type="submit" name="upload_files" value="1">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
