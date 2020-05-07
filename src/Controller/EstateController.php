<?php
    namespace App\Controller;

    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\HttpKernel\KernelInterface;
    use Symfony\Component\Finder\Finder;
    
    use App\Middleware\SearchQuerySorter;
    use App\Middleware\SearchQuery;
    use App\Entity\Estate;
    use App\Entity\Message;
    use App\Entity\Mapping;

    use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class EstateController extends AbstractController
{
    /**
     * @Route("/ingatlanok/{slug}", name="index_estates", defaults={"slug"=null})
     */
    public function search( Request $request, $slug, EntityManagerInterface $em )
    {
        $sorter = new SearchQuerySorter( $em, $slug );
        $filter = $sorter->querySorter();
        $twigFilter = $sorter->twigSorter( $filter );

        $search = new SearchQuery( $em );
        $result = $search->getResults( $filter );

        $valuta = 0;
        if ( array_key_exists( 'megbizas-tipusa', $filter ) )
        {
            if ( strpos( $filter['megbizas-tipusa'], 'Kiadó' ) !== false ) $valuta = 1000;
            if ( strpos( $filter['megbizas-tipusa'], 'Eladó' ) !== false ) $valuta = 1000000;
        }

        $values = [
            'title' => "PreAlp House Ingatlaniroda | Keresés",
            'filter' => $filter,
            'valuta' => $valuta,
            'twigFilter' => $twigFilter,
            'queryCount' => count( $result ),
            'queries' => $result
        ];

        $displayString = "";

        if ( array_key_exists( "megbizas-tipusa", $filter ) )
        {
            $displayString = $displayString . $filter["megbizas-tipusa"] . " ";
        }
        else
        {
            $displayString = $displayString . "Eladó és kiadó ";
        }

        if ( array_key_exists( "ingatlan-tipusa", $filter ) )
        {
            if ( $filter["ingatlan-tipusa"] == "Lakás" )
            {
                $displayString = $displayString . strtolower( $filter["ingatlan-tipusa"] ) . "ok ";
            }
            else
            {
                $displayString = $displayString . strtolower( $filter["ingatlan-tipusa"] ) . "ak ";
            }
        }
        else
        {
            $displayString = $displayString . "ingatlanok ";
        }

        if ( array_key_exists( "kereses", $filter ) )
        {
            $displayString = $displayString . "\"" . $filter["kereses"] . "\" kifejezés alapján";
        }
        else
        {
            $displayString = $displayString . "az ország területén";
        }
        $values["displayString"] = $displayString;

        $estates = $em->getRepository( Estate::class )->createQueryBuilder( 'e' )->select( 'count(e.id)' )->getQuery()->getSingleScalarResult();
        $values["estateCount"] = $estates;
        
        $values["page"] = array_key_exists( "oldal", $filter ) && $filter["oldal"] > 0 ? $filter["oldal"] : 1;
        dump($values["page"]);

        return $this->render('page/search.twig', $values);
    }

    /**
     * @Route("/ingatlan/{slug}", name="estate_details", defaults={"slug"=null})
     */
    public function estate( $slug )
    {
        $query = $this->getDoctrine()
                      ->getRepository( Estate::class )
                      ->findBySlug( $slug );

        if ( $query === [] ) return $this->render('page/estate_not_found.twig', [
            "title" => "PreAlp House Ingatlaniroda | Nincs találat"
        ]);

        $estate = $query[0];
        $location;
        
        switch ( $estate->getPermission() )
        {
            case null:
                $location = $estate->getCity() . ", " . $estate->getCounty() . " megye";
                break;

            case false:
                $location = $estate->getCity() . ", " . $estate->getCounty() . " megye";
                break;

            case true:
                $location = $estate->getAddress() . ", " . $estate->getCity() . ", " . $estate->getCounty() . " megye";
                break;
        }

        $imageString = [];
        for( $i = 0; $i < count( $estate->getImages() ); $i++ )
        {
            array_push( $imageString, $estate->getId() . "/" . $estate->getImages()[$i] );
        }

        return $this->render('page/estate.twig', [
            'title' => "PreAlp House Ingatlaniroda | " . $estate->getTitle(),
            'estate' => $estate,
            'location' => $location,
            'upload' => $estate->getUpload()->format('Y-m-d'),
            'publicId' => $estate->getPublicId(),
            'imageString' => json_encode( $imageString )
        ]);
    }

    /**
     * @Route( "/uzenet", name="message_post", methods={"POST"} )
     */
    public function message( Request $request, EntityManagerInterface $em )
    {
        if ( !$request->request->has( 'name' ) || !$request->request->has( 'email' ) || !$request->request->has( 'message' ) )
        {
            throw new BadRequestHttpException( 'Hiányzó mezők!' );
        }

        $name = $request->request->get( 'name' );
        $email = $request->request->get( 'email' );
        $message = $request->request->get( 'message' );

        if ( $request->request->has( 'phone' ) )
        {
            $phone = $request->request->get( 'phone' );

            if ( preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $phone ) )
            {
                throw new BadRequestHttpException( 'Hibás telefonszám mező!' );
            }
        }
        else
        {
            $phone = null;
        }

        if ( preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $name) )
        {
            throw new BadRequestHttpException( 'Hibás név mező!' );
        }
        
        if ( !filter_var($email, FILTER_VALIDATE_EMAIL) )
        {
            throw new BadRequestHttpException( 'Hibás email mező!' );
        }

        $messageQuery = new Message();
        $messageQuery->setName( $name );
        $messageQuery->setEmail( $email );
        if ( $phone !== null )
        {
            $messageQuery->setPhoneNumber( $phone );
        }
        $messageQuery->setMessage( $message );
        $messageQuery->setDate( new \DateTime("NOW") );
        $messageQuery->setSeen( false );

        $em->persist( $messageQuery );
        $em->flush();

        return new JsonResponse(["success" => true]); 
    }
}