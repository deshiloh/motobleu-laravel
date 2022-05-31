<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Sentry\Severity;
use Sentry\State\Scope;
use function Sentry\captureMessage;
use function Sentry\configureScope;
use function Sentry\withScope;

class SentryService
{
    protected string $message;
    protected array $context;
    protected Severity $level;

    public function __construct()
    {
        $user = Auth::user();
        configureScope(function (Scope $scope) use ($user) {
            $scope->setUser([
                'id' => $user->id,
                'email' => $user->email
            ]);
        });
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info(string $message, array $context): void
    {
        $this->message = $message;
        $this->context = $context;
        $this->level = Severity::info();

        $this->sendMessage();
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error(string $message, array $context): void
    {
        $this->message = $message;
        $this->context = $context;
        $this->level = Severity::fatal();

        $this->sendMessage();
    }

    /**
     * @return void
     */
    private function sendMessage(): void
    {
        withScope(function (Scope $scope) {
            $scope->setLevel($this->level);

            foreach ($this->context as $name => $value) {
                $scope->setContext($name, $value);
            }

            captureMessage($this->message);
        });
    }
}
