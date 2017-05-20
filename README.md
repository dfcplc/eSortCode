# eSortCode

[eSortCode](http://www.etellect.com/products/payment-services) UK Bank Account Validation - PHP Client Library


To run:

```php
use Dfcplc\eSortCode\eSortCode;

$result = eSortCode::validate_bank_details('', '', '123456', '12345678');

var_dump($result);
```