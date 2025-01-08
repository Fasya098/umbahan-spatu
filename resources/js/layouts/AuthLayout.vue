<template>
	<!--begin::Authentication Layout -->
	<div class="d-flex flex-column flex-column-fluid flex-lg-row justify-content-center auth-layout"
		:style="`background-image: url('${setting?.bg_auth}');`">
		<!--begin::Body-->
		<div
			class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end py-10">
			<!--begin::Card-->
			<div style="background-color: grey;"
				class="d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-md-20 w-100  card-container">
				<!--begin::Wrapper-->
				<div class="d-flex flex-center flex-column flex-column-fluid px-10 py-30 py-md-0">
					<router-view></router-view>
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Card-->
		</div>
		<!--end::Body-->
	</div>
	<!--end::Authentication Layout -->
</template>

<script lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, onMounted } from "vue";
import LayoutService from "@/core/services/LayoutService";
import { useBodyStore } from "@/stores/body";
import { useSetting } from "@/services";

export default defineComponent({
	name: "auth-layout",
	setup() {
		const store = useBodyStore();
		const { data: setting = {} } = useSetting();

		onMounted(() => {
			LayoutService.emptyElementClassesAndAttributes(document.body);

			store.addBodyClassname("app-blank");
			store.addBodyClassname("bg-body");
		});

		return {
			getAssetPath,
			setting
		};
	},
});
</script>

<style scoped>
.auth-layout {
	background-size: cover;
	/* Gambar akan menyesuaikan ukuran elemen */
	background-position: center;
	/* Gambar akan dipusatkan */
	background-repeat: no-repeat;
	/* Hindari pengulangan gambar */
	height: 100vh;
	/* Pastikan background menutupi seluruh tinggi viewport */
	width: 100%;
	/* Pastikan background menutupi seluruh lebar viewport */
}

.card-container {
	background-color: rgba(128, 128, 128, 0.8);
	/* Opacity untuk warna abu-abu */
	border-radius: 10px;
	/* Rounded edges */
}
</style>
