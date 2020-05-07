<?php
    namespace App\Middleware;

    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpFoundation\Request;
    use Doctrine\ORM\EntityManagerInterface;
    use App\Entity\User;


class Authentication
{
    private $secret = "46ds8grg8dfgdfgs3e5rsa34w4sgs";
    private $METHOD = 'aes-256-ctr';
    private $request;
    private $em;

    public function __construct( RequestStack $req, EntityManagerInterface $em )
    {
        $this->request = $req->getCurrentRequest();
        $this->em = $em;
    }

    public function getUser()
    {
        if ( !$this->request->cookies->has( "_ga" ) && !$this->request->cookies->has( "_gt" ) )
        {
            return null;
        }
        
        try
        {
            $user = $this->decrypt( $this->request->cookies->get( "_ga" ) );
            $pass = $this->decrypt( $this->request->cookies->get( "_gt" ) );
        }
        catch( \Exception $ex )
        {
            return false;
        }

        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select( 'user' )
                     ->from( User::class, 'user' )
                     ->where( $queryBuilder->expr()->eq( 'user.id', '?1' ) )
                     ->setParameter( 1, $user );

        $queryResponse = $queryBuilder->getQuery()->getResult();

        if ( count( $queryResponse ) === 0 )
        {
            return false;
        }

        if( $queryResponse[0]->getPassword() !== $pass )
        {
            return false;
        }

        return $queryResponse[0];
    }

    public function isLoggedIn()
    {
        if ( is_object( $this->getUser()) )
        {
            return true;
        }

        return false;
    }

    public function encrypt( $message, $encode = true )
    {
        $nonceSize = openssl_cipher_iv_length( $this->METHOD );
        $nonce = openssl_random_pseudo_bytes( $nonceSize );

        $ciphertext = openssl_encrypt(
            $message,
            $this->METHOD,
            $this->secret,
            OPENSSL_RAW_DATA,
            $nonce
        );

        if ( $encode ) 
        {
            return base64_encode( $nonce.$ciphertext );
        }

        return $nonce.$ciphertext;
    }

    public function decrypt( $message, $encoded = true )
    {
        if ( $encoded ) 
        {
            $message = base64_decode( $message, true );

            if ( $message === false ) 
            {
                throw new \Exception( 'Encryption failure' );
            }
        }

        $nonceSize = openssl_cipher_iv_length( $this->METHOD );
        $nonce = mb_substr( $message, 0, $nonceSize, '8bit' );
        $ciphertext = mb_substr( $message, $nonceSize, null, '8bit' );

        $plaintext = openssl_decrypt(
            $ciphertext,
            $this->METHOD,
            $this->secret,
            OPENSSL_RAW_DATA,
            $nonce
        );

        return $plaintext;
    }
}