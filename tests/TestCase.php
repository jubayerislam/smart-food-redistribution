<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $compiledViewPath = storage_path('framework/views/testing/'.str_replace('\\', '_', static::class).'_'.uniqid());

        File::ensureDirectoryExists($compiledViewPath);

        config()->set('view.compiled', $compiledViewPath);
    }
}
