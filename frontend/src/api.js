// Utilidad para hacer peticiones al backend
export async function apiRequest(endpoint, method = 'GET', data = null) {
  const url = import.meta.env.VITE_API_URL
    ? import.meta.env.VITE_API_URL + endpoint
    : 'http://localhost:8000' + endpoint

  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  }
  
  const token = localStorage.getItem('token')
  if (token) {
    headers['Authorization'] = `Bearer ${token}`
  }

  const options = {
    method,
    headers,
  }
  
  if (data) {
    options.body = JSON.stringify(data)
  }
  
  const res = await fetch(url, options)
  
  if (res.status === 401) {
    // Si el token expira o es inválido, forzar logout
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    window.location.href = '/'
    throw new Error('No autorizado')
  }
  
  if (!res.ok) throw new Error(await res.text())
  return await res.json()
}
