<?php

namespace app\Models;

use DateTime;
use Exception;
use InvalidArgumentException;
use PDO;
use PDOException;
use ReflectionClass;
use ReflectionProperty;

abstract class Model
{
    protected static PDO $pdo;
    protected static string $table;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        static::getPDO();
    }

    /**
     * @throws Exception
     */
    private static function getPDO(): void
    {
        if (!isset(static::$pdo)) {
            static::initializeDatabase();
        }
    }

    /**
     * @throws Exception
     */
    private static function initializeDatabase(): void
    {
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];

        try {
            static::$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            static::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public static function find(int $id): ?static
    {
        static::getPDO();
        $statement = static::$pdo->prepare("SELECT * FROM " . static::$table . " WHERE id = :id");
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->setFetchMode(PDO::FETCH_CLASS, static::class);
        $statement->execute();
        $result = $statement->fetch();

        if ($result && property_exists($result, 'last_updated')) {
            $result->last_updated = new DateTime($result->last_updated);
        }

        if ($result === false) {
            return null;
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public static function findAll(): array
    {
        static::getPDO();
        $statement = static::$pdo->prepare("SELECT * FROM " . static::$table);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC) ? : [];
    }

    /**
     * @throws Exception
     */
    public static function update(int $id, array $data): Model
    {
        static::getPDO();
        self::validateRequiredFields($data, array_keys($data));

        $fields = implode(', ', array_map(fn ($key) => "$key = :$key", array_keys($data)));

        $statement = static::$pdo->prepare("UPDATE " . static::$table . " SET $fields WHERE id = :id");
        $statement->execute(array_merge(['id' => $id], $data));

        return self::getCreatedInstance($id);
    }

    /**
     * @throws Exception
     */
    public static function updateBy(string $column, array $data): void
    {
        static::getPDO();
        self::validateRequiredFields($data, array_keys($data));

        $fields = implode(', ', array_map(fn ($key) => "$key = :$key", array_keys($data)));

        $statement = static::$pdo->prepare("UPDATE " . static::$table . " SET $fields WHERE $column = :$column");
        $statement->execute($data);
    }

    /**
     * @throws Exception
     */
    public static function create(array $data): Model
    {
        static::getPDO();
        static::validateRequiredFields($data, array_keys($data));

        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn ($key) => ":$key", array_keys($data)));

        $statement = static::$pdo->prepare("INSERT INTO " . static::$table . " ($fields) VALUES ($placeholders)");
        $statement->execute($data);

        $id = static::$pdo->lastInsertId();
        return static::getCreatedInstance($id);
    }

    /**
     * @throws Exception
     */
    public static function delete(int $id): void
    {
        static::getPDO();
        $statement = static::$pdo->prepare("DELETE FROM " . static::$table . " WHERE id = :id");
        $statement->execute(['id' => $id]);
    }

    protected static function getModelAttributes(): array
    {
        $reflector = new ReflectionClass(new static);
        $properties = $reflector->getProperties(ReflectionProperty::IS_PROTECTED);

        $attributes = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            if ($propertyName !== 'table' && isset(static::$$propertyName)) {
                $attributes[$propertyName] = static::$$propertyName;
            }
        }

        return $attributes;
    }

    protected static function validateRequiredFields(array $data, array $requiredFields): void
    {
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("Missing required field: $field");
            }
        }
    }

    /**
     * @throws Exception
     */
    private static function getCreatedInstance(int $id): Model
    {
        $instance = new static();
        $data = $instance->find($id);

        if ($data) {
            foreach ($data as $key => $value) {
                if ($key === 'last_updated') {
                    $instance->$key = $value->format('Y-m-d H:i:s');
                } else {
                    $instance->$key = $value;
                }
            }
        }

        return $instance;
    }
}
