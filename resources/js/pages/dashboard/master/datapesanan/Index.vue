<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { User } from "@/types";

const column = createColumnHelper<User>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

const { delete: deleteUser } = useDelete({
    onSuccess: () => paginateRef.value.refetch(),
});

const columns = [
    column.accessor("no", {
        header: "#",
    }),
    column.accessor("user.name", {
        header: "Customer",
    }),
    column.accessor("toko.nama_toko", {
        header: "Toko",
    }),
    column.accessor("layanan.referensi_layanan.nama_layanan", {
        header: "Layanan",
    }),
    column.accessor("brand_sepatu", {
        header: "Brand sepatu",
    }),
    column.accessor("warna_sepatu", {
        header: "Warna sepatu",
    }),
    column.accessor("tanggal_pesanan", {
        header: "Tanggal memesan",
    }),
    column.accessor("status", {
        header: "Status",
    }),
    column.accessor("total_harga", {
        header: "Total harga",
    }),
    column.accessor("foto_sepatu", {
        header: "Foto sepatu",
        cell: cell => h ('img', {src: `/storage/${cell.getValue()}`,width : 100})
    }),
    column.accessor("uuid", {
        header: "Aksi",
        cell: (cell) =>
            h("div", { class: "d-flex gap-2" }, [
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-info",
                        onClick: () => {
                            selected.value = cell.getValue();
                            openForm.value = true;
                        },
                    },
                    "Lihat foto"
                ),
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-danger",
                        onClick: () =>
                            deleteUser(`/pesanan/${cell.getValue()}`),
                    },
                    h("i", { class: "la la-trash fs-2" })
                ),
            ]),
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
    <Form
        :selected="selected"
        @close="openForm = false"
        v-if="openForm"
        @refresh="refresh"
    />

    <div class="card">
        <div class="card-header align-items-center">
            <h2 class="mb-0">List Pesanan</h2>
            <button
                type="button"
                class="btn btn-sm btn-primary ms-auto"
                v-if="!openForm"
                @click="openForm = true"
            >
                Tambah
                <i class="la la-plus"></i>
            </button>
        </div>
        <div class="card-body">
            <paginate
                ref="paginateRef"
                id="table-users"
                url="/pesanan/datapesanan"
                :columns="columns"
            ></paginate>
        </div>
    </div>
</template>
