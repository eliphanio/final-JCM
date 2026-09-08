import type { Auth } from '@/types/auth';
import type { Foyer } from '@/types/foyers';

declare module 'react' {
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            currentFoyer: Foyer | null;
            foyers: Foyer[];
            [key: string]: unknown;
        };
    }
}
