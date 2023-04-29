const config = useRuntimeConfig();

export async function signUp(
  firstname: string,
  lastname: string,
  email: string,
  password: string
): Promise<any> {
  try {
    const data = await $http.$post(`https://localhost:8000/api/register`, {
      body: { firstname, lastname, email, password },
    });
    return data;
  } catch (e) {
    console.log(e);
  }
}
export async function ShowUser() {}
