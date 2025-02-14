<template>
  <div>
    <div class="background" id="base">
      <div class="container mt-5">
        <div class="card shadow">
          <div class="">
          </div>
          <div class="d-flex align-items-center px-10 mt-10">
            <button class="btn btn-danger px-3 py-2 rounded-3 shadow-sm " @click="goBack">
              <i class="las la-angle-left" style="color: white; margin-left: 3px;"></i>
            </button>
            <div class="text-center flex-grow-1">
              <h3 class="mb-0">Form Pendaftaran Mitra</h3>
            </div>
          </div>

          <div class="card-body">
            <form @submit.prevent="submitForm">
              <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="name" v-model="form.name"
                  placeholder="Masukkan Nama Lengkap" required />
              </div>

              <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" class="form-control" id="alamat" v-model="form.alamat"
                  placeholder="Masukkan Alamat Lengkap" required />
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" v-model="form.email" placeholder="Masukkan email"
                  required />
              </div>

              <div class="mb-3">
                <label for="phone" class="form-label">No.Telepon</label>
                <input type="tel" class="form-control" id="phone" v-model="form.phone" placeholder="Masukkan No Telepon"
                  required />
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                  <input :type="showPassword ? 'text' : 'password'" class="form-control hide-password-reveal" id="password" 
                    v-model="form.password" placeholder="Masukkan Password" required />
                  <span class="input-group-text password-toggle" @click="showPassword = !showPassword">
                    <i class="las" :class="showPassword ? 'la-eye' : 'la-eye-slash'"></i>
                  </span>
                </div>
              </div>

              <div class="mb-6">
                <label for="password_confirmation" class="form-label">Password Konfirmasi</label>
                <div class="input-group">
                  <input :type="showConfirmPassword ? 'text' : 'password'" class="form-control hide-password-reveal" 
                    id="password_confirmation" v-model="form.password_confirmation" 
                    placeholder="Masukkan Password Konfirmasi" required />
                  <span class="input-group-text password-toggle" @click="showConfirmPassword = !showConfirmPassword">
                    <i class="las password-icon" :class="showConfirmPassword ? 'la-eye' : 'la-eye-slash'"></i>
                  </span>
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100" :disabled="isSubmitting">
                <span v-if="isSubmitting" class="spinner-border spinner-border-sm"></span>
                <span v-else>Submit</span>
              </button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import axios from "@/libs/axios";
import Swal from 'sweetalert2';
import { useRouter } from 'vue-router';

const router = useRouter();
const isSubmitting = ref(false);
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = reactive({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
})

const submitForm = async () => {
  if (isSubmitting.value) return;
  isSubmitting.value = true;
  try {
    const response = await axios.post('/userpage/store', form);
    console.log(response.data);

    // Menggunakan SweetAlert2 untuk menampilkan alert sukses
    const { isConfirmed } = await Swal.fire({
      icon: 'success',
      title: 'Pendaftaran sedang ditinjau!',
      text: 'Tunggu email masuk pada hp anda',
      confirmButtonText: 'Oke',
    });

    if (isConfirmed) {
      router.push('/userpage');
    }

    // Kamu bisa menambahkan logika untuk melakukan tindakan setelah sukses (misalnya redirect)
  } catch (error) {
    console.error(error);

    // Menggunakan SweetAlert2 untuk menampilkan alert kesalahan
    await Swal.fire({
      icon: 'error',
      title: 'Terjadi kesalahan!',
      text: 'Silakan coba lagi.',
      confirmButtonText: 'Oke'
    });
  } finally {
    isSubmitting.value = false;
  }
}

function goBack() {
  window.history.back();
}
</script>

<style scoped>
.password-icon {
  font: 1,5rem;
}

.container {
  max-width: 600px;
}

.background {
  background-image: url('/media/misc/bg-sepatu.png');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  height: 100vh;
  width: 100vw;
}

#base {
  display: flex;
  align-items: center;
  justify-content: center;
}

.card {
  border-radius: 10px;
}

.btn-primary {
  background-color: #4a90e2;
  border-color: #4a90e2;
}

.card-header {
  background-color: #f7f7f7;
  border-bottom: none;
}

.hide-password-reveal::-ms-reveal,
.hide-password-reveal::-ms-clear {
  display: none !important;
}

.hide-password-reveal::-webkit-contacts-auto-fill-button,
.hide-password-reveal::-webkit-credentials-auto-fill-button,
.hide-password-reveal::-webkit-inner-spin-button,
.hide-password-reveal::-webkit-outer-spin-button {
  display: none !important;
}

.form-control {
  border-radius: 8px;
}
</style>