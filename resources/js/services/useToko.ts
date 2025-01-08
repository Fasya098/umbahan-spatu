import { useQuery } from "@tanstack/vue-query";
import axios from "@/libs/axios";

export function useToko(options = {}) {
    return useQuery({
        queryKey: ["Toko"],
        queryFn: async () =>
            await axios.get("/master/toko/get").then((res: any) => res.data.data),
        ...options,
    });
}
