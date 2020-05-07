<?php
    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Doctrine\ORM\EntityManagerInterface;

    use App\Entity\Estate;
    use App\Entity\Address;
    use App\Entity\Mapping;


class HomeController extends AbstractController
{
    private $cdn = 'https://cdn.prealphouse.hu';

    /**
     * @Route("/", name="homepage")
     */
    public function homepage( EntityManagerInterface $em )
    {
        $estates = [];

        /*$estatesQuery = $this->getDoctrine()
                        ->getRepository( Estate::class )
                        ->findAll();*/

        $estatesQuery = $em->createQueryBuilder();
        $estatesQuery->select('e')->from(Estate::class, 'e')->orderBy( 'e.id', 'DESC' );

        $estatesResult = $estatesQuery->getQuery()->getResult();


        foreach ( $estatesResult as $estate )
        {
            array_push( $estates, [
                "imageCdn"  => $this->cdn . '/image/',
                "title"     => $estate->getTitle(),
                "price"     => \number_format( $estate->getPrice(), 0, ",", " " ),
                "size"      => $estate->getSize(),
                "rooms"     => $estate->getRooms(),
                "slug"      => $estate->getSlug(),
                "image"     => $estate->getId() . '/' . $estate->getImages()[0],
                "county"    => $estate->getCounty(),
                "city"      => $estate->getCity(),
            ]);
        }

        return $this->render( './page/homepage.twig', [
            "title" => 'PreAlp House Ingatlaniroda',
            "estates" => $estates
        ]);
    }
}