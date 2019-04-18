<?php

namespace App\Tests\Functional;

use App\DataFixtures\BookFixtures;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class BookTest extends WebTestCase
{
    /**
     * @test
     */
    public function getBooksTest()
    {
        $this->loadFixtures([BookFixtures::class]);

        // Borrar Book 0
        $client = $this->createClient();
        $client->request('GET', '/books');
        $this->assertStatusCode(200, $client);

        // Cambiar de pagina (50 book, 10 por pagina)
        $client->request('GET', '/books');
        $this->assertStatusCode(200, $client);
        for ($i=5; $i>0; $i--) {
            $client->clickLink($i);
            $this->assertStatusCode(200, $client);
        }
    }

    /**
     * @test
     */
    public function newBooksTest()
    {
        $this->loadFixtures();

        $client = $this->createClient();
        $crawler = $client->request('GET', '/books/new');
        $this->assertStatusCode(200, $client);

        // Errores notBlank creando Book
        $form = $crawler->selectButton('Create')->form();
        $crawler = $client->submit($form);
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.title', 'data.description', 'data.author'], $client->getContainer());

        // Crear Book
        $form = $crawler->selectButton('Create')->form();
        $form->setValues(['book[title]' => 'Test Title', 'book[description]' => 'Test Description', 'book[author]' => 'Test Author']);
        $client->submit($form);
        $this->assertStatusCode(302, $client);

        // Error de unicidad editando Book
        $form = $crawler->selectButton('Create')->form();
        $form->setValues(['book[title]' => 'Test Title', 'book[description]' => 'Test Description', 'book[author]' => 'Test Author']);
        $client->submit($form);
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.title'], $client->getContainer());

        // Crear Book desde el link del listado
        $client->request('GET', '/books');
        $this->assertStatusCode(200, $client);
        $crawler = $client->clickLink('Add Book');
        $this->assertStatusCode(200, $client);
        $form = $crawler->selectButton('Create')->form();
        $form->setValues(['book[title]' => 'Test Title from link', 'book[description]' => 'Test Title from link', 'book[author]' => 'Test Title from link']);
        $client->submit($form);

        $this->loadFixtures([BookFixtures::class]);
    }

    /**
     * @test
     */
    public function editBooksTest()
    {
        $this->loadFixtures([BookFixtures::class]);

        $client = $this->createClient();
        $crawler = $client->request('GET', '/books/book-0');
        $this->assertStatusCode(200, $client);

        // Error de unicidad editando Book
        $form = $crawler->selectButton('Edit')->form();
        $form->setValues(['book[title]' => 'Book 1', 'book[description]' => 'Test Description', 'book[author]' => 'Test Author']);
        $client->submit($form);
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.title'], $client->getContainer());

        // Errores notBlank editando Book
        $form = $crawler->selectButton('Edit')->form();
        $form->setValues(['book[title]' => '', 'book[description]' => '', 'book[author]' => '']);
        $client->submit($form);
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.title', 'data.description', 'data.author'], $client->getContainer());

        // Editar Book
        $form = $crawler->selectButton('Edit')->form();
        $form->setValues(['book[title]' => 'Title Edited', 'book[description]' => 'Description Edited', 'book[author]' => 'Author Edited']);
        $client->submit($form);
        $this->assertStatusCode(302, $client);

        // Editar Book desde el link del listado
        $client->request('GET', '/books');
        $this->assertStatusCode(200, $client);
        $crawler = $client->clickLink('edit');
        $this->assertStatusCode(200, $client);
        $form = $crawler->selectButton('Edit')->form();
        $form->setValues(['book[title]' => 'Title Edited 2 from link', 'book[description]' => 'Description Edited from link', 'book[author]' => 'Author Edited from link']);
        $client->submit($form);

        $this->loadFixtures([BookFixtures::class]);
    }

    /**
     * @test
     */
    public function deleteBooksTest()
    {
        $this->loadFixtures([BookFixtures::class]);

        // Borrar Book 0
        $client = $this->createClient();
        $client->request('GET', '/books/book-0/delete');
        $this->assertStatusCode(302, $client);

        // Comprobar borrado
        $client->request('GET', '/books/book-0/delete');
        $this->assertStatusCode(404, $client);

        // Borrar Book desde el link del listado
        $client->request('GET', '/books');
        $this->assertStatusCode(200, $client);
        $client->clickLink('delete');
        $this->assertStatusCode(302, $client);

        $this->loadFixtures([BookFixtures::class]);
    }

    /**
     * @test
     */
    public function bookCountCommandTest()
    {
        $this->loadFixtures([BookFixtures::class]);

        $this->verbosityLevel = 'debug';
        $this->decorated = true;
        $command = $this->runCommand('books:count');
        $this->assertContains('[OK] Total: 50  Books', $command->getDisplay());
    }
}
