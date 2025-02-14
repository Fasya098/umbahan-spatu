<template>
  <div>
    <div class="background" id="base">
      <div class="container mt-5">
        <div class="card shadow">
          <div class="d-flex align-items-center px-10 mt-10">
            <button class="btn btn-danger px-3 py-2 rounded-3 shadow-sm" @click="goBack">
              <i class="las la-angle-left" style="color: white; margin-left: 3px;"></i>
            </button>
            <div class="text-center flex-grow-1">
              <h3 class="mb-0">Form Pendaftaran Customer</h3>
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
                <input type="password" class="form-control" id="password" v-model="form.password"
                  placeholder="Masukkan Password" required />
              </div>

              <div class="mb-6">
                <label for="password_confirmation" class="form-label">Password Konfirmasi</label>
                <input type="password" class="form-control" id="password_confirmation"
                  v-model="form.password_confirmation" placeholder="Masukkan Password Konfirmasi" required />
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

    <!-- Enhanced Modal OTP -->
    <div v-if="showOtpModal" class="modal-overlay" @click.self="closeOtpModal">
      <div class="modal-content bg-white p-4 rounded-4 shadow-lg" style="max-width: 400px;">
        <div class="modal-header border-0 pb-0">
          <h4 class="modal-title fw-bold text-center w-100">Verifikasi Email Anda</h4>
          <button type="button" class="btn-close" @click="closeOtpModal"></button>
        </div>

        <div class="modal-body py-4">
          <div class="text-center mb-4">
            <!-- <img src="/api/placeholder/80/80" alt="OTP Icon" class="mb-3"> -->
            <!-- <h5 class="fw-bold">Verifikasi Email Anda</h5> -->
            <p class="text-muted">Kode OTP telah dikirim ke email Anda</p>
          </div>

          <div class="d-flex justify-content-center gap-4 mb-4">
            <input type="text" v-model="otp" class="form-control form-control-lg text-center"
              placeholder="Masukkan kode OTP" maxlength="6" pattern="\d*" />
            <!-- <label>Kode OTP</label> -->
          </div>

          <div class="d-grid gap-4">
            <button class="btn btn-primary btn-lg py-3 rounded-3 shadow-sm fw-bold" @click="verifyOtp">
              Verifikasi
            </button>
          </div>

          <div class="text-center mt-4">
            <p class="text-muted mb-2">Belum menerima kode?</p>
            <button class="btn btn-link text-primary text-decoration-none fw-bold" @click="resendOtp">
              <i class="las la-redo-alt me-1"></i> Kirim Ulang OTP
            </button>
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
const showOtpModal = ref(false);
const otp = ref('');
const isSubmitting = ref(false);

const form = reactive({
  name: '',
  alamat: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
});

  const submitForm = async () => {
    if (isSubmitting.value) return;
    isSubmitting.value = true;

  try {
    const response = await axios.post('/userpage/register', form);
    console.log(response.data);

    const { isConfirmed } = await Swal.fire({
      icon: 'success',
      title: 'Pendaftaran sedang ditinjau!',
      text: 'Silahkan cek email anda',
      confirmButtonText: 'Oke',
    });

    if (isConfirmed) {
      showOtpModal.value = true;
    }
  } catch (error) {
    console.error(error);
    await Swal.fire({
      icon: 'error',
      title: 'Terjadi kesalahan!',
      text: 'Silakan coba lagi.',
      confirmButtonText: 'Oke'
    });
  } finally {
    isSubmitting.value = false;
  }
};

const closeOtpModal = () => {
  showOtpModal.value = false;
  otp.value = '';
};

const resendOtp = async () => {
  try {
    const loadingAlert = Swal.fire({
      title: 'Mengirim ulang OTP...',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    await axios.post('/userpage/resendOtp', {
      email: form.email,
    });

    await loadingAlert.close();

    await Swal.fire({
      icon: 'success',
      title: 'OTP Terkirim!',
      text: 'Silakan cek email Anda',
      confirmButtonText: 'Oke'
    });
  } catch (error) {
    console.error('Resend OTP Error:', error);
    await Swal.fire({
      icon: 'error',
      title: 'Gagal mengirim OTP',
      text: 'Silakan coba lagi setelah beberapa saat',
      confirmButtonText: 'Oke'
    });
  }
};

const verifyOtp = async () => {
  try {
    if (!otp.value || otp.value.length !== 6) {
      await Swal.fire({
        icon: 'error',
        title: 'Invalid OTP',
        text: 'Please enter a valid 6-digit OTP code',
        confirmButtonText: 'OK'
      });
      return;
    }

    const loadingAlert = Swal.fire({
      title: 'Verifying OTP...',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    const response = await axios.post('/userpage/checkOtp', {
      email: form.email,
      otp_code: otp.value.trim()
    });

    await loadingAlert.close();

    if (response.data.status) {
      await Swal.fire({
        icon: 'success',
        title: 'Verifikasi Berhasil!',
        text: response.data.message,
        confirmButtonText: 'Oke',
      });
      router.push('/sign-in');
    } else {
      throw new Error(response.data.message);
    }
  } catch (error) {
    console.error('OTP Verification Error:', error);

    const errorMessage = error.response?.data?.message ||
      'Terjadi kesalahan saat verifikasi OTP';

    await Swal.fire({
      icon: 'error',
      title: 'Verifikasi Gagal',
      text: errorMessage,
      confirmButtonText: 'Oke'
    });

    closeOtpModal();
    resetForm();
  }
};

const resetForm = () => {
  form.name = '';
  form.alamat = '';
  form.email = '';
  form.phone = '';
  form.password = '';
  form.password_confirmation = '';
};

function goBack() {
  window.history.back();
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
}

.modal-content {
  max-width: 400px;
  width: 90%;
  margin: 20px;
  animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
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

.btn-primary:disabled {
  background-color: #a8c7eb;
  border-color: #a8c7eb;
}

.card-header {
  background-color: #f7f7f7;
  border-bottom: none;
}

.form-control {
  border-radius: 8px;
}

.form-control:focus {
  border-color: #4a90e2;
  box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25);
}
</style>