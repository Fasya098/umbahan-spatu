<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter, useRoute } from 'vue-router';
import { toast } from 'vue3-toastify';

const tokos = ref([]);
const router = useRouter();
const route = useRoute();
const uuid = route.params.uuid;

async function checkAuthAndNavigate(tokoUuid: string, userId: string) {
    let customerId: string | null = null;

    try {
        // Check authentication status
        const response = await axios.get('/userpage/me');
        const customerId = response.data?.id ?? null;
        // If successful (no error thrown), user is authenticated
        router.push({
            name: 'userpage.form',
            params: {
                uuid: tokoUuid,
                userId: userId,
                id: customerId,
            }
        });
    } catch (error) {
        // If error, user is not authenticated
        // Store the intended destination for after login
        localStorage.setItem('intended_route', JSON.stringify({
            name: 'userpage.form',
            params: {
                uuid: tokoUuid,
                userId: userId,
                id: customerId,
            }
        }));

        // Redirect to login page
        router.push('/sign-in');
    }
}

function goBack() {
    router.push('/userpage');
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

    <div class="container-fluid" style="background-color: #f8f9fa; margin-bottom: 50px;">
        <div class="container">
            <div class="card border-0 p-4 rounded-4 shadow-lg bg-white">
                <div v-if="tokos.length" v-for="toko in tokos" :key="toko.uuid" class="store-card">
                    <!-- Store Header -->
                    <div class="row g-4 align-items-stretch">
                        <!-- Store Image -->
                        <div class="col-lg-4 col-md-5">
                            <div class="rounded-4 overflow-hidden shadow-sm h-100">
                                <img :src="getImageUrl(toko.foto_toko)" alt="Store Image" class="img-fluid w-90 h-90"
                                    style="object-fit: cover; min-height: 300px;">
                            </div>
                        </div>

                        <!-- Store Information -->
                        <div class="col-lg-8 col-md-7">
                            <div class="h-100 p-4 bg-light rounded-4 shadow">
                                <div class="d-flex flex-column h-100">
                                    <!-- Store Name -->
                                    <h3 class="mb-3 text-primary fw-bold">{{ toko.nama_toko }}</h3>

                                    <!-- Store Description -->
                                    <div class="mb-4">
                                        <p class="text-muted mb-0" style="line-height: 1.6; font-size: 1.2rem;">
                                            {{ toko.deskripsi }}
                                        </p>
                                    </div>

                                    <!-- Contact Details -->
                                    <div class="mt-auto">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-telephone-fill me-2 text-primary"></i>
                                                <span class="fw-medium" style="font-size: 1rem;">{{ toko.nomor_telepon
                                                    }}</span>
                                            </div>
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-geo-alt-fill me-2 mt-2 text-primary"></i>
                                                <span class="fw-medium" style="font-size: 1rem;">{{ toko.alamat
                                                    }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-10 px-2">
                        <button class="btn btn-danger px-3 py-2 rounded-3 shadow-sm" @click="goBack">
                            <i class="las la-angle-left" style="color: white;"></i>
                            Kembali
                        </button>
                        <button class="btn btn-primary px-3 py-2 rounded-3 shadow-sm"
                            @click="checkAuthAndNavigate(toko.uuid, toko.user_id)">
                            <i class="bi bi-cart-plus me-2"></i>
                            Pesan Disini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="footer-content">
            <div class="company-info">
                <img src="/media/logo-spatu-nobackground.png" alt="Logo Perusahaan" class="company-logo">
                <h2 class="text-white">Cuci Sepatu Bersih</h2>
                <p>Jl. Contoh No. 123, Kota Anda</p>
                <p>Telp: (021) 1234-5678</p>
                <p>Email: info@cucisepatubersih.com</p>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.footer {
    background-color: #333;
    color: white;
    padding: 2rem;
}

.footer-content {
    display: flex;
    justify-content: center;
}

.company-info {
    text-align: center;
}

.company-logo {
    width: 100px;
    height: auto;
    margin-bottom: 1rem;
}

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

/* .container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
} */

.container {
    margin-top: 30px;
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
