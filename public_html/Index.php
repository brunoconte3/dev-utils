<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\{
    Validator,
    Format,
};
use DevUtils\conf\Conf;
use DevUtils\ValidateCard;

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
                        function showResult($validator, $successMsg = 'Sucesso! dados válidos!') {
                            if (empty($validator->getErros())) {
                                echo '<p class="alert-success">' . $successMsg . '</p>';
                            } else {
                                echo '<p class="alert-error">Revise a entrada</p>';
                                echo '<pre class="error-details">';
                                print_r($validator->getErros());
                                echo '</pre>';
                            }
                        }
                        showResult($validator);
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
                        if (empty($fileUploadSingle['name'][0]) && empty($fileUploadMultiple['name'][0])) {
                            echo '<p class="alert-warning">⚠ Nenhum arquivo foi enviado!</p>';
                        } else {
                            showResult($validator, '✓ Sucesso! Arquivos válidos!');
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
            <div class="item-section-class">
                <div>
                    <!-- Formulário para testar cartões -->
                    <div class="item-section-class">
                        <h3>Teste de Validação de Cartão</h3>
                        <form method="POST">
                            <div>
                                <label for="cartao_numero">Número do Cartão:</label>
                                <input type="text" name="cartao_numero" id="cartao_numero" placeholder="Ex: 4111111111111111" value="<?php echo $_POST['cartao_numero'] ?? ''; ?>">
                                <small>Digite um número de cartão válido (Visa, Mastercard, Elo, Hipercard, Amex)</small>
                                </div>
                            <div>
                                <label for="cartao_cvv">CVV:</label>
                                <input type="text" name="cartao_cvv" id="cartao_cvv" placeholder="Ex: 123" value="<?php echo $_POST['cartao_cvv'] ?? ''; ?>">
                                <small>Digite um CVV válido (3 dígitos para Visa/Mastercard/Elo/Hipercard, 4 dígitos para Amex)</small>
                            </div>
                            <div>
                                <button type="submit" name="testar_cartao">Testar Cartão</button>
                            </div>
                        </form>
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['testar_cartao'])) {
                            $numero = $_POST['cartao_numero'] ?? '';
                            $cvv = $_POST['cartao_cvv'] ?? '';
                            $bandeira = null;
                            $erros = [];
                            if (ValidateCard::isVisa($numero)) {
                                $bandeira = 'Visa';
                            } elseif (ValidateCard::isMastercard($numero)) {
                                $bandeira = 'Mastercard';
                            } elseif (ValidateCard::isElo($numero)) {
                                $bandeira = 'Elo';
                            } elseif (ValidateCard::isHipercard($numero)) {
                                $bandeira = 'Hipercard';
                            } elseif (ValidateCard::isAmex($numero)) {
                                $bandeira = 'Amex';
                            } else {
                                $erros['cartao_numero'] = 'Bandeira não reconhecida ou número inválido!';
                            }
                            if (!ValidateCard::isValidCvv($cvv, $bandeira)) {
                                $erros['cartao_cvv'] = 'CVV inválido!';
                            }
                            echo '<hr><h4>Resultado:</h4>';
                            function showCardResult($erros, $bandeira) {
                                if (empty($erros)) {
                                    echo '<p class="alert-success">Sucesso! dados válidos!</p>';
                                    echo '<pre class="success-details">';
                                    echo 'cartao_numero: Cartão ' . $bandeira . ' válido' . PHP_EOL;
                                    echo 'cartao_cvv: CVV válido!';
                                    echo '</pre>';
                                } else {
                                    echo '<p class="alert-error">Revise a entrada</p>';
                                    echo '<pre class="error-details">';
                                    print_r($erros);
                                    echo '</pre>';
                                }
                            }
                            showCardResult($erros, $bandeira);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
