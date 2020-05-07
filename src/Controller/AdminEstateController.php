<?php
    namespace App\Controller;

    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\HttpKernel\KernelInterface;
    use Symfony\Component\Finder\Finder;
    
    use App\Entity\User;
    use App\Entity\Estate;
    use App\Entity\Mapping;
    use Symfony\Component\HttpFoundation\Cookie;

    use App\Middleware\Authentication;

    use App\Form\EstateNewFormType;

    use Gedmo\Sluggable\Util\Urlizer;
    use App\Middleware\SlugFactory;

    use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
    use Symfony\Component\Filesystem\Filesystem;

/**
 * @Route( "/ingatlanok", host="dashboard.prealphouse.hu")
 */
class AdminEstateController extends AbstractController
{
    /**
     * @Route( "/attekintes", name="admin_estate_overview" )
     */
    public function overview( Request $request, EntityManagerInterface $em, Authentication $auth )
    {
        if ( !$auth->isLoggedIn() )
        {
            return $this->redirectToRoute( "admin_login" );
        }

        $query = $em->createQueryBuilder();
        $query->select( 'e' )->from( Estate::class, 'e' )->orderBy( 'e.id', 'DESC' );

        if ( $request->query->has( 'q' ) )
        {
            $query->where( $query->expr()->eq( 'e.publicId', '?1' ) );
            $query->setParameter( 1, $request->query->get( 'q' ) );
        }

        $result = $query->getQuery()->getResult();
        $route = $this->get('router')->getRouteCollection();

        return $this->render( "admin/estate_overview.twig", [
            "submenu" => [
                "Áttekintés" => "https://" . $route->get( 'admin_estate_overview' )->getHost() . $route->get( 'admin_estate_overview' )->getPath(),
                "Feltöltés" => "https://" . $route->get( 'admin_estate_add' )->getHost() . $route->get( 'admin_estate_add' )->getPath(),
            ],
            "estates" => $result,
            "submenuTitle" => "Ingatlan kezelés"
        ]);
    }

    /**
     * @Route( "/feltoltes", name="admin_estate_add", methods={"GET"} )
     */
    public function addEstate( Request $request, EntityManagerInterface $em, Authentication $auth )
    {
        if ( !$auth->isLoggedIn() )
        {
            return $this->redirectToRoute( "admin_login" );
        }

        $form = $this->createForm( EstateNewFormType::class );
        $route = $this->get('router')->getRouteCollection();

        $mapping = [];
        foreach( $em->createQueryBuilder()->select( 'm' )->from( Mapping::class, 'm' )->getQuery()->getResult() as $result )
        {
            if ( $result->getValue()[0] !== '*' )
            {
                $mapping[$result->getEnum()] = $result->getValue();
            }
        }

        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() )
        {
            dd($form->getData());
        }
        dump($mapping);

