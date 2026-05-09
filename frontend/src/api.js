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
  
  if (!res.ok) {
    const raw = await res.text()
    try {
      const json = JSON.parse(raw)
      const msg = json.message || json.error || (Array.isArray(json.errors) ? JSON.stringify(json.errors) : null)
      throw new Error(msg || raw || `Error HTTP ${res.status}`)
    } catch (e) {
      if (e instanceof SyntaxError) throw new Error(raw)
      throw e
    }
  }
  return await res.json()
}

function buildApiUrl(endpoint) {
  return import.meta.env.VITE_API_URL
    ? import.meta.env.VITE_API_URL + endpoint
    : 'http://localhost:8000' + endpoint
}

function authHeaders(extra = {}) {
  const headers = {
    Accept: 'application/json',
    ...extra,
  }
  const token = localStorage.getItem('token')
  if (token) {
    headers.Authorization = `Bearer ${token}`
  }
  return headers
}

/** POST JSON y parsea respuesta JSON (p. ej. formato=json para vista previa / PDF). */
export async function apiPostJson(endpoint, body = {}) {
  const url = buildApiUrl(endpoint)
  const headers = authHeaders({ 'Content-Type': 'application/json' })

  const res = await fetch(url, {
    method: 'POST',
    headers,
    body: JSON.stringify(body),
  })

  if (res.status === 401) {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    window.location.href = '/'
    throw new Error('No autorizado')
  }

  if (!res.ok) {
    const raw = await res.text()
    try {
      const json = JSON.parse(raw)
      const msg = json.message || json.error || (Array.isArray(json.errors) ? JSON.stringify(json.errors) : null)
      throw new Error(msg || raw || `Error HTTP ${res.status}`)
    } catch (e) {
      if (e instanceof SyntaxError) throw new Error(raw)
      throw e
    }
  }

  return await res.json()
}

/** POST que descarga un archivo (CSV, XLS). */
export async function apiDownloadPost(endpoint, body = {}, fallbackFilename = 'descarga.dat') {
  const url = buildApiUrl(endpoint)
  const headers = authHeaders({ 'Content-Type': 'application/json' })

  const res = await fetch(url, {
    method: 'POST',
    headers,
    body: JSON.stringify(body),
  })

  if (res.status === 401) {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    window.location.href = '/'
    throw new Error('No autorizado')
  }

  if (!res.ok) {
    const raw = await res.text()
    try {
      const json = JSON.parse(raw)
      const msg = json.message || json.error || (Array.isArray(json.errors) ? JSON.stringify(json.errors) : null)
      throw new Error(msg || raw || `Error HTTP ${res.status}`)
    } catch (e) {
      if (e instanceof SyntaxError) throw new Error(raw)
      throw e
    }
  }

  const blob = await res.blob()
  let filename = fallbackFilename
  const cd = res.headers.get('Content-Disposition')
  if (cd) {
    const m =
      /filename\*=(?:UTF-8'')?([^;]+)|filename="([^"]+)"|filename=([^;\s]+)/i.exec(cd)
    if (m) {
      const raw = (m[1] || m[2] || m[3] || '').trim().replace(/^["']|["']$/g, '')
      try {
        filename = decodeURIComponent(raw)
      } catch {
        filename = raw
      }
    }
  }

  const blobUrl = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = blobUrl
  a.download = filename
  document.body.appendChild(a)
  a.click()
  a.remove()
  URL.revokeObjectURL(blobUrl)
}
