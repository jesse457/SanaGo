<?php

namespace App\Enums;

enum BeneficiaryType: string
{
    case Titulaire = 'titulaire';
    case Conjoint = 'conjoint';
    case Enfant = 'enfant';
}
