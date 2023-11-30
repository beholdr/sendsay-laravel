# Sendsay Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/beholdr/sendsay-laravel.svg?style=flat-square)](https://packagist.org/packages/beholdr/sendsay-laravel)

Unofficial Sendsay.ru mailer transport for Laravel to sending transactional emails.

## Support

Do you like **Sendsay Laravel**? Please support me via [Boosty](https://boosty.to/beholdr).

## Installation

You can install the package via composer:

```bash
composer require beholdr/sendsay-laravel
```

You need to set `.env` variables:

```bash
MAIL_SENDSAY_ACCOUNT="root_account_name"
MAIL_SENDSAY_KEY="YOUR_API_KEY"
```

And add mailer transport in `config/mail.php`:

```php
'mailers' => [
    ...
    'sendsay' => [
        'transport' => 'sendsay',
    ],
]
```

Optionally, you can publish the config file with:

```bash
php artisan vendor:publish --tag="sendsay-config"
```

## Usage

### Unsubscribe link

For better user experience you should provide unsubscribe link in your emails. You can add such link, using special code `#UNSUBSCRIBE_LINK#` in your templates, that will be substituted with a real url.

For example, if you are using markdown mailables:

1. Publish laravel-mail components:

```bash
php artisan vendor:publish --tag=laravel-mail
```

2. Add to html footer template code:

```blade
@aware(['unsubscribe'])

...

@if ($unsubscribe)
<a style="font-size: 12px" href="{{ $unsubscribe }}">{{ __('Unsubscribe') }}</a>
@endif
```

3. Add to text footer template code:

```blade
@aware(['unsubscribe'])

...

@if ($unsubscribe)
{{ __('Unsubscribe') }}: {{ $unsubscribe }}
@endif
```

4. Pass `unsubscribe` prop to `x-mail::message` component in letter template:

```blade
<x-mail::message :unsubscribe="$mailer === 'sendsay' ? '#UNSUBSCRIBE_LINK#' : false">
...
</x-mail::message>
```

### Proxy

If you set `APP_LOCAL_PROXY` variable, your requests to Sendsay.ru will be proxified via given proxy. Example for proxy inside Docker:

```bash
APP_LOCAL_PROXY="socks5://host.docker.internal:8123"
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
