<?php

namespace App\Controller\Admin;

use App\Entity;
use App\Security\Voter\EventVoter;
use App\Security\Voter\VenueVoter;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin', routeOptions: ['methods' => ['GET', 'POST', 'DELETE', 'PATCH', 'PUT']])]
class DashboardController extends AbstractDashboardController
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
        yield MenuItem::section('Administration');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Convention organisation')->setPermission(EventVoter::CAN_VIEW_EVENTS);
        yield MenuItem::linkToCrud('Events', 'fas fa-list', Entity\Event::class)->setPermission(EventVoter::CAN_VIEW_EVENTS);
        yield MenuItem::linkToRoute('Calendar', 'fas fa-calendar', 'admin_calendar')->setPermission(EventVoter::CAN_VIEW_EVENTS);

        yield MenuItem::section('Venue configurations')->setPermission(VenueVoter::CAN_VIEW_VENUES);
        yield MenuItem::linkToCrud('Event Venues', 'fas fa-list', Entity\Venue::class)->setPermission(VenueVoter::CAN_VIEW_VENUES);
        yield MenuItem::linkToCrud('├─ Floors', 'fas fa-list', Entity\Floor::class)->setController(FloorCrudController::class)->setPermission(VenueVoter::CAN_VIEW_VENUES);
        yield MenuItem::linkToCrud('├── Rooms', 'fas fa-list', Entity\Room::class)->setPermission(VenueVoter::CAN_VIEW_VENUES);
        yield MenuItem::linkToCrud('└─── Tables', 'fas fa-list', Entity\Table::class)->setPermission(VenueVoter::CAN_VIEW_VENUES);

        yield MenuItem::section('Activities');
        yield MenuItem::linkToCrud('Animations', 'fas fa-list', Entity\Animation::class);
        yield MenuItem::linkToCrud('└─ Scheduled Animations', 'fas fa-list', Entity\ScheduledAnimation::class);
        yield MenuItem::linkToCrud('Time Slots', 'fas fa-list', Entity\TimeSlot::class);
    }
}
