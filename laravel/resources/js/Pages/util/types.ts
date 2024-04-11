
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
    updated_at: string
}

export type requestObj = {
    id: number,
    user_id: number,
    channel_id: number,
    name: string,
    status: 'pending' | 'rejected' | 'accepted'
}