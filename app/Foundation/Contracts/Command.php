<?php

namespace App\Foundation\Contracts;

/**
 * Command Interface.
 *
 * Represents a command in the CQRS pattern - an intention to change state.
 * Commands should be validated and processed transactionally.
 */
interface Command
{
    /**
     * Validate the command data.
     */
    public function isValid(): bool;

    /**
     * Get validation errors if any.
     */
    public function getValidationErrors(): array;

    /**
     * Convert command to array for logging/serialization.
     */
    public function toArray(): array;

    /**
     * Get cache tags that should be cleared when this command executes.
     */
    public function getCacheTags(): array;

    /**
     * Get the unique identifier for this command.
     */
    public function getId(): string;

    /**
     * Get the user who initiated this command.
     */
    public function getUser(): mixed;
}
