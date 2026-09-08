import { Head } from "@inertiajs/react"

export default function Facture() {
    return (
        <>
            <Head title="Facture" />
            <h1>Facture</h1>
        </>
    );
}

Facture.layout = (props: { currentFoyer?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Factures',
            href: props.currentFoyer ? `/${props.currentFoyer.slug}/factures` : '/',
        },
    ],
});