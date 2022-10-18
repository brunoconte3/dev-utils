# dev-utils

A complete library, with PSR standard and guarantee of all methods unit tested by phpunit and passed by phpstan.

- Class of Arrays
- Comparison Class
- Formatting Class
- Utility Class
- Validate Data in General
- Validate Upload Files

## Installation

composer.json

```
"brunoconte3/dev-utils": "2.4.0"
```

With composer, require

```
$ composer require brunoconte3/dev-utils
```

## Data Validation Example

`Data`

```php
$data = [
   'name'  => 'brunoconte3',
   'email' => 'brunoconte3@gmail.com',
   'validatePassingJson' => '@&451',
   'newPassword' => 'min:5',
   'confirmPassword' => 'min:5|equals:newPassword',
];
```

`Rules`

```php
$rules = [
   'name'  => 'required|regex:/^[a-zA-Z\s]+$/',
   'email' => 'required|email|max:50',
   'validatePassingJson' => '{"required":"true","type":"alpha"}',
];
```

`Validating the data according to the rules`

```php
  require 'vendor/autoload.php';

  $validator = new DevUtils\Validator();
  $validator->set($data, $rules);

    if(!$validator->getErros()){
       echo 'Data successfully validated';
   } else {
       var_dump($validator->getErros());
   }
```

## Validating File(s) Upload

With validators fileName, maxFile, maxUploadSize, mimeType, minFile, minUploadSize, minHeight, minWidth, maxHeight,
maxWidth and requiredFile, you can set the minimum and maximum size (bytes) of the file; minimum and maximum amount of files; allowed extensions; minimum and maximum height and length of images, validate the name of the file and define if the field of type "File" is mandatory.

`Example:`

```html
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    ...
  </head>
  <body>
    <form method="POST" enctype="multipart/form-data">
      <!-- Upload a single file. -->
      <input type="file" name="fileUploadSingle" />

      <!-- Uploading single or multiple files. -->
      <input type="file" name="fileUploadMultiple[]" multiple="multiple" />

      <button type="submit">Upload</button>
    </form>
  </body>
</html>
```

```php
<?php
    /**
     * Comments
     *
     * maxFile, minFile, minHeight, minWidth, maxUploadSize, maxHeight, maxWidth and minUploadSize: They must be of the integer type.
     * mimeType: To pass an array with the allowed extensions, just use the ';' between values.
     */
    if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
        $fileUploadSingle = $_FILES['fileUploadSingle'];
        $fileUploadMultiple = $_FILES['fileUploadMultiple'];

        $datas = [
            'fileUploadSingle' => $fileUploadSingle,
            'fileUploadMultiple' => $fileUploadMultiple,
        ];

        $rules = [
            'fileUploadSingle' => 'requiredFile|fileName|mimeType:jpeg;png;jpg;txt;docx;xlsx;pdf|minUploadSize:10|
            maxUploadSize:100|minWidth:200|maxWidth:200',
            'fileUploadMultiple' => 'fileName|mimeType:jpeg|minFile:1|maxFile:3|minUploadSize:10|
            minWidth:200|maxWidth:200|maxUploadSize:100, Mensagem personalizada aqui',
        ];

        $validator = new DevUtils\Validator();
        Format::convertTypes($datas, $rules);
        $validator->set($datas, $rules);

        if (!$validator->getErros()) {
            echo 'Data successfully validated';
        } else {
            echo '<pre>';
            print_r($validator->getErros());
        }
    }
```

## Validation types (validators)

