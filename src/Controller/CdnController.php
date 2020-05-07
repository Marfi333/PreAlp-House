<?php
    namespace App\Controller;

    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\KernelInterface;
    use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @Route( host="cdn.prealphouse.hu" )
 */
class CdnController extends AbstractController
{
    /**
     * @Route("/image/{_folder}/{_file}", name="cdn_image")
     */
    public function image( $_folder, $_file, KernelInterface $kernel )
    {
        if ( \file_exists( $kernel->getProjectDir() . '/images/' . $_folder . '/' . $_file ) )
        {
            return new BinaryFileResponse( $kernel->getProjectDir() . '/images/' . $_folder . '/' . $_file );
        }

        throw $this->createNotFoundException('Image not found.');
    }

    /**
     * @Route( "/uploads/{path}", name="cdn_uploads" )
     */
    public function uploadImage( $path, KernelInterface $kernel )
    {
        if ( \file_exists( $kernel->getProjectDir() . '/uploads/' . $path ) )
        {
            return new BinaryFileResponse( $kernel->getProjectDir() . '/uploads/' . $path );
        }

        throw $this->createNotFoundException( 'Image not found.' );
    }
}