# Laravel Custom Attribute Casts

Simple custom class attribute casting for Eloquent models.

## Installation

Installation with composer require:

```sh
composer require adamdburton/laravel-custom-casts
```

## Usage

You will need to create a class for your custom cast. The class can contain all of the logic for your custom
cast or be a wrapper for another class, api, or anything else. All fields used for custom casts should be set to JSON
or TEXT as all data is saved and restored using json_encode and json_decode respectively, from the class's `$data` property.

Here is a simple example for casting an attribute as an Imgur image, using an API.

```php
namespace App\Casts;

use AdamDBurton\EloquentCustomCasts\AttributeCast;

class ImgurImage extends AttributeCast
{  
  public function setId($id)
  {
    $this->data['id'] = $id;
    
    return $this;
  }
  
  public function get()
  {
    return Imgur::getImage($this->data['id']);
  }
  
  public function creating($value)
  {
    $this->setImageId($value);
  }
}
```

And then to use this cast, simply specify it in your model `$casts` array.

```php
namespace App\Models;

use App\Casts;

class GalleryImage extends Model
{
  protected $casts = [ 'image' => ImgurImage::class ];
}
```

You can then access the cast attribute using any of the standard model methods. The original attributes are also cast to
your custom class.

```php
$model = new App\Models\GalleryImage;

// Returns an ImgurImage
$model->image;

// Returns the original ImgurImage
$model->getOriginal('image');

// Returns an ImgurImage
$model->getAttribute('image');

// Updates the ID of the ImgurImage
$model->image->setId('hJ8cZhp');
```

You can also set an attribute to an instance of a Castable class, or simply pass a value which will be transformed into
a class.

```php
$model = new App\Models\GalleryImage;

// $model->image attribute will be set to a new instance of ImgurImage
// with the string 'hJ8cZhp' passed to the creating method.

$model->image = 'hJ8cZhp';

// Alternatively, you can pass an instance of your Castable class as the attribute.

$model->image = new ImgurImage('hJ8cZhp');
$model->image = (new ImgurImage)->setId('hJ8cZhp');
```