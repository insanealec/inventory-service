<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="API Tokens" />

        <h1 class="sr-only">API Tokens</h1>

        <SettingsLayout>
            <div class="space-y-6">
                <Heading
                    variant="small"
                    title="API Tokens"
                    description="Manage your API tokens for external applications and services"
                />

                <Card>
                    <CardHeader>
                        <CardTitle>Create New Token</CardTitle>
                        <CardDescription>
                            Generate a new API token to use with external
                            applications.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            :form="form"
                            class="space-y-4"
                            @submit="submit"
                            v-slot="{ processing, errors }"
                        >
                            <div>
                                <Label for="token-name">Token Name</Label>
                                <Input
                                    id="token-name"
                                    name="name"
                                    placeholder="Enter token name"
                                    :disabled="processing"
                                    v-model="form.name"
                                />
                                <p
                                    v-if="errors.name"
                                    class="mt-1 text-sm text-destructive"
                                >
                                    {{ errors.name }}
                                </p>
                            </div>
                            <div class="flex justify-end">
                                <Button type="submit" :disabled="processing">
                                    {{
                                        processing
                                            ? 'Creating...'
                                            : 'Create Token'
                                    }}
                                </Button>
                            </div>
                        </Form>
                    </CardContent>
                </Card>

                <Card v-if="newToken?.token">
                    <CardHeader>
                        <CardTitle>Token Created</CardTitle>
                        <CardDescription>
                            Save this token securely. You won't be able to see
                            it again.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                class="rounded-md bg-muted p-4 font-mono break-all"
                            >
                                {{ newToken.token }}
                            </div>
                            <div class="flex gap-2">
                                <Button @click="copyToken">Copy Token</Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Your Tokens</CardTitle>
                        <CardDescription>
                            Manage your existing API tokens.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-if="tokens.length === 0"
                                class="py-8 text-center text-muted-foreground"
                            >
                                <p>No tokens found.</p>
                            </div>
                            <div v-else class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="border-b">
                                        <tr>
                                            <th class="p-3 text-left">Name</th>
                                            <th class="p-3 text-left">
                                                Created
                                            </th>
                                            <th class="p-3 text-left">
                                                Last Used
                                            </th>
                                            <th class="p-3 text-left">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="token in tokens"
                                            :key="token.id"
                                            class="border-b"
                                        >
                                            <td class="p-3">
                                                {{ token.name }}
                                            </td>
                                            <td class="p-3">
                                                {{ token.created_at }}
                                            </td>
                                            <td class="p-3">
                                                {{
                                                    token.last_used_at ||
                                                    'Never'
                                                }}
                                            </td>
                                            <td class="p-3">
                                                <Dialog>
                                                    <DialogTrigger as-child>
                                                        <Button
                                                            variant="destructive"
                                                            size="sm"
                                                        >
                                                            Delete
                                                        </Button>
                                                    </DialogTrigger>
                                                    <DialogContent>
                                                        <DialogHeader>
                                                            <DialogTitle
                                                                >Delete
                                                                Token</DialogTitle
                                                            >
                                                            <DialogDescription>
                                                                Are you sure you
                                                                want to delete
                                                                the token "{{
                                                                    token.name
                                                                }}"? This action
                                                                cannot be
                                                                undone.
                                                            </DialogDescription>
                                                        </DialogHeader>
                                                        <Form
                                                            :form="deleteForm"
                                                            :action="`/settings/tokens/${token.id}`"
                                                            method="delete"
                                                            v-slot="{
                                                                processing,
                                                            }"
                                                        >
                                                            <DialogFooter>
                                                                <DialogClose
                                                                    as-child
                                                                >
                                                                    <Button
                                                                        variant="outline"
                                                                        >Cancel</Button
                                                                    >
                                                                </DialogClose>
                                                                <Button
                                                                    type="submit"
                                                                    variant="destructive"
                                                                    :disabled="
                                                                        processing
                                                                    "
                                                                >
                                                                    {{
                                                                        processing
                                                                            ? 'Deleting...'
                                                                            : 'Delete'
                                                                    }}
                                                                </Button>
                                                            </DialogFooter>
                                                        </Form>
                                                    </DialogContent>
                                                </Dialog>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>

<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { index } from '@/routes/tokens';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const page = usePage();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'API Tokens',
        href: index().url,
    },
];

const tokens = ref([]);
const newToken = ref(null);
const form = useForm({
    name: '',
});
const deleteForm = useForm({});

const copyToken = async () => {
    try {
        await navigator.clipboard.writeText(newToken.value.token);
        // Simple alert instead of toast component that's not available
        alert('Token copied to clipboard!');
    } catch (err) {
        alert('Failed to copy token');
    }
};

// Initialize tokens data from props
const props = defineProps<{
    tokens: Array<{
        id: string;
        name: string;
        created_at: string;
        last_used_at: string | null;
        expires_at: string | null;
    }>;
    newToken?: {
        token: string;
        id: number;
        name: string;
    };
}>();

watch(
    () => props.tokens,
    (newTokens) => {
        tokens.value = newTokens;
    },
    { immediate: true },
);

watch(
    () => props.newToken,
    (newTokenValue) => {
        newToken.value = newTokenValue;
    },
    { immediate: true },
);

const submit = () => {
    form.post('/settings/tokens', {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>
