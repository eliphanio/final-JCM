// import { router, usePage } from '@inertiajs/react';
// import { useState } from 'react';
// import AbsenceController from '@/actions/App/Http/Controllers/AbsenceController';
// import { Button } from '@/components/ui/button';
// import {
//     Dialog,
//     DialogContent,
//     DialogDescription,
//     DialogHeader,
//     DialogTitle,
// } from '@/components/ui/dialog';
// import type { DashboardAbsenceRequest } from '@/types';

// type Props = {
//     absences: DashboardAbsenceRequest[];
//     open: boolean;
//     onOpenChange: (open: boolean) => void;
// };

// export default function PendingAbsenceModal({
//     absences,
//     open,
//     onOpenChange,
// }: Props) {

//     const page = usePage<{
//             currentFoyer: {
//                 slug: string;
//             };
//         }>();
//     const [processingCode, setProcessingCode] = useState<string | null>(null);

//     const acceptAbsence = (absence: DashboardAbsenceRequest) => {

//         const acceptAbsence = (absence: DashboardAbsenceRequest) => {
//             router.visit(AbsenceController.AcceptAbsence(absence.foyer.id, absence.id), {
//                 onStart: () => setProcessingCode(absence.id),
//                 onFinish: () => setProcessingCode(null),
//                 onSuccess: () => {
//                     if (absences.length === 1) {
//                         onOpenChange(false);
//                     }
//                 },
//             });
//         };

//     };

//     // const declineabsence = (absence: DashboardAbsenceRequest) => {
//     //     router.visit(FoyerabsenceController.decline(absence), {
//     //         onStart: () => setProcessingCode(absence.code),
//     //         onFinish: () => setProcessingCode(null),
//     //         onSuccess: () => {
//     //             if (absences.length === 1) {
//     //                 onOpenChange(false);
//     //             }
//     //         },
//     //     });
//     // };

//     return (
//         <Dialog open={open} onOpenChange={onOpenChange}>
//             <DialogContent data-test="pending-absences-modal">
//                 <DialogHeader>
//                     <DialogTitle>Pending foyer absences</DialogTitle>
//                     <DialogDescription>
//                         Accept or decline the foyers you have been invited to
//                         join.
//                     </DialogDescription>
//                 </DialogHeader>

//                 <div className="grid gap-4">
//                     {absences.map((absence) => (
//                         <div
//                             key={absence.id}
//                             data-test="pending-absence-row"
//                             className="rounded-lg border p-4"
//                         >
//                             <div className="space-y-1">
//                                 <p className="font-medium">
//                                     {absence.user.name}
//                                 </p>
//                                 <p className="text-muted-foreground text-sm">
//                                     Debut: {absence.start_date}
//                                 </p>
//                                 <p className="text-muted-foreground text-sm">
//                                     fin :{absence.end_date}
//                                 </p>
//                             </div>

//                             <div className="mt-4 flex justify-end gap-2">
//                                 {/* <Button
//                                     variant="secondary"
//                                     data-test="pending-absence-decline"
//                                     disabled={
//                                         processingCode === absence.code
//                                     }
//                                     onClick={() =>
//                                         declineabsence(absence)
//                                     }
//                                 >
//                                     Decline
//                                 </Button> */}

//                                 <Button
//                                     data-test="pending-absence-accept"
//                                     disabled={
//                                         processingCode === absence.id
//                                     }
//                                     onClick={() => acceptAbsence(absence)}
//                                 >
//                                     Accept
//                                 </Button>
//                             </div>
//                         </div>
//                     ))}
//                 </div>
//             </DialogContent>
//         </Dialog>
//     );
// }
