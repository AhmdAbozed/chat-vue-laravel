async function getXsrf() {
    const options: RequestInit = {
        method: "GET",
        headers: {
            'Content-Type':'application/json',
            'Access-Control-Allow-Credentials': 'true'
        },
        credentials: "include",
    }
    const endpoint = location.protocol+"//"+location.host+"/sanctum/csrf-cookie"
    const res = await fetch(endpoint, options);
    console.log(res.status)

    if (res.status == 204) {
        console.log(decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!))
        return res.headers.get('set-cookie')
    }
    else {
        console.log("invalid credentials: " + res.status)
    }
}
export default getXsrf;