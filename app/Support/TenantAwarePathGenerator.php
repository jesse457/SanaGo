<?php

declare(strict_types=1);

namespace App\Support;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class TenantAwarePathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        $tenant = tenancy()->tenant;

        if ($tenant) {
            return 'tenants/'.$tenant->getTenantKey().'/'.$media->id.'/';
        }

        return $media->id.'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media).'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'responsive/';
    }
}
