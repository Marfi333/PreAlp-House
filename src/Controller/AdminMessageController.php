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
    use App\Entity\Message;
    use App\Entity\Mapping;
    use Symfony\Component\HttpFoundation\Cookie;

    use App\Middleware\Authentication;

    use App\Form\EstateNewFormType;

    use Gedmo\Sluggable\Util\Urlizer;
    use App\Middleware\SlugFactory;

    use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
    use Symfony\Component\Filesystem\Filesystem;

/**
 * @Route( "/uzenetek", host="dashboard.prealphouse.hu")
 */
class AdminMessageController extends AbstractController
{
    /**
     * @Route("/attekintes", name="admin_message_overview")
     */
    public function attekintes(Authentication $auth, EntityManagerInterface $em, Request $request)
    {
        if ( !$auth->isLoggedIn() )
        {
            return $this->redirectToRoute( "admin_login" );
        }

        $query = $em->createQueryBuilder();
        $query->select( 'm' )->from( Message::class, 'm' )->orderBy("m.date", "DESC");

        $result = $query->getQuery()->getResult();
        $route = $this->get('router')->getRouteCollection();

        return $this->render( "admin/message_overview.twig", [
            "submenu" => [
                "Áttekintés" => "https://" . $route->get( 'admin_message_overview' )->getHost() . $route->get( 'admin_message_overview' )->getPath(),
            ],
            "messages" => $result,
            "submenuTitle" => "Üzenet kezelés"
        ]);
    }

    /**
     * @Route("/megtekintes/{id}", name="admin_message_megtekintes")
     */
    public function megtekintes(Authentication $auth, EntityManagerInterface $em, Request $request, $id)
    {
        if ( !$auth->isLoggedIn() )
        {
            return $this->redirectToRoute( "admin_login" );
        }

        $query = $em->createQueryBuilder();
        $query->select( 'm' )->from( Message::class, 'm' )->where($query->expr()->eq('m.id', '?1'))->setParameter(1, $id);
        
        $result = $query->getQuery()->getResult();

        if ( count($result) > 0 )
        {
            $message = $result[0];
            if ( $message->getSeen() !== true )
            {
                $message->setSeen(true);
            }

            $em->flush();
        }

        $route = $this->get('router')->getRouteCollection();

        return $this->render( "admin/message_view.twig", [
            "submenu" => [
                "Áttekintés" => "https://" . $route->get( 'admin_message_overview' )->getHost() . $route->get( 'admin_message_overview' )->getPath(),
            ],
            "message" => $result,
            "submenuTitle" => "Üzenet kezelés"
        ]);
    }
}