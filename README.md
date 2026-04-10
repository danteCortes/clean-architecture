# Laravel Hexagonal Maker

A Laravel package to scaffold hexagonal architecture modules with a single Artisan command.

## Installation

```bash
composer require vendor/laravel-hexagonal-maker --dev
```

The package auto-discovers itself via Laravel's package auto-discovery.

## Usage

```bash
php artisan make:module NombreModulo
```

### Example

```bash
php artisan make:module Product
```

This will generate the following structure in `app/Src/Product/`:

```
app/Src/Product/
├── Domain/
│   ├── Entities/Product.php
│   └── ValueObjects/ProductId.php
├── Application/
│   └── UseCases/CreateProductUseCase.php
└── Infrastructure/
    ├── Persistence/
    │   ├── Models/ProductModel.php
    │   ├── Repositories/ProductRepositoryInterface.php
    │   └── Repositories/MongoProductRepository.php
    └── Http/
        ├── Controllers/ProductController.php
        └── Requests/CreateProductRequest.php
```

## Autoloading

Add the `App\Src` namespace to your `composer.json`:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "App\\Src\\": "app/Src/"
    }
}
```

Then run:

```bash
composer dump-autoload
```

## Binding the Repository

After generating the module, bind the repository interface to its implementation in a Service Provider:

```php
use App\Src\Product\Infrastructure\Persistence\Repositories\ProductRepositoryInterface;
use App\Src\Product\Infrastructure\Persistence\Repositories\MongoProductRepository;

$this->app->bind(ProductRepositoryInterface::class, MongoProductRepository::class);
```

## Publishing Stubs

You can publish the stubs to customize them:

```bash
php artisan vendor:publish --tag=hexagonal-stubs
```

Stubs will be published to `stubs/hexagonal/`.

## Requirements

- PHP 8.1+
- Laravel 10 or 11
- `mongodb/laravel-mongodb` (for MongoDB models)

## License

MIT
