<?php

namespace App\Data;

readonly class FoyerPermissions
{
    public function __construct(
        public bool $canUpdateFoyer,
        public bool $canDeleteFoyer,
        public bool $canAddMember,
        public bool $canUpdateMember,
        public bool $canRemoveMember,
        public bool $canCreateInvitation,
        public bool $canCancelInvitation,
        public bool $canAddFacture,
        public bool $canPaid,
        public bool $canAddAppareil,
        public bool $canRemoveAppareil,
        public bool $canAcceptAbsence,
    ) {
        //
    }
}
