<?php
    /** @var $car ?Car */

use App\Model\Car;

?>

<div class="form-group">
    <label for="brand">Brand</label>
    <input type="text" id="brand" name="car[brand]" value="<?= $car ? $car->getBrand() : '' ?>">
</div>

<div class="form-group">
    <label for="model">Model</label>
    <input type="text" id="model" name="car[model]" value="<?= $car? $car->getModel() : '' ?>">
</div>

<div class="form-group">
    <label></label>
    <input type="submit" value="Submit">
</div>
