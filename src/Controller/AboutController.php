<?php
    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Doctrine\ORM\EntityManagerInterface;

    use App\Entity\Estate;
    use App\Entity\Address;
    use App\Entity\Mapping;

/**
 * @Route( "/rolunk" )
 */
class AboutController extends AbstractController
{
    /**
     * @Route( "/ceginformacio", name="ceginfo" )
     */
    public function cookies()
    {
        return $this->render( './page/ceginfo.twig', [
            "title" => 'PreAlp House Ingatlaniroda | Céginformáció'
        ]);
    }
}