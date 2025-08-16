<?php

namespace App\Controller\Admin;

use App\Entity;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(
    routePath: '/{_locale}/admin',
    routeName: 'admin',
    routeOptions: [
        'requirements' => ['_locale' => '%locales_regex%'],
        'methods' => ['GET', 'POST', 'DELETE', 'PATCH', 'PUT'],
    ]
)]
final class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly array $locales,
    ) {
    }

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Conventionist')
            ->setLocales($this->locales)
        ;
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addCssFile('styles/admin.css')
            ->addCssFile('styles/common.css')
            ->addAssetMapperEntry('admin')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Back to website', 'fas fa-arrow-left', 'index');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-table-columns');

        yield MenuItem::section('Convention organisation');
        yield MenuItem::linkToCrud('Events', 'fas fa-calendar-days', Entity\Event::class);
        yield MenuItem::linkToRoute('Calendar', 'fas fa-timeline', 'admin_calendar');

        yield MenuItem::section('Venue configurations');
        yield MenuItem::linkToCrud('Event Venues', 'fas fa-map-pin', Entity\Venue::class);
        yield MenuItem::linkToCrud('Floors', 'fas fa-layer-group', Entity\Floor::class)->setController(FloorCrudController::class);
        yield MenuItem::linkToCrud('Rooms', 'fas fa-person-shelter', Entity\Room::class)->setController(RoomCrudController::class);
        yield MenuItem::linkToCrud('Booths', 'fas fa-person-booth', Entity\Booth::class)->setController(BoothCrudController::class);

        yield MenuItem::section('Activities');
        yield MenuItem::linkToCrud('Activities', 'fas fa-dice-d20', Entity\Activity::class);
        yield MenuItem::linkToCrud('Time Slots', 'fas fa-bars-staggered', Entity\TimeSlot::class);
        yield MenuItem::linkToCrud('Scheduled Activities', 'fas fa-diagram-predecessor', Entity\ScheduledActivity::class);

        yield MenuItem::section('Administration')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Users', 'fas fa-user', Entity\User::class)->setPermission('ROLE_ADMIN');
    }
}
