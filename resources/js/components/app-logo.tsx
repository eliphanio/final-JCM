import { usePage } from '@inertiajs/react';

import AppLogoIcon from '@/components/app-logo-icon';
import { HousePlugIcon } from 'lucide-react';

export default function AppLogo() {

    return (
        <>
            <div className="bg-sidebar-primary text-sidebar-primary-foreground flex aspect-square size-8 items-center justify-center rounded-md">
                <HousePlugIcon className="size-5 fill-current text-white dark:text-black" />
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="mb-0.5 truncate leading-tight font-semibold">
                    Foyer charge manager
                </span>
            </div>
        </>
    );
}
