<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    const LOREM_IPSUM = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Habent enim et bene longam et satis " .
    "litigiosam disputationem. Gracchum patrem non beatiorem fuisse quam fillum, cum alter stabilire rem publicam " .
    "studuerit, alter evertere. Si quae forte-possumus. Reguli reiciendam; Habes, inquam, Cato, formam eorum, de quibus " .
    "loquor, philosophorum. Duo Reges: constructio interrete. Beatus autem esse in maximarum rerum timore nemo potest. " .
    "Virtutis, magnitudinis animi, patientiae, fortitudinis fomentis dolor mitigari solet.";

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 50; $i++) {
            $book = new Book();
            $book->setTitle("Book $i");
            $book->setDescription(self::LOREM_IPSUM);
            $book->setAuthor('Autor ' . $i%4);
            $manager->persist($book);
        }

        $manager->flush();
    }
}
