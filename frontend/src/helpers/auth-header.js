export function authHeader() {
    let token = localStorage.getItem('token');

    if (token) {
        return { 'X-AUTH-TOKEN': token };
    } else {
        return {};
    }
}