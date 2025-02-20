<?php

use App\Controller\AuthController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $configurator) {
    $configurator->import('../src/Controller/', 'attribute');
    $configurator->import('.', 'easyadmin.routes');

    $configurator->add('logout', AuthController::LOGOUT_PATHS)->methods(['GET']);
};
