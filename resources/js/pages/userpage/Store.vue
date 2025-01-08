<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter, useRoute } from 'vue-router';

const tokos = ref([]);
const router = useRouter();
const route = useRoute();
const uuid = route.params.uuid;

function goToForm(uuid, userId) {
    router.push({ name: 'userpage.form', params: { uuid, userId } });
};

function goBack() {
    router.go(-1);
}

const getShoesData = async () => {
    try {
        const response = await axios.get(`/userpage/toko/shiw/${route.params.uuid}`);
        tokos.value = response.data;
    } catch (error) {
        console.error('Error mengambil data toko:', error);
    }
};

// Fungsi untuk mendapatkan URL gambar
const getImageUrl = (path: string) => {
    return path ? `/storage/${path}` : '/placeholder-shoe.jpg';
};

// Memuat data sepatu saat komponen dimuat
onMounted(() => {
    getShoesData();
});
</script>

<template>
    <nav class="navbar">
        <div>
            <h1 style="color: aliceblue;">FreshFT.Clean</h1>
        </div>
    </nav>

    <div class="container-fluid " style="background-color: #f8f9fa; min-height: 100vh;">
        <div class="container">
            <div class="card border-0 p-4 rounded-4 shadow-lg bg-white">
                <div v-if="tokos.length" v-for="toko in tokos" :key="toko.uuid" class="store-card">
                    <!-- Store Header -->
                    <div class="row g-4 align-items-stretch">
                        <!-- Store Image -->
                        <div class="col-lg-4 col-md-5">
                            <div class="rounded-4 overflow-hidden shadow-sm h-100">
                                <img :src="getImageUrl(toko.foto_toko)" alt="Store Image" class="img-fluid w-100 h-100"
                                    style="object-fit: cover; min-height: 300px;">
                            </div>
                        </div>

                        <!-- Store Information -->
                        <div class="col-lg-8 col-md-7">
                            <div class="h-100 p-4 bg-light rounded-4 shadow">
                                <div class="d-flex flex-column h-100">
                                    <!-- Store Name -->
                                    <h2 class="mb-3 text-primary fw-bold">{{ toko.nama_toko }}</h2>

                                    <!-- Store Description -->
                                    <div class="mb-4">
                                        <p class="text-muted mb-0" style="line-height: 1.6; font-size: 1.4rem;">
                                            {{ toko.deskripsi }}
                                        </p>
                                    </div>

                                    <!-- Contact Details -->
                                    <div class="mt-auto">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-telephone-fill me-2 text-primary"></i>
                                                <span class="fw-medium" style="font-size: 1.2rem;">{{ toko.nomor_telepon }}</span>
                                            </div>
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-geo-alt-fill me-2 mt-2 text-primary"></i>
                                                <span class="fw-medium" style="font-size: 1.2rem;">{{ toko.alamat }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-10 px-2">
                        <button class="btn btn-danger px-4 py-3 rounded-3 shadow-sm" @click="goBack">
                            <i class="las la-angle-left" style="color: white;"></i>
                            Kembali
                        </button>
                        <button class="btn btn-primary px-4 py-3 rounded-3 shadow-sm"
                            @click="goToForm(toko.uuid, toko.user_id)">
                            <i class="bi bi-cart-plus me-2"></i>
                            Pesan Disini
                        </button>
                    </div>

                    <!-- Divider -->
                    <hr class="my-4 opacity-25">
                </div>
            </div>
        </div>
    </div>

</template>

<style scoped>
.shoe-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 0.25rem;
}

.navbar {
    background-color: #333;
    padding: 2rem;
}

.nav-item {
    color: white;
    text-decoration: none;
    margin: 0 1rem;
}

.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.card {
    width: 100%;
    height: 90%;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    padding: 20px;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
}

.image-card {
    background-color: grey;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
}

.card-title {
    font-size: 2.5rem;
    margin-bottom: 20px;
    font-weight: bold;
}

.card-content {
    font-size: 1.2rem;
    margin-bottom: 30px;
}

.card-button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
}

.card-button:hover {
    background-color: #0056b3;
}
</style>
