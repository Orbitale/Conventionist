<?php

namespace App\Controller\Admin;

use App\Entity;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin', routeOptions: ['methods' => ['GET', 'POST', 'DELETE', 'PATCH', 'PUT']])]
final class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Conventionist')
        ;
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addCssFile('styles/admin.css')
            ->addAssetMapperEntry('admin')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Convention organisation');
        yield MenuItem::linkToCrud('Events', 'fas fa-list', Entity\Event::class);
        yield MenuItem::linkToRoute('Calendar', 'fas fa-calendar', 'admin_calendar');

        yield MenuItem::section('Venue configurations');
        yield MenuItem::linkToCrud('Event Venues', 'fas fa-list', Entity\Venue::class);
        yield MenuItem::linkToCrud('Floors', 'fas fa-list', Entity\Floor::class)->setController(FloorCrudController::class);
        yield MenuItem::linkToCrud('Rooms', 'fas fa-list', Entity\Room::class)->setController(RoomCrudController::class);
        yield MenuItem::linkToCrud('Booths', 'fas fa-list', Entity\Booth::class)->setController(BoothCrudController::class);

        yield MenuItem::section('Activities');
        yield MenuItem::linkToCrud('Activities', 'fas fa-list', Entity\Activity::class);
        yield MenuItem::linkToCrud('Scheduled Activities', 'fas fa-list', Entity\ScheduledActivity::class);
        yield MenuItem::linkToCrud('Time Slots', 'fas fa-list', Entity\TimeSlot::class);

        yield MenuItem::section('Administration')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Users', 'fas fa-user', Entity\User::class)->setPermission('ROLE_ADMIN');
    }
}
