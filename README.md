# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/aliwebto/easy-payment.svg?style=flat-square)](https://packagist.org/packages/aliwebto/easy-payment)
[![Total Downloads](https://img.shields.io/packagist/dt/aliwebto/easy-payment.svg?style=flat-square)](https://packagist.org/packages/aliwebto/easy-payment)
![GitHub Actions](https://github.com/aliwebto/easy-payment/actions/workflows/main.yml/badge.svg)

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer require aliwebto/easy-payment
php artisan vendor:publish --provider="Aliwebto\EasyPayment\EasyPaymentServiceProvider"
php artisan migrat

// set your configs to config/easy-payment.php
```

## Usage

```php
// payable model - use Payable trait in your payable model  Ex: invoice.
use \Aliwebto\EasyPayment\Payable;




// you have to make a payable model like invoice and relate it to users . then create invoice and pass to easy payment



// create transaction and get data
use Aliwebto\EasyPayment\EasyPayment
$payable = \App\Models\Invoice::find(1);
$amount = $payable->price;
$description = "description of payment";
$easyPayments = EasyPayment::pay($payable,$amount,$description);
return redirect($easyPayments["pay_url"]);


// check transaction is paid
use Aliwebto\EasyPayment\EasyPayment
$payable = \App\Models\Product::find(1);
$payable->isPaid(); // bool
```


### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email aliwebto@gmail.com instead of using the issue tracker.

## Credits

-   [Alireza Zarei](https://github.com/aliwebto)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
