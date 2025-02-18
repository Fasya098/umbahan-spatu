<script setup lang="ts">
import { useRouter } from "vue-router"
import { ref, onMounted } from "vue"
import axios from "@/libs/axios";
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();
const userId = auth.user.role_id;
const router = useRouter()
const local = localStorage.getItem('napbar')

const butt = () => {
  const redirect = localStorage.getItem('napbar');
  if (redirect) {
    const cleanedURL = redirect.replace('/admin', '');
    const cleanedURLWithoutHost = cleanedURL.replace(/^.*\/\/[^\/]+/, '');
    router.push(cleanedURLWithoutHost);
  }
}
</script>

<template>
  <main>
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
              <div
                class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 border-success border-left-5 border-start d-block">
                <div class="symbol symbol-30px me-5 mb-8">
                  <span class="symbol-label">
                    <i class="ki-duotone ki-user fs-2x text-success">
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
                      {{ userId === 2 ? 'Profil Toko' : 'Terima Mitra' }}
                    </span>
                    <span class="text-gray-500 fw-semibold fs-6 cursor">
                      {{ userId === 2 ? 'Siapkan profil toko anda' : 'Kelola permintaan mitra baru' }}
                    </span>
                  </div>
                  <router-link :to="userId === 2 ? '/dashboard/profile' : '/dashboard/master/terima'">
                    <i class="fa fa-chevron-right text-success fs-2"></i>
                  </router-link>
                </div>
              </div>
            </div>

            <div class="col-xl-4 col-sm-6">
              <div
                class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 border-warning border-left-5 border-start d-block">
                <div class="symbol symbol-30px me-5 mb-8">
                  <span class="symbol-label">
                    <i class="fas fa-store fs-2x text-warning">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                  </span>
                </div>
                <div class="m-0 d-flex justify-content-between align-items-center">
                  <div>
                    <span class="text-warning fw-bolder cursor d-block fs-2qx lh-1 ls-n1 mb-1">
                      {{ userId === 2 ? 'Pesanan' : 'Toko' }}
                    </span>
                    <span class="text-gray-500 fw-semibold cursor fs-6">
                      {{ userId === 2 ? 'Data pesanan' : 'Data toko' }}
                    </span>
                  </div>
                  <router-link :to="userId === 2 ? '/dashboard/master/pesanan' : '/dashboard/master/toko'">
                    <i class="fa fa-chevron-right text-success fs-2"></i>
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
                    <i class="ki-duotone ki-gear fs-2x text-info">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                  </span>
                </div>
                <div class="m-0 d-flex justify-content-between align-items-center">
                  <div>
                    <span class="text-info fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1 cursor">
                      {{ userId === 2 ? 'Layanan' : 'Terima Layanan' }}
                    </span>
                    <span class="text-gray-500 fw-semibold fs-6 cursor">
                      {{ userId === 2 ? 'Tambah layanan' : 'Kelola layanan baru' }}
                    </span>
                  </div>
                  <router-link :to="userId === 2 ? '/dashboard/master/layanan' : '/dashboard/master/terima/layanan'">
                    <i class="fa fa-chevron-right text-info fs-2"></i>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<style>
.pointer {
  cursor: pointer;
}

.cursor {
  cursor: default;
}
</style>