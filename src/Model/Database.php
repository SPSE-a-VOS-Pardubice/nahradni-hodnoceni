<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Database {
    protected const SIZE_VARCHAR_SHORT = 40;
    protected const SIZE_VARCHAR_LONG = 255;
    protected const SIZE_HASH = 32;

    protected $connection;

    public function __construct(string $db_type, string $db_host, string $db_name, string $db_user, string $db_pass) {
        $dsn = sprintf(
            "%s:host=%s;dbname=%s;charset=utf8mb4",
            $db_type,
            $db_host,
            $db_name
        );
        $this->connection = new \PDO(
            $dsn,
            $db_user,
            $db_pass,
            array(
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_PERSISTENT => false
            ),
        );
    }

    public function check(): void {

    }

    public function lastInsertId(?string $name = null): mixed {
        return $this->connection->lastInsertId($name);
    }

    public function execute(string $query, array $params = []): void {
        $handle = $this->connection->prepare($query);
        foreach ($params as $param) {
            $handle->bindParam($param->name, $param->value, $param->type);
        }

        $handle->execute();
    }

    public function fetchSingle(string $query, array $params = []): array {
        $handle = $this->connection->prepare($query);
        foreach ($params as $param) {
            $handle->bindParam($param->name, $param->value, $param->type);
        }

        $handle->execute();
        return $handle->fetch(\PDO::FETCH_NUM);
    }

    public function fetchMultiple(string $query, array $params = []): array {
        $handle = $this->connection->prepare($query);
        foreach ($params as $param) {
            $handle->bindParam($param->name, $param->value, $param->type);
        }

        $handle->execute();
        return $handle->fetchAll(\PDO::FETCH_NUM);
    }
}
