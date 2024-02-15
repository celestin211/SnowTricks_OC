<?php

namespace App\EnumTypes;

class EnumVilleType extends EnumType
{

    const  VILLETANEUSE = 'VILLETANEUSE';
    const  ALFORTVILLE = 'ALFORTVILLE';
    const  MAISONALFORT = 'MAISONALFORT';
    const  DRANCY = 'DRANCY';
    const  EPINAY = 'EPINAY/SEINE';
    const  SAINTDENIS = 'SAINT-DENIS';
    const  BOBIGNY = 'BOBIGNY';
    const  AULNAY = ' AULNAY/BOI';


    const VALUES = [
        self::VILLETANEUSE => 'VILLETANEUSE',
        self::ALFORTVILLE => 'ALFORTVILLE',
        self::MAISONALFORT => 'MAISONALFORT',
        self::DRANCY => 'DRANCY',
        self::SAINTDENIS => 'SAINTDENIS',
        self::BOBIGNY => 'BOBIGNY',
        self::AULNAY => 'AULNAY',
    ];
}
