export function formatFileSize(bytes: number) {
    if (bytes < 0) {
        return "";
    }

    const units = ["Bytes", "KB", "MB", "GB", "TB"];
    const threshold = 1024;

    let i = 0;
    let size = bytes;

    while (size >= threshold && i < units.length - 1) {
        size /= threshold;
        i++;
    }

    return `${size.toFixed(1)} ${units[i]}`;
}

export function isImage(fileName: string) {
    
    const validImageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];
    const extension = fileName.split('.').pop()!.toLowerCase();

    return validImageExtensions.includes(extension);
  }
