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
 * @Route( "/dokumentumok" )
 */
class DocumentController extends AbstractController
{
    /**
     * @Route( "/sutik-kezelese", name="sutik" )
     */
    public function cookies()
    {
        return $this->render( './page/sutik.twig', [
            "title" => 'PreAlp House Ingatlaniroda | Sütik kezelése'
        ]);
    }

    /**
     * @Route("/jogi-nyilatkozat", name="jogi-nyilatkozat")
     */
    public function jogi_nyilatkozat()
    {
        return $this->render( './page/jogi.twig', [
            "title" => 'PreAlp House Ingatlaniroda | Jogi nyilatkozat'
        ]);
    }

    /**
     * @Route("/adatvedelmi-tajekoztato", name="adatvedelmi-tajekoztato")
     */
    public function adatvedelmi_tajekoztato()
    {
        return $this->render( './page/adatvedelmi.twig', [
            "title" => 'PreAlp House Ingatlaniroda | Adatvédelmi tájékoztató'
        ]);
    }
}