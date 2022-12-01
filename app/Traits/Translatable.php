<?php
declare(strict_types=1);

namespace App\Traits;

use Astrotomic\Translatable\Translatable as OriginalTranslatable;

trait Translatable
{
    use OriginalTranslatable;

    /**
     * Check if a model is translatable, by the adapter's standards.
     *
     * @return bool
     */
    public function translatableEnabled()
    {
        return property_exists($this, 'translatedAttributes');
    }
}
