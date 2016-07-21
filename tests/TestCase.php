<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }    

    protected function createAuthenticatedUser()
    {
        $this->user  = factory(App\User::class)->create();
        $this->token = JWTAuth::fromUser($this->user);
        JWTAuth::setToken($this->token);
        Auth::attempt(['email' => $this->user->email, 'password' => $this->user->password]);
    }

    protected function callAuthenticated($method, $uri, array $data = [], array $headers = [])
    {
        if ($this->token && !isset($headers['Authorization'])) {
            $headers['Authorization'] = "Bearer: $this->token";
        }

        $server = $this->transformHeadersToServerVars($headers);

        $this->call(strtoupper($method), $uri, $data, [], [], $server);

        return $this;
    }
}
