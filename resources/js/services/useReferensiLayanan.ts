import { useQuery } from "@tanstack/vue-query";
import axios from "@/libs/axios";

export function useReferensiLayanan(options = {}) {
    return useQuery({
        queryKey: ["ReferensiLayanan"],
        queryFn: async () =>
            await axios.get("/master/referensi/layanan/get").then((res: any) => res.data.data),
        ...options,
    });
}
