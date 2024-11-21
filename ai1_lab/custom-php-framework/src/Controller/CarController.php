<?php
namespace App\Controller;

use App\Exception\ConfigException;
use App\Exception\NotFoundException;
use App\Model\Car;
use App\Service\Router;
use App\Service\Templating;

class CarController
{
    /**
     * @throws ConfigException
     */
    public function indexAction(Templating $templating, Router $router): ?string
    {
        $cars = Car::findAll();
        return $templating->render('car/index.html.php', [
            'cars' => $cars,
            'router' => $router,
        ]);
    }

    /**
     * @throws ConfigException
     */
    public function createAction(?array $requestCar, Templating $templating, Router $router): ?string
    {
        if ($requestCar) {
            $car = Car::fromArray($requestCar);
            $this->validate($requestCar);
            $car->save();

            $path = $router->generatePath('car-index');
            $router->redirect($path);
            return null;
        } else {
            $car = new Car();
        }

        return $templating->render('car/create.html.php', [
            'car' => $car,
            'router' => $router,
        ]);
    }

    /**
     * @throws ConfigException
     * @throws NotFoundException
     */
    public function editAction(int $carId, ?array $requestCar, Templating $templating, Router $router): ?string
    {
        $car = Car::find($carId);
        if (! $car) {
            throw new NotFoundException("Missing car with id $carId");
        }

        if ($requestCar) {
            $car->fill($requestCar);
            $this->validate($requestCar);
            $car->save();

            $path = $router->generatePath('car-index');
            $router->redirect($path);
            return null;
        }

        return $templating->render('car/edit.html.php', [
            'car' => $car,
            'router' => $router,
        ]);
    }

    /**
     * @throws ConfigException
     * @throws NotFoundException
     */
    public function showAction(int $carId, Templating $templating, Router $router): ?string
    {
        $car = Car::find($carId);
        if (! $car) {
            throw new NotFoundException("Missing car with id $carId");
        }

        return $templating->render('car/show.html.php', [
            'car' => $car,
            'router' => $router,
        ]);
    }

    /**
     * @throws ConfigException
     * @throws NotFoundException
     */
    public function deleteAction(int $carId, Router $router): ?string
    {
        $car = Car::find($carId);
        if (! $car) {
            throw new NotFoundException("Missing car with id $carId");
        }

        $car->delete();
        $path = $router->generatePath('car-index');
        $router->redirect($path);
        return null;
    }

    private function validate(array $data): void
    {
        $errors = [];

        if (empty($data['model'])) {
            $errors[] = 'Model is required.';
        }

        if (empty($data['brand'])) {
            $errors[] = 'Brand is required.';
        }

        if ($errors) {
            throw new InvalidArgumentException(implode(' ', $errors));
        }
    }
}
