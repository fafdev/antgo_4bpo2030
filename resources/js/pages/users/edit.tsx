import { Head, Link, useForm } from '@inertiajs/react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index as usersIndex, update as usersUpdate } from '@/routes/users';

type ManagedUser = {
    id: number;
    name: string;
    email: string;
    role_codelist: number | null;
};

type Role = {
    id: number;
    label: string;
};

type Props = {
    user: ManagedUser;
    roles: Role[];
    isSelf: boolean;
};

export default function UsersEdit({ user, roles, isSelf }: Props) {
    const form = useForm({
        name: user.name,
        email: user.email,
        role_codelist: user.role_codelist?.toString() ?? '',
    });

    return (
        <>
            <Head title={`Edit ${user.name}`} />

            <div className="space-y-6 p-4">
                <Heading
                    title="Edit user"
                    description="Update the user's profile and application role."
                />

                <form
                    className="max-w-2xl space-y-4 rounded-xl border p-4"
                    onSubmit={(event) => {
                        event.preventDefault();
                        form.put(usersUpdate(user.id).url);
                    }}
                >
                    <div className="grid gap-2">
                        <Label htmlFor="name">Name</Label>
                        <Input
                            id="name"
                            name="name"
                            value={form.data.name}
                            onChange={(event) => form.setData('name', event.target.value)}
                            required
                        />
                        <InputError message={form.errors.name} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="email">Email</Label>
                        <Input
                            id="email"
                            name="email"
                            type="email"
                            value={form.data.email}
                            onChange={(event) => form.setData('email', event.target.value)}
                            required
                        />
                        <InputError message={form.errors.email} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="role_codelist">Role</Label>
                        <select
                            id="role_codelist"
                            name="role_codelist"
                            className="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                            value={form.data.role_codelist}
                            onChange={(event) => form.setData('role_codelist', event.target.value)}
                            disabled={isSelf}
                        >
                            <option value="">No role</option>
                            {roles.map((role) => (
                                <option key={role.id} value={role.id}>
                                    {role.label}
                                </option>
                            ))}
                        </select>
                        {isSelf && (
                            <p className="text-sm text-muted-foreground">
                                You cannot change your own role.
                            </p>
                        )}
                        <InputError message={form.errors.role_codelist} />
                    </div>

                    <div className="flex gap-2">
                        <Button disabled={form.processing}>
                            {form.processing ? 'Saving…' : 'Save changes'}
                        </Button>
                        <Button variant="secondary" asChild>
                            <Link href={usersIndex()}>Cancel</Link>
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}

UsersEdit.layout = {
    breadcrumbs: [
        {
            title: 'Users',
            href: usersIndex(),
        },
        {
            title: 'Edit',
            href: '#',
        },
    ],
};
