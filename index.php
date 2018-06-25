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
        echo $goal . "+";
        echo " -> " . implode(' ', $stat['max_dices']) . " : " . round($stat['chance'] * 100, 2) . "% \n";
    }

    function create_stats( &$stats, $pool, $goal ) {
        $tmp = Pool::create([ $pool['i'], $pool['j'], $pool['h'] ])->calcChancesToBeat( $goal , false, 1);
        $stats[ str_pad( number_format (round($tmp['chance']*100, 2 ), 2) , 6, "0", STR_PAD_LEFT ) . implode('-',$pool) ] = $tmp;
    }

    $goal = 11;

   /* Pool::Create( [4,4] )->sayMyName()->showNumberOfSuccess($goal);
    Pool::Create( [4,6] )->sayMyName()->showNumberOfSuccess($goal);

    $p = Pool::Create( [4, '6', ['4', 12]] )->sayMyName()->showNumberOfSuccess($goal);*/
   // Pool::create([ 8,8,6 ])->sayMyName()->printBaseStat()->showChancesToBeat( 11 , false, 1);

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


    echo '<div style="float:left; margin-right: 10px;">';
    ksort($pools);
    $stats = [];
    foreach( $pools as $pool ) {
        create_stats($stats, $pool, $goal );
    }
    ksort( $stats );
    foreach($stats as $stat) {
        print_line($goal, $stats);
    }
    echo"</div>";

    $goal = 12;

    echo '<div style="float:left">';
    ksort($pools);
    $stats = [];
    foreach( $pools as $pool ) {
        create_stats($stats, $pool, $goal );
    }
    ksort( $stats );
    foreach($stats as $stat) {
        print_line($goal, $stats);
    }
    echo"</div>";

    $goal = 13;

    echo '<div style="float:left">';
    ksort($pools);
    $stats = [];
    foreach( $pools as $pool ) {
        create_stats($stats, $pool, $goal );
    }
    ksort( $stats );
    foreach($stats as $stat) {
        print_line($goal, $stats);
    }
    echo"</div>";




  /* $p = Pool::Create([4,12,6]);
    $p->sayMyName();
   echo "max : " . $p->getBiggestDice() . "\n min : ". $p->getSmallesttDice() . "\n";*/




    ?></pre>