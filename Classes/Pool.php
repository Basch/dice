<?php

class Pool {

    /** @var Dice[] $_dices */
    private $_dices ;
    private $_sideStat ;
    private $_renderer ;

    private $_debug;


    public function getDebug(): bool {
        return $this->_debug;
    }

    public function setDebug( bool $debug ): self {
        $this->_debug = $debug;
        return $this;
    }

    public function __construct( $data = null ) {
        $this->_renderer = new PoolRenderer( $this );
        $this->initCreate( $data );
    }


    private function initCreate( $data ) {

        switch( gettype( $data ) ){
            case 'integer' :
                $this->createFromInteger( $data );
                break;

            case 'array' :
                $this->createFromArray( $data );
                break;

            case 'object' :
                $this->createFromObject( $data );
                break;

            case 'string' :
                $this->createFromString( $data );
                break;

            default :

        }
    }

    private function createFromInteger( int $int ) {
        if ( $int > 0 ) {
            $this->addDice( Dice::Create( $int ) );
        }
    }

    private function createFromArray( array $array ) {
        foreach( $array as $data ) {
            $this->initCreate( $data );
        }
    }

    private function createFromObject( $object ) {
        switch ( get_class( $object ) ) {
            case Dice::class :
                $this->addDice( $object );
                break;
            case Pool::class :
                $this->addPool( $object );
                break;
        }
    }

    private function createFromString( string $string ) {
        $this->createFromInteger( intval( $string ) );
    }

    public static function Create ( $dice = null ) {
        return new Pool( $dice );
    }

    public function addNewDice( $size ) {
        $this->addDice( Dice::Create( $size ) );
        return $this;
    }

    public function addDice( Dice $dice ) {
        $this->_dices[] = $dice;
        $this->mergeStat( $dice->getSideStat() );

        return $this;
    }

    public function addPool( Pool $pool ) {
        $this->mergeDices( $pool->getDices() );
        $this->mergeStat( $pool->getSideStat() );
        return $this;
    }

    private function mergeStat( $dice_b ) {

        $dice_a = $this->_sideStat ?? null;

        if( $dice_a ) {
            $min = firstk($dice_a) + firstk($dice_b);
            $max = endk($dice_a) + endk($dice_b) - $min + 1;

            $dice_new = array_fill($min, $max, 0);

            foreach ( $dice_a as $key_a => $val_a ) {
                foreach ( $dice_b as $key_b => $val_b ) {
                    $dice_new[$key_a + $key_b] += $val_a * $val_b;
                }
            }
            $this->_sideStat = $dice_new;
        }
        else {
            $this->_sideStat = $dice_b;
        }
    }

    private function mergeDices( $dice_b ) {
        if( isset( $this->_dices ) ) {
            $this->_dices = array_merge( $this->_dices, $dice_b );
        }
        else {
            $this->_dices = $dice_b;
        }
    }

    private function nbBytes( $binary ) {
        return substr_count( $binary,'1' );
    }

    private static function CharToBool( string $char ){
        return $char == '1';
    }

    public function getName(): string {
        $a_sizes = [];
        $a_names = [];

        foreach( $this->_dices ?? [] as $dice ){
            $a_sizes[$dice->getSize()] = ( $a_sizes[$dice->getSize()] ?? 0 ) + 1;
        }
        foreach ($a_sizes as $s => $q) {
            $a_names[] = $q.'d'.$s;
        }
        return implode(' + ', $a_names );
    }

    /** @return Dice[] */
    public function getDices() {
        return $this->_dices;
    }

    public function countDices(): int {
        return count( $this->_dices );
    }

    public function getBiggestDice(): int {
        $max = 0;
        foreach ( $this->_dices as $dice ) {
            $size = $dice->getSize();
            if( $size > $max ) { $max = $size; }
        }
        return $max;
    }

    public function getSmallesttDice(): int {
        $min = null;
        foreach ( $this->_dices as $dice ) {
            $size = $dice->getSize();
            if( $size < $min || $min == null) { $min = $size; }
        }
        return $min;
    }

    /** @return array */
    public function getSideStat() {
        return $this->_sideStat;
    }

    public function getMaxStat() {
        return endk( $this->_sideStat );
    }

    public function getMinStat() {
        return firstk( $this->_sideStat );
    }


    private function renderer(): PoolRenderer {
        return $this->_renderer;
    }


    public function sayMyName(): self {
        $this->renderer()->name();
        return $this;
    }

    public function printBaseStat(): self {
        $this->renderer()->base();
        return $this;
    }

