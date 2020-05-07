<?php
    namespace App\Middleware;

    use Doctrine\ORM\EntityManagerInterface;
    use App\Entity\Estate;


class SlugFactory
{
    private $em;
    private $accentArray = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
    'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
    'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
    'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
    'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ű' => 'u', 'ő' => 'o', 'Ű' => 'u', 'Ő' => 'o', 'ü' => 'u', 'Ü' => 'u', 'ö' => 'o', 'Ö' => 'o' );

    public function __construct(EntityManagerInterface $_em)
    {
        $this->em = $_em;
    }

    public function generatePublicId()
    {
        $exists = false;
        
        do
        {
            $randomString = '';
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            for ($i = 0; $i < 8; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }

            $query = $this->em->createQueryBuilder();
            $query->select( 'e' )->from( Estate::class, 'e' )->where(
                $query->expr()->eq( 'e.publicId', '?1' )
            );
            $query->setParameter(1, $randomString);
            $result = $query->getQuery()->getResult();

            if ( count($result) > 0 )
            {
                $exists = true;
            }
            else
            {
                return strtoupper( $randomString );
            }
        }
        while ( $exists );
    }

    public function generateSlug( $slug )
    {
        $trimmedSlug = explode( " ", $slug );
        $str = "";

        for( $i = 0; $i < count( $trimmedSlug ); $i++ )
        {
            $str = $str . $trimmedSlug[$i];

            if ( $i !== count($trimmedSlug)-1 )
            {
                $str = $str . "-";
            }
        }

        if ( $this->isSlugExists( $str ) )
        {
            $num = rand( 1000, 100000 );
            $str = $str . $num;

            return strtolower( strtr( $str, $this->accentArray ) );
        }

        return strtolower( strtr( $str, $this->accentArray ) );
    }

    private function isSlugExists( $slug )
    {
        $query = $this->em->createQueryBuilder();
        $query->select( 'e' )->from( Estate::class, 'e' )->where(
            $query->expr()->eq( 'e.slug', '?1' )
        );
        $query->setParameter(1, $slug);

        $result = $query->getQuery()->getResult();

        if ( count($result) > 0 )
        {
            return true;
        }

        return false;
    }
}