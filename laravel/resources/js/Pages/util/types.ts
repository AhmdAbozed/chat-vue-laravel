
export type channelObj = {
    id: number,
    name: string,
    unreadCount: number,
    type: 'private' | 'group'
}
export type msgObj = {
    id: number,
    user_id: number,
    channel_id: number,
    user: string,
    content: string,
    updated_at: string,
    file_name: string|null,
    file_type: string|null,
    file_size: number|null
}

export type requestObj = {
    id: number,
    user_id: number,
    channel_id: number,
    name: string,
    status: 'pending' | 'rejected' | 'accepted'
}

export type fileHeaderData = {
    fileToken: string,
    fileBucket?:string,
    fileUrl: string 
}