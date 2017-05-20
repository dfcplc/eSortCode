<?php

include './vendor/autoload.php';

use Dfcplc\eSortCode\eSortCode;

var_dump(eSortCode::validate_bank_details('', '', '123456', '12345678'));