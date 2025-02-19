<template>
  <div class="shoe-cleaning-website">
    <!-- Navbar -->
    <nav class="navbar">
      <div class="navbar-container">
        <router-link to="/userpage" class="nav-item">Home</router-link>
        <router-link to="/cek-pesanan" class="nav-item">Cek Pesanan</router-link>
      </div>
      <div class="navbar-actions">
        <button v-if="!userLocation" class="btn-join" @click="getUserLocation">Dapatkan lokasi anda</button>
        <input type="text" v-model="searchQuery" @input="filterStores" placeholder="Cari nama toko..."
          class="search-input" />
        <button class="btn-join" @click="navigateToMitra">Bergabung dengan kami</button>
      </div>
    </nav>

    <!-- Content -->
    <main class="content">
      <div class="card-container">
        <div v-if="filteredTokos.length" class="cards-wrapper">
          <div v-for="toko in paginatedTokos" :key="toko.uuid" class="shoe-card">
            <img :src="getImageUrl(toko.foto_toko)" alt="Sepatu" class="shoe-image">
            <div class="shoe-info">
              <h3>{{ toko.nama_toko }}</h3>
              <p>Alamat: {{ toko.alamat }}</p>
              <div class="card-footer">
                <p v-if="userLocation">Jarak: {{ toko.jarak.toFixed(2) }} km</p>
                <button class="btn btn-info btn-sm" @click="navigateToStore(toko.uuid)">Lihat Toko</button>
              </div>
            </div>
          </div>
        </div>
        <p v-else class="loading-text">{{ tokos.length === 0 ? 'Loading data...' : 'Tidak ada toko ditemukan' }}</p>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="pagination-container mt-4 flex justify-center gap-2">
          <button @click="currentPage--" :disabled="currentPage === 1"
            class="px-4 py-2 bg-gray-200 rounded-lg disabled:opacity-50">
            Previous
          </button>
          <button v-for="page in totalPages" :key="page" @click="currentPage = page" :class="[
            'px-4 py-2 rounded-lg',
            currentPage === page ? 'bg-gray-500' : 'bg-gray-200'
          ]">
            {{ page }}
          </button>
          <button @click="currentPage++" :disabled="currentPage === totalPages"
            class="px-4 py-2 bg-gray-200 rounded-lg disabled:opacity-50">
            Next
          </button>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-content">
        <div class="company-info">
          <img src="/media/logo-spatu-nobackground.png" alt="Logo Perusahaan" class="company-logo">
          <h2 class="text-white">Cuci Sepatu Bersih</h2>
          <p>Telp: (021) 1234-5678</p>
          <p>Email: info@cucisepatubersih.com</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const tokos = ref([]);
const searchQuery = ref('');
const userLocation = ref<{ lat: number; lng: number } | null>(null);
const currentPage = ref(1);
const itemsPerPage = 12;

// Computed property for total pages
const totalPages = computed(() =>
  Math.ceil(filteredTokos.value.length / itemsPerPage)
);

// Computed property for paginated stores
const paginatedTokos = computed(() => {
  const startIndex = (currentPage.value - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  return filteredTokos.value.slice(startIndex, endIndex);
});

// Watch for changes in search query to reset pagination
watch(searchQuery, () => {
  currentPage.value = 1;
});

const saveLocation = (location: { lat: number; lng: number }) => {
  localStorage.setItem('userLocation', JSON.stringify(location));
};

const loadSavedLocation = () => {
  const savedLocation = localStorage.getItem('userLocation');
  if (savedLocation) {
    userLocation.value = JSON.parse(savedLocation);
    calculateDistances();
  }
};

const filteredTokos = computed(() => {
  if (!searchQuery.value) return tokos.value;
  const query = searchQuery.value.toLowerCase();
  return tokos.value.filter(toko =>
    toko.nama_toko.toLowerCase().includes(query) ||
    toko.alamat.toLowerCase().includes(query)
  );
});

const getUserLocation = () => {
  if (!navigator.geolocation) {
    alert("Geolocation tidak didukung oleh browser ini.");
    return;
  }

  if (confirm("Izinkan akses lokasi Anda untuk menampilkan toko terdekat?")) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const location = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        userLocation.value = location;
        saveLocation(location);
        calculateDistances();
      },
      (error) => {
        alert("Gagal mendapatkan lokasi: " + error.message);
      }
    );
  } else {
    alert("Anda perlu mengaktifkan lokasi untuk fitur ini.");
  }
};

const calculateDistance = (lat1: number, lng1: number, lat2: number, lng2: number): number => {
  const R = 6371;
  const dLat = (lat2 - lat1) * (Math.PI / 180);
  const dLng = (lng2 - lng1) * (Math.PI / 180);

  const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
    Math.sin(dLng / 2) * Math.sin(dLng / 2);

  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c;
};

