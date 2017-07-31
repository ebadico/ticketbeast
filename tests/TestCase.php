<?php

namespace Tests;

use App\User;
use \Mockery;
use App\Exceptions\Handler;
use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp()
    {   
        parent::setUp();

        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);

        TestResponse::macro('data', function($key) {
            return $this->original->getData()[$key];
        });
    }

    protected function signIn(User $user = null)
    {
        $this->be($user = $user ?: factory(User::class)->create());

        return $user;
    }

    protected function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report(\Exception $e) {}
            public function render($request, \Exception $e) {
                throw $e;
            }
        });
    }

    protected function from($url)
    {
        session()->setPreviousUrl(url($url));

        return $this;
    }
}
