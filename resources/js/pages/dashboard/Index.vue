<script setup lang="ts">
import { useRouter } from "vue-router"
import { ref, onMounted } from "vue"
import axios from "@/libs/axios";

const hotel = ref([])
const router = useRouter()
const local = localStorage.getItem('napbar')

const carouselOptions = {
  autoplay: true,
  interval: 3000,
  arrows: false,
  gap: 5,
  type: 'loop',
  height: '21rem',
  perPage: 2,
  perMove: 1,
  pagination: false,
  focus  : 'center',
}

const butt = () => {
  const redirect = localStorage.getItem('napbar');
  if (redirect) {
    // Menghapus bagian "/admin" dari URL
    const cleanedURL = redirect.replace('/admin', '');
    // Menghapus bagian "localhost:8000/" dari URL
    const cleanedURLWithoutHost = cleanedURL.replace(/^.*\/\/[^\/]+/, '');
    router.push(cleanedURLWithoutHost);
  }
}

// const getData = async () => {
//   try{
//     const getHotel = await axios.get('/master/hotel_image')
//     hotel.value = getHotel.data.data
//   } catch (err) {
//     console.error(err)
//   } 
// }

// onMounted(() => {
//   getData()
// })
</script>

<template>
  <main>
    <!-- <div class="card mb-10">
      <div class="card-body px-6">
        <div v-if="hotel.length > 0">
          <Splide :options="carouselOptions" class="shadow-sm bg-body-tertiary rounded">
            <SplideSlide v-for="(image, index) in hotel" :key="index">
              <img :src="`/storage/${image.image}`" class="img-fluid w-100 h-100 rounded" alt="Hotel Image">
            </SplideSlide>
          </Splide>
        </div>
      </div>
    </div> -->


    <div class="card mb-5">
      <div
        class="card-header rounded bgi-no-repeat bgi-size-cover bgi-position-y-top bgi-position-x-center align-items-start h-200px bg-primary">
        <h3 class="card-title align-items-start flex-column text-white pt-10">
          <span class="fw-bold fs-2x cursor">Navigation</span>
        </h3>
      </div>
      <div class="card-body mt-n20">
        <div class="mt-n20 position-relative">
          <div class="row g-3 g-lg-6">
            
            <div class="col-xl-4 col-sm-6">
              <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 border-success border-left-5 border-start d-block">
                <div class="symbol symbol-30px me-5 mb-8">
                  <span class="symbol-label">
                    <i class="ki-duotone ki-code fs-2x text-success">
                      <span class="path1"></span>
                      <span class="path2"></span>
                      <span class="path3"></span>
                      <span class="path4"></span>
                    </i>
                  </span>
                </div>
                <div class="m-0 d-flex justify-content-between align-items-center">
                  <div>
                    <span class="text-success fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1 cursor">
                    Profil Toko
                    </span>
                    <span class="text-gray-500 fw-semibold fs-6 cursor">Siapkan profil toko anda</span>
                  </div>
                  <router-link to="/dashboard/profile">
                    <i class="fa fa-chevron-right text-success fs-2"></i>
                  </router-link>
                </div>
              </div>
            </div>

            <div class="col-xl-4 col-sm-6">
              <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 border-warning border-left-5 border-start d-block">
                <div class="symbol symbol-30px me-5 mb-8">
                  <span class="symbol-label">
                    <i class="ki-duotone ki-gear fs-2x text-warning">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                  </span>
                </div>
                <div class="m-0 d-flex justify-content-between align-items-center">
                  <div>
                    <span class="text-warning fw-bolder cursor d-block fs-2qx lh-1 ls-n1 mb-1">
                    Pesanan
                    </span>
                    <span class="text-gray-500 fw-semibold cursor fs-6">Data pesanan</span>
                  </div>
                  <router-link to="/dashboard/master/pesanan">
                    <i class="fa fa-chevron-right text-warning fs-2"></i>
                  </router-link>
                </div>
              </div>
            </div>

            <div v-if="local" class="col-xl-4 col-sm-6">
              <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 border-info border-left-5 border-start d-block">
                <div class="symbol symbol-30px me-5 mb-8">
                  <span class="symbol-label">
                    <i class="ki-duotone ki-send fs-2x text-info">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                  </span>
                </div>
                <div class="m-0 d-flex justify-content-between align-items-center">
                  <div>
                    <span class="text-info fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1 cursor">
                    Continue Booking
                    </span>
                    <span class="text-gray-500 fw-semibold fs-6 cursor">go to booking page</span>
                  </div>
                  <i class="fa fa-chevron-right text-info fs-2 pointer" @click="butt"></i>
                </div>
              </div>
            </div>
            <div v-else class="col-xl-4 col-sm-6">
              <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 border-info border-left-5 border-start d-block">
                <div class="symbol symbol-30px me-5 mb-8">
                  <span class="symbol-label">
                    <i class="ki-duotone ki-send fs-2x text-info">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                  </span>
                </div>
                <div class="m-0 d-flex justify-content-between align-items-center">
                  <div>
                    <span class="text-info fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1 cursor">
                    Layanan
                    </span>
                    <span class="text-gray-500 fw-semibold fs-6 cursor">Tambah layanan</span>
                  </div>
                  <router-link to="/dashboard/master/layanan">
                    <i class="fa fa-chevron-right text-info fs-2"></i>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- <div class="form card mb-10">
      <div class="card-header align-items-center">
        <h2 class="mb-0">Dashboard Navigation</h2>
      </div>
      <div v-if="local" class="card-body">
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-10 py-5 bg-secondary">
          <input type="hidden">
          <button class="text-black w-100 btn" @click="butt">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Continue Booking</div>
            </div>
          </button>
          </input>
        </div>
      </div>
      <div class="card-body">
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-10 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/landing/page">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Landing Page</div>
            </div>
          </router-link>
          </input>
        </div>
      </div>
      <div class="card-body">
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-10 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/master/users">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">User</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Master - Users - User</div>
            </div>
          </router-link>
          </input>
        </div>
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-15 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/master/users/roles">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Role</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Master - Users - Role</div>
            </div>
          </router-link>
          </input>
        </div>
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-15 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/master/banner">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Banner</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Master - Banner</div>
            </div>
          </router-link>
          </input>
        </div>
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-15 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/master/brand">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Brand</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Master - Brand</div>
            </div>
          </router-link>
          </input>
        </div>
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-15 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/master/provinsi">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Province</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Master - Province</div>
            </div>
          </router-link>
          </input>
        </div>
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-15 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/master/kota">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">City</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Master - City</div>
            </div>
          </router-link>
          </input>
        </div>
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-15 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/master/destinasi">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Destination</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Master - Destination</div>
            </div>
          </router-link>
          </input>
        </div>
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-15 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/master/hotel_facility">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Hotel Facility</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Master - Hotel Facility</div>
            </div>
          </router-link>
          </input>
        </div>
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-15 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/master/room_facility">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Room Facility</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Master - Room Fasilitas</div>
            </div>
          </router-link>
          </input>
        </div>
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-15 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/master/hotel">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Hotel</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Master - Hotel</div>
            </div>
          </router-link>
          </input>
        </div>
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-15 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/master/article">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Article</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Master - Article</div>
            </div>
          </router-link>
          </input>
        </div>
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-15 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/promo">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Promo</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Website - Promo</div>
            </div>
          </router-link>
          </input>
        </div>
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-15 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/promotion">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Promotion</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Website - Promotion</div>
            </div>
          </router-link>
          </input>
        </div>
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-15 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/booking">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Booking</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Website - Booking</div>
            </div>
          </router-link>
          </input>
        </div>
        <div class="max-w-xs rounded overflow-hidden shadow-lg mt-15 py-5 bg-secondary">
          <input type="hidden">
          <router-link class="text-black" to="/admin/dashboard/setting">
            <div class="px-10 py-7">
              <div class="fs-2 fw-bold text-center text-body">Setting</div>
              <div class="text-base fw-medium pt-4 text-center text-body">Dashboard - Website - Setting</div>
            </div>
          </router-link>
          </input>
        </div>
      </div>
    </div> -->
  </main>
</template>

<style>
.pointer{
  cursor: pointer;
}

.cursor{
  cursor: default;
}
</style>