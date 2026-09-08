import { Head } from '@inertiajs/react';
import { useState } from 'react';
import PendingInvitationsModal from '@/components/pending-invitations-modal';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { dashboard } from '@/routes';
import Dashboard from './dashboard';

export default function Appareil() {
    

    return (
        <>
            <h1>Appareil</h1>
        </>
    );
}

Appareil.layout = (props: { currentFoyer?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Appareils',
            href: props.currentFoyer
                ? `/${props.currentFoyer.slug}/appareils`
                : '/',
        },
    ],
});