- alpha: `Checks that the field contains only alphabetic characters`
- alphaNoSpecial: `Checks if the field contains regular text characters, it cannot have accents`
- alphaNum: `Checks if the field contains alphanumeric characters`
- alphaNumNoSpecial: `Checks if the field contains letters without accents, numbers, cannot special character`
- array: `Checks if the variable is an array`
- arrayValues: `Checks whether the variable has one of the options in the specified array`
- bool: `Values of logical type.` `Ex: true or false, 1 or 0, yes or no`
- companyIdentification: `Validates if the CNPJ is valid, passing CNPJ with or without mask`
- dateAmerican: `Validates if the American date is valid`
- dateBrazil: `Validates if the Brazilian date is valid`
- ddd: `Validates ddd informed in YYY or YY format, by UF or in general` `Ex: ddd:pr, ddd do Paraná/Brazil, or just ddd`
- email: `Check if it's a valid email`
- equals: `Checks if the field is the same as another field, example above in the documentation, look for equals`
- fileName: `Checks that the filename is a valid name, and formats the name by removing special characters`
- float: `Checks if the value is of type floating(real value)`
- hour: `Validates if the time is valid`
- identifier: `Validates if the CPF is valid, passing CPF with or without mask`
- identifierOrCompany: `Validates if the CPF or CNPJ is valid, passing CPF or CNPJ with or without mask`
- int: `Checks if the value is of type integer (If the format is String, it tries to parse it)`
- integer: `Checks if the value is of type integer (here checks exact typing)`
- ip: `Checks if the value is a valid IP address`
- json: `Checks if the value is a valid json`
- lower: `Checks if all characters are lowercase`
- mac: `Checks if the value is a valid MAC address`
- max: `Sets the maximum size of the value`
- minHeight: `Sets the minimum height size (pixels) of the image`
- minWidth: `Sets the minimum size in length (pixels) of the image`
- maxHeight: Sets the maximum height (pixels) size of the image``
- maxWidth: `Sets the maximum size in length (pixels) of the image`
- maxFile: `Sets the maximum number of files to upload`
- maxUploadSize: `Sets the maximum file size (bytes)`
- maxWords: `Defines the maximum number of words in a string`
- min: `Sets the minimum size of the value`
- minFile: `Sets the minimum amount of files to upload`
- minWords: `Defines the minimum number of words in a string`
- mimeType: `Defines the extension(s) allowed for upload`
- minUploadSize: `Sets the minimum file size (bytes)`
- numeric: `Checks if the value contains only numeric values (Left zero accepted)`
- numMax: `Sets a maximum value, with the minimum being zero`
- numMin: `Sets a minimum value, with the minimum being zero`
- numMonth: `Checks if the value is a valid month (1 to 12)`
- notSpace: `Checks if the string contains spaces`
- noWeekend: `Checks if the date (Brazilian or American is not a Weekend)`
- optional: `If inserted, it only validates if the value is different from empty, null or false`
- phone: `Checks if the value matches a valid phone. (DDD + NUMBERS) 10 or 11 digits`
- plate: `Checks if the value matches the shape of a license plate`
- regex: `Defines a rule for the value through a regular expression`
- required: `Set the field to mandatory`
- requiredFile: `Sets the field of type 'File' as mandatory`
- rgbColor: `Checks if the string has a valid RGB Color`
- timestamp: `Checks if the value is a valid timestamp (accepts Brazilian or American format)`
- upper: `Checks if all characters are uppercase`
- url: `Checks if the value is a valid URL address`
- zipcode: `Checks if the value matches the format of a zip code`

## Defining custom message

After defining some of our rules to the data you can also add a custom message using the ',' delimiter in some specific rule or using the default message.

`Example:`

```php
<?php

    $validator->set($datas, [
        'name'  => 'required, The name field cannot be empty',
        'email' => 'email, The email field is incorrect|max:50',
        'password' => 'min:8, nat least 8 characters|max:12, no máximo 12 caracteres.',
    ]);
```

## Formatting Examples

