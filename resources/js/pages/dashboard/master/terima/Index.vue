<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { User } from "@/types";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";

const column = createColumnHelper<User>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

const handleTerima = async (userId) => {
    try {
        const response = await axios.post('/master/users/terima', {
            user_id: userId,
        });
        // Menangani response sukses
        console.log(response.data);
        toast.success('Mitra Diterima!');
        refresh();
    } catch (error) {
        // Menangani error
        console.error(error);
        toast.error('Error menerima mitra');
    }
};

const handleTolak = async (userId) => {
    try {
        const response = await axios.post('/master/users/tolak', {
            user_id: userId,
        });
        // Menangani response sukses
        console.log(response.data);
        toast.success('Mitra Ditolak!');
        refresh();
    } catch (error) {
        // Menangani error
        console.error(error);
        toast.error('Error menolak mitra');
    }
};

const { delete: deleteUser } = useDelete({
    onSuccess: () => paginateRef.value.refetch(),
});

const columns = [
    column.accessor("no", {
        header: "#",
    }),
    column.accessor("name", {
        header: "Nama",
    }),
    column.accessor("email", {
        header: "Email",
    }),
    column.accessor("phone", {
        header: "No. Telp",
    }),
    column.accessor("id", {
        header: "Aksi",
        cell: (cell) => {
            const user_id = cell.getValue(); // Ambil user_id dari cell
            return h("div", { class: "d-flex gap-2" }, [ // Pastikan untuk mengembalikan elemen
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-success",
                        onClick: () => handleTerima(user_id),
                    },
                    h("i", { class: "la la-check fs-2" })
                ),
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-danger",
                        onClick: () => handleTolak(user_id), // Jika perlu mengirim user_id
                    },
                    h("i", { class: "la la-times fs-2" })
                ),
            ]);
        },
    }),

];

const refresh = () => paginateRef.value.refetch();

watch(openForm, (val) => {
    if (!val) {
        selected.value = "";
    }
    window.scrollTo(0, 0);
});
</script>

<template>
    <Form :selected="selected" @close="openForm = false" v-if="openForm" @refresh="refresh" />

    <div class="card">
        <div class="card-header align-items-center">
            <h2 class="mb-0">List Terima Mitra</h2>
        </div>
        <div class="card-body">
            <paginate ref="paginateRef" id="table-users" url="/master/terima" :columns="columns"></paginate>
        </div>
    </div>
</template>
