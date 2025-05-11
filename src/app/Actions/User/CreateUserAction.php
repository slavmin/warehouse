<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

final readonly class CreateUserAction
{
    private function __construct(
        private string $name,
        private string $email,
        private string $password,
    ) {}

    /**
     * @param  array<string, mixed>  $inputArr
     */
    public static function fromArray(array $inputArr): User
    {
        $self = new self(
            name: data_get($inputArr, 'name'),
            email: data_get($inputArr, 'email'),
            password: data_get($inputArr, 'password'),
        );

        return self::handle($self);
    }

    private static function handle(self $instance): User
    {
        $user = User::query()->create([
            'name' => $instance->name,
            'email' => $instance->email,
            'password' => Hash::make($instance->password),
        ]);

        event(new Registered($user));

        return $user->fresh();
    }
}
