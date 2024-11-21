<?php
namespace App\Model;

use App\Exception\ConfigException;
use App\Service\Config;
use PDO;

class Car
{
    private ?int $id = null;
    private ?string $brand = null;
    private ?string $model = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Car
    {
        $this->id = $id;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): Car
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): Car
    {
        $this->model = $model;

        return $this;
    }

    public static function fromArray($array): Car
    {
        $car = new self();
        $car->fill($array);

        return $car;
    }

    public function fill($array): Car
    {
        if (isset($array['id']) && ! $this->getId()) {
            $this->setId($array['id']);
        }
        if (isset($array['brand'])) {
            $this->setBrand($array['brand']);
        }
        if (isset($array['model'])) {
            $this->setModel($array['model']);
        }

        return $this;
    }

    /**
     * @throws ConfigException
     */
    public static function findAll(): array
    {
        $pdo = new PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        $sql = 'SELECT * FROM car';
        $statement = $pdo->prepare($sql);
        $statement->execute();

        $cars = [];
        $carsArray = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($carsArray as $carArray) {
            $cars[] = self::fromArray($carArray);
        }

        return $cars;
    }

    /**
     * @throws ConfigException
     */
    public static function find($id): ?Car
    {
        $pdo = new PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        $sql = 'SELECT * FROM car WHERE id = :id';
        $statement = $pdo->prepare($sql);
        $statement->execute(['id' => $id]);

        $carArray = $statement->fetch(PDO::FETCH_ASSOC);
        if (! $carArray) {
            return null;
        }
        return Car::fromArray($carArray);
    }

    /**
     * @throws ConfigException
     */
    public function save(): void
    {
        $pdo = new PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        if (! $this->getId()) {
            $sql = "INSERT INTO car (brand, model) VALUES (:brand, :model)";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                'brand' => $this->getBrand(),
                'model' => $this->getModel(),
            ]);

            $this->setId($pdo->lastInsertId());
        } else {
            $sql = "UPDATE car SET brand = :brand, model = :model WHERE id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':brand' => $this->getBrand(),
                ':model' => $this->getModel(),
                ':id' => $this->getId(),
            ]);
        }
    }

    /**
     * @throws ConfigException
     */
    public function delete(): void
    {
        $pdo = new PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        $sql = "DELETE FROM car WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->execute([
            ':id' => $this->getId(),
        ]);

        $this->setId(null);
        $this->setBrand(null);
        $this->setModel(null);
    }
}
