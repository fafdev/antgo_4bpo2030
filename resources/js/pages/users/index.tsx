import { Head, Link, useForm } from '@inertiajs/react';
import { UsersRound } from 'lucide-react';
import UserController from '@/actions/App/Http/Controllers/UserController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit as usersEdit, index as usersIndex } from '@/routes/users';

type ManagedUser = {
    id: number;
    name: string;
    email: string;
    role_codelist: number | null;
    role_label: string | null;
    email_verified_at: string | null;
};

type Role = {
    id: number;
    label: string;
};

type Props = {
    users: ManagedUser[];
    roles: Role[];
};

export default function UsersIndex({ users, roles }: Props) {
    const form = useForm({
        name: '',
        email: '',
        role_codelist: '',
    });

    return (
        <>
            <Head title="Users" />

            <div className="space-y-8 p-4">
                <Heading
                    title="User management"
                    description="Create accounts and assign application roles."
                />

                <section className="max-w-2xl space-y-4 rounded-xl border p-4">
                    <h2 className="text-base font-semibold">Create user</h2>
                    <p className="text-sm text-muted-foreground">
                        A password setup link will be sent to the user by email.
                    </p>

                    <form
                        className="grid gap-4"
                        onSubmit={(event) => {
                            event.preventDefault();
                            form.post(UserController.store().url, {
                                onSuccess: () => form.reset(),
                            });
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
                            >
                                <option value="">No role</option>
                                {roles.map((role) => (
                                    <option key={role.id} value={role.id}>
                                        {role.label}
                                    </option>
                                ))}
                            </select>
                            <InputError message={form.errors.role_codelist} />
                        </div>

                        <div>
                            <Button disabled={form.processing}>
                                {form.processing ? 'Creating…' : 'Create user'}
                            </Button>
                        </div>
                    </form>
                </section>

                <section className="space-y-3 rounded-xl border p-4">
                    <div className="flex items-center gap-2">
                        <UsersRound className="size-5" />
                        <h2 className="text-base font-semibold">Users</h2>
                        <span className="text-sm text-muted-foreground">({users.length})</span>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full min-w-160 text-left text-sm">
                            <thead>
                                <tr className="border-b">
                                    <th className="px-2 py-3 font-medium">Name</th>
                                    <th className="px-2 py-3 font-medium">Email</th>
                                    <th className="px-2 py-3 font-medium">Role</th>
                                    <th className="px-2 py-3 font-medium">Email status</th>
                                    <th className="px-2 py-3 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {users.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="px-2 py-6 text-center text-muted-foreground">
                                            No users found.
                                        </td>
                                    </tr>
                                ) : (
                                    users.map((user) => (
                                        <tr key={user.id} className="border-b">
                                            <td className="px-2 py-3">{user.name}</td>
                                            <td className="px-2 py-3">{user.email}</td>
                                            <td className="px-2 py-3">{user.role_label ?? '-'}</td>
                                            <td className="px-2 py-3">
                                                {user.email_verified_at ? 'Verified' : 'Pending'}
                                            </td>
                                            <td className="px-2 py-3">
                                                <Button size="sm" variant="outline" asChild>
                                                    <Link href={usersEdit(user.id)}>Edit</Link>
                                                </Button>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </>
    );
}

UsersIndex.layout = {
    breadcrumbs: [
        {
            title: 'Users',
            href: usersIndex(),
        },
    ],
};
