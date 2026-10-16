import { Form, Head, router } from '@inertiajs/react';
import { ChevronDown, Search } from 'lucide-react';
import { useEffect, useState } from 'react';
import CodeListController from '@/actions/App/Http/Controllers/CodeListController';
import CodeListItemController from '@/actions/App/Http/Controllers/CodeListItemController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import { index } from '@/routes/code-lists';

const expandedListsStorageKey = 'code-lists.expanded-ids';

type CodeListItem = {
    id: number;
    code: string;
    label: string;
    sort_order: number;
    active: boolean;
};

type CodeList = {
    id: number;
    key: string;
    name: string;
    items: CodeListItem[];
};

type Props = {
    codeLists: CodeList[];
    filters: {
        search: string;
    };
};

function readExpandedIds(): number[] {
    if (typeof sessionStorage === 'undefined') {
        return [];
    }

    try {
        const raw = sessionStorage.getItem(expandedListsStorageKey);

        if (!raw) {
            return [];
        }

        const parsed: unknown = JSON.parse(raw);

        if (!Array.isArray(parsed)) {
            return [];
        }

        return parsed.filter((id): id is number => typeof id === 'number');
    } catch {
        return [];
    }
}

function writeExpandedIds(ids: number[]): void {
    if (typeof sessionStorage === 'undefined') {
        return;
    }

    sessionStorage.setItem(expandedListsStorageKey, JSON.stringify(ids));
}

export default function CodeLists({ codeLists, filters }: Props) {
    const [expandedIds, setExpandedIds] = useState<number[]>([]);
    const [storageReady, setStorageReady] = useState(false);
    const [search, setSearch] = useState(filters.search);

    useEffect(() => {
        setExpandedIds(readExpandedIds());
        setStorageReady(true);
    }, []);

    useEffect(() => {
        if (!storageReady) {
            return;
        }

        writeExpandedIds(expandedIds);
    }, [expandedIds, storageReady]);

    useEffect(() => {
        setSearch(filters.search);
    }, [filters.search]);

    useEffect(() => {
        const timeout = window.setTimeout(() => {
            const nextSearch = search.trim();

            if (nextSearch === filters.search) {
                return;
            }

            router.get(
                index.url({
                    query: nextSearch === '' ? {} : { search: nextSearch },
                }),
                {},
                {
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                    only: ['codeLists', 'filters'],
                },
            );
        }, 300);

        return () => window.clearTimeout(timeout);
    }, [search, filters.search]);

    const allExpanded =
        codeLists.length > 0 &&
        codeLists.every((codeList) => expandedIds.includes(codeList.id));

    function setListOpen(id: number, open: boolean): void {
        setExpandedIds((current) => {
            if (open) {
                return current.includes(id) ? current : [...current, id];
            }

            return current.filter((expandedId) => expandedId !== id);
        });
    }

    function expandAll(): void {
        setExpandedIds(codeLists.map((codeList) => codeList.id));
    }

    function collapseAll(): void {
        setExpandedIds([]);
    }

    return (
        <>
            <Head title="Code lists" />

            <div className="space-y-8 p-4">
                <Heading
                    title="Code lists"
                    description="Manage your code lists and their items"
                />

                <div className="rounded-xl border p-4">
                    <h2 className="mb-4 text-sm font-semibold">Create code list</h2>

                    <Form
                        {...CodeListController.store.form()}
                        resetOnSuccess
                        className="grid gap-4 md:grid-cols-[1fr_1fr_auto]"
                    >
                        {({ errors, processing }) => (
                            <>
                                <div className="grid gap-2">
                                    <Label htmlFor="new_key">Key</Label>
                                    <Input
                                        id="new_key"
                                        name="key"
                                        placeholder="e.g. document_status"
                                        required
                                    />
                                    <InputError message={errors.key} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="new_name">Name</Label>
                                    <Input
                                        id="new_name"
                                        name="name"
                                        placeholder="e.g. Document Status"
                                        required
                                    />
                                    <InputError message={errors.name} />
                                </div>

                                <div className="self-end">
                                    <Button disabled={processing}>Create</Button>
                                </div>
                            </>
                        )}
                    </Form>
                </div>

                <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div className="grid gap-2 sm:max-w-sm sm:flex-1">
                        <Label htmlFor="code_list_search">Search description</Label>
                        <div className="relative">
                            <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                id="code_list_search"
                                value={search}
                                onChange={(event) => setSearch(event.target.value)}
                                placeholder="Filter by name..."
                                className="pl-9"
                            />
                        </div>
                    </div>

                    {codeLists.length > 0 && (
                        <div className="flex flex-wrap items-center gap-2">
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                onClick={expandAll}
                                disabled={allExpanded}
                            >
                                Expand all items
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                onClick={collapseAll}
                                disabled={expandedIds.length === 0}
                            >
                                Collapse all items
                            </Button>
                        </div>
                    )}
                </div>

                <div className="space-y-6">
                    {codeLists.length === 0 && (
                        <p className="text-sm text-muted-foreground">
                            {filters.search !== ''
                                ? 'No code lists match this description.'
                                : 'No code lists yet.'}
                        </p>
                    )}

                    {codeLists.map((codeList) => (
                        <CodeListCard
                            key={codeList.id}
                            codeList={codeList}
                            itemsOpen={expandedIds.includes(codeList.id)}
                            onItemsOpenChange={(open) =>
                                setListOpen(codeList.id, open)
                            }
                        />
                    ))}
                </div>
            </div>
        </>
    );
}