const calculateDistances = () => {
  if (!userLocation.value) return;

  tokos.value.forEach(toko => {
    if (toko.latitude && toko.longitude) {
      toko.jarak = calculateDistance(
        userLocation.value.lat,
        userLocation.value.lng,
        toko.latitude,
        toko.longitude
      );
    } else {
      toko.jarak = 0;
    }
  });

  tokos.value.sort((a, b) => a.jarak - b.jarak);
};

const navigateToMitra = () => {
  router.push('/userpage/mitra');
};

const getShoesData = async () => {
  try {
    const response = await axios.get('/userpage/toko/get');
    tokos.value = response.data;
    if (userLocation.value) {
      calculateDistances();
    }
  } catch (error) {
    console.error('Error fetching shoes data:', error);
  }
};

const getImageUrl = (path: string) => {
  return path ? `/storage/${path}` : '/placeholder-shoe.jpg';
};

function navigateToStore(uuid) {
  router.push({
    name: 'userpage.store',
    params: { uuid }
  });
};

onMounted(() => {
  loadSavedLocation();
  getShoesData();

  if (!userLocation.value) {
    getUserLocation();
  }
});
</script>

<style scoped>
.pagination-container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 2rem;
  gap: 0.5rem;
}

.pagination-container button {
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  transition: all 0.2s;
}

.pagination-container button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.cards-wrapper {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1rem;
  padding: 1rem;
}

.nav {
  display: flex;
  flex-direction: row;
  width: 100%;
  justify-content: space-between;
}

.shoe-cleaning-website {
  font-family: Arial, sans-serif;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #222;
  padding: 1rem 2rem;
  width: 100%;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}

.navbar-container {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.nav-item {
  text-decoration: none;
  color: white;
  font-weight: 500;
  transition: color 0.3s ease;
}

.nav-item:hover {
  color: #00bcd4;
}

.navbar-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.search-input {
  padding: 0.5rem 1rem;
  border: 1px solid #555;
  border-radius: 8px;
  outline: none;
  background-color: #333;
  color: white;
  transition: all 0.3s ease;
}

.search-input:focus {
  border-color: #00bcd4;
}

.btn-join {
  background-color: #00bcd4;
  color: white;
  border: none;
  padding: 0.6rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.3s ease;
}

.btn-join:hover {
  background-color: #0097a7;
}

.search-location-wrapper {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex: 1;
}

.search-join-container {
  display: flex;
  align-items: center;
  gap: 1rem;
  justify-content: space-between;
  margin: 0 auto;
}

/* .search-input {
  padding: 0.5rem 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.95rem;
  width: 200px;
  background-color: white;
} */

.search-input:focus {
  outline: none;
  border-color: #17a2b8;
  box-shadow: 0 0 0 2px rgba(23, 162, 184, 0.2);
}

.btn-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  border: none;
  background-color: #4CAF50;
  color: white;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.95rem;
}

.btn-location:hover {
  background-color: #45a049;
  transform: translateY(-1px);
}

.content {
  padding: 2rem;
  min-height: 50vh;
}

.card-container {
  max-width: 1450px;
}

.cards-wrapper {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.shoe-card {
  flex: 0 1 185px;
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease-in-out;
}

.shoe-card:hover {
  transform: translateY(-5px);
}

.shoe-image {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

.shoe-info {
  padding: 1.25rem;
}

.shoe-info h3 {
  color: #333;
  font-size: 1.25rem;
  margin: 0 0 0.5rem 0;
}

.shoe-info p {
  color: #666;
  font-size: 0.95rem;
  margin: 0.5rem 0;
}

.card-footer {
  margin-top: 1rem;
}

.btn.btn-info {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  border: none;
  background-color: #17a2b8;
  color: white;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn.btn-info:hover {
  background-color: #138496;
}

.loading-text {
  text-align: center;
  font-size: 1.1rem;
  color: #666;
  padding: 2rem;
}

.footer {
  background-color: #333;
  color: white;
  padding: 2rem;
  margin-top: 3rem;
}

.footer-content {
  display: flex;
  justify-content: center;
}

.company-info {
  text-align: center;
}

.company-logo {
  width: 85px;
  height: auto;
  margin-bottom: 2rem;
}

@media (max-width: 768px) {
  .shoe-card {
    flex: 0 1 100%;
    max-width: 400px;
  }

  .card-container {
    padding: 0 0.5rem;
  }

  .cards-wrapper {
    gap: 1rem;
  }

  .search-location-wrapper {
    flex-direction: column;
    width: 100%;
  }

  .search-join-container {
    flex-direction: column;
    gap: 1rem;
  }

  .btn-location {
    width: 100%;
    justify-content: center;
  }

  .search-input {
    width: 100%;
  }
}
</style>