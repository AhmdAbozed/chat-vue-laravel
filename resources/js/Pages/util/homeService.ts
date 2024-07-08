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
export async function getMsgs(channel: channelObj, withFileToken: boolean, reset_unread: boolean) {
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
        const endpoint = location.protocol + "//" + location.host + "/_api/chats/" +channel.id + "/messages?withFileToken="+ `${withFileToken ? 1 : 0}`+`&resetUnread=${reset_unread?1:0}`;
        const res = await fetch(endpoint, options);
        if (res.status === 200) {
            const toJson = await res.json();
            console.log(toJson)
            return toJson
        }
    }
}


export async function sendMsg(e: Event, channel_id: number, uploadedFile?: File | null) {
    e.preventDefault();
    const target = e.target as any
    const submission = new FormData();
    submission.append('message', target.elements.message.value)
    if (uploadedFile) { submission.append('file', uploadedFile as Blob); }
    const options: RequestInit = {
        method: "POST",
        headers: {
            'Access-Control-Allow-Credentials': 'true',
            //without decoding, %3D in token isn't converted to =, which causes token mismatch
            'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!),
            'Accept': 'multipart/form-data'
        },
        credentials: "include",
        body: submission
    };

    (e.target! as HTMLFormElement).reset();
    const endpoint = location.protocol + "//" + location.host + "/_api/chats/" + channel_id + "/messages";
    const res = await fetch(endpoint, options);
    const json = await res.json()
    if (res.status === 200) {
        console.log('message sent', json);
    }
}
