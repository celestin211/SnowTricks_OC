<?php

namespace App\EnumTypes;

class EnumCiviliteType extends EnumType
{
    const MONSIEUR = 'm.';
    const MADAME = 'mme';

//    protected $name = 'enum_civilite';
//    protected $values = ['m.', 'mme'];

    const VALUES = [
        self::MONSIEUR => 'M.',
        self::MADAME => 'Mme',
    ];
}