```php
<?php

require 'vendor/autoload.php';

use DevUtils\Format;

Format::companyIdentification('39678379000129'); //CNPJ ==> 39.678.379/0001-29
Format::convertTimestampBrazilToAmerican('15/04/2021 19:50:25'); //Convert Timestamp Brazil to American format
Format::currency('113', 'R$ '); //Default currency BR ==> 123.00 - the 2nd parameter chooses the Currency label
Format::currencyUsd('1123.45'); //Default currency USD ==> 1,123.45 - the 2nd parameter chooses the Currency label
Format::dateAmerican('12-05-2020'); //return date ==>  2020-05-12
Format::dateBrazil('2020-05-12'); //return date ==>  12/05/2020
Format::identifier('73381209000');  //CPF ==>  733.812.090-00
Format::identifierOrCompany('30720870089'); //CPF/CNPJ Brazil ==> 307.208.700-89
Format::falseToNull(false); //Return ==> null
Format::lower('CArrO'); //lowercase text ==> carro - the 2nd parameter chooses the charset, UTF-8 default
//[Apply any type of Mask, accepts space, points and others]
Format::mask('#### #### #### ####', '1234567890123456'); //Mask ==> 1234 5678 9012 3456
Format::maskStringHidden('065.775.009.96', 3, 4, '*'); //Mask of string ==> 065.***.009.96
Format::onlyNumbers('548Abc87@'); //Returns only numbers ==> 54887;
Format::onlyLettersNumbers('548Abc87@'); //Returns only letters and numbers ==> 548Abc87;
Format::pointOnlyValue('1.350,45'); //Currency for recording on the BD ==>  1350.45
Format::removeAccent('Açafrão'); //Remove accents and character 'ç' ==> Acafrao
//Removes all special characters ==> "Acafrao com Espaco", 2nd parameter chooses whether to allow space, default true
Format::removeSpecialCharacters('Açafrão com Espaco %$#@!', true);
Format::returnPhoneOrAreaCode('44999998888', false); //Returns only the phone number ==> 999998888
Format::returnPhoneOrAreaCode('44999998888', true); //Returns only the phone's area code ==> 44
Format::reverse('Abacaxi'); //Returns inverted string ==> ixacabA
Format::telephone('44999998888');  //Return phone format brazil ==> (44) 99999-8888
Format::ucwordsCharset('aÇafrÃo maCaRRão'); //Return first capital letter ==> Açafrão Macarrão
Format::upper('Moto'); //lowercase text ==> MOTO - the 2nd parameter chooses the charset, UTF-8 default
Format::zipCode('87030585'); //CEP format brazil ==>  87030-585
Format::writeDateExtensive('06/11/2020'); //Date by Long Brazilian format ==> sexta-feira, 06 de novembro de 2020
Format::writeCurrencyExtensive(1.97); //Coin by Extensive Brazilian format ==> um real e noventa e sete centavos
Format::convertStringToBinary('amor'); //String to binary ==> 1100001 1101101 1101111 1110010
Format::slugfy('Polenta frita e Parmesão'); //Returns a slug from a string ==> polenta-frita-e-parmesao

$data = [
    'treatingIntType' => '12',
    'handlingFloatType' => '9.63',
    'treatingBooleanType' => 'true',
    'handlingNumericType' => '11',
];
$rules = [
    'treatingIntType' => 'convert|int',
    'handlingFloatType' => 'convert|float',
    'treatingBooleanType' => 'convert|bool',
    'handlingNumericType' => 'convert|numeric',
];
Format::convertTypes($data, $rules); //Convert the value to its correct type ['bool', 'float', 'int', 'numeric',]
/*** Return
[
  'treatingIntType' => int 12
  'handlingFloatType' => float 9.63
  'treatingBooleanType' => boolean true
  'handlingNumericType' => float 11
]
***/

$array = [
    0 => '1',
    1 => '123',
    'a' => '222',
    'b' => 333,
    'c' => '',
];
$newArray = Format::emptyToNull($array); //Convert empty to null, - the 2nd parameter is optional, passing the desired exception
/*** Return
[
  0 => 1,
  1 => 123,
  'a' => 222,
  'b' => 333,
  'c' => null,
];
**/

//$value = Format::arrayToInt($array); ==> Option for other than by Reference
Format::arrayToIntReference($array); //Formats array values in integer ==>
[
  0 => 1,
  1 => 123,
  'a' => 222,
  'b' => 333,
  'c' => 0,
];

```

## Formatting Upload File(s)

`Example: Uploading a single file`

```php
<?php

$fileUploadSingle = [
    'name' => 'JPG - Upload Validation v.1.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => '/tmp/phpODnLGo',
    'error' => 0,
    'size' => 8488,
];

Format::restructFileArray($fileUploadSingle); // Call of the method responsible for normalizing the array
[
    0 => [
        'name' => 'jpg___upload_validation_v_1.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => '/tmp/phpBmqX1i',
        'error' => 0,
        'size' => 8488,
        'name_upload' => '22-01-2021_13_1830117018768373446425980271611322393600ad419619ec_jpg___upload_validation_v_1.jpg',
    ]
]

```

`Example: Uploading multiple files`

