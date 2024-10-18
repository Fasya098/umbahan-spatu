import { useQuery } from "@tanstack/vue-query";
import axios from "@/libs/axios";

export function useUser(options = {}) {
    return useQuery({
        queryKey: ["User"],
        queryFn: async () =>
            await axios.get("/master/toko/get").then((res: any) => res.data.data),
        ...options,
    });
}
