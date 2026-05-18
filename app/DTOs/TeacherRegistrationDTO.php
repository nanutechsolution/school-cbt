<?php

namespace App\DTOs;

readonly class TeacherRegistrationDTO
{
    public function __construct(
        public string $name,
        public string $nip,
        public string $password,
        public ?string $gender = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            nip: $data['nip'],
            password: $data['password'] ?? $data['nip'], // Default password adalah NIP
            gender: $data['gender'] ?? null,
        );
    }
}