<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const shoes = ref([]); // Data sepatu
const router = useRouter();

// Fungsi untuk navigasi ke halaman form dengan menyertakan ID sepatu
const goToForm = (id: number) => {
    router.push({ path: '/userpage/form', query: { shoeId: id } });
};

// Fungsi untuk kembali ke halaman sebelumnya
function goBack() {
    router.go(-1);
}

// Fungsi untuk mendapatkan data sepatu dari API
const getShoesData = async () => {
    try {
        const response = await axios.get('/userpage/toko/get');
        shoes.value = response.data;
    } catch (error) {
        console.error('Error fetching shoes data:', error);
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

    <div class="p-10" style="height: 100vh; width: 100vw;">
        <div class="card rounded-3 w-100 h-100 p-4">
            <div class="flex-col" v-if="shoes.length" v-for="shoe in shoes" :key="shoe.id">
                <div class="d-flex flex-row justify-content-between">
                    <div class="w-40 h-80 rounded-3" style="width: 200px; height : 250px">
                        <img :src="getImageUrl(shoe.foto_toko)" alt="Sepatu" style="border-radius: 10px;"
                            class="shoe-image">
                    </div>
                    <div class="w-40 h-80 p-5 rounded-3" style="width: 950px; height : 300px">
                        <div class="text-center h-20">
                            <h1>{{ shoe.nama_toko }}</h1>
                        </div>
                        <br>
                        <div class="text-center h-20">
                            {{ shoe.deskripsi }}
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-row justify-content-between">
                    <div class="d-flex flex-row justify-content-between">
                        <div class="rounded-3" style="height: auto; width: 350px;">
                            <h5>No telepon : {{ shoe.nomor_telepon }}</h5>
                            <h5>Alamat : {{ shoe.alamat }}</h5>
                            <div style="margin-top: 59px;">
                                <button class="btn btn-danger" @click="goBack">Kembali</button>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-3" style="height: 50px; width: 150px; margin-top: 100px;">
                        <button class="btn btn-info" @click="goToForm(shoe.id)">Pesan Disini</button>
                    </div>
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
