<?php

namespace App\Tests\Controller\Admin\Utils;

use App\Tests\TestUtils\GetUser;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;

trait TestAdminEdit
{
    protected function runEditFormSubmit(string|int $id, array $newData): void
    {
        if (!$this instanceof AbstractCrudTestCase) {
            throw new \RuntimeException(\sprintf('Trait "%s" used by class "%s" can only be used in an instance of "%s".', self::class, static::class, AbstractCrudTestCase::class));
        }
        if (!isset(\class_uses(static::class)[GetUser::class])) {
            throw new \RuntimeException(\sprintf('Trait "%s" used by class "%s" can only be used when using the "%s" trait.', self::class, static::class, GetUser::class));
        }

        $this->client->loginUser($this->getUser());
        $this->client->request('GET', $this->generateEditFormUrl($id));

        /** @var class-string<AbstractCrudController> $controllerClass */
        $controllerClass = $this->getControllerFqcn();
        $entityClass = $controllerClass::getEntityFqcn();
        $entityName = \basename(\str_replace('\\', '/', $entityClass));

        $data = [];
        foreach ($newData as $k => $v) {
            $data[sprintf('%s[%s]', $entityName, $k)] = $v;
        }

        $form = $this->client->getCrawler()->filter($this->getEntityFormSelector())->form($data);
        $this->client->submit($form, $data);
        self::assertResponseStatusCodeSame(302);
        $crawler = $this->client->followRedirect();
        self::assertResponseStatusCodeSame(200);
        $flashText = $crawler->filter('#flash-messages')?->text();
        self::assertNotEmpty($flashText, 'There are apparently no flash message confirming object creation.');
        self::assertStringStartsWith('Successfully updated ', $flashText);
        self::assertStringEndsWith(sprintf('%s"!', $newData['name']), $flashText);

        $repo = $this->client->getContainer()->get(EntityManagerInterface::class)->getRepository($entityClass);
        $element = $repo->find($id);
        self::assertInstanceOf($entityClass, $element);
    }
}
