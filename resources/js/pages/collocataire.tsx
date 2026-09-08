import { Head } from "@inertiajs/react";

export default function Collocataire() {
    return (
        <>
            <Head title="Collocataire" />
            <h1>Collocataire</h1>
        </>
    );
}

Collocataire.layout = (props: { currentFoyer?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Collocataires',
            href: props.currentFoyer
                ? `/${props.currentFoyer.slug}/collocataires`
                : '/',
        },
    ],
});