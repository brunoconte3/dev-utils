# dev-utils

**dev-utils** Pure PHP Data Validation & Formatting Library

[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

**Complete pure PHP library** for data validation, string formatting, array manipulation, and general utilities. Fully tested with **PHPUnit** and validated with **PHPStan level 10** and **SonarQube** and **PHPCS**.

### ✨ Key Features

- **Robust data validation** - Email, CPF, CNPJ, dates, time, phone, file uploads and more
- **String formatting** - Type conversion, currency, date and text formatting
- **Array manipulation** - Search, filter, sort and transform arrays
- **File upload validation** - File type, MIME type, image dimensions, size
- **General utilities** - UUID, comparisons, arrays and string operations
- **100% tested** - PHPUnit + PHPStan level 10 + SonarQube + PHPCS
- **Code Quality** - Validated with industry-standard tools

## Quick Navigation

- [Quick Start](#quick-start)
- [Installation](#installation)
- [Common Use Cases](#common-use-cases)
- [Data Validation](#data-validation-example)
- [File Upload Validation](#validating-files-upload)
- [Validation Types](#validation-types-validators)
- [Regex Validator](#using-the-regex-validator)
- [Custom Messages](#defining-custom-message)
- [String Formatting](#formatting-examples)
- [Data Comparison](#comparisons-examples)
- [Validation Methods](#validations-in-the-form-of-methods)
- [Generation Utilities](#generation-utilities)
- [Array Manipulation](#manipulate-arrays)
- [General Utilities](#utilities)

## Quick Start

```php
<?php

require 'vendor/autoload.php';

use DevUtils\Validator;

$data = ['email' => 'user@example.com'];
$rules = ['email' => 'required|email'];

$validator = new Validator();
$validator->set($data, $rules);

if (!$validator->getErros()) {
    echo '✓ Validation successful!';
} else {
    var_dump($validator->getErros());
}
```

## Installation

Install using **Composer**:

```bash
composer require brunoconte3/dev-utils
```

**Requirements:**

- PHP >= 8.4
- Composer

### Why use dev-utils?

- ✓ **Pure PHP** - Zero external dependencies
- ✓ **Fully tested** - PHPUnit + PHPStan Level 10 + SonarQube
- ✓ **Code Quality** - Validated with PHPCS standards
- ✓ **Professional Code** - Production-ready quality
- ✓ **Complete Documentation** - Examples for each validator
- ✓ **Active Maintenance** - Regular updates

<a id="common-use-cases"></a>

## 🎯 Common Use Cases

### Validate registration forms

Validate email, CPF/CNPJ, phone and other data in a single validator.

### Process file uploads

Control file size, MIME type, image dimensions and filename.

### Format data for display

Format currencies, dates, strings and perform type conversions.

### Validate API data

Ensure received data meets your business criteria.

### Manipulate complex arrays

Search, sort, filter and transform arrays with ready-to-use methods.

## Data Validation Example

### Sample Data

```php
$data = [
   'name'  => 'Bruno Conte',
   'email' => 'bruno@example.com',
   'newPassword' => '123456',
];
```

### Validation Rules

```php
$rules = [
   'name'  => 'required|alpha|min:7|max:100',
   'email' => 'required|email|max:80',
   'newPassword' => 'required|email|max:50',
];
```

### Validate the Data

```php
<?php
require 'vendor/autoload.php';

use DevUtils\Validator;

$validator = new Validator();
$validator->set($data, $rules);

if (!$validator->getErros()) {
   echo '✓ Validation successful!';
} else {
   var_dump($validator->getErros());
}
```

## Validating File(s) Upload

Validate file uploads with validators: **fileName**, **maxFile**, **maxUploadSize**, **mimeType**, **minFile**, **minUploadSize**, **minHeight**, **minWidth**, **maxHeight**, **maxWidth** and **requiredFile**.

Control minimum/maximum file size (bytes), number of files, allowed extensions, image dimensions, filename and field requirements.

### HTML Form

```html
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <title>Upload de Arquivos</title>
  </head>
  <body>
    <form method="POST" enctype="multipart/form-data">
      <!-- Upload a single file -->
      <input type="file" name="fileUploadSingle" />

      <!-- Upload single or multiple files -->
      <input type="file" name="fileUploadMultiple[]" multiple="multiple" />

      <button type="submit">Submit</button>
    </form>
  </body>
</html>
```

### PHP Validation

```php
<?php
/**
 * Notes:
 * - maxFile, minFile, minHeight, minWidth, maxUploadSize, maxHeight, maxWidth, minUploadSize: Must be integers
 * - mimeType: Pass an array with allowed extensions, separated by ';'
 */
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    $fileUploadSingle = $_FILES['fileUploadSingle'];
    $fileUploadMultiple = $_FILES['fileUploadMultiple'];

    $data = [
        'fileUploadSingle' => $fileUploadSingle,
        'fileUploadMultiple' => $fileUploadMultiple,
    ];

    $rules = [
        'fileUploadSingle' => 'requiredFile|fileName|mimeType:jpeg;png;jpg;txt;docx;xlsx;pdf|minUploadSize:10|maxUploadSize:100|minWidth:200|maxWidth:200',
        'fileUploadMultiple' => 'fileName|mimeType:jpeg|minFile:1|maxFile:3|minUploadSize:10|minWidth:200|maxWidth:200|maxUploadSize:100',
    ];

    $validator = new DevUtils\Validator();
    DevUtils\Format::convertTypes($data, $rules);
    $validator->set($data, $rules);

    if (!$validator->getErros()) {
        echo '✓ Files validated successfully!';
    } else {
        echo '<pre>';
        print_r($validator->getErros());
    }
}
?>
```

## Validation types (validators)

Complete list of available validators in the library. Use them in your validation rules to ensure data meets your business criteria.

### Text Validators

| Validator         | Description                          |
| ----------------- | ------------------------------------ |
| alpha             | Only alphabetic characters           |
| alphaNoSpecial    | Regular text without accents         |
| alphaNum          | Alphanumeric characters              |
| alphaNumNoSpecial | Letters without accents + numbers    |
| lower             | No uppercase letters (digits ok)     |
| notSpace          | Check if contains spaces             |
| regex             | Custom regex, e.g. regex:/^[0-9]+$/  |
| upper             | No lowercase letters (digits ok)     |

### Brazilian Data Validators

| Validator             | Description                                                              |
| --------------------- | ------------------------------------------------------------------------ |
| companyIdentification | Validates CNPJ with or without mask, upper or lowercase                  |
| ddd                   | Validates DDD by state or general (e.g. ddd:pr), with or without mask    |
| identifier            | Validates CPF with or without mask                                       |
| identifierOrCompany   | Validates CPF or CNPJ with or without mask, upper or lowercase           |
| phone                 | Phone with DDD (10 or 11 digits), with or without mask                   |
| plate                 | Vehicle license plate AAA-0000 or Mercosul AAA0A00, with or without mask |
| zipcode               | Brazilian CEP (8 digits), with or without mask                           |

### Date and Time Validators

| Validator              | Description                                       |
| ---------------------- | ------------------------------------------------- |
| dateAmerican           | Date YYYY-MM-DD or MM/DD/YYYY (8 digits ok)       |
| dateBrazil             | Date DD/MM/YYYY (or 8 digits: 31122024)           |
| dateIso8601            | ISO 8601 date, week, ordinal, duration or interval |
| dateNotFuture          | Date not in the future (DD/MM/YYYY or YYYY-MM-DD) |
| dateUTCWithoutTimezone | UTC date without Z (2025-11-20T10:30:00)          |
| hour                   | Hour HH:MM, from 00:00 to 23:59                   |
| noWeekend              | Date is not Saturday or Sunday                    |
| numMonth               | Month from 1 to 12 (01 accepted)                  |
| timestamp              | Date and time YYYY-MM-DD HH:MM:SS (not Unix)      |

`dateAmerican` takes both the database format `2024-12-31` and the US format `12/31/2024`. When a
slashed date could be read either way — `05/06/2024` is 5 June for Brazil and 6 May for the US — the
**Brazilian reading wins**, so use `2024-06-05` whenever the source is really American.

### Type Validators

| Validator | Description                                 |
| --------- | ------------------------------------------- |
| array     | Check if it is an array                     |
| bool      | Boolean (true/false, 1/0, yes/no, on/off)   |
| float     | Decimal/floating value                      |
| int       | Integer type (attempts parse)               |
| integer   | Integer with strict type check              |
| json      | Valid JSON                                  |
| numeric   | Only numeric values (accepts leading zeros) |

### Constraint Validators

| Validator | Description                    |
| --------- | ------------------------------ |
| equals    | Field must equal another field |
| max       | Maximum size                   |
| maxWords  | Maximum number of words        |
| min       | Minimum size                   |
| minWords  | Minimum number of words        |
| optional  | Validates only if not empty    |
| required  | Required field                 |

### Network and Identifier Validators

| Validator | Description                                       |
| --------- | ------------------------------------------------- |
| email     | Email validation                                  |
| ip        | Valid IP address                                  |
| mac       | Valid MAC address                                 |
| rgbColor  | Valid RGB color (255,255,255 or rgb(255,255,255)) |

### Numeric Comparison Validators

| Validator | Description                 |
| --------- | --------------------------- |
| numMax    | Maximum value (minimum = 0) |
| numMin    | Minimum value (minimum = 0) |

### File Upload Validators

| Validator     | Description                                 |
| ------------- | ------------------------------------------- |
| fileName      | Validates and formats filename              |
| maxFile       | Maximum number of files                     |
| maxHeight     | Maximum image height (pixels)               |
| maxUploadSize | Maximum file size (bytes)                   |
| maxWidth      | Maximum image width (pixels)                |
| minFile       | Minimum number of files                     |
| minHeight     | Minimum image height (pixels)               |
| minUploadSize | Minimum file size (bytes)                   |
| minWidth      | Minimum image width (pixels)                |
| mimeType      | Defines allowed extensions (separated by ;) |
| requiredFile  | Required file field                         |

### Using the regex validator

The pattern is handed straight to `preg_match()`, so write it **exactly as you would in PHP —
delimiters included**. Modifiers go after the closing delimiter.

```php
$rules = [
    'code'     => 'required|regex:/^[A-Z]{3}[0-9]{4}$/',        // ABC1234
    'slug'     => 'regex:/^[a-z0-9-]+$/',
    'name'     => 'regex:/^[a-z ]+$/i',                         // modifiers work
    'nickname' => 'optional|regex:/^[a-z]+$/',                  // only checked when filled
    'digits'   => 'regex:/^[0-9]+$/, Only digits are allowed',  // custom message
];
```

Delimiters are not optional: `regex:^[0-9]+$` raises `preg_match(): No ending delimiter` and the
field then fails for every value. The rule name is case-sensitive too — `Regex:` is reported as an
invalid rule.

#### Characters the pipe syntax cannot carry

The rule string is parsed before it reaches `preg_match()`: `|` separates rules, `,` starts a custom
message and `:` separates a rule from its parameter. A pattern containing any of them is cut short —
and it fails quietly, so it is worth knowing in advance:

```php
'regex:/^(cat|dog)$/'    // '|' splits the rule    -> "Uma regra inválida está sendo aplicada..."
'regex:/^[0-9]{2,4}$/'   // ',' starts a message   -> pattern is cut and '4}$/' becomes the message
'regex:/^[a-z]:[0-9]$/'  // ':' splits the param   -> pattern truncated to '/^[a-z]'
'regex:/^[a-z];[0-9]$/'  // ';' is safe, it is special-cased for this rule
```

For those patterns, declare that field's rules as a **JSON string**. Nothing is split, so any
pattern goes through — and the `message` key carries the custom message:

```php
$rules = [
    'range' => '{"regex":"/^[0-9]{2,4}$/"}',                          // comma
    'pet'   => '{"required":true,"regex":"/^(cat|dog)$/"}',           // alternation
    'time'  => '{"regex":"/^[0-9]{2}:[0-9]{2}$/","message":"Use HH:MM"}',
];
```

---

## Defining custom message

After defining some of our rules to the data you can also add a custom message using the ',' delimiter in some specific rule or using the default message.

`Example:`

```php
<?php

    $validator->set($data, [
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

//Methods that accept masked input (companyIdentification, identifier, identifierOrCompany, telephone, zipCode)
//throw InvalidArgumentException when the value has a leading or trailing space
Format::companyIdentification('A1B2C3D45E6F59'); //CNPJ ==> A1.B2C.3D4/5E6F-59 - accepts masked input
Format::convertTimestampBrazilToAmerican('15/04/2021 19:50:25'); //Convert Timestamp Brazil to American format
//Default currency BR ==> R$ 113,00 - the 2nd parameter chooses the Currency label. A leading '-' is preserved.
//An empty string returns 0,00, but a value without any digit throws InvalidArgumentException
Format::currency('113', 'R$ ');
Format::currencyUsd('1123.45'); //Default currency USD ==> 1,123.45 - the 2nd parameter chooses the Currency label
//Accepts dd/mm/yyyy, mm/dd/yyyy, dd-mm-yyyy and yyyy-mm-dd. An invalid date throws InvalidArgumentException
//An ambiguous slashed date is read as Brazilian: '05/06/2024' returns 2024-06-05, not 2024-05-06
Format::dateAmerican('12-05-2020'); //return date ==>  2020-05-12
Format::dateBrazil('2020-05-12'); //return date ==>  12/05/2020
Format::identifier('73381209000');  //CPF ==>  733.812.090-00 - accepts masked input
Format::identifierOrCompany('30720870089'); //CPF/CNPJ Brazil ==> 307.208.700-89 - accepts masked input
Format::falseToNull(false); //Return ==> null
Format::lower('CArrO'); //lowercase text ==> carro - the 2nd parameter chooses the charset, UTF-8 default
//[Apply any type of Mask, accepts space, points and others]
Format::mask('#### #### #### ####', '1234567890123456'); //Mask ==> 1234 5678 9012 3456
//Mask of string ==> 065.***.009.96 - a position outside the string throws InvalidArgumentException
Format::maskStringHidden('065.775.009.96', 3, 4, '*');
Format::onlyNumbers('548Abc87@'); //Returns only numbers ==> 54887;
Format::onlyLettersNumbers('548Abc87@'); //Returns only letters and numbers ==> 548Abc87;
//Currency for recording on the BD ==> 1350.45 - accepts Brazilian ('1.350,45') and American ('1,350.45') formats
Format::pointOnlyValue('1.350,45');
Format::removeAccent('Açafrão'); //Remove accents and character 'ç' ==> Acafrao
//Removes all special characters ==> "Acafrao com Espaco", 2nd parameter chooses whether to allow space, default true
Format::removeSpecialCharacters('Açafrão com Espaco %$#@!', true);
Format::returnPhoneOrAreaCode('44999998888', false); //Returns only the phone number ==> 999998888
Format::returnPhoneOrAreaCode('44999998888', true); //Returns only the phone's area code ==> 44
Format::reverse('Abacaxi'); //Returns inverted string ==> ixacabA
Format::telephone('44999998888');  //Return phone format brazil ==> (44) 99999-8888 - accepts masked input
Format::ucwordsCharset('aÇafrÃo maCaRRão'); //Return first capital letter ==> Açafrão Macarrão
Format::upper('Moto'); //lowercase text ==> MOTO - the 2nd parameter chooses the charset, UTF-8 default
Format::zipCode('87030585'); //CEP format brazil ==>  87030-585 - accepts masked input
Format::writeDateExtensive('06/11/2020'); //Date by Long Brazilian format ==> sexta-feira, 06 de novembro de 2020
Format::writeCurrencyExtensive(1.97); //Coin by Extensive Brazilian format ==> um real e noventa e sete centavos
Format::writeCurrencyExtensive(1000); //==> mil reais
Format::writeCurrencyExtensive(1000000); //==> um milhão de reais
Format::convertStringToBinary('amor'); //String to binary ==> 1100001 1101101 1101111 1110010
Format::slugify('Polenta frita e Parmesão'); //Returns a slug from a string ==> polenta-frita-e-parmesao

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
//Convert the value to its correct type ['bool', 'float', 'int', 'numeric',]
//Returns an array with the fields that could not be converted (empty when everything succeeded)
$conversionErrors = Format::convertTypes($data, $rules);
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
//Throws InvalidArgumentException when the date is not dd/mm/yyyy, mm/dd/yyyy or yyyy-mm-dd (31/02/2020 is rejected)
Compare::daysDifferenceBetweenData('31/05/2020', '30/06/2020'); //Accepts American date too

//Compares if start date is less than end date => Returns [bool]
//null or empty string returns false; an invalid filled date throws InvalidArgumentException
Compare::startDateLessThanEnd('30/07/2020', '30/06/2020'); //Accepts American date too

//Difference between hours ==> 01:36:28 [Hours displays negative and positive difference]
//A negative result is the real duration prefixed by '-' ==> '-11:00:05'
//Requires HH:MM:SS, otherwise throws InvalidArgumentException
Compare::differenceBetweenHours('10:41:55', '12:18:23');

//Compares if the start time is less than the end time (3rd parameter, accept custom message)
//4th parameter accepts a custom message for when one of the hours is not filled
Compare::startHourLessThanEnd('12:05:01', '10:20:01');

//Compares the date to the current date, and returns the person's age. Future dates return 0
Compare::calculateAgeInYears('20/05/1989');

//2nd parameter pins the reference instant (useful to keep tests deterministic)
//3rd parameter sets the timezone, defaults to America/Sao_Paulo
Compare::calculateAgeInYears('20/05/1989', new DateTimeImmutable('2025-06-01'), 'America/Sao_Paulo');

//Compares fields for equality, returns boolean
//optional third parameter, false to not compare caseSensitive, default true
//The case-insensitive comparison is multibyte aware ('AÇAFRÃO' matches 'açafrão')
Compare::checkDataEquality('AçaFrão', 'Açafrão');

//Compares if desired content exists in String, returns boolean
Compare::contains('AçaFrão', 'çaF');

//Compares the corresponding URL with the second parameter, starts with the string entered in the first parameter. Returns boolean.
//Case-insensitive and tolerant to a trailing slash, but slashes inside the path are significant ('/te/ste' does not match '/teste').
Compare::beginUrlWith('/teste', '/teste/variavel');

//Compares the corresponding URL with the second parameter, ends with the string entered in the first parameter. Returns boolean.
Compare::finishUrlWith('/teste', 'sistema/teste');

//Compares if the corresponding string with the first parameter is equal to the substring obtained from the second parameter. Extracting to compare 7 characters from the second parameter starting at position 0. Returns boolean.
//The start/length window is always honoured, even when both strings are identical.
Compare::compareStringFrom('sistema', 'sistema/teste', 0, 7);

```

## Validations in the form of Methods

```php
<?php

require 'vendor/autoload.php';

use DevUtils\ValidateCnpj;
ValidateCnpj::validateCnpj('A1.B2C.3D4/5E6F-59'); //Returns boolean, example true [Can pass without mask]

use DevUtils\validateCpf;
ValidateCpf::validateCpf('257.877.760-89'); //Returns boolean, example true [Can pass without mask]

use DevUtils\ValidateDate;
//Examples return true
ValidateDate::validateDateBrazil('29/04/2021'); //Return boolean [Format dd/mm/yyyy]
ValidateDate::validateDateAmerican('2021-04-29'); //Return boolean [Format yyyy-mm-dd or mm/dd/yyyy]
ValidateDate::validateDateAmerican('12/31/2024'); //Return boolean [US format, month first]
ValidateDate::validateTimestamp('2021-04-29 11:17:12'); //Return boolean [Format yyyy-mm-dd hh:mm:ss]
ValidateDate::validateDateIso8601('2025-11-20T10:30:00Z'); //Return boolean [Format ISO 8601: 2025-11-20T10:30:00Z]
ValidateDate::validateDateUTCWithoutTimezone('2025-11-20T10:30:00'); //Return boolean [Format UTC without Z: 2025-11-20T10:30:00]

use DevUtils\ValidateHour;
ValidateHour::validateHour('08:50'); //Return boolean [Format YY:YY]

use DevUtils\ValidatePhone;
ValidatePhone::validate('44999999999'); //Return boolean [[You can wear a mask]

use DevUtils\ValidateString;
ValidateString::minWords('Bruno Conte', 2) //Return boolean
ValidateString::maxWords('Bruno Conte', 2) //Return boolean
```

## Generation Utilities

### UUID v7 - Generate and Validate

```php
<?php

require 'vendor/autoload.php';

use DevUtils\Uuid;

// Generate UUID v7 (timestamp-based, sortable, unique)
$uuid = Uuid::generate(); // ==> 01890f87-4f0b-7f6b-8b1d-9f4f9d7c3b5a

// Validate any UUID version (v1 to v8)
Uuid::isValid('550e8400-e29b-41d4-a716-446655440000'); // ==> true

// Validate specific version
Uuid::isValid('01890f87-4f0b-7f6b-8b1d-9f4f9d7c3b5a', 7); // ==> true

```

Validation follows RFC 9562 strictly, so the following are rejected:

```php
Uuid::isValid('550e8400-e29b-41d4-c716-446655440000');   // ==> false (variant is not 10xx)
Uuid::isValid('00000000-0000-0000-0000-000000000000');   // ==> false (nil UUID, no version)
Uuid::isValid('ffffffff-ffff-ffff-ffff-ffffffffffff');   // ==> false (max UUID, no version)
Uuid::isValid("550e8400-e29b-41d4-a716-446655440000\n"); // ==> false (trailing characters)
```

`ValidateUuid::isValid()` accepts only v4 and v7, delegating to the same RFC checks:

```php
use DevUtils\ValidateUuid;

ValidateUuid::isValid('550e8400-e29b-41d4-a716-446655440000'); // ==> true  (v4)
ValidateUuid::isValid('01890f87-4f0b-7f6b-8b1d-9f4f9d7c3b5a'); // ==> true  (v7)
ValidateUuid::isValid('6ba7b810-9dad-11d1-80b4-00c04fd430c8'); // ==> false (v1)
```

### Password Generation

Built on `random_int()`, PHP's cryptographically secure generator — safe for real passwords,
temporary access codes and reset tokens.

```php
<?php

use DevUtils\Utility;

Utility::generatePassword(10);                          // ==> aB3$xY9!zK
Utility::generatePassword(16, true, true, true, false); // 16 chars, no symbols
Utility::generatePassword(32, false, false, true, false); // 32-digit numeric token
```

Two guarantees worth relying on:

- **The result always has exactly the requested length**, even when it exceeds the character set
  (a 32-digit token draws from only 10 digits, so characters repeat — as they should).
- **Every enabled group is present.** `generatePassword(4)` returns one uppercase, one lowercase,
  one digit and one symbol, so you never have to re-check the password against your own policy.

| Parameter    | Default    | Description                       |
| ------------ | ---------- | --------------------------------- |
| `$size`      | *required* | Number of characters              |
| `$uppercase` | `true`     | Include `A-Z`                     |
| `$lowercase` | `true`     | Include `a-z`                     |
| `$numbers`   | `true`     | Include `0-9`                     |
| `$symbols`   | `true`     | Include `@#$!()-+%=`              |

Impossible requests fail loudly instead of returning a weak password:

```php
Utility::generatePassword(10, false, false, false, false);
// InvalidArgumentException: Ao menos um conjunto de caracteres deve ser habilitado!

Utility::generatePassword(2); // 2 chars cannot hold the 4 enabled groups
// InvalidArgumentException: O tamanho da senha deve ser no mínimo 4 para os conjuntos habilitados!
```

## Manipulate Arrays

```php
<?php

require 'vendor/autoload.php';

use DevUtils\Arrays;

$array = ['first' => 15, 'second' => 25];
var_dump(Arrays::searchKey($array, 'first'));   // Search for key in array, and Return position ==> returns 0
var_dump(Arrays::searchKey($array, 'second'));    // Search for key in array, and Return position ==> returns 1
var_dump(Arrays::searchKey($array, 'does-not-exist')); // Search for key in array, and Return position ==> returns null

$array = ['first' => 10, 'second' => 20];
Arrays::renameKey($array, 'first', 'newName');
var_dump($array); //Rename array key ==> ['newName' => 10, 'second' => 20];

$array = [
    'fruits' => ['fruit_1' => 'Maçã', 'fruit_2' => 'Pêra', 'fruit_3' => 'fruit', 'fruit_4' => 'Uva'],
    'vegetables' => ['vegetable_1' => 'Rúcula', 'vegetable_2' => 'Acelga', 'vegetable_3' => 'Alface'],
    'legume' => 'Tomate'
];

// Checks in the array, if there is any index with the desired value
var_dump(Arrays::checkExistIndexByValue($array, 'Tomate'));

// Performs the search in the array, through the key and Return an array with all indexes located
var_dump(Arrays::findValueByKey($array, 'vegetables'));

// Performs the search in the array, through a value and returns an array with all items located
var_dump(Arrays::findIndexByValue($array, 'Tomate'));

$xml = new SimpleXMLElement('<root/>');
Arrays::convertArrayToXml($array, $xml); // Convert array to Xml
var_dump($xml->asXML());

$array = [
    'fruits' => ['fruit_1' => 'Maçã', 'fruit_2' => 'Pêra', 'fruit_3' => 'fruit', 'fruit_4' => 'Uva'],
    'vegetables' => '{"vegetable_1": "Rúcula", "vegetable_2": "Acelga", "vegetable_3": "Alface"}'
];

// Checks the array, if it has any index with JSON and turns it into an array
Arrays::convertJsonIndexToArray($array);
var_dump($array);

$array = [
            'person' => [
                'orders' => ['order1', 'order2'],
                'categories' => [
                    'subcategories' => [
                        'subcategory1' => 'test value'
                    ]
                ]
            ]
        ];

// Checks if a specific index exists in a multilevel array
var_dump(Arrays::checkExistIndexArrayRecursive($array, 'subcategory1')); // Return true

```

## Utilities

### buildUrl - absolute URLs without hardcoding protocol or domain

A relative path like `/reset/abc123` is useless outside the browser. Use `buildUrl` whenever the
address has to survive on its own: links sent by e-mail, HTTP redirects, canonical tags, webhook
callbacks, OAuth `redirect_uri` and sitemaps.

```php
<?php

require 'vendor/autoload.php';

use DevUtils\Utility;

Utility::buildUrl('localhost', '/sua-url/complemento');        // ==> http://localhost/sua-url/complemento
Utility::buildUrl('localhost', '/sua-url/complemento', 'on');  // ==> https://localhost/sua-url/complemento
Utility::buildUrl('meusite.com.br');                           // ==> http://meusite.com.br
Utility::buildUrl('localhost:8080', '/api/v1?page=2');         // ==> http://localhost:8080/api/v1?page=2
```

**The 3rd parameter is meant to receive `$_SERVER['HTTPS']` as is**, so the same code produces the
right scheme in development and in production. `https` is generated for `'on'`, `'On'`, `'ON'`,
`'1'`, `'true'` and `'yes'`; anything else — including `'off'`, `'0'`, `''` and `null` — generates
`http`.

```php
// Real use: a password reset link that works on http locally and https in production
use DevUtils\conf\Conf;

$link = Utility::buildUrl(Conf::host(), '/reset/' . $token, $_SERVER['HTTPS'] ?? null);

// dev  ==> http://localhost/reset/9f3a...
// prod ==> https://meusite.com.br/reset/9f3a...

mail($email, 'Recuperação de senha', "Acesse: {$link}");
```

`Conf::host()` already returns the current sanitized domain, so the two pair naturally and you
never repeat the domain in your code.

### captureClientIp - visitor IP address

```php
Utility::captureClientIp(); // ==> '201.200.25.40', or null when no source is available
```

Checks, in order: `HTTP_CLIENT_IP`, `HTTP_X_FORWARDED_FOR` and `REMOTE_ADDR`, returning the first
one that is filled.

## Check the minimum coverage of CI/CD unit tests using PHPUnit

```php
file: .gitlab-ci.yml
Add Lines:

script:
    - composer install
    - ./vendor/bin/phpunit --coverage-xml coverage #Here generates the coverage file
    - php ./src/CI.php  coverage/index.xml 80 #Change the value 80 to your value


file: .gitignore
Add Line: /coverage/
```

## Will perform pull request, please execute unit tests, and phpstan level 10

`./vendor/bin/phpunit --coverage-xml coverage`
`./vendor/bin/phpstan analyse -c phpstan.neon --level 10`
`If you don't know how to run phpstan, I execute and adjust whatever is necessary`

## 💬 Support and Documentation

### Need Help?

- 📖 Check the [complete documentation](https://github.com/brunoconte3/dev-utils/wiki)
- 🐛 Found a bug? [Open an issue](https://github.com/brunoconte3/dev-utils/issues)
- 💡 Have a suggestion? [Submit a feature request](https://github.com/brunoconte3/dev-utils/issues/new)

### Contributing

Contributions are welcome! Please:

1. Fork the project
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

Make sure:

- All tests pass: `phpunit`
- Code follows standards: `phpstan analyse -c phpstan.neon --level 10`
- PSR-12 compliance: `phpcs`

## 📊 Code Quality

- ✅ **PHPUnit** - Full test coverage
- ✅ **PHPStan Level 10** - Advanced static analysis
- ✅ **SonarQube** - Code quality and security analysis
- ✅ **PHPCS** - PHP Code Sniffer for coding standards
- ✅ **Zero dependencies** - Pure PHP

## 🔗 Useful Links

```
GitHub    https://github.com/brunoconte3/dev-utils
Packagist https://packagist.org/packages/brunoconte3/dev-utils
Issues    https://github.com/brunoconte3/dev-utils/issues
Wiki      https://github.com/brunoconte3/dev-utils/wiki
```

## 🌟 If you like this project

If this project was useful to you:

- ⭐ Leave a star on GitHub
- 🍴 Fork and share
- 💬 Give feedback and suggestions

# License

The validator is an open-source application licensed under the [MIT License](https://opensource.org/licenses/MIT).
