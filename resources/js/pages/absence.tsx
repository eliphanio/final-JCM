import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Head, router, usePage } from '@inertiajs/react';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogHeader,
    DialogTrigger,
} from '@/components/ui/dialog'; import { Item } from '@radix-ui/react-dropdown-menu';
import { Button } from '@/components/ui/button';
import { CheckIcon, Delete, Plus, SendIcon, TrashIcon } from 'lucide-react';
// import CreateAbsenceModal from '@/components/create-Absence-modal';
import Heading from '@/components/heading';
import CreateAbsenceModal from '@/components/create-absence-modal';
import accept, { absence } from '@/routes/accept';

interface Absence {
    id: number;
    user: {
        name: string;
    };
    start_day: string;
    end_day: string;
    motif: string;
}

interface AbsenceProps {
    absenceRequests: Absence[];
    absences: Absence[]
}
export default function Absence({ absenceRequests, absences }: AbsenceProps) {

    const page = usePage<{
        currentFoyer: {
            slug: string;
        };
    }>();

    return (
        <>
            <Head title="Absence" />

            <div className=" gap-4 overflow-x-auto rounded-xl p-5 px-8">
                <div className="flex items-center justify-between flex-wrap gap-2 m-3 ">
                    <Heading
                        variant="small"
                        title="Absence"
                        description="Gérez les absences de votre foyer"
                    />
                    <CreateAbsenceModal>
                        <Button>
                            <SendIcon /> Demande d'absence
                        </Button>
                    </CreateAbsenceModal>
                </div>

                <div className="border-sidebar-border/70 dark:border-sidebar-border relative flex-1 overflow-hidden rounded-xl border md:min-h-min p-5">

                    {
                        absenceRequests.length !== 0 ? (
                            <>
                                <h2>Absence en attende de confirmation</h2>
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Nom</TableHead>
                                            <TableHead>Départ</TableHead>
                                            <TableHead>Retour</TableHead>
                                            <TableHead>Motif</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>

                                        {
                                            absenceRequests.map((absenceRequest) => (
                                                <TableRow key={absenceRequest.id}>
                                                    <TableCell>{absenceRequest.user.name}</TableCell>
                                                    <TableCell>{new Date(absenceRequest.start_day).toLocaleDateString(
                                                        'fr-FR',
                                                        {
                                                            day: 'numeric',
                                                            month: 'long',
                                                            year: 'numeric',
                                                        }
                                                    )}</TableCell>
                                                    <TableCell>{new Date(absenceRequest.end_day).toLocaleDateString(
                                                        'fr-FR',
                                                        {
                                                            day: 'numeric',
                                                            month: 'long',
                                                            year: 'numeric',
                                                        }
                                                    )}</TableCell>
                                                    <TableCell>Pas encore operationnel</TableCell>
                                                    <TableCell>
                                                        <Dialog>
                                                            <DialogTrigger asChild>
                                                                <Button className="bg-green-500 hover:bg-green-400 h-8 w-8 p-0">
                                                                    <CheckIcon className="h-4 w-4" />
                                                                </Button>
                                                            </DialogTrigger>
                                                            <DialogContent className="sm:max-w-106.25">
                                                                <DialogHeader>
                                                                    <h3 className="text-lg font-semibold">
                                                                        Accepté la demande
                                                                    </h3>
                                                                    <p className="text-sm text-muted-foreground">
                                                                        Êtes-vous sûr de vouloir accepté cette demande d'absence ? Cette action ne peut pas être annulée.
                                                                    </p>
                                                                </DialogHeader>
                                                                <div className="flex justify-end space-x-2">
                                                                    <DialogClose asChild>
                                                                        <Button variant="outline">Annuler</Button>
                                                                    </DialogClose>
                                                                    <Button className='bg-green-500 hover:bg-green-400' onClick={() => {
                                                                        router.post(accept.absence(
                                                                            { current_foyer: page.props.currentFoyer.slug, absenceRequest: absenceRequest.id }).url)
                                                                    }} >Accepté
                                                                    </Button>
                                                                </div>
                                                            </DialogContent>
                                                        </Dialog>

                                                    </TableCell>
                                                </TableRow>
                                            ))
                                        }

                                    </TableBody>
                                </Table>

                            </>
                        ) : (
                            <h2>Aucune demande d'absence</h2>
                        )
                    }

                </div>
            </div >
            <div className=" gap-4 overflow-x-auto rounded-xl p-5 px-8">
                <div className="border-sidebar-border/70 dark:border-sidebar-border relative flex-1 overflow-hidden rounded-xl border md:min-h-min p-5">

                    {
                        absences.length !== 0 ? (
                            <>
                                <h2>Absence accepé</h2>
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Nom</TableHead>
                                            <TableHead>Départ</TableHead>
                                            <TableHead>Retour</TableHead>
                                            <TableHead>Motif</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {
                                            absences.map((absence) => [
                                                <TableRow>
                                                    <TableCell>{absence.user.name}</TableCell>
                                                    <TableCell>{new Date(absence.end_day).toLocaleDateString(
                                                        'fr-FR',
                                                        {
                                                            day: 'numeric',
                                                            month: 'long',
                                                            year: 'numeric',
                                                        }
                                                    )}</TableCell>
                                                    <TableCell>{new Date(absence.end_day).toLocaleDateString(
                                                        'fr-FR',
                                                        {
                                                            day: 'numeric',
                                                            month: 'long',
                                                            year: 'numeric',
                                                        }
                                                    )}</TableCell>
                                                    <TableCell>Pas encore operationnel</TableCell>
                                                </TableRow>
                                            ])
                                        }
                                    </TableBody>
                                </Table>

                            </>
                        ) : (
                            <h2>Aucune demande accepté</h2>
                        )
                    }

                </div>
            </div >

        </>
    );
}

Absence.layout = (props: { currentFoyer?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Absences',
            href: props.currentFoyer
                ? `/${props.currentFoyer.slug}/Absences`
                : '/',
        },
    ],
});


