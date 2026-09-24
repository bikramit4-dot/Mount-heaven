<?php

namespace App\Core;

/**
 * Base model — thin table-data-gateway over the Database class.
 *
 * Subclasses declare their table and (optionally) add named finder methods
 * so SQL lives in exactly one place instead of being repeated across
 * controllers. Everything stays parameterized; LIMIT values are always
 * (int)-cast inside the finders.
 */
abstract class Model
{
    /** Database table this model maps to. */
    abstract protected static function table(): string;

    /** Every row, in primary-key order. */
    public static function all(): array
    {
        return Database::fetchAll('SELECT * FROM `' . static::table() . '` ORDER BY id');
    }

    /** Single row by primary key, or null. */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM `' . static::table() . '` WHERE id = ?', [$id]);
    }

    /** Total row count. */
    public static function count(): int
    {
        return Database::count(static::table());
    }

    /** Insert a row, returning the new primary key. */
    public static function create(array $data): int
    {
        return Database::insert(static::table(), $data);
    }

    /** Update a row by primary key. */
    public static function update(int $id, array $data): void
    {
        Database::update(static::table(), $data, $id);
    }

    /** Delete a row by primary key. */
    public static function remove(int $id): void
    {
        Database::delete(static::table(), $id);
    }
}
