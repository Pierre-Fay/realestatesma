<?php

namespace App\Enums;

enum CategoryGroupType: string
{
    case PROPERTY_AREA = 'property_area';
    case PROPERTY_FEATURE = 'property_feature';
    case PROPERTY_LABEL = 'property_label';
    case PROPERTY_STATUS = 'property_status';
    case PROPERTY_TYPE = 'property_type';
}
