self.onmessage = async (e) => {

  const msg = e.data;
  if (!msg || msg.type !== 'START') return;

  const {
    url, RAM, RAM_O_EXP = 'RAM', Consulta,
    pageSize,
    totalTramites,
    timeoutMs = 420_000,      // ✅ nuevo valor por defecto: 5 minutos por página
    maxRetries = 3,
    backoffBaseMs = 800,
    backoffCapMs = 10_000
  } = msg.payload;

  // ✅ Timeout adaptativo: páginas posteriores pueden tardar más
  const computeTimeout = (p) => Math.min(timeoutMs + p * 10_000, 800_000);

  const totalPaginas = Math.max(1, Math.ceil(totalTramites / pageSize));
  let page = 1;
  let cancelled = false;

  const cancelHandler = (evt) => { if (evt.data === 'CANCEL') cancelled = true; };
  self.addEventListener('message', cancelHandler);

  const fetchWithTimeout = async (resource, options = {}, ms = 600000) => {
    const controller = new AbortController();
    const id = setTimeout(() => controller.abort(), ms);
    try { return await fetch(resource, { ...options, signal: controller.signal }); }
    finally { clearTimeout(id); }
  };

  try {
    while (!cancelled && page <= totalPaginas) {
      const fd = new FormData();
      fd.append('action', 'get-datosporomisionpaginacion');
      fd.append('RAM', RAM ?? '');
      fd.append('RAM_O_EXP', RAM_O_EXP ?? 'RAM');
      fd.append('page', String(page));
      fd.append('pageSize', String(pageSize));
      fd.append('Consulta', Consulta ?? false);

      const t0 = performance.now();
      const resp = await fetchWithTimeout(url, { method: 'POST', body: fd }, computeTimeout(page));
      const t1 = performance.now();
      const ms = t1 - t0;

      let datos;
      try {
        datos = await resp.json();
      } catch (err) {
        self.postMessage({ type: 'ERROR', payload: { error: 'JSON_PARSE', detail: String(err), page } });
        break;
      }

      if (datos && typeof datos.error !== 'undefined') {
        self.postMessage({ type: 'ERROR', payload: { ...datos, page } });
        break;
      }

      // PROGRESS para la barra
      self.postMessage({
        type: 'PROGRESS',
        payload: { page, totalPaginas, ms }
      });

      const datos5 = Array.isArray(datos?.[5]) ? datos[5] : (datos?.[5] ? [datos[5]] : []);
      const datos7 = (datos && typeof datos[7] !== 'undefined') ? datos[7] : null;
      const Reportes = (page === 1 && typeof datos?.Reportes !== 'undefined') ? datos.Reportes : null;

      self.postMessage({
        type: 'DATA_PAGE',
        payload: { page, datos5, datos7, Reportes }
      });

      page++;
    }

    if (!cancelled) self.postMessage({ type: 'DONE' });
    } catch (err) {
    } finally {
        self.removeEventListener('message', cancelHandler);
        self.close();
    }

};