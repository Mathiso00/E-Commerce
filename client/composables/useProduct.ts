import {IProduct} from "~/types/IProduct";

const config = useRuntimeConfig();

export async function getProducts(): Promise<any>{
    try {
        //const data = await $http.$get(`${config.apiBaseUrl}/products`);
        const data = await $http.$get(`https://localhost:8000/api/products`);
        //console.log(data);
        return data;
    }catch (e) {
       console.log(e)
    }
}

export async function getProduct(productId: number): Promise<any>{
    try {
        const data = await $http.$get(`https://localhost:8000/api/products/${productId}`);
        console.log(data);
        return data;
    }catch (e) {
       console.log(e)
    }
}