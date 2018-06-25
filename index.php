<style>
    td {
        padding : 0 3px;
        border: 1px solid lightcyan;
    }
</style><pre><?php

    include "generic.php";
    include "AutoLoader.php";

    echo "stat lancés de dés\n";




    function print_line ($goal, $stat) {

        //$dices = ;
        $pad_dice = [];
        foreach ( $stat['max_dices'] as $dice ){
            $pad_dice[] = str_pad( $dice, 2, ' ', STR_PAD_LEFT );
        }

        $pad_dice = implode(' ', $pad_dice);

        echo $goal . "+";
        echo " -> " . $pad_dice . " : " . round($stat['chance'] * 100, 2) . "% \n";
    }

    function create_stats( &$stats, $pool, $goal ) {
        $tmp = Pool::create([ $pool['i'], $pool['j'], $pool['h'] ])->calcChancesToBeat( $goal , false, 1);
        $stats[array_sum($pool)][ str_pad( number_format (round($tmp['chance']*100, 2 ), 2) , 6, "0", STR_PAD_LEFT ) . implode('-',$pool) ] = $tmp;
    }

    function stats( $pools , $goal ) {
        echo '<div style="float:left; margin-right: 20px;">';
        $stats = [];
        foreach( $pools as $pool ) {
            create_stats($stats, $pool, $goal );
        }
        ksort( $stats );
        foreach($stats as $stat) {
            //ksort( $stat );
            foreach($stat as $data) {
                print_line($goal, $data);
            }
            echo"\n";
        }
        echo"</div>";
    }

    $pools = [];
    for( $i = 4; $i < 14; $i = $i + 2 )
    for( $j = 4; $j < 14; $j = $j + 2 )
    for( $h = 4; $h < 14; $h = $h + 2 ) {

        $tmp_keys =[];
        $tmp_keys[] = $i;
        $tmp_keys[] = $j;
        $tmp_keys[] = $h;

        sort($tmp_keys);

        $key = $tmp_keys[0]*10000 + $tmp_keys[1]*100 + $tmp_keys[2];

        $pools[ $key ] = [
            'i' => $i,
            'j' => $j,
            'h' => $h,
            ];
    }
    ksort($pools);

    stats( $pools , 11 );
    stats( $pools , 12 );
    stats( $pools , 13 );
    stats( $pools , 14 );
    stats( $pools , 15 );
    stats( $pools , 16 );




?></pre>