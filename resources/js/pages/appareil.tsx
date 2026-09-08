import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Head } from '@inertiajs/react';
import { Dialog, DialogContent, DialogTrigger } from '@radix-ui/react-dialog';
import { Item } from '@radix-ui/react-dropdown-menu';
import { Button } from '@/components/ui/button';
import { Delete, Plus, TrashIcon } from 'lucide-react';
import { DialogHeader } from '@/components/ui/dialog';
import CreateAppareilModal from '@/components/create-appareil-modal';
import Heading from '@/components/heading';

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
                                                    <Button className='bg-red-500' size="sm">
                                                        <TrashIcon  />
                                                    </Button>
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
            </div>

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


