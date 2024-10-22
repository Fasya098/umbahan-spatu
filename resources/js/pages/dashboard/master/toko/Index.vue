<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
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
    column.accessor("nama_toko", {
        header: "Nama Toko",
    }),
    column.accessor("alamat", {
        header: "Alamat",
    }),
    column.accessor("nomor_telepon", {
        header: "No.Telp",
    }),
    column.accessor("foto_toko", {
    header: "Foto Toko",
    cell: (info) => {
        const imageUrl = `/storage/${info.getValue()}`; // Sesuaikan path jika perlu
        return h('img', {
            src: imageUrl,
            alt: 'Foto Toko',
            style: 'width: 50px; height: 50px; object-fit: cover;', // Sesuaikan ukuran gambar
        });
    }
}),
    column.accessor("id", {
        header: "Aksi",
        cell: (cell) =>
            h("div", { class: "d-flex gap-2" }, [
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-danger",
                        onClick: () =>
                            deleteUser(`/master/toko/destroy/${cell.getValue()}`),
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
            <h2 class="mb-0">List Toko</h2>
        </div>
        <div class="card-body">
            <paginate
                ref="paginateRef"
                id="table-users"
                url="/master/toko"
                :columns="columns"
            ></paginate>
        </div>
    </div>
</template>