        return $this->render( "admin/estate_new.twig", [
            "submenu" => [
                "Áttekintés" => "https://" . $route->get( 'admin_estate_overview' )->getHost() . $route->get( 'admin_estate_overview' )->getPath(),
                "Feltöltés" => "https://" . $route->get( 'admin_estate_add' )->getHost() . $route->get( 'admin_estate_add' )->getPath(),
            ],
            "form" => $form->createView(),
            "mapping" => $mapping,
            "submenuTitle" => "Ingatlan kezelés"
        ]);
    }

    /**
     * @Route( "/feltoltes", name="admin_estate_add_process", methods={"POST"} )
     */
    public function processEstateUpload( Request $request, Authentication $auth, EntityManagerInterface $em, SlugFactory $slugFactory, Filesystem $filesystem )
    {
        if ( !$auth->isLoggedIn() )
        {
            return $this->redirectToRoute( "admin_login" );
        }

        $params = json_decode( $request->getContent(), true );
        $mustHaveParams = [
            "images",
            "title",
            "megbizasTipusa",
            "ingatlanTipusa",
            "szerkezet",
            "futes",
            "kilatas",
            "allapot",
            "emelet",
            "szobak",
            "meret",
            "ar",
            "megye",
            "varos",
            "address",
            "content"
        ];

        foreach ( $mustHaveParams as $testParam )
        {
            if ( !array_key_exists( $testParam, $params ) )
            {
                return new JsonResponse([ "success" => false, "message" => "Hiányzó adatmezők!" ]);
            }
        }

        $estate = new Estate();
        $estate->setTitle( $params["title"] );
        $estate->setPrice( (int)$params["ar"] );
        $estate->setSize( (int)$params["meret"] );
        $estate->setRooms( $params["szobak"] );
        $estate->setUpload( new \DateTime("NOW") );

        if ( array_key_exists( "megjegyzes", $params ) )
            $estate->setComment( $params["megjegyzes"] );

        $estate->setSlug( $slugFactory->generateSlug( $params["title"] ) );
        $estate->setImages([]);
        $estate->setPublicId( $slugFactory->generatePublicId() );
        $estate->setOrderType( $params["megbizasTipusa"] );
        $estate->setEstateType( $params["ingatlanTipusa"] );;
        $estate->setStructure( $params["szerkezet"] );
        $estate->setHeating( $params["futes"] );

        if ( array_key_exists( "erkely", $params ) )
            if ( $params["erkely"] == "Igen" )
            {
                $estate->setBalcony( true );
            }
            elseif ( $params["erkely"] == "Nem" )
            {
                $estate->setBalcony( false );
            }
            else
            {
                $estate->setBalcony( null );
            }

        if ( array_key_exists( "built", $params ) )
            $estate->setBuild( new \DateTime( $params["built"] ) );

        if ( array_key_exists( "lift", $params ) ) 
            if ( $params["lift"] == 1 )
            {
                $estate->setLift( true );
            }
            elseif ( $params["lift"] == 0 )
            {
                $estate->setLift( false );
            }
            else
            {
                $estate->setLift( null );
            }

        $estate->setCondition( $params["allapot"] );
        $estate->setFloor( $params["emelet"] );
        $estate->setView( $params["kilatas"] );

        if ( array_key_exists( "akadalymentesitett", $params ) ) 
            if ( $params["kilatas"] == "Igen" )
            {
                $estate->setHandicap( true );
            }
            elseif ( $params["akadalymentesitett"] == "Nem" )
            {
                $estate->setHandicap( false );
            }
            else
            {
                $estate->setHandicap( null );
            }

        $estate->setDescription( $params["content"] );
        $estate->setCountry( "Magyarország" );
        $estate->setCounty( str_replace( " megye", "", $params["megye"]) );
        $estate->setCity( $params["varos"] );
        $estate->setAddress( $params["address"] );

        if ( array_key_exists( "publikusCim", $params ) )
            $estate->setPermission( $params["publikusCim"] );

        $em->persist( $estate );
        $em->flush();

        $id = $estate->getId();
        $uploadDir = $this->getParameter('kernel.project_dir').'/uploads';
        $imageDir = $this->getParameter('kernel.project_dir').'/images';
        $imageArray = [];

        $filesystem->mkdir($imageDir . "/{$id}");
        foreach ( $params["images"] as $image )
        {
            $img = str_replace( "https://cdn.prealphouse.hu/uploads/", "", $image["src"] );

            if ( $image["main"] )
            {
                array_unshift( $imageArray, $img );
            }
            else
            {
                array_push( $imageArray, $img );
            }

            $filesystem->copy( $uploadDir . "/{$img}", $imageDir . "/{$id}/{$img}", true );
            //TODO: Remove original file
        }

        $estate->setImages($imageArray);
        $em->flush();

        return new JsonResponse(["success" => true]);
    }

    /**
     * @Route( "/torles/{id}", name="admin_estate_remove" )
     */
    public function remove(Authentication $auth, $id, EntityManagerInterface $em, Filesystem $filesystem)
    {
        if ( !$auth->isLoggedIn() )
        {
            return $this->redirectToRoute( "admin_login" );
        }

        $query = $em->createQueryBuilder();
        $query->select( 'e' )->from( Estate::class, 'e' )->where(
            $query->expr()->eq( 'e.id', '?1' )
        )->setParameter( 1, $id );

        $result = $query->getQuery()->getResult();

        if ( count($result) < 1 )
        {
            return new JsonResponse(["success" => false, "message" => "Az ingatlan nem található!"]);
        }

        $em->remove( $result[0] );
        $em->flush();

        $estateImageFolder = $this->getParameter('kernel.project_dir').'/images/'.$id;

        try
        {
            $filesystem->remove($estateImageFolder);
        }
        catch(Exception $ex)
        {
            return new JsonResponse(["success" => false, "message" => "Hiba történt a mappa törlésekor"]);
        }

        return new JsonResponse(["success" => true]);
    }

    /**
     * @Route( "/szerkesztes/{id}", name="admin_estate_edit", methods={"GET"} )
     */
    public function editMenu(Authentication $auth, EntityManagerInterface $em, $id, Request $request)
    {
        if ( !$auth->isLoggedIn() )
        {
            return $this->redirectToRoute( "admin_login" );
        }

        $form = $this->createForm( EstateNewFormType::class );
        $route = $this->get('router')->getRouteCollection();
        $query = $em->createQueryBuilder();
        $resultEstate = $query->select('e')->from(Estate::class, 'e')->where($query->expr()->eq('e.id', '?1'))->setParameter(1, $id)->getQuery()->getResult();

        if ( count($resultEstate) < 1 )
        {
            return $this->redirectToRoute('admin_not_found');
        }

        $mapping = [];
        foreach( $em->createQueryBuilder()->select( 'm' )->from( Mapping::class, 'm' )->getQuery()->getResult() as $result )
        {
            if ( $result->getValue()[0] !== '*' )
            {
                $mapping[$result->getEnum()] = $result->getValue();
            }
        }

        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() )
        {
            dd($form->getData());
        }
        dump($resultEstate[0]);
        dump($mapping);

        return $this->render( "admin/estate_edit.twig", [
            "submenu" => [
                "Áttekintés" => "https://" . $route->get( 'admin_estate_overview' )->getHost() . $route->get( 'admin_estate_overview' )->getPath(),
                "Feltöltés" => "https://" . $route->get( 'admin_estate_add' )->getHost() . $route->get( 'admin_estate_add' )->getPath(),
            ],
            "form" => $form->createView(),
            "mapping" => $mapping,
            "estate" => $resultEstate[0],
            "submenuTitle" => "Ingatlan kezelés"
        ]);
    }

    /**
     * @Route( "/szerkesztes/{id}", name="admin_estate_edit_post", methods={"POST"} )
     */
    public function editEstate( $id, Authentication $auth, Request $request, EntityManagerInterface $em, Filesystem $filesystem )
    {
        if ( !$auth->isLoggedIn() )
        {
            return $this->redirectToRoute( "admin_login" );
        }

        $estate = $em->getRepository(Estate::class)->find($id);

        if ( !$estate )
        {
            throw $this->createNotFoundException( "Nem található a megadott ingatlan." );
        }

        $params = json_decode( $request->getContent(), true );

        if ( array_key_exists("title", $params) ) $estate->setTitle( $params["title"] );
        if ( array_key_exists("ar", $params) ) $estate->setPrice( (int)$params["ar"] );
        if ( array_key_exists("meret", $params) ) $estate->setSize( $params["meret"] );
        if ( array_key_exists("szobak", $params) ) $estate->setRooms( $params["szobak"] );
        if ( array_key_exists("megjegyzes", $params) ) $estate->setComment( $params["megjegyzes"] );
        if ( array_key_exists("megbizasTipusa", $params) ) $estate->setOrderType( $params["megbizasTipusa"] );
        if ( array_key_exists("ingatlanTipusa", $params) ) $estate->setEstateType( $params["ingatlanTipusa"] );
        if ( array_key_exists("szerkezet", $params) ) $estate->setStructure( $params["szerkezet"] );
        if ( array_key_exists("futes", $params) ) $estate->setHeating( $params["futes"] );
        //TODO: valami hiba van
        if ( array_key_exists( "erkely", $params ) )
            if ( $params["erkely"] == "Igen" )
            {
                $estate->setBalcony( true );
            }
            elseif ( $params["erkely"] == "Nem" )
            {
                $estate->setBalcony( false );
            }
            else
            {
                $estate->setBalcony( null );
            }

        if ( array_key_exists( "built", $params ) )
            $estate->setBuild( new \DateTime( $params["built"] ) );

        if ( array_key_exists( "lift", $params ) ) 
            if ( $params["lift"] == 1 )
            {
                $estate->setLift( true );
            }
            elseif ( $params["lift"] == 0 )
            {
                $estate->setLift( false );
            }
            else
            {
                $estate->setLift( null );
            }

        if ( array_key_exists("allapot", $params) ) $estate->setCondition( $params["allapot"] );
        if ( array_key_exists("emelet", $params) ) $estate->setFloor( $params["emelet"] );
        if ( array_key_exists("kilatas", $params) ) $estate->setView( $params["kilatas"] );

        if ( array_key_exists( "akadalymentesitett", $params ) ) 
            if ( $params["akadalymentesitett"] == "Igen" )
            {
                $estate->setHandicap( true );
            }
            elseif ( $params["akadalymentesitett"] == "Nem" )
            {
                $estate->setHandicap( false );
            }
            else
            {
                $estate->setHandicap( null );
            }

        if ( array_key_exists("content", $params) ) $estate->setDescription( $params["content"] );
        // $estate->setCountry("Magyarország");
        if ( array_key_exists("megye", $params) ) $estate->setCounty( str_replace( " megye", "", $params["megye"]) );
        if ( array_key_exists("varos", $params) ) $estate->setCity( $params["varos"] );
        if ( array_key_exists("cim", $params) ) $estate->setAddress( $params["cim"] );

        if ( array_key_exists( "publikusCim", $params ) )
            $estate->setPermission( $params["publikusCim"] );

        $uploadDir = $this->getParameter('kernel.project_dir').'/uploads';
        $imageDir = $this->getParameter('kernel.project_dir').'/images';
        $newImageArray = [];

        foreach ( $params["images"] as $image )
        {
            $src = str_replace( "https://cdn.prealphouse.hu/uploads/", "", $image["src"] );
            
            $image["main"] ? array_unshift( $newImageArray, $src ) : array_push( $newImageArray, $src );
            !in_array( $src, $estate->getImages() ) ? $filesystem->copy( $uploadDir . "/{$src}", $imageDir . "/{$estate->getId()}/{$src}", true ) : $filesystem->remove( $imageDir . "/{$estate->getId()}/{$src}" );
        }

        $estate->setImages( $newImageArray );
        $em->flush();

//dump($estate->getImages());
//dd($params["images"]);
        return new JsonResponse(["success" => true]);
    }

    /**
     * @Route("/kep/feltoltes", name="upload_test", methods={"POST"})
     */
    public function imageUploadAction( Request $request, Authentication $auth )
    {
        if ( !$auth->isLoggedIn() )
        {
            return $this->redirectToRoute( "admin_login" );
        }

        try
        {
            $uploadedFile = $request->files->get('image');
            if ( strpos( $uploadedFile->getMimeType(), "image" ) === false )
            {
                return new Jsonresponse([
                    "success" => false,
                    "message" => "Not an image"
                ]);
            }
            $destination = $this->getParameter('kernel.project_dir').'/uploads';

            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();

            $uploadedFile->move($destination, $newFilename);
        }
        catch( \Exception $exception )
        {
            return new JsonResponse([
                "success" => false,
                "message" => $exception->getMessage()
            ]);
        }

        return new JsonResponse([
            "success" => true,
            "path" => $newFilename
        ]);
    }
}