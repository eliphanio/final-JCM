import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Head, router, usePage } from '@inertiajs/react';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogHeader,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Item } from '@radix-ui/react-dropdown-menu';
import { Button } from '@/components/ui/button';
import { Delete, Plus, TrashIcon } from 'lucide-react';
import CreateAppareilModal from '@/components/create-appareil-modal';
import Heading from '@/components/heading';
import destroy from '@/routes/destroy';

interface Device {
    id: number;
    name: string;
    user: {
        name: string;
    };
    foyer_id: number;
    usage: number;
    power_watt: number;
}

interface AppareilProps {
    devices: Device[];
}
export default function Appareil({ devices }: AppareilProps) {

    const page = usePage<{
        currentFoyer: {
            slug: string;
        };
    }>();

    return (
        <>
            <Head title="Appareil" />

            <div className=" gap-4 overflow-x-auto rounded-xl p-5 px-8">
                <div className="flex items-center justify-between flex-wrap gap-2 m-3 ">
                    <Heading
                        variant="small"
                        title="Appareil"
                        description="Gérer vos appareils et leurs informations"
                    />
                    <CreateAppareilModal>
                        <Button >
                            <Plus /> Ajouter un appareil
                        </Button>
                    </CreateAppareilModal>
                </div>

                <div className="border-sidebar-border/70 dark:border-sidebar-border relative flex-1 overflow-hidden rounded-xl border md:min-h-min p-5">
                    {devices.length !== 0 ? (
                        <>
                            <h2>Appareil enregistrer</h2>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nom</TableHead>
                                        <TableHead>Proprietaire</TableHead>
                                        <TableHead>Heure d'utilisation</TableHead>
                                        <TableHead>Puissance</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {
                                        devices.map((device) => (
                                            <TableRow key={device.id}>
                                                <TableCell>{device.name}</TableCell>
                                                <TableCell>{device.user.name}</TableCell>
                                                <TableCell>{device.usage}</TableCell>
                                                <TableCell>{device.power_watt}</TableCell>
                                                <TableCell>
                                                    <Dialog>
                                                        <DialogTrigger asChild>
                                                            <Button variant="destructive" className="h-8 w-8 p-0">
                                                                <TrashIcon className="h-4 w-4" />
                                                            </Button>
                                                        </DialogTrigger>
                                                        <DialogContent className="sm:max-w-106.25">
                                                            <DialogHeader>
                                                                <h3 className="text-lg font-semibold">
                                                                    Supprimer l'appareil
                                                                </h3>
                                                                <p className="text-sm text-muted-foreground">
                                                                    Êtes-vous sûr de vouloir supprimer cet appareil ? Cette action ne peut pas être annulée.
                                                                </p>
                                                            </DialogHeader>
                                                            <div className="flex justify-end space-x-2">
                                                                <DialogClose asChild>
                                                                    <Button variant="outline">Annuler</Button>
                                                                </DialogClose>
                                                                <Button variant="destructive" onClick={() => {
                                                                    router.delete(destroy.appareil(
                                                                        { current_foyer: page.props.currentFoyer.slug, appareil: device.id }).url)
                                                                }}>Supprimer
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
                        <h2>Aucun appareil enregister</h2>
                    )
                    }
                </div>
            </div >

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


