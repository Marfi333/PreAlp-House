<?php   
    namespace App\Middleware;

    use Doctrine\ORM\EntityManagerInterface;
    use App\Entity\Mapping;

class SearchQuerySorter
{
    private $entityManager;
    private $queryString;
    private $filterElements = [
        "szerkezet",
        "futes",
        "erkely",
        "lift",
        "allapot",
        "emelet",
        "kilatas",
        "akadalymentesitett",
        "rendezes"
    ];

    public function __construct( EntityManagerInterface $em, $string )
    {
        $this->entityManager = $em;
        $this->queryString = $string;
    }


    public function querySorter()
    {
        $keyset = $this->keySet();
        $sorted = explode( '+', $this->queryString );
        $queries = [];

        foreach( $sorted as $item )
        {
            if ( count( explode( '=', $item )) !== 2 ) continue;
            $key = explode( '=', $item )[0];
            $value = explode( '=', $item )[1];

            if ( !array_key_exists( $key, $keyset ) ) continue;

            if ( count( explode( ';', $value )) === 1 )
            {
                if ( in_array( '*', $keyset[$key] ) )
                {
                    switch( $key )
                    {
                        case 'meret':
                            if ( count( explode( '-', $value)) === 2 )
                            {
                                $queries[$key] = [
                                    "minimum" => explode( '-', $value )[0],
                                    "maximum" => explode( '-', $value )[1]
                                ];
                            }
                        break;

                        case 'ar':
                            if ( strpos( $value, 'e' ) === false && strpos( $value, 'm' ) === false ) break;
                            str_replace( 'e', '', $value );
                            str_replace( 'm', '', $value );

                            if ( count( explode( '-', $value )) === 2 )
                            {
                                if ( strpos( $value, 'e' ) !== false )
                                {
                                    $queries[$key] = [
                                        "minimum" => intval( explode( '-', $value )[0]) * 1000,
                                        "maximum" => intval( explode( '-', $value )[1]) * 1000,
                                    ];
                                }
                                else
                                {
                                    $queries[$key] = [
                                        "minimum" => intval( explode( '-', $value )[0]) * 1000000,
                                        "maximum" => intval( explode( '-', $value )[1]) * 1000000,
                                    ];
                                }
                            }
                        break;

                        default:
                            $queries[$key] = $value;
                        break;
                    }
                }
                elseif ( in_array( $value, $keyset[$key] ) )
                {
                    $queries[$key] = $value;
                }
            }
            else
            {
                $valueArray = [];

                foreach ( explode( ";", $value ) as $val )
                {
                    if ( $key === "emelet" )
                    {
                        if ( in_array( $val, $keyset[$key] ) )
                        {
                            array_push( $valueArray, $val );
                        }
                        elseif ( strpos( $val, ". emelet" ) !== false )
                        {
                            array_push( $valueArray, $val );
                        }
                    }
                    else
                    {
                        if ( in_array( $val, $keyset[$key] ) )
                        {
                            array_push( $valueArray, $val );
                        }
                    }
                }

                $queries[$key] = $valueArray;
            }
            //array_push($queries, $item);
        }

        return $queries;
    }

    public function twigSorter( $array )
    {
        $returnArray = [];

        foreach ( $array as $key => $value )
        {
            if ( !in_array( $key, $this->filterElements ) ) continue;

            if ( is_array( $value ) )
            {
                $string = "";

                for ( $i = 0; $i < count( $value ); $i++ )
                {
                    $string = $string . $value[$i];

                    if ( $i !== count( $value )-1 )
                    {
                        $string = $string . ";";
                    }
                }

                $returnArray[$key] = $string;
            }
            else
            {
                $returnArray[$key] = $value;
            }
        }

        return $returnArray;
    }


    private function keySet()
    {
        $dbQuery = $this->entityManager->getRepository( Mapping::class )
                      ->findAll();

        $returnArray = [];

        foreach( $dbQuery as $query )
        {
            $returnArray[$query->getEnum()] = $query->getValue();
        }

        return $returnArray;
    }
}