<?php

namespace App\Foundation\Contracts;

/**
 * Command Interface
 * 
 * Represents a command in the CQRS pattern - an intention to change state.
 * Commands should be validated and processed transactionally.
 */
interface Command
{
    /**
     * Validate the command data
     *
     * @return bool
     */
    public function isValid(): bool;

    /**
     * Get validation errors if any
     *
     * @return array
     */
    public function getValidationErrors(): array;

    /**
     * Convert command to array for logging/serialization
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Get cache tags that should be cleared when this command executes
     *
     * @return array
     */
    public function getCacheTags(): array;

    /**
     * Get the unique identifier for this command
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get the user who initiated this command
     *
     * @return mixed
     */
    public function getUser(): mixed;
}