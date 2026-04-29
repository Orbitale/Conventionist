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
        yield MenuItem::linkTo(EventCrudController::class, 'Events', 'fas fa-calendar-days');
        yield MenuItem::linkToRoute('Calendar', 'fas fa-timeline', 'admin_calendar');

        yield MenuItem::section('Venue configurations');
        yield MenuItem::linkTo(VenueCrudController::class, 'Event Venues', 'fas fa-map-pin');
        yield MenuItem::linkTo(FloorCrudController::class, 'Floors', 'fas fa-layer-group');
        yield MenuItem::linkTo(RoomCrudController::class, 'Rooms', 'fas fa-person-shelter');
        yield MenuItem::linkTo(BoothCrudController::class, 'Booths', 'fas fa-person-booth');

        yield MenuItem::section('Activities');
        yield MenuItem::linkTo(ActivityCrudController::class, 'Activities', 'fas fa-dice-d20');
        yield MenuItem::linkTo(TimeSlotCrudController::class, 'Time Slots', 'fas fa-bars-staggered');
        yield MenuItem::linkTo(ScheduledActivityCrudController::class, 'Scheduled Activities', 'fas fa-diagram-predecessor');

        yield MenuItem::section('Organizations');
        yield MenuItem::linkTo(OrganizationCrudController::class, 'Organizations', 'fas fa-building');
        yield MenuItem::linkTo(UserGroupCrudController::class, 'User Groups', 'fas fa-users');
        yield MenuItem::linkTo(UserGroupMembershipCrudController::class, 'Group Memberships', 'fas fa-user-group');
        yield MenuItem::linkTo(UserExhibitionRoleCrudController::class, 'Exhibition Roles', 'fas fa-user-tag');

        yield MenuItem::section('Game catalog');
        yield MenuItem::linkTo(GameCrudController::class, 'Games', 'fas fa-dice');
        yield MenuItem::linkTo(GameCategoryCrudController::class, 'Game Categories', 'fas fa-tags');
        yield MenuItem::linkTo(GameThemeCrudController::class, 'Game Themes', 'fas fa-palette');
        yield MenuItem::linkTo(SafetyToolCrudController::class, 'Safety Tools', 'fas fa-shield-heart');

        yield MenuItem::section('Attendance');
        yield MenuItem::linkTo(EventRegistrationCrudController::class, 'Event Registrations', 'fas fa-ticket');
        yield MenuItem::linkTo(EventRequestCrudController::class, 'Event Requests', 'fas fa-hand-paper');

        yield MenuItem::section('Administration')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkTo(UsersCrudController::class, 'Users', 'fas fa-user')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkTo(NotificationCrudController::class, 'Notifications', 'fas fa-bell')->setPermission('ROLE_ADMIN');
    }
}
