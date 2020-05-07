<?php
    namespace App\Controller;

    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\HttpKernel\KernelInterface;
    use Symfony\Component\Finder\Finder;
    
    use App\Middleware\SearchQuerySorter;
    use App\Middleware\SearchQuery;
    use App\Entity\User;
    use Symfony\Component\HttpFoundation\Cookie;

    use App\Middleware\Authentication;

/**
 * @Route(host="dashboard.prealphouse.hu")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/bejelentkezes", name="admin_login", methods={"GET"})
     */
    public function login( Authentication $auth )
    {
        if ( $auth->isLoggedIn() )
        {
            return $this->redirectToRoute( "admin_estate_overview" );
        }

        return $this->render( "admin/login.twig", [
            "error" => false
        ]);
    }

    /**
     * @Route( "/kijelentkezes", name="admin_logout" )
     */
    public function logout()
    {
        $response = $this->redirectToRoute( "admin_login" );

        $response->headers->clearCookie( "_ga" );
        $response->headers->clearCookie( "_gt" );
        
        return $response;
    }

    /**
     * @Route( "/_1df45gd4fr85gdr/regisztracio/{username}/{email}/{password}/{name}/{phone}/{workemail}/{seller}", name="admin_register" )
     */
    public function register( $username, $email, $password, $name, $phone, $seller, $workemail, EntityManagerInterface $em )
    {
        $qb = $em->createQueryBuilder();

        $qb ->select( 'u' )
            ->from( User::class, 'u' )
            ->where( $qb->expr()->orX(
                $qb->expr()->eq( 'u.username', '?1' ),
                $qb->expr()->eq( 'u.email', '?2' )
            ))
            ->setParameter( 1, $username )
            ->setParameter( 2, $email );

        $qResponse = $qb->getQuery()->getResult();

        if ( count( $qResponse ) !== 0 )
        {
            return new Response( "<h1 style='font-family: Segoe UI'>A megadott felhasználónév vagy jelszó foglalt!</h1>" );
        }

        $user = new User();
        $user->setUsername( $username );
        $user->setEmail( $email );
        $user->setPassword( password_hash( $password, PASSWORD_DEFAULT ) );
        $user->setName( $name );
        $user->setPhoneNumber( $phone );
        $user->setWorkEmail( $workemail );
        if ( $seller == 0 )
        {
            $user->setSeller( false );
        }
        else
        {
            $user->setSeller( 1 );
        }

        $em->persist( $user );
        $em->flush();

        return new Response( "<h1 style='font-family: Segoe UI'>\"".$username."\" felhasználó sikeresen regisztrálva!</h1>" );
    }

    /**
     * @Route("/404", name="admin_not_found")
     */
    public function adminNotFound()
    {
        return new Response("404");
    }

    /**
     * @Route("/bejelentkezes", name="admin_login_process", methods={"POST"})
     */
    public function loginProcess( Request $request, EntityManagerInterface $em, Authentication $auth )
    {
        $errorBool = false;
        $errorMessage = [];

        if ( !$this->isCsrfTokenValid( 'login', $request->request->get( 'token' ) ) )
        {
            $errorBool = true;
            array_push( $errorMessage, "Érvénytelen CSRF token!" );
        }

        if ( !$request->request->has( "username" ) && !$request->request->has( "password" ) )
        {
            $errorBool = true;
            array_push( $errorMessage, "Hiányzó mezők!" );
        }

        $request->request->has( "username" ) ? $username = $request->request->get( "username" ) : $username = null;

        $qb = $em->createQueryBuilder();

        $qb ->select( 'u' )
            ->from( User::class, 'u' )
            ->where( $qb->expr()->orX(
                $qb->expr()->eq( 'u.username', '?1' ),
                $qb->expr()->eq( 'u.email', '?1' )
            ))
            ->setParameter( 1, $request->request->get( "username" ) );

        if ( count( $qb->getQuery()->getResult() ) !== 1 )
        {
            $errorBool = true;
            array_push( $errorMessage, "Nem létező felhasználó!" );
        }
        else
        {
            $responseQuery = $qb->getQuery()->getResult()[0];

            if ( !password_verify( $request->request->get( "password" ), $responseQuery->getPassword() ) )
            {
                $errorBool = true;
                array_push( $errorMessage, "Hibás jelszó!" );
            }
            else
            {
                $response = $this->redirectToRoute( "admin_estate_overview" );
                $response->headers->setCookie(new Cookie( "_ga", $auth->encrypt( $responseQuery->getId() ) ));
                $response->headers->setCookie(new Cookie( "_gt", $auth->encrypt( $responseQuery->getPassword() ) ));

                return $response;
            }
        }

        return $this->render( "admin/login.twig", [
            "error" => $errorBool,
            "errorList" => $errorMessage,
            "username" => $username
        ]);
    }

    /**
     * @Route("/", name="admin_dashboard")
     */
    public function dashboard( Authentication $auth )
    {
        if ( !$auth->isLoggedIn() )
        {
            return $this->redirectToRoute( 'admin_login' );
        }

        return $this->redirectToRoute('admin_estate_overview');

        $route = $this->get('router')->getRouteCollection();

        return $this->render( "admin/homepage.twig", [
            "submenu" => [
                "Kezdőlap" => "/",
                "Statisztika" => "/statisztika"
            ]
        ]);
    }
}