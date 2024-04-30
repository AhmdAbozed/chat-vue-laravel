import type { channelObj } from "./types";

export async function getChannels() {
    const options: RequestInit = {
        method: "GET",
        headers: {
            "Accept": "application/json",
            'Content-Type': 'application/json',
            'Access-Control-Allow-Credentials': 'true',
            'Access-Control-Allow-Origin': 'true',
        },
        credentials: "include",
    }
    const endpoint = location.protocol + "//" + location.host + "/_api/chats/";
    //const endpoint = 'https://api.backblazeb2.com/b2api/v3/b2_authorize_account'
    console.log(endpoint)
    const res = await fetch(endpoint, options);
    if (res.status === 200) {
        const toJson = await res.json();
        return toJson
    }
    else return;
}
export async function getMsgs(channel: channelObj, withFileToken: boolean) {
    if (channel) {
        const options: RequestInit = {
            method: "GET",
            headers: {
                'Content-Type': 'application/json',
                'Access-Control-Allow-Credentials': 'true',
                'Accept': 'application/json'
            },
            credentials: "include",
        }
        const endpoint = location.protocol + "//" + location.host + "/_api/chats/" +channel.id + "/messages?withFileToken="+1;
        const res = await fetch(endpoint, options);
        if (res.status === 200) {
            const toJson = await res.json();
            console.log(toJson)
            return toJson
        }
    }

}
