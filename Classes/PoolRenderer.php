<?php


class PoolRenderer
{
    private $_pool;

    public function __construct( Pool $pool ){
        $this->_pool = $pool;
    }

    private function pool() {
        return $this->_pool;
    }

    public function name() {
        echo "\n";
        echo 'Pool : '.$this->pool()->getName()."\n\n";
    }

    public function base() {
        echo "\n";
        print_t( $this->pool()->getSideStat() );
        echo "\n";
        echo "Total : ".array_sum( $this->pool()->getSideStat() )."\n\n";
    }

}