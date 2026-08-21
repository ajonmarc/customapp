<script setup lang="ts">
/*
|--------------------------------------------------------------------------
| Imports
|--------------------------------------------------------------------------
*/

import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

import SimpleTable from '@kikiloaw/simple-table';
import { Pencil, Plus, Trash2 } from '@lucide/vue';

import Heading from '@/components/Heading.vue';
import FormDialog from '@/components/crud/FormDialog.vue';
import Form from './Form.vue';

import { Button } from '@/components/ui/button';

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

import {
    index,
    store,
    update,
    destroy,
    data,
} from '@/routes/admin/roles';


/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

interface Role {
    id: number;
    name: string;
    description: string | null;
}


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

/**
 * Role currently being created.
 */
const creating = ref(false);

/**
 * Role currently being edited.
 */
const editingRole = ref<Role | null>(null);

/**
 * Role currently being deleted.
 */
const deleteTarget = ref<Role | null>(null);

/**
 * SimpleTable reference.
 */
const tableRef = ref();

/**
 * Prevent duplicate delete requests.
 */
const deleting = ref(false);


/*
|--------------------------------------------------------------------------
| Table Configuration
|--------------------------------------------------------------------------
*/

const columns = [
    {
        key: 'id',
        label: 'ID',
        sortable: true,
        width: '80px',
    },
    {
        key: 'name',
        label: 'Name',
        sortable: true,
    },
    {
        key: 'description',
        label: 'Description',
        sortable: true,
    },
    {
        key: 'actions',
        label: 'Actions',
        sortable: false,
    },
];


/*
|--------------------------------------------------------------------------
| Create Actions
|--------------------------------------------------------------------------
*/

/**
 * Open create modal.
 */
const openCreate = () => {
    creating.value = true;
};

/**
 * Close create modal.
 */
const closeCreate = () => {
    creating.value = false;
};

/**
 * Called after successful creation.
 */
const handleCreateSuccess = () => {
    creating.value = false;

    // Refresh SimpleTable data
    tableRef.value?.clearCache('current');
};


/*
|--------------------------------------------------------------------------
| Edit Actions
|--------------------------------------------------------------------------
*/

/**
 * Open edit modal.
 */
const openEdit = (role: Role) => {
    editingRole.value = role;
};

/**
 * Close edit modal.
 */
const closeEdit = () => {
    editingRole.value = null;
};

/**
 * Called after successful update.
 */
const handleEditSuccess = () => {
    editingRole.value = null;

    // Refresh SimpleTable data
    tableRef.value?.clearCache('current');
};


/*
|--------------------------------------------------------------------------
| Delete Actions
|--------------------------------------------------------------------------
*/

/**
 * Open delete confirmation dialog.
 */
const confirmDelete = (role: Role) => {
    deleteTarget.value = role;
};

/**
 * Delete selected role.
 */
const performDelete = () => {
    if (!deleteTarget.value || deleting.value) {
        return;
    }

    const roleId = deleteTarget.value.id;

    deleting.value = true;

    router.delete(destroy(roleId).url, {
        preserveScroll: true,

        onSuccess: () => {
            deleteTarget.value = null;

            tableRef.value?.clearCache('current');
        },

        onFinish: () => {
            deleting.value = false;
        },
    });
};


/*
|--------------------------------------------------------------------------
| Page Options
|--------------------------------------------------------------------------
*/

defineOptions({
    layout: {
        breadcrumbs: [
            {
                href: index(),
                title: 'Roles',
            },
        ],
    },
});
</script>


<template>

    <Head title="Roles" />

    <div class="px-4 py-6">

        <!-- ==========================================================
             Page Header
        =========================================================== -->

        <div class="flex items-center justify-between">

            <Heading
                title="Roles"
                description="Manage roles"
            />

            <Button @click="openCreate">
                <Plus class="mr-2 h-4 w-4" />
                New Role
            </Button>

        </div>


        <!-- ==========================================================
             Data Table
        =========================================================== -->

        <div class="mt-6">

            <SimpleTable
                ref="tableRef"
                :fetch-url="data().url"
                :columns="columns"
                searchable
                enable-cache
            >

                <!-- ==================================================
                     Actions Column
                =================================================== -->

                <template v-slot:[`cell-actions`]="{ row }">

                    <div class="inline-flex items-center gap-2">

                        <!-- Edit -->
                        <Button
                            variant="outline"
                            size="icon"
                            title="Edit role"
                            @click="openEdit(row)"
                        >
                            <Pencil class="h-4 w-4" />
                        </Button>


                        <!-- Delete -->
                        <Button
                            variant="destructive"
                            size="icon"
                            title="Delete role"
                            :disabled="deleting"
                            @click="confirmDelete(row)"
                        >
                            <Trash2 class="h-4 w-4" />
                        </Button>

                    </div>

                </template>

            </SimpleTable>

        </div>


        <!-- ==========================================================
             Create Dialog
        =========================================================== -->

        <FormDialog
            :open="creating"
            title="Create New Role"
            content-class="sm:max-w-2xl"
            description="Add a new role to the system."
            @update:open="
                (value) => {
                    if (!value) {
                        closeCreate();
                    }
                }
            "
        >

            <template #default="{ close }">

                <Form
                    :submit-action="store()"
                    submit-label="Create Role"
                    :on-cancel="close"
                    @success="handleCreateSuccess"
                />

            </template>

        </FormDialog>


        <!-- ==========================================================
             Edit Dialog
        =========================================================== -->

        <FormDialog
            :open="!!editingRole"
            title="Edit Role"
            content-class="sm:max-w-2xl"
            :description="
                editingRole
                    ? `Update ${editingRole.name} details.`
                    : undefined
            "
            @update:open="
                (value) => {
                    if (!value) {
                        closeEdit();
                    }
                }
            "
        >

            <template #default="{ close }">

                <Form
                    v-if="editingRole"
                    :role="editingRole"
                    :submit-action="update(editingRole.id)"
                    submit-label="Save Changes"
                    :on-cancel="close"
                    @success="handleEditSuccess"
                />

            </template>

        </FormDialog>


        <!-- ==========================================================
             Delete Confirmation
        =========================================================== -->

        <Dialog
            :open="!!deleteTarget"
            @update:open="
                (value) => {
                    if (!value && !deleting) {
                        deleteTarget = null;
                    }
                }
            "
        >

            <DialogContent>

                <DialogHeader>

                    <DialogTitle>
                        Delete role?
                    </DialogTitle>

                    <DialogDescription>
                        This will permanently delete
                        <strong>
                            {{ deleteTarget?.name }}
                        </strong>.
                        This action cannot be undone.
                    </DialogDescription>

                </DialogHeader>


                <DialogFooter>

                    <!-- Cancel -->
                    <Button
                        variant="outline"
                        :disabled="deleting"
                        @click="deleteTarget = null"
                    >
                        Cancel
                    </Button>


                    <!-- Delete -->
                    <Button
                        variant="destructive"
                        :disabled="deleting"
                        @click="performDelete"
                    >

                        <Trash2
                            v-if="!deleting"
                            class="mr-2 h-4 w-4"
                        />

                        {{ deleting ? 'Deleting...' : 'Delete' }}

                    </Button>

                </DialogFooter>

            </DialogContent>

        </Dialog>

    </div>

</template>