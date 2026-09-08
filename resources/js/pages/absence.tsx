import { Head } from "@inertiajs/react";

export default function Absence() {
    return (
        <>
            <Head title="Absence" />
            <h1>Absence</h1>
        </>
    );
}

Absence.layout = (props: { currentFoyer?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Absences',
            href: props.currentFoyer ? `/${props.currentFoyer.slug}/absences` : '/',
        },
    ],
});