function CodeListCard({
    codeList,
    itemsOpen,
    onItemsOpenChange,
}: {
    codeList: CodeList;
    itemsOpen: boolean;
    onItemsOpenChange: (open: boolean) => void;
}) {
    const itemCountLabel =
        codeList.items.length === 1
            ? '1 item'
            : `${codeList.items.length} items`;

    return (
        <article className="space-y-4 rounded-xl border p-4">
            <div className="grid gap-2 md:grid-cols-[1fr_auto]">
                <Form
                    {...CodeListController.update.form(codeList.id)}
                    className="grid gap-4 md:grid-cols-[1fr_1fr_auto]"
                >
                    {({ errors, processing }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor={`key_${codeList.id}`}>Key</Label>
                                <Input
                                    id={`key_${codeList.id}`}
                                    name="key"
                                    defaultValue={codeList.key}
                                    required
                                />
                                <InputError message={errors.key} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor={`name_${codeList.id}`}>Name</Label>
                                <Input
                                    id={`name_${codeList.id}`}
                                    name="name"
                                    defaultValue={codeList.name}
                                    required
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="self-end">
                                <Button
                                    type="submit"
                                    variant="secondary"
                                    disabled={processing}
                                >
                                    Save list
                                </Button>
                            </div>
                        </>
                    )}
                </Form>

                <Form
                    {...CodeListController.destroy.form(codeList.id)}
                    className="self-end md:justify-self-end"
                >
                    {({ processing: deleting }) => (
                        <Button
                            type="submit"
                            variant="destructive"
                            disabled={deleting}
                        >
                            Delete list
                        </Button>
                    )}
                </Form>
            </div>

            <Collapsible open={itemsOpen} onOpenChange={onItemsOpenChange}>
                <div className="flex flex-wrap items-center justify-between gap-2 border-t pt-4">
                    <div className="flex items-center gap-2">
                        <h3 className="text-sm font-semibold">Items</h3>
                        <Badge variant="secondary">{itemCountLabel}</Badge>
                    </div>

                    <CollapsibleTrigger asChild>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            aria-expanded={itemsOpen}
                        >
                            {itemsOpen ? 'Collapse items' : 'Expand items'}
                            <ChevronDown
                                className={cn(
                                    'size-4 transition-transform',
                                    itemsOpen && 'rotate-180',
                                )}
                            />
                        </Button>
                    </CollapsibleTrigger>
                </div>

                <CollapsibleContent className="space-y-3 pt-3">
                    {codeList.items.length === 0 && (
                        <p className="text-sm text-muted-foreground">
                            No items yet.
                        </p>
                    )}

                    {codeList.items.map((item) => (
                        <div
                            key={item.id}
                            className="grid gap-2 md:grid-cols-[1fr_auto]"
                        >
                            <Form
                                {...CodeListItemController.update.form([
                                    codeList.id,
                                    item.id,
                                ])}
                                className="grid gap-3 rounded-lg border p-3 md:grid-cols-[1fr_1fr_120px_auto_auto]"
                            >
                                {({ errors, processing }) => (
                                    <>
                                        <div className="grid gap-2">
                                            <Label htmlFor={`item_code_${item.id}`}>
                                                Code
                                            </Label>
                                            <Input
                                                id={`item_code_${item.id}`}
                                                name="code"
                                                defaultValue={item.code}
                                                required
                                            />
                                            <InputError message={errors.code} />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label
                                                htmlFor={`item_label_${item.id}`}
                                            >
                                                Label
                                            </Label>
                                            <Input
                                                id={`item_label_${item.id}`}
                                                name="label"
                                                defaultValue={item.label}
                                                required
                                            />
                                            <InputError message={errors.label} />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label
                                                htmlFor={`item_sort_order_${item.id}`}
                                            >
                                                Sort order
                                            </Label>
                                            <Input
                                                id={`item_sort_order_${item.id}`}
                                                name="sort_order"
                                                type="number"
                                                min={0}
                                                defaultValue={item.sort_order}
                                                required
                                            />
                                            <InputError
                                                message={errors.sort_order}
                                            />
                                        </div>

                                        <div className="grid gap-2 self-end">
                                            <Label
                                                htmlFor={`item_active_${item.id}`}
                                            >
                                                Active
                                            </Label>
                                            <div className="flex h-9 items-center gap-2">
                                                <input
                                                    type="hidden"
                                                    name="active"
                                                    value="0"
                                                />
                                                <input
                                                    id={`item_active_${item.id}`}
                                                    type="checkbox"
                                                    name="active"
                                                    value="1"
                                                    defaultChecked={item.active}
                                                />
                                            </div>
                                            <InputError message={errors.active} />
                                        </div>

                                        <div className="self-end">
                                            <Button
                                                type="submit"
                                                variant="secondary"
                                                disabled={processing}
                                            >
                                                Save
                                            </Button>
                                        </div>
                                    </>
                                )}
                            </Form>

                            <Form
                                {...CodeListItemController.destroy.form([
                                    codeList.id,
                                    item.id,
                                ])}
                                className="self-end md:justify-self-end"
                            >
                                {({ processing: deleting }) => (
                                    <Button
                                        type="submit"
                                        variant="destructive"
                                        disabled={deleting}
                                    >
                                        Delete
                                    </Button>
                                )}
                            </Form>
                        </div>
                    ))}

                    <Form
                        {...CodeListItemController.store.form(codeList.id)}
                        resetOnSuccess
                        className="grid gap-3 rounded-lg border border-dashed p-3 md:grid-cols-[1fr_1fr_120px_auto_auto]"
                    >
                        {({ errors, processing }) => (
                            <>
                                <div className="grid gap-2">
                                    <Label
                                        htmlFor={`new_item_code_${codeList.id}`}
                                    >
                                        Code
                                    </Label>
                                    <Input
                                        id={`new_item_code_${codeList.id}`}
                                        name="code"
                                        placeholder="e.g. pending"
                                        required
                                    />
                                    <InputError message={errors.code} />
                                </div>

                                <div className="grid gap-2">
                                    <Label
                                        htmlFor={`new_item_label_${codeList.id}`}
                                    >
                                        Label
                                    </Label>
                                    <Input
                                        id={`new_item_label_${codeList.id}`}
                                        name="label"
                                        placeholder="e.g. Pending"
                                        required
                                    />
                                    <InputError message={errors.label} />
                                </div>

                                <div className="grid gap-2">
                                    <Label
                                        htmlFor={`new_item_sort_order_${codeList.id}`}
                                    >
                                        Sort order
                                    </Label>
                                    <Input
                                        id={`new_item_sort_order_${codeList.id}`}
                                        name="sort_order"
                                        type="number"
                                        min={0}
                                        defaultValue={0}
                                        required
                                    />
                                    <InputError message={errors.sort_order} />
                                </div>

                                <div className="grid gap-2 self-end">
                                    <Label
                                        htmlFor={`new_item_active_${codeList.id}`}
                                    >
                                        Active
                                    </Label>
                                    <div className="flex h-9 items-center gap-2">
                                        <input
                                            type="hidden"
                                            name="active"
                                            value="0"
                                        />
                                        <input
                                            id={`new_item_active_${codeList.id}`}
                                            type="checkbox"
                                            name="active"
                                            value="1"
                                            defaultChecked
                                        />
                                    </div>
                                    <InputError message={errors.active} />
                                </div>

                                <div className="self-end">
                                    <Button disabled={processing}>Add item</Button>
                                </div>
                            </>
                        )}
                    </Form>
                </CollapsibleContent>
            </Collapsible>
        </article>
    );
}

CodeLists.layout = {
    breadcrumbs: [
        {
            title: 'Code lists',
            href: index(),
        },
    ],
};
