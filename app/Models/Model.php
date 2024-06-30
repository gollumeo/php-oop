<?php

namespace app\Models;

use InvalidArgumentException;
use PDO;
use PDOException;
use ReflectionClass;
use ReflectionProperty;

abstract class Model
{
    protected static PDO $pdo;
    protected string $table;

    public function __construct()
    {
        if (!isset(self::$pdo)) {
            self::initializeDatabase();
        }

    }

    private static function initializeDatabase(): void
    {
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];

        try {
            self::$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function find(int $id): array
    {
        $statement = self::$pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $statement->execute(['id' => $id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function findAll(): array
    {
        $statement = self::$pdo->prepare("SELECT * FROM {$this->table}");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): void
    {
        $this->validateRequiredFields($data, array_keys($data));

        $fields = '';
        foreach (array_keys($data) as $key) {
            $fields .= $key . ' = :' . $key . ', ';
        }
        $fields = rtrim($fields, ', ');

        $statement = self::$pdo->prepare("UPDATE {$this->table} SET $fields WHERE id = :id");
        $statement->execute(array_merge(['id' => $id], $data));
    }

    public function updateBy(string $column, array $data): void
    {
        $this->validateRequiredFields($data, array_keys($data));

        $fields = '';
        foreach (array_keys($data) as $key) {
            $fields .= $key . ' = :' . $key . ', ';
        }
        $fields = rtrim($fields, ', ');

        $statement = self::$pdo->prepare("UPDATE {$this->table} SET $fields WHERE $column = :$column");
        $statement->execute($data);
    }

    public function create(): void
    {
        $this->validateRequiredFields($this->getModelAttributes(), array_keys($this->getModelAttributes()));

        $fields = '';
        $placeholders = '';
        foreach ($this->getModelAttributes() as $key => $value) {
            $fields .= $key . ', ';
            $placeholders .= ':' . $key . ', ';
        }
        $fields = rtrim($fields, ', ');
        $placeholders = rtrim($placeholders, ', ');

        $statement = self::$pdo->prepare("INSERT INTO {$this->table} ($fields) VALUES ($placeholders)");
        $statement->execute($this->getModelAttributes());
    }

    public function delete(int $id): void
    {
        $statement = self::$pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $statement->execute(['id' => $id]);
    }

    protected function getModelAttributes(): array
    {
        $reflector = new ReflectionClass($this);
        $properties = $reflector->getProperties(ReflectionProperty::IS_PROTECTED);

        $attributes = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            if ($propertyName !== 'table') {
                $attributes[$propertyName] = $this->$propertyName;
            }
        }

        return $attributes;
    }

    protected function validateRequiredFields(array $data, array $requiredFields): void
    {
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("Missing required field: $field");
            }
        }
    }
}
