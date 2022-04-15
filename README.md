# Laravel Fillable Generator

This package provides a laravel artisan command to generate model fillable automaticly.

## Installation

This package can be installed through Composer.
```
composer require adetxt/laravel-fillable-generator --dev
```

## Usage

You can start generate your model fillable by this artisan command:
```
php artisan generate-model-fillable
```

### With Specific Model
```
php artisan generate-model-fillable User Post Category
```

### Options
```
  -P, --path                     Model path (default: app\Models)
  -E, --excludecol[=EXCLUDECOL]  Exclude column [default: "id,created_at,updated_at,deleted_at"]
```

