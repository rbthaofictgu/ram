async function fetchWithTimeout(url, options, timeout = 300000) {
  return new Promise((resolve, reject) => {
    const controller = new AbortController();
    const signal = controller.signal;
    console.log(timeout,'timeout');
    // Ajusta el tiempo de espera si es necesario
    const fetchTimeout = setTimeout(() => {
      controller.abort(); // Abortamos la solicitud
      reject(new Error('Request timed out'));
    }, timeout);
    fetch(url, { ...options, signal })
      .then(response => {
        clearTimeout(fetchTimeout); // Limpiamos el timeout al recibir respuesta
        if (!response.ok) {
          reject(new Error('Network response was not ok'));
        } else {
          resolve(response);
        }
      })
      .catch(error => {
        clearTimeout(fetchTimeout); // Limpiamos el timeout si ocurre un error
        if (error.name === 'AbortError') {
          reject(new Error('Request was aborted due to timeout'));
        } else {
          reject(error);
        }
      });
  });
}