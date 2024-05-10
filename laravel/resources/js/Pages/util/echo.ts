import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const PusherRef = Pusher;

//Private channels need key from backend to connect to pusher from client-side
//Default echo authorizer doesn't add X-XSRF or cookies, hence the custom authorizer
export const EchoObj = new Echo({
    broadcaster: 'pusher',
    key: "c5ee73fab227563f76ec",
    cluster: "eu",
    forceTLS: true,
    authorizer: (channel: any, options: any) => {
        return {
            authorize: (socketId: any, callback: any) => {
                fetch(location.protocol + "//" + location.host + '/broadcasting/auth', {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        'Content-Type': 'application/json',
                        'Access-Control-Allow-Credentials': 'true',
                        'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!)
                    },
                    credentials: "include",

                    body: JSON.stringify({
                        socket_id: socketId,
                        channel_name: channel.name
                    })
                }).then((response: any) => {
                    return response.json()
                }).then((response: any) => {
                    callback(null, response);
                }).catch(error => {
                    callback(error);
                })
            }
        };
    },
})