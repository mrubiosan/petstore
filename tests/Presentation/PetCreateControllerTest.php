<?php
namespace Mrubiosan\PetStore\Tests\Presentation;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Fig\Http\Message\StatusCodeInterface;
use Mrubiosan\PetStore\Domain\Pet\Category;
use Mrubiosan\PetStore\Domain\Pet\Pet;
use Mrubiosan\PetStore\Domain\Pet\Tag;
use Mrubiosan\PetStore\Presentation\PetCreateController;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class PetCreateControllerTest extends TestCase
{
    private $testSubject;

    private $entityManagerMock;

    protected function setUp() : void
    {
        $this->entityManagerMock = $this->prophesize(EntityManager::class);
        $this->testSubject = new PetCreateController(
            $this->entityManagerMock->reveal()
        );
    }

    /**
     * @dataProvider badInputProvider
     */
    public function testCreateValidatesInput($payload, $expectedMessage)
    {
        $requestMock = $this->prophesize(Request::class);
        $requestMock
            ->getParsedBody()
            ->willReturn($payload)
            ->shouldBeCalled();

        $responseStub = new Response();

        $result = $this->testSubject->create($requestMock->reveal(), $responseStub);
        $this->assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $result->getStatusCode());
        $this->assertStringContainsString($expectedMessage, $result->getBody().'');
    }

    public function badInputProvider()
    {
        return [
            [[], 'name'],
            [['name' => 'foo'], 'photoUrls'],
            [['name' => 'foo', 'photoUrls' => ['moo']], 'photoUrls'],
            [['name' => 'foo', 'photoUrls' => ['moo'], 'tags' => '123'], 'tags'],
            [['name' => 'foo', 'photoUrls' => ['http://foo'], 'tags' => ['1']], 'tags[0].id'],
            [['name' => 'foo', 'photoUrls' => ['http://foo'], 'tags' => [['id' => 1]]], 'tags[0].name'],
            [['name' => 'foo', 'photoUrls' => ['http://foo'], 'status' => 'oops'], 'status'],
            [['name' => 'foo', 'photoUrls' => ['http://foo'], 'category' => 'oops'], 'category'],
        ];
    }

    public function testCreateSuccess()
    {
        $payload = [
            'name' => 'Doggie',
            'photoUrls' => [
                'http://example.com/photo1.jpg',
                'http://example.com/photo2.jpg',
            ],
            'category' => [
                'id' => 1,
                'name' => 'Dogs'
            ],
            'status' => 'pending',
            'tags' => [
                [
                    'id' => 123,
                    'name' => 'Tag 1',
                ],
                [
                    'id' => 124,
                    'name' => 'Tag 2',
                ]
            ]
        ];

        $requestMock = $this->prophesize(Request::class);
        $requestMock
            ->getParsedBody()
            ->willReturn($payload)
            ->shouldBeCalled();

        $responseStub = new Response();

        $categoryRepoMock = $this->prophesize(EntityRepository::class);
        $this->entityManagerMock
            ->getRepository(Category::class)
            ->willReturn($categoryRepoMock);

        $categoryRepoMock->find(1)
            ->shouldBeCalled()
            ->willReturn();

        $tagRepoMock = $this->prophesize(EntityRepository::class);
        $this->entityManagerMock
            ->getRepository(Tag::class)
            ->willReturn($tagRepoMock);

        $tagRepoMock->findBy(['id' => [123, 124]])
            ->shouldBeCalled()
            ->willReturn([]);

        $this->entityManagerMock
            ->persist(Argument::type(Pet::class))
            ->shouldBeCalled();

        $this->entityManagerMock
            ->flush()
            ->shouldBeCalled();

        $result = $this->testSubject->create($requestMock->reveal(), $responseStub);
        $this->assertEquals(StatusCodeInterface::STATUS_CREATED, $result->getStatusCode());
    }
}
