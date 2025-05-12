async function fetchWithTimeout(url, options, timeout = 120000) {
    return new Promise((resolve, reject) => {
      const controller = new AbortController();
      const signal = controller.signal;
  
      const fetchTimeout = setTimeout(() => {
        controller.abort();
        reject(new Error('Request timed out'));
      }, timeout);
  
      fetch(url, { ...options, signal })
        .then(response => {
          clearTimeout(fetchTimeout);
          if (!response.ok) {
            reject(new Error('Network response was not ok'));
          } else {
            resolve(response);
          }
        })
        .catch(error => {
          clearTimeout(fetchTimeout);
          if (error.name === 'AbortError') {
            reject(new Error('Request was aborted'));
          } else {
            reject(error);
          }
        });
    });
}
  