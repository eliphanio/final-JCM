export default function Absence() {
    return (
        <>
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