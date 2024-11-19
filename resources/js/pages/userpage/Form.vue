<script setup lang="ts">
import { ref, watch, onMounted } from "vue";
import { toast } from "vue3-toastify";
import { useRouter, useRoute } from "vue-router";
import axios from 'axios';

const route = useRoute();
const shoeId = ref<number | null>(null);

// State untuk menyimpan data detail sepatu
const shoeDetail = ref<any>(null);

// Fungsi untuk mengambil data sepatu berdasarkan shoeId
const getShoeDetail = async (id: number) => {
    try {
        const response = await axios.get(`/userpage/toko/get`);
        shoeDetail.value = response.data;
    } catch (error) {
        console.error('Error fetching shoe detail:', error);
    }
};

const emit = defineEmits(["close", "refresh"]);
const router = useRouter ();
function goBack() {
    router.go(-1); // Kembali ke halaman sebelumnya
}
const jumlahSepatu = ref(1);
const sepatuList = ref(
    Array.from({ length: jumlahSepatu.value }, () => ({
        foto: null,
        jenis: "",
        warna: "",
        layanan: "",
    }))
);

watch(jumlahSepatu, (newVal) => {
    // Update sepatuList based on jumlahSepatu
    if (newVal > sepatuList.value.length) {
        const newItems = Array.from({ length: newVal - sepatuList.value.length }, () => ({
            foto: null,
            jenis: "",
            warna: "",
            layanan: "",
        }));
        sepatuList.value.push(...newItems);
    } else {
        sepatuList.value.splice(newVal);
    }
});

function updateFoto(event: Event, index: number) {
    const file = (event.target as HTMLInputElement).files?.[0] || null;
    sepatuList.value[index].foto = file;
}

function submit() {
    // Validate and prepare form data
    const isValid = sepatuList.value.every((sepatu) => sepatu.jenis && sepatu.warna && sepatu.layanan);
    if (!isValid) {
        toast.error("Pastikan semua data sepatu terisi!");
        return;
    }

    const formData = new FormData();
    sepatuList.value.forEach((sepatu, index) => {
        formData.append(`sepatu[${index}][foto]`, sepatu.foto || "");
        formData.append(`sepatu[${index}][jenis]`, sepatu.jenis);
        formData.append(`sepatu[${index}][warna]`, sepatu.warna);
        formData.append(`sepatu[${index}][layanan]`, sepatu.layanan);
    });

    toast.success("Data berhasil disimpan!");
    emit("refresh");
}

onMounted(() => {
    shoeId.value = Number(route.query.shoeId);
    if (shoeId.value) {
        getShoeDetail(shoeId.value); // Panggil fungsi untuk mengambil data
    }
});
</script>


<template>
    <div class="container-fluid d-flex justify-content-center align-items-center py-5">
        <VForm
            class="form card p-4 shadow-lg rounded border-0 mx-3"
            id="form-user"
            ref="formRef"
            @submit="submit"
            style="max-width: 800px; width: 100%;"
        >
            <!-- Card Header -->
            <div class="bg-white border-0 mt-6 d-flex align-items-center pb-3">
                <button
                    type="button"
                    class="btn btn-sm ms-9 btn-danger "
                    @click="goBack"
                >
                     <
                </button>
                <h3 class="mb-0 fw-bold ms-3">
                    Tambah Pesanan
                </h3>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <!-- Input Jumlah Sepatu -->
                <div class="mb-4">
                    <label for="jumlah_sepatu" class="form-label fw-bold">
                        Berapa sepatu yang akan dicuci?
                    </label>
                    <input
                        id="jumlah_sepatu"
                        type="number"
                        min="1"
                        max="10"
                        class="form-control form-control-lg"
                        v-model="jumlahSepatu"
                        placeholder="Masukkan jumlah sepatu"
                    />
                </div>

                <!-- Grup Input Dinamis -->
                <div v-for="(sepatu, index) in sepatuList" :key="index" class="border rounded p-3 mb-3">
                    <h5 class="fw-bold">Sepatu {{ index + 1 }}</h5>

                    <!-- Upload Foto -->
                    <div class="mb-3">
                        <label :for="'foto_sepatu_' + index" class="form-label">
                            Upload Foto
                        </label>
                        <input
                            :id="'foto_sepatu_' + index"
                            type="file"
                            class="form-control"
                            @change="updateFoto($event, index)"
                        />
                    </div>

                    <!-- Jenis Sepatu -->
                    <div class="mb-3">
                        <label :for="'jenis_sepatu_' + index" class="form-label">Jenis Sepatu</label>
                        <input
                            :id="'jenis_sepatu_' + index"
                            type="text"
                            class="form-control"
                            v-model="sepatu.jenis"
                            placeholder="Masukkan jenis sepatu"
                        />
                    </div>

                    <!-- Warna Sepatu -->
                    <div class="mb-3">
                        <label :for="'warna_sepatu_' + index" class="form-label">Warna Sepatu</label>
                        <input
                            :id="'warna_sepatu_' + index"
                            type="text"
                            class="form-control"
                            v-model="sepatu.warna"
                            placeholder="Masukkan warna sepatu"
                        />
                    </div>

                    <!-- Layanan -->
                    <div class="mb-3">
                        <label :for="'layanan_' + index" class="form-label">Layanan</label>
                        <select
                            :id="'layanan_' + index"
                            class="form-control"
                            v-model="sepatu.layanan"
                        >
                            <option value="" disabled>Pilih layanan</option>
                            <option value="cuci">Cuci</option>
                            <option value="reparasi">Reparasi</option>
                            <option value="polish">Polish</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="card-footer bg-white border-0 d-flex justify-content-end pt-3">
                <button type="submit" class="btn btn-primary px-5">
                    Simpan
                </button>
            </div>
        </VForm>
    </div>
</template>




