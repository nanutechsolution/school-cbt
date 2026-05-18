<?php

namespace App\DTOs;

readonly class StudentRegistrationDTO
{
    public function __construct(
        public string $name,
        public string $nis,
        public string $password,
        public int $classroomId,
        public ?string $nisn = null,
        public ?string $gender = null,
        public ?string $religion = null,
        public ?int $examSessionId = null,
        public ?int $roomId = null,
    ) {}

    /**
     * Membantu memparsing data dari array (misal dari Form Request atau Excel)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            nis: $data['nis'],
            password: $data['password'] ?? $data['nis'], // Default password adalah NIS
            classroomId: $data['classroom_id'],
            nisn: $data['nisn'] ?? null,
            gender: $data['gender'] ?? null,
            religion: $data['religion'] ?? null,
            examSessionId: $data['exam_session_id'] ?? null,
            roomId: $data['room_id'] ?? null,
        );
    }
}