```php
<?php

$fileUploadMultiple = [
	'name' => [
		'0' => 'JPG - Upload Validation v.1.jpg',
		'1' => 'PDF - Upload Validation v.1.pdf',
		'2' => 'PNG - Upload Validation v.1.png',
	],
	'type' => [
		'0' => 'image/jpeg',
		'1' => 'application/pdf',
		'2' => 'image/png',
	],
	'tmp_name' => [
		'0' => '/tmp/phpODnLGo',
		'1' => '/tmp/phpfmb0tL',
		'2' => '/tmp/phpnoejk8',
	],
	'error' => [
		'0' => 0,
		'1' => 0,
		'2' => 0,
	],
	'size' => [
		'0' => 8488,
		'1' => 818465,
		'2' => 1581312,
	],
];

Format::restructFileArray($fileUploadMultiple); // Call of the method responsible for normalizing the array
[
	0 => [
		'name' => 'jpg___upload_validation_v_1.jpg',
		'type' => 'image/jpeg',
		'tmp_name' => '/tmp/phpBmqX1i',
		'error' => 0,
		'size' => 8488,
		'name_upload' => '22-01-2021_13_1830117018768373446425980271611322393600ad419619ec_jpg___upload_validation_v_1.jpg',
	],
	1 => [
		'name' => 'pdf___upload_validation_v_1.pdf',
		'type' => 'application/pdf',
		'tmp_name' => '/tmp/phpYo0w7c',
		'error' => 0,
		'size' => 818465,
		'name_upload' => '22-01-2021_13_170624609160164419213582611971611322393600ad41961a5a_pdf___upload_validation_v_1.pdf',
	],
	2 => [
		'name' => 'png___upload_validation_v_1.png',
		'type' => 'image/png',
		'tmp_name' => '/tmp/phpme7Yf7',
		'error' => 0,
		'size' => 1581312,
		'name_upload' => '22-01-2021_13_8675237129330338531328755051611322393600ad41961ac8_png___upload_validation_v_1.png',
	],
]

```

## Comparisons Examples

```php
<?php

require 'vendor/autoload.php';

use DevUtils\Compare;

//Returns +30 (+30 days difference)
Compare::daysDifferenceBetweenData('31/05/2020', '30/06/2020'); //Accepts American date too

//Compares if start date is less than end date => Returns [bool]
Compare::startDateLessThanEnd('30/07/2020', '30/06/2020'); //Accepts American date too

//Difference between hours ==> 01:36:28 [Hours displays negative and positive difference]
Compare::differenceBetweenHours('10:41:55', '12:18:23');

//Compares if the start time is less than the end time (3rd parameter, accept custom message)
Compare::startHourLessThanEnd('12:05:01', '10:20:01');

//Compares the date to the current date, and returns the person's age
Compare::calculateAgeInYears('20/05/1989');

//Compares fields for equality, returns boolean
//optional third parameter, false to not compare caseSensitive, default true
Compare::checkDataEquality('AçaFrão', 'Açafrão');

//Compares if desired content exists in String, returns boolean
Compare::contains('AçaFrão', 'çaF');

//Compares the corresponding URL with the second parameter, starts with the string entered in the first parameter. Returns boolean.
Compare::beginUrlWith('/teste', '/teste/variavel');

//Compares the corresponding URL with the second parameter, ends with the string entered in the first parameter. Returns boolean.
Compare::finishUrlWith('/teste', 'sistema/teste');

//Compares if the corresponding string with the first parameter is equal to the substring obtained from the second parameter. Extracting to compare 7 characters from the second parameter starting at position 0. Returns boolean.
Compare::compareStringFrom('sistema', 'sistema/teste', 0, 7);

```

## Validations in the form of Methods

```php
<?php

require 'vendor/autoload.php';

use DevUtils\ValidateCnpj;
ValidateCnpj::validateCnpj('57.169.078/0001-51'); //Returns boolean, example true [Can pass with mask]

use DevUtils\validateCpf;
validateCpf::validateCpf('257.877.760-89'); //Returns boolean, example true [Can pass with mask]

use DevUtils\ValidateDate;
//Examples return true
ValidateDate::validateDateBrazil('29/04/2021'); //Return boolean [Format dd/mm/yyyy]
ValidateDate::validateDateAmerican('2021-04-29'); //Return boolean [Format yyyy-mm-dd]
ValidateDate::validateTimeStamp('2021-04-29 11:17:12'); //Return boolean [Format yyyy-mm-dd hh:mm:ss]

use DevUtils\ValidateHour;
ValidateHour::validateHour('08:50'); //Return boolean [Format YY:YY]

use DevUtils\ValidatePhone;
ValidatePhone::validate('44999999999'); //Return boolean [[You can wear a mask]

use DevUtils\ValidateString;
ValidateString::minWords('Bruno Conte', 2) //Return boolean
ValidateString::maxWords('Bruno Conte', 2) //Return boolean
```