    public function calcChancesToBeat( int $goal, bool $reverse = false, int $strip = 0 ): array
    {

        $dices = [];
        $max_dices = [];
        $max = 1;

        foreach ($this->getDices() as $dice) {
            $dices[] = 1;
            $max_dices[] = $dice->getSize();
            $max *= $dice->getSize();
        }

        if ($goal <= 0) {
            echo "Goal cannot be negative";
            return null;
        }

        if ($strip >= $this->countDices()) {
            echo "You strip too much dices";
            return null;
        }

        $stat = [];
        for ($i = 0; $i < $max; $i++) {
            //$stat[$i]['dices'] = $dices;
            //$stat[$i]['sum'] = array_sum($dices);
            if ($strip == 0) {
                $sum = array_sum($dices);
            } else {
                $tmp = $dices;
                sort($tmp);
                //echo implode(' ', $tmp )."\n";
                for ($s = 0; $s < $strip; $s++) {
                    array_shift($tmp);
                }
                //echo implode(' ', $tmp )."\n\n";
                $sum = array_sum($tmp);
            }

            //echo implode(' ',$dices) . " : " . array_sum($dices) . "\n";

            $stat[$sum] = ($stat[$sum] ?? 0) + 1;

            $dices[0]++;

            for ($j = 0; $j < count($dices); $j++) {
                if ($dices[$j] > $max_dices[$j]) {
                    $dices[$j] = 1;
                    if (isset($dices[$j + 1])) {
                        $dices[$j + 1]++;
                    }
                }
            }
        }


        $succes = 0;
        foreach ($stat as $key => $val) {
            if ($reverse) {
                if ($key <= $goal) {
                    $succes += $val;
                }
            } else {
                if ($key >= $goal) {
                    $succes += $val;
                }
            }

        }
        $chance = $succes / $max;

        return [
            'chance' => $chance,
            'succes' => $succes,
            'max' => $max,
            'max_dices' => $max_dices,
            ];
    }

    public function showChancesToBeat( int $goal, bool $reverse = false, int $strip = 0 ): self {

        $stat = $this->calcChancesToBeat( $goal, $reverse, $strip );

        $chance = $stat['chance'];
        $succes = $stat['succes'];
        $max = $stat['max'];
        $max_dices = $stat['max_dices'];


        $tiny = true ;

        if( $tiny ) {
            echo $goal;
            if( $reverse ){
                echo "-";
            } else {
                echo '+';
            }
            echo " -> ".implode( ' ', $max_dices ) . " : " . round($chance * 100, 2) . "%\n\n";
        }
        else {
            //print_t( $stat );
            echo "\n";
            echo "Chance to beat " . $goal;
            if ($reverse) {
                echo "-";
            } else {
                echo "+";
            }

            if ($strip > 0) {
                $nb_dices = $this->countDices() - $strip;
                echo " keeping only " . $nb_dices . " dice";
                if ($nb_dices > 1) echo "s";
            }

            echo "\n";

            echo "$succes / $max \n";
            echo "Total : " . round($chance * 100, 2) . "%\n\n";
        }


        return $this;
    }

    public function showNumberOfSuccess( int $goal, bool $reverse = false, int $strip = 0 ): self {
        if( $goal <= 0 || $goal >= $this->getMaxStat() ) {
            echo "Goal is not in pool range";
            return $this;
        }

        if( $strip >= $this->countDices() ) {
            echo "You strip too much dices";
            return $this;
        }

        $tab = array_fill(0, 2**$this->countDices(), 0);
        $max = count( str_split( decbin(2**$this->countDices() - 1 ) ) );

        $chance = array_fill(0,$max+1,0);

        foreach( $tab as $i => $v ) {

            $bin = str_pad( decbin($i), $max, '0', STR_PAD_LEFT );
            $nb_bytes = $this->nbBytes( $bin );



            $chars = str_split($bin);
            $chars += array_fill(0,$max,0 );

            $tmp = [];
            foreach ( $chars as $j => $char ) {
                $tmp[] = $this->getDices()[$j]->getChanceToBeat( $goal, !self::CharToBool($char) xor $reverse );
            }

            $a = 1;
            foreach( $tmp as $val ) {
                $a *= $val;
            }

            $nb_success = $nb_bytes;
            $nb_fail = $this->countDices() - $nb_bytes;


            $rest = $nb_fail - $strip;

            if( $rest < 0 ){
                $nb_success += $rest;
            }
            if( $nb_success < 0 ) $nb_success = 0;


            $chance[ $nb_success ] += $a;
            $tab[$i] = $bin;

            if( $this->_debug ) {
                echo $bin . "\n";
                echo implode(' ', $tmp) . "\n";
                echo "nb fail : $nb_fail\n";
                echo "rest : $rest\n";
                echo "nb_bytes : $nb_bytes\n";
                echo "nb_success : $nb_success\n";
                echo "\n";
            }

        }
        if( $reverse ) {
            echo "Chance to pass under ".$goal;
        } else {
            echo "Chance to beat ".$goal;
        }
        if( $strip > 0 ) {
            $nb_dices = $this->countDices() - $strip;
            echo " keeping only " . $nb_dices . " dice";
            if( $nb_dices > 1 ) echo "s";
        }
        print_t( $chance, true );
        array_shift($chance );
        echo "Total : ". round( array_sum( $chance )*100, 2 ). "%\n\n";

        return $this;
    }

}