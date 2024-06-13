<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class ExempleTest extends TestCase
{
    public function testSomething(): void
    { // fonction [assertTrue()] nous permet de savoir si le parametre qu'on lui donne en parametre est a [true]
        // exemple 
        $param = false; 
        // et donne la variable param à la fontion [assertTrue()] en paramettre pour tester si elle envoi vraiment true 
        $this->assertTrue($param);  
        // se rendre dans le terminal pour la suite ==> saisir:  php bin/phpunit 
    }
}
