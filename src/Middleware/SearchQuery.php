<?php
    namespace App\Middleware;

    use App\Entity\Estate;
    use Doctrine\ORM\EntityManagerInterface;


class SearchQuery
{
    private $em;
    private $betweenElements = [
        "ar",
        "meret"
    ];
    private $keyMapping = [
        "megbizas-tipusa" => "order_type",
        "ingatlan-tipusa" => "estate_type",
        "meret" => "size",
        "szobak" => "rooms",
        "ar" => "price",
        "szerkezet" => "structure",
        "futes" => "heating",
        "erkely" => "balcony",
        "lift" => "lift",
        "akadalymentesitett" => "handicap",
        "allapot" => "condition",
        "emelet" => "floor",
        "kilatas" => "view"
    ];
    private $keyBooleans = [
        "erkely",
        "lift",
        "akadalymentesitett"
    ];

    public function __construct( EntityManagerInterface $em )
    {
        $this->em = $em;
    }

    private function createSelect( array $filter )
    {
        $query = $this->em->createQueryBuilder();
        $query->select( 'e' )
              ->from( Estate::class, 'e' );

        $queryArray = [];
        $queryParameters = [];
        $paramCounter = 1;

        foreach ( $filter as $key => $value )
        {
            if ( !is_array( $value ) )
            {
                if ( in_array( $key, $this->keyBooleans ) )
                {
                    array_push( $queryArray, $query->expr()->eq( "e.{$this->keyMapping[$key]}", "?{$paramCounter}" ) );
                    $queryParameters[$paramCounter] = $this->convertToBoolean( $value );
                    $paramCounter++;
                }
                elseif ( $key === "kereses" )
                {
                    // TODO: Jobb keresés
                    array_push( $queryArray, $query->expr()->orX(
                        $query->expr()->like( 'e.title', "?{$paramCounter}" ),
                        $query->expr()->like( 'e.country', "?{$paramCounter}" ),
                        $query->expr()->like( 'e.county', "?{$paramCounter}" ),
                        $query->expr()->like( 'e.city', "?{$paramCounter}" )
                    ));
                    $queryParameters[$paramCounter] = $value;
                    $paramCounter++;
                }
                elseif ( $key === "rendezes" )
                {
                    //TODO: Rendezés
                    switch( $value )
                    {
                        case 'Népszerűség szerint':
                            // TODO: Népszerűség figyelése
                        break;

                        case 'Legolcsóbb elől':
                            $query->orderBy( 'e.price', 'ASC' );
                        break;

                        case 'Legdrágább elől':
                            $query->orderBy( 'e.price', 'DESC' );
                        break;
                    }
                }
                elseif ( $key === "oldal" )
                {
                    $estateCount = $this->em->getRepository( Estate::class )->createQueryBuilder( 'e' )->select( 'count(e.id)' )->getQuery()->getSingleScalarResult();

                    if ( $value > 0 && $estateCount >= ((($value-1) * 20) + 1) )
                    {
                        $query->setFirstResult( (($value-1)*10) );
                        $query->setMaxResults( 20 );
                    }
                }
                else
                {
                    array_push( $queryArray, $query->expr()->eq( "e.{$this->keyMapping[$key]}", "?{$paramCounter}" ) );
                    $queryParameters[$paramCounter] = $value;
                    $paramCounter++;
                }
            }
            else
            {
                if ( in_array( $key, $this->betweenElements ) )
                {
                    array_push( $queryArray, $query->expr()->between( "e.{$this->keyMapping[$key]}", "?{$paramCounter}", "?" . ($paramCounter+1) ) );
                    $queryParameters[$paramCounter] = intval( $value["minimum"] );
                    $queryParameters[$paramCounter+1] = intval( $value["maximum"] );
                    $paramCounter += 2;
                }
                elseif ( in_array( $key, $this->keyBooleans ) )
                {
                    $selfArray = [];
                    foreach ( $value as $valueItem )
                    {
                        array_push( $selfArray, $query->expr()->eq("e.{$this->keyMapping[$key]}", "?{$paramCounter}") );
                        $queryParameters[$paramCounter] = $valueItem;
                        $paramCounter++;
                    }

                    array_push( $queryArray, $query->expr()->orX()->addMultiple($selfArray) );
                }
                else
                {
                    $selfArray = [];
                    foreach ( $value as $valueItem )
                    {
                        array_push( $selfArray, $query->expr()->eq("e.{$this->keyMapping[$key]}", "?{$paramCounter}") );
                        $queryParameters[$paramCounter] = $valueItem;
                        $paramCounter++;
                    }

                    array_push( $queryArray, $query->expr()->orX()->addMultiple($selfArray) );
                }
            }
        }
        
        if ( count( $queryArray ) !== 0 )
        {
            $query->where( $query->expr()->andX()->addMultiple( $queryArray ))
                  ->setParameters( $queryParameters );
        }

        return $result = $query->getQuery()->getResult();
    }

    public function getResults( array $filter )
    {
        return $this->createSelect( $filter );
    }

    public function getJsonResults( array $filter )
    {

    }

    
    private function convertToBoolean( string $string )
    {
        return $string === "Igen" ? true : false;
    }
}