## Manipulate Arrays

```php
<?php

require 'vendor/autoload.php';

use DevUtils\Array;

$array = ['primeiro' => 15, 'segundo' => 25];
var_dump(Arrays::searchKey($array, 'primeiro'));   // Search for key in array, and Return position ==> returns 0
var_dump(Arrays::searchKey($array, 'segundo'));    // Search for key in array, and Return position ==> returns 1
var_dump(Arrays::searchKey($array, 'nao-existe')); // Search for key in array, and Return position ==> returns null

$array = ['primeiro' => 10, 'segundo' => 20];
Arrays::renameKey($array, 'primeiro', 'novoNome');
var_dump($array); //Rename array key ==> ['novoNome' => 10, 'segundo' => 20];

$array = [
    'frutas' => ['fruta_1' => 'Maçã', 'fruta_2' => 'Pêra', 'fruta_3' => 'fruta', 'fruta_4' => 'Uva'],
    'verduras' => ['verdura_1' => 'Rúcula', 'verdura_2' => 'Acelga', 'verdura_3' => 'Alface'],
    'legume' => 'Tomate'
];

// Checks in the array, if there is any index with the desired value
var_dump(Arrays::checkExistIndexByValue($array, 'Tomate'));

// Performs the search in the array, through the key and Return an array with all indexes located
var_dump(Arrays::findValueByKey($array, 'verduras'));

// Performs the search in the array, through a value and returns an array with all items located
var_dump(Arrays::findIndexByValue($array, 'Tomate'));

$xml = new SimpleXMLElement('<root/>');
Arrays::convertArrayToXml($array, $xml); // Convert array to Xml
var_dump($xml->asXML());

$array = [
    'frutas' => ['fruta_1' => 'Maçã', 'fruta_2' => 'Pêra', 'fruta_3' => 'fruta', 'fruta_4' => 'Uva'],
    'verduras' => '{"verdura_1": "Rúcula", "verdura_2": "Acelga", "verdura_3": "Alface"}'
];

// Checks the array, if it has any index with JSON and turns it into an array
Arrays::convertJsonIndexToArray($array);
var_dump($array);

$array = [
            'pessoa' => [
                'pedidos' => ['pedido1', 'pedido2'],
                'categorias' => [
                    'subcategorias' => [
                        'subcategoria1' => 'valor teste'
                    ]
                ]
            ]
        ];

// Checks if a specific index exists in a multilevel array
var_dump(Arrays::checkExistIndexArrayRecursive($array, 'subcategoria1')); // Return true

```

## Utilities

```php
<?php

require 'vendor/autoload.php';

use DevUtils\Utility;

Utility::captureClientIp(); // Return user IP, capture per layer available, eg 201.200.25.40

/*
Return an automatically generated password, there are 5 parameters, only the first one is mandatory
int $size       ==> Number of characters in the password (Required)
bool $uppercase ==> If there will be capital letters
bool $lowercase ==> If there will be lowercase letters
bool $numbers   ==> if there will be numbers
bool $symbols   ==> if there will be symbols
*/
Utility::generatePassword(10);

/*
* @return string -> Full URL string
* @param string $host -> system domain
* @param string $absolutePath -> absolute path
* @param string $https -> 'on' to generate https url, null or other value, generate http url
*/
Utility::buildUrl('localhost', '/Framework-Cooper/testando', 'on'); // Return to URL
```

## Check the minimum coverage of CI/CD unit tests using PHPUnit

```php
file: .gitlab-ci.yml
Add Lines:

script:
    - composer install
    - ./vendor/bin/phpunit --coverage-xml coverage #Here generates the coverage file
    - php ./vendor/brunoconte3/dev-utils/src/CI.php  coverage/index.xml 80 #Change the value 80 to your value


file: .gitignore
Add Line: /coverage/
```

## Will perform pull request, please execute unit tests, and phpstan level 8

`./vendor/bin/phpunit --coverage-xml coverage`
`If you don't know how to run phpstan, I execute and adjust whatever is necessary`

# License

The validator is an open-source application licensed under the [MIT License](https://opensource.org/licenses/MIT).
