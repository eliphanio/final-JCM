<?php

namespace App\Enums;

enum FoyerPermission: string
{
    case UpdateFoyer = 'foyer:update';
    case DeleteFoyer = 'foyer:delete';

    case AddMember = 'member:add';
    case UpdateMember = 'member:update';
    case RemoveMember = 'member:remove';

    case CreateInvitation = 'invitation:create';
    case CancelInvitation = 'invitation:cancel';
}
