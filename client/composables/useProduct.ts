import {IProduct} from "~/types/IProduct";


export async function getProducts(): Promise<any>{
    try {
        const data = await $http.$get(`https://localhost:8000/api/products`);
        console.log(data);
        return data;
    }catch (e) {
       console.log(e)
    